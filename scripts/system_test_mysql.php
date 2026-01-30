#!/usr/bin/php -q
<?
$fname="system_test/mysql_test.txt";
if(!file_exists($fname)) {
	file_get_contents("https://api.telegram.org/bot1820548789:AAGejAyt2oBcru_EsvVwU6JGlUNj_SyYvo8/sendMessage?chat_id=315058329&text=❗❗❗ATTENTION: system_test_mysql.php went wrong!");
}
unlink($fname);

include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt("vkt");
$klid=$db->get_klid(1);
if($klid==1002) {
	if(file_put_contents($fname,$klid)) {
		print "$fname created Ok\n";
		//file_get_contents("https://api.telegram.org/bot1820548789:AAGejAyt2oBcru_EsvVwU6JGlUNj_SyYvo8/sendMessage?chat_id=315058329&text=system_test_mysql.php : $fname created Ok");
	} else
		print "ERROR: file_put_contents $fname\n";
} else
	print "ERROR: db->get_klid(1) =$klid\n";

?>
