<?php
/**
 * @file
 * Cassiopeia Session Management - Quản lý phiên làm việc và chuyển hướng an toàn
 */

/**
 * Chuyển hướng an toàn, đảm bảo phiên làm việc được commit trước khi chuyển hướng
 *
 * @param string $path
 *   Đường dẫn cần chuyển hướng đến
 * @param array $options
 *   Tùy chọn cho drupal_goto
 * @param int $http_response_code
 *   Mã HTTP phản hồi
 */
function cassiopeia_safe_goto($path = '', array $options = array(), $http_response_code = 302) {
  // Đảm bảo phiên làm việc được lưu trước khi chuyển hướng
  drupal_session_commit();

  // Thực hiện chuyển hướng
  drupal_goto($path, $options, $http_response_code);
}

/**
 * Hoàn tất một quá trình xử lý không cần chuyển hướng, đảm bảo phiên làm việc được commit
 *
 * @param string $message
 *   Thông báo hiển thị cho người dùng (tùy chọn)
 * @param string $type
 *   Loại thông báo (status, warning, error)
 */
function cassiopeia_process_complete($message = NULL, $type = 'status') {
  // Đảm bảo phiên được lưu
  drupal_session_commit();

  // Hiển thị thông báo nếu được cung cấp
  if (!empty($message)) {
    drupal_set_message($message, $type);
  }
}

/**
 * Ghi dữ liệu vào phiên làm việc và đảm bảo phiên được lưu ngay lập tức
 *
 * @param string $key
 *   Khóa để lưu trữ dữ liệu
 * @param mixed $value
 *   Giá trị cần lưu trữ
 */
function cassiopeia_session_set($key, $value) {
  $_SESSION[$key] = $value;
  drupal_session_commit();
}

/**
 * Hoàn tất quá trình quét và xử lý
 *
 * @param array $data
 *   Dữ liệu phản hồi
 * @param string $message
 *   Thông báo đi kèm (tùy chọn)
 */
function cassiopeia_scan_complete(&$data, $message = "Đã quét xong!") {
  // Đặt thông báo
  drupal_set_message($message);

  // Đảm bảo phiên làm việc được commit
  drupal_session_commit();

  // Thêm thông tin kết quả để JavaScript xử lý
  $data['response'] = "OK";
  $data['message'] = $message;

  // Đặt một biến session báo hiệu quét đã hoàn tất
  $_SESSION['scan_completed'] = TRUE;
}
