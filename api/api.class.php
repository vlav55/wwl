<?php
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
/*
200 OK — Штатный ответ, указывающий на успешное выполнение запроса.
201 Created — Может возвращаться при создании новых объектов, также означает успешную операцию.
400 Bad Request — Некорректный HTTP-запрос, сервер не смог его обработать (например, слишком большой заголовок).
401 Unauthorized — Ошибка авторизации; необходимо проверить данные для Basic-авторизации приложения.
403 Forbidden — Нет прав для выполнения данной операции; проверьте настройки прав доступа для вашего приложения.
404 Not Found — Запрошенный объект не найден, возможно, его удалили или ID указан ошибочно.
429 Too Many Requests — Превышены лимиты на API-запросы; если лимитов не хватает, стоит обратиться в поддержку.
500 Internal Server Error — Внутренняя ошибка сервераnsales; возможно, проблема в некорректном формировании данных в запросе.
504 Gateway Timeout — Сервер не успел обработать запрос за установленное время (50 секунд); может быть вызвано слишком большим объемом запрашиваемых данных. 
*/
class api extends vkt {
    private $secret;

    public function __construct()
    {
        header('Content-Type: application/json');
    }

    public function authenticate()
    {
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
        list($ctrl_id_encoded, $client_secret) = explode(':', $decoded, 2);
        $ctrl_id=$this->decode_ctrl_id(trim($ctrl_id_encoded));
        if(!$ctrl_id) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized: invalid login.']);
            exit;
		}
        $this->connect('vkt');
        $this->secret=$this->dlookup("api_secret","0ctrl","id='$ctrl_id'");
        $this->secret=empty($this->secret) ? $this->get_api_secret($ctrl_id) : $this->secret;

        // Проверяем секретный ключ
        if ($client_secret !== $this->secret) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized: Invalid secret.']);
            $this->notify_me("api Unauthorized: Invalid secret ,'ctrl_id'=>$ctrl_id,'secret'=>$this->secret]");
            exit;
        }
        return $ctrl_id; // Возвращаем ctrl_id для дальнейшего использования
    }
    public function log($msg="") {
		$out=date("d.m.Y H:i:s")."\n--GET--\n".print_r($_GET,true)."\n--POST--\n".print_r($_POST,true)."\n--END--\n";
		file_put_contents("dapi_.log",$out,FILE_APPEND);
	}
}
$api = new api();
$ctrl_id = $api->authenticate();
$db=new vkt('vkt');

$r=$db->fetch_assoc($db->query("SELECT * FROM 0ctrl WHERE id='$ctrl_id'"));
$ctrl_dir=$r['ctrl_dir'];
$tg_bot_notif=$r['tg_bot_notif'];

chdir("/var/www/vlav/data/www/wwl/d/$ctrl_dir");
$database=$db->get_ctrl_database($ctrl_id);
$db->connect($database);
$db->telegram_bot=$tg_bot_notif;
$db->db200="https://for16.ru/d/$ctrl_dir";
$api->log();
?>
