
<div class="visible-xs mobile-note">
    <div class="text confirm">
        Mời bạn dùng Laptop hoặc Desktop để sử dụng chức năng này
    </div>
    <!--                                   <span class="close">&times;</span>-->
</div>
<?php
drupal_add_js("https://code.jquery.com/ui/1.13.0/jquery-ui.js");
drupal_add_css("https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css");
drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/user-content-project-detail.js', ['weight' => 1000]);
global $user;

$packet = cassiopeia_get_available_packet_by_uid($user->uid);
$cache = !empty($_REQUEST['data'])?$_REQUEST['data']:array();
//_print_r($cache);
$page = isset($cache['page'])?$cache['page']:1;
$limit = isset($cache['limit'])?$cache['limit']:10;
$start = ($page-1)*$limit;

$project = node_load($variables['pid']);
$cache['pid'] = $project->nid;
$cache['rank'] = !empty($cache['rank'])?$cache['rank']:"all";
$cache['sort_by'] = !empty($cache['sort_by'])?$cache['sort_by']:"changed";
$cache['sort_direction'] = !empty($cache['sort_direction'])?$cache['sort_direction']:"DESC";
//_print_r($cache);
$query = db_select("cassiopeia_content","cassiopeia_content");
$query->condition("cassiopeia_content.pid",$project->nid);
//$query->fields("cassiopeia_content");
$query->addExpression("SUM(CASE WHEN cassiopeia_content.point>=90 THEN 1 ELSE 0 END)","standard");
$query->addExpression("SUM(CASE WHEN cassiopeia_content.point>=50 AND cassiopeia_content.point<90 THEN 1 ELSE 0 END)","need_to_optimize");
$query->addExpression("SUM(CASE WHEN cassiopeia_content.point<50 THEN 1 ELSE 0 END)","not_up_to_standard");
$query->addExpression("COUNT(cassiopeia_content.id)","total");

if(isset($cache['rank'])&&$cache['rank']!="all"){
    switch ($cache['rank']){
        case 1 :
            $query->condition("cassiopeia_content.point",90,">=");
            break;
        case 2 :
            $query->condition("cassiopeia_content.point",50,"<");
            break;
        case 3 :
            $query->condition("cassiopeia_content.point",50,">=");
            $query->condition("cassiopeia_content.point",90,"<");
            break;
    }
}
if(!empty($cache['tag'])&&$cache['tag']!="all"){
    $query->join("tbl_tags","tbl_tags","tbl_tags.nid=cassiopeia_content.id");
    $query->condition("tbl_tags.tid",$cache['tag']);
}
if(!empty($cache['from_date'])){
    $query->condition("cassiopeia_content.changed",strtotime(date("d-m-Y 00:00:00",strtotime($cache['from_date']))),">=");
}
if(!empty($cache['to_date'])){
    $query->condition("cassiopeia_content.changed",strtotime(date("d-m-Y 23:59:59",strtotime($cache['to_date']))),"<=");
}
if(!empty($cache['title'])){
    $query->condition("cassiopeia_content.title","%".$cache['title']."%","LIKE");
}

