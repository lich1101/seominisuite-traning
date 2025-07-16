<?php
global $user;
//$user = user_load(1);
//$pid = 1882416;
$conditions = array();
$conditions['pid'] = 1919695;
$backlinks = cassiopeia_get_backlinks($conditions);
_print_r($backlinks);
die;
$responseObject = json_decode($_REQUEST['responseObject']);
$query = db_select("tbl_backlink","tbl_backlink");
$query->fields("tbl_backlink");
$query->condition("id",3355691  );
$backlink = $query->execute()->fetchObject();


//$query = db_select("tbl_backlink_report","tbl_backlink_report");
//$query->fields("tbl_backlink_report");
//$query->condition("nid",$backlink->pid);
//$query->condition("date",strtotime(date("d-m-Y 00:00:00",REQUEST_TIME)));
//$check = $query->execute()->fetchAll();
//
$query_detail = db_select("tbl_backlink_detail","tbl_backlink_detail");
$query_detail->fields("tbl_backlink_detail");
$query->condition("tbl_backlink_detail.pid",1919695);

$query = db_select("tbl_backlink","tbl_backlink");
$query->fields("tbl_backlink");
$query->join($query_detail,"tbl_backlink_detail","tbl_backlink.id=tbl_backlink_detail.nid");
$query->addExpression("COUNT(tbl_backlink_detail.id)","TotalBacklink");
$query->condition("tbl_backlink.pid",$backlink->pid);
$backlinkResult = $query->execute()->fetchObject();
_print_r($backlink);
//_print_r($check);
_print_r($backlinkResult);
die;
if($user->uid!=31){
    $query = db_select("tbl_backlink","tbl_backlink");
    $query->fields("tbl_backlink");
    $query->addField("tbl_backlink","id","bid");
    $query->condition("tbl_backlink.pid",$backlink->pid);
    $query->groupBy("domain");
    $domains = $query->execute()->fetchAll();
    $_node = node_load($backlink->pid);
    node_save($_node);
    if(empty($check)){
        try{
            db_insert("tbl_backlink_report")->fields(array(
                "created"   => REQUEST_TIME,
                "date"   => strtotime(date("d-m-Y 00:00:00",REQUEST_TIME)),
                "backlink_count"    => $backlinkResult->TotalBacklink,
                "domain_count"    => count($domains),
                "nid"    => $backlink->pid,
            ))->execute();
        }catch (Exception $e){

        }
    }else{
        try{
            db_update("tbl_backlink_report")->fields(array(
                "created"   => REQUEST_TIME,
                //                                "date"   => strtotime(date("d-m-Y 00:00:00",REQUEST_TIME)),
                "backlink_count"    => $backlinkResult->TotalBacklink,
                "domain_count"    => count($domains),
                //                                "nid"    => $backlink->pid,
            ))->condition("nid",$backlink->pid)->condition("date",strtotime(date("d-m-Y 00:00:00",REQUEST_TIME)))->execute();
        }catch (Exception $e){

        }
    }
}