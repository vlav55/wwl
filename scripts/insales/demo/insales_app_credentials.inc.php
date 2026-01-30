<?
$id_app="winwinland-demo";
$secret_key='0d1be667a35cf2f5a7253c04c12b86de';

if(!$insales_id=intval($_GET['insales_id']))
	$insales_id=5798588; //test wwl
	
$ctrl_id = $db->dlookup("id","0ctrl","insales_shop_id='$insales_id'");
$token = $db->dlookup("insales_token", "0ctrl", "id='$ctrl_id'");
$shop = $db->dlookup("insales_shop", "0ctrl", "id='$ctrl_id'");
$passw = md5($token . $secret_key);
$credentials = base64_encode("$id_app:$passw");
?>
