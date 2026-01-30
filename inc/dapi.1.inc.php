<?
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	exit;
}
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "init.inc.php";

//if(in_array($ctrl_id,[198,199]))
file_put_contents("dapi.log","\n".date("d.m.Y H:i:s")."\n".print_r($_POST,true),FILE_APPEND);

if(isset($_POST['save_bank_details'])) {
	if($_POST['secret']!==md5($_POST['uid'].$db->get_api_secret($ctrl_id))) {
		$dapi_msg= "err: secret is not match\n";
		print $dapi_msg;
		file_put_contents("dapi.log",$dapi_msg,FILE_APPEND);
		exit;
	}
	$uid=$db->get_uid($_POST['uid']);
	//$db->notify_me("$uid {$_POST['uid']}");
	$bank_details=mb_substr($_POST['bank_details'],0,2048);
	if($klid=$db->dlookup("id","cards","uid='$uid'")) {
		$db->query("UPDATE users SET bank_details='".$db->escape($bank_details)."' WHERE klid='$klid'");
		print "ok ".$db->uid_md5($uid);
	} else
		print "err: partner not found";
	exit;
}
if(isset($_POST['check_login'])) {
	if($_POST['secret']!==md5($_POST['direct_code'].$_POST['login'].$_POST['passw'].$db->get_api_secret($ctrl_id))) {
		$dapi_msg= "err: secret is not match\n";
		print $dapi_msg;
		file_put_contents("dapi.log",$dapi_msg,FILE_APPEND);
		exit;
	}
	$direct_code=(isset($_POST['direct_code']) && !empty($_POST['direct_code'])) ?substr($_POST['direct_code'],0,32) : "";
	$login=(isset($_POST['login']) && !empty($_POST['login'])) ?substr($_POST['login'],0,32) : "";
	$passw=(isset($_POST['passw']) && !empty($_POST['passw'])) ?substr($_POST['passw'],0,32) : "";
	if(!empty($direct_code)) {
		if($klid=$db->dlookup("klid","users","del=0 AND direct_code='$direct_code'")) {
			$uid=$db->dlookup("uid_md5","cards","id='$klid'");
			$access_level=$db->dlookup("access_level","users","klid='$klid'");
			print(json_encode(['uid'=>$uid,'access_level'=>$access_level]));
		} else
			print (json_encode(['error'=>'direct_code not match']));
	} elseif(!empty($login) && !empty($passw)) {
		$md5=md5($passw);
		if($klid=$db->dlookup("klid","users","del=0 AND username='$login' AND passw='$md5' AND fl_allowlogin=1")) {
			$uid=$db->dlookup("uid_md5","cards","id='$klid'");
			$access_level=$db->dlookup("access_level","users","klid='$klid'");
			print(json_encode(['uid'=>$uid,'access_level'=>$access_level]));
		} else
			print (json_encode(['error'=>'login or password not match']));
	} else
		print json_encode(['error'=>'input data incorrect']);
	exit;
}

if($_POST['secret']!=md5($_POST['land_num'].$_POST['client_name'].$_POST['client_phone'].$db->get_api_secret($ctrl_id))) {
	$dapi_msg= "err: secret is not match\n";
	print $dapi_msg;
	file_put_contents("dapi.log",$dapi_msg,FILE_APPEND);
	exit;
}

if(isset($_POST['get_info'])) {
	include "/var/www/vlav/data/www/wwl/inc/dapi_get_info.inc.php";
	exit;
}

if(empty(trim($_POST['client_name']))) {
	$dapi_msg= "err: client_name is empty\n";
	print $dapi_msg;
	file_put_contents("dapi.log",$dapi_msg,FILE_APPEND);
	exit;
}
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

include_once "/var/www/vlav/data/www/wwl/inc/thanks.1.inc.php";

if($tg_id) {
	$db->query("UPDATE cards SET telegram_id='$tg_id' WHERE uid='$uid'");
}
if(!empty($tg_nic)) {
	$db->query("UPDATE cards SET telegram_nic='".$db->escape($tg_nic)."' WHERE uid='$uid'");
}
if($partner_klid) {
	if($make_partner_code) {
		if(!$db->dlookup("klid","users","del=0 AND bc='$make_partner_code'"))
			$db->query("UPDATE users SET bc='".$vkt->escape($make_partner_code)."' WHERE klid='$partner_klid'");
	}
	if($tg_id) {
		$db->query("UPDATE users SET telegram_id='$tg_id' WHERE klid='$partner_klid'");
	}
}

$dapi_msg="ok ". $db->uid_md5($uid)."\n";
print $dapi_msg;
file_put_contents("dapi.log",$dapi_msg,FILE_APPEND);
exit;


$db=new db($database);
		$r=[
			'tm'=>0, //for new uid - tm=time() if 0
			'uid'=>0, //если не найдет в базе то выход с ошибкой
			'first_name'=>'Вася',
			'last_name'=>'Иванов',
			'phone'=>'+7-000-9999999',
			'email'=>'123456789@mail.ru',
			'city'=>'СПб',
			'tg_id'=>'123456789', //if not 0 will be added
			'tg_nic'=>'qwerty', //if not empty will be added
			'vk_id'=>'123456789', //if not 0 will be added
			'razdel'=>'3', //in not 2  will added
			'source_id'=>'1', //0
			'user_id'=>'0',
			'klid'=>'0',
			'wa_allowed'=>'0',
			'comm1'=>'12345',
			'tz_offset'=>'3',
			'test_cyrillic'=>false
		];
$uid=$db->cards_add($r,$update_if_exist=false);

$land_num=1;
if(isset($_POST['land_num']))
	$land_num=intval($_POST['land_num']);

$land_name=$db->dlookup("land_name","lands","land_num='$land_num' AND del=0");
$tm_scdl=$db->dlookup("tm_scdl","lands","land_num='$land_num' AND del=0");
$land_razdel=$db->dlookup("land_razdel","lands","land_num='$land_num' AND del=0");
$land_tag=$db->dlookup("land_tag","lands","land_num='$land_num' AND del=0");

if($tm_scdl) {
	$db->query("UPDATE cards SET tm_schedule='$tm_scdl',scdl_web_id='$land_num' WHERE uid='$uid'");
}
if($land_razdel) {
	$db->query("UPDATE cards SET razdel='$land_razdel' WHERE uid='$uid'");
}
if($land_tag) {
	if(!$db->dlookup("id","tags_op","tag_id='$land_tag' AND uid='$uid'"))
		$db->query("INSERT INTO tags_op SET tag_id='$land_tag',uid='$uid',tm='".time()."'");
}
$db->save_comm($uid,0,$land_name,1000+$land_num);
$db->notify($uid,"⭐ $land_name: api");


exit;
?>
