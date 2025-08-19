# Cải thiện Logic Quét Website - Kiểm tra khả năng đăng bài thực tế

## 🎯 Mục tiêu
Cải thiện logic quét website để website chỉ hiển thị "OK" khi thực sự có thể đăng bài được, không chỉ kiểm tra khả năng truy cập cơ bản.

## 🔧 Thay đổi đã thực hiện

### 1. Sửa logic trong `cassiopeia_guest_post.inc`
**File:** `sites/all/modules/cassiopeia_guest_post/cassiopeia_guest_post.inc`
**Function:** `cassiopeia_guest_post_ajax_page()` - case "Guest_Post_Website_Get_Categories_Complete"

**Thay đổi:**
- Thêm kiểm tra khả năng đăng bài thực tế trước khi đặt status = 1
- Chỉ lưu danh mục và đặt status = 1 khi website thực sự có thể đăng bài
- Đặt status = 0 nếu không thể đăng bài

### 2. Thêm function kiểm tra khả năng đăng bài
**File:** `sites/all/modules/cassiopeia_guest_post/cassiopeia_guest_post.module`

**Function mới:**
- `cassiopeia_guest_post_test_posting_ability($domain)`: Kiểm tra khả năng đăng bài
- `cassiopeia_guest_post_get_website_cookie($domain)`: Lấy cookie cho website
- `cassiopeia_guest_post_update_website_cookie($domain, $cookie)`: Cập nhật cookie

## 📋 Logic mới

### Quy trình quét website:
1. **Kiểm tra kết nối cơ bản** (như cũ)
2. **Lấy danh mục WordPress** (như cũ)
3. **Kiểm tra khả năng đăng bài thực tế** (MỚI)
   - Gửi request POST đến `/wp-admin/admin-ajax.php`
   - Sử dụng cookie thực tế của website
   - Kiểm tra HTTP status code
4. **Xác định trạng thái cuối cùng**

### Điều kiện để hiển thị "OK":
- ✅ Có thể truy cập `/wp-admin/admin-ajax.php`
- ✅ Có thể lấy được danh mục WordPress
- ✅ **Có thể gửi request POST với authentication hợp lệ**
- ✅ **Không bị lỗi HTTP 403 (Authentication failed)**

### Điều kiện để hiển thị "Lỗi":
- ❌ Không thể kết nối đến website
- ❌ Không thể lấy danh mục WordPress
- ❌ **HTTP 403 - Cookie/authentication không hợp lệ**
- ❌ **HTTP 404 - admin-ajax.php không tồn tại**
- ❌ **HTTP 500 - Lỗi server**
- ❌ **Không thể kết nối (HTTP 0)**

## 🎯 Kết quả mong đợi

### Trước khi sửa:
- Website `giadung-thongminh.com` hiển thị "OK" (có thể truy cập)
- Nhưng khi đăng bài bị lỗi HTTP 403

### Sau khi sửa:
- Website `giadung-thongminh.com` sẽ hiển thị "Lỗi" (không thể đăng bài)
- Chỉ những website thực sự có thể đăng bài mới hiển thị "OK"

## 📝 Logging

Hệ thống sẽ ghi log chi tiết:
- `error_log("Website posting test failed for {$domain}: HTTP 403 - Authentication failed");`
- `error_log("Website posting test successful for {$domain}: HTTP " . $http_code);`

## 🔄 Cách test

1. Vào trang quản lý website
2. Chọn website cần quét
3. Nhấn nút "Quét"
4. Kiểm tra kết quả:
   - Website có cookie hợp lệ → "OK"
   - Website có cookie hết hạn → "Lỗi"

## ⚠️ Lưu ý

- Việc quét sẽ mất nhiều thời gian hơn do phải kiểm tra authentication
- Cần đảm bảo cookie được cập nhật thường xuyên
- Có thể tạo trang admin để quản lý cookie cho từng website
