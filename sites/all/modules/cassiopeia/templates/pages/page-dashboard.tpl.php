<?php
drupal_add_js(drupal_get_path('theme', 'cassiopeia_theme') . '/js/page-home.js', ['weight' => 1000]);
print render($page['content']['metatags']);

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
//$conditions['nid'] = array(
//    "type"      => "propertyCondition",
//    "value"     => $pid,
//    "condition" => "="
//);
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
//print_r($cache);
$FromDateTimestamp = strtotime($cache['from_date']);
$ToDateTimestamp = strtotime($cache['to_date']);
if(!empty($project_backlinks)){
    foreach($project_backlinks as $project_backlink){
        $dataBackLink = array();
        $TotalBanklinkByProject = 0;
        $cache['project'] = $project_backlink;
//        $_backlinks = cassiopeia_get_backlink_project_report($cache);
//        $domains = cassiopeia_get_backlink_domain_report($cache);

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
<div class="home-page">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 home-backlinks home-items">
            <div class="home-items-content">
                <div class="home-item">
                    <div class="home-item-header">
                        <div class="statistic">
                            <!-- <div class="statistic-img">
                                <span class="icon">
                                    <img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-9.png" class="img-responsive" alt="">
                                </span>
                            </div> -->
                            <div class="statistic-detail">
                                <h3>Số lượng Domain</h3>
                                <span>Tổng quan dự án</span>
                                <!-- <span class="amount"><?php echo count($totalBacklinks); ?></span> -->
                            </div>
                        </div>
                        <div class="choose-project">
                            <span>Chọn dự án:</span>
                            <div class="select-box">
                                <div class="select-box__current" tabindex="1">
                                    <?php if(!empty($project_backlinks)): ?>
                                        <?php $stt=1; foreach((array)$project_backlinks as $project_backlink): ?>
                                            <div class="select-box__value">
                                                <input class="select-box__input" type="radio" id="domain-<?php echo $project_backlink->nid ?>" value="<?php echo $project_backlink->nid ?>" name="domain" <?php if($stt==1) echo "checked"; ?>/>
                                                <div class="select-box__input-text">
                                                    <span>
                                                        <?php echo htmlspecialchars($project_backlink->title) ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <?php $stt++; endforeach; ?>
                                    <?php else: ?>
<!--                                        --><?php //$stt=1; foreach((array)$project_backlinks as $project_backlink): ?>
                                            <div class="select-box__value">
                                                <input class="select-box__input" type="radio" id="domain-" value="_none" name="domain" checked/>
                                                <p class="select-box__input-text">Chưa có dự án</p>
                                            </div>
<!--                                            --><?php //$stt++; endforeach; ?>
                                    <?php endif; ?>
                                    <img class="select-box__icon" src="http://cdn.onlinewebfonts.com/svg/img_295694.svg" alt="Arrow Icon" aria-hidden="true"/>
                                </div>
                                <ul class="select-box__list">
                                    <?php $stt=1; foreach((array)$project_backlinks as $project_backlink): ?>
                                        <li>
                                            <label class="select-box__option" for="domain-<?php echo $project_backlink->nid ?>" aria-hidden="aria-hidden"> <?php echo $stt." ". htmlspecialchars($project_backlink->title)?></label>
                                        </li>
                                    <?php $stt++; endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="home-item-chart">
                        <!-- <div class="canvas-title">Số lượng Domain của các dự án Backlink gần đây</div> -->
                        <?php
                        $image_mobile_url = image_style_url("style_337x390","public://logo1.png");
                        $image_url = image_style_url("style_724x362","public://logo1.png");
                        ?>
                        <div class="canvas-zone">
                            <div class="mobile-bg visible-xs">
                                <img src="<?php echo $image_mobile_url; ?>" alt="">
                            </div>
<!--                            <div class="desktop-bg hidden-xs">-->
<!--                                <img src="--><?php //echo $image_url; ?><!--" alt="">-->
<!--                            </div>-->
                            <div class="backlink-chart-result">

                            </div>
                        </div>
<!--                        <div class="px-15">-->
<!--                            <div class="backlinkChartLabel row">-->
<!--                                --><?php //if(!empty($ProjectBanklink)): $stt=1; ?>
<!--                                    --><?php //foreach($ProjectBanklink as $item): ?>
<!--                                        <div class="item col-md-4">-->
<!--                                            <span style="background-color:--><?php //echo $colorList[$stt]; ?><!--"></span> <label title="--><?php //echo $item['Project']->title; ?><!--" for="">--><?php //echo $item['Project']->title; ?><!--</label>-->
<!--                                        </div>-->
<!--                                        --><?php //$stt++; endforeach; ?>
<!--                                --><?php //endif; ?>
<!--                            </div>-->
<!--                        </div>-->
                    </div>
                    <div class="home-item-table table-responsive">
                        <table class="table table-striped table-div-responsive table-type-1">
                            <thead>
                            <tr>
                                <th scope="col" class="item-center">STT</th>
                                <th class="sort current" data-sort="title" data-direction="ASC" scope="col">Tên dự án</th>
                                <th class="sort current" data-sort="domain" data-direction="ASC" scope="col">Website</th>
                                <th data-sort="total" data-direction="ASC" scope="col" class="text-right sort current">Số lượng Domain</th>
                                <th data-sort="created" data-direction="ASC" scope="col" class="text-center sort current">Ngày cập nhật</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($ProjectBanklink)): $stt=1; ?>
                                <?php foreach($ProjectBanklink as $item): ?>
                                    <?php $project = $item['Project']; ?>
                                    <tr class="" data-key="<?php echo htmlspecialchars($project->title); ?>">
                                        <td  class="text-center col-stt">
                                            <?php echo $stt; ?>
                                        </td>
                                        <td data-key="title" class="text-green " >
                                            <a href="/quan-ly-backlink/du-an/<?php echo $project->nid; ?>" title="<?php echo htmlspecialchars($project->title); ?>" class="sort-text">
                                                <?php echo htmlspecialchars($project->title); ?>
                                            </a>
                                            <a class="go-to-detail" href="/quan-ly-backlink/du-an/<?php echo $project->nid; ?>"></a>
                                        </td>
                                        <td data-key="domain">
                                            <?php
                                            $url = str_replace("https://","",$project->field_domain['und'][0]['value']);
                                            $url = str_replace("http://","",$url);
                                            $s1 = explode("/",$url);
                                            $domain = $s1[0];
                                            $domain = "http://".$domain;
                                            ?>
                                            <div class="d-flex space-between">
                                                <span class="sort-text" target="_blank" href="<?php echo $domain; ?>" title="<?php echo !empty($project->field_domain['und'])?$project->field_domain['und'][0]['value']:""; ?>">
                                                    <?php echo !empty($project->field_domain['und'])?$project->field_domain['und'][0]['value']:""; ?>
                                                </span>
                                                <a target="_blank" href="<?php echo $domain; ?>" title="<?php echo !empty($project->field_domain['und'])?$project->field_domain['und'][0]['value']:""; ?>">
                                                    <i class="fa fa-external-link"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td data-key="total" class="text-right">
                                            <?php echo $item['BanklinkCount']; ?>
                                            <a class="go-to-detail" href="/quan-ly-backlink/du-an/<?php echo $project->nid; ?>"></a>
                                        </td>
                                        <td data-key="created" data-value="<?php echo $project->changed; ?>" class="text-center">
                                            <?php echo date("d-m-Y",$project->changed); ?>
                                            <a class="go-to-detail" href="/quan-ly-backlink/du-an/<?php echo $project->nid; ?>"></a>
                                        </td>
                                    </tr>
                                    <?php $stt++; endforeach; ?>
                            <?php else: ?>
                                    <tr class="">
                                        <td  class="text-center col-stt">
                                            ...
                                        </td>
                                        <td data-key="title" class="text-green " >
                                            ...
                                        </td>
                                        <td data-key="domain">
                                           ...
                                        </td>
                                        <td data-key="total" class="text-right">
                                            ...
                                        </td>
                                        <td data-key="created" class="text-center">
                                            ...
                                        </td>
                                    </tr>
                                    <tr class="">
                                        <td  class="text-center col-stt">
                                            ...
                                        </td>
                                        <td data-key="title" class="text-green " >
                                            ...
                                        </td>
                                        <td data-key="domain">
                                           ...
                                        </td>
                                        <td data-key="total" class="text-right">
                                            ...
                                        </td>
                                        <td data-key="created" class="text-center">
                                            ...
                                        </td>
                                    </tr>
                                    <tr class="">
                                        <td  class="text-center col-stt">
                                            ...
                                        </td>
                                        <td data-key="title" class="text-green " >
                                            ...
                                        </td>
                                        <td data-key="domain">
                                           ...
                                        </td>
                                        <td data-key="total" class="text-right">
                                            ...
                                        </td>
                                        <td data-key="created" class="text-center">
                                            ...
                                        </td>
                                    </tr>
                                    <tr class="">
                                        <td  class="text-center col-stt">
                                            ...
                                        </td>
                                        <td data-key="title" class="text-green " >
                                            ...
                                        </td>
                                        <td data-key="domain">
                                           ...
                                        </td>
                                        <td data-key="total" class="text-right">
                                            ...
                                        </td>
                                        <td data-key="created" class="text-center">
                                            ...
                                        </td>
                                    </tr>
                                    <tr class="">
                                        <td  class="text-center col-stt">
                                            ...
                                        </td>
                                        <td data-key="title" class="text-green " >
                                            ...
                                        </td>
                                        <td data-key="domain">
                                           ...
                                        </td>
                                        <td data-key="total" class="text-right">
                                            ...
                                        </td>
                                        <td data-key="created" class="text-center">
                                            ...
                                        </td>
                                    </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="home-item-actions">
                        <a href="#" class="btn-df btn-green btn-ad-new btn-add-backlink-project" data-toggle="modal" data-target="#backlinkModal">Thêm dự án</a>
                        <a href="/quan-ly-backlink" class="btn-df btn-border btn-view-all">Xem tất cả</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
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
        $totalKeywords = cassiopeia_get_items_by_conditions($conditions,"project_keyword","node");
        $conditions['range'] = array(
            "type"      => "range",
            "start"     => 0,
            "limit"     => 5,
        );
        $project_keywords = cassiopeia_get_items_by_conditions($conditions,"project_keyword","node");
        //                                    $project_keywords = array_reverse($project_keywords);
        //                                    echo count($project_keywords);
        //                                    cassiopeia_dump($project_keywords);
        $maxNumber = 0;
        $count = 14;
        $TotalKeyword = 0;
        $ProjectKeyword = array();
        $keywordX = array();
        $dataKeywords = array();
        $_index=1;

        if(!empty($project_keywords)){
            foreach($project_keywords as $project_keyword){
//                $keywordX = array();
                $current = 100;
                $best = 100;
                $lineDataSet = array();
                $dataKeyword = array();
                $lineResult = array();
                $lineData = array();
                $TotalKeywordByProject = 0;

                $cache['project'] = $project_keyword;
//                $keywords = cassiopeia_get_keyword_by_conditions($cache);
                $FromDateTimestamp = strtotime($cache['from_date']);
                $ToDateTimestamp = strtotime($cache['to_date']);
                $query = db_select("tbl_check_keyword","tbl_check_keyword");
                $query->fields("tbl_check_keyword");
                $query->condition("uid",$user->uid);
                $query->condition("nid",$cache['project']->nid);
                $query->condition("tbl_check_keyword.date",array($FromDateTimestamp,$ToDateTimestamp),"BETWEEN");
                $lineResult = $query->execute()->fetchAll();
                if(empty($lineResult)){
                    $query = db_select("tbl_check_keyword","tbl_check_keyword");
                    $query->fields("tbl_check_keyword");
                    $query->condition("uid",$user->uid);
                    $query->condition("nid",$cache['project']->nid);
                    $query->range(0,1);
                    $lineLastResult = $query->execute()->fetchObject();
                    if(!empty($lineLastResult)){
                        $current = $lineLastResult->AVG;
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
//        $project_keywords = !empty($project_keywords)?$project_keywords:array("");
//        $project_keywords = [];
        ?>
        <div class="col-xs-12 col-sm-12 col-md-6 home-keywords home-items">
            <div class="home-items-content">
                <div class="home-item">
                    <div class="home-item-header">
                        <div class="statistic">
                            <!-- <div class="statistic-img">
                                <span class="icon">
                                    <img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-10.png" class="img-responsive" alt="">
                                </span>
                            </div> -->
                            <div class="statistic-detail">
                                <h3>Thứ hạng trung bình</h3>
                                <span>Tổng quan dự án</span>
                                <!-- <span class="amount"><?php echo count($totalKeywords); ?></span> -->
                            </div>
                        </div>
                        <div class="choose-project">
                            <span>Chọn dự án:</span>
                            <div class="select-box">
                                <div class="select-box__current" tabindex="1">
                                    <?php if(!empty($project_keywords)): ?>
                                        <?php $stt=1; foreach((array)$project_keywords as $project_keyword): ?>
                                            <div class="select-box__value">
                                                <input class="select-box__input" type="radio" id="keyword-<?php echo $project_keyword->nid ?>" value="<?php echo $project_keyword->nid ?>" name="keywords" <?php if($stt==1) echo "checked"; ?>/>
                                                <div class="select-box__input-text">
                                                    <span>
                                                        <?php echo htmlspecialchars($project_keyword->title) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php $stt++; endforeach; ?>
                                    <?php else: ?>
                                        <div class="select-box__value">
                                            <input class="select-box__input" type="radio" id="keyword-" value="_none" name="keywords" checked/>
                                            <p class="select-box__input-text">Chưa có dự án</p>
                                        </div>
                                    <?php endif; ?>
                                    <img class="select-box__icon" src="http://cdn.onlinewebfonts.com/svg/img_295694.svg" alt="Arrow Icon" aria-hidden="true"/>
                                </div>
                                <ul class="select-box__list">
                                    <?php $stt=1; foreach((array)$project_keywords as $project_keyword): ?>
                                        <li>
                                            <label class="select-box__option" for="keyword-<?php echo $project_keyword->nid ?>" aria-hidden="aria-hidden"><?php echo $stt ?> <?php echo $project_keyword->title?></label>
                                        </li>
                                    <?php $stt++; endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="home-item-chart">
                        <!-- <div class="canvas-title">Thứ hạng từ khóa trung bình của các dự án SEO gần đây</div> -->
                        <?php
                        $image_mobile_url = image_style_url("style_337x390","public://logo1.png");
                        $image_url = image_style_url("style_724x362","public://logo1.png");
                        ?>
                       <div class="canvas-zone">
                           <div class="mobile-bg visible-xs">
                               <img src="<?php echo $image_mobile_url; ?>" alt="">
                           </div>
<!--                           <div class="desktop-bg hidden-xs">-->
<!--                               <img src="--><?php //echo $image_url; ?><!--" alt="">-->
<!--                           </div>-->
                           <div class="keyword-chart-result">

                           </div>
                       </div>

<!--                        <div class="px-15">-->
<!--                            <div class="backlinkChartLabel row">-->
<!--                                --><?php //if(!empty($project_keywords)): $stt=1; ?>
<!--                                    --><?php //foreach($project_keywords as $nid => $project): ?>
<!--                                        <div class="item col-md-4">-->
<!--                                            <span style="background-color:--><?php //echo $colorList[$stt]; ?><!--"></span> <label title="--><?php //echo $project->title; ?><!--" for="">--><?php //echo $project->title; ?><!--</label>-->
<!--                                        </div>-->
<!--                                        --><?php //$stt++; endforeach; ?>
<!--                                --><?php //endif; ?>
<!--                            </div>-->
<!--                        </div>-->
                    </div>
                    <div class="home-item-table table-responsive">
                        <table class="table table-striped table-div-responsive table-type-1">
                            <thead>
                            <tr>
                                <th scope="col" class="item-center">STT</th>
                                <th class="sort current" data-sort="title" data-direction="ASC" scope="col">Tên dự án</th>
                                <th class="sort current" data-sort="domain" data-direction="ASC" scope="col">Website</th>
                                <th data-sort="total" data-direction="ASC" scope="col" class="text-right sort current">Thứ hạng trung bình</th>
                                <th data-sort="created" data-direction="ASC" scope="col" class="text-center sort current">Ngày cập nhật</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($ProjectKeyword)): $stt=1; ?>
                                <?php foreach($ProjectKeyword as $item): ?>
                                    <?php $project = $item['Project']; ?>
                                    <tr>
                                        <td class="text-center col-stt"><?php echo $stt; ?>
                                        </td>
                                        <td data-key="title" class="text-green">
                                            <a class="sort-text" href="/quan-ly-keywords/du-an/<?php echo $project->nid; ?>" title="<?php echo $project->title; ?>">
                                                <?php echo $project->title; ?></a> <a  class="go-to-detail" href="/quan-ly-keywords/du-an/<?php echo $project->nid; ?>"></a>
                                        </td>
                                        <td data-key="domain">
                                            <?php
                                            $url = str_replace("https://","",$project->field_domain['und'][0]['value']);
                                            $url = str_replace("http://","",$url);
                                            $s1 = explode("/",$url);
                                            $domain = $s1[0];
                                            $domain = "http://".$domain;
                                            ?>
                                            <div class="d-flex space-between">
                                                <span class="sort-text" target="_blank" href="<?php echo $domain; ?>" title="<?php echo !empty($project->field_domain['und'])?$project->field_domain['und'][0]['value']:""; ?>">
                                                    <?php echo !empty($project->field_domain['und'])?$project->field_domain['und'][0]['value']:""; ?>
                                                </span>
                                                <a target="_blank" href="<?php echo $domain; ?>" title="<?php echo !empty($project->field_domain['und'])?$project->field_domain['und'][0]['value']:""; ?>">
                                                    <i class="fa fa-external-link"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td data-key="total" class="text-right"><?php echo $item['best']; ?> <a  class="go-to-detail" href="/quan-ly-keywords/du-an/<?php echo $project->nid; ?>"></a></td>
                                        <td data-key="created" data-value="<?php echo $project->changed; ?>" class="text-center"><?php echo date("d-m-Y",$project->changed); ?> <a  class="go-to-detail" href="/quan-ly-keywords/du-an/<?php echo $project->nid; ?>"></a></td>
                                    </tr>
                                    <?php $stt++; endforeach; ?>
                            <?php else: ?>
                                    <tr class="">
                                        <td  class="text-center col-stt">
                                            ...
                                        </td>
                                        <td data-key="title" class="text-green " >
                                            ...
                                        </td>
                                        <td data-key="domain">
                                           ...
                                        </td>
                                        <td data-key="total" class="text-right">
                                            ...
                                        </td>
                                        <td data-key="created" class="text-center">
                                            ...
                                        </td>
                                    </tr>
                                    <tr class="">
                                        <td  class="text-center col-stt">
                                            ...
                                        </td>
                                        <td data-key="title" class="text-green " >
                                            ...
                                        </td>
                                        <td data-key="domain">
                                           ...
                                        </td>
                                        <td data-key="total" class="text-right">
                                            ...
                                        </td>
                                        <td data-key="created" class="text-center">
                                            ...
                                        </td>
                                    </tr>
                                    <tr class="">
                                        <td  class="text-center col-stt">
                                            ...
                                        </td>
                                        <td data-key="title" class="text-green " >
                                            ...
                                        </td>
                                        <td data-key="domain">
                                           ...
                                        </td>
                                        <td data-key="total" class="text-right">
                                            ...
                                        </td>
                                        <td data-key="created" class="text-center">
                                            ...
                                        </td>
                                    </tr>
                                    <tr class="">
                                        <td  class="text-center col-stt">
                                            ...
                                        </td>
                                        <td data-key="title" class="text-green " >
                                            ...
                                        </td>
                                        <td data-key="domain">
                                           ...
                                        </td>
                                        <td data-key="total" class="text-right">
                                            ...
                                        </td>
                                        <td data-key="created" class="text-center">
                                            ...
                                        </td>
                                    </tr>
                                    <tr class="">
                                        <td  class="text-center col-stt">
                                            ...
                                        </td>
                                        <td data-key="title" class="text-green " >
                                            ...
                                        </td>
                                        <td data-key="domain">
                                           ...
                                        </td>
                                        <td data-key="total" class="text-right">
                                            ...
                                        </td>
                                        <td data-key="created" class="text-center">
                                            ...
                                        </td>
                                    </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="home-item-actions">
                        <a href="#" class="btn-df btn-green btn-ad-new btn-add-keyword-project">Thêm dự án</a>
                        <a href="/quan-ly-keywords" class="btn-df btn-border btn-view-all">Xem tất cả</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
