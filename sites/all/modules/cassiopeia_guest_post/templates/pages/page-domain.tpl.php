<div class="visible-xs mobile-note mb-24">
    <div class="text confirm">
        Mời bạn dùng Laptop hoặc Desktop để sử dụng chức năng này
    </div>
</div>
<?php
drupal_add_js("https://code.jquery.com/ui/1.13.0/jquery-ui.js");
drupal_add_css("https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css");
ctools_include('modal');
ctools_modal_add_js();
global $user;
?>
<div class="page page-order-content page-domain hidden-xs">
    <?php
    $cassiopeia_guest_post_domain_search_form = drupal_get_form("cassiopeia_guest_post_domain_search_form");
    if(!empty($cassiopeia_guest_post_domain_search_form)){
        $cassiopeia_guest_post_domain_search_form = drupal_render($cassiopeia_guest_post_domain_search_form);
        echo $cassiopeia_guest_post_domain_search_form;
    }
    ?>
</div>
