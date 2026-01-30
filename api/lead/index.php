<?
include "../api.class.php";
if($_SERVER['REQUEST_METHOD']==='GET') {
	$uid=(isset($_GET['client_uid'])) ? $db->dlookup("uid","cards","del=0 AND uid='".$db->get_uid($_GET['client_uid'])."'") : 0;
	$uid=(!$uid && isset($_GET['client_phone'])) ? $db->dlookup("uid","cards","del=0 AND mob_search='".$db->check_mob($_GET['client_phone'])."'") : $uid;
	$uid=(!$uid && isset($_GET['client_email'])) ? $db->dlookup("uid","cards","del=0 AND email='".$db->escape(trim($_GET['client_email']))."'") : $uid;
	$uid=(!$uid && isset($_GET['client_tg'])) ? $db->dlookup("uid","cards","del=0 AND telegram_id='".intval($_GET['client_tg'])."'") : $uid;
	$uid=(!$uid && isset($_GET['client_vk'])) ? $db->dlookup("uid","cards","del=0 AND vk_id='".intval($_GET['client_vk'])."'") : $uid;

	if(!$uid) {
		print json_encode(['error'=>'not found']);
		http_response_code(400);
		exit;
	}

	//print "uid=$uid"; exit;

	if(isset($_GET['order'])) {
		$order_num= mb_substr($_GET['order'],0,32);
		$db->vkt_send_msg_order_id=$db->dlookup("order_id","avangard","order_number='".$db->escape($order_num)."'");
	} else
		$db->vkt_send_msg_order_id=false;
		
	$db->ctrl_id=$ctrl_id;
	$arr=$db->get_webhook_data($uid,$action);
	http_response_code(200);
	print json_encode($arr);
	exit;
}
if($_SERVER['REQUEST_METHOD']==='POST') {
	if(!isset($_POST['client_name']) || empty(trim($_POST['client_name']))) {
		print json_encode(['error'=>'client_name is empty']);
		http_response_code(400);
		exit;
	}
	if(!isset($_POST['land_num']) || empty(trim($_POST['land_num']))) {
		print json_encode(['error'=>'land_num is empty or incorrect']);
		http_response_code(400);
		exit;
	}

	$last_name=(isset($_POST['last_name']) && !empty(trim($_POST['last_name']))) ? mb_substr($_POST['last_name'],0,32) : "";

	if(isset($_POST['city']))
		$_POST['regCity']=trim($_POST['city']);
	if(isset($_POST['comm']))
		$_POST['regComm']=trim($_POST['comm']);

	$tg_id=(isset($_POST['telegram_id'])) ? intval($_POST['telegram_id']) : 0;
	$tg_nic=(isset($_POST['telegram_nic'])) ? mb_substr(trim($_POST['telegram_nic']),0,32) : 0;
	$vk_id=(isset($_POST['vk_id'])) ? intval($_POST['vk_id']) : 0;
	$test_cyr=(isset($_POST['test_cyr'])) ? intval($_POST['test_cyr']) : 0;

	if(isset($_POST['make_partner']))
		$make_partner= intval($_POST['make_partner']);
	$make_partner_code=(isset($_POST['make_partner_code'])) ? mb_substr($_POST['make_partner_code'],0,32) : false;

	$_POST['secret']=true; //need for thanks.1.inc.php
	include_once "/var/www/vlav/data/www/wwl/inc/thanks.1.inc.php";

	$r=$db->fetch_assoc($db->query("SELECT * FROM cards WHERE del=0 AND uid='$uid'"));

	if($tg_id && !$r['telegram_id']) {
		$db->query("UPDATE cards SET telegram_id='$tg_id' WHERE uid='$uid'");
	}
	if(!empty($tg_nic) && empty($r['telegram_nic'])) {
		$db->query("UPDATE cards SET telegram_nic='".$db->escape($tg_nic)."' WHERE uid='$uid'");
	}
	if($vk_id && !$r['vk_id']) {
		$db->query("UPDATE cards SET vk_id='$vk_id' WHERE uid='$uid'");
	}
	if(!empty($last_name && empty($r['last_name']))) {
		$db->query("UPDATE cards SET surname='".$db->escape($last_name)."' WHERE uid='$uid'");
	}
	if($partner_klid) { //come from thanks.1.inc.php
		if($make_partner_code) {
			if(!$db->dlookup("klid","users","del=0 AND bc='$make_partner_code'"))
				$db->query("UPDATE users SET bc='".$vkt->escape($make_partner_code)."' WHERE klid='$partner_klid'");
		}
		if($tg_id) {
			$db->query("UPDATE users SET telegram_id='$tg_id' WHERE klid='$partner_klid'");
		}
	}

	//$dapi_msg="ok ". $db->uid_md5($uid)."\n";
	http_response_code(200);
	print json_encode(['uid'=>$db->uid_md5($uid)]);
}
?>
