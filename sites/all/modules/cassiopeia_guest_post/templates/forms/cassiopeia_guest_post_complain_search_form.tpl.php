<?php
ctools_include('modal');
ctools_modal_add_js();
global $user;
$limit = $form['#limit'];
$page = $form['#page'];
$start = ($page-1)*$limit;
$min = $start+1;
$max = $start+$limit;
$nodes = $form['#nodes'];
$total_result = $form['#total_result'];
if($max>$total_result->total){
    $max = $total_result->total;
}
?>
<div class="page-header">
    <div class="page-title">
        <h1 title="">
            <span style="max-width: unset;">Quản lý khiếu nại</span>
        </h1>
        <?php echo drupal_render($form['complain_add']); ?>
<!--        --><?php //print(l('<i class="fa fa-plus"></i> Khiếu nại', 'guest-post/complain/add',  array('html'=>true, 'attributes' => array('class' => 'btn btn-green btn-icon-before ml-10'))));?>
        <!--        --><?php //print(l('<button class="btn btn-green"><i class="fa-regular fa-bookmark"></i> Quản lý tags</button>', 'guest-post/export',  array('html'=>true, 'attributes' => array('class' => 'btn btn-icon-before no-padding ml-10'))));?>
    </div>
    <div class="page-search">
        <div class="input-group-search">
            <?php echo drupal_render($form['keyword']); ?>
            <button type="button" class="btn-clear-text"><span class="fa fa-times" aria-hidden="true"></span></button>
            <?php echo drupal_render($form['search']); ?>
        </div>
    </div>
</div>
<div class="author">
    <?php echo drupal_render($form['author']); ?>
</div>
<div class="page-container">
    <div class="page-search d-flex justify-between align-center mb-24">
        <div class="left-block d-flex align-center">
           <h2>
               <b class="mr-24">
                   <?php echo $form['author']['#value']==1?"Danh sách khiếu nại":"Danh sách bị khiếu nại"; ?>
               </b>
           </h2>
        </div>
        <div class="right-block">
            <div class="d-flex">
                <?php echo drupal_render($form['status']); ?>
                <?php echo drupal_render($form['search_2']); ?>
            </div>
        </div>
    </div>
    <div class="page-main result">
        <div class="t-body">
            <table class="table table-striped table-div-responsive table-type-2 table-responsive">
                <thead>
                    <tr>
                        <th class="w-3">
                            STT
                        </th>
                        <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="title">Bài viết</th>
                        <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="title">Vấn đề khiếu nại</th>
                        <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="title"><?php echo $form['author']['#value']==1?"Người bị khiếu nại":"Người khiếu nại"; ?></th>
                        <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="title">Tình trạng</th>
                        <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="title">Ngày tạo</th>
                        <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="title">Điểm thay đổi</th>
                        <?php if($form['author']['#value']==1): // khiếu nại ?>
                            <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="title">Đăng lại</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if(!empty($nodes)): $stt=1;?>
                    <?php foreach($nodes as $node): ?>
                        <?php
                        $article = cassiopeia_guest_post_article_load($node->aid);
                        $account = $form['author']['#value']==1?user_load($node->target_id):user_load($node->uid);

                        $account_role = $node->target==1?"Người đăng":"Chủ website";
                        ?>
                        <tr>
                            <td><?php echo $stt; ?></td>
                            <td class="text-green text-left">
                                <div class="ellipsis-2">
                                    <?php echo $article->title; ?>
                                </div>
                            </td>
                            <td class="text-green text-left"><a href="/guest-post/complain/<?php echo $node->id; ?>/view"><?php echo $node->title; ?></a></td>
                            <td class="text-left">
                                <div>
                                    <?php echo !empty($account)?!empty($account->field_full_name['und'][0]['value'])?$account->field_full_name['und'][0]['value']:"-":""; ?>
                                </div>
                                <div color-grey>
                                    <?php echo $account_role; ?>
                                </div>
                            </td>
                            <td>
                                <?php if($form['author']['#value']==1): // khiếu nại ?>
                                    <span class="complain-status complain-status-<?php echo $node->status; ?>">
                                    <?php
                                    switch ($node->status){
                                        case 0 : echo "Admin đang kiểm tra"; break;
                                        case 1 : echo "Thành công"; break;
                                        case 2 : echo "Hủy"; break;
                                        case 3 : echo "Thất bại"; break;
                                    }
                                    ?>
                                </span>
                                <?php else: //bị khiếu nại ?>
                                    <?php
                                    switch ($node->status){
                                        case 0 : $status = 0;$text = "Admin đang kiểm tra"; break;
                                        case 3 : $status = 1;$text = "Thành công"; break;
                                        case 2 : $status = 0;$text = "Hủy"; break;
                                        case 1 : $status = 3;$text = "Thất bại"; break;
                                    }
                                    ?>
                                    <span class="complain-status complain-status-<?php echo $status; ?>">
                                        <?php echo $text; ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date("d/m/Y",$node->created); ?></td>
                            <td>
                                <?php if($form['author']['#value']==1): // khiếu nại ?>
                                    <b class="<?php echo $node->point<0?"color-red":"color-green"; ?>"><?php echo !empty($node->point)?$node->point:"-"; ?></b>
                                <?php else: //bị khiếu nại ?>
                                    <b class="<?php echo $node->defendant_point<0?"color-red":"color-green"; ?>"><?php echo !empty($node->defendant_point)?$node->defendant_point:"-"; ?></b>
                                <?php endif; ?>
                            </td>
                            <?php if($form['author']['#value']==1): // khiếu nại ?>
                                <td>
                                    <?php if(!empty($article->re_post)&&$article->uid==$user->uid): ?>
                                        <a href="/guest-post/article/<?php echo $article->id; ?>/re-post" class="btn btn-green color-white btn-small">Đăng lại</a>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
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
                            <td data-value="1648399487" data-key="created">...</td>
                            <?php if($form['author']['#value']==1): // khiếu nại ?>
                                <td data-value="1648399487" data-key="created">...</td>
                            <?php endif; ?>
                        </tr>
                    <?php endfor; ?>
                <?php endif;?>
                </tbody>
            </table>
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
</div>
<div class="hidden">
    <?php echo drupal_render_children($form); ?>
</div>