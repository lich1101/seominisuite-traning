<?php
$cassiopeia_captcha_resolve_booking_delete_form = drupal_get_form("cassiopeia_captcha_resolve_booking_delete_form",$booking);
if(!empty($cassiopeia_captcha_resolve_booking_delete_form)){
    $cassiopeia_captcha_resolve_booking_delete_form = drupal_render($cassiopeia_captcha_resolve_booking_delete_form);
    echo $cassiopeia_captcha_resolve_booking_delete_form;
}
?>