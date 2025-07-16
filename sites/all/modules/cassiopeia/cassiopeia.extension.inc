<?php
/**
 * @file
 * Module xử lý dữ liệu từ extension cho Cassiopeia.
 */

/**
 * Callback nhận dữ liệu từ extension.
 */
function cassiopeia_extension_data_callback() {
  // Nhận dữ liệu JSON
  $json_data = file_get_contents('php://input');
  $data = json_decode($json_data, TRUE);

  // Nếu không có dữ liệu JSON hợp lệ, thử kiểm tra POST/GET
  if (empty($data)) {
    // Kiểm tra POST
    if (!empty($_POST['data'])) {
      $data = $_POST['data'];
      if (is_string($data)) {
        $data = json_decode($data, TRUE);
      }
    }
    // Kiểm tra GET nếu không có POST
    elseif (!empty($_GET['data'])) {
      $data = $_GET['data'];
      if (is_string($data)) {
        $data = json_decode($data, TRUE);
      }
    }
  }

  // Kiểm tra xem có dữ liệu hợp lệ không
  if (empty($data)) {
    return array(
      'status' => 'error',
      'message' => 'Không có dữ liệu hợp lệ',
    );
  }

  // Thêm thông tin người dùng (nếu đã đăng nhập)
  global $user;

  // Chỉ thêm dữ liệu cần thiết về user để tránh thông tin quá nhiều
  $data['uid'] = $user->uid;
  $data['timestamp'] = REQUEST_TIME;

  // Nén dữ liệu nếu quá lớn
  if (isset($data['content']) && strlen($data['content']) > 1024) {
    $data['content_compressed'] = base64_encode(gzcompress($data['content'], 9));
    unset($data['content']);
    $data['is_compressed'] = true;
  }

  // Đưa dữ liệu vào hàng đợi xử lý
  return cassiopeia_receive_extension_data($data);
}

/**
 * Nhận dữ liệu từ extension và đưa vào hàng đợi xử lý.
 *
 * @param array $data
 *   Dữ liệu từ extension gửi đến.
 *
 * @return array
 *   Kết quả xử lý dạng JSON.
 */
function cassiopeia_receive_extension_data($data) {
  // Đảm bảo module queue đã được tải
  module_load_include('inc', 'cassiopeia', 'cassiopeia.queue');

  // Xác định ưu tiên dựa trên loại dữ liệu
  $priority = 5; // Mặc định
  if (!empty($data['type'])) {
    switch ($data['type']) {
      case 'backlink':
        $priority = 3; // Ưu tiên cao hơn
        break;
      case 'keyword':
        $priority = 4;
        break;
      case 'content':
        $priority = 5;
        break;
    }
  }

  // Thêm thông tin về thời gian nhận
  $data['received_time'] = REQUEST_TIME;

  // Tạo dữ liệu cho hàng đợi
  $queue_data = array(
    'type' => 'extension_data',
    'data' => $data,
    'created' => REQUEST_TIME,
  );

  // Thêm vào hàng đợi với mức ưu tiên đã xác định
  $result = cassiopeia_add_to_queue($queue_data, 'cassiopeia_processing_queue', $priority);

  // Trả về kết quả
  if ($result) {
    return array(
      'status' => 'success',
      'message' => 'Dữ liệu đã được thêm vào hàng đợi xử lý.',
      'queue_id' => $result,
      'items_in_queue' => cassiopeia_queue_count(),
    );
  }
  else {
    return array(
      'status' => 'error',
      'message' => 'Không thể thêm dữ liệu vào hàng đợi.',
    );
  }
}

/**
 * Xử lý dữ liệu từ extension.
 *
 * @param array $data
 *   Dữ liệu từ hàng đợi.
 */
