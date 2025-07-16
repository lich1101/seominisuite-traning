<?php drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/user-keyword-projects.js', ['weight' => 1000]); ?>
<?php
global $user;
$packet = cassiopeia_get_available_packet_by_uid($user->uid);
$conditions = array();
if(isset($_REQUEST['sort']) && isset($_REQUEST['direction'])){
    switch ($_REQUEST['sort']){
        case "name" :
            $conditions['title'] = array(
                "type"      => "propertyOrderBy",
                "direction" => $_REQUEST['direction'],
            );
            break;
        case "website" :
            $conditions['field_domain'] = array(
                "type"      => "fieldOrderBy",
                "column"    => "value",
                "direction" => $_REQUEST['direction'],
            );
            break;
        case "created" :
            $conditions['created'] = array(
                "type"      => "propertyOrderBy",
                "direction" => $_REQUEST['direction'],
            );
            break;
    }
}else{
    $conditions['created'] = array(
        "type"      => "propertyOrderBy",
        "direction" => "DESC"
    );
}
if(!empty($_REQUEST['title'])){
    $conditions['title'] = array(
        "type"      => "propertyCondition",
        "value"     => "%".trim($_REQUEST['title'])."%",
        "condition" => "LIKE"
    );
}
$conditions['uid'] = array(
    "type"      => "propertyCondition",
    "value"     => $user->uid,
    "condition" => "=",
);
$projects = cassiopeia_get_items_by_conditions($conditions,"project_keyword","node");
?>
<div class="page page-keywords">
    <div class="page-header">
        <div class="page-title">
            <h1>Tất cả dự án từ khoá</h1>
            <button type="button" class="btn-green btn-add-keyword-project">
                <span class="fa fa-plus"></span>
                Thêm dự án
            </button>
            <button type="button" class="btn btn-export btn-green btn-search-project <?php echo (empty($projects))?'btn-disable':''; ?> <?php echo !empty($packet->excel)?"excel":""; ?>" style="">
                <span class="fa fa-file-excel-o fs-16 mr-5" aria-hidden="true"></span>
                Xuất báo cáo
            </button>
        </div>

        <div class="page-search">
<!--            <form action="">-->
                <div class="input-group-search">
                    <input type="text" placeholder="Tìm dự án..." name="title" value="<?php if(!empty($_REQUEST['title'])) echo $_REQUEST['title']; ?>">
                    <button type="button" class="btn-clear-text"><span class="fa fa-times" aria-hidden="true"></span></button>
                    <button type="button" class="btn-type-1 btn-submit btn-search-project">
                        <i class="fas fa-magnifying-glass fa-fw"></i>
                    </button>
                </div>
<!--            </form>-->
        </div>
    </div>

    <div class="page-utilities d-none">
        <div class="page-utilities-left">
            <form action="#">
