<?
$tm1=(time());
$tm2=(time()+(5*60+5));
$res=$db->query("SELECT * FROM cards WHERE del=0 AND tm_delay>='$tm1' AND tm_delay<='$tm2' ");
while($r=$db->fetch_assoc($res)) {
	print "{$r['uid']} 💡 НАСТУПИЛО ОТЛОЖЕНОЕ ВРЕМЯ \n";
	$db->notify($r['uid'],"💡 НАСТУПИЛО ОТЛОЖЕНОЕ ВРЕМЯ");
}

?>
