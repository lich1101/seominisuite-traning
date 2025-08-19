# Khắc phục lỗi "Thiếu dữ liệu node hoặc content" - Website vts.net.vn

## 🎯 Vấn đề phát hiện

Website `https://vts.net.vn/` trả về lỗi:
```json
{
  "success": false,
  "data": {
    "message": "Thiếu dữ liệu node hoặc content"
  }
}
```

## 🔍 Phân tích nguyên nhân

### **Kết quả test:**
1. ✅ Website có thể truy cập được
2. ✅ Plugin WordPress hoạt động
3. ✅ admin-ajax.php tồn tại
4. ❌ **Dữ liệu gửi đi không đúng format hoặc thiếu thông tin**

### **Nguyên nhân có thể:**
1. **Thiếu trường dữ liệu bắt buộc** trong object `$article`
2. **Format JSON không đúng** khi encode
3. **Content bị rỗng** hoặc không đúng format
4. **Plugin WordPress yêu cầu thêm trường dữ liệu**

## 🔧 Thay đổi đã thực hiện

### 1. **Thêm Debug Logging chi tiết**
```php
// Debug logging - dữ liệu gửi đi
error_log("Guest Post Debug - Sending data to: " . $article->website_domain);
error_log("Guest Post Debug - Article object: " . print_r($article, true));
error_log("Guest Post Debug - Content: " . substr($content, 0, 200) . "...");
error_log("Guest Post Debug - Title: " . $article->title);
error_log("Guest Post Debug - Cookie: " . substr($cookie, 0, 50) . "...");

// Debug logging - response
error_log("Guest Post Debug - HTTP Code: " . $http_code);
error_log("Guest Post Debug - Response: " . $response);
error_log("Guest Post Debug - cURL Error: " . $curl_error);
error_log("Guest Post Debug - cURL Error No: " . $curl_errno);
```

### 2. **Thêm xử lý lỗi WordPress success = false**
```php
// Kiểm tra response có success = false
if (is_object($decoded_response) && isset($decoded_response->success) && $decoded_response->success === false) {
    $error_message = "Website báo lỗi: ";
    if (isset($decoded_response->data) && isset($decoded_response->data->message)) {
        $error_message .= $decoded_response->data->message;
    } else {
        $error_message .= "Không xác định được lỗi";
    }
    error_log("Guest Post WordPress Success False: " . $error_message);
    return (object) array('error' => $error_message, 'type' => 'wordpress_error');
}
```

## 📋 Cách Debug

### 1. **Kiểm tra log sau khi đăng bài:**
```bash
# Tìm log mới nhất
grep "Guest Post Debug" /var/log/nginx/error.log | tail -20
```

### 2. **Thông tin cần kiểm tra:**
- **Article object:** Xem có đầy đủ thông tin không
- **Content:** Xem có bị rỗng không
- **Title:** Xem có đúng format không
- **Response:** Xem website trả về gì chính xác

### 3. **Test thủ công:**
```bash
# Test với dữ liệu đầy đủ
curl -X POST https://vts.net.vn/wp-admin/admin-ajax.php \
  -d "action=seominisuite_add_article&node={\"title\":\"Test Title\",\"content\":\"Test content\"}&content=Test content&title=Test Title" \
  -H "Content-Type: application/x-www-form-urlencoded"
```

## 🛠️ Cách khắc phục

### **Bước 1: Kiểm tra dữ liệu gửi đi**
Xem log để đảm bảo:
- `$article` object có đầy đủ thông tin
- `content` không bị rỗng
- `title` không bị rỗng

### **Bước 2: Kiểm tra format JSON**
Đảm bảo `json_encode($article)` tạo ra JSON hợp lệ

### **Bước 3: Kiểm tra plugin WordPress**
Có thể plugin yêu cầu thêm trường dữ liệu khác

### **Bước 4: Cập nhật cookie**
Đảm bảo cookie hợp lệ cho website này

## 📊 Kết quả mong đợi

Sau khi áp dụng các thay đổi:
1. **Thông báo lỗi chi tiết:** "Website báo lỗi: Thiếu dữ liệu node hoặc content"
2. **Logging chi tiết:** Để admin có thể debug
3. **Xử lý đúng response:** Không còn "không nhận được phản hồi"

## 🔄 Các bước tiếp theo

1. **Thử đăng bài lại** với website `vts.net.vn`
2. **Kiểm tra log** để xem dữ liệu gửi đi
3. **So sánh** với website khác hoạt động tốt
4. **Cập nhật** format dữ liệu nếu cần

## 📝 Ghi chú

- Website `vts.net.vn` có plugin WordPress hoạt động
- Lỗi là do format dữ liệu, không phải kết nối
- Cần kiểm tra yêu cầu cụ thể của plugin WordPress
