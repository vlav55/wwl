#!/usr/bin/php -q
<?
include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt('vkt');
print "SCAN LANDS FOR tm_scdl RESCHEDULE \n";
$res=$db->query("SELECT * FROM 0ctrl WHERE del=0");
$dbs=[];
while($r=$db->fetch_assoc($res)) {
	$dbs[]=$db->get_ctrl_database($r['id']);
}
print "dbs array created (".sizeof($dbs).")\n";
//exit;
foreach($dbs AS $database) {
	if(empty($database))
		continue;
	print "DB=".$database."\n";
	//include "products_exclude.inc.php";
	$db->connect($database);
	$tm=time();
	$res=$db->query("SELECT id,land_num,tm_scdl,tm_scdl_period FROM lands WHERE del=0 AND tm_scdl<'$tm' AND tm_scdl_period>0");
	while($r=$db->fetch_assoc($res)) {
		$tm_scdl=$r['tm_scdl']+$r['tm_scdl_period'];
		$db->query("UPDATE lands SET tm_scdl='$tm_scdl' WHERE id={$r['id']}");
		$dt1=date("d.m.Y H:i",$r['tm_scdl']);
		$dt2=date("d.m.Y H:i",$tm_scdl);
		print "LAND {$r['land_num']} tm_scdl=$dt1 corrected to $dt2 \n";
	}
}
print "FINISHED ".$db->num_rows($res)."\n";
?>
