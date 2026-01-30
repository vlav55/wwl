<?
$title="Экспорт";
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "init.inc.php";
//exit;
$db=new top($database,"640px;",true);
print "<h2>Экспорт данных</h2>";
if($_SESSION['access_level']>3) {
	print "<p class='alert alert-info' >Извините, у вас нет доступа ({$_SESSION['access_level']})</p>";
	exit;
}
if($ctrl_id==1 && $_SESSION['userid_sess']!=1) {
	print "<p class='alert alert-info' >Извините, у вас нет доступа user_id=({$_SESSION['userid_sess']})</p>";
	exit;
}

$delim=";";
$out="\"id\"".$delim."\"first_name\"".$delim."\"last_name\"".$delim."\"phone\"".$delim."\"email\"".$delim."\"comm\"".$delim."\"comm1\"".$delim."\"vk_id\"".$delim."\"tg_id\"".$delim."\"tg_nic\"".$delim."\"instagram\"".$delim."\"affiliate_id\""."\r\n";
$out1="phone,email\n";
$w=($db->dlookup("id","cards","del=0 AND fl=1")) ? "AND fl=1" : "";
$res=$db->query("SELECT * FROM cards WHERE del=0 $w");
while($r=$db->fetch_assoc($res)) {
	$out.="\"".addslashes($r['uid'])."\"".$delim;
	$out.="\"".addslashes($r['name'])."\"".$delim;
	$out.="\"".addslashes($r['surname'])."\"".$delim;
	$out.="\"".addslashes($r['mob_search'])."\"".$delim;
	$out.="\"".addslashes($r['email'])."\"".$delim;
	$out.="\"".addslashes($r['comm'])."\"".$delim;
	$out.="\"".addslashes($r['comm1'])."\"".$delim;
	$out.="\"".addslashes($r['vk_id'])."\"".$delim;
	$out.="\"".addslashes($r['telegram_id'])."\"".$delim;
	$out.="\"".addslashes($r['telegram_nic'])."\"".$delim;
	$out.="\"".addslashes($r['insta'])."\"".$delim;
	$out.="\"".addslashes($r['user_id'])."\"".$delim;
	$out.="\r\n";
	if(!empty($r['mob_search']) || !empty($r['email']) )
		$out1.="{$r['mob_search']},{$r['email']}\n";
}
$dt=date("d-m-Y_H-i");
$fname="export_$dt.csv";
$fname1="export_$dt"."_retarget.csv";
file_put_contents($fname,$out);
file_put_contents($fname1,$out1);
print "<p>Скачать: <a href='$fname' class='' target=''>$fname</a></p>";
print "<p>Версия для ретагета: <a href='$fname1' class='' target=''>$fname1</a></p>";
$db->bottom();
?>
