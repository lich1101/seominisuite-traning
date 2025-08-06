// test_puppeteer_direct.js
// Test trực tiếp Puppeteer script

const { exec } = require('child_process');

function testPuppeteerDirect() {
  console.log('Testing Puppeteer script directly...');
  
  const testData = {
    url: 'https://www.google.com/recaptcha/api2/demo',
    sitekey: '6LfwuyUTAAAAAOAmoS0fdqijC2PbbdH4kjq62Y1b',
    data_s: ''
  };
  
  const jsonData = JSON.stringify(testData);
  const command = `node puppeteer_2captcha.js '${jsonData}'`;
  
  console.log('Executing command:', command);
  
  exec(command, (error, stdout, stderr) => {
    if (error) {
      console.error('Error:', error);
      return;
    }
    
    if (stderr) {
      console.error('Stderr:', stderr);
    }
    
    console.log('Stdout:', stdout);
    
    try {
      const result = JSON.parse(stdout);
      if (result.success) {
        console.log('✅ Puppeteer worked! Token received');
      } else {
        console.log('❌ Puppeteer failed:', result.message);
      }
    } catch (e) {
      console.log('❌ Invalid JSON response:', stdout);
    }
  });
}

testPuppeteerDirect(); 