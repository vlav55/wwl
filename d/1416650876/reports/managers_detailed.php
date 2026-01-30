<?
$title="Детализация по менеджерам";
$no_menu=true;
include "top.reports.php";
?>
<?
if(!isset($_GET['user_id'])) {
	print "<p class='alert alert-dander' >Не выбран менеджер</p>";
	exit;
}
$tm1=intval($_GET['tm1']);
$tm2=intval($_GET['tm2']);
$user_id=intval($_GET['user_id']);
$res=$db->query("SELECT *, msgs.msg AS comm, msgs.tm AS msgs_tm, cards.razdel AS razdel_id
		FROM msgs
		JOIN cards ON cards.uid=msgs.uid
		JOIN razdel ON cards.razdel=razdel.id
		WHERE msgs.user_id='$user_id' AND  outg=2 AND msgs.tm>=$tm1 AND msgs.tm<=$tm2 AND imp=12
		ORDER BY msgs_tm DESC
		LIMIT 100");
$sum=0;

while($r=$db->fetch_assoc($res)) {
	$arr[]=[
		'dt'=>date("d.m.Y H:i",$r['msgs_tm']),
		'uid'=>$r['uid'],
		'name'=>$r['surname'].' '.$r['name'],
		'razdel'=>"<span class='badge p-1' style=".$db->get_style_by_razdel($r['razdel_id']).">".$r['razdel_name']."</span>",
		'comm'=>nl2br($r['comm']),
		'comm1'=>nl2br($r['comm1']),
		];
	$sum+=$r['cnt'];

}

?>
<h3><?=$db->dlookup("real_user_name","users","id='$user_id'");?> c <?=date("d.m.Y",$tm1)?> по <?=date("d.m.Y",$tm2)?></h3>
<table class='table table-striped' >
	<thead>
		<tr>
			<th>№</th>
			<th>Время</th>
			<th>Имя</th>
			<th>Этап</th>
			<th>Задача</th>
			<th>Комм</th>
		</tr>
	</thead>
	<tbody>
<?
$n=1;
$last_msg="";
foreach($arr AS $r) {
	if(empty(trim($r['comm'])))
		continue;
	if(trim($r['comm'])=='n/a')
		continue;
	if($r['comm']==$last_msg)
		continue;
	?>
	<tr>
		<td><?=$n?></td>
		<td><?=$r['dt']?></td>
		<td><a href='../msg.php?uid=<?=$r['uid']?>' class='' target='_blank'><?=$r['name']?></a></td>
		<td><?=$r['razdel']?></td>
		<td><?=$r['comm']?></td>
		<td class='text-muted' ><?=$r['comm1']?></td>
	</tr>
	<?
	$last_msg=$r['comm'];
	$n++;
}
?>
</tbody></table>
</div>

<?
include "reports/bottom.reports.php";
?>
