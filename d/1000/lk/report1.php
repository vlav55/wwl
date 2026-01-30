<?
$title="report1";
include "../top.inc.php";
include "products_exclude.inc.php";
print "<div class='container' >";
$dt1="01.01.2019";
$dt2="01.12.2021";
$res=$db->query("SELECT utm_affiliate,COUNT(uid) AS cnt FROM cards WHERE utm_affiliate>0 GROUP BY utm_affiliate");
$n=1;
print "<h1>Партнерка</h1>";
print "<h2>$dt1 - $dt2</h2>";
$id_prohibited=[13100];
while($r=$db->fetch_assoc($res)) {
	$id_ua=$r['utm_affiliate'];
	$klid=$id_ua;
	if(in_array($id_ua,$id_prohibited) )  //Vlada and me
		continue;
	$pay_amount=$db->fetch_assoc($db->query("SELECT SUM(amount) AS s FROM avangard JOIN cards ON vk_uid=uid WHERE utm_affiliate=$id_ua AND res=1"))['s'];
	$uid=$db->dlookup("uid","cards","id=$id_ua");
	$name=$db->dlookup("name","cards","id=$id_ua")." ".$db->dlookup("surname","cards","id=$id_ua");
	$email=$db->dlookup("email","cards","id=$id_ua");
	$mob=$db->dlookup("mob","cards","id=$id_ua");
	print "$n ua=$id_ua <a href='https://1-info.ru/yogacenter_vkt/db/msg.php?uid=$uid' class='' target='_blank'>$uid</a> $name $email $mob - партнеров {$r['cnt']} $pay_amount<br>";
	if($pay_amount>0) {

		$res1=$db->query("SELECT * FROM cards WHERE utm_affiliate=$klid");
		while($r1=$db->fetch_assoc($res1)) {
			$dt1=date("d.m.Y",$r1['tm']);
			$name1=$db->dlookup("name","cards","uid={$r1['uid']}");
			$email1=$db->dlookup("email","cards","uid={$r1['uid']}");
			print "<div class='ml-5 text-secondary' >регистрация: $dt1 <a href='https://1-info.ru/yogacenter_vkt/db/msg.php?uid={$r1['uid']}' class='' target='_blank'>$name1</a> $email1 </div>";
		}

		
		$add_where="";
		foreach($products_exclude AS $pid_e)
			$add_where.="product_id!=$pid_e AND ";
		$add_where.="1";
		$res_pay=$db->query("SELECT * FROM avangard
						JOIN cards ON vk_uid=uid
						WHERE utm_affiliate=$id_ua AND cards.id!=$id_ua AND res=1 AND $add_where");
		while($r_pay=$db->fetch_assoc($res_pay)) {
			$dt_pay=date("d.m.Y",$r_pay['tm']);
			$name_pay=$db->dlookup("name","cards","uid={$r_pay['uid']}");
			$email_pay=$db->dlookup("email","cards","uid={$r_pay['uid']}");
			print "<div class='ml-5 text-info' >оплата: $dt_pay <a href='https://1-info.ru/yogacenter_vkt/db/msg.php?uid={$r_pay['uid']}' class='' target='_blank'>$name_pay</a> $email_pay <b>{$r_pay['amount']}</b> {$r_pay['order_descr']}</div>";
		}
	}
	$n++;
	
}
print "</div>";
include "../bottom.inc.php";
?>
