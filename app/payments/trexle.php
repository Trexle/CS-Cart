<?php

use Tygh\Http;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

// Switch between test and live API
if ($processor_data['processor_params']['test'] == 'Y') {
	$api = 'https://sandbox.trexle.com/api/v1/charges';
} else {
	$api = 'https://core.trexle.com/api/v1/charges';
}
$prefix = $processor_data['processor_params']['order_prefix'];
$secretkey = $processor_data['processor_params']['secret_key'];


// Build the data array
$data = array();
$data['email'] = $order_info['email'];
$data['description'] = $prefix.' order #'.$order_info['order_id'];
$data['amount'] = strval($order_info['total']*100);
$data['currency'] = 'USD';
$data['ip_address'] = $order_info['payment_info']['ip_address'];
$data['card_token'] = $order_info['payment_info']['card_token'];

Registry::set('log_cut_data', array('card_token', 'ip_address'));
// Need to authenticate to make an API call
$http_response =  Http::post($api, $data, 
    array( 'basic_auth' => array($secretkey, '')  )
);

$return = json_decode($http_response);

$success = $return->response->success;
$amount = $return->response->amount;
$transaction_id = $return->response->token;

if($success == "true" && fn_format_price($amount) == fn_format_price($order_info['total'] * 100)) {
	$pp_response['order_status'] = 'P';
  $message = $return->response->status_message;
	$pp_response["reason_text"] = $message;
  $pp_response['card_number'] = $return->response->card->display_number;
  $pp_response['card_name'] = $return->response->card->name;
  $pp_response['expiry_month'] = $return->response->card->expiry_month;
  $pp_response['expiry_year'] = $return->response->card->expiry_year;

} else {
	$pp_response['order_status'] = 'F';
    $error = $return->messages[0]->message;
	if (!empty($error)) {
		$pp_response["reason_text"] = "Error: " .$error;
	}
}

if (!empty($transaction_id)) {
	$pp_response["transaction_id"] = $transaction_id;
}

?>