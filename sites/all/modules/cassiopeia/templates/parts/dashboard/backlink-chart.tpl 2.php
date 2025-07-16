<?php

global $user;

if(empty($user->uid)){
//    drupal_goto("/index.html");
}

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
$totalBacklinks = cassiopeia_get_items_by_conditions($conditions,"project_backlink","node");
$conditions['range'] = array(
    "type"      => "range",
    "start"     => 0,
    "limit"     => 5,
);
$project_backlinks = cassiopeia_get_items_by_conditions($conditions,"project_backlink","node");
//_print_r($project_backlinks)
$maxNumber = 0;
$dataBacklinks = array();
$TotalBacklink = 0;
$ProjectBanklink = array();
$ValueX = array();
$FromDateTimestamp = strtotime($cache['from_date']);
$ToDateTimestamp = strtotime($cache['to_date']);
if(!empty($project_backlinks)){
    foreach($project_backlinks as $project_backlink){
        $dataBackLink = array();
        $TotalBanklinkByProject = 0;
        $cache['project'] = $project_backlink;
        $_backlinks = cassiopeia_get_backlink_project_report($cache);
        $domains = cassiopeia_get_backlink_domain_report($cache);

        $backLinkList = array();
        $query = db_select("tbl_backlink_report","tbl_backlink_report");
        $query->fields("tbl_backlink_report");
        $query->condition("tbl_backlink_report.nid",$project_backlink->nid);
        $query->orderBy("tbl_backlink_report.date","DESC");
        $query->range(0,1);
        $last_report = $query->execute()->fetchObject();
//        _print_r($last_report);
        $query = db_select("tbl_backlink_report","tbl_backlink_report");
        $query->fields("tbl_backlink_report");
        $query->condition("tbl_backlink_report.nid",$project_backlink->nid);
        $query->condition("tbl_backlink_report.date",array(strtotime(date("01-m-Y 00:00:00",strtotime($cache['from_date']))),strtotime(date("t-m-Y 23:59:59",strtotime($cache['to_date'])))),"BETWEEN");
        $report = $query->execute()->fetchAll();
        $reportList = [];
        if(!empty($report)){
            foreach($report as $item){
                $reportList[$item->date] = $item;
            }
        }
        $lastValue = !empty($last_report)?$last_report->domain_count:0;
        $temp = 0;
//        print_r($last_report);
        if(!empty($last_report)){
            if($FromDateTimestamp>$last_report->date){
                $temp = $lastValue;
            }
        }else{
            $query = db_select("tbl_backlink","tbl_backlink");
            $query->fields("tbl_backlink");
            $query->join("tbl_backlink_detail","tbl_backlink_detail","tbl_backlink.id=tbl_backlink_detail.nid");
            $query->addExpression("COUNT(tbl_backlink_detail.id)","TotalBacklink");
            $query->condition("tbl_backlink.pid",$project_backlink->nid);
            $__backlinkResult = $query->execute()->fetchObject();
            $query = db_select("tbl_backlink","tbl_backlink");
            $query->fields("tbl_backlink");
            $query->addField("tbl_backlink","id","bid");
            $query->condition("tbl_backlink.pid",$project_backlink->nid);
            $query->groupBy("domain");
            $__domains = $query->execute()->fetchAll();
            try{
                db_insert("tbl_backlink_report")->fields(array(
                    "created"   => REQUEST_TIME,
                    "date"   => strtotime(date("d-m-Y 00:00:00",REQUEST_TIME)),
                    "backlink_count"    => $__backlinkResult->TotalBacklink,
                    "domain_count"    => count($__domains),
                    "nid"    => $project_backlink->nid,
                ))->execute();
                $query = db_select("tbl_backlink_report","tbl_backlink_report");
                $query->fields("tbl_backlink_report");
                $query->condition("tbl_backlink_report.nid",$project_backlink->nid);
                $query->orderBy("tbl_backlink_report.date","DESC");
                $query->range(0,1);
                $last_report = $query->execute()->fetchObject();
                if($FromDateTimestamp>$last_report->date){
                    $temp = $lastValue;
                }
                $query = db_select("tbl_backlink_report","tbl_backlink_report");
                $query->fields("tbl_backlink_report");
                $query->condition("tbl_backlink_report.nid",$project_backlink->nid);
                $query->condition("tbl_backlink_report.date",array(strtotime(date("01-m-Y 00:00:00",strtotime($cache['from_date']))),strtotime(date("t-m-Y 23:59:59",strtotime($cache['to_date'])))),"BETWEEN");
                $report = $query->execute()->fetchAll();
                $reportList = [];
                if(!empty($report)){
                    foreach($report as $item){
                        $reportList[$item->date] = $item;
                    }
                }
            }catch (Exception $e){

            }
        }
//        _print_r($temp);
        for($i=$FromDateTimestamp;$i<=$ToDateTimestamp;$i+=86400){
            if(empty($temp)){
                $temp = !empty($reportList[$i])?$reportList[$i]->domain_count:0;
            }
            $current = !empty($reportList[$i])?$reportList[$i]->domain_count:$temp;
            $TotalBacklink+=$current;
            $TotalBanklinkByProject=$current;
            $maxNumber = $maxNumber<$current?$current:$maxNumber;

            array_push($dataBackLink,$current);
        }
        $ProjectBanklink[$project_backlink->nid]['BanklinkCount'] = $TotalBanklinkByProject;
        $ProjectBanklink[$project_backlink->nid]['Project'] = $project_backlink;
        $ProjectTitle = $project_backlink->title;
        $P = vn_to_str($ProjectTitle);
//        $StrLen = strlen($P);
//        if($StrLen>10){
//            $temp = strrev($ProjectTitle);
//            $temp = substr($temp,$StrLen-10);
//            $ProjectTitle = strrev($temp)."...";
//        }
        $dataBacklinks[] = array(
            "title" => $ProjectTitle,
            "value" => $dataBackLink,
            "alt"   => $project_backlink->title,
        );
    }
}
//_print_r($dataBacklinks);
for($i=$FromDateTimestamp;$i<=$ToDateTimestamp;$i+=86400){
    array_push($ValueX,date('d/m',$i));
}
$maxNumber = $maxNumber==0?1000:$maxNumber;
$DisplayLegend = true;

