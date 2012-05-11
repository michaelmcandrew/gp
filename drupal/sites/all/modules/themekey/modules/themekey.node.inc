<?php
// $Id: themekey.node.inc,v 1.9.2.10 2010/03/18 11:38:53 mkalkbrenner Exp $

/**
 * @file
 * Provides some node attributes as ThemeKey properties.
 */


/**
 * Implements hook_themekey_properties().
 *
 * Provides additional properties for module ThemeKey:
 * - node:changed
 * - node:created
 * - node:changed_date_time
 * - node:created_date_time
 * - node:changed_date
 * - node:created_date
 * - node:language
 * - node:nid
 * - node:promote
 * - node:sticky
 * - node:type
 * - node:uid
 * - node:title
 *
 * @return
 *   array of themekey properties and mapping functions
 */
function themekey_node_themekey_properties() {
  // Attributes for properties
  $attributes = array();
  $attributes['node:changed'] = array(
    'description' => t('Node: Changed date - The date when the node was last edited/updated, formatted as unix timestamp like "1248873565".'),
    'validator' => 'themekey_validator_ctype_digit',
  );
  $attributes['node:created'] = array(
    'description' => t('Node: Created date - The date when the node was created, formatted as unix timestamp like "1248873565".'),
    'validator' => 'themekey_validator_ctype_digit',
  );
  $attributes['node:changed_date_time'] = array(
    'description' => t('Node: Changed date - The date including the time when the node was last edited/updated, formatted like "2009-12-24 18:13:24"'),
    'validator' => 'themekey_validator_date_time',
  );
  $attributes['node:created_date_time'] = array(
    'description' => t('Node: Created date - The date including the time when the node was created, formatted like "2009-12-24 18:13:24"'),
    'validator' => 'themekey_validator_date_time',
  );
  $attributes['node:changed_date'] = array(
    'description' => t('Node: Changed date - The date without the time when the node was last edited/updated, formatted like "2009-12-24"'),
    'validator' => 'themekey_validator_date',
  );
  $attributes['node:created_date'] = array(
    'description' => t('Node: Created date - The date without the time when the node was created, formatted like "2009-12-24"'),
    'validator' => 'themekey_validator_date',
  );
  $attributes['node:language'] = array(
    'description' => t('Node: Language - The code of the selected language of a node (formatted like "en" or "de") or "neutral". See !link for the codes of your enabled languages',
      array('!link' => l('admin/settings/language', 'admin/settings/language'))),
    'validator' => 'themekey_validator_language',
  );
  $attributes['node:nid'] = array(
    'description' => t('Node: ID - The id of a node (nid), can be found in the URL of the node, "node/23" or "node/23/edit" (23 = nid)'),
    'validator' => 'themekey_validator_ctype_digit',
  );
  $attributes['node:promote'] = array(
    'description' => t('Node: Promoted - If the node is promoted to the front page. Possible values are "0" for true and "1" for false.'),
    'validator' => 'themekey_validator_nummeric_boolean',
  );
  $attributes['node:sticky'] = array(
    'description' => t('Node: Sticky - If the node is set "Sticky at top of lists". Possible values are "0" for true and "1" for false.'),
    'validator' => 'themekey_validator_nummeric_boolean',
  );
  $attributes['node:type'] = array(
    'description' => t('Node: Type - The machine readable content type of the node. See !link for your content types (use column "Type"). Drupal default types are "page" and "story".',
      array('!link' => l('admin/content/types', 'admin/content/types'))),
    'validator' => 'themekey_validator_node_type',
  );
  $attributes['node:uid'] = array(
    'description' => t('Node: User ID - The user id (uid) of the author of the node. The user id can be found in the URL of the users profile page, "user/23" or "user/23/edit" (23 = uid). See !link for your users.',
      array('!link' => l('admin/user/user/list', 'admin/user/user/list'))),
    'validator' => 'themekey_validator_ctype_digit',
  );
  $attributes['node:title'] = array(
    'description' => t('Node: Title - The title of the node.'),
    'validator' => '',
  );

  // Mapping functions
  $maps = array();
  $maps[] = array('src'       => 'node:nid',
                  'dst'       => 'node:changed',
                  'callback'  => 'themekey_node_nid2changed');
  $maps[] = array('src'       => 'node:nid',
                  'dst'       => 'node:created',
                  'callback'  => 'themekey_node_nid2created');
  $maps[] = array('src'       => 'node:changed',
                  'dst'       => 'node:changed_date_time',
                  'callback'  => 'themekey_node_timestamp2datetime');
  $maps[] = array('src'       => 'node:created',
                  'dst'       => 'node:created_date_time',
                  'callback'  => 'themekey_node_timestamp2datetime');
  $maps[] = array('src'       => 'node:changed',
                  'dst'       => 'node:changed_date',
                  'callback'  => 'themekey_node_timestamp2date');
  $maps[] = array('src'       => 'node:created',
                  'dst'       => 'node:created_date',
                  'callback'  => 'themekey_node_timestamp2date');
  $maps[] = array('src'       => 'node:nid',
                  'dst'       => 'node:language',
                  'callback'  => 'themekey_node_nid2language');
  $maps[] = array('src'       => 'node:nid',
                  'dst'       => 'node:promote',
                  'callback'  => 'themekey_node_nid2promote');
  $maps[] = array('src'       => 'node:nid',
                  'dst'       => 'node:sticky',
                  'callback'  => 'themekey_node_nid2sticky');
  $maps[] = array('src'       => 'node:nid',
                  'dst'       => 'node:type',
                  'callback'  => 'themekey_node_nid2type');
  $maps[] = array('src'       => 'node:nid',
                  'dst'       => 'node:uid',
                  'callback'  => 'themekey_node_nid2uid');
  $maps[] = array('src'       => 'node:nid',
                  'dst'       => 'node:title',
                  'callback'  => 'themekey_node_nid2title');
  $maps[] = array('src'       => 'drupal:get_q',
                  'dst'       => 'node:type',
                  'callback'  => 'themekey_node_get_q2type');

  return array('attributes' => $attributes, 'maps' => $maps);
}


