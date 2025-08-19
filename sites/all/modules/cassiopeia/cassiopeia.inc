<?php
drupal_session_start();

function cassiopeia_order_content_callback() {
    return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-order-content.tpl.php");
}
function cassiopeia_content_edit_page_callback($pid,$node) {
    return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-content-add.tpl.php",array("pid"=>$pid,"node"=>$node));
}
function cassiopeia_content_add_page_callback($pid) {
    return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-content-add.tpl.php",array("pid"=>$pid));
}
function cassiopeia_content_project_detail_page_callback($pid) {
    return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-content-project-detail.tpl.php",array("pid"=>$pid));
}
function cassiopeia_content_project_page_callback(){
    global $user;
    $arg = arg();
    if(!empty($arg[1]) && $arg[1]=="export"){
        $export = cassiopeia_create_content_projects();
        if(!empty($export)){
            drupal_goto("excel/download/".$export['name']);
        }else{

        }
    }else{
        return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-content-projects.tpl.php");
    }
}
function cassiopeia_get_url_page_callback() {
    return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-get-url.tpl.php");
}
function cassiopeia_booking_complete_page_callback() {
    return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-booking-complete.tpl.php");
}
function cassiopeia_admin_manager_report_page_callback() {
    return _cassiopeia_render_theme("module","cassiopeia","templates/admin/page-manager-report.tpl.php");
}
function cassiopeia_admin_manager_user_page_callback() {
    return _cassiopeia_render_theme("module","cassiopeia","templates/admin/page-manager-user.tpl.php");
}
function cassiopeia_user_dashboard_page_callback() {
    return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-dashboard.tpl.php");
}
function cassiopeia_booking_edit_callback($booking) {
    return _cassiopeia_render_theme("module","cassiopeia","templates/admin/page-booking-edit-page.tpl.php",array("booking"=>$booking));
}
function cassiopeia_backlink_project_detail_page_callback($pid) {
    global $user;
    $arg = arg();
    if(!empty($arg[3]) && $arg[3]=="export"){
        $packet = cassiopeia_get_available_packet_by_uid($user->uid);
        if($packet->product!=BASIC && $packet->expired>0 && $packet->expired<REQUEST_TIME) { // tài khoản hết hạn}
            cassiopeia_set_alert("UserExpired","Số lượng dự án backlink");
            drupal_goto("quan-ly-backlink/du-an/".$arg[2]);
        }else{
            if(!empty($packet->excel)){
                $_data = $_SESSION['export_data'][$_REQUEST['request_time']];
//                print_r($_data);
                try{
                    $sub_query_2 = db_select("tbl_backlink_detail","tbl_backlink_detail");
                    $sub_query_2->addField("tbl_backlink_detail","nid","nid");
                    $sub_query_2->addExpression("CASE WHEN tbl_backlink_detail.is_in_content is null THEN -1 ELSE tbl_backlink_detail.is_in_content END","is_in_content");
                    $sub_query_2->addExpression("CASE WHEN tbl_backlink_detail.url is null THEN '-' ELSE tbl_backlink_detail.url END","url");
                    $sub_query_2->addExpression("CASE WHEN tbl_backlink_detail.rel is null THEN '-' ELSE tbl_backlink_detail.rel END","rel");
                    $sub_query_2->addExpression("CASE WHEN tbl_backlink_detail.anchor_text is null THEN '-' ELSE tbl_backlink_detail.anchor_text END","anchor_text");
                    $sub_query_2->groupBy("tbl_backlink_detail.id");
                    $sub_query_2->condition("pid",$pid);
//                    $result_2 = $sub_query_2->execute()->fetchAll();
//                    _print_r($result_2);
//                    die;
                    $query = db_select("tbl_backlink","tbl_backlink");
                    $query->fields("tbl_backlink");
                    $query->addField("tbl_backlink","id","bid");
                    $query->condition("tbl_backlink.pid",$pid);
                    $query->addField("tbl_backlink","refer_page","source");
                    $query->addField("tbl_backlink","indexed","indexed");
                    $query->addField("tbl_backlink","changed","changed");
                    $query->addExpression("DATE_FORMAT(FROM_UNIXTIME(tbl_backlink.changed), '%e/%m/%Y') ","date_created");
//                    $query->addExpression("COUNT(tbl_backlink.id)","nid_count");
                    $query->leftJoin($sub_query_2,"tbl_query_2","tbl_query_2.nid=tbl_backlink.id");
                    $query->fields("tbl_query_2");

                    if(!empty($_data)){
                        $query->condition("tbl_backlink.id",$_data,"IN");
                    }

                    $sub = db_select("tbl_backlink","tbl_backlink");
                    $sub->fields("tbl_backlink");
                    $sub->join("tbl_tags","tbl_tags","tbl_tags.nid=tbl_backlink.id");
                    $sub->join("taxonomy_term_data","taxonomy_term_data","taxonomy_term_data.tid=tbl_tags.tid");
                    $sub->groupBy("tbl_backlink.id");
                    $sub->addExpression("GROUP_CONCAT (taxonomy_term_data.name SEPARATOR ',' ) ","tags");
//                $sub->addExpression("CASE WHEN tbl_backlink.status=200 THEN 1 ELSE 0 END","b_status");
//                $sub->condition("tbl_backlink.pid",$pid);
//
                    $query->leftJoin($sub,"tbl_sub","tbl_sub.id=tbl_backlink.id");
                    $query->addField("tbl_sub","tags","tags");
                    $query->orderBy("tbl_backlink.changed","DESC");
//                $query->addField("tbl_sub","b_status","b_status");
//                $query->addField("tbl_backlink_detail","changed","b_changed");

                    $result = $query->execute()->fetchAll();
                    if(empty($result)){
                        $query = db_select("tbl_backlink","tbl_backlink");
                        $query->fields("tbl_backlink");
                        $query->addField("tbl_backlink","id","bid");
                        $query->condition("tbl_backlink.pid",$pid);
                        $query->addField("tbl_backlink","refer_page","source");
                        $query->orderBy("tbl_backlink.changed","DESC");
                        $query->addField("tbl_backlink","indexed","indexed");
                        $query->addExpression("DATE_FORMAT(FROM_UNIXTIME(tbl_backlink.changed), '%e/%m/%Y') ","date_created");
                        $result = $query->execute()->fetchAll();
                    }
                    if($user->uid==1){
                      _print_r(($result));
                      die;
                    }

                }catch (Exception $e){
                    print_r($e);
                }
                $export = cassiopeia_create_backlinks_export($result);
//                _print_r($export);
//                if(!empty($export)){
//                    drupal_goto("excel/download/".$export['name']);
//                }else{
//
//                }
//                cassiopeia_backlink_project_detail_export_page_callback($result,$pid);

                if(!empty($export)){
                    // Serve the file directly instead of redirecting
                    $filename = $export['name'];
                    $file_path = drupal_realpath("public://".$filename);
                    
                    if ($file_path && file_exists($file_path)) {
                        header('Pragma: public');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                        header('Cache-Control: private', false);
                        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                        header('Content-Disposition: attachment; filename="'. basename($filename) . '"');
                        header('Content-Transfer-Encoding: binary');
                        header('Content-Length: ' . filesize($file_path));
                        
                        readfile($file_path);
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                        exit;
                    } else {
                        drupal_set_message("Không thể tạo file Excel. Vui lòng thử lại.", 'error');
                        drupal_goto("quan-ly-backlink/du-an/".$pid);
                    }
                }else{
                    drupal_set_message("Không thể tạo file Excel. Vui lòng thử lại.", 'error');
                    drupal_goto("quan-ly-backlink/du-an/".$pid);
                }

            }else{
                drupal_goto("price-board");
            }
        }
    }else{
        return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-backlink-project-detail.tpl.php",array("project_id"=>$pid));
    }
}
function cassiopeia_get_url_result_page_callback(){
    $key = isset($_REQUEST['keyword'])?$_REQUEST['keyword']:"";
    $export = isset($_REQUEST['export'])?$_REQUEST['export']:0;
    $arg = arg();
    if($export==1){
        // Thêm debug để kiểm tra dữ liệu session
        if(empty($_SESSION['GetUrlData'])) {
            drupal_set_message("Không có dữ liệu trong session. Vui lòng thực hiện tìm kiếm trước khi xuất Excel.", 'error');
            drupal_goto("get-url");
            return;
        }

        if(!empty($key) && empty($_SESSION['GetUrlData'][$key])) {
            drupal_set_message("Không tìm thấy dữ liệu cho từ khóa: " . $key, 'error');
            drupal_goto("get-url/result");
            return;
        }

//        $export = cassiopeia_create_get_urls_export($key);
////        _print_r($export);
//        if(!empty($export)){
//            drupal_goto("excel/download/".$export['name']);
//        }else{
//            drupal_set_message("Không thể tạo file Excel. Vui lòng thử lại.", 'error');
//            drupal_goto("get-url/result?keyword=" . $key);
//        }

        $export = cassiopeia_create_get_urls_export($key);
        
        if(!empty($export)){
            // Serve the file directly instead of redirecting
            $filename = $export['name'];
            $file_path = drupal_realpath("public://".$filename);
            
            if ($file_path && file_exists($file_path)) {
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: private', false);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="'. basename($filename) . '"');
                header('Content-Transfer-Encoding: binary');
                header('Content-Length: ' . filesize($file_path));
                
                readfile($file_path);
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
                exit;
            } else {
                drupal_set_message("Không thể tạo file Excel. Vui lòng thử lại.", 'error');
                drupal_goto("get-url/result?keyword=" . $key);
            }
        }else{
            drupal_set_message("Không thể tạo file Excel. Vui lòng thử lại.", 'error');
            drupal_goto("get-url/result?keyword=" . $key);
        }

    }else{
        return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-get-url-result.tpl.php");
    }
}
function cassiopeia_user_excel_export_callback(){
    $cache = !empty($_REQUEST['data'])?$_REQUEST['data']:array();

    $backlink = db_select("node","tbl_node");
    $backlink->fields("tbl_node");
    $backlink->condition("tbl_node.type","project_backlink");
    $backlink->addExpression("COUNT(tbl_node.nid)","TotalBacklinkProject");
    $backlink->groupBy("tbl_node.uid");

    $keyword = db_select("node","tbl_node");
    $keyword->fields("tbl_node");
    $keyword->condition("tbl_node.type","project_keyword");
    $keyword->addExpression("COUNT(tbl_node.nid)","TotalkeywordProject");
    $keyword->groupBy("tbl_node.uid");

    $product = db_select("tbl_available_product","tbl_available_product");
    $product->fields("tbl_available_product");
    $product->condition("tbl_node.type","project_keyword");
    $product->addExpression("COUNT(tbl_node.nid)","TotalkeywordProject");
    $product->groupBy("tbl_node.uid");

    $user_query = db_select("users","tbl_query");
    $user_query->fields("tbl_query");
    $user_query->join("users_roles","tbl_role","tbl_role.uid=tbl_query.uid");
    $user_query->groupBy("tbl_query.uid");
    $query = db_select($user_query,"tbl_user");
    $query->fields("tbl_user");
    $query->leftJoin("field_data_field_full_name","field_full_name","field_full_name.entity_id=tbl_user.uid");
    $query->addField("field_full_name","field_full_name_value","full_name");

    $query->leftJoin("field_data_field_tel","field_tel","field_tel.entity_id=tbl_user.uid");
    $query->addField("field_tel","field_tel_value","field_tel");

    $query->leftJoin($backlink,"tbl_backlink","tbl_backlink.uid=tbl_user.uid");
    $query->addField("tbl_backlink","TotalBacklinkProject","TotalBacklinkProject");

    $query->leftJoin($keyword,"tbl_keyword","tbl_keyword.uid=tbl_user.uid");
    $query->addField("tbl_keyword","TotalkeywordProject","TotalkeywordProject");

//$query->leftJoin("tbl_available_product","tbl_available_product","tbl_available_product.uid=tbl_user.uid");
//$query->addField("tbl_available_product","expired");

    if(!empty($roles)){
        $query->condition("tbl_role.rid",array(4),"IN");
    }
    if(!empty($excludes)){
        $query->condition("tbl_role.rid",array(3),"NOT IN");
    }
    if(!empty($cache['full_name'])){
        $query->condition("field_full_name.field_full_name_value","%".$cache['full_name']."%","LIKE");
    }
    if(!empty($cache['mail'])){
        $query->condition("tbl_user.mail","%".$cache['mail']."%","LIKE");
    }
    if(!empty($cache['name'])){
        $query->condition("tbl_user.name","%".$cache['name']."%","LIKE");
    }
    $query->orderBy("tbl_user.created","DESC");
    $accounts = $query->execute()->fetchAll();
    $export = cassiopeia_create_user_export($accounts);
    if(!empty($export)){
        drupal_goto("excel/download/".$export['name']);
    }else{

    }
}
function cassiopeia_keyword_project_detail_page_callback($pid) {
    global $user;
    $arg = arg();
    $cache = !empty($_REQUEST)?$_REQUEST:array();
    try{
        $_data = isset($_REQUEST['data'])?$_SESSION['export_data'][$_REQUEST['data']]:array();
//        _print_r($_data);
//        die;
        $query = db_select("node","tbl_node");
        $query->fields("tbl_node");
        $query->condition("type","keyword");
        $query->orderBy("tbl_node.changed","DESC");
        $query->join("field_data_field_keyword_project","field_keyword_project","field_keyword_project.entity_id=tbl_node.nid");
        $query->condition("field_keyword_project.field_keyword_project_nid",$pid);

        $query->leftJoin("field_data_field_keyword_old_position","field_keyword_old_position","field_keyword_old_position.entity_id=tbl_node.nid");
        $query->addField("field_keyword_old_position","field_keyword_old_position_value","field_keyword_old_position");

        $query->leftJoin("field_data_field_keyword_position","field_keyword_position","field_keyword_position.entity_id=tbl_node.nid");
        $query->addField("field_keyword_position","field_keyword_position_value","field_keyword_position");

        $query->leftJoin("field_data_field_keyword_best_position","field_keyword_best_position","field_keyword_best_position.entity_id=tbl_node.nid");
        $query->addField("field_keyword_best_position","field_keyword_best_position_value","field_keyword_best_position");

        $query->leftJoin("field_data_field_updated","field_updated","field_updated.entity_id=tbl_node.nid");
        $query->addField("field_updated","field_updated_value","field_updated");

        $query->leftJoin("field_data_field_url","field_url","field_url.entity_id=tbl_node.nid");
        $query->addField("field_url","field_url_value","field_url");

        if(!empty($_data)){
            $query->condition("tbl_node.nid",$_data,"IN");
        }
        if(!empty($cache['tag']) && $cache['tag']!="all"){
            $query->join("field_data_field_tags","field_tags","field_tags.entity_id=tbl_node.nid");
            $query->condition("field_tags.field_tags_tid",$cache['tag']);
        }
        if(!empty($cache['top']) && $cache['top']!="all"){
            $query->condition("field_keyword_position.field_keyword_position_value",$cache['top'],"<=");
            $query->condition("field_keyword_position.field_keyword_position_value",1,">=");
        }
        if(!empty($cache['rank']) && $cache['rank']!="all"){
            if($cache['rank']=="new"){
                $query->leftJoin("field_data_field_checked","field_checked","field_checked.entity_id=tbl_node.nid");
                $or = db_or();
                $or->where("field_checked.field_checked_value is null");
                $or->where("field_checked.field_checked_value = ''");
                $or->where("field_checked.field_checked_value = 0");
                $query->condition($or);
//                $query->condition("tbl_node.changed",strtotime(date("d-m-Y 00:00:00",REQUEST_TIME)),"<");
            }elseif($cache['rank']=="new_today"){
                $query->leftJoin("field_data_field_checked_date","field_data_field_checked_date","field_data_field_checked_date.entity_id=tbl_node.nid");
                $or = db_or();
                $or->condition("field_data_field_checked_date.field_checked_date_value",strtotime(date("d-m-Y 00:00:00",REQUEST_TIME)),"<");
                $or->where("field_data_field_checked_date.field_checked_date_value is null");
                $query->condition($or);
            }elseif($cache['rank']==">10"){
                $or = db_or();
                $or->condition("field_keyword_position.field_keyword_position_value",10,">=");
                $or->where("field_keyword_position.field_keyword_position_value IS NULL");
                $or->condition("field_keyword_position.field_keyword_position_value","");
                $query->condition($or);
            }elseif($cache['rank']=='other'){
                $query->condition("field_keyword_position.field_keyword_position_value",$cache['rank-from'],">=");
                $query->condition("field_keyword_position.field_keyword_position_value",$cache['rank-to'],"<=");
            }else{
                $query->condition("field_keyword_position.field_keyword_position_value",$cache['rank'],"=");
            }
        }
        if(!empty($cache['title'])){
            $query->condition("tbl_node.title","%".$cache['title']."%","LIKE");
        }
//                            $query->leftJoin("field_data_field_tags","field_tags","field_tags.entity_id=tbl_node.nid");

        $sub = db_select("field_data_field_tags","field_tags");
        $sub->fields("field_tags");
        $sub->addExpression("GROUP_CONCAT(field_tags.field_tags_tid SEPARATOR ',')","field_tags");
        $sub->groupBy("field_tags.entity_id");

        $query->leftJoin($sub,"tbl_tags","tbl_tags.entity_id=tbl_node.nid");
        $query->fields("tbl_tags");


//
//                            $query->range(0,50);
        $_result = $query->execute()->fetchall();
        $keywords = array();
        if(!empty($_result)){
            foreach($_result as $value){
                if(!empty($_SESSION['CheckKeywordData'])){
                    if(array_key_exists($value->nid,$_SESSION['CheckKeywordData'])){
                        $value->checked = 1;
                    }
                }
                $keywords[] = $value;
            }
        }
    }catch (Exception $E){
        _print_r($E);
    }
//    _print_r($keywords);
    if(!empty($arg[3]) && $arg[3]=="export"){
        $packet = cassiopeia_get_available_packet_by_uid($user->uid);
        if($packet->product!=BASIC && $packet->expired>0 && $packet->expired<REQUEST_TIME) { // tài khoản hết hạn}
            cassiopeia_set_alert("UserExpired","Số lượng dự án backlink");
            drupal_goto("quan-ly-keywords/du-an/".$arg[2]);
        }else{
            if(!empty($packet->excel)){
                $export = cassiopeia_create_keywords_export($keywords,$pid);
//                _print_r($export);
                if(!empty($export)){
                    // Serve the file directly instead of redirecting
                    $filename = $export['name'];
                    $file_path = drupal_realpath("public://".$filename);
                    
                    if ($file_path && file_exists($file_path)) {
                        header('Pragma: public');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                        header('Cache-Control: private', false);
                        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                        header('Content-Disposition: attachment; filename="'. basename($filename) . '"');
                        header('Content-Transfer-Encoding: binary');
                        header('Content-Length: ' . filesize($file_path));
                        
                        readfile($file_path);
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                        exit;
                    } else {
                        drupal_set_message("Không thể tạo file Excel. Vui lòng thử lại.", 'error');
                        drupal_goto("quan-ly-keywords/du-an/".$arg[2]);
                    }
                }else{
                    drupal_set_message("Không thể tạo file Excel. Vui lòng thử lại.", 'error');
                    drupal_goto("quan-ly-keywords/du-an/".$arg[2]);
                }
//                cassiopeia_keyword_export($keywords,$pid);
            }else{
                drupal_goto("price-board");
            }
        }
    }else{
        return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-keyword-project-detail.tpl.php",array("project_id"=>$pid,"keywords"=>$keywords));
    }
}
function cassiopeia_backlink_project_detail_report_page_callback($id) {
    return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-backlink-project-detail-report.tpl.php",array("project_id"=>$id));
}
function cassiopeia_keyword_project_detail_report_page_callback($id) {
    return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-keyword-project-detail-report.tpl.php",array("project_id"=>$id));
}
function cassiopeia_setting_page_callback() {
    return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-setting.tpl.php");
}
function cassiopeia_keyword_projects_page_callback() {
    global $user;
    $arg = arg();
    if(!empty($arg[1]) && $arg[1]=="export"){
        $packet = cassiopeia_get_available_packet_by_uid($user->uid);
        if($packet->product!=BASIC && $packet->expired>0 && $packet->expired<REQUEST_TIME) { // tài khoản hết hạn}
            cassiopeia_set_alert("UserExpired","Số lượng dự án backlink");
            drupal_goto("quan-ly-keywords");
        }else{
            if(!empty($packet->excel)){
                $export = cassiopeia_create_keyword_projects();
                if(!empty($export)){
                    drupal_goto("excel/download/".$export['name']);
                }else{

                }
//                cassiopeia_export_keyword_projects();
            }else{
                drupal_goto("price-board");
            }
        }

    }else{
        return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-keyword-projects.tpl.php");
    }

}
function cassiopeia_check_duplicate_content_page_callback() {
    return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-check-duplicate-content.tpl.php");
}
function cassiopeia_duplicate_export_excel_page_callback(){
    global $user;
    $packet = cassiopeia_get_available_packet_by_uid($user->uid);
    if($packet->product!=BASIC && $packet->expired>0 && $packet->expired<REQUEST_TIME) { // tài khoản hết hạn}
        cassiopeia_set_alert("UserExpired","Số lượng dự án backlink");
        drupal_goto("kiem-tra-dao-van");
    }else{
        if(!empty($packet->excel)){
            $export = cassiopeia_create_duplicate_export();
            if(!empty($export)){
                drupal_goto("excel/download/".$export['name']);
            }else{

            }
//                cassiopeia_export_backlink_projects();
        }else{
            drupal_goto("price-board");
        }
    }
}
function cassiopeia_backlink_projects__page_callback() {
//    print_r(123);
    global $user;
    $arg = arg();
    if(!empty($arg[1]) && $arg[1]=="export"){
        $packet = cassiopeia_get_available_packet_by_uid($user->uid);
        if($packet->product!=BASIC && $packet->expired>0 && $packet->expired<REQUEST_TIME) { // tài khoản hết hạn}
            cassiopeia_set_alert("UserExpired","Số lượng dự án backlink");
            drupal_goto("quan-ly-backlink");
        }else{
            if(!empty($packet->excel)){
                $export = cassiopeia_create_backlink_projects();
                if(!empty($export)){
                    drupal_goto("excel/download/".$export['name']);
                }else{

                }
//                cassiopeia_export_backlink_projects();
            }else{
                drupal_goto("price-board");
            }
        }
    }else{
//        if($user->uid==1){
//            return _cassiopeia_render_theme("module","cassiopeia","templates/test.tpl.php");
//        }else{
            return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-backlink-projects.tpl.php");
//        }
    }
}
function cassiopeia_admin_manager_booking_page_callback() {
    return _cassiopeia_render_theme("module","cassiopeia","templates/admin/page-admin-manager-booking.tpl.php");
}
function cassiopeia_key_check_page_callback() {
    return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-key-check.tpl.php");
}
function cassiopeia_product_page_callback() {
    return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-products.tpl.php");
}
function cassiopeia_shopping_cart_page_callback() {
    return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-shopping-cart.tpl.php");
}
function cassiopeia_price_board_page_callback() {
    return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-price-board.tpl.php");
}
function cassiopeia_price_confirm_page_callback() {
    return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-price-confirm.tpl.php");
}
function cassiopeia_home_page_callback() {
    global $user;
//    die;
//    if(!empty($user->uid)){
        return '';
//    }else{
//        drupal_goto("index.html");
//    }
}
function cassiopeia_admin_manager_order_page_callback() {
    return _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-admin-manager-order.tpl.php");
}
function cassiopeia_dev_page_callback() {
    return _cassiopeia_render_theme("module","cassiopeia","templates/pages/dev.tpl.php");
}
function cassiopeia_dashboard_page_callback() {
  return _cassiopeia_render_theme("module","cassiopeia","templates/dashboard.tpl.php");
}
function cassiopeia_test_page_callback() {
//    $result = cassiopeia_get_nodes_by_category("backlink",null,10);
//cassiopeia_dump($result);
//die;
//    cassiopeia_event_registration_export($result);
  return _cassiopeia_render_theme("module","cassiopeia","templates/test.tpl.php");
}


