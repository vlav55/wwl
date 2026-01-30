<?
include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
include "init.inc.php";
$db=new partnerka(false,$database);

$title='Благодарим за регистрацию в партнерской программе';
$descr=$title;
$og_image="";
$favicon="https://for16.ru/images/favicon.png";

include "land_top.inc.php";

$klid=0; $user_id=0; $uid=0;
if(isset($_POST['bc'])) {
	if($bc=intval($_POST['bc'])) {
		if($klid=$db->dlookup("klid","users","bc='$bc'")) {
			$user_id=$db->get_user_id($klid);
			$uid=0;
		}
	}
}

if(isset($_POST['regPhone']))
	$_POST['client_phone']=$_POST['regPhone'];
if(isset($_POST['regEmail']))
	$_POST['client_email']=$_POST['regEmail'];
if(isset($_POST['regName']))
	$_POST['client_name']=$_POST['regName'];

$client_name=false;
if(isset($_POST['client_name']))
	$client_name=$_POST['client_name'];

if($client_name) {
	if(isset($_POST['client_phone']))
		if(!$mob=$db->check_mob($_POST['client_phone']))
			$mob="";
	if(isset($_POST['client_email']))
		$email=($db->validate_email($_POST['client_email']))?trim($_POST['client_email']):"";
	else
		$email="";

	$uid=false;
	if(!empty($mob)) {
		if(!$uid=$db->dlookup("uid","cards","mob_search='$mob'")) {
			if(!empty($email)) {
				$uid=$db->dlookup("uid","cards","email='$email'");
			}
		}
	}
	if(!$uid) {
		$uid=$db->get_unicum_uid();
		$uid_md5=$db->uid_md5($uid);
		$client_name=$_POST['client_name'];

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
	} else {
		if(empty($db->dlookup("mob_search","cards","uid='$uid'")) && $db->check_mob($mob) )
			$db->query("UPDATE cards SET mob='$mob',mob_search='$mob' WHERE uid='$uid'");
		if(empty($db->dlookup("email","cards","uid='$uid'")) && !empty($email) )
			$db->query("UPDATE cards SET email='".$db->escape($email)."' WHERE uid='$uid'");
	}

	if($uid) {
		$referer=parse_url($_SERVER['HTTP_REFERER'])['path'];
		$db->save_comm_tm_ignore=60*60;
		$db->save_comm($uid,0,"⭐ Регистрация с лэндинга в партнерской программе: $referer",12,$user_id);
		if(!$db->dlookup("user_id","cards","uid='$uid'") && $user_id) {
			$db->query("UPDATE cards SET user_id='$user_id',utm_affiliate='$klid' WHERE uid='$uid'");
		}
		$db->notify($uid,"⭐ Регистрация с лэндинга в партнерской программе: $referer");

		$crm_uid=$uid;
		$db->save_comm($crm_uid,$user_id,false,25);

		$partner_klid=$db->dlookup("id","cards","uid='$crm_uid'");
		$f_name=$db->dlookup("name","cards","uid='$crm_uid'");
		$l_name=$db->dlookup("surname","cards","uid='$crm_uid'");
		$db->fee_hello=$fee_hello;
		$db->fee=$fee_1;
		$db->fee2=$fee_2;

		$r=$db->partner_add($partner_klid,$email='',$l_name.' '.$f_name,$username_pref='partner_');
		$partner_user_id=$db->get_user_id($partner_klid);
		//$vk->vk_msg_send(198746774, "P uid=$crm_uid u=$partner_user_id k=$partner_klid ".print_r($r,true));
		//$vk->vk_msg_send(198746774, print_r($r,true));

		$bc=$db->dlookup("bc","users","klid='$partner_klid'");
		$direct_code_link=$db->get_direct_code_link($partner_klid);
		$partner_link=$db->get_partner_link($partner_klid,'land');

		$db->query("UPDATE cards SET razdel='2' WHERE uid='$uid'");

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

		//code for TG 
		$tm=time()-(6*60*60);
		$db->query("DELETE FROM telegram WHERE tm<'$tm'");
		$tg_code=rand(1000,9999);
		$n=1000;
		while($db->dlookup("id","telegram","code='$tg_code'")) {
			$tg_code=rand(1000,9999);
			if(!$n--)
				break;
		}
		$db->query("INSERT INTO telegram SET tm='".time()."',uid='$uid',code='$tg_code',confirmed=1");
	} else
		print "<p class='alert alert-danger' >Ошибка. Обратитесь в техподдержку!</p>";
}
	
$db->connect('vkt');
$thanks_pic_p=(file_exists('tg_files/thanks_pic_p.jpg'))?"<img src='tg_files/thanks_pic_p.jpg' class='img-fluid' >":"";
$thanks_txt_p=$db->dlookup("thanks_txt_p","0ctrl","id=$ctrl_id");

?>
<div class='text-center' ><?=$thanks_pic_p?></div>
<div class='container' >
<div class='p-3 m-3 text-center' >
	<?=$thanks_txt_p?>
</div>

<!--
<div class='card p-3 my-3' >
	<p>Теперь необходимо подключить телеграм, в телеграм вам будет отправлена
	ссылка на ваш личный кабинет и партнерская ссылка,
	которую можете давать для регистрации на наших лэндингах.
	<br>
	Каждый, кто по ней зарегистрируется, будет закреплен за вами.
	</p>
	<p>Нажмите кнопку ниже и перейдите в ТГ.</p>
</div>
-->


<div class='text-center mb-5' ><a href='https://t.me/<?=$tg_bot_msg_name?>?start=<?=$tg_code?>' class='btn btn-info btn-lg' target=''>Перейти в телеграм</a></div>
<?
//print "uid=$uid tg_code=$tg_code<br>";
//$db->print_r($_POST);
?>

<? include "land_bottom.inc.php"; ?>
