<?
require "/var/www/vlav/data/www/wwl/inc/tg_api/vendor/autoload.php";
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";

//Set web hook
//curl -F "url=https://for16.ru/scripts/tg_bot/bot_chk_public.php" https://api.telegram.org/bot6907872687:AAGdxuMuvNX7FaNERXmkMC4HA5qwKGcGfxw/setWebhook

$db=new db('vkt');
$token="6907872687:AAGdxuMuvNX7FaNERXmkMC4HA5qwKGcGfxw";
$bot = new \TelegramBot\Api\Client($token);

//print "HERE $id_admin";

try {
	$bot->command('start', function ($message) use ($bot) {
	});

    //Handle text messages
    $bot->on(function (\TelegramBot\Api\Types\Update $update) use ($bot) {
        $message = $update->getMessage();
        if($message) {
			$id = $message->getChat()->getId();
			$user_name=$message->getChat()->getUsername();
			if($m=$message->getNewChatMembers()) {
				$tg_id=$m[0]->getId();
				$tg_nic=$m[0]->getUsername();
				$f_name=$m[0]->getFirstName();
				$l_name=$m[0]->getLastName();
				//$bot->sendMessage(315058329, "New chat member: $tg_id $tg_nic $f_name $l_name");
				$msg="$f_name $l_name - приветствуем вас в чате кандидатов для работы в отделе продаж ООО \"ВИНВИНЛЭНД\"
ВИНВИНЛЭНД - это сервис, с помощью которого любая компания или частный эксперт может оцифровать свою собственную партнерскую программу, что увеличит их продажи.

В вашей будущей работе важно то, что вы не продаете сервис, а предлагаете купить их продукцию, открываете своим клиентам новые рынки сбыта.

Всю информацию и ссылки на видео, которые нужно посмотреть, можно найти в документе https://winwinland.ru/pdf/winwinland_job.pdf
";
				//$bot->sendMessage(-4191898172, $msg);
				file_put_contents("job_chat_members.txt",date("d.m.Y H:i:s")." New chat member: $tg_id $tg_nic $f_name $l_name\n",FILE_APPEND);
			}
		}
		//$bot->sendMessage($id, "ok");
    }, function () {
        return true;
    });
    
    $bot->run();

} catch (\TelegramBot\Api\Exception $e) {
    $e->getMessage();
}
print "ok";
?>
