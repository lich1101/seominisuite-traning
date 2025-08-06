# Puppeteer 2Captcha Script

Script NodeJS sử dụng Puppeteer để giải reCAPTCHA tự động bằng 2Captcha.

## Cài đặt

1. **Cài đặt NodeJS và npm** (nếu chưa có):
   ```bash
   # Ubuntu/Debian
   sudo apt update
   sudo apt install nodejs npm
   
   # CentOS/RHEL
   sudo yum install nodejs npm
   ```

2. **Cài đặt dependencies**:
   ```bash
   cd public_html/scripts
   npm install
   ```

3. **Cài đặt Chrome/Chromium** (nếu chưa có):
   ```bash
   # Ubuntu/Debian
   sudo apt install chromium-browser
   
   # CentOS/RHEL
   sudo yum install chromium
   ```

## Cấu hình

1. **Set API key 2Captcha**:
   - Cách 1: Set biến môi trường:
     ```bash
     export TWOCAPTCHA_API_KEY="your_2captcha_api_key_here"
     ```
   - Cách 2: Sửa trực tiếp trong file `puppeteer_2captcha.js`:
     ```javascript
     const API_KEY = 'your_2captcha_api_key_here';
     ```

## Sử dụng

### Test thủ công:
```bash
node puppeteer_2captcha.js '{"url":"https://example.com","sitekey":"SITE_KEY_HERE","data_s":""}'
```

### Tích hợp với PHP:
Script đã được tích hợp vào module `cassiopeia_captcha.module` và sẽ tự động được gọi khi có request giải captcha.

## Troubleshooting

1. **Lỗi "Puppeteer script not found"**:
   - Kiểm tra đường dẫn file trong PHP: `DRUPAL_ROOT . '/scripts/puppeteer_2captcha.js'`

2. **Lỗi "Failed to execute Puppeteer script"**:
   - Kiểm tra quyền thực thi: `chmod +x puppeteer_2captcha.js`
   - Kiểm tra NodeJS đã cài đặt: `node --version`

3. **Lỗi Chrome/Chromium**:
   - Cài đặt Chrome/Chromium
   - Hoặc set đường dẫn Chrome trong script

4. **Lỗi API key**:
   - Kiểm tra API key 2Captcha có hợp lệ
   - Kiểm tra tài khoản 2Captcha có đủ tiền

## Logs

Script sẽ trả về JSON response:
- Thành công: `{"success":true,"code":"token_here"}`
- Thất bại: `{"success":false,"message":"error_message"}` 