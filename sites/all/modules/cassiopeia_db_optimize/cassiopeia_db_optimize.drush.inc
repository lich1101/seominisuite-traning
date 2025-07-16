<?php
/**
 * @file
 * Drush integration for Cassiopeia Database Optimizer.
 */

/**
 * Implements hook_drush_command().
 */
function cassiopeia_db_optimize_drush_command() {
  $items = array();

  $items['cassiopeia-optimize-db'] = array(
    'description' => 'Thực hiện tối ưu cơ sở dữ liệu Cassiopeia.',
    'arguments' => array(
      'type' => 'Loại tối ưu (all, index, archive, cache, tables, cleanup)',
    ),
    'options' => array(
      'days' => 'Số ngày dữ liệu cần lưu trữ (áp dụng với archive)',
      'limit' => 'Số lượng bản ghi xử lý mỗi lần (áp dụng với archive)',
      'force' => 'Bỏ qua xác nhận',
    ),
    'aliases' => array('copt'),
    'examples' => array(
      'drush cassiopeia-optimize-db all' => 'Thực hiện tất cả các thao tác tối ưu.',
      'drush cassiopeia-optimize-db archive --days=180' => 'Lưu trữ dữ liệu cũ hơn 180 ngày.',
      'drush cassiopeia-optimize-db tables' => 'Tối ưu các bảng dữ liệu.',
      'drush copt cleanup --force' => 'Làm sạch cache và log mà không cần xác nhận.',
    ),
  );

  $items['cassiopeia-db-status'] = array(
    'description' => 'Hiển thị thông tin trạng thái cơ sở dữ liệu.',
    'aliases' => array('cdbstat'),
    'examples' => array(
      'drush cassiopeia-db-status' => 'Hiển thị thông tin trạng thái cơ sở dữ liệu.',
    ),
  );

  return $items;
}

/**
 * Callback cho lệnh cassiopeia-optimize-db.
 */
function drush_cassiopeia_db_optimize_cassiopeia_optimize_db($type = 'all') {
  // Xác nhận trước khi thực hiện
  if (!drush_get_option('force') && !drush_confirm(dt('Bạn có chắc chắn muốn thực hiện tối ưu cơ sở dữ liệu (@type)?', array('@type' => $type)))) {
    return drush_user_abort();
  }

  // Tải module nếu chưa được tải
  if (!module_exists('cassiopeia_db_optimize')) {
    drush_log(dt('Module cassiopeia_db_optimize chưa được bật.'), 'error');
    return;
  }

  // Thực hiện tối ưu theo loại
  switch ($type) {
    case 'all':
      drush_log(dt('Thực hiện tất cả các thao tác tối ưu...'), 'notice');

      _drush_cassiopeia_db_optimize_add_indexes();
      _drush_cassiopeia_db_optimize_archive_old_data();
      _drush_cassiopeia_db_optimize_cleanup_cache();
      _drush_cassiopeia_db_optimize_optimize_tables();
      _drush_cassiopeia_db_optimize_cleanup_watchdog();

      drush_log(dt('Đã hoàn thành tất cả các thao tác tối ưu.'), 'success');
      break;

    case 'index':
      drush_log(dt('Thêm chỉ mục cho cơ sở dữ liệu...'), 'notice');
      _drush_cassiopeia_db_optimize_add_indexes();
      drush_log(dt('Đã thêm chỉ mục cho cơ sở dữ liệu.'), 'success');
      break;

    case 'archive':
      drush_log(dt('Lưu trữ dữ liệu cũ...'), 'notice');
      _drush_cassiopeia_db_optimize_archive_old_data();
      drush_log(dt('Đã hoàn thành lưu trữ dữ liệu cũ.'), 'success');
      break;

    case 'cache':
      drush_log(dt('Làm sạch cache...'), 'notice');
      _drush_cassiopeia_db_optimize_cleanup_cache();
      drush_log(dt('Đã làm sạch cache.'), 'success');
      break;

    case 'tables':
      drush_log(dt('Tối ưu bảng dữ liệu...'), 'notice');
      _drush_cassiopeia_db_optimize_optimize_tables();
      drush_log(dt('Đã tối ưu bảng dữ liệu.'), 'success');
      break;

    case 'cleanup':
      drush_log(dt('Làm sạch log và dữ liệu tạm...'), 'notice');
      _drush_cassiopeia_db_optimize_cleanup_watchdog();
      _drush_cassiopeia_db_optimize_cleanup_cache();
      drush_log(dt('Đã làm sạch log và dữ liệu tạm.'), 'success');
      break;

    default:
      drush_log(dt('Loại tối ưu không hợp lệ. Sử dụng: all, index, archive, cache, tables, cleanup'), 'error');
      break;
  }
}

