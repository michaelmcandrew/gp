<?php
// $Id: themekey_validators.inc,v 1.1.2.12 2010/01/13 14:19:35 mkalkbrenner Exp $

/**
 * @file
 * Provides set of validators which could be used to validate
 * ThemeKey Theme Switching Rules.
 * @see themekey_admin.inc
 *
 * @author Markus Kalkbrenner | Cocomore AG
 *   @see http://drupal.org/user/124705
 *
 * @author Carsten Müller | Cocomore AG
 *   @see http://drupal.org/user/124707
 */


/**
 * Validates a Theme Switichng Rule.
 * Allowed Operators: "=", "!"
 * Allowed values: "true", "false" as string
 *
 *
 * @param $rule
 *   A Theme Switching Rule as associative array:
 *   - property: ThemeKey property as string (p.e. "drupal:path")
 *   - wildcard: optional string, only used if property is "drupal:path:wildcard"
 *   - operator: ThemeKey operator as string ("=", "!", "<". "<=", ">", ">=", "~")
 *   - value: ThemeKey property value as string
 *
 * @return
 *   An associative array of errors:
 *   - property: translated error message as string
 *     describing a problem with the property
 *   - wildcard: translated error message as string
 *     describing a problem with the wildcard
 *   - operator: translated error message as string
 *     describing a problem with the operator
 *   - value: translated error message as string
 *     describing a problem with the value
 *   If no errors detected the array is empty.
 */
function themekey_validator_string_boolean($rule) {
  $errors = array();

  switch ($rule['operator']) {
    case '=':
    case '!':
      if ('true' !== $rule['value'] && 'false' !== $rule['value']) {
        $errors['value'] = t('Possible values are "true" and "false"');
      }
      break;

    default:
      $errors['operator'] = t('Possible operators are "=" and "!"');
  }

  return $errors;
}


/**
 * Validates a Theme Switichng Rule.
 * Allowed Operators: "=", "!"
 * Allowed values: "1", "0"
 *
 *
 * @param $rule
 *   A Theme Switching Rule as associative array:
 *   - property: ThemeKey property as string (p.e. "drupal:path")
 *   - wildcard: optional string, only used if property is "drupal:path:wildcard"
 *   - operator: ThemeKey operator as string ("=", "!", "<". "<=", ">", ">=", "~")
 *   - value: ThemeKey property value as string
 *
 * @return
 *   An associative array of errors:
 *   - property: translated error message as string
 *     describing a problem with the property
 *   - wildcard: translated error message as string
 *     describing a problem with the wildcard
 *   - operator: translated error message as string
 *     describing a problem with the operator
 *   - value: translated error message as string
 *     describing a problem with the value
 *   If no errors detected the array is empty.
 */
function themekey_validator_nummeric_boolean($rule) {
  $errors = array();
  switch ($rule['operator']) {
    case '=':
    case '!':
      if (!ctype_digit($rule['value']) || 1 < $rule['value']) {
        $errors['value'] = t('Possible values are "0" and "1"');
      }
      break;

    default:
      $errors['operator'] = t('Possible operators are "=" and "!"');
  }

  return $errors;
}


/**
 * Validates a Theme Switichng Rule.
 * Allowed Operators: "=", "!", "<", "<=", ">", ">="
 * Allowed values: string of digits (numbers)
 *
 *
 * @param $rule
 *   A Theme Switching Rule as associative array:
 *   - property: ThemeKey property as string (p.e. "drupal:path")
 *   - wildcard: optional string, only used if property is "drupal:path:wildcard"
 *   - operator: ThemeKey operator as string ("=", "!", "<". "<=", ">", ">=", "~")
 *   - value: ThemeKey property value as string
 *
 * @return
 *   An associative array of errors:
 *   - property: translated error message as string
 *     describing a problem with the property
 *   - wildcard: translated error message as string
 *     describing a problem with the wildcard
 *   - operator: translated error message as string
 *     describing a problem with the operator
 *   - value: translated error message as string
 *     describing a problem with the value
 *   If no errors detected the array is empty.
 */
