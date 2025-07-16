
if (typeof (window.cassiopeia_tagifies) == 'undefined') {
    window.cassiopeia_tagifies = {};
}
(function ($) {
    Drupal.behaviors.cassiopeia_guest_post_article_search_form = {
        attach: function (context, settings) {
            $('.cassiopeia-guest-post-article-search-form', context).once('cassiopeia-guest-post-article-search-form',function () {
                $.fn.guestPostExportEmpty = function (data) {
                    setModalAlert("Bạn chưa chọn website!");
                }
                $.fn.guestPostAlert = function (data) {
                    console.log(data);
                    let _html = "<ul class='pd-0 mg-0'>";
                    jQuery.each(JSON.parse(data), function( index, value ) {
                        _html+="<li>"+value+"</li>";
                    });
                    _html+="</ul>";
                    setModalAlert(_html);
                }
                $("document").ready(function () {
                    $(".btn-clear-text").click(function (e) {
                        $(this).parent().find("input").val("");
                    });
                    $("input[name='author']").each(function (e) {
                        let _this = $(this);
                        $(".cassiopeia-guest-post-article-search-form .author").removeClass("active");
                        if(_this.is(":checked")){
                            _this.parent().addClass("active");
                        }
                    });
                    $(".selectAll").change(function (e) {
                        let _this = $(this);
                        if(_this.is(":checked")){
                            console.log(1);
                            $("input.input-checkbox").prop("checked",true);
                        }else{
                            console.log(2);
                            $("input.input-checkbox").prop("checked",false);
                        }
                    });
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
                    $("select[name='sort_direction']").val(data_direction);
                    $("select[name='sort_direction']").trigger("change");
                    $("select[name='sort_by']").val(data_sort);
                    $("select[name='sort_by']").trigger("change");
                });
                // $("input[name='author']").change(function (e) {
                //    let _this = $(this);
                //    $(".cassiopeia-guest-post-article-search-form .author").removeClass("active");
                //    if(_this.is(":checked")){
                //        _this.parent().addClass("active");
                //    }
                // });
            });
        },
        detach: function(context, settings, trigger) {
            $('.cassiopeia-guest-post-article-search-form', context).removeOnce('cassiopeia-guest-post-article-search-form', function() {});
        }
    };
})(jQuery);