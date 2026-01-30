<?
include "chk.inc.php";

include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt('vkt');
print "ch_databases <br> \n";
$res=$db->query("SELECT * FROM 0ctrl WHERE del=0 AND id>1");
$databases=[];
while($r=$db->fetch_assoc($res)) {
	$ctrl_db=$db->get_ctrl_database($r['id']);
	$databases[]=$ctrl_db;
	//print "$ctrl_db <br>";
}

exit;

$sql="UPDATE `users` SET fl_allowlogin=0,access_level=5 WHERE telegram_id=1577633936;";
  
print nl2br($sql);
print "<hr>";
foreach($databases AS $ctrl_db) {
	$db->connect($ctrl_db,"vlaV^fokovA-mysql");
	if(!$db->query($sql))
		break;
	print "$ctrl_db proceed <br>";
}

?>