function themekey_validator_ctype_digit($rule) {
  $errors = array();
  switch ($rule['operator']) {
    case '~':
      $errors['operator'] = t('Possible operators are "=", "!", "<", "<=", ">" and ">="');
      break;
  }

  if (!ctype_digit($rule['value'])) {
    $errors['value'] = t('Value must be a number');
  }

  return $errors;
}


/**
 * Validates a Theme Switichng Rule.
 * Allowed Operators: any
 * Allowed values:
 * - configured drupal language as string or 'neutral' if operator is "="
 * - valid regular expression if operator is "~"
 * - any string for different operators
 *
 *
 * @param $rule
 *   A Theme Switching Rule as associative array:
 *   - property: ThemeKey property as string (p.e. "drupal:path")
 *   - wildcard: optional string, only used if property is "drupal:path:wildcard"
 *   - operator: ThemeKey operator as string ("=", "!", "<". "<=", ">", ">=", "~")
 *   - value: ThemeKey property value as string
 *
 * @return
 *   An associative array of errors:
 *   - property: translated error message as string
 *     describing a problem with the property
 *   - wildcard: translated error message as string
 *     describing a problem with the wildcard
 *   - operator: translated error message as string
 *     describing a problem with the operator
 *   - value: translated error message as string
 *     describing a problem with the value
 *   If no errors detected the array is empty.
 */
function themekey_validator_language($rule) {
  $errors = array();
  switch ($rule['operator']) {
    case '=':
      $languages = language_list();
      // add 'neutral' to the list of languages
      $languages['neutral'] = TRUE;
      if (!array_key_exists($rule['value'], $languages)) {
        $errors['value'] = t('Possible values are %languages', array('%languages' => '"' . implode('", "', array_keys($languages)) . '"'));
      }
      break;

    case '~':
      $errors = themekey_validator_regex($rule);
      break;
  }

  return $errors;
}


/**
 * Validates a Theme Switichng Rule.
 * Allowed Operators: "<", "<=", ">", ">=" and "~"
 * Allowed values:
 * - valid regular expression if operator is "~"
 * - string formatted like "2009-12-24 23:56:17" for different operators
 *
 *
 * @param $rule
 *   A Theme Switching Rule as associative array:
 *   - property: ThemeKey property as string (p.e. "drupal:path")
 *   - wildcard: optional string, only used if property is "drupal:path:wildcard"
 *   - operator: ThemeKey operator as string ("=", "!", "<". "<=", ">", ">=", "~")
 *   - value: ThemeKey property value as string
 *
 * @return
 *   An associative array of errors:
 *   - property: translated error message as string
 *     describing a problem with the property
 *   - wildcard: translated error message as string
 *     describing a problem with the wildcard
 *   - operator: translated error message as string
 *     describing a problem with the operator
 *   - value: translated error message as string
 *     describing a problem with the value
 *   If no errors detected the array is empty.
 */
function themekey_validator_date_time($rule) {
  $errors = array();
  switch ($rule['operator']) {
    case '=':
    case '!':
      // It seems senseless to switch a theme for one second
      $errors['operator'] = t('Possible operators are "<", "<=", ">", ">=" and "~"');
      break;

    case '~':
      $errors = themekey_validator_regex($rule);
      break;

    default:
      if (!preg_match("/^[0-9]{4}[0-9\- :]*$/", $rule['value'])) {
        $errors['value'] = t("Value isn't suitable for checks against dates formatted like \"2009-12-24 23:56:17\"");
      }
  }

  return $errors;
}


