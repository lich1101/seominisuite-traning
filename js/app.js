(function($) {
    $(document).ready(function() {
        var imgComment = new Swiper(".img-comment .Comment-slider", {
            slidesPerView: 4,
            spaceBetween: 30,
            speed: 1200,
            autoplay: {
                delay: 3000
            },
            loop: true,
            navigation: {
                nextEl: ".img-comment .swiper-button-next",
                prevEl: ".img-comment .swiper-button-prev",
            },
            pagination: {
                el: ".img-comment .swiper-pagination",
                clickable: true,
            },
            breakpoints: {
                // when window width is >= 320px
                320: {
                    slidesPerView: 1,
                    spaceBetween: 20
                },
                // when window width is >= 480px
                480: {
                    slidesPerView: 2,
                    spaceBetween: 30
                },
                // when window width is >= 640px
                640: {
                    slidesPerView: 2,
                    spaceBetween: 40
                },
                991: {
                    slidesPerView: 3,
                    spaceBetween: 40
                },
                1024: {
                    slidesPerView: 4,
                    spaceBetween: 40
                }
            }
        });
        AOS.init({
            disable: false,
            animatedClassName: 'aos-animate',
            offset: 120, // offset (in px) from the original trigger point
            delay: 0, // values from 0 to 3000, with step 50ms
            duration: 1200, // values from 0 to 3000, with step 50ms
            easing: 'ease-in-out', // default easing for AOS animations
            once: true, // whether animation should happen only once - while scrolling down
        });
        $(window).on('scroll', function() {
            $('.back-to-top').toggleClass('active', $(window).scrollTop() > 150);
        });
        $('.back-to-top').on('click', function() {
            $("html, body").animate({ scrollTop: 0 }, 1000);
        });
        $("#BacklinkVideo").on("hidden.bs.modal", function() {
            $('#BacklinkVideo iframe').attr('src', $('#BacklinkVideo iframe').attr('src'))
        });

        $("#KeywordVideo").on("hidden.bs.modal", function() {
            $('#KeywordVideo iframe').attr('src', $('#KeywordVideo iframe').attr('src'))
        });

        $("#duplicateVideo").on("hidden.bs.modal", function() {
            $('#duplicateVideo iframe').attr('src', $('#duplicateVideo iframe').attr('src'))
        });

        $("#VideoModal").on("hidden.bs.modal", function() {
            $('#VideoModal iframe').attr('src', $('#VideoModal iframe').attr('src'))
        });

    });
})(jQuery)