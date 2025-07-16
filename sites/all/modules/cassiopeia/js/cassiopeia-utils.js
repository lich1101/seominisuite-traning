/**
 * Cassiopeia Utilities - Các hàm tiện ích dùng chung cho module cassiopeia
 */

// Thiết lập biến basePath toàn cục
var basePath = '';
if (typeof Drupal !== 'undefined' && Drupal.settings && Drupal.settings.basePath) {
  basePath = Drupal.settings.basePath;
} else {
  basePath = '/';
}

/**
 * Lấy domain hiện tại
 * @returns {string} Domain hiện tại (với protocol)
 */
function getCurrentDomain() {
  return window.location.origin;
}

/**
 * Trích xuất domain từ một URL
 * @param {string} href URL cần trích xuất domain
 * @returns {string} Domain đã được trích xuất
 */
function getDomainFromHref(href) {
  let c = "";
  if (href !== undefined) {
    let a = href.split("//");
    let d;
    if (a.length < 2) {
      d = a[0]
    } else {
      d = a[1]
    }
    if (d !== null && d !== "" && d !== undefined) {
      let b = d.split("/");
      c = b[0].toLowerCase();
      c = c.replaceAll("www.", "")
    }
  }
  return c;
}

/**
 * Gửi thông báo đến extension
 * @param {string} eventName Tên sự kiện
 * @param {object} data Dữ liệu gửi đi
 */
function sendEventToExtension(eventName, data) {
  try {
    // Đảm bảo luôn có domain và basePath
    const eventData = Object.assign({
      domain: getCurrentDomain(),
      basePath: basePath
    }, data || {});

    document.dispatchEvent(new CustomEvent(eventName, {
      detail: eventData
    }));
    console.log("Đã gửi sự kiện " + eventName);
  } catch (e) {
    console.error("Không thể gửi sự kiện " + eventName + ":", e);
  }
}

/**
 * Xóa dấu tiếng Việt
 * @param {string} str Chuỗi cần xóa dấu
 * @returns {string} Chuỗi đã xóa dấu
 */
function removeAccents(str) {
  if (str === null || str === undefined) return '';
  str = str.toString();
  return str.normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/đ/g, 'd').replace(/Đ/g, 'D');
}

/**
 * Đóng thông báo quét
 */
function closeScanningMessage() {
  try {
    // Gửi thông báo đến extension để đóng popup "Chương trình đang chạy"
    sendEventToExtension("ScanCompleted", {
      status: "completed"
    });
    console.log("Đã gửi tín hiệu đóng thông báo quét");
  } catch (e) {
    console.error("Không thể đóng thông báo quét:", e);
  }
}

// Đăng ký hàm toàn cục để các extension có thể gọi
window.closeScanningMessage = closeScanningMessage;

// Thêm sự kiện lắng nghe để đóng thông báo khi trang đã tải xong
document.addEventListener("DOMContentLoaded", function() {
  // Kiểm tra xem có cần đóng thông báo không
  if (typeof window.closeScanningMessage === 'function') {
    window.closeScanningMessage();
  }
});
