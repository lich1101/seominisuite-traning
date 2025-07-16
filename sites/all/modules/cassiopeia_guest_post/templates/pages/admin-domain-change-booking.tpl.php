<?php
ctools_include('modal');
ctools_modal_add_js();
$cache = !empty($_REQUEST['data'])?$_REQUEST['data']:array();
$temp = array();
if(!empty($cache)){
    foreach($cache as $key => $item){
        $temp[$key] = trim($item);
    }
}
$cache = $temp;
$query = db_select("cassiopeia_guest_post_domain_change_booking","cassiopeia_guest_post_domain_change_booking");
$query->fields("cassiopeia_guest_post_domain_change_booking");

if(!empty($cache['code'])){
    $query->condition("cassiopeia_guest_post_domain_change_booking.booking_code","%".$cache['code']."%","LIKE");
}
if(!empty($cache['from_date'])){
    $query->condition("cassiopeia_guest_post_domain_change_booking.created",strtotime(date("d-m-Y 00:00:00",strtotime($cache['from_date']))),">=");
}
if(!empty($cache['to_date'])){
    $query->condition("cassiopeia_guest_post_domain_change_booking.created",strtotime(date("d-m-Y 23:59:59",strtotime($cache['to_date']))),"<=");
}
if(!empty($cache['account'])&&$cache['account']!="all"){
    $query->condition("cassiopeia_guest_post_domain_change_booking.uid",$cache['account']);
}
if(isset($cache['status'])&&$cache['status']!="all"){
    $query->condition("cassiopeia_guest_post_domain_change_booking.status",$cache['status']);
}
$query->orderBy("changed","DESC");
$result = $query->execute()->fetchAll();
?>
<div class="custom-filter mb-10">
    <?php
        $cassiopeia_guest_post_domain_change_booking_filter_form = drupal_get_form("cassiopeia_guest_post_domain_change_booking_filter_form",$cache);
        if(!empty($cassiopeia_guest_post_domain_change_booking_filter_form)){
            $cassiopeia_guest_post_domain_change_booking_filter_form = drupal_render($cassiopeia_guest_post_domain_change_booking_filter_form);
            echo $cassiopeia_guest_post_domain_change_booking_filter_form;
        }
    ?>
</div>
<table class="table table-hover table-stripped">
    <thead>
        <tr>
            <th>Ngày tạo</th>
            <th>Mã DH</th>
            <th>Tài khoản</th>
            <th>Số lượng</th>
            <th>Tổng tiền</th>
            <th>Tình trạng</th>
            <th>Người duyệt</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($result)): ?>
            <?php foreach($result as $item): ?>
                <?php $account = user_load($item->uid); ?>
                <?php $tran_user = !empty($item->tran_user)?user_load($item->tran_user):null; ?>
                <tr>
                    <td><?php echo date("d/m/Y",$item->created); ?></td>
                    <td><?php echo $item->booking_code; ?></td>
                    <td><?php echo !empty($account)?$account->mail:""; ?></td>
                    <td><?php echo $item->quantity; ?></td>
                    <td><?php echo number_format($item->total_price,0,",","."); ?>đ</td>
                    <td>
                        <?php
                            switch ($item->status){
                                case 0 : echo "Đang chờ"; break;
                                case 1 : echo "Hoàn thành"; break;
                                case 2 : echo "Đã hủy"; break;
                            }
                        ?>
                    </td>
                    <td>
                        <?php echo !empty($tran_user)?$tran_user->mail:""; ?>
                    </td>
                    <td>
                        <?php if($item->status!=1): ?>
                            <?php print(l('<span class="icon"><i class="fa fa-edit" aria-hidden="true"></i></span>', 'admin/manager/guest-post/domain/change/booking/'.$item->booking_code.'/edit/nojs',  array('html'=>true, 'attributes' => array('class' => 'ctools-use-modal btn btn-primary'))));?>
                            <a href="/admin/manager/guest-post/domain/change/booking/<?php echo $item->booking_code; ?>/delete?destination=admin/manager/guest-post/domain/change/booking" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
