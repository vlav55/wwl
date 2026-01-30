<?

include "/var/www/vlav/data/www/wwl/inc/db.class.php";
chdir("../d/1000/");
include "init.inc.php";
$db=new db('vkt');

$res=$db->query("SELECT cards.uid AS uid,
						cards.name AS name,
						cards.surname AS surname,
						MAX(avangard.tm_end) AS max_tm_end,
						SUM(amount) AS s,
						MAX(0ctrl.company) AS company,
						MAX(ctrl_dir) AS ctrl_dir
	FROM avangard
	JOIN cards ON vk_uid=cards.uid
	JOIN 0ctrl ON vk_uid=0ctrl.uid
	WHERE res=1 AND avangard.tm_end>0 AND amount>4000
	GROUP BY cards.uid
	ORDER BY max_tm_end DESC");
$n=1;
while($r=$db->fetch_assoc($res)) {
	$dt=date("d.m.Y",$r['max_tm_end']);
	$s=$r['max_tm_end']<time()?"color:red;":"";
	$sum=$r['s']>3000?"<b><span style='font-size:18px; color:blue;'>{$r['s']}</span></b>":$r['s'];
	print "{$r['uid']} {$r['name']} {$r['surname']} <b><a href='https://for16.ru/d/{$r['ctrl_dir']}' class='' target='_blank'>{$r['company']}</a></b> $sum <span style='$s'>$dt</span> <br>";
	$n++;
}


?>
