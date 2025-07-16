<?php

/**
 * @file
 * Công cụ xuất trực tiếp dữ liệu từ khóa ra Excel, bỏ qua module phpexcel.
 */

// Khởi tạo Drupal
define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

// Kiểm tra quyền
global $user;
if (!user_is_logged_in()) {
  drupal_access_denied();
  exit;
}

// Lấy tham số từ URL
$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
$data = isset($_GET['data']) ? $_GET['data'] : null;

// Kiểm tra dự án
if (empty($pid)) {
  drupal_set_message(t('Không tìm thấy dự án từ khóa!'), 'error');
  drupal_goto('quan-ly-keywords');
}

// Kiểm tra quyền xuất Excel
$packet = cassiopeia_get_available_packet_by_uid($user->uid);
if ($packet->product != BASIC && $packet->expired > 0 && $packet->expired < REQUEST_TIME) {
  drupal_set_message(t('Tài khoản của bạn đã hết hạn!'), 'error');
  drupal_goto("quan-ly-keywords/du-an/{$pid}");
}

if (empty($packet->excel)) {
  drupal_set_message(t('Gói dịch vụ của bạn không hỗ trợ xuất Excel!'), 'error');
  drupal_goto('price-board');
}

try {
  // Tạo truy vấn để lấy dữ liệu từ khóa
  $query = db_select("node", "tbl_node");
  $query->fields("tbl_node");
  $query->condition("type", "keyword");
  $query->orderBy("tbl_node.changed", "DESC");
  $query->join("field_data_field_keyword_project", "field_keyword_project", "field_keyword_project.entity_id=tbl_node.nid");
  $query->condition("field_keyword_project.field_keyword_project_nid", $pid);

  $query->leftJoin("field_data_field_keyword_old_position", "field_keyword_old_position", "field_keyword_old_position.entity_id=tbl_node.nid");
  $query->addField("field_keyword_old_position", "field_keyword_old_position_value", "field_keyword_old_position");

  $query->leftJoin("field_data_field_keyword_position", "field_keyword_position", "field_keyword_position.entity_id=tbl_node.nid");
  $query->addField("field_keyword_position", "field_keyword_position_value", "field_keyword_position");

  $query->leftJoin("field_data_field_keyword_best_position", "field_keyword_best_position", "field_keyword_best_position.entity_id=tbl_node.nid");
  $query->addField("field_keyword_best_position", "field_keyword_best_position_value", "field_keyword_best_position");

  $query->leftJoin("field_data_field_updated", "field_updated", "field_updated.entity_id=tbl_node.nid");
  $query->addField("field_updated", "field_updated_value", "field_updated");

  $query->leftJoin("field_data_field_url", "field_url", "field_url.entity_id=tbl_node.nid");
  $query->addField("field_url", "field_url_value", "field_url");

  // Nếu có data id, thì lấy từ session
  if (!empty($data) && isset($_SESSION['export_data'][$data])) {
    $query->condition("tbl_node.nid", $_SESSION['export_data'][$data], "IN");
  }

  // Lấy thông tin tag
  $sub = db_select("field_data_field_tags", "field_tags");
  $sub->fields("field_tags");
  $sub->addExpression("GROUP_CONCAT(field_tags.field_tags_tid SEPARATOR ',')", "field_tags");
  $sub->groupBy("field_tags.entity_id");

  $query->leftJoin($sub, "tbl_tags", "tbl_tags.entity_id=tbl_node.nid");
  $query->fields("tbl_tags");

  $keywords = $query->execute()->fetchAll();

  // Sử dụng PHPSpreadsheet trực tiếp
  require_once(DRUPAL_ROOT . "/sites/all/libraries/PhpOffice/autoload.php");

  use PhpOffice\PhpSpreadsheet\Spreadsheet;
  use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

  // Tạo workbook mới và set active sheet
  $project = node_load($pid);
  $spreadsheet = new Spreadsheet();
  $sheet = $spreadsheet->getActiveSheet();
  $sheet->setTitle(substr($project->title, 0, 31)); // Giới hạn 31 ký tự cho tên sheet

  // Thiết lập header
  $headers = array(
    "STT",
    "Từ khóa",
    "Thay đổi",
    "Vị trí hiện tại",
    "Vị trí cũ",
    "Vị trí tốt nhất",
    "URL SEO",
    "Tag",
    "Ngày cập nhật",
  );

  // Thêm headers vào sheet
  foreach ($headers as $col => $header) {
    $sheet->setCellValueByColumnAndRow($col + 1, 1, $header);
  }

  // Định dạng header
  $sheet->getStyle('A1:I1')->getFont()->setBold(true);

  // Thêm dữ liệu từ khóa vào sheet
  $row = 2;
  foreach ($keywords as $keyword) {
    $tags = '';
    if (!empty($keyword->field_tags)) {
      $tag_ids = explode(',', $keyword->field_tags);
      $tag_names = array();
      foreach ($tag_ids as $tid) {
        $term = taxonomy_term_load($tid);
        if ($term) {
          $tag_names[] = $term->name;
        }
      }
      $tags = implode(', ', $tag_names);
    }

    // Xác định biểu tượng thay đổi vị trí
    $status_class = '';
    $change = '';
    if (!empty($keyword->field_keyword_old_position) && !empty($keyword->field_keyword_position)) {
      $diff = $keyword->field_keyword_position - $keyword->field_keyword_old_position;
      if ($diff > 0) {
        $status_class = '-';
        $change = '- ' . abs($diff);
      } elseif ($diff < 0) {
        $status_class = '+';
        $change = '+ ' . abs($diff);
      } else {
        $change = '0';
      }
    } else {
      $change = '-';
    }

    // Đổ dữ liệu vào hàng
    $col = 1;
    $sheet->setCellValueByColumnAndRow($col++, $row, $row - 1); // STT
    $sheet->setCellValueByColumnAndRow($col++, $row, $keyword->title); // Từ khóa
    $sheet->setCellValueByColumnAndRow($col++, $row, $change); // Thay đổi
    $sheet->setCellValueByColumnAndRow($col++, $row, !empty($keyword->field_keyword_position) ? $keyword->field_keyword_position : '-'); // Vị trí hiện tại
    $sheet->setCellValueByColumnAndRow($col++, $row, !empty($keyword->field_keyword_old_position) ? $keyword->field_keyword_old_position : '-'); // Vị trí cũ
    $sheet->setCellValueByColumnAndRow($col++, $row, !empty($keyword->field_keyword_best_position) ? $keyword->field_keyword_best_position : '-'); // Vị trí tốt nhất
    $sheet->setCellValueByColumnAndRow($col++, $row, !empty($keyword->field_url_value) ? $keyword->field_url_value : '-'); // URL SEO
    $sheet->setCellValueByColumnAndRow($col++, $row, $tags); // Tag
    $sheet->setCellValueByColumnAndRow($col++, $row, date('d/m/Y', $keyword->changed)); // Ngày cập nhật

    $row++;
  }

  // Tự động điều chỉnh chiều rộng cột
  foreach (range('A', 'I') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
  }

  // Thiết lập header cho file tải về
  $filename = preg_replace('/[^a-z0-9_\-]/i', '_', $project->title) . '_tu_khoa_' . date('d_m_Y');
  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
  header('Cache-Control: max-age=0');

  // Tạo writer và xuất file
  ob_end_clean();
  $writer = new Xlsx($spreadsheet);
  $writer->save('php://output');
  exit;
}
catch (Exception $e) {
  watchdog('cassiopeia_cleanup', 'Lỗi khi xuất dữ liệu từ khóa: @error', array('@error' => $e->getMessage()), WATCHDOG_ERROR);
  drupal_set_message(t('Đã xảy ra lỗi khi xuất dữ liệu từ khóa: @error', array('@error' => $e->getMessage())), 'error');
  drupal_goto("quan-ly-keywords/du-an/{$pid}");
}
