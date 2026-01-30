<?
$shop="myshop-cqy361.myinsales.ru";
$url = "https://$shop/admin/webhooks.json";
$credentials="";

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
print($response); // Возвращаем ассоциативный массив

?>
