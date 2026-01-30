#!/usr/bin/php -q
<?
include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt('vkt');
$res=$db->query("SELECT * FROM 0ctrl WHERE del=0");
$dbs=[];
while($r=$db->fetch_assoc($res)) {
	$dbs[]=['db'=>$db->get_ctrl_database($r['id']),
		'tg_bot'=>$r['tg_bot_notif'],
		'db200'=>'https://for16.ru/d/'.$db->get_ctrl_dir($r['id'])];
}
print "dbs array created (".sizeof($dbs).")\n";

foreach($dbs AS $r) {
	$database=$r['db'];
	if(empty($database))
		continue;
		
	print "DB=".$database."\n";

	$db->connect($database);
	$db->telegram_bot=$r['tg_bot'];
	$db->db200=$r['db200'];
	$db->vktrade_send_tg_bot=$r['tg_bot'];

	$tm1=(time());
	$tm2=(time()+(5*60+5));
	$res=$db->query("SELECT * FROM cards WHERE del=0 AND tm_delay>='$tm1' AND tm_delay<='$tm2' ");
	while($r=$db->fetch_assoc($res)) {
		print "{$r['uid']} 💡 КОНТРОЛЬ \n";
		$msg=$substr($db->dlookup("msg","msgs","uid={$r['uid']} AND outg=2"),0,255);
		$db->notify($r['uid'],"💡 КОНТРОЛЬ\n $msg");
	}
}
print "ok\n";
?>