function cassiopeia_dev_config_form() {
    $form = array();
    $form['cassiopeia_dev_config_form_login_method'] = array(
        '#type' => "fieldset",
        '#title' => t('Đăng nhập'),
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,
    );
    $form['cassiopeia_dev_config_form_login_method']['cassiopeia_dev_config_login_method'] = array(
        '#type' => 'checkboxes',
        '#options' => drupal_map_assoc(
            array(
                0 => t('User name'),
                1 => t('Email'),
                2 => t('Both'),
            )
        ),
        '#title' => t('Đăng nhập bằng?'),

    );
    return system_settings_form($form);
}
function cassiopeia_config_form() {

  $form = array();
  // Site inforemation
  $form['cassiopeia_config_site'] = array(
    '#type' => "fieldset",
    '#title' => t('Website information'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $form['cassiopeia_config_site']['site_name'] = array(
    '#type' => "textfield",
    '#title' => t('Website name'),
    "#default_value" => variable_get('site_name', ''),
  );

  $form['cassiopeia_config_site']['site_mail'] = array(
    '#type' => "textfield",
    '#title' => t('Website Email'),
    "#default_value" => variable_get('site_mail', ''),
  );

  $form['cassiopeia_config_site']['hotline'] = array(
    '#type' => "textfield",
    '#title' => t('Hotline'),
    "#default_value" => variable_get('hotline', ''),
  );
    $form['cassiopeia_config_ads'] = array(
        '#type' => "fieldset",
        '#title' => 'Links',
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
    );
    $form['cassiopeia_config_ads']['cassiopeia_config_ads_click_on_site'] = array(
        '#type' => "fieldset",
        '#title' => 'Clickon.vn',
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
    );
    $form['cassiopeia_config_ads']['cassiopeia_config_ads_click_on_site']['cassiopeia_config_ads_click_on_site_title'] = array(
        '#type' => "textfield",
        '#title' => t('Anchor Text'),
        "#default_value" => variable_get('cassiopeia_config_ads_click_on_site_title', ''),
    );
    $form['cassiopeia_config_ads']['cassiopeia_config_ads_click_on_site']['cassiopeia_config_ads_click_on_site_url'] = array(
        '#type' => "textfield",
        '#title' => t('URL'),
        "#default_value" => variable_get('cassiopeia_config_ads_click_on_site_url', ''),
    );
    $form['cassiopeia_config_ads']['cassiopeia_config_ads_click_on_page'] = array(
        '#type' => "fieldset",
        '#title' => 'Clickon FB page',
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
    );
    $form['cassiopeia_config_ads']['cassiopeia_config_ads_click_on_page']['cassiopeia_config_ads_click_on_page_title'] = array(
        '#type' => "textfield",
        '#title' => t('Anchor Text'),
        "#default_value" => variable_get('cassiopeia_config_ads_click_on_page_title', ''),
    );
    $form['cassiopeia_config_ads']['cassiopeia_config_ads_click_on_page']['cassiopeia_config_ads_click_on_page_url'] = array(
        '#type' => "textfield",
        '#title' => t('URL'),
        "#default_value" => variable_get('cassiopeia_config_ads_click_on_page_url', ''),
    );
  $form['cassiopeia_config_social'] = array(
    '#type' => "fieldset",
    '#title' => 'Social',
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

//  todo
  $form['cassiopeia_config_social']['youtube_url'] = array(
    '#type' => "textfield",
    "#title" => t('Youtube ') . ' ' . t('link'),
    "#default_value" => variable_get('youtube_url', ''),
  );

  $form['cassiopeia_config_social']['facebook_url'] = array(
    '#type' => "textfield",
    "#title" => t('Facebook') . ' ' . t('link'),
    "#default_value" => variable_get('facebook_url', ''),
  );

  $form['cassiopeia_config_social']['twitter_url'] = array(
    '#type' => "textfield",
    "#title" => t('Twitter ') . ' ' . t('link'),
    "#default_value" => variable_get('twitter_url', ''),
  );

  $form['cassiopeia_config_social']['google_plus_url'] = array(
    '#type' => "textfield",
    "#title" => t('Google Plus ') . ' ' . t('link'),
    "#default_value" => variable_get('google_plus_url', ''),
  );

  $form['cassiopeia_config_social']['facebook_page'] = array(
    '#type' => "textarea",
    '#title' => 'Facebook page',
    '#resizable' => TRUE,
    "#default_value" => variable_get('facebook_page', ''),
  );
  $form['cassiopeia_config_social']['metu_plugin'] = array(
    '#type' => "textarea",
    '#title' => 'Metu',
    '#resizable' => TRUE,
    "#default_value" => variable_get('metu_plugin', ''),
  );

//  todo
    $form['cassiopeia_config_mail'] = array(
        '#type' => "fieldset",
        '#title' => 'Mail',
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
    );
    $form['cassiopeia_config_mail']['cassiopeia_config_mail_guest_post'] = array(
        '#type' => "fieldset",
        '#title' => 'Guest Post',
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
    );
    $form['cassiopeia_config_mail']['cassiopeia_config_mail_guest_post']['cassiopeia_config_mail_guest_post_receiver_mail'] = array(
        '#type' => "textfield",
        '#title' => t('Email nhận thông tin đăng ký'),
        "#default_value" => variable_get('cassiopeia_config_mail_guest_post_receiver_mail', ''),
    );
    $form['cassiopeia_config_mail']['cassiopeia_config_mail_guest_post']['cassiopeia_config_mail_guest_post_domain_booking_receiver_mail'] = array(
        '#type' => "textfield",
        '#title' => t('Email nhận thông tin mua lượt đổi domain'),
        "#default_value" => variable_get('cassiopeia_config_mail_guest_post_domain_booking_receiver_mail', ''),
    );
  $form['cassiopeia_config_mail']['cassiopeia_config_captcha_resolve'] = array(
    '#type' => "fieldset",
    '#title' => 'Captcha',
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['cassiopeia_config_mail']['cassiopeia_config_captcha_resolve']['cassiopeia_config_captcha_resolve_receiver_mails'] = array(
    '#type' => "textfield",
    '#title' => t('Email nhận thông tin đăng ký'),
    "#default_value" => variable_get('cassiopeia_config_captcha_resolve_receiver_mails', ''),
  );
  $form['cassiopeia_config_mail']['cassiopeia_config_mail_guest_post']['cassiopeia_config_mail_guest_post_receiver_mail'] = array(
    '#type' => "textfield",
    '#title' => t('Email nhận thông tin đăng ký'),
    "#default_value" => variable_get('cassiopeia_config_mail_guest_post_receiver_mail', ''),
  );
    $form['cassiopeia_config_mail']['cassiopeia_config_mail_forgot_password'] = array(
        '#type' => "fieldset",
        '#title' => 'Quên mật khẩu tài khoản - gửi cho khách hàng',
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
    );
    $user_mail_password_reset_body = variable_get('user_mail_password_reset_body', array(
        'value' => '',
        'format' => 'full_html'
    ));
//    $form['cassiopeia_config_mail']['cassiopeia_config_mail_forgot_password']['user_mail_password_reset_body'] = array(
//        '#type' => "text_format",
//        '#title' => t('Nội dung'),
//        '#format' => 'full_html',
//        "#default_value" => $user_mail_password_reset_body['value'],
//    );
    $form['cassiopeia_config_mail']['cassiopeia_config_mail_forgot_password']['user_mail_password_reset_body'] = array(
        '#type' => "textarea",
        '#title' => t('Nội dung'),
        '#resizable' => TRUE,
        "#default_value" => variable_get('user_mail_password_reset_body', ''),
    );
    $form['cassiopeia_config_mail']['cassiopeia_config_mail_active'] = array(
        '#type' => "fieldset",
        '#title' => 'Kích hoạt tài khoản - gửi cho khách hàng',
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
    );
    $cassiopeia_config_mail_active_content = variable_get('cassiopeia_config_mail_active_content', array(
        'value' => '',
        'format' => 'full_html'
    ));

    $form['cassiopeia_config_mail']['cassiopeia_config_mail_active']['cassiopeia_config_mail_active_content'] = array(
        '#type' => "text_format",
        '#title' => t('Nội dung'),
        '#format' => 'full_html',
        "#default_value" => $cassiopeia_config_mail_active_content['value'],
    );
    $form['cassiopeia_config_mail']['cassiopeia_config_mail_register'] = array(
        '#type' => "fieldset",
        '#title' => 'Đăng ký tài khoản - gửi cho admin',
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
    );
    $form['cassiopeia_config_mail']['cassiopeia_config_mail_register']['cassiopeia_config_mail_register_mail'] = array(
        '#type' => "textfield",
        "#title" => "Email nhận thông tin",
        "#default_value" => variable_get('cassiopeia_config_mail_register_mail', ''),
    );
    $cassiopeia_config_mail_register_content = variable_get('cassiopeia_config_mail_register_content', array(
        'value' => '',
        'format' => 'full_html'
    ));
    $form['cassiopeia_config_mail']['cassiopeia_config_mail_register']['cassiopeia_config_mail_register_content'] = array(
        '#type' => "text_format",
        '#title' => t('Nội dung'),
        '#format' => 'full_html',
        "#default_value" => $cassiopeia_config_mail_register_content['value'],
    );
    $form['cassiopeia_config_mail']['cassiopeia_config_mail_buy_product'] = array(
        '#type' => "fieldset",
        '#title' => 'Khách hàng mua gói dịch vụ - gửi cho admin',
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
    );
    $form['cassiopeia_config_mail']['cassiopeia_config_mail_buy_product']['cassiopeia_config_mail_buy_product_mail'] = array(
        '#type' => "textfield",
        "#title" => "Email nhận thông tin",
        "#default_value" => variable_get('cassiopeia_config_mail_buy_product_mail', ''),
    );
    $cassiopeia_config_mail_buy_product_content = variable_get('cassiopeia_config_mail_buy_product_content', array(
        'value' => '',
        'format' => 'full_html'
    ));
    $form['cassiopeia_config_mail']['cassiopeia_config_mail_buy_product']['cassiopeia_config_mail_buy_product_content'] = array(
        '#type' => "text_format",
        '#title' => t('Nội dung'),
        '#format' => 'full_html',
        "#default_value" => $cassiopeia_config_mail_buy_product_content['value'],
        "#desction" => "",
    );

    $form['cassiopeia_config_footer'] = array(
    '#type' => "fieldset",
    '#title' => 'Footer',
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $_footer = variable_get('footer', array(
    'value' => '',
    'format' => 'full_html'
  ));
  $form['cassiopeia_config_footer']['footer'] = array(
    '#type' => "text_format",
    '#title' => 'Footer',
    '#resizable' => TRUE,
    '#format' => 'full_html',
    "#default_value" => $_footer['value'],
  );


  $form['cassiopeia_config_copyright'] = array(
    '#type' => "fieldset",
    '#title' => t('Copyright'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $_copyright = variable_get('copyright', array(
    'value' => '',
    'format' => 'full_html'
  ));
  $form['cassiopeia_config_copyright']['copyright'] = array(
    '#type' => "text_format",
    '#title' => t('Copyright'),
    '#format' => 'full_html',
    "#default_value" => $_copyright['value'],
  );


  $form['cassiopeia_config_script'] = array(
    '#type' => "fieldset",
    '#title' => 'Scripts',
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $form['cassiopeia_config_script']['header_script'] = array(
    '#type' => "textarea",
    '#title' => t('Page header'),
    '#resizable' => TRUE,
    "#default_value" => variable_get('header_script', ''),
  );
  $form['cassiopeia_config_script']['header_body'] = array(
    '#type' => "textarea",
    '#title' => t('Page Body'),
    '#resizable' => TRUE,
    "#default_value" => variable_get('header_body', ''),
  );
  $form['cassiopeia_config_script']['footer_script'] = array(
    '#type' => "textarea",
    '#title' => t('Page footer'),
    '#resizable' => TRUE,
    "#default_value" => variable_get('footer_script', ''),
  );

  return system_settings_form($form);

}



function cassiopeia_change_tags($option,$_data,$tags,$type,$_nid){
    global $user;
    if($option=="ADD"){
        foreach($tags as $tag){
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
                "value"     => $_nid,
                "condition" => "=",
            );
            $conditions['name'] = array(
                "type"      => "propertyCondition",
                "value"     => $tag->value,
                "condition" => "=",
            );
            $tags = cassiopeia_get_items_by_conditions($conditions,"tags","taxonomy_term");
            $term = !empty($tags)?array_values($tags)[0]:null;

            if(!empty($term)){
                $query = db_select("node","tbl_node");
                $query->fields("tbl_node");
                $query->condition("type",$type);
                $query->leftJoin("field_data_field_tags","field_tags","field_tags.entity_id=tbl_node.nid");
                $query->condition("tbl_node.nid",(array)$_data,"IN");
                $query->condition("field_tags.field_tags_tid",$term->tid);
                $query->fields("field_tags");
                $result = $query->execute()->fetchAll();
                $notInIDs = array();
                if(!empty($result)){
                    foreach($result as $item){
                        $notInIDs[] = $item->nid;
                    }
                    $query2 = db_select("node","tbl_node");
                    $query2->fields("tbl_node");
                    $query2->condition("type",$type);
                    $query2->condition("tbl_node.nid",(array)$_data,"IN");
                    $query2->condition("tbl_node.nid",$notInIDs,"NOT IN");
                    $result2 = $query2->execute()->fetchAll();
                    if(!empty($result2)){
                        foreach($result2 as $_node){
                            $_node->field_tags['und'][]['tid'] = $term->tid;
                            node_save($_node);
                        }
                    }
                }else{
                    if(!empty($_data)){
                        foreach($_data as $nid){
                            $node = node_load($nid);
                            $node->field_tags['und'][]['tid'] = $term->tid;
                            node_save($node);
                        }
                    }
                }
            }else{
                $v = taxonomy_vocabulary_machine_name_load("tags");
                $newterm = new stdClass();
                $newterm->name = $tag->value;
                $newterm->vid = $v->vid;
                $newterm->field_user['und'][0]['target_id'] = $user->uid;
                if($type=="backlink"){
                    $newterm->field_backlink_project['und'][0]['nid'] = $_nid;
                    foreach($_data as $nid){
                        db_insert("tbl_tags")->fields(array(
                            "nid"   => $nid,
                            "tid"   => $newterm->tid,
                        ))->execute();
                    }
                }else{
                    $newterm->field_keyword_project['und'][0]['nid'] = $_nid;
                    taxonomy_term_save($newterm);
                    foreach($_data as $nid){
                        $node = node_load($nid);
                        $node->field_tags['und'][]['tid'] = $newterm->tid;
                        node_save($node);
                    }
                }

            }
        }
    }else{
        $temp = array();
        if(!empty($tags)){
            foreach($tags as $tag){
                $temp[] = $tag->value;
            }
        }
        foreach($_data as $nid){
            $query = db_select("node","tbl_node");
            $query->fields("tbl_node");
            $query->condition("tbl_node.nid",$nid);
            $query->join("field_data_field_tags","tbl_tag","tbl_tag.entity_id=tbl_node.nid");
            $query->join("taxonomy_term_data","taxonomy_term_data","taxonomy_term_data.tid=tbl_tag.field_tags_tid");
            $query->condition("taxonomy_term_data.name",$temp,"not in");
            $query->addField("taxonomy_term_data","tid","tid");
            $result = $query->execute()->fetchAll();
            $node = node_load($nid);
            $node->field_tags['und'] = array();
            if(!empty($result)){
                foreach($result as $item){
                    $node->field_tags['und'][]['tid'] = $item->tid;
                }
            }
            node_save($node);
        }
    }
}
function cassiopeia_test_ajax_page() {
    $content = trim(file_get_contents("php://input"));
    $node = json_decode($content);

    $wp_str = '{"ID":22,"post_author":"1","post_date":"2023-03-30 04:50:43","post_date_gmt":"2023-03-30 04:50:43","post_content":"<p>123<\/p>","post_title":"123","post_excerpt":"","post_status":"publish","comment_status":"open","ping_status":"open","post_password":"","post_name":"123-4","to_ping":"","pinged":"","post_modified":"2023-03-30 04:50:43","post_modified_gmt":"2023-03-30 04:50:43","post_content_filtered":"","post_parent":0,"guid":"http:\/\/wordpress.test:8888\/2023\/03\/30\/123-4\/","menu_order":0,"post_type":"post","post_mime_type":"","comment_count":"0","filter":"raw"}';
    $wp_post = json_decode($wp_str);
    $wp_post->status = "OK";

    return $wp_post;
}
function cassiopeia_ajax_page() {
    global $user;
  $data = array();
  $data['message'] = '';
  $data['success'] = 0;
//  print_r($_REQUEST);
  if (isset($_POST['cmd'])) {
    switch ($_POST['cmd']) {
        case "delete-content-articles":
            $_data = $_REQUEST['data'];
            if(!empty($_data)){
                foreach($_data as $datum){
                    $node = content_article_load($datum);
                    content_article_delete($node);
                }
                $data['response'] = "OK";
            }
            break;
        case "delete-content-article":
            $id = $_REQUEST['id'];
            $node = content_article_load($id);
            if(user_has_role(3)||$node->uid==$user->uid){
                if(content_article_delete($node)){
                    $data['response'] = "OK";
                };
            }
            break;
        case "load-check-content":

            $_data = $_REQUEST['data'];
            $data['html'] = _cassiopeia_render_theme("module","cassiopeia","templates/parts/check-content.tpl.php",array("article_info"=>$_data));
            break;
        case "user-check-content-response":
            $today = date("d-m-Y",REQUEST_TIME);
            $tracking = cassiopeia_get_user_tracking_by_date($today);
            $packet = cassiopeia_get_available_packet_by_uid($user->uid);
            if(empty($tracking)){
                try{
                    db_insert("tbl_user_tracking")->fields(array(
                        "uid"  => $user->uid,
                        "date"  => $today,
                        "available_product" => $packet->id,
                        "check_backlink" => 0,
                        "indexed_google" => 0,
                        "content_duplicate" => 0,
                        "key_search" => 0,
                        "content_check" => 0,
                    ))->execute();
                    $tracking = cassiopeia_get_user_tracking_by_date($today);
                }catch (Exception $e){
                    print_r($e);
                }
            }
            if($packet->product!=BASIC && $packet->expired>0 && $packet->expired<REQUEST_TIME){ // tài khoản hết hạn
                $data['response'] = "EXPIRED";
            }else{
                if($packet->content_check>0 && $tracking->content_check>=$packet->content_check){
                    $data['response'] = "LIMITED";
                }else{
                    $_result = json_decode($_REQUEST['data']);
                    if(isset($_result->index)){

                        $record = new stdClass();
                        $record->uid = $user->uid;
                        $record->google_index = $_result->index+1;
                        $record->data = serialize($_result);
                        try{
                            drupal_write_record("cassiopeia_content_check",$record);
                        }catch (Exception $e){
                            _print_r($e);
                        }
                    }
                }
            }
            break;
        case "user-check-content-begin":
            db_delete("cassiopeia_content_check")->condition("uid",$user->uid)->condition("nid",0)->execute();
            break;
        case "get-url-response":
            $_data = $_REQUEST['data'];
            $key = $_REQUEST['key'];
            $_SESSION['GetUrlData'][$key] = $_data;
            // Lưu danh sách loại trừ nếu có
            if (isset($_REQUEST['exclude_urls'])) {
                $_SESSION['GetUrlExclude'][$key] = $_REQUEST['exclude_urls'];
            }
            break;
        case "reset-get-url-session":
            $_SESSION['GetUrlData'] = null;
            $_SESSION['GetUrlExclude'] = null;
            $data['response'] = "OK";
            $data['message'] = "Session đã được reset";
            break;
        case "save-exclude-urls":
            $exclude_urls = $_REQUEST['exclude_urls'];
            $key = isset($_REQUEST['key']) ? $_REQUEST['key'] : '';
            if ($key !== '') {
                $_SESSION['GetUrlExclude'][$key] = $exclude_urls;
            }
            $data['response'] = "OK";
            $data['message'] = "Đã lưu danh sách loại trừ";
            break;
        case "setExportData":
            $_SESSION['export_data'] = null;
            $_data = json_decode($_REQUEST['data']);
            $request_time = REQUEST_TIME;
            $_SESSION['export_data'][$request_time] = $_data;
//            _print_r($_SESSION);
            $data['request_time'] = $request_time;
            break;
        case "getKeywordChartDashboard":
            $pid = $_REQUEST['pid'];
            // Disabled chart functionality
            $data['html'] = "<div class='no-chart-message'>Biểu đồ đã bị vô hiệu hóa</div>";
            break;
        case "getBacklinkChartDashboard":
            $pid = $_REQUEST['pid'];
            // Disabled chart functionality
            $data['html'] = "<div class='no-chart-message'>Biểu đồ đã bị vô hiệu hóa</div>";
            break;
        case "isGift":
            $id = $_REQUEST['id'];
            $val = $_REQUEST['val'];
            if(user_has_role(3)){
                try{
                    db_update("tbl_booking_product")->fields(array(
                        "gift"  => $val==1?0:1,
                    ))->condition("id",$id)->execute();
                    $data['response'] = "OK";
                    $data['message'] = "Cập nhật thành công!";
                }   catch (Exception $e){
                    $data['response'] = "FAIL";
                    $data['message'] = "Cập nhật thất bại!";
                }
            }
            break;
        case "read-note":
            $_SESSION['mobile_note'] = null;
            break;
        case "user-check-duplicate":
            $today = date("d-m-Y",REQUEST_TIME);
            $tracking = cassiopeia_get_user_tracking_by_date($today);
            $packet = cassiopeia_get_available_packet_by_uid($user->uid);
            if(empty($tracking)){
                try{
                    db_insert("tbl_user_tracking")->fields(array(
                        "uid"  => $user->uid,
                        "date"  => $today,
                        "available_product" => $packet->id,
                        "check_backlink" => 0,
                        "indexed_google" => 0,
                        "content_duplicate" => 0,
                        "key_search" => 0,
                    ))->execute();
                    $tracking = cassiopeia_get_user_tracking_by_date($today);
                }catch (Exception $e){
                    print_r($e);
                }
            }
            if($packet->product!=BASIC && $packet->expired>0 && $packet->expired<REQUEST_TIME){ // tài khoản hết hạn
                $data['response'] = "EXPIRED";
            }else{
                if($packet->content_duplicate>0 && $tracking->content_duplicate>=$packet->content_duplicate){
                    $data['response'] = "LIMITED";
                }else{
                    // save check duplicate history
                    try{
                        db_update("tbl_user_tracking")->fields(array(
                            "content_duplicate"    => $tracking->key_search+1,
                        ))->condition("uid",$user->uid)->condition("date",$tracking->date)->execute();
                    }catch (Exception $e){

                    }
                }
            }
            break;
        case "changeTagName":
            $val = $_REQUEST['val'];
            $tid = $_REQUEST['tid'];
            $tag = taxonomy_term_load($tid);
            $tag->name=$val;
            taxonomy_term_save($tag);
            break;
        case "delete-tag":
            $tid = $_REQUEST['tid'];
            $tag = taxonomy_term_load($tid);
            if($user->uid == $tag->field_user['und'][0]['target_id']){
                try{
                    db_delete("tbl_tags")->condition("tid",$tag->tid)->execute();
                    db_delete("field_data_field_tags")->condition("field_tags_tid",$tag->tid)->execute();
                    taxonomy_term_delete($tag->tid);
                    $data['response'] = "OK";
                }catch (Exception $e){
                    _print_r($e);
                }
            }
            break;
        case "create-keyword-complete":
            if(!empty($_SESSION['keyword_valid'])){
                drupal_set_message("Đã thêm ".$_SESSION['keyword_valid']." từ khóa");
            }
            if(!empty($_SESSION['keyword_invalid'])){
                drupal_set_message("Hệ thống đã xóa tự động ".$_SESSION['keyword_invalid']." từ khóa không hợp lệ!","error");
            }
            if(!empty($_SESSION['keyword_duplicate'])){
                drupal_set_message("Hệ thống đã xóa tự động ".$_SESSION['keyword_duplicate']." từ khóa trùng lặp!","error");
            }
            $_SESSION['keyword_valid'] = 0;
            $_SESSION['keyword_invalid'] = 0;
            $_SESSION['keyword_duplicate'] = 0;
            break;
        case "create-keyword":
            $valid = !empty($_SESSION['keyword_valid'])?$_SESSION['keyword_valid']:0;
            $invalid = !empty($_SESSION['keyword_invalid'])?$_SESSION['keyword_invalid']:0;
            $duplicate = !empty($_SESSION['keyword_duplicate'])?$_SESSION['keyword_duplicate']:0;
            $splitter = json_decode($_REQUEST['part']);
            $tids = json_decode($_REQUEST['tids']);
            $pid = $_REQUEST['pid'];
            $project= node_load($pid);
            if($project->uid==$user->uid){
                try{
                    $db_transaction = db_transaction();
                    foreach($splitter as $item) {
                        $item = trim($item);
                        if(!empty($item)){
                            $query = db_select("node", "tbl_node");
                            $query->fields("tbl_node");
                            $query->condition("tbl_node.type", "keyword");
                            $query->where('tbl_node.title = :title', array(':title' => $item));
                            $query->join("field_data_field_keyword_project","field_keyword_project","field_keyword_project.entity_id=tbl_node.nid");
                            $query->condition("field_keyword_project.field_keyword_project_nid",$project->nid);
                            $check = $query->execute()->fetchAll();
                            if(empty($check)){
                                $_node = new stdClass();
                                $_node->type = "keyword";
                                if (!empty($tids)) {
                                    foreach ($tids as $tid) {
                                        $_node->field_tags['und'][]['tid'] = $tid;
                                    }
                                }
                                $_node->uid = $user->uid;
                                $_node->title = strtolower(trim($item));
                                $_node->field_keyword_project['und'][0]['nid'] = $project->nid;
                                node_save($_node);
                                node_submit($_node);
                                $valid++;
                                $data['response'] = "OK";
                            }else{
                                $title = strtolower(trim($item));
                                $flag = true;
                                foreach($check as $__item){
                                    if(strtolower($title)===strtolower($__item->title)){
                                        $flag=false;
                                    }
                                }
                                if($flag){
                                    $_node = new stdClass();
                                    $_node->type = "keyword";
                                    if (!empty($tids)) {
                                        foreach ($tids as $tid) {
                                            $_node->field_tags['und'][]['tid'] = $tid;
                                        }
                                    }
                                    $_node->uid = $user->uid;
                                    $_node->title = strtolower(trim($item));
                                    $_node->field_keyword_project['und'][0]['nid'] = $project->nid;
                                    node_save($_node);
                                    node_submit($_node);
                                    $valid++;
                                    $data['response'] = "OK";
                                }else{
                                    $checkDuplicate = true;
                                    $duplicate++;
                                }
                            }
                        }
                    }
                    $_SESSION['keyword_valid'] = $valid;
                    $_SESSION['keyword_invalid'] = $invalid;
                    $_SESSION['keyword_duplicate'] = $duplicate;
                    $data['response'] = "OK";
                }catch (Exception $e){
                    $db_transaction->rollback();
                }
            }
            break;
        case "create-keywords-init":
            $tags = $_REQUEST['tags'];
            $pid = $_REQUEST['pid'];
            try{
                foreach(json_decode($tags) as $_tag){
                    $term = null;
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
                        "value"     => $pid,
                        "condition" => "=",
                    );
                    $conditions['name'] = array(
                        "type"      => "propertyCondition",
                        "value"     => $_tag->value,
                        "condition" => "=",
                    );
                    $tags = cassiopeia_get_items_by_conditions($conditions,"tags","taxonomy_term");
                    if(!empty($tags)){
                        $term = array_values($tags)[0];
                    }
                    if(!empty($term)){
                        $tids[] = $term->tid;
                    }else{
                        $v = taxonomy_vocabulary_machine_name_load("tags");
                        $newterm = new stdClass();
                        $newterm->name = $_tag->value;
                        $newterm->vid = $v->vid;
                        $newterm->field_user['und'][0]['target_id'] = $user->uid;
                        $newterm->field_keyword_project['und'][0]['nid'] = $pid;
                        taxonomy_term_save($newterm);
                        $tids[] = $newterm->tid;
                    }
                }
                $data['tids'] = json_encode($tids);
                $data['response'] = "OK";
            }catch (Exception $e){
                _print_r($e);
                $data['response'] = "FAIL";
            }
            break;
        case "create-backlink-complete":
            if(!empty($_SESSION['backlink_valid'])){
                drupal_set_message("Đã thêm ".$_SESSION['backlink_valid']." backlinks");
            }
            if(!empty($_SESSION['backlink_invalid'])){
                drupal_set_message("Hệ thống đã xóa tự động ".$_SESSION['backlink_invalid']." backlinks không hợp lệ!","error");
            }
            if(!empty($_SESSION['backlink_duplicate'])){
                drupal_set_message("Hệ thống đã xóa tự động ".$_SESSION['backlink_duplicate']." backlinks trùng lặp!","error");
            }
            $_SESSION['backlink_valid'] = 0;
            $_SESSION['backlink_invalid'] = 0;
            $_SESSION['backlink_duplicate'] = 0;
            break;
        case "create-backlink":
            $pid = $_REQUEST['pid'];
            $valid = !empty($_SESSION['backlink_valid'])?$_SESSION['backlink_valid']:0;
            $invalid = !empty($_SESSION['backlink_invalid'])?$_SESSION['backlink_invalid']:0;
            $duplicate = !empty($_SESSION['backlink_duplicate'])?$_SESSION['backlink_duplicate']:0;
            $reg = "/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$/i";
            $splitter = json_decode($_REQUEST['part']);
            $tids = json_decode($_REQUEST['tids']);

            try{
                $db_transaction = db_transaction();
                foreach($splitter as $datum){
                    if(!empty(trim($datum))){
                        $datum = trim($datum);

                        $query = db_select("tbl_backlink","tbl_backlink");
                        $query->fields("tbl_backlink");
                        $query->condition("uid",$user->uid);
                        $query->condition("pid",$pid);
                        $query->where('refer_page = :title', array(':title' => $datum));
                        $check = $query->execute()->fetchAll();
                        if(empty($check)){
                            $url = str_replace("https://","",$datum);
                            $url = str_replace("http://","",$url);
                            $s1 = explode("/",$url);
                            $domain = $s1[0];
                            if(preg_match($reg, $domain)){
                                $domain_query = db_select("tbl_domain","tbl_domain");
                                $domain_query->fields("tbl_domain");
                                $domain_query->condition("domain",$domain);
                                $domain_check = $domain_query->execute()->fetchObject();
                                if(!empty($domain_check)){
                                    $_domain = $domain_check->id;
                                }else{
                                    $_domain = db_insert("tbl_domain")->fields(array(
                                        "created"   => REQUEST_TIME,
                                        "domain"    => $domain,
                                    ))->execute();
                                }
                                $backlinks[] =  array(
                                    "created"       => REQUEST_TIME,
                                    "changed"       => REQUEST_TIME,
                                    "uid"       => $user->uid,
                                    "refer_page"       => ($datum),
                                    "pid"           => $pid,
                                    "domain"       => strtolower($domain),
                                    "_domain"       => strtolower($_domain),
                                    "indexed"       => 2,
                                    "status"       => "RAW",
                                );
                                $valid++;
                            }else{
                                $invalid++;
                            }
                        }else{
                            $duplicate++;
                        }
                    }
                }
                foreach ($backlinks as $__backlink__) {
                    $id = db_insert("tbl_backlink")->fields($__backlink__)->execute();
                    foreach((array)$tids as $tid){
                        db_insert("tbl_tags")->fields(array(
                            "created"   => REQUEST_TIME,
                            "nid"       => $id,
                            "tid"       => $tid,
                            "pid"       => $pid,
                        ))->execute();
                    }
                }
                $_SESSION['backlink_valid'] = $valid;
                $_SESSION['backlink_invalid'] = $invalid;
                $_SESSION['backlink_duplicate'] = $duplicate;
                $data['response'] = "OK";
            }catch (Exception $e){
                $db_transaction->rollback();
            }

            break;
        case "create-backlink-init":
            $tags = $_REQUEST['tags'];
            $pid = $_REQUEST['pid'];
            try{
                foreach(json_decode($tags) as $_tag){
                    $term = null;
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
                        "value"     => $pid,
                        "condition" => "=",
                    );
                    $conditions['name'] = array(
                        "type"      => "propertyCondition",
                        "value"     => $_tag->value,
                        "condition" => "=",
                    );
                    $tags = cassiopeia_get_items_by_conditions($conditions,"tags","taxonomy_term");
                    if(!empty($tags)){
                        $term = array_values($tags)[0];
                    }
                    if(!empty($term)){
//                            $term = array_values($term)[0];
                        $tids[] = $term->tid;
                    }else{
                        $v = taxonomy_vocabulary_machine_name_load("tags");
                        $newterm = new stdClass();
                        $newterm->name = $_tag->value;
                        $newterm->vid = $v->vid;
                        $newterm->field_user['und'][0]['target_id'] = $user->uid;
                        $newterm->field_backlink_project['und'][0]['nid'] = $pid;
                        taxonomy_term_save($newterm);
                        $tids[] = $newterm->tid;
                    }
                }
                $data['tids'] = json_encode($tids);
                $data['response'] = "OK";
            }catch (Exception $e){
                $data['response'] = "FAIL";
            }
            break;
        case "export-duplicate":
            $_SESSION['duplicate'] = json_decode($_REQUEST['data']);
//            _print_r($_SESSION['duplicate']);
            break;
        case "getPriceDetail":
            $priceID = $_REQUEST['priceID'];
            $productID = $_REQUEST['productID'];
            $data['html'] = _cassiopeia_render_theme("module","cassiopeia","templates/parts/price-detail.tpl.php",array("id"=>$priceID,"nid"=>$productID));
            break;
        case "check-complete":
            $listOfID = json_decode($_REQUEST['listOfID']);
            $temp = array();
            if(!empty($listOfID)){
                foreach($listOfID as $ID){
                    $temp[$ID] = $ID;
                }
            }
            $_SESSION['CheckKeywordData'] = $temp;
            drupal_set_message("Đã quét xong!");
            break;
        case "test-backlink":
            drupal_set_message("alo");
            break;
        case "user-check-indexed-response-complete":
            $listOfID = json_decode($_REQUEST['listOfID']);
            $temp = array();
            if(!empty($listOfID)){
                foreach($listOfID as $ID){
                    $temp[$ID] = $ID;
                }
            }
            $_SESSION['CheckBacklinkData'] = $temp;
            break;
        case "user-backlink-check-response-complete":
            $_SESSION['backlink-check-complete'] = $_REQUEST['value'];
            if($_REQUEST['value']==1){
                $listOfID = json_decode($_REQUEST['listOfID']);
                $temp = array();
                if(!empty($listOfID)){
                    foreach($listOfID as $ID){
                        $temp[$ID] = $ID;
                    }
                }
                $_SESSION['CheckBacklinkData'] = $temp;
//                if($user->uid!=31){
                  $today = date("d-m-Y",REQUEST_TIME);
                  $tracking = cassiopeia_get_user_tracking_by_date($today);
                  $packet = cassiopeia_get_available_packet_by_uid($user->uid);
                  if(empty($tracking)){
                    try{
                      db_insert("tbl_user_tracking")->fields(array(
                        "uid"  => $user->uid,
                        "date"  => $today,
                        "available_product" => $packet->id,
                        "anchor_text" => 0,
                        "indexed_google" => 0,
                        "content_duplicate" => 0,
                        "key_search" => 0,
                      ))->execute();
                      $tracking = cassiopeia_get_user_tracking_by_date($today);
                    }catch (Exception $e){
                      print_r($e);
                    }
                  }
                  if($packet->product!=BASIC && $packet->expired>0 && $packet->expired<REQUEST_TIME){ // tài khoản hết hạn
                    $data['response'] = "EXPIRED";
                  }else{
                    if($packet->check_backlink>0 && $tracking->check_backlink>=$packet->check_backlink){
                      $data['response'] = "LIMITED";
                    }else{

                    }
                    if(!empty($_REQUEST['responseObject'])){

                        $responseObject = json_decode($_REQUEST['responseObject']);
                        $query = db_select("tbl_backlink","tbl_backlink");
                        $query->fields("tbl_backlink");
                        $query->condition("id",$responseObject->id  );
                        $backlink = $query->execute()->fetchObject();

                        $query = db_select("tbl_backlink_report","tbl_backlink_report");
                        $query->fields("tbl_backlink_report");
                        $query->condition("nid",$backlink->pid);
                        $query->condition("date",strtotime(date("d-m-Y 00:00:00",REQUEST_TIME)));
                        $check = $query->execute()->fetchAll();

                      $query_detail = db_select("tbl_backlink_detail","tbl_backlink_detail");
                      $query_detail->fields("tbl_backlink_detail");
                      $query->condition("tbl_backlink_detail.pid",$backlink->pid);

                      $query = db_select("tbl_backlink","tbl_backlink");
                      $query->fields("tbl_backlink");
                      $query->join($query_detail,"tbl_backlink_detail","tbl_backlink.id=tbl_backlink_detail.nid");
                      $query->addExpression("COUNT(tbl_backlink_detail.id)","TotalBacklink");
                      $query->condition("tbl_backlink.pid",$backlink->pid);
                      $backlinkResult = $query->execute()->fetchObject();
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

                    }
                    drupal_set_message("Đã quét xong!");
                  }
//                }

            }else{
                $_SESSION['CheckBacklinkData'] = array();
            }
            break;
        case "loadBackLinkChildren":
            $nid = $_REQUEST['nid'];
            $stt = $_REQUEST['stt'];
            $children = (array)cassiopeia_get_backlink_children($nid);
//            _print_r($children);
//            $children = array_slice($children,1);
//            cassiopeia_dump($children);
            $data['html'] = _cassiopeia_render_theme("module","cassiopeia","templates/parts/backlink-children.tpl.php",array("children"=>$children,"stt"=>$stt));
            break;
        case "load-backlink-detail":
            $pid = $_REQUEST['pid'];
            $tag = $_REQUEST['tag'];
            $indexed = $_REQUEST['indexed'];
            $from_date = $_REQUEST['from_date'];
            $to_date = $_REQUEST['to_date'];
            $option = $_REQUEST['option'];
            $url = $_REQUEST['url'];
            $sort = $_REQUEST['sort'];
            $direction = $_REQUEST['direction'];
            $status = $_REQUEST['status'];
            $project = node_load($pid);
            $conditions['pid'] = $project->nid;
            $conditions['option'] = $option;
            $conditions['url'] = $url;
            $conditions['sort'] = $sort;
            $conditions['direction'] = $direction;
            $conditions['from_date'] = $from_date;
            $conditions['to_date'] = $to_date;
            $conditions['tag'] = $tag;
            $conditions['status'] = $status;
            $conditions['indexed'] = $indexed;
            if($project->uid==$user->uid || $user->uid==1){
                $backlinks = cassiopeia_get_backlinks($conditions);
//                print_r($backlinks);
                $data['total'] = !empty($backlinks)?count($backlinks):0;
                $data['response'] = json_encode($backlinks);
            }
            break;
        case "content-article-change-tags":
            $nid = $_REQUEST['id'];
            $_data = json_decode($_REQUEST['data']);
            $tags = json_decode($_REQUEST['tags']);
            $option = $_REQUEST['option'];
            if(!empty($tags)){
                if($option=="ADD"){
                    foreach($tags as $tag){
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
                            "value"     => $nid,
                            "condition" => "=",
                        );
                        $conditions['name'] = array(
                            "type"      => "propertyCondition",
                            "value"     => $tag->value,
                            "condition" => "=",
                        );
                        $tags = cassiopeia_get_items_by_conditions($conditions,"tags","taxonomy_term");
                        $term = !empty($tags)?array_values($tags)[0]:null;
                        if(!empty($term)){
                            $query = db_select("tbl_tags","tbl_tags");
                            $query->fields("tbl_tags");


                            $query->condition("tbl_tags.nid",(array)$_data,"IN");
                            $query->condition("tbl_tags.tid",$term->tid);
                            $query->fields("tbl_tags");
                            $result = $query->execute()->fetchAll();
                            $notInIDs = array();
                            if(!empty($result)){
                                foreach($result as $item){
                                    $notInIDs[] = $item->nid;
                                }
                                $query2 = db_select("cassiopeia_content","cassiopeia_content");
                                $query2->fields("cassiopeia_content");

                                $query2->condition("cassiopeia_content.id",(array)$_data,"IN");
                                $query2->condition("cassiopeia_content.id",$notInIDs,"NOT IN");
                                $result2 = $query2->execute()->fetchAll();
                                if(!empty($result2)){
                                    $query = db_insert("tbl_tags")->fields(array("created","tid","nid"));

                                    foreach($result2 as $_node){
                                        $values = array();
                                        $values['created'] = REQUEST_TIME;
                                        $values['nid'] = $_node->nid;
                                        $values['tid'] = $term->tid;
                                        $query->values($values);
                                    }
                                    $query->execute();
                                }
                            }else{
                                if(!empty($_data)){
                                    $query = db_insert("tbl_tags")->fields(array("created","tid","nid"));
                                    foreach($_data as $nid){
                                        $values = array();
                                        $values['created'] = REQUEST_TIME;
                                        $values['nid'] = $nid;
                                        $values['tid'] = $term->tid;
                                        $query->values($values);
                                    }
                                    $query->execute();
                                }
                            }
                        }else{
                            $v = taxonomy_vocabulary_machine_name_load("tags");
                            $newterm = new stdClass();
                            $newterm->name = $tag->value;
                            $newterm->vid = $v->vid;
                            $newterm->field_user['und'][0]['target_id'] = $user->uid;
//                            if($type=="backlink"){
                                $newterm->field_content_project['und'][0]['nid'] = $nid;
                                taxonomy_term_save($newterm);
                                foreach($_data as $_nid){
                                    db_insert("tbl_tags")->fields(array(
                                        "created"   => REQUEST_TIME,
                                        "nid"   => $_nid,
                                        "pid"   => $nid,
                                        "tid"   => $newterm->tid,
                                    ))->execute();
                                }
//                            }

                        }
                    }
                }else{
                    $temp = array();
                    if(!empty($tags)){
                        foreach($tags as $tag){
                            $temp[] = $tag->value;
                        }
                    }
                    foreach($_data as $nid){
                        $query = db_select("tbl_tags","tbl_tags");
//                        $query->fields("tbl_node");
                        $query->condition("tbl_tags.nid",$nid);
//                        $query->join("field_data_field_tags","tbl_tag","tbl_tag.entity_id=tbl_node.nid");
                        $query->join("taxonomy_term_data","taxonomy_term_data","taxonomy_term_data.tid=tbl_tags.tid");
                        $query->condition("taxonomy_term_data.name",$temp,"IN");
                        $query->addField("taxonomy_term_data","tid","tid");
                        $result = $query->execute()->fetchAll();

                        if(!empty($result)){
                            foreach($result as $item){
                                db_delete("tbl_tags")->condition("tid",$item->tid)->condition("tbl_tags.nid",$nid)->execute();
                            }
                        }

                    }
                }
            }
            drupal_set_message("Cập nhật thành công!");
            $data['response'] = "OK";
            break;
        case "keyword-change-tags":
            $type = "keyword";
            $nid = $_REQUEST['nid'];
            $option = $_REQUEST['option'];
            $_data = json_decode($_REQUEST['data']);
            $tags = json_decode($_REQUEST['tags']);
            if(!empty($tags)){
                cassiopeia_change_tags($option,$_data,$tags,$type,$nid);
            }
            drupal_set_message("Cập nhật thành công!");
            $data['response'] = "OK";
            break;
        case "add-remove-backlinks-to-tags":
            $type = "backlink";
            $option = $_REQUEST['option'];
            $nid = $_REQUEST['nid'];
            $_data = json_decode($_REQUEST['data']);
            $tags = json_decode($_REQUEST['tags']);
            if(!empty($tags)){
                if($option=="ADD"){
//                    print_r($tags);
                    foreach($tags as $tag){
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
                            "value"     => $nid,
                            "condition" => "=",
                        );
                        $conditions['name'] = array(
                            "type"      => "propertyCondition",
                            "value"     => $tag->value,
                            "condition" => "=",
                        );
                        $tags = cassiopeia_get_items_by_conditions($conditions,"tags","taxonomy_term");
                        $term = !empty($tags)?array_values($tags)[0]:null;
                        if(!empty($term)){
                            $query = db_select("tbl_tags","tbl_tags");
                            $query->fields("tbl_tags");


                            $query->condition("tbl_tags.nid",(array)$_data,"IN");
                            $query->condition("tbl_tags.tid",$term->tid);
                            $query->fields("tbl_tags");
                            $result = $query->execute()->fetchAll();
                            $notInIDs = array();
                            if(!empty($result)){
                                foreach($result as $item){
                                    $notInIDs[] = $item->nid;
                                }
                                $query2 = db_select("tbl_backlink","tbl_backlink");
                                $query2->fields("tbl_backlink");

                                $query2->condition("tbl_backlink.id",(array)$_data,"IN");
                                $query2->condition("tbl_backlink.id",$notInIDs,"NOT IN");
                                $result2 = $query2->execute()->fetchAll();
                                if(!empty($result2)){
                                    $query = db_insert("tbl_tags")->fields(array("created","tid","nid"));

                                    foreach($result2 as $_node){
                                        $values = array();
                                        $values['created'] = REQUEST_TIME;
                                        $values['nid'] = $_node->nid;
                                        $values['tid'] = $term->tid;
                                        $query->values($values);
                                    }
                                    $query->execute();
                                }
                            }else{
                                if(!empty($_data)){
                                    $query = db_insert("tbl_tags")->fields(array("created","tid","nid"));
                                    foreach($_data as $nid){
                                        $values = array();
                                        $values['created'] = REQUEST_TIME;
                                        $values['nid'] = $nid;
                                        $values['tid'] = $term->tid;
                                        $query->values($values);
                                    }
                                    $query->execute();
                                }
                            }
                        }else{
                            $v = taxonomy_vocabulary_machine_name_load("tags");
                            $newterm = new stdClass();
                            $newterm->name = $tag->value;
                            $newterm->vid = $v->vid;
                            $newterm->field_user['und'][0]['target_id'] = $user->uid;
                            if($type=="backlink"){
                                $newterm->field_backlink_project['und'][0]['nid'] = $nid;
                                taxonomy_term_save($newterm);
                                foreach($_data as $_nid){
                                    db_insert("tbl_tags")->fields(array(
                                        "created"   => REQUEST_TIME,
                                        "nid"   => $_nid,
                                        "pid"   => $nid,
                                        "tid"   => $newterm->tid,
                                    ))->execute();
                                }
                            }

                        }
                    }
                }else{
                    $temp = array();
                    if(!empty($tags)){
                        foreach($tags as $tag){
                            $temp[] = $tag->value;
                        }
                    }
                    foreach($_data as $nid){
                        $query = db_select("tbl_tags","tbl_tags");
//                        $query->fields("tbl_node");
                        $query->condition("tbl_tags.nid",$nid);
//                        $query->join("field_data_field_tags","tbl_tag","tbl_tag.entity_id=tbl_node.nid");
                        $query->join("taxonomy_term_data","taxonomy_term_data","taxonomy_term_data.tid=tbl_tags.tid");
                        $query->condition("taxonomy_term_data.name",$temp,"IN");
                        $query->addField("taxonomy_term_data","tid","tid");
                        $result = $query->execute()->fetchAll();

                        if(!empty($result)){
                            foreach($result as $item){
                                db_delete("tbl_tags")->condition("tid",$item->tid)->condition("tbl_tags.nid",$nid)->execute();
                            }
                        }

                    }
                }
            }
            drupal_set_message("Cập nhật thành công!");
            $data['response'] = "OK";
            break;
        case "user-backlink-check-indexed-response":
            $id = $_REQUEST['id'];
            $indexed = $_REQUEST['indexed'];
            try{
                $today = date("d-m-Y",REQUEST_TIME);
                $tracking = cassiopeia_get_user_tracking_by_date($today);
                $packet = cassiopeia_get_available_packet_by_uid($user->uid);
                if(empty($tracking)){
                    try{
                        db_insert("tbl_user_tracking")->fields(array(
                            "uid"  => $user->uid,
                            "date"  => $today,
                            "available_product" => $packet->id,
                            "check_backlink" => 0,
                            "indexed_google" => 0,
                            "content_duplicate" => 0,
                            "key_search" => 0,
                        ))->execute();
                        $tracking = cassiopeia_get_user_tracking_by_date($today);
                    }catch (Exception $e){
                        print_r($e);
                    }
                }
                if($packet->product!=AGENCY && $packet->product!=BASIC && $packet->expired>0 && $packet->expired<REQUEST_TIME){ // tài khoản hết hạn
                    $data['response'] = "EXPIRED";
                }else {
                    if ($packet->indexed_google > 0 && $tracking->indexed_google >= $packet->indexed_google) {
                        $data['response'] = "LIMITED";
                    } else {
                        db_update("tbl_user_tracking")->fields(array(
                            "indexed_google"    => $tracking->indexed_google+1,
                        ))->condition("uid",$user->uid)->condition("date",$tracking->date)->execute();
                        db_update("tbl_backlink")->fields(array(
                            "changed"       => REQUEST_TIME,
                            "indexed"        => $indexed,
                        ))->condition("id",$id)->execute();
                        $array = array(
                            "pid"   => null,
                            "nid"   => $id,
                        );
                        if(!empty($id)){
                            $backlinks = cassiopeia_get_backlinks($array);
                        }
                        $data['values'] = json_encode($backlinks);
                    }
                }

            }catch (Exception $e){
                cassiopeia_dump($e);
            }
            break;
        case "user-backlink-get-tels-response-complete":
          drupal_set_message("Đã quét xong!");
          // Đảm bảo phiên làm việc được commit
          drupal_session_commit();

          // Thêm thông tin kết quả để JavaScript xử lý
          $data['response'] = "OK";
          $data['message'] = "Quét số điện thoại hoàn tất!";

          // Đặt một biến session báo hiệu quét đã hoàn tất
          $_SESSION['tel_scan_completed'] = true;
          break;
        case "user-backlink-get-tels-response":
          $response = json_decode($_REQUEST['responseObject']);
          $tels = $response->tels;
          try{
            if(!empty($tels)){
              db_delete("cassiopeia_backlink_tel")->condition("bid",$response->id)->execute();
              $query = db_insert("cassiopeia_backlink_tel")->fields(array("uid","bid","tel","created"));
              foreach($tels as $tel){
                $query->values(array($user->uid,$response->id,$tel,REQUEST_TIME));
              }
              $query->execute();
            }
            $array = array(
              "pid"   => null,
              "nid"   => $response->id,
            );
            $backlinks = cassiopeia_get_backlinks($array);
            $data['values'] = json_encode($backlinks);
            $data['response'] = "OK";
          }catch (Exception $e){
            _print_r($e);
          }
          break;
        case "user-backlink-check-response":
            $responseObject = json_decode($_REQUEST['responseObject']);
            $data = cassiopeia_user_check_backlink($responseObject);
            break;
        case "user-backlink-bulk":
             $option = $_REQUEST['option'];
             $_data = json_decode($_REQUEST['_data']);
             switch($option){
                 case "check_baclink" :break;
                 case "delete_backlink" :
                        $query = db_select("tbl_backlink","tbl_backlink");
                        $query->fields("tbl_backlink");

                        $query->condition("tbl_backlink.id",$_data,"IN");
                        $query->condition("tbl_backlink.uid",$user->uid);
                        $query->addExpression("GROUP_CONCAT(tbl_backlink.id separator ',')","ids");
                        $result = $query->execute()->fetchObject();
//                        cassiopeia_dump($result);
                        try{
                            if(!empty($result->ids)){
                                $db_transaction = db_transaction();
                                try{
                                    db_delete("tbl_backlink")->condition("id",explode(",",$result->ids),"IN")->execute();
                                    db_delete("tbl_backlink_detail")->condition("nid",explode(",",$result->ids),"IN")->execute();
                                }catch (Exception $e){
                                    $db_transaction->rollback();
                                    cassiopeia_dump($e);
                                }
                                $data['response'] = "OK";
                                drupal_set_message("Đã xóa thành công!");
                            }
                        }catch (Exception $e){
                            cassiopeia_dump($e);
                            $data['response'] = "FAIL";
                            drupal_set_message("Đã xảy ra lỗi, vui lòng liên hệ với người quản trị!");
                        }
                     break;
             }
            break;


        case "load-backlink-project-detail":
            $_data = $_REQUEST;
            $data['html'] = _cassiopeia_render_theme("module","cassiopeia","templates/parts/user-backlink-detail.tpl.php",array("data"=>$_data));
            break;
        case "load-backlink-project":
            $_data = $_REQUEST;
            $data['html'] = _cassiopeia_render_theme("module","cassiopeia","templates/parts/user-backlink.tpl.php",array("data"=>$_data));
            break;
        case "load-key-project":
            $_data = $_REQUEST;
            $data['html'] = _cassiopeia_render_theme("module","cassiopeia","templates/parts/user-keyword.tpl.php",array("data"=>$_data));
            break;
        case "user-key-check":
           $id = $_REQUEST['id'];
           $keyword = node_load($id);
           $project = node_load($keyword->field_keyword_project['und'][0]['nid']);
           $lastKey = $_REQUEST['lastKey'];
           $stt = $_REQUEST['stt'];
           $index = $_REQUEST['index'];
           $result = json_decode($index);
//           if(!empty($result)&&is_object($result)){
               $_data['id'] = $id;
               $_data['index'] = $index==-1?-1:$result->index+1;
               $_data['url'] = $result->url??"";
               $response = cassiopeia_user_key_check($_data);
//           }
//        print_r($response);
           if(!isset($response['status']) || (isset($response['status']) && $response['status']!="EXPIRED")){
//              print_r(123213);
               if(!empty($lastKey) && $lastKey==1){
                  $project->changed = REQUEST_TIME;
//                  print_r($project);
                   node_save($project);
                   $query = db_select("node","tbl_node");
                   $query->fields("tbl_node");
                   $query->condition("type","keyword");
                   $query->join("field_data_field_keyword_project","field_keyword_project","field_keyword_project.entity_id=tbl_node.nid");
                   $query->condition("field_keyword_project.field_keyword_project_nid",$project->nid);
                   $query->leftJoin("field_data_field_keyword_position","field_keyword_position","field_keyword_position.entity_id=tbl_node.nid");
                   $query->fields("field_keyword_position");
                   $query->addExpression("COUNT(tbl_node.nid)","total");
                   $query->addExpression("AVG(field_keyword_position.field_keyword_position_value)","AVG");
                   $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=1 and field_keyword_position.field_keyword_position_value>0 THEN 1 ELSE 0 END)","top_1");
                   $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=2 and field_keyword_position.field_keyword_position_value>0 THEN 1 ELSE 0 END)","top_2");
                   $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=3 and field_keyword_position.field_keyword_position_value>0 THEN 1 ELSE 0 END)","top_3");
                   $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=4 and field_keyword_position.field_keyword_position_value>0 THEN 1 ELSE 0 END)","top_4");
                   $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=5 and field_keyword_position.field_keyword_position_value>0 THEN 1 ELSE 0 END)","top_5");
                   $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=6 and field_keyword_position.field_keyword_position_value>0 THEN 1 ELSE 0 END)","top_6");
                   $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=7 and field_keyword_position.field_keyword_position_value>0 THEN 1 ELSE 0 END)","top_7");
                   $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=8 and field_keyword_position.field_keyword_position_value>0 THEN 1 ELSE 0 END)","top_8");
                   $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=9 and field_keyword_position.field_keyword_position_value>0 THEN 1 ELSE 0 END)","top_9");
                   $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=10 and field_keyword_position.field_keyword_position_value>0 THEN 1 ELSE 0 END)","top_10");
                   $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=30 and field_keyword_position.field_keyword_position_value>0 THEN 1 ELSE 0 END)","top_30");
                   $query->addExpression("SUM(CASE WHEN field_keyword_position.field_keyword_position_value<=100 and field_keyword_position.field_keyword_position_value>0 THEN 1 ELSE 0 END)","top_100");
                   $result = $query->execute()->fetchObject();
                   $query = db_select("tbl_check_keyword","tbl_check_keyword");
                   $query->fields("tbl_check_keyword",array("id"));
                   $query->condition("uid",$user->uid);
                   $query->condition("nid",$project->nid);
                   $query->condition("date",strtotime(date("d-m-Y 00:00:00",REQUEST_TIME)));
                   $check = $query->execute()->fetchObject();
                   if(!empty($check)){
                       db_update("tbl_check_keyword")->fields(array(
                           "created"       => REQUEST_TIME,
                           "uid"           => $user->uid,
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
                       ))->condition("id",$check->id)->execute();
                   }else{
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
                   }
               }
           }
            $data['response'] = $response;
            $query = db_select("node","tbl_node");
            $query->fields("tbl_node");
            $query->condition("tbl_node.nid",$id);
            $query->condition("type","keyword");
            $query->orderBy("tbl_node.created","ASC");
