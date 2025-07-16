<?php
global $user;
$article = $form['#article'];
_print_r(12312);
$query = db_select("cassiopeia_guest_post_tag","cassiopeia_guest_post_tag");
$query->fields("cassiopeia_guest_post_tag");
$query->condition("uid",$user->uid);
$tags = $query->execute()->fetchAll();
$tag_options = array();
if(!empty($tags)){
    foreach($tags as $tag){
        $tag_options[$tag->id] = $tag->title;
    }
}
$cache = !empty($form['#cache'])?$form['#cache']:null;
?>
<?php if($form['#preview']): ?>
    <div class="page-article-preview ">
        <div class="mb-24">
            <?php echo drupal_render($form['return']); ?>
        </div>
        <div class="page-container bg-white padding-24">
            <div class="page-title mb-24">
                <h1><?php echo $form['title']['#value']; ?></h1>
            </div>
            <div class="page-body">
                <?php echo $form['content']['value']['#value']; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="page-header">
        <div class="page-title">
            <h1 title="">
                <span style="max-width: unset;">Đăng guest post</span> sdf
            </h1>
            <!--            <button class="bg-green color-white border-none btn btn-success btn-pre-save" type="button" value="Lưu bài viết" ><span class="icon glyphicon glyphicon-ok" aria-hidden="true"></span>-->
            <!--                Lưu bài viết</button>-->
            <?php echo drupal_render($form['save']); ?>
<!--            --><?php //echo drupal_render($form['preview']); ?>
        </div>
    </div>
    <div class="form-container">
        <div class="form-main">
            <div class="row">
                <div class="page-left col-md-8">sdf
                    <div>
                        <?php echo drupal_render($form['title']); ?>
                    </div>
                    <div>
                        <?php echo $article->content; ?>
                    </div>
<!--                    <div class="post-attributes">-->
<!--                        <ul class="nav nav-tabs">-->
<!--                            <li class="active"><a data-toggle="tab" href="#menu1">Đánh giá bài viết</a></li>-->
<!--                            <li><a data-toggle="tab" href="#menu2">Mạng xã hội</a></li>-->
<!--                        </ul>-->
<!---->
<!--                        <div class="tab-content">-->
<!--                            <div id="menu1" class="tab-pane fade in active">-->
<!--                                <div class="bg-white pd-24">-->
<!--                                    <div>-->
<!--                                        --><?php //echo drupal_render($form['seo_title']); ?>
<!--                                    </div>-->
<!--                                    <div>-->
<!--                                        --><?php //echo drupal_render($form['seo_description']); ?>
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
                </div>
                <div class="page-right col-md-4">
                    <div class="right-block bg-white mb-24">
                        <div class="block-title">
                            <h3 class="title">Chọn website đăng Guest Post</h3>
                        </div>
                        <div class="block-body">
                            <?php echo drupal_render($form['category']); ?>
                            <?php echo drupal_render($form['website']); ?>
                            <?php if(!empty($form['wp_category'])) echo drupal_render($form['wp_category']); ?>
                        </div>
                    </div>
                    <div class="right-block bg-white mb-24">
                        <div class="block-title">
                            <h3 class="title">Ảnh đại diện</h3>
                        </div>
                        <div class="block-body">
                            <div>
                                <?php echo drupal_render($form['image']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="hidden">
    <?php echo drupal_render_children($form); ?>
</div>
<textarea class="hidden" type="hidden" id="tags" value=''><?php echo(json_encode($tag_options)); ?></textarea>
<!-- Modal -->

<span id="listOfID" data-value=""></span>
<input hidden type="number" id="totalItems">
<input hidden type="number" id="checkedItems">