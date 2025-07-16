<?php global $user;?>
<div class="page-complain-add mb-24 page-guest-post-article-form hidden-xs">
    <div class="page-title mb-24"><h1>Gửi khiếu nại mới</h1></div>
    <div class="row">
        <div class="col-md-8">
            <?php
            $cassiopeia_guest_post_complain_form = drupal_get_form("cassiopeia_guest_post_complain_form");
            if(!empty($cassiopeia_guest_post_complain_form)){
                $cassiopeia_guest_post_complain_form = drupal_render($cassiopeia_guest_post_complain_form);
                echo $cassiopeia_guest_post_complain_form;
            }
            ?>
        </div>
        <div class="col-md-4 block-right">
            <?php $complains = cassiopeia_guest_post_complain_load_by_uid($user->uid,6); ?>
            <div class="bg-white padding-24">
                <div class="page-title"><h1>Khiếu nại gần đây của bạn</h1></div>
                <div class="items">
                    <?php if(!empty($complains)): ?>
                        <?php foreach($complains as $complain): ?>
                            <div class="item">
                                <div class="mb-10"><b>#<?php echo $complain->code; ?> - <?php echo $complain->title; ?></b></div>
                                <div class="mb-10">
                                 <span class="complain-status complain-status-<?php echo $complain->status; ?>">
                                    <?php
                                    switch ($complain->status){
                                        case 0 : echo "Admin đang kiểm tra"; break;
                                        case 1 : echo "Thành công"; break;
                                        case 2 : echo "Hủy"; break;
                                        case 3 : echo "Thất bại"; break;
                                    }
                                    ?>
                                </span>
                                </div>
                                <div class="mb-10"><?php echo _cassiopeia_convert_time_ago(REQUEST_TIME-$complain->created); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>