<?php
global $user;
ctools_include('modal');
ctools_modal_add_js();
$article = !empty($variables['article'])?$variables['article']:null;
?>
<div class="visible-xs mobile-note">
    <div class="text confirm">
        Mời bạn dùng Laptop hoặc Desktop để sử dụng chức năng này
    </div>
    <!--                                   <span class="close">&times;</span>-->
</div>
<div class="page-guest-post-article-form page-check-content">
    <?php
        $cassiopeia_guest_post_article_form = drupal_get_form("cassiopeia_guest_post_article_form",array("article"=>$article));
        if(!empty($cassiopeia_guest_post_article_form)){
            $cassiopeia_guest_post_article_form = drupal_render($cassiopeia_guest_post_article_form);
            echo $cassiopeia_guest_post_article_form;
        }
    ?>
</div>
<div class="hidden">
    <?php print(l('<i class="fa fa-edit"></i>', 'guest-post/article/pre-save/nojs',  array('html'=>true, 'attributes' => array('class' => 'ctools-use-modal btn btn-icon-before no-padding btn-pre-save-form'))));?>
</div>
<div class="program-running">

</div>
<?php
db_delete("cassiopeia_content_check")->condition("uid",$user->uid)->condition("nid",0)->execute();
?>