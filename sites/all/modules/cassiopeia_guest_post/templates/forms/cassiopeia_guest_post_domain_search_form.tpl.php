<?php
$limit = $form['#limit'];
$page = $form['#page'];
$start = ($page-1)*$limit;
$min = $start+1;
$max = $start+$limit;
$domains = $form['#domains'];
$total_result = $form['#total_result'];
if($max>$total_result->total){
    $max = $total_result->total;
}
ctools_include('modal');
ctools_modal_add_js();
global $user;
$cache = !empty($form['#cache'])?$form['#cache']:null;
try{
    $domain_change = cassiopeia_guest_post_domain_change_load($user->uid);
    $alert = "success";
    if($domain_change->remaining>1){
        $alert = "success";
    }elseif($domain_change->remaining==1){
        $alert = "warning";
    }else{
        $alert = "danger";
    }

}catch (Exception $e){

}

?>
<div class="page-notify <?php echo $alert; ?>">
    <?php if($domain_change->remaining==0): ?>
        Thông báo: <b>Bạn đã hết lượt thay đổi website</b> <a href="/guest-post/domain/change/booking">Mua thêm</a>
    <?php else: ?>
        Thông báo: <b>Bạn còn <span><?php echo $domain_change->remaining; ?></span> lượt thay đổi website</b> <a href="/guest-post/domain/change/booking">Mua thêm</a>
    <?php endif; ?>
</div>
<div class="page-notify warning" id="message-model">
    <button class="close">&times;</button>
    Bạn chỉ được chèn Backlink vào bài GuestPost từ <h4><b>Danh sách website mà bạn đăng ký mới nhất!</b></h4>
</div>
<div class="page-header">
    <div class="page-title">
        <h1 title="">
            <span style="max-width: unset;">Website bạn muốn đặt backlink</span>
        </h1>
        <?php if($domain_change->remaining>0): ?>
            <?php print(l('Thay đổi website', 'guest-post/domain/change/form/nojs',  array('html'=>true, 'attributes' => array('class' => 'btn-green btn-icon-before ml-10 ctools-use-modal btn btn-primary'))));?>
        <?php endif; ?>
    </div>
    <div class="page-search">
        <div class="input-group-search d-flex">
            <?php echo drupal_render($form['keyword']); ?>
            <button type="button" class="btn-clear-text"><span class="fa fa-times" aria-hidden="true"></span></button>
            <?php echo drupal_render($form['search']); ?>
        </div>
    </div>
</div>

<div class="page-container">
    <div class="page-main result guest-post">
        <div class="t-body">
            <table class="table table-striped table-div-responsive table-type-2 table-responsive">
                <thead>
                <tr>
                    <th class="w-3">
                        STT
                    </th>
                    <th class="sort text-left" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="domain">Domain</th>
                    <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="article_count">Số bài viết đã đặt backlink</th>
                    <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="backlink_count">Số backlink đã đặt</th>
                    <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="status">Tình trạng</th>
                    <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="changed">Ngày cập nhật</th>
                </tr>
                <tr class="">
                    <th colspan="10" class="th-progress">
                        <div class="progress-bar-block modal-custom">
                            <progress id="file" value="0" max="2180"> 32% </progress>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php if(!empty($domains)): $stt=1;?>
                    <?php foreach($domains as $domain):?>
                        <?php  ?>
                        <tr>
                            <td><?php echo $stt; ?></td>
                            <td class=" text-left"><?php echo $domain->domain; ?></td>
                            <td><?php echo !empty($domain->article_count)?$domain->article_count:0; ?></td>
                            <td><?php echo !empty($domain->backlink_count)?$domain->backlink_count:0; ?></td>
                            <td class="d-flex justify-center">
                                <div class="<?php echo $domain->status==1?"active":"in-active"; ?>">
                                    <span></span>
                                    <?php echo $domain->status==1?"Active":"Inactive"; ?>
                                </div>
                            </td>
                            <td><?php echo date("d/m/Y",$domain->changed); ?></td>
                        </tr>
                        <?php $stt++; endforeach; ?>
                <?php else: ?>
                    <?php for($i=1;$i<=5;$i++): ?>
                        <tr>
                            <td width="50px;" data-title="Stt">...</td>
                            <td data-key="title" data-title="title" class="text-green text-left title">...</td>
                            <td data-key="totalDomain">...</td>
                            <td data-value="61" data-key="googleIndex">...</td>
                            <td data-value="100" data-key="dofollow">...</td>
                            <td data-value="1648399487" data-key="created">...</td>
                        </tr>
                    <?php endfor; ?>
                <?php endif;?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="clickon-pagination">
        <div class="item-per-page">
            <span>Số bài mỗi trang:</span>
            <?php echo drupal_render($form['num_per_page']); ?>
        </div>
        <div class="current-page">
            <?php echo ($min); ?>-<?php echo $max; ?> of <?php echo $total_result->total; ?>
        </div>
        <div class="nav-buttons">
            <?php echo drupal_render($form['prev']); ?>
            <?php echo drupal_render($form['next']); ?>
        </div>
    </div>
</div>
<div class="hidden">
    <?php echo drupal_render_children($form); ?>
</div>