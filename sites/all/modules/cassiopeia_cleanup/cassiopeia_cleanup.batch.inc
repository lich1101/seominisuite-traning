<?php

/**
 * @file
 * Batch processing cho việc làm trắng dữ liệu.
 */

/**
 * Thiết lập batch process.
 */
function cassiopeia_cleanup_run_batch($cleanup_options) {
  // Đặt thời gian xử lý tối đa
  $max_execution_time = !empty($cleanup_options['max_execution_time']) ? $cleanup_options['max_execution_time'] : 3600;
  set_time_limit($max_execution_time);

  // Xác định kích thước lô
  $batch_size = !empty($cleanup_options['batch_size']) ? $cleanup_options['batch_size'] : 50;

  // Nếu sử dụng SQL trực tiếp, xử lý riêng
  if (!empty($cleanup_options['use_direct_sql'])) {
    return cassiopeia_cleanup_run_direct_sql($cleanup_options);
  }

  $batch = array(
    'title' => t('Đang làm trắng dữ liệu...'),
    'operations' => array(),
    'init_message' => t('Đang khởi tạo...'),
    'progress_message' => t('Đã xử lý @current trên tổng số @total.'),
    'error_message' => t('Quá trình làm trắng dữ liệu đã gặp lỗi.'),
    'finished' => 'cassiopeia_cleanup_batch_finished',
    'file' => drupal_get_path('module', 'cassiopeia_cleanup') . '/cassiopeia_cleanup.batch.inc',
  );

  // Thiết lập các batch operations dựa trên tùy chọn của người dùng
  if (!empty($cleanup_options['cassiopeia_content'])) {
    $query = db_select('cassiopeia_content', 'c')
      ->fields('c', array('id'));
    $count = $query->countQuery()->execute()->fetchField();

    if ($count > 0) {
      $batch['operations'][] = array(
        'cassiopeia_cleanup_process_content',
        array($count, $batch_size)
      );
    }
  }

  if (!empty($cleanup_options['cassiopeia_guest_post'])) {
    $query = db_select('cassiopeia_guest_post_article', 'a')
      ->fields('a', array('id'));
    $count = $query->countQuery()->execute()->fetchField();

    if ($count > 0) {
      $batch['operations'][] = array(
        'cassiopeia_cleanup_process_guest_posts',
        array($count, $batch_size)
      );
    }
  }

  if (!empty($cleanup_options['cassiopeia_websites'])) {
    $query = db_select('cassiopeia_guest_post_website', 'w')
      ->fields('w', array('id'));
    $count = $query->countQuery()->execute()->fetchField();

    if ($count > 0) {
      $batch['operations'][] = array(
        'cassiopeia_cleanup_process_websites',
        array($count, $batch_size)
      );
    }
  }

  if (!empty($cleanup_options['nodes'])) {
    $query = db_select('node', 'n')
      ->fields('n', array('nid'));

    if (!empty($cleanup_options['nodes_types'])) {
      $node_types = array_filter($cleanup_options['nodes_types']);
      if (!empty($node_types)) {
        $query->condition('type', array_keys($node_types), 'IN');
      }
    }

    $count = $query->countQuery()->execute()->fetchField();

    if ($count > 0) {
      $batch['operations'][] = array(
        'cassiopeia_cleanup_process_nodes',
        array($count, min($batch_size, 20), $cleanup_options['nodes_types']) // Giới hạn ở mức 20 nodes mỗi lô
      );
    }
  }

  if (!empty($cleanup_options['comments'])) {
    $query = db_select('comment', 'c')
      ->fields('c', array('cid'));
    $count = $query->countQuery()->execute()->fetchField();

    if ($count > 0) {
      $batch['operations'][] = array(
        'cassiopeia_cleanup_process_comments',
        array($count, $batch_size)
      );
    }
  }

  if (!empty($cleanup_options['users'])) {
    $query = db_select('users', 'u')
      ->fields('u', array('uid'))
      ->condition('uid', 1, '>');
    $count = $query->countQuery()->execute()->fetchField();

    if ($count > 0) {
      $batch['operations'][] = array(
        'cassiopeia_cleanup_process_users',
        array($count, min($batch_size, 10)) // Giới hạn ở mức 10 users mỗi lô
      );
    }
  }

  if (!empty($cleanup_options['taxonomies'])) {
    $query = db_select('taxonomy_term_data', 't')
      ->fields('t', array('tid'));
    $count = $query->countQuery()->execute()->fetchField();

    if ($count > 0) {
      $batch['operations'][] = array(
        'cassiopeia_cleanup_process_taxonomies',
        array($count, $batch_size)
      );
    }
  }

  // Thêm một bước cuối cùng để xóa cache
  $batch['operations'][] = array('cassiopeia_cleanup_clear_cache', array());

  // Bắt đầu batch
  batch_set($batch);
}

