(function($) {

    $(document).ready(function (e) {
        $(".btn-send-booking-mail").click(function (e) {
            let id = $(this).attr("data-id");
            let type = $(this).attr("data-type");
            let href = $(this).attr("href");
            $("input[name='id']").val(id);
            $("select[name='type']").val(type);
            $("button[type='submit']").trigger("mousedown");

            // return false;
        });
    })
})(jQuery);