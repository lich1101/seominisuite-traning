// test_2captcha.js
// Script test cho puppeteer_2captcha.js

const { exec } = require('child_process');
const path = require('path');

// Test case 1: Test với Google reCAPTCHA demo
async function testGoogleRecaptcha() {
  console.log('🧪 Test 1: Google reCAPTCHA Demo');
  
  const testData = {
    url: 'https://www.google.com/recaptcha/api2/demo',
    sitekey: '6Le-wvkSAAAAAPBMRTvw0Q4Muexq9bi0DJwx_mJ-'
  };
  
  console.log('📋 Test data:', JSON.stringify(testData, null, 2));
  
  const command = `node puppeteer_2captcha.js '${JSON.stringify(testData)}'`;
  
  exec(command, { cwd: __dirname }, (error, stdout, stderr) => {
    if (error) {
      console.error('❌ Error:', error.message);
      return;
    }
    if (stderr) {
      console.error('⚠️  Stderr:', stderr);
    }
    
    try {
      const result = JSON.parse(stdout);
      console.log('✅ Result:', JSON.stringify(result, null, 2));
    } catch (e) {
      console.log('📄 Raw output:', stdout);
    }
  });
}

// Test case 2: Test với một trang web khác có reCAPTCHA
async function testOtherSite() {
  console.log('\n🧪 Test 2: Other site with reCAPTCHA');
  
  const testData = {
    url: 'https://recaptcha-demo.appspot.com/recaptcha-v2-checkbox.php',
    sitekey: '6Le-wvkSAAAAAPBMRTvw0Q4Muexq9bi0DJwx_mJ-'
  };
  
  console.log('📋 Test data:', JSON.stringify(testData, null, 2));
  
  const command = `node puppeteer_2captcha.js '${JSON.stringify(testData)}'`;
  
  exec(command, { cwd: __dirname }, (error, stdout, stderr) => {
    if (error) {
      console.error('❌ Error:', error.message);
      return;
    }
    if (stderr) {
      console.error('⚠️  Stderr:', stderr);
    }
    
    try {
      const result = JSON.parse(stdout);
      console.log('✅ Result:', JSON.stringify(result, null, 2));
    } catch (e) {
      console.log('📄 Raw output:', stdout);
    }
  });
}

// Test case 3: Test với data-s parameter
async function testWithDataS() {
  console.log('\n🧪 Test 3: With data-s parameter');
  
  const testData = {
    url: 'https://example.com/recaptcha',
    sitekey: '6Le-wvkSAAAAAPBMRTvw0Q4Muexq9bi0DJwx_mJ-',
    data_s: 'test_data_s_value'
  };
  
  console.log('📋 Test data:', JSON.stringify(testData, null, 2));
  
  const command = `node puppeteer_2captcha.js '${JSON.stringify(testData)}'`;
  
  exec(command, { cwd: __dirname }, (error, stdout, stderr) => {
    if (error) {
      console.error('❌ Error:', error.message);
      return;
    }
    if (stderr) {
      console.error('⚠️  Stderr:', stderr);
    }
    
    try {
      const result = JSON.parse(stdout);
      console.log('✅ Result:', JSON.stringify(result, null, 2));
    } catch (e) {
      console.log('📄 Raw output:', stdout);
    }
  });
}

// Chạy tất cả tests
async function runAllTests() {
  console.log('🚀 Bắt đầu test puppeteer_2captcha.js\n');
  
  // Kiểm tra API key
  const apiKey = process.env.TWOCAPTCHA_API_KEY || 'ac51483e4f0908132f9ad0482722627b';
  console.log(`🔑 API Key: ${apiKey.substring(0, 10)}...`);
  
  // Chạy tests tuần tự
  await testGoogleRecaptcha();
  
  // Đợi 5 giây giữa các tests
  setTimeout(() => {
    testOtherSite();
  }, 5000);
  
  setTimeout(() => {
    testWithDataS();
  }, 10000);
}

// Chạy test
runAllTests().catch(console.error); 