$summary = $query->execute()->fetchObject();
if($limit>$summary->total){
    $limit = $summary->total;
}
if($limit==0) $limit = 1;
$total_page = ceil($summary->total/$limit);
try{

    $tag_query = db_select("tbl_tags","tbl_tags");
    $tag_query->fields("tbl_tags",array("nid","tid"));
    $tag_query->condition("pid",$project->nid);

    $sub = db_select("cassiopeia_content","cassiopeia_content");
    $sub->condition("cassiopeia_content.pid",$project->nid);
    $sub->fields("cassiopeia_content");
    $sub->join($tag_query,"tbl_tags","tbl_tags.nid=cassiopeia_content.id");
    $sub->join("taxonomy_term_data","taxonomy_term_data","taxonomy_term_data.tid=tbl_tags.tid");

    $sub->groupBy("cassiopeia_content.id");
    $sub->addExpression("GROUP_CONCAT (taxonomy_term_data.name SEPARATOR ',' ) ","tags");

//    $result = $sub->execute()->fetchAll();
//    _print_r($result);

    $query = db_select("cassiopeia_content","cassiopeia_content");
    $query->fields("cassiopeia_content");
    $query->condition("cassiopeia_content.pid",$project->nid);

    $query->leftJoin($sub,"tbl_sub","tbl_sub.id=cassiopeia_content.id");
    $query->addField("tbl_sub","tags","tags");
    $query->range($start,$limit);
//    _print_r($start);
//    _print_r($limit);
    if(isset($cache['rank'])&&$cache['rank']!="all"){
        switch ($cache['rank']){
            case 1 :
                $query->condition("cassiopeia_content.point",90,">=");
                break;
            case 2 :
                $query->condition("cassiopeia_content.point",50,"<");
                break;
            case 3 :
                $query->condition("cassiopeia_content.point",50,">=");
                $query->condition("cassiopeia_content.point",90,"<");
                break;
        }
    }
    if(!empty($cache['tag'])&&$cache['tag']!="all"){
        $query->join("tbl_tags","tbl_tags","tbl_tags.nid=cassiopeia_content.id");
        $query->condition("tbl_tags.tid",$cache['tag']);
    }
    if(!empty($cache['from_date'])){
        $query->condition("cassiopeia_content.changed",strtotime(date("d-m-Y 00:00:00",strtotime($cache['from_date']))),">=");
    }
    if(!empty($cache['to_date'])){
        $query->condition("cassiopeia_content.changed",strtotime(date("d-m-Y 23:59:59",strtotime($cache['to_date']))),"<=");
    }
    if(!empty($cache['title'])){
        $query->condition("cassiopeia_content.title","%".$cache['title']."%","LIKE");
    }
//    switch ($cache['sort_by']){
//        case "created" :
//            $query->orderBy("cassiopeia_content.created",$cache['sort_direction']);
//            break;
//        case "title" :
//            $query->orderBy("cassiopeia_content.title",$cache['sort_direction']);
//            break;
//        case "word_count" :
//            $query->orderBy("cassiopeia_content.title",$cache['sort_direction']);
//            break;
//        case "point" : break;
//    }
    $query->orderBy("cassiopeia_content.".$cache['sort_by'],$cache['sort_direction']);
    $contents = $query->execute()->fetchAll();
//    _print_r($contents);
}catch (Exception $e){
    _print_r($e);
}

$conditions = array();
$conditions['field_user'] = array(
    "type"      => "fieldCondition",
    "key"       => "target_id",
    "value"     => $user->uid,
    "condition" => "=",
);
$conditions['field_content_project'] = array(
    "type"      => "fieldCondition",
    "key"       => "nid",
    "value"     => $project->nid,
    "condition" => "=",
);
$tags = cassiopeia_get_items_by_conditions($conditions,"tags","taxonomy_term");
$tag_options = array();
$tag_options['all'] = "Tất cả";
if(!empty($tags)){
    foreach($tags as $tag){
        $tag_options[$tag->tid] = $tag->name;
    }
}
$cache['tag_options'] = $tag_options;
unset($tag_options['all']);
?>
<span id="listOfID" data-value=""></span>
<input hidden type="number" id="totalItems">
<input hidden type="number" id="checkedItems">
<input type="hidden" id="project_id" value="<?php echo($project->nid); ?>">
<input type="hidden" id="project_domain" value="<?php echo($project->field_domain['und'][0]['value']); ?>">
<div class="page page-detail page-content-project-detail">
    <div class="page-header">
        <div class="page-title">
            <h1 title="<?php echo($project->title); ?> - <?php echo($project->field_domain['und'][0]['value']); ?>">
                <span><?php echo($project->title); ?> - <?php echo($project->field_domain['und'][0]['value']); ?></span>
            </h1>
            <button class="btn btn-click-on btn-add-article"><a href="/quan-ly-du-an-content/<?php echo $project->nid; ?>/add" style="color:white"><i class="fa fa-plus"></i> Thêm bài viết</a></button>
            <button class="btn btn-green btn-tag-manager"><i class="fas fa-tags fa-fw" aria-hidden="true"></i>Quản lý tags</button>
        </div>
        <div class="tutorial">
            <a href="/<?php echo drupal_get_path_alias("node/808450"); ?>" target="_blank" class="tutorial-keywords">
                <span class="icon"><img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-20.png" alt=""></span> <span>Hướng dẫn sử dụng</span>
            </a>
        </div>

        <form class="page-header-bottom">
            <!--                <form action="">-->
            <div class="article-rank backlink-status">
                <ul>
                    <li data-rank="all" class="<?php if(!empty($cache['rank'])&&$cache['rank']=="all") echo "active"; ?>"><label for="top_all">Tất cả(<?php echo $summary->total; ?>)</label></li>
                    <li data-rank="1" class="<?php if(!empty($cache['rank'])&&$cache['rank']=="1") echo "active"; ?>"><label for="top_3">Đạt tiêu chuẩn(<?php echo $summary->standard; ?>)</label></li>
                    <li data-rank="2" class="<?php if(!empty($cache['rank'])&&$cache['rank']=="2") echo "active"; ?>"><label for="top_5">Chưa đạt(<?php echo $summary->not_up_to_standard; ?>)</label></li>
                    <li data-rank="3" class="<?php if(!empty($cache['rank'])&&$cache['rank']=="3") echo "active"; ?>"><label for="top_10">Cần tối ưu(<?php echo $summary->need_to_optimize; ?>)</label></li>
                </ul>
            </div>