//            $query->join("field_data_field_keyword_project","field_keyword_project","field_keyword_project.entity_id=tbl_node.nid");
//            $query->condition("field_keyword_project.field_keyword_project_nid",$pid);

            $query->leftJoin("field_data_field_keyword_old_position","field_keyword_old_position","field_keyword_old_position.entity_id=tbl_node.nid");
            $query->addField("field_keyword_old_position","field_keyword_old_position_value","field_keyword_old_position");

            $query->leftJoin("field_data_field_keyword_position","field_keyword_position","field_keyword_position.entity_id=tbl_node.nid");
            $query->addField("field_keyword_position","field_keyword_position_value","field_keyword_position");

            $query->leftJoin("field_data_field_keyword_best_position","field_keyword_best_position","field_keyword_best_position.entity_id=tbl_node.nid");
            $query->addField("field_keyword_best_position","field_keyword_best_position_value","field_keyword_best_position");

            $query->leftJoin("field_data_field_updated","field_updated","field_updated.entity_id=tbl_node.nid");
            $query->addField("field_updated","field_updated_value","field_updated");

            $query->leftJoin("field_data_field_url","field_url","field_url.entity_id=tbl_node.nid");
            $query->addField("field_url","field_url_value","field_url");

            if(!empty($cache['tag']) && $cache['tag']!="all"){
                $query->join("field_data_field_tags","field_tags","field_tags.entity_id=tbl_node.nid");
                $query->condition("field_tags.field_tags_tid",$cache['tag']);
            }
            if(!empty($cache['top']) && $cache['top']!="all"){
                $query->condition("field_keyword_position.field_keyword_position_value",$cache['top'],"<=");
            }
            if(!empty($cache['rank']) && $cache['rank']!="all"){
                $query->condition("field_keyword_position.field_keyword_position_value",$cache['rank'],"<=");
            }
            if(!empty($cache['title'])){
                $query->condition("tbl_node.title","%".$cache['title']."%","LIKE");
            }
