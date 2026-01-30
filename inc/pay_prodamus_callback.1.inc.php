<?
$headers = apache_request_headers();
$P=$_POST;
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/justclick_api.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/sendpulse.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/unisender.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/senler_api.class.php";

include "init.inc.php";
//include_once "../../prices.inc.php";
$db=new db($database); 

require_once '/var/www/vlav/data/www/wwl/inc/prodamus_hmac.inc.php';
$secret_key =  $db->dlookup("prodamus_secret","pay_systems","1");
$_POST=$P;


function log1($msg) {
	file_put_contents("avangard_last_pay.log", date("d.m.Y H:i:s")."--->".$msg."\n",FILE_APPEND); //FILE_APPEND
}

log1("\nSTART");
log1(__LINE__);

try { 
	file_put_contents("prodamus_last_webhook.txt",print_r($_POST,true));
	if ( empty($_POST) ) {
		print "HERE_1"; 
log1(__LINE__."HERE_1");
		throw new Exception('$_POST is empty');
	}
	elseif ( empty($headers['Sign']) ) {
		print "HERE_2";
log1(__LINE__."HERE_2");
		throw new Exception('signature not found');
	}
	elseif ( !Hmac::verify($_POST, $secret_key, $headers['Sign']) ) {
		print "HERE_3 $database $secret_key ".$headers['Sign'];
log1(__LINE__."HERE_3 $database $secret_key ".$headers['Sign']);
		throw new Exception('signature incorrect');
	}
	http_response_code(200);
	echo 'success';
	if($_POST['payment_status']=='success') {
log1(__LINE__);
		$prodamus_id=$_POST['order_id'];
		$order_id=$_POST['order_num'];
		$commission_sum=isset($_POST['commission_sum'])?$_POST['commission_sum']:0;

		include "/var/www/vlav/data/www/wwl/inc/pay_callback_common.1.inc.php";

	} elseif($_POST['payment_status']=='order_denied' || $_POST['payment_status']=='order_canceled') {
log1(__LINE__." payment denied");
		$uid=intval($_POST['vk_user_id']);
		if($uid) {
			$product_id=$_POST['customer_extra'];
			$descr=$base_prices[$product_id]["descr"];
			$payment_status_description=$_POST['payment_status_description'];
			$sum=intval($_POST['sum']);
			$db->notify($uid,"ОТКАЗ В РАССРОЧКЕ: $sum р., $descr \n$payment_status_description");
			$db->mark_new($uid,3);
		}
	}
}
catch (Exception $e) {
	http_response_code($e->getCode() ? $e->getCode() : 400);
	$err=sprintf('error: %s', $e->getMessage());
	file_put_contents("last_webhook_error.txt",$err);
log1(__LINE__." error");
}
?>
