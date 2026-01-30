<?
$title='custom_1';
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
chdir("..");
include "init.inc.php";
$t=new top($database,$title, false);
$db=new db($database);

?>
<style type='text/css'>
.table-hover tbody tr:hover td {
    background: #FFFF00;
}
</style>

<?
print "<h2>Выборка по тэгу: ".$db->dlookup("tag_name","tags","id=4")." </h2>";
$res=$db->query("SELECT klid,cards.uid AS uid,cards.name AS name
		FROM `cards`
		JOIN users ON users.id=cards.user_id
		JOIN tags_op ON tags_op.uid=cards.uid
		WHERE cards.del=0 AND tag_id=4
		GROUP BY users.id,cards.uid,cards.name
		ORDER BY users.id;");
$n=1;
while($r=$db->fetch_assoc($res)) {
	$klid=$r['klid'];
	$partner=trim($db->dlookup("name","cards","id=$klid")." ".$db->dlookup("surname","cards","id=$klid"));
	if (empty($partner))
		$partner="без_партнера";
	$uid=$r['uid'];
	print "$n <b>$partner</b> - <a href='../msg.php?uid=$uid' class='' target='_blank'>{$r['name']} {$r['surname']}</a> <br>";
	$n++;
}
?>

<?
include "bottom.reports.php";
?>