//                            $query->leftJoin("field_data_field_tags","field_tags","field_tags.entity_id=tbl_node.nid");

            $sub = db_select("field_data_field_tags","field_tags");
            $sub->fields("field_tags");
            $sub->addExpression("GROUP_CONCAT(field_tags.field_tags_tid SEPARATOR ',')","field_tags");
            $sub->groupBy("field_tags.entity_id");

            $query->leftJoin($sub,"tbl_tags","tbl_tags.entity_id=tbl_node.nid");
            $query->fields("tbl_tags");


//
//                            $query->range(0,50);
            $keywords = $query->execute()->fetchObject();
//            _print_r($keywords);
            $data['html'] = _cassiopeia_render_theme("module","cassiopeia","templates/parts/row-key-check.tpl.php",array("item"=>$keywords,"stt"=>$stt));
            break;
        case "user-delete-key-search":
            $_data = json_decode($_REQUEST['_data']);
            $project_id = $_REQUEST['project_id'];
            $project = node_load($project_id);
            if(user_has_role(3) || $project->uid == $user->uid){
                try{
                    $query = db_select("node","tbl_node");
                    $query->fields("tbl_node");
                    $query->condition("tbl_node.type","keyword","=");
                    $query->condition("tbl_node.nid",$_data,"IN");
                    $query->condition("tbl_node.uid",$user->uid);
                    $query->addExpression("GROUP_CONCAT(tbl_node.nid separator ',')","ids");
                    $result = $query->execute()->fetchObject();
                    node_delete_multiple(explode(",",$result->ids));
                    drupal_set_message("Đã xóa từ khóa thành công!");
                }catch (Exception $e){
                    print_r($e);
                }
            }
            break;
        case "user-add-key-search":
            $project_id = $_REQUEST['project_id'];
            $key = $_REQUEST['key'];
            try{
                db_insert("tbl_key_search")->fields(array(
                    "created"       => REQUEST_TIME,
                    "key_search"       => $key,
                    "project_id"       => $project_id,
                ))->execute();
                $data['message'] = "Thêm mới từ khóa thành công!";
            }catch (Exception $e){

            }
            break;
        case "get-user-key-search":
            $project_id = $_REQUEST['project_id'];
            $data['html'] = _cassiopeia_render_theme("module","cassiopeia","templates/parts/user-key-search.tpl.php",array("project_id"=>$project_id));
            break;
        case "delete-project":
            $project_name = $_REQUEST['project_name'];
            $project_id = $_REQUEST['project_id'];
            $project = node_load($project_id);
