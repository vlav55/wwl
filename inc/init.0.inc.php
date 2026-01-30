<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
$db=new db("vktrade");
$d="./customer.id";
if(!file_exists($d))
	$d="./db/customer.id";
$db->test_microtime(__LINE__." init0");
$customer_id=file_get_contents($d);
if(!$customer_id) {
	print "<div class='alert alert-danger' >Error customer_id= customer.id is not found!</div>"; exit;
}
$customer_id=intval($customer_id);
if($customer_id==0 || !is_numeric($customer_id)) {
	print "<div class='alert alert-danger' >customer_id=$customer_id is not numeric or 0!</div>"; exit;
}
$db->test_microtime(__LINE__." init0");

$db->test_microtime(__LINE__." init0");

//~ $r=$db->fetch_assoc($db->query("SELECT *,customers.id AS id, c_persons.uid AS uid, customers.del AS del
						//~ FROM customers 
						//~ LEFT JOIN c_persons ON customers.id=c_persons.cid 
						//~ WHERE customers.id='$customer_id' AND fl_contact=1 AND c_persons.del=0"));
//print "HERE_$customer_id \n";

$r=$db->fetch_assoc($db->query("SELECT * FROM customers WHERE customers.id='$customer_id'"));
if(!$r) {
	print "<div class='alert alert-danger' >Error getting from db.vktrade - customer_id=$customer_id is not found!</div>"; exit;
}
if($r['del']!=0) {
	//print "<div>Аккаунт отключен.</div>";
	header("Location: https://vk.com/vktradecrm");
	exit;
}

$dir=$r['dir'];
if(!file_exists("$dir")) {
	print "<div class='alert alert-danger' >Dir=$dir is not exists!</div>"; exit;
}
$database=$r['db'];
if(!$db->query("USE $database")) {
	print "<div class='alert alert-danger' >Database=$database selecting error!</div>"; exit;
}

$VK_GROUP_ID=$r['gid'];
if($VK_GROUP_ID==0 || !is_numeric($VK_GROUP_ID)){
	//print "<div class='alert alert-danger' >VK_GROUP_ID=$VK_GROUP_ID is not numeric or 0!</div>"; exit;
}
$VK_OWN_UID=$r['uid']; //
if(!$VK_OWN_UID || !is_numeric($VK_OWN_UID)) {
	//print "<div class='alert alert-danger' >Error. VK_OWN_UID=$VK_OWN_UID is not numeric or 0! See fl_contact in c_persons</div>"; exit;
}
$VK_GROUP_NAME_TITLE=$r['group_name'];
if(trim($VK_GROUP_NAME_TITLE)=="") {
	//print "<div class='alert alert-danger' >VK_GROUP_NAME_TITLE=$VK_GROUP_NAME_TITLE is empty!</div>"; exit;
}
$VK_GROUP_NAME=$r['group_domain'];
if(trim($VK_GROUP_NAME)=="") {
	//print "<div class='alert alert-danger' >VK_GROUP_NAME=$VK_GROUP_NAME is empty!</div>"; exit;
}

$city_id=$r['city_id'];
if($city_id==0 || !is_numeric($city_id)) {
	print "<div class='alert alert-danger' >city_id=$city_id is not numeric or 0!</div>"; exit;
}


$vk=new vklist_api;
//$vk::$proxy="tcp://18.218.163.13:3128";
//$vk::$proxy_passw="vlav:fokova^142586";

$vk::$proxy=trim($r['proxy']);
$vk::$proxy_passw=trim($r['proxy_passw']);

//print "here_".$vk::$proxy."\n";
//print "here_".$vk::$proxy_passw."\n";


$app_id=intval($r['app']);

$DO_NOT_TOUCH_FRIENDS=$VK_OWN_UID;//
$favicon=false;
$TELEGRAM_BOT="vktrade";
$DB200="https://1-info.ru/$dir/db";
$retarketing_target_group_id=false;

		$db->test_microtime(__LINE__." init0");


//msg.php
$msg_add_to_friends="Здравствуйте, вы заходили к нам в группу, можно задать вопрос?";
$save_images=false;
$send_talk_to_email=($r['chk_outg_msgs']==1)?array("vlav@mail.ru"):array();
$send_talk_to_email_from="$database@1-info.ru";
$send_talk_to_vk=array();

//print "here_".sizeof($send_talk_to_email); exit;
		$db->test_microtime(__LINE__." init0");


//vklist_msgs_scan.php
$razdel_do_not_notify=array();
$request_to_friends_as_default=false;

//vklist_scan_groups
$add_to_vklist_if_from_spb_only=false;
if($city_id!=-1) {
	$add_if_city_only=$city_id; 
	$add_if_city_or_country_not_specified=true; //
} else {
	$add_if_city_only=false;
	$add_if_city_or_country_not_specified=true; //
}
$add_if_country_only=false; //VK country_id RUSSIA=1

$add_if_sex_only=false; //'M' or 'F'
$scan_groups_mode=$r['grpadd_mode']; //0- add to vklist, 1- to cards
$VK_GROUP_ADDED=$r['grpadd_grp']; //id of group in vklist_groups
$delay_if_notif=7*24*60*60; //7x24x60x60 - update vklist for sending if last sent was before

//vklist_scan_votes
$scan_votes_mode=$r['vote_mode'];  //0-add new to vklist; 1 -add to cards
$razdel_exclude=array();

//vklist_send
$hour_of_start_sending=0;
$hour_of_end_sending=24;
$interval_min=3*60*60; //interval_in_hours_of_next_trying_after_errors
$sex_allowed=false; //false - any sex allowed; 1-female only; 2-male only; 0- not specified
$min_age_limit=10;
$max_age_limit=160;
$allow_if_in_cards=true;

//vk_landing
$landing_mode=$r['landing_mode'];
$landing_grp=$r['landing_grp'];


if(!isset($url)) {
	if(preg_match("|\/.*\/(.*)|",getcwd(),$m)) {
		$url="/".$m[1]; //need in index.php
	}
}

		$db->test_microtime(__LINE__." init0");
// $db->print_r($db->runtime_log);

?>
