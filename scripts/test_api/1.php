<?php
header('Content-Type: application/json');

// Храните ваш секретный ключ на сервере
$secret = 'YOUR_SECRET_KEY'; // Замените на свой секретный ключ

// Получаем заголовки из запроса
$headers = getallheaders();

// Проверяем наличие базовой аутентификации
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized: No authorization header provided.']);
    exit;
}

// Получаем данные аутентификации
list($type, $credentials) = explode(' ', $headers['Authorization'], 2);
if (strcasecmp($type, 'Basic') != 0) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized: Authorization type not supported.']);
    exit;
}

// Декодируем учетные данные
$decoded = base64_decode($credentials);
list($client_id, $client_secret) = explode(':', $decoded, 2);

// Проверяем секретный ключ
if ($client_secret !== $secret) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized: Invalid secret.']);
    exit;
}

// Если аутентификация успешна, обрабатываем запрос
// Ваш логика API здесь
$responseData = [
    'status' => 'success',
    'message' => 'API request was successful!',
    'client_id' => $client_id, // Возвращаем информацию о клиенте, если нужно
];

// Отправляем ответ
echo json_encode($responseData);
?>
