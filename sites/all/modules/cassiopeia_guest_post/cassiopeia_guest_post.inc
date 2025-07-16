<?php
function cassiopeia_guest_post_domain_change_sample_file_page_callback(){
    include  drupal_get_path('module','cassiopeia')."/excel/phpspreadsheet.inc";

    $worksheets = array();
    $worksheet = array('title' => 'Danh sách domain','data'=>array(), 'merge_cells'=> array());
    $worksheet['data'][] = array(
        mb_convert_encoding('Domain' ,  "UTF-8"),
    );
    $worksheet['data'][] = array("vipsedan.vn");
    $worksheet['data'][] = array("mamcungviet.com.vn");
    $worksheet['data'][] = array("kienvanghcm.com");
    $worksheet['data'][] = array("truyenviet24h.com");
    $worksheet['data'][] = array("datvere24h.com");
    $worksheet['data'][] = array("kienvangsaigon.com");
    $worksheet['data'][] = array("giaytoldo.com");
    $worksheet['data'][] = array("thuviensach.vn");
    $worksheet['data'][] = array("baobianhsang.vn");
    $worksheet['data'][] = array("18kauthentic.com");
    $worksheets[]=$worksheet;
//    _print_r($worksheets);
    phpspreadsheet_export($worksheets, 'website-ban-muon-dat-backlink');
}

