<?php
function cassiopeia_user_transaction_page_callback() {
  return _cassiopeia_render_theme('module', 'cassiopeia_user', 'templates/page/user_transaction.tpl.php');
}
function cassiopeia_user_registered_page($account) {
  return _cassiopeia_render_theme('module', 'cassiopeia_user', 'templates/page/user_registered_page.tpl.php', array('account' => $account));
}
function cassiopeia_user_user_change_password_page($account) {
  return _cassiopeia_render_theme('module', 'cassiopeia_user', 'templates/page/user_change_password_page.tpl.php', array('account' => $account));
}
function cassiopeia_user_user_change_packet_page($account) {
  return _cassiopeia_render_theme('module', 'cassiopeia_user', 'templates/page/user_change_packet_page.tpl.php', array('account' => $account));
}
function cassiopeia_user_ajax_autocomplete_callback () {

    global $user;
    $string = isset($_REQUEST['string'])?trim($_REQUEST['string']):"";
    $matches = array();
    try {
        $query = db_select("users", "tbl_user");
        $query->fields("tbl_user");
        $query->join("users_roles", "tbl_role", "tbl_role.uid = tbl_user.uid");
        $query->condition("tbl_role.rid", 4);
        $query->join("field_data_field_full_name", "field_full_name", "field_full_name.entity_id=tbl_user.uid");
        $query->addField("field_full_name", "field_full_name_value", "agent_transaction_name");
        if(!empty($string)){
            $or = db_or();
            $or->condition("field_full_name.field_full_name_value","%".$string."%","LIKE");
            $or->condition("tbl_user.name","%".$string."%","LIKE");
            $query->condition($or);
        }
        $query->range(0,50);
        $result = $query->execute()->fetchAll();
        foreach ($result as $row) {
            $matches[] = array('id'=>$row->uid, 'name'=> $row->agent_transaction_name, 'code'=>$row->uid, 'value'=>$row->uid);
        }
    }catch (Exception $e) {

    }
    drupal_json_output($matches);
}