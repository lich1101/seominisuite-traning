
(function ($) {
    Drupal.behaviors.cassiopeia_qt_search_flight_form = {
        attach: function (context, settings) {
            $('.cassiopeia-order-content-search-form', context).once('cassiopeia-order-content-search-form',function () {
                let str = "<div class=\"loading-block active\">\n" +
                    "    <div class=\"loading-block-container\">\n" +
                    "        <div class=\"lds-css ng-scope\">\n" +
                    "            <div class=\"lds-spin\" style=\"width:100%;height:100%\"><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div></div>\n" +
                    "        </div>\n" +
                    "    </div>\n" +
                    "</div>";
                if($(".btn-ajax").length){
                    $(".btn-ajax").each(function (e) {
                        let _this = $(this);
                        Drupal.ajax[_this.attr("id")].options.beforeSubmit = function(){
                            $(".cassiopeia-order-content-search-form").append(str);
                        };
                    });
                }
                $(document).ready(function () {

                    $(".btn-clear-text").click(function (e) {
                        $(this).parent().find("input").val("");
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
                    $("input[type=radio]").each(function (e) {
                        let _this = $(this);
                        if(_this.is(":checked")){
                            $(".form-item-task").has(_this).addClass("active");
                        };
                    });
                })
            });
        },
        detach: function(context, settings, trigger) {
            $('.cassiopeia-order-content-search-form', context).removeOnce('cassiopeia-order-content-search-form', function() {});
        }
    };
})(jQuery);
