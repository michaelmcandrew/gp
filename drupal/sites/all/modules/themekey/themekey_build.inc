<?php
// $Id: themekey_build.inc,v 1.13.2.22 2010/07/16 16:41:28 mkalkbrenner Exp $

/**
 * @file
 * The functions in this file are the back end of ThemeKey which should be
 * used only if you configure something but not when ThemeKey switches themes.
 *
 * @author Markus Kalkbrenner | Cocomore AG
 *   @see http://drupal.org/user/124705
 *
 * @author profix898
 *   @see http://drupal.org/user/35192
 */


/**
 * Creates options array for a theme select box.
 *
 * Example:
 *   $form['theme'] = array(
 *     '#type' => 'select',
 *     '#title' => t('Theme'),
 *     '#options' => themekey_theme_options(),
 *   );
 *
 * @param $default
 *   Boolean to indicate if options array should contain
 *   'System default' theme. Default is TRUE.
 * @param $admin
 *   Boolean to indicate if options array should contain
 *   'Administration theme'. Default is FALSE.
 *
 * @return
 *   options array for a theme select box
 */
function themekey_theme_options($default = TRUE, $admin = FALSE) {
  $themes = list_themes();
  ksort($themes);

  $options_themes = array();
  if ($default) {
    $options_themes['default'] = '=> ' . t('System default');
  }
  foreach ($themes as $theme) {
    if ($theme->status || variable_get('themekey_allthemes', 0)) {
      $options_themes[$theme->name] = $theme->info['name'];
    }
  }
  if ($admin) {
    $options_themes['ThemeKeyAdminTheme'] = '=> ' . t('Administration theme');
  }

  return $options_themes;
}


/**
 * Rebuilds all ThemeKey related drupal variables
 * by calling ThemeKey's hooks:
 * - hook_themekey_properties()
 * - hook_themekey_paths()
 */
function themekey_rebuild() {
  // includes all modules in the themekey/modules subfolder (internal modules)
  themekey_scan_modules();

  // Get property definitions (from internal and other modules)
  $properties = module_invoke_all('themekey_properties');

  // Attributes
  $attributes = isset($properties['attributes']) ? $properties['attributes'] : array();
  ksort($attributes);
  variable_set('themekey_attributes', $attributes);

  $property_names = array();
  foreach ($attributes as $property_name => $attribute) {
    if (empty($attribute['static'])) {
      $property_names[$property_name] = $property_name;
    }
  }
  variable_set('themekey_properties', $property_names);

  // Property maps
  $maps = isset($properties['maps']) ? $properties['maps'] : array();
  variable_set('themekey_maps', $maps);

  // Find all previous registered paths
  $obsolete_paths = array();
  $result = db_query('SELECT id, path FROM {themekey_paths}');
  while ($item = db_fetch_array($result)) {
    $obsolete_paths[$item['id']] = $item['path'];
  }
  // Get (and register) paths from themekey modules
  $paths = module_invoke_all('themekey_paths');
  foreach ($paths as $item) {
    themekey_path_set($item);
    if (isset($item['id'])) {
      unset($obsolete_paths[$item['id']]);
    }
  }
  // Unregister obsolete paths
  foreach (array_keys($obsolete_paths) as $id) {
    themekey_path_del($id);
  }
}


/**
 * Scans directory themekey/modules for suitable files
 * which provide ThemeKey properties mapping function and so on
 * and stores the file names for later usage in a drupal variable
 * called 'themekey_modules'.
 *
 * @see themekey_rebuild()
 * @see themekey_invoke_modules()
 */
function themekey_scan_modules() {
  $modules = array();
  $files = file_scan_directory(dirname(__FILE__) . '/modules', '^themekey.[a-z]+.inc$');
  foreach ($files as $file) {
    list( , $module) = explode('.', $file->name);
    if (module_exists($module)) {
      $modules[] = $file->name;
    }
  }

  variable_set('themekey_modules', $modules);
}


/**
 * Named wildcards in ThemeKey rules based on property
 * drupal:path are stored as serialized array in the database.
 *
 * This function deserializes those wildcards and inject them back
 * into the value of the rule. This format is needed by ThemeKey's
 * administartion interface.
 *
 * It's the counterpart of these functions:
 * @see themekey_prepare_path().
 * @see themekey_prepare_custom_path().
 *
 * @see themekey_load_rules()
 *
 * @param $item
 *   reference to an associative array
 *   containing a ThemeKey rule as returned
 *   directly from database
 */
