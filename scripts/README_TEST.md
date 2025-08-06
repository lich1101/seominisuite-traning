# 🧪 Cách Test Script puppeteer_2captcha.js

## ⚡ Test Nhanh (Khuyến nghị)

```bash
cd public_html/scripts
./quick_test.sh
```

## 📋 Các Cách Test Khác

### 1. Test Đơn Giản
```bash
node simple_test.js
```
- Kiểm tra API key và balance
- Mở trang web và chụp screenshot
- Không giải reCAPTCHA

### 2. Test Script Chính
```bash
node puppeteer_2captcha.js '{"url":"https://www.google.com/recaptcha/api2/demo","sitekey":"6Le-wvkSAAAAAPBMRTvw0Q4Muexq9bi0DJwx_mJ-"}'
```
- Giải reCAPTCHA thật
- Trả về token

### 3. Test Đầy Đủ
```bash
node test_2captcha.js
```
- Chạy nhiều test cases
- Có delay giữa các tests

## ✅ Kết Quả Mong Đợi

### Thành công:
```json
{
  "success": true,
  "code": "03AFcWeA5Lo21zgimNS6QotZWzk46fBuundJ6HTTLbubIjGj8fc7iw7uE4J_v1_1OiJ8p1JAfwDyCTSho29EJUTMqfaGIAV1mfxp_brlwJ8uGXkftxmCT0LhSMvcSVIcGo3MHpTDFwBOr05ONCDE54UaCoaW21K6fkSWuAR4qDDJBT3CxDI26Npm4iIa748hMIAbbt3VWzGP5revrZ22klZt_hES6Y6eay3W2266A4tNO2TaGuJfTfw12g7_VSB1hZkm14yPvtmqNXudWUusrhfJx6rZ-lMNBn_bJp4KQiwJenssZ7sw3wdoU8_eWZALJ-a9vTr5HIoz_d8Ug4wlivHOgjIkkGhbSdR89z7VJQTsPsD4r6aGJD_wPKnyFFDeaoqgILVsMOOijY5I0HVD2EEv9FyCraMBoWp-2k9cGyhs1vGk0VfRlAS7k6-AmgolEhEm4ycBzlFjjt5ajYpsbbBrQaxQk77UIV1xhzjLjq9Ai7iFmOY2fTU6V9PQMvcbo1iYnA_oeQQT3iaXnOZaIBDnM5__Ne_MJIegpLKPgx1Fiscy-k2Y1bccfZsYoka3Dcc5YFcNRZOGRv_4KrMEeNVI-CLbGQ2oU8ioMrFWiaxtLG8j0xJJyGoRJl0l4OnDW2SOlPRQTymSF_TYm1VT70TJ8pDqrZ5Ya4hYEMoMU_BiThJZjzqC4UU-of7XUbdfxVoVc2nLmRqPdL_ALqDe1ISx8IWyAprrTuPI_0xaeQDQeM-cG95lIAD2O_6oGYJtxV8OYHIayDqJY1RT0N96y9I9BpEe9MXfaAIz6yrYtJtDxnshfNNAZ_uNWP-ISHiz49vFRn6saDA_6x67TQNjiaQ2NUiEbNYnuNcGyymkTf5Y6Hg8kFHstgBCSn_whPb3Vr5xyUlBu30hC_LKKQ_X8Nc94rghV3n9ebAbAZFBaDG5kqcPw6qQxiUqmSQIhlp61LsjYQ4QWr3Q5IZw1sXUgMFx2jsNQ3HEs6UdKhWI1BAB32R19VAkrMf-hc6QLOlREOugLVw-rif9urDHACnDjtgqWBEw2HJZL9594VCZCiTf8L3dPAhnSPAwpTlOUXtfu4F9d58z5FydOVx1hb40l4Z69C-tSPIKi7GglnIpYR3faZ2UvRYIr4IrqbOuFLxnhsYHcnghSxwZxEcHrxzIO_nKml1-xFptPhaK4689i_doLPYbMcFxDzRFQ6HKpS30EJUMpjJ3U710XE87L79cTUxcZIjnT1ct68Y7yeIjOiOmp6GxX1Pe3Un39DnIKneMi-4oP5x262BSXxXt6-iXxHjmN0W_rtwhSeSeaimE-rXB8_1T6g2v-9PrXt-Dw6qN_XU_9RVM48HL2er3D6J3YggI30hzSkZhy5cRkD9cFSiTIoWBRlLf33IsoYDaf0qNl64dbWzJ_qYLXb81tNXvNVLnJqluTbrNuFzEmSDxjdogAXF0NhJCyseli2t_YnrkXdttGugekSmnleM8FvpO5KupVEgXsbPAoIBLXOcO3f7zZt4QCvnasu41JaiDTVBbqzhWOFoSfhHR08wdQW5EAbjoIaFZt4Fc_m84qM1LuzaQQiiPP0yjnno-_1qA2l7c3cl4yyG1BWrYrM38tHWQnKBZA9rFDEnGnFZZP3cRWPL2NBZSqeoeKBp-rvPjNaUQk-47I30riUDC2g8APyHIXH5XAJR428vDWTIKNX9kFhfh7SI7-1lct5ra2WkS1d5FDlUF8h-RD0dxUURo2SfKUM_2rDt_SIASPEE95k3S9CzY-m0Kxw6vB9Dhoz25Gz8zacZyCDNXutGtQbjbWHU5n87sw9grQnunZX0tq3LmEPqahhGBBUjEddlnUeZ2qP1N3r_B57s9ABGdKS3MIrl66ewdiR36swV4GjxVlq3yEdmtqX1XqB2khUWZQsmcJPZrTxFEpTB0zoPEwNydRN2soIn2R6tbQeUiWeLR7kh0UtlpFDU_fYHtT8CNmPYklE8DvV27m7sVCRx4wxCnZbHDijctJoCo90V21dtNoaxiMFGqLBu1HDt6eBbzRMbk1hNs8pXpjpP2BohmiDQD1wJq1OcLqpLhRcVFuDp4PFKChvjVw5QpgO0GmeVY9d0derNfULTyesMyXY5X9Xi01L3nbF3r4T4t6hLPD-2MlKRyb-k7d4sQr7EKOTeQwQP9RgHKSKlJm7Bjc0iXIVR4t_GJhOlajRlbM3vhJO_5Bq8Dg7zugXluwdxhhVt3wJ9dkqPYHbzeLPC7voKvPWZY-9ZNgtNXRYIKDEYVDIfM60yBetgFv0i1V3l-Fuox-q8gBRXJ5dthDXngNdMqdF8eij4AoyXFVuU68QOI2zXwPsvwINmUwpP6p_QdFYGSt7b4tpYvtBOwsDDj16fzg_"
}
```

### Lỗi:
```json
{
  "success": false,
  "message": "Error message here"
}
```

## 🔧 Cấu Hình

### API Key
- Hiện tại: `ac51483e4f0908132f9ad0482722627b`
- Balance: ~33.70 USD
- Thay đổi trong file `puppeteer_2captcha.js` dòng 8

### Puppeteer
- Chạy headless mode (không hiển thị browser)
- Có thể thay đổi trong code

## 📁 Files Tạo Ra

- `test_screenshot.png`: Screenshot trang test
- Logs trong console

## 🐛 Troubleshooting

1. **Lỗi X server**: Đã fix bằng headless mode
2. **Lỗi API key**: Kiểm tra balance
3. **Lỗi network**: Kiểm tra internet connection
4. **Lỗi dependencies**: Chạy `npm install`

## 📞 Hỗ Trợ

Xem file `TEST_GUIDE.md` để biết thêm chi tiết. 