<div class="m-24 d-flex justify-center mb-24">
    Bạn tạo file danh sách website theo mẫu này&nbsp;<a href="/guest-post/domain/change/sample-file" class="color-green"><b>Tải mẫu</b></a>
</div>
<div class="error-message warning">

</div>
<div class="file-zone">
<!--    <div class="image">-->
<!--        <div class="form-group">-->
<!--            <div class="fake-image d-flex justify-center">-->
<!--                <div class="text-center">-->
<!--                    --><?php
//                    $image_url = "/sites/all/themes/cassiopeia_theme/img/icons/excel.png";
//                    if(!empty($form['#file'])){
//                        $image_url = file_create_url($form['#file']->uri);
//                    }?>
<!--                    <img id="imgPreview" src="--><?php //echo $image_url; ?><!--" alt="">-->
<!--                    <div class="img-name">-->
<!--                        --><?php //if(!empty($form['#file'])): ?>
<!--                            --><?php //echo $form['#file']->filename; ?>
<!--                        --><?php //else: ?>
<!--                            <div>Chọn ảnh từ máy tính</div>-->
<!--                            <div>hoặc kéo và thả</div>-->
<!--                        --><?php //endif; ?>
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--    <div class="d-flex justify-center">-->
<!--    </div>-->
    <?php echo drupal_render($form['excel_file']); ?>
    <?php echo drupal_render($form['submit']); ?>
</div>
<div class="hidden">
    <?php echo drupal_render_children($form); ?>
</div>