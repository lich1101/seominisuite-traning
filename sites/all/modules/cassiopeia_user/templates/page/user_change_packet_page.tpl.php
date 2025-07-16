<?php

?>
<div class="page-user-change-packet">
    <div class="block-href">
        <a href="/admin/manager/user" class="btn btn-primary">Quay lại</a>
    </div>
    <div class="block-title">
        <div>
            Tài khoản: <b><?php echo $account->mail; ?></b>
        </div>
        <div>
            Gói đang sử dụng: <b><?php if(!empty($packet)) echo $packet->product_name; ?></b>
        </div>
    </div>
    <div class="form">
        <?php
            $cassiopeia_user_change_packet_form = drupal_get_form("cassiopeia_user_change_packet_form",$account);
            if(!empty($cassiopeia_user_change_packet_form)){
                $cassiopeia_user_change_packet_form = drupal_render($cassiopeia_user_change_packet_form);
                echo $cassiopeia_user_change_packet_form;
            }
        ?>
    </div>
</div>
