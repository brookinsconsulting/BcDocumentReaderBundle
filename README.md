BC Document Reader
===================

This bundle implements a solution to detect file download links within XMLBlock content object attribute content and display the document helper applications which the user can use to open the file documents.


Version
=======

* The current version of BC Document Reader is 0.1.6

* Last Major update: May 20, 2015


Copyright
=========

* BC Document Reader is copyright 1999 - 2015 Brookins Consulting and 2013 - 2015 Think Creative

* See: [COPYRIGHT.md](COPYRIGHT.md) for more information on the terms of the copyright and license


License
=======

BC Document Reader is licensed under the GNU General Public License.

The complete license agreement is included in the [LICENSE](LICENSE) file.

BC Document Reader is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License or at your
option a later version.

BC Document Reader is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

The GNU GPL gives you the right to use, modify and redistribute
BC Document Reader under certain conditions. The GNU GPL license
is distributed with the software, see the file doc/LICENSE.

It is also available at [http://www.gnu.org/licenses/gpl.txt](http://www.gnu.org/licenses/gpl.txt)

You should have received a copy of the GNU General Public License
along with BC Document Reader in doc/LICENSE.  If not, see [http://www.gnu.org/licenses/](http://www.gnu.org/licenses/).

Using BC Document Reader under the terms of the GNU GPL is free (as in freedom).

For more information or questions please contact: license@brookinsconsulting.com


Requirements
============

The following requirements exists for using BC Document Reader extension:


### eZ Publish version

* Make sure you use eZ Publish version 5.x (required) or higher.

* Designed and tested with eZ Publish Community Project 2013.11


### PHP version

* Make sure you have PHP 5.x or higher.


Features
========

### Twig operators

This solution provides the following twig filters and functions

* Twig Filter: `bc_document_reader` - Used within template overrides to detect download file links configured to detect (via yaml options) and detected within the passed twig filter url parameter input.

* Twig Function: `bc_document_readers` - Displays the list of document readers for the download file links configured to detect (via yaml options) and detected within the current page's content object XML BLock attribute(s) content.

* Twig filter: `bc_file_size` - Used with the content/download file download embed template to display the embedded file size. This twig filter is based directly on `ez_file_size` provided by default within ezplatform6.0-alpha1. This duplication was required to introduce the same feature support within older versions of eZ Publish Platform 5.x.


### Services

* Services to assist in detection of file download file links within content and templates


### Dependencies

* This solution does not depend on eZ Publish Legacy in any way

* Persistent storage of pagedata and document reader file link extensions

* Underlying persistent storage of pagedata variables is provided by the [BcPageData](https://bitbucket.org/brookinsconsulting/bcpagedatabundle) bundle.

    * If you install via composer this dependency will be installed automatically


Use case requirements
=====================

This solution was created to provide for a common set of customer website requirements regarding document accessibility.

There may not be specific standards requiring all or parts of this solution, but it is part of the best practices we should strongly consider following for making accessible document management based solutions on the web.


## ADA / Section 508 Compliance

The standards describe more specifically about making linked documents themselves accessible (which is out of our hands and instead in the hands of the customer) the rest are best practices but not necessarily 'required'.

Section 508 Standards / Compliance requirements focus heavily on standards for making the documents themselves accessible (which is often out of our hands and instead in the hands of the customer).

While the Section 508 Standards / Compliance requirements do not specifically mention or require all of the below items, we find the following best practices (not necessarily 'required') help create a more accessible user-experience for document management / reading on the web.

* Links to document direct-downloads should include the title of the document and/or be explicitly descriptive of the linked document.

* All links including but not limited to document direct-download links are subject to all general 508 Standards.

    * Links should be of a readable size depending on device.

    * Links should comply with color contrast standards.

* Document direct-download links should have the associated mime-type icon and filesize when and where appropriate.

* Pages with links for direct download of documents should also display links to an appropriate download page of a corresponding document reader (ie Adobe acrobat) which can open the document at the bottom of the page where possible.

* Any image (or any non-text element) associated with the document link (e.g. thumbnail of the document) must include 'alt' text that succinctly describes the content conveyed by the element.

* Avoid having redundant links next to each other within the structure of the page (e.g. If a thumbnail image links to a document followed by a standard text link to the same document, both should be wrapped inside a single `<a>` element where possible).

Visit [http://www.section508.gov](http://www.section508.gov) for more information on the 'Americans with Disabilities Act and Section 508 Compliance'.


Installation
============

### Bundle Installation via Composer

Run the following command from your project root to install the bundle:

    bash$ composer require brookinsconsulting/bcdocumentreaderbundle dev-master;


### Bundle Activation

Within file `ezpublish/EzPublishKernel.php` at the bottom of the `use` statements, add the following lines.

    use BrookinsConsulting\BcPageDataBundle\BcPageDataBundle;
    use BrookinsConsulting\BcDocumentReaderBundle\BcDocumentReaderBundle;


Within file `ezpublish/EzPublishKernel.php` method `registerBundles` add the following into the `$bundles = array(` variable definition.

    // Brookins Consulting : BcDocumentReaderBundle Requirements
    new BrookinsConsulting\BcPageDataBundle\BcPageDataBundle(),
    new BrookinsConsulting\BcDocumentReaderBundle\BcDocumentReaderBundle()


### Bundle Assets Installation

It is recommended (for the built-in functionality to work) to install bundle assets after bundle installation and activation.

Here is an example of how to install bundle assets:

    php -d memory_limit=-1 ezpublish/console assets:install --relative --symlink web


### Bundle Styles Installation

It is recommended (for the built-in functionality to work as designed) to install an include of the bundle's styles within your pagelayout template.


### Bundle Styles Installation for sites which are already configured to serve scss

This solution uses scss by default for it's stylesheets instead of pure css and expects your installation's assetic config.yml to have enabled the assetic stylesheets filters "compass,cssrewrite".

Edit your pagelayout twig template and add the bundle's styles.scss within your stylesheets include:

    {% stylesheets filter="compass,cssrewrite"
        "@YourCustomBundle/Resources/public/scss/styles.scss"
        "@BcDocumentReaderBundle/Resources/public/scss/styles.scss"
    %}

You will also want to ensure both Ruby and Compass are installed and available as they are used by assetic to convert the scss into css.

You can install ruby and compass in various ways. Make sure that they are available via command line before running the assetic:dump command and then testing the website's use of them.

It is recommended (for the built-in functionality to work as designed) to dump bundle assets after installing the bundle styles include in the pagelayout template.

Here is an example of how to dump bundle assets:

    php -d memory_limit=-1 ezpublish/console assetic:dump --env=dev web


### Bundle Styles Installation for sites which are -not- already configured to serve scss

This solution uses scss by default for it's stylesheets instead of pure css and expects your installation's assetic config.yml to have enabled the assetic stylesheets filters "compass,cssrewrite".

In a default installation of eZ Publish Platform / eZ Platform you can enable these requirements yourself by making the following changes.


#### Enable assetic stylesheets filters compass and cssrewrite

Edit your `ezpublish/config.yml` file and make the following changes:

    # Assetic Configuration
    assetic:
        debug:          %kernel.debug%
        use_controller: true
    # snip (to avoid confusion)
        filters:
            cssrewrite: ~
            compass: ~

Specifically you must add these lines to your assetic configuration, save all config changes and clear all caches:

    # Assetic Configuration
    # snip (to avoid confusion)
        filters:
            cssrewrite: ~
            compass: ~


#### Ensure Ruby and Compass are installed and available

Ruby and Compass are used by assetic to convert the scss into css. You can install ruby and compass in various ways. Make sure that they are available via command line before testing the website's use of them.


#### Scss style template code installation for sites which are -not- already configured to serve scss

Edit your `pagelayout.html.twig` template or `page_head_style.html.twig` template and add the bundle's styles.scss calls *after* your existing css only stylesheets include calls:

    {% stylesheets
        '@eZDemoBundle/Resources/public/css/bootstrap.css'
        '@eZDemoBundle/Resources/public/css/responsive.css'
        '@eZDemoBundle/Resources/public/css/video.css'
        '@BCPageLayoutOverrideTestBundle/Resources/public/css/override.css'
    %}
        <link rel="stylesheet" type="text/css" href="{{ asset_url }}"/>
    {% endstylesheets %}

    {% stylesheets filter="compass,cssrewrite"
        '@BcDocumentReaderBundle/Resources/public/scss/styles.scss'
    %}
        <link rel="stylesheet" type="text/css" href="{{ asset_url }}"/>
    {% endstylesheets %}

The above example is from a default installation of eZ Publish Platform Demo Bundle (2014.11).

It is recommended (for the built-in functionality to work as designed) to dump bundle assets after installing the bundle styles include in the pagelayout template.

Here is an example of how to dump bundle assets:

    php -d memory_limit=-1 ezpublish/console assetic:dump --env=dev web


### Bundle Document Helper Twig Function Installation

Edit your pagelayout twig template and add the following `bc_document_readers` twig function code:

    {# START INSTALL THIS CODE: BC Document Reader : Display Document Reader Helper Application Download Links #}
    {{ include('BcDocumentReaderBundle:parts:reader_links.html.twig', {}) }}
    {# END INSTALL THIS CODE: BC Document Reader #}

Here is a basic example usage:

    <div id="main-content" class="{% if is_homepage is not defined %}panel-light-blue subpage{% endif %}" >
        {% if is_homepage is not defined %}
        <div class="container">
        {% endif %}

        {% block content %}
            {% if module_result %}
                {# we are in a legacy rendered module #}
                {{ module_result.content|raw }}
            {% endif %}
        {% endblock %}

        {# START INSTALL THIS CODE: BC Document Reader : Display Document Reader Helper Application Download Links #}
        {{ include('BcDocumentReaderBundle:parts:reader_links.html.twig', {}) }}
        {# END INSTALL THIS CODE: BC Document Reader #}

        {% if is_homepage is not defined %}
        </div>
        {% endif %}
    </div>


### Clear the caches

Clear eZ Publish Platform / eZ platfrom caches (Required).

    php ezpublish/console cache:clear;


Template Customization
===================================

## Custom Template Override Twig Filter Installation

If you need to use the `bc_document_reader` twig filter within your own custom template overrides (possibly for custom content types) simply pass your template content's url string value into the filter and the file / document type will be detected.


Mimetype / File type Customization
===================================

## Supported File Extensions and Mimetypes

Please review `Resources/config/documentreader.yml` and `Resources/config/mimetypes.yml` for the default detected file extensions and mimetypes.

If you wish to support further file extensions and mimetypes simply add them to the yaml settings and clear all caches (required).


### Warning to avoid duplicate mimetypes

It is important to only have one mimetype per document reader helper application group settings.

If a mimetype already exists within the settings do not add a duplicate as this will cause serious problems.

Instead just add the additional file extension to the existing document reader helper application group settings.


## Add a new file extension to an existing mimetype document reader helper application group settings.


### Detecting additional custom file extension and mimetypes


### Example

First edit the bundle settings options file `Resources/config/mimeptypes.yml` and search for the mimetype you wish to add file extension support for.

In this example we are adding detection support for the `.zipx` files which are not supported by default.

Since there is already a setting for `application/zip` mimetype, you only need to add the additional extension.

Here is a clear example of the addition / change that needs to be made.

Before:

    - { type: "CompressedWin", mimeType: "application/zip", extensions: [ zip ] }

After:

    - { type: "CompressedWin", mimeType: "application/zip", extensions: [ zip, zipx ] }

Save your yaml settings additions and clear all caches (required).


## Add a new file extension and mimetype not already within the default settings within an existing document reader helper application group mapping


### Example

First edit the bundle settings options file `Resources/config/mimeptypes.yml` and search for the mimetype you wish to add file extension suport for.

In this example we are adding detection support for the `.cab` files which are not supported by default.

Since there is not already a setting for `application/vnd.ms-cab-compressed` mimetype, you only need to add the complete settings definition.

Here is a clear example of the addition / change that needs to be made.

Add the following setting line:

    helper_mime_map:
    # Snip (Feel free to place the following line any position below the helper_mime_map: line
    - { type: "CompressedWin", mimeType: "application/vnd.ms-cab-compressed", extensions: [ cab ] }

Save your yaml settings additions and clear all caches (required).

By default with the above settings additions will display the CompressedWin document reader helper application, 'WinRar (for Windows)'.


## Add a new file extension and mimetype not already within the default settings within a new document reader helper application group mapping


### Example

First edit the bundle settings options file `Resources/config/mimeptypes.yml` and search for the mimetype you wish to add file extension suport for.

In this example we are adding detection support for the `.ogg` audio files which are not supported by default.

Since there is not already a setting for `audio/ogg` mimetype, you only need to add the complete settings definition.

Here is a clear example of the addition / change that needs to be made.

Add the following setting line:

    helper_mime_map:
    # Snip (Feel free to place the following line any position below the helper_mime_map: line
    - { type: "WindowsVlc", mimeType: "audio/ogg", extensions: [ ogg, opus, oga, spx ] }

Save your yaml settings additions.

Second edit the bundle settings options file `Resources/config/documentreaders.yml` and add the following document reader helper settings:

Here is a clear example of the addition / change that needs to be made.

Add the following setting line:

    helpers:
        WindowsVlc:
            name: VLC Media File
            viewer_name: VLC Media Player
            viewer_url: http://get.videolan.org/vlc/2.2.1/win32/vlc-2.2.1-win32.exe
            icon: audio
            icon_image: mimetypes/sound.png

Save your yaml settings additions and clear all caches (required).

By default with the above settings additions will display the WindowsVlc document reader helper application, 'Vlc (for Windows)'.


Usage
=====

The solution is configured to work virtually by default once properly installed.


Troubleshooting
===============

### Read the FAQ

Some problems are more common than others. The most common ones are listed in the the [doc/FAQ.md](doc/FAQ.md)


### Support

If you have find any problems not handled by this document or the FAQ you can contact Brookins Consulting through the support system: [http://brookinsconsulting.com/contact](http://brookinsconsulting.com/contact)

