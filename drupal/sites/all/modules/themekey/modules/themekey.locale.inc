<?php
// $Id: themekey.locale.inc,v 1.3.2.4 2010/01/04 15:00:42 mkalkbrenner Exp $

/**
 * @file
 * Provides some node attributes as ThemeKey properties.
 */


/**
 * Implements hook_themekey_properties().
 *
 * Provides additional properties for module ThemeKey:
 * - locale:language
 *
 * @return
 *   array of themekey properties and mapping functions
 */
function themekey_locale_themekey_properties() {
  // Attributes for properties
  $attributes = array();
  $attributes['locale:language'] = array(
    'description' => t('Locale: Language - The code of the current site language, formatted like "en" or "de". See !link for your the codes of the enabled languages',
      array('!link' => l('admin/settings/language', 'admin/settings/language'))),
    'validator' => 'themekey_validator_language',
  );

  return array('attributes' => $attributes);
}


/**
 * Implements hook_themekey_paths().
 */
function themekey_locale_themekey_global() {
  global $language;

  $parameters = array();
  $parameters['locale:language'] = !empty($language->language) ? $language->language : NULL;

  return $parameters;
}
