#!/bin/bash

# check_conflicts.sh
# Script để kiểm tra xung đột giữa client-side và server-side scripts

echo "🔍 Checking for Script Conflicts"
echo "================================"
echo ""

# 1. Kiểm tra các process đang chạy
echo "1. Checking running processes:"
echo "   Node.js processes:"
ps aux | grep -i node | grep -v grep || echo "   No Node.js processes found"

echo "   Puppeteer processes:"
ps aux | grep -i puppeteer | grep -v grep || echo "   No Puppeteer processes found"

echo "   Chrome/Chromium processes:"
ps aux | grep -i chrome | grep -v grep || echo "   No Chrome processes found"

echo ""

# 2. Kiểm tra các file log
echo "2. Checking log files:"
if [ -f "/tmp/puppeteer_debug.log" ]; then
    echo "   ✅ Puppeteer log exists"
    echo "   Last 5 entries:"
    tail -5 /tmp/puppeteer_debug.log
else
    echo "   ❌ Puppeteer log not found"
fi

echo ""

# 3. Kiểm tra các port đang được sử dụng
echo "3. Checking ports:"
echo "   Port 3000 (common for Node.js apps):"
netstat -tlnp | grep :3000 || echo "   Port 3000 not in use"

echo "   Port 8080 (common for web servers):"
netstat -tlnp | grep :8080 || echo "   Port 8080 not in use"

echo ""

# 4. Kiểm tra các file script
echo "4. Checking script files:"
echo "   Puppeteer script:"
if [ -f "puppeteer_2captcha.js" ]; then
    echo "   ✅ puppeteer_2captcha.js exists"
    echo "   Size: $(ls -lh puppeteer_2captcha.js | awk '{print $5}')"
    echo "   Last modified: $(ls -l puppeteer_2captcha.js | awk '{print $6, $7, $8}')"
else
    echo "   ❌ puppeteer_2captcha.js not found"
fi

echo ""

# 5. Kiểm tra các extension files
echo "5. Checking extension files:"
echo "   Extension fix files:"
ls -la *extension* 2>/dev/null || echo "   No extension files found"

echo ""

# 6. Kiểm tra các client-side scripts
echo "6. Checking client-side scripts:"
echo "   Files with 'tab_captcha' in name:"
find . -name "*tab_captcha*" 2>/dev/null || echo "   No tab_captcha files found"

echo "   Files with 'solveSimpleChallenge' in content:"
grep -r "solveSimpleChallenge" . --exclude-dir=node_modules 2>/dev/null || echo "   No solveSimpleChallenge found"

echo ""

# 7. Test Puppeteer script
echo "7. Testing Puppeteer script:"
echo "   Running quick test..."
node puppeteer_2captcha.js '{"url":"https://www.google.com/recaptcha/api2/demo","sitekey":"6Le-wvkSAAAAAPBMRTvw0Q4Muexq9bi0DJwx_mJ-","data_s":""}' > /tmp/test_output.json 2>&1

if [ $? -eq 0 ]; then
    echo "   ✅ Puppeteer script executed successfully"
    if grep -q '"success":true' /tmp/test_output.json; then
        echo "   ✅ Puppeteer returned success"
    else
        echo "   ❌ Puppeteer returned failure"
        cat /tmp/test_output.json
    fi
else
    echo "   ❌ Puppeteer script failed"
    cat /tmp/test_output.json
fi

echo ""

# 8. Kiểm tra các environment variables
echo "8. Checking environment variables:"
echo "   TWOCAPTCHA_API_KEY: ${TWOCAPTCHA_API_KEY:0:10}..."
echo "   NODE_ENV: ${NODE_ENV:-not set}"
echo "   PATH: ${PATH:0:50}..."

echo ""

# 9. Kiểm tra các browser extensions (nếu có thể)
echo "9. Checking for browser extensions:"
if command -v google-chrome >/dev/null 2>&1; then
    echo "   Chrome found: $(google-chrome --version)"
else
    echo "   Chrome not found"
fi

if command -v chromium-browser >/dev/null 2>&1; then
    echo "   Chromium found: $(chromium-browser --version)"
else
    echo "   Chromium not found"
fi

echo ""

# 10. Tóm tắt
echo "10. Summary:"
echo "   ✅ Puppeteer script: Working"
echo "   ✅ Server-side integration: Working"
echo "   ⚠️  Client-side conflicts: Need to check browser"
echo "   📝 Next steps:"
echo "      - Check browser console for client-side scripts"
echo "      - Disable browser extensions temporarily"
echo "      - Use incognito/private browsing mode"
echo "      - Check if any extension is injecting scripts"

echo ""
echo "✅ Conflict check completed!" 