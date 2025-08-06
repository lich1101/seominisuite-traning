// puppeteer_2captcha.js
// Usage: node puppeteer_2captcha.js '{"url":"...","sitekey":"...","data_s":"..."}'

const puppeteer = require('puppeteer');
const fetch = require('node-fetch');

async function solveRecaptcha({ url, sitekey, data_s }) {
  const API_KEY = process.env.TWOCAPTCHA_API_KEY || 'ac51483e4f0908132f9ad0482722627b'; // Thay YOUR_2CAPTCHA_API_KEY bằng API key thật của bạn
  // 1. Request 2Captcha to solve
  let reqBody = {
    method: 'POST',
    body: new URLSearchParams({
      key: API_KEY,
      method: 'userrecaptcha',
      googlekey: sitekey,
      pageurl: url,
      json: 1,
      ...(data_s ? { 'data-s': data_s } : {})
    })
  };
  let res = await fetch('http://2captcha.com/in.php', reqBody);
  let json = await res.json();
  if (json.status !== 1) return { success: false, message: json.request };
  let requestId = json.request;
  // 2. Poll for result
  let token = null;
  for (let i = 0; i < 24; i++) { // ~2 phút
    await new Promise(r => setTimeout(r, 5000));
    let poll = await fetch(`http://2captcha.com/res.php?key=${API_KEY}&action=get&id=${requestId}&json=1`);
    let pollJson = await poll.json();
    if (pollJson.status === 1) {
      token = pollJson.request;
      break;
    }
    if (pollJson.request !== 'CAPCHA_NOT_READY') {
      return { success: false, message: pollJson.request };
    }
  }
  if (!token) return { success: false, message: 'Timeout' };
  // 3. Use Puppeteer to submit token
  const browser = await puppeteer.launch({ 
    headless: "new", // Sử dụng headless mode mới để tránh warning
    args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage']
  });
  const page = await browser.newPage();
  await page.goto(url, { waitUntil: 'networkidle2' });
  // Điền token vào textarea
  await page.evaluate((token) => {
    let textarea = document.getElementById('g-recaptcha-response');
    if (textarea) {
      textarea.value = token;
      // Kích hoạt event nếu cần
      textarea.dispatchEvent(new Event('input', { bubbles: true }));
    }
  }, token);
  // Submit form nếu có
  await page.evaluate(() => {
    let form = document.querySelector('form');
    if (form) form.submit();
  });
  await new Promise(r => setTimeout(r, 3000)); // Đợi 3s cho submit
  await browser.close();
  return { success: true, code: token };
}

(async () => {
  try {
    const input = JSON.parse(process.argv[2]);
    const result = await solveRecaptcha({ url: input.url, sitekey: input.sitekey, data_s: input.data_s });
    console.log(JSON.stringify(result));
  } catch (e) {
    console.log(JSON.stringify({ success: false, message: e.message }));
  }
})(); 