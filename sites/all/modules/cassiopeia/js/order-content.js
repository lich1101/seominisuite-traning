
var footer = jQuery("footer");
(function($) {
    $("document").ready(function(e) {
        $(".btn-add-order-content").click(function (e) {
            $("#modalOrderContent").css({ "display": "flex", "align-items": "center" });
            $("#modalOrderContent").modal("show");
        });
    });
})(jQuery);