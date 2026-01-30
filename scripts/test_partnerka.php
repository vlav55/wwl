#!/usr/bin/php -q
<?
include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";

	$database="vkt1_102";
	print "DB=".$database."\n";
	//include "products_exclude.inc.php";
	$db=new db($database);
	$tm1=$db->date2tm("01.10.2022");
	$tm2=time();
	$res=$db->query("SELECT * FROM users WHERE del=0 AND klid=23");
	while($r=$db->fetch_assoc($res)) {
		$klid=$r['klid'];
		$p=new partnerka($klid,$database);
		//$p->products_exclude=$products_exclude;
		//$p->products_include_only=[1001];
		$p->fill_op($klid,$tm1,$tm2);
	}
?>
