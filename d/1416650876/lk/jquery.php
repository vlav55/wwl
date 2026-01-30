<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
chdir("..");
include "init.inc.php";
$db=new db($database);
$db->telegram_bot=$tg_bot_notif;
$db->db200=$DB200;

//file_put_contents("jquery.txt",print_r($_POST,true));

$uid=intval($_POST['uid']);
$db->notify($uid,"❓Вопрос из партнерского кабинета: ".$_POST['q']);
$db->mark_new($uid,3);
$db->save_comm($uid,$_SESSION['userid_sess'],"❓Вопрос из партнерского кабинета: ".$_POST['q']);
?>
