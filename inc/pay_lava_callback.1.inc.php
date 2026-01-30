<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/justclick_api.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/sendpulse.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/unisender.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/senler_api.class.php";

include "init.inc.php";
$db=new db($database); 

$secret_seed = $db->dlookup("lava_api_key","pay_systems","1");
$jsonData = file_get_contents('php://input');
if($jsonData) {
	// Decode the JSON data into a PHP associative array
	$r = json_decode($jsonData, true);
	file_put_contents("pay_lava_callback.log",print_r($r,true),FILE_APPEND);
	if($r['eventType']=='payment.success' && $r['status']=='completed') {
		$order_id=$r['contractId'];
		$commission_sum=0;
		include "/var/www/vlav/data/www/wwl/inc/pay_callback_common.1.inc.php";
	}
}
die("OK");
?>
