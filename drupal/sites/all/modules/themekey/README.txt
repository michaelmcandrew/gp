// $Id: README.txt,v 1.6.2.5 2010/01/04 14:18:14 mkalkbrenner Exp $

ThemeKey
========

Name: themekey
Authors: Markus Kalkbrenner | Cocomore AG
         Carsten Müller | Cocomore AG
         Thilo Wawrzik <drupal at profix898 dot de>
Drupal: 6.x
Sponsor: Cocomore AG - http://www.cocomore.com


Description
===========

ThemeKey is meant to be a generic theme switching module. It
allows you to switch the theme for different paths and based
on object properties (e.g. node field values). It can also be
easily extended to support additional paths or properties as
exposed by other modules.

Documentation for users and developers is very sparse at the
moment. I hope to complete the docs in the next few weeks.
Thanks for your patience :)


Installation
============

1. Place whole themekey folder into your Drupal modules/ or better
   sites/x/modules/ directory.

2. Enable the themekey module by navigating to
     administer > modules

3. Bring up themekey configuration screens by navigating to
     administer > settings > themekey


ThemeKey UI
===========

How to use ThemeKey UI on node forms ...

- Go to admin/settings/themekey/settings/ui to make theme options available
  on node forms, and check off the content types you want to enable the
  options for


ThemeKey Properties
===================

"ThemeKey Properties" adds additional properties to ThemeKey module.
Download it from http://drupal.org/project/themekey_properties


For Developers
==============

HOOK_themekey_properties()
  Attributes
    Key:    namespace:property
    Value:  array()
            - description => Readable name of property (required)
            - validator   => Callback function to validate a rule starting with that property (optional)
                             TODO: describe validator arguments and return value 
              static      => true/false, static properties don't occur in properties drop down
                             and have fixed operator and value (optional)

  Maps
    Key:    none (indexed)
    Value:  array()
            - src       => Source property path (required)
            - dst       => Destination property path (required)
            - callback  => Mapping callback (required)

HOOK_themekey_global()
  Global properties
    Key:    namespace:property
    Value:  property value (scalar value or array of scalar values)

HOOK_themekey_paths()
  Paths
    Key:    none (indexed)
    Value:  array()
            - path      => Router path to register (required)
            - callbacks => Load (and/or match) callback (optional)
              (the callback function can set the 'theme' element in $params array directly, which will be applied)
              Callback arguments:
              - $item:    array of elements associated with the path/callback
              - $params:  array of parameters available for load callback

HOOK_themekey_load_validators()
  include files containing validators
  