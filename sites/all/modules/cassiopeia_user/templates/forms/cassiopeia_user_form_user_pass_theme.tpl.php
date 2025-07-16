<div >
    <div class="form-header">
        <div class="form-header-logo">
            <a href="./index.html">
                <img src="/sites/all/themes/cassiopeia_theme/img/logo-1.png" alt="">
            </a>
        </div>
        <div class="form-header-text">
            <span class="color-red">
                Địa chỉ email để lấy lại mật khẩu
            </span>
        </div>
    </div>

    <div class="form-main">
<!--        <div class="form-input-group">-->
<!--            --><?php //echo drupal_render($form['name']); ?>
<!--        </div>-->
<!--        <div class="form-input-group">-->
<!--            --><?php //echo drupal_render($form['pass']); ?>
<!--        </div>-->
        <?php print drupal_render_children($form); ?>
    </div>
</div>