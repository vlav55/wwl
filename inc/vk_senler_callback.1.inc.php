<?
http_response_code(200);
include "init.inc.php";
$secret=$senler_secret;
$data = json_decode(file_get_contents('php://input'),true);


file_put_contents("vk_senler_callback.txt",print_r($data,true));

if(empty($secret) && isset($data['secret']) ) {
	$secret=$data['secret'];
	$db=new db('vkt');
	$db->query("UPDATE 0ctrl SET senler_secret='".$db->escape($secret)."' WHERE id=$ctrl_id");
}


if( ($data['type']=="subscribe" || $data['type']=="unsubscribe") && $data['secret']==$secret) {

	include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
	include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";

	$vk_uid=intval($data['object']['vk_user_id']);
	if($vk_group_id!=$data['object']['vk_group_id']) {
		$vk_group_id=intval($data['object']['vk_group_id']);
		$db_ctrl=new partnerka(false,'vkt');
		$db_ctrl->query("UPDATE 0ctrl SET vk_group_id='$vk_group_id' WHERE id='$ctrl_id'");
	}

	$db=new partnerka(false,$database);
	$db->telegram_bot=$tg_bot_notif;
	$db->db200=$DB200;

	$vk=new vklist_api;
	$vk->token=$db->dlookup("token","vklist_acc","id=2");

	//$vk->vk_msg_send(198746774, print_r($data['object'],true));

	$utm_source=(isset($data['object']['utm_source']))?$data['object']['utm_source']:"";
	$utm_campaign=(isset($data['object']['utm_campaign']))?$data['object']['utm_campaign']:"";
	$utm_content=(isset($data['object']['utm_content']))?$data['object']['utm_content']:"";
	$utm_term=(isset($data['object']['utm_term']))?$data['object']['utm_term']:"";
	$utm_medium=(isset($data['object']['utm_medium']))?$data['object']['utm_medium']:"";

	$r=$vk->vk_get_userinfo($vk_uid);
	$f_name=$r['first_name'];
	$l_name=$r['last_name'];
	$city=$r['city']['title'];

	$user_id=0;
	$klid=0;
	if(intval($utm_term)) { //partnerka codes
		if($user_id=$db->dlookup("id","users","bc='$utm_term'")) {
			if($klid=$db->get_klid($user_id))
				$utm_term="";
			
		}
	}

	$crm_uid=0;
	if($db->is_md5($utm_source)) {
		if($crm_uid=$db->get_uid($utm_source))
			$utm_source="";
	}
	if(!$crm_uid)
		$crm_uid=$db->get_uid($vk_uid);

	//~ $test_uid=$db->dlookup("uid","cards","uid='$vk_uid'");
	//~ $db->notify_me("vk_senler_callback: crm_uid=$crm_uid vk_uid=$vk_uid test_uid=$test_uid \n\n".print_r($data,true));

	if(!$crm_uid) {
		$crm_uid=$vk_uid;
		$uid_md5=$db->uid_md5($crm_uid);
		$db->query("INSERT INTO cards SET 
				uid='$crm_uid',
				vk_id='$vk_uid',
				uid_md5='$uid_md5',
				name='".$db->escape($f_name)."',
				surname='".$db->escape($l_name)."',
				city='".$db->escape($city)."',
				acc_id=2,
				razdel='4',
				fl_newmsg=0,
				tm_lastmsg=".time().",
				tm=".time().",
				user_id='$user_id',
				utm_affiliate='$klid',
				wa_allowed=0
				");
	}

	$db->query("UPDATE cards SET vk_id='$vk_uid' WHERE uid='$crm_uid'");
	//~ if($user_id && $klid) {
		//~ $tm=time();
		//~ if($db->dlookup("user_id","cards","uid='crm_uid'") !=0) {
			//~ if(!$keep)
				//~ $db->query("UPDATE cards SET tm_user_id='$tm',user_id='$user_id',utm_affiliate='$klid' WHERE uid='$crm_uid'");
		//~ } else
			//~ $db->query("UPDATE cards SET tm_user_id='$tm', user_id='$user_id',utm_affiliate='$klid' WHERE uid='$crm_uid'");
	//~ }
	$db->save_comm($crm_uid,0,false,12);
	//$db->notify_me("HERE $crm_uid $vk_uid");
	$uid=$crm_uid;
	if(!$db->hold_chk($uid) && $user_id) {
		$db->query("UPDATE cards SET user_id='$user_id',tm_user_id='".time()."',utm_affiliate='$klid' WHERE uid='$uid'");
	}
	if(!$keep && $user_id) {
		$db->query("UPDATE cards SET user_id='$user_id',tm_user_id='".time()."',utm_affiliate='$klid' WHERE uid='$uid'");
	}

	
	if(intval($data['object']['subscription_id'])==$senler_gid_partnerka && $senler_gid_partnerka>0) { //партнерка
		$vk->vk_msg_send($vk_uid,"🙏 Поздравляем с регистрацией и рады Вас видеть в числе участников нашей партнерской программы! ");
		$db->save_comm($crm_uid,$user_id,false,25);

		$partner_klid=$db->dlookup("id","cards","uid='$crm_uid'");
		$db->fee_hello=$fee_hello;
		$db->fee=$fee_1;
		$db->fee1=$fee_2;
		$db->ctrl_id=$ctrl_id;

		$r=$db->partner_add($partner_klid,$email='',$f_name.' '.$l_name,$username_pref='partner_');
		$partner_user_id=$db->get_user_id($partner_klid);
		//$vk->vk_msg_send(198746774, "P uid=$crm_uid u=$partner_user_id k=$partner_klid ".print_r($r,true));
		//$vk->vk_msg_send(198746774, print_r($r,true));

		$bc=$db->dlookup("bc","users","klid='$partner_klid'");
		$direct_code_link=$db->get_direct_code_link($partner_klid);
		$partner_link=$db->get_partner_link($partner_klid,'senler');

		include_once "/var/www/vlav/data/www/wwl/inc/senler_api.class.php";
		$s=new senler_api($senler_secret);
		$s->vars_set($vk_uid,$vk_group_id,'cabinet_link',$direct_code_link); //{%cabinet_link%}
		$s->vars_set($vk_uid,$vk_group_id,'partner_link',$partner_link); //{%partner_link%}
		$s->vars_set($vk_uid,$vk_group_id,'partner_code',$bc); //{%partner_code%}
	
		$vk->vk_msg_send($vk_uid, "Вход в личный кабинет: $direct_code_link");
		$vk->vk_msg_send($vk_uid, "Партнерская ссылка: $partner_link \n\n");

		$db->notify($crm_uid,"🙋‍♀️(VK) Новая регистрация в партнерской программе");
		$db->mark_new($crm_uid,3);

	} else { //if it is an usual lead, not a partner
		$db->notify($crm_uid,"🔸(VK) Новая регистрация из группы {$data['object']['subscription_id']}");
	}

	if(!empty($utm_campaign) ||
		!empty($utm_content) ||
		!empty($utm_medium) ||
		!empty($utm_source) ||
		!empty($utm_term) ) {
		//~ if(isset($land_num)) //if utm set land_num=0 that cause wrong par pass to telegram bot, I don't know why
			//~ $land_num=0;
		$db->query("INSERT INTO utm SET
				uid='$crm_uid',
				tm='".time()."',
				utm_campaign='".$db->escape($utm_campaign)."',
				utm_content='".$db->escape($utm_content)."',
				utm_medium='".$db->escape($utm_medium)."',
				utm_source='".$db->escape($utm_source)."',
				utm_term='".$db->escape($utm_term)."',
				promo_code='0'
				");
	}


	if(file_exists("vk_senler_callback.inc.php") && $ctrl_id==1)
		include "vk_senler_callback.inc.php";

}


print "ok";
?>
