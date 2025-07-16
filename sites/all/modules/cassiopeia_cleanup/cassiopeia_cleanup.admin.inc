<?php

/**
 * @file
 * Form quản trị cho module cassiopeia_cleanup.
 */

/**
 * Form để làm trắng dữ liệu.
 */
function cassiopeia_cleanup_form($form, &$form_state) {
  // Thêm CSS để làm nổi bật các cảnh báo
  $form['#attached']['css'] = array(
    drupal_get_path('module', 'cassiopeia_cleanup') . '/cassiopeia_cleanup.css',
  );

  $form['warning'] = array(
    '#markup' => '<div class="messages warning cleanup-warning"><strong>' . t('CẢNH BÁO: CÁC THAO TÁC NÀY KHÔNG THỂ HOÀN TÁC!') . '</strong><br />' .
                 t('Làm trắng dữ liệu sẽ xóa vĩnh viễn dữ liệu từ cơ sở dữ liệu. Hãy đảm bảo bạn đã sao lưu trước khi tiếp tục.') . '</div>',
  );

  $form['cleanup_options'] = array(
    '#type' => 'fieldset',
    '#title' => t('Tùy chọn làm trắng dữ liệu'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
    '#description' => t('Chọn loại dữ liệu bạn muốn làm trắng:'),
  );

  $form['cleanup_options']['cassiopeia_content'] = array(
    '#type' => 'checkbox',
    '#title' => t('Xóa tất cả nội dung Cassiopeia'),
    '#description' => t('Xóa dữ liệu từ bảng cassiopeia_content và các tags liên quan'),
  );

  $form['cleanup_options']['cassiopeia_guest_post'] = array(
    '#type' => 'checkbox',
    '#title' => t('Xóa tất cả bài viết khách'),
    '#description' => t('Xóa tất cả dữ liệu từ bảng cassiopeia_guest_post_article và bảng liên quan'),
  );

  $form['cleanup_options']['cassiopeia_websites'] = array(
    '#type' => 'checkbox',
    '#title' => t('Xóa tất cả websites'),
    '#description' => t('Xóa tất cả website từ cassiopeia_guest_post_website'),
  );

  $form['cleanup_options']['nodes'] = array(
    '#type' => 'checkbox',
    '#title' => t('Xóa tất cả nội dung (nodes)'),
    '#description' => t('NGUY HIỂM: Xóa tất cả nội dung từ hệ thống Drupal'),
  );

  $form['cleanup_options']['nodes_types'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Loại nội dung cần xóa:'),
    '#options' => node_type_get_names(),
    '#states' => array(
      'visible' => array(
        ':input[name="nodes"]' => array('checked' => TRUE),
      ),
    ),
  );

  $form['cleanup_options']['comments'] = array(
    '#type' => 'checkbox',
    '#title' => t('Xóa tất cả bình luận'),
    '#description' => t('Xóa tất cả bình luận từ hệ thống'),
  );

  $form['cleanup_options']['users'] = array(
    '#type' => 'checkbox',
    '#title' => t('Xóa tất cả người dùng (trừ admin)'),
    '#description' => t('NGUY HIỂM: Xóa tất cả người dùng trừ user ID 1'),
  );

  $form['cleanup_options']['taxonomies'] = array(
    '#type' => 'checkbox',
    '#title' => t('Xóa tất cả phân loại (taxonomy)'),
    '#description' => t('Xóa tất cả thuật ngữ và từ vựng'),
  );

  $form['cleanup_options']['use_direct_sql'] = array(
    '#type' => 'checkbox',
    '#title' => t('Sử dụng SQL trực tiếp (nhanh hơn)'),
    '#description' => t('NGUY HIỂM: Tùy chọn này sẽ sử dụng câu lệnh SQL trực tiếp TRUNCATE/DELETE để xóa dữ liệu, sẽ nhanh hơn nhưng có thể gây mất đồng bộ. Chỉ dùng khi muốn xóa toàn bộ!'),
    '#default_value' => FALSE,
  );

  $form['cleanup_options']['max_execution_time'] = array(
    '#type' => 'select',
    '#title' => t('Thời gian xử lý tối đa'),
    '#description' => t('Tăng thời gian xử lý tối đa nếu bạn gặp timeout.'),
    '#options' => array(
      300 => t('5 phút'),
      600 => t('10 phút'),
      1800 => t('30 phút'),
      3600 => t('1 giờ'),
      7200 => t('2 giờ'),
      14400 => t('4 giờ'),
    ),
    '#default_value' => 3600,
  );

  $form['cleanup_options']['batch_size'] = array(
    '#type' => 'select',
    '#title' => t('Kích thước lô'),
    '#description' => t('Số lượng mục xử lý trong một lần. Giảm con số này nếu bạn gặp lỗi hết bộ nhớ.'),
    '#options' => array(
      10 => t('10 (rất chậm, an toàn nhất)'),
      20 => t('20 (chậm, rất an toàn)'),
      50 => t('50 (vừa phải, an toàn)'),
      100 => t('100 (nhanh, khuyến nghị)'),
      200 => t('200 (rất nhanh, có thể gặp lỗi)'),
      500 => t('500 (cực nhanh, rủi ro cao)'),
    ),
    '#default_value' => 50,
    '#states' => array(
      'disabled' => array(
        ':input[name="use_direct_sql"]' => array('checked' => TRUE),
      ),
    ),
  );

  $form['security'] = array(
    '#type' => 'fieldset',
    '#title' => t('Xác nhận an toàn'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );

  $form['security']['confirm_text'] = array(
    '#type' => 'textfield',
    '#title' => t('Gõ "XÓA DỮ LIỆU" để xác nhận'),
    '#description' => t('Đây là bước an toàn để tránh xóa dữ liệu do vô tình.'),
    '#required' => TRUE,
  );

  $form['security']['confirm_backup'] = array(
    '#type' => 'checkbox',
    '#title' => t('Tôi xác nhận đã sao lưu cơ sở dữ liệu'),
    '#required' => TRUE,
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Làm trắng dữ liệu đã chọn'),
    '#attributes' => array('class' => array('button--danger')),
  );

  return $form;
}

/**
 * Validate handler cho form làm trắng dữ liệu.
 */
function cassiopeia_cleanup_form_validate($form, &$form_state) {
  // Kiểm tra xác nhận văn bản
  if ($form_state['values']['confirm_text'] !== 'XÓA DỮ LIỆU') {
    form_set_error('confirm_text', t('Bạn phải nhập chính xác "XÓA DỮ LIỆU" để tiếp tục.'));
  }

  // Kiểm tra nếu không có tùy chọn nào được chọn
  $has_selection = FALSE;
  foreach (array('cassiopeia_content', 'cassiopeia_guest_post', 'cassiopeia_websites', 'nodes', 'comments', 'users', 'taxonomies') as $option) {
    if (!empty($form_state['values'][$option])) {
      $has_selection = TRUE;
      break;
    }
  }

  if (!$has_selection) {
    form_set_error('cleanup_options', t('Bạn phải chọn ít nhất một loại dữ liệu để làm trắng.'));
  }

  // Nếu nodes được chọn, kiểm tra xem có node_types nào được chọn không
  if (!empty($form_state['values']['nodes'])) {
    $node_types_selected = FALSE;
    foreach ($form_state['values']['nodes_types'] as $type => $selected) {
      if ($selected) {
        $node_types_selected = TRUE;
        break;
      }
    }

    if (!$node_types_selected) {
      form_set_error('nodes_types', t('Bạn phải chọn ít nhất một loại nội dung để xóa.'));
    }
  }
}

/**
 * Submit handler cho form làm trắng dữ liệu.
 */
function cassiopeia_cleanup_form_submit($form, &$form_state) {
  // Đặt timeout cao hơn
  set_time_limit(3600);

  // Thu thập tùy chọn xóa
  $cleanup_options = array(
    'cassiopeia_content' => !empty($form_state['values']['cassiopeia_content']),
    'cassiopeia_guest_post' => !empty($form_state['values']['cassiopeia_guest_post']),
    'cassiopeia_websites' => !empty($form_state['values']['cassiopeia_websites']),
    'nodes' => !empty($form_state['values']['nodes']),
    'nodes_types' => !empty($form_state['values']['nodes_types']) ? $form_state['values']['nodes_types'] : array(),
    'comments' => !empty($form_state['values']['comments']),
    'users' => !empty($form_state['values']['users']),
    'taxonomies' => !empty($form_state['values']['taxonomies']),
    'use_direct_sql' => !empty($form_state['values']['use_direct_sql']),
    'max_execution_time' => $form_state['values']['max_execution_time'],
    'batch_size' => $form_state['values']['batch_size'],
  );

  // Tạo và thực thi batch
  module_load_include('inc', 'cassiopeia_cleanup', 'cassiopeia_cleanup.batch');
  cassiopeia_cleanup_run_batch($cleanup_options);
}

/**
 * Xóa nội dung Cassiopeia.
 */
function _cassiopeia_cleanup_delete_content() {
  $count = 0;

  // Lấy tất cả ID nội dung
  $query = db_select('cassiopeia_content', 'c')
    ->fields('c', array('id'));
  $ids = $query->execute()->fetchCol();

  foreach ($ids as $id) {
    $node = new stdClass();
    $node->id = $id;
    if (function_exists('content_article_delete')) {
      content_article_delete($node);
      $count++;
    }
  }

  // Đảm bảo xóa hết
  db_delete('cassiopeia_content')->execute();
  db_delete('tbl_tags')->execute();

  return $count;
}

/**
 * Xóa bài viết khách.
 */
function _cassiopeia_cleanup_delete_guest_posts() {
  $count = 0;

  // Lấy tất cả ID bài viết
  $query = db_select('cassiopeia_guest_post_article', 'a')
    ->fields('a', array('id'));
  $ids = $query->execute()->fetchCol();

  foreach ($ids as $id) {
    if (function_exists('cassiopeia_guest_post_article_delete')) {
      cassiopeia_guest_post_article_delete($id);
      $count++;
    }
  }

  // Đảm bảo xóa hết
  db_delete('cassiopeia_guest_post_article')->execute();
  db_delete('cassiopeia_guest_post_article_tag')->execute();

  return $count;
}

/**
 * Xóa websites.
 */
function _cassiopeia_cleanup_delete_websites() {
  $count = 0;

  // Lấy tất cả ID website
  $query = db_select('cassiopeia_guest_post_website', 'w')
    ->fields('w', array('id'));
  $websites = $query->execute()->fetchAll();

  foreach ($websites as $website) {
    $node = new stdClass();
    $node->id = $website->id;
    if (function_exists('cassiopeia_guest_post_website_delete')) {
      cassiopeia_guest_post_website_delete($node);
      $count++;
    }
  }

  // Đảm bảo xóa hết
  db_delete('cassiopeia_guest_post_website')->execute();
  db_delete('cassiopeia_guest_post_website_category')->execute();

  return $count;
}

/**
 * Xóa nodes.
 */
function _cassiopeia_cleanup_delete_nodes($node_types) {
  $count = 0;

  // Lấy tất cả nids theo loại
  $query = db_select('node', 'n')
    ->fields('n', array('nid'));

  if (!empty($node_types)) {
    $query->condition('type', array_keys($node_types), 'IN');
  }

  $nids = $query->execute()->fetchCol();

  // Xóa theo lô để tránh quá tải
  $chunks = array_chunk($nids, 50);
  foreach ($chunks as $chunk) {
    node_delete_multiple($chunk);
    $count += count($chunk);
  }

  return $count;
}

/**
 * Xóa comments.
 */
function _cassiopeia_cleanup_delete_comments() {
  $count = 0;

  // Lấy tất cả comment IDs
  $query = db_select('comment', 'c')
    ->fields('c', array('cid'));
  $cids = $query->execute()->fetchCol();

  // Xóa theo lô để tránh quá tải
  $chunks = array_chunk($cids, 50);
  foreach ($chunks as $chunk) {
    comment_delete_multiple($chunk);
    $count += count($chunk);
  }

  return $count;
}

/**
 * Xóa users trừ user ID 1.
 */
function _cassiopeia_cleanup_delete_users() {
  $count = 0;

  // Lấy tất cả user IDs trừ user 1
  $query = db_select('users', 'u')
    ->fields('u', array('uid'))
    ->condition('uid', 1, '>');
  $uids = $query->execute()->fetchCol();

  // Xóa theo lô để tránh quá tải
  $chunks = array_chunk($uids, 50);
  foreach ($chunks as $chunk) {
    user_delete_multiple($chunk);
    $count += count($chunk);
  }

  return $count;
}

/**
 * Xóa taxonomy terms.
 */
function _cassiopeia_cleanup_delete_taxonomies() {
  $count = 0;

  // Lấy tất cả term IDs
  $query = db_select('taxonomy_term_data', 't')
    ->fields('t', array('tid'));
  $tids = $query->execute()->fetchCol();

  foreach ($tids as $tid) {
    taxonomy_term_delete($tid);
    $count++;
  }

  return $count;
}