//            print_r($project);
//            print_r($project_name);
            if($project->title==$project_name){
                if($project->type=="content_project"){
                    if(user_has_role(3) || $project->uid==$user->uid){
                        try{
                            db_delete("tbl_tags")->condition("pid",$project_id)->execute();
                            db_delete("cassiopeia_content")->condition("pid",$project_id)->execute();
                            node_delete($project_id);
                            drupal_set_message("Đã xóa dự án thành công!");
                            $data['response'] = "OK";
                        }catch (Exception $E){

                        }
                    }
                }elseif($project->type=="project_backlink"){
                    if(user_has_role(3) || $project->uid==$user->uid){
                        try{
                            db_delete("tbl_tags")->condition("pid",$project_id)->execute();
                            db_delete("tbl_backlink")->condition("pid",$project_id)->execute();
                            db_delete("tbl_backlink_detail")->condition("pid",$project_id)->execute();
                            node_delete($project_id);
                            drupal_set_message("Đã xóa dự án thành công!");
                            $data['response'] = "OK";
                        }catch (Exception $E){

                        }
                    }
                }elseif($project->type=="project_keyword"){
                    if(user_has_role(3) || $project->uid==$user->uid){
                        try{
                            $conditions = array();
                            $conditions['field_keyword_project'] = array(
                                "type"  => "fieldCondition",
                                "key"   => "nid",
                                "value" => $project_id,
                                "condition" => "=",
                            );
                            $nodes = cassiopeia_get_items_by_conditions($conditions,"keyword","node");
                            if(!empty($nodes)){
                                $nides = array();
                                foreach($nodes as $node){
                                    $nides[] = $node->nid;
                                }
                                node_delete_multiple($nides);
                            }
                            node_delete($project_id);
                            drupal_set_message("Đã xóa dự án thành công!");
                            $data['response'] = "OK";
                        }catch (Exception $E){

                        }
                    }
                }
            }else{
                $data['response'] = "FAIL";
                $data['message'] = "Tên dự án không đúng";
            }
            break;
        case "get-project-detail":
            $project_id = $_REQUEST['project_id'];
            $project = node_load($project_id);
            $data['response'] = json_encode($project);
            break;
        case "booking-delete":
            if(user_has_role(3)){
                $code = $_REQUEST['code'];
                try{
                    db_delete("tbl_booking_product")->condition("code",$code)->execute();
                    $data['message'] = "Đã xóa thành công!";
                }catch (Exception $e){
                    $data['message'] = "Đã xảy ra lỗi!";
                }
            }else{
                $data['message'] = "Bạn không có quyền thực hiện chức năng này!";
            }
            break;
        case "booking-change-status":
            if(user_has_role(3)){
                $code = $_REQUEST['code'];
                $status = $_REQUEST['status'];
               if($status==2){
                   $db_transaction = db_transaction();
                   try{

                       $booking = cassiopeia_get_booking_by_code($code);
                       $uid = $booking->uid;
                       $product = node_load($booking->nid);
                       if($booking->nid==AGENCY){
                           $month = 0;
                           user_save(user_load($uid), array('data' => array('session_limit' => 99)));
                       }else{
                           $month = $booking->month;
                       }


//                       cassiopeia_dump($booking);
//                       die;
                       $existing = cassiopeia_get_available_packet_by_uid($uid);
                       // mua mới
//                       _print_r($existing);
//                       _print_r($product);
                       if($existing->product == $product->nid){// gia hạn
                           db_update("tbl_available_product")->fields(array(
                               "expired"                =>strtotime('+'.$month.' months' . date('Y/m/d',  REQUEST_TIME)),
                               "status"                 => 1,
                           ))->condition("id",$existing->id)->execute();
                       }else{
                           db_insert("tbl_available_product")->fields(array(
                               "created"                => REQUEST_TIME,
                               "uid"                    => $uid,
                               "tran_user"              => $user->uid,
                               "product"                => $product->nid,
                               "check_backlink"         => $product->field_p_backlink_check['und'][0]['value'],
                               "indexed_google"         => $product->field_p_indexed_google['und'][0]['value'],
                               "content_duplicate"      => $product->field_p_content_duplicate['und'][0]['value'],
                               "device"                 => $product->field_p_device['und'][0]['value'],
                               "excel"                  => $product->field_p_excel_export['und'][0]['value'],
                               "backlink_project"       => $product->field_p_backlink_project['und'][0]['value'],
                               "key_project"            => $product->field_p_key_project['und'][0]['value'],
                               "key_search"             => $product->field_p_key_check['und'][0]['value'],
                               "content_project"             => $product->field_p_content_project['und'][0]['value'],
                               "content_article"             => $product->field_p_article_content['und'][0]['value'],
                               "content_check"             => $product->field_p_content_check['und'][0]['value'],
                               "expired"                => strtotime('+'.$month.' months' . date('Y/m/d', REQUEST_TIME)),
                               "status"                 => 1,
                           ))->execute();
                       }
                       // nâng cấp lên gold

                       // nâng cấp lên Premium
                       db_update("tbl_booking_product")->fields(array(
                           "status"        => $status,
                           "updated"        => REQUEST_TIME,
                       ))->condition("code",$code)->execute();
                       $data['message'] = "Đã cập nhật!";
                   }catch (Exception $e){
                       _print_r($e);
                       $data['message'] = "Đã xảy ra lỗi!";
                       $db_transaction->rollback();
                   }
               }else{
                   $data['message'] = "Đã cập nhật!";
                   db_update("tbl_booking_product")->fields(array(
                       "status"        => $status,
                       "update"        => REQUEST_TIME,
                   ))->condition("code",$code)->execute();
               }

            }else{
                $data['message'] = "Bạn không có quyền thực hiện chức năng này!";
            }
            break;
        case "buy-product":
            $id= !empty($_REQUEST['priceID'])?$_REQUEST['priceID']:-1;
            $nid= $_REQUEST['product'];
            $_user = user_load($user->uid);
            $product = node_load($nid);
            $db_transaction = db_transaction();
            try{
                $query = db_select("tbl_product_price","tbl_product_price");
                $query->fields("tbl_product_price");
                $query->condition("id",$id);
                $price = $query->execute()->fetchObject();
                $query =db_select("tbl_booking_product","tbl_booking_product");
                $query->fields("tbl_booking_product");
                $query->range(0,1);
                $query->orderBy("id","DESC");
                $last = $query->execute()->fetchObject();
                if(!empty($last)){
                    $code = $last->code;
                    $code = str_replace("DH","",$code);
                    $code++;
                    $temp = $code;
                    for($i=1;$i<=6-count($code);$i++){
                        $temp = "0".$temp;
                    }
                    $code = "DH".$temp;
                }else{
                    $code = "DH000001";
                }
//                $_user = user_load($user->uid);
                $amount = 0;
                if($product->nid==AGENCY){
                    db_insert("tbl_booking_product")->fields(array(
                        "created"  => REQUEST_TIME,
                        "uid"  => $user->uid,
                        "status"  => 0,
                        "priceID"  => $id,
                        "nid"  => $nid,
                        "code"  => $code,
                        "price"  => !empty($product->field_price['und'][0]['value'])?$product->field_price['und'][0]['value']:0,
                        "month"  => 0,
                    ))->execute();
                    $amount = !empty($product->field_price['und'][0]['value'])?$product->field_price['und'][0]['value']:0;
                }else{
                    db_insert("tbl_booking_product")->fields(array(
                        "created"  => REQUEST_TIME,
                        "uid"  => $user->uid,
                        "status"  => 0,
                        "priceID"  => $id,
                        "nid"  => $nid,
                        "code"  => $code,
                        "price"  => !empty($price->price)?$price->price:0,
                        "month"  => $price->month,
                    ))->execute();
                    $amount = !empty($price->price)?($price->price * $price->month):0;
                }
                $site_mail = "";
                $content = variable_get('cassiopeia_config_mail_buy_product_content', array(
                    'value' => '',
                    'format' => 'full_html'
                ));
                $content = $content['value'];
                $content = str_replace("#email",$user->mail,$content);
                $content = str_replace("#product",$product->title,$content);
                $content = str_replace("#tel",$_user->field_tel['und'][0]['value'],$content);
                $content = str_replace("#price",number_format($amount,0,",",".")." đ",$content);
                if($product->nid==AGENCY){
                    $content = str_replace("#month","trọn đời",$content);
                }else{
                    $content = str_replace("#month",$price->month." tháng",$content);
                }
                $mails = variable_get("cassiopeia_config_mail_buy_product_mail");
                $splitter = explode(",",$mails);
                $cc = "";
                $first = "";
                $index = 1;
                if(!empty($splitter)){
                    foreach($splitter as $item){
                        if($index>1){
                            $cc.=",".trim($item);
                        }else{
                            $first = $item;
                            $cc.=trim($item);
                        }
                        $index++;
                    }
                }
                if(!empty($cc)){
                    $headers['Cc'] = $cc;
            }
                $body[] = $content;
                $headers['From'] = $headers['Sender'] = $headers['Return-Path'] = $site_mail;
                $headers['MIME-Version'] = '1.0';
                $headers['Content-Type'] = 'text/html; charset=UTF-8; format=flowed; delsp=yes';
                $params = array(
                    'body' => $body,
                    'subject' => "Khách hàng ".$_user->field_full_name['und'][0]['value']." mua gói dịch vụ",
                    'headers' => $headers,
                );
                if (drupal_mail('cassiopeia', 'custom_key',$first, language_default(), $params)) {

                };

                $data['redirect'] = "/booking/complete/".$code;
            }catch (Exception $e){
                print_r($e);
            }
            break;
        case "add-to-cart":
            $product_id = $_REQUEST['product_id'];
            if(empty($_SESSION['cart'])){
                $product = array();
                $product['id'] = $product_id;
                $product['quantity'] = 1;
                $_SESSION['cart'][] = $product;
                $data['check'] = 1;
            }else{
                $data['check'] = 2;
                $flag = false;
                $cart = array();
                $product = array();
                $product['id'] = $product_id;
                $product['quantity'] = 1;
                foreach($_SESSION['cart'] as $item){
                    if($item['id'] == $product_id){
                        $item['quantity'] += 1;
                        $cart[] = $item;
                        $flag=true;
                    }else{
                        $cart[] = $item;
                    }
                }
                if($flag==false){
                    $cart[] = $product;
                }
//                $data['cart'] = $flag;
                $_SESSION['cart'] = $cart;
            }
            break;
    }
  }
  return $data;
}

