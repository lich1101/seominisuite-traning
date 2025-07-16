<div class="form-left">
    <div class="form-group">
        <label for="">Từ ngày</label>
        <?php echo drupal_render($form['from_date']); ?>
    </div>
    <div class="form-group">
        <label for="">Đến ngày</label>
        <?php echo drupal_render($form['to_date']); ?>
    </div>
    <div class="form-group">
        <label for="">Tag</label>
        <?php echo drupal_render($form['tag']); ?>
    </div>
<!--    <div class="form-group">-->
<!--        --><?php //echo drupal_render($form['rank']); ?>
<!--    </div>-->
    <div class="">
        <?php echo drupal_render($form['submit']); ?>
    </div>
</div>
<div class="form-right">
    <div class="form-group">
        <?php echo drupal_render($form['title']); ?>
        <?php echo drupal_render($form['search']); ?>
    </div>
</div>
<div class="hidden" style="display: none;">
    <?php echo drupal_render_children($form); ?>
</div>