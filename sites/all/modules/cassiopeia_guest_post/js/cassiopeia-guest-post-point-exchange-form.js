
if (typeof (window.cassiopeia_tagifies) == 'undefined') {
    window.cassiopeia_tagifies = {};
}
(function ($) {
    Drupal.behaviors.cassiopeia_guest_post_article_form = {
        attach: function (context, settings) {
            $('.cassiopeia-guest-post-point-exchange-form', context).once('cassiopeia-guest-post-point-exchange-form',function () {
                var typingTimer;
                var str = "<div class=\"loading-block active\">\n" +
                    "    <div class=\"loading-block-container\">\n" +
                    "        <div class=\"lds-css ng-scope\">\n" +
                    "            <div class=\"lds-spin\" style=\"width:100%;height:100%\"><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div></div>\n" +
                    "        </div>\n" +
                    "    </div>\n" +
                    "</div>";
                $(".select-exchange-update").change(function (e) {
                   let id = $(this).attr("data-exchange-id");
                   $("input[name='exchange-update-id']").val(id);
                   $("select[name='exchange-update-status']").val($(this).val());
                   $("button[name='exchange-update']").trigger("mousedown");

                });
                $.fn.guestPostAlert = function (data) {
                    let _html = "<ul class='pd-0 mg-0'>";
                    jQuery.each(JSON.parse(data), function( index, value ) {
                        _html+="<li>"+value+"</li>";
                    });
                    _html+="</ul>";
                    setModalAlert(_html);
                }
                $("#modalConfirm button.btn-default").click(function (e) {
                  location.reload();
                })
                $.fn.guestPostPointExchangeAdd = function (data) {
                    console.log(123);
                    setModalConfirm(JSON.parse(data),function (e) {
                        $("button[name='exchange-update-confirm']").trigger("mousedown");
                    });
                }
            });
        },
        detach: function(context, settings, trigger) {
            $('.cassiopeia-guest-post-point-exchange-form', context).removeOnce('cassiopeia-guest-post-point-exchange-form', function() {});
        }
    };
})(jQuery);