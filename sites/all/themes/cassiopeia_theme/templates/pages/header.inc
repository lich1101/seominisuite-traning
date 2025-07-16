<?php
/**
 * Created by PhpStorm.
 * User: VDP
 * Date: 18/04/2017
 * Time: 10:01 AM
 */

global $user;
global $base_url;
global $packet ;
$packet = cassiopeia_get_available_packet_by_uid($user->uid);
//_print_r($packet);
if (!empty($user->uid)) {
  $_user = user_load($user->uid);
}

$cassiopeia_site_name = variable_get('site_name');
$cassiopeia_hotline = variable_get('hotline');
?>

<header class="header" data-uid="<?php echo !empty($user->uid)?$user->uid:""; ?>" >
    <div class="header-container">
        <div class="header-inner">
            <div class="header-logo">
                <div class="header-logo-content">
                    <h1>
                        <a href="/">
                            <img src="/sites/all/themes/cassiopeia_theme/img/header-logo.png" class="img-responsive" alt="">
                        </a>
                    </h1>
                </div>
            </div>

            <div class="header-main">
                <div class="toggle-menu d-sm">
                    <span></span>
                </div>
    
                <div class="header-action">
                </div>
    
                <div class="header-user">
                    <div class="header-user-content">
                        <div class="captcha-resolve hidden">
                          <label class="control-label on-off-switch" for="edit-13-check"><input class="table-checkbox form-checkbox" type="checkbox" id="edit-13-check" name="13[check]" value="1"><span class="on-off-slider round"></span><span class="title">Giải captcha<br> tự động</span></label>
                        </div>
<!--                        --><?php //if(!empty($user->uid) && $user->uid==1 && !empty($packet)): ?>
                            <?php $available_point = cassiopeia_guest_post_user_available_point_load($user->uid);
                            $user_captcha_resolve = cassiopeia_captcha_resolve_load($user->uid);
                            ?>
                            <div class="user-point hidden-xs">
                                <div class="point <?php if($available_point->point_status==0) echo "line-through"; ?>">
                                    <span>Điểm tích lũy: </span>
                                    <b><?php echo $available_point->point; ?></b>
                                </div>
                                <div class="point <?php if($available_point->day_point_status==0) echo "line-through"; ?>">
                                    <span>Điểm ngày: </span>
                                    <b><?php echo $available_point->day_point; ?></b>
                                </div>
                              <div class="point <?php if($available_point->day_point_status==0) echo "line-through"; ?>">
                                    <span>Lượt giải captcha: </span>
                                    <b><?php echo !empty($user_captcha_resolve)?$user_captcha_resolve->remaining:0; ?></b>
                                </div>
                            </div>
