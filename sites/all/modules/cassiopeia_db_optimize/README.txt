CASSIOPEIA DATABASE OPTIMIZER
==========================

Module này cung cấp các công cụ để tối ưu hóa cơ sở dữ liệu Cassiopeia bị phình to.
Khi cơ sở dữ liệu đạt đến kích thước lớn (như 7GB trong trường hợp của bạn),
cần các giải pháp để duy trì hiệu suất tốt.

TÍNH NĂNG
---------

1. Quản lý chỉ mục database:
   - Tự động thêm chỉ mục (index) cho các bảng quan trọng
   - Theo dõi và tối ưu các chỉ mục hiện có

2. Lưu trữ dữ liệu tự động:
   - Di chuyển dữ liệu cũ sang bảng lưu trữ (archive)
   - Giảm kích thước bảng chính để cải thiện hiệu suất

3. Quản lý cache:
   - Cung cấp hệ thống cache cho các truy vấn phức tạp
   - Tự động làm sạch cache định kỳ

4. Tối ưu bảng:
   - Tự động thực hiện OPTIMIZE TABLE cho các bảng quan trọng
   - Giảm phân mảnh dữ liệu để cải thiện hiệu suất

5. Quản lý log:
   - Làm sạch watchdog logs cũ
   - Giảm kích thước bảng log

CÀI ĐẶT
-------

1. Giải nén và đặt thư mục module vào /sites/all/modules/
2. Bật module tại admin/modules
3. Cấu hình tại admin/config/system/cassiopeia-db-optimize

CÁCH SỬ DỤNG
-----------

1. Cấu hình tối ưu:
   - Thiết lập thời gian lưu trữ dữ liệu
   - Cấu hình tần suất làm sạch tự động

2. Thực hiện tối ưu thủ công:
   - Tối ưu bảng
   - Làm sạch cache
   - Lưu trữ dữ liệu cũ

3. Tích hợp với module khác:
   - Sử dụng API để mở rộng chức năng tối ưu
   - Thêm bảng tùy chỉnh vào danh sách tối ưu

CẤU HÌNH MYSQL KHUYẾN NGHỊ
------------------------

Thêm các cấu hình sau vào file my.cnf của MySQL:

[mysqld]
# Tăng bộ nhớ cache cho InnoDB
innodb_buffer_pool_size = 2G
innodb_log_file_size = 256M

# Tối ưu câu truy vấn
query_cache_size = 64M
query_cache_limit = 2M

# Tối ưu kết nối
max_connections = 150
thread_cache_size = 16

LƯU Ý
----

- Đảm bảo sao lưu cơ sở dữ liệu trước khi thực hiện tối ưu lớn.
- Các thao tác tối ưu nặng nên được thực hiện trong thời gian ít người dùng.
- Để đạt hiệu quả tối đa, nên kết hợp với tối ưu server MySQL.