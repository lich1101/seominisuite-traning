
<div class="domain-change-booking hidden-xs">
    <?php
    $cassiopeia_guest_post_domain_change_booking_form = drupal_get_form("cassiopeia_guest_post_domain_change_booking_form");
    if(!empty($cassiopeia_guest_post_domain_change_booking_form)){
        $cassiopeia_guest_post_domain_change_booking_form = drupal_render($cassiopeia_guest_post_domain_change_booking_form);
        echo $cassiopeia_guest_post_domain_change_booking_form;
    }
    ?>
</div>