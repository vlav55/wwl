<?
include_once "/var/www/vlav/data/www/wwl/inc/lk_jquery.1.inc.php";
exit;
?>

<?
chdir("..");
include "init.inc.php";
$db=new db($database);
$db->telegram_bot=$tg_bot_notif;
$db->db200=$DB200;

//file_put_contents("jquery.txt",print_r($_POST,true));
if (isset($_POST['q'])) {
	$uid=intval($_POST['uid']);
	$msg=mb_substr($_POST['q'],0,1024);
	$db->notify($uid,"❓Вопрос из партнерского кабинета: ".$msg);
	$db->mark_new($uid,3);
	$db->save_comm($uid,$_SESSION['userid_sess'],"❓Вопрос из партнерского кабинета: ".$msg);
}

if (isset($_POST['cashout'])) {
	$amount = $_POST['amount'];
	$uid = $_POST['uid']; // Get the user identifier (uid)

	// Additional validation can be performed here
	if (!empty($amount) && is_numeric($amount) && $amount > 0) {
		// Assume $db is your database object
		$db->notify($uid, "Запрос на вывод средств: {$amount}"); // Execute the notification

		// Optionally process the cashout logic here (e.g., database update)

		// Respond with a success message
		echo "Запрос на вывод отправлен!";
	} else {
		// Respond with an error message if the input is invalid
		echo "Неверная сумма. Пожалуйста, введите корректную сумму для вывода.";
	}
} else {
	// Handle the case where 'cashout' is not set
	echo "Ошибка. Не удалось обработать запрос.";
}

?>
