<?
include "../../api.class.php";
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

	$klid=$db->dlookup("id","cards","del=0 AND uid='$uid'");
	if(!$user_id=$db->dlookup("id","users","klid='$klid'")) {
		print json_encode(['error'=>"$uid is not a partner"]);
		http_response_code(400);
		exit;
	}
	require_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
	$p=new partnerka($klid,$database);
	$p->mute=true;
	$ops=$p->fill_op($klid,0,time(),$ctrl_id);
	$rest_fee=$p->rest_fee($klid);

	http_response_code(200);
	print json_encode(['uid'=>$db->uid_md5($uid),'rest_fee'=>$rest_fee, 'count'=>sizeof($ops)]);
}
if($_SERVER['REQUEST_METHOD']==='POST') {
	$uid=(isset($_POST['client_uid'])) ? $db->dlookup("uid","cards","del=0 AND uid='".$db->get_uid($_POST['client_uid'])."'") : 0;

	if(!$uid) {
		print json_encode(['error'=>'not found']);
		http_response_code(400);
		exit;
	}

	$klid=$db->dlookup("id","cards","del=0 AND uid='$uid'");
	if(!$user_id=$db->dlookup("id","users","klid='$klid'")) {
		print json_encode(['error'=>"$uid is not a partner"]);
		http_response_code(400);
		exit;
	}

	$sum=(isset($_POST['sum'])) ? intval($_POST['sum']) : 0;
	if(!$sum) {
		print json_encode(['error'=>'sum missing or zero']);
		http_response_code(400);
		exit;
	}
	
	$vid=(isset($_POST['vid'])) ? intval($_POST['vid']) : 0;
	$comm=isset($_POST['comm']) ? $_POST['comm'] : "";
	
	require_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
	$p=new partnerka($klid,$database);
	if(!$p->pay_fee($klid,$sum,$vid,$comm)) {
		print json_encode(['error'=>'fee_pay error']);
		http_response_code(400);
		exit;
	}
	$rest_fee=$p->rest_fee($klid);

	http_response_code(200);
	print json_encode(['uid'=>$db->uid_md5($uid),'rest_fee'=>$rest_fee, 'sum'=>$sum]);
}
?>
