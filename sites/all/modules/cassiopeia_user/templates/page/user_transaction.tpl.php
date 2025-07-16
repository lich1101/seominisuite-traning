<?php
global $user;
drupal_add_js(drupal_get_path("module","cassiopeia_user")."/js/user_transaction.js");
?>
<div class="page-user-transaction">
    <div class="page-title mb-24">
        <h1>Lịch sử giao dịch</h1>
    </div>
    <div class="page-body">
        <?php
        $cache = !empty($_REQUEST['data'])?$_REQUEST['data']:array();
        $active = 0;
        $status = isset($_REQUEST['status'])?$_REQUEST['status']:-1;
        if($status==0){
            $active=1;
        }
       try{
           $query = db_select("tbl_booking_product","tbl_booking_product");
           $query->fields("tbl_booking_product",array("id","created"));
           $query->condition("tbl_booking_product.uid",$user->uid);
           $query->addExpression("CASE WHEN tbl_booking_product.uid>0 THEN 'product' ELSE 'product' END","type");

//
           $domain_query = db_select("cassiopeia_guest_post_domain_change_booking","cassiopeia_guest_post_domain_change_booking");
           $domain_query->fields("cassiopeia_guest_post_domain_change_booking",array("id","created"));
           $domain_query->condition("uid",$user->uid);
           $domain_query->addExpression("CASE WHEN cassiopeia_guest_post_domain_change_booking.uid>0 THEN 'domain' ELSE 'domain' END","type");
           $quer1y = Database::getConnection()
               ->select($domain_query->union($query))
               ->fields(NULL, array("id","created","type"));
           $quer1y->orderBy("created","DESC");
           $result = $quer1y->execute()->fetchAll();
       }catch (Exception $e){
            print_r($e);
       }
//        print_r($result);
        ?>
        <div class="page-booking-manager bg-white padding-24">
            <table class="table table-striped table-div-responsive table-type-2 table-responsive">
                <thead>
                <tr>
                    <th class="text-left">Mã đơn hàng</th>
                    <th>Ngày tạo đơn hàng</th>
                    <th class="text-left">Tên đơn hàng</th>
                    <th>Giá trị</th>
                    <th>Tình trạng</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php  if(!empty($result)): ?>
                    <?php foreach($result as $item): ?>
                        <?php
                        if($item->type=="domain"){
                            $booking = cassiopeia_guest_post_domain_change_booking_load_by_id($item->id);
                        }else{
                            $booking = tbl_booking_product_load($item->id);
                        }
                        $_user = user_load($booking->uid);
                        ?>
                        <?php  if($item->type=="domain"): ?>
                            <tr>
                                <td class="text-left"><?php echo $booking->booking_code; ?></td>
                                <td><?php echo date("d/m/Y",$booking->created); ?></td>
                                <td class="text-left"><b>Lượt đổi domain x <?php echo $booking->quantity; ?></b></td>
                                <td><?php echo number_format($booking->total_price,0,",","."); ?>VNĐ</td>
                                <td>
                                    <?php
                                    switch ($booking->status){
                                        case 0 : echo "<span class='radius-30 status-warning'>Chưa thanh toán</span>"; break;
                                        case 1 : echo "<span class='radius-30 status-success'>Đã thanh toán</span>"; break;
                                        case 2 : echo "<span class='radius-30 status-danger'>Đã hủy</span>"; break;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if($booking->status==0): ?>
                                        <a class="btn-send-booking-mail"  data-type="domain" data-id="<?php echo $booking->id; ?>" href="/guest-post/domain/change/booking/payment/<?php echo $booking->booking_code; ?>">Thanh toán lại <i class="fa fa-external-link" aria-hidden="true"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $_node = node_load($booking->nid); ?>
                            <?php if(!empty($_node) && !empty($_user->uid)): ?>
                                <tr>
                                    <td class="text-left"><?php echo($booking->code); ?></td>
                                    <td><?php echo(date("d/m/Y H:i",$booking->created)); ?></td>
                                    <td class="text-left"><?php echo $_node->title; ?>
                                        <?php if($_node->nid!=AGENCY): ?>
                                            x <?php echo $booking->month; ?> tháng
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <b>
                                            <?php if($booking->gift): ?>
                                                0 đ
                                            <?php else: ?>
                                                <?php if($booking->nid==AGENCY):  ?>
                                                    <?php echo number_format($_node->field_price['und'][0]['value'],0,",","."); ?> VNĐ
                                                <?php else:  ?>
                                                    <?php echo number_format($booking->price*$booking->month,0,",","."); ?> VNĐ
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </b>
                                    </td>
                                    <td>
                                        <?php
                                        switch ($booking->status){
                                            case 0 : echo "<span class='radius-30 status-warning'>Chưa thanh toán</span>"; break;
                                            case 1 : echo "<span class='radius-30 status-success'>Đã thanh toán</span>"; break;
                                            case 2 : echo "<span class='radius-30 status-danger'>Không thành công</span>"; break;
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if($booking->status==0): ?>
                                            <a class="btn-send-booking-mail" data-type="product" data-id="<?php echo $booking->id; ?>" href="/booking/complete/<?php echo $booking->code; ?>">Thanh toán lại <i class="fa fa-external-link" aria-hidden="true"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td>...</td>
                        <td>...</td>
                        <td>...</td>
                        <td>...</td>
                        <td>...</td>
                        <td>...</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="hidden">
    <?php
    $cassiopeia_send_booking_mail_form = drupal_get_form("cassiopeia_send_booking_mail_form");
    if(!empty($cassiopeia_send_booking_mail_form)){
        $cassiopeia_send_booking_mail_form = drupal_render($cassiopeia_send_booking_mail_form);
        echo $cassiopeia_send_booking_mail_form;
    }
    ?>
</div>