/**
 * Thực hiện làm trắng dữ liệu bằng SQL trực tiếp.
 */
function cassiopeia_cleanup_run_direct_sql($cleanup_options) {
  // Tạo một giao dịch đơn
  $transaction = db_transaction();
  $results = array();

  try {
    // Tắt chế độ khóa khóa ngoại tạm thời
    if (db_driver() == 'mysql') {
      db_query("SET FOREIGN_KEY_CHECKS = 0");
    }

    // Xóa nội dung Cassiopeia
    if (!empty($cleanup_options['cassiopeia_content'])) {
      // Đếm trước khi xóa
      $count = db_query("SELECT COUNT(*) FROM {cassiopeia_content}")->fetchField();
      $results['cassiopeia_content'] = $count;

      // Xóa trực tiếp bằng TRUNCATE
      db_query("TRUNCATE TABLE {cassiopeia_content}");
      db_query("TRUNCATE TABLE {tbl_tags}");
    }

    // Xóa bài viết khách
    if (!empty($cleanup_options['cassiopeia_guest_post'])) {
      $count = db_query("SELECT COUNT(*) FROM {cassiopeia_guest_post_article}")->fetchField();
      $results['guest_posts'] = $count;

      db_query("TRUNCATE TABLE {cassiopeia_guest_post_article}");
      db_query("TRUNCATE TABLE {cassiopeia_guest_post_article_tag}");
    }

    // Xóa websites
    if (!empty($cleanup_options['cassiopeia_websites'])) {
      $count = db_query("SELECT COUNT(*) FROM {cassiopeia_guest_post_website}")->fetchField();
      $results['websites'] = $count;

      db_query("TRUNCATE TABLE {cassiopeia_guest_post_website}");
      db_query("TRUNCATE TABLE {cassiopeia_guest_post_website_category}");
    }

    // Xóa nodes
    if (!empty($cleanup_options['nodes'])) {
      $query = "SELECT COUNT(*) FROM {node}";
      $args = array();

      if (!empty($cleanup_options['nodes_types'])) {
        $node_types = array_filter($cleanup_options['nodes_types']);
        if (!empty($node_types)) {
          $placeholders = array();
          foreach (array_keys($node_types) as $i => $type) {
            $placeholders[] = ':type_' . $i;
            $args[':type_' . $i] = $type;
          }
          $query .= " WHERE type IN (" . implode(',', $placeholders) . ")";
        }
      }

      $count = db_query($query, $args)->fetchField();
      $results['nodes'] = $count;

      // Cần cẩn thận khi xóa nodes, nên xóa từng bảng liên quan
      if (!empty($cleanup_options['nodes_types'])) {
        $node_types = array_filter($cleanup_options['nodes_types']);
        if (!empty($node_types)) {
          $placeholders = array();
          foreach (array_keys($node_types) as $i => $type) {
            $placeholders[] = ':type_' . $i;
            $args[':type_' . $i] = $type;
          }

          // Lấy tất cả nid cần xóa
          $nids_query = "SELECT nid FROM {node} WHERE type IN (" . implode(',', $placeholders) . ")";
          $nids = db_query($nids_query, $args)->fetchCol();

          if (!empty($nids)) {
            // Xóa các bảng liên quan dựa trên nid
            db_delete('node_revision')->condition('nid', $nids, 'IN')->execute();
            db_delete('node_access')->condition('nid', $nids, 'IN')->execute();

            // Xóa trong các bảng field
            $field_tables = array();
            foreach (array_keys($node_types) as $type) {
              $fields = field_info_instances('node', $type);
              foreach ($fields as $field_name => $field) {
                $field_info = field_info_field($field_name);
                if (!empty($field_info['storage']['details']['sql'])) {
                  foreach ($field_info['storage']['details']['sql'] as $age => $table_info) {
                    foreach ($table_info as $table_name => $column_info) {
                      $field_tables[] = $table_name;
                    }
                  }
                }
              }
            }

            $field_tables = array_unique($field_tables);
            foreach ($field_tables as $table) {
              if (db_table_exists($table)) {
                db_delete($table)->condition('entity_id', $nids, 'IN')->execute();
              }
            }

            // Cuối cùng xóa node
            db_delete('node')->condition('nid', $nids, 'IN')->execute();
          }
        } else {
          // Nếu không có loại nội dung được chọn, xóa tất cả
          db_query("TRUNCATE TABLE {node_revision}");
          db_query("TRUNCATE TABLE {node_access}");
          db_query("TRUNCATE TABLE {node}");
        }
      } else {
        // Xóa tất cả nodes
        db_query("TRUNCATE TABLE {node_revision}");
        db_query("TRUNCATE TABLE {node_access}");
        db_query("TRUNCATE TABLE {node}");
      }
    }

    // Xóa bình luận
    if (!empty($cleanup_options['comments'])) {
      $count = db_query("SELECT COUNT(*) FROM {comment}")->fetchField();
      $results['comments'] = $count;

      db_query("TRUNCATE TABLE {comment}");
    }

    // Xóa users (trừ admin)
    if (!empty($cleanup_options['users'])) {
      $count = db_query("SELECT COUNT(*) FROM {users} WHERE uid > 1")->fetchField();
      $results['users'] = $count;

      // Xóa dữ liệu user trừ uid = 1
      db_delete('users')->condition('uid', 1, '>')->execute();
      db_delete('users_roles')->condition('uid', 1, '>')->execute();
      db_delete('authmap')->condition('uid', 1, '>')->execute();
    }

    // Xóa phân loại
    if (!empty($cleanup_options['taxonomies'])) {
      $count = db_query("SELECT COUNT(*) FROM {taxonomy_term_data}")->fetchField();
      $results['taxonomies'] = $count;

      db_query("TRUNCATE TABLE {taxonomy_term_hierarchy}");
      db_query("TRUNCATE TABLE {taxonomy_term_data}");
    }

    // Xóa cache
    db_query("TRUNCATE TABLE {cache}");
    db_query("TRUNCATE TABLE {cache_block}");
    db_query("TRUNCATE TABLE {cache_bootstrap}");
    db_query("TRUNCATE TABLE {cache_field}");
    db_query("TRUNCATE TABLE {cache_filter}");
    db_query("TRUNCATE TABLE {cache_form}");
    db_query("TRUNCATE TABLE {cache_image}");
    db_query("TRUNCATE TABLE {cache_menu}");
    db_query("TRUNCATE TABLE {cache_page}");
    db_query("TRUNCATE TABLE {cache_path}");

    // Khôi phục chế độ khóa ngoại
    if (db_driver() == 'mysql') {
      db_query("SET FOREIGN_KEY_CHECKS = 1");
    }

    // Thông báo kết quả
    $message = t('Đã làm trắng dữ liệu thành công:');
    foreach ($results as $type => $count) {
      switch ($type) {
        case 'cassiopeia_content':
          $message .= '<br />' . t('@count nội dung Cassiopeia đã bị xóa.', array('@count' => $count));
          break;
        case 'guest_posts':
          $message .= '<br />' . t('@count bài viết khách đã bị xóa.', array('@count' => $count));
          break;
        case 'websites':
          $message .= '<br />' . t('@count website đã bị xóa.', array('@count' => $count));
          break;
        case 'nodes':
          $message .= '<br />' . t('@count nội dung (nodes) đã bị xóa.', array('@count' => $count));
          break;
        case 'comments':
          $message .= '<br />' . t('@count bình luận đã bị xóa.', array('@count' => $count));
          break;
        case 'users':
          $message .= '<br />' . t('@count người dùng đã bị xóa.', array('@count' => $count));
          break;
        case 'taxonomies':
          $message .= '<br />' . t('@count thuật ngữ phân loại đã bị xóa.', array('@count' => $count));
          break;
      }
    }

    drupal_set_message($message);

  } catch (Exception $e) {
    // Nếu có lỗi, rollback transaction
    $transaction->rollback();
    watchdog_exception('cassiopeia_cleanup', $e);
    drupal_set_message(t('Đã xảy ra lỗi khi xóa dữ liệu: @error', array('@error' => $e->getMessage())), 'error');
  }

  // Chuyển hướng người dùng quay lại form
  drupal_goto('admin/config/cassiopeia/cleanup');
}

