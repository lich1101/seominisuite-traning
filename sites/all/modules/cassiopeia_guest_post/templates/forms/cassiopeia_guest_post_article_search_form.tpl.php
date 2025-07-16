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
            <span style="max-width: unset;">Tất cả guest post đã đăng</span>
        </h1>
        <?php echo drupal_render($form['article_add']); ?>
<!--        --><?php //print(l('<i class="fa fa-plus"></i> Đăng bài guest post', 'guest-post/article/add',  array('html'=>true, 'attributes' => array('class' => 'btn btn-green btn-icon-before ml-10'))));?>
        <?php print(l('<i class="fa-regular fa-bookmark"></i> Quản lý tags', 'guest-post/article/tag/nojs',  array('html'=>true, 'attributes' => array('class' => 'ctools-use-modal btn btn-icon-before btn btn-green'))));?>
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
            <h2><b class="mr-24">Quản lý bài viết</b></h2>
            <?php echo drupal_render($form['from_date']); ?>
            <?php echo drupal_render($form['to_date']); ?>
            <?php echo drupal_render($form['tag']); ?>
            <?php echo drupal_render($form['search_2']); ?>
        </div>
        <div class="right-block">
            <?php echo drupal_render($form['export']); ?>
        </div>
    </div>
    <div class="page-main result">
        <div class="t-body">
            <table class="table table-striped table-div-responsive table-type-2 table-responsive">
                <thead>
                <tr>
                    <th width="50px;" data-title="select">
                        <label class="mask-chekbox" >
                            <input type="checkbox" name="select" class="selectAll" <?php echo empty($nodes)?"readonly disabled":""; ?>>
                            <i class="fa-regular fa-square"></i>
                        </label>
                    </th>
                    <th class="w-3">
                        STT
                    </th>
                    <th class="sort text-left" width="25%" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="title">Bài viết lưu trên hệ thống</th>
                    <th class="sort text-left" width="25%" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="url_guest_post" >Url guest post</th>
                    <th class="" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="title">Tag</th>
                    <?php if($form['author']['#value']==2): ?>
                        <th>Người đăng</th>
                    <?php endif; ?>
                    <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="publish_date">Ngày xuất bản</th>
                    <th style="    width: 125px;">Đăng lại</th>
                </tr>
                </thead>
                <tbody>
                <?php if(!empty($nodes)): $stt=1;?>
                    <?php foreach($nodes as $node):?>
                        <?php $article = cassiopeia_guest_post_article_load($node->id);?>
                        <tr>
                            <td width="50px;" data-title="select">
                                <label class="mask-chekbox" >
                                    <input name="id[]" type="checkbox" value="<?php echo($node->id); ?>" class="input-checkbox" >
                                    <i class="fa-regular fa-square"></i>
                                </label>
                            </td>
                            <td><?php echo $stt; ?></td>
                            <td class="text-green text-left">
                                <div class="d-flex space-between">
                                    <span class="ellipsis-2 link-external" title="<?php echo $node->title; ?>"><?php echo $node->title; ?></span>
                                    <a target="_blank" href="/guest/post/article/<?php echo $node->id; ?>/view"><i class="fa fa-external-link" aria-hidden="true"></i></a>
                                </div>
                            </td>
                            <td class=" text-left">
                                <?php if(!empty($node->url_guest_post)): ?>
                                    <div class="d-flex justify-between">
                                        <span class="ellipsis-2 link-external" title="<?php echo $node->url_guest_post; ?>"><?php echo $node->url_guest_post; ?></span>
                                        <a target="_blank" href="<?php echo !empty($node->url_guest_post)?$node->url_guest_post:"#"; ?>"><i class="fa fa-external-link" aria-hidden="true"></i></a>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo !empty($article->list_of_tag)?$article->list_of_tag:""; ?>
                            </td>
                            <?php if($form['author']['#value']==2): ?>
                                <td>
                                    <?php $author = user_load($node->uid);
                                    if(!empty($author)){
                                        $text = "";
                                        $text.= !empty($author->field_full_name['und'][0]['value'])?$author->field_full_name['und'][0]['value']:"";
                                        $text.= " - ";
                                        $text.= !empty($author)?$author->mail:"";
                                        echo $text;
                                    }
                                    ?>
                                </td>
                            <?php endif; ?>
                            <td><?php if(!empty($node->publish_date)) echo date("d/m/Y",$node->created); ?></td>
                            <td>
                                <?php if(!empty($article->draft)): ?>
                                    <?php echo l("Đăng lại","guest-post/article/".$article->id."/edit",array("html"=>TRUE,"attributes"=>array("class"=>array("btn btn-green color-white btn-small")))); ?>
                                <?php endif; ?>
                            </td>
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
                            <td data-value="1648399487" data-key="created">...</td>
                            <?php if($form['author']['#value']==2): ?>
                                <td>

                                </td>
                            <?php endif; ?>
                            <td data-title="actions">

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