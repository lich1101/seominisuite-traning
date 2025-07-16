<?php

/**
 * Implements hook_menu().
 */
function cassiopeia_menu() {
    $items = array();
    $items['admin/manager/landing_page_links_render'] = array(
        'type' => MENU_CALLBACK,
        'page callback' => 'cassiopeia_landing_page_links_render_page_callback',
        'title' => t('Liên kết với trang tĩnh'),
        'access arguments' => array('cassiopeia module'),
        'file' => 'cassiopeia.inc',
    );

    $items['home'] = array(
        'type' => MENU_CALLBACK,
        'page callback' => 'cassiopeia_home_page_callback',
        'title' => t('Home'),
        'access arguments' => array('access content'),
        'file' => 'cassiopeia.inc',
    );
    $items['thanh-toan'] = array(
        'type' => MENU_CALLBACK,
        'page callback' => 'cassiopeia_payment_page_callback',
        'title' => t('Thanh toán'),
        'page arguments' => array(1),
        'access arguments' => array('access content'),
        'file' => 'cassiopeia.inc',
    );
    $items['hoan-tat-thanh-toan'] = array(
        'type' => MENU_CALLBACK,
        'page callback' => 'cassiopeia_payment_finish_page_callback',
        'title' => t('Hoàn tất thanh toán'),
//    'page arguments' => array(1),
        'access arguments' => array('access content'),
        'file' => 'cassiopeia.inc',
    );
    $items['admin/manager/UTM'] = array(
        'type' => MENU_CALLBACK,
        'page callback' => 'cassiopeia_manager_utm_page_callback',
        'title' => t('UTM manager'),
        'access arguments' => array('cassiopeia module'),
        'file' => 'cassiopeia.inc',
    );
    $items['admin/cassiopeia/dashboard'] = array(
        'type' => MENU_CALLBACK,
        'page callback' => 'cassiopeia_dashboard_page_callback',
        'title' => t('Dashboard'),
        'access arguments' => array('cassiopeia module'),
        'file' => 'cassiopeia.inc',
    );
    $items['admin/manager/orders/export'] = array(
        'type' => MENU_CALLBACK,
        'page callback' => 'cassiopeia_order_export_callback',
        'title' => t('Dashboard'),
        'access arguments' => array('cassiopeia module'),
        'file' => 'cassiopeia.inc',
    );

    $items['admin/config/cassiopeia'] = array(
        'title' => 'Cassiopeia',
        'description' => 'Cassiopeia tools.',
        'page callback' => 'system_admin_menu_block_page',
        'access arguments' => array('cassiopeia module'),
        'file' => 'system.admin.inc',
        'file path' => drupal_get_path('module', 'system'),
    );

    $items['admin/config/cassiopeia/general'] = array(
        'type' => MENU_NORMAL_ITEM,
        'title' => t('General configuration'),
        'page callback' => array('drupal_get_form'),
        'page arguments' => array('cassiopeia_config_form'),
        'access arguments' => array('cassiopeia module'),
        'file' => 'cassiopeia.inc',
    );

    $items['cassiopeia/ajax'] = array(
        'type' => MENU_CALLBACK,
        'page callback' => 'cassiopeia_ajax_page',
        'delivery callback' => 'drupal_json_output',
        'access arguments' => array('access content'),
        'file' => 'cassiopeia.inc',
    );

    return $items;
}

/**
 * Implements hook_permission().
 */
function cassiopeia_permission() {
    return array(
        'cassiopeia module' => array(
            'title' => t('cassiopeia module'),
            'description' => t('Access for Cassiopeia module'),
        )
    );
}


function _cassiopeia_admin_theme() {
    return 'cassiopeia_admin_theme';
}

// =----- alter -------------

/**
 * Implements hook_menu_alter().
 */