<!--            <input hidden onchange="this.form.submit()" type="radio" name="top" id="top_all" value="all">-->
<!--            <input hidden onchange="this.form.submit()" type="radio" name="top" id="top_3" value="3">-->
<!--            <input hidden onchange="this.form.submit()" type="radio" name="top" id="top_5" value="5">-->
<!--            <input hidden onchange="this.form.submit()" type="radio" name="top" id="top_10" value="10">-->

            <!--                </form>-->
        </form>
    </div>

    <div class="df-tabs">
<!--        <ul class="p-tabs nav nav-pills mb-3" id="pills-tab">-->
<!--            <li class="nav-item active">-->
<!--                <a class="nav-link active" id="pills-statistic-tab" href="#statistic">Quản lý thứ hạng từ khoá</a>-->
<!--            </li>-->
<!--            <li class="nav-item">-->
<!--                <a class="nav-link" id="pills-report-tab" href="/quan-ly-keywords/du-an/--><?php //echo($project->nid); ?><!--/bao-cao">Báo cáo</a>-->
<!--            </li>-->
<!--        </ul>-->
<!--        <div class="df-tabs-search">-->
<!--            <div class="page-search">-->
<!--                <div class="input-group-search">-->
<!--                    <input type="text" placeholder="Tìm từ khóa..." name="url" value="">-->
<!--                    <button type="button" class="btn-clear-text"><span class="fa fa-times" aria-hidden="true"></span></button>-->
<!--                    <button type="button" class="btn-type-1 btn-submit btn-search-project"><i class="fas fa-magnifying-glass fa-fw"></i></button>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
    </div>

    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade active in" id="statistic" role="tabpanel" aria-labelledby="pills-statistic-tab">
            <div class="page-container">
                <div class="page-main result">
                    <div class="running"></div>
                    <div class="smart-filter mb-24">
                        <div class="smart-filter-name">Quản lý bài viết</div>
                        <div class="smart-filter-form">
                            <?php
                            $cassiopeia_backlink_filter_form = drupal_get_form("cassiopeia_content_article_filter_form",$cache);
                            if(!empty($cassiopeia_backlink_filter_form)){
                                $cassiopeia_backlink_filter_form = drupal_render($cassiopeia_backlink_filter_form);
                                print($cassiopeia_backlink_filter_form);
                            }
                            ?>
                        </div>
                    </div>
                    <div class="t-body">
                        <table class="table table-striped table-div-responsive table-type-2 table-responsive">
                            <thead>
                            <tr>
                                <th class="w-3" data-title="select">
                                    <label class="mask-chekbox">
                                        <input type="checkbox" name="select" class="selectAll">
                                        <i class="fa-regular fa-square"></i>
                                    </label>
                                </th>
                                <th class="w-3">
                                    STT
                                </th>
                                <th data-sort="raw_title" data-direction="<?php echo $cache['sort_direction']; ?>" class="w-18 text-left sort">Tiêu đề</th>
                                <th data-sort="word_count" data-direction="<?php echo $cache['sort_direction']; ?>" class="w-6 sort ">Số từ</th>
                                <th data-sort="point" data-direction="<?php echo $cache['sort_direction']; ?>" class="w-7 sort ">Điểm đánh giá</th>
                                <th data-sort="" data-direction="<?php echo $cache['sort_direction']; ?>" class="w-10  ">Tag</th>
                                <th data-sort="changed" data-direction="<?php echo $cache['sort_direction']; ?>" class="w-10 sort ">Ngày cập nhật</th>
                                <th data-sort="" data-direction="<?php echo $cache['sort_direction']; ?>" class="w-10 ">Action</th>
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
                            <?php if(!empty($contents)): $stt=1;?>
                                <?php foreach($contents as $node): ?>
                                    <?php  ?>
                                    <tr>
                                        <td class="w-3" data-title="select">
                                            <label class="mask-chekbox">
                                                <input  value="<?php echo($node->id); ?>" type="checkbox" name="select" class="" data-stt="<?php echo $stt; ?>" data-id="<?php echo($node->id); ?>" data-key="<?php echo($node->title); ?>" >
                                                <i class="fa-regular fa-square"></i>
                                            </label>
                                        </td>
                                        <td><?php echo $stt; ?></td>
                                        <td class="text-left text-green"><a href="/quan-ly-du-an-content/<?php echo $project->nid; ?>/bai-viet/<?php echo $node->id; ?>/edit"><?php echo $node->title; ?></a></td>
                                        <td><?php echo $node->word_count; ?></td>
                                        <td><span class="point rank-<?php if($node->point<50){ echo "low";}elseif($node->point<90){ echo "medium";}else{ echo "high";} ?>"><?php echo $node->point; ?></span>/100</td>
                                        <td>
                                            <div class="line-break-2"><?php echo $node->tags; ?></div>
                                        </td>
                                        <td><?php echo date("d/m/Y",$node->changed); ?></td>
                                        <td>
                                            <button><a href="/quan-ly-du-an-content/<?php echo $project->nid; ?>/bai-viet/<?php echo $node->id; ?>/edit"><i class="far fa-pen-to-square fa-fw"></i></a></button>
                                            <button class="btn-delete-content-article" type="button" data-id="<?php echo $node->id; ?>" title="Xóa">
                                                <i class="far fa-trash-can fa-fw" aria-hidden="true"></i>
                                            </button>
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
                                        <td data-value="61" data-key="googleIndex">...</td>
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
                </div>
                <div class="page-footer">
                    <div class="page-utilities-bot">
                        <div class="page-utilities-left">
                            <form action="#" class="form-bulk d-flex align-center">
                                <select name="" id="" class="btn-gray btn-type-1 mr-20">
                                    <option value="none">Tác vụ khác</option>
                                    <option value="deleteContentArticles">Xóa bài viết</option>
                                    <option value="addToTags">Thêm vào tags</option>
                                    <option value="removeFormTags">Xóa khỏi tags</option>
                                </select>
                                <button type="button" class="btn btn-green btn-type-1">Áp dụng</button>

                            </form>
                        </div>
                        <div class="custom-paging">
                            <div class="item-per-page">
                                <span>Số bài mỗi trang:</span>
                                <select name="item_per_page" id="">
                                    <option <?php if(isset($cache['limit'])&&$cache['limit']==10) echo "selected"; ?> value="10">10</option>
                                    <option <?php if(isset($cache['limit'])&&$cache['limit']==50) echo "selected"; ?> value="50">50</option>
                                    <option <?php if(isset($cache['limit'])&&$cache['limit']==100) echo "selected"; ?> value="100">100</option>
                                    <option <?php if(isset($cache['limit'])&&$cache['limit']==200) echo "selected"; ?> value="200">200</option>
                                </select>
                            </div>
                            <div class="current-page">
                                <?php $temp = $start+$limit;
                                if($temp>$summary->total) $temp = $summary->total;
                                ?>
                                <?php echo ($start+1); ?>-<?php echo $temp; ?> of <?php echo $summary->total; ?>
                            </div>
                            <div class="nav-buttons">
                                <?php if($page<=1): ?>
                                    <i class="fa fa-angle-left  <?php if($page<=1) echo "disabled"; ?>"></i>
                                <?php else: ?>
                                    <i class="fa fa-angle-left btn-prev <?php if($page<=1) echo "disabled"; ?>"></i>
                                <?php endif; ?>
                                <?php if($page>=$total_page): ?>
                                    <i class="fa fa-angle-right disabled"></i>
                                <?php else: ?>
                                    <i class="fa fa-angle-right btn-next"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- paging-->

                    <!--e: paging-->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div id="modalAddKeyword" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Thêm Từ khóa</h4>
            </div>
            <div class="modal-body">
                <div class="Tag-toolTip">
                    <button
                        type="button"
                        class="btn btn-secondary btn-notice-tooltip"
                        data-toggle="tooltip"
                        data-placement="right"
                        title="Tạo Tag nhằm mục đích nhóm các Bài viết cùng Tính chất hoặc Chủ Đề"
                    >
                        <span class="fa fa-question-circle-o" aria-hidden="true"></span>
                    </button>
                </div>
                <?php
                $cassiopeia_add_backlink_form = drupal_get_form("cassiopeia_add_keyword_form",$project);
                if(!empty($cassiopeia_add_backlink_form)){
                    $cassiopeia_add_backlink_form = drupal_render($cassiopeia_add_backlink_form);
                    print($cassiopeia_add_backlink_form);
                }
                ?>
            </div>
        </div>

    </div>