/**
 * Xử lý nội dung Cassiopeia theo lô.
 */
function cassiopeia_cleanup_process_content($total, $limit, &$context) {
  if (!isset($context['sandbox']['progress'])) {
    $context['sandbox']['progress'] = 0;
    $context['sandbox']['current_id'] = 0;
    $context['sandbox']['max'] = $total;
    $context['results']['cassiopeia_content'] = 0;
  }

  // Lấy một lô ID để xử lý
  $query = db_select('cassiopeia_content', 'c')
    ->fields('c', array('id'))
    ->condition('id', $context['sandbox']['current_id'], '>')
    ->orderBy('id', 'ASC')
    ->range(0, $limit);
  $ids = $query->execute()->fetchCol();

  if (!empty($ids)) {
    foreach ($ids as $id) {
      $node = new stdClass();
      $node->id = $id;
      if (function_exists('content_article_delete')) {
        content_article_delete($node);
      } else {
        // Nếu không có hàm xóa chuyên biệt, xóa trực tiếp
        db_delete('cassiopeia_content')
          ->condition('id', $id)
          ->execute();
        db_delete('tbl_tags')
          ->condition('nid', $id)
          ->execute();
      }

      $context['results']['cassiopeia_content']++;
      $context['sandbox']['progress']++;
      $context['sandbox']['current_id'] = $id;
    }
  }

  // Cập nhật tiến trình
  if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
    $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
  } else {
    // Đảm bảo xóa hết (nếu có sót)
    try {
      db_truncate('cassiopeia_content')->execute();
      db_truncate('tbl_tags')->execute();
    } catch (Exception $e) {
      // Ghi log nếu có lỗi
      watchdog('cassiopeia_cleanup', 'Lỗi khi làm trắng cassiopeia_content: @error', array('@error' => $e->getMessage()), WATCHDOG_ERROR);
    }
  }

  $context['message'] = t('Đang xóa nội dung Cassiopeia... (@progress / @total)',
    array('@progress' => $context['sandbox']['progress'], '@total' => $context['sandbox']['max']));
}

