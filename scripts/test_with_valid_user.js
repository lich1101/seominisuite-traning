// test_with_valid_user.js
// Test với user có lượt giải captcha

const fetch = require('node-fetch');

async function testWithValidUser() {
  try {
    console.log('Testing with valid user...');
    
    // Test với user có lượt (thay UID thật)
    const testData = {
      url: 'https://www.google.com/recaptcha/api2/demo',
      data_sitekey: '6LfwuyUTAAAAAOAmoS0fdqijC2PbbdH4kjq62Y1b',
      data_s: '',
      uid: '1' // Thay bằng UID user có lượt
    };
    
    const response = await fetch('https://seominisuite.com/cassiopeia-captcha/resolve', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Origin': 'https://www.google.com'
      },
      body: JSON.stringify(testData)
    });
    
    console.log('Response status:', response.status);
    
    const result = await response.json();
    console.log('Response body:', JSON.stringify(result, null, 2));
    
    if (result.success) {
      console.log('✅ Puppeteer worked! Token received:', result.code.substring(0, 50) + '...');
    } else if (result.limited) {
      console.log('❌ User has no remaining captcha solves');
    } else {
      console.log('❌ Failed:', result.message);
    }
    
  } catch (error) {
    console.error('Error:', error.message);
  }
}

testWithValidUser(); 