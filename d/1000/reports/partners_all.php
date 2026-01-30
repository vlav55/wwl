<?
$title='Отчеты по партнерам';
include "top.reports.php";
?>
<style type='text/css'>
.table-hover tbody tr:hover td {
    background: #FFFF00;
}
</style>
<table class='table table-striped table-hover' >
	<thead>
		<tr class='sticky-top bg-info text-white' >
			<th>#</th>
			<th>CRM</th>
			<th>Имя</th>
			<th>%_1</th>
			<th>%_2</th>
			<th>%_колич</th>
		</tr>
	</thead>
	<tbody>
<?
$res=$db->query("SELECT *  FROM partnerka_spec
			JOIN cards ON partnerka_spec.uid=cards.uid
			WHERE 1");
$n=1;
while($r=$db->fetch_assoc($res)) {
?>
	<tr>
		<td><?=$n?></td>
		<td><a href=javascript:wopen("../partner.php?uid=<?=$r['uid']?>#__fee")><?=$r['uid']?></a></td>
		<td><?=$r['surname']?> <?=$r['name']?></td>
		<td><?=$r['fee_1']?></td>
		<td><?=$r['fee_2']?></td>
		<td><?=$r['fee_cnt']?></td>
	</tr>
<?
$n++;
}
?>
	</tbody>
</table>
<?
include "bottom.reports.php";
?>