<!--                        --><?php //endif; ?>
                        <?php if(!empty($user->uid) && !empty($packet)): ?>
                            <?php if($packet->product==BASIC): ?>
                                <div class="user-current-package">
                                    <div class="package-name">
                                        <span><?php if(!empty($packet)) echo $packet->product_name; ?> </span>
                                        <span>Miễn phí</span>
                                    </div>
                                    <div class="package-content">
                                        <a href="/price-board" class="btn btn-package-up">
                                            Nâng Cấp
                                        </a>
                                    </div>
                                </div>
                            <?php elseif($packet->product==AGENCY): ?>
                                <div class="user-current-package">
                                    <div class="package-name">
                                        <span><?php if(!empty($packet)) echo $packet->product_name; ?> </span>
                                        <span>Trọn đời</span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="user-current-package">
                                    <div class="package-name">
                                        <span>Gói Advanced </span>
                                        <span>
                                            <?php
                                                $date1=date_create(date("Y-m-d",REQUEST_TIME));
                                                $date2=date_create( date("Y-m-d",$packet->expired));
                                                $diff=date_diff($date1,$date2);
                                                $days = $diff->format("%a");
                                            ?>
                                            <?php
                                                $time_remaining = $packet->expired-REQUEST_TIME;
                                                if((($packet->expired-REQUEST_TIME)/86400)>1){
                                                    echo "Còn ".floor(($packet->expired-REQUEST_TIME)/86400)." ngày";
                                                }elseif($packet->expired<REQUEST_TIME) {
                                                    echo "Hết hạn!";
                                                }
                                                else{
                                                    $hours = floor($time_remaining/3600);
                                                    echo "Còn ".$hours." giờ";
                                                }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="package-content">
                                        <a href="/price-board" class="btn btn-package-up">
                                            Nâng Cấp
                                        </a>
                                    </div>
                                    <!-- <div class="package-content">
                                        <a href="/price-board" class="btn btn-package-up ">
                                            
                                            <?php if($packet->expired<REQUEST_TIME): ?>
                                                <div class="out-of-date">
                                                    <i class="fa fa-clock-o"></i>
                                                    Hết hạn!
                                                </div>
                                            <?php else: ?>
                                                <?php if($days<=30): ?>
                                                    <div class="remaining">
                                                        <i class="fa fa-clock-o"></i>
                                                        <?php
                                                        $time_remaining = $packet->expired-REQUEST_TIME;
                                                        if((($packet->expired-REQUEST_TIME)/86400)>1){
                                                            echo "Còn ".floor(($packet->expired-REQUEST_TIME)/86400)." ngày";
                                                        }else{
                                                            $hours = floor($time_remaining/3600);
                                                            echo "Còn ".$hours." giờ";
                                                        }
                                                        ?>
                                                    </div>
                                                <?php else: ?>
                                                    <i class="far fa-clock fa-fw"></i>
                                                    Ngày hết hạn : <?php if(!empty($packet) && !empty($packet->expired)) echo date("d/m/Y",$packet->expired); ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </a>
                                    </div> -->
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

    
                        <div class="user-logined">
                            <?php if(!empty($user->uid)): ?>
                                <div class="user-logined-content">
                                    <?php
                                    if(!empty($user->picture)){
                                        $avatar = file_load($user->picture);
                                    }
                                    if (!empty($avatar)) {
                                        $node_img = (array) $avatar;
                                        $node_img['style_name'] = "style_60x60";
                                        $node_img['path'] = $node_img['uri'];
                                        $node_img = theme('image_style', $node_img);
                                        print(l($node_img, 'user/'.$user->uid."/edit", array('html'=>TRUE)));
                                    }else{
                                        ?>
                                        <a href="/user/<?php echo $user->uid; ?>/edit">
                                            <img src="/sites/all/themes/cassiopeia_theme/img/logo-icon.jpg" class="img-responsive" alt="">
                                        </a>
                                        <?php
                                    }
                                    ?>
                                    <span><?php echo !empty($_user->field_full_name['und'][0]['value'])?$_user->field_full_name['und'][0]['value']:$_user->name; ?></span>
                                    <span class="ml-10 fa fa-angle-down" aria-hidden="true"></span>
                                    <div class="user-profile">
                                        <div class="user-profile-container">
                                            <div class="user-profile-navigator">
                                                <ul>

                                                    <li>
                                                        <a href="/user/<?php echo $user->uid; ?>/edit">
                                                            Thông tin cá nhân
                                                        </a>
                                                    </li>
                                                    <?php if(user_has_role(5)||user_has_role(3)): ?>
                                                        <li>
                                                            <a href="/admin/manager/user">
                                                                Danh sách người dùng
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="/admin/manager/booking">
                                                                Danh sách đăng ký mua gói
                                                            </a>
                                                        </li>
                                                    <?php endif; ?>
                                                    <li>
                                                        <a href="/price-board">
                                                            Nâng cấp tài khoản
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="/user/<?php echo $user->uid; ?>/change-password">
                                                            Đổi mật khẩu
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="/user/logout" style="color: red;">
                                                            Đăng xuất
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="user-login-block">
                                    <span class="btn-login-form">Đăng nhập</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>