/**
 * Callback cho lệnh cassiopeia-db-status.
 */
function drush_cassiopeia_db_optimize_cassiopeia_db_status() {
  drush_log(dt('Đang kiểm tra trạng thái cơ sở dữ liệu...'), 'notice');

  // Kiểm tra kích thước bảng
  $tables = array(
    'tbl_backlink',
    'tbl_backlink_detail',
    'watchdog',
    'node',
    'node_revision',
    'cassiopeia_content',
  );

  // Hiển thị thông tin
  drush_print(dt('THÔNG TIN CƠ SỞ DỮ LIỆU CASSIOPEIA:'));
  drush_print('----------------------------------------');

  // Kích thước bảng
  drush_print(dt('KÍCH THƯỚC BẢNG:'));
  foreach ($tables as $table) {
    if (db_table_exists($table)) {
      $size = _drush_cassiopeia_db_optimize_get_table_size($table);
      drush_print(sprintf('  %-25s %s', $table, _drush_cassiopeia_db_optimize_format_bytes($size)));
    }
  }

  // Thông tin chỉ mục
  drush_print('');
  drush_print(dt('CHỈ MỤC:'));
  foreach ($tables as $table) {
    if (db_table_exists($table)) {
      $indexes = _drush_cassiopeia_db_optimize_get_table_indexes($table);
      drush_print(sprintf('  %-25s %d chỉ mục', $table, count($indexes)));
    }
  }

  // Số lượng bản ghi
  drush_print('');
  drush_print(dt('SỐ LƯỢNG BẢN GHI:'));
  foreach ($tables as $table) {
    if (db_table_exists($table)) {
      $count = db_query("SELECT COUNT(*) FROM {" . $table . "}")->fetchField();
      drush_print(sprintf('  %-25s %s bản ghi', $table, number_format($count)));
    }
  }

  // Thông tin tối ưu
  drush_print('');
  drush_print(dt('THÔNG TIN TỐI ƯU:'));
  $last_cleanup = variable_get('cassiopeia_db_optimize_last_cleanup', 0);
  $cleanup_interval = variable_get('cassiopeia_db_optimize_cleanup_interval', 86400);
  drush_print(sprintf('  %-25s %s', 'Lần làm sạch cuối:', $last_cleanup ? format_date($last_cleanup) : 'Chưa bao giờ'));
  drush_print(sprintf('  %-25s %s', 'Khoảng thời gian làm sạch:', _drush_cassiopeia_db_optimize_format_interval($cleanup_interval)));
  drush_print(sprintf('  %-25s %s', 'Làm sạch tiếp theo:', $last_cleanup ? format_date($last_cleanup + $cleanup_interval) : 'Ngay khi có thể'));
}

/**
 * Thêm chỉ mục cho cơ sở dữ liệu.
 */
function _drush_cassiopeia_db_optimize_add_indexes() {
  try {
    // Danh sách các chỉ mục cần thêm
    $indexes = array(
      'tbl_backlink' => array(
        'idx_backlink_pid_status' => array('pid', 'status'),
        'idx_backlink_created_changed' => array('created', 'changed'),
      ),
      'tbl_backlink_detail' => array(
        'idx_backlink_detail_pid' => array('pid'),
      ),
      'cassiopeia_content' => array(
        'idx_content_created_changed' => array('created', 'changed'),
      ),
    );

    foreach ($indexes as $table => $table_indexes) {
      if (!db_table_exists($table)) {
        drush_log(dt('Bảng @table không tồn tại.', array('@table' => $table)), 'warning');
        continue;
      }

      $existing_indexes = _drush_cassiopeia_db_optimize_get_table_indexes($table);

      foreach ($table_indexes as $index_name => $columns) {
        if (!in_array($index_name, $existing_indexes)) {
          db_add_index($table, $index_name, $columns);
          drush_log(dt('Đã thêm chỉ mục @index cho bảng @table.', array('@index' => $index_name, '@table' => $table)), 'success');
        }
        else {
          drush_log(dt('Chỉ mục @index đã tồn tại trên bảng @table.', array('@index' => $index_name, '@table' => $table)), 'notice');
        }
      }
    }
  }
  catch (Exception $e) {
    drush_log(dt('Lỗi khi thêm chỉ mục: @error', array('@error' => $e->getMessage())), 'error');
  }
}

