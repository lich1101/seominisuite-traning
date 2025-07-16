<?php
//db_delete("tbl_booking_product")->execute();
//db_delete("tbl_available_product")->execute();
$cache = !empty($_REQUEST['data'])?$_REQUEST['data']:array();
//_print_r($cache);
$active = 0;
$status = isset($_REQUEST['status'])?$_REQUEST['status']:-1;
if($status==0){
    $active=1;
}
$query = db_select("tbl_booking_product","tbl_booking_product");
$query->fields("tbl_booking_product");

$query->orderBy("tbl_booking_product.created","DESC");
if(!empty($cache['code'])){
    $query->condition("tbl_booking_product.code","%".$cache['code']."%","LIKE");
}
if(!empty($cache['from_date'])){
    $query->condition("tbl_booking_product.created",strtotime(date("d-m-Y 00:00:00",strtotime($cache['from_date']))),">=");
}
if(!empty($cache['to_date'])){
    $query->condition("tbl_booking_product.created",strtotime(date("d-m-Y 23:59:59",strtotime($cache['to_date']))),"<=");
}
if(!empty($cache['account']) && $cache['account']!="all"){
    $query->condition("tbl_booking_product.uid",$cache['account']);
}
if(!empty($cache['product']) && $cache['product']!="all"){
    $query->condition("tbl_booking_product.nid",$cache['product']);
}
if(isset($cache['b_status']) && $cache['b_status']!="all"){
    $query->condition("tbl_booking_product.status",$cache['b_status']);
}
if(isset($cache['gift']) && $cache['gift']!="all"){
    $or = db_or();
    if($cache['gift']==0){
        $or->condition("tbl_booking_product.gift",$cache['gift']);
        $or->condition("tbl_booking_product.gift",null);
        $query->condition($or);
    }else{
        $query->condition("tbl_booking_product.gift",$cache['gift']);
    }
}
$result = $query->execute()->fetchAll();
//_print_r(strtotime(date("22-1-2022 25:59:59",strtotime($cache['to_date']))));
//_print_r(date("d-m-Y",1642490347));
//_print_r(strtotime($cache['to_date']));
?>
<div class="page-booking-manager">
    <div class="add-more">
        <?php
        $menu_item = menu_get_item('admin/add/booking');
        if(!empty($menu_item) && !empty($menu_item['access'])):?>
            <a href="/admin/add/booking" class="btn btn-primary">Thêm mới</a>
        <?php endif; ?>
    </div>
    <div class="filter-form">
        <?php
        $cassiopeia_admin_manager_user_filter_form = drupal_get_form("cassiopeia_admin_manager_booking_filter_form",$cache);
        if(!empty($cassiopeia_admin_manager_user_filter_form)){
            $cassiopeia_admin_manager_user_filter_form = drupal_render($cassiopeia_admin_manager_user_filter_form);
            echo $cassiopeia_admin_manager_user_filter_form;
        }
        ?>
    </div>
    <table class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Code</th>
            <th>Ngày tạo</th>
            <th>Tài khoản</th>
            <th>Số điện thoại</th>
            <th>Gói sản phẩm</th>
            <th>Quà tặng</th>
            <th>Giá tiền</th>
            <th>Tình trạng</th>
        </tr>
        </thead>
        <tbody>
        <?php if(!empty($result)): ?>
            <?php foreach($result as $item): ?>
                <?php
                $_user = user_load($item->uid);
                ?>
                <?php $_node = node_load($item->nid); ?>
                <?php if(!empty($_node) && !empty($_user->uid)): ?>
                    <tr>
                        <td><?php echo($item->code); ?></td>
                        <td><?php echo(date("d/m/Y H:i",$item->created)); ?></td>
                        <td><?php echo(l($_user->mail,"user/".$_user->uid."/edit")); ?></td>
                        <td>
                            <?php if(!empty($_user->field_tel['und'])) echo $_user->field_tel['und'][0]['value']; ?>
                        </td>
                        <td><?php echo(l($_node->title,"node/".$_node->nid,array("html"=>TRUE))); ?>
                            <?php if($_node->nid!=AGENCY): ?>
                                x <?php echo $item->month; ?> tháng
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(user_has_role(3)): ?>
                                <input   <?php if(!empty($item->gift)) echo "checked"; ?> type="checkbox" value="<?php echo $item->gift; ?>" data-id="<?php echo $item->id; ?>" name="gift">
                            <?php else: ?>
                                <input disabled readonly  <?php if(!empty($item->gift)) echo "checked"; ?> type="checkbox" value="<?php echo $item->gift; ?>" data-id="<?php echo $item->id; ?>" name="gift">
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($item->gift): ?>
                                0 đ
                            <?php else: ?>
                                <?php if($item->nid==AGENCY):  ?>
                                    <?php echo number_format($_node->field_price['und'][0]['value'],0,",","."); ?> đ
                                <?php else:  ?>
                                    <?php echo number_format($item->price*$item->month,0,",","."); ?> đ
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(user_has_role(3)): ?>
                                <?php if($item->status==2): ?>
                                    <select name="status" id="" class="form-control" disabled="disabled" readonly="readonly">
                                        <option value="2">Đã xử lý</option>
                                    </select>
                                <?php else: ?>
                                    <select name="status" id="" class="form-control" data-code="<?php echo($item->code); ?>">
                                        <option <?php if($item->status==0) echo "selected"; ?> value="0">Chưa xử lý</option>
                                        <option <?php if($item->status==1) echo "selected"; ?> value="1">Đang xử lý</option>
                                        <option <?php if($item->status==2) echo "selected"; ?> value="2">Đã xử lý</option>
                                    </select>
                                <?php endif; ?>
                            <?php else: ?>
                                <select name="" id="" class="form-control" disabled="disabled" readonly="readonly">
                                    <option <?php if($item->status==0) echo "selected"; ?> value="0">Chưa xử lý</option>
                                    <option <?php if($item->status==1) echo "selected"; ?> value="1">Đang xử lý</option>
                                    <option <?php if($item->status==2) echo "selected"; ?> value="2">Đã xử lý</option>
                                </select>
                            <?php endif; ?>

                        </td>
                        <?php if(user_has_role(3)): ?>
                            <td>
                                <?php if($item->status!=2): ?>
                                    <button class="btn btn-danger btn-booking-delete" data-code="<?php echo($item->code); ?>"><span class="fa fa-trash"></span></button>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>