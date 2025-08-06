# Hướng dẫn Test Script puppeteer_2captcha.js

## 📋 Yêu cầu trước khi test

1. **Node.js và npm** đã được cài đặt
2. **Dependencies** đã được cài đặt: `npm install`
3. **API Key 2Captcha** hợp lệ (hiện tại đang dùng key mặc định)

## 🚀 Cách test

### 1. Test đơn giản (Khuyến nghị cho người mới)

```bash
cd public_html/scripts
node simple_test.js
```

Script này sẽ:
- Kiểm tra API key
- Kiểm tra balance 2captcha
- Mở browser và chụp screenshot
- Hiển thị browser để bạn có thể xem

### 2. Test đầy đủ

```bash
cd public_html/scripts
node test_2captcha.js
```

Script này sẽ chạy 3 test cases:
- Google reCAPTCHA demo
- Trang web khác có reCAPTCHA
- Test với data-s parameter

### 3. Test trực tiếp script gốc

```bash
cd public_html/scripts
node puppeteer_2captcha.js '{"url":"https://www.google.com/recaptcha/api2/demo","sitekey":"6Le-wvkSAAAAAPBMRTvw0Q4Muexq9bi0DJwx_mJ-"}'
```

## 🔧 Cấu hình

### Thiết lập API Key

Có 2 cách:

1. **Environment variable:**
```bash
export TWOCAPTCHA_API_KEY="your_api_key_here"
```

2. **Sửa trực tiếp trong code:**
```javascript
const API_KEY = 'your_api_key_here';
```

### Cấu hình Puppeteer

Trong file `puppeteer_2captcha.js`, bạn có thể thay đổi:

```javascript
const browser = await puppeteer.launch({ 
  headless: false, // true = ẩn browser, false = hiển thị browser
  args: ['--no-sandbox', '--disable-setuid-sandbox']
});
```

## 📊 Kiểm tra kết quả

### Output thành công:
```json
{
  "success": true,
  "code": "03AFcWeA..."
}
```

### Output lỗi:
```json
{
  "success": false,
  "message": "Error message here"
}
```

## 🐛 Debug

### 1. Kiểm tra balance 2captcha:
```bash
curl "http://2captcha.com/res.php?key=YOUR_API_KEY&action=getbalance&json=1"
```

### 2. Xem logs chi tiết:
Thêm `console.log` vào script để debug:

```javascript
console.log('Debug info:', someVariable);
```

### 3. Chụp screenshot:
Script `simple_test.js` sẽ tạo file `test_screenshot.png`

## ⚠️ Lưu ý quan trọng

1. **API Key**: Đảm bảo API key có đủ balance
2. **Rate limiting**: Không gọi quá nhiều request cùng lúc
3. **Sitekey**: Phải đúng với trang web đang test
4. **URL**: Phải chính xác và có thể truy cập được

## 🔍 Test với trang web thật

Để test với trang web thật, bạn cần:

1. **Tìm sitekey**: Inspect element và tìm `data-sitekey`
2. **Xác định URL**: URL chính xác của trang có reCAPTCHA
3. **Kiểm tra data-s**: Một số trang cần thêm parameter này

### Ví dụ:
```bash
node puppeteer_2captcha.js '{"url":"https://example.com/contact","sitekey":"6Lc_XXXXXX","data_s":"optional_data_s_value"}'
```

## 📞 Hỗ trợ

Nếu gặp lỗi:
1. Kiểm tra console output
2. Xem file `test_screenshot.png`
3. Kiểm tra balance 2captcha
4. Đảm bảo internet connection ổn định 