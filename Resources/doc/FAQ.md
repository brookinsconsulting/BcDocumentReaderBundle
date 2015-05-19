FAQ
===

Less commonly known current limitations of the BC Document Reader solution

## Supported FieldTypes

* ezurl

* ezstring

* ezbinaryfile


## Supported ContentTypes

* File


## ContentTypes Template Overrides Provided by Default

* Full : File

* Line : File

* Embed : File


## FieldTypes under consideration

The following FieldTypes are being considered for future support (provided by default). If you desire support for any of these please contact us and share your support!

* eztext

* ezobjectrelation

* ezobjectrelationlist

* ezimage


## Icon Images

Default icon themes and icon images come directly from eZ Publish Legacy [ezpublish_legacy/share/icons](https://github.com/ezsystems/ezpublish-legacy/tree/master/share/icons) files which are free (as in freedom).

You can customize the icon theme (dir) and icon images per mimetype document reader helper application group yaml settings.

This would in theory require changes to both `Resources/config/mimeptypes.yml` and `Resources/config/documentreader.yml` yaml settings file settings.
