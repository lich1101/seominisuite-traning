<?php
// test_integration.php
// Test integration của disable script với Drupal

// Simulate Drupal environment
define('DRUPAL_ROOT', '/home/teso/seominisuite.com/public_html');

// Test function
function test_disable_script_integration() {
    echo "🧪 Testing Disable Script Integration\n";
    echo "=====================================\n\n";
    
    // Check if disable script exists
    $disable_script = DRUPAL_ROOT . '/scripts/disable_client_scripts.js';
    if (file_exists($disable_script)) {
        echo "✅ Disable script exists\n";
        echo "   Size: " . filesize($disable_script) . " bytes\n";
    } else {
        echo "❌ Disable script not found\n";
    }
    
    // Check if Puppeteer script exists
    $puppeteer_script = DRUPAL_ROOT . '/scripts/puppeteer_2captcha.js';
    if (file_exists($puppeteer_script)) {
        echo "✅ Puppeteer script exists\n";
        echo "   Size: " . filesize($puppeteer_script) . " bytes\n";
    } else {
        echo "❌ Puppeteer script not found\n";
    }
    
    // Test Puppeteer execution
    echo "\n🔧 Testing Puppeteer execution:\n";
    $test_data = array(
        'url' => 'https://www.google.com/recaptcha/api2/demo',
        'sitekey' => '6Le-wvkSAAAAAPBMRTvw0Q4Muexq9bi0DJwx_mJ-',
        'data_s' => ''
    );
    
    $json_data = json_encode($test_data);
    $command = "cd " . escapeshellarg(DRUPAL_ROOT . '/scripts') . " && node " . escapeshellarg($puppeteer_script) . " " . escapeshellarg($json_data) . " 2>&1";
    
    $output = shell_exec($command);
    echo "   Command: " . $command . "\n";
    echo "   Output: " . $output . "\n";
    
    if (strpos($output, '"success":true') !== false) {
        echo "✅ Puppeteer working correctly\n";
    } else {
        echo "❌ Puppeteer failed\n";
    }
}

// Run test
test_disable_script_integration();
