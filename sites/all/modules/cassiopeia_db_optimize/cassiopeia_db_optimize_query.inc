<?php
/**
 * @file
 * Các truy vấn tối ưu được sử dụng trong module Cassiopeia.
 */

/**
 * Truy vấn tối ưu dự án backlink.
 *
 * @param int $pid
 *   ID dự án.
 * @param array $options
 *   Các tùy chọn truy vấn.
 *
 * @return array
 *   Dữ liệu backlink.
 */
function cassiopeia_db_optimize_get_backlinks($pid, $options = array()) {
  $defaults = array(
    'limit' => 50,
    'offset' => 0,
    'order_by' => 'changed',
    'order_dir' => 'DESC',
    'use_cache' => TRUE,
    'cache_lifetime' => 3600,
  );

  $options = array_merge($defaults, $options);
  $cache_key = cassiopeia_db_optimize_cache_key('backlinks', array('pid' => $pid, 'options' => $options));

  // Kiểm tra cache
  if ($options['use_cache']) {
    $cached = cassiopeia_db_optimize_cache_get($cache_key);
    if ($cached !== FALSE) {
      return $cached;
    }
  }

  // Thực hiện truy vấn
  try {
    // Sử dụng các index đã tạo cho truy vấn hiệu quả hơn
    $query = db_select('tbl_backlink', 'tbl_backlink')
      ->fields('tbl_backlink')
      ->condition('tbl_backlink.pid', $pid)
      ->orderBy($options['order_by'], $options['order_dir'])
      ->range($options['offset'], $options['limit']);

    // Thêm các điều kiện tùy chọn
    if (isset($options['conditions']) && is_array($options['conditions'])) {
      foreach ($options['conditions'] as $field => $value) {
        $query->condition('tbl_backlink.' . $field, $value);
      }
    }

    // Tối ưu LEFT JOIN - thay vì join tất cả một lúc, chỉ join những gì cần thiết
    if (!empty($options['include_details'])) {
      $query->leftJoin('tbl_backlink_detail', 'tbl_backlink_detail', 'tbl_backlink_detail.nid = tbl_backlink.id');
      $query->fields('tbl_backlink_detail');
    }

    if (!empty($options['include_tags'])) {
      // Sử dụng subquery để lấy tags thay vì nhiều JOIN
      $sub = db_select('tbl_backlink', 'b');
      $sub->fields('b', array('id'));
      $sub->join('tbl_tags', 't', 't.nid = b.id');
      $sub->join('taxonomy_term_data', 'ttd', 'ttd.tid = t.tid');
      $sub->groupBy('b.id');
      $sub->addExpression("GROUP_CONCAT(ttd.name SEPARATOR ', ')", 'tags');

      $query->leftJoin($sub, 'sub_tags', 'sub_tags.id = tbl_backlink.id');
      $query->addField('sub_tags', 'tags', 'tags');
    }

    $result = $query->execute()->fetchAll();

    // Cache kết quả
    if ($options['use_cache']) {
      cassiopeia_db_optimize_cache_set($cache_key, $result, $options['cache_lifetime']);
    }

    return $result;
  }
  catch (Exception $e) {
    watchdog('cassiopeia_db_optimize', 'Lỗi truy vấn backlink: @error', array('@error' => $e->getMessage()), WATCHDOG_ERROR);
    return array();
  }
}

/**
 * Truy vấn tối ưu dự án keyword.
 *
 * @param int $pid
 *   ID dự án.
 * @param array $options
 *   Các tùy chọn truy vấn.
 *
 * @return array
 *   Dữ liệu keyword.
 */
