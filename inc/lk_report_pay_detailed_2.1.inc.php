<?
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "../init.inc.php";

//$database='papa';
$db=new top($database,'Выплаты',false);
print "<div class='container' >";

$klid=intval($_GET['klid']);
$username=$db->dlookup("username","users","klid=$klid");
$real_user_name=$db->dlookup("real_user_name","users","klid=$klid");
print "<p class='p-3 text-right' ><a href='javascript:window.close();' class='btn btn-warning' target=''>назад</a></p>";
print "<h3 class='py-4' >Выплаты по партнерской программе - $real_user_name ($username)</h3>";


$res=$db->query("SELECT * FROM partnerka_pay WHERE klid='$klid' ORDER BY tm DESC");
print "<table class='table table-striped' >
	<thead>
		<tr>
			<th>Дата</th>
			<th>Сумма</th>
			<th>Вид</th>
			<th>Коммент</th>
			<th> </th>
		</tr>
	</thead>
	<tbody>";
$s=0;
while($r=$db->fetch_assoc($res)) {
	$vid=($r['vid']==1)?"Деньгами":"Услугами";
	//$vid="";
	$dt=date("d.m.Y",$r['tm']);
	print "<tr>
		<td>$dt</td>
		<td>{$r['sum_pay']} р.</td>
		<td>$vid</td>
		<td>".nl2br($r['comm'])."</td>
		<td></td>
		</tr>";
	$s+=$r['sum_pay'];
}
print "</tbody></table>";
print "<h3 class='text-right' >Всего $s р.</h3>";
print "</div>";
$db->bottom();

?>
