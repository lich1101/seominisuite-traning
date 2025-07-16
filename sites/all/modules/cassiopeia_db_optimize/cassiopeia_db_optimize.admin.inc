<?php
/**
 * @file
 * Mã quản trị cho module Cassiopeia Database Optimizer.
 */

/**
 * Form cấu hình tối ưu cơ sở dữ liệu.
 */
function cassiopeia_db_optimize_admin_form($form, &$form_state) {
  $form['settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Cấu hình lưu trữ dữ liệu'),
    '#collapsible' => FALSE,
  );

  $form['settings']['cassiopeia_db_optimize_cleanup_interval'] = array(
    '#type' => 'select',
    '#title' => t('Tần suất làm sạch dữ liệu'),
    '#options' => array(
      43200 => t('12 giờ'),
      86400 => t('1 ngày'),
      259200 => t('3 ngày'),
      604800 => t('1 tuần'),
    ),
    '#default_value' => variable_get('cassiopeia_db_optimize_cleanup_interval', 86400),
    '#description' => t('Tần suất thực hiện tối ưu và làm sạch cơ sở dữ liệu tự động.'),
  );

  $form['settings']['cassiopeia_db_optimize_backlink_retention'] = array(
    '#type' => 'select',
    '#title' => t('Thời gian lưu trữ backlink'),
    '#options' => array(
      90 => t('90 ngày'),
      180 => t('6 tháng'),
      365 => t('1 năm'),
      730 => t('2 năm'),
    ),
    '#default_value' => variable_get('cassiopeia_db_optimize_backlink_retention', 365),
    '#description' => t('Dữ liệu backlink cũ hơn thời gian này sẽ được di chuyển vào bảng lưu trữ.'),
  );

  $form['settings']['cassiopeia_db_optimize_watchdog_retention'] = array(
    '#type' => 'select',
    '#title' => t('Thời gian lưu trữ watchdog logs'),
    '#options' => array(
      7 => t('1 tuần'),
      14 => t('2 tuần'),
      30 => t('1 tháng'),
      90 => t('3 tháng'),
    ),
    '#default_value' => variable_get('cassiopeia_db_optimize_watchdog_retention', 30),
    '#description' => t('Nhật ký hệ thống cũ hơn thời gian này sẽ bị xóa.'),
  );

  $form['mysql'] = array(
    '#type' => 'fieldset',
    '#title' => t('Cấu hình MySQL'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $form['mysql']['mysql_info'] = array(
    '#type' => 'item',
    '#markup' => t('Thêm các cấu hình sau vào file my.cnf của MySQL để tối ưu hiệu suất:') .
      '<pre>
[mysqld]
# Tăng bộ nhớ cache cho InnoDB
innodb_buffer_pool_size = 2G
innodb_log_file_size = 256M

# Tối ưu câu truy vấn
query_cache_size = 64M
query_cache_limit = 2M

# Tối ưu kết nối
max_connections = 150
thread_cache_size = 16
</pre>',
  );

  $form['manual_actions'] = array(
    '#type' => 'fieldset',
    '#title' => t('Hành động thủ công'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $form['manual_actions']['archive_old_data'] = array(
    '#type' => 'submit',
    '#value' => t('Lưu trữ dữ liệu cũ'),
    '#submit' => array('cassiopeia_db_optimize_archive_old_data_submit'),
    '#description' => t('Di chuyển dữ liệu backlink cũ vào bảng lưu trữ.'),
  );

  $form['manual_actions']['optimize_tables'] = array(
    '#type' => 'submit',
    '#value' => t('Tối ưu bảng'),
    '#submit' => array('cassiopeia_db_optimize_optimize_tables_submit'),
    '#description' => t('Thực hiện tối ưu các bảng trong cơ sở dữ liệu.'),
  );

  $form['manual_actions']['cleanup_cache'] = array(
    '#type' => 'submit',
    '#value' => t('Xóa cache'),
    '#submit' => array('cassiopeia_db_optimize_cleanup_cache_submit'),
    '#description' => t('Làm sạch tất cả cache hệ thống.'),
  );

  $form['manual_actions']['warning'] = array(
    '#type' => 'item',
    '#markup' => '<div class="messages warning">' . t('Lưu ý: Việc lưu trữ dữ liệu cũ có thể mất thời gian tùy thuộc vào kích thước cơ sở dữ liệu. Nếu có quá nhiều dữ liệu, hãy sử dụng chức năng này trong thời gian ít người dùng hoặc thông qua cron.') . '</div>',
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Lưu cấu hình'),
  );

  return $form;
}

/**
 * Form submit handler cho form cấu hình tối ưu cơ sở dữ liệu.
 */
function cassiopeia_db_optimize_admin_form_submit($form, &$form_state) {
  // Lưu các biến cấu hình
  variable_set('cassiopeia_db_optimize_cleanup_interval', $form_state['values']['cassiopeia_db_optimize_cleanup_interval']);
  variable_set('cassiopeia_db_optimize_backlink_retention', $form_state['values']['cassiopeia_db_optimize_backlink_retention']);
  variable_set('cassiopeia_db_optimize_watchdog_retention', $form_state['values']['cassiopeia_db_optimize_watchdog_retention']);

  drupal_set_message(t('Cấu hình tối ưu cơ sở dữ liệu đã được lưu.'));
}

/**
 * Submit handler cho nút lưu trữ dữ liệu cũ.
 */
function cassiopeia_db_optimize_archive_old_data_submit($form, &$form_state) {
  // Thiết lập batch process để xử lý dữ liệu lớn
  $batch = array(
    'operations' => array(
      array('cassiopeia_db_optimize_archive_batch_process', array()),
    ),
    'finished' => 'cassiopeia_db_optimize_archive_batch_finished',
    'title' => t('Đang lưu trữ dữ liệu cũ'),
    'init_message' => t('Quá trình lưu trữ đang bắt đầu...'),
    'progress_message' => t('Đã xử lý @current trên tổng số @total.'),
    'error_message' => t('Quá trình lưu trữ gặp lỗi.'),
    'file' => drupal_get_path('module', 'cassiopeia_db_optimize') . '/cassiopeia_db_optimize.admin.inc',
  );

  batch_set($batch);
}

/**
 * Xử lý batch lưu trữ dữ liệu cũ.
 */
function cassiopeia_db_optimize_archive_batch_process(&$context) {
  // Khởi tạo biến theo dõi tiến trình
  if (!isset($context['sandbox']['progress'])) {
    $context['sandbox']['progress'] = 0;
    $context['sandbox']['current_id'] = 0;
    $context['sandbox']['max'] = db_query("SELECT COUNT(id) FROM {tbl_backlink} WHERE changed < :time",
      array(':time' => REQUEST_TIME - variable_get('cassiopeia_db_optimize_backlink_retention', 365) * 86400))->fetchField();
  }

  // Xử lý 100 bản ghi mỗi lần
  $limit = 100;
  $retention = variable_get('cassiopeia_db_optimize_backlink_retention', 365);
  $threshold = REQUEST_TIME - $retention * 86400;

  $result = db_query_range("SELECT * FROM {tbl_backlink} WHERE changed < :time AND id > :id ORDER BY id ASC",
    0, $limit, array(':time' => $threshold, ':id' => $context['sandbox']['current_id']))->fetchAll();

  foreach ($result as $backlink) {
    // Di chuyển vào bảng lưu trữ
    db_insert('tbl_backlink_archive')
      ->fields((array) $backlink)
      ->execute();

    // Xóa từ bảng chính
    db_delete('tbl_backlink')
      ->condition('id', $backlink->id)
      ->execute();

    $context['sandbox']['progress']++;
    $context['sandbox']['current_id'] = $backlink->id;
  }

  // Cập nhật thông tin tiến trình
  $context['message'] = t('Đang xử lý @current trên tổng số @max bản ghi...',
    array('@current' => $context['sandbox']['progress'], '@max' => $context['sandbox']['max']));

  // Kiểm tra xem đã hoàn thành chưa
  if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
    $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
  }
}

/**
 * Batch process hoàn thành cho lưu trữ dữ liệu.
 */
function cassiopeia_db_optimize_archive_batch_finished($success, $results, $operations) {
  if ($success) {
    drupal_set_message(t('Lưu trữ dữ liệu cũ hoàn tất.'));
  }
  else {
    drupal_set_message(t('Lưu trữ dữ liệu cũ không thành công.'), 'error');
  }
}

/**
 * Submit handler cho nút tối ưu bảng.
 */
function cassiopeia_db_optimize_optimize_tables_submit($form, &$form_state) {
  // Thực hiện tối ưu bảng
  _cassiopeia_db_optimize_optimize_tables();
  drupal_set_message(t('Các bảng đã được tối ưu.'));
}

/**
 * Submit handler cho nút xóa cache.
 */
function cassiopeia_db_optimize_cleanup_cache_submit($form, &$form_state) {
  // Thực hiện làm sạch cache
  _cassiopeia_db_optimize_cleanup_cache();
  drupal_set_message(t('Đã làm sạch cache.'));
}

/**
 * Tối ưu cơ sở dữ liệu thủ công.
 */
function cassiopeia_db_optimize_manual() {
  // Làm sạch log
  _cassiopeia_db_optimize_cleanup_watchdog();

  // Làm sạch cache
  _cassiopeia_db_optimize_cleanup_cache();

  // Tối ưu bảng
  _cassiopeia_db_optimize_optimize_tables();

  // Cập nhật thời gian làm sạch
  variable_set('cassiopeia_db_optimize_last_cleanup', REQUEST_TIME);

  drupal_set_message(t('Đã thực hiện tối ưu cơ sở dữ liệu thủ công.'));
  drupal_goto('admin/config/system/cassiopeia-db-optimize');
}
