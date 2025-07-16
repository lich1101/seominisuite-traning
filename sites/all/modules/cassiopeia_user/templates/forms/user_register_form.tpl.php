<div class="block-register">
    <div class="form-header">
        <div class="form-header-logo">
            <a href="/">
                <img src="/sites/all/themes/cassiopeia_theme/img/logo-1.png" alt="">
            </a>
        </div>
        <div class="form-header-text">
            Chào mừng bạn đến với SEO MiniSuite
        </div>
    </div>

    <div class="form-main">
        <div class="form-input-group">
            <?php echo drupal_render($form['field_full_name']); ?>
        </div>

        <div class="form-input-group">
            <?php echo drupal_render($form['account']['mail']); ?>
        </div>

        <div class="form-input-group">
            <?php echo drupal_render($form['field_company']); ?>
        </div>

        <div class="form-input-group">
            <?php echo drupal_render($form['account']['name']); ?>
        </div>

        <div class="form-input-group">
            <?php echo drupal_render($form['account']['pass']); ?>
        </div>
        <div class="form-input-group">
            <?php  echo drupal_render($form['captcha']); ?>
        </div>

        <div class="form-input-group form-buttons">
            <?php echo drupal_render($form['actions']['submit']); ?>
        </div>

        <div class="form-input-group">
            <div class="login-link">
                <span>Bạn đã có tài khoản ?</span>
                <a href="/user/login">Đăng nhập.</a>
            </div>
        </div>
    </div>
</div>
<?php print drupal_render_children($form); ?>