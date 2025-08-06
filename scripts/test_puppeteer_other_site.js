// test_puppeteer_other_site.js
// Test với site khác có captcha

const { exec } = require('child_process');

function testPuppeteerOtherSite() {
  console.log('Testing Puppeteer with other site...');
  
  // Test với site demo reCAPTCHA
  const testData = {
    url: 'https://recaptcha-demo.appspot.com/recaptcha-v2-checkbox.php',
    sitekey: '6Le-wvkSAAAAAPBMRTvw0Q4Muexq9bi0DJwx_mJ-',
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

testPuppeteerOtherSite(); 