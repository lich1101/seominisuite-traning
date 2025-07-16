/**
 * Script để khắc phục vấn đề jQuery-Bootstrap
 */

// Lưu phiên bản jQuery hiện tại (nếu có)
var oldJQuery = window.jQuery || null;

// Tải jQuery 2.2.4
(function() {
  var script = document.createElement('script');
  script.src = 'https://code.jquery.com/jquery-2.2.4.min.js';
  script.type = 'text/javascript';

  // Đợi tải jQuery mới xong và thiết lập lại jQuery cho trang
  script.onload = function() {
    // Thiết lập biến $ và jQuery toàn cục
    var newJQuery = jQuery.noConflict(true);
    window.jQuery = window.$ = newJQuery;

    console.log("jQuery được nâng cấp lên phiên bản: " + jQuery.fn.jquery);

    // Tải jQuery Once plugin (cần thiết cho Drupal)
    var onceScript = document.createElement('script');
    onceScript.src = '/sites/all/libraries/jquery.once/jquery.once.min.js';
    onceScript.type = 'text/javascript';
    document.getElementsByTagName('head')[0].appendChild(onceScript);

    // Tải Bootstrap sau khi jQuery đã sẵn sàng
    var bootstrapScript = document.createElement('script');
    bootstrapScript.src = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js';
    bootstrapScript.type = 'text/javascript';
    bootstrapScript.onload = function() {
      console.log("Bootstrap đã được tải thành công");

      // Đảm bảo tooltip có sẵn
      if (typeof jQuery.fn.tooltip === 'undefined') {
        jQuery.fn.tooltip = function(options) {
          var defaults = {
            template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
            placement: 'top',
            trigger: 'hover'
          };
          options = jQuery.extend(defaults, options || {});

          return this.each(function() {
            var $el = jQuery(this);
            var title = $el.attr('title') || $el.data('original-title') || '';

            if (title) {
              $el.attr('data-original-title', title).removeAttr('title');

              $el.hover(function() {
                var $tooltip = jQuery('<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner">' + title + '</div></div>');
                $tooltip.addClass('in ' + options.placement);
                jQuery('body').append($tooltip);

                var position = $el.offset();
                var elWidth = $el.outerWidth();
                var elHeight = $el.outerHeight();
                var tooltipWidth = $tooltip.outerWidth();
                var tooltipHeight = $tooltip.outerHeight();

                if (options.placement === 'top') {
                  $tooltip.css({
                    top: position.top - tooltipHeight - 5,
                    left: position.left + (elWidth/2) - (tooltipWidth/2)
                  });
                }

                $el.data('tooltip-element', $tooltip);
              }, function() {
                var $tooltip = $el.data('tooltip-element');
                if ($tooltip) {
                  $tooltip.remove();
                  $el.removeData('tooltip-element');
                }
              });
            }
          });
        };
        console.log("Polyfill cho tooltip đã được tạo");
      }

      // Kích hoạt sự kiện để thông báo jQuery đã được cập nhật
      var event = document.createEvent('Event');
      event.initEvent('jQueryUpdated', true, true);
      document.dispatchEvent(event);
    };
    document.getElementsByTagName('head')[0].appendChild(bootstrapScript);
  };

  // Chèn script vào phần head của trang
  document.getElementsByTagName('head')[0].appendChild(script);
})();

// Sửa lỗi cho các plugin jQuery đã được tải với jQuery cũ
document.addEventListener('jQueryUpdated', function() {
  // Nếu có bất kỳ plugin nào đã được tải với jQuery cũ,
  // bạn có thể khởi tạo lại chúng ở đây

  // Đảm bảo once() có sẵn cho Drupal
  if (typeof jQuery.fn.once !== 'function') {
    jQuery.fn.once = function(id) {
      var element = this;
      id = id || 'once';

      if (element.length && !element.hasClass(id)) {
        element.addClass(id);
        return element;
      }
      return jQuery();
    };
    console.log("Tạo polyfill cho jQuery.once");
  }

  // Ví dụ: khởi tạo lại confirm plugin nếu cần
  if (jQuery.fn.confirm && typeof jQuery.fn.confirm === 'function') {
    console.log("Tái khởi tạo plugin confirm");
  }

  // Ví dụ: khởi tạo lại tree plugin từ app.js nếu cần
  if (typeof jQuery.fn.tree !== 'function' && window.tree) {
    jQuery.fn.tree = window.tree;
    console.log("Đã khôi phục plugin tree");
  }
});