/**
 * Kiểm tra xem module queue đã được bật chưa và tải tệp queue.inc nếu cần
 */
function cassiopeia_ensure_queue() {
  static $queue_loaded = FALSE;

  if (!$queue_loaded) {
    module_load_include('inc', 'cassiopeia', 'cassiopeia.queue');
    $queue_loaded = TRUE;
  }

  return $queue_loaded;
}

/**
 * Thêm dữ liệu vào hàng đợi hoặc xử lý trực tiếp tùy theo cấu hình
 */
function cassiopeia_process_or_queue($data, $type = NULL) {
  cassiopeia_ensure_queue();

  // Nếu có loại dữ liệu, thêm vào dữ liệu
  if ($type) {
    $data['type'] = $type;
  }

  // Kiểm tra xem có nên sử dụng hàng đợi hay không
  $use_queue = variable_get('cassiopeia_use_queue', TRUE);

  if ($use_queue) {
    // Thêm vào hàng đợi
    return cassiopeia_add_to_queue($data);
  } else {
    // Xử lý trực tiếp
    if (!empty($data['type']) && function_exists('cassiopeia_process_' . $data['type'])) {
      return call_user_func('cassiopeia_process_' . $data['type'], $data);
    } else {
      return cassiopeia_process_default($data);
    }
  }
}

