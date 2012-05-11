<?php

/**
 * @file
 * Provides set of validators which can be used to validate
 * ThemeKey Theme Switching Rules.
 * @see themekey_validators.inc
 * @see themekey_admin.inc
 *
 * @author Markus Kalkbrenner | Cocomore AG
 *   @see http://drupal.org/user/124705
 *
 * @author Carsten Müller | Cocomore AG
 *   @see http://drupal.org/user/124707
 */


/**
 * Validates a Theme Switching Rule.
 * Allowed Operators: any
 * Allowed values:
 * - valid regular expression if operator is "~"
 * - a valid IPv4 address like "123.123.123.123" if operator is "=" or "!"
 * - fragment of an IPv4 address which contains at least one digit
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
 *   If no were errors detected, the array is empty.
 */
function themekey_properties_validator_ip_address($rule) {
  $errors = array();

  switch ($rule['operator']) {
    case '=':
    case '!':
      // TODO add for support IPv6
      // TODO improve regex for IPv4
      if (!preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $rule['value'])) {
        $errors['value'] = t('Not a valid IPv4 address. Format should look like "123.123.123.123"');
      }
      break;

    case '~':
      // CHECK THE REGULAR EXPRESSION
      $errors = themekey_validator_regex($rule);
      break;

    default:
      // TODO add for support IPv6
      // TODO improve regex for IPv4
      if (!preg_match("/^[1-2][0-9\.]*$/", $rule['value'])) {
        $errors['value'] = t("Value isn't suitable for checks against dates formatted like \"123.123.123.123\"");
      }
      break;
  }

  return $errors;
}


/**
 * Validates a Theme Switching Rule.
 * Allowed Operators: "=", "!", "~"
 * Allowed values:
 * - valid regular expression if operator is "~"
 * - a valid Drupal role if operator is "=" or "!"
 *
 *
 * @param $rule
 *   A Theme Switching Rule as associative array:
 *   - property: ThemeKey property as string (e.g., "drupal:path")
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
function themekey_validator_role($rule) {
  $errors = array();

  switch ($rule['operator']) {
    case '=':
    case '!':
      $roles = array();
      $result = db_query('SELECT * FROM {role} ORDER BY name');
      while ($role = db_fetch_object($result)) {
        $roles[$role->rid] = $role->name;
      }

      if (!in_array($rule['value'], $roles)) {
        $errors['value'] = t('The entered user role, %value, is not valid. Possible roles are "%roles".', array('%value' => $rule['value'], '%roles' => implode('", "', $roles)));
      }
      break;

    case '~':
      $errors = themekey_validator_regex($rule);
      break;

    default:
      $errors['operator'] = t('Possible operators are "=", "!", "~"');
      break;

  }

  return $errors;
}


/**
 * Validates a Theme Switching Rule.
 * Allowed Operators: "=", "!"
 * Allowed values:
 * - a three letter day String if operator is "=" or "!"
 *
 *
 * @param $rule
 *   A Theme Switching Rule as associative array:
 *   - property: ThemeKey property as string (e.g. "drupal:path")
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
function themekey_validator_day_of_week($rule) {
  static $days_of_week = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');

  $errors = array();

  switch ($rule['operator']) {
    case '=':
    case '!':
      if (!in_array($rule['value'], $days_of_week)) {
        $errors['value'] = t('The day %value is not valid. Possible days are "%roles".', array('%value' => $rule['value'], '%roles' => implode('", "', $days_of_week)));
      }
      break;

    default:
      $errors['operator'] = t('Possible operators are "=" and "!"');
      break;

  }

  return $errors;
}