<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "init.inc.php";
$db=new top($database,"640px;",false);

if(isset($_GET['do_merge'])) {
	$uid1=intval($_GET['uid1']);
	$uid2=intval($_GET['uid2']);
	if($uid1<0 && $uid2>0) {
		$tmp=$uid2; $uid2=$uid1; $uid1=$tmp;
	}
		
	if($db->merge_cards($uid1,$uid2)) {
		if($db->dlookup("del","cards","uid='$uid'")==0)
			$uid=$uid1; else $uid=$uid2;
		print "<div class='alert alert-success' >$uid1 $uid2 успешно объединены в <a href='javascript:opener.location=\"msg.php?uid=$uid\";window.close();' class='' target=''>$uid</a></div>";
	} else
		print "<div class='alert alert-danger' >Error merging $uid1 $uid2</div>";
	$db->bottom();
	exit;
}

$uid=intval($_GET['uid']);
$email=strtolower($db->dlookup("email","cards","uid='$uid'"));
$mob=$db->dlookup("mob_search","cards","uid='$uid'");
print "<h1>Объединить карточки с одинаковыми телефонами ($mob) или емэйл ($email)</h1>";
if($db->num_rows($res=$db->query("SELECT * FROM cards WHERE del=0 AND email=LOWER('$email') AND email!='' ORDER BY uid DESC"))>1) {
	$r=$db->fetch_assoc($res);
	$uid1=$r['uid']; $name1=$r['name']." ".$r['surname']; $mob1=$r['mob_search'];
	$r=$db->fetch_assoc($res);
	$uid2=$r['uid']; $name2=$r['name']." ".$r['surname']; $mob2=$r['mob_search'];
	print "<div class='card bg-light p-2 p-2'  >
		<h3>Объединяем по емэйл $email</h3>
		<p class='alert alert-success'>$uid1 $name1 $email $mob1</p>
		<p>+</p>
		<p class='alert alert-warning' >$uid2 $name2 $email $mob2</p>
		<p>=
		<a href='?do_merge=yes&uid1=$uid1&uid2=$uid2' class='' target=''><button class='btn btn-sm btn-primary' >Объединить</button></a>
		</p>
		</div>";
} elseif($db->num_rows($res=$db->query("SELECT * FROM cards WHERE del=0 AND mob_search='$mob' AND mob_search!='' ORDER BY uid DESC"))>1) {
	$r=$db->fetch_assoc($res);
	$uid1=$r['uid']; $name1=$r['name']." ".$r['surname']; $email1=$r['email'];
	$r=$db->fetch_assoc($res);
	$uid2=$r['uid']; $name2=$r['name']." ".$r['surname']; $email1=$r['email'];
	print "<div class='cards bg-light p-3'  >
		<h3>Объединяем по телефону $mob</h3>
		<p class='alert alert-success'>$uid1 $name1 $email1 $mob</p>
		<p>+</p>
		<p class='alert alert-warning' >$uid2 $name2 $email2 $mob</p>
		<p>=
		<a href='?do_merge=yes&uid1=$uid1&uid2=$uid2' class='' target=''><button class='btn btn-sm btn-primary' >Объединить</button></a>
		</p>
		</div>";
} else {
	print "<div class='alert alert-danger' >Нечего объединять</div>";
}


$db->bottom();
?>
