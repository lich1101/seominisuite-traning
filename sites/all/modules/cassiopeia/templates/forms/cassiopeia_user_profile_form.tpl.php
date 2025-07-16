<?php
global $user;
$image_url = image_style_url("style_144x144","public://avatar-default.png");
?>
<div class="row">
<!--    <div class="col-md-4">-->
<!--        <div class="bg-white pd-15 br-5 bsd-black">-->
<!--            <div class="page-header">-->
<!--                <div class="page-title tutorial-title">-->
<!--                    <h1>Đổi mật khẩu</h1>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="form-input-group user-input-form input-password">-->
<!--                --><?php
//                echo drupal_render($form['account']['pass']);
//                ?>
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
    <div class="col-md-8">
        <div class="br-5">
            <div class="page-header">
                <div class="page-title">
                    <h1>Thông tin cá nhân</h1>
                </div>
            </div>
            <div class="bg-white profile-user">
                <div class="form-input-group user-input-form">
                    <?php
                    if(empty($form['picture']['picture']['#value'])){
                        ?>
                        <div class="default-avatar">
                            <div class="user-picture">
                                <img src="<?php echo $image_url; ?>" alt="">
                                <label for="edit-picture-upload"><i class="fa fa-pencil"></i></label>
                            </div>
                        </div>
                        <div class="hidden"><?php echo(drupal_render($form['picture'])); ?></div>
                        <?php
                    }else{
                        echo(drupal_render($form['picture'])); 
                    }
                    ?>
                </div>
                <div class="form-input-group user-input-form">
                    <?php echo(drupal_render($form['field_full_name'])); ?>
                </div>
                <div class="form-input-group user-input-form">
                    <?php echo drupal_render($form['field_tel']); ?>
                </div>
                <div class="form-input-group user-input-form">
                    <div id="field-email-add-more-wrapper">
                        <div class="email">
                            <label for="">Email</label>
                            <div>
                                <span>
                                    <?php echo($user->mail); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
        
                <div class="form-input-group user-input-form user-input-actions">
                    <?php echo drupal_render($form['actions']['submit']); ?>
                </div>
                <div style="width: 0px; height: 0px; overflow: hidden; opacity: 0;">
                    <?php print drupal_render_children($form); ?>
                </div>
            </div>
        </div>
    </div>
</div>