function cassiopeia_db_optimize_get_keyword_project($pid, $options = array()) {
  $defaults = array(
    'limit' => 50,
    'offset' => 0,
    'order_by' => 'changed',
    'order_dir' => 'DESC',
    'use_cache' => TRUE,
    'cache_lifetime' => 3600,
  );

  $options = array_merge($defaults, $options);

  // Kiểm tra cache
  if ($options['use_cache']) {
    $cache_key = cassiopeia_db_optimize_cache_key('keyword_project', array('pid' => $pid, 'options' => $options));
    $cached = cassiopeia_db_optimize_cache_get($cache_key);
    if ($cached !== FALSE) {
      return $cached;
    }
  }

  // Thực hiện truy vấn
  try {
    $query = db_select('node', 'n');
    $query->fields('n');
    $query->condition('n.type', 'project_keyword');
    $query->condition('n.nid', $pid);

    // Tối ưu bằng cách sử dụng subquery thay vì nhiều join
    if (!empty($options['include_keywords'])) {
      $sub = db_select('node', 'kn');
      $sub->fields('kn', array('nid', 'title'));
      $sub->condition('kn.type', 'keyword');
      $sub->join('node_keyword', 'nk', 'nk.kid = kn.nid');
      $sub->condition('nk.pid', $pid);
      $sub->orderBy('kn.title', 'ASC');

      // Lấy kết quả của subquery thay vì join vào truy vấn chính
      $keywords = $sub->execute()->fetchAll();

      // Lấy dự án
      $project = $query->execute()->fetchObject();

      // Kết hợp dữ liệu
      if ($project) {
        $project->keywords = $keywords;

        // Cache kết quả
        if ($options['use_cache']) {
          cassiopeia_db_optimize_cache_set($cache_key, $project, $options['cache_lifetime']);
        }

        return $project;
      }
    }
    else {
      $result = $query->execute()->fetchObject();

      // Cache kết quả
      if ($options['use_cache']) {
        cassiopeia_db_optimize_cache_set($cache_key, $result, $options['cache_lifetime']);
      }

      return $result;
    }
  }
  catch (Exception $e) {
    watchdog('cassiopeia_db_optimize', 'Lỗi truy vấn dự án keyword: @error', array('@error' => $e->getMessage()), WATCHDOG_ERROR);
    return NULL;
  }

  return NULL;
}

/**
 * Truy vấn tối ưu danh sách người dùng.
 *
 * @param array $options
 *   Các tùy chọn truy vấn.
 *
 * @return array
 *   Danh sách người dùng.
 */
function cassiopeia_db_optimize_get_users($options = array()) {
  $defaults = array(
    'limit' => 50,
    'offset' => 0,
    'roles' => array(), // ID vai trò
    'excludes' => array(), // ID người dùng loại trừ
    'order_by' => 'u.created',
    'order_dir' => 'DESC',
    'use_cache' => TRUE,
    'cache_lifetime' => 600, // cache ngắn hơn cho dữ liệu người dùng
  );

  $options = array_merge($defaults, $options);

  // Kiểm tra cache
  if ($options['use_cache']) {
    $cache_key = cassiopeia_db_optimize_cache_key('users', $options);
    $cached = cassiopeia_db_optimize_cache_get($cache_key);
    if ($cached !== FALSE) {
      return $cached;
    }
  }

  // Thực hiện truy vấn tối ưu
  try {
    // Sử dụng truy vấn cấu trúc thay vì subquery phức tạp
    $query = db_select('users', 'u');
    $query->fields('u', array('uid', 'name', 'mail', 'created', 'access'));

    // Thêm điều kiện vai trò nếu cần
    if (!empty($options['roles'])) {
      $query->join('users_roles', 'ur', 'ur.uid = u.uid');
      $query->condition('ur.rid', $options['roles'], 'IN');
      $query->distinct(); // Tránh trùng lặp khi người dùng có nhiều vai trò
    }

    // Loại trừ người dùng nếu cần
    if (!empty($options['excludes'])) {
      $query->condition('u.uid', $options['excludes'], 'NOT IN');
    }

    // Truy vấn profile nếu cần
    if (!empty($options['include_profile'])) {
      $query->leftJoin('field_data_field_full_name', 'fn', 'fn.entity_id = u.uid');
      $query->leftJoin('field_data_field_tel', 'ft', 'ft.entity_id = u.uid');

      $query->addField('fn', 'field_full_name_value', 'full_name');
      $query->addField('ft', 'field_tel_value', 'tel');
    }

    // Thứ tự và giới hạn
    if (!empty($options['order_by'])) {
      $query->orderBy($options['order_by'], $options['order_dir']);
    }

    $query->range($options['offset'], $options['limit']);

    $result = $query->execute()->fetchAll();

    // Cache kết quả
    if ($options['use_cache']) {
      cassiopeia_db_optimize_cache_set($cache_key, $result, $options['cache_lifetime']);
    }

    return $result;
  }
  catch (Exception $e) {
    watchdog('cassiopeia_db_optimize', 'Lỗi truy vấn người dùng: @error', array('@error' => $e->getMessage()), WATCHDOG_ERROR);
    return array();
  }
}
