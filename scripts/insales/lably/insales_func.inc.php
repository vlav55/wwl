<?
function err($arr) {
	global $db;
	$db->notify_me("INSALES ERROR in db=$db->database\n".print_r($arr,true));
	print_r($arr);
	return $arr;
}
function insales_get_account() {
    global $db,$shop,$credentials,$passw;

    print file_get_contents("http://winwinland:4dbbcb57789ffef5d434aef8a83d297c@myshop-cpc885.myinsales.ru/admin/account.xml");
    exit;

    // Формируем URL для запроса данных аккаунта
    $url = "https://$shop/admin/account.json";

    // Инициализируем cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Basic $credentials",
        "Content-Type: application/json",
    ]);

    // Выполняем запрос
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Проверяем успешность выполнения запроса
    if ($httpCode == 200) {
        // Декодируем ответ JSON
        return json_decode($response, true); // Возвращаем ассоциативный массив
    } else {
        return err([
            'error' => true,
            'message' => 'Failed to get account info',
            'http_code' => $httpCode,
            'response' => $response,
        ]);
    }
}


function insales_get_webhooks() {
    global $db,$shop,$credentials;

    // Формируем URL для запроса всех вебхуков
    $url = "https://$shop/admin/webhooks.json";

    // Инициализируем cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Basic $credentials",
        "Content-Type: application/json",
    ]);

    // Выполняем запрос
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Проверяем успешность выполнения запроса
    if ($httpCode == 200) {
        // Декодируем полученный JSON в ассоциативный массив
        return json_decode($response, true); // Возвращаем ассоциативный массив
    } else {
        return err([
            'error' => true,
            'message' => 'Failed to get webhooks.',
            'http_code' => $httpCode,
            'response' => $response,
        ]);
    }
}

function insales_get_webhook($insales_id) {
    global $db,$ctrl_id,$shop,$credentials;
	$res=insales_get_webhooks();
	$ctrl_dir=$db->get_ctrl_dir($ctrl_id);
	$url="https://for16.ru/d/$ctrl_dir/insales_webhook.php";
	foreach($res AS $r) {
		if($r['address']==$url)
			return $r['id'];
	}
	return false;
}

function insales_webhook_del($webhook_id) {
    global $db,$shop, $credentials;

    // Формируем URL для удаления вебхука
    $url = "https://$shop/admin/webhooks/$webhook_id.json";

    // Инициализируем cURL для удаления вебхука
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Basic $credentials",
        "Content-Type: application/json",
    ]);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

    // Выполняем запрос на удаление
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Проверяем код ответа
    if ($httpCode == 200) {
        return true; // Вебхук успешно удалён
    } else {
        return err([
            'error' => true,
            'message' => 'Failed to delete webhook.',
            'http_code' => $httpCode,
            'response' => $response,
        ]);
    }
}

function insales_webhook_create($url, $event) {
    global $db,$shop,$credentials; // Access the global credentials variable
    
    // Prepare the API endpoint for creating a webhook
    $apiUrl = "https://$shop/admin/webhooks.json"; // Replace with your InSales store URL
    // Prepare the webhook data
    $webhookData = [
        'webhook' => [
            'address' => $url, // The URL to receive the webhook notifications
            'topic' => $event, // The event to subscribe to
            "format_type" => "json"
        ],
    ];

    // Initialize cURL
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Basic $credentials",
        "Content-Type: application/json",
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhookData));

    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Check if the webhook was created successfully
    if ($httpCode == 201) {
        return true;
    } else {
        return err([
            'error' => true,
            'message' => 'Failed to create webhook.',
            'http_code' => $httpCode,
            'response' => $response,
        ]);
    }
}

function insales_get_order($order_id) {
    global $shop,$credentials,$passw; // Access the global credentials variable
	$url = "https://$shop/admin/orders/$order_id.json"; // пример URL
	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		"Authorization: Basic $credentials"
	]);

	$response = curl_exec($ch);
	curl_close($ch);
	return json_decode($response,true);
}


?>
