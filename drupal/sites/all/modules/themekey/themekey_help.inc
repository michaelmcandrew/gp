<?php
// $Id: themekey_help.inc,v 1.1.2.3 2010/07/21 10:00:28 mkalkbrenner Exp $

/**
 * @file
 */


/**
 * Uses drupal's form builder to format ThemeKey's help tutorials
 *
 * @see themekey_help()
 */
function themekey_help_tutorials_form($collapsed = TRUE) {
  $form['themekey_help_tutorials'] = array(
    '#type' => 'fieldset',
    '#title' => t('Tutorials'),
    '#collapsible' => TRUE,
    '#collapsed' => $collapsed,
  );

  $form['themekey_help_tutorials']['Rule Chaining: Using a special theme for content creation depending on user roles'] = array(
    '#type' => 'fieldset',
    '#title' => t('Rule Chaining: Using a special theme for content creation depending on user roles'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $form['themekey_help_tutorials']['Rule Chaining: Using a special theme for content creation depending on user roles']['author'] = array(
    '#type' => 'item',
    '#title' => t('Author'),
    '#value' => l('mkalkbrenner', 'http://drupal.org/user/124705'),
  );

  $img_path = base_path() . drupal_get_path('module', 'themekey') . '/img/tutorials/';

  $form['themekey_help_tutorials']['Rule Chaining: Using a special theme for content creation depending on user roles']['item'] = array(
    '#type' => 'item',
    '#value' =>
'<p>' . t('Note: This tutorial uses some ThemeKey properties from an additional module called !themekey_properties_link.', array('!themekey_properties_link' => l('ThemeKey Properties', 'http://drupal.org/project/themekey_properties'))) . '</p>' .
'<p>' . t("In this tutorial you'll learn how to define ThemeKey rules and how to cascade them to achieve sophisticated rule chains.") . '</p>' .
'<p>' . t("The use case is to use a different theme during conent creation for premium users, p.e. you don't want to show advertisements or you want to show some advanced help blocks ...") . '</p>' .
'<p>' . t('Therefor you created two user roles called "premium user" and "standard user". Using ThemeKey it\'s easy to create a rule that switches the theme in general if a user\'s role is "premium user":') . '<br />' .
'<img src="' . $img_path . 'premium_user.png" alt="' . t('ThemeKey rule that switches the theme if the user\'s role is "premium user"') . '" /></p>' .
'<p>' . t('The same way we can create a rule that switches the theme if a content creation form is requested. To do this for any kind of content we use ThemeKey\'s drupal:path property and it\'s wildcard feature:') . '<br />' .
'<img src="' . $img_path . 'create_content.png" alt="' . t('ThemeKey rule that switches the theme on content creation forms') . '" /></p>' .
'<p>' . t('But having these two seperated rules is not what our use case described. To implement our use case we have to cascade or "chain" both rules. Using the cross in front of a rule you can move any rule up and down or indent it. The chain we need should like this:') . '<br />' .
'<img src="' . $img_path . 'create_content_premium_user.png" alt="' . t('ThemeKey rule chain that switches the theme on content creation forms for premium users') . '" /></p>' .
'<p>' . t('Now using this chain ThemeKey only switches the theme on content creation forms if the current user\'s role is "premium user". Non premium users will use current system standard theme.') . '</p>' .
'<p>' . t('You might noticed that the theme select box disapeared for the first rule as you intented the second one. That\'s because both seperated rules became one.') . '</p>' .
'<p>' . t('Now it\'s possible to extend our chain to use another dedicated theme on content creation forms for "standard users".') . '<br />' .
'<img src="' . $img_path . 'create_content_premium_user_standard_user.png" alt="' . t('ThemeKey rule chain that switches to different themes on content creation forms for different user roles') . '" /></p>' .
'<p>' . t('If you have more than these two roles in your system and you simply want use a theme for premium users and one for non premium users on content creation pages you don\'t need to add one rule per role like decribed above. In this case it\'s easier to change the latest rule we added and say any other rule than "premium user" using ThemeKey\'s not operator "!".') . '<br />' .
'<img src="' . $img_path . 'create_content_premium_user_any_role.png" alt="' . t('Another ThemeKey rule chain that switches to different themes on content creation forms for different user roles') . '" /></p>' .
'<p>' . t('Now we implemented our use case using chained ThemeKey rules and you can add more rules to ThemeKey\'s Rule Chain to implement different use cases. The only decision you have to take is the order the rules are checked by ThemeKey on every page request. To demonstrate this let\'s add another rule that switches the theme if the user uses an iPhone to access your page.') . '<br />' .
'<img src="' . $img_path . 'iphone_create_content_premium_user_any_role.png" alt="' . t('ThemeKey rule chain selecting special theme for iPhones') . '" /></p>' .
'<p>' . t('What happens now is that ThemeKey switches to a special theme for iPhones as soon as the user uses such a device to access you page because ThemeKey stops processing the rules if a rule or one set of chained rules matched. This means that content creation forms are  shown using the iPhone theme as well no matter which role is assigned to the current user.') . '</p>' .
'<p>' . t('I think that\'s a good choice. If you move the iPhone rule below the chain for the content cration form these forms will use the configured themes even on the iPhone which might not be suitable to display them.') . '</p>' .
'<p>' . t('If you like to treat node creation forms differently even on the iPhone you should "chain" a dedicated set of rules for that.') . '<br />' .
'<img src="' . $img_path . 'iphone_create_content_pages.png" alt="' . t('Sophisticated ThemeKey rule chain') . '" /></p>' .
'<p>' . t('Have Fun!') . '</p>',
    );

  $form['themekey_help_tutorials']['Rule Chaining: Using a special theme for content creation depending on user roles']['versions'] = array(
    '#type' => 'fieldset',
    '#title' => t('Versions used to create this tutorial'),
    '#collapsible' => FALSE,
  );

  $form['themekey_help_tutorials']['Rule Chaining: Using a special theme for content creation depending on user roles']['versions']['themekey'] = array(
    '#type' => 'item',
    '#title' => t('ThemeKey'),
    '#value' => '6.x-2.0',
  );

  $form['themekey_help_tutorials']['Rule Chaining: Using a special theme for content creation depending on user roles']['versions']['themekey_ui'] = array(
    '#type' => 'item',
    '#title' => t('ThemeKey Properties'),
    '#value' => '6.x-2.0',
  );

  return $form;
}


/**
 * Uses drupal's form builder to format ThemeKey's help examples
 *
 * @see themekey_help()
 */
function themekey_help_examples_form($collapsed = TRUE) {
  $form['themekey_help_examples'] = array(
    '#type' => 'fieldset',
    '#title' => t('Examples'),
    '#collapsible' => TRUE,
    '#collapsed' => $collapsed,
  );

  $form['themekey_help_examples']['Set a special theme for site administrator'] = array(
    '#type' => 'fieldset',
    '#title' => t('Set a special theme for site administrator'),
    '#collapsible' => TRUE,
    '#collapsed' => $form['themekey_help_examples']['#collapsed'],
  );

  $form['themekey_help_examples']['Set a special theme for site administrator']['item'] = array(
    '#type' => 'item',
    '#value' => t('Create a Theme Switching Rules:') . '<pre>
Property: user:uid
Operator: =
Value: 1
</pre>',
    );

  // TODO: add some examples using drupal:path and drupal:path:wildcard
  $form['themekey_help_examples']['TODO: add some examples using drupal:path and drupal:path:wildcard'] = array(
    '#type' => 'fieldset',
    '#title' => 'TODO: add some examples using drupal:path and drupal:path:wildcard',
    '#collapsible' => TRUE,
    '#collapsed' => $form['themekey_help_examples']['#collapsed'],
  );

  $form['themekey_help_examples']['TODO: add some examples using drupal:path and drupal:path:wildcard']['item'] = array(
    '#type' => 'item',
    '#value' => 'TODO' . '<pre>
</pre>',
    );

  return $form;
}


/**
 * Uses drupal's form builder to format ThemeKey's help properties
 *
 * @see themekey_help()
 */
function themekey_help_properties_form($collapsed = TRUE) {
  $attributes = variable_get('themekey_attributes', array());

  $form['themekey_help_properties'] = array(
    '#type' => 'fieldset',
    '#title' => t('Properties explained'),
    '#collapsible' => TRUE,
    '#collapsed' => $collapsed,
  );

  foreach ($attributes as $property => $attribute) {
    $form['themekey_help_properties'][$property] = array(
      '#type' => 'fieldset',
      '#title' => $property,
      '#collapsible' => TRUE,
      '#collapsed' => $collapsed,
    );

    $form['themekey_help_properties'][$property]['item'] = array(
      '#type' => 'item',
      '#value' => $attribute['description'],
    );
  }

  return $form;
}
