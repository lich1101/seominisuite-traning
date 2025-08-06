// test_extension_call.js
// Script để test xem extension có gọi về server không

const fetch = require('node-fetch');

async function testExtensionCall() {
  try {
    console.log('Testing extension call to server...');
    
    const testData = {
      url: 'https://www.google.com/recaptcha/api2/demo',
      data_sitekey: '6LfwuyUTAAAAAOAmoS0fdqijC2PbbdH4kjq62Y1b',
      data_s: '',
      uid: 'test_user'
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
    console.log('Response headers:', response.headers.raw());
    
    const result = await response.json();
    console.log('Response body:', JSON.stringify(result, null, 2));
    
  } catch (error) {
    console.error('Error:', error.message);
  }
}

testExtensionCall(); 