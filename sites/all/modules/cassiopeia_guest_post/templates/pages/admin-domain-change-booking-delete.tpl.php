<?php
$cassiopeia_guest_post_admin_domain_change_booking_delete_form = drupal_get_form("cassiopeia_guest_post_admin_domain_change_booking_delete_form",$booking);
if(!empty($cassiopeia_guest_post_admin_domain_change_booking_delete_form)){
    $cassiopeia_guest_post_admin_domain_change_booking_delete_form = drupal_render($cassiopeia_guest_post_admin_domain_change_booking_delete_form);
    echo $cassiopeia_guest_post_admin_domain_change_booking_delete_form;
}
?>