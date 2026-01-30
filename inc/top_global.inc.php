<?
$pwd=str_replace("/var/www/html/pini/","",getcwd());
$pwd_id=$db->dlookup("id","pixel_pages","pwd='$pwd'");
if(!$pwd_id) {
	$db->query("INSERT INTO pixel_pages SET pwd='".$db->escape($pwd)."'");
	$pwd_id=$db->insert_id();
}

//$db->email($emails=array("vlav@mail.ru"), "TEST", "", $from="noreply@yogahelpyou.com",$fromname="YOGAHELPYOU", $add_globals=true);


$uids_prohibited=array();
$disp_contacts=false;
if(!isset($uid))
	$uid=false;
if(isset($_GET['uid'])) {
	if(is_numeric($_GET['uid'])) {
		//~ if(intval($_GET['uid']))
			//~ $db->yoga_email("UID IS INTEGER uid={$_GET['uid']}", nl2br("REQUEST_URI=".$_SERVER['REQUEST_URI']."\n"."HTTP_REFERER=".$_SERVER['HTTP_REFERER']) );
	}
	if($db->is_md5($_GET['uid']) ) {
		$_GET['uid']=$db->dlookup("uid","cards","uid_md5='{$_GET['uid']}'");
		if(!$_GET['uid']) {
			$res=$db->query("SELECT * FROM cards WHERE cards.del=0");
			while($r=$db->fetch_assoc($res)) {
				if($r['uid_md5']!=$db->uid_md5($r['uid']) ) {
					$db->query("UPDATE cards SET uid_md5='".$db->uid_md5($r['uid'])."' WHERE uid='{$r['uid']}'");
				}
			}
		}
		$disp_contacts=true;
	}
	$_SESSION['vk_uid']=intval($_GET['uid']);
	if($_SESSION['vk_uid'])
		setcookie("saved_uid", $_SESSION['vk_uid'], time()+(365*24*60*60));
	$uid=$_SESSION['vk_uid'];
}

if(isset($_POST['uid'])) {
	$_SESSION['vk_uid']=intval($_POST['uid']);
	if($_SESSION['vk_uid'])
		setcookie("saved_uid", $_SESSION['vk_uid'], time()+(365*24*60*60));
	$uid=$_SESSION['vk_uid'];
}

if(isset($_POST['client_email']))
	$_GET['email']=$_POST['client_email'];
if(isset($_GET['client_email']))
	$_GET['email']=$_GET['client_email'];
if(isset($_POST['email']))
	$_GET['email']=$_POST['email'];

if(isset($_GET['email']) && filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
	if(!$uid)
		$uid=$db->dlookup("uid","cards","del=0 AND email='{$_GET['email']}'",0);
	if( $uid !==false ) {
		$_SESSION['vk_uid']=intval($uid);
		if($_SESSION['vk_uid'])
			setcookie("saved_uid", $_SESSION['vk_uid'], time()+(365*24*60*60));
		$disp_contacts=true;
	}
}
if(!isset($_SESSION['vk_uid']) ) {
	if(!$uid) {
		if(isset($_COOKIE['saved_uid'])) {
			$_SESSION['vk_uid']=intval($_COOKIE['saved_uid']);
			$uid=$_SESSION['vk_uid'];
			$disp_contacts=true;
		} else
			$_SESSION['vk_uid']=false;
	}
}

if( in_array($_SESSION['vk_uid'],$uids_prohibited) ) {
	$_SESSION['vk_uid']=false;
	$uid=false;
}

if($_SESSION['vk_uid'] && !isset($_GET['bc'])) {
	$dt1=$db->dt1(time());
	$tm1=time()-$dt1;
	$db->query("INSERT INTO pixel SET pwd_id='$pwd_id',uid='{$_SESSION['vk_uid']}',tm='".time()."',dt1='".$dt1."',tm1='".$tm1."'"); 
	$utm="";
	if(isset($_GET['insta_c'])) {
		$_GET['utm_source']="insta_direct";
	}
	if(isset($_GET['utm_source']))
		$utm.="utm_source='".$db->escape($_GET['utm_source'])."',";
	if(isset($_GET['utm_campaign']))
		$utm.="utm_campaign='".$db->escape($_GET['utm_campaign'])."',";
	if(isset($_GET['utm_content']))
		$utm.="utm_content='".$db->escape($_GET['utm_content'])."',";
	if(isset($_GET['utm_medium']))
		$utm.="utm_medium='".$db->escape($_GET['utm_medium'])."',";
	if(isset($_GET['utm_term']))
		$utm.="utm_term='".$db->escape($_GET['utm_term'])."',";
	if(!empty($utm)) {
		$utm=substr($utm,0,strlen($utm)-1);
	//	print $utm;
		$db->query("INSERT INTO utm SET uid='{$_SESSION['vk_uid']}',tm='".time()."',$utm,pwd_id='$pwd_id'");
	}
	if(!$uid)
		$uid=$_SESSION['vk_uid'];
	$uid_md5=$db->uid_md5($uid);
} else {
	$uid=0; $uid_md5=0;
}

if(isset($_GET['ua']))
	$_GET['utm_affiliate']=$_GET['ua'];
if(isset($_GET['bc']))
	$_GET['utm_affiliate']=$_GET['bc'];
if(isset($_GET['utm_affiliate'])) {
	$_SESSION['utm_affiliate']=intval($_GET['utm_affiliate']);
}
if(!isset($_SESSION['utm_affiliate']))
	$_SESSION['utm_affiliate']=false;
if(!isset($pixel))
	$pixel="";

if(isset($_GET['insta_c'])) {
	$_SESSION['insta_cid']=intval($_GET['insta_c']);
	$_SESSION['insta_user']=$_GET['u'];
}
if(!isset($_SESSION['insta_cid']))
	$_SESSION['insta_cid']=false;

if(isset($not_check_uid)) {
	$uid=0; $uid_md5=0;
}

?>
