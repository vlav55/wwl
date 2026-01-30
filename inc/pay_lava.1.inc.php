<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "init.inc.php";
$db=new db($database);

//$db->print_r($_POST); exit;

if(isset($_POST['go_submit'])) {
	$pay_system="lava";
	include_once "/var/www/vlav/data/www/wwl/inc/lava.class.php";
	include_once "/var/www/vlav/data/www/wwl/inc/pay_common.1.inc.php";
	$apiKey=$db->dlookup("lava_api_key","pay_systems","1"); //"29b5f5e973b9";
//$apiKey = 'NQYv5NebjrkwdfbYzQteK6IXWpocaKIUYVcFg818fLRwJPsgpmViKtheKnKefDVk';
	$lava = new lava($apiKey);
	//~ $products = $lava->get_products();
	//~ $db->print_r($products);
	//print "$email $order_id";
	if($r=$lava->get_invoice($email, 'd4726459-1217-4155-a40e-36ba294ffbdf')) {
		$db->query("UPDATE avangard SET order_id='".$db->escape($r['id'])."',order_number='".$db->escape($r['id'])."' WHERE order_id='$order_id' AND res=0",1);
		//print "Ok"; exit;
		$link=$r['paymentUrl'];
		header("Location: $link");
		echo $link;
	} else {
		print $lava->err;
		print "<br><a href='javascript:history.back()' class='' target=''>вернуться</a>";
		exit;
	}
}
?>
