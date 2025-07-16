<?php
global $user;
$cache = !empty($_REQUEST['data'])?$_REQUEST['data']:array();
if(isset($_REQUEST['expired'])){
    $cache['expired'] = $_REQUEST['expired'];
}
$backlink = db_select("node","tbl_node");
$backlink->fields("tbl_node");
$backlink->condition("tbl_node.type","project_backlink");
$backlink->addExpression("COUNT(tbl_node.nid)","TotalBacklinkProject");
$backlink->groupBy("tbl_node.uid");

$keyword = db_select("node","tbl_node");
$keyword->fields("tbl_node");
$keyword->condition("tbl_node.type","project_keyword");
$keyword->addExpression("COUNT(tbl_node.nid)","TotalkeywordProject");
$keyword->groupBy("tbl_node.uid");

$content = db_select("node","tbl_node");
$content->fields("tbl_node");
$content->condition("tbl_node.type","content_project");
$content->addExpression("COUNT(tbl_node.nid)","TotalcontentProject");
$content->groupBy("tbl_node.uid");

$product = db_select("tbl_available_product","tbl_available_product");
$product->fields("tbl_available_product");
$product->condition("tbl_available_product.expired",array(REQUEST_TIME,REQUEST_TIME),"BETWEEN");

$user_query = db_select("users","tbl_query");
$user_query->fields("tbl_query");
$user_query->leftJoin("users_roles","tbl_role","tbl_role.uid=tbl_query.uid");
$user_query->groupBy("tbl_query.uid");

$query = db_select($user_query,"tbl_user");
$query->fields("tbl_user");
$query->leftJoin("field_data_field_full_name","field_full_name","field_full_name.entity_id=tbl_user.uid");
$query->addField("field_full_name","field_full_name_value","full_name");

$query->leftJoin("field_data_field_tel","field_tel","field_tel.entity_id=tbl_user.uid");
$query->addField("field_tel","field_tel_value","field_tel");

$query->leftJoin($backlink,"tbl_backlink","tbl_backlink.uid=tbl_user.uid");
$query->addField("tbl_backlink","TotalBacklinkProject","TotalBacklinkProject");

$query->leftJoin($keyword,"tbl_keyword","tbl_keyword.uid=tbl_user.uid");
$query->addField("tbl_keyword","TotalkeywordProject","TotalkeywordProject");

$query->leftJoin($content,"tbl_content","tbl_content.uid=tbl_user.uid");
$query->addField("tbl_content","TotalcontentProject","TotalcontentProject");

//$query->leftJoin("tbl_available_product","tbl_available_product","tbl_available_product.uid=tbl_user.uid");
//$query->addField("tbl_available_product","expired");

if(!empty($roles)){
    $query->condition("tbl_role.rid",array(4),"IN");
}
if(!empty($excludes)){
    $query->condition("tbl_role.rid",array(3),"NOT IN");
}
if(!empty($cache['full_name'])){
    $query->condition("field_full_name.field_full_name_value","%".$cache['full_name']."%","LIKE");
}
if(!empty($cache['mail'])){
    $query->condition("tbl_user.mail","%".trim($cache['mail'])."%","LIKE");
}
if(!empty($cache['name'])){
    $query->condition("tbl_user.name","%".trim($cache['name'])."%","LIKE");
}
if(!empty($cache['tel'])){
    $query->condition("field_tel.field_tel_value","%".trim($cache['tel'])."%","LIKE");
}
$query->condition("tbl_user.uid",1,">");
$query->orderBy("tbl_user.created","DESC");
$accounts = $query->execute()->fetchAll();
$TotalRow = count($accounts);
//_print_r($accounts);
$limit = 50;
if($user->uid==1){
    $limit=50;
}