/**
 * Validates a Theme Switichng Rule.
 * Allowed Operators: any
 * Allowed values:
 * - valid regular expression if operator is "~"
 * - a vaild date like "2009-12-24" if operator is "=" or "!"
 * - fragment of a date which contains at least the year as four digits
 *   for different operators
 *
 *
 * @param $rule
 *   A Theme Switching Rule as associative array:
 *   - property: ThemeKey property as string (p.e. "drupal:path")
 *   - wildcard: optional string, only used if property is "drupal:path:wildcard"
 *   - operator: ThemeKey operator as string ("=", "!", "<". "<=", ">", ">=", "~")
 *   - value: ThemeKey property value as string
 *
 * @return
 *   An associative array of errors:
 *   - property: translated error message as string
 *     describing a problem with the property
 *   - wildcard: translated error message as string
 *     describing a problem with the wildcard
 *   - operator: translated error message as string
 *     describing a problem with the operator
 *   - value: translated error message as string
 *     describing a problem with the value
 *   If no errors detected the array is empty.
 */
function themekey_validator_date($rule) {
  $errors = array();
  switch ($rule['operator']) {
    case '=':
    case '!':
      if (!preg_match("/^[0-9]{4}-[0-1]{1}[0-9]{1}-[0-3]{1}[0-9]{1}$/", $rule['value'])) {
        $errors['value'] = t('Not a valid date string. Format should look like "2009-12-24"');
      }
      break;

    case '~':
      $errors = themekey_validator_regex($rule);
      break;

    default:
      if (!preg_match("/^[0-9]{4}[0-9\-]*$/", $rule['value'])) {
        $errors['value'] = t("Value isn't suitable for checks against dates formatted like \"2009-12-24\"");
      }
  }

  return $errors;
}


/**
 * Validates a Theme Switichng Rule.
 * Allowed Operators: any
 * Allowed values:
 * - valid regular expression if operator is "~"
 * - a vaild time like "23:16:56" if operator is "=" or "!"
 * - fragment of a time which contains at least the hour as two digits
 *   for different operators
 *
 *
 * @param $rule
 *   A Theme Switching Rule as associative array:
 *   - property: ThemeKey property as string (p.e. "drupal:path")
 *   - wildcard: optional string, only used if property is "drupal:path:wildcard"
 *   - operator: ThemeKey operator as string ("=", "!", "<". "<=", ">", ">=", "~")
 *   - value: ThemeKey property value as string
 *
 * @return
 *   An associative array of errors:
 *   - property: translated error message as string
 *     describing a problem with the property
 *   - wildcard: translated error message as string
 *     describing a problem with the wildcard
 *   - operator: translated error message as string
 *     describing a problem with the operator
 *   - value: translated error message as string
 *     describing a problem with the value
 *   If no errors detected the array is empty.
 */
function themekey_validator_time($rule) {
  $errors = array();
  switch ($rule['operator']) {
    case '=':
    case '!':
      if (!preg_match("/^[0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/", $rule['value'])) {
        $errors['value'] = t('Not a valid date string. Format should look like "23:16:56"');
      }
      break;

    case '~':
      $errors = themekey_validator_regex($rule);
      break;

    default:
      if (!preg_match("/^[0-2][0-9][0-9:]*$/", $rule['value'])) {
        $errors['value'] = t("Value isn't suitable for checks against dates formatted like \"23:16:56\"");
      }
  }

  return $errors;
}


/**
 * Validates a Theme Switichng Rule.
 * Allowed Operators: any
 * Allowed values:
 * - valid regular expression if operator is "~"
 * - an existing content type if operator is "=" or "!"
 * - must contain only lowercase letters, numbers, and underscores
 *   for different operators
 *
 *
 * @param $rule
 *   A Theme Switching Rule as associative array:
 *   - property: ThemeKey property as string (p.e. "drupal:path")
 *   - wildcard: optional string, only used if property is "drupal:path:wildcard"
 *   - operator: ThemeKey operator as string ("=", "!", "<". "<=", ">", ">=", "~")
 *   - value: ThemeKey property value as string
 *
 * @return
 *   An associative array of errors:
 *   - property: translated error message as string
 *     describing a problem with the property
 *   - wildcard: translated error message as string
 *     describing a problem with the wildcard
 *   - operator: translated error message as string
 *     describing a problem with the operator
 *   - value: translated error message as string
 *     describing a problem with the value
 *   If no errors detected the array is empty.
 */
