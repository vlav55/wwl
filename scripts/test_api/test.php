<?php
$secret = 'YOUR_SECRET_KEY'; // Замените на свой секретный ключ
$client_id = '123'; // Уникальный идентификатор клиента

// Создаем заголовок авторизации
$credentials = base64_encode("$client_id:$secret");
$headers = [
    "Authorization: Basic $credentials",
    "Content-Type: application/json",
];

// URL вашего API
$url = "https://for16.ru/scripts/test_api/1.php";

// Выполняем POST-запрос
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Выполняем запрос и получаем ответ
$response = curl_exec($ch);

// Проверяем на наличие ошибок
if ($response === false) {
    echo 'CURL Error: ' . curl_error($ch);
} else {
    echo 'Response from API: ' . $response;
}

// Закрываем cURL сессию
curl_close($ch);
?>
