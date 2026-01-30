<?php
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db("vkt");
$db->telegram_bot="vkt";
$db->db200="https://for16.ru/d/1000";
chdir("/var/www/vlav/data/www/wwl/d/1000/");
include "init.inc.php";

if(!isset($pwd_id))
	$pwd_id=0;

$bc=0;
if(isset($_GET['bc'])) {
	if($bc=$db->promocode_validate($_GET['bc']))
		$_SESSION['bc']=$bc;
}elseif(isset($_SESSION['bc']) ) {
	$bc=$db->promocode_validate($_SESSION['bc']);
}
$uid=0;$disp_contacts=false;

if(isset($_GET['uid'])) {
	if($uid=$db->get_uid($_GET['uid'])) {
		if($db->is_md5($_GET['uid']))
			$disp_contacts=true;
	}
	if($_GET['uid']==0) {
		unset($_SESSION['vk_uid']);
	}
}

if($uid)
	$_SESSION['vk_uid']=$uid;

if(isset($_SESSION['vk_uid'])) {
	$uid=intval($_SESSION['vk_uid']);
	if(empty($client_email)) {
		$r=$db->fetch_assoc($db->query("SELECT * FROM cards WHERE uid='$uid'"));
		if($r) {
			$client_phone=$r['mob']; $client_name=$r['name']; $client_email=$r['email'];
		}
	}
} else 
	$uid=0;

$uid_md5=($uid)?$db->uid_md5($uid):0;

if($uid) {
	$utm_campaign=isset($_GET['utm_campaign'])?$_GET['utm_campaign']:"";
	$utm_content=isset($_GET['utm_content'])?$_GET['utm_content']:"";
	$utm_medium=isset($_GET['utm_medium'])?$_GET['utm_medium']:"";
	$utm_source=isset($_GET['utm_source'])?$_GET['utm_source']:"";
	$utm_term=isset($_GET['utm_term'])?$_GET['utm_term']:"";
	$utm_ab=isset($_GET['utm_ab'])?$_GET['utm_ab']:"";

	if(!empty($utm_campaign) ||
		!empty($utm_content) ||
		!empty($utm_medium) ||
		!empty($utm_source) ||
		!empty($utm_term) ||
		!empty($utm_ab) ) {
		$db->query("INSERT INTO utm SET
				uid='$uid',
				tm='".time()."',
				utm_campaign='".$db->escape($utm_campaign)."',
				utm_content='".$db->escape($utm_content)."',
				utm_medium='".$db->escape($utm_medium)."',
				utm_source='".$db->escape($utm_source)."',
				utm_term='".$db->escape($utm_term)."',
				utm_ab='".$db->escape($utm_ab)."',
				pwd_id='$pwd_id'
				");
	}
}

$par_url="";
if($bc)
	$par_url.="bc=$bc&";
if($uid)
	$par_url.="uid=$uid_md5&";
if($utm_source)
	$par_url.="utm_source=$utm_source&";
if($utm_campaign)
	$par_url.="utm_campaign=$utm_campaign&";
if($utm_medium)
	$par_url.="utm_medium=$utm_medium&";
if($utm_content)
	$par_url.="utm_content=$utm_content&";
if($utm_term)
	$par_url.="utm_term=$utm_term&";
if($utm_ab)
	$par_url.="utm_ab=$utm_ab&";
if (substr($par_url, -1) === "&") {
    $par_url = substr($par_url, 0, -1);
}

?>
