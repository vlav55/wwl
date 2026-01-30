<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "init.inc.php";
$db=new db($database);

//$db->print_r($_POST); exit;

if(isset($_POST['go_submit'])) {
	$pay_system="paykeeper";
	include_once "/var/www/vlav/data/www/wwl/inc/pay_common.1.inc.php";

	//~ $params = ['fio','phone','email','go_submit','vk_uid','product_id','sum_disp','order_number','bc'];
	//~ foreach($params as $p) {
		//~ if(!isset($_POST[$p]))	die("Param '$p' not found");
		//~ if(empty($_POST[$p]))	die("Param '$p' is empty");
	//~ }

	# Логин и пароль от личного кабинета PayKeeper
	$user="admin";
	$password=$db->dlookup("alfa_passw","pay_systems","1"); //"29b5f5e973b9"; 

	# Basic-авторизация передаётся как base64
	$base64=base64_encode("$user:$password"); 
	$headers=Array(); 
	array_push($headers,'Content-Type: application/x-www-form-urlencoded');

	# Подготавливаем заголовок для авторизации
	array_push($headers,'Authorization: Basic '.$base64);

	# Укажите адрес ВАШЕГО сервера PayKeeper, адрес demo.paykeeper.ru - пример!
	$server_paykeeper=trim($db->dlookup("alfa_url","pay_systems","1")); //"https://winwinland.server.paykeeper.ru"; 

	# Параметры платежа, сумма - обязательный параметр
	# Остальные параметры можно не задавать

	$cart[]=['name' => $descr, //	Наименование экземпляра товара. Строка, не более 128 символов длиной.	Да
		'price' => $sum, //	Стоимость единичного экземпляра товара	Да
		'quantity' => 1, //	Количество экземпляров товара в данной позиции	Да
		'sum' => $sum, //	Полная сумма к уплате по данной товарной позиции, включая НДС	Да
		'tax' => 'none', //	Код налога, применяющегося к данной позиции	Да
		'item_type' => 'service', //
	];
	//$db->notify_me(print_r($cart,true)); exit;
	$service_name=['cart'=>$cart,
			'receipt_properties'=> null,
			'lang'=>'ru',
			'user_result_callback'=> null,	//Параметр user_result_callback платежа
			'service_name' => '', //	При платеже будет отображаться в качестве наименования услуги];
	];
	$payment_data = array (
		"pay_amount" => $sum,
		"clientid" => $name,
		"orderid" => $order_id,
		"client_email" => $email,
		"service_name" => json_encode($service_name),
		"client_phone" => $mob,

		"metadata" => ['a'=>99],
		"my_field2" => $vk_uid,
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

if ($response === false) {
    $error = curl_error($curl);
    echo "cURL Error: $error";
} 

	# В ответе должно быть заполнено поле token, иначе - ошибка
	if (isset($php_array['token'])) $token=$php_array['token']; else die("1");


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

	if (isset($response['invoice_id'])) $invoice_id = $response['invoice_id']; else die("2");

	# В этой переменной прямая ссылка на оплату с заданными параметрами
	$link = "$server_paykeeper/bill/$invoice_id/";

	# Теперь её можно использовать как угодно, например, выводим ссылку на оплату
	header("Location: $link");
	echo $link;



	exit;
}
?>
