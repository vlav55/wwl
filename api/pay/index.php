<?
include "../api.class.php";

if($_SERVER['REQUEST_METHOD']==='POST') {
	include "init.inc.php";
	// $_POST['bc'], $_POST['land_num'], $_POST['client_name'], $_POST['client_phone'],
	//$_POST['client_email'], $_POST['order_number'], $_POST['product_id'], $_POST['product_descr'],
	//$_POST['pay_system'], $_POST['payed_money'], $_POST['payed_at'], $_POST['payed_end'], $_POST['comm']

	//~ (
		//~ [bc] => 0
		//~ [land_num] => 1
		//~ [client_name] => Лилия
		//~ [client_phone] => +79235313108
		//~ [client_email] => minbaeva-lili@mail.ru
		//~ [order_number] => 0391
		//~ [product_id] => 6405453
		//~ [product_descr] => Тестовое предложение
		//~ [pay_system] => webhook.
		//~ [payed_money] => 10 руб.
		// [payed_at]=> timestamp
		// [payed_end]=> timestamp
		//~ [comm] => Завершен
		//secret=md5(land_num+client_name+client_phone+secret_from_profile)
		

	//~ )

	if(!isset($_POST['client_email']) || !$db->validate_email(trim($_POST['client_email']))) {
		print json_encode(['error'=>'client_email missing or incorrect']);
		http_response_code(400);
		exit;
	}
	if(!isset($_POST['order_number']) || empty($_POST['order_number'])) {
		print json_encode(['error'=>'order_number is missing']);
		http_response_code(400);
		exit;
	}
	$order_number=mb_substr(trim($_POST['order_number']),0,32);

	if($db->dlookup("id","avangard","order_number='$order_number'")) {
		print json_encode(['error'=>'order_number is already exists']);
		http_response_code(400);
		exit;
	}

	$_POST['secret']=true;
	$land_num=1;
	if(isset($_POST['land_num']))
		$land_num=intval($_POST['land_num']);

	$bc=0; $klid=0; $user_id=0;
	if(isset($_POST['bc']))
		$bc=intval($_POST['bc']);
	if($bc) {
		$klid=$db->get_klid_by_bc($bc);
		$user_id=$db->get_user_id($klid);
	}

	$tm=time();
	if(isset($_POST['payed_at'])) {
		$tm=intval($_POST['payed_at']);
	}
	$tm_end=0;
	if(isset($_POST['payed_end'])) {
		$tm_end=intval($_POST['payed_end']);
	}

	$card=[
		'tm'=>$tm,
		'uid'=>isset($_POST['uid']) ? $_POST['uid'] : 0,
		'first_name'=>trim($_POST['client_name']),
		'phone'=>isset($_POST['client_phone']) ? $_POST['client_phone'] : "",
		'email'=>isset($_POST['client_email']) ? $_POST['client_email'] : "",
		'city'=>'',
		'tg_id'=>0,
		'tg_nic'=>'',
		'vk_id'=>0,
		'razdel'=>2, //2 
		'source_id'=>0, //0
		'user_id'=>$user_id,
		'klid'=>$klid,
		'wa_allowed'=>'0',
		'comm1'=>'',
		'tz_offset'=>0,
		'test_cyrillic'=>false
	];
	if(!$uid=$db->cards_add($card)) {
		print json_encode(['error'=>'card add error']);
		http_response_code(400);
		exit;
	}

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



	$mob=isset($_POST['client_phone']) ? $db->check_mob($_POST['client_phone']) : "";
	$email=isset($_POST['client_email']) ? strtolower(trim($_POST['client_email'])) : "";
	$email=$db->validate_email($email)?$email : "";
	$name=mb_substr(trim($_POST['client_name']),0,32);

	$fee_1=0; $fee_2=0;
	if(isset($_POST['promocode'])) {
		if(mb_strlen($_POST['promocode'])>64) {
			print json_encode(['error'=>'promocode length exceed 64']);
			http_response_code(400);
			exit;
		}
		if($r=$db->fetch_assoc($db->query("SELECT * FROM promocodes WHERE promocode LIKE '".$db->escape(trim($_POST['promocode']))."'"))) {
			$fee_1=floatval($r['fee_1']);
			$fee_2=floatval($r['fee_2']);
			$fl_fix_partner=$r['fl_fix_partner'];
			$hold=$r['hold'];
			$keep=$r['keep'];
		}
	}

	$sum=round(preg_replace('/[^\d.]/', '', $_POST['payed_money']),0);
	$product_descr=mb_substr(trim($_POST['product_descr']),0,64);
	$pay_system=mb_substr(trim($_POST['pay_system']),0,16);
	$product_id=intval($_POST['product_id']);
	$sku=mb_substr(trim($_POST['sku']),0,32);
	$comm=mb_substr(trim($_POST['comm']),0,1024);
	$db->query("INSERT INTO avangard SET
				tm='$tm',
				pay_system='$pay_system',
				sku='".$db->escape($sku)."',
				product_id='$product_id',
				order_id='0',
				order_number='".$db->escape($order_number)."',
				order_descr='".$db->escape($product_descr)."',
				ticket='$ctrl_id',
				amount='$sum',
				amount1='$sum',
				c_name='".$db->escape($name)."',
				phone='".$db->escape($mob)."',
				email='".$db->escape($email)."',
				vk_uid='$uid',
				res=1,
				land_num='$land_num',
				tm_end='$tm_end',
				comm='".$db->escape($comm)."',
				fee_1='$fee_1',
				fee_2='$fee_2'
				",0);

	if($land_num=$db->dlookup("land_num","lands","del=0 AND product_id='$product_id'")) {
		include_once "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
		$s=new vkt_send($database);
		$res=$db->query("SELECT * FROM vkt_send_1 WHERE del=0 AND (sid=30 OR sid=31) AND (land_num='$land_num' OR land_num=0)",0);
		while($r=$db->fetch_assoc($res)) {
			if($r['sid']==30)
				$s->vkt_send_task_add($ctrl_id, $tm_event=intval(time()+$r['tm_shift']), $vkt_send_id=$r['id'],$vkt_send_type=3,$uid,$order_id);
			elseif($r['sid']==31 && $tm_end)
				$s->vkt_send_task_add($ctrl_id, $tm_event=intval($tm_end+$r['tm_shift']), $vkt_send_id=$r['id'],$vkt_send_type=3,$uid,$order_id);
		}
	}


	$db->save_comm($uid,0,"Зафиксирована оплата по API $pay_system
	Заказ $order_number
	Продукт ($product_id) $product_descr
	Сумма: $sum");

	$db->notify($uid,"Зафиксирована оплата по $pay_system
	заказ $order_number
	продукт ($product_id) $product_descr
	Сумма: $sum");

	http_response_code(200);
	print json_encode(['uid'=>$db->uid_md5($uid)]);
}
?>
