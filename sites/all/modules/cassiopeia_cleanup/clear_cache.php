<?php

/**
 * @file
 * Cache clearing script for Drupal 7.
 */

// Đường dẫn tới file bootstrap.inc của Drupal
$drupal_root = realpath(dirname(__FILE__) . '/../../../..');
include_once $drupal_root . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

// Xóa tất cả cache
drupal_flush_all_caches();

echo "Đã xóa cache thành công!";
