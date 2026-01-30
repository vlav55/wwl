<?
include_once "/var/www/vlav/data/www/wwl/inc/pay_alfa_callback.1.inc.php";
exit;
?>

<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/justclick_api.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/sendpulse.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/unisender.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/senler_api.class.php";

include "init.inc.php";
//include_once "../../prices.inc.php";
$db=new db($database); 

$secret_seed = $db->dlookup("alfa_secret","pay_systems","1"); //"xawP628r}F(BWPr";
$id = $_POST['id'];
$sum = $_POST['sum'];
$clientid = $_POST['clientid'];
$orderid = $_POST['orderid'];
$key = $_POST['key'];
 
if ($key != md5 ($id.number_format($sum, 2, ".", "")
 .$clientid.$orderid.$secret_seed))
{
	echo "Error! Hash mismatch";
	exit;
}

$result = []; // готовим массив для передачи в pay_callback.php
$result['payment_status']	=		'success';
$result['order_id']			=		$_POST['orderid'];
$result['order_num']		=		$_POST['orderid'];
$result['payment_system']	=		'paykeeper';
$result['commission_sum']	=	0;

if($result['payment_status']=='success') {
	$order_id=$result['order_num'];
	$commission_sum=isset($result['commission_sum'])?$result['commission_sum']:0;

	include "/var/www/vlav/data/www/wwl/inc/pay_callback_common.1.inc.php";

}
die("OK ".md5($id.$secret_seed));

?>
