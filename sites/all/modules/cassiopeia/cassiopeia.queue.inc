<?php
/**
 * @file
 * Queue handlers for Cassiopeia module.
 */

/**
 * Add an item to the Cassiopeia processing queue.
 *
 * @param array $data
 *   Data to be processed later. Should include all needed info.
 * @param string $queue_name
 *   Name of the queue to add to. Default is 'cassiopeia_processing_queue'.
 * @param int $priority
 *   Priority of the item (1-10, 1 is highest). Default is 5.
 *
 * @return bool
 *   TRUE if the item was added to the queue.
 */
function cassiopeia_add_to_queue($data, $queue_name = 'cassiopeia_processing_queue', $priority = 5) {
  // Ensure priority is between 1-10
  $priority = max(1, min(10, $priority));

  // Tối ưu dữ liệu trước khi thêm vào queue
  $queue_data = array(
    'priority' => $priority,
    'retry_count' => isset($data['retry_count']) ? $data['retry_count'] : 0,
    'created' => REQUEST_TIME,
    'type' => isset($data['type']) ? $data['type'] : 'default',
    'data' => $data,
    'hash' => md5(serialize($data)), // Thêm hash để tránh trùng lặp
  );

  // Kiểm tra trùng lặp trước khi thêm
  if (cassiopeia_check_duplicate_queue_item($queue_data['hash'], $queue_name)) {
    watchdog('cassiopeia', 'Duplicate queue item detected, skipping: @hash',
      array('@hash' => $queue_data['hash']),
      WATCHDOG_INFO);
    return FALSE;
  }

  // Get a queue object
  $queue = DrupalQueue::get($queue_name);

  // Create the queue if it doesn't exist
  $queue->createQueue();

  // Add the item to the queue
  return $queue->createItem($queue_data);
}

/**
 * Check for duplicate queue items.
 *
 * @param string $hash
 *   Hash of the queue item data.
 * @param string $queue_name
 *   Name of the queue to check.
 *
 * @return bool
 *   TRUE if duplicate exists.
 */
function cassiopeia_check_duplicate_queue_item($hash, $queue_name) {
  // Sử dụng cache để kiểm tra trùng lặp nhanh
  $cache_key = 'cassiopeia_queue_hash:' . $hash;
  $cache = cache_get($cache_key);

  if ($cache) {
    return TRUE;
  }

  // Nếu không có trong cache, kiểm tra trong queue
  $queue = DrupalQueue::get($queue_name);
  $items = $queue->claimItem(100); // Lấy 100 item gần nhất

  foreach ($items as $item) {
    if (isset($item->data['hash']) && $item->data['hash'] === $hash) {
      // Lưu vào cache để tránh kiểm tra lại
      cache_set($cache_key, TRUE, 'cache', REQUEST_TIME + 3600);
      return TRUE;
    }
  }

  return FALSE;
}

/**
 * Process items in the Cassiopeia queue.
 *
 * @param string $queue_name
 *   Name of the queue to process. Default is 'cassiopeia_processing_queue'.
 * @param int $limit
 *   Maximum number of items to process. Default is 10.
 * @param int $max_execution_time
 *   Maximum execution time in seconds. Default is 30.
 *
 * @return int
 *   Number of items processed.
 */
