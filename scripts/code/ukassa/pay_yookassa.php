<?php
define("SHOP_ID",  "281662");
define("SHOP_KEY", "live_tconBHuIZEW8DP6v8HYCGFLt1x1IBb2gstNqWwXVd5A");
define("RETURN_URL", "yandex.ru"); // ссылка на магазин, куда вернется покупатель после оплаты

if(isset($_GET['callback'])) {
	$source = file_get_contents('php://input');
	$requestBody = json_decode($source, true);

	if(
		(isset($requestBody['object']) && isset($requestBody['type']) && $requestBody['type']=='notification')
		// || $is_test
	) {

		$netq = array(
			"185.71.76.0/27",
			"185.71.77.0/27",
			"77.75.153.0/25",
			"77.75.154.128/25",
			"206.189.205.251/24",
		);
		$white_adresses = array("77.75.156.11", "77.75.156.35");
		foreach($netq as $cidr) $white_adresses = array_merge($white_adresses, get_list_ip($cidr) );
		if(!in_array($_SERVER['REMOTE_ADDR'], $white_adresses)) { 
			//send_tg("<b>Incorrect IP</b>: " . var_export($requestBody,1) . "\r\n" . $_SERVER['REMOTE_ADDR'] );
			http_response_code(403);
			die($_SERVER['REMOTE_ADDR'] . " blocked");
		}

		$payment = $requestBody['object'];
		$amount			= $payment['amount']['value'];
		$income_amount 	= $payment['income_amount']['value'];

		if(!isset($payment['metadata']['order_id'])) {
			send_tg("<b>Перевод без order_id</b>\r\n\r\n<code>".var_export($requestBody, 1)."</code>");
			die;
		}

		if($payment['test']) {
			send_tg("Тестовый платеж, зачислен не будет!");
			die;
		}

		if($requestBody['event'] != "payment.succeeded") {
			send_tg("⚠️⚠️⚠️ Статус платежа != succeeded, пропускаем!\r\n\r\n".var_export($requestBody,1));
			die;
		}

		send_tg("<b>New transaction</b>: $txn_id\r\nOrder: {$payment['metadata']['order_id']}\r\nSum: $amount\r\n income_amount: $income_amount\r\nСоздана: ".date("d.m.y G:i:s", strtotime($payment['created_at']))."\r\nОплачена: ".date("d.m.y G:i:s", strtotime($payment['captured_at'])));
	}
	http_response_code(403);
	die("No data.");
}


# Создание платежа #
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
	"description"=> "Оплата заказа ".$_POST['order_id'],
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
	send_tg("<b>Ошибка создания платежа</b>\r\n\r\n<code>".var_export($create_payment, 1)."</code>\r\n\r\n<code>".var_export($json, 1)."</code>");
	die("Ошибка создания платежа.");
}
header("Location: {$json['confirmation']['confirmation_url']}");
# Создание платежа #



function send_tg($text) {
	global $user, $sum, $txn_id, $txn_date;
	$token = "862036054:AAHrc0xX5G52ZS67yY89k90Q30LEd5i-ycI";
	$chatid = "-4006998170";
	connect("https://api.telegram.org/bot$token/sendMessage", "chat_id=$chatid&text=".urlencode($text)."&parse_mode=html", 0);
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
