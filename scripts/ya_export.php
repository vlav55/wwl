<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db('vkt');
$res=$db->query("SELECT mob_search,email
			FROM msgs JOIN cards ON msgs.uid=cards.uid
			WHERE msgs.source_id=25
			GROUP BY cards.uid");
$out="phone,email\n";
while($r=$db->fetch_assoc($res)) {
	if(empty($r['mob_search']))
		continue;
	$out.= "{$r['mob_search']},{$r['email']}\n";
}
$res=$db->query("SELECT mob_search,email
			FROM msgs JOIN cards ON msgs.uid=cards.uid
			WHERE msgs.source_id=12
			GROUP BY cards.uid LIMIT 100");
while($r=$db->fetch_assoc($res)) {
	if(empty($r['mob_search']))
		continue;
	$out.= "{$r['mob_search']},{$r['email']}\n";
}
file_put_contents("out.csv",$out);
print "<a href='out.csv' class='' target=''>out.csv</a>";
?>
