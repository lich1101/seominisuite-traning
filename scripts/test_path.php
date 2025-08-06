<?php
// Test script để kiểm tra đường dẫn và thực thi puppeteer

// Giả lập DRUPAL_ROOT
define('DRUPAL_ROOT', '/home/teso/seominisuite.com/public_html');

// Test data
$data = new stdClass();
$data->websiteURL = 'https://www.google.com/recaptcha/api2/demo';
$data->websiteKey = '6Le-wvkSAAAAAPBMRTvw0Q4Muexq9bi0DJwx_mJ-';
$data->recaptchaDataSValue = '';

echo "🧪 Test Puppeteer Integration\n";
echo "=============================\n\n";

// Test 1: Kiểm tra đường dẫn
$script_path = DRUPAL_ROOT . '/scripts/puppeteer_2captcha.js';
echo "1. Kiểm tra đường dẫn script:\n";
echo "   Script path: $script_path\n";
echo "   File exists: " . (file_exists($script_path) ? "✅ YES" : "❌ NO") . "\n\n";

// Test 2: Kiểm tra thư mục scripts
$scripts_dir = DRUPAL_ROOT . '/scripts';
echo "2. Kiểm tra thư mục scripts:\n";
echo "   Scripts dir: $scripts_dir\n";
echo "   Dir exists: " . (is_dir($scripts_dir) ? "✅ YES" : "❌ NO") . "\n";
echo "   Files in dir:\n";
if (is_dir($scripts_dir)) {
    $files = scandir($scripts_dir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "     - $file\n";
        }
    }
}
echo "\n";

// Test 3: Kiểm tra Node.js
echo "3. Kiểm tra Node.js:\n";
$node_version = shell_exec('node --version 2>&1');
echo "   Node version: " . trim($node_version) . "\n";
echo "   Node available: " . (strpos($node_version, 'v') === 0 ? "✅ YES" : "❌ NO") . "\n\n";

// Test 4: Test thực thi script
echo "4. Test thực thi script:\n";
$script_data = array(
    'url' => $data->websiteURL,
    'sitekey' => $data->websiteKey,
    'data_s' => $data->recaptchaDataSValue
);

$json_data = json_encode($script_data);
$command = "cd " . escapeshellarg($scripts_dir) . " && node " . escapeshellarg($script_path) . " " . escapeshellarg($json_data) . " 2>&1";

echo "   Command: $command\n";
echo "   Executing...\n";

$output = shell_exec($command);
echo "   Output: " . $output . "\n";

if ($output === null) {
    echo "   Result: ❌ Failed to execute\n";
} else {
    $result = json_decode($output, true);
    if ($result && isset($result['success'])) {
        echo "   Result: " . ($result['success'] ? "✅ Success" : "❌ Failed") . "\n";
        if ($result['success']) {
            echo "   Token: " . substr($result['code'], 0, 50) . "...\n";
        } else {
            echo "   Error: " . ($result['message'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "   Result: ❌ Invalid JSON response\n";
    }
}

echo "\n✅ Test completed!\n";
?> 