<?php
ctools_include('modal');
ctools_modal_add_js();
$cache = !empty($_REQUEST['data'])?$_REQUEST['data']:array();
try{
    $query = db_select("cassiopeia_guest_post_order_content","cassiopeia_guest_post_order_content");
    $query->fields("cassiopeia_guest_post_order_content");
    $query->orderBy("cassiopeia_guest_post_order_content.created","DESC");

    if(!empty($cache['title'])){
        $query->condition("cassiopeia_guest_post_order_content.title","%".trim($cache['title'])."%","LIKE");
    }
    if(!empty($cache['website'])){
        $query->condition("cassiopeia_guest_post_order_content.website","%".trim($cache['website'])."%","LIKE");
    }
    if(!empty($cache['tel'])){
        $query->condition("cassiopeia_guest_post_order_content.tel","%".trim($cache['tel'])."%","LIKE");
    }
    if(isset($cache['status'])&&$cache['status']!="all"){
        $query->condition("cassiopeia_guest_post_order_content.status",$cache['status']);
    }
    $nodes = $query->execute()->fetchAll();
}catch (Exception $e){

}
?>
<div class="page-admin-order-content">
    <div class="custom-filter mb-24">
        <?php
            $cassiopeia_guest_post_order_content_filter_form = drupal_get_form("cassiopeia_guest_post_order_content_filter_form",$cache);
            if(!empty($cassiopeia_guest_post_order_content_filter_form)){
                $cassiopeia_guest_post_order_content_filter_form = drupal_render($cassiopeia_guest_post_order_content_filter_form);
                echo $cassiopeia_guest_post_order_content_filter_form;
            }
        ?>
    </div>
    <table class="table table-stripped">
        <thead>
            <tr>
                <th>Ngày tạo</th>
                <th>Tên đơn vị</th>
                <th>Website/profile</th>
                <th>Giá niêm yết</th>
                <th>Khuyến mại</th>
                <th>Giá ưu đãi</th>
                <th>Số điện thoại</th>
                <th>Email</th>
                <th>Tình trạng</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($nodes)): ?>
                <?php foreach($nodes as $node): ?>
                    <tr>
                        <td><?php echo date("d/m/Y",$node->created); ?></td>
                        <td><?php echo $node->title; ?></td>
                        <td><?php echo $node->website; ?></td>
                        <td><b><?php echo number_format($node->price,0,",","."); ?>đ</b>/1000 words</td>
                        <td><?php echo $node->discount; ?>%</td>
                        <td><b><?php echo number_format($node->promotion_price,0,",","."); ?>đ</b>/1000 words</td>
                        <td><?php echo $node->tel; ?></td>
                        <td><?php echo $node->email; ?></td>
                        <td>
                            <?php
                            switch ($node->status){
                                case 0 : echo "<span class='radius-30 status-warning'>Chờ</span>"; break;
                                case 1 : echo "<span class='radius-30 status-success'>Thành công</span>"; break;
                                case 2 : echo "<span class='radius-30 status-danger'>Không thành công</span>"; break;
                            }
                            ?>
                        </td>
                        <td>
                            <?php print(l('<i class="fa fa-edit"></i>', 'admin/manager/guest-post/order-content/'.$node->id.'/edit/nojs',  array('html'=>true, 'attributes' => array('class' => 'ctools-use-modal btn btn-icon-before no-padding '))));?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>