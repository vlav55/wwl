<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
chdir("..");
include "init.inc.php";
$db=new db($database);
$db->telegram_bot=$tg_bot_notif;
$db->db200=$DB200;

//file_put_contents("jquery.txt",print_r($_POST,true));
if (isset($_POST['q'])) {
	$uid=intval($_POST['uid']);
	$msg=mb_substr($_POST['q'],0,1024);
	$db->notify($uid,"❓Вопрос из партнерского кабинета: ".$msg,'partner');
	$db->mark_new($uid,3);
	$db->save_comm($uid,$_SESSION['userid_sess'],"❓Вопрос из партнерского кабинета: ".$msg);
}

if (isset($_POST['cashout'])) {
	$amount = $_POST['amount'];
	$uid = $_POST['uid']; // Get the user identifier (uid)

	// Additional validation can be performed here
	if (!empty($amount) && is_numeric($amount) && $amount > 0) {
		// Assume $db is your database object
		$db->notify($uid, "Запрос на вывод средств: {$amount}",'fee'); // Execute the notification

		// Optionally process the cashout logic here (e.g., database update)

		// Respond with a success message
		echo "Запрос на вывод отправлен!";
	} else {
		// Respond with an error message if the input is invalid
		echo "Неверная сумма. Пожалуйста, введите корректную сумму для вывода.";
	}
}
if (isset($_POST['cashout_insales'])) {
	$amount = $_POST['amount'];
	$uid = $_POST['uid']; // Get the user identifier (uid)

	// Additional validation can be performed here
	if (!empty($amount) && is_numeric($amount) && $amount > 0) {
		// Assume $db is your database object

		include_once "/var/www/vlav/data/www/wwl/inc/insales.class.php";
		$in=new insales($insales_id,$insales_shop);
		if(!$client_id=$in->search_client($uid)['id']) {
			$client_id=$in->create_client($uid)['id'];
		}

		//$db->notify_me("cashout_insales uid=$uid client_id=$client_id"); // Execute the notification
		if($client_id) {
			//print "OK klid=$klid uid=$uid client_id=$client_id"; exit;
			$comm="Вывод партнером вознаграждения бонусами магазина";
			$res=$in->bonus_create($client_id, $amount, $comm);
			if(isset($res['error'])) {
				$err=true;
				$msg="Ошибка синхронизации с inSales.
				Для начисления бонусов в inSales должен быть клиент с таким же номером телефона или email,
				а также должен быть соответствующий тариф и стоять галочка, что разрешены бонусы";
			} else {
				$msg="Бонусы магазина начислены, вы можете использовать их для оплаты.";
				$tm1=$db->dt1(time());
				$klid=$db->get_klid_by_uid($uid);
				$vid=3; //insales
				$db->query("INSERT INTO partnerka_pay SET tm='$tm1', klid='$klid', sum_pay='$amount' ,vid='$vid',comm='".$db->escape($comm)."'");
			}

			$db->notify_me("lk_jquery $ctrl_id ".$msg);
		} else
			print "Ошибка, не найден клиент";
		echo "$msg";
	} else {
		// Respond with an error message if the input is invalid
		echo "Неверная сумма. Пожалуйста, введите корректную сумму для вывода.";
	}
}

 
?>
