<?php


function cassiopeia_admin_theme_preprocess_html(&$variables) {
  $variables['classes_array'][] = 'hold-transition skin-blue sidebar-mini admin-menu';
//    drupal_add_css('https://cdn.jsdelivr.net/npm/@unicorn-fail/drupal-bootstrap-styles@0.0.2/dist/3.3.1/7.x-3.x/drupal-bootstrap.css', array('type' => 'external'));
    drupal_add_css("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css");
    drupal_add_js("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js");
    drupal_add_js('https://kit.fontawesome.com/378c867ac3.js', array('type' => 'external'));
}


function cassiopeia_admin_theme_preprocess_page(&$variables) {
  $_arg = arg();
  $variables['primary_local_tasks'] = menu_primary_local_tasks();
  $variables['secondary_local_tasks'] = menu_secondary_local_tasks();

// Set cassiopeia admin theme
  if ($_arg[0] == 'admin') {
      if (count($_arg) >= 2 ) {
          if ($_arg[1] == 'cassiopeia') {
            $variables['theme_hook_suggestions'][] = 'page__cassiopeia';
          }
      }
  }
}


function cassiopeia_admin_theme_preprocess_admin_block(&$vars) {
  // Add icon and classes to admin block titles.
  if (isset($vars['block']['href'])) {
    $vars['block']['localized_options']['attributes']['class'] = _cassiopeia_admin_theme_icon_classes($vars['block']['href']);
  }
  $vars['block']['localized_options']['html'] = TRUE;
  if (isset($vars['block']['link_title'])) {
    $vars['block']['title'] = l('<span class="icon"></span>' . filter_xss_admin($vars['block']['link_title']), $vars['block']['href'], $vars['block']['localized_options']);
  }

  if (empty($vars['block']['content'])) {
    $vars['block']['content'] = "<div class='admin-block-description description'>{$vars['block']['description']}</div>";
  }
}


function cassiopeia_admin_theme_admin_block($variables) {
  $block = $variables['block'];
  $output = '';

  // Don't display the block if it has no content to display.
  if (empty($block['show'])) {
    return $output;
  }

  $output .= '<div class="admin-panel panel panel-default">';
  if (!empty($block['title'])) {
    $output .= '<div class="panel-heading"><h3 class="panel-title fieldset-legend">' . $block['title'] . '</h3></div>';
  }
  if (!empty($block['content'])) {
    $output .= '<div class="body panel-body">' . $block['content'] . '</div>';
  }
  else {
    $output .= '<div class="description">' . $block['description'] . '</div>';
  }
  $output .= '</div>';

  return $output;
}

function cassiopeia_admin_theme_admin_block_content($vars) {

  $content = $vars['content'];

  $output = '';
  if (!empty($content)) {

    foreach ($content as $k => $item) {

      //-- Safety check for invalid clients of the function
      if (empty($content[$k]['localized_options']['attributes']['class'])) {
        $content[$k]['localized_options']['attributes']['class'] = array();
      }
      if (!is_array($content[$k]['localized_options']['attributes']['class'])) {
        $content[$k]['localized_options']['attributes']['class'] = array($content[$k]['localized_options']['attributes']['class']);
      }

      $content[$k]['title'] = '<span class="icon"></span>' . filter_xss_admin($item['title']);


      $content[$k]['localized_options']['html'] = TRUE;
      if (!empty($content[$k]['localized_options']['attributes']['class'])) {
        $content[$k]['localized_options']['attributes']['class'] += _cassiopeia_admin_theme_icon_classes($item['href']);
      }
      else {
        $content[$k]['localized_options']['attributes']['class'] = _cassiopeia_admin_theme_icon_classes($item['href']);
      }
    }
    $output = system_admin_compact_mode() ? '<ul class="admin-list admin-list-compact">' : '<ul class="admin-list">';
    foreach ($content as $item) {
      $output .= '<li class="leaf">';
      $output .= l($item['title'], $item['href'], $item['localized_options']);
      if (isset($item['description']) && !system_admin_compact_mode()) {
        $output .= "<div class='description'>{$item['description']}</div>";
      }
      $output .= '</li>';
    }
    $output .= '</ul>';
  }

  if (!empty($vars['theme-type']) && $vars['theme-type']== 'box') {
    $output = '<div class="box box-primary"><div class="box-body">' . $output .'</div></div>';
  }

  return $output;
}

function _cassiopeia_admin_theme_icon_classes($path) {
  $classes = array();
  $args = explode('/', $path);
  if ($args[0] === 'admin' || (count($args) > 1 && $args[0] === 'node' && $args[1] === 'add')) {
    // Add a class specifically for the current path that allows non-cascading
    // style targeting.
    $classes[] = 'path-' . str_replace('/', '-', implode('/', $args)) . '-';
    while (count($args)) {
      $classes[] = drupal_html_class('path-' . str_replace('/', '-', implode('/', $args)));
      array_pop($args);
    }
    return $classes;
  }
  return array();
}
