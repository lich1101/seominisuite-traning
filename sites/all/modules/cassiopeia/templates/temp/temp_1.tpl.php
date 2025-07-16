<?php
//    print_r($_REQUEST);
try{
    $query = db_select("tbl_transaction","tbl_transaction");
    $query -> fields("tbl_transaction");
    $query -> orderBy("created","DESC");
    $query->join("node","tbl_booking","tbl_booking.nid = tbl_transaction.booking_id");
    $query->condition("tbl_booking.type","teacher_booking","=");
    $query->fields("tbl_booking",array("title","nid"));
    $query->join("field_data_field_booking_teacher_teacher","tbl_data_teacher","tbl_data_teacher.entity_id = tbl_booking.nid");
    $query->fields("tbl_data_teacher");
    $query->join("users","tbl_user","tbl_user.uid = tbl_data_teacher.field_booking_teacher_teacher_target_id");
    $query->fields("tbl_user");
//        $query->fields()
    if(!empty($_REQUEST['data']['order_code'])){
        $query->condition('tbl_transaction.code',"%".$_REQUEST['data']['order_code']."%","LIKE");
    }
    if(!empty($_REQUEST['data']['order_student'])){
        $query->condition('tbl_booking.title',"%".$_REQUEST['data']['order_student']."%","LIKE");
    }
    if(!empty($_REQUEST['data']['order_teacher'])){
        $query->condition('tbl_booking.title',"%".$_REQUEST['data']['order_teacher']."%","LIKE");
    }
//        $query2 = db_select()
    $result = $query -> execute() -> fetchAll();
    print_r($result);
}catch (Exception $e){
    print_r($e);
}
?>
<div class="order-filter-form">
    <?php
    $cassiopeia_order_filter_form  = drupal_get_form("cassiopeia_order_filter_form");
    if(!empty($cassiopeia_order_filter_form)){
        $cassiopeia_order_filter_form = drupal_render($cassiopeia_order_filter_form);
        print($cassiopeia_order_filter_form);
    }
    ?>
</div>
<table class="table table-hovered">
    <thead>
    <tr>
        <th>STT</th>
        <th>Mã đơn hàng</th>
        <th>Học viên</th>
        <th>Giảng viên</th>
        <th>Khóa học</th>
        <th>Tình trạng</th>
        <th>Ngày tạo</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php if(!empty($result)): ?>
        <?php $stt=1; ?>
        <?php foreach($result as $item): ?>
            <?php
            $booking = node_load($item->booking_id);
            $student = user_load($booking->field_booking_teacher_uid['und'][0]['target_id']);
            $teacher = user_load($booking->field_booking_teacher_teacher	['und'][0]['target_id']);
            $course  = node_load($item->class_id);
            $status = $item->status == 1 ? "Đã thanh toán" : "Chưa thanht toán";
            ?>
            <tr>
                <td><?php print($stt); ?></td>
                <td></td>
                <td><?php print(!empty($student->field_account_full_name['und'][0]['value'])?$student->field_account_full_name['und'][0]['value']:$student->name); ?></td>
                <td><?php print(!empty($teacher->field_account_full_name['und'][0]['value'])?$teacher->field_account_full_name['und'][0]['value']:$teacher->name); ?></td>
                <td><?php print($course->title); ?></td>
                <td><?php print($status); ?></td>
                <td><?php print(date("d/m/Y",$item->created)); ?></td>
                <td>
                    <?php if($item->status==0): ?>
                        <a title="Xác nhận" class="btn btn-primary" href="/admin/manager/order/confirm/<?php print($item->id); ?>"><span class="fa fa-check"></span></a>
                    <?php endif; ?>
                    <a title="Hủy" class="btn btn-warning" href="/admin/manager/order/cancel/<?php print($item->id); ?>"><span class="fa fa-times"></span></a>
                    <a title="Xóa" class="btn btn-danger" href="/admin/manager/order/delete/<?php print($item->id); ?>"><span class="fa fa-trash"></span></a>
                </td>
            </tr>
            <?php $stt++; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>