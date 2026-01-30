<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/justclick_api.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/sendpulse.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/unisender.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/senler_api.class.php";

include "init.inc.php";
//include_once "../../prices.inc.php";
$db=new db($database); 

$source = file_get_contents('php://input');
$requestBody = json_decode($source, true);
file_put_contents("pay_yookassa_callback.txt",$requestBody);

if(
	(isset($requestBody['object']) && isset($requestBody['type']) && $requestBody['type']=='notification')
	// || $is_test
) {

	//~ $netq = array(
		//~ "185.71.76.0/27",
		//~ "185.71.77.0/27",
		//~ "77.75.153.0/25",
		//~ "77.75.154.128/25",
		//~ "206.189.205.251/24",
	//~ );
	//~ $white_adresses = array("77.75.156.11", "77.75.156.35");
	//~ foreach($netq as $cidr) $white_adresses = array_merge($white_adresses, get_list_ip($cidr) );
	//~ if(!in_array($_SERVER['REMOTE_ADDR'], $white_adresses)) { 
		//~ //send_tg("<b>Incorrect IP</b>: " . var_export($requestBody,1) . "\r\n" . $_SERVER['REMOTE_ADDR'] );
		//~ http_response_code(403);
		//~ die($_SERVER['REMOTE_ADDR'] . " blocked");
	//~ }

	$payment = $requestBody['object'];
	$amount			= $payment['amount']['value'];
	$income_amount 	= $payment['income_amount']['value'];

	if(!isset($payment['metadata']['order_id'])) {
		//send_tg("<b>Перевод без order_id</b>\r\n\r\n<code>".var_export($requestBody, 1)."</code>");
		die;
	}

	if($payment['test']) {
		//send_tg("Тестовый платеж, зачислен не будет!");
		die;
	}

	if($requestBody['event'] != "payment.succeeded") {
		//send_tg("⚠️⚠️⚠️ Статус платежа != succeeded, пропускаем!\r\n\r\n".var_export($requestBody,1));
		die;
	} else {
		$order_id=$payment['metadata']['order_id'];
		$commission_sum= intval($amount - $income_amount);

		include "/var/www/vlav/data/www/wwl/inc/pay_callback_common.1.inc.php";

	}

	die("OK");
	//send_tg("<b>New transaction</b>: $txn_id\r\nOrder: {$payment['metadata']['order_id']}\r\nSum: $amount\r\n income_amount: $income_amount\r\nСоздана: ".date("d.m.y G:i:s", strtotime($payment['created_at']))."\r\nОплачена: ".date("d.m.y G:i:s", strtotime($payment['captured_at'])));

}
?>