/**
 * Lưu trữ dữ liệu cũ.
 */
function _drush_cassiopeia_db_optimize_archive_old_data() {
  try {
    $days = drush_get_option('days', variable_get('cassiopeia_db_optimize_backlink_retention', 365));
    $threshold = REQUEST_TIME - ($days * 86400);
    $limit = drush_get_option('limit', 5000);

    // Đảm bảo bảng lưu trữ tồn tại
    if (!db_table_exists('tbl_backlink_archive')) {
      $schema = cassiopeia_db_optimize_schema();
      db_create_table('tbl_backlink_archive', $schema['tbl_backlink_archive']);
      drush_log(dt('Đã tạo bảng lưu trữ tbl_backlink_archive.'), 'success');
    }

    // Đếm số bản ghi cần di chuyển
    $count = db_query("SELECT COUNT(*) FROM {tbl_backlink} WHERE changed < :time", array(':time' => $threshold))->fetchField();

    if ($count == 0) {
      drush_log(dt('Không có dữ liệu cũ cần lưu trữ (cũ hơn @days ngày).', array('@days' => $days)), 'notice');
      return;
    }

    drush_log(dt('Có @count bản ghi cũ hơn @days ngày cần lưu trữ.', array('@count' => number_format($count), '@days' => $days)), 'notice');

    // Xử lý theo lô để tránh timeout
    $processed = 0;
    while ($processed < $count) {
      $result = db_query_range("SELECT * FROM {tbl_backlink} WHERE changed < :time ORDER BY id ASC", 0, $limit, array(':time' => $threshold))->fetchAll();

      if (empty($result)) {
        break;
      }

      $batch_count = count($result);
      $batch_success = 0;

      foreach ($result as $backlink) {
        try {
          // Di chuyển vào bảng lưu trữ
          db_insert('tbl_backlink_archive')
            ->fields((array) $backlink)
            ->execute();

          // Xóa từ bảng chính
          db_delete('tbl_backlink')
            ->condition('id', $backlink->id)
            ->execute();

          $batch_success++;
        }
        catch (Exception $e) {
          drush_log(dt('Lỗi khi lưu trữ backlink ID @id: @error', array('@id' => $backlink->id, '@error' => $e->getMessage())), 'warning');
        }
      }

      $processed += $batch_success;
      drush_log(dt('Đã lưu trữ @batch_count/@total bản ghi (@percent%).',
        array(
          '@batch_count' => number_format($processed),
          '@total' => number_format($count),
          '@percent' => round(($processed / $count) * 100, 2),
        )), 'notice');

      // Tạm dừng một chút để giảm tải database
      sleep(1);
    }

    drush_log(dt('Đã hoàn thành lưu trữ @count bản ghi cũ.', array('@count' => number_format($processed))), 'success');
  }
  catch (Exception $e) {
    drush_log(dt('Lỗi khi lưu trữ dữ liệu cũ: @error', array('@error' => $e->getMessage())), 'error');
  }
}

/**
 * Làm sạch cache.
 */
function _drush_cassiopeia_db_optimize_cleanup_cache() {
  try {
    // Làm sạch cache Drupal
    cache_clear_all('*', 'cache', TRUE);
    cache_clear_all('*', 'cache_block', TRUE);
    cache_clear_all('*', 'cache_filter', TRUE);
    cache_clear_all('*', 'cache_page', TRUE);
    cache_clear_all('*', 'cache_menu', TRUE);
    cache_clear_all('*', 'cache_form', TRUE);

    // Làm sạch các bản ghi cache tự tạo đã hết hạn
    if (db_table_exists('cassiopeia_query_cache')) {
      $count = db_delete('cassiopeia_query_cache')
        ->condition('expire', REQUEST_TIME, '<')
        ->execute();

      drush_log(dt('Đã xóa @count bản ghi cache hết hạn.', array('@count' => $count)), 'notice');
    }

    drush_log(dt('Đã làm sạch cache thành công.'), 'success');
  }
  catch (Exception $e) {
    drush_log(dt('Lỗi khi làm sạch cache: @error', array('@error' => $e->getMessage())), 'error');
  }
}

