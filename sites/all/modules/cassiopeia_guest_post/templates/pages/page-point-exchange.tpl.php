<div class="hidden-xs">
    <?php
    $cassiopeia_guest_post_point_exchange_form = drupal_get_form("cassiopeia_guest_post_point_exchange_form");
    if(!empty($cassiopeia_guest_post_point_exchange_form)){
        $cassiopeia_guest_post_point_exchange_form = drupal_render($cassiopeia_guest_post_point_exchange_form);
        echo $cassiopeia_guest_post_point_exchange_form;
    }
    ?>
</div>