/**
 * Xử lý bài viết khách theo lô.
 */
function cassiopeia_cleanup_process_guest_posts($total, $limit, &$context) {
  if (!isset($context['sandbox']['progress'])) {
    $context['sandbox']['progress'] = 0;
    $context['sandbox']['current_id'] = 0;
    $context['sandbox']['max'] = $total;
    $context['results']['guest_posts'] = 0;
  }

  // Lấy một lô ID để xử lý
  $query = db_select('cassiopeia_guest_post_article', 'a')
    ->fields('a', array('id'))
    ->condition('id', $context['sandbox']['current_id'], '>')
    ->orderBy('id', 'ASC')
    ->range(0, $limit);
  $ids = $query->execute()->fetchCol();

  if (!empty($ids)) {
    foreach ($ids as $id) {
      if (function_exists('cassiopeia_guest_post_article_delete')) {
        cassiopeia_guest_post_article_delete($id);
      } else {
        // Nếu không có hàm xóa chuyên biệt, xóa trực tiếp
        db_delete('cassiopeia_guest_post_article')
          ->condition('id', $id)
          ->execute();
        db_delete('cassiopeia_guest_post_article_tag')
          ->condition('aid', $id)
          ->execute();
      }

      $context['results']['guest_posts']++;
      $context['sandbox']['progress']++;
      $context['sandbox']['current_id'] = $id;
    }
  }

  // Cập nhật tiến trình
  if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
    $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
  } else {
    // Đảm bảo xóa hết (nếu có sót)
    try {
      db_truncate('cassiopeia_guest_post_article')->execute();
      db_truncate('cassiopeia_guest_post_article_tag')->execute();
    } catch (Exception $e) {
      watchdog('cassiopeia_cleanup', 'Lỗi khi làm trắng guest posts: @error', array('@error' => $e->getMessage()), WATCHDOG_ERROR);
    }
  }

  $context['message'] = t('Đang xóa bài viết khách... (@progress / @total)',
    array('@progress' => $context['sandbox']['progress'], '@total' => $context['sandbox']['max']));
}

