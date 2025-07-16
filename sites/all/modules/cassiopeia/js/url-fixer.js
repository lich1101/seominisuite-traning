/**
 * URL Fixer - Tự động sửa các URL tuyệt đối thành URL tương đối
 *
 * File này được sử dụng để sửa các URL tuyệt đối trong các AJAX request
 * mà không cần sửa từng file JavaScript. Nó hoạt động bằng cách nắm bắt
 * (intercepting) các request AJAX và sửa URL của chúng trước khi gửi.
 */

(function($) {
  // Lấy basePath từ biến toàn cục hoặc Drupal settings
  var basePath = '';
  if (typeof window.basePath !== 'undefined') {
    basePath = window.basePath;
  } else if (typeof Drupal !== 'undefined' && Drupal.settings && Drupal.settings.basePath) {
    basePath = Drupal.settings.basePath;
    // Đảm bảo basePath có sẵn dưới dạng biến toàn cục
    window.basePath = basePath;
  } else {
    basePath = '/';
    window.basePath = basePath;
  }

  // Lưu trữ phương thức ajax gốc
  var originalAjax = $.ajax;

  // Nắm bắt và sửa tất cả các AJAX request
  $.ajax = function(options) {
    // Tạo một bản sao của options để không thay đổi đối tượng gốc
    var newOptions = $.extend(true, {}, options);

    // Kiểm tra và sửa URL nếu nó là URL tuyệt đối bắt đầu bằng "/"
    if (typeof newOptions.url === 'string' && newOptions.url.charAt(0) === '/' && newOptions.url.charAt(1) !== '/') {
      // Loại bỏ dấu / nếu basePath đã có
      if (basePath.charAt(basePath.length - 1) === '/') {
        newOptions.url = basePath + newOptions.url.substring(1);
      } else {
        newOptions.url = basePath + newOptions.url;
      }

      console.log('URL Fixer: Đã sửa URL từ ' + options.url + ' thành ' + newOptions.url);
    }

    // Gọi phương thức ajax gốc với options đã sửa
    return originalAjax.call(this, newOptions);
  };

  // Nắm bắt các sự kiện gửi form đến URL tuyệt đối
  $(document).on('submit', 'form', function(e) {
    var $form = $(this);
    var action = $form.attr('action');

    // Kiểm tra và sửa action nếu nó là URL tuyệt đối bắt đầu bằng "/"
    if (typeof action === 'string' && action.charAt(0) === '/' && action.charAt(1) !== '/') {
      // Loại bỏ dấu / nếu basePath đã có
      if (basePath.charAt(basePath.length - 1) === '/') {
        action = basePath + action.substring(1);
      } else {
        action = basePath + action;
      }

      $form.attr('action', action);
      console.log('URL Fixer: Đã sửa form action từ ' + $form.attr('action') + ' thành ' + action);
    }
  });

  // Lưu trữ lại hàm gốc để có thể sử dụng trong trường hợp cần thiết
  window.originalAjax = originalAjax;

  console.log('URL Fixer: Đã khởi tạo với basePath = ' + basePath);
})(jQuery);
