<?php
/**
 * Created by PhpStorm.
 * User: VDP
 * Date: 19/04/2017
 * Time: 11:06 PM
 */
$query = db_select("tbl_booking_product","tbl_booking_product");
$query->fields("tbl_booking_product");
$query->orderBy("tbl_booking_product.created","DESC");
$query->condition("tbl_booking_product.status",2);
$query->addExpression("SUM(CASE WHEN nid=158033 THEN tbl_booking_product.price ELSE tbl_booking_product.price*tbl_booking_product.month END)","TotalPrice");
$or = db_or();
$or->condition("tbl_booking_product.gift",0);
$or->condition("tbl_booking_product.gift",null);
$query->condition($or);
$query->condition($or);
$TotalRevenue = $query->execute()->fetchObject();

$query = db_select("tbl_booking_product","tbl_booking_product");
$query->fields("tbl_booking_product");
$query->orderBy("tbl_booking_product.created","DESC");
$or = db_or();
$or->condition("tbl_booking_product.gift",0);
$or->condition("tbl_booking_product.gift",null);
$query->condition($or);
$query->addExpression("COUNT(id)","TotalCount");
$TotalOrder = $query->execute()->fetchObject();

$query = db_select("tbl_booking_product","tbl_booking_product");
$query->fields("tbl_booking_product");
$query->orderBy("tbl_booking_product.created","DESC");
$query->condition("tbl_booking_product.created",strtotime(date("d-m-Y 00:00:00",REQUEST_TIME)),">=");
$or = db_or();
$or->condition("tbl_booking_product.gift",0);
$or->condition("tbl_booking_product.gift",null);
$query->condition($or);
$query->addExpression("COUNT(id)","TotalCount");
$NewOrder = $query->execute()->fetchObject();

$query = db_select("users","tbl_user");
$query->fields("tbl_user");
//$query->groupBy("tbl_role.uid");
$query->join("users_roles","tbl_role","tbl_role.uid=tbl_user.uid");
$query->orderBy("tbl_user.created","DESC");
$query->addExpression("COUNT(distinct tbl_user.uid)","TotalCount");
$TotalUser = $query->execute()->fetchObject();

$query = db_select("users","tbl_user");
$query->fields("tbl_user");
$query->join("users_roles","tbl_role","tbl_role.uid=tbl_user.uid");
$query->orderBy("tbl_user.created","DESC");
$query->addExpression("COUNT(distinct tbl_user.uid)","TotalCount");
$query->condition("created",strtotime(date("d-m-Y 00:00:00",REQUEST_TIME)),">=");
$NewUser = $query->execute()->fetchObject();

$query = db_select("tbl_available_product","tbl_available_product");
$query->fields("tbl_available_product");
$query->condition("expired",array(REQUEST_TIME,REQUEST_TIME+3*86400),"BETWEEN");
$query->addExpression("COUNT(id)","TotalCount");
$ExpiredUser = $query->execute()->fetchObject();
?>

<div class="row">
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3><?php echo number_format($TotalRevenue->TotalPrice,0,",","."); ?> đ</h3>
                <p>Doanh thu</p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="/admin/manager/report" class="small-box-footer">Chi tiết <i
                        class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green">
            <div class="inner">
                <h3><?php echo $NewOrder->TotalCount; ?>/<?php echo $TotalOrder->TotalCount; ?></h3>

                <p>Đơn hàng</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="/admin/manager/booking" class="small-box-footer">Chi tiết <i
                        class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3><?php echo $NewUser->TotalCount; ?>/<?php echo $TotalUser->TotalCount; ?></h3>

                <p>Tài khoản</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
            <a href="/admin/manager/user" class="small-box-footer">Chi tiết <i
                        class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-red">
            <div class="inner">
                <h3><?php echo $ExpiredUser->TotalCount; ?></h3>

                <p>Tài khoản sắp hết hạn</p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
                <a href="/admin/manager/user?expired=1" class="small-box-footer">Chi tiết <i
                            class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
</div>


<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="text-center">
                            <strong>Nội dung mới</strong>
                        </p>
                        <table class="table table-bordered table-hover dataTable">
                            <thead>
                            <tr>
                                <th>Tiêu đề</th>
                                <th>Kiểu</th>
                                <th>Nhày tạo</th>
                                <th style="text-align: center;">Sửa</th>
                                <th style="text-align: center;">Xóa</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($new_nodes)): ?>
                            <?php foreach ($new_nodes as $key => $value): ?>
                            <tr>
                                <td><?php print($value->title); ?></td>
                                <td><?php  $node_types = node_type_get_types(); print($node_types[$value->type]->name);  ?></td>
                                <td><?php print(date("d/m/Y",$value->created)); ?></td>
                                <td style="text-align: center;"><a style="color: black" href="<?php print('/node/'.$value->nid.'/edit'); ?>"><i class="fa fa-edit"></i></a></td>
                                <td style="text-align: center;"><a style="color: black" href="<?php print('/node/'.$value->nid.'/delete'); ?>"><i class="fa fa-trash"></i></a></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                        <!-- /.chart-responsive -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-4">



                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- ./box-body -->
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>




