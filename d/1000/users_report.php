#!/usr/bin/php -q
<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db("yogacenter");
$res_u=$db->query("SELECT * FROM users WHERE access_level=5 AND fl_allowlogin=1");
while($r_u=$db->fetch_assoc($res_u)) {
	$user_id=$r_u['id'];
	$days=30;
	$out=$db->users_report_day($user_id,time(),$days);
	print "$out";
	$db->email($emails=array("vlav@mail.ru"), "MANAGERS ({$r_u['username']}) AT ".date("d.m.Y H:i"), nl2br($out), $from="noreply@yogahelpyou.com",$fromname="YOGAHELPYOU", $add_globals=false);
}

?>
