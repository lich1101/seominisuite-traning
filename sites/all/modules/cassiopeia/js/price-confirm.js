(function($) {
    function getPriceDetail(){
        let priceID = $("input[name='package-price']:checked").val();
        let productID = $("#product").val();
        $.ajax({
            method: "POST",
            url: "/cassiopeia/ajax",
            data: {
                cmd: "getPriceDetail",
                priceID:priceID,
                productID:productID,
            },
            success: function(result) {
                $(".event-order-info-content").html(result.html);
            }
        });
    }
    $(document).ready(function (e) {
        getPriceDetail();
        $("input[name='package-price']").change(function (e) {
            getPriceDetail();
        });
        $(".btn-buy-product-complete").click(function (e) {
            setModalAlert("Chúng tôi đã nhận được thông tin của bạn, chúng tôi sẽ liên hệ trong thời gian sớm nhất!",function (e) {
                location.href = "/";
            });
        })
        $(".btn-buy-product").click(function (e) {
            $(".loading-block").addClass("active");
            let priceID = $("input[name='package-price']:checked").val();
            let product = $("#product").val();
            $.ajax({
                method: "POST",
                url: "/cassiopeia/ajax",
                data: {
                    cmd: "buy-product",
                    priceID:priceID,
                    product:product,
                },
                success: function(result) {
                    location.href = result.redirect;
                }
            });
        });
    })
})(jQuery);