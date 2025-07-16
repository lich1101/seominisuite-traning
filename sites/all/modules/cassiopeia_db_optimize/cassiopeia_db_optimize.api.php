<?php
/**
 * @file
 * API cho module Cassiopeia Database Optimizer.
 */

/**
 * Cho phép các module khác cung cấp danh sách bảng cần tối ưu.
 *
 * @return array
 *   Mảng tên bảng cần tối ưu.
 */
function hook_cassiopeia_db_optimize_tables() {
  return array(
    'my_custom_table_1',
    'my_custom_table_2',
  );
}

/**
 * Cho phép các module khác thay đổi danh sách bảng cần tối ưu.
 *
 * @param array $tables
 *   Mảng tên bảng cần tối ưu.
 *
 * @return array
 *   Mảng đã sửa đổi.
 */
function hook_cassiopeia_db_optimize_tables_alter(&$tables) {
  // Thêm bảng
  $tables[] = 'my_custom_table';

  // Loại bỏ bảng
  if (($key = array_search('some_table', $tables)) !== FALSE) {
    unset($tables[$key]);
  }

  return $tables;
}

/**
 * Cho phép các module khác thực hiện hành động sau khi tối ưu dữ liệu.
 */
function hook_cassiopeia_db_optimize_post_cleanup() {
  // Thực hiện các thao tác làm sạch dữ liệu bổ sung
  db_delete('my_custom_table')
    ->condition('timestamp', REQUEST_TIME - 30 * 86400, '<')
    ->execute();
}

/**
 * Cho phép các module khác ảnh hưởng đến thời gian lưu trữ của các bảng.
 *
 * @param array $retention
 *   Mảng chứa thời gian lưu trữ (ngày) cho mỗi bảng.
 *   - key: Tên bảng
 *   - value: Số ngày lưu trữ
 *
 * @return array
 *   Mảng đã sửa đổi.
 */
function hook_cassiopeia_db_optimize_retention_alter(&$retention) {
  // Thay đổi thời gian lưu trữ cho bảng tùy chỉnh
  $retention['my_custom_table'] = 180; // 6 tháng

  return $retention;
}
