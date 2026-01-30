<?
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt('vkt');

//    [shop] => myshop-cpc885.myinsales.ru
//    [token] => 6687a0fe25ab2d7ec927795c0e9dd0ff
//    [insales_id] => 5790531


include "insales_app_credentials.inc.php";
include "insales_func.inc.php";

if(isset($_GET['insales_id'])) {
	$insales_id=intval($_GET['insales_id']);
	if($ctrl_id=$db->dlookup("id","0ctrl","del=0 AND insales_shop_id='$insales_id'",0)) {
		$shop=$_GET['shop'];
		$token=$_GET['token'];
		$passw=md5($token.$secret_key);
		$credentials = base64_encode("$id_app:$passw");
		$ctrl_dir=$db->get_ctrl_dir($ctrl_id);
		$url="https://for16.ru/d/$ctrl_dir/insales_webhook.php";
		$res=insales_webhook_create($url, $event='orders/update');
		if($res===true) {
			$res=insales_webhook_create($url, $event='orders/create');
			$db->query("UPDATE 0ctrl SET
				insales_shop='".$db->escape($shop)."',
				insales_token='".$db->escape($token)."'
				WHERE id='$ctrl_id'
				");
			$db->notify_me("INSALES app install Ok. ctrl_id=$ctrl_id shop=$shop");
			http_response_code(200);
			
		} else {
			$db->notify_me("INSALES app install ERROR. ctrl_id=$ctrl_id shop=$shop");
			http_response_code(210);
		}
		exit;
	} else {
		if(file_put_contents("$insales_id.token",$_GET['token'])) {
			$db->notify_chat(-4799845674,"INSALES APP INSTALLED id=$id_app : ctrl_id not exists for: \n".print_r($_GET,true) );
		} else
			$db->notify_me("INSALES APP INSTALL ERROR : $insales_id.token save error \n".print_r($_GET,true) );
		http_response_code(200);
		exit;
	}
}
http_response_code(210);
?>
