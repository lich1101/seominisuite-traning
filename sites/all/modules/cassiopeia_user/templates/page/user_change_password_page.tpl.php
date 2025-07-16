<?php
/**
 * Created by PhpStorm.
 * User: VDP
 * Date: 12/05/2017
 * Time: 9:18 AM
 */

global $user;
$account  = $variables['account'];
if (empty($user->uid) || $user->uid != $account->uid) {
    drupal_goto('/');
}
$_user_change_password_form = drupal_get_form('cassiopeia_user_user_change_password_form', $account);
?>

<div class="row">
    <div class="col-md-8">
        <div class="">
            <div class="page-header">
                <div class="page-title">
                    <h1>Đổi mật khẩu</h1>
                </div>
            </div>
            <div class="changpass-user">
                <?php
                    if(!empty($_user_change_password_form)){
                        $_user_change_password_form = drupal_render($_user_change_password_form);
                        print ($_user_change_password_form);
                    }
                ?>
            </div>
        </div>
    </div>
</div>



