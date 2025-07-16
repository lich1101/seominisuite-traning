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
    });
})(jQuery)