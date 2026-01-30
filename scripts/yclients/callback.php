<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db('vkt');
// Установите заголовки, чтобы указать, что ответ будет в формате JSON
header('Content-Type: application/json');

// Получаем данные из входящего запроса
$input = file_get_contents('php://input');

// Декодируем JSON
$data = json_decode($input, true);

// Проверяем, правильно ли мы получили данные
if (json_last_error() === JSON_ERROR_NONE) {
    // Здесь вы можете обработать данные вебхука
    // Например, выведите данные на экран или выполните какую-то логику
    // Пример: обработка события
	//$db->notify_me(print_r($data,true));
    // Возвращаем статус 200 OK
    http_response_code(200);
    echo json_encode(['status' => 'success']);
} else {
    // Если произошла ошибка при декодировании JSON, возвращаем 400 Bad Request
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
}
print "OK";
?>
