# Vấn đề xuất dữ liệu Get-URL

## Vấn đề hiện tại
Khi truy cập vào URL `/get-url/result?keyword=&export=1`, trang hiển thị trắng thay vì xuất file Excel.

## Nguyên nhân chính
1. **Session bị xóa**: Template `page-get-url.tpl.php` ban đầu xóa session `$_SESSION['GetUrlData']` mỗi khi truy cập trang `/get-url`
2. **Không có dữ liệu**: Extension chưa gửi dữ liệu hoặc dữ liệu chưa được lưu vào session
3. **Thiếu xử lý lỗi**: Khi không có dữ liệu, trang hiển thị trắng thay vì thông báo lỗi

## Các sửa đổi đã thực hiện

### 1. Sửa logic session trong `page-get-url.tpl.php`
- **Trước**: Luôn xóa session khi vào trang
- **Sau**: Chỉ xóa session khi có tham số `?reset=1` hoặc khi session chưa tồn tại
- **Thêm**: Thông báo khi đã có dữ liệu từ lần tìm kiếm trước

### 2. Thêm xử lý lỗi trong `cassiopeia_get_url_result_page_callback()`
- Kiểm tra session có dữ liệu hay không
- Hiển thị thông báo lỗi thay vì trang trắng
- Redirect về trang phù hợp khi có lỗi

### 3. Thêm JavaScript reset session
- Reset session khi người dùng bắt đầu tìm kiếm mới
- Đảm bảo dữ liệu cũ không bị xung đột

### 4. Thêm endpoint debug
- Route `/get-url/debug` để kiểm tra session
- Hiển thị chi tiết dữ liệu trong session

## Cách sử dụng

### Kiểm tra session hiện tại
```
http://seominisuite_online.com:8888/get-url/debug
```

### Test với dữ liệu mẫu
```bash
php test_get_url_data.php
```

### Xuất Excel (sau khi có dữ liệu)
```
http://seominisuite_online.com:8888/get-url/result?keyword=&export=1
```

### Reset session và tìm kiếm mới
```
http://seominisuite_online.com:8888/get-url?reset=1
```

## Quy trình khắc phục

1. **Kiểm tra session**: Truy cập `/get-url/debug` để xem có dữ liệu không
2. **Nếu không có dữ liệu**:
   - Kiểm tra extension đã gửi dữ liệu chưa
   - Chạy script test: `php test_get_url_data.php`
3. **Nếu có dữ liệu**: Thử xuất Excel
4. **Nếu vẫn lỗi**: Kiểm tra log Drupal và permissions file

## Files đã thay đổi
- `sites/all/modules/cassiopeia/templates/pages/page-get-url.tpl.php`
- `sites/all/modules/cassiopeia/cassiopeia.inc`
- `sites/all/modules/cassiopeia/cassiopeia.module`
- `sites/all/modules/cassiopeia/js/get-url.js`

## Files mới
- `clear_cache.php` - Script clear cache
- `test_get_url_data.php` - Script test dữ liệu
- `README_GET_URL_ISSUE.md` - Tài liệu này
