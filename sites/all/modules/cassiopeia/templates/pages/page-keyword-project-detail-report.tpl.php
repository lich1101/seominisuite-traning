<?php //drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/user.js', ['weight' => 1000]); ?>
<?php
global $user;

$arg = arg();
drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/user-keyword-project-detail-report.js', ['weight' => 1000]);
$cache = !empty($_REQUEST['data'])?$_REQUEST['data']:array();
$project_id = $variables['project_id'];
$cache['project_id'] = $project_id;
$project = node_load($project_id);
$project = node_load($variables['project_id']);
$conditions = array();
$conditions['nid'] = array(
    "type"      => "propertyOrderBy",
    "direction" => "DESC",
);
$conditions['field_keyword_project'] = array(
    "type"      => "fieldCondition",
    "key"       => "nid",
    "value"     => $project->nid,
    "condition" => "=",
);
if(!empty($cache['tag']) && $cache['tag']!="all"){
    $conditions['field_tags'] = array(
        "type"      => "fieldCondition",
        "key"       => "tid",
        "value"     => $cache['tag'],
        "condition" => "=",
    );
}
if(!empty($cache['rank']) && $cache['rank']!="all"){
    $conditions['field_keyword_position'] = array(
        "type"      => "fieldCondition",
        "key"       => "value",
        "value"     => $cache['rank'],
        "condition" => "=",
    );
}
$keywords = cassiopeia_get_items_by_conditions($conditions,"keyword","node");

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



//cassiopeia_dump($cache);
$query = db_select("tbl_check_keyword","tbl_check_keyword");
$query->fields("tbl_check_keyword");
$query->condition("uid",$user->uid);
$query->condition("nid",$project->nid);
$query->range(0,2);
$query->orderBy("created","DESC");
$checkHistory = $query->execute()->fetchAll();
//print_r($checkHistory);
if(!empty($checkHistory)){
    $check1 = array_values($checkHistory)[0];
    $check2 = count($checkHistory)>1?array_values($checkHistory)[1]:null;
}
//cassiopeia_dump($checkHistory);
try{

}catch (Exception $e){
//    cassiopeia_dump($e);
}
$stt=1;
?>

<input type="hidden" id="project_id" value="<?php echo($project_id); ?>">
<input type="hidden" id="project_domain" value="<?php echo($project->field_domain['und'][0]['value']); ?>">

