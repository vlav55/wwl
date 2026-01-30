<?
include_once "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
//include "init.inc.php";

$vkts=new vkt_send($database);
$vkts->telegram_bot=$tg_bot_notif;
$vkts->db200=$DB200;
$vkts->vkt_send_tg_bot=$tg_bot_msg;
$vkts->pact_secret=$pact_secret;
$vkts->pact_company_id=$pact_company_id;

if(isset($fl_tg))
	$vkts->fl_tg=true;
elseif(isset($fl_vk))
	$vkts->fl_vk=true;
elseif(isset($fl_wa)) {
	$vkts->fl_wa=true;
	$vkts->vkt_send_skip_wa=false;
}

include_once "/var/www/vlav/data/www/wwl/inc/bot.1.inc.php"; 

if($msg=='hello') {
	$fl_notify=false;
	//$fl_save=false; //only condition for bot listening
	$vkts->vkt_send_msg($uid,"Hi {{bot_set iwish}}",0,false,true);
	//$uid,$msg,$source_id=3,$num=0,$attach=false,$force_if_not_wa_allowed=false);
	//$vkts->vkt_send_msg($uid,"Hi {{bot_set 1}}"); //1 - command to start time for webinar dialog
}

//~ if(mb_strtolower($msg)=="да") {
        //~ if($vkts->bot_chk($uid)==1521) { //skolkovo
			//~ sleep(2);
                //~ $vkts->vkt_send_msg($uid,"Отлично, вы записаны. Менеджер свяжется с вами по этому вопросу");
        //~ }
//~ }

if(!preg_match("/[0-9]{1,2}[\s\.\:\,\-]{1,1}[0-9]{1,2}/s",$msg,$m))
	preg_match("/[0-9]{1,2}/s",$msg,$m);
if(isset($m[0])) {
	$times=[
			6=>6*60*60,600=>6*60*60,
			7=>7*60*60,700=>7*60*60,
			8=>8*60*60,800=>8*60*60,
			9=>9*60*60,900=>9*60*60,
			10=>10*60*60,1000=>10*60*60,
			11=>11*60*60,1100=>11*60*60,
			12=>12*60*60,1200=>12*60*60,
			13=>13*60*60,1300=>13*60*60,
			14=>14*60*60,1400=>14*60*60,
			15=>15*60*60,1500=>15*60*60,
			16=>16*60*60,1600=>16*60*60,
			17=>17*60*60,1700=>17*60*60,
			18=>18*60*60,1800=>18*60*60,
			19=>19*60*60,1900=>19*60*60,
			20=>20*60*60,2000=>20*60*60,
			21=>21*60*60,2100=>21*60*60,
			22=>22*60*60,2200=>22*60*60,
			23=>23*60*60,2300=>23*60*60,
			1440=>14*60*60+40*60,
			1720=>17*60*60+20*60,
			1420=>14*60*60+40*60,
			1740=>17*60*60+20*60,
			];

	$web_ids=[];
	$res_bot=$vkts->query("SELECT * FROM lands WHERE del=0 AND bizon_duration>0 AND tm_scdl>".time());
	while($r_bot=$db->fetch_assoc($res_bot)) {
		$web_ids[$r_bot['tm_scdl'] - $db->dt1($r_bot['tm_scdl'])]=$r_bot['land_num'];
	}
	//~ $web_ids=[9*60*60=>17,
			//~ 12*60*60=>18,
			//~ 14*60*60+40*60=>19,
			//~ 17*60*60+20*60=>20,
			//~ 20*60*60=>21,
		//~ ];
		
	$key=preg_replace("/[\s\.\:\,\-]+/i","",$m[0]);
	if(in_array($key,array_keys($times)) ) {
		//$this->notify($uid,"TEST vote=$last_num uid=$uid tm=".$times[$key]." msg=$msg");
		$step=$vkts->bot_chk($uid);
	//$vkts->vkt_send_msg($uid,"HERE_");
		if($step==1) {
			$fl_notify=false;
			$web_id=$web_ids[$times[$key]];
			$tm=$vkts->dt1(time()+(24*60*60))+$times[$key];
			if(intval(date("H")) <=3)
				$tm=$vkts->dt1(time())+$times[$key];
			else
				$tm=$vkts->dt1(time()+(24*60*60))+$times[$key];
			$tm_val=date("H-i",$tm);
			//$out=str_replace("{tm}",$tm_val,$this->msg_2);
	//$vkts->vkt_send_msg($uid,"$tm $web_id\n".print_r($web_ids,true));		
			sleep(5);
			if(array_key_exists($tm-$db->dt1($tm),$web_ids)) {
				$vkts->query("UPDATE cards SET tm_schedule='$tm',scdl_fl=0,scdl_web_id='$web_id' WHERE uid='$uid'");


				$dt=date('H:i',$tm);
				//$vkts->save_comm_custom_fl=$web_id;
				//$vkts->save_comm($uid,0,"УСТАНОВКА В РАСПИСАНИЕ funnel_bot НА $dt",100,'funnel_bot');
				//$this->add_action_after_reg($uid,$this->web_id,$msg);
				$msg="Ок, вы записаны на завтра на $dt. Cсылку я пришлю сюда перед началом, до встречи на вебинаре🤝 {{bot_set thank}}";
				$vkts->vkt_send_msg($uid,$msg);
			} else {
				$vkts->vkt_send_msg($uid,"Извините, но на это время не запланирован вебинар");
			}
		}
	}
} elseif(preg_match("/спасибо|спс|благодарю|отлично|хорошо|ok|ок|да|договорились/ius",$msg)) {
	if($vkts->bot_chk($uid)=='thank') {
		$msg="пожалуйста. на связи";
		sleep(5);
		$fl_notify=false; 
		$vkts->vkt_send_msg($uid,$msg);
	}
} elseif(preg_match("/хочу/ius",$msg)) {
	if($vkts->bot_chk($uid)=='iwish') {
		$msg="🔥 Ваш промокод 777 и он действует в течение одного часа на оплату тарифа по 2900р.

Оплатить можно по ссылке https://winwinland.ru/order.php?s=0&t=0&product_id=53&land_num=41&uid={{uid}}
{{promocode 777 after 1 [53] 0 2900}}";
		sleep(5);
		$fl_notify=false; 
		$vkts->vkt_send_msg($uid,$msg);
		$vkts->notify($uid,"🔥 отправлено ХОЧУ");
		$vkts->save_comm($uid,0,false,603,0,0,true);
	}
}


?>
