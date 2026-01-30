<?
$title="Сводка по менеджерам";
include "top.reports.php";
?>
<?

$w_user_id = ($_SESSION['access_level']==4 )
		? "user_id={$_SESSION['userid_sess']}" : "user_id>0";

$res=$db->query("SELECT q1.user_id,COUNT(cnt_by_uid) AS cnt
		FROM (SELECT user_id,COUNT(user_id) as cnt_by_uid, uid
			FROM `msgs`
			WHERE outg=2 AND tm>=$tm1 AND tm<=$tm2 AND imp=12 AND $w_user_id AND msg!='n/a' AND msg!=''
			GROUP BY user_id,uid
			ORDER BY cnt_by_uid DESC) AS q1
		WHERE 1
		GROUP BY q1.user_id;",0);
$sum=0;

while($r=$db->fetch_assoc($res)) {
	$arr[]=[
		'user_id'=>$r['user_id'],
		'real_user_name'=>$db->dlookup("real_user_name","users","id='{$r['user_id']}'"),
		'cnt'=>$r['cnt'],
		'assigned'=>$db->fetch_assoc($db->query("SELECT COUNT(q1.uid) AS cnt
				FROM (SELECT uid
					FROM msgs
					WHERE source_id=122 AND user_id={$r['user_id']} AND tm>=$tm1 AND tm<=$tm2
					GROUP BY uid)
					AS q1
					WHERE 1"))['cnt'],
		];
	$sum+=$r['cnt'];

}
//$db->print_r($arr);
?>
<table class='table table-striped' >
	<thead>
		<tr>
			<th>№</th>
			<th>Код менеджера</th>
			<th>Имя</th>
			<th>Контактов</th>
			<th>%</th>
			<th>Взято в работу</th>
		</tr>
	</thead>
	<tbody>
<?
$n=1;
foreach($arr AS $r) {
	?>
	<tr>
		<td><?=$n?></td>
		<td><?=$r['user_id']?></td>
		<td><?=$r['real_user_name']?></td>
		<td><a href='managers_detailed.php?user_id=<?=$r['user_id']?>&tm1=<?=$tm1?>&tm2=<?=$tm2?>' class='' target='_blank'><?=$r['cnt']?></a></td>
		<td><?=round($r['cnt']/$sum*100)?>%</td>
		<td><?=$r['assigned']?></td>
	</tr>
	<?
	$n++;
}
?>
</tbody></table>
<h3>Всего: <?=$sum?></h3>

<?
include "reports/bottom.reports.php";
?>
