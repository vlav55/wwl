<?
//this file for remote host to check winwinland.ru
print nl2br(file_get_contents("https://for16.ru/scripts/chk_server_touch.php?create=yes"));
if($tm=file_get_contents("https://for16.ru/scripts/chk_server.txt")) {
	print "chk ok ".date("d.m.Y H:i:s",$tm)."<br>";
	print nl2br(file_get_contents("https://for16.ru/scripts/chk_server_touch.php?remove=yes"));
} else {
	file_get_contents("https://api.telegram.org/bot1820548789:AAGejAyt2oBcru_EsvVwU6JGlUNj_SyYvo8/sendMessage?chat_id=315058329&text=".urlencode("‼️‼️‼️ Server is not accessible"));
	print "err <br>";
}
?>
