# Hướng dẫn sửa lỗi toàn diện cho SeoMiniSuite

Tài liệu này mô tả các vấn đề đã được xác định trong hệ thống SeoMiniSuite và cách triển khai các thay đổi để sửa lỗi trên toàn bộ hệ thống.

## Các vấn đề đã xác định

1. **Vấn đề về phiên làm việc**: Các chức năng không kết thúc phiên làm việc đúng cách trước khi chuyển hướng.
2. **Vấn đề về URL tuyệt đối**: Nhiều file JavaScript sử dụng URL tuyệt đối, không hoạt động trên các domain và subdomain khác nhau.
3. **Vấn đề về chọn dòng mặc định**: Các bảng tự động chọn dòng đầu tiên sau khi tải trang.
4. **Vấn đề về giao tiếp với extension**: Thông tin domain và basePath không được gửi đến extension.

## Các file đã được tạo/sửa đổi

1. **sites/all/modules/cassiopeia/js/cassiopeia-utils.js**: File tiện ích JavaScript với các hàm dùng chung.
2. **sites/all/modules/cassiopeia/includes/cassiopeia-session.inc**: File tiện ích PHP để xử lý phiên làm việc và chuyển hướng.
3. **sites/all/modules/cassiopeia/js/url-fixer.js**: Script tự động sửa các URL tuyệt đối thành URL tương đối trong runtime.

## Hướng dẫn triển khai

### 1. Bổ sung file tiện ích vào hệ thống

1. Đảm bảo các file sau được thêm vào hệ thống:
   - `sites/all/modules/cassiopeia/js/cassiopeia-utils.js`
   - `sites/all/modules/cassiopeia/includes/cassiopeia-session.inc`
   - `sites/all/modules/cassiopeia/js/url-fixer.js`

2. Cập nhật file `sites/all/modules/cassiopeia/cassiopeia.module` để bao gồm các file tiện ích:

```php
function cassiopeia_init() {
  global $user;
  // Thêm file tiện ích JavaScript toàn cục (đặt trọng số -100 để đảm bảo nó được tải trước)
  drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/cassiopeia-utils.js', array('weight' => -100));
  drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/url-fixer.js', array('weight' => -99));

  drupal_add_js(array('user_js_uid' => $user->uid), 'setting');

  drupal_add_css(drupal_get_path('module', 'cassiopeia') . '/js/libs/jquery-confirm/jquery-confirm.min.css');
  drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/libs/jquery-confirm/jquery-confirm.min.js');
}

/**
 * Implementation of hook_boot().
 */
function cassiopeia_boot() {
  // Tải file tiện ích quản lý phiên
  module_load_include('inc', 'cassiopeia', 'includes/cassiopeia-session');
}
```

### 2. Sửa các file PHP

Thay thế tất cả các lệnh `drupal_goto()` bằng `cassiopeia_safe_goto()` trong các file PHP của module cassiopeia. Dưới đây là danh sách các file cần kiểm tra:

- `sites/all/modules/cassiopeia/cassiopeia.inc`
- `sites/all/modules/cassiopeia_nl/cassiopeia_nl.module`
- `sites/all/modules/cassiopeia_nl/cassiopeia_nl.inc`
- `sites/all/modules/cassiopeia_user/cassiopeia_user.module`
- `sites/all/modules/cassiopeia_guest_post/cassiopeia_guest_post.module`

Ví dụ về cách thay thế:

```php
// Thay
drupal_goto("excel/download/".$export['name']);

// Bằng
cassiopeia_safe_goto("excel/download/".$export['name']);
```

### 3. Sửa các file JavaScript

Tùy chọn 1: Cập nhật thủ công từng file JavaScript để sử dụng basePath:

- Thay thế tất cả các URL dạng `/cassiopeia/ajax` bằng `basePath + "cassiopeia/ajax"`
- Thay thế các URL tuyệt đối khác bằng URL tương đối sử dụng biến basePath

Tùy chọn 2: Sử dụng file `url-fixer.js` để tự động sửa các URL:

- Đảm bảo file `url-fixer.js` được tải trong tất cả các trang
- Không cần sửa thủ công các file JavaScript, url-fixer sẽ xử lý trong runtime

### 4. Sửa vấn đề về checkbox được chọn mặc định

Thêm đoạn code sau vào tất cả các file JavaScript xử lý bảng có checkbox:

```javascript
// Đảm bảo không có checkbox nào được chọn mặc định sau khi tải trang
setTimeout(function() {
  $("tbody input[type='checkbox']").prop('checked', false);
}, 500);
```

### 5. Cập nhật giao tiếp với extension

Sử dụng hàm `sendEventToExtension()` từ file `cassiopeia-utils.js` để gửi sự kiện đến extension:

```javascript
// Thay
document.dispatchEvent(new CustomEvent('CheckIndexed', {
  detail: {
    data: '123'
  }
}));

// Bằng
sendEventToExtension('CheckIndexed', {
  data: '123'
});
```

## Kiểm tra sau khi triển khai

1. Kiểm tra chức năng quét số điện thoại, backlink và Google indexed
2. Kiểm tra hệ thống trên các domain và subdomain khác nhau
3. Kiểm tra các bảng để đảm bảo không có checkbox nào được chọn mặc định
4. Kiểm tra giao tiếp với extension để đảm bảo thông tin domain và basePath được gửi đúng

## Lưu ý quan trọng

1. Sao lưu tất cả các file trước khi thực hiện thay đổi
2. Xóa cache trình duyệt và Drupal sau khi triển khai thay đổi
3. Kiểm tra kỹ các chức năng sau khi triển khai để đảm bảo không có lỗi mới

---

Tài liệu này được tạo bởi Claude 3.7 Sonnet (Anthropic)