function cassiopeia_guest_post_user_domain_change_edit_callback($account){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/admin-user-domain-change-edit.tpl.php",array("account"=>$account));
}
function cassiopeia_guest_post_user_point_edit_callback($account){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/admin-user-point-edit.tpl.php",array("account"=>$account));
}
function cassiopeia_guest_post_complain_view_callback($node){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/page-complain-view.tpl.php",array("node"=>$node));
}
function cassiopeia_guest_post_article_view_callback($article){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/page-article-view.tpl.php",array("node"=>$article));
}
function cassiopeia_guest_post_admin_order_content_callback(){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/admin-order-content.tpl.php");
}
function cassiopeia_guest_post_point_exchange_callback(){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/page-point-exchange.tpl.php");
}
function cassiopeia_guest_post_order_content_callback(){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/page-order-content.tpl.php");
}
function cassiopeia_guest_post_admin_manager_complain_page_callback(){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/admin-complain.tpl.php");
}
function cassiopeia_guest_post_complain_page_callback(){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/page-complain.tpl.php");
}
function cassiopeia_guest_post_complain_add_page_callback(){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/page-complain-add.tpl.php");
}
function cassiopeia_guest_post_article_re_post_callback($article){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/page-article-re-post.tpl.php",array("article"=>$article));
}
function cassiopeia_guest_post_admin_manager_website_delete_page_callback($website){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/admin-website-delete.tpl.php",array("website"=>$website));
}
function cassiopeia_guest_post_admin_manager_website_edit_page_callback($website){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/admin-website-edit.tpl.php",array("website"=>$website));
}
function cassiopeia_guest_post_admin_manager_website_category_edit_page_callback($category){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/admin-website-category-edit.tpl.php",array("category"=>$category));
}
function cassiopeia_guest_post_admin_manager_website_category_delete_page_callback($category){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/admin-website-category-delete.tpl.php",array("category"=>$category));
}
function cassiopeia_guest_post_admin_manager_website_category_add_page_callback(){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/admin-website-category-add.tpl.php");
}
function cassiopeia_guest_post_admin_manager_website_add_page_callback(){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/admin-website-add.tpl.php");
}
function cassiopeia_guest_post_admin_manager_website_category_page_callback(){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/admin-website-category.tpl.php");
}
function cassiopeia_guest_post_admin_manager_website_page_callback(){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/admin-website.tpl.php");
}
function cassiopeia_guest_post_admin_manager_domain_booking_price_edit_page_callback($price){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/admin-domain-booking-price-edit.tpl.php",array("price"=>$price));
}
function cassiopeia_guest_post_admin_manager_domain_booking_price_delete_page_callback($price){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/admin-domain-booking-price-delete.tpl.php",array("price"=>$price));
}
function cassiopeia_guest_post_admin_manager_domain_booking_price_add_page_callback(){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/admin-domain-booking-price-add.tpl.php");
}
function cassiopeia_guest_post_admin_manager_domain_booking_price_page_callback(){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/admin-domain-booking-price.tpl.php");
}
function cassiopeia_guest_post_domain_change_booking_page_callback(){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/page-domain-change-booking.tpl.php");
}
function cassiopeia_guest_post_domain_change_booking_payment_page_callback($booking){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/page-domain-change-booking-payment.tpl.php",array("booking"=>$booking));
}
function cassiopeia_guest_post_admin_manager_domain_change_booking_delete_page_callback($booking){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/admin-domain-change-booking-delete.tpl.php",array("booking"=>$booking));
}
function cassiopeia_guest_post_admin_manager_domain_change_booking_page_callback(){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/admin-domain-change-booking.tpl.php");
}
function cassiopeia_guest_post_website_add_page_callback(){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/page-website-add.tpl.php");
}
function cassiopeia_guest_post_article_callback(){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/page-article.tpl.php");
}
function cassiopeia_guest_post_website_page_callback(){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/page-website.tpl.php");
}
function cassiopeia_guest_post_domain_page_callback(){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/page-domain.tpl.php");
}
function cassiopeia_guest_post_article_add_callback(){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/page-article-add.tpl.php");
}
function cassiopeia_guest_post_article_edit_callback($article){
    return _cassiopeia_render_theme("module","cassiopeia_guest_post","templates/pages/page-article-add.tpl.php",array("article"=>$article));
}
function cassiopeia_guest_post_ajax_page(){
    global $user;
    $data = array();
    $data['message'] = '';
    $data['success'] = 0;
    if (isset($_POST['cmd'])) {
        switch ($_POST['cmd']) {
            case "GuestPostArticleAddResponse":
                $id = $_REQUEST['id'];
                $wp_post = !empty($_REQUEST['wp_post'])?json_decode($_REQUEST['wp_post']):null;
                if(!empty($wp_post)&&!empty($wp_post->ID)){
                    $node = cassiopeia_guest_post_article_load($id);
                    $node->url_guest_post = $wp_post->guid;
                    $node->publish_date = strtotime($wp_post->post_date);
                    $node->post_status = $wp_post->post_status=="publish"?1:0;
                    $node->post_id = $wp_post->ID;
                    $node->re_post = 0;
                    $file = file_load($node->image->fid);
                    db_delete("file_managed")->condition("fid",$file->fid)->execute();
                    drupal_unlink($file->uri);
                    $node->image = 0;
                    cassiopeia_guest_post_article_save($node);
                    $data['response'] = "OK";
                    drupal_set_message("Đăng bài thành công!");
                    $log = new stdClass();
                    $log->aid = $id;
                    unset($wp_post->post_content);
                    $log->wp_post = serialize($wp_post);
                    cassiopeia_guest_post_article_log_save($log);
                }else{
                    cassiopeia_guest_post_article_delete($id);
                    $data['response'] = "FAIL";
                }
                break;
            case "Guest_Post_Website_Get_Categories_Complete":
                if(user_has_role(3)){
                    $_node = json_decode($_REQUEST['node']);
                    $website = cassiopeia_guest_post_website_load($_node->id);
                    $website->wp_check = 1;
                    if($_node->wp_response=="FAIL"){
                        $website->status=0;
                        cassiopeia_guest_post_website_save($website);
                    }else{
                        db_delete("cassiopeia_guest_post_category")->condition("website",$website->id)->execute();
                        $categories = $_node->wp_response;
                        if(!empty($categories)){
                            foreach($categories as $category){
                                $_cat = new stdClass();
                                $_cat->title = $category->name;
                                $_cat->website = $website->id;
                                $_cat->tid = $category->term_id;
                                cassiopeia_guest_post_category_save($_cat);
                            }
                        }
                        $website->status=1;
                        cassiopeia_guest_post_website_save($website);
                    }
                }
                break;
        }
    }
    return $data;
}