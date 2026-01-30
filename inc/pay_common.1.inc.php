<?
$mtm=microtime(true);
include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
//~ $_POST = [
    //~ 'csrf_token_order',
    //~ 'product_id',
    //~ 'sku',
    //~ 'phone',
    //~ 'email',
    //~ 'fio',
    //~ 'city',
    //~ 'comm',
    //~ 'comm_pay_cash',
    //~ 'tm_pay_cash',
    //~ 'land_num',
    //~ 'bc',
    //~ 'vk_uid',
    //~ 'sum_disp',
    //~ 'client_ctrl_id', //only for vkt
    //~ 'fee_1', //not use - override from promocode or 0 if no promocode passed
    //~ 'fee_2', //not use - override from promocode
    //~ 'promocode_id',
    //~ 'fee_pay', //pay by fee/ Not implemented yet
    //~ 'tzoffset',
//~ ];

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// Check for CSRF token
		if (!isset($_POST['csrf_token_order']) || $_POST['csrf_token_order'] !== $_SESSION['csrf_token_order']) {
			//$db->notify_me("HERE_".$_SERVER['HTTP_REFERER']);
			if(strpos($_SERVER['HTTP_REFERER'],'https://winwinland.ru/order.php')!== 0) {
				$db->notify_me("pay_common.1.inc.php CSRF error - $ctrl_id S={$_SESSION['csrf_token_order']} P={$_POST['csrf_token_order']}");
				//die('error 6');
			}
		}
		//$db->notify_me("pay_common.1.inc.php CSRF check OK - $ctrl_id {$_SESSION['csrf_token_order']} {$_POST['csrf_token_order']}");

		// Proceed with the processing of the form
	} else {
		$db->notify_me("pay_common.1.inc.php error - not POST request detected");
		//die("error 7");
	}

//---------------------------


	//~ $order_id=$db->get_next_avangard_orderid();
	//~ $order_number=$order_id;
	//~ $order_name=$order_id;
	$custom=isset($_POST['custom']) ? mb_substr($_POST['custom'],0,16) : null;
	$product_id=intval($_POST['product_id']);
	if(isset($_POST['sku']))
		$sku=mb_substr($_POST['sku'],0,32); else $sku="";
	if(empty($sku))
		$sku=$product_id;

	$mob=isset($_POST['phone'])?$db->check_mob($_POST['phone']):false;
	if(!$mob)
		$mob="";

	$email=isset($_POST['email'])?$_POST['email']:"";
	$email=$db->validate_email($email) ? strtolower(trim($email)):"";

	$name=mb_substr($_POST['fio'],0,64);

	$city=(isset($_POST['city']))?mb_substr($_POST['city'],0,32):"";
	$comm=(isset($_POST['comm']))?mb_substr($_POST['comm'],0,2048):"";
	$descr=$base_prices[$product_id]['descr'];

	$comm_avangard="";
	if(isset($_POST['comm_pay_cash']))
		$comm_avangard=trim(mb_substr($_POST['comm_pay_cash'],0,512));
	elseif($custom)
		$comm_avangard=$custom;
	
	$tm_pay_cash=(isset($_POST['tm_pay_cash']))?intval($_POST['tm_pay_cash']):0;
//$db->notify_me(print_r($_POST,true)." =$tm_pay_cash");

	$land_num=0; $land_name=""; $land_tag=0; $land_man_id=0; $land_razdel=0; $tm_scdl=0;
	if(isset($_POST['land_num'])) {
		$land_num=intval($_POST['land_num']);
		if($land_num) {
			$r=$db->fetch_assoc($db->query("SELECT * FROM lands WHERE del=0 AND land_num='$land_num'"));
			if($r) {
				$land_name=$r['land_name'];
				$land_man_id=$r['land_man_id'];
				$land_tag=$r['land_tag'];
				$land_razdel=$r['land_razdel'];
				$tm_scdl=$r['tm_scdl'];
			}
		}
	}

	//~ $klid=0; $user_id=0;
	//~ if($bc=intval($_POST['bc'])) {
		//~ if($klid=$db->get_klid_by_bc($bc)) {
			//~ $user_id=$db->get_user_id($klid);
		//~ }
	//~ }

	$klid=0; $user_id=0; $uid=0; $bc=false;
	if(isset($_POST['bc'])) {
		if($bc=$db->promocode_validate($_POST['bc'])) {
			if($klid=$db->get_klid_by_bc($bc)) {
				if(!$user_id=$db->get_user_id($klid)) {
					$p=new partnerka($klid,$database);
					$p->ctrl_id=$ctrl_id;
					$p_res=$p->partner_add($klid,'','');
					if(!$user_id=$p_res['user_id'])
						$klid=0;
				}
				$uid=0;
			}
		}
	}


	$vk_uid=0;
	if(!$vk_uid && isset($_POST['vk_uid']))
		$vk_uid=$db->get_uid($_POST['vk_uid']);
	if(!$vk_uid && $db->validate_email($email))
		$vk_uid=$db->dlookup("uid","cards","email='$email'");
	if(!$vk_uid && $db->check_mob($mob) )
		$vk_uid=$db->dlookup("uid","cards","mob_search='$mob'");