/**
 * Xử lý websites theo lô.
 */
function cassiopeia_cleanup_process_websites($total, $limit, &$context) {
  if (!isset($context['sandbox']['progress'])) {
    $context['sandbox']['progress'] = 0;
    $context['sandbox']['current_id'] = 0;
    $context['sandbox']['max'] = $total;
    $context['results']['websites'] = 0;
  }

  // Lấy một lô ID để xử lý
  $query = db_select('cassiopeia_guest_post_website', 'w')
    ->fields('w', array('id'))
    ->condition('id', $context['sandbox']['current_id'], '>')
    ->orderBy('id', 'ASC')
    ->range(0, $limit);
  $ids = $query->execute()->fetchCol();

  if (!empty($ids)) {
    foreach ($ids as $id) {
      $node = new stdClass();
      $node->id = $id;
      if (function_exists('cassiopeia_guest_post_website_delete')) {
        cassiopeia_guest_post_website_delete($node);
      } else {
        // Nếu không có hàm xóa chuyên biệt, xóa trực tiếp
        db_delete('cassiopeia_guest_post_website')
          ->condition('id', $id)
          ->execute();
      }

      $context['results']['websites']++;
      $context['sandbox']['progress']++;
      $context['sandbox']['current_id'] = $id;
    }
  }

  // Cập nhật tiến trình
  if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
    $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
  } else {
    // Đảm bảo xóa hết (nếu có sót)
    try {
      db_truncate('cassiopeia_guest_post_website')->execute();
      db_truncate('cassiopeia_guest_post_website_category')->execute();
    } catch (Exception $e) {
      watchdog('cassiopeia_cleanup', 'Lỗi khi làm trắng websites: @error', array('@error' => $e->getMessage()), WATCHDOG_ERROR);
    }
  }

  $context['message'] = t('Đang xóa websites... (@progress / @total)',
    array('@progress' => $context['sandbox']['progress'], '@total' => $context['sandbox']['max']));
}

/**
 * Xử lý nodes theo lô.
 */