/**
 * Form thiết lập cho hàng đợi Cassiopeia.
 */
function cassiopeia_queue_settings_form() {
  $form = array();

  $form['cassiopeia_queue_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Cài đặt hàng đợi'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $form['cassiopeia_queue_settings']['cassiopeia_use_queue'] = array(
    '#type' => 'checkbox',
    '#title' => t('Sử dụng hàng đợi cho dữ liệu từ extension'),
    '#description' => t('Nếu được chọn, dữ liệu từ extension sẽ được đưa vào hàng đợi và xử lý dần dần. Nếu không, dữ liệu sẽ được xử lý ngay lập tức.'),
    '#default_value' => variable_get('cassiopeia_use_queue', TRUE),
  );

  $form['cassiopeia_queue_settings']['cassiopeia_queue_cron_limit'] = array(
    '#type' => 'select',
    '#title' => t('Số lượng mục xử lý mỗi lần chạy cron'),
    '#options' => drupal_map_assoc(array(10, 20, 50, 100, 200, 500)),
    '#default_value' => variable_get('cassiopeia_queue_cron_limit', 50),
    '#description' => t('Số lượng mục trong hàng đợi sẽ được xử lý mỗi lần chạy cron.'),
  );

  $form['cassiopeia_queue_settings']['cassiopeia_queue_stats'] = array(
    '#type' => 'item',
    '#title' => t('Thống kê hàng đợi'),
    '#markup' => _cassiopeia_queue_stats(),
  );

  $form['cassiopeia_queue_settings']['process_queue'] = array(
    '#type' => 'submit',
    '#value' => t('Xử lý hàng đợi ngay'),
    '#submit' => array('cassiopeia_process_queue_submit'),
  );

  return system_settings_form($form);
}

/**
 * Submit handler cho form xử lý hàng đợi ngay.
 */
function cassiopeia_process_queue_submit($form, &$form_state) {
  // Đảm bảo module queue đã được tải
  cassiopeia_ensure_queue();

  // Lấy giới hạn từ cấu hình
  $limit = variable_get('cassiopeia_queue_cron_limit', 50);

  // Xử lý hàng đợi
  $count = cassiopeia_process_queue('cassiopeia_processing_queue', $limit);

  // Hiển thị thông báo
  drupal_set_message(t('Đã xử lý @count mục trong hàng đợi.', array('@count' => $count)));
}

/**
 * Tạo thống kê hàng đợi.
 */
function _cassiopeia_queue_stats() {
  // Đảm bảo module queue đã được tải
  cassiopeia_ensure_queue();

  // Lấy số lượng mục trong hàng đợi
  $queue = DrupalQueue::get('cassiopeia_processing_queue');
  $count = $queue->numberOfItems();

  // Hiển thị thống kê
  $output = '<div class="queue-stats">';
  $output .= '<p>' . t('Số mục trong hàng đợi: @count', array('@count' => $count)) . '</p>';
  $output .= '</div>';

  return $output;
}

function cassiopeia_get_url_debug_callback() {
    global $user;

    $output = "<h2>Debug thông tin Session GetUrlData</h2>";
    $output .= "<p><strong>User ID:</strong> " . $user->uid . "</p>";
    $output .= "<p><strong>Session ID:</strong> " . session_id() . "</p>";
    $output .= "<p><strong>Thời gian hiện tại:</strong> " . date('Y-m-d H:i:s') . "</p>";

    if (isset($_SESSION['GetUrlData'])) {
        if (empty($_SESSION['GetUrlData'])) {
            $output .= "<p><strong>Session GetUrlData:</strong> Tồn tại nhưng rỗng (null hoặc array rỗng)</p>";
        } else {
            $output .= "<p><strong>Session GetUrlData:</strong> Có dữ liệu</p>";
            $output .= "<p><strong>Số lượng từ khóa:</strong> " . count($_SESSION['GetUrlData']) . "</p>";
            $output .= "<h3>Chi tiết dữ liệu:</h3>";
            $output .= "<pre>" . print_r($_SESSION['GetUrlData'], true) . "</pre>";
        }
    } else {
        $output .= "<p><strong>Session GetUrlData:</strong> Không tồn tại</p>";
    }

    // Kiểm tra tất cả session
    $output .= "<h3>Toàn bộ Session:</h3>";
    $output .= "<pre>" . print_r($_SESSION, true) . "</pre>";

    return $output;
}

/**
 * Mở khóa tài khoản bị khóa do đăng nhập sai quá 5 lần
 */
function cassiopeia_unlock_user_account($uid) {
    global $user;
    
    // Kiểm tra quyền admin
    if (!user_access('cassiopeia module')) {
        drupal_set_message(t('Bạn không có quyền thực hiện hành động này.'), 'error');
        return FALSE;
    }
    
    // Lấy thông tin user
    $account = user_load($uid);
    if (!$account) {
        drupal_set_message(t('Không tìm thấy tài khoản.'), 'error');
        return FALSE;
    }
    
    // Xóa flood control events cho user này
    $identifier = $account->uid . '-' . ip_address();
    flood_clear_event('failed_login_attempt_user', $identifier);
    
    // Cũng xóa theo uid nếu có
    flood_clear_event('failed_login_attempt_user', $account->uid);
    
    // Log hành động
    watchdog('user', 'Admin %admin unlocked account %user from flood control.', array(
        '%admin' => $user->name,
        '%user' => $account->name
    ), WATCHDOG_NOTICE);
    
    drupal_set_message(t('Đã mở khóa tài khoản %name thành công.', array('%name' => $account->name)), 'status');
    return TRUE;
}

/**
 * Kiểm tra xem tài khoản có bị khóa do flood control hay không
 */
function cassiopeia_is_user_locked_by_flood($uid) {
    $account = user_load($uid);
    if (!$account) {
        return FALSE;
    }
    
    // Kiểm tra flood control cho user
    $identifier = $account->uid . '-' . ip_address();
    $is_locked_by_uid_ip = !flood_is_allowed('failed_login_attempt_user', variable_get('user_failed_login_user_limit', 5), variable_get('user_failed_login_user_window', 21600), $identifier);
    
    // Kiểm tra flood control chỉ theo uid
    $is_locked_by_uid = !flood_is_allowed('failed_login_attempt_user', variable_get('user_failed_login_user_limit', 5), variable_get('user_failed_login_user_window', 21600), $account->uid);
    
    return $is_locked_by_uid_ip || $is_locked_by_uid;
}

/**
 * AJAX callback để mở khóa tài khoản
 */
function cassiopeia_ajax_unlock_user() {
    $uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
    
    if ($uid > 0) {
        $result = cassiopeia_unlock_user_account($uid);
        drupal_json_output(array(
            'success' => $result,
            'message' => $result ? 'Mở khóa thành công' : 'Có lỗi xảy ra'
        ));
    } else {
        drupal_json_output(array(
            'success' => FALSE,
            'message' => 'UID không hợp lệ'
        ));
    }
}