?>
<div class="page-manager-user">
    <div style="margin-bottom: 30px;">
        <?php
        $menu_item = menu_get_item('admin/people/create');
        if(!empty($menu_item) && !empty($menu_item['access'])):?>
            <a href="/admin/people/create" class="btn btn-primary">Thêm mới</a>
        <?php endif; ?>
        <a href="/admin/manager/user/export" class="btn btn-success">Excel</a>
    </div>
    <div class="filter-form">
        <?php
            $cassiopeia_admin_manager_user_filter_form = drupal_get_form("cassiopeia_admin_manager_user_filter_form",$cache);
            if(!empty($cassiopeia_admin_manager_user_filter_form)){
                $cassiopeia_admin_manager_user_filter_form = drupal_render($cassiopeia_admin_manager_user_filter_form);
                echo $cassiopeia_admin_manager_user_filter_form;
            }
        ?>
    </div>
    <table class="table table-hover table-stripped">
        <thead>
            <tr>
                <th>STT</th>
                <th>Họ tên</th>
                <th>Tên đăng nhập/Email</th>
                <th>Số điện thoại</th>
                <th>Gói</th>
                <th>Dự án Backlink</th>
                <th>Dự án Từ khóa</th>
                <th>Dự án Outline Content</th>
                <th>Ngày tạo</th>
                <th>Thời hạn</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $__accounts = $accounts;
            if(!empty($cache['expired']) && $cache['expired']!="all") {
                $__accounts = [];
                foreach ($accounts as $_account) {
                    $packet = cassiopeia_get_available_packet_by_uid($_account->uid);
                    if (!empty($packet)) {
                        if ($packet->product != AGENCY) {
                            if($packet->expired>REQUEST_TIME&&$packet->expired<=REQUEST_TIME+30*86400){
                                $__accounts[] = $_account;
                            }
                        }
                    }
                }
            }
            $page = pager_default_initialize(count($__accounts), $limit, 0);
            $offset = $limit * $page;
            $TotalRow -= $limit * $page;
            if(!empty($__accounts)){
                $__accounts = array_slice($__accounts, $offset, $limit);
            }else{
                $__accounts=null;
            }
            ?>
            <?php $stt=$TotalRow; foreach((array)$__accounts as $_account):  $account = user_load($_account->uid);?>
                <?php $packet = cassiopeia_get_available_packet_by_uid($_account->uid);  ?>
                <tr>
                    <td class="stt"><?php echo $stt; ?></td>
                    <td><?php _echo($_account->full_name); ?></td>
                    <td><?php echo $_account->mail; ?></td>
                    <td><?php echo $_account->field_tel; ?></td>
                    <td><?php if(!empty($packet)) _echo($packet->product_name); ?></td>
                    <td><?php echo !empty($_account->TotalBacklinkProject)?$_account->TotalBacklinkProject:0; ?></td>
                    <td><?php echo !empty($_account->TotalkeywordProject)?$_account->TotalkeywordProject:0  ; ?></td>
                    <td><?php echo !empty($_account->TotalcontentProject)?$_account->TotalcontentProject:0  ; ?></td>
                    <td><?php echo date("d-m-Y H:i",$_account->created); ?></td>
                    <td>
                        <?php
                        if(!empty($packet)){
                            if($packet->product!=AGENCY){
                                if($packet->expired>REQUEST_TIME&&$packet->expired<=REQUEST_TIME+30*86400){
                                    ?>
                                    <span class="color-red">
                                        <?php
                                        $time_remaining = $packet->expired-REQUEST_TIME;
                                        if((($packet->expired-REQUEST_TIME)/86400)>1){
                                            echo "Còn ".floor(($packet->expired-REQUEST_TIME)/86400)." ngày";
                                        }else{
                                            $hours = floor($time_remaining/3600);
                                            echo "Còn ".$hours." giờ";
                                        }
                                        ?>
                                    </span>
                                    <?php
                                }else{
                                    if($packet  ->expired>0) echo date("d-m-Y H:i",$packet->expired);
                                }
                            }
                        }else{
                            echo "Tài khoản test";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        $menu_item = menu_get_item('user/'.$_account->uid.'/change-packet');
                        if(!empty($menu_item) && !empty($menu_item['access'])):?>
                            <a href="/user/<?php echo $_account->uid; ?>/change-packet" class="btn btn-success" title="Thay đổi gói"><i class="fa fa-pencil"></i></a>
                        <?php endif; ?>
                        <?php
                        $menu_item = menu_get_item('user/'.$_account->uid.'/edit');
                        if(!empty($menu_item) && !empty($menu_item['access'])):?>
                            <a href="/user/<?php echo $_account->uid; ?>/edit" class="btn btn-primary" title="Sửa"><i class="fa fa-edit"></i></a>
                        <?php endif; ?>
                        <?php if($user->uid==1): ?>
                            <a href="/user/<?php echo $_account->uid; ?>/cancel" class="btn btn-danger" title="Xóa"><i class="fa fa-trash"></i></a>
                        <?php endif; ?>
                        <a title="Thay đổi điểm người dùng" class="btn btn-warning" href="/admin/manager/guest-post/point/<?php echo $_account->uid; ?>/edit?destination=admin/manager/user"><i class="fa-brands fa-wordpress-simple"></i></a>
                        <a title="Thay đổi lượt đổi domain" class="btn btn-info" href="/admin/manager/guest-post/domain-change/<?php echo $_account->uid; ?>/edit?destination=admin/manager/user"><i class="fa-solid fa-sliders"></i></a>
                        <a title="Thay đổi lượt giải captcha" class="btn btn-purple" href="/admin/manager/captcha/resolve/<?php echo $_account->uid; ?>/edit?destination=admin/manager/user"><i class="fa-solid fa-sliders"></i></a>
                    </td>
                </tr>
            <?php $stt--; endforeach; ?>
        </tbody>
    </table>
    <!-- paging-->
    <div class="page">
        <div class="cassiopeia-pagination">
            <div class="cassiopeia-pagination-container">
                <?php print (theme('pager',  array('tags' => array('«','‹','','›','»'))));?>
            </div>
        </div>
    </div>
    <!--e: paging-->
</div>