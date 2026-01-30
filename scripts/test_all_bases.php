<?
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt('vkt');
print "TEST ALL BASES <br> \n";
$res=$db->query("SELECT * FROM 0ctrl WHERE del=0 AND id>1");
$databases=[];
while($r=$db->fetch_assoc($res)) {
	$ctrl_db=$db->get_ctrl_database($r['id']);
	$databases[]=$ctrl_db;
	//print "$ctrl_db <br>";
}

foreach($databases AS $ctrl_db) {
	print "$ctrl_db <br>";
	$db->connect($ctrl_db,"vlaV^fokovA-mysql");
	$res=$db->query("SELECT * FROM lands WHERE bot_first_msg LIKE '%cabinet_link%'");
	while($r=$db->fetch_assoc($res)) {
		print $r['bot_first_msg']."<hr>";
	}
	print "$ctrl_db proceed <br>";
}

?>
