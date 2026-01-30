<?
include "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$vkt=new vkt("vkt");
$db=new vkt_send("vkt");
$res=$db->query("SELECT * FROM 0ctrl_vkt_send_tasks WHERE 1 ORDER BY id DESC");

$title="0ctrl_vkt_send_tasks_list.php";
include "../top.inc.php";

print "<div class='container' >";
print "<table class='table table-condenced' >";
print "<tr>
		<th>#</th>
		<th>date</th>
		<th>ctrl_id</th>
		<th>vkt_send_id</th>
		<th>vkt_send_type</th>
		<th>uid</th>
		<th>order_id</th>
		</tr>";
$n=1;
while($r=$db->fetch_assoc($res)) {
	$dt=date("d.m.Y H:i",$r['tm']);
	$db1=new db($vkt->get_ctrl_database($r['ctrl_id']));
	if($r['vkt_send_id']>0)
		$vkt_send_name=$db1->dlookup("name_send","vkt_send_1","id={$r['vkt_send_id']}");
	else
		$vkt_send_name="-";
	$ref=$r['ctrl_id']==1 ? "-&gt;".$db->get_ctrl_id_by_uid($r['uid']) : "";
	print "<tr>
			<td>$n</td> 
			<td>$dt</td>
			<td>{$r['ctrl_id']}$ref</td>
			<td title='{$r['vkt_send_id']}'>({$r['vkt_send_id']}) $vkt_send_name</td>
			<td>{$r['vkt_send_type']}</td>
			<td>{$r['uid']}</td>
			<td>{$r['order_id']}</td>
			</tr>";
	$n++;
}
print "</table>";
print "</div>";
include "../bottom.inc.php";

?>
