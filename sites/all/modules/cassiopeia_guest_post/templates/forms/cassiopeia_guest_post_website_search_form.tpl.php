<?php
$limit = $form['#limit'];
$page = $form['#page'];
$start = ($page-1)*$limit;
$min = $start+1;
$max = $start+$limit;
$websites = $form['#websites'];
$total_result = $form['#total_result'];
if($max>$total_result->total){
    $max = $total_result->total;
}
?>
<div class="page-header">
    <div class="page-title">
        <h1 title="">
            <span style="max-width: unset;">Tất cả website trên  hệ thống</span>
        </h1>
        <?php echo drupal_render($form['article_add']); ?>
<!--        --><?php //print(l('<i class="fa fa-plus"></i> Đăng bài guest post', 'guest-post/article/add',  array('html'=>true, 'attributes' => array('class' => 'btn btn-green btn-icon-before'))));?>
        <?php echo drupal_render($form['export']); ?>
    </div>
</div>

<div class="page-container">
    <div class="page-search d-flex justify-between align-center mb-24">
        <div class="left-block d-flex align-center">
            <b class="mr-24 search-title">Tìm kiếm website</b>
            <?php if(!empty($form['min'])) echo drupal_render($form['min']); ?>
            <?php if(!empty($form['max'])) echo drupal_render($form['max']); ?>
            <?php echo drupal_render($form['advanced_filter']); ?>
            <?php echo drupal_render($form['category']); ?>
            <?php echo drupal_render($form['search_2']); ?>
        </div>
        <div class="right-block">
            <div class="input-group-search">
                <?php echo drupal_render($form['keyword']); ?>
                <button type="button" class="btn-clear-text"><span class="fa fa-times" aria-hidden="true"></span></button>
                <?php echo drupal_render($form['search']); ?>
            </div>
        </div>
    </div>
    <div class="page-main result guest-post">
        <div class="t-body">
            <table class="table table-striped table-div-responsive table-type-2 table-responsive">
                <thead>
                <tr>
                    <th width="50px;" data-title="select">
                        <label class="mask-chekbox" >
                            <input type="checkbox" name="select" class="selectAll" <?php echo empty($websites)?"readonly disabled":""; ?>>
                            <i class="fa-regular fa-square"></i>
                        </label>
                    </th>
                    <th class="w-3">
                        STT
                    </th>
                    <th class="sort text-left" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="domain">Tên miền</th>
                    <th class=" text-left" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="">Lĩnh vực</th>
                    <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="ref_domain">Ref domain</th>
                    <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="organic_traffic">Organic traffic</th>
                    <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="posted">Bài viết đã đăng</th>
                    <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="posted_of_day">Đã đăng trong ngày</th>
                    <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="last_posted_date">Ngày đăng gần nhất</th>
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
                <?php if(!empty($websites)): $stt=1;?>
                    <?php foreach($websites as $website): $website = cassiopeia_guest_post_website_load($website->id);?>
                        <?php  ?>
                        <tr>
                            <td width="50px;" data-title="select">
                                <label class="mask-chekbox" >
                                    <input name="id[]" type="checkbox" value="<?php echo($website->id); ?>" class="input-checkbox" >
                                    <i class="fa-regular fa-square"></i>
                                </label>
                            </td>
                            <td><?php echo $stt; ?></td>
                            <td class="text-green text-left" width="150px"><a title="<?php echo $website->domain; ?>" class="ellipsis-1 w-220 text-nowrap" href="/guest-post/article/add?website=<?php echo $website->id; ?>"><?php echo $website->domain; ?></a></td>
                            <td class="text-left">
                                <div class="ellipsis-2" title="<?php echo !empty($website->list_of_category)?$website->list_of_category:""; ?>"><?php echo !empty($website->list_of_category)?$website->list_of_category:""; ?></div>
                            </td>
                            <td><?php echo !empty($website->ref_domain)?number_format($website->ref_domain,0,",","."):""; ?></td>
                            <td><?php echo !empty($website->organic_traffic)?number_format($website->organic_traffic,0,",","."):""; ?></td>
                            <td><?php echo !empty($website->posted)?$website->posted:0; ?></td>
                            <td><?php echo !empty($website->posted_of_day)?$website->posted_of_day:0; ?></td>

                            <td><?php echo !empty($website->last_posted_date)?date("d/m/Y",$website->last_posted_date):"-"; ?></td>
                        </tr>
                        <?php $stt++; endforeach; ?>
                <?php else: ?>
                    <?php for($i=1;$i<=5;$i++): ?>
                        <tr>
                            <td width="50px;" data-title="select">
                                <label class="mask-chekbox">
                                    <input name="nid[]" type="checkbox" value="156128" class="" disabled readonly>
                                    <i class="fa-regular fa-square"></i>
                                </label>
                            </td>
                            <td width="50px;" data-title="Stt">...</td>
                            <td data-key="title" data-title="title" class="text-green text-left title">...</td>
                            <td data-key="totalDomain">...</td>
                            <td data-value="61" data-key="googleIndex">...</td>
                            <td data-value="100" data-key="dofollow">...</td>
                            <td data-value="100" data-key="dofollow">...</td>
                            <td data-value="1648399487" data-key="created">...</td>
                            <td data-title="actions">
                                <button type="button" class="btn-edit-project btn-disable" data-id="156128" title="Sửa dự án"><i class="far fa-pen-to-square fa-fw"></i></button>
                                <button type="button" class="btn-delete-project btn-disable" data-id="156128" title="Xóa"><i class="far fa-trash-can fa-fw"></i></button>
                            </td>
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