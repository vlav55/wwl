<?
if (!isset($_REQUEST)) {
return;
}

include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include_once "init.inc.php";
$db=new db($database);
$db->telegram_bot=$tg_bot_notif;
$db->db200=$DB200;


//Строка для подтверждения адреса сервера из настроек Callback API
$confirmation_token = $vk_confirmation_token;
//$confirmation_token = '49ee4d44';

//Ключ доступа сообщества
$token = $db->dlookup("token","vklist_acc","id=2");;

//Получаем и декодируем уведомление
$data = json_decode(file_get_contents('php://input'));

//Проверяем, что находится в поле "type"
switch ($data->type) {
		//Если это уведомление для подтверждения адреса...
		case 'confirmation':
		//...отправляем строку для подтверждения
		echo $confirmation_token;
	break;

	//Если это уведомление о новом сообщении...
	case 'message_new':
		//...получаем id его автора
		$user_id = $data->object->message->from_id;
		$msg=$data->object->message->text;
		//затем с помощью users.get получаем данные об авторе

		if($db->is_md5($msg)) {
			if($uid=$db->get_uid($msg)) {
				$db->query("UPDATE cards SET vk_id='$user_id' WHERE uid='$uid'");
				$db->notify_me("$uid $user_id");
			}
		}
		
		$uid=$db->get_uid($user_id);

		$fl_notify=true;
		$fl_save=true;
		if(file_exists("bot.inc.php")) {
			$fl_vk=true;
			include_once("bot.inc.php");
		}


		if($fl_notify) {
			$db->mark_new($uid,2);
			$db->notify($uid,'VK: '.$msg,'msg');
		}

		if(file_exists("vk_callback.inc.php"))
			include "vk_callback.inc.php";

		if($user_id == 198746774_123) {
			$user_info = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$user_id}&access_token={$token}&v=5.103"));

			//и извлекаем из ответа его имя
			$user_name = $user_info->response[0]->first_name;

			//С помощью messages.send отправляем ответное сообщение
			$request_params = array(
			'message' => 'HERE_'.$tg_bot_notif, //$data->object->message->text, //print_r($data->object->message,true),
			'peer_id' => $user_id,
			'access_token' => $token,
			'v' => '5.103',
			'random_id' => '0'
			); 

			$get_params = http_build_query($request_params);

			//file_get_contents('https://api.vk.com/method/messages.send?'. $get_params);
		}
		//Возвращаем "ok" серверу Callback API

		echo('ok');

	break; 
} 
?>
