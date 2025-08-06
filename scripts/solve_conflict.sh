#!/bin/bash

# solve_conflict.sh
# Script tổng hợp để giải quyết xung đột client-side vs server-side

echo "🔧 Solving Client-Side vs Server-Side Conflict"
echo "=============================================="
echo ""

# 1. Kiểm tra tình trạng hiện tại
echo "1. Current Status Check:"
echo "   Server-side Puppeteer:"
if [ -f "puppeteer_2captcha.js" ]; then
    echo "   ✅ puppeteer_2captcha.js exists"
    node puppeteer_2captcha.js '{"url":"https://www.google.com/recaptcha/api2/demo","sitekey":"6Le-wvkSAAAAAPBMRTvw0Q4Muexq9bi0DJwx_mJ-","data_s":""}' > /tmp/quick_test.json 2>&1
    if grep -q '"success":true' /tmp/quick_test.json; then
        echo "   ✅ Server-side working perfectly"
    else
        echo "   ❌ Server-side failed"
    fi
else
    echo "   ❌ puppeteer_2captcha.js not found"
fi

echo ""

# 2. Tạo script để disable client-side
echo "2. Creating client-side disable script:"
if [ -f "disable_client_scripts.js" ]; then
    echo "   ✅ disable_client_scripts.js exists"
else
    echo "   ❌ disable_client_scripts.js not found"
fi

echo ""

# 3. Tạo test page
echo "3. Creating test page:"
if [ -f "test_disable_client.html" ]; then
    echo "   ✅ test_disable_client.html exists"
else
    echo "   ❌ test_disable_client.html not found"
fi

echo ""

# 4. Hướng dẫn sử dụng
echo "4. Usage Instructions:"
echo "   📝 To solve the conflict:"
echo "   "
echo "   Step 1: Open test_disable_client.html in browser"
echo "   Step 2: Click 'Load Disable Script' button"
echo "   Step 3: Test various client-side blocking features"
echo "   Step 4: Use server-side Puppeteer for captcha solving"
echo "   "
echo "   Alternative solutions:"
echo "   - Disable browser extensions temporarily"
echo "   - Use incognito/private browsing mode"
echo "   - Check for any extension injecting captcha scripts"
echo "   - Use a different browser without extensions"

echo ""

# 5. Kiểm tra log file
echo "5. Checking Puppeteer logs:"
if [ -f "/tmp/puppeteer_debug.log" ]; then
    echo "   ✅ Log file exists"
    echo "   Last 3 entries:"
    tail -3 /tmp/puppeteer_debug.log
else
    echo "   ❌ Log file not found"
fi

echo ""

# 6. Tạo script để inject disable script vào Drupal
echo "6. Creating Drupal integration script:"
cat > inject_disable_script.php << 'EOF'
<?php
// inject_disable_script.php
// Script để inject disable script vào Drupal

// Thêm script vào Drupal
function cassiopeia_captcha_add_disable_script() {
    drupal_add_js(drupal_get_path('module', 'cassiopeia_captcha') . '/scripts/disable_client_scripts.js', 'file');
}

// Hook để load script
function cassiopeia_captcha_page_alter(&$page) {
    if (isset($page['content']['system_main']['cassiopeia_captcha_form'])) {
        cassiopeia_captcha_add_disable_script();
    }
}
EOF

echo "   ✅ Created inject_disable_script.php"

echo ""

# 7. Tạo script để test integration
echo "7. Creating integration test:"
cat > test_integration.php << 'EOF'
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
EOF

echo "   ✅ Created test_integration.php"

echo ""

# 8. Tóm tắt giải pháp
echo "8. Solution Summary:"
echo "   🎯 Problem: Client-side scripts conflicting with server-side Puppeteer"
echo "   🔧 Solution: Disable client-side captcha solving scripts"
echo "   📝 Implementation:"
echo "      - Load disable_client_scripts.js in browser"
echo "      - Block solveSimpleChallenge and other captcha functions"
echo "      - Allow server-side Puppeteer to handle captcha solving"
echo "   ✅ Result: Clean separation between client and server"

echo ""

# 9. Next steps
echo "9. Next Steps:"
echo "   📋 Immediate actions:"
echo "      1. Open test_disable_client.html in browser"
echo "      2. Load disable script and test functionality"
echo "      3. Verify server-side Puppeteer still works"
echo "      4. Integrate disable script into Drupal if needed"
echo "   "
echo "   🔍 If problem persists:"
echo "      - Check browser extensions"
echo "      - Use different browser"
echo "      - Check for any other client-side scripts"

echo ""
echo "✅ Conflict resolution script completed!"
echo "📝 Use the provided tools to solve the client-side conflict" 