function themekey_complete_path(&$item) {
  $item['wildcards'] = unserialize($item['wildcards']);
  if (count($item['wildcards'])) {
    $parts = explode('/', $item['value'], MENU_MAX_PARTS);
    foreach ($item['wildcards'] as $index => $wildcard) {
      $parts[$index] .= $wildcard;
    }
    $item['value'] = implode('/', $parts);
  }
}


/**
 * Stores ThemeKey paths created by modules
 * via hook_themekey_paths() in database.
 *
 * @see themekey_rebuild()
 *
 * @param $item
 *   reference to an associative array
 *   containing a ThemeKey path structure
 */
function themekey_path_set(&$item) {
  $item['callbacks'] = (isset($item['callbacks']) && !empty($item['callbacks'])) ? $item['callbacks'] : array();

  list($item['fit'], $item['weight'], $item['wildcards']) = themekey_prepare_path($item['path']);

  drupal_write_record('themekey_paths', $item, isset($item['id']) ? 'id' : array());
}


/**
 * Deletes a ThemeKey paths formally created by
 * modules via hook_themekey_paths() from database.
 *
 * @see themekey_rebuild()
 *
 * @param $id
 *   the id of a ThemeKey path
 */
function themekey_path_del($id) {
  db_query('DELETE FROM {themekey_paths} WHERE id = %d', $id);
}


/**
 * Extracts named wildcards from ThemeKey paths returned
 * by modules via hook_themekey_paths() and associates a
 * weight and a fit factor to this path.
 *
 * @param $item
 *   reference to path as string
 *
 * @return
 *   array containing three elements:
 *   - fit as integer
 *   - weight as integer
 *   - named wildcards as array
 */
function themekey_prepare_path(&$path) {
  $fit = 0;
  $weight = 0;
  $wildcards = array();

  $parts = explode('/', $path, MENU_MAX_PARTS);
  $slashes = count($parts) - 1;
  foreach ($parts as $index => $part) {
    if (preg_match('/^(\%|\#)([a-z0-9_:]*)$/', $part, $matches)) {
      $parts[$index] = $matches[1];
      if (!empty($matches[2])) {
        $wildcards[$index] = $matches[2];
      }
      if ($matches[1] == '#') {
        $weight |=  1 << ($slashes - $index);
      }
    }
    else {
      $fit |=  1 << ($slashes - $index);
    }
  }
  $path = implode('/', $parts);

  return array($fit, $weight, $wildcards);
}


/**
 * Extracts named wildcards from paths entered as value
 * in a ThemeKey rule with property drupal:path.
 *
 * @param $path
 *   path as string
 *
 * @return
 *   array containing two elements:
 *   - path with unnamed wildcards
 *   - named wildcards as array
 */
function themekey_prepare_custom_path($path) {
  $wildcards = array();

  $parts = explode('/', $path, MENU_MAX_PARTS);
  foreach ($parts as $index => $part) {
    if (preg_match('/^(\%|\#)([a-z0-9_:]*)$/', $part, $matches)) {
      $parts[$index] = $matches[1];
      if (!empty($matches[2])) {
        $wildcards[$index] = $matches[2];
      }
    }
  }
  $path = implode('/', $parts);

  return array($path, $wildcards);
}


/**
 * Loads all ThemeKey Rules from the database.
 * Therefor it uses a recursion to build the rule chains.
 *
 * @param $parent
 *   id of the parent rule. Default is '0'.
 *   During the recursion this parameter changes.
 *
 * @param $depth
 *   Integer that represents the 'indention'
 *   in current rule chain. Default is '0'.
 *   During the recursion this parameter changes.
 *
 * @return
 *   sorted array containing all ThemeKey rules
 */
