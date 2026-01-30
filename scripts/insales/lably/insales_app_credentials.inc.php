<?
$id_app="winwinland_lably";
$secret_key='5e8b5bf8cc54dd879cebded00b46775d';

if(!$insales_id=intval($_GET['insales_id']))
	$insales_id=5790531; //test wwl
	
$ctrl_id = $db->dlookup("id","0ctrl","insales_shop_id='$insales_id'");
$token = $db->dlookup("insales_token", "0ctrl", "id='$ctrl_id'");
$shop = $db->dlookup("insales_shop", "0ctrl", "id='$ctrl_id'");
$passw = md5($token . $secret_key);
$credentials = base64_encode("$id_app:$passw");
?>
