<?php
// $Id: themekey_base.inc,v 1.15.2.25 2010/07/30 15:05:07 mkalkbrenner Exp $

/**
 * @file
 * The functions in this file are the back end of ThemeKey.
 *
 * @author Markus Kalkbrenner | Cocomore AG
 *   @see http://drupal.org/user/124705
 *
 * @author profix898
 *   @see http://drupal.org/user/35192
 */


/**
 * Invokes a hook on all modules stored in the global
 * variable 'themekey_modules'
 *
 * @param $hook
 *   name of the hook as string
 *
 * @return
 *   mixed output of all hooks
 */
function themekey_invoke_modules($hook) {
  $return = array();
  foreach (variable_get('themekey_modules', array('themekey.node')) as $module) {
    if (is_readable(drupal_get_path('module', 'themekey') .'/modules/'. $module .'.inc')) {
      require_once(drupal_get_path('module', 'themekey') .'/modules/'. $module .'.inc');
    }
    $function = str_replace('.', '_', $module) .'_'. $hook;
    if (function_exists($function)) {
      $return = array_merge_recursive($return, $function());
    }
  }

  return $return;
}


/**
 * Detects if a ThemeKey rule for property drupal:path
 * matches to the current page request.
 *
 * @param $condition
 *   ThemeKey rule as associative array:
 *   - property (must be "drupal:path")
 *   - operator
 *   - value
 *
 * @param $parameters
 *   reference to an array containing all
 *   ThemeKey properties an their values
 *
 * @return
 *   boolean
 */
function themekey_match_path($condition, &$parameters) {
  static $alias_uri = '';

  if ('drupal:path' != $condition['property']) {
    return FALSE;
  }

  if (!variable_get('themekey_path_case_sensitive', 0)) {
    $condition['value'] = strtolower($condition['value']);
  }

  $condition_parts = explode('/', $condition['value']);

  $get_q = themekey_get_q();
  $get_q_parts = explode('/', $get_q);

  $wildcards = themekey_match_path_parts($get_q_parts, $condition_parts);
  if (FALSE === $wildcards && module_exists('path')) {
    if (empty($alias_uri)) {
      // Derive path from request_uri
      $offset = (variable_get('clean_url', 0) ? 0 : 3) + strlen(base_path());
      $alias_uri = substr(request_uri(), $offset);
      if (strpos($alias_uri, '?') !== FALSE) {
        // Remove query string from request uri
        $alias_uri_parts = explode('?', $alias_uri);
        $alias_uri = $alias_uri_parts[0];
      }
      // For $alias_uri != $_GET['q'] the page was requested using an
      // aliased path, otherwise get the path alias internally
      if ($alias_uri == $get_q) {
        $alias_uri = drupal_get_path_alias($get_q);
      }
    }

    if ($alias_uri != $get_q) {
      $alias_parts = explode('/', $alias_uri);

      $wildcards = themekey_match_path_parts($alias_parts, $condition_parts);
    }
  }

  if (is_array($wildcards)) {
    $parameters['internal:temp_wildcards'] = $wildcards;
    return TRUE;
  }

  $parameters['internal:temp_wildcards'] = array();
  return FALSE;
}


/**
 * Compares if two paths are identical accepting
 * wildcards "%" and "#".
 *
 * @param $path_parts
 *   array containg single parts of a path
 *
 * @param $condition_parts
 *   array containg single parts of a path
 *   whereas a part could be a wildcard
 *
 * @return
 *   FALSE if paths doesn't match or array containing
 *   the wildcards if paths matched
 */
function themekey_match_path_parts($path_parts, $condition_parts) {
  if (count($path_parts) < count($condition_parts)) {
    return FALSE;
  }

  $wildcards = array();

  foreach ($condition_parts as $key => $part) {
    if ('#' == $part) {
      if (ctype_digit($path_parts[$key])) {
        $wildcards[$key] = $path_parts[$key];
        continue;
      }
      return FALSE;
    }

    if ('%' == $part) {
      if (isset($path_parts[$key])) {
        $wildcards[$key] = $path_parts[$key];
        continue;
      }
      return FALSE;
    }

    if ($part == $path_parts[$key]) {
      continue;
    }

    return FALSE;
  }

  return $wildcards;
}



/**
 * Assigns global parameters' values to ThemeKey properties.
 * Therefor it calls hook_themekey_global()
 *
 * @return
 *   associative array containing some
 *   ThemeKey properties and their values
 */
function themekey_get_global_parameters() {
  static $global_parameters = NULL;

  if (is_null($global_parameters)) {
    $global_parameters = module_invoke_all('themekey_global');

    // TODO don't store paths in database. maybe use a variable.
    if ($result = db_query('SELECT * FROM {themekey_paths} ORDER BY fit DESC, weight DESC')) {
      $get_q_parts = explode('/', themekey_get_q());
      while ($item = db_fetch_array($result)) {
        $item_parts = explode('/', $item['path']);
        $wildcards = themekey_match_path_parts($get_q_parts, $item_parts);
        if (!empty($wildcards)) {
          $item_wildcards = unserialize($item['wildcards']);
          foreach ($item_wildcards as $index => $item_wildcard) {
            $global_parameters[$item_wildcard] = $wildcards[$index];
          }

          $callbacks = unserialize($item['callbacks']);
          if (count($callbacks)) {
            foreach ($callbacks as $callback) {
              $callback($item, $global_parameters);
            }
          }
        }
      }
    }
  }

  return $global_parameters;
}

