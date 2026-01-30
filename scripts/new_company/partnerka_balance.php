#!/usr/bin/php -q
<?
include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt('vkt');
$res=$db->query("SELECT * FROM 0ctrl WHERE del=0");
$dbs=[];
while($r=$db->fetch_assoc($res)) {
	$dbs[]=$db->get_ctrl_database($r['id']);
}

include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
foreach($dbs AS $database) {
	print $database."\n";
	//include "products_exclude.inc.php";
	$db=new db($database);
	$tm1=$db->date2tm("01.10.2022");
	$tm2=time();
	$res=$db->query("SELECT * FROM users WHERE del=0");
	while($r=$db->fetch_assoc($res)) {
		$klid=$r['klid'];
		$p=new partnerka($klid);
		//$p->products_exclude=$products_exclude;
		$p->products_include_only=[1001];
		$p->fill_op($klid,$tm1,$tm2,0);
	}
}

?>
