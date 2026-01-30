<?
exit;
$title="ЛИДЫ У ПАРТНЕРОВ";
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
//include "/var/www/html/pini/formula12/scripts/leadgen/leadgen.class.php";
include "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
include "init.inc.php";
$db=new top($database,$title,false);
$l=new leadgen;
$p=new partnerka;
$user_id=$_SESSION['userid_sess'];
$res=$p->get_all_partners($user_id);
//print_r($res);
print "<div class='container font18' >";
print "<h2>Остатки лидов по партнерам</h2>";
print "<table class='table table-striped font18' >";
print "<thead>
	<tr>
		<th>№</th>
		<th>Партнер</th>
		<th>Имя</th>
		<th>Лидов куплено</th>
		<th>Лидов получено</th>
		<th>Лидов осталось</th>
		<th>Продажи</th>
	</tr>
	</thead>";
$n=1;
foreach($res AS $user_id=>$r) {
	$bought=$l->get_leads_bought($user_id);
	$delivered=$l->get_leads_delivered($user_id);
	$rest=$bought-$delivered;
	$rest_=($rest)?"<span class=' ' >$rest</span>":"<span class=' text-danger' >$rest</span>";
	$s=$db->fetch_assoc($db->query("SELECT SUM(amount) AS s FROM avangard JOIN cards ON vk_uid=uid WHERE user_id='$user_id'"))['s'];
	print "<tr>
		<td>$n</td>
		<td>{$r['login']}</td>
		<td>{$r['name']}</td>
		<td>$bought</td>
		<td>$delivered</td>
		<td>$rest_</td>
		<td>$s</td>
		</tr>";
	$n++;
}
print "</table>";
print "</div>";
$db->bottom();


?>