//$db->notify_me(print_r($_POST,true));
	if(!$vk_uid) {
		$vk_uid=$db->get_unicum_uid();
		$razdel=4;
		$source_id=12;
//print __LINE__."  ". round((microtime(true)-$mtm)*1000,0)."<br>";	
		$db->query("INSERT INTO cards SET 
				name='".$db->escape($name)."',
				mob='".$db->escape($mob)."',
				mob_search='".$db->escape($mob)."',
				email='".$db->escape($email)."',
				uid='$vk_uid',
				uid_md5='".$db->uid_md5($vk_uid)."',
				acc_id=2,
				razdel='$razdel',
				source_id='$source_id',
				user_id='$user_id',
				utm_affiliate='$klid',
				fl_newmsg=3,
				tm_lastmsg=".time().",
				tm=".time());
//print __LINE__."  ". round((microtime(true)-$mtm)*1000,0)."<br>";	
		$db->save_comm($vk_uid,0,"⭐ Регистрация на форме оплаты: $land_num",12,$user_id);
//print __LINE__."  ". round((microtime(true)-$mtm)*1000,0)."<br>";	
		$db->save_comm($vk_uid,0,$land_name,1000+$land_num);
//print __LINE__."  ". round((microtime(true)-$mtm)*1000,0)."<br>";	
	}
	$uid=$vk_uid;
	if(!$db->hold_chk($uid) && $user_id) {
		$db->query("UPDATE cards SET user_id='$user_id',tm_user_id='".time()."',utm_affiliate='$klid' WHERE uid='$uid'");
	}
	$card_keep=$db->dlookup("card_keep","cards","uid='$uid'");
	$keep=$card_keep ? $card_keep : $keep;
	if(!$keep && $user_id) {
		$db->query("UPDATE cards SET user_id='$user_id',tm_user_id='".time()."',utm_affiliate='$klid' WHERE uid='$uid'");
	}
	if($user_id && $user_id==$db->dlookup("user_id","cards","uid='$uid'")) {
		$db->query("UPDATE cards SET tm_user_id='".time()."' WHERE uid='$uid'");
	}
	//~ if(!$db->dlookup("user_id","cards","uid='$uid' AND del=0") && $user_id) {
		//~ $db->query("UPDATE cards SET user_id='$user_id',utm_affiliate='$klid' WHERE uid='$uid'");
	//~ }
	if(empty($db->dlookup("mob","cards","uid='$uid'")))
		$db->query("UPDATE cards SET mob='".$db->escape($mob)."',mob_search='".$db->escape($mob)."' WHERE uid='$uid'");
	if(empty($db->dlookup("email","cards","uid='$uid'")))
		$db->query("UPDATE cards SET email='".$db->escape($email)."' WHERE uid='$uid'");
	if(!empty($city))
		$db->query("UPDATE cards SET city='".$db->escape($city)."' WHERE uid='$uid'");
	if(!empty($comm))
		$db->save_comm($vk_uid,0,$comm,0,0,0,true);
	if(isset($_POST['tzoffset'])) {
		$tzoffset=intval($_POST['tzoffset']);
		$db->query("UPDATE cards SET tzoffset='$tzoffset' WHERE uid='$uid'");
	}
	if($tm_scdl) {
		$db->query("UPDATE cards SET tm_schedule='$tm_scdl',scdl_web_id='$land_num' WHERE uid='$vk_uid'");
	}
	if($land_razdel) {
		$db->query("UPDATE cards SET razdel='$land_razdel' WHERE uid='$vk_uid'");
	}
	if($land_tag) {
		if(!$db->dlookup("id","tags_op","tag_id='$land_tag' AND uid='$vk_uid'"))
			$db->query("INSERT INTO tags_op SET tag_id='$land_tag',uid='$vk_uid',tm='".time()."'");
	}
	if($land_man_id) {
		if(!$db->dlookup("man_id","cards","uid='$vk_uid'"))
			$db->query("UPDATE cards SET man_id='$land_man_id' WHERE uid='$vk_uid'");
	}


//-------------------------


	$sum=intval($_POST['sum_disp']);
	if(isset($_GET['k'])) {
		$sum*=floatval($_GET['k']);
	}
//print __LINE__."  ". round((microtime(true)-$mtm)*1000,0)."<br>";	

	if($_SESSION['username']!='vlav') { //if($vk_uid!=-1002 && $ctrl_id!=1) {
		$db->save_comm($vk_uid,0,"Зашел на платежную систему: ".$descr,23,$product_id);
//print __LINE__."  ". round((microtime(true)-$mtm)*1000,0)."<br>";	
		$db->notify($vk_uid, "❗ Зашел на платежную систему: ".$base_prices[$product_id]['descr'],'pay');
//print __LINE__."  ". round((microtime(true)-$mtm)*1000,0)."<br>";	
		$db->mark_new($vk_uid,3);
//print __LINE__."  ". round((microtime(true)-$mtm)*1000,0)."<br>";	
	}
//$db->notify_me("HERE_4 $vk_uid $ctrl_id");

	$tm_end_last=$db->fetch_assoc($db->query("SELECT vk_uid,tm_end FROM `avangard` WHERE res=1 AND product_id='$product_id' AND vk_uid='$vk_uid' ORDER BY tm_end DESC LIMIT 1",0))['tm_end'];
	$tm_end_last=($tm_end_last>time()) ? $tm_end_last : time();
	$tm_end=$db->dt2($tm_end_last+(intval($base_prices[$product_id]['term'])*24*60*60) );

	$client_ctrl_id="";
	if($db->database=='vkt') {
		$client_ctrl_id=(isset($_POST['client_ctrl_id'])) ? intval($_POST['client_ctrl_id']) : 0;
		//~ if($client_uid=$db->dlookup("uid","0ctrl","id='$client_ctrl_id'"))
			//~ $vk_uid=$client_uid;
	}

	$fee_1=isset($_POST['fee_1']) ? floatval($_POST['fee_1']) : 0;
	$fee_2=isset($_POST['fee_2']) ? floatval($_POST['fee_2']) : 0;
	if($promocode_id=intval($_POST['promocode_id'])) {
		//~ if($db->dlookup("tm","avangard","res=1 AND promocode_id='$promocode_id' AND vk_uid='$vk_uid'")) {
			//~ print "Error : Promocode is already used by you! <a href='javascript:history.back();' class='' target=''>back</a><br>";
			//~ print "Ошибка : Вы уже использовали этот промокод ранее! <a href='javascript:history.back();' class='' target=''>вернуться</a>";
			//~ exit;
		//~ }
		if($r=$db->fetch_assoc($db->query("SELECT * FROM promocodes WHERE id='$promocode_id'"))) {
			$fee_1=$r['fee_1'];
			$fee_2=$r['fee_2'];
			if($uid != $r['uid']) { //$r['fl_fix_partner'] && 
				$klid=$db->dlookup("id","cards","uid='{$r['uid']}'");
				$user_id=$db->get_user_id($klid);
				if(!$db->hold_chk($uid)) {
					if(!$user_id) { //make uid a partner
						$p=new partnerka($klid,$database);
						$p->ctrl_id=$ctrl_id;
						$user_id=$p->partner_add($klid,$email,$name)['user_id'];
					}
					$hold=$r['hold'] ? $r['hold'] : 1;
					$card_hold_tm=time()+$hold*(24*60*60);
					$keep=$r['keep'];
					$db->query("UPDATE cards SET user_id='$user_id',
												utm_affiliate='$klid',
												tm_user_id='".time()."',
												card_hold_tm='$card_hold_tm',
												card_keep='$keep'
											WHERE uid='$uid'");
				}
			}
		} else {
			$fee_1=0; $fee_2=0;
		}
	} else {
		$fee_1=0; $fee_2=0;
	}
		
	if($promocode_uid=$db->dlookup("uid","promocodes","id='$promocode_id'")) {
		if($klid && $db->dlookup("id","cards","uid='$promocode_uid'") != $klid) {
			$fee_1=0; $fee_2=0;
		}
	}

	$fee_pay=isset($_POST['fee_pay']) ? intval($_POST['fee_pay']) : 0;
	if($fee_pay) {
		$k_fee=0.75;
		$klid=$db->get_klid_by_uid($uid);
		$p=new partnerka($klid,$database);
		$rest_fee=(float)$p->rest_fee($klid,0,time())*$k_fee;
		//$db->notify_me("HERE_".$_SESSION['csrf_token_order']);
		if($fee_pay>$rest_fee) {
			print "Error : fee sum is wrong <a href='javascript:history.back();' class='' target=''>back</a><br>";
			$db->notify_me("pay_common.1.inc.php error - fee sum is wrong - $ctrl_id $fee_pay $rest_fee $uid");
			exit;
		}
	}
	
	$order_id=$db->get_next_avangard_orderid();
	$order_number=$order_id;
	$order_name=$order_id;
	$tm=$tm_pay_cash ? $tm_pay_cash : time();
	$db->query("INSERT INTO avangard SET
				tm='$tm',
				pay_system='".$db->escape($pay_system)."',
				sku='".$db->escape($sku)."',
				product_id='$product_id',
				order_id='$order_id',
				order_number='".$db->escape($order_number)."',
				order_descr='".$db->escape($descr)."',
				ticket='$client_ctrl_id',
				amount='$sum',
				amount1='$sum',
				c_name='".$db->escape($name)."',
				phone='".$db->escape($mob)."',
				email='".$db->escape($email)."',
				vk_uid='$vk_uid',
				res=0,
				land_num='$land_num',
				fee_1='$fee_1',
				fee_2='$fee_2',
				tm_end='$tm_end',
				promocode_id='$promocode_id',
				comm='".$db->escape($comm_avangard)."'
				",0);
	$avangard_id=$db->insert_id();
//print __LINE__."  ". round((microtime(true)-$mtm)*1000,0)."<br>";	
	
	if(!$land_num)
		$land_num=$db->dlookup("land_num","lands","del=0 AND product_id='$product_id'");
	if($land_num) {
		include_once "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
		$s=new vkt_send($database);
		$res=$db->query("SELECT * FROM vkt_send_1 WHERE del=0 AND (sid=23) AND (land_num='$land_num' OR land_num=0)",0);
		while($r=$db->fetch_assoc($res)) {
			$s->vkt_send_task_add($ctrl_id, $tm_event=intval(time()+$r['tm_shift']), $vkt_send_id=$r['id'],$vkt_send_type=3,$uid,$order_id);
		}
	}
	//print "OK ".date("d.m.Y H:i",$tm_end);
	if($sum==0 || $land_num<0) {
		include_once "/var/www/vlav/data/www/wwl/inc/pay_callback_common.1.inc.php";
		include "land_top.inc.php";
		include "init.inc.php";

		//~ if(!$land_num)
			//~ $land_num=$db->dlookup("land_num","lands","del=0 AND product_id='$product_id'");
		//~ if($land_num) {
			//~ include_once "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
			//~ $s=new vkt_send($database);
			//~ $res=$db->query("SELECT * FROM vkt_send_1 WHERE del=0 AND (sid=30 OR sid=31) AND (land_num='$land_num' OR land_num=0)",0);
			//~ while($r=$db->fetch_assoc($res)) {
				//~ if($r['sid']==30)
					//~ $s->vkt_send_task_add($ctrl_id, $tm_event=intval(time()+$r['tm_shift']), $vkt_send_id=$r['id'],$vkt_send_type=3,$uid,$order_id);
				//~ elseif($r['sid']==31 && $tm_end)
					//~ $s->vkt_send_task_add($ctrl_id, $tm_event=intval($tm_end+$r['tm_shift']), $vkt_send_id=$r['id'],$vkt_send_type=3,$uid,$order_id);
			//~ }
			//~ //$db->notify_me("HERE $ctrl_id ");
		//~ }

		$thanks_pic=(file_exists("tg_files/thanks_pic_$land_num.jpg"))?"<img src='tg_files/thanks_pic_$land_num.jpg' class='img-fluid' >":"";
		if(empty($thanks_pic)) {
			$thanks_pic=(file_exists("tg_files/logo200x50.jpg"))?"<img src='tg_files/logo200x50.jpg' class='img-fluid' >":"";
		}
		print "<div class='container my-3 mb-5 text-center' >$thanks_pic</div>";
		if($land_num>=0) {
			$msg="<p>Оплата продукта: <br> <b>$descr</b> <br> прошла успешно!</p>
			<p>Информация будет выслана Вам в ближайшее время.</p>
			";
		} else {
			$msg="<h3>Операция проведена. <a href='pay_cash.php?uid=$uid' class='' target=''>Перейти</a></h3>";
		}
		?>
		<div class='container text-center' >
			<?=$msg?>
		</div>
		<?
		include "land_bottom.inc.php";
		exit;
	}
?>