</div>
<!-- Modal -->
<!--<input type="hidden" id="tags" value='--><?php //echo(json_encode($tag_options)); ?><!--'>-->
<textarea class="hidden" type="hidden" id="tags" value=''><?php echo(json_encode($tag_options)); ?></textarea>
<div id="modalTags" class="modal fade" role="dialog">
    <input type="hidden" id="modalTagOption">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Thêm vào tags</h4>
            </div>
            <div class="modal-body">
                <div class="Tag-toolTip" style="bottom: 105px;">
                    <button
                        type="button"
                        class="btn btn-secondary btn-notice-tooltip"
                        data-toggle="tooltip"
                        data-placement="right"
                        title="Tạo Tag nhằm mục đích nhóm các Bài viết cùng Tính chất hoặc Chủ Đề"
                    >
                        <span class="fa fa-question-circle-o" aria-hidden="true"></span>
                    </button>
                </div>
                <div class="form-item form-item-tags form-type-textfield form-group">
                    <label class="control-label" for="edit-tags">Thẻ</label>
                    <input type="text" class="tagify-input" placeholder="Phân cách các thẻ Tag nhấn phím Enter">
                </div>
                <div class="buttons">
                    <button class="btn btn-success btn-green">Xác nhận</button>
                </div>
            </div>
        </div>

    </div>
</div>
<div class="modal-create-backlink-progress">
    <div class="block-dialog">
        <div class="block-header"><h4>Đang tạo từ khóa...</h4></div>
        <div class="block-body">
            <div class="create-backlink-progress-bar">
                <progress id="file" value="0" max="2180"> 32% </progress>
            </div>
        </div>
    </div>
</div>
<div class="modal-tag-manager">
    <div class="modal-dialog block-dialog">
        <div class="block-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Quản lý tags</h4>
        </div>
        <div class="block-container">
            <table class="table table-hover table-stripped">
                <thead>
                <tr>
                    <th width="80%">Tag</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($tags as $tag): ?>
                    <tr data-tid="<?php echo $tag->tid; ?>">
                        <td>
                            <input type="text" class="form-control" value="<?php echo $tag->name; ?>" name="tag" data-tid="<?php echo $tag->tid; ?>">
                        </td>
                        <td class="text-center">
                            <button class="btn btn-delete-tag" data-tid="<?php echo $tag->tid; ?>"><i class="fa fa-trash  danger" data-tid="<?php echo $tag->tid; ?>"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>