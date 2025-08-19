# Tính năng Lưu trữ Dữ liệu Kiểm tra Đạo văn

## 🎯 Mục tiêu

Giải quyết vấn đề mất dữ liệu kiểm tra đạo văn khi chuyển đổi giữa các website trong form đăng bài guest post.

## 🔧 Vấn đề ban đầu

**Trước khi sửa:**
- Khi người dùng kiểm tra đạo văn cho một bài viết
- Sau đó chuyển sang website khác để đăng bài
- Dữ liệu kiểm tra đạo văn bị mất hoàn toàn
- Phải kiểm tra lại từ đầu

## ✅ Giải pháp đã thực hiện

### **1. Hệ thống lưu trữ dữ liệu:**
```javascript
// Lưu trữ dữ liệu kiểm tra đạo văn
function saveDuplicateCheckData() {
    let duplicateData = {
        timestamp: new Date().getTime(),
        content: tinyMCE.activeEditor.getContent(),
        results: {}
    };
    
    // Lưu kết quả từng câu
    $('.duplicate-content-table tbody tr').each(function() {
        // Lưu query, sources, result cho từng câu
    });
    
    // Lưu tổng kết
    duplicateData.summary = {
        noneDuplicate: noneDuplicate,
        duplicate: duplicate
    };
    
    // Lưu vào localStorage
    localStorage.setItem('guest_post_duplicate_check_data', JSON.stringify(duplicateData));
}
```

### **2. Hệ thống khôi phục dữ liệu:**
```javascript
// Khôi phục dữ liệu kiểm tra đạo văn
function restoreDuplicateCheckData() {
    let savedData = localStorage.getItem('guest_post_duplicate_check_data');
    if (savedData) {
        // Khôi phục nội dung bài viết
        // Khôi phục kết quả kiểm tra từng câu
        // Khôi phục tổng kết
    }
}
```

### **3. Tự động lưu trữ:**
- **Lưu khi hoàn thành kiểm tra:** Tự động lưu khi quá trình kiểm tra kết thúc
- **Lưu khi có thay đổi:** Lưu khi người dùng chỉnh sửa nội dung
- **Lưu khi DOM thay đổi:** Lưu khi có thay đổi trong bảng kết quả

### **4. Tự động khôi phục:**
- **Khôi phục khi load trang:** Tự động khôi phục dữ liệu khi vào form
- **Kiểm tra thời gian:** Chỉ khôi phục dữ liệu trong vòng 1 giờ
- **Xử lý lỗi:** Tự động xóa dữ liệu lỗi

## 📋 Dữ liệu được lưu trữ

### **Thông tin cơ bản:**
- **Timestamp:** Thời gian lưu trữ
- **Content:** Nội dung bài viết từ TinyMCE editor

### **Kết quả kiểm tra từng câu:**
- **Query:** Câu truy vấn được kiểm tra
- **Sources:** Danh sách nguồn trùng lặp (HTML)
- **Result:** Kết quả (true/false)
- **ResultValue:** Giá trị số (0/1)

### **Tổng kết:**
- **NoneDuplicate:** Phần trăm nội dung độc đáo
- **Duplicate:** Phần trăm nội dung trùng lặp

## 🔄 Quy trình hoạt động

### **Khi kiểm tra đạo văn:**
1. Người dùng nhập nội dung và nhấn "Kiểm tra đạo văn"
2. Hệ thống thực hiện kiểm tra
3. **Tự động lưu dữ liệu** khi hoàn thành
4. Hiển thị kết quả cho người dùng

### **Khi chuyển đổi website:**
1. Người dùng chọn website khác
2. Form được reload
3. **Tự động khôi phục dữ liệu** sau 1 giây
4. Hiển thị lại kết quả kiểm tra đạo văn

### **Khi đăng bài thành công:**
1. Bài viết được đăng thành công
2. **Tự động xóa dữ liệu** đã lưu
3. Chuẩn bị cho bài viết mới

## 🛡️ Tính năng bảo mật

### **Thời gian hết hạn:**
- Dữ liệu tự động hết hạn sau **1 giờ**
- Tránh lưu trữ dữ liệu cũ không cần thiết

### **Xử lý lỗi:**
- Tự động xóa dữ liệu JSON lỗi
- Log lỗi để debug
- Fallback an toàn khi không có dữ liệu

### **Kiểm tra điều kiện:**
- Chỉ lưu khi có dữ liệu thực sự
- Chỉ khôi phục khi TinyMCE editor sẵn sàng
- Tránh lưu dữ liệu rỗng

## 🎯 Kết quả mong đợi

### **Trước khi sửa:**
- ❌ Mất dữ liệu kiểm tra đạo văn khi chuyển website
- ❌ Phải kiểm tra lại từ đầu
- ❌ Tốn thời gian và công sức

### **Sau khi sửa:**
- ✅ Giữ nguyên dữ liệu kiểm tra đạo văn
- ✅ Tự động khôi phục khi chuyển website
- ✅ Tiết kiệm thời gian và công sức
- ✅ Trải nghiệm người dùng tốt hơn

## 📝 Cách sử dụng

1. **Nhập nội dung** vào form đăng bài
2. **Kiểm tra đạo văn** - dữ liệu sẽ được lưu tự động
3. **Chuyển đổi website** - dữ liệu sẽ được khôi phục tự động
4. **Đăng bài** - dữ liệu sẽ được xóa tự động

## ⚠️ Lưu ý

- Dữ liệu chỉ được lưu trong **localStorage** của trình duyệt
- Dữ liệu sẽ **tự động hết hạn** sau 1 giờ
- Dữ liệu sẽ **bị mất** nếu xóa cache trình duyệt
- Chỉ hoạt động trên **cùng một trình duyệt**

## 🔧 Cấu hình

### **Thời gian hết hạn:**
```javascript
// Thay đổi thời gian hết hạn (mili giây)
if (now - duplicateData.timestamp > 3600000) { // 1 giờ
```

### **Key lưu trữ:**
```javascript
// Thay đổi key lưu trữ
localStorage.setItem('guest_post_duplicate_check_data', JSON.stringify(duplicateData));
```

### **Selector CSS:**
```javascript
// Thay đổi selector nếu cần
$('.duplicate-content-check .duplicate-content-table tbody tr')
```

## 🐛 Debug và Testing

### **Các function test có sẵn:**
```javascript
// Kiểm tra dữ liệu hiện tại
testDuplicateCheckData()

// Lưu dữ liệu thủ công
saveDuplicateCheckData()

// Khôi phục dữ liệu thủ công
restoreDuplicateCheckData()

// Xóa dữ liệu
clearDuplicateCheckData()
```

### **Cách debug:**
1. Mở Developer Tools (F12)
2. Vào tab Console
3. Gọi `testDuplicateCheckData()` để xem dữ liệu hiện tại
4. Kiểm tra các log message để theo dõi quá trình lưu/khôi phục

### **Các log message quan trọng:**
- `"Bắt đầu lưu dữ liệu kiểm tra đạo văn..."`
- `"Đã lưu dữ liệu kiểm tra đạo văn:"`
- `"Bắt đầu khôi phục dữ liệu kiểm tra đạo văn..."`
- `"Đã khôi phục nội dung bài viết"`
- `"Đã khôi phục tổng kết:"`

