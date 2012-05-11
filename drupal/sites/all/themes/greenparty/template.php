<?php

/**
 * Sets the body-tag class attribute.
 *
 * Adds 'sidebar-left', 'sidebar-right' or 'sidebars' classes as needed.
 */
function greenparty_body_class($left, $right) {
  $class = array();

  // ST: designs are for 3 column layout only. 
  //if ($left != '' && $right != '') {
    $class[] = 'sidebars';
  /*}
  elseif ($left != '') {
    $class[] = 'sidebar-left';
  }
  elseif ($right != '') {
    $class[] = 'sidebar-right';
  }*/

  if (arg(0) == 'admin') {
    $class[] = 'admin';
  }

  if ($class) {
    print ' class="' . implode(' ', $class) . '"';
  }
}

/**
 * Return a themed breadcrumb trail.
 *
 * @param $breadcrumb
 *   An array containing the breadcrumb links.
 * @return a string containing the breadcrumb output.
 */
function phptemplate_breadcrumb($breadcrumb) {
  if (!empty($breadcrumb)) {
// uncomment the next line to enable current page in the breadcrumb trail
//    $breadcrumb[] = drupal_get_title();
    return '<div class="breadcrumb">'. implode(' » ', $breadcrumb) .'</div>';
  }
}

/**
 * Allow themable wrapping of all comments.
 */
function greenparty_comment_wrapper($content, $node) {
  if (!$content || $node->type == 'forum') {
    return '<div id="comments">'. $content .'</div>';
  }
  else {
    return '<div id="comments"><h2 class="comments">'. t('Comments') .'</h2>'. $content .'</div>';
  }
}

/**
 * Override or insert PHPTemplate variables into the templates.
 */
function greenparty_preprocess_page(&$vars) {
  $vars['tabs2'] = menu_secondary_local_tasks();
  
	if (isset ($vars['node']) && arg(2) != 'edit' && $vars['node']->type == "news") {
		$vars['template_files'] = array();
		$vars['template_files'][] = 'page-news';
	}
	if (isset ($vars['node']) && arg(2) != 'edit' && $vars['node']->type == "events") {
		$vars['template_files'] = array();
		$vars['template_files'][] = 'page-events';
	}
	if (isset ($vars['node']) && arg(2) != 'edit' && $vars['node']->type == "resource") {
		$vars['template_files'] = array();
		$vars['template_files'][] = 'page-resources';
	}
	if (isset ($vars['node']) && arg(2) != 'edit' && $vars['node']->type == "forum") {
		$vars['template_files'] = array();
		$vars['template_files'][] = 'page-forums';
	}

}

/**
 * Returns the rendered local tasks. The default implementation renders
 * them as tabs. Overridden to split the secondary tasks.
 *
 * @ingroup themeable
 */
function phptemplate_menu_local_tasks() {
  return menu_primary_local_tasks();
}

function greenparty_comment_submitted($comment) {
  return t('by <strong>!username</strong> | !datetime',
    array(
      '!username' => theme('username', $comment),
      '!datetime' => format_date($comment->timestamp)
    ));
}

function phptemplate_node_submitted($node) {
  return t('!datetime | by <strong>!username</strong>',
    array(
      '!username' => theme('username', $node),
      '!datetime' => format_date($node->created),
    ));
}

/**
 * Generates IE CSS links.
 */
function greenparty_get_ie_styles() {
  $iecss = '<link type="text/css" rel="stylesheet" media="all" href="'. base_path() . path_to_theme() .'/ie.css" />';
  return $iecss;
}

function greenparty_get_ie6_styles() {
  $iecss = '<link type="text/css" rel="stylesheet" media="all" href="'. base_path() . path_to_theme() .'/ie6.css" />';
  return $iecss;
}

/**
 * Adds even and odd classes to <li> tags in ul.menu lists
 */ 
function phptemplate_menu_item($link, $has_children, $menu = '', $in_active_trail = FALSE, $extra_class = NULL) {
  static $zebra = FALSE;
  $zebra = !$zebra;
  $class = ($menu ? 'expanded' : ($has_children ? 'collapsed' : 'leaf'));
  if (!empty($extra_class)) {
    $class .= ' '. $extra_class;
  }
  if ($in_active_trail) {
    $class .= ' active-trail';
  }
  if ($zebra) {
    $class .= ' even';
  }
  else {
    $class .= ' odd';
  }
  return '<li class="'. $class .'">'. $link . $menu ."</li>\n";
}	

?>
