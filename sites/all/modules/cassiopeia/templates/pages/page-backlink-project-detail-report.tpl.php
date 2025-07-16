<?php //drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/user.js', ['weight' => 1000]); ?>
<?php
global $user;
$arg = arg();
drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/user-backlink-project-detail-report.js', ['weight' => 1000]);
$cache = !empty($_REQUEST['data'])?$_REQUEST['data']:array();
if(empty($cache['date_filter'])){
    $cache['date_filter'] = "thisMonth";
}
//$project_id = 365849;
$cache['project_id'] = $project_id;
$project = node_load($project_id);
try{
    $query = db_select("tbl_backlink","tbl_backlink");
    $query->fields("tbl_backlink");

    $query->join("tbl_backlink_detail","tbl_backlink_detail","tbl_backlink_detail.nid=tbl_backlink.id");
    $query->addExpression("SUM(CASE WHEN tbl_backlink_detail.rel='dofollow' THEN 1 ELSE 0 END)","total_dofollow");
    $query->addExpression("SUM(CASE WHEN tbl_backlink.indexed=1 THEN 1 ELSE 0 END)","total_indexed");
    $query->addExpression("COUNT(DISTINCT(tbl_backlink_detail.id))","totalBacklink");

    $query->condition("tbl_backlink.pid",$project_id);
    if(!empty($cache['date_filter'])){
        switch ($cache['date_filter']){
            case "thisMonth" :
                $cache['from_date'] = date("01-m-Y 00:00:00",REQUEST_TIME);
                $cache['to_date'] = date("t-m-Y 23:59:00",REQUEST_TIME);
                $query->condition("tbl_backlink.changed",array(strtotime($cache['from_date']),strtotime($cache['to_date'])),"BETWEEN");
                break;
            case "lastMonth" :
                $cache['from_date'] = date("01-m-Y 00:00:00",strtotime(date("d-m-Y",REQUEST_TIME)." - 1 month"));
                $cache['to_date'] = date("t-m-Y 23:59:00",strtotime(date("d-m-Y",REQUEST_TIME)." - 1 month"));
                $query->condition("tbl_backlink.changed",array(strtotime($cache['from_date']),strtotime($cache['to_date'])),"BETWEEN");
                break;
            case "other" : break;
        }
    }
    if(!empty($cache['from_date'])){
        $query->condition("tbl_backlink.changed",strtotime(date("d-m-Y 00:00",strtotime($cache['from_date']))),">=");;
    }
    if(!empty($cache['to_date'])){
        $query->condition("tbl_backlink.changed",strtotime(date("d-m-Y 23:59",strtotime($cache['to_date']))),"<=");;
    }
    $result = $query->execute()->fetchObject();
    $conditions = array();
    $conditions['nid'] = array(
        "type"      => "propertyOrderBy",
        "direction" => "DESC",
    );
    $conditions['field_backlink_project'] = array(
        "type"      => "fieldCondition",
        "key"       => "nid",
        "value"     => $project->nid,
        "condition" => "=",
    );
    if(!empty($cache['from_date'])){
        $conditions['created'] = array(
            "type"      => "propertyCondition",
            "value"     => strtotime($cache['from_date']),
            "condition" => ">="
        );
    }
    if(!empty($cache['to_date'])){
        $conditions['created'] = array(
            "type"      => "propertyCondition",
            "value"     => strtotime($cache['to_date']),
            "condition" => "<="
        );
    }
    if(!empty($cache['tag']) && $cache['tag']!="all"){
        $conditions['field_tags'] = array(
            "type"      => "fieldCondition",
            "key"       => "tid",
            "value"     => $cache['tag'],
            "condition" => "=",
        );
    }
    $backlinks = cassiopeia_get_items_by_conditions($conditions,"backlink","node");
    $total = count($backlinks);
    $conditions = array();
    $conditions['field_user'] = array(
        "type"      => "fieldCondition",
        "key"       => "target_id",
        "value"     => $user->uid,
        "condition" => "=",
    );
    $conditions['field_backlink_project'] = array(
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
}catch (Exception $e){
    cassiopeia_dump($e);
}
$stt=1;
?>

<input type="hidden" id="project_id" value="<?php echo($project_id); ?>">
<input type="hidden" id="project_domain" value="<?php echo($project->field_domain['und'][0]['value']); ?>">

<div class="page page-detail page-user-backlink-project-detail page-backlink-report">
    <div class="page-header">
        <div class="page-title">
            <h1><?php echo($project->title); ?> - <?php echo($project->field_domain['und'][0]['value']); ?></h1>
        </div>
        <div class="page-header-bottom">
            <div class="backlink-status">
                <ul>
                    <li><a href="/quan-ly-backlink/du-an/<?php echo $project->nid; ?>?option=total-backlink">Tất cả backlink(<?php echo($result->totalBacklink); ?>)</a></li>
                    <li><a href="/quan-ly-backlink/du-an/<?php echo $project->nid; ?>?option=total-dofollow">Do follow(<?php echo($result->total_dofollow) ?>)</a></li>
                    <li><a href="/quan-ly-backlink/du-an/<?php echo $project->nid; ?>?option=total-indexed">Google index(<?php echo($result->total_indexed); ?>)</a></li>
                    <!-- <li><span>|</span></li>
                    <li><a href="#">Main content(<?php //echo($result->total_content_position); ?>)</a></li> -->
                </ul>
                <div class="backlink-status-search">
                    <?php
                        $cassiopeia_backlink_filter_form = drupal_get_form("cassiopeia_backlink_report_filter_form",$cache);
                        if(!empty($cassiopeia_backlink_filter_form)){
                            $cassiopeia_backlink_filter_form = drupal_render($cassiopeia_backlink_filter_form);
                            print($cassiopeia_backlink_filter_form);
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="page-tabs">
        <ul>
            <li><a href="/quan-ly-backlink/du-an/<?php echo $project_id; ?>">Quản lý baclink</a></li>
            <li class="active"><a href="/quan-ly-backlink/du-an/<?php echo $project_id; ?>/bao-cao">Báo cáo</a></li>
        </ul>
    </div>
    <div class="page-body">
        <div class="row backlink-report-chart">
            <div class="col-md-6">
            <?php
        try{

            $sub_query = db_select("tbl_backlink","tbl_backlink");
            $sub_query->fields("tbl_backlink");

            if(!empty($cache['date_filter'])){
                switch ($cache['date_filter']){
                    case "thisMonth" :
                        $cache['from_date'] = date("d-m-Y 00:00:00",strtotime(date("d-m-Y",REQUEST_TIME)." - 14 days"));
                        $cache['to_date'] = date("d-m-Y 23:59:00",REQUEST_TIME);
                        $sub_query->condition("tbl_backlink.changed",array(strtotime($cache['from_date']),strtotime($cache['to_date'])),"BETWEEN");
                        break;
                    case "lastMonth" :
                        $cache['from_date'] = date("01-m-Y 00:00:00",strtotime(date("d-m-Y",REQUEST_TIME)." - 1 month"));
                        $cache['to_date'] = date("t-m-Y 23:59:00",strtotime(date("d-m-Y",REQUEST_TIME)." - 1 month"));
                        $sub_query->condition("tbl_backlink.changed",array(strtotime($cache['from_date']),strtotime($cache['to_date'])),"BETWEEN");
                        break;
                    case "other" : break;
                }
            }
//            $result = $sub_query->execute()->fetchAll();
//            _print_r($result);

//            $sub_query->addField("tbl_backlink","refer_page","refer_page");
            $sub_query->addField("tbl_backlink_detail","rel","rel");
            $sub_query->condition("tbl_backlink.pid",$project->nid);
            $sub_query->leftJoin("tbl_backlink_detail","tbl_backlink_detail","tbl_backlink_detail.nid=tbl_backlink.id");
            $sub_query->orderBy("tbl_backlink.id","DESC");
            $sub_query->groupBy("tbl_backlink_detail.id");
            $sub_query->addExpression("COUNT(tbl_backlink_detail.nid)","backlink_count");

//            $sub_query->addExpression("SUM(DISTINCT(CASE WHEN field_backlink_indexed_value=1 THEN 1 ELSE 0 END))","total_indexed");
            $sub_query->addExpression("CASE WHEN (tbl_backlink_detail.rel LIKE 'dofollow') THEN 1 ELSE 0 END","dofollow");
            $sub_query->addExpression("CASE WHEN (tbl_backlink_detail.rel LIKE '%ucg%') THEN 1 ELSE 0 END","ucg");
            $sub_query->addExpression("CASE WHEN (tbl_backlink_detail.rel LIKE '%sponsor%') THEN 1 ELSE 0 END","sponsor");
            $sub_query->addExpression("CASE WHEN (tbl_backlink_detail.rel LIKE '%nofollow%') THEN 1 ELSE 0 END","nofollow");
            $sub_query->addExpression("DATE_FORMAT(FROM_UNIXTIME(tbl_backlink.changed), '%e %b %Y') ","cr");

            $d_query = db_select("tbl_backlink","tbl_backlink");
            $d_query->fields("tbl_backlink");

            $d_query->condition("tbl_backlink.pid",$project->nid);
            $d_query->addExpression("DATE_FORMAT(FROM_UNIXTIME(tbl_backlink.changed), '%e %b %Y') ","cr");
            $d_query->addExpression("COUNT(DISTINCT tbl_backlink.id)","total_source");
            $d_query->join("tbl_backlink_detail","tbl_backlink_detail","tbl_backlink_detail.nid=tbl_backlink.id");
            $d_query->addExpression("SUM(CASE WHEN tbl_backlink.indexed=1 THEN 1 ELSE 0 END)","total_indexed");

            $d_query->addExpression("SUM((CASE WHEN tbl_backlink.indexed=1 THEN 0 ELSE 1 END))","not_indexed");
//            $d_query->groupBy("tbl_backlink.id");;

            $d_query = db_select($d_query,"tbl_sub");
            $d_query->fields("tbl_sub");
            $d_query->addExpression("SUM(total_indexed)","indexed");
            $d_query->addExpression("SUM(not_indexed)","not_indexed");
            $d_query->addExpression("SUM( total_source)","total_source");
            $d_query->groupBy("cr");
            $d_result = $d_query->execute()->fetchAll();
//                _print_r($d_result);

            $query = db_select($sub_query,"tbl_sub");
            $query->fields("tbl_sub");
            $query->addExpression("SUM(backlink_count)","backLinkCount");
            $query->addExpression("SUM(dofollow)","dofollowCount");
            $query->addExpression("SUM(sponsor)","sponsorCount");
            $query->addExpression("SUM(nofollow)","nofollowCount");
            $query->addExpression("SUM(ucg)","ucgCount");
            $query->groupBy("cr");
            $_backlinks = $query->execute()->fetchAll();
//            _print_r($backlinks);
            $FromDateTimestamp = strtotime($cache['from_date']);
            $ToDateTimestamp = strtotime($cache['to_date']);
            if($ToDateTimestamp>REQUEST_TIME){
                $ToDateTimestamp = strtotime(date("d-m-Y 23:59:59",REQUEST_TIME));
            }

            $backLinkList = array();
            $sourceList = array();
            if(!empty($_backlinks)){
                foreach($_backlinks as $_backlink){
                    $backLinkList[strtotime($_backlink->cr." 00:00:00")] = $_backlink;
                }
            }
            if(!empty($d_result)){
                foreach($d_result as $d_item){
                    $sourceList[strtotime($d_item->cr." 00:00:00")] = $d_item;
                }
            }
            $query = db_select("tbl_backlink_report","tbl_backlink_report");
            $query->fields("tbl_backlink_report");
            $query->condition("tbl_backlink_report.nid",$project->nid);
            $query->condition("tbl_backlink_report.date",array(strtotime(date("01-m-Y 00:00:00",REQUEST_TIME)),strtotime(date("t-m-Y 23:59:59",REQUEST_TIME))),"BETWEEN");
            $report = $query->execute()->fetchAll();
            _print_r($report);
            $reportList = [];
            if(!empty($report)){
                foreach($report as $item){
                    $reportList[$item->date] = $item;
                }
            }

            $ValueX = array();
            $dataBackLink = array();
            $dataDomain = array();
            $dataRel = array();
            $dataIndex = array();
            $dataNotIndex = array();
            $maxNumber = 0;
            $dofollow = 0;
            $nofollow = 0;
            $indexed = 0;
            $notIndex = 0;
            $ucg = 0;
            $sponsor = 0;
            $current = $domainCount = 0;
            for($i=$FromDateTimestamp;$i<=$ToDateTimestamp;$i+=86400){
                $indexed+=!empty($sourceList[$i])?$sourceList[$i]->indexed:0;
                $notIndex+=!empty($sourceList[$i])?$sourceList[$i]->not_indexed:0;
                $dofollow+=!empty($backLinkList[$i])?$backLinkList[$i]->dofollowCount:0;
                $nofollow+=!empty($backLinkList[$i])?$backLinkList[$i]->nofollowCount:0;
                $sponsor+=!empty($backLinkList[$i])?$backLinkList[$i]->sponsorCount:0;
                $ucg+=!empty($backLinkList[$i])?$backLinkList[$i]->ucgCount:0;
                $current = !empty($reportList[$i])?$reportList[$i]->backlink_count:$current;
                $domainCount = !empty($reportList[$i])?$reportList[$i]->domain_count:$domainCount;
                $maxNumber = $maxNumber<$current?$current:$maxNumber;
                $maxNumber = $maxNumber<$domainCount?$domainCount:$maxNumber;
                array_push($ValueX,date('d/m',$i));
                if($i<REQUEST_TIME){
                    array_push($dataBackLink,$current);
                    array_push($dataDomain,$domainCount);
                }
            }
            array_push($dataIndex,$indexed);
            array_push($dataIndex,$notIndex);
            array_push($dataRel,$dofollow);
            array_push($dataRel,$nofollow);
            array_push($dataRel,$ucg);
            array_push($dataRel,$sponsor);
        }catch (Exception $e){
            cassiopeia_dump($e);
        }

//        _print_r($report);
//        _print_r(strtotime(date("01-m-Y 00:00:00",REQUEST_TIME)));
//        _print_r(strtotime(date("t-m-Y 23:59:59",REQUEST_TIME)));
        ?>
        <input hidden type="" id="max-num" value="<?php echo $maxNumber>=10?$maxNumber:10; ?>">
        <input hidden type="text" value='<?php echo json_encode($ValueX); ?>' id="ValueX">
        <input hidden type="text" value='<?php echo json_encode($dataBackLink); ?>' id="dataBackLink">
        <input hidden type="text" value='<?php echo json_encode($dataDomain); ?>' id="dataDomain">
        <input hidden type="text" value='<?php echo json_encode($dataRel); ?>' id="dataRel">
        <input hidden type="text" value='<?php echo json_encode($dataIndex); ?>'  data-not-index="<?php echo json_encode($dataNotIndex); ?>" id="indexChartData">
<!--        <div class="backlink-chart">-->
<!--<!--            <canvas id="back_link_chart" style="width:50%;"></canvas>-->-->
<!--        </div>-->
        <?php
      try{
          $query = db_select("tbl_backlink_detail","tbl_backlink_detail");
          $query->fields("tbl_backlink_detail");
          $query->join("tbl_backlink","tbl_backlink","tbl_backlink.id=tbl_backlink_detail.nid");
          $query->groupBy("tbl_backlink_detail.anchor_text");
//          $query->join("field_data_field_backlink_project","field_backlink_project","field_backlink_project.entity_id=tbl_node.nid");
          $query->condition("tbl_backlink.pid",$project->nid);
          $query->addExpression("COUNT(tbl_backlink_detail.nid)","anchorCount");
          $query->condition("tbl_backlink.changed",array(strtotime($cache['from_date']),strtotime($cache['to_date'])),"BETWEEN");
          $query->orderBy("anchorCount","DESC");
          $query->range(0,6);
          $resultAnchor = $query->execute()->fetchAll();
//          cassiopeia_dump($resultAnchor);

          $query = db_select("tbl_backlink_detail","tbl_backlink_detail");
          $query->fields("tbl_backlink_detail");
          $query->join("tbl_backlink","tbl_backlink","tbl_backlink.id=tbl_backlink_detail.nid");
//          $query->groupBy("tbl_backlink_detail.anchor_text");
//          $query->join("field_data_field_backlink_project","field_backlink_project","field_backlink_project.entity_id=tbl_node.nid");
          $query->condition("tbl_backlink.pid",$project->nid);
          $query->addExpression("COUNT(tbl_backlink_detail.nid)","anchorCount");
          $query->addExpression("SUM(CASE  WHEN tbl_backlink_detail.nid THEN 1 ELSE 0 END)","anchorTotal");
          $query->condition("tbl_backlink.changed",array(strtotime($cache['from_date']),strtotime($cache['to_date'])),"BETWEEN");
          $resultTotal = $query->execute()->fetchObject();

      }catch (Exception $E){
          cassiopeia_dump($E);
      }
//        cassiopeia_dump($resultTotal);
//        cassiopeia_dump($resultAnchor);
        ?>
            </div>
            <div class="col-md-6">
            <div class="backlink-chart-item chart-item-items">
<!--                <div class="chart-item-items-container">-->
<!--                    --><?php //if($dofollow+$nofollow+$ucg+$sponsor>0): ?>
<!--                        <div class="chart-item-small">-->
<!--                            <div class="chart-item-small-chart">-->
<!--<!--                                <canvas id="back_link_rel_chart"></canvas>-->-->
<!--                                <span>Tỷ lệ <br> Follow</span>-->
<!--                            </div>-->
<!--                            <ul class=" fake-chart-label">-->
<!--                                <li>-->
<!--                                    <a href="#">-->
<!--                                        <label for="" class="mark-radio">Dofollow-->
<!--                                            <input type="checkbox" id="Dofollow">-->
<!--                                            <span style="--bg: #348FE3; --rgba: #d4d4ffba"></span>-->
<!--                                        </label>-->
<!--                                    </a>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <a href="#">-->
<!--                                        <label for="" class="mark-radio">Nofollow-->
<!--                                            <input type="checkbox" id="Nofollow">-->
<!--                                            <span style="--bg: #06C5D1; --rgba: #b8f1f1"></span>-->
<!--                                        </label>-->
<!--                                    </a>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <a href="#">-->
<!--                                        <label for="" class="mark-radio">Sponsored-->
<!--                                            <input type="checkbox" id="Sponsored">-->
<!--                                            <span style="--bg: #FBCA4D; --rgba: #fde7b8cc"></span>-->
<!--                                        </label>-->
<!--                                    </a>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <a href="#">-->
<!--                                        <label for="" class="mark-radio">UGC-->
<!--                                            <input type="checkbox" id="UGC">-->
<!--                                            <span style="--bg: #9DD246; --rgba: #E2F2C8"></span>-->
<!--                                        </label>-->
<!--                                    </a>-->
<!--                                </li>-->
<!--                            </ul>-->
<!--                        </div>-->
<!--                    --><?php //endif; ?>
<!--                    --><?php //if($indexed+$notIndex>0): ?>
<!--<!--                        <div class="chart-item-small">-->-->
<!--<!--<!--                            <div class="chart-item-small-chart">-->-->-->
<!--<!--<!--<!--                                <canvas id="back_link_index_chart"></canvas>-->-->-->-->
<!--<!--<!--                                <span>Tỷ lệ <br> Index</span>-->-->-->
<!--<!--<!--                            </div>-->-->-->
<!--<!--                            <ul class=" fake-chart-label">-->-->
<!--<!--                                <li>-->-->
<!--<!--                                    <a href="#">-->-->
<!--<!--                                        <label for="" class="mark-radio">GG Indexed-->-->
<!--<!--                                            <input type="checkbox" id="GG-Indexed">-->-->
<!--<!--                                            <span style="--bg: #716EEE; --rgba: #d4d4ffba"></span>-->-->
<!--<!--                                        </label>-->-->
<!--<!--                                    </a>-->-->
<!--<!--                                </li>-->-->
<!--<!--                                <li>-->-->
<!--<!--                                    <a href="#">-->-->
<!--<!--                                        <label for="" class="mark-radio">Not Indexed-->-->
<!--<!--                                            <input type="checkbox" id="Not-Indexed">-->-->
<!--<!--                                            <span style="--bg: #24CECE; --rgba: #b8f1f1"></span>-->-->
<!--<!--                                        </label>-->-->
<!--<!--                                    </a>-->-->
<!--<!--                                </li>-->-->
<!--<!--                            </ul>-->-->
<!--<!--                        </div>-->-->
<!--                    --><?php //endif; ?>
<!--                </div>-->
            </div>
            </div>
        </div>
        <?php if(!empty($resultAnchor)): ?>
        <div class="anchor-text">
            <div class="anchor-text-title">
                <h2 class="heading-secondary">Anchortext</h2>
            </div>
            <div class="anchor-text-container">
                <div class="row">

                        <?php foreach($resultAnchor as $item): ?>
                            <?php if(round($item->anchorCount*100/$resultTotal->anchorTotal,0)>0): ?>
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="anchor-text-item">
                                        <span class="title"><?php echo $item->anchor_text; ?></span>
                                        <div class="chart" data-percent="<?php echo round($item->anchorCount*100/$resultTotal->anchorTotal,0); ?>%">
                                            <div class="chart-percent" style="width: <?php echo round($item->anchorCount*100/$resultTotal->anchorTotal,0); ?>%;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>

                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<!-- Modal -->
<div id="modalAddBacklink" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Thêm Banklink</h4>
            </div>
            <div class="modal-body">
                <?php
                $cassiopeia_add_backlink_form = drupal_get_form("cassiopeia_add_backlink_form",$project);
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
<input type="hidden" id="tags" value='<?php echo(json_encode($tag_options)); ?>'>
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
                <input type="text" class="tagify-input">
                <div class="buttons">
                    <button class="btn btn-success btn-green">Xác nhận</button>
                </div>
            </div>
        </div>

    </div>
</div>
