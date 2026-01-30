<?
//if(in_array($ctrl_id,[198,199]))
file_put_contents("pay_webhook.log",date("d/m/Y H:i:s")."\n".print_r($_POST,true)."\n\n",FILE_APPEND);
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "init.inc.php";

$db=new db($database);

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


if(!sizeof($_POST)) {
	print "err: POST is empty";
	exit;
}
if(empty(trim($_POST['client_email']))) {
	print "err: email is not specified";
	exit;
}

if($_POST['secret']!=md5($_POST['land_num'].$_POST['client_name'].$_POST['client_phone'].$db->get_api_secret($ctrl_id))) {
	print "err: secret is not match";
	exit;
}


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

$order_number=mb_substr(trim($_POST['order_number']),0,32);
if(empty($order_number)) {
	print "err - order number is empty";
	exit;
}

if($db->dlookup("id","avangard","order_number='$order_number'")) {
	print "err - order_number=$order_number already exists";
	exit;
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
	'uid'=>0, //если не найдет в базе то выход с ошибкой
	'first_name'=>trim($_POST['client_name']),
	'phone'=>$_POST['client_phone'],
	'email'=>$_POST['client_email'],
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
	//$db->notify_me("pay_POSTcourse - cards_add error");
	exit;
}
$db->save_comm($uid,0,"Получена оплата по ВЕБХУК.
Заказ: ".$_POST['order_number']."
Продукт: ".$_POST['product_id']."
Название: ".$_POST['product_descr']."
Статус:".$_POST['status']."
Сумма: ".$_POST['payed_money']);

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

$sum=round(preg_replace('/[^\d.]/', '', $_POST['payed_money']),0);
$product_descr=mb_substr(trim($_POST['product_descr']),0,64);
$pay_system=mb_substr(trim($_POST['pay_system']),0,16);
$product_id=intval($_POST['product_id']);
$sku=mb_substr(trim($_POST['sku']),0,32);
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
			c_name='".$db->escape($name)."',
			phone='".$db->escape($mob)."',
			email='".$db->escape($email)."',
			vk_uid='$uid',
			res=1,
			land_num='$land_num',
			tm_end='$tm_end',
			comm='".$db->escape($comm)."'
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


$db->notify($uid,"Зафиксирована оплата по $pay_system
заказ $order_number
продукт ($product_id) $product_descr
Сумма: $sum");

print "ok";
?>
