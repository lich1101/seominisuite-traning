
<div class="domain-change-booking">
    <?php
    $cassiopeia_captcha_resolve_booking_form = drupal_get_form("cassiopeia_captcha_resolve_booking_form");
    if(!empty($cassiopeia_captcha_resolve_booking_form)){
        $cassiopeia_captcha_resolve_booking_form = drupal_render($cassiopeia_captcha_resolve_booking_form);
        echo $cassiopeia_captcha_resolve_booking_form;
    }
    ?>
</div>
