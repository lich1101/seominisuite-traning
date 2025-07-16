<?php drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/user.js', ['weight' => 1000]);
global $user;
$arg = arg();
$conditions = array();
$conditions['status'] = array(
    "type"      => "propertyCondition",
    "value"     => 1,
    "condition" => "=",
);
$conditions['field_sih'] = array(
    "type"      => "fieldCondition",
    "key"       => "value",
    "value"     => 1,
    "condition" => "=",
);
$conditions['field_weight'] = array(
    "type"      => "fieldOrderBy",
    "column"       => "value",
    "direction" => "ASC",
);

$articles = cassiopeia_get_items_by_conditions($conditions,"article","node");
?>
<div class="sidebar">
    <div class="sidebar-content">
        <ul>
            <li class="<?php if(!empty($arg[0]) && $arg[0]=="dashboard") echo "active"; ?>">
                <a class="<?php if(!empty($arg[0]) && $arg[0]=="dashboard") echo "active"; ?>" href="/dashboard">
                    <span class="icon material-icons-outlined">dashboard</span>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="expanded <?php if(!empty($arg[0]) && $arg[0]=="quan-ly-backlink") echo "active"; ?>">
                <a href="/quan-ly-backlink" class="sm-event-none">
                    <span class="icon material-icons-outlined">link</span>
                    <span>Quản lý backlink</span>
                    <div class="mb-none pull-right-container">
                        <i class="fa fa-angle-down"></i>
                    </div>
                </a>
                <ul class="mb-none">
                    <li>
                        <a class="<?php if(!empty($arg[0]) && $arg[0]=="quan-ly-backlink") echo "active"; ?>" href="/quan-ly-backlink">Tất cả dự án</a>
                    </li>
                    <li>
                        <a href="#" class="btn-add-backlink-project">Thêm dự án</a>
                    </li>
                </ul>
            </li>
            <li class="expanded <?php if(!empty($arg[0]) && $arg[0]=="quan-ly-keywords") echo "active"; ?>">
                <a href="/quan-ly-keywords" class="sm-event-none">
                    <span class="icon material-icons-outlined">spellcheck</span>
                    <span>Thứ hạng từ khoá</span>
                    <div class="mb-none pull-right-container">
                        <i class="fa fa-angle-down"></i>
                    </div>
                </a>
                <ul class="mb-none">
                    <li>
                        <a class="<?php if(!empty($arg[0]) && $arg[0]=="quan-ly-keywords") echo "active"; ?>" href="/quan-ly-keywords">Tất cả dự án</a>
                    </li>
                    <li>
                        <a href="#" class="btn-add-keyword-project">Thêm dự án</a>
                    </li>
                </ul>
            </li>
<!--            --><?php //if(user_has_role(3)): ?>
                <li class="expanded <?php if(!empty($arg[0]) && $arg[0]=="quan-ly-du-an-content") echo "active"; ?>">
                    <a href="/quan-ly-du-an-content" class="sm-event-none">
                        <span class="icon material-icons-outlined" style=" font-size: 18px;
    margin-left: 3px;
    margin-right: 14px;"><i class="fa-sharp fa-regular fa-pen-to-square"></i></span>
                        <span>Hỗ trợ Outline Content</span>
                        <div class="mb-none pull-right-container">
                            <i class="fa fa-angle-down"></i>
                        </div>
                    </a>
                    <ul class="mb-none">
                        <li>
                            <a class="<?php if(!empty($arg[0]) && $arg[0]=="quan-ly-du-an-content") echo "active"; ?>" href="/quan-ly-du-an-content">Tất cả dự án</a>
                        </li>
                        <li>
                            <a href="#" class="btn-add-content-project">Thêm dự án</a>
                        </li>
                    </ul>
                </li>
