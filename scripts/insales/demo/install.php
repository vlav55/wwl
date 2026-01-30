<?
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt('vkt');

//    [shop] => myshop-cpc885.myinsales.ru
//    [token] => 6687a0fe25ab2d7ec927795c0e9dd0ff
//    [insales_id] => 5790531


include "insales_app_credentials.inc.php";
include "../insales_func.inc.php";

if(isset($_GET['insales_id'])) {
	$insales_id=intval($_GET['insales_id']);
	if($ctrl_id=$db->dlookup("id","0ctrl","insales_shop_id='$insales_id'")) {
		$shop=$_GET['shop'];
		$token=$_GET['token'];
		$db->query("UPDATE 0ctrl SET
			insales_shop='".$db->escape($shop)."',
			insales_token='".$db->escape($token)."'
			WHERE id='$ctrl_id'
			",0);
		$passw=md5($token.$secret_key);
		$credentials = base64_encode("$id_app:$passw");
		$ctrl_dir=$db->get_ctrl_dir($ctrl_id);
		$url="https://for16.ru/d/$ctrl_dir/insales_webhook.php";
		$res=insales_webhook_create($url, $event='orders/update');
		if($res===true) {
			
			$db->notify_me("INSALES app install Ok. ctrl_id=$ctrl_id shop=$shop");
			http_response_code(200);
			
		} else {
			http_response_code(210);
		}
		exit;
	}
}
http_response_code(210);



?>