function themekey_validator_node_type($rule) {
  $errors = array();
  switch ($rule['operator']) {
    case '=':
    case '!':
      $node_types = node_get_types();
      if (!array_key_exists($rule['value'], $node_types)) {
        $errors['value'] = t('Possible values are %node_types', array('%node_types' => '"' . implode('", "', array_keys($node_types)) . '"'));
      }
      break;

    case '~':
      $errors = themekey_validator_regex($rule);
      break;

    default:
      if (!preg_match("/^[0-9a-z_]+$/", $rule['value'])) {
        $errors['value'] = t("Value isn't suitable. It must contain only lowercase letters, numbers, and underscores");
      }
  }

  return $errors;
}


/**
 * Validates a Theme Switichng Rule.
 * Allowed Operators: any
 * Allowed values:
 * - valid regular expression if operator is "~"
 * - must contain only lowercase letters, numbers, and hyphens
 *   for different operators
 *
 *
 * @param $rule
 *   A Theme Switching Rule as associative array:
 *   - property: ThemeKey property as string (p.e. "drupal:path")
 *   - wildcard: optional string, only used if property is "drupal:path:wildcard"
 *   - operator: ThemeKey operator as string ("=", "!", "<". "<=", ">", ">=", "~")
 *   - value: ThemeKey property value as string
 *
 * @return
 *   An associative array of errors:
 *   - property: translated error message as string
 *     describing a problem with the property
 *   - wildcard: translated error message as string
 *     describing a problem with the wildcard
 *   - operator: translated error message as string
 *     describing a problem with the operator
 *   - value: translated error message as string
 *     describing a problem with the value
 *   If no errors detected the array is empty.
 */
function themekey_validator_http_host($rule) {
  $errors = array();
  switch ($rule['operator']) {
    case '~':
      $errors = themekey_validator_regex($rule);
      break;

    default:
      if (!preg_match("/^[0-9a-z\-.]+$/", $rule['value'])) {
        $errors['value'] = t("Value isn't suitable. It must contain only lowercase letters, numbers, and hyphens");
      }
  }

  return $errors;
}


/**
 * Validates a Theme Switichng Rule.
 * Allowed Operators: all
 * Allowed values: any string without whitespaces
 *
 *
 * @param $rule
 *   A Theme Switching Rule as associative array:
 *   - property: ThemeKey property as string (p.e. "drupal:path")
 *   - wildcard: optional string, only used if property is "drupal:path:wildcard"
 *   - operator: ThemeKey operator as string ("=", "!", "<". "<=", ">", ">=", "~")
 *   - value: ThemeKey property value as string
 *
 * @return
 *   An associative array of errors:
 *   - property: translated error message as string
 *     describing a problem with the property
 *   - wildcard: translated error message as string
 *     describing a problem with the wildcard
 *   - operator: translated error message as string
 *     describing a problem with the operator
 *   - value: translated error message as string
 *     describing a problem with the value
 *   If no errors detected the array is empty.
 */
function themekey_validator_no_whitespace($rule) {
  $errors = array();

  if (preg_match("/\s/", $rule['value'])) {
    $errors['value'] = t('Value must not contain white spaces');
  }

  return $errors;
}


/**
 * Validates a Theme Switichng Rule.
 * Allowed Operators: any
 * Allowed wildcards: any string without whitespaces and not starting with "#" or "%"
 *
 *
 * @param $rule
 *   A Theme Switching Rule as associative array:
 *   - property: ThemeKey property as string (p.e. "drupal:path")
 *   - wildcard: optional string, only used if property is "drupal:path:wildcard"
 *   - operator: ThemeKey operator as string ("=", "!", "<". "<=", ">", ">=", "~")
 *   - value: ThemeKey property value as string
 *
 * @return
 *   An associative array of errors:
 *   - property: translated error message as string
 *     describing a problem with the property
 *   - wildcard: translated error message as string
 *     describing a problem with the wildcard
 *   - operator: translated error message as string
 *     describing a problem with the operator
 *   - value: translated error message as string
 *     describing a problem with the value
 *   If no errors detected the array is empty.
 */
