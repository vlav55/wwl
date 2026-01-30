#!/usr/bin/php -q
<?
include_once "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/sendpulse.class.php";
//include_once "/var/www/vlav/data/www/wwl/prices.inc.php";
$db=new vkt_send("vkt");
$db->telegram_bot="vkt";
$db->db200="https://for16.ru/d/1000";
//$db->notify(-1002,"test");

$sp=new sendpulse('vkt');
print "SCANNING FOR PAYMENTS FOR WWL\n";

$tm1=$db->dt1(time());
$tm2=$db->dt2(time());
$tm_chk=$db->dt1(time()-(14*24*60*60));
$yesterday=$db->dt1(time()-(1*24*60*60));
$res=$db->query("SELECT *,avangard.tm AS tm, avangard.email AS email FROM avangard
			JOIN cards ON vk_uid=uid
			WHERE res=1 AND tm_end>=$tm_chk
			ORDER BY tm_end DESC",0);
$n=0;
/////
$test=false;
//$test=false;
/////
$out="";
while($r=$db->fetch_assoc($res)) {
	//~ if(!in_array($r['product_id'],[30,31,32])) 
		//~ continue;
	if($product_id<30 || $product_id>39) 
		continue;
	$uid=intval($r['vk_uid']);
	$uid_md5=$db->uid_md5($uid);
	$ctrl_dir=$db->dlookup("ctrl_dir","0ctrl","uid='$uid'");
	$crm_url="https://for16.ru/d/$ctrl_dir";
	$dt_pay=date('d.m.Y',$r['tm']);
	$dt_end=date('d.m.Y',$r['tm_end']);

	if($db->dlast("tm_end","avangard","res=1 AND vk_uid='$uid'")>$r['tm_end']) {
		print "";
		continue;
	}
	if($db->dlookup("tm_end","0ctrl","uid='$uid'")) {
		$out.= "=====$uid 0ctrl.tm_end >0 PASSED \n";
		continue;
	}


	$tm_first_notif=$r['tm_end']-(2*24*60*60);
	$tm_second_notif=$r['tm_end']-(0*24*60*60);
	$tm_stop_access=$r['tm_end']+(1*24*60*60);
	$tm_last_notif=$db->dt1($r['tm_end']+(5*24*60*60));

	$uid=($test) ? -1002 : $uid;

	$email=($test) ? 'vlav@mail.ru' : $r['email'];
	$product_id=intval($r['product_id']);

	if($tm_first_notif>=$tm1 && $tm_first_notif <= $tm2) {
		if(!$db->dlookup("id","msgs","uid='$uid' AND source_id=150 AND tm>$yesterday",1)) { //NOTIF #1
			print " --- $uid notif_1 ".date('d.m.Y',$tm_first_notif)." DONE\n";
			$db->save_comm($uid,0,"Уведомление об окончании подписки #1",150);
			$msg="Здравствуйте, через 2 дня у вас заканчивается доступ к платформе WinWinLand
			Продлите доступ на 30, 90 или 360 дней по ссылке $crm_url/billing_pay.php
			";

			$db->vkt_send_tg_bot=$db->dlookup("tg_bot_msg","0ctrl","uid='$uid'");
			$db->vkt_send_msg($uid,$msg);

			if($db->validate_email($email)) {
				$sp->email_by_template($sp_template='avangard_pay_check_1.html',
									$email,
									$r['name'],
									$subj="🟡 [клиенту] через 2 дня у вас заканчивается доступ к продукту «{$r['order_descr']}»",
									$from_email='office@winwinland.ru',
									$from_name='WinWinLand',
									$uid,"$crm_url/billing_pay.php");
			}

			$db->notify($uid,"Клиенту отправлено уведомление: 🟡 [клиенту] через 2 дня у вас заканчивается доступ к продукту «{$r['order_descr']}» (на емэйл и мессенджеры)");
			$db->mark_new($uid,3);
			$out.="*** first_notif SENT \n";
		} else {
			print "$uid notif_1 ".date('d.m.Y',$tm_first_notif)." PASSED BECAUSE DONE\n";
		}
	}
	if($tm_second_notif>=$tm1 && $tm_second_notif <= $tm2) {
		if(!$db->dlookup("id","msgs","uid='$uid' AND source_id=151 AND tm>$yesterday")) { //NOTIF #2
			print " --- $uid notif_2 ".date('d.m.Y',$tm_second_notif)." DONE\n";
			$db->save_comm($uid,0,"Уведомление об окончании подписки #2",151);
			$msg="Здравствуйте, сегодня у вас заканчивается доступ к CRM WINWINLAND.
			Продлите доступ на 30, 90 или 360 дней по ссылке $crm_url/billing_pay.php
			";

			$db->vkt_send_tg_bot=$db->dlookup("tg_bot_msg","0ctrl","uid='$uid'");
			$db->vkt_send_msg($uid,$msg);

			if($db->validate_email($email)) {
				$sp->email_by_template($sp_template='avangard_pay_check_2.html',
									$email,
									$r['name'],
									$subj="🟡 [клиенту] сегодня у вас заканчивается доступ к продукту «{$r['order_descr']}»",
									$from_email='office@winwinland.ru',
									$from_name='WinWinLand',
									$uid,$r['order_descr']);
			}

			$db->notify($uid,"Клиенту отправлено уведомление: 🟡 [клиенту] сегодня у вас заканчивается доступ к продукту «{$r['order_descr']}» (на емэйл и мессенджеры)");
			$db->mark_new($uid,3);
			$out.="*** second_notif SENT \n";
		} else {
			print "$uid notif_2 ".date('d.m.Y',$tm_first_notif)." PASSED BECAUSE DONE\n";
		}
	}
	if($tm_last_notif>=$tm1 && $tm_last_notif <= $tm2) {
		if(!$db->dlookup("id","msgs","uid='$uid' AND source_id=152 AND tm>$yesterday")) { //NOTIF #LAST
			print " --- $uid notif_last ".date('d.m.Y',$tm_last_notif)." DONE\n";
			$db->save_comm($uid,0,"Уведомление об окончании подписки #LAST",152);
			$msg="Здравствуйте, несколько дней назад у вас закончился доступ к CRM WINWINLAND.
			
			Продлите доступ на 30, 90 или 360 дней по ссылке $crm_url/billing_pay.php

			P.S. Это последнее автоматическое сообщение с напоминанием об оплате.
			";

			$db->vkt_send_tg_bot=$db->dlookup("tg_bot_msg","0ctrl","uid='$uid'");
			$db->vkt_send_msg($uid,$msg);

			if($db->validate_email($email)) {
				$sp->email_by_template($sp_template='avangard_pay_check_3.html',
									$email,
									$r['name'],
									$subj="🟡 [клиенту] продлите доступ к продукту «{$r['order_descr']}»",
									$from_email='office@winwinland.ru',
									$from_name='WinWinLand',
									$uid,$r['order_descr']);
			}

			$db->notify($uid,"Клиенту отправлено уведомление: 🟡 [клиенту последнее уведомление] продлите доступ к продукту «{$r['order_descr']}» (на емэйл и мессенджеры)");
			$db->mark_new($uid,3);
			$out.="*** last_notif SENT \n";
		} else {
			print "$uid notif_3 ".date('d.m.Y',$tm_first_notif)." PASSED BECAUSE DONE\n";
		}
	}
	if($tm_stop_access>=$tm1 && $tm_stop_access <= $tm2) { //stoping access
		$db->save_comm($uid,0,"Доступ остановлен за неоплату",153);
		$db->notify($uid,"Клиент не оплатил продление доступа к «{$r['order_descr']}» \n Доступ остановлен.");
		$db->mark_new($uid,3);
		$out.="*** STOP_ACCESS ACTION REQUIRED \n";
	}

	$dt_last_notif=date('d.m.Y',$tm_last_notif);
	$dt_first_notif=date('d.m.Y',$tm_first_notif);
	$dt_second_notif=date('d.m.Y',$tm_second_notif);
	$dt_stop_access=date('d.m.Y',$tm_stop_access);
	
	$dt1=date('d.m.Y H:i',$tm1);
	$dt2=date('d.m.Y H:i',$tm2);
	$out.= "dt_pay=$dt_pay \n dt_end=$dt_end \n dt_first_notif=$dt_first_notif\n dt_second_notif=$dt_second_notif\n dt_stop_access=$dt_stop_access\n  dt_last_notif=$dt_last_notif\n dt1=$dt1 dt2=$dt2\n uid=$uid {$r['name']} {$r['surname']} {$r['city']} {$r['order_descr']} \n\n";
	$n++;
}
print "$out\n"; 
$db->vkt_email("avangard_tm_end_scan",nl2br($out));
print "FINISHED OK cnt=$n \n";

?>