function cassiopeia_cleanup_process_nodes($total, $limit, $node_types, &$context) {
  if (!isset($context['sandbox']['progress'])) {
    $context['sandbox']['progress'] = 0;
    $context['sandbox']['current_nid'] = 0;
    $context['sandbox']['max'] = $total;
    $context['results']['nodes'] = 0;
  }

  // Lấy một lô NID để xử lý
  $query = db_select('node', 'n')
    ->fields('n', array('nid'))
    ->condition('nid', $context['sandbox']['current_nid'], '>')
    ->orderBy('nid', 'ASC')
    ->range(0, $limit);

  // Áp dụng filter theo loại nội dung
  if (!empty($node_types)) {
    $node_types = array_filter($node_types);
    if (!empty($node_types)) {
      $query->condition('type', array_keys($node_types), 'IN');
    }
  }

  $nids = $query->execute()->fetchCol();

  if (!empty($nids)) {
    // Xóa từng node một để tránh quá tải
    foreach ($nids as $nid) {
      try {
        node_delete($nid);
        $context['results']['nodes']++;
      } catch (Exception $e) {
        watchdog('cassiopeia_cleanup', 'Lỗi khi xóa node @nid: @error',
          array('@nid' => $nid, '@error' => $e->getMessage()), WATCHDOG_ERROR);
      }

      $context['sandbox']['progress']++;
      $context['sandbox']['current_nid'] = $nid;
    }
  }

  // Cập nhật tiến trình
  if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
    $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
  }

  $context['message'] = t('Đang xóa nội dung... (@progress / @total)',
    array('@progress' => $context['sandbox']['progress'], '@total' => $context['sandbox']['max']));
}

/**
 * Xử lý comments theo lô.
 */
function cassiopeia_cleanup_process_comments($total, $limit, &$context) {
  if (!isset($context['sandbox']['progress'])) {
    $context['sandbox']['progress'] = 0;
    $context['sandbox']['current_cid'] = 0;
    $context['sandbox']['max'] = $total;
    $context['results']['comments'] = 0;
  }

  // Lấy một lô CID để xử lý
  $query = db_select('comment', 'c')
    ->fields('c', array('cid'))
    ->condition('cid', $context['sandbox']['current_cid'], '>')
    ->orderBy('cid', 'ASC')
    ->range(0, $limit);
  $cids = $query->execute()->fetchCol();

  if (!empty($cids)) {
    try {
      comment_delete_multiple($cids);
      $context['results']['comments'] += count($cids);
    } catch (Exception $e) {
      watchdog('cassiopeia_cleanup', 'Lỗi khi xóa comments: @error',
        array('@error' => $e->getMessage()), WATCHDOG_ERROR);
    }

    // Cập nhật tiến trình
    $context['sandbox']['progress'] += count($cids);
    $context['sandbox']['current_cid'] = end($cids);
  }

  if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
    $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
  }

  $context['message'] = t('Đang xóa bình luận... (@progress / @total)',
    array('@progress' => $context['sandbox']['progress'], '@total' => $context['sandbox']['max']));
}

/**
 * Xử lý users theo lô.
 */
function cassiopeia_cleanup_process_users($total, $limit, &$context) {
  if (!isset($context['sandbox']['progress'])) {
    $context['sandbox']['progress'] = 0;
    $context['sandbox']['current_uid'] = 1; // Bắt đầu từ sau UID 1 (admin)
    $context['sandbox']['max'] = $total;
    $context['results']['users'] = 0;
  }

  // Lấy một lô UID để xử lý
  $query = db_select('users', 'u')
    ->fields('u', array('uid'))
    ->condition('uid', $context['sandbox']['current_uid'], '>')
    ->condition('uid', 1, '>') // Không xóa admin
    ->orderBy('uid', 'ASC')
    ->range(0, $limit);
  $uids = $query->execute()->fetchCol();

  if (!empty($uids)) {
    try {
      user_delete_multiple($uids);
      $context['results']['users'] += count($uids);
    } catch (Exception $e) {
      watchdog('cassiopeia_cleanup', 'Lỗi khi xóa users: @error',
        array('@error' => $e->getMessage()), WATCHDOG_ERROR);
    }

    // Cập nhật tiến trình
    $context['sandbox']['progress'] += count($uids);
    $context['sandbox']['current_uid'] = end($uids);
  }

  if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
    $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
  }

  $context['message'] = t('Đang xóa người dùng... (@progress / @total)',
    array('@progress' => $context['sandbox']['progress'], '@total' => $context['sandbox']['max']));
}

