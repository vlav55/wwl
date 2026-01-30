<?
include "land_top.inc.php";
?>
		<div class="youtube my-4">
			<div id="player"></div>
			<script>
			   var player = new Playerjs({id:"player",
				   file:"https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/Promo/winwinland_for_ecommerce/master.m3u8",
				   poster:"https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/Promo/winwinland_for_ecommerce/poster.jpg"
				   });
			</script>
		</div>
<?

include "land_bottom.inc.php";

exit;

include "/var/www/vlav/data/www/wwl/inc/unisender.class.php";
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
include "/var/www/vlav/data/www/wwl/inc/tg_bot.class.php";
//chdir("../d/4009429771/"); //anikieva
//chdir("../d/1000/");
//chdir("../d/2741317649/"); //AO
//chdir("../d/3447271878/"); //kiberpravo
//chdir("../d/3771153172"); //julia
//chdir("../d/1416650876"); //yoga
//chdir("../d/2788221432"); //talalay
//chdir("../d/4033496702"); //nasledniki
//chdir("../d/766302424"); //anikieva 108
//chdir("../d/2286445522"); //конгресс
//chdir("../../d/3947455183"); //insales demo_11
//chdir("../d/1801126452"); //insales demo_app
//chdir("../d/1923582808"); //- Vegannova -
include "init.inc.php";
$db=new vkt($database);

$db=new db('test');
$res=$db->query("SELECT * FROM insales_1 WHERE email!='' OR mob!=''");
$out="phone,email\n";
while($r=$db->fetch_assoc($res)) {
	$out.="{$r['mob']},{$r['email']}\n"; 
}
if(file_put_contents("insales_retarget.csv",$out))
	print "OK ".getcwd();
else
	print "ERR";
exit;

print getcwd()."<br>";
print "HERE_$ctrl_id $insales_id $insales_shop<br>";
include_once "/var/www/vlav/data/www/wwl/inc/insales.class.php";
$in=new insales($insales_id,$insales_shop);
if($ctrl_id==167) {
	$in->id_app="winwinland_demo_11";
	$in->secret_key='e5697c177c0f51497d069969e170dbcb';
	$in->get_credentials();
}
$passw=md5($in->token.$in->secret_key);


print "https://$in->id_app:$passw@$in->shop/admin/clients.json<br>";


//$db->notify_me("1=$in->id_app 2=$in->secret_key");
//$in->ctrl_id=$ctrl_id;
//$res=$in->get_order($order_id=135774253); //135605814 for 11
//$res=$in->get_clients($updated_since = null, $from_id = null, $per_page = 10);
//$res=$in->get_account();
//$res=$in->create_promocode(['code'=>'p125','type_id'=>1,'discount'=>23]);
//$res=$in->create_client("Петров Иван", "89119990000", "9119990000@mail.ru", $password = null,true);
//$res=$in->bonus_create($client_id=86677817, $amount=1234, $descr='Бонус при регистрации');
$res=$in->get_webhooks();
//$in->ctrl_id=$ctrl_id; $res=$in->check_webhooks($insales_id);
//$res=$in->webhook_create("https://for16.ru/d/3947455183/insales_webhook.php", "orders/create");
//$res=$in->webhook_del($webhook_id=23641434);
$in->print_r($res);

exit;
?>