<!--                <select name="" id="" class="btn-gray btn-type-1">-->
<!--                    <option value="">Bulk Action</option>-->
<!--                    <option value="">item 1</option>-->
<!--                    <option value="">item 2</option>-->
<!--                </select>-->
<!--                <button type="submit" class="btn-submit btn-type-1">Áp dụng</button>-->
            </form>
        </div>
        <form action="" class="form-sort">
            <input type="hidden" name="sort" value="">
            <input type="hidden" name="direction" value="">
        </form>
        <div class="page-utilities-right">

        </div>
    </div>

    <div class="page-container">
        <form class="form-export" action="/quan-ly-keywords/export">
            <div class="page-main result table-responsive">
                <table class="table table-striped table-div-responsive table-type-2">
                    <thead>
                    <tr>
                        <th width="50px" data-title="select">
                            <label class="mask-chekbox">
                                <input type="checkbox" name="select" class="selectAll" <?php echo empty($projects)?"readonly disabled":""; ?>>
                                <i class="fa-regular fa-square"></i>
                            </label>
                        </th>
                        <th width="50px" data-title="Stt">
                            STT
                        </th>
                        <th data-sort="title" data-title="title" data-direction="DESC" class="text-left sort w-15">Tên dự án</th>
                        <th data-sort="domain" data-direction="DESC" class="text-left sort">Website</th>
                        <th data-sort="1_3" data-direction="DESC" class="w-8 sort">top 01-03</th>
                        <th data-sort="1_5" data-direction="DESC" class="w-8 sort">top 01-05</th>
                        <th data-sort="1_10" data-direction="DESC" class="w-8 sort">top 01-10</th>
                        <th data-sort="1_30" data-direction="DESC" class="w-8 sort">top 01-30</th>
                        <th data-sort="AVG" data-direction="DESC" class="w-8 sort">Thứ hạng trung bình</th>
                        <th data-sort="created" data-direction="DESC" class="sort current">Ngày cập nhật</th>
                        <th class="w-12" data-title="actions">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($projects)): $stt=1; ?>
                        <?php foreach($projects as $item): ?>
                            <?php
                            $query = db_select("node","tbl_node");
                            $query->fields("tbl_node");
                            $query->condition("type","keyword");
                            $query->join("field_data_field_keyword_project","field_keyword_project","field_keyword_project.entity_id=tbl_node.nid");
                            $query->condition("field_keyword_project.field_keyword_project_nid",$item->nid);
                            $query->addExpression("COUNT(tbl_node.nid)","total");
                            $query->leftJoin("field_data_field_keyword_position","field_keyword_position","field_keyword_position.entity_id=tbl_node.nid");
                            $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=3 THEN 1 ELSE 0 END)","top_3");
                            $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=5 THEN 1 ELSE 0 END)","top_5");
                            $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=10 THEN 1 ELSE 0 END)","top_10");
                            $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=30 THEN 1 ELSE 0 END)","top_30");
                            $query->addExpression("AVG(field_keyword_position.field_keyword_position_value)","AVG");

                            $query->range(0,1);
                            $result = $query->execute()->fetchObject();
                            $_domain = str_replace("http://","",$item->field_domain['und'][0]['value']);
                            $_domain = str_replace("https://","",$_domain);
                            $_domain = str_replace("www","",$_domain);
                            ?>
                            <tr>
                                <td data-title="select">
                                    <label class="mask-chekbox">
                                        <input name="nid[]" type="checkbox" value="<?php echo($item->nid); ?>">
                                        <i class="fa-regular fa-square"></i>
                                    </label>
                                </td>
                                <td data-title="Stt"><?php echo $stt; ?></td>
                                <td data-key="title" data-title="title" class="text-green text-left" title="<?php echo($item->title); ?>">
                                    <div class="d-flex space-between">
                                        <a href="/quan-ly-keywords/du-an/<?php echo($item->nid); ?>" class="project-title sort-text"><?php echo($item->title); ?></a>
                                    </div>
                                </td>
                                <td data-key="domain" class="text-left " title="<?php echo($item->field_domain['und'][0]['value']); ?>">
                                    <div class="d-flex space-between">
                                        <span class="sort-text" target="_blank" href="//<?php echo($_domain); ?>"><?php echo($item->field_domain['und'][0]['value']); ?></span>
                                        <a href="//<?php echo($_domain); ?>" title="<?php echo($item->field_domain['und'][0]['value']); ?>" target="_blank">
                                            <i class="fa fa-external-link"></i>
                                        </a>
                                    </div>
                                </td>
                                <td data-value="<?php echo !empty($result->top_3)?$result->top_3:0; ?>" data-key="1_3"><?php echo !empty($result->top_3)?$result->top_3:0; ?>/<?php echo $result->total; ?></td>
                                <td data-value="<?php echo !empty($result->top_5)?$result->top_5:0; ?>" data-key="1_5"><?php echo !empty($result->top_5)?$result->top_5:0; ?>/<?php echo $result->total; ?></td>
                                <td data-value="<?php echo !empty($result->top_10)?$result->top_10:0; ?>" data-key="1_10"><?php echo !empty($result->top_10)?$result->top_10:0; ?>/<?php echo $result->total; ?></td>
                                <td data-value="<?php echo !empty($result->top_30)?$result->top_30:0; ?>" data-key="1_30"><?php echo !empty($result->top_30)?$result->top_30:0; ?>/<?php echo $result->total; ?></td>
                                <td data-value="<?php echo !empty($result->AVG)?$result->AVG:100; ?>" data-key="AVG"><?php echo !empty($result->AVG)?round($result->AVG,2):100; ?></td>
                                <td data-value="<?php echo $item->changed; ?>" data-key="created"><?php echo date("d/m/Y",$item->changed); ?></td>
                                <td data-type="actions">
                                    <button class="btn-edit-project" type="button" data-id="<?php echo($item->nid); ?>" title="Sửa dự án">
                                        <i class="far fa-pen-to-square fa-fw"></i>
                                    </button>
                                    <button class="btn-delete-project" type="button" data-id="<?php echo($item->nid); ?>" data-name="<?php echo($item->title); ?>" title="Xóa">
                                        <i class="far fa-trash-can fa-fw"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php $stt++; endforeach; ?>
                    <?php else: ?>
                        <?php for($i=1;$i<=5;$i++): ?>
                            <tr>
                                <td width="50px;" data-title="select">
                                    <label class="mask-chekbox">
                                        <input name="nid[]" type="checkbox" value="156128" class="" readonly disabled>
                                        <i class="fa-regular fa-square"></i>
                                    </label>
                                </td>
                                <td width="50px;">...</td>
                                <td data-title="title" class="text-green text-left title">...</td>
                                <td data-title="domain" class="text-left ">...</td>
                                <td data-title="1_3">...</td>
                                <td data-title="1_5">...</td>
                                <td data-title="1_10" data-key="googleIndex">...</td>
                                <td data-title="1_30" data-key="dofollow">...</td>
                                <td data-title="AVG" data-key="created">...</td>
                                <td data-title="created"></td>
                                <td data-title="actions">
                                    <button type="button" class="btn-edit-project btn-disable" data-id="156128" title="Sửa dự án"><i class="far fa-pen-to-square fa-fw"></i></button>
                                    <button type="button" class="btn-delete-project btn-disable" data-id="156128" title="Xóa"><i class="far fa-trash-can fa-fw"></i></button>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>

</div>
<!--<a href="https://autic.vn/da-ban-ve-ngai-gi-khong-ban-combo" rel="nofollow">Bán combo du lịch</a>-->