function cassiopeia_menu_alter(&$items) {
    /* $items['taxonomy/term/%taxonomy_term']['module'] = 'cassiopeia'; */
    $items['taxonomy/term/%taxonomy_term']['page callback'] = '_cassiopeia_tvi_render_term_view';
    $items['taxonomy/term/%taxonomy_term']['page arguments'] = array(2);
    $items['node/%node']['page callback'] = '_cassiopeia_node_render_view';
    $items['node/%node']['page arguments'] = array(1);
}


/**
 * Implements hook_variable_info().
 */

function cassiopeia_variable_info($options) {
}


// =----- End alter -------------

function _cassiopeia_node_render_view($node) {
    global $user;
    if (is_object($node)) {
        $nid = $node->nid;
    }
    else {
        $node = node_load($node);
    }
    if (module_exists('metatag')) {
        metatag_entity_view($node, 'node', 'full', NULL);
    }
    if ($node->type == 'ctype_service') {
//     return _cassiopeia_render_theme('module', 'cassiopeia', 'templates/node/page-service.tpl.php', array('node' => $node));
    }

    return node_page_view($node);
}



function _cassiopeia_tvi_render_term_view($term, $depth = NULL) {
    if (is_object($term)) {
        $tid = $term->tid;
    }
    else {
        $term = taxonomy_term_load($term);
    }
    if (module_exists('metatag')) {
        metatag_entity_view($term, 'taxonomy_term', 'full', NULL);
    }
    if ($term->vocabulary_machine_name == 'tx_article') {
//    return _cassiopeia_render_theme('module', 'cassiopeia', 'templates/term/page-tx-article.tpl.php', array('term' => $term));
    }


    module_load_include('inc', 'taxonomy', 'taxonomy.pages');
    return taxonomy_term_page($term);
}


function cassiopeia_theme($existing, $type, $theme, $path) {
    $themes = array(
        'cassiopeia_table_drag_components' => array(
            'render element' => 'element'
        ),
    );
    return $themes;
}

function theme_cassiopeia_table_drag_components($vars) {
    $element = $vars['element'];

    drupal_add_tabledrag($vars['element']['#id'] . '-table', 'order', 'sibling', 'item-row-weight');

    $header = array(
        'label' => t('label'),
        'weight' => t('Weight'),
    );

    $rows = array();
    foreach (element_children($element) as $key) {
        $row = array();
        $row['data'] = array();
        foreach ($header as $fieldname => $title) {
            $row['data'][] = drupal_render($element[$key][$fieldname]);
            $row['class'] = array('draggable');
        }
        $rows[] = $row;
    }

    return theme('table', array(
        'header' => $header,
        'rows' => $rows,
        'attributes' => array('id' => $vars['element']['#id'] . '-table'),
    ));
}

function cassiopeia_page_build(&$page) {
    global $user, $theme_key;
    if ( user_has_role(3) && $theme_key == 'cassiopeia_theme') {
        $page['page_bottom']['c3s_admin_menu'] = array(
            '#markup' => '<div style="position: fixed; top: 40%; left: 10px; background: #ccc; border-radius: 5px; padding: 5px; z-index: 999;">'.l('Quản trị', 'admin/cassiopeia/dashboard').'</div>'
        );
    }
}


// ----- api ------
function cassiopeia_get_nodes($type = array(), $language = NULL, $range = NULL) {
    $_nodes_query = new EntityFieldQuery();
    $_nodes_query_result = NULL;
    if (is_array($type)) {
        $_nodes_query_result = $_nodes_query
            ->entityCondition('entity_type', 'node')
            ->propertyCondition('type', $type, 'IN');

    }
    else {
        $_nodes_query_result = $_nodes_query
            ->entityCondition('entity_type', 'node')
            ->propertyCondition('type', $type, '=');
    }

    if ($language && is_string($language) && $language != 'all') {
        $_nodes_query->propertyCondition('language', $language, '=');
    }

    if ($range && is_array($range) && !empty($range['start']) && is_numeric($range['start']) && !empty($range['end']) && is_numeric($range['end'])) {
        $_nodes_query->range($range['start'], $range['end']);
    }

    $_nodes_query_result = $_nodes_query->execute();

    return $_nodes_query_result;
}




// ----- api core ------

