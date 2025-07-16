
if (typeof (window.cassiopeia_tagifies) == 'undefined') {
    window.cassiopeia_tagifies = {};
}
(function ($) {
    Drupal.behaviors.cassiopeia_guest_post_website_search_form = {
        attach: function (context, settings) {
            $('.cassiopeia-guest-post-domain-search-form', context).once('cassiopeia-guest-post-domain-search-form',function () {
                $(".btn-clear-text").click(function (e) {
                    $(this).parent().find("input").val("");
                });
                $(".cassiopeia-guest-post-domain-search-form .page-notify .close").click(function (e) {
                   $(this).parent().remove();
                });
                $(".sort").click(function (e) {
                    let _this = $(this);
                    let data_sort = _this.attr("data-sort");
                    let data_direction = _this.attr("data-direction");
                    if(data_direction=="ASC"){
                        data_direction = "DESC";
                    }else{
                        data_direction = "ASC";
                    }
                    console.log("data_direction",data_direction);
                    $("select[name='sort_direction']").val(data_direction);
                    $("select[name='sort_direction']").trigger("change");
                    $("select[name='sort_by']").val(data_sort);
                    $("select[name='sort_by']").trigger("change");
                });
            });

        },
        detach: function(context, settings, trigger) {
            $('.cassiopeia-guest-post-domain-search-form', context).removeOnce('cassiopeia-guest-post-domain-search-form', function() {});
        }
    };
})(jQuery);