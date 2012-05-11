<?php
// $Id: themekey.system.inc,v 1.3.2.17 2010/07/20 12:43:23 mkalkbrenner Exp $

/**
 * @file
 * Provides some ThemeKey properties.
 */


/**
 * Implements hook_themekey_properties().
 *
 * Provides additional properties for module ThemeKey:
 * - system:host
 * - drupal:path
 * - drupal:path:wildcard
 *
 * @return
 *   array of themekey properties and mapping functions
 */
function themekey_system_themekey_properties() {
  // Attributes for properties
  $attributes = array();
  $attributes['system:host'] = array(
    'description' => t('System: HTTP_HOST - The hostname/domain of the site without http:// or https://, like "www.drupal.org" or "drupal.cocomore.com"'),
    'validator' => 'themekey_validator_http_host',
  );
  $attributes['drupal:path'] = array(
    'description' => t('Drupal: Drupal path like "node/add/story" or path alias with support for wildcards.<br />
Query parameters are stripped off before the path gets examined. P.e. "node/add/story?destination=node" becomes "node/add/story" first. If you want to access query parameters have a look at property system:query_param provided by !link.<br />
Wildcard characters are "#" for numeric parts and "%" for all characters. To match conditions against a certain part, use an identifier with the wildcard. For example "comment/reply/#xyz" matches all paths with "comment/reply" and a numeric third argument. You can then specify conditions for every wildcard argument using the property "drupal:path:wildcard" and the identifier you choose ("xyz" in this example).<br />
These are the possible wildcard replacements for foo/bar/42/test.html:', array('!link' => l('ThemeKey Properties', 'http://drupal.org/project/themekey_properties'))) .
'<pre>
foo/bar/42/test.html
foo/bar/42/%
foo/bar/42
foo/bar/%/test.html
foo/bar/%/%
foo/bar/%
foo/bar/#/test.html
foo/bar/#/%
foo/bar/#
foo/bar
foo/%/42/test.html
foo/%/42/%
foo/%/42
foo/%/%/test.html
foo/%/%/%
foo/%/%
foo/%/#/test.html
foo/%/#/%
foo/%/#
foo/%
foo
%/bar/42/test.html
%/bar/42/%
%/bar/42
%/bar/%/test.html
%/bar/%/%
%/bar/%
%/bar/#/test.html
%/bar/#/%
%/bar/#
%/bar
%/%/42/test.html
%/%/42/%
%/%/42
%/%/%/test.html
%/%/%/%
%/%/%
%/%/#/test.html
%/%/#/%
%/%/#
%/%
%
</pre>',
    'validator' => 'themekey_validator_drupal_path',
  );
  $attributes['drupal:path:wildcard'] = array(
    'description' => t('Wildcard of "drupal:path". See explaination of "drupal:path" for details.'),
    'validator' => 'themekey_validator_wildcard',
  );
  $attributes['drupal:get_q'] = array(
    'description' => t('Drupal: $_GET[\'q\'] - Current value of drupal\'s query parameter "q"'),
    'validator' => '',
  );


  return array('attributes' => $attributes);
}


/**
 * Implements hook_themekey_global().
 */
function themekey_system_themekey_global() {
  global $user;

  $parameters = array();
  $parameters['system:host'] = $_SERVER['HTTP_HOST'];
  $parameters['drupal:get_q'] = themekey_get_q();

  return $parameters;
}
