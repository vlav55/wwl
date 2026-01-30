<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db("vkt");
$db->telegram_bot="vkt";
$db->db200="";
$db->pact_token="yogahelpyou";

include "/var/www/vlav/data/www/wwl/inc/top_global.inc.php";
if(!isset($_SESSION['admin']))
	$_SESSION['admin']=false;

include_once "/var/www/vlav/data/www/wwl/prices.inc.php";

$bc=false;$utm_term=false;
$test_bc=array();
if(isset($_GET['bc'])) {
	if($bc=intval($_GET['bc'])) {
		if($klid=$db->dlookup("klid","users","bc='$bc'")) {
			if($user_id=$db->dlookup("id","users","klid='$klid'")) {
				$uid=false;
			}
		}
	}
}

if($uid) {
	$user_id=$db->dlookup("user_id","cards","uid='$uid'");
	$klid=$db->dlookup("klid","users","id='$user_id'");
	$uid_md5=$db->uid_md5($uid);
	$client_name=$db->dlookup("name","cards","uid='$uid'");
	$client_email=$db->dlookup("email","cards","uid='$uid'");
	$client_phone=$db->dlookup("mob_search","cards","uid='$uid'");
}

if($klid) {
	$pact_phone=$db->dlookup("pact_phone","users","klid='$klid'");
} else {
	$user_id=0;
}

$utm_source=isset($_GET['utm_source'])?$_GET['utm_source']:"";
$utm_medium=isset($_GET['utm_medium'])?$_GET['utm_medium']:"";
$utm_content=isset($_GET['utm_content'])?$_GET['utm_content']:"";
$utm_campaign=isset($_GET['utm_campaign'])?$_GET['utm_campaign']:"";
$utm_term=isset($_GET['utm_term'])?$_GET['utm_term']:"";
$utm_ab=isset($_GET['utm_ab'])?$_GET['utm_ab']:"";
$user_agent=isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"";
$ip=isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:"";
$referer=isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:"";

$code=random_int(0,2147483647);
while($db->dlookup("id","index_log","code='$code'") )
	$code=random_int(0,2147483647);


?>
