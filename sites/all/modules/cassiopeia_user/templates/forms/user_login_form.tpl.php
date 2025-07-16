<?php
$messages = drupal_get_messages();
?>
<div >
    <div class="form-header">
        <div class="form-header-logo">
            <a href="./index.html">
                <img src="/sites/all/themes/cassiopeia_theme/img/logo-1.png" alt="">
            </a>
        </div>
        <div class="form-header-text">
            Chào mừng bạn đến với SEO MiniSuite
        </div>
        <?php if ($messages): ?>
            <div id="console" class="clearfix"><?php print $messages; ?></div>
        <?php endif; ?>
    </div>

    <div class="form-main">
        <div class="form-input-group">
            <label for="">Email</label>
            <?php echo drupal_render($form['name']); ?>
        </div>
        <div class="form-input-group">
            <label for="">&nbsp;</label>
            <?php echo drupal_render($form['pass']); ?>
        </div>
        <div class="mt-24 text-right mb-24">
            <a href="/user/password" class="c-blue">Quên mật khẩu ?</a>
        </div>
        <?php print drupal_render_children($form); ?>
        <div class="form-input-group">
            <a class="btn-user-register" href="/user/register">Đăng ký miễn phí</a>
        </div>
    </div>
</div>