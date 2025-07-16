<?php
function cassiopeia_captcha_user_captcha_resolve_edit_callback($account){
    return _cassiopeia_render_theme("module","cassiopeia_captcha","templates/pages/admin/cassiopeia_captcha_user_captcha_resolve_edit_page.tpl.php",array("account"=>$account));
}
function cassiopeia_captcha_admin_resolve_booking_delete_page($booking){
    return _cassiopeia_render_theme("module","cassiopeia_captcha","templates/pages/admin/cassiopeia_captcha_admin_resolve_booking_delete_page.tpl.php",array("booking"=>$booking));
}
function cassiopeia_captcha_resolve_booking_payment_page($booking){
    return _cassiopeia_render_theme("module","cassiopeia_captcha","templates/pages/cassiopeia_captcha_resolve_booking_payment_page.tpl.php",array("booking"=>$booking));
}
function cassiopeia_captcha_resolve_booking_page(){
    return _cassiopeia_render_theme("module","cassiopeia_captcha","templates/pages/cassiopeia_captcha_resolve_booking_page.tpl.php");
}
function cassiopeia_captcha_resolve_booking_price_page(){
    return _cassiopeia_render_theme("module","cassiopeia_captcha","templates/pages/admin/cassiopeia_captcha_resolve_booking_price_page.tpl.php");
}
function cassiopeia_captcha_resolve_booking_price_edit_page($booking_price){
    return _cassiopeia_render_theme("module","cassiopeia_captcha","templates/pages/admin/cassiopeia_captcha_resolve_booking_price_edit_page.tpl.php",array("price"=>$booking_price));
}
function cassiopeia_captcha_resolve_booking_price_delete_page($booking_price){
    return _cassiopeia_render_theme("module","cassiopeia_captcha","templates/pages/admin/cassiopeia_captcha_resolve_booking_price_delete_page.tpl.php",array("price"=>$booking_price));
}
function cassiopeia_captcha_resolve_booking_price_add_page(){
    return _cassiopeia_render_theme("module","cassiopeia_captcha","templates/pages/admin/cassiopeia_captcha_resolve_booking_price_add_page.tpl.php");
}
function cassiopeia_captcha_admin_resolve_booking_page(){
    return _cassiopeia_render_theme("module","cassiopeia_captcha","templates/pages/admin/cassiopeia_captcha_admin_resolve_booking_page.tpl.php");
}
