/**
 * Giải quyết xung đột jQuery giữa phiên bản mặc định (1.4.4) và phiên bản mới (2.2.4)
 * File này sẽ được tải ngay sau jQuery 2.2.4 và trước Bootstrap
 */

// Đảm bảo sử dụng jQuery 2.2.4 cho tất cả plugin và phần mã cũ
(function() {
  // Bảo vệ $ và jQuery toàn cục
  var jq = jQuery.noConflict(true);

  // Gán lại $ và jQuery để sử dụng jQuery 2.2.4
  window.jQuery = window.$ = jq;

  // Khắc phục các hàm quan trọng từ jQuery 1.4.4 sang jQuery 2.2.4
  if (typeof jQuery.fn.on !== 'function') {
    // Triển khai .on() cho các phiên bản jQuery cũ
    jQuery.fn.on = function(events, selector, data, handler) {
      return this.delegate(selector, events, data, handler);
    };
  }

  if (typeof jQuery.fn.off !== 'function') {
    // Triển khai .off() cho các phiên bản jQuery cũ
    jQuery.fn.off = function(events, selector, handler) {
      return this.undelegate(selector, events, handler);
    };
  }

  // Sửa lỗi cho các sự kiện của Drupal
  if (typeof Drupal !== 'undefined' && Drupal.behaviors) {
    // Lưu lại behaviors gốc
    var originalBehaviors = Drupal.behaviors;

    // Đảm bảo behaviors sử dụng jQuery mới
    for (var behavior in originalBehaviors) {
      if (originalBehaviors.hasOwnProperty(behavior) &&
          typeof originalBehaviors[behavior].attach === 'function') {

        // Lưu hàm attach gốc
        var originalAttach = originalBehaviors[behavior].attach;

        // Thay thế hàm attach để sử dụng jQuery mới
        originalBehaviors[behavior].attach = function(context, settings) {
          return originalAttach.call(this, context, settings);
        };
      }
    }
  }

  console.log("jQuery đã được khởi tạo lại với phiên bản: " + jQuery.fn.jquery);
})();
