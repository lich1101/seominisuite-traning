<div class="visible-xs mobile-note">
    <div class="text confirm">
        Mời bạn dùng Laptop hoặc Desktop để sử dụng chức năng này
    </div>
    <!--                                   <span class="close">&times;</span>-->
</div>
<div class="page-guest-post-article-form page-check-content page-article-re-post">
    <?php
//    if($article->re_post==1){
        $cassiopeia_guest_post_article_form = drupal_get_form("cassiopeia_guest_post_article_re_post_form",$article);
        if(!empty($cassiopeia_guest_post_article_form)){
            $cassiopeia_guest_post_article_form = drupal_render($cassiopeia_guest_post_article_form);
            echo $cassiopeia_guest_post_article_form;
//        }
    }
    ?>
</div>
