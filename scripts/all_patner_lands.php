<?
include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt('vkt');
$res=$db->query("SELECT * FROM 0ctrl WHERE del=0");
$dbs=[];
while($r=$db->fetch_assoc($res)) {
	$dbs[]=['db'=>$db->get_ctrl_database($r['id']),
		'tg_bot'=>$r['tg_bot_notif'],
		'db200'=>'https://for16.ru/d/'.$db->get_ctrl_dir($r['id']),
		'company'=>$r['company'],
		];
}
print "dbs array created (".sizeof($dbs).")\n";

foreach($dbs AS $r1) {
	$database=$r1['db'];
	if(empty($database))
		continue;
		
	print "<b>DB=".$database." {$r1['company']}</b> <br>";
	$db->connect($database);

	$res=$db->query("SELECT * FROM lands WHERE del=0 AND fl_partner_land=1 AND bot_first_msg!=''");
	while($r=$db->fetch_assoc($res)) {
		print "<a href='{$r['land_url']}' class='' target='_blank'>{$r['land_name']}</a> <br>";
	}
	print "<hr>";
}

?>
