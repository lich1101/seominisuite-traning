<?php
drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/user-keyword-project-detail.js', ['weight' => 1000]);
global $user;
$packet = cassiopeia_get_available_packet_by_uid($user->uid);
$cache = !empty($_REQUEST)?$_REQUEST:array();
$project = node_load($variables['project_id']);
//$keywords = $variables['keywords'];
$query = db_select("node","tbl_node");
$query->fields("tbl_node");
$query->condition("type","keyword");
$query->join("field_data_field_keyword_project","field_keyword_project","field_keyword_project.entity_id=tbl_node.nid");
$query->condition("field_keyword_project.field_keyword_project_nid",$project->nid);
$query->addExpression("COUNT(tbl_node.nid)","total");
$query->leftJoin("field_data_field_keyword_position","field_keyword_position","field_keyword_position.entity_id=tbl_node.nid");
$query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=3 and field_keyword_position.field_keyword_position_value>0 THEN 1 ELSE 0 END)","top_3");
$query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=5 and field_keyword_position.field_keyword_position_value>0 THEN 1 ELSE 0 END)","top_5");
$query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=10 and field_keyword_position.field_keyword_position_value>0 THEN 1 ELSE 0 END)","top_10");
$query->range(0,1);
$result = $query->execute()->fetchObject();
//_print_r($project);
$conditions = array();
$conditions['field_user'] = array(
    "type"      => "fieldCondition",
    "key"       => "target_id",
    "value"     => $user->uid,
    "condition" => "=",
);
$conditions['field_keyword_project'] = array(
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
<div class="page page-detail page-keyword-project-detail">
    <div class="page-header">
        <div class="page-title">
            <h1 title="<?php echo($project->title); ?> - <?php echo($project->field_domain['und'][0]['value']); ?>">
                <span><?php echo($project->title); ?> - <?php echo($project->field_domain['und'][0]['value']); ?></span>
            </h1>
            <button class="btn btn-click-on btn-add-key"><i class="fa fa-plus"></i> Thêm từ khóa</button>
            <button class="btn btn-green btn-tag-manager"><i class="fas fa-tags fa-fw" aria-hidden="true"></i>Quản lý tags</button>
        </div>
        <div class="tutorial">
            <a href="/<?php echo drupal_get_path_alias("node/169972"); ?>" target="_blank" class="tutorial-keywords">
                <span class="icon"><img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-20.png" alt=""></span> <span>Hướng dẫn sử dụng</span>
            </a>
        </div>

            <form class="page-header-bottom">
<!--                <form action="">-->
                <div class="backlink-status">
                    <ul>
                        <li class="<?php if(empty($_REQUEST['top']) || $_REQUEST['top']=="all") echo "active"; ?>"><label for="top_all">Tất cả từ khoá (<?php echo $result->total; ?>)</label></li>
                        <li class="<?php if(!empty($_REQUEST['top']) && $_REQUEST['top']=="3") echo "active"; ?>"><label for="top_3">Top 1- 3 (<?php echo $result->top_3; ?>)</label></li>
                        <li class="<?php if(!empty($_REQUEST['top']) && $_REQUEST['top']=="5") echo "active"; ?>"><label for="top_5">Top 1 - 5 (<?php echo $result->top_5; ?>)</label></li>
                        <li class="<?php if(!empty($_REQUEST['top']) && $_REQUEST['top']=="10") echo "active"; ?>"><label for="top_10">Top 1 - 10 (<?php echo $result->top_10; ?>)</label></li>
                    </ul>
                </div>
                <input hidden onchange="this.form.submit()" type="radio" name="top" id="top_all" value="all">
                <input hidden onchange="this.form.submit()" type="radio" name="top" id="top_3" value="3">
                <input hidden onchange="this.form.submit()" type="radio" name="top" id="top_5" value="5">
                <input hidden onchange="this.form.submit()" type="radio" name="top" id="top_10" value="10">

<!--                </form>-->
            </form>
    </div>

    <div class="df-tabs">
        <ul class="p-tabs nav nav-pills mb-3" id="pills-tab">
            <li class="nav-item active">
                <a class="nav-link active" id="pills-statistic-tab" href="#statistic">Quản lý thứ hạng từ khoá</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-report-tab" href="/quan-ly-keywords/du-an/<?php echo($project->nid); ?>/bao-cao">Báo cáo</a>
            </li>
        </ul>
        <div class="df-tabs-search">
            <div class="page-search">
                <div class="input-group-search">
                    <input type="text" placeholder="Tìm từ khóa..." name="url" value="">
                    <button type="button" class="btn-clear-text"><span class="fa fa-times" aria-hidden="true"></span></button>
                    <button type="button" class="btn-type-1 btn-submit btn-search-project"><i class="fas fa-magnifying-glass fa-fw"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade active in" id="statistic" role="tabpanel" aria-labelledby="pills-statistic-tab">
            <div class="page-container">
                <div class="page-main result">
                    <div class="running"></div>
                    <div class="smart-filter mb-24">
                        <div class="smart-filter-name">Quản lý từ khóa</div>
                        <div class="smart-filter-form">
                            <form action="" class="d-flex align-center">
                                <div class="rank-other <?php if(isset($_REQUEST['rank'])&&$_REQUEST['rank']=="other") echo "active"; ?>">
                                    <input name="rank-from" type="number" class="rank-from form-control" placeholder="Từ" value="<?php echo isset($_REQUEST['rank-from'])?$_REQUEST['rank-from']:1; ?>">
                                    <input name="rank-to" type="number" class="rank-to form-control" placeholder="Đến" value="<?php echo isset($_REQUEST['rank-to'])?$_REQUEST['rank-to']:10; ?>">
                                </div>
                                <select name="rank" id="" class="btn-gray btn-type-1 mr-8">
                                    <option value="all">Lọc theo thứ hạng</option>
                                    <?php for($i=1;$i<=10;$i++): ?>
                                        <option <?php if(isset($_REQUEST['rank'])&&$_REQUEST['rank']==$i) echo "selected"; ?> value="<?php echo($i); ?>">Top <?php echo $i; ?></option>
                                    <?php endfor; ?>
                                    <option  <?php if(isset($_REQUEST['rank'])&&$_REQUEST['rank']==">10") echo "selected"; ?>  value=">10">Nằm ngoài top 1-10</option>
                                    <option  <?php if(isset($_REQUEST['rank'])&&$_REQUEST['rank']=="other") echo "selected"; ?>  value="other">Lọc theo khoảng</option>
                                    <option <?php if(isset($_REQUEST['rank'])&&$_REQUEST['rank']=="new") echo "selected"; ?> value="new">Chưa kiểm tra</option>
                                    <option <?php if(isset($_REQUEST['rank'])&&$_REQUEST['rank']=="new_today") echo "selected"; ?> value="new_today">Chưa kiểm tra trong ngày</option>
                                </select>
                                <select name="tag" id="" class="btn-gray btn-type-1 mr-8">
                                    <option value="all">Lọc theo tag</option>
                                    <?php if(!empty($tag_options)): ?>
                                        <?php foreach($tag_options as $key => $datum): ?>
                                            <option <?php if(isset($_REQUEST['tag'])&&$_REQUEST['tag']==$key) echo "selected"; ?> value="<?php echo($key); ?>"><?php echo $datum;?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <button type="submit" class="btn btn-green ">
                                    <span class="icon glyphicon glyphicon-filter" aria-hidden="true"></span>
                                    Lọc
                                </button>
                            </form>
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
                                <th data-sort="sort_1" data-direction="DESC" class="w-18 text-left sort">Từ khoá</th>
                                <th data-sort="sort_2" data-direction="DESC" class="w-6 sort ">Thay đổi</th>
                                <th data-sort="sort_3" data-direction="DESC" class="w-7 sort ">Vị trí hiện tại</th>
                                <th data-sort="sort_4" data-direction="DESC" class="w-10 sort ">Vị trí cũ</th>
                                <th data-sort="sort_5" data-direction="DESC" class="w-10 sort ">Vị trí tốt nhất</th>
                                <th data-sort="sort_6" data-direction="DESC" class="w-20 sort ">Url SEO</th>
                                <th data-sort="sort_8" data-direction="DESC" class="w-10 sort ">Tag</th>
                                <th data-sort="sort_7" data-direction="DESC" class="w-7 sort current">Ngày cập nhật</th>
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
                            <?php

//                            print_r($keywords);
                            ?>
                            <?php if(!empty($keywords)): $stt=1;?>
                                <?php foreach($keywords as $key): ?>
                                    <?php echo _cassiopeia_render_theme("module","cassiopeia","templates/parts/row-key-check.tpl.php",array("item"=>$key,"stt"=>$stt)); ?>
                                    <?php $stt++; endforeach; ?>
                            <?php endif;?>
                            </tbody>
                        </table>
                    </div>
                    <div class="page-utilities-bot">
                        <div class="page-utilities-left">
                            <div class="page-footer-left block-search-button">
                                <select name="device" id="" class="btn form-control btn-gray hidden">
                                    <option value="desktop">Desktop</option>
                                </select>
                                <select name="search-engine" id="" class="btn form-control btn-gray">
                                    <option value="https://www.google.com">https://www.google.com</option>
                                    <option value="https://www.google.com.vn">https://www.google.com.vn</option>
                                </select>
                                <select name="captcha-resolve" id="" class="btn form-control btn-gray btn-type-1">
                                  <option value="auto" >Giải Captcha tự động</option>
                                  <option value="manual" selected>Giải Captcha thủ công</option>
                                </select>
                                <button class="btn btn-key-check btn-type-1"><span class="fa fa-search"></span> Tìm kiếm <span class="text">trong top 100</span></button>

                                <button class="btn btn-danger btn-delete hidden">Xóa</button>
                            </div>
                            <form action="#" class="form-bulk d-flex align-center">
                                <select name="" id="" class="btn-gray btn-type-1 mr-20">
                                    <option value="none">Tác vụ khác</option>
                                    <option value="deleteKeyword">Xóa từ khóa</option>
                                    <option value="addToTags">Thêm vào tags</option>
                                    <option value="removeFormTags">Xóa khỏi tags</option>
                                </select>
                                <button type="button" class="btn btn-green btn-type-1">Áp dụng</button>

                            </form>
                        </div>
                        <div class="page-utilities-right">
<!--                            --><?php //if(!empty($keywords)): ?>
                                <a class="btn btn-green btn-export <?php echo !empty($packet->excel)?"excel":""; ?> <?php echo empty($keywords)?"btn-disable":""; ?>" href="#">
                                    <span class="fa fa-file-excel-o fs-16 mr-5" aria-hidden="true"></span>
                                    Xuất báo cáo
                                </a>
<!--                            --><?php //endif; ?>
                        </div>
                    </div>
                </div>

                <div class="page-footer">


<!--                    <div class="page-footer-right">-->
<!--                        <div class="table-items-show">-->
<!--                            <span>số hàng mỗi trang:</span>-->
<!--                            <select name="" id="">-->
<!--                                <option value="">10</option>-->
<!--                                <option value="">15</option>-->
<!--                                <option value="">20</option>-->
<!--                            </select>-->
<!--                        </div>-->
<!---->
<!--                        <div class="table-pagination">-->
<!--                            <span class="count">1-10/450</span>-->
<!--                            <div class="table-navigator">-->
<!--                                <a class="btn-text btn-pagination">-->
<!--                                    <span class="material-icons">chevron_left</span>-->
<!--                                </a>-->
<!--                                <a class="btn-text btn-pagination">-->
<!--                                    <span class="material-icons">chevron_right</span>-->
<!--                                </a>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
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
                        title="Tạo Tag nhằm mục đích nhóm các Từ khóa cùng Tính chất hoặc Chủ Đề"
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
                        title="Tạo Tag nhằm mục đích nhóm các Từ khóa cùng Tính chất hoặc Chủ Đề"
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