function _cassiopeiaviews_display($view_name, $display_id, $arg = array()) {
    $view = views_get_view($view_name);
    $output = "";
    if (!empty($view)) {
        $output = $view->execute_display($display_id, $arg);
        if (is_array($output)) {
            $output = $output['content'];
        }
        if (!$output && !count($view->result)) {
            $output = "";
        }
    }
    return $output;
}

function _cassiopeia_render_theme($type, $name, $path, $variables = array()) {
    $path_temp = drupal_get_path($type, $name);
    return theme_render_template($path_temp . "/" . $path, $variables);
}


function _cassiopeia_convert_time_ago($timestamp) {
    $_output = "";
    $day = $timestamp / (60 * 60 * 24);
    if ($day >= 365) {
        $_output = floor($day % 365) . ' năm trước';
    }
    elseif ($day >= 30 && $day < 365) {
        $_output = floor($day % 30) . ' tháng trước';
    }
    elseif ($day >= 1 && $day < 30) {
        $_output = floor($day % 30) . ' ngày trước';
    }
    elseif ($day > 0 && $day < 1) {
        if ($day * 24 >= 1) {
            $_output = floor($day * 24) . ' giờ trước';
        }
        elseif ($day * 24 * 60 > 1) {
            $_output = floor($day * 24 * 60) . ' phút trước';
        }
        else {
            $_output = floor($day * 24 * 60 * 60) . ' giây trước';
        }
    }
    return $_output;
}

function _cassiopeia_get_variable($name, $default) {
    global $language;
    $_variable_query = db_select('cassiopeia_variable', 'cv');
    $_variable_query->fields('cv');
    $_variable_query->condition('cv.name', $name, '=');
    $_variable_query->join('cassiopeia_variable_store', 'cvt', ' cv.name = cvt.name');
    $_variable_query->fields('cvt');
    $_variable_query->condition('cvt.language', $language->language, '=');
    $_variable_query = $_variable_query->execute();
    $_variable_query_result = $_variable_query->fetchAssoc();
    if (!empty($_variable_query_result)) {
        return unserialize($_variable_query_result['value']);
    }
    else {
        return $default;
    }
}

function _cassiopeia_set_variable($name, $value) {
    global $language;
    db_merge('cassiopeia_variable')
        ->key(array('name' => $name))
        ->fields(array('name' => $name))
        ->execute();
    db_merge('cassiopeia_variable_store')
        ->key(array('name' => $name, 'language' => $language->language))
        ->fields(array('value' => serialize($value)))
        ->execute();
}

function _cassiopeia_del_variable($name) {
    db_delete('cassiopeia_variable')
        ->condition('name', $name)
        ->execute();
    db_delete('cassiopeia_variable_store')
        ->condition('name', $name)
        ->execute();
}


function _cassiopeia_load_collections ($collections) {
    $_collection_ids = array();
    foreach ($collections as $_key => $_value) {
        $_collection_ids[] =  $_value['value'];
    }
    $_collection = entity_load('field_collection_item', $_collection_ids);
    return $_collection;
}

function cassiopeia_get_items_by_conditions ($conditions = array(),$bundles,$entity_type) {
    $nodes = array();
    try {
        $query = new EntityFieldQuery();
        $query->entityCondition('entity_type', $entity_type)
            ->entityCondition('bundle', $bundles);
        if (!empty($conditions) && is_array($conditions)) {
            foreach ($conditions as $condition_key => $condition_value) {
                if ($condition_value['type'] == 'propertyCondition') {
                    $query->propertyCondition($condition_key, $condition_value['value'],$condition_value['condition']);
                }elseif ($condition_value['type'] == 'fieldCondition') {
//                    print(1);die;
                    $query->fieldCondition($condition_key, $condition_value['key'], $condition_value['value'], $condition_value['condition']);
//                var_dump($query);die;
                }
                elseif ($condition_value['type'] == 'propertyOrderBy') {
                    $query->propertyOrderBy($condition_key, $condition_value['direction']);
                }
                elseif ($condition_value['type'] == 'fieldOrderBy') {
                    $query->fieldOrderBy($condition_key, $condition_value['column'], $condition_value['direction']);
                }
                elseif ($condition_value['type'] == 'range' && isset($condition_value['start']) && isset($condition_value['limit'])) {
                    $query->range($condition_value['start'], $condition_value['limit']);
                }
            }
        }

        $result = $query->execute();
//        var_dump($result);die;
        if($entity_type=="node"){
            if (isset($result['node'])) {
                $node_nids = array_keys($result['node']);
                $nodes = entity_load('node', $node_nids);
            }
        }else if($entity_type=="taxonomy_term"){
            if($result['taxonomy_term']){
                $term_tids = array_keys($result['taxonomy_term']);

                $terms = taxonomy_term_load_multiple($term_tids);
                return $terms;
            }
        }
    }catch (Exception $e) {
        throw $e;
    }

    return $nodes;
}

