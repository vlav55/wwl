<?
include "/var/www/vlav/data/www/wwl/inc/vkt_send_log.1.inc.php";
exit;

include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
include "init.inc.php";

$t=new top($database,'Статистика рассылок',false);

if($_SESSION['username']!='vlav') {
	//~ print "ведутся технические работы";
	//~ $t->bottom();
	//~ exit;
}

$db=new db('vkt');
$res=$db->query("SELECT * FROM 0ctrl_vkt_send_tasks WHERE 1 ORDER BY tm");
print "<h3>0ctrl_vkt_send_tasks</h3>";
print "<table class='table table-striped' >";
print "<thead>
<tr>
	<th>dt</th>
	<th>ctrl_id</th>
	<th>vkt_send_id</th>
	<th>vkt_send_type</th>
	<th>uid</th>
</tr>
</thead>
<tbody>";
while($r=$db->fetch_assoc($res)) {
	$dt=date("d.m.Y H:i",$r['tm']);
	print "<tr>
	<td>$dt</td>
	<td>{$r['ctrl_id']}</td>
	<td>{$r['vkt_send_id']}</td>
	<td>{$r['vkt_send_type']}</td>
	<td>{$r['uid']}</td>
	</tr>";
}
print "</tbody></table>";


print "<h3>vkt_send_log</h3>";

$db=new vkt_send($database);

$res=$db->query("SELECT *,vkt_send_log.tm AS tm, vkt_send_log.id AS id FROM vkt_send_log
		JOIN cards ON vkt_send_log.uid=cards.uid
		WHERE 1
		ORDER BY vkt_send_log.id DESC");
print "<table class='table table-striped' >";
print "<thead>
<tr>
	<th>id</th>
	<th>vkt_send_id</th>
	<th>Время</th>
	<th>uid</th>
	<th>Name</th>
	<th>vkt_send_id</th>
	<th>res_email</th>
	<th>res_tg</th>
	<th>res_vk</th>
	<th>res_wa</th>
</tr>
</thead>
<tbody>
";
while($r=$db->fetch_assoc($res)) {
	$dt=date("d.m.Y H:i:s",$r['tm']);
	print "<tr>
	<td>{$r['id']}</td>
	<td>{$r['vkt_send_id']}</td>
	<td>$dt</td>
	<td>{$r['uid']}</td>
	<td>{$r['name']} {$r['surname']}</td>
	<td>{$r['vkt_send_id']}</td>
	<td>{$r['res_email']}</td>
	<td>{$r['res_tg']}</td>
	<td>{$r['res_vk']}</td>
	<td>{$r['res_wa']}</td>
	</tr>";
	
}
print "</tbody></table>";

$t->bottom();
?>
