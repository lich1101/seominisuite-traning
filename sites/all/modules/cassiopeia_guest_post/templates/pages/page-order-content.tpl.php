<?php
$cache = !empty($_REQUEST['data'])?$_REQUEST['data']:array();
$cache['sort_by'] = !empty($cache['sort_by'])?$cache['sort_by']:"changed";
$cache['sort_direction'] = !empty($cache['sort_direction'])?$cache['sort_direction']:"DESC";

//_print_r($orders);
?>
<div class="visible-xs mobile-note">
    <div class="text confirm">
        Mời bạn dùng Laptop hoặc Desktop để sử dụng chức năng này
    </div>
    <!--                                   <span class="close">&times;</span>-->
</div>
<?php
drupal_add_js("https://code.jquery.com/ui/1.13.0/jquery-ui.js");
drupal_add_css("https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css");
drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/order-content.js', ['weight' => 1000]);
ctools_include('modal');
ctools_modal_add_js();
global $user;
?>
<div class="page page-order-content hidden-xs">
    <?php
    $cassiopeia_guest_post_order_content_search_form = drupal_get_form("cassiopeia_guest_post_order_content_search_form");
    if(!empty($cassiopeia_guest_post_order_content_search_form)){
        $cassiopeia_guest_post_order_content_search_form = drupal_render($cassiopeia_guest_post_order_content_search_form);
        echo $cassiopeia_guest_post_order_content_search_form;
    }
    ?>
    <div class="notes">
        <div class="row">
            <div class="col-md-6">
                <div class="block-container success">
                    <div class="block-title">Chú ý:</div>
                    <div class="block-body">
                        <ul class="no-style-list no-padding no-margin">
                            <li><i class="fa fa-angle-right"></i> Bạn có thể đăng ký thông tin để nhận job content từ khách hàng</li>
                            <li><i class="fa fa-angle-right"></i> Người mua có thể liên hệ trực tiếp với CTV content qua SĐT hoặc Email</li>
                            <li><i class="fa fa-angle-right"></i> Người mua và người bán tự nguyện giao dịch cùng nhau</li>
                            <li><i class="fa fa-angle-right"></i> <b>SeoMinisuite</b> không thu bất cứ khoản phí nào. Vì vậy <b>SeoMinisuite</b> không chịu trách nhiệm với bất cứ vấn đề gì giữa đôi bên</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="block-container danger">
                    <div class="block-title">Lưu ý:</div>
                    <div class="block-body">
                        <ul class="no-style-list no-padding no-margin">
                            <li><i class="fa fa-angle-right"></i> Kiểm tra độ tin tưởng người bán qua website/profile</li>
                            <li><i class="fa fa-angle-right"></i> Mọi vấn đề Scam mọi người có thể tạo bài viết và tranh luận cùng nhau trong group</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
