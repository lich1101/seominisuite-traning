<?php

function cassiopeia_icon_config_form() {
  $form = array();
  // Site inforemation
  $form['cassiopeia_config_site'] = array(
    '#type' => "fieldset",
    '#title' => 'Thông tin website',
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $form['cassiopeia_config_site']['site_name'] = array(
    '#type' => "textfield",
    '#title' => 'Tên website',
    "#default_value" => variable_get('site_name', ''),
  );

  $form['cassiopeia_config_site']['site_mail'] = array(
    '#type' => "textfield",
    '#title' => 'Site Email',
    "#default_value" => variable_get('site_mail', ''),
  );

  $form['cassiopeia_config_site']['hotline'] = array(
    '#type' => "textfield",
    '#title' => 'Hotline',
    "#default_value" => variable_get('hotline', ''),
  );


  $form['cassiopeia_config_footer'] = array(
    '#type' => "fieldset",
    '#title' => 'Footer',
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $_footer = variable_get('_footer', array(
    'value' => '',
    'format' => 'full_html'
  ));
  $form['cassiopeia_config_footer']['footer'] = array(
    '#type' => "text_format",
    '#title' => 'Footer',
    '#resizable' => TRUE,
    '#format' => 'full_html',
    "#default_value" => $_footer['value'],
  );


  $form['cassiopeia_config_copyright'] = array(
    '#type' => "fieldset",
    '#title' => 'Copyright',
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $_copyright = variable_get('_copyright', array(
    'value' => '',
    'format' => 'full_html'
  ));
  $form['cassiopeia_config_copyright']['copyright'] = array(
    '#type' => "text_format",
    '#title' => 'Copyright',
    '#format' => 'full_html',
    "#default_value" => $_copyright['value'],
  );

  return system_settings_form($form);
}