function themekey_load_rules($parent = 0, $depth = 0) {
  static $properties = array();
  static $parent_lookups = array();

  if (isset($parent_lookups[$parent])) {
    // prevent endless recursion in case of malformated data in database
    return $properties;
  }

  $query = 'SELECT * FROM {themekey_properties} WHERE parent = %d ORDER BY weight';
  $result = db_query($query, $parent);
  while ($item = db_fetch_array($result)) {
    if ('drupal:path' == $item['property']) {
      themekey_complete_path($item);
    }
    $item['depth'] = $depth;
    $properties[$item['id']] = $item;
    themekey_load_rules($item['id'], $depth + 1);
    $parent_lookups[$item['id']] = TRUE;
  }
  return $properties;
}


/**
 * Stores ThemeKey rules in database.
 * It creates a new dataset or updates an existing one.
 *
 * @param $item
 *   reference to an associative array
 *   containing a ThemeKey rule structure:
 *   - id
 *   - property
 *   - operator
 *   - value
 *   - weight
 *   - theme
 *   - enabled
 *   - wildcards
 *   - parent
 *
 */
function themekey_rule_set(&$item) {
  if ('drupal:path' == $item['property']) {
    list($item['value'], $item['wildcards']) = themekey_prepare_custom_path($item['value']);
  }
  else {
    $item['wildcards'] = array();
  }

  if (empty($item['id'])) {
    // prevent table lock issues with drupal_write_record
    // see http://drupal.org/node/372308#comment-2442664
    drupal_get_schema(FALSE);
    db_lock_table('themekey_properties');
    // new entry should be added at the end of the chain
    $weight = db_result(db_query("SELECT MAX(weight) FROM {themekey_properties}"));
    // if query fails $weight will be FALSE which will cause $item['weight'] to be set to '1'
    $item['weight'] = 1 + $weight;
    drupal_write_record('themekey_properties', $item, array());
    db_unlock_tables();
  }
  else {
    drupal_write_record('themekey_properties', $item, 'id');
  }
}


/**
 * Deletes a ThemeKey rule from database.
 *
 * @param $id
 *   id of the rule to be deleted from database
 */
function themekey_rule_del($id) {
  // lock table to prevent race condition
  db_lock_table('themekey_properties');
  $num_childs = db_result(db_query("SELECT COUNT(*) FROM {themekey_properties} WHERE parent = %d", $id));
  if (FALSE !== $num_childs) {
    if ($num_childs > 0) {
      db_unlock_tables();
      drupal_set_message(t('ThemeKey rule could not be deleted because it has children in the chain'), 'error');
    }
    else {
      $result = db_query('DELETE FROM {themekey_properties} WHERE id = %d', $id);
      db_unlock_tables();
      if (!$result) {
        drupal_set_message(t('Error while deleteing ThemeKey rule'), 'error');
      }
    }
  }
  else {
    db_unlock_tables();
    drupal_set_message(t('Error while deleteing ThemeKey rule'), 'error');
  }
}


/**
 * Loads ThemeKey rule from database.
 *
 * @param $id
 *   id of the rule to be loaded from database
 *
 * @return
 *   the rule as associative array or NULL
 */
function themekey_rule_get($id) {
  if ($result = db_query('SELECT * FROM {themekey_properties} WHERE id = %d', $id)) {
    if ($item = db_fetch_array($result)) {
      if ('drupal:path' == $item['property']) {
        themekey_complete_path($item);
      }
      return $item;
    }
  }

  return NULL;
}


/**
 * Adds or modifies a so called static rule in the
 * database. Static rules could be moved around in the chain
 * and enabled or disabled by the site administrator but the values
 * are immutable. There's one static rule per static property.
 *
 * @param $property
 *   name of the static property as string
 *
 * @param $state
 *   boolean:
 *   - TRUE the rule should be created or updated
 *   - FALSE the rule should be deleted
 */
function themekey_update_static_rule($property, $state) {
  $id = db_result(db_query("SELECT id FROM {themekey_properties} WHERE property = '%s'", $property));
  if ($state) {
    $item = array(
      'property' => $property,
      'operator' => '=',
      'value' => 'static',
      'theme' => 'default',
    );

    if ($id) {
      $item['id'] = $id;
      // leave 'enabled' as it is in database
    }
    else {
      // enable new rule
      $item['enabled'] = 1;
    }

    themekey_rule_set($item);
  }
  elseif ($id) {
    themekey_rule_del($id);
  }
}

