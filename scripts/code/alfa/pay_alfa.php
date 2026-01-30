<?php


if(isset($_GET['callback'])) {
	$secret_seed = "xawP628r}F(BWPr";
	$id = $_POST['id'];
	$sum = $_POST['sum'];
	$clientid = $_POST['clientid'];
	$orderid = $_POST['orderid'];
	$key = $_POST['key'];
	 
	if ($key != md5 ($id.number_format($sum, 2, ".", "")
	 .$clientid.$orderid.$secret_seed))
	{
		echo "Error! Hash mismatch";
		send_tg("Error! Hash mismatch\r\n\r\n".var_export($_POST,1));
		exit;
	}

	send_tg("<b>Успешный платеж</b>\r\n".var_export($_POST,1));

	$result = []; // готовим массив для передачи в pay_callback.php
	$result['payment_status']	=		'success';
	$result['order_id']			=		$_POST['orderid'];
	$result['order_num']		=		$_POST['orderid'];
	$result['payment_system']	=		'alfa';
	$result['commission_sum']	=	0;
	
	# Платеж успешно совершен

	// connect("pay_callback.php", $result); #
	
	die("OK ".md5($id.$secret_seed));
}

$params = ['fio','phone','email','agree','go_submit','vk_uid','product_id','sum_disp','order_number','bc'];
foreach($params as $p) {
	if(!isset($_POST[$p]))	die("Param '$p' not found");
	if(empty($_POST[$p]))	die("Param '$p' is empty");
}

# Логин и пароль от личного кабинета PayKeeper
$user="admin";
$password="29b5f5e973b9"; 

# Basic-авторизация передаётся как base64
$base64=base64_encode("$user:$password"); 
$headers=Array(); 
array_push($headers,'Content-Type: application/x-www-form-urlencoded');

# Подготавливаем заголовок для авторизации
array_push($headers,'Authorization: Basic '.$base64);

# Укажите адрес ВАШЕГО сервера PayKeeper, адрес demo.paykeeper.ru - пример!
$server_paykeeper="https://winwinland.server.paykeeper.ru"; 

# Параметры платежа, сумма - обязательный параметр
# Остальные параметры можно не задавать
$payment_data = array (
	"pay_amount" => $_POST['sum_disp'],
	"clientid" => $_POST['fio'],
	"orderid" => $_POST['order_number'],
	"client_email" => $_POST['email'],
	"service_name" => "Услуга",
	"client_phone" => $_POST['phone'],

	"metadata" => ['a'=>99],
	"my_field2" => "qweqwe",
	"my_field3" => "amama",
);

# Готовим первый запрос на получение токена безопасности
$uri="/info/settings/token/";

# Для сетевых запросов в этом примере используется cURL
$curl=curl_init(); 

curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl,CURLOPT_URL,$server_paykeeper.$uri);
curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'GET');
curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
curl_setopt($curl,CURLOPT_HEADER,false);

# Инициируем запрос к API
$response=curl_exec($curl); 
$php_array=json_decode($response,true);

# В ответе должно быть заполнено поле token, иначе - ошибка
if (isset($php_array['token'])) $token=$php_array['token']; else die();


# Готовим запрос 3.4 JSON API на получение счёта
$uri="/change/invoice/preview/";

# Формируем список POST параметров
$request = http_build_query(array_merge($payment_data, array ('token'=>$token)));

curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl,CURLOPT_URL,$server_paykeeper.$uri);
curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
curl_setopt($curl,CURLOPT_HEADER,false);
curl_setopt($curl,CURLOPT_POSTFIELDS,$request);


$response=json_decode(curl_exec($curl),true);
# В ответе должно быть поле invoice_id, иначе - ошибка
if (isset($response['invoice_id'])) $invoice_id = $response['invoice_id']; else die();

# В этой переменной прямая ссылка на оплату с заданными параметрами
$link = "$server_paykeeper/bill/$invoice_id/";

# Теперь её можно использовать как угодно, например, выводим ссылку на оплату
header("Location: $link");
echo $link;

send_tg("Invoice created: $invoice_id\r\n<pre>".var_export($payment_data,1)."</pre>");


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

?>