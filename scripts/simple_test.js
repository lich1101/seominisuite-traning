// simple_test.js
// Script test đơn giản cho puppeteer_2captcha.js

const puppeteer = require('puppeteer');
const fetch = require('node-fetch');

// Test function đơn giản
async function simpleTest() {
  console.log('🧪 Bắt đầu test đơn giản...');
  
  // Test data
  const testData = {
    url: 'https://www.google.com/recaptcha/api2/demo',
    sitekey: '6Le-wvkSAAAAAPBMRTvw0Q4Muexq9bi0DJwx_mJ-'
  };
  
  console.log('📋 Test URL:', testData.url);
  console.log('🔑 Sitekey:', testData.sitekey);
  
  try {
    // Test 1: Kiểm tra API key
    const API_KEY = process.env.TWOCAPTCHA_API_KEY || 'ac51483e4f0908132f9ad0482722627b';
    console.log('🔑 API Key:', API_KEY.substring(0, 10) + '...');
    
    // Test 2: Kiểm tra kết nối 2captcha
    console.log('🌐 Kiểm tra kết nối 2captcha...');
    const balanceRes = await fetch(`http://2captcha.com/res.php?key=${API_KEY}&action=getbalance&json=1`);
    const balanceJson = await balanceRes.json();
    console.log('💰 Balance:', balanceJson.request);
    
    // Test 3: Test với puppeteer
    console.log('🤖 Khởi động Puppeteer...');
    const browser = await puppeteer.launch({ 
      headless: true, // Chạy headless vì server không có X server
      args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage']
    });
    
    const page = await browser.newPage();
    console.log('📄 Mở trang test...');
    await page.goto(testData.url, { waitUntil: 'networkidle2' });
    
    // Chụp screenshot để xem
    await page.screenshot({ path: 'test_screenshot.png' });
    console.log('📸 Đã chụp screenshot: test_screenshot.png');
    
    // Đợi 5 giây để xem
    console.log('⏳ Đợi 5 giây...');
    await new Promise(r => setTimeout(r, 5000));
    
    await browser.close();
    console.log('✅ Test hoàn thành!');
    
  } catch (error) {
    console.error('❌ Lỗi:', error.message);
  }
}

// Chạy test
simpleTest(); 