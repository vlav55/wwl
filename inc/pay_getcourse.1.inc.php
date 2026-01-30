<?
http_response_code(200);
$txt=print_r($_GET,true)."\n\n".$txt;
file_put_contents("pay_getcourse.log",date("d/m/Y H:i:s")."\n".print_r($_GET,true)."\n\n",FILE_APPEND);
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "init.inc.php";

$db=new db($database);

//~ (
    //~ [first_name] => Лилия
    //~ [last_name] => 
    //~ [phone] => +79235313108
    //~ [email] => minbaeva-lili@mail.ru
    //~ [order_number] => 0391
    //~ [offers] => 6405453
    //~ [positions] => Тестовое предложение
    //~ [payed_money] => 10 руб.
    //~ [status] => Завершен
//~ )


//~ Array
//~ (
    //~ [first_name] => Лилия
    //~ [last_name] => 
    //~ [phone] => +79235313108
    //~ [email] => minbaeva-lili@mail.ru
    //~ [order_number] => 0392
    //~ [offers] => 6405453
    //~ [positions] => Тестовое предложение
    //~ [payed_money] => 5 руб.
    //~ [status] => Частично оплачен
//~ )

if(!sizeof($_GET)) {
	print "error";
	exit;
}
if(empty(trim($_GET['phone']))) {
	print "error";
	exit;
}
if(empty(trim($_GET['email']))) {
	print "error";
	exit;
}

$order_number=mb_substr(trim($_GET['order_number']),0,32);
if(empty($order_number)) {
	print "error - order number is empty";
	exit;
}

if($db->dlookup("id","avangard","order_number='$order_number'")) {
	print "error - order_number=$order_number already exists";
	exit;
}

$tm=time();
if(isset($_GET['payed_at'])) {
	if($tm=strtotime($_GET['payed_at'])) {
		if($db->dt1($tm)==$db->d1(time()))
			$tm=time();
	}
}

$card=[
	'tm'=>$tm,
	'uid'=>0, //если не найдет в базе то выход с ошибкой
	'first_name'=>$_GET['first_name'],
	'last_name'=>$_GET['last_name'],
	'phone'=>$_GET['phone'],
	'email'=>$_GET['email'],
	'city'=>'',
	'tg_id'=>0,
	'tg_nic'=>'',
	'vk_id'=>0,
	'razdel'=>2, //2 
	'source_id'=>0, //0
	'user_id'=>'0',
	'klid'=>'0',
	'wa_allowed'=>'0',
	'comm1'=>'',
	'tz_offset'=>0,
	'test_cyrillic'=>false
];
if(!$uid=$db->cards_add($card)) {
	//$db->notify_me("pay_getcourse - cards_add error");
	exit;
}
$db->save_comm($uid,0,"Получена оплата в Геткурс.
Заказ: ".$_GET['order_number']."
Продукт: ".$_GET['offers']."
Название: ".$_GET['positions']."
Статус:".$_GET['status']."
Сумма: ".$_GET['payed_money']);


$mob=isset($_GET['phone']) ? $db->check_mob($_GET['phone']) : "";
$email=isset($_GET['email']) ? strtolower(trim($_GET['email'])) : "";
$email=$db->validate_email($email)?$email : "";
$first_name=mb_substr($_GET['first_name'],0,32);
$last_name=mb_substr($_GET['last_name'],0,32);

$sum=intval(preg_replace('/\D/', '', $_GET['payed_money']));
$positions=mb_substr(trim($_GET['positions']),0,64);
$offers=intval($_GET['offers']);
$db->query("INSERT INTO avangard SET
			tm='$tm',
			pay_system='getcourse',
			sku='',
			product_id='$offers',
			order_id='0',
			order_number='".$db->escape($order_number)."',
			order_descr='".$db->escape($positions)."',
			ticket='$ctrl_id',
			amount='$sum',
			c_name='".trim($db->escape($first_name)." ".$db->escape($last_name))."',
			phone='".$db->escape($mob)."',
			email='".$db->escape($email)."',
			vk_uid='$uid',
			res=1,
			land_num='0',
			tm_end='0'
			",0);


print "ok";
?>
