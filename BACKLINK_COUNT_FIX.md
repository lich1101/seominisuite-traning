# Sửa lỗi hiển thị số lượng Backlink không chính xác

## 🔍 **Vấn đề được phát hiện:**

### **Mô tả vấn đề:**
- Trang hiển thị "Tất cả backlink(441)" nhưng thực tế có tận gần 800 dòng
- Số lượng hiển thị không khớp với số dòng thực tế trong bảng
- Người dùng bị nhầm lẫn về số lượng backlink thực sự

### **Nguyên nhân:**
Hệ thống đang đếm sai do cấu trúc database:

1. **`tbl_backlink`** - Bảng chính lưu các nguồn backlink (các website nguồn)
2. **`tbl_backlink_detail`** - Bảng chi tiết lưu các link cụ thể từ mỗi nguồn

**Logic cũ (sai):**
```php
$query->join("tbl_backlink_detail", "tbl_backlink_detail", "tbl_backlink_detail.nid=tbl_backlink.id");
$query->addExpression("COUNT(DISTINCT(tbl_backlink_detail.id))", "totalBacklink");
```

**Vấn đề:** Khi JOIN với `tbl_backlink_detail`, mỗi backlink có thể có nhiều detail, dẫn đến việc đếm trùng lặp.

## 🔧 **Giải pháp đã thực hiện:**

### **1. Sửa logic tính toán số lượng backlink**
**File:** `sites/all/modules/cassiopeia/templates/pages/page-backlink-project-detail.tpl.php`

**Thay đổi:**
```php
// TRƯỚC (sai):
$query->join("tbl_backlink_detail", "tbl_backlink_detail", "tbl_backlink_detail.nid=tbl_backlink.id");
$query->addExpression("COUNT(DISTINCT(tbl_backlink_detail.id))", "totalBacklink");

// SAU (đúng):
$query->addExpression("COUNT(DISTINCT(tbl_backlink.id))", "totalBacklink");
```

### **2. Tách riêng query tính dofollow**
**Thay đổi:**
```php
// Query chính để tính tổng số backlink nguồn
$query = db_select("tbl_backlink", "tbl_backlink");
$query->addExpression("COUNT(DISTINCT(tbl_backlink.id))", "totalBacklink");
$query->addExpression("SUM(CASE WHEN tbl_backlink.indexed=1 THEN 1 ELSE 0 END)", "total_indexed");

// Query riêng để tính dofollow từ detail
$dofollow_query = db_select("tbl_backlink", "tbl_backlink");
$dofollow_query->join("tbl_backlink_detail", "tbl_backlink_detail", "tbl_backlink_detail.nid=tbl_backlink.id");
$dofollow_query->addExpression("SUM(CASE WHEN tbl_backlink_detail.rel='dofollow' THEN 1 ELSE 0 END)", "total_dofollow");
```

### **3. Cập nhật hiển thị**
**Thay đổi:**
```php
// TRƯỚC:
Do follow(<?php echo($result->total_dofollow) ?>)

// SAU:
Do follow(<?php echo($dofollow_result->total_dofollow) ?>)
```

## 📊 **Kết quả mong đợi:**

### **Trước khi sửa:**
- Hiển thị: "Tất cả backlink(441)"
- Thực tế: ~800 dòng
- **Không khớp:** 441 ≠ 800

### **Sau khi sửa:**
- Hiển thị: "Tất cả backlink(800)" (hoặc số thực tế)
- Thực tế: ~800 dòng
- **Khớp:** 800 = 800

## 🗄️ **Cấu trúc Database:**

### **Bảng `tbl_backlink`:**
- `id` - ID của backlink nguồn
- `pid` - ID dự án
- `refer_page` - URL nguồn
- `domain` - Domain nguồn
- `indexed` - Trạng thái index
- `status` - Trạng thái backlink

### **Bảng `tbl_backlink_detail`:**
- `id` - ID của detail
- `nid` - ID của backlink nguồn (foreign key)
- `url` - URL cụ thể
- `rel` - Thuộc tính (dofollow/nofollow)
- `anchor_text` - Text anchor
- `is_in_content` - Có trong nội dung không

### **Mối quan hệ:**
```
1 tbl_backlink → N tbl_backlink_detail
```

## 🔍 **Cách kiểm tra:**

### **1. Kiểm tra số lượng thực tế:**
```sql
SELECT COUNT(DISTINCT id) as total_backlinks 
FROM tbl_backlink 
WHERE pid = [project_id];
```

### **2. Kiểm tra số lượng detail:**
```sql
SELECT COUNT(*) as total_details 
FROM tbl_backlink_detail 
WHERE nid IN (
    SELECT id FROM tbl_backlink WHERE pid = [project_id]
);
```

### **3. Kiểm tra dofollow:**
```sql
SELECT COUNT(*) as total_dofollow 
FROM tbl_backlink_detail 
WHERE nid IN (
    SELECT id FROM tbl_backlink WHERE pid = [project_id]
) AND rel = 'dofollow';
```

## 🚀 **Deploy:**

1. **Upload file đã sửa**
2. **Clear cache:** `drush cc all`
3. **Kiểm tra lại trang backlink management**
4. **Verify số lượng hiển thị khớp với thực tế**

## 📝 **Lưu ý quan trọng:**

1. **Backup database** trước khi deploy
2. **Test với nhiều dự án** khác nhau
3. **Kiểm tra performance** của query mới
4. **Verify dofollow count** vẫn chính xác

## 🎯 **Lợi ích:**

- ✅ **Số liệu chính xác:** Người dùng thấy số lượng backlink thực tế
- ✅ **Tránh nhầm lẫn:** Không còn bị lừa bởi số liệu sai
- ✅ **Dễ quản lý:** Biết chính xác có bao nhiêu nguồn backlink
- ✅ **Báo cáo đúng:** Các báo cáo sẽ dựa trên số liệu chính xác
