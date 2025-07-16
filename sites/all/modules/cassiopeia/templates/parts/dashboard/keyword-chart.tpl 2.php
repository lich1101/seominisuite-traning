<?php
global $user;
$cache = array();
$cache['from_date'] = date("d-m-Y",strtotime(date("d-m-Y",REQUEST_TIME)." - 14 days"));
$cache['to_date'] = date("d-m-Y",REQUEST_TIME);
$conditions = array();
$conditions['status'] = array(
    "type"      => "propertyCondition",
    "value"     => 1,
    "condition" => "=",
);
$conditions['changed'] = array(
    "type"      => "propertyOrderBy",
    "direction" => "DESC",
);
$conditions['uid'] = array(
    "type"      => "propertyCondition",
    "value"     => $user->uid,
    "condition" => "="
);
$conditions['nid'] = array(
    "type"      => "propertyCondition",
    "value"     => $pid,
    "condition" => "="
);
$totalKeywords = cassiopeia_get_items_by_conditions($conditions,"project_keyword","node");
$conditions['range'] = array(
    "type"      => "range",
    "start"     => 0,
    "limit"     => 5,
);
$project_keywords = cassiopeia_get_items_by_conditions($conditions,"project_keyword","node");
$maxNumber = 0;
$count = 14;
$TotalKeyword = 0;
$ProjectKeyword = array();
$keywordX = array();
$dataKeywords = array();
$_index=1;
$FromDateTimestamp = strtotime($cache['from_date']);
$ToDateTimestamp = strtotime($cache['to_date']);
if(!empty($project_keywords)){
    foreach($project_keywords as $project_keyword){
        $current = 100;
        $best = 100;
        $lineDataSet = array();
        $dataKeyword = array();
        $lineResult = array();
        $lineData = array();
        $TotalKeywordByProject = 0;

        $cache['project'] = $project_keyword;
        $keywords = cassiopeia_get_keyword_by_conditions($cache);

        $query = db_select("tbl_check_keyword","tbl_check_keyword");
        $query->fields("tbl_check_keyword");
        $query->condition("uid",$user->uid);
        $query->condition("nid",$cache['project']->nid);
        $query->condition("tbl_check_keyword.date",array($FromDateTimestamp,$ToDateTimestamp),"BETWEEN");
        $lineResult = $query->execute()->fetchAll();
//        _print_r($lineResult);
        if(empty($lineResult)){
            $query = db_select("tbl_check_keyword","tbl_check_keyword");
            $query->fields("tbl_check_keyword");
            $query->condition("uid",$user->uid);
            $query->condition("nid",$cache['project']->nid);
            $query->range(0,1);
            $lineLastResult = $query->execute()->fetchObject();
//            _print_r($lineLastResult);
            if(!empty($lineLastResult)){
                $current = $lineLastResult->AVG;
            }else{
                $query = db_select("node","tbl_node");
                $query->fields("tbl_node");
                $query->condition("type","keyword");
                $query->join("field_data_field_keyword_project","field_keyword_project","field_keyword_project.entity_id=tbl_node.nid");
                $query->condition("field_keyword_project.field_keyword_project_nid",$project_keyword->nid);
                $query->leftJoin("field_data_field_keyword_position","field_keyword_position","field_keyword_position.entity_id=tbl_node.nid");
                $query->fields("field_keyword_position");
                $query->addExpression("COUNT(tbl_node.nid)","total");
                $query->addExpression("AVG(field_keyword_position.field_keyword_position_value)","AVG");
                $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=1 THEN 1 ELSE 0 END)","top_1");
                $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=2 THEN 1 ELSE 0 END)","top_2");
                $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=3 THEN 1 ELSE 0 END)","top_3");
                $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=4 THEN 1 ELSE 0 END)","top_4");
                $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=5 THEN 1 ELSE 0 END)","top_5");
                $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=6 THEN 1 ELSE 0 END)","top_6");
                $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=7 THEN 1 ELSE 0 END)","top_7");
                $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=8 THEN 1 ELSE 0 END)","top_8");
                $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=9 THEN 1 ELSE 0 END)","top_9");
                $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=10 THEN 1 ELSE 0 END)","top_10");
                $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=30 THEN 1 ELSE 0 END)","top_30");
                $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=100 THEN 1 ELSE 0 END)","top_100");
                $result = $query->execute()->fetchObject();
                $query = db_select("tbl_check_keyword","tbl_check_keyword");
                $query->fields("tbl_check_keyword",array("id"));
                $query->condition("uid",$user->uid);
                $query->condition("nid",$project->nid);
                $query->condition("date",strtotime(date("d-m-Y 00:00:00",REQUEST_TIME)));
                $check = $query->execute()->fetchObject();
                if(!empty($check)){
                    try{
                        db_insert("tbl_check_keyword")->fields(array(
                            "date"          => strtotime(date("d-m-Y 00:00:00",REQUEST_TIME)),
                            "created"       => REQUEST_TIME,
                            "uid"           => $user->uid,
                            "nid"           => $project->nid,
                            "AVG"           => $result->AVG,
                            "top_1"         => $result->top_1,
                            "top_2"         => $result->top_2,
                            "top_3"         => $result->top_3,
                            "top_4"         => $result->top_4,
                            "top_5"         => $result->top_5,
                            "top_6"         => $result->top_6,
                            "top_7"         => $result->top_7,
                            "top_8"         => $result->top_8,
                            "top_9"         => $result->top_9,
                            "top_10"        => $result->top_10,
                            "top_30"        => $result->top_30,
                            "top_100"       => $result->top_100,
                            "total"         => $result->total,
                        ))->execute();
                        $query = db_select("tbl_check_keyword","tbl_check_keyword");
                        $query->fields("tbl_check_keyword");
                        $query->condition("uid",$user->uid);
                        $query->condition("nid",$cache['project']->nid);
                        $query->range(0,1);
                        $lineLastResult = $query->execute()->fetchObject();
//            _print_r($lineLastResult);
                        if(!empty($lineLastResult)){
                            $current = $lineLastResult->AVG;
                        }
                        $query = db_select("tbl_check_keyword","tbl_check_keyword");
                        $query->fields("tbl_check_keyword");
                        $query->condition("uid",$user->uid);
                        $query->condition("nid",$cache['project']->nid);
                        $query->condition("tbl_check_keyword.date",array($FromDateTimestamp,$ToDateTimestamp),"BETWEEN");
                        $lineResult = $query->execute()->fetchAll();
                    }catch (Exception $e){

                    }
                }
            }
        }
        if(!empty($lineResult)){
            foreach($lineResult as $item){
                $lineData[$item->date] = $item;
            }
        }

        for($i=$FromDateTimestamp;$i<=$ToDateTimestamp;$i+=86400){
            if(!empty($lineData[$i])){
                $current = $lineData[$i]->AVG;
            }else{

            }
            $current = round($current,2);
            if($current==0) $current = 100;
//                                                cassiopeia_dump($current);
            $best = $best>$current?$current:$best;
            $lineDataSet[] = $current;
            if($_index==1){
//                        array_push($keywordX,date('d/m',$i));
            }
            $count++;
        }

        $_index++;
        $ProjectKeyword[$project_keyword->nid]['best'] = $best;
        $ProjectKeyword[$project_keyword->nid]['Project'] = $project_keyword;
        $ProjectTitle = $project_keyword->title;
        $P = vn_to_str($ProjectTitle);
        $StrLen = strlen($P);
        if($StrLen>10){
            $temp = strrev($ProjectTitle);
            $temp = substr($temp,$StrLen-10);
            $ProjectTitle = strrev($temp)."...";
        }
        $dataKeywords[] = array(
            "title"     => $ProjectTitle,
            "value"     => $lineDataSet,
        );
    }

}
for($i=$FromDateTimestamp;$i<=$ToDateTimestamp;$i+=86400){
    array_push($keywordX,date('d/m',$i));
}
$maxNumber = $maxNumber==0?100:$maxNumber;
$DisplayLegend = true;
if(empty($dataKeywords)){
    $DisplayLegend = false;
}
$dataKeywords = !empty($dataKeywords)?$dataKeywords:array(0=>array());
//                                    cassiopeia_dump($dataKeywords);
?>