/**
 * Xử lý taxonomies theo lô.
 */
function cassiopeia_cleanup_process_taxonomies($total, $limit, &$context) {
  if (!isset($context['sandbox']['progress'])) {
    $context['sandbox']['progress'] = 0;
    $context['sandbox']['current_tid'] = 0;
    $context['sandbox']['max'] = $total;
    $context['results']['taxonomies'] = 0;
  }

  // Lấy một lô TID để xử lý
  $query = db_select('taxonomy_term_data', 't')
    ->fields('t', array('tid'))
    ->condition('tid', $context['sandbox']['current_tid'], '>')
    ->orderBy('tid', 'ASC')
    ->range(0, $limit);
  $tids = $query->execute()->fetchCol();

  if (!empty($tids)) {
    foreach ($tids as $tid) {
      try {
        taxonomy_term_delete($tid);
        $context['results']['taxonomies']++;
      } catch (Exception $e) {
        watchdog('cassiopeia_cleanup', 'Lỗi khi xóa taxonomy term @tid: @error',
          array('@tid' => $tid, '@error' => $e->getMessage()), WATCHDOG_ERROR);
      }

      $context['sandbox']['progress']++;
      $context['sandbox']['current_tid'] = $tid;
    }
  }

  if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
    $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
  }

  $context['message'] = t('Đang xóa phân loại... (@progress / @total)',
    array('@progress' => $context['sandbox']['progress'], '@total' => $context['sandbox']['max']));
}

/**
 * Xóa cache.
 */
function cassiopeia_cleanup_clear_cache(&$context) {
  // Xóa tất cả cache
  cache_clear_all('*', 'cache', TRUE);
  cache_clear_all('*', 'cache_block', TRUE);
  cache_clear_all('*', 'cache_bootstrap', TRUE);
  cache_clear_all('*', 'cache_field', TRUE);
  cache_clear_all('*', 'cache_filter', TRUE);
  cache_clear_all('*', 'cache_form', TRUE);
  cache_clear_all('*', 'cache_menu', TRUE);
  cache_clear_all('*', 'cache_page', TRUE);
  cache_clear_all('*', 'cache_path', TRUE);

  // Xóa watchdog nếu cần
  try {
    db_truncate('watchdog')->execute();
  } catch (Exception $e) {
    // Bỏ qua lỗi
  }

  $context['message'] = t('Đang xóa cache...');
  $context['results']['cache_cleared'] = TRUE;
}

/**
 * Hàm callback khi batch hoàn thành.
 */
function cassiopeia_cleanup_batch_finished($success, $results, $operations) {
  if ($success) {
    // Xây dựng thông báo kết quả
    $message = t('Đã làm trắng dữ liệu thành công:');
    foreach ($results as $type => $count) {
      if ($type == 'cache_cleared') {
        continue;
      }

      switch ($type) {
        case 'cassiopeia_content':
          $message .= '<br />' . t('@count nội dung Cassiopeia đã bị xóa.', array('@count' => $count));
          break;
        case 'guest_posts':
          $message .= '<br />' . t('@count bài viết khách đã bị xóa.', array('@count' => $count));
          break;
        case 'websites':
          $message .= '<br />' . t('@count website đã bị xóa.', array('@count' => $count));
          break;
        case 'nodes':
          $message .= '<br />' . t('@count nội dung (nodes) đã bị xóa.', array('@count' => $count));
          break;
        case 'comments':
          $message .= '<br />' . t('@count bình luận đã bị xóa.', array('@count' => $count));
          break;
        case 'users':
          $message .= '<br />' . t('@count người dùng đã bị xóa.', array('@count' => $count));
          break;
        case 'taxonomies':
          $message .= '<br />' . t('@count thuật ngữ phân loại đã bị xóa.', array('@count' => $count));
          break;
      }
    }

    drupal_set_message($message);
  }
  else {
    // Đã xảy ra lỗi
    drupal_set_message(t('Quá trình làm trắng dữ liệu đã gặp lỗi.'), 'error');
  }
}
