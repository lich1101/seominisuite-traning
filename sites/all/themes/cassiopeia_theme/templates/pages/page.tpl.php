<?php
global $user;
global $packet;

$packet = cassiopeia_get_available_packet_by_uid($user->uid);

if(empty($user->uid)){
//    drupal_goto("/user");
}
//drupal_set_message("ff");
//print_r(123);
?>
<!--asdfasdf-->
<div class="wrapper">
  <?php include('header.inc'); ?>
    <div id="main-container">
        <section class="content cassiopeia-container">
            <div id="content" class="clearfix">
              <?php if ($action_links): ?>
                  <ul class="action-links"><?php print render($action_links); ?></ul>
              <?php endif; ?>
              <?php if ($tabs): ?>
                  <div class="tabs">
                    <?php print render($tabs); ?>
                  </div>
              <?php endif; ?>
               <div class="main">

                   <div class="main-inner" >
                       <?php if(!empty($user->uid)) echo(_cassiopeia_render_theme("module","cassiopeia","templates/nav/nav-user.tpl.php")); ?>
                       <div class="content " style="<?php if(empty($user->uid)) echo "margin:auto;"; ?>">
                            <div class="header-note">
                                <div class="note">
                                    Cài đặt Extension để sử dụng phần mềm
                                </div>
                                <a target="_blank" href="https://chrome.google.com/webstore/detail/jkofekpejnnboenpjppalhmlimgggeni" class='extension-setting'>Cài Extension</a>
                                <a target="_blank" href="/<?php echo drupal_get_path_alias("node/169974"); ?>">
                                    Hướng dẫn
                                </a>
                            </div>
                           <?php $available_point = cassiopeia_guest_post_user_available_point_load($user->uid); ?>
                           <?php if($available_point->point<1): ?>
                               <div class="danger-alert hidden" style="display: flex;">
                                   <div class="note">
                                       Mời bạn tham gia hệ thống GuestPost,  Miễn phí & nhận nhiều Quà tặng, ưu đãi
                                   </div>
                                   <a target="_blank" href="/<?php echo drupal_get_path_alias("node/986751"); ?>" class='extension-setting'>Tham gia</a>
                                   <a target="_blank" href="/chia-se-guestpost">
                                       Hướng dẫn
                                   </a>
                               </div>
                           <?php endif; ?>
                            <?php if(!empty($user->uid)): ;?>


                                <?php if($packet->product==AGENCY): ?>

                                <?php elseif($packet->product==BASIC): ?>
                                    <div class="warning-alert">
                                        <div class="note">
                                            Ủng hộ <span>2.000đ</span> mỗi ngày để phần mềm có ngân sách duy trì miễn phí lâu dài!
                                        </div>
                                        <a href="/price-board" class='extension-setting'>Ủng hộ</a>
                                    </div>
                                <?php else: ?>
                                    <?php if($packet->expired<REQUEST_TIME): ?>
                                        <div class="danger-alert">
                                            <div class="note">
                                                Tài khoản của bạn hết hạn, vui lòng gia hạn hoặc nâng cấp để tiếp tục sử dụng!
                                            </div>
                                            <a href="/price-board" class='extension-setting'>Gia hạn</a>
                                        </div>
                                    <?php else: ?>
                                        <?php
                                        $date1=date_create(date("Y-m-d",REQUEST_TIME));
                                        $date2=date_create( date("Y-m-d",$packet->expired));
                                        $diff=date_diff($date1,$date2);
                                        $days = $diff->format("%a");
                                        ?>
                                        <?php if($days<=30): ?>
                                            <div class="warning-alert">
                                                <div class="note">
                                                    Tài khoản của bạn sắp hết hạn, vui lòng gia hạn hoặc nâng cấp để tiếp tục sử dụng!
                                                </div>
                                                <a href="/price-board" class='extension-setting'>Gia hạn</a>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php else: ?>

                            <?php endif; ?>
<!--                         <div class="danger-alert">-->
<!--                           <div class="note">-->
<!--                             SEO MiniSuite đang nâng cấp hệ thống Captcha. Quý khách tạm thời vượt Captcha thủ công trong 7-14 ngày tới!-->
<!--                           </div>-->
<!--                         </div>-->
                           <?php if(isset($_SESSION['mobile_note'])): ?>
                               <div class="visible-xs mobile-note">
                                   <div class="text confirm">
                                       Phần mềm chỉ hiển thị số liệu dự án trên Mobile & Tablet. Vui lòng chạy chức năng trên Máy tính (PC/Laptop)
                                   </div>
                                   <span class="close">&times;</span>
                               </div>
                           <?php endif; ?>
                           <?php if ($messages): ?>
                               <?php print $messages; ?>
                           <?php endif; ?>
<!--                           <div style="--><?php //if(empty($user->uid)) echo "margin:auto;"; ?><!--">-->
                               <?php print render($page['content']); ?>
<!--                           </div>-->
                       </div>
                   </div>
               </div>
            </div>
        </section>
    </div>
  <?php  include('footer.inc'); ?>
</div>

