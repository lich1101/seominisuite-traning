<?php
// inject_disable_script.php
// Script để inject disable script vào Drupal

// Thêm script vào Drupal
function cassiopeia_captcha_add_disable_script() {
    drupal_add_js(drupal_get_path('module', 'cassiopeia_captcha') . '/scripts/disable_client_scripts.js', 'file');
}

// Hook để load script
function cassiopeia_captcha_page_alter(&$page) {
    if (isset($page['content']['system_main']['cassiopeia_captcha_form'])) {
        cassiopeia_captcha_add_disable_script();
    }
}