/**
 * This function steps through
 * the rule chain and returns a theme.
 *
 * @return
 *   a theme as string or NULL
 */
function themekey_match_rules() {
  $parameters = themekey_get_global_parameters();

  $theme = themekey_match_rule_childs($parameters);
  if (FALSE === $theme || TRUE === $theme) {
    $theme = NULL;
  }
  elseif ('ThemeKeyAdminTheme' === $theme) {
    $theme = variable_get('admin_theme', '0');
  }

  return $theme;
}

/**
 * Helper function of @see themekey_match_rules()
 *
 * @param $parameters
 *   reference to an array containing all
 *   ThemeKey properties an their values
 *
 * @param $parent
 *   id of parent rule
 *
 * @return
 *   NULL in case of an error
 *   string name of the theme if child rule matched
 *   FALSE if no child rule matches
 *   TRUE if no child properties in the chain
 */
function themekey_match_rule_childs(&$parameters, $parent = 0) {
  static $child_lookups = array();

  if (isset($child_lookups[$parent])) {
    // prevent endless recursion in case of malformated data in database
    return $child_lookups[$parent];
  }

  if ($result = db_query("SELECT * FROM {themekey_properties} WHERE enabled = 1 AND parent = %d AND value <> '' ORDER BY weight", $parent)) {
    $num_childs = 0;

    while ($item = db_fetch_array($result)) {
      $num_childs++;
      if (themekey_match_condition($item, $parameters)) {
        if ('drupal:path' == $item['property'] && !empty($parameters['internal:temp_wildcards'])) {
          $wildcards = unserialize($item['wildcards']);
          if (!empty($wildcards)) {
            foreach ($wildcards as $index => $wildcard) {
              $parameters[$wildcard] = $parameters['internal:temp_wildcards'][$index];
            }
          }
        }

        if (variable_get('themekey_debug_trace_rule_switching', FALSE)) {
          themekey_set_debug_message('Match: %rule', array('%rule' => themekey_format_rule_as_string($item['id'])));
        }

        $child_lookups[$parent] = themekey_match_rule_childs($parameters, $item['id']);

        if (FALSE === $child_lookups[$parent]) {
          continue;
        }
        elseif (TRUE === $child_lookups[$parent]) {
          if (!themekey_check_theme_enabled($item['theme'])) {
            if (variable_get('themekey_debug_trace_rule_switching', FALSE)) {
              themekey_set_debug_message('Theme disabled: %theme', array('%theme' => $item['theme']));
            }
            continue;
          }

          $child_lookups[$parent] = $item['theme'];
        }

        // return own theme or theme from child property or NULL in case of a database error
        return $child_lookups[$parent];
      }
      elseif (variable_get('themekey_debug_trace_rule_switching', FALSE)) {
        themekey_set_debug_message('No match: %rule', array('%rule' => themekey_format_rule_as_string($item['id'])));
      }
    }

    $child_lookups[$parent] = (!$num_childs);
    return $child_lookups[$parent];
  }

  $child_lookups[$parent] = NULL;
  return $child_lookups[$parent];
}


/**
 * Detects if a ThemeKey rule matches to the current
 * page request.
 *
 * @param $condition
 *   ThemeKey rule as associative array:
 *   - property
 *   - operator
 *   - value
 *
 * @param $parameters
 *   reference to an array containing all
 *   ThemeKey properties an their values
 *
 * @return
 *   boolean
 */
function themekey_match_condition($condition, &$parameters) {
  global $custom_theme;

  if (is_array($condition) && count($condition)) {
    // Default operator is 'equal'
    if (empty($condition['operator'])) {
      $condition['operator'] = '=';
    }

    if ('drupal:path' == $condition['property']) {
      return themekey_match_path($condition, $parameters);
    }

    $value = themekey_property_value($parameters, $condition['property']);

    if ('static' === $value && $custom_theme) {
      if (variable_get('themekey_debug_trace_rule_switching', FALSE)) {
        themekey_set_debug_message('A static rule set custom theme %custom_theme', array('%custom_theme' => $custom_theme));
      }
      return TRUE;
    }

    if (!is_array($value)) {
      $value = array($value);
    }

    if (!empty($value)) {
      foreach ($value as $single_value) {
        if (!is_null($single_value)) {
          // Supported operators for condition check:
          // smaller ('<'), greater ('>'), equal ('='), not equal ('!'), regex match ('~')
          if ($condition['operator'] == '<' && $single_value >= $condition['value']) {
            return FALSE;
          }
          elseif ($condition['operator'] == '>' && $single_value <= $condition['value']) {
            return FALSE;
          }
          elseif ($condition['operator'] == '<=' && $single_value > $condition['value']) {
            return FALSE;
          }
          elseif ($condition['operator'] == '>=' && $single_value < $condition['value']) {
            return FALSE;
          }
          elseif ($condition['operator'] == '=' && $single_value == $condition['value']) {
            return TRUE;
          }
          elseif ($condition['operator'] == '!' && $single_value == $condition['value']) {
            return FALSE;
          }
          elseif ($condition['operator'] == '~' && preg_match($condition['value'], $single_value)) {
            return TRUE;
          }
        }
        else {
          // value is NULL
          return FALSE;
        }
      }

      if ($condition['operator'] == '=' || $condition['operator'] == '~') {
        // no value matched
        return FALSE;
      }
      else {
        // condition matched for all values
        return TRUE;
      }
    }
    else {
      // value array is empty => value is NULL
      return FALSE;
    }
  }
  else {
    trigger_error(t('Function themekey_match_condition() called with illegal parameters'), E_USER_ERROR);
  }
}


