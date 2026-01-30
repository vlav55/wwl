#!/usr/bin/php -q
<?
include "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
$db=new vkt_send('vkt');
print "0ctrl_vkt_send_tasks \n\n";
$tm1=time();
$tm2=time()+1*60;
$res=$db->query("SELECT * FROM 0ctrl_vkt_send_tasks WHERE tm<='$tm2'");
while($r=$db->fetch_assoc($res)) {
	$tm=$r['tm'];
	$dt=date('d.m.Y H:i',$tm);
	$ctrl_id=$r['ctrl_id'];
	$pact_secret=$db->dlookup("pact_secret","0ctrl","id='$ctrl_id'");
	$pact_company_id=$db->dlookup("pact_company_id","0ctrl","id='$ctrl_id'");
	$vkt_send_id=$r['vkt_send_id'];
	$vkt_send_type=$r['vkt_send_type'];
	$order_id=$r['order_id'];
	$uid=$r['uid'];
	print "$dt vkt_send_type=$vkt_send_type ctrl_id=$ctrl_id vkt_send_id=$vkt_send_id uid=$uid order_id=$order_id\n";
	switch($r['vkt_send_type']) {
		case 1: //by time
		case 2: //by event from landing
		case 3: //by sid
			//$fname="task_$vkt_send_id"."_"."$ctrl_id.php";
			$fname=$db->vkt_send_task_fname($vkt_send_id,$ctrl_id,$uid);
			$cmd="#!/usr/bin/php -q
<?
include '/var/www/vlav/data/www/wwl/inc/vkt_send.class.php';
\$db=new vkt_send('vkt');
\$db->ctrl_id=$ctrl_id;
\$db->pact_secret='$pact_secret';
\$db->pact_company_id=$pact_company_id;
\$db->vkt_send_task_0ctrl($vkt_send_id,$ctrl_id,$uid,$tm,$order_id);
unlink ('$fname');
?>
			";
			file_put_contents("vkt_send_tasks/$fname",$cmd);
			file_put_contents("vkt_send_tasks/tmp.sh",$cmd);
			chmod("vkt_send_tasks/$fname",0755);
			print "vkt_send_tasks/$fname generated \n";
			break;
		case 2: //by event from landing
			break;
		case 3: //by sid
			break;
		default:
	}
}
print "ok\n";
?>