function cassiopeia_process_queue($queue_name = 'cassiopeia_processing_queue', $limit = 50, $max_execution_time = 120) {
  // Get the queue
  $queue = DrupalQueue::get($queue_name);

  // Count how many items we've processed
  $count = 0;
  $processed_items = array();

  // Set time limit
  $start_time = time();
  $max_time_reached = false;

  // Tăng giới hạn thời gian thực thi PHP nếu có thể
  if (!ini_get('safe_mode')) {
    $orig_time_limit = ini_get('max_execution_time');
    if ($orig_time_limit < $max_execution_time && $orig_time_limit != 0) {
      set_time_limit($max_execution_time);
    }
  }

  // Lấy nhiều item cùng lúc để xử lý
  $items = $queue->claimItem($limit);

  if (empty($items)) {
    return 0;
  }

  // Nhóm các item theo loại để xử lý hàng loạt
  $items_by_type = array();
  foreach ($items as $item) {
    $type = isset($item->data['type']) ? $item->data['type'] : 'default';
    $items_by_type[$type][] = $item;
  }

  // Xử lý từng nhóm item
  foreach ($items_by_type as $type => $type_items) {
    // Kiểm tra thời gian
    if ((time() - $start_time) >= $max_execution_time) {
      $max_time_reached = true;
      break;
    }

    try {
      // Xử lý hàng loạt nếu có thể
      if (function_exists('cassiopeia_process_batch_' . $type)) {
        call_user_func('cassiopeia_process_batch_' . $type, $type_items);
        foreach ($type_items as $item) {
          $queue->deleteItem($item);
          $count++;
        }
      }
      else {
        // Xử lý từng item nếu không có hàm xử lý hàng loạt
        foreach ($type_items as $item) {
          if (function_exists('cassiopeia_process_' . $type)) {
            call_user_func('cassiopeia_process_' . $type, $item->data);
          }
          else {
            cassiopeia_process_default($item->data);
          }
          $queue->deleteItem($item);
          $count++;
        }
      }
    }
    catch (Exception $e) {
      // Xử lý lỗi cho từng item
      foreach ($type_items as $item) {
        $retry_count = isset($item->data['retry_count']) ? $item->data['retry_count'] : 0;
        $max_retries = variable_get('cassiopeia_queue_max_retries', 5);

        watchdog('cassiopeia', 'Error processing queue item: @message',
          array('@message' => $e->getMessage()),
          WATCHDOG_ERROR);

        if ($retry_count < $max_retries) {
          $item->data['retry_count'] = $retry_count + 1;
          $delay = pow(2, $retry_count);
          $item->data['next_retry'] = REQUEST_TIME + $delay;
          cassiopeia_add_to_queue($item->data, $queue_name);
        }
        else {
          cassiopeia_add_to_queue($item->data, 'cassiopeia_failed_queue');
        }
        $queue->deleteItem($item);
      }
    }
  }

  return $count;
}

/**
 * Process a batch of items from the queue.
 *
 * This function is designed to be run via batch API for admin operations.
 *
 * @param int $batch_size
 *   Number of items to process in this batch.
 * @param array $context
 *   Batch context information.
 */
function cassiopeia_process_queue_batch($batch_size, &$context) {
  // Initialize results array
  if (!isset($context['results']['processed'])) {
    $context['results']['processed'] = 0;
  }

  // Process a batch of items
  $count = cassiopeia_process_queue('cassiopeia_processing_queue', $batch_size, 20);

  // Update progress
  $context['results']['processed'] += $count;
  $context['message'] = t('Processed @count items', array('@count' => $context['results']['processed']));

  // Check if queue is empty
  $queue = DrupalQueue::get('cassiopeia_processing_queue');
  $context['finished'] = ($queue->numberOfItems() == 0);
}

/**
 * Default processor for queue items.
 *
 * @param array $data
 *   The data from the queue item.
 */
function cassiopeia_process_default($data) {
  // Default implementation - only log minimal info
  watchdog('cassiopeia', 'Processing default queue item of type: @type',
    array('@type' => isset($data['type']) ? $data['type'] : 'unknown'),
    WATCHDOG_INFO);
}

/**
 * Get number of items in a queue.
 *
 * @param string $queue_name
 *   Name of the queue.
 *
 * @return int
 *   Number of items in the queue.
 */
function cassiopeia_queue_count($queue_name = 'cassiopeia_processing_queue') {
  $queue = DrupalQueue::get($queue_name);
  return $queue->numberOfItems();
}

/**
 * Clean up failed queue items that are older than a specified time.
 *
 * @param int $age
 *   Age in seconds. Default is 7 days.
 *
 * @return int
 *   Number of items cleaned up.
 */
function cassiopeia_clean_failed_queue($age = 604800) {
  // This would require a custom implementation with database access
  // For now, we'll just return 0
  return 0;
}
