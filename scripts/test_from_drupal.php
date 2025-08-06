<?php
// Test script để chạy trực tiếp từ Drupal environment
// Chạy: php test_from_drupal.php

// Giả lập Drupal environment
define('DRUPAL_ROOT', '/home/teso/seominisuite.com/public_html');

// Giả lập hàm log
function cassiopeia_captcha_puppeteer_log($msg) {
    file_put_contents('/tmp/puppeteer_debug.log', date('Y-m-d H:i:s') . ' ' . $msg . "\n", FILE_APPEND);
}

// Giả lập hàm cassiopeia_captcha_resolve_with_puppeteer
function cassiopeia_captcha_resolve_with_puppeteer($data) {
    $return = array();
    $return['success'] = false;
    
    try {
        // Log để debug
        cassiopeia_captcha_puppeteer_log("Puppeteer: Starting captcha resolution for URL: " . $data->websiteURL);
        
        // Chuẩn bị dữ liệu cho script NodeJS
        $script_data = array(
            'url' => $data->websiteURL,
            'sitekey' => $data->websiteKey,
            'data_s' => $data->recaptchaDataSValue
        );
        
        // Đường dẫn đến script Puppeteer
        $script_path = DRUPAL_ROOT . '/scripts/puppeteer_2captcha.js';
        
        // Kiểm tra file tồn tại
        if (!file_exists($script_path)) {
            cassiopeia_captcha_puppeteer_log("Puppeteer: Script not found at: " . $script_path);
            $return['message'] = 'Puppeteer script not found';
            return $return;
        }
        
        // Gọi script NodeJS
        $json_data = json_encode($script_data);
        $command = "cd " . escapeshellarg(DRUPAL_ROOT . '/scripts') . " && node " . escapeshellarg($script_path) . " " . escapeshellarg($json_data) . " 2>&1";
        
        cassiopeia_captcha_puppeteer_log("Puppeteer: Executing command: " . $command);
        
        $output = shell_exec($command);
        
        cassiopeia_captcha_puppeteer_log("Puppeteer: Output: " . $output);
        
        if ($output === null) {
            cassiopeia_captcha_puppeteer_log("Puppeteer: Failed to execute script");
            $return['message'] = 'Failed to execute Puppeteer script';
            return $return;
        }
        
        // Parse kết quả JSON từ script
        $result = json_decode($output, true);
        
        if ($result && isset($result['success'])) {
            if ($result['success']) {
                cassiopeia_captcha_puppeteer_log("Puppeteer: Success! Token received");
                $return['success'] = true;
                $return['code'] = $result['code'];
            } else {
                cassiopeia_captcha_puppeteer_log("Puppeteer: Failed with message: " . ($result['message'] ?? 'Unknown error'));
                $return['message'] = $result['message'] ?? 'Puppeteer failed';
            }
        } else {
            cassiopeia_captcha_puppeteer_log("Puppeteer: Invalid JSON response: " . $output);
            $return['message'] = 'Invalid JSON response from Puppeteer script';
        }
        
    } catch (Exception $e) {
        cassiopeia_captcha_puppeteer_log("Puppeteer: Exception: " . $e->getMessage());
        $return['message'] = 'Exception: ' . $e->getMessage();
    }
    
    return $return;
}

// Test data
$data = new stdClass();
$data->websiteURL = 'https://www.google.com/recaptcha/api2/demo';
$data->websiteKey = '6Le-wvkSAAAAAPBMRTvw0Q4Muexq9bi0DJwx_mJ-';
$data->recaptchaDataSValue = '';

echo "🧪 Test từ Drupal Environment\n";
echo "=============================\n\n";

echo "1. DRUPAL_ROOT: " . DRUPAL_ROOT . "\n";
echo "2. Script path: " . DRUPAL_ROOT . '/scripts/puppeteer_2captcha.js' . "\n";
echo "3. File exists: " . (file_exists(DRUPAL_ROOT . '/scripts/puppeteer_2captcha.js') ? "YES" : "NO") . "\n\n";

echo "4. Gọi hàm cassiopeia_captcha_resolve_with_puppeteer:\n";
$result = cassiopeia_captcha_resolve_with_puppeteer($data);

echo "   Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

echo "5. Kiểm tra log file:\n";
if (file_exists('/tmp/puppeteer_debug.log')) {
    echo "   Log file exists: YES\n";
    echo "   Last 10 lines:\n";
    $lines = file('/tmp/puppeteer_debug.log');
    $last_lines = array_slice($lines, -10);
    foreach ($last_lines as $line) {
        echo "     " . trim($line) . "\n";
    }
} else {
    echo "   Log file exists: NO\n";
}

echo "\n✅ Test completed!\n";
?> 