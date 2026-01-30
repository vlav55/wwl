#!/usr/bin/php -q
<?
include "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
//include "products_exclude.inc.php";
$db=new db("papa");
$tm1=$db->date2tm("01.06.2021");
$tm2=time();
//$klid=1704; //8106;
$res=$db->query("SELECT * FROM users WHERE del=0");
while($r=$db->fetch_assoc($res)) {
	$klid=$r['klid'];
	$p=new partnerka($klid);
	//$p->products_exclude=$products_exclude;
	$p->products_include_only=[10,11,12];
	$p->fill_op($klid,$tm1,$tm2,0);
}

exit;

//~ $res=$db->query("SELECT * FROM users WHERE del=0");
//~ while($r=$db->fetch_assoc($res)) {
	//~ $klid=$r['klid'];
	//~ $user_id=$r['id'];
	//~ $p=new partnerka($klid);
	//~ $p->products_exclude=$products_exclude;
	//~ $tm_start=$p->fetch_assoc($p->query("SELECT tm FROM partnerka_balance WHERE klid='$klid' AND sum_r>0 ORDER BY tm DESC LIMIT 1"))['tm'];
	//~ if(!$tm_start)
		//~ $tm_start=$db->date2tm("01.06.2021");
	//~ $tm_end=time();
	//~ for($tm=$tm_start; $tm<$tm_end; $tm+=24*60*60) {
		//~ $tm1=$db->dt1($tm);
		//~ $tm2=$db->dt2($tm);
		//~ $sum_p=$p->sum_fee($klid,$tm1,$tm2,$debug=0);
		//~ $dt1=date("d.m.Y",$tm1);
		//~ $dt2=date("d.m.Y",$tm2);
		//~ print "$dt1-$dt2 $klid $sum_p \n";
		//~ if($db->dlookup("id","partnerka_balance","klid='$klid' AND  tm='$tm1'"))
			//~ $db->query("UPDATE partnerka_balance SET  sum_p='$sum_p' WHERE klid='$klid' AND  tm='$tm1'");
		//~ else
			//~ $db->query("INSERT INTO partnerka_balance SET tm='$tm1', klid='$klid', user_id='$user_id', sum_p='$sum_p' ");
	//~ }
	//~ print "\n\n";
//~ }

?>