<div class="page page-detail page-user-keyword-project-detail page-user-backlink-project-detail page-keyword-report">
    <div class="page-header">
        <div class="page-title">
            <h1><?php echo($project->title); ?> - <?php echo($project->field_domain['und'][0]['value']); ?></h1>
        </div>
        <div class="page-header-bottom">
            <div class="backlink-status">
                <ul>
                    <li><a href="/quan-ly-keywords/du-an/<?php echo $project->nid; ?>?top=all">Tất cả từ khoá (<?php echo $result->total; ?>)</a></li>
                    <li><a href="/quan-ly-keywords/du-an/<?php echo $project->nid; ?>?top=3">Top 1-3 (<?php echo $result->top_3; ?>)</a></li>
                    <li><a href="/quan-ly-keywords/du-an/<?php echo $project->nid; ?>?top=5">Top 1-5 (<?php echo $result->top_5; ?>)</a></li>
                    <li><a href="/quan-ly-keywords/du-an/<?php echo $project->nid; ?>?top=10">Top 1-10 (<?php echo $result->top_10; ?>)</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-tabs">
        <ul>
            <li><a href="/quan-ly-keywords/du-an/<?php echo $project_id; ?>">Quản lý Từ khóa</a></li>
            <li class="active"><a href="/quan-ly-keywords/du-an/<?php echo $project_id; ?>/bao-cao">Báo cáo</a></li>
        </ul>
    </div>
    <div class="page-body">
        <div class="statistic-keywords">
            <div class="statistic-keywords-container">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-2">
                        <div class="box-statistic">
                            <div class="box-statistic-title">
                                Trung bình
                            </div>
                            <div class="box-statistic-info">
                                <span><?php if(!empty($check1)) echo round($check1->AVG,2); ?></span>
                                <div class="box-statistic-compare">
                                    vs <?php echo !empty($check2)?round($check2->AVG,2):"-"; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-6 col-sm-6 col-md-2">
                        <div class="box-statistic">
                            <div class="box-statistic-title">
                                Top 3
                            </div>
                            <div class="box-statistic-info">
                                <span><?php if(!empty($check1)) echo $check1->top_3."/".$check1->total; ?></span>
                                <div class="box-statistic-compare">
                                    vs <?php if(!empty($check2)) echo $check2->top_3."/".$check2->total; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-6 col-sm-6 col-md-2">
                        <div class="box-statistic">
                            <div class="box-statistic-title">
                                Top 5
                            </div>
                            <div class="box-statistic-info">
                                <span><?php if(!empty($check1)) echo $check1->top_5."/".$check1->total; ?></span>
                                <div class="box-statistic-compare">
                                    vs <?php if(!empty($check2)) echo $check2->top_5."/".$check2->total; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-6 col-sm-6 col-md-2">
                        <div class="box-statistic">
                            <div class="box-statistic-title">
                                Top 10
                            </div>
                            <div class="box-statistic-info">
                                <span><?php if(!empty($check1)) echo $check1->top_10."/".$check1->total; ?></span>
                                <div class="box-statistic-compare">
                                    vs <?php if(!empty($check2)) echo $check2->top_10."/".$check2->total; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-6 col-sm-6 col-md-2">
                        <div class="box-statistic">
                            <div class="box-statistic-title">
                                Top 30
                            </div>
                            <div class="box-statistic-info">
                                <span><?php if(!empty($check1)) echo $check1->top_30."/".$check1->total; ?></span>
                                <div class="box-statistic-compare">
                                    vs <?php if(!empty($check2)) echo $check2->top_30."/".$check2->total; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-6 col-sm-6 col-md-2">
                        <div class="box-statistic">
                            <div class="box-statistic-title">
                                Top 100
                            </div>
                            <div class="box-statistic-info">
                                <span><?php if(!empty($check1)) echo $check1->top_100."/".$check1->total; ?></span>
                                <div class="box-statistic-compare">
                                    vs <?php if(!empty($check2)) echo $check2->top_100."/".$check2->total; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $maxNumber = 20;
        $current = 100;
        $FromDateTimestamp = strtotime(date("d-m-Y",REQUEST_TIME)." - 7 days");
        $ToDateTimestamp = REQUEST_TIME;
        if(!empty($check1)){
            $dataSet1 = array();
            $dataSet2 = array();
            for($i=1;$i<=10;$i++){
                $key = "top_".$i;
                array_push($dataSet1,$check1->$key);
            }
            if(!empty($check2)){
                for($i=1;$i<=10;$i++){
                    $key = "top_".$i;
                    array_push($dataSet2,$check2->$key);
                }

            }

            $query = db_select("tbl_check_keyword","tbl_check_keyword");
            $query->fields("tbl_check_keyword");
            $query->condition("uid",$user->uid);
            $query->condition("nid",$project->nid);
            $query->condition("tbl_check_keyword.date",array($FromDateTimestamp,$ToDateTimestamp),"BETWEEN");
            $lineResult = $query->execute()->fetchAll();
            $lineData = array();
            $lineDataSet = array();

            if(!empty($lineResult)){
                foreach($lineResult as $item){
                    $lineData[$item->date] = $item;
                }
            }

            for($i=$FromDateTimestamp;$i<=$ToDateTimestamp;$i+=86400){
                if(!empty($lineData[$i])){
                    $current = $lineData[$i]->AVG;
                }
                $maxNumber = $maxNumber<$current?$current:$maxNumber;
                $lineDataSet[] = $current;
            }
        }else{

        }
        $ValueX = array();
        for($i=$FromDateTimestamp;$i<=$ToDateTimestamp;$i+=86400){
            array_push($ValueX,date('d/m',$i));
        }
        $display = true;
        if(empty($dataSet1) && empty($dataSet2)){
            $display = false;
        }
        if(empty($dataSet1)){
            $dataSet1 = array(0=>array());
        }
        if(empty($dataSet2)){
            $dataSet2 = array(0=>array());
        }
        if(empty($lineDataSet)){
            $lineDataSet = array(0=>array());
        }
//        print_r($dataSet1);
        ?>
        <input hidden type="text" value='<?php echo json_encode($ValueX); ?>' id="ValueX">
        <input display="<?php echo $display; ?>" hidden type="text" id="barChartDataSet1" value='<?php  echo json_encode($dataSet1); ?>' data-label="<?php if(!empty($check1)) echo "Thứ hang ".date("d/m/Y",$check1->date); ?>">
        <input  hidden type="text" id="barChartDataSet2" value='<?php  echo json_encode($dataSet2); ?>' data-label="<?php if(!empty($check2)) echo "Thứ hạng ".date("d/m/Y",$check2->date); ?>">
        <input hidden type="text" id="lineDataSet" value='<?php  echo json_encode($lineDataSet); ?>'>
        <input type="hidden" id="maxNumber" value="<?php echo $maxNumber; ?>">
        <div class="statistic-keywords-chart">
            <div class="statistic-keywords-chart-container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="statistic-keywords-chart-item">
                            <div class="title">Số lượng từ khóa lọt top 10</div>
<!--                            <div class="chart">-->
<!--<!--                                <canvas id="barChart" style="width:100%;"></canvas>-->-->
<!--                            </div>-->
                        </div>
                    </div>

                    <div class="col-md-6">
<!--                        <div class="statistic-keywords-chart-item">-->
<!--                            <div class="title">Số liệu tăng trưởng</div>-->
<!--<!--                            <div class="chart">-->-->
<!--<!--                                <canvas id="lineChart" style="width:100%;"></canvas>-->-->
<!--<!--                            </div>-->-->
<!--                        </div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
