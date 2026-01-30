<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "init.inc.php";
$db=new db($database);

//$db->print_r($_POST); exit;
define("SHOP_ID",  $db->dlookup("yookassa_passw","pay_systems","1"));
define("SHOP_KEY", $db->dlookup("yookassa_secret","pay_systems","1"));
define("RETURN_URL", "yandex.ru"); // ссылка на магазин, куда вернется покупатель после оплаты
//https://for16.ru/d/2660368260/pay_yookassa_callback.php

if(isset($_POST['go_submit'])) {
	$pay_system="yookassa";
	include_once "/var/www/vlav/data/www/wwl/inc/pay_common.1.inc.php";

	//print_r($_POST);

	$_POST['sum']=$sum;
	$_POST['order_id']=$order_id;
	if(!isset($_POST['sum']) || !isset($_POST['order_id'])) {
		die("Sum or order_id not found");
	}
	$create_payment = array(
		"amount"=> array(
			"value"=>$_POST['sum'],
			"currency"=>"RUB",
		),
		'confirmation' => array(
			'type' => 'redirect',
			'return_url' => RETURN_URL,
		),
		"capture"=>true,
		"description"=> "Оплата заказа ".$_POST['order_id']." $descr",
		'metadata' => array(
			'order_id' => $_POST['order_id'],
			// 'product' => "",
		),
		// 'test'=>1,
	);
	$res = connect(
		"https://api.yookassa.ru/v3/payments",
		json_encode($create_payment), 0, 
		array("Authorization: Basic ".base64_encode(SHOP_ID.":".SHOP_KEY), "Idempotence-Key: ".uniqid(), "Content-Type: application/json")
	);
	$json = json_decode($res,1);
	if(!isset($json['confirmation']['confirmation_url'])) {
		//send_tg("<b>Ошибка создания платежа</b>\r\n\r\n<code>".var_export($create_payment, 1)."</code>\r\n\r\n<code>".var_export($json, 1)."</code>");
		die("Ошибка создания платежа.");
	}
	header("Location: {$json['confirmation']['confirmation_url']}");
}

function connect($link, $post=null, $head=1, $header=null, $req=false) {

	$ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL,$link);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
	if($req!==false) 
	//curl_setopt($ch, CURLOPT_PUT, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $req);
	curl_setopt($ch, CURLOPT_HEADER, $head);
	if($header) curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	if($post !== null)
	curl_setopt($ch, CURLOPT_POST, 1);
	if($post !== null)
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $otvet = curl_exec($ch);
    curl_close($ch);
	return $otvet;
}

function get_list_ip($ip_addr_cidr){ $ip_arr = explode("/", $ip_addr_cidr); $bin = ""; for($i=1;$i<=32;$i++) { $bin .= $ip_arr[1] >= $i ? '1' : '0'; } $ip_arr[1] = bindec($bin); $ip = ip2long($ip_arr[0]); $nm = $ip_arr[1]; $nw = ($ip & $nm); $bc = $nw | ~$nm; $bc_long = ip2long(long2ip($bc)); for($zm=1;($nw + $zm)<=($bc_long - 1);$zm++) { $ret[]=long2ip($nw + $zm); } return $ret; } 
?>
