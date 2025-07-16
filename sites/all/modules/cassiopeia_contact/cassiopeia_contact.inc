<?php
drupal_session_start();
#contact
function cassiopeia_contact_page_callback() {
    return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-contact.tpl.php");
}
function cassiopeia_config_contact_form() {
    $form = array();
    $form['cassiopeia_config_contact']['contact_title'] = array(
        '#type' => "textfield",
        "#title" => 'Tiêu đề',
        "#default_value" => variable_get('contact_title', ''),
    );
    $_contact_content = variable_get('contact_content', array(
        'value' => '',
        'format' => 'full_html'
    ));
    $form['cassiopeia_config_contact']['contact_content'] = array(
        '#type' => "text_format",
        '#title' => t('Mô tả'),
        '#format' => 'full_html',
        "#default_value" => $_contact_content['value'],
    );
    $form['cassiopeia_config_contact']['contact_map'] = array(
        '#type' => "textarea",
        '#title' => t('Mã nhúng bản đồ'),
        '#resizable' => TRUE,
        "#default_value" => variable_get('contact_map'),
    );
    return system_settings_form($form);
}
#contact