<!--            --><?php //endif; ?>
            <li class="expanded  <?php if(!empty($arg[0]) && $arg[0]=="kiem-tra-dao-van") echo "active"; ?>">
                <a class="<?php if(!empty($arg[0]) && $arg[0]=="kiem-tra-dao-van") echo "active"; ?>" href="/kiem-tra-dao-van">
                <span class="icon material-icons-outlined">search</span>
                    <span>Kiểm tra đạo văn</span>
                </a>
                <!-- <ul>
                    <li>
                        <a class="<?php if(!empty($arg[0]) && $arg[0]=="kiem-tra-dao-van") echo "active"; ?>" href="/kiem-tra-dao-van">Kiểm tra đạo văn</a>
                    </li>
                    <li>
                        <a href="./page-check-backlink.html">Kiểm tra chỉ số backlink</a>
                    </li>
                </ul> -->
            </li>
            <li class="expanded hidden-xs <?php if(!empty($arg[0]) && $arg[0]=="guest-post" || ($arg[0]=="node"&&$arg[1]==986751)) echo "active"; ?>">
                <a href="#" class="sm-event-none">
                    <i style="font-size: 18px;
    margin-left: 3px;
    margin-right: 14px;" class="fa-regular fa-share-from-square"></i>
                    <span>Chia sẻ Guest Post</span>
                    <div class="mb-none pull-right-container">
                        <i class="fa fa-angle-down"></i>
                    </div>
                </a>
                <ul>
                    <li><?php echo l("Đăng bài Guest Post","guest-post/article",array("html"=>TRUE)); ?></li>
                    <li><?php echo l("Website của bạn","guest-post/domain",array("html"=>TRUE)); ?></li>
                    <li><?php echo l("Danh sách website","guest-post/website",array("html"=>TRUE)); ?></li>
                    <li><?php echo l("Order content","guest-post/order-content",array("html"=>TRUE)); ?></li>
                    <li><?php echo l("Khiếu nại","guest-post/complain",array("html"=>TRUE)); ?></li>
                    <li><?php echo l("Giao dịch điểm","guest-post/point/exchange",array("html"=>TRUE)); ?></li>
                    <li><?php echo l("Quy định tham gia","node/986751",array("html"=>TRUE)); ?></li>
                </ul>
            </li>
            <li class="visible-xs">
                <?php echo l("<i style=\"font-size: 18px;
    margin-left: 3px;
    margin-right: 14px;\" class=\"fa-regular fa-share-from-square\"></i>
                    <span>Chia sẻ Guest Post</span>","node/986751",array("html"=>TRUE)); ?>
            </li>
            <?php if(user_has_role(3)): ?>
                <li class="expanded mb-none <?php if(!empty($arg[0]) && $arg[0]=="get-url") echo "active"; ?>">
                    <a class="<?php if(!empty($arg[0]) && $arg[0]=="get-url") echo "active"; ?>" href="/get-url">
                        <span class="icon material-icons-outlined">search</span>
                        <span>Get URL</span>
                    </a>
                    <!-- <ul>
                    <li>
                        <a class="<?php if(!empty($arg[0]) && $arg[0]=="kiem-tra-dao-van") echo "active"; ?>" href="/kiem-tra-dao-van">Kiểm tra đạo văn</a>
                    </li>
                    <li>
                        <a href="./page-check-backlink.html">Kiểm tra chỉ số backlink</a>
                    </li>
                </ul> -->
                </li>
            <?php endif; ?>
          <li class="<?php if(!empty($arg[0]) && $arg[0]=="captcha") echo "active"; ?>">
            <a class="<?php if(!empty($arg[0]) && $arg[0]=="captcha") echo "active"; ?>" href="/captcha/resolve/booking">
              <i class="fa-regular fa-circle-exclamation-check"></i>
              <span>Mua lượt giải Captcha</span>
            </a>
          </li>
            <li class="expanded <?php if(!empty($arg[0]) && $arg[0]=="node"&&$arg[1]!=986751) echo "active"; ?>">
                <a href="#" class=" sm-event-none">
                    <span class="icon material-icons-outlined">support_agent</span>
                    <span>Hướng dẫn sử dụng</span>
                    <div class=" pull-right-container">
                        <i class="fa fa-angle-down"></i>
                    </div>
                </a>
                 <ul>
                    <?php foreach($articles as $article): $_class = ""; if(!empty($arg[1])&&$arg[1]==$article->nid) $_class = "active";?>
                        <li>
                            <?php echo l($article->title,"node/".$article->nid,array("html"=>TRUE,"attributes"=>array("class"=>array($_class)))); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <li class="expanded <?php if(!empty($arg[0]) && $arg[0]=="user") echo "active"; ?>">
                <a href="#">
                <span class="icon material-icons-outlined">admin_panel_settings</span>
                    <span>Quản lý tài khoản</span>
                    <div class="pull-right-container">
                        <i class="fa fa-angle-down"></i>
                    </div>
                </a>
                <ul>
                    <li>
                        <a class="<?php if(!empty($arg[2]) && $arg[2]=="edit") echo "active"; ?>" href="/user/<?php echo $user->uid; ?>/edit">
                            <span>Thông tin cá nhân</span>
                        </a>
                    </li>
                    <li>
                        <a class="" href="/price-board">
                            <span>Nâng cấp tài khoản</span>
                        </a>
                    </li>
                    <li><?php echo l("Lịch sử giao dịch","user/manager/transaction",array("html"=>TRUE)); ?></li>
                     <li>
                        <a class="<?php if(!empty($arg[2]) && $arg[2]=="change-password") echo "active"; ?>" href="/user/<?php echo $user->uid; ?>/change-password">
                            <span>Đổi mật khẩu</span>
                        </a>
                    </li>
                </ul>
            </li>

        </ul>

        <div class="support-links">
            <a class="click-on-link" target="_blank" href="<?php _echo(variable_get("cassiopeia_config_ads_click_on_site_url")); ?>">
                <!-- <?php _echo(variable_get("cassiopeia_config_ads_click_on_site_title")); ?> -->
                <span><?php _echo(variable_get("cassiopeia_config_ads_click_on_site_title")); ?></span>
                <span>Moz PA-DA>50</span>
            </a>
<!--            <div class="line"></div>-->
            <a target="_blank" class="fb-links" href="<?php _echo(variable_get("cassiopeia_config_ads_click_on_page_url")); ?>">
              <i class="fa-brands fa-facebook"></i>
                <span class="text"><?php _echo(variable_get("cassiopeia_config_ads_click_on_page_title")); ?></span>
            </a>
        </div>
    </div>
</div>

<div id="modalBacklinkProject" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Thêm mới dự án</h4>
            </div>
            <div class="modal-body">
                <?php
                $cassiopeia_backlink_project_form = drupal_get_form("cassiopeia_backlink_project_form");
                if(!empty($cassiopeia_backlink_project_form)){
                    $cassiopeia_backlink_project_form = drupal_render($cassiopeia_backlink_project_form);
                    print($cassiopeia_backlink_project_form);
                }
                ?>
            </div>
        </div>

    </div>
</div>
<div id="modalContentProject" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Thêm mới dự án</h4>
            </div>
            <div class="modal-body">
                <?php
                $cassiopeia_content_project_form = drupal_get_form("cassiopeia_content_project_form");
                if(!empty($cassiopeia_content_project_form)){
                    $cassiopeia_content_project_form = drupal_render($cassiopeia_content_project_form);
                    print($cassiopeia_content_project_form);
                }
                ?>
            </div>
        </div>

    </div>
</div>
<span id="current-page" data-page="1"></span>

<div id="modalKeywordProject" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Thêm mới dự án Từ khóa</h4>
            </div>
            <div class="modal-body">
                <?php
                $cassiopeia_user_project_form = drupal_get_form("cassiopeia_user_project_form","keyword");
                if(!empty($cassiopeia_user_project_form)){
                    $cassiopeia_user_project_form = drupal_render($cassiopeia_user_project_form);
                    print($cassiopeia_user_project_form);
                }
                ?>
            </div>
        </div>

    </div>
</div>
<div class="extension-alert">
    <div class="block-container">
<!--        <span class="close btn-close">&times;</span>-->
        <div class="block-body">
            <div>Google tạm khóa chức năng captcha, hệ thống sẽ tự động tiếp tục sau 5 phút</div>
        </div>
<!--        <div class="block-button">-->
<!--            <div class="btn-close btn btn-default">Đóng</div>-->
<!--        </div>-->
    </div>
</div>
<div id="modalExtensionInstall" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Hướng dẫn cài đặt và sử dụng</h4>
            </div>
            <div class="modal-body">
                <div>
                    <b>Chrome, CocCoc:</b> <a href="">Link cài đặt add on SeoTool</a>
                </div>
                <ul>
                    <li>1. Tải extension dành cho Chrome, CocCoc ở link trên</li>
                    <li>2. Tải extension dành cho Chrome, CocCoc ở link trên</li>
                </ul>
            </div>
        </div>

    </div>
</div>
<script>
    <?php if(isset($_SESSION['custom_alert']) && !empty($_SESSION['custom_alert'])): ?>
        setTimeout(function() { document.dispatchEvent(new CustomEvent('<?php echo $_SESSION['custom_alert']['type']; ?>', { detail: '<?php echo $_SESSION['custom_alert']['message'] ?>' })); }, 500);
    <?php $_SESSION['custom_alert'] = array(); endif; ?>
</script>