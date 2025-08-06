#!/bin/bash

# Test script để kiểm tra integration giữa PHP và Node.js

echo "🧪 Test Puppeteer Integration"
echo "============================="
echo ""

# Test 1: Kiểm tra đường dẫn
SCRIPT_PATH="/home/teso/seominisuite.com/public_html/scripts/puppeteer_2captcha.js"
SCRIPTS_DIR="/home/teso/seominisuite.com/public_html/scripts"

echo "1. Kiểm tra đường dẫn script:"
echo "   Script path: $SCRIPT_PATH"
if [ -f "$SCRIPT_PATH" ]; then
    echo "   File exists: ✅ YES"
else
    echo "   File exists: ❌ NO"
fi
echo ""

# Test 2: Kiểm tra thư mục scripts
echo "2. Kiểm tra thư mục scripts:"
echo "   Scripts dir: $SCRIPTS_DIR"
if [ -d "$SCRIPTS_DIR" ]; then
    echo "   Dir exists: ✅ YES"
    echo "   Files in dir:"
    ls -la "$SCRIPTS_DIR" | grep -v "^total" | while read line; do
        echo "     $line"
    done
else
    echo "   Dir exists: ❌ NO"
fi
echo ""

# Test 3: Kiểm tra Node.js
echo "3. Kiểm tra Node.js:"
NODE_VERSION=$(node --version 2>&1)
echo "   Node version: $NODE_VERSION"
if [[ $NODE_VERSION == v* ]]; then
    echo "   Node available: ✅ YES"
else
    echo "   Node available: ❌ NO"
fi
echo ""

# Test 4: Test thực thi script
echo "4. Test thực thi script:"
SCRIPT_DATA='{"url":"https://www.google.com/recaptcha/api2/demo","sitekey":"6Le-wvkSAAAAAPBMRTvw0Q4Muexq9bi0DJwx_mJ-","data_s":""}'

COMMAND="cd '$SCRIPTS_DIR' && node '$SCRIPT_PATH' '$SCRIPT_DATA' 2>&1"
echo "   Command: $COMMAND"
echo "   Executing..."

OUTPUT=$(eval $COMMAND)
echo "   Output: $OUTPUT"

if [ -z "$OUTPUT" ]; then
    echo "   Result: ❌ Failed to execute"
else
    # Kiểm tra JSON response
    if echo "$OUTPUT" | grep -q '"success"'; then
        if echo "$OUTPUT" | grep -q '"success":true'; then
            echo "   Result: ✅ Success"
            TOKEN=$(echo "$OUTPUT" | grep -o '"code":"[^"]*"' | cut -d'"' -f4)
            echo "   Token: ${TOKEN:0:50}..."
        else
            echo "   Result: ❌ Failed"
            ERROR=$(echo "$OUTPUT" | grep -o '"message":"[^"]*"' | cut -d'"' -f4)
            echo "   Error: $ERROR"
        fi
    else
        echo "   Result: ❌ Invalid JSON response"
    fi
fi

echo ""
echo "✅ Test completed!" 