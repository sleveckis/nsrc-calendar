<?php
// $Id$

/**
 * @file
 * Admin page callback for the uobanner module.
 */

/**
 * Builds and returns the uobanner settings form.
 */
function uobanner_admin_settings() {

  $form['#tree'] = TRUE;	// Prevent flattening the form values

  $uobanner = variable_get('uobanner', array()); // returns fieldset child fields as an array

  $form['uobanner'] = array(
    '#type' => 'fieldset',
    '#title' => t('UO Banner Options'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#tree' => TRUE,
  );
  
  $form['uobanner']['uobanner_color_examples'] = array(
    '#type' => 'markup',
    '#markup' => "<div id=\"uobanner-examples\"><strong>EXAMPLES</strong>
    <p><strong>Black:</strong><br /><img src=\"/" . drupal_get_path('module', 'uobanner') ."/images/logobar_black.png\" alt='Example of a black UO banner.' height='35' width='400' alt='Black UO banner example graphic.' /></p>
    <p><strong>Green:</strong><br /><img src=\"/" . drupal_get_path('module', 'uobanner') ."/images/logobar_green.png\" alt='Example of a green UO banner.' height='35' width='400' alt='Green UO banner example graphic.' /></p></div>",
  );
  $form['uobanner']['uobanner_color_setting'] = array(
    '#title' => t('Banner color'),
    '#type' => 'radios',
    '#options' => array('black' => t('Black'), 'green' => t('Green')),
    '#default_value' => (!empty($uobanner['uobanner_color_setting']) && ($uobanner['uobanner_color_setting'] != 'green_bevel'))
                    ? $uobanner['uobanner_color_setting']
                    : 'green',
    '#required' => TRUE,
  );
  $form['uobanner']['uobanner_page_width'] = array(
    '#title' => t('Page width (in pixels)'),
    '#type' => 'textfield',
    '#maxlength' => 6,
    '#size' => 10,
    '#default_value' => (!empty($uobanner['uobanner_page_width']))
                    ? $uobanner['uobanner_page_width']
                    : '',
    '#description' => t('Specify the width of your web site in pixels. This field only accepts a numeric value. If it is a fluid-width website then leave this field blank.'),
    '#required' => FALSE,
  );

  $uoclosure = variable_get('uoclosure', array()); // returns fieldset child fields as an array

  $form['uoclosure'] = array(
    '#type' => 'fieldset',
    '#title' => t('UO Closure Options'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#tree' => TRUE,
  );
    $form['uoclosure']['uobanner_display_copyright'] = array(
    '#title' => t('Should closure display UO copyright text?'),
    '#type' => 'radios',
    '#options' => array('yes' => t('Yes'), 'no' => t('No'),),
    '#default_value' => (!empty($uoclosure['uobanner_display_copyright']))
                    ? $uoclosure['uobanner_display_copyright']
                    : 'no',
    '#description' => t('This should be the street address of the department or organization that this web site represents.'),
    '#required' => TRUE,
  );
  $form['uoclosure']['uobanner_closure_address'] = array(
    '#title' => t('Street Address'),
    '#type' => 'textfield',
    '#maxlength' => 80,
    '#size' => 80,
    '#default_value' => (!empty($uoclosure['uobanner_closure_address']))
                    ? $uoclosure['uobanner_closure_address']
                    : '',
    '#description' => t('This should be the street address of the department or organization that this web site represents.'),
    '#required' => FALSE,
  );
  $form['uoclosure']['uobanner_closure_phone'] = array(
    '#title' => t('Phone Number'),
    '#type' => 'textfield',
    '#maxlength' => 20,
    '#size' => 20,
    '#default_value' => (!empty($uoclosure['uobanner_closure_phone']))
                    ? $uoclosure['uobanner_closure_phone']
                    : '',
    '#description' => t('This should be the primary contact phone number for this  web site. Expected format: <em>(111) 111-1111</em>.'),
    '#required' => FALSE,
  );
  $form['uoclosure']['uobanner_closure_email'] = array(
    '#title' => t('Email Address'),
    '#type' => 'textfield',
    '#maxlength' => 30,
    '#size' => 30,
    '#default_value' => (!empty($uoclosure['uobanner_closure_email']))
                    ? $uoclosure['uobanner_closure_email']
                    : '',
    '#description' => t('This should be the primary email address for this web site.'),
    '#required' => FALSE,
  );
    $form['uoclosure']['uobanner_closure_year'] = array(
    '#title' => t('Copyright Year'),
    '#type' => 'textfield',
    '#maxlength' => 4,
    '#size' => 10,
    '#default_value' => (!empty($uoclosure['uobanner_closure_year']))
                    ? $uoclosure['uobanner_closure_year']
                    : '',
    '#description' => t('This should be the year that this web site was first copyrighted. For example: <em>2005</em>.'),
    '#required' => FALSE,
  );
  $form['uoclosure']['uobanner_closure_text_color'] = array(
    '#title' => t('Closure Text Color'),
    '#type' => 'textfield',
    '#maxlength' => 7,
    '#size' => 10,
    '#default_value' => (!empty($uoclosure['uobanner_closure_text_color']))
                    ? $uoclosure['uobanner_closure_text_color']
                    : '',
    '#description' => t('The hexidecimal value for the closure text. For example: <em>#FFFFFF</em>.'),
    '#required' => FALSE,
  );

  return system_settings_form($form);
}