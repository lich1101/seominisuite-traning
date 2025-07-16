<?php
$cache = !empty($_REQUEST['data'])?$_REQUEST['data']:array();
_print_r($cache);
$active = 0;
$status = isset($_REQUEST['status'])?$_REQUEST['status']:-1;
if($status==0){
    $active=1;
}
//db_delete("tbl_booking_product")->execute();
$query = db_select("tbl_booking_product","tbl_booking_product");
$query->fields("tbl_booking_product");
//$query->leftJoin("tbl_product_price","tbl_product_price","tbl_product_price.id=tbl_booking_product.priceID");
//$query->fields("tbl_product_price",array("price","month"));
$query->orderBy("tbl_booking_product.created","DESC");
if(!empty($cache['code'])){
    $query->condition("tbl_booking_product.code","%".$cache['code']."%","LIKE");
}
if(!empty($cache['from_date'])){
    $query->condition("tbl_booking_product.updated",strtotime(date("d-m-Y 00:00:00",strtotime($cache['from_date']))),">");
}
if(!empty($cache['to_date'])){
    $query->condition("tbl_booking_product.updated",strtotime(date("d-m-Y 23:59:59",strtotime($cache['to_date']))),"<=");
}
if(!empty($cache['account']) && $cache['account']!="all"){
    $query->condition("tbl_booking_product.uid",$cache['account']);
}
if(!empty($cache['product']) && $cache['product']!="all"){
    $query->condition("tbl_product_price.nid",$cache['product']);
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
//if(isset($cache['b_status']) && $cache['b_status']!="all"){
    $query->condition("tbl_booking_product.status",2);
//}

$result = $query->execute()->fetchAll();
//_print_r($result);
$Total = 0;
?>
<div class="page-booking-manager">
    <div class="filter-form">
        <?php
        $cassiopeia_admin_manager_user_filter_form = drupal_get_form("cassiopeia_admin_manager_booking_filter_form",$cache);
        if(!empty($cassiopeia_admin_manager_user_filter_form)){
            $cassiopeia_admin_manager_user_filter_form = drupal_render($cassiopeia_admin_manager_user_filter_form);
            echo $cassiopeia_admin_manager_user_filter_form;
        }
        ?>
    </div>
    <table class="table table-hover table-striped ">
        <thead>
        <tr>
            <th>Code</th>
            <th>Ngày tạo</th>
            <th>Tài khoản</th>
            <th>Gói sản phẩm</th>
            <th>Ngày cập nhật</th>
            <th>Quà tặng</th>
            <th class="text-right">Tổng giá</th>
        </tr>
        </thead>
        <tbody>
        <?php if(!empty($result)): ?>
            <?php foreach($result as $item): ?>
                <?php
//            _print_r($result);
                $_user = user_load($item->uid);
//                $Total+=$item->price;
                ?>
                <?php $_node = node_load($item->nid); ?>
                <tr>
                    <td><?php echo($item->code); ?></td>
                    <td><?php echo(date("d/m/Y H:i",$item->created)); ?></td>
                    <td>
                        <?php
                        if(!empty($_user->uid)){
                            echo(l($_user->mail,"user/".$_user->uid."/edit"));
                        }else{
                            echo "Tài khoản đã bị xóa!";
                        }
                        ?>
                    </td>
                    <td>
                        <?php if($item->nid==AGENCY): ?>
                            <?php echo(l($_node->title,"node/".$_node->nid,array("html"=>TRUE))); ?>
                        <?php else: ?>
                            <?php echo(l($_node->title,"node/".$_node->nid,array("html"=>TRUE))); ?> <b>x <?php echo $item->month; ?> tháng</b>
                        <?php endif; ?>
                    </td>
                    <td><?php if(!empty($item->updated)) echo date("d-m-Y",$item->updated); ?></td>
                    <td>
                        <input disabled readonly <?php if(!empty($item->gift)) echo "checked"; ?> type="checkbox" value="<?php echo $item->gift; ?>" data-id="<?php echo $item->id; ?>" >
                    </td>
                    <td style="text-align: right;">
                        <?php if($item->gift): ?>
                            0 đ
                        <?php else: ?>
                            <?php if($item->nid==AGENCY): $Total+=$_node->field_price['und'][0]['value']; ?>
                                <?php echo number_format($_node->field_price['und'][0]['value'],0,",","."); ?> đ
                            <?php else:  $Total+=($item->price*$item->month);?>
                                <?php echo number_format($item->price*$item->month,0,",","."); ?> đ
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td >
                        <?php if($item->status!=2): ?>
                            <button class="btn btn-danger btn-booking-delete" data-code="<?php echo($item->code); ?>"><span class="fa fa-trash"></span></button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        <tr>
            <td colspan="5" style="color: #1EB04B; font-size: 20px; line-height: 26px; font-weight: bold;">Tổng giá:</td>
            <td align="right" style="color: #1EB04B; font-size: 20px; line-height: 26px; font-weight: bold;"><b><?php echo number_format($Total,0,",","."); ?> đ</b></td>
        </tr>
        </tbody>
    </table>
</div>