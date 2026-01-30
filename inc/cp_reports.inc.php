<?
$db=new top($database,"640px;",false);

$tm=intval($_GET['tm']);
$res=$db->query("SELECT * FROM cards 
			JOIN razdel ON razdel.id=cards.razdel 
			WHERE cards.del=0 AND tm_schedule='$tm'
			ORDER BY razdel,surname");

print "<div class='text-right' ><a href='javascript:window.close()' class='btn btn-warning' target=''>Закрыть</a></div>";
print "<h3>График на : ".date("d.m.Y H:i",$_GET['tm'])."</h3>";
//print "<h3>Категория : $cat</h3>";
$n=1;
print "<table class='table table-striped'>";
print "<thead><tr>
	<th style='width:50px;'>№</th>
	<th style='width:100px;'>Раздел</th>
	<th style='width:250px;'>ФИО, тел</th>
	<th style='width:250px;'>Комм</th>
	<th style='width:250px;'>Комм1</th>
	</tr></thead>";
while($r=$db->fetch_assoc($res)) {
	$l1=strpos($r['comm'],"\n");
	if($l1>0)
		$comm=substr($r['comm'],0,$l1); else $comm =$r['comm'];
	print "<tr style='height:40px;'>
		<td style='text-align:center;'>$n</td>
		<td style='text-align:center;'>{$r['razdel_name']}</td>
		<td><b><a href='javascript:wopen(\"msg.php?uid={$r['uid']}\")' class='' target=''>{$r['surname']} {$r['name']}</a></b> {$r['mob']}</td>
		<td>".(nl2br($r['comm']))."</td>
		<td>".(nl2br($r['comm1']))."</td>
		</tr>";
	$n++;
}
print "</table>";

//print "<br><br><br> Подпись  __________________________ Дата ________________";

$db->bottom();
?>
