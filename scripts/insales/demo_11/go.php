<?
include "../go.php";
exit;

include_once "/var/www/vlav/data/www/wwl/inc/insales.class.php";
include "insales_app_credentials.inc.php";
$in=new insales($_GET['insales_id'],$_GET['shop']);
$in->id_app=$id_app;
$in->secret_key=$secret_key;
$in->get_credentials();
$in->ctrl_id=167;
//print($in->get_webhook($_GET['insales_id']));

	//~ discount_code[code] * required	code
	//~ discount_code[description]	description
	//~ discount_code[disabled]	disabled
	//~ discount_code[act_once]	act once
	//~ discount_code[act_once_for_client]	act once for client
	//~ discount_code[expired_at]	expired at
	//~ discount_code[type_id] * required	type_id (1 - percent, 2 - money)
	//~ discount_code[discount] * required	discount
	//~ discount_code[min_price]	min price
$r=['code'=>'test2',
	'description'=>'this is a test',
	'disabled'=>false,
	'act_once'=>false,
	'expired_at'=>'2025-03-30',
	'type_id'=>2,
	'discount'=>2555,
	];
//$in->create_promocode($r);

//print_r($in->create_client($name='Ivan', $phone='79119991122', $email='mail@mail.ru', $password = null));
//$in->print_r($in->get_clients($updated_since = null, $from_id = null, $per_page = 10));
$in->print_r($in->search_client(["vlav@mail.ru"],$per_page=2) );
print "ok";
exit;


//~ include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
//~ $db=new vkt('vkt');
//~ $db->telegram_bot=$db->dlookup("tg_bot_notif","0ctrl","id=1");
//~ $db->db200="https://for16.ru/d/1000";

//~ include "insales_app_credentials.inc.php";
//~ include "../insales_func.inc.php";
//~ if(isset($_GET['shop']))
	//~ $shop=$_GET['shop'];
//~ if(isset($_GET['insales_id']))
	//~ $insales_id=$_GET['insales_id'];
//~ $token=trim(file_get_contents("$insales_id.token"));
//~ $passw=md5($token.$secret_key);
//~ $credentials = base64_encode("$id_app:$passw");

//~ print "HERE $insales_id $shop <br>";

//~ print_r(insales_bonus_create($client_id=86396954, $amount=1234, $descr='Бонус при регистрации'));
//~ print_r(insales_get_order($order_id=133303778));
//~ exit;

//создать на вашу почту пользователя и дать полные Права на раздел Расширения

//~ include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
//~ $db=new vkt('vkt');
//~ include "insales_app_credentials.inc.php";
//~ include "../insales_func.inc.php";

include "../go.php";

//$db->print_r(insales_get_account());
//print_r (insales_bonus_create($client_id=85752031, $amount=1234, $descr='Бонус при регистрации'));

exit;

$ctrl_dir=$db->get_ctrl_dir($ctrl_id);
$url="https://for16.ru/d/$ctrl_dir/insales_webhook.php";
insales_webhook_create($url,'orders/update');


?>
