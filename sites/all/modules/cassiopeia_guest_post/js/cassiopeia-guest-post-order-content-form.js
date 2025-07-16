
(function ($) {
    Drupal.behaviors.cassiopeia_qt_search_flight_form = {
        attach: function (context, settings) {
            $('.cassiopeia-order-content-form', context).once('cassiopeia-order-content-form',function () {
                $("#modalContent").addClass("modal-cassiopeia-order-content-form");
            });
            $.fn.guestPostAlert = function (data) {
                let _html = "<ul class='pd-0 mg-0'>";
                jQuery.each(JSON.parse(data), function( index, value ) {
                    _html+="<li>"+value+"</li>";
                });
                _html+="</ul>";
                setModalAlert(_html);
            }
        },
        detach: function(context, settings, trigger) {
            $('.cassiopeia-order-content-form', context).removeOnce('cassiopeia-order-content-form', function() {});
        }
    };
})(jQuery);
