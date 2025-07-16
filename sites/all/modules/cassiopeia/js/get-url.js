var footer = jQuery("footer");
(function($) {
    $("document").ready(function(e) {
        $("#form-get-url button").click(function (e) {
            console.log("$(\"#footer\").hasClass(\"seoToolExtension\")",$("#footer").hasClass("seoToolExtension"));
            if ($("#footer").hasClass("seoToolExtension") !== true) {
                installExtension();
                return false;
            }

            // Reset session khi bắt đầu tìm kiếm mới
            $.ajax({
                method: "POST",
                url: "/cassiopeia/ajax",
                data: {
                    action: "reset-get-url-session"
                },
                success: function(result) {
                    console.log("Session đã được reset");
                }
            });

            // Lưu danh sách loại trừ vào session để extension có thể truy cập
            var excludeUrls = $("textarea[name='exclude-urls']").val();
            var keywords = $("textarea[name='keywords']").val();
            var key = keywords ? keywords.trim().split("\n")[0] : '';
            if (excludeUrls && excludeUrls.trim() !== '' && key !== '') {
                $.ajax({
                    method: "POST",
                    url: "/cassiopeia/ajax",
                    data: {
                        action: "save-exclude-urls",
                        exclude_urls: excludeUrls,
                        key: key
                    },
                    success: function(result) {
                        console.log("Đã lưu danh sách loại trừ cho key: " + key);
                    }
                });
            }

          let captcha_resolve = $("select[name='captcha-resolve']").val();
          if(captcha_resolve==="auto"){ // giải captcha tự động
            jQuery.ajax({
              method: "POST",
              url: "/cassiopeia-captcha/resolve/get-info",
              success: function (result) {
                if(result.remaining<1 ){
                  let a = $.confirm({
                    title: 'Hết lượt giải captcha tự động!',
                    content: 'Bạn đã hết lượt giải captcha tự động!',
                    columnClass: 'captcha_resolve_limited col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1',
                    buttons: {
                      formSubmit: {
                        text: '<span class="icon glyphicon glyphicon-plus" aria-hidden="true"></span> Mua thêm',
                        btnClass: 'btn-success',
                        action: function () {
                          // document.dispatchEvent(new CustomEvent('CheckKeyword', {detail: '123'}))
                          // location.href = "/captcha/resolve/booking";
                          window.open(
                            'https://seominisuite.com//captcha/resolve/booking',
                            '_blank' // <- This is what makes it open in a new window.
                          );
                        }
                      },
                      continue: {
                        text: 'Kiểm tra thủ công',
                        btnClass: 'btn-default',
                        action: function () {
                          $("select[name='captcha-resolve']").val("manual");
                          document.dispatchEvent(new CustomEvent('GetUrl', { detail: '123' }));
                        }
                      },
                      cancel: {
                        text: '×',
                        btnClass: 'btn-default btn-close',
                        action: function () {

                        }
                      },
                    }
                  });
                }else{
                  document.dispatchEvent(new CustomEvent('GetUrl', { detail: '123' }));
                }
              }
            });
          }else{
            document.dispatchEvent(new CustomEvent('GetUrl', { detail: '123' }));
          }
        });
        // input check all
        $(".selectAll").change(function(e) {
            if ($(this).is(":checked")) {
                $("tbody input").prop("checked", true);
                // $("tbody tr").addClass("tr-checked");
            } else {
                $("tbody input").prop("checked", false);
                // $("tbody tr").removeClass("tr-checked");
            }
        });
    });
})(jQuery);
