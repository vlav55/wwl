<?
include "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
$database="vkt1_101";
if(isset($_GET['db']))
	$database=$_GET['db'];
$db=new vkt_send($database);
$res=$db->query("SELECT * FROM vkt_send_log WHERE 1 ORDER BY tm DESC LIMIT 100");

$title="vkt_send_log";
include "../top.inc.php";

print "<h1 class='text-center' >$database</h1>";
print "<div class='px-3' >";
print "<table class='table table-striped' >";
print "<tr>
		<th>date</th>
		<th>uid</th>
		<th>vkt_send_id</th>
		<th>dt_event</th>
		<th>tg_id</th>
		<th>vk_id</th>
		<th>wa_id</th>
		<th>email</th>
		<th>res_emai</th>
		<th>res_vk</th>
		<th>res_wa</th>
		<th>res_tg</th>
		</tr>";
$n=1;
while($r=$db->fetch_assoc($res)) {
	$dt=date("d.m.Y H:i",$r['tm']);
	$dt_event=$r['tm_event'] ? date("d.m.Y H:i",$r['tm_event']) : 0;
	if($r['vkt_send_id']>0)
		$vkt_send_name=$db->dlookup("name_send","vkt_send_1","id={$r['vkt_send_id']}");
	else
		$vkt_send_name="0";
	print "<tr>
			<td>$dt</td>
			<td>{$r['uid']}</td>
			<td title='{$r['vkt_send_id']}'>$vkt_send_name</td>
			<td>$dt_event</td>
			<td>{$r['tg_id']}</td>
			<td>{$r['vk_id']}</td>
			<td>{$r['wa_id']}</td>
			<td>{$r['email']}</td>
			<td>{$r['res_emai']}</td>
			<td>{$r['res_vk']}</td>
			<td>{$r['res_wa']}</td>
			<td>{$r['res_tg']}</td>
			</tr>";
	$n++;
}
print "</table>";
print "</div>";
include "../botton.inc.php";

?>
