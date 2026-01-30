<?
$title="export csv";
$no_menu=true;
include "top.reports.php";
$db=new db('vkt');

$res=$db->query("SELECT * FROM cards JOIN tags_op ON cards.uid=tags_op.uid WHERE tag_id=27");
$n=1;
$out="phone,email\n";
while($r=$db->fetch_assoc($res)) {
	if($r['mob_search']=='0' && empty($r['email']))
		continue;
	if(empty($r['mob_search']) && empty($r['email']))
		continue;
	$uid=$r['uid'];
	//print "$n $uid 	<br>";
	$n++;
	$out.=$r['mob_search'].",".$r['email']."\n";
}
//print getcwd();
$fname="ecom-".date("d-m-Y").".csv";
file_put_contents("reports/tmp/$fname",$out);
print "<p><a href='tmp/$fname' class='' target=''>$fname</a></p>";

include "bottom.reports.php";
?>
