<?php
/**
 * Plugin Name: SeominiSuite
 * Description: This is the SeominiSuite Plugin.
 * Version: 1.0
 * Author: SEOMiniSuite.com
 * Author URI: https://seominisuite.com/
 **/

// Cho phép CORS cho mọi domain (chỉ dùng khi DEV/test)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit; // Xử lý preflight
}

add_action('wp_ajax_seominisuite_add_article', 'seominisuite_add_article_callback');
add_action('wp_ajax_nopriv_seominisuite_add_article', 'seominisuite_add_article_callback');

add_action('wp_ajax_seominisuite_get_categories', 'seominisuite_get_categories_function');
add_action('wp_ajax_nopriv_seominisuite_get_categories', 'seominisuite_get_categories_function');

add_action('wp_ajax_seominisuite_check_status', 'seominisuite_check_status_function');
add_action('wp_ajax_nopriv_seominisuite_check_status', 'seominisuite_check_status_function');
//add_menu_page(
//    'SeoMinisuite',
//    'test',
//    $capability,
//    '',
//    '',
//    plugins_url( 'SeoMinisuite/images/icon.jpg' ),
//    ''
//);
function seominisuite_add_article_callback() {
    // Nhận dữ liệu từ extension
    $node = isset($_POST['node']) ? json_decode(stripslashes($_POST['node']), true) : null;
    $content = isset($_POST['content']) ? $_POST['content'] : '';
    if (!$node || empty($content)) {
        wp_send_json_error(['message' => 'Thiếu dữ liệu node hoặc content']);
        wp_die();
    }
    // Tạo bài viết mới
    $postarr = [
        'post_title'   => isset($node['title']) ? $node['title'] : 'Bài viết từ SeoMiniSuite',
        'post_content' => $content,
        'post_status'  => 'pending', // hoặc 'draft', 'publish' nếu muốn
        'post_author'  => 1, // ID user admin
        'post_type'    => 'post',
    ];
    $post_id = wp_insert_post($postarr);
    if (is_wp_error($post_id)) {
        wp_send_json_error(['message' => 'Không tạo được bài viết', 'error' => $post_id->get_error_message()]);
        wp_die();
    }
    // Nếu có meta SEO, lưu lại
    if (!empty($node['seo_title'])) {
        update_post_meta($post_id, '_yoast_wpseo_title', $node['seo_title']);
    }
    if (!empty($node['seo_description'])) {
        update_post_meta($post_id, '_yoast_wpseo_metadesc', $node['seo_description']);
    }
    // Trả về kết quả
    wp_send_json_success(['message' => 'Đã tạo bài viết thành công', 'post_id' => $post_id]);
    wp_die();
}
function seominisuite_get_categories_function(){
    $args = array(
        'type'      => 'post',
        'child_of'  => 0,
        'parent'    => ''
    );
    $categories = get_categories();
    wp_send_json($categories);
//    wp_send_json(get_terms());
}
function seominisuite_check_status_function(){
    wp_send_json("OK");
}