function cassiopeia_process_extension_data($data) {
  // Chỉ ghi log thông tin cơ bản, không ghi toàn bộ dữ liệu
  watchdog('cassiopeia', 'Đang xử lý dữ liệu từ extension: type=@type, uid=@uid',
    array(
      '@type' => !empty($data['data']['type']) ? $data['data']['type'] : 'unknown',
      '@uid' => !empty($data['data']['uid']) ? $data['data']['uid'] : 0,
    ),
    WATCHDOG_INFO);

  // Xử lý dữ liệu từ extension
  if (!empty($data['data'])) {
    $extension_data = $data['data'];

    // Giải nén dữ liệu nếu cần
    if (!empty($extension_data['is_compressed']) && !empty($extension_data['content_compressed'])) {
      $extension_data['content'] = gzuncompress(base64_decode($extension_data['content_compressed']));
      unset($extension_data['content_compressed']);
      unset($extension_data['is_compressed']);
    }

    // Xử lý dữ liệu tùy theo loại
    if (!empty($extension_data['type'])) {
      switch ($extension_data['type']) {
        case 'backlink':
          _cassiopeia_process_backlink_data($extension_data);
          break;

        case 'keyword':
          _cassiopeia_process_keyword_data($extension_data);
          break;

        case 'content':
          _cassiopeia_process_content_data($extension_data);
          break;

        default:
          // Xử lý mặc định
          _cassiopeia_process_default_extension_data($extension_data);
          break;
      }
    }
    else {
      // Nếu không có loại dữ liệu, xử lý mặc định
      _cassiopeia_process_default_extension_data($extension_data);
    }

    // Ghi log hoàn thành
    watchdog('cassiopeia', 'Hoàn thành xử lý dữ liệu từ extension', array(), WATCHDOG_INFO);
  }
}

/**
 * Xử lý dữ liệu backlink từ extension.
 *
 * @param array $data
 *   Dữ liệu backlink.
 */
function _cassiopeia_process_backlink_data($data) {
  // Kiểm tra dữ liệu đầu vào
  if (empty($data['url']) || empty($data['uid'])) {
    watchdog('cassiopeia', 'Dữ liệu backlink không hợp lệ', array(), WATCHDOG_WARNING);
    return;
  }

  // Thêm logic xử lý backlink ở đây
  // ...

  // Ghi log kết quả ngắn gọn
  watchdog('cassiopeia', 'Xử lý backlink: @url', array('@url' => $data['url']), WATCHDOG_INFO);
}

/**
 * Xử lý dữ liệu từ khóa từ extension.
 *
 * @param array $data
 *   Dữ liệu từ khóa.
 */
function _cassiopeia_process_keyword_data($data) {
  // Kiểm tra dữ liệu đầu vào
  if (empty($data['keyword']) || empty($data['uid'])) {
    watchdog('cassiopeia', 'Dữ liệu từ khóa không hợp lệ', array(), WATCHDOG_WARNING);
    return;
  }

  // Thêm logic xử lý từ khóa ở đây
  // ...

  // Ghi log kết quả ngắn gọn
  watchdog('cassiopeia', 'Xử lý từ khóa: @keyword', array('@keyword' => $data['keyword']), WATCHDOG_INFO);
}

/**
 * Xử lý dữ liệu nội dung từ extension.
 *
 * @param array $data
 *   Dữ liệu nội dung.
 */
function _cassiopeia_process_content_data($data) {
  // Kiểm tra dữ liệu đầu vào
  if (empty($data['content']) || empty($data['uid'])) {
    watchdog('cassiopeia', 'Dữ liệu nội dung không hợp lệ', array(), WATCHDOG_WARNING);
    return;
  }

  // Thêm logic xử lý nội dung ở đây
  // ...

  // Ghi log kết quả ngắn gọn
  watchdog('cassiopeia', 'Xử lý nội dung từ: @url',
    array('@url' => isset($data['url']) ? $data['url'] : 'không có URL'),
    WATCHDOG_INFO);
}

/**
 * Xử lý dữ liệu mặc định từ extension.
 *
 * @param array $data
 *   Dữ liệu gốc.
 */
function _cassiopeia_process_default_extension_data($data) {
  // Xử lý dữ liệu không xác định loại
  watchdog('cassiopeia', 'Xử lý dữ liệu mặc định từ extension', array(), WATCHDOG_INFO);
}
