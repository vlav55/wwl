<?
include "chk.inc.php";

include "/var/www/vlav/data/www/wwl/inc/unisender.class.php";
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
include "/var/www/vlav/data/www/wwl/inc/tg_bot.class.php";
include "/var/www/vlav/data/www/wwl/inc/insales.class.php";
//chdir("../d/1000/");
chdir("../d/725690976/"); //VitaBridge
//chdir("../d/1815529005/"); //BIOMAGIC Бады
//chdir("../d/2316230027/"); //TESTBREW
//chdir("../d/319261745/"); //DIVNO
//chdir("../d/2741317649/"); //AO
//chdir("../d/3447271878/"); //kiberpravo
//chdir("../d/3771153172"); //julia
//chdir("../d/1416650876"); //yoga
//chdir("../d/2788221432"); //talalay
//chdir("../d/4033496702"); //nasledniki
//chdir("../d/4009429771"); //anikieva antilopa
//chdir("../d/766302424"); //anikieva 108
//chdir("../d/2286445522"); //конгресс
//chdir("../d/3947455183"); //insales demo_11
//chdir("../d/1801126452"); //insales demo_app
//chdir("../d/1923582808"); //- Vegannova -
include "init.inc.php";

//~ $r=$db->fetch_assoc($db->query("SELECT * FROM cards WHERE uid='-8615'"));
//~ print_r($db->split_fio($r['name']." ".$r['surname']));
//~ exit;

$in=new insales($insales_id,$insales_shop);
//~ $uid=-1008;
//~ $res=$in->create_promocode(['code'=>'чай123','type_id'=>1, 'discount'=>10]);
//~ $db->print_r($res);
//~ print "Ok";
//~ exit;
print "<br>ctrl_id=$ctrl_id <br>";
$uid=-1007;

			$sku='VB04'; $promocode='VITABRIDGe3634'; $pid=2;
			$fee_1=0; $fee_2=0;
			if($promocode) {
				$tm=time();
				if($r_p=$db->fetch_assoc($db->query("SELECT * FROM promocodes WHERE
							promocode LIKE '$promocode'
							AND product_id='$pid'
							AND cnt!=0
							AND (tm1<$tm ANd tm2>$tm)"))) {
					$klid=$db->get_klid_by_uid($r_p['uid']);
					$user_id=$db->get_user_id($klid);
					$fee_1=$r_p['fee_1'];
					$fee_2=$r_p['fee_2'];
				}
			}
			print "HERE_$pid $fee_1 $fee_2";

print "<br>OK";
exit;


$res=$db->query("SELECT * FROM product WHERE del=0");
while($r=$db->fetch_assoc($res)) {
	$pid=$r['id_product'];
	$id=$r['id']+10;
	
	$db->query("UPDATE product SET id='$id' WHERE id_product='$pid'",0);
	print "{$r['id']} {$r['sku']} {$r['descr']}  {$r['fee_1']}  {$r['fee_2']} <br>";
}
print "HERE_$res";

exit;
$in->print_r($in->get_clients($updated_since = null, $from_id = null, $per_page = 10, $search_arr=['phone'=> '79111234567']));
$res=$in->bonus_create($client_id=151936425, $amount=1000, $descr='test');
//$client_id=$db->dlookup("tool_uid","cards2other","uid='$uid' AND tool='insales'");
//print "<br>HERE_ $client_id <br>";
print "OK";

exit;
print_r( $in->search_client($uid));
//exit;
print_r($in->bonus_create($client_id, $amount=1000, $descr='Бонус при регистрации'));
//$db->print_r($in->create_client(-8719));
print "OK";
?>