<input hidden type="text" id="maxKeyword" value='<?php echo $maxNumber; ?>'>
<input hidden type="text" value='<?php echo json_encode($keywordX); ?>' id="keywordX">
<input hidden type="text" id="dataProjectKeyword" value='<?php echo json_encode($dataKeywords); ?>' display-legend="<?php echo $DisplayLegend; ?>">

<canvas id="keyword_chart" style="width:100%;height: 100%;"></canvas>

<script>
    (function($) {
        var colorArray = ["#0084FF"];
        let displayLegend = jQuery("#dataProjectKeyword").attr("display-legend");
        let maxNumber = jQuery("#maxKeyword").val();
        let xValues = jQuery("#keywordX").val();
        let dataProjectBanklink = JSON.parse(jQuery("#dataProjectKeyword").val());
        if (JSON.parse(xValues).length < 1) {
            return false;
        }
        let dataSet = [];
        let stt = 0;
        $.each(dataProjectBanklink, function(index, value) {
            let _data = {};
            _data['label'] = value.title;
            _data['data'] = value.value;
            _data['backgroundColor'] = colorArray[stt];
            _data['fill'] = false
            _data['borderColor'] = colorArray[stt];
            dataSet.push(_data);
            stt++;
            console.log("_data", _data);
        });
        // }

        // return;
        // let dataDomain = jQuery("#dataDomain").val();
        new Chart("keyword_chart", {
            type: "line",
            data: {
                labels: JSON.parse(xValues),
                datasets: dataSet,
            },
            options: {
                // legend: { display: false },
                scales: {
                    y: {
                        reverse: true,
                        min: 0,
                        max: parseInt(maxNumber),
                    },
                },
                plugins: {
                    legend: {
                        display: false,
                        // maxWidth: 5,
                        position: 'bottom',
                        align: 'start',
                        labels: {
                            padding: 20
                        },
                    }
                }
            }
        });
    })(jQuery);
</script>