<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db('vkt');
$txt=file("vkt.sql");
$res=[];
foreach($txt AS $str) {
	if(preg_match("/([\-]{2,})|(^SET)|(\/\*\!)/",$str)) {
		if(!empty(trim($out)))
			$res[]=$out;
		$out="";
		continue;
	}
	$out.=$str;
}
$db->print_r($res);
?>
