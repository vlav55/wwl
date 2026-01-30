<?
http_response_code(200);
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
include "init.inc.php";
$db=new db($database);

if(isset($_POST['url'])) {
	if(!empty($query=parse_url($_POST['url'],PHP_URL_QUERY))) {
		parse_str($query, $m);
		if(isset($m['bc'])) {
			$_POST['bc']=$m['bc'];
		}
	}
	$klid=0; $user_id=0; $uid=0; $bc=false;
	$webhooktype=intval($_POST['webhooktype']);
	$bc=isset($_POST['bc'])?intval($_POST['bc']):false;
	$client_name=isset($_POST['name'])?substr($_POST['name'],0,32):"";
	$phone=isset($_POST['phone'])?substr($_POST['phone'],0,32):"";
	$mob=$db->check_mob($phone)?$db->check_mob($phone):"";
	$email=isset($_POST['email'])?substr($_POST['email'],0,32):"";
	$email=$db->validate_email($email)?$email:"";
	$city=isset($_POST['place'])?substr($_POST['place'],0,32):"";
	$message=isset($_POST['message'])?substr($_POST['message'],0,2000):"";
	$chat_text=isset($_POST['chat_text'])?substr($_POST['chat_text'],0,6000):"";

	if($bc) {
		if($klid=$db->dlookup("klid","users","bc='$bc'")) {
			$user_id=$db->get_user_id($klid);
		}
	}
	if(!empty($mob)) {
		if(!$uid=$db->dlookup("uid","cards","mob_search='$mob' AND del=0")) {
			if(!empty($email)) {
				$uid=$db->dlookup("uid","cards","email='$email' AND del=0");
			}
		}
	}

	if(!$uid) {
		$uid=$db->get_unicum_uid();
		$uid_md5=$db->uid_md5($uid);

		$db->query("INSERT INTO cards SET 
				uid='$uid',
				uid_md5='$uid_md5',
				name='".$db->escape($client_name)."',
				email='".$db->escape($email)."',
				mob='$mob',
				mob_search='$mob',
				acc_id=2,
				razdel='4',
				source_id='0',
				fl_newmsg=0,
				tm_lastmsg=".time().",
				tm=".time().",
				user_id='$user_id',
				pact_conversation_id=0,
				utm_affiliate='$klid',
				wa_allowed=0
				",0);
		if(!$mob && empty($email)) {
			print "<p class='alert alert-danger' >Отсутствуют контактные данные!</p>";
		}
	} else {
		if(empty($db->dlookup("mob_search","cards","uid='$uid' AND del=0")) && $db->check_mob($mob) )
			$db->query("UPDATE cards SET mob='$mob',mob_search='$mob' WHERE uid='$uid'");
		if(empty($db->dlookup("email","cards","uid='$uid' AND del=0")) && !empty($email) )
			$db->query("UPDATE cards SET email='".$db->escape($email)."' WHERE uid='$uid'");
	}

	$db->save_comm_tm_ignore=60*60;
	$db->save_comm($uid,0,"⭐Регистрация  (envybox $webhooktype) ВИДЖЕТ",12,$user_id);
	$db->save_comm($uid,0,"envybox $webhooktype",1000+900+$webhooktype);
	$db->notify($uid,"⭐ (envybox $webhooktype ) ВИДЖЕТ");

	if(!empty($message))
		$db->save_comm($uid,0,$message);
	if(!empty($chat_text))
		$db->save_comm($uid,0,$chat_text);

	$utm_campaign=isset($_POST['utm_campaign'])?$_POST['utm_campaign']:"";
	$utm_content=isset($_POST['utm_content'])?$_POST['utm_content']:"";
	$utm_medium=isset($_POST['utm_medium'])?$_POST['utm_medium']:"";
	$utm_source=isset($_POST['utm_source'])?$_POST['utm_source']:"";
	$utm_term=isset($_POST['utm_term'])?$_POST['utm_term']:"";
	$utm_ab=isset($_POST['utm_ab'])?$_POST['utm_ab']:"";
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
				pwd_id='1',
				promo_code='0',
				mob='$mob' ");
	}
}

file_put_contents("envybox_webhook.txt",print_r($_POST,true));
print "OK";
?>
