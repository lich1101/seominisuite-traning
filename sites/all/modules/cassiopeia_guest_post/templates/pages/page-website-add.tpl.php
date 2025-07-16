<div class="hidden-xs">
    <?php
    $cassiopeia_guest_post_website_form = drupal_get_form("cassiopeia_guest_post_website_form");
    if(!empty($cassiopeia_guest_post_website_form)){
        $cassiopeia_guest_post_website_form = drupal_render($cassiopeia_guest_post_website_form);
        echo $cassiopeia_guest_post_website_form;
    }
    ?>
</div>