function themekey_validator_wildcard($rule) {
  $errors = themekey_validator_no_whitespace($rule);

  if (preg_match("/^[#%]/", $rule['wildcard'])) {
    $errors['wildcard'] = t('Wildcard must not start with type identifier "#" or "%" at this point');
  }
  elseif (preg_match("/\s/", $rule['wildcard'])) {
    $errors['wildcard'] = t('Wildcard must not contain white spaces');
  }

  return $errors;
}


/**
 * Validates a Theme Switichng Rule.
 * Allowed Operators: "~"
 * Allowed values: valid regular expression
 *
 * @see http://php.net/manual/en/pcre.pattern.php
 *
 *
 * @param $rule
 *   A Theme Switching Rule as associative array:
 *   - property: ThemeKey property as string (p.e. "drupal:path")
 *   - wildcard: optional string, only used if property is "drupal:path:wildcard"
 *   - operator: ThemeKey operator as string ("=", "!", "<". "<=", ">", ">=", "~")
 *   - value: ThemeKey property value as string
 *
 * @return
 *   An associative array of errors:
 *   - property: translated error message as string
 *     describing a problem with the property
 *   - wildcard: translated error message as string
 *     describing a problem with the wildcard
 *   - operator: translated error message as string
 *     describing a problem with the operator
 *   - value: translated error message as string
 *     describing a problem with the value
 *   If no errors detected the array is empty.
 */
function themekey_validator_regex($rule) {
  $errors = array();
  switch ($rule['operator']) {
    case '~':
      if (FALSE === @preg_match($rule['value'], 'dummy')) {
        $errors['value'] = t('Regular expression seems to be malformed. See !link for details', array('!link' => l(t('PHP Manual'), 'http://php.net/manual/en/pcre.pattern.php')));
      }
      break;

    default:
      $errors['operator'] = t('Only possible operators is "~"');
      break;
  }

  return $errors;
}
/**
 * @file
 * Provides set of validators which could be used to validate
 * ThemeKey Theme Switching Rules.
 * @see themekey_admin.inc
 *
 * @author Markus Kalkbrenner | Cocomore AG
 *   @see http://drupal.org/user/124705
 *
 * @author Carsten Müller | Cocomore AG
 *   @see http://drupal.org/user/124707
 */


/**
 * Validates a Theme Switichng Rule.
 * Allowed Operators: "=", "!"
 * Allowed values: paths without whitespaces
 *
 *
 * @param $rule
 *   A Theme Switching Rule as associative array:
 *   - property: ThemeKey property as string (p.e. "drupal:path")
 *   - wildcard: optional string, only used if property is "drupal:path:wildcard"
 *   - operator: ThemeKey operator as string ("=", "!", "<". "<=", ">", ">=", "~")
 *   - value: ThemeKey property value as string
 *
 * @return
 *   An associative array of errors:
 *   - property: translated error message as string
 *     describing a problem with the property
 *   - wildcard: translated error message as string
 *     describing a problem with the wildcard
 *   - operator: translated error message as string
 *     describing a problem with the operator
 *   - value: translated error message as string
 *     describing a problem with the value
 *   If no errors detected the array is empty.
 */
function themekey_validator_drupal_path($rule) {
  $errors = themekey_validator_no_whitespace($rule);

  switch ($rule['operator']) {
    case '=':
    case '!':
      if (strpos($rule['value'], '/') === 0) {
        $errors['value'] = t('A drupal path must not start with "/"');
      }
      if (preg_match("@[^/][%#]@", $rule['value'])) {
        $errors['value'] = t('A wildcard could only be used to replace part(s) of the path but not for parts of a word');
      }
      if (strpos($rule['value'], '?') !== FALSE) {
        $errors['value'] = t('Query strings will be striped before a drupal path is precessed. Maybe you want to chain drupal:path and system:query_string or system:query_param (both provided by additional module ThemeKey Properties).');
      }
      break;

    default:
      $errors['operator'] = t('Possible operators are "=" and "!"');
  }

  return $errors;
}

