<?php

/**
 * Script để clear cache Drupal
 */

// Bootstrap Drupal
define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

// Clear all caches
drupal_flush_all_caches();

print "Cache đã được clear thành công!\n";
