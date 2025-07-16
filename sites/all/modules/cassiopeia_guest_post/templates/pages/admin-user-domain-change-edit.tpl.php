<!--<div>-->
<!--    <h3 class="mg-0">Thay đổi lượt đổi domain cho tài khoản: --><?php //echo $account->name; ?><!-- - --><?php //echo $account->field_full_name['und'][0]['value']; ?><!--</h3>-->
<!--</div>-->
<?php
drupal_set_title("Thay đổi lượt đổi domain cho tài khoản:".$account->name." - ".$account->field_full_name['und'][0]['value']);
$cassiopeia_guest_post_website_category_form = drupal_get_form("cassiopeia_guest_post_user_domain_change_edit_form",array("account"=>$account));
if(!empty($cassiopeia_guest_post_website_category_form)){
    $cassiopeia_guest_post_website_category_form = drupal_render($cassiopeia_guest_post_website_category_form);
    echo $cassiopeia_guest_post_website_category_form;
}
?>