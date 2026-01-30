<?
require "/var/www/vlav/data/www/wwl/inc/tg_api/vendor/autoload.php";
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "init.inc.php";

//Set web hook
//curl -F "url=https://for16.ru/d/1000/tg_bot.php" https://api.telegram.org/bot1451314745:AAHLX4MAf3M008jcAtWiQPCgJDi1-IZr28k/setWebhook

$db=new db('vkt');
//$token=$tg_bot_msg;//$db->dlookup("tg_bot_msg","0ctrl","id=$ctrl_id");
$bot = new \TelegramBot\Api\Client($tg_bot_msg);
$id_admin=$db->get_tg_id($uid_admin);
//$db->notify_me("db=$database ctrl_id=$ctrl_id $cwd ".getcwd());
//print "HERE $id_admin";

try {
	$bot->command('start', function ($message) use ($bot) {
		global $ctrl_dir,$ctrl_id,$database,$DB200,$bot_first_msg_p,$bot_first_msg;
		$id=$message->getChat()->getId();
		$f_name=$message->getChat()->getFirstName();
		$l_name=$message->getChat()->getLastName();
		$user_name=$message->getChat()->getUsername();
		$bot->sendMessage($id, $message->getText());
		$bot->sendMessage($id, "Добро пожаловать!");
		//$bot->sendMessage($id,"here $ctrl_dir");
		if(preg_match("/\/start ([0-9]{3,5})$/",$message->getText(),$m)) { //tg code received
			$code=intval($m[1]);
		//$db=new db('vkt');
		$db=new db($database);
			$uid=$db->dlookup("uid","telegram","code='$code'");
		//$ctrl_dir=$db->dlookup("ctrl_dir","telegram","code='$code'");
			if(!$land_num=$db->dlookup("user_id","telegram","code='$code'"))
				$land_num=1;
		//chdir("/var/www/vlav/data/www/wwl/d/$ctrl_dir/");
			//$db->notify_me("HERE_$ctrl_dir $dir ".getcwd());
		//include "init.inc.php";
			//$db->notify_me("db=$database uid=$uid code=$code $ctrl_dir");
			if($uid) { 
		//$db=new db($database);
		//$db->db200=$DB200;
				if($db->dlookup("id","lands","land_num='$land_num' AND del=0")) {
					$db->ctrl_id=$ctrl_id;
					$bot_first_msg=$db->dlookup("bot_first_msg","lands","land_num='$land_num' AND del=0");
					$bot_first_msg_p=$bot_first_msg;
				} else
					$bot_first_msg="";
 	//$bot->sendMessage(315058329, "HERE $database code=$code land_num=$land_num uid=$uid");
			//$db->notify_me("HERE_$uid");
				if(!$crm_id=$db->dlookup("telegram_id","cards","del=0 AND uid='$uid'")) {
					if(!$db->dlookup("id","cards","del=0 AND telegram_id='$id' ")) { //not already in cards
						$db->query("UPDATE cards SET telegram_id='$id',telegram_nic='".$db->escape($user_name)."' WHERE uid='$uid'");
						$klid=$db->dlookup("id","cards","uid='$uid'");
						$db->query("UPDATE users SET telegram_id='$id' WHERE klid='$klid'");
						$crm_id=$id;
					} else
						$bot->sendMessage($id, "Телеграм уже привязан к другой карточке. Для отмены привязки и смены ТГ обратитесь к администратору");
				} elseif($id != $crm_id) { //tg changed
					$bot->sendMessage($id, "Эта карточка уже зарегистрирована на другой телеграм. Для отмены привязки и смены ТГ обратитесь к администратору");
				}
				$id=$crm_id;
				$client_name=$db->dlookup("name","cards","uid='$uid'");
				//~ if($db->dlookup("confirmed","telegram","code='$code'")==1) { //partner registered first message
					//~ $msg=$db->dlookup("bot_first_msg","lands","land_num='$land_num' AND del=0");
				//~ } else {
					//~ $msg=$bot_first_msg;
				//~ }
				$db->db200=$DB200;
				$db->ctrl_id=$ctrl_id;
				//$msg_p=$db->prepare_msg($uid,$msg);
			$msg_p=$db->prepare_msg($uid,$bot_first_msg);
				$bot->sendMessage($id, $msg_p);
				$db->query("INSERT INTO msgs SET
							uid='$uid',
							acc_id=103,
							tm=".time().",
							msg='".$db->escape($msg_p)."',
							outg=1
							");
			} else {
				//$bot->sendMessage($id, "Ошибка, сообщите в техподдержку ");
				$db->vkt_email("VKT TELEGRAM ERROR: tg_bot tg_id=$id ctrl_id=$ctrl_id - code=$code не найден","");
				sleep(10);
			}
	//$bot->sendMessage(315058329, "HERE $database DB200=$DB200 code=$code land_num=$land_num uid=$uid");
		} elseif(preg_match("/\/start ([a-f0-9]{32})$/i",$message->getText(),$m)) { //start uid_md5
			$db=new db($database);
			$uid=$db->dlookup("uid","cards","uid_md5='{$m[1]}'");
			if($uid) {
				//$bot->sendMessage($id, "uid=$uid");
				if(!$crm_id=$db->dlookup("telegram_id","cards","uid='$uid'")) {
					$db->query("UPDATE cards SET telegram_id='$id',telegram_nic='".$db->escape($user_name)."' WHERE uid='$uid'");
					$klid=$db->dlookup("id","cards","uid='$uid'");
					$db->query("UPDATE users SET telegram_id='$id' WHERE klid='$klid'");
					$name=$db->dlookup("name","cards","uid='$uid'");
					$bot->sendMessage($id, "Здравствуйте $name\nУспешная регистрация.");
				} elseif($id == $crm_id) {
					$klid=$db->dlookup("id","cards","uid='$uid'");
					$db->query("UPDATE users SET telegram_id='$id' WHERE klid='$klid'");
					$name=$db->dlookup("name","cards","uid='$uid'");
					$bot->sendMessage($id, "Здравствуйте $name\nУспешная регистрация.");
				} else { //tg changed
					$bot->sendMessage($id, "Аккаунт зарегистрирован на другой телеграм ID. Для отмены привязки и смены ТГ обратитесь к администратору");
				}
			} elseif($m[1] == md5('admin')) {
				$bot->sendMessage($id, "admin");
				$db->query("UPDATE users SET telegram_id='$id' WHERE id='3'");
				$bot->sendMessage($id, "Здравствуйте admin\n Служебный бот подключен.");
			} else {
				$bot->sendMessage($id, "{$m[1]} не найден");
				$db->vkt_email("TELEGRAM ERROR: md5 {$m[1]} не найден","");
			}
		} elseif(preg_match("/\/start u([0-9]+)$/i",$message->getText(),$m)) { //start u Служебный ТГ бот для уведомлений из CRM
			$db=new db($database);
			$user_id=$db->dlookup("id","users","id=".intval($m[1]));
			if($user_id) {
				$db->query("UPDATE users SET telegram_id='$id' WHERE id='$user_id'");
				$bot->sendMessage($id, "Служебный бот подключен к вашему телеграм. Проверьте также, чтобы вы были подписаны на него!");
			} else {
				$bot->sendMessage($id, "Error: {$m[1]} is not found");
			}
		}  elseif(preg_match("/\/start ask_support_([\d]+)/",$message->getText(),$m)) { //
			$ask_ctrl_id=intval($m[1]);
			$db=new db($database);
			if($uid=$db->dlookup("uid","cards","telegram_id='$id'")) {
				$bot->sendMessage($id, "Здравствуйте это техподдержка ВИНВИНЛЭНД, какой у вас вопрос?");
				$db->notify($uid,"запрос в техподдержку от компании $ask_ctrl_id");
			} else {
				$db->notify_me("Запрос в техподдержку от ctrl_id=$ask_ctrl_id tg_id=$id (not found in vkt)");
				$bot->sendMessage($id, "Чтобы подключиться к чату с техподдержкой зарегистрируйтесь по ссылке https://wwl.winwinland.ru/51/?ctrl_id=$ask_ctrl_id");
			}
		}  elseif(preg_match("/\/start getid/",$message->getText(),$m)) { //
			$bot->sendMessage($id, $id);
		}  elseif(preg_match("/\/start cashout/",$message->getText(),$m)) { //
			$db=new db($database);
			if($uid=$db->dlookup("uid","cards","telegram_id='$id'")) {
				include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
				$klid=$db->get_klid_by_uid($uid);
				$p=new partnerka($klid,$database);
				$fee=$p->rest_fee($klid);
				$bot->sendMessage($id, "Здравствуйте. Пожалуйста, напишите здесь ваш запрос на вывод средств.");
				$bot->sendMessage($id, "Доступно к выводу: $fee р.");
			}
		} 
	});
	$bot->command('1', function ($message) use ($bot) {
		global $ctrl_dir,$ctrl_id,$database,$DB200,$bot_first_msg_p,$bot_first_msg;
		$id=$message->getChat()->getId();
		$f_name=$message->getChat()->getFirstName();
		$l_name=$message->getChat()->getLastName();
		$user_name=$message->getChat()->getUsername();
		$bot->sendMessage($id, $message->getText());
		$db=new db($database);
		$db->db200=$DB200;
		if($uid=$db->dlookup("uid","cards","telegram_id='$id'")) {
			$land_num=$db->fetch_assoc($db->query("SELECT land_num
					FROM lands WHERE del=0 AND fl_partner_land=1 AND land_num>0 ORDER BY land_num"))['land_num'];
			if($land_num) {
				//$bot->sendMessage($id, "uid=$uid land_num=$land_num");
				if($db->is_partner_db($uid)) {
					$msg=$db->dlookup("bot_first_msg","lands","land_num='$land_num' AND del=0");
					$db->ctrl_id=$ctrl_id;
					$bot->sendMessage($id, $db->prepare_msg($uid,$msg));
					//~ $bot->sendMessage($id,"Ваш партнерский код: ".$db->prepare_msg($uid,"{{partner_code}}"));
					//~ $bot->sendMessage($id,"Кабинет партнера: ".$DB200.$db->prepare_msg($uid,"{{cabinet_link}}"));
				} else
					$bot->sendMessage($id, "Вы не зарегистрированы в партнерской программе, отправьте запрос администратору, ответив на это сообщение");
			}
		} else
			$bot->sendMessage($id, "Извините, ваш телеграм не был указан при регистрации в партнерской программе, обратитесь к представителю компании");
	});
	$bot->command('2', function ($message) use ($bot) {
		global $ctrl_dir,$ctrl_id,$database,$DB200,$bot_first_msg_p,$bot_first_msg;
		$id=$message->getChat()->getId();
		$f_name=$message->getChat()->getFirstName();
		$l_name=$message->getChat()->getLastName();
		$user_name=$message->getChat()->getUsername();
		$bot->sendMessage($id, $message->getText());
		$db=new db('vkt');
		$db->db200=$DB200;
		if($r=$db->fetch_assoc($db->query("SELECT admin_passw,ctrl_dir FROM 0ctrl JOIN cards ON cards.uid=0ctrl.uid WHERE cards.del=0 AND 0ctrl.del=0 AND telegram_id='$id'"))) {
			$admin_passw=$r['admin_passw'];
			$ctrl_dir=$r['ctrl_dir'];
			$msg="Доступ в ваш аккаунт ВИНВИНЛЭНД:
			https://for16.ru/d/$ctrl_dir/cp.php?view=yes&filter=new
			admin
			$admin_passw
			";
			$bot->sendMessage($id, $msg);
		} else
			$bot->sendMessage($id, "Извините, у вас нет клиентского аккаунта");
	});
	$bot->command('3', function ($message) use ($bot) {
		$id=$message->getChat()->getId();
		$bot->sendMessage($id, "id=".$id);
	});

    //Handle text messages
    $bot->on(function (\TelegramBot\Api\Types\Update $update) use ($bot) {
		global $database, $ctrl_dir,$id_admin,$tg_bot_notif,$tg_bot_msg,$DB200;
		$url_attach="https://for16.ru/d/"."$ctrl_dir";
		$db=new db($database);
        $message = $update->getMessage();
        if($message) {
			$id = $message->getChat()->getId();
			$user_name=$message->getChat()->getUsername();
			$msg="";
			$fname=false;
			//$bot->sendMessage($id, "HERE_$id_admin");

			$voice=$message->getVoice();
			if($voice) {
				$file_id=$voice->getFileId();
				$file = $bot->getFile($file_id);
				$ext=pathinfo($file->getFilePath())['extension'];
				$fname='tg_files/'.$id.'_'.time().'.'.$ext;
				file_put_contents($fname,$bot->downloadFile($file_id));
				$msg.="ГОЛОСОВОЕ $url_attach/$fname";
			}

			$pic=$message->getPhoto();
			if($pic) {
				$file_id=$pic[sizeof($pic)-1]->getFileId();
				$file = $bot->getFile($file_id);
				$ext=pathinfo($file->getFilePath())['extension'];
				$fname='tg_files/'.$id.'_'.time().'.'.$ext;
				file_put_contents($fname,$bot->downloadFile($file_id));
				$msg.="ФОТО $url_attach/$fname";
			}

			$uid=$db->dlookup("uid","cards","del=0 AND telegram_id='$id'");
			//$bot->sendMessage($id, $message->getText()." - id= $id uid=$uid");
			$msg.=$message->getText();
   // $db->notify_me("HERE_$database $uid $msg");
			if($uid) {

				$fl_notify=true;
				$fl_save=true;
				//$klid=$db->get_klid_by_uid($uid);
				
				if($db->dlookup("access_level","users","telegram_id='$id' AND access_level<=3 AND del=0 AND fl_allowlogin=1")) { //test
					//$bot->sendMessage($id,print_r($message,true));
					if($message->getPhoto()) {
						$max_size=sizeof($message->getPhoto())-1;
						$file_id=$message->getPhoto()[$max_size]->getFileId();
						$bot->sendMessage($id, $file_id);
						$fl_notify=false;
						$fl_save=false;
					}
					if($message->getVideo()) {
						$file_id=$message->getVideo()->getFileId();
						$bot->sendMessage($id, $file_id);
						$fl_notify=false;
						$fl_save=false;
					}
					if($message->getVoice()) {
						$file_id=$message->getVoice()->getFileId();
						$bot->sendMessage($id, $file_id);
						$fl_notify=false;
						$fl_save=false;
					}
				}

				if($fl_save) {
					$q="INSERT INTO msgs SET
								tm='".time()."',
								uid='$uid',
								acc_id=103,
								user_id=0,
								msg='".$db->escape($msg)."',
								outg=0
								";
					$db->query($q);
					$msgs_id=$db->insert_id();

					if($fname) {
						$db->query("INSERT INTO msgs_attachments SET msgs_id=$msgs_id,
						url='".$db->escape("$url_attach/$fname")."'");
					}
					//$bot->sendMessage($id, $message->getText()." - id= $id uid=$uid $msg");
					if($db->dlookup("id","cards","telegram_nic=''"))
						$db->query("UPDATE cards SET telegram_nic='".$db->escape($user_name)."' WHERE uid='$uid'");
				}	

				if(file_exists("bot.inc.php")) {
					$fl_tg=true;
					include_once("bot.inc.php");
				}

				if($fl_notify) {
					$db->mark_new($uid,2);
					$db->telegram_bot=$tg_bot_notif;
					$db->db200=$DB200;
					$db->notify($uid,'TELEGRAM: '.$msg,'msg');
				}


			} else {
			}
			//~ if($m=$message->getNewChatMembers()) {
				//~ $tg_id=$m[0]->getId();
				//~ $tg_nic=$m[0]->getUsername();
				//~ $f_name=$m[0]->getFirstName();
				//~ $l_name=$m[0]->getLastName();
				//~ $bot->sendMessage(315058329, "NEw chat member: $tg_id $tg_nic $f_name $l_name");
				//~ $db->query("INSERT INTO tg_public_yoga SET
						//~ tm='".time()."',
						//~ tg_id='$tg_id',
						//~ tg_nic='".$db->escape($tg_nic)."',
						//~ f_name='".$db->escape($f_name)."',
						//~ l_name='".$db->escape($l_name)."',
						//~ res=1
						//~ ");
			//~ }
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