/**
 * Implements hook_themekey_paths().
 */
function themekey_node_themekey_paths() {
  $paths = array();
  $paths[] = array('path' => 'node/#node:nid');

  return $paths;
}


/**
 * Helper function that loads a node and returns the
 * value of a node's property.
 *
 * @param $nid
 *   a node id
 *
 * @param $property
 *   name of a nodes attribute as string
 *
 * @return
 *   the value of the property or NULL
 */
function themekey_node_get_simple_node_property($nid, $property) {
  static $nodes = array();

  if (!isset($nodes[$nid])) {
    // node_load() must not be called from hook_init(). Therefor we have to execute SQL here
    $nodes[$nid] = db_fetch_array(db_query('SELECT * FROM {node} WHERE nid = %d', $nid));
  }

  if (isset($nodes[$nid][$property])) {
    return $nodes[$nid][$property];
  }
  else {
    return NULL;
  }
}


/**
 * ThemeKey mapping function to set a
 * ThemeKey property's value (destination)
 * with the aid of another ThemeKey property (source).
 *
 * src: node:nid
 * dst: node:changed
 *
 * @param $nid
 *   a node id
 *
 * @return
 *   string
 *   or NULL if no value could be mapped
 */
function themekey_node_nid2changed($nid) {
  return themekey_node_get_simple_node_property($nid, 'changed');
}


/**
 * ThemeKey mapping function to set a
 * ThemeKey property's value (destination)
 * with the aid of another ThemeKey property (source).
 *
 * src: node:nid
 * dst: node:created
 *
 * @param $nid
 *   a node id
 *
 * @return
 *   string
 *   or NULL if no value could be mapped
 */
function themekey_node_nid2created($nid) {
  return themekey_node_get_simple_node_property($nid, 'created');
}


/**
 * ThemeKey mapping function to set a
 * ThemeKey property's value (destination)
 * with the aid of another ThemeKey property (source).
 *
 * src: node:nid
 * dst: node:language
 *
 * @param $nid
 *   a node id
 *
 * @return
 *   string
 *   or NULL if no value could be mapped
 */
function themekey_node_nid2language($nid) {
  $language = themekey_node_get_simple_node_property($nid, 'language');
  if (empty($language)) {
    $language = 'neutral';
  }
  return $language;
}


/**
 * ThemeKey mapping function to set a
 * ThemeKey property's value (destination)
 * with the aid of another ThemeKey property (source).
 *
 * src: node:nid
 * dst: node:promote
 *
 * @param $nid
 *   a node id
 *
 * @return
 *   string
 *   or NULL if no value could be mapped
 */
function themekey_node_nid2promote($nid) {
  return themekey_node_get_simple_node_property($nid, 'promote');
}


/**
 * ThemeKey mapping function to set a
 * ThemeKey property's value (destination)
 * with the aid of another ThemeKey property (source).
 *
 * src: node:nid
 * dst: node:sticky
 *
 * @param $nid
 *   a node id
 *
 * @return
 *   string
 *   or NULL if no value could be mapped
 */
function themekey_node_nid2sticky($nid) {
  return themekey_node_get_simple_node_property($nid, 'sticky');
}


/**
 * ThemeKey mapping function to set a
 * ThemeKey property's value (destination)
 * with the aid of another ThemeKey property (source).
 *
 * src: node:nid
 * dst: node:type
 *
 * @param $nid
 *   a node id
 *
 * @return
 *   string
 *   or NULL if no value could be mapped
 */
function themekey_node_nid2type($nid) {
  return themekey_node_get_simple_node_property($nid, 'type');
}


/**
 * ThemeKey mapping function to set a
 * ThemeKey property's value (destination)
 * with the aid of another ThemeKey property (source).
 *
 * src: node:nid
 * dst: node:uid
 *
 * @param $nid
 *   a node id
 *
 * @return
 *   string
 *   or NULL if no value could be mapped
 */
function themekey_node_nid2uid($nid) {
  return themekey_node_get_simple_node_property($nid, 'uid');
}


/**
 * ThemeKey mapping function to set a
 * ThemeKey property's value (destination)
 * with the aid of another ThemeKey property (source).
 *
 * src: node:nid
 * dst: node:title
 *
 * @param $nid
 *   a node id
 *
 * @return
 *   string
 *   or NULL if no value could be mapped
 */
function themekey_node_nid2title($nid) {
  return themekey_node_get_simple_node_property($nid, 'title');
}


/**
 * ThemeKey mapping function to set a
 * ThemeKey property's value (destination)
 * with the aid of another ThemeKey property (source).
 *
 * src: any timestamp
 * dst: time formatted
 *
 * @param $timestamp
 *   a unix timestamp
 *
 * @return
 *   string
 *   or NULL if no value could be mapped
 */
function themekey_node_timestamp2datetime($timestamp) {
  return date('Y-m-d H:i:s', $timestamp);
}


/**
 * ThemeKey mapping function to set a
 * ThemeKey property's value (destination)
 * with the aid of another ThemeKey property (source).
 *
 * src: any timestamp
 * dst: time formatted
 *
 * @param $timestamp
 *   a unix timestamp
 *
 * @return
 *   string
 *   or NULL if no value could be mapped
 */
function themekey_node_timestamp2date($timestamp) {
  return date('Y-m-d', $timestamp);
}


/**
 * ThemeKey mapping function to set a
 * ThemeKey property's value (destination)
 * with the aid of another ThemeKey property (source).
 *
 * src: drupal path
 * dst: node type
 *
 * @param $patch
 *   a drupal path
 *
 * @return
 *   string
 *   or NULL if no value could be mapped
 */
function themekey_node_get_q2type($q) {
  if (strpos($q, 'node/add/') === 0) {
    return str_replace('-', '_', str_replace('node/add/', '', $q));
  }
  return NULL;
}


/**
 * This function isn't needed anymore and has been removed with ThemeKey 6.x-2.0-beta2.
 * But if it's missing a fatal error occurs if you upgrade from any version before
 * 6.x-2.0-beta2 until you called update.php. That should be avoided.
 * So this function is available again and does nothing else than reminding the
 * administrator to call update.php
 *
 * see http://drupal.org/node/662786
 *
 * @param unknown_type $item
 * @param unknown_type $parameters
 * @return unknown_type
 */
function _themekey_node_callback(&$item, &$parameters) {
  global $user;

  if (1 == $user->uid) {
    drupal_set_message(t('You uploaded a new version of ThemeKey. Please !link your site!', array('!link' => l(t('update'), '/update.php'))));
  }
}
