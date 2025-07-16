<div class="hidden-xs">
    <?php
    $cassiopeia_guest_post_complain_form = drupal_get_form("cassiopeia_guest_post_complain_search_form");
    if(!empty($cassiopeia_guest_post_complain_form)){
        $cassiopeia_guest_post_complain_form = drupal_render($cassiopeia_guest_post_complain_form);
        echo $cassiopeia_guest_post_complain_form;
    }
    ?>
</div>