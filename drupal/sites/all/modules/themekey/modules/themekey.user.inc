<?php
// $Id: themekey.user.inc,v 1.3.2.5 2010/01/04 15:00:42 mkalkbrenner Exp $

/**
 * @file
 * Provides some user attributes as ThemeKey properties.
 */


/**
 * Implements hook_themekey_properties().
 *
 * Provides additional properties for module ThemeKey:
 * - user:hostname
 * - user:language
 * - user:name
 * - user:uid
 *
 * @return
 *   array of themekey properties and mapping functions
 */
function themekey_user_themekey_properties() {
  // Attributes for properties
  $attributes = array();
  $attributes['user:hostname'] = array(
    'description' => t("User: Hostname - The user hostname property which is the IP address of client machine, adjusted for reverse proxy. Text from Drupal bootstrap.inc:
      If Drupal is behind a reverse proxy, we use the X-Forwarded-For header instead of \$_SERVER['REMOTE_ADDR'], which would be the IP address of the proxy server, and not the client's."),
    'validator' => 'themekey_validator_http_host',
  );
  $attributes['user:language'] = array(
    'description' => t('User: Language - The language the user has set in the settings of his profile page.
      Format is the language code (for example "en" for english or "de" for german) that can be found on !link.',
      array('!link' => l('admin/settings/language', 'admin/settings/language'))),
    'validator' => 'themekey_validator_language',
  );
  $attributes['user:name'] = array(
    'description' => t('User: Name - The username of the user. See !link for your users.', array('!link' => l('admin/user/user/list', 'admin/user/user/list'))),
  );
  $attributes['user:uid'] = array(
    'description' => t('User: ID - The id of the user (uid). The user id can be found in the URL of the users profile page, "user/23" or "user/23/edit" (23 = uid). See !link for your users.',
      array('!link' => l('admin/user/user/list', 'admin/user/user/list'))),
    'validator' => 'themekey_validator_ctype_digit',
  );

  return array('attributes' => $attributes);
}


/**
 * Implements hook_themekey_global().
 */
function themekey_user_themekey_global() {
  global $user;

  $parameters = array();
  $parameters['user:hostname'] = !empty($user->hostname) ? $user->hostname : NULL;
  $parameters['user:language'] = !empty($user->language) ? $user->language : NULL;
  $parameters['user:name'] = !empty($user->name) ? $user->name : NULL;
  $parameters['user:uid'] = $user->uid;

  return $parameters;
}
