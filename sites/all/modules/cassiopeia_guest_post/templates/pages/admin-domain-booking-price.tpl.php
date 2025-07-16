<?php
$query = db_select("cassiopeia_guest_post_domain_change_booking_price","cassiopeia_guest_post_domain_change_booking_price");
$query->fields("cassiopeia_guest_post_domain_change_booking_price");
$query->orderBy("cassiopeia_guest_post_domain_change_booking_price.weight","ASC");
$result = $query->execute()->fetchAll();
?>
<div class="mb-24">
    <a href="/admin/manager/guest-post/domain/booking/price/add?destination=admin/manager/guest-post/domain/booking/price" class="btn btn-primary">Thêm mới</a>
    <a href="/admin/manager/guest-post/domain/booking/price/config?destination=admin/manager/guest-post/domain/booking/price" class="btn btn-success">Config</a>
</div>
<table class="table table-hover table-stripped">
    <thead>
        <tr>
            <th>Weight</th>
            <th>Số lượt</th>
            <th>Giá / lượt</th>
            <th>Ngày tạo</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($result)): ?>
            <?php foreach($result as $item): ?>
                <tr>
                    <td><?php echo $item->weight; ?></td>
                    <td><?php echo $item->quantity; ?></td>
                    <td><?php echo number_format($item->price,0,",","."); ?> đ</td>
                    <td><?php echo date("d/m/Y",$item->created); ?></td>
                    <td>
                        <a href="/admin/manager/guest-post/domain/booking/price/<?php echo $item->id; ?>/edit?destination=admin/manager/guest-post/domain/booking/price" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                        <a href="/admin/manager/guest-post/domain/booking/price/<?php echo $item->id; ?>/delete?destination=admin/manager/guest-post/domain/booking/price" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>