/**
 * Tối ưu bảng dữ liệu.
 */
function _drush_cassiopeia_db_optimize_optimize_tables() {
  try {
    // Danh sách các bảng cần tối ưu
    $tables = array(
      'tbl_backlink',
      'tbl_backlink_detail',
      'node',
      'node_revision',
      'cassiopeia_content',
      'watchdog',
      'cassiopeia_query_cache',
      'cache',
      'cache_block',
      'cache_filter',
      'cache_form',
      'cache_menu',
      'cache_page',
    );

    // Thêm bảng từ module khác
    if (module_exists('cassiopeia_db_optimize')) {
      $more_tables = module_invoke_all('cassiopeia_db_optimize_tables');
      if (!empty($more_tables)) {
        $tables = array_merge($tables, $more_tables);
      }

      // Cho phép module khác sửa đổi danh sách
      drupal_alter('cassiopeia_db_optimize_tables', $tables);
    }

    $count = 0;
    foreach ($tables as $table) {
      // Kiểm tra bảng tồn tại
      if (db_table_exists($table)) {
        // Tối ưu bảng
        db_query('OPTIMIZE TABLE {' . $table . '}');
        drush_log(dt('Đã tối ưu bảng @table.', array('@table' => $table)), 'notice');
        $count++;
      }
      else {
        drush_log(dt('Bảng @table không tồn tại.', array('@table' => $table)), 'warning');
      }
    }

    drush_log(dt('Đã tối ưu @count bảng dữ liệu.', array('@count' => $count)), 'success');
  }
  catch (Exception $e) {
    drush_log(dt('Lỗi khi tối ưu bảng: @error', array('@error' => $e->getMessage())), 'error');
  }
}

/**
 * Làm sạch watchdog logs.
 */
function _drush_cassiopeia_db_optimize_cleanup_watchdog() {
  try {
    $days = drush_get_option('days', variable_get('cassiopeia_db_optimize_watchdog_retention', 30));
    $threshold = REQUEST_TIME - ($days * 86400);

    $count = db_delete('watchdog')
      ->condition('timestamp', $threshold, '<')
      ->execute();

    drush_log(dt('Đã xóa @count bản ghi log cũ hơn @days ngày.', array('@count' => $count, '@days' => $days)), 'success');
  }
  catch (Exception $e) {
    drush_log(dt('Lỗi khi làm sạch log: @error', array('@error' => $e->getMessage())), 'error');
  }
}

/**
 * Lấy kích thước bảng.
 */
function _drush_cassiopeia_db_optimize_get_table_size($table) {
  try {
    $size = db_query("SELECT data_length + index_length FROM information_schema.TABLES WHERE table_schema = DATABASE() AND table_name = :table", array(':table' => $table))->fetchField();
    return $size ? $size : 0;
  }
  catch (Exception $e) {
    return 0;
  }
}

/**
 * Format kích thước byte thành chuỗi dễ đọc.
 */
function _drush_cassiopeia_db_optimize_format_bytes($bytes) {
  $units = array('B', 'KB', 'MB', 'GB', 'TB');

  $bytes = max($bytes, 0);
  $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
  $pow = min($pow, count($units) - 1);

  $bytes /= pow(1024, $pow);

  return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Format interval thành chuỗi dễ đọc.
 */
function _drush_cassiopeia_db_optimize_format_interval($seconds) {
  if ($seconds < 60) {
    return dt('@count giây', array('@count' => $seconds));
  }
  elseif ($seconds < 3600) {
    return dt('@count phút', array('@count' => floor($seconds / 60)));
  }
  elseif ($seconds < 86400) {
    return dt('@count giờ', array('@count' => floor($seconds / 3600)));
  }
  else {
    return dt('@count ngày', array('@count' => floor($seconds / 86400)));
  }
}

/**
 * Lấy danh sách chỉ mục của bảng.
 */
function _drush_cassiopeia_db_optimize_get_table_indexes($table) {
  try {
    $indexes = array();
    $result = db_query("SHOW INDEX FROM {" . $table . "}");

    foreach ($result as $row) {
      $indexes[] = $row->Key_name;
    }

    return array_unique($indexes);
  }
  catch (Exception $e) {
    return array();
  }
}
