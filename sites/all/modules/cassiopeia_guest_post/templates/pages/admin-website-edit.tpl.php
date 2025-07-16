<?php
$cassiopeia_guest_post_website_category_form = drupal_get_form("cassiopeia_guest_post_website_form",array("website"=>$website));
if(!empty($cassiopeia_guest_post_website_category_form)){
    $cassiopeia_guest_post_website_category_form = drupal_render($cassiopeia_guest_post_website_category_form);
    echo $cassiopeia_guest_post_website_category_form;
}
?>