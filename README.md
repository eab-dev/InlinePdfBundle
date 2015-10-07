EabInlinePdfBundle
==================

##Summary

An eZ Publish bundle providing routing and a controller that enables inline
display of PDF files with friendly URL aliases.

##Copyright

Copyright (C) 2015 Andy Caiger, [Enterprise AB Ltd](http://eab.uk)

##License

Licensed under [GNU General Public License 2.0](http://www.gnu.org/licenses/gpl-2.0.html)

##Features

Suppose a page `http://www.example.com/Folder/File` has a field that contains a
binary file called `My document.pdf`. You can access an inline version of the PDF
using `http://www.example.com/Folder/File/My%20document.pdf`.

You can also download it using the usual legacy module at
`http://www.example.com/content/download/...`

##Usage

Example of generating the URL in a template:

    {% set file = content.getField( 'file' ) %}
    <a href="{{ path( location ) ~ '/' ~ file.value.fileName | escape( 'url' ) }}">
        <button class="btn btn-default btn-lg" type="button">View</button>
    </a>

##Install

1. Download the bundle to `src/Eab/InlinePdfBundle`.

2. Edit `registerBundles()` in `ezpublish/EzPublishKernel.php` and add the following:

        new Eab\InlinePdfBundle\EabInlinePdfBundle(),

3. Add the following to `ezpublish/config/routing.yml`:

        eab_inlinepdf:
            resource: "@EabInlinePdfBundle/Resources/config/routing.yml"

##Caveat

There is no support for external file storage. The file is assumed to be stored
in the eZ Publish legacy file tree.
