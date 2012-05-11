
ThemeKey Properties
===================

Name: themekey_properties
Authors: Markus Kalkbrenner | Cocomore AG
         Carsten Müller | Cocomore AG
Drupal: 6.x
Sponsor: Cocomore AG - http://www.cocomore.com


About
=====

"ThemeKey Properties" adds additional properties to ThemeKey module which
can be found at http://drupal.org/project/themekey

"ThemeKey", itself, provides an infrastructure to switch Drupal themes according
to rules which might use such properties.

Additional Properties provided by "ThemeKey Properties":
 - drupal:base_path
 - drupal:is_front_page
 - system:query_param
 - system:query_string
 - system:cookie
 - system:server_ip
 - system:server_port
 - system:server_name
 - system:https
 - system:remote_ip
 - system:referer
 - system:user_agent
 - system:user_browser
 - system:user_browser_simplified
 - system:user_os
 - system:user_os_simplified
 - system:date
 - system:time
 - system:date_time
 - system:session
 - system:day_of_week
 - system:dummy
 - user:role
 
ThemeKey Properties contains an additional module called
"ThemeKey Properties Debug". This sub module allows you to turn on
a debug feature that shows the current values of all properties on
every page including those properties provided by different modules.

The debug values are clickable. If you do so, you can easily start
creating a Theme Switching rule from this property and value.


Installation
============

1. ThemeKey Properties

  1.1 Install ThemeKey itself from http://drupal.org/project/themekey

  1.2 Place whole themekey_properties folder into your Drupal modules/
      directory, or better, your sites/x/modules directory.

  1.3 Enable the "ThemeKey Properties" module at /admin/build/modules


2. ThemeKey Properties Debug

  2.1 Install ThemeKey itself from http://drupal.org/project/themekey

  2.2 Place whole themekey_properties folder into your Drupal modules/
      directory, or better, your sites/x/modules directory.

  2.3 Enable the "ThemeKey Properties" module at /admin/build/modules
  
  2.4 Activate "Show themekey properties values" at
      /admin/settings/themekey/settings/debug


Examples
========                                                              

1. Select a theme for Firefox 3.0.x, but not Firefox 3.5.x

  1.1 go to /admin/settings/themekey

  1.2 cascade following Theme Switching Rules:
      
      Property: system:user_browser_simplified
      Operator: =
      Value: Mozilla Firefox
        
        Property: system:user_browser
        Operator: >
        Value: Mozilla Firefox 3.0
          
          Property: system:user_browser
          Operator: <
          Value: Mozilla Firefox 3.5


2. Select a theme for IE 6

  2.1 go to /admin/settings/themekey

  2.2 cascade following Theme Switching Rules:
      
      Property: system:user_browser_simplified
      Operator: =
      Value: Internet Explorer
        
        Property: system:user_browser
        Operator: >
        Value: Internet Explorer 6
          
          Property: system:user_browser
          Operator: <
          Value: Internet Explorer 7


3. Select a theme for Christmas 2009

  3.1  go to /admin/settings/themekey
  
  3.2 cascade following Theme Switching Rules:
      
      Property: system:date
      Operator: >
      Value: 2009-12-24
        
        Property: system:date
        Operator: <
        Value: 2009-12-27


4. Select a theme for New Year 2010
  
  4.1 go to /admin/settings/themekey
  
  4.2 create a Theme Switching Rule:
      
      Property: system:date
      Operator: =
      Value: 2010-01-01


5. Select a theme dedicated for your start page (front page, index page, ...)

  5.1 go to /admin/settings/themekey
  
  5.2 create a Theme Switching Rule:
      
      Property: drupal:is_front_page
      Operator: =
      Value: true