function cassiopeia_payment_form($form,&$form_state,$utm=array()){
    $form = array();
    /* print_r($utm); */
    $form['customer_full_name'] = array(
        '#title' => "Họ tên:*",
        '#type' => 'textfield',

        '#size' => 60,

        '#maxlength' => 128,

        '#required' => TRUE,
        "#prefix" => "<div class='item'><label>Họ tên:*</label>",
        "#suffix" => "</div>",
        '#attributes' => array("class"=>array("item")),
        '#theme_wrappers' => array(),
    );
    $form['customer_tel'] = array(
        '#title' => "Điện thoại:*",
        '#type' => 'textfield',

        '#size' => 60,

        '#maxlength' => 128,

        '#required' => TRUE,
        "#prefix" => "<div class='item'><label>Điện thoại:*</label>",
        "#suffix" => "</div>",
        '#attributes' => array("class"=>array("item")),
        '#theme_wrappers' => array(),

    );
    $form['customer_email'] = array(
        '#title' => "Email:*",
        '#type' => 'textfield',

        '#size' => 60,

        '#maxlength' => 128,

        '#required' => TRUE,
        "#prefix" => "<div class='item'><label>Email:*</label>",
        "#suffix" => "</div>",
        '#attributes' => array("class"=>array("item")),
        '#theme_wrappers' => array(),

    );
    $form['customer_address'] = array(
        '#title' => "Địa chỉ:",
        '#type' => 'textfield',

        '#size' => 60,

        '#maxlength' => 128,

//        '#required' => TRUE,
        "#prefix" => "<div class='item'><label>Địa chỉ:</label>",
        "#suffix" => "</div>",
        '#attributes' => array("class"=>array("item")),
        '#theme_wrappers' => array(),
    );
    $form['ticket_quantity'] = array(

        '#type' => 'textfield',
        '#attributes' => array("placeholder"=>array("Số vé")),
        '#size' => 60,

        '#maxlength' => 128,

//        '#required' => TRUE,
        "#default_value" => 1,
//        '#theme_wrappers' => array(""),
        '#attributes' => array("class"=>array("hidden")),
    );
    $form['discount_code'] = array(

        '#type' => 'textfield',

        '#size' => 60,

        '#maxlength' => 128,

//        '#required' => TRUE,
        '#attributes' => array("placeholder"=>array("Mã giảm giá")),
        '#attributes' => array("class"=>array("hidden")),

    );
    $form['packet_id'] = array(

        '#type' => 'textfield',

        '#size' => 60,

        '#maxlength' => 128,

//        '#required' => TRUE,
        '#attributes' => array("placeholder"=>array("ID Gói học")),
        '#attributes' => array("class"=>array("hidden")),

    );
    $form['utm_source'] = array(
        '#type' => 'textfield',
        '#size' => 60,
        '#maxlength' => 128,
        '#default_value'    => !empty($utm['utm_source'])?$utm['utm_source']:"",
        '#attributes' => array(
            "placeholder"=>array("ID Gói học"),
            "readonly"=>"readonly",
            "class"=>array("hidden"),
        ),
    );
    $form['utm_campaign'] = array(
        '#type' => 'textfield',
        '#size' => 60,
        '#maxlength' => 128,
        '#default_value'    => $utm['utm_campaign'],
        '#attributes' => array(
            "placeholder"=>array("ID Gói học"),
            "readonly"=>"readonly",
            "class"=>array("hidden"),
        ),
    );
    $form['utm_media'] = array(
        '#type' => 'textfield',
        '#size' => 60,
        '#maxlength' => 128,
        '#default_value'    => !empty($utm['utm_media'])?$utm['utm_media']:0,
        '#attributes' => array(
            "placeholder"=>array("ID Gói học"),
            "readonly"=>"readonly",
            "class"=>array("hidden"),
        ),
    );

    $form["callback-request-submit"] = array(
        "#type" => "submit",

        "#value" => "<span class='fa fa-search'></span>",

//        "#attributes" => array("class" => array("btn-lg btn-wetbrush")),
        '#attributes' => array("class"=>array("hidden")),
    );
    $form['#attributes'] = array("class"=>array("payment-form"));
    return $form;
}
function cassiopeia_payment_form_submit($form,&$form_state){
    global $user;
//    var_dump($form_state);die;
    $_user = user_load($user->uid);
    $contact = array();
    foreach($form_state['values'] as $key => $value){
        if(!empty($form_state['values'][$key])){
            $contact[$key] = $value;
        }
    }
    $contact['created'] = REQUEST_TIME;
    $contact['status'] = 0;
    if (!empty($contact)) {
        try {
            $total_cost = 0;
            $node = new stdClass();
            $node->type = "ctype_order";
            $node->uid = 0;
            $node->title = $contact['customer_full_name'];
            $node->field_order_tel['und'][0]['value'] = $contact['customer_tel'];
            $node->field_order_email['und'][0]['value'] = $contact['customer_email'];
            $node->field_utm_code['und'][0]['tid'] = $contact['utm_source'];
            $node->field_utm_campaign['und'][0]['value'] = $contact['utm_campaign'];
            $node->field_utm_media['und'][0]['value'] = $contact['utm_media'];
            if(!empty($contact['customer_address'])){
                $node->field_order_address['und'][0]['value'] = $contact['customer_address'];
            }
//            $node->field_order_province['und'][0]['value'] = $contact['customer_province'];

            $node->field_order_ticket_quantity['und'][0]['value'] = $contact['ticket_quantity'];
            $node->field_order_packet['und'][0]['nid'] = $contact['packet_id'];
            $packet = node_load($contact['packet_id']);
            $node->field_order_training['und'][0]['tid'] = $packet->field_ctype_course_training['und'][0]['tid'];
            $last_order_conditions = array();
            $last_order_conditions['range'] = array(
                "type" => "range",
                "start" => 0,
                "limit" =>1,
            );
            $last_order_conditions['created'] = array(
                "type" => "propertyOrderBy",
                "direction" => "DESC",
            );
            $last_order = cassiopeia_get_items_by_conditions($last_order_conditions,"ctype_order","node");
            if(!empty($last_order)){
                foreach($last_order as $key => $value){
                    $last_order = $value;
                }
                $order_code = $last_order->field_order_code['und'][0]['value']+1;
            }else{
                $order_code = 102023;
            }
            $node->field_order_code['und'][0]['value'] = $order_code;
            if(!empty($contact['discount_code'])){
                $node->field_order_discount_code['und'][0]['value'] = $contact['discount_code'];
                $today = getdate();
                $time = strtotime($today['mday']." ".$today['month']." ".$today['year']." ".$today['hours'].":".$today['minutes']);
                $discount_code_conditions = array();
                $discount_code_conditions['status'] = array(
                    "type" => "propertyCondition",
                    "value" => 1,
                    "condition" => "=",
                );
                $discount_code_conditions['title'] = array(
                    "type" => "propertyCondition",
                    "value" => $contact['discount_code'],
                    "condition" => "=",
                );
                $discount_code_conditions['field_discount_code_packets'] = array(
                    "type" => "fieldCondition",
                    "key" => "nid",
                    "value" => $contact['packet_id'],
                    "condition" => "=",
                );
                $discount_code_conditions['field_discount_code_start_time'] = array(
                    "type" => "fieldCondition",
                    "key" => "value",
                    "value" => date("Y-m-d H:i:s",$time),
                    "condition" => "<=",
                );
                $discount_code_conditions['field_discount_code_end_time'] = array(
                    "type" => "fieldCondition",
                    "key" => "value",
                    "value" => date("Y-m-d H:i:s",$time),
                    "condition" => ">=",
                );
                $discount = cassiopeia_get_items_by_conditions($discount_code_conditions,"discount_code","node");
                foreach($discount as $key => $value){
                    $discount = $value;
                }
                $discount_type = $discount->field_discount_code_type['und'][0]['tid'];
//                12 = %
//                13 = tiền cụ thẻ
                if($discount_type==13){
                    $pvalue = $discount->field_discount_code_pvalue['und'][0]['value'];
                    $node->field_order_total_cost['und'][0]['value'] = ($packet->field_ctype_course_fee['und'][0]['value'] - $pvalue)*$contact['ticket_quantity'];
                    $total_cost = ($packet->field_ctype_course_fee['und'][0]['value'] - $pvalue)*$contact['ticket_quantity'];
                }else if($discount_type==12){
                    $pvalue = $discount->field_discount_ppercent['und'][0]['value'];
                    $node->field_order_total_cost['und'][0]['value'] = ($packet->field_ctype_course_fee['und'][0]['value'] - $pvalue*$packet->field_ctype_course_fee['und'][0]['value'])*$contact['ticket_quantity'];
                    $total_cost = ($packet->field_ctype_course_fee['und'][0]['value'] - $pvalue*$packet->field_ctype_course_fee['und'][0]['value'])*$contact['ticket_quantity'];
                }
            }else{
                $node->field_order_total_cost['und'][0]['value'] = $packet->field_ctype_course_fee['und'][0]['value']*$contact['ticket_quantity'];
                $total_cost = $packet->field_ctype_course_fee['und'][0]['value']*$contact['ticket_quantity'];
            }
            $node = node_submit($node);
            node_save($node);
            $_SESSION['payment']['order_code'] = $order_code;
            $_SESSION['payment']['total_cost'] = $total_cost;
            $form_state['redirect'] =  "hoan-tat-thanh-toan";
        }catch ( Exception $e) {
            drupal_set_message('Hệ thống đang bận vui lòng quay lại sau it phút.');
            throw ($e);
        }
    }
}
function cassiopeia_get_orders_by_utm($utm_id=null){
    $order_conditions = array();
    $order_conditions['status'] = array(
        "type"      => "propertyCondition",
        "value"     => 1,
        "condition" => "=",
    );
    $order_conditions['created'] = array(
        "type"      => "propertyOrderBy",
        "direction" => "DESC",
    );
    $order_conditions['field_utm_code'] = array(
        "type"      => "fieldCondition",
        "key"       => "tid",
        "value"     => $utm_id,
        "condition" => "=",
    );
    $orders = cassiopeia_get_items_by_conditions($order_conditions,"ctype_order","node");
    return $orders;
}
function cassiopeia_get_orders_by_conditions($conditions = array()){
    $order_conditions = array();
    $order_conditions['status'] = array(
        "type"      => "propertyCondition",
        "value"     => 1,
        "condition" => "=",
    );
    $order_conditions['created'] = array(
        "type"      => "propertyOrderBy",
        "direction" => "DESC",
    );
    if(!empty($conditions) && !empty($conditions['utm'])){
        $order_conditions['field_utm_code'] = array(
            "type"      => "fieldCondition",
            "key"       => "tid",
            "value"     => $conditions['utm']['value'],
            "condition" => "=",
        );
    }
    $orders = cassiopeia_get_items_by_conditions($order_conditions,"ctype_order","node");
    return $orders;
}