//print_r($dataBacklinks);
if(empty($dataBacklinks)){
    $DisplayLegend = false;
}
$dataBacklinks = !empty($dataBacklinks)?$dataBacklinks:array(0=>array());
//$dataBacklinks = array_reverse($dataBacklinks);
//$ProjectBanklink = array_reverse($ProjectBanklink);
//print_r($dataBacklinks);
$colorList = array(
    1 => "#1f9f4c",
    2 => "#f8a03b",
    3 => "#0084ff",
    4 => "#ef9ac4",
    5 => "#f8de66",
);

?>
<input hidden type="text" value='<?php echo json_encode($ValueX); ?>' id="ValueX">
<input hidden type="text" id="dataProjectBanklink" value='<?php echo json_encode($dataBacklinks); ?>' display-legend="<?php echo $DisplayLegend; ?>">
<input hidden type="text" id="maxBacklink" value='<?php echo $maxNumber; ?>'>

<canvas id="back_link_chart" style="width:100%;height: 100%;"></canvas>

<script>
    (function($) {
        try {
            // Kiểm tra xem Chart đã được định nghĩa chưa
            if (typeof Chart === 'undefined') {
                console.error('Chart.js is not loaded');
                return;
            }

            var colorArray = ["#F9AA4D"];
            let displayLegend = jQuery("#dataProjectBanklink").attr("display-legend");
            let maxNumber = jQuery("#maxBacklink").val();

            if (maxNumber < 1) {
                console.warn('No data available for chart');
                return;
            }

            let xValues = jQuery("#ValueX").val();
            let dataProjectBanklink = JSON.parse(jQuery("#dataProjectBanklink").val());

            if (!dataProjectBanklink || !dataProjectBanklink.length) {
                console.warn('No project data available');
                return;
            }

            let dataSet = [];
            let stt = 0;

            $.each(dataProjectBanklink, function(index, value) {
                if (!value || !value.value) return;

                let _data = {
                    label: value.title,
                    data: value.value,
                    borderColor: colorArray[stt % colorArray.length],
                    backgroundColor: colorArray[stt % colorArray.length],
                    fill: false,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 5
                };

                dataSet.push(_data);
                stt++;
            });

            const ctx = document.getElementById('back_link_chart').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: JSON.parse(xValues),
                    datasets: dataSet
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: displayLegend === 'true',
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: maxNumber,
                            ticks: {
                                stepSize: Math.ceil(maxNumber / 5)
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        } catch (error) {
            console.error('Error creating chart:', error);
        }
    })(jQuery);
</script>
