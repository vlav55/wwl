#!/usr/bin/php -q
<?
include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";

$w="";
if($argc>1) {
	if(intval($argv[1]))
		$w=" AND id=".intval($argv[1]);
}
print "$w \n";

$db=new vkt('vkt');
$res=$db->query("SELECT * FROM 0ctrl WHERE del=0 AND insales_delay_fee=0 $w");
$dbs=[];
while($r=$db->fetch_assoc($res)) {
	$dbs[]=['ctrl_id'=>$r['id'], 'database'=>$db->get_ctrl_database($r['id'])];
}
print "dbs array created (".sizeof($dbs).")\n";

foreach($dbs AS $d) {
	$database=$d['database'];
	$ctrl_id=$d['ctrl_id'];
	if(empty($database))
		continue;
	print "ctrl_id=$ctrl_id DB=".$database."\n";
	//include "products_exclude.inc.php";
	$db->connect($database);
	$tm1=$db->date2tm("01.10.2022");
	$tm2=time();
	$res=$db->query("SELECT * FROM users WHERE del=0");
	while($r=$db->fetch_assoc($res)) {
		$klid=$r['klid'];
		$p=new partnerka($klid,$database);
		//$p->products_exclude=$products_exclude;
		//$p->products_include_only=[1001];
		$p->fill_op($klid,$tm1,$tm2, $ctrl_id);
	}
}

?>
