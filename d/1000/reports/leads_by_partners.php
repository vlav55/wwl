<?
include "top.reports.php";
?>
<?
$res=$db->query("SELECT cards.user_id AS user_id,username,real_user_name,COUNT(cards.uid) AS cnt
	FROM (SELECT uid FROM msgs WHERE source_id=12 AND tm>=$tm1 AND tm<=$tm2 GROUP BY uid) AS t1
	JOIN cards ON t1.uid=cards.uid
	JOIN users ON users.id=cards.user_id
	WHERE cards.del=0 AND users.id>3
	GROUP BY cards.user_id
	ORDER BY cnt DESC",0);
$sum=0;

while($r=$db->fetch_assoc($res)) {
	if($r['user_id']==0)
		$r['username']="БЕЗ ПАРТНЕРА";
	$arr[]=[
		'username'=>$r['username'],
		'real_user_name'=>$r['real_user_name'],
		'cnt'=>$r['cnt'],
		];
	$sum+=$r['cnt'];

}

?>
<table class='table table-striped' >
	<thead>
		<tr>
			<th>№</th>
			<th>Код партнера</th>
			<th>Имя</th>
			<th>Лидов</th>
			<th>%</th>
		</tr>
	</thead>
	<tbody>
<?
$n=1;
foreach($arr AS $r) {
	?>
	<tr>
		<td><?=$n?></td>
		<td><?=$r['username']?></td>
		<td><?=$r['real_user_name']?></td>
		<td><?=$r['cnt']?></td>
		<td><?=round($r['cnt']/$sum*100)?>%</td>
	</tr>
	<?
	$n++;
}
?>
</tbody></table>
<h3>Всего: <?=$sum?></h3>
</div>

<?
include "bottom.reports.php";
?>