/**
 * Detects if a ThemeKey property's value for the current
 * page request.
 *
 * @param $parameters
 *   reference to an array containing all
 *   ThemeKey properties an their values
 *
 * @param $property
 *   the name of the property as string
 *
 * @return
 *   The value of the property:
 *   - string i it's a single value
 *   - array of strings if there're multiple values
 *   - NULL if no value
 */
function themekey_property_value(&$parameters, $property) {

  // TODO Warning if property is not part of variable_get('themekey_attributes')

  // Property value is available directly
  if (isset($parameters[$property])) {
    return $parameters[$property];
  }

  $parameters[$property] = NULL;

  $src_candidates = array();
  $maps = variable_get('themekey_maps', array());

  foreach ($maps as $pos => $map) {
    if ($map['dst'] == $property) {
      if (!empty($parameters[$map['src']])) {
        $map_func = $map['callback'];
        $parameters[$property] = $map_func($parameters[$map['src']], $parameters);
        break;
      }
      $src_candidates[$pos] = $map['src'];
    }
  }

  if (is_null($parameters[$property]) && !empty($src_candidates)) {
    foreach ($src_candidates as $pos => $src) {
      $return = themekey_property_value($parameters, $src);
      if ($return) {
        $map_func = $maps[$pos]['callback'];
        $parameters[$property] = $map_func($return, $parameters);
        break;
      }
    }
  }

  return $parameters[$property];
}


/**
 * Checks if a theme is enabled and fires warning messages
 * to the site's administrator
 *
 * @param $theme
 *   name of the theme as string
 *
 * @param $settings_page
 *   boolean that indicates if the function
 *   is called from ThemeKey's administration
 *   backend which causes a different message
 *
 * @return
 *   TRUE if the theme is enabled, otherwise FALSE
 */
function themekey_check_theme_enabled($theme, $settings_page = FALSE) {
  static $themes_enabled = array();
  static $warned = FALSE;
  static $displayed_error = FALSE;

  if (!$theme || 'default' == $theme) {
    return TRUE;
  }

  if (empty($themes_enabled)) {
    if ($result = db_query("SELECT name FROM {system} WHERE type = 'theme' AND status = 1;")) {
      while ($row = db_fetch_array($result)) {
        $themes_enabled[] = $row['name'];
      }
    }
  }

  if (in_array($theme, $themes_enabled)) {
    return TRUE;
  }
  elseif ('ThemeKeyAdminTheme' == $theme && variable_get('admin_theme', '0') && in_array(variable_get('admin_theme', '0'), $themes_enabled)) {
    return TRUE;
  }

  if ($settings_page) {
    if (!$displayed_error) {
      drupal_set_message(t("Your current configuration of theme rules uses at least one theme that is not enabled. Nevertheless this configuration is stored but affected rules won't be applied until the targeted theme will be enabled at !build_themes.",
        array('!build_themes' => l('admin/build/themes', 'admin/build/themes'))), 'error');
      $displayed_error = TRUE;
    }
  }
  else {
    global $user;

    if (!$warned && 1 == $user->uid) {
      themekey_set_debug_message('A matching Theme Switching Rule to select theme %theme was not applied because this theme is disabled. You can enable this theme at !build_themes or remove this Theme Switching Rule at !themekey_properties or edit current node if the theme was selected using ThemeKey UI.',
        array('%theme' => $theme, '!build_themes' => l('admin/build/themes', 'admin/build/themes'), '!themekey_properties' => l('admin/settings/themekey/properties', 'admin/settings/themekey/properties')));
      $warned = TRUE;
    }
  }

  return FALSE;
}

function themekey_format_rule_as_string($themekey_property_id) {
    // fallback title
  $title = $themekey_property_id;

  $item = themekey_rule_get($themekey_property_id);
  if (!empty($item)) {
    $properties = variable_get('themekey_properties', array());
    if (!in_array($item['property'], $properties)) {
      $item['wildcard'] = $item['property'];
      $item['property'] = 'drupal:path:wildcard';
    }

    $title = '"' . $item['property'] . ' ';
    if (!empty($item['wildcard'])) {
      $title .= $item['wildcard'] . ' ';
    }
    $title .= $item['operator'] . ' ' . $item['value'] . ' >>> ' . $item['theme'] . '"';
  }

  return $title;
}