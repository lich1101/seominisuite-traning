#!/bin/bash

# Quick test script cho puppeteer_2captcha.js
echo "🚀 Quick Test Script cho puppeteer_2captcha.js"
echo "=============================================="

# Kiểm tra dependencies
echo "📦 Kiểm tra dependencies..."
if [ ! -d "node_modules" ]; then
    echo "❌ node_modules không tồn tại. Chạy: npm install"
    exit 1
fi

# Test 1: Kiểm tra balance
echo "💰 Kiểm tra balance 2captcha..."
API_KEY="ac51483e4f0908132f9ad0482722627b"
BALANCE=$(curl -s "http://2captcha.com/res.php?key=$API_KEY&action=getbalance&json=1" | grep -o '"request":"[^"]*"' | cut -d'"' -f4)
echo "Balance: $BALANCE"

# Test 2: Test đơn giản
echo "🧪 Chạy test đơn giản..."
node simple_test.js

# Test 3: Test script chính
echo "🎯 Test script chính..."
node puppeteer_2captcha.js '{"url":"https://www.google.com/recaptcha/api2/demo","sitekey":"6Le-wvkSAAAAAPBMRTvw0Q4Muexq9bi0DJwx_mJ-"}'

echo "✅ Test hoàn thành!" 