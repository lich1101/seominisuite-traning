<?php
//echo 123;
//die;
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
$query = db_select("cassiopeia_guest_post_complain","cassiopeia_guest_post_complain");
$query->fields("cassiopeia_guest_post_complain");

$query->orderBy("cassiopeia_guest_post_complain.created","DESC");
if(!empty($cache['keyword'])){
    $query->condition("cassiopeia_guest_post_complain.booking_code","%".$cache['code']."%","LIKE");
}
if(!empty($cache['from_date'])){
    $query->condition("cassiopeia_guest_post_complain.created",strtotime(date("d-m-Y 00:00:00",strtotime($cache['from_date']))),">=");
}
if(!empty($cache['to_date'])){
    $query->condition("cassiopeia_guest_post_complain.created",strtotime(date("d-m-Y 23:59:59",strtotime($cache['to_date']))),"<=");
}
if(!empty($cache['account'])&&$cache['account']!="all"){
    $query->condition("cassiopeia_guest_post_complain.uid",$cache['account']);
}
if(isset($cache['status'])&&$cache['status']!="all"){
    $query->condition("cassiopeia_guest_post_complain.status",$cache['status']);
}
//$query->range(0,50);
$result = $query->execute()->fetchAll();
//_print_r($result);
//die;
?>
<div class="custom-filter mb-10">
<!--    --><?php
//    $cassiopeia_guest_post_complain_filter_form = drupal_get_form("cassiopeia_guest_post_complain_filter_form",$cache);
//    if(!empty($cassiopeia_guest_post_complain_filter_form)){
//        $cassiopeia_guest_post_complain_filter_form = drupal_render($cassiopeia_guest_post_complain_filter_form);
//        echo $cassiopeia_guest_post_complain_filter_form;
//    }
//    ?>
</div>
<table class="table table-hover table-stripped">
    <thead>
    <tr>
        <th>Mã</th>
        <th>Ngày tạo</th>
        <th>Bài viết</th>
        <th>URL guest post</th>
        <th>Vấn đề khiếu nại</th>
        <th>Người khiếu nại</th>
        <th>Người xử lý</th>
        <th>Tình trạng</th>
        <th>Thao tác</th>
    </tr>
    </thead>
    <tbody>
    <?php if(!empty($result)): ?>
        <?php foreach($result as $item): ?>
            <?php
            $account = user_load($item->uid);
            $article = !empty($item->aid)?cassiopeia_guest_post_article_load($item->aid):null;
            $account_role = $item->target==1?"Người đăng":"Chủ website";
            ?>
            <?php $tran_user = !empty($item->tran_user)?user_load($item->tran_user):null; ?>
            <tr>
                <td><?php echo $item->code; ?></td>
                <td><?php echo date("d/m/Y",$item->created); ?></td>
                <td><?php echo !empty($article)?l($article->title,"/guest/post/article/".$article->id."/view",array("html"=>TRUE)):"Bài viết đã bị xóa!"; ?></td>
                <td>
                    <a href="<?php echo !empty($article->url_guest_post)?$article->url_guest_post:""; ?>" target="_blank"><?php echo !empty($article->url_guest_post)?$article->url_guest_post:""; ?></a>
                </td>
                <td><?php echo $item->title; ?></td>
                <td>
                    <div>
                        <?php echo !empty($account)?$account->mail:""; ?>
                    </div>
                    <i class="color-grey">
                        <?php echo $account_role; ?>
                    </i>
                </td>
                <td>
                    <?php echo !empty($tran_user)?$tran_user->mail:""; ?>
                </td>
                <td>
                    <?php
                    switch ($item->status){
                        case 0 : echo "Đang chờ"; break;
                        case 1 : echo "Thành công"; break;
                        case 2 : echo "Đã hủy"; break;
                        case 3 : echo "Thất bại"; break;
                    }
                    ?>
                </td>
                <td>
                    <?php if($item->status!=1): ?>
                        <?php print(l('<span class="icon"><i class="fa fa-edit" aria-hidden="true"></i></span>', 'admin/manager/guest-post/complain/'.$item->id.'/edit/nojs',  array('html'=>true, 'attributes' => array('class' => 'ctools-use-modal btn btn-primary'))));?>
<!--                        <a href="/admin/manager/guest-post/domain/change/booking/--><?php //echo $item->booking_code; ?><!--/delete?destination=admin/manager/guest-post/domain/change/booking" class="btn btn-danger"><i class="fa fa-trash"></i></a>-->
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
