<?php
// $Id: themekey.comment.inc,v 1.4.2.3 2010/01/04 15:00:42 mkalkbrenner Exp $

/**
 * @file
 * Provides some comment attributes as ThemeKey properties.
 */


/**
 * Implements hook_themekey_properties().
 *
 * Provides additional properties for module ThemeKey:
 * - comment:cid
 *
 * @return
 *   array of themekey properties and mapping functions
 */
function themekey_comment_themekey_properties() {
  // Attributes for properties
  $attributes = array();
  $attributes['comment:cid'] = array(
    'description' => t('Comment: ID - The id of a comment (cid). See !link for your published comments or !link_unpublished for comments awaiting approval',
      array('!link' => l('admin/content/comment', 'admin/content/comment'), '!link_unpublished' => l('admin/content/comment/approval', 'admin/content/comment/approval'))),
    'validator' => 'themekey_validator_ctype_digit',
  );

  // Mapping functions
  $maps = array();
  $maps[] = array('src'       => 'comment:cid',
                  'dst'       => 'node:nid',
                  'callback'  => 'themekey_comment_cid2nid');

  return array('attributes' => $attributes, 'maps' => $maps);
}


/**
 * Implements hook_themekey_paths().
 */
function themekey_comment_themekey_paths() {
  $paths = array();
  $paths[] = array('path' => 'comment/reply/#node:nid');
  $paths[] = array('path' => 'comment/reply/#node:nid/#comment:cid');
  $paths[] = array('path' => 'comment/edit/#comment:cid');
  $paths[] = array('path' => 'comment/delete/#comment:cid');

  return $paths;
}


/**
 * ThemeKey mapping function to set a
 * ThemeKey property's value (destination)
 * with the aid of another ThemeKey property (source).
 *
 * src: comment:cid
 * dst: node:nid
 *
 * @param $nid
 *   a comment id
 *
 * @return
 *   a node id
 *   or NULL if no value could be mapped
 */
function themekey_comment_cid2nid($cid) {
  $nid = db_result(db_query('SELECT nid FROM {comments} WHERE cid = %d', $cid));
  return $nid ? $nid : NULL;
}
