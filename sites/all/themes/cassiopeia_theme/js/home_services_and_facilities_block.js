/**
 * Created by VDP on 17/05/2017.
 */
/**
 * Created by VDP on 05/05/2017.
 */
(function ($) {
    $(document).ready(function () {

        $('#home-services-and-facilities-block .home-services-and-facilities-block-content-inner').owlCarousel({
            items: 3,
            autoplay: true,
            nav: false,
            dots: true,
            margin:0,
            animateOut: 'fadeOut',
            smartSpeed: 450,
            loop: true
        });
    })
})(jQuery);