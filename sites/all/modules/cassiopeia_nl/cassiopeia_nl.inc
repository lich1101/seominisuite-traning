<?php

function cassiopeia_config_nl_form() {
  $form = array();
  $form['cassiopeia_config_nl'] = array(
    '#type' => "fieldset",
    '#title' => 'Ngân lượng',
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $form['cassiopeia_config_nl']['nl_email'] = array(
    '#type' => "textfield",
    '#title' => 'Địa chỉ email tài khoản Ngân Lượng',
    "#default_value" => variable_get('nl_email', ''),
  );

  $form['cassiopeia_config_nl']['nl_merchant_id'] = array(
    '#type' => "textfield",
    '#title' => 'Merchant ID',
    "#default_value" => variable_get('nl_merchant_id', ''),
  );

  $form['cassiopeia_config_nl']['nl_merchant_pass'] = array(
    '#type' => "textfield",
    '#title' => 'Merchant Pass',
    "#default_value" => variable_get('nl_merchant_pass', ''),
  );

  $form['cassiopeia_config_nl']['nl_bank_url'] = array(
    '#type' => "textfield",
    '#title' => 'Địa chỉ thanh toán thẻ ngân hàng',
    "#default_value" => variable_get('nl_bank_url', ''),
  );

  $form['cassiopeia_config_nl']['nl_card_url'] = array(
    '#type' => "textfield",
    '#title' => 'Địa chỉ thanh toán thẻ điện thoại',
    "#default_value" => variable_get('nl_card_url', ''),
  );

  $form['cassiopeia_config_nl']['nl_card_url'] = array(
    '#type' => "textfield",
    '#title' => 'Địa chỉ thanh toán thẻ điện thoại',
    "#default_value" => variable_get('nl_card_url', ''),
  );

  return system_settings_form($form);
}

function cassiopeia_config_payment_form() {

  $form = array();

  $form['cassiopeia_config_payment_form'] = array(
    '#type' => "container",
    '#title' => 'Form thanh toán',
  );

  $form['cassiopeia_config_payment_personal_account_form'] = array(
    '#type' => "fieldset",
    '#title' => 'Tài khoản cá nhân',
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $_payment_personal_account_description = variable_get('payment_personal_account_description', array(
    'value' => '',
    'format' => 'full_html'
  ));

  $form['cassiopeia_config_payment_personal_account_form']['payment_personal_account_description'] = array(
    '#type' => "text_format",
    '#title' => 'Mô tả',
    '#resizable' => TRUE,
    '#format' => 'full_html',
    "#default_value" => $_payment_personal_account_description['value'],
  );


  $form['cassiopeia_config_payment_atm_credit_card_form'] = array(
    '#type' => "fieldset",
    '#title' => 'ATM / thẻ tín dụng',
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $_payment_atm_credit_card_description = variable_get('payment_credit_card_description', array(
    'value' => '',
    'format' => 'full_html'
  ));

  $form['cassiopeia_config_payment_atm_credit_card_form']['payment_credit_card_description'] = array(
    '#type' => "text_format",
    '#title' => 'Mô tả',
    '#resizable' => TRUE,
    '#format' => 'full_html',
    "#default_value" => $_payment_atm_credit_card_description['value'],
  );

  $form['cassiopeia_config_payment_bank_transfer_form'] = array(
    '#type' => "fieldset",
    '#title' => 'Chuyển khoản ngân hàng',
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $_payment_bank_transfer_description = variable_get('payment_bank_transfer_description', array(
    'value' => '',
    'format' => 'full_html'
  ));

  $form['cassiopeia_config_payment_bank_transfer_form']['payment_bank_transfer_description'] = array(
    '#type' => "text_format",
    '#title' => 'Mô tả',
    '#resizable' => TRUE,
    '#format' => 'full_html',
    "#default_value" => $_payment_bank_transfer_description['value'],
  );


  $form['cassiopeia_config_payment_mobile_card_form'] = array(
    '#type' => "fieldset",
    '#title' => 'Thẻ điện thoại',
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $_payment_mobile_card_description = variable_get('payment_mobile_card_description', array(
    'value' => '',
    'format' => 'full_html'
  ));

  $form['cassiopeia_config_payment_mobile_card_form']['payment_mobile_card_description'] = array(
    '#type' => "text_format",
    '#title' => 'Mô tả',
    '#resizable' => TRUE,
    '#format' => 'full_html',
    "#default_value" => $_payment_mobile_card_description['value'],
  );


  $form['cassiopeia_config_payment_office_form'] = array(
    '#type' => "fieldset",
    '#title' => 'Thu tiền tại văn phòng',
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $_payment_office_description = variable_get('payment_office_description', array(
    'value' => '',
    'format' => 'full_html'
  ));

  $form['cassiopeia_config_payment_office_form']['payment_office_description'] = array(
    '#type' => "text_format",
    '#title' => 'Mô tả',
    '#resizable' => TRUE,
    '#format' => 'full_html',
    "#default_value" => $_payment_office_description['value'],
  );


  $form['cassiopeia_config_payment_suport'] = array(
    '#type' => "fieldset",
    '#title' => 'Hỗ trợ, tư vấn',
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );


  $_payment_suport_content = variable_get('payment_suport_content', array(
    'value' => '',
    'format' => 'full_html'
  ));

  $form['cassiopeia_config_payment_suport']['payment_suport_content'] = array(
    '#type' => "text_format",
    '#title' => 'Nội dung',
    '#resizable' => TRUE,
    '#format' => 'full_html',
    "#default_value" => $_payment_suport_content['value'],
  );

  $form['cassiopeia_config_payment_suport']['payment_phone_suport'] = array(
    '#type' => 'textfield',
    '#title' => 'Số điện thoại hỗ trợ',
    '#default_value' => variable_get('payment_phone_suport',''),
  );

  $form['cassiopeia_config_payment_suport']['payment_email_suport'] = array(
    '#type' => 'textfield',
    '#title' => 'Email hỗ trợ',
    '#default_value' => variable_get('payment_email_suport',''),
  );

  return system_settings_form($form);
}

function cassiopeia_payment_complete() {
  global $user;
  $_message = '';
  if (!empty($_GET['token'])) {
    $_data = cassiopeia_nl_check_link($_GET['token']);
    if (!empty($_data) && ($_data->error_code == 0)) {
      $transaction = db_transaction();
      try {
        $check_result = db_select('credicard_transfers', 'credicard_transfers')
          ->fields('credicard_transfers')
          ->condition('order_code', $_data->order_code,'=')
          ->execute()
          ->fetchAssoc();

        if(empty($check_result)) {
          $_transaction_id = db_insert('transactions')->fields(array(
            'uid' => $user->uid,
            'total_amount' => $_data->total_amount,
            'type' => TRANSACTION_BUY_COURSE,
            'payment_type' => TRANSACTION_PAYMENT_CREDIT,
            'created' => REQUEST_TIME
          ))->execute();

          $user_buy_course_values = array();
          foreach ($_SESSION['cart'] as $_key => $_value) {
            $__value = array();
            $__value['tid'] = $_transaction_id;
            $__value['nid'] = $_value->nid;
            $__value['amount'] = $_value->field_ctype_course_price['und'][0]['value'];
            if (!empty($_value->field_ctype_course_sale_off['und'][0]['value'])) {
              $__value['amount'] = $__value['amount'] - $_value->field_ctype_course_sale_off['und'][0]['value'];
            }
            $user_buy_course_values[] = $__value;
          }

          $_user_buy_course_insert_query = db_insert('user_buy_course')->fields(array(
            'tid',
            'nid',
            'amount'
          ));

          foreach ($user_buy_course_values as $record) {
            $_user_buy_course_insert_query->values($record);
          }
          $_user_buy_course_insert_query->execute();



          $_credicard_transfer_id = db_insert('credicard_transfers')
            ->fields(array(
              'tid' => $_transaction_id,
              'order_code' => $_data->order_code,
              'payment_method' => $_data->payment_method,
              'bank_code' => $_data->bank_code,
              'payment_type' => $_data->payment_type,
            ))
            ->execute();

          $user_course_values = array();
          foreach ($_SESSION['cart'] as $_key => $_value) {
            $__value = array();
            $__value['tid'] = $_transaction_id;
            $__value['uid'] = $user->uid;
            $__value['nid'] = $_value->nid;
            $__value['locked'] = 0;
            $user_course_values[] = $__value;
          }

          $_user_course_insert_query = db_insert('user_courses')->fields(array(
            'tid',
            'uid',
            'nid',
            'locked'
          ));

          foreach ($user_course_values as $record) {
            $_user_course_insert_query->values($record);
          }
          $_user_course_insert_query->execute();

          $_SESSION['cart'] = array();
          unset($_SESSION['cart']);

          drupal_goto('user/'.$user->uid.'/courses');
        }

      } catch (Exception $e) {
        $transaction->rollback();
      }
    }
  }

  return $_message;
}

function cassiopeia_payment_class_complete() {
  global $user;
  $_message = '';
  if (!empty($_GET['token'])) {
    $_data = cassiopeia_nl_check_link($_GET['token']);
    if (!empty($_data) && ($_data->error_code == 0)) {
      $transaction = db_transaction();
      try {
        $check_result = db_select('credicard_transfers', 'credicard_transfers')
          ->fields('credicard_transfers')
          ->condition('order_code', $_data->order_code,'=')
          ->execute()
          ->fetchAssoc();

        if(empty($check_result)) {
          $_transaction_id = db_insert('transactions')->fields(array(
            'uid' => $user->uid,
            'total_amount' => $_data->total_amount,
            'type' => TRANSACTION_BUY_CLASS,
            'payment_type' => TRANSACTION_PAYMENT_CREDIT,
            'created' => REQUEST_TIME
          ))->execute();

          $user_buy_course_values = array();
          foreach ($_SESSION['cart_class'] as $_key => $_value) {
            $__value = array();
            $__value['tid'] = $_transaction_id;
            $__value['nid'] = $_value->nid;
            $__value['amount'] = $_value->field_ctype_course_price['und'][0]['value'];
            if (!empty($_value->field_ctype_course_sale_off['und'][0]['value'])) {
              $__value['amount'] = $__value['amount'] - $_value->field_ctype_course_sale_off['und'][0]['value'];
            }
            $user_buy_course_values[] = $__value;
          }

          $_user_buy_course_insert_query = db_insert('user_buy_class')->fields(array(
            'tid',
            'nid',
            'amount'
          ));


          foreach ($user_buy_course_values as $record) {
            $_user_buy_course_insert_query->values($record);
          }
          $_user_buy_course_insert_query->execute();

          $_credicard_transfer_id = db_insert('credicard_transfers')
            ->fields(array(
              'tid' => $_transaction_id,
              'order_code' => $_data->order_code,
              'payment_method' => $_data->payment_method,
              'bank_code' => $_data->bank_code,
              'payment_type' => $_data->payment_type,
            ))
            ->execute();



          foreach ($_SESSION['cart_class'] as $_key => $_value) {
            $check_in_class = FALSE;
            $_class = node_load($_value->nid);
            if(empty($_class->field_ctype_class_students['und'])) {
              $_class->field_ctype_class_students['und'][] = array('target_id'=> $user->uid);
              node_save($_class);
            }else {
              foreach ($_class->field_ctype_class_students['und'] as $key => $value) {
                if($value['target_id'] == $_user->uid) {
                  $check_in_class = TRUE;
                }
              }
            }
            if($check_in_class == FALSE) {
              $_class->field_ctype_class_students['und'][] = array('target_id'=> $user->uid);
              node_save($_class);
            }
          }

          $_SESSION['cart_class'] = array();
          unset($_SESSION['cart_class']);

          drupal_goto('user/'.$user->uid.'/class');
        }

      } catch (Exception $e) {
        $transaction->rollback();
      }
    }
  }

  return $_message;
}