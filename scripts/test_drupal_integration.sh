#!/bin/bash

# Test script để giả lập cách Drupal module gọi puppeteer

echo "🧪 Test Drupal Integration"
echo "=========================="
echo ""

# Giả lập DRUPAL_ROOT
DRUPAL_ROOT="/home/teso/seominisuite.com/public_html"

# Test data giống như Drupal
echo "1. Chuẩn bị test data:"
echo "   DRUPAL_ROOT: $DRUPAL_ROOT"
echo ""

# Test 2: Kiểm tra đường dẫn script
SCRIPT_PATH="$DRUPAL_ROOT/scripts/puppeteer_2captcha.js"
echo "2. Kiểm tra đường dẫn script:"
echo "   Script path: $SCRIPT_PATH"
if [ -f "$SCRIPT_PATH" ]; then
    echo "   File exists: ✅ YES"
else
    echo "   File exists: ❌ NO"
    exit 1
fi
echo ""

# Test 3: Giả lập hàm cassiopeia_captcha_resolve_with_puppeteer
echo "3. Giả lập hàm cassiopeia_captcha_resolve_with_puppeteer:"

# Test data
SCRIPT_DATA='{"url":"https://www.google.com/recaptcha/api2/demo","sitekey":"6Le-wvkSAAAAAPBMRTvw0Q4Muexq9bi0DJwx_mJ-","data_s":""}'

# Đường dẫn đến script Puppeteer
SCRIPTS_DIR="$DRUPAL_ROOT/scripts"

# Gọi script NodeJS (giống như trong PHP)
COMMAND="cd '$SCRIPTS_DIR' && node '$SCRIPT_PATH' '$SCRIPT_DATA' 2>&1"

echo "   Command: $COMMAND"
echo "   Executing..."

# Thực thi command
OUTPUT=$(eval $COMMAND)

echo "   Output: $OUTPUT"

# Parse kết quả JSON từ script
if [ -z "$OUTPUT" ]; then
    echo "   Result: ❌ Failed to execute script"
    exit 1
fi

# Kiểm tra JSON response
if echo "$OUTPUT" | grep -q '"success"'; then
    if echo "$OUTPUT" | grep -q '"success":true'; then
        echo "   Result: ✅ Success"
        TOKEN=$(echo "$OUTPUT" | grep -o '"code":"[^"]*"' | cut -d'"' -f4)
        echo "   Token: ${TOKEN:0:50}..."
        
        # Giả lập log như trong PHP
        echo "$(date '+%Y-%m-%d %H:%M:%S') Puppeteer: Success! Token received" >> /tmp/puppeteer_debug.log
    else
        echo "   Result: ❌ Failed"
        ERROR=$(echo "$OUTPUT" | grep -o '"message":"[^"]*"' | cut -d'"' -f4)
        echo "   Error: $ERROR"
        echo "$(date '+%Y-%m-%d %H:%M:%S') Puppeteer: Failed with message: $ERROR" >> /tmp/puppeteer_debug.log
    fi
else
    echo "   Result: ❌ Invalid JSON response"
    echo "$(date '+%Y-%m-%d %H:%M:%S') Puppeteer: Invalid JSON response: $OUTPUT" >> /tmp/puppeteer_debug.log
fi

echo ""
echo "4. Kiểm tra log file:"
if [ -f "/tmp/puppeteer_debug.log" ]; then
    echo "   Log file exists: ✅ YES"
    echo "   Last 5 lines:"
    tail -5 /tmp/puppeteer_debug.log | while read line; do
        echo "     $line"
    done
else
    echo "   Log file exists: ❌ NO"
fi

echo ""
echo "✅ Test completed!" 