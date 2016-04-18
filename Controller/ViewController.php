<?php

namespace Eab\InlinePdfBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;
use eZ\Publish\Core\FieldType\BinaryFile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ViewController extends Controller
{
    /*
     * View a PDF inline for articles from old website
     * We need this intermediate step because URL aliases cannot include query
     * strings.
     * @param request
     * @return response
     */
    public function viewAction( $path )
    {
        $repository = $this->getRepository();
        $urlAliasService = $repository->getURLAliasService();
        $contentUri = dirname( $path );

        try {
            $locationID = $urlAliasService->lookup( $contentUri )->destination;
        } catch ( NotFoundException $e ) {
            // Convert a 500 error into a 404
            throw $this->createNotFoundException();
        }

        try {
            $locationService = $repository->getLocationService();
            $location = $locationService->loadLocation( $locationID );
        } catch ( UnauthorizedException $e ) {
            // Avoid a 500 error by making eZ Publish handle the exception
            return $this->redirect( "/" . $contentUri );
        }

        $contentService = $repository->getContentService();
        $content = $contentService->loadContentByContentInfo( $location->contentInfo );

        foreach ( $content->getFields() as $field ) {
            if ( $field->value instanceof BinaryFile\Value && $field->value->fileName == basename( $path ) . ".pdf" ) {
                $legacyRootDir = $this->container->getParameter( 'ezpublish_legacy.root_dir' );
                return new BinaryFileResponse( $legacyRootDir . "/" . $field->value->uri );
            }
        }
        // There were no correctly named PDF files, so throw an exception
        throw $this->createNotFoundException();
    }
}
