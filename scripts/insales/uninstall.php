<?
//~ Array
//~ (
    //~ [shop] => myshop-cpc885.myinsales.ru
    //~ [token] => e0ba59ec2ed785b2152c1fbfa3b48a9c
    //~ [insales_id] => 5790531
//~ )
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt('vkt');
include "insales_app_credentials.inc.php";
include "insales_func.inc.php";

$insales_id=(isset($_GET['insales_id'])) ? intval($_GET['insales_id']) : false;
if($insales_id) {
	$shop=$_GET['shop'];
	$token=$_GET['token'];
	$passw=md5($token.$secret_key);
	$credentials = base64_encode("$id_app:$passw");
	$db->notify_me("INSALES app uninstalled $insales_id $shop<br>");
	$res=insales_webhook_del(insales_get_webhook($insales_id));
	if(!isset($res['error']))
		$db->notify_me("INSALES app UNinstalled in $insales_id");
	else
		$db->notify_me("INSALES error insales_webhook_del");
} else
	$db->notify_me("INSALES error uninstall.php insales_id=false");
http_response_code(200);

?>
