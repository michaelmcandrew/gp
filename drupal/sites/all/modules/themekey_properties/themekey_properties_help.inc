<?php

/**
 * @file
 * Adds ThemeKey Properties specific sections to ThemeKey's help
 *
 * @see themekey_help()
 */

/**
 * Adds examples to themekey_help_form().
 *
 * @see themekey_help()
 * @see themekey_help_form()
 * @see themekey_properties_form_alter()
 *
 * @param $form
 *   reference to a Drupal form
 */
function themekey_properties_help_examples(&$form) {
  $form['themekey_help_examples']['Select a theme for Firefox 3.0.x, but not Firefox 3.5.x'] = array(
    '#type' => 'fieldset',
    '#title' => t('Select a theme for Firefox 3.0.x, but not Firefox 3.5.x'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $form['themekey_help_examples']['Select a theme for Firefox 3.0.x, but not Firefox 3.5.x']['item'] = array(
    '#type' => 'item',
    '#value' => t('Cascade following Theme Switching Rules:') . '<pre>
Property: system:user_browser_simplified
Operator: =
Value: Mozilla Firefox

  Property: system:user_browser
  Operator: >
  Value: Mozilla Firefox 3.0

    Property: system:user_browser
    Operator: <
    Value: Mozilla Firefox 3.5
</pre>',
    );

  $form['themekey_help_examples']['Select a theme for IE 6'] = array(
    '#type' => 'fieldset',
    '#title' => t('Select a theme for IE 6'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $form['themekey_help_examples']['Select a theme for IE 6']['item'] = array(
    '#type' => 'item',
    '#value' => t('Cascade following Theme Switching Rules:') . '<pre>
Property: system:user_browser_simplified
Operator: =
Value: Internet Explorer

  Property: system:user_browser
  Operator: >
  Value: Internet Explorer 6

    Property: system:user_browser
    Operator: <
    Value: Internet Explorer 7
</pre>',
  );

  $form['themekey_help_examples']['Select a theme for Christmas 2009'] = array(
    '#type' => 'fieldset',
    '#title' => t('Select a theme for Christmas 2009'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $form['themekey_help_examples']['Select a theme for Christmas 2009']['item'] = array(
    '#type' => 'item',
    '#value' => t('Cascade following Theme Switching Rules:') . '<pre>
Property: system:date
Operator: >
Value: 2009-12-24

  Property: system:date
  Operator: <
  Value: 2009-12-27
</pre>',
  );

  $form['themekey_help_examples']['Select a theme for New Year 2010'] = array(
    '#type' => 'fieldset',
    '#title' => t('Select a theme for New Year\'s 2010'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $form['themekey_help_examples']['Select a theme for New Year 2010']['item'] = array(
    '#type' => 'item',
    '#value' => t('Create a Theme Switching Rule:') . '<pre>
Property: system:date
Operator: =
Value: 2010-01-01
</pre>',
  );

  $form['themekey_help_examples']['Select a theme dedicated for your start page (front page, index page, ...)'] = array(
    '#type' => 'fieldset',
    '#title' => t('Select a theme dedicated for your start page (front page, index page, ...)'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $form['themekey_help_examples']['Select a theme dedicated for your start page (front page, index page, ...)']['item'] = array(
    '#type' => 'item',
    '#value' => t('Create a Theme Switching Rule:') . '<pre>
Property: drupal:is_front_page
Operator: =
Value: true
</pre>',
  );

}
