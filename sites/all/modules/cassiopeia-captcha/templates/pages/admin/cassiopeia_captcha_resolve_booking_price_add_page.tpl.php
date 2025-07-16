<?php
$cassiopeia_captcha_resolve_booking_price_form = drupal_get_form("cassiopeia_captcha_resolve_booking_price_form");
if(!empty($cassiopeia_captcha_resolve_booking_price_form)){
    $cassiopeia_captcha_resolve_booking_price_form = drupal_render($cassiopeia_captcha_resolve_booking_price_form);
    echo $cassiopeia_captcha_resolve_booking_price_form;
}
?>