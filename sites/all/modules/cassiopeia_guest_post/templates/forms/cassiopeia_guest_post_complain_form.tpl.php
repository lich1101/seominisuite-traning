<div class="box bg-white mb-24 pd-24">
    <div class="box-title">Thông tin khiếu nại</div>
    <div class="row">
<!--        <div class="col-md-6">--><?php //echo drupal_render($form['user']); ?><!--</div>-->
        <div class="col-md-6"><?php echo drupal_render($form['target']); ?></div>
        <div class="col-md-6"><?php echo drupal_render($form['article']); ?></div>
<!--        <div class="col-md-6">--><?php //echo drupal_render($form['article']); ?><!--</div>-->
    </div>
</div>
<div class="box bg-white mb-24 pd-24">
    <div class="box-title">Nội dung khiếu nại</div>
    <div><?php echo drupal_render($form['title']); ?></div>
    <div><?php echo drupal_render($form['content']); ?></div>
</div>
<div class="mb-24">
    <?php echo drupal_render($form['re-captcha']); ?>
</div>
<div>
    <?php echo drupal_render($form['submit']); ?>
</div>
<div class="hidden">
    <?php echo drupal_render_children($form); ?>
</div>