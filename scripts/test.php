<?
include "/var/www/vlav/data/www/wwl/inc/unisender.class.php";
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
include "/var/www/vlav/data/www/wwl/inc/tg_bot.class.php";
include "/var/www/vlav/data/www/wwl/inc/insales.class.php";
include "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
include "/var/www/vlav/data/www/wwl/inc/cashier.class.php";
chdir("../d/1000/");
//chdir("../d/1237327324/"); //POLINA TEST 148
//chdir("../d/169655664/"); //TOPLASER
//chdir("../d/2338935559/"); //BERMUDA
//chdir("../d/1677769895/"); //recplace
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

$db=new db('vkt1_251');

$db->query("UPdate lands SET land_num=1 WHERE  id=1");

$res=$db->query("SELECT * FROM lands WHERE 1");
while($r=$db->fetch_assoc($res)) {
	print "id={$r['id']} land_num={$r['land_num']} {$r['land_name']} del={$r['del']} <br>";
}


print "<br>OK";
exit;

$db->query("SELECT uid FROM users WHERE id=3",1);

//~ $tg=new tg_bot($tg_bot_msg);
//~ print "<br>HERE_".$tg->send_photo($chat_id=315058329,$path="/var/www/vlav/data/www/wwl/scripts/qrcode.png",$caption=getcwd());


print "<br>OK";
exit;
$land_num=5;
$land_url_last="https://www.for16.ru/8";
$r=parse_url($land_url_last);
$path=!empty($r['path']) ? $r['path'].'' : '';

$path= preg_replace('/(?<=\/|^)\d+(?=\/?$)/', $land_num, $path);	
$land_url=$r['scheme']."://".$r['host'].$path;
print "HERE_$land_url_last $land_url {$r['path']}";

print "<br>OK";
exit;

$txt="loyalty_20        
winwinland_bizmall_podcast       
winwinland_ref_program  
wwl_loyalty_20.draft
loyalty_20_preza  
WinWinLand_ecommerce_2           
winwinland_simply       
wwl_loyalty_partner_cabinet
toplaser          
winwinland_finalist_accelerator  
winwinland_who
toplaser_case     
winwinland_for_ecommerce         
winwinland_work
";

$arr=explode("\n",$txt);
foreach($arr AS $r) {
	print "<a href='https://winwinland.ru/tube/Promo/$r' class='' target=''>https://winwinland.ru/tube/Promo/$r</a><br>";
}


exit;

include_once "/var/www/vlav/data/www/wwl/inc/users_log.class.php";
class test extends db {
	function t() {
		$l=new users_log($this->database);
		print "<br>HERE_$this->database";
	}
}
$u=new test('vkt');
$u->t();
print "<br>OK";
exit;

$v=new vkt('vkt');
$v->test_microtime(__LINE__);
//$v->vkt_create_account($uid=-9139,$product_id=false);
//sleep(2);
$v->test_microtime(__LINE__);

$v->print_runtime_log();
//$v->ctrl_days_end_set(232,5);
print "<br>OK";
exit;



include_once "/var/www/vlav/data/www/wwl/inc/qrcode.class.php";
$qr = new qrcode_gen();
if ($res=$qr->generate('https://example.com', 'qrcode.png')) {
	//$qr->display($res);
    echo "QR code created successfully!";
} else
	print "ERR";

exit;
$url = "https://example.com/page.php?param1=value1&param2=value2";
print "HERE_". strtok($url, '?');
print "OK";

exit;

require_once '/var/www/vlav/data/www/wwl/inc/qr_code/vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Create a QR code
$qrCode = new QrCode('https://for16.ru/d/1000/cashier.php?p=бьюти5694');
$writer = new PngWriter();

// Create a QR code PNG file
$result = $writer->write($qrCode);
$result->saveToFile(__DIR__.'/qrcode.png');
//$db->notify_me("HERE_".__DIR__.'/qrcode.png');

// Output the QR code to the browser
header('Content-Type: '.$result->getMimeType());
echo $result->getString();

exit;

require_once '/var/www/vlav/data/www/wwl/inc/pact.class.php';
$p=new pact($pact_secret,$pact_company_id);
$fname=__DIR__.'/qrcode.png';
$cid=$p->get_cid($db,$uid=-1001);
$res=$p->upload_attachment($fname,$cid);
$db->notify_me("HERE_$res");
$p->attach=[$res];
$p->send_msg($cid,"Промокод на скидку 10% в салоне красоты - бьюти5694
Ждем Вас по адресу: Невский пр. 16");

exit;

$env = file_get_contents("/etc/wwl/.env");
if (!$env) {
    die('Invalid configuration file');
}

print_r(json_decode($env,true));

exit;

		$r=[
			'tm'=>0, //for new uid - tm=time() if 0
			'uid'=>0, //если не найдет в базе то выход с ошибкой
			'man_id'=>0,
			'first_name'=>'Вася',
			'last_name'=>'Иванов',
			'phone'=>'+7-000-9998877',
			'email'=>'9998877@mail.ru',
			'city'=>'СПб',
			'tg_id'=>'123456789', //if not 0 will be added
			'tg_nic'=>'qwerty', //if not empty will be added
			'vk_id'=>'123456789', //if not 0 will be added
			'razdel'=>'3', //default 4 (D)  will added
			'source_id'=>'1', //0
			'user_id'=>'0',
			'klid'=>'0',
			'wa_allowed'=>'0',
			'comm1'=>'12345',
			'tz_offset'=>'3',
			'test_cyrillic'=>false
		];
print $db->cards_add($r);

exit;

$vkt=new vkt($database);

for ($i = 100; $i < 300; $i+=1)
{
print $n=$vkt->encode_ctrl_id($i) ." $i ";
print $vkt->decode_ctrl_id($n) ."<br>";
}


exit;

$pat = '/\{\{promocode\s+([\w\-]+)\s+(for_price|for_discount)\s+([0-9]+)\s+([\w\.]+)\s+(\d\d:\d\d|\d\d|\d)\s+\[([\d\,]+)\]\s+([\d]+)\s+([\d]+)\s+([\d]+)(?:\s+(hide|show))?(?:\s+(\d+))?(?:\s+(1|0))?\}\}/u';
$msg="test {{promocode promo12345 for_price 20000 31.12.2025 23:59 [30] 20 5 0 hide 123 1}} test";
if(preg_match($pat, $msg, $m)) {
	$db->print_r($m);
} else
	print "NOT MATCH";


exit;

$res=$db->query("SELECT * FROM csrf WHERE 1");
while($r=$db->fetch_assoc($res)) {
	print "{$r['token']}<br>";
}
print "<br>ok";
exit;

$res=$db->query("SELECT * FROM product WHERE del=0 ORDER BY id");
$n=0;
$out="";
while($r=$db->fetch_assoc($res)) {
	$n++;
	$out.= "{$r['id']}|{$r['descr']}|{$r['term']}|{$r['price0']}|{$r['price1']}|{$r['price2']}\n";
}
$fname="yoga_products.csv";
file_put_contents($fname,$out);
print "ok $n  <a href='../d/1416650876/$fname' class='' target=''>$fname</a> ".getcwd();

exit;

$sku="333/22";
$pid=1;
if(!empty(trim($sku))) {
	$res_sku=$db->query("SELECT * FROM product WHERE sku LIKE '%$sku%'");
	while($r_sku=$db->fetch_assoc($res_sku)) {
		$arr=preg_split('/[\s,]+/', $r_sku['sku']);
		$db->print_r($arr);
		foreach($arr AS $pat) {
			if(trim($pat)==$sku) {
				$pid=$r_sku['id'];
				break;
			}
		}
		if($pid>1)
			break;
	}
}
print "HERE_$pid <br>";

exit;

$jsonData = file_get_contents('php://input');
if($jsonData) {
	// Decode the JSON data into a PHP associative array
	$data = json_decode($jsonData, true);
	file_put_contents("test.log",print_r($data,true));
	print "webhook Ok";
	exit;
}

$db=new vkt_send($database);
$db->ctrl_id=$ctrl_id;
$msg="test {{webhook https://for16.ru/scripts/test.php pay}} test";
$db->vkt_send_msg_order_id=563;
print $db->prepare_msg_webhook(-1002,$msg);
exit;


chdir("../../scripts");

// URL to send the POST request to
$url = 'https://for16.ru/d/1000/novofon_webhook.php';

// Data to be sent in the POST request
$data = array(
    'key1' => 'value1',
    'key2' => 'value2'
);

// Initialize cURL
$ch = curl_init($url);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

// Execute the POST request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
} else {
    // Output the response
    echo 'Response from server: ' . $response;
}

// Close the cURL session
curl_close($ch);


exit;
$db->notify_chat(-4799845674,"test");

print md5(189);
exit;

$res=$db->query("SELECT *,msgs.tm AS tm FROM cards JOIN msgs ON msgs.uid=cards.uid WHERE man_id=245 AND del=0 AND outg=1 ORDER BY msgs.tm DESC");
$n=1;
while($r=$db->fetch_assoc($res)) {
	if(preg_match("/https:\/\/winwinland.ru\/\?uid=/",$r['msg'])) { ///https:\/\/winwinland.ru\/\?uid=/
		$dt=date("d.m.Y",$r['tm']);
		print "$n $dt MATCH {$r['uid']} <br>";
		print nl2br($r['msg'])."<hr>";
		$n++;
	}
}
exit;

exit;
$res=$db->query("SELECT * FROM cards JOIN tags_op ON cards.uid=tags_op.uid WHERE tag_id=27");
$n=1;
$out="phone,email\n";
while($r=$db->fetch_assoc($res)) {
	if($r['mob_search']=='0' && empty($r['email']))
		continue;
	if(empty($r['mob_search']) && empty($r['email']))
		continue;
	$uid=$r['uid'];
	print "$n $uid 	<br>";
	$n++;
	$out.=$r['mob_search'].",".$r['email']."\n";
}
file_put_contents("tmp/cards_insales.csv",$out);
print "<p><a href='tmp/cards_insales.csv' class='' target=''>tmp/cards_insales.csv</a></p>";
exit;

$res=$db->query("SELECT *,msgs.tm AS tm FROM cards JOIN msgs ON msgs.uid=cards.uid WHERE man_id=245 AND del=0 AND outg=1");
$n=1;
while($r=$db->fetch_assoc($res)) {
	if(preg_match("/https:\/\/winwinland.ru\/\?uid=/",$r['msg'])) { ///https:\/\/winwinland.ru\/\?uid=/
		$dt=date("d.m.Y",$r['tm']);
		print "$n $dt MATCH {$r['uid']} <br>";
		print nl2br($r['msg'])."<hr>";
		$n++;
	}
}
exit;


$res=$db->query("SELECT * FROM 0ctrl WHERE del=0 AND insales_shop_id>119 AND id=170");
while($r=$db->fetch_assoc($res)) {
	$ctrl_id=$r['id'];
	$insales_id=$r['insales_shop_id'];
	$insales_shop=$r['insales_shop'];
	print "<br>$ctrl_id $insales_id $insales_shop<br>	";
	$in=new insales($insales_id,$insales_shop);
	$arr=$in->get_webhooks();
	if(!$arr['error']) {
		foreach($arr AS $w) {
			if(strpos($w['address'],"for16.ru/d/")) {
				print "{$w['id']} {$w['address']} {$w['topic']} <br>";
			}
		}
		usleep(100000);
	}
}


exit;

$res=$db->query("SELECT * FROM 0ctrl WHERE del=0");
while($r=$db->fetch_assoc($res)) {
	$uid=$r['uid'];
	if(!$db->dlookup("uid","cards","del=0 AND uid=$uid")) {
		$db->tag_del($uid,29);
		print "$uid <br>";
	} else
		$db->tag_add($uid,29);
}
print "Ok";
exit;

print "HERE_$insales_id $insales_shop<br>";
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
//$res=$in->get_webhooks();
$in->ctrl_id=$ctrl_id; $res=$in->check_webhooks($insales_id=5785245);
//$res=$in->webhook_create("https://for16.ru/d/3947455183/insales_webhook.php", "orders/create");
//$res=$in->webhook_del($webhook_id=23641434);
$in->print_r($res);

exit;

$wwl=file("/etc/wwl/wwl_creditials.txt");
print_r($wwl);
exit;

include_once "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
$land_num_insales=9;
$uid=-8404;
$s=new vkt_send('vkt');
$res_s=$db->query("SELECT * FROM vkt_send_1 WHERE del=0 AND sid=12 AND land_num='$land_num_insales'",0);
while($r=$db->fetch_assoc($res_s)) {
	$s->vkt_send_task_add(1, $tm_event=intval(time()+$r['tm_shift']), $vkt_send_id=$r['id'],$vkt_send_type=3,$uid);
}
print "OK";
exit;

if(!isset($_GET['ctrl_id'])) {
	print "?ctrl_id="; exit;
}
$uid=$db->vkt_delete_account(intval($_GET['ctrl_id']));
$db->query("UPDATE avangard SET res=0 WHERE vk_uid='$uid'");
$db->query("UPDATE cards SET email='',mob='',mob_search='',del=1 WHERE uid='$uid'");
print "$uid deleted";

exit;

$res=$db->query("SELECT * FROM 0ctrl WHERE del=0");
while($r=$db->fetch_assoc($res)) {
	$db->tag_add($r['uid'],29);
}

exit;

if($db->vkt_create_account($uid=-8364,$product_id=21))
	print "OK"; else print "FALSE";



exit;

$res=$db->query("SELECT * FROM vkt_send_1 WHERE msg LIKE '%winwinland_chat%'");
while($r=$db->fetch_assoc($res)) {
	$msg=$r['msg'];
	$msg=preg_replace("/winwinland_chat/","winwinland_ru",$msg);
	//~ $msg = preg_replace("/https:\/\/drive\.google\.com\/file\/d\/1exLqUHaAc78DA5zBUhFTq_AyvYpL3IU8\/view\?usp=drive_link/", "https://winwinland.ru/pdf/winwinland.pdf", $msg);
	print "{$r['id']} <br> $msg <hr>";
	$db->query("UPDATE vkt_send_1 SET msg='".$db->escape($msg)."' WHERE id={$r['id']}");
}

exit;

	function gpt_get_messages($uid,$limit=50) {
		global $db;
		if($last_id=$db->dlast("id","msgs","uid='$uid' AND acc_id=10")) {
			$res=$db->query("SELECT * FROM msgs
					WHERE uid='$uid' AND id>=$last_id AND (outg=0 OR outg=1 OR outg=2)
					ORDER BY id ASC");
		} else {
			$num_rows=$db->num_rows($db->query("SELECT id FROM msgs WHERE uid='$uid' AND source_id=0 AND (outg=0 OR outg=1)"));
			$from=$num_rows-$Limit;
			if($from<0)
				$from=0;
			$res=$db->query("SELECT * FROM msgs
					WHERE uid='$uid' AND source_id=0 AND (outg=0 OR outg=1)
					ORDER BY id ASC
					LIMIT $from,$limit");
		}
		$arr[]=['role' => 'system', 'content' => "You are a large language model.
Carefully heed the user's instructions.
Respond without Markdown."];
		while($r=$db->fetch_assoc($res)) {
			$role=($r['outg']==0)?"user":"assistant";
			$arr[]=['role' => $role, 'content' => $r['msg']];
		}
		return $arr;
	}

	
$db->print_r($arr=$db->gpt_get_messages(-1002,$limit=50));
//$msg_gpt= $db->vsegpt($vsegpt_secret,$arr,$vsegpt_model);
print "<hr>";
print nl2br($msg_gpt);

exit;

include_once '/var/www/vlav/data/www/wwl/inc/vkt_send.class.php';
$db=new vkt_send('vkt');
$db->pact_secret='6dac370b7133847c9230239533b7a0a1667cfd1ff30ee9695a5861b7fe6b662aacdb3988b032656858b10231f937ad2025d96a34c862a54b83435e0282b2c318';
$db->pact_company_id=85300;
$db->query("DELETE FROM vkt_send_log WHERE uid=-1002 AND tm_event=1735164663");
$db->vkt_send_task_0ctrl(190,1,-1002,1735164663);


exit;

$tm=time()-(365*24*60*60);
$res=$db->query("SELECT * FROM cards WHERE del=0 AND mob_search!='' AND tm>$tm ORDER BY id DESC LIMIT 400");
$n=0;
while($r=$db->fetch_assoc($res)) {
	print "{$r['mob_search']}<br>";
	$n++;
}

exit;

function link_shortener($url) {
   // Parse the URL into its components
    $parsed_url = parse_url($url);
    
    // Get the domain
    $domain = $parsed_url['scheme'] . '://' . $parsed_url['host'];
    
    // Parse the query string into an array
    parse_str($parsed_url['query'] ?? '', $params);
    
    // Generate a sorted string from the parameters to ensure consistent hashing
    ksort($params); // Sort parameters by key for consistency
    $param_string = http_build_query($params);
    
    // Create a unique hash using md5 and convert it to a base-26 string
    $hash = md5($param_string);
    $short_symbol_code = base_convert(hexdec(substr($hash, 0, 8)), 10, 26); // Convert hash to base 26
    
    // Convert numbers to letters (a -> 0, b -> 1, ..., z -> 25)
    $short_symbol_code = preg_replace_callback('/\d/', function ($matches) {
        return chr($matches[0] + 97); // Convert 0-25 to 'a'-'z'
    }, $short_symbol_code);
    
    // Return the shortened link
    return $domain . "/?" . rtrim($short_symbol_code, '0'); // Remove any trailing zeros
}
print link_shortener("https://wwl.winwinland.ru/7?bc=1002");

exit;
$res=$db->query("SELECT * FROM cards JOIN usres=
	WHERE ");


exit;
$tm=$db->date2tm("01.09.2024");
//$res=$db->query("SELECT * FROM msgs WHERE tm>$tm AND source_id=1002",1);
$res=$db->query("SELECT * FROM avangard WHERE res=1 AND product_id>=100 AND amount>100",0);
$n=0;
while($r=$db->fetch_assoc($res)) {
	$uid=$r['vk_uid'];
	print "$uid {$r['amount']}<br>";
	//$db->query("UPDATE cards SET fl=0 WHERE uid=$uid");
	$db->tag_add($uid,4);
	$n++;
}
print "OK $n";
exit;

$dt='22-9-2024';
$tm=strtotime($dt);
print date("d-m-Y H:i:s",$tm);
//exit;

$arr=array(
    'first_name' => "Лилия",
    'last_name' =>'' ,
    'phone' => "+79235313108",
    'email' => "minbaeva-lili@mail.ru",
    'order_number' => '0391',
    'offers' => 6405453,
    'positions' => "Тестовое предложение",
    'payed_money' => "10 руб.",
    'status' => "Завершен",
);
foreach($arr AS $key=>$val)
	print "$key=$val&";

exit;
$card=[
	'uid'=>0, //если не найдет в базе то выход с ошибкой
	'first_name'=>'Вася',
	'last_name'=>'Иванов',
	'phone'=>'+7-000-9999999',
	'email'=>'123456789@mail.ru',
	'city'=>'СПб',
	'tg_id'=>'123456789',
	'tg_nic'=>'qwerty',
	'vk_id'=>'123456789',
	'razdel'=>'3', //2 
	'source_id'=>'0', //0
	'user_id'=>'202',
	'klid'=>'8278',
	'wa_allowed'=>'0',
	'comm1'=>'12345',
	'tz_offset'=>'3',
	'test_cyrillic'=>false
];
if($db->cards_add($card))
	print "OK"; else print "false";



exit;

	$base64="YWRtaW46NmNhZDg2N2YzN2M0";
	$url="https://alfa-winwinland.server.paykeeper.ru/./info/settings/token/";
	$headers=Array(); 
	array_push($headers,'Content-Type: application/x-www-form-urlencoded');
	array_push($headers,'Authorization: Basic '.$base64);

	$curl=curl_init();
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl,CURLOPT_URL,$url);
	curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'GET');
	curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
	curl_setopt($curl,CURLOPT_HEADER,false);

	# Инициируем запрос к API
	$response=curl_exec($curl); 

   if ($response === false) {
       echo 'cURL Error: ' . curl_error($curl);
   } else {
       echo 'Response: ' . $response;
   }

   curl_close($curl);

exit;


$res=$db->query("SELECT * FROM cards WHERE 1 ");
while($r=$db->fetch_assoc($res)) {
	$uid=$r['uid'];
	$uid_md5=$db->uid_md5($uid);
	if($r['uid_md5']!=$db->uid_md5($uid)) {
		print "{$r['id']} uid=$uid"." ".$r['uid_md5']." --- ".$uid_md5."<br>";
		$db->query("UPDATE cards SET uid_md5='$uid_md5' WHERE uid='$uid'");
	}
}
exit; 

$res=$db->query("SELECT uid, MAX(tm) AS tm FROM msgs WHERE source_id=50 GROUP BY uid");
while($r=$db->fetch_assoc($res)) {
	$uid=$r['uid'];
	$tm=$r['tm'];
	print "$uid $tm<br>";
	//$db->query("INSERT INTO tags_op SET uid='$uid',tag_id=25,tm=$tm");
}

exit;

$res=$db->query("SELECT vk_uid,SUM(amount) AS s FROM avangard JOIN cards ON vk_uid=cards.uid WHERE res=1 AND amount>10 GROUP BY vk_uid");
while($r=$db->fetch_assoc($res)) {
	print "{$r['vk_uid']} {$r['s']}<br>";
	//$db->query("UPDATE cards SET razdel='3' WHERE uid={$r['vk_uid']}");
}

exit;

$res=$db->query("SELECT * FROM vkt_send_log WHERE vkt_send_id=87");
while($r=$db->fetch_assoc($res)) {
	$dt=date("d.m.Y H:i",$r['tm']);
	print "$dt <br>";
}

print "OK";
exit;

$api_key = 'sk-or-vv-fcd452dc948a76370a3637fa8d88f970e6a9744fb97f00ca6d10b98ad2668174';
$arr=$db->gpt_get_messages(-19093,$limit=50);

$prompt="Твоя роль продавец онлайн курсов йоги.
	Тебя зовут Юлия.
	Отвечай на вопросы клиента с целью продать курс йоги.
	Обращайся к клиенту на 'вы' и говори как с пожилым человеком.
	Не предлагай клиенту консультироваться с врачом или получать информацию из любых других источников.
	Заканчивай каждое свое сообщение вопросом подводящим пользователя к покупке курса.
	Клиент только что прошел пробное занятие сейчас ему нужно продать полный курс йоги.
	Сейчас представься и спроси можешь как впечатление от пробного занятия и все ли понравилось.";
$arr[]=['role' => 'user', 'content' => $prompt];

print $db->vsegpt($api_key,$arr,$model='openai/gpt-3.5-turbo');

exit;

$res=$db->query("SELECT * FROM sources WHERE 1");
while($r=$db->fetch_assoc($res)) {
	$arr[]=$r['id'];
}
print_r($arr);
$res=$db->query("SELECT * FROM cards WHERE del=0");
while($r=$db->fetch_assoc($res)) {
	if(!in_array($r['source_id'],$arr))
		$db->query("UPDATE cards SET source_id=0 WHERE id=".$r['id']);
}

exit;
$res=$db->query("SELECT *,cards.id AS id, cards.del AS del FROM cards
         WHERE (cards.user_id='5' OR man_id='5') AND  (cards.user_id='5' OR man_id='5') AND  cards.del=0 AND  tm_lastmsg>0 ORDER BY tm_lastmsg DESC");
print "OK_".$db->num_rows($res);

exit;
$db=new db('vkt');
$tm=time()+(1*60);
$db->query("INSERT INTO 0ctrl_vkt_send_tasks SET tm='$tm',ctrl_id='101',vkt_send_id=4,vkt_send_type=3,uid=-19093");
print "OK ".date("d.m.Y H:i:s",$tm);
exit;

include "/var/www/vlav/data/www/wwl/inc/s3.class.php";
$s3=new s3("yogahelpyou");
$arr=$s3->list_folders("yogahelpyou");
$out="";
foreach($arr AS $val) {
	if(preg_match("/promo\/seminar_cut\/(.*?)$/",trim($val),$m)) {
		$out.= "\$seminar_cut[]=['title'=>'".$m[1]."','url'=>'".rawurlencode($val)."/master.m3u8'];\n";
	}
}
file_put_contents("seminar_cut.txt",$out);
print getcwd()."/seminar_cut.txt";
exit;

require "/var/www/vlav/data/www/wwl/inc/s3/vendor/autoload.php";
 
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\S3\Exception\S3Exception;
// Создание клиента
$s3 = new S3Client([
   "version" 	=> "latest",
   "region"  	=> "ru-1",
   "use_path_style_endpoint" => true,
   "credentials" => [
   	"key"	=> "1f7cfef5bd48487aa5042dba18c34e1a",
   	"secret" => "98789b6aea58449dba2290e94e27aea1",
   ],
   "endpoint" => "https://s3.storage.selcloud.ru"
]);

$bucket = 'yogahelpyou';

$objects = [];
$nextToken = null;

do {
    $params = [
        'Bucket' => $bucket,
        'MaxKeys' => 1000, // Maximum number of objects to fetch in a single request
    ];

    if ($nextToken) {
        $params['ContinuationToken'] = $nextToken;
    }

    try {
        $result = $s3->listObjectsV2($params);
        
        $objects = array_merge($objects, $result['Contents']);
        
        $nextToken = $result['NextContinuationToken'] ?? null;
    } catch (AwsException $e) {
        echo $e->getMessage() . "<br>";
        break;
    }
} while ($nextToken !== null);

print "HERE_".sizeof($objects);
$arr=[];
foreach ($objects as $object) {
	if(!in_array(dirname($object['Key']),$arr))
		$arr[]= dirname($object['Key']);
}
print nl2br(print_r($arr,true));


exit;

$res=$db->query("SHOW DATABASES LIKE 'vkt_test%'");
$out="";
while($r=$db->fetch_assoc($res)) {
	$out.= "DROP DATABASE {$r['Database (vkt_test%)']};\n";
}
file_put_contents("drop.txt",$out);
print "OK";
exit;
$res=$db->query("SELECT * FROM cards WHERE del=0");
while($r=$db->fetch_assoc($res)) {
	if(!$db->is_cyrillic($r['name']) && !$db->is_cyrillic($r['city'])) {
		$uid=$r['uid'];
		print "$uid {$r['name']} {$r['city']} {$r['email']} <br>";
		$db->query("UPDATE cards SET del=1 WHERE uid='$uid'");
	}
}

exit;

function chk_empty_fields($query) {
		$arr=['name'];
		preg_match_all("/\bUPDATE cards SET\s+(.+?)\s+WHERE\b/i", $query, $matches);
		if(isset($matches[1][0])) {
			$assignments = $matches[1][0];
			$assignmentsArr = explode(",", $assignments);
			
			$pairs = [];
			foreach($assignmentsArr as $assignment) {
				$parts = explode("=", $assignment);
				$field = trim($parts[0]);
				$value = trim($parts[1], " '");
				if(in_array($field,$arr) &&  empty($value))
					return true;
			}
		}
		return false;
}

$sql = "UPDATE cards SET name='12',email='' WHERE 1=2";

if (chk_empty_fields($sql)) {
    echo "TRUE";
} else {
    echo "FALSE";
}

exit;

$db=new db('vkt_test_1');

$res=$db->query("SELECT * FROM cards WHERE name='' AND surname=''");
$n=1;
while($r=$db->fetch_assoc($res)) {
	$dt=date("d.m.Y H:i",$r['tm']);
	$dt_lastmsg=date("d.m.Y H:i",$r['tm_lastmsg']);
	print "$n {$r['uid']} $dt $dt_lastmsg email={$r['email']} mob={$r['mob']} {$r['mob_search']} {$r['telegram_id']}<br>";
	$n++;
}

exit;
$s=new vkt_send($database);
$uid=-12116;
$tm_event=$s->date2tm("06.04.2024");
$tm_end=$tm_event+(30*24*60*60);
$land_num=71;
$res=$s->query("SELECT * FROM vkt_send_1 WHERE (sid=30 OR sid=31) AND land_num='$land_num'",0);
while($r=$db->fetch_assoc($res)) {
	continue;
	if($r['sid']==30) {
		$s->vkt_send_task_add($ctrl_id, $tm_event=intval(time()+$r['tm_shift']), $vkt_send_id=$r['id'],$vkt_send_type=3,$uid);
	} elseif($r['sid']==31 && $tm_end) {
		if($s->vkt_send_task_add($ctrl_id, $tm_event=intval($tm_end+$r['tm_shift']), $vkt_send_id=$r['id'],$vkt_send_type=3,$uid))
			print "Event set at ".date("d.m.Y H:i",$tm_event)." <br>"; else print "false <br>";
	}
}
$s->connect('vkt');
$res=$s->query("SELECT * FROM 0ctrl_vkt_send_tasks WHERE ctrl_id=$ctrl_id");
while($r=$s->fetch_assoc($res)) {
	$dt=date("d.m.Y H:i",$r['tm']);
	print "{$r['id']} $dt {$r['vkt_send_id']} {$r['vkt_send_type']} {$r['uid']} <br>";
}
print "OK";
exit;

$db=new db($database);
		$uid="111111";
		$msgs[]=	"test {{price2 48 23:59 [30]}} test"; //30 - product_id
		$msgs[]=	"test {{price2 tomorrow  23:59 [30]}} test"; //30 - product_id
		$msgs[]=	"test {{price2 today 23:59 [30]}} test"; //30 - product_id
		$msgs[]=	"test {{price2 for 3 [30,31]}} test"; //30 - product_id
		
		foreach($msgs AS $msg) {
			//~ $pat = '/\{\{price2\s+(\w+)\s+(\d\d:\d\d|\d\d|\d)\s+\[([\w\,]+)\]\}\}/';
			//~ if(preg_match($pat,$msg,$m)) {
				//~ print_r($m);
			//~ }
			$db->prepare_msg_price2($uid,$msg);
			print "<br><br>";
		}

exit;
$db=new db($database);
print "$database <br>";
$res=$db->query("SELECT * FROM cards WHERE 1");
while($r=$db->fetch_assoc($res)) {
	$uid=$r['uid'];
	$uid_md5=$db->uid_md5($uid);
	$err=($uid_md5 !=$r['uid_md5']) ? "ERR" : "OK";
	print "$uid $uid_md5 {$r['uid_md5']} $err<br>";
	$db->query("UPDATE cards SET uid_md5='$uid_md5' WHERE uid=$uid");
}

exit;

// Get the current system time in microseconds as a float
$timeInMicrosecondsFloat = microtime(true);

// Split the float into seconds and microseconds
list($seconds, $microseconds) = explode(' ', $timeInMicrosecondsFloat);

// Combine seconds and microseconds into a single integer value
$timeInMicrosecondsInt = (int)($seconds * 1000000 + $microseconds);

echo $timeInMicrosecondsInt; // Output the time in microseconds as an integer
//1708417179758500

exit;

$res=$db->query("SELECT * FROM cards WHERE email=''");
while($r=$db->fetch_assoc($res)) {
	$uid=$r['uid'];
	$db->query("UPDATE cards SET fl=0 WHERE uid='$uid'");
}
$res=$db->query("SELECT * FROM vkt_send_log WHERE res_email=1");
while($r=$db->fetch_assoc($res)) {
	$uid=$r['uid'];
	$db->query("UPDATE cards SET fl=0 WHERE uid='$uid'");
}
print "DONE";

exit;

if($db->ch_land_num(2,100))
	print "OK"; else print "FALSE";


exit;

$res=$db->query("SELECT uid FROM `msgs` WHERE user_id=119 GROUP BY uid");
while($r=$db->fetch_assoc($res)) {
	$uid=$r['uid'];
	print "$uid <br>";
	$db->query("INSERT INTO tags_op SET tag_id=20,uid='$uid',tm='".time()."',user_id=1");
}

exit;
$res=$db->query("SELECT * FROM cards WHERE del=0 ORDER BY tm");
$n=1;
while($r=$db->fetch_assoc($res)) {
	if($db->check_mob($r['name']) && empty($r['email'])) {
		$dt=date("d.m.Y",$r['tm']);
		$uid=$r['uid'];
		print "$n $dt $uid {$r['name']} <br>";
		$n++;
		$db->query("DELETE FROM cards WHERE uid='$uid'");
		$db->query("DELETE FROM msgs WHERE uid='$uid'");
	}
}

print "OK";
exit;


$t=new top('vkt',"TEST",false);
$db_js=new db_js;
$db_js->js_select_partner("test",$GET_userid_name="userid",$GET_submit_name="set_userid",$GET_submit_clr="clr_userid");



$t->bottom();



exit;
echo '<pre>';

// Выводит весь результат шелл-команды "ls", и возвращает 
// последнюю строку вывода в переменной $last_line. Сохраняет код возврата
// шелл-команды в $retval.
$last_line = system('ls', $retval);

// Выводим дополнительную информацию
echo '
</pre>
<hr />Последняя строка вывода: ' . $last_line . '
<hr />Код возврата: ' . $retval;
exit;

$mob="79119841012";
//$_POST['vk_uid']="-1002";
//$email="vlav@mail.ru";


exit;
print intval("c667e4bf35df040b0e62b326948b100c")?"1":"0";

	$vk_uid=0;
	if(!$vk_uid && $db->check_mob($mob) )
		$vk_uid=$db->dlookup("uid","cards","mob_search='$mob'");
	if(!$vk_uid && $db->validate_email($email))
		$vk_uid=$db->dlookup("uid","cards","email='$email'");
	if(!$vk_uid && isset($_POST['vk_uid']))
		$vk_uid=$db->get_uid($_POST['vk_uid']);

print "uid=$vk_uid";

exit;




foreach($base_prices AS $pid=>$r) {
    //~ [70] => Array
        //~ (
            //~ [0] => 5500
            //~ [1] => 5200
            //~ [2] => 1900
            //~ [s1] => 0
            //~ [descr] => Вступительный взнос в клуб YOGAHELPYOU + участие, 30 дней
            //~ [term] => 30
            //~ [link] => 
            //~ [stock] => 6
            //~ [jc] => clients_4
            //~ [senler] => 0
            //~ [sp] => 0
            //~ [sp_template] => 0
            //~ [source_id] => 160
            //~ [razdel] => 3
            //~ [use] => 1
            //~ [vid] => 1
            //~ [installment] => 0
        //~ )
	$sql="INSERT INTO product SET
		id='$pid',
		price0='{$r['0']}',
		price1='{$r['1']}',
		price2='{$r['2']}',
		descr='".$db->escape($r['descr'])."',
		term='{$r['term']}',
		stock='{$r['stock']}',
		jc='".$db->escape($r['jc'])."',
		senler='{$r['senler']}',
		sp='{$r['sp']}',
		sp_template='".$db->escape($r['sp_template'])."',
		in_use='{$r['in_use']}',
		vid='{$r['vid']}',
		installment='{$r['installment']}'
		";
	print "$sql <br>";
	$db->query($sql);
}

exit;

	print "ctrl_id=".$vkt->get_ctrl_id_by_db($db->database);
	print "<br>";
	print "hold=".$db->get_hold_period($user_id=1);
	//print $db->get_ctrl_id_by_db("vkt1_45");
	//print $db->hold_chk(-8040)?"HOLD":"CLEAR"; 


exit;


exit;
$res=$db->query("SELECT * FROM avangard WHERE tm_end=1209600 AND res=1");
while($r=$db->fetch_assoc($res)) {
	$uid=$r['vk_uid'];
	$dt=date("d.m.Y",$r['tm']);
	$pid=$r['product_id'];
	$term=$base_prices[$pid]['term'];
	$products_sarafan=$products_winwinland;
	$tm_end=$r['tm']+($term*24*60*60);
	if($cnt= $db->avangard_payments_count($uid,$products_sarafan)==1) {
		$tm_end+=(14*24*60*60);
	}
	$dt_end=date("d.m.Y",$tm_end);
	print "{$r['id']} $uid $dt $pid $term $cnt $dt_end<br>";
	$db->query("UPDATE avangard SET tm_end=$tm_end WHERE id={$r['id']}");
}

exit;
print $db->get_hold_period($user_id);
exit;

	$db->hold_chk(-1002);
exit;

$uids=[-1004,-7557,-7822];
foreach($uids AS $uid) {
	$db->hold_chk($uid);
}

exit;
	$vk_uid=-7877;
	$product_id=35;
	$tm1=$db->date2tm("11.07.2024");
	$tm_end_last=$db->fetch_assoc($db->query("SELECT vk_uid,tm_end FROM `avangard` WHERE res=1 AND product_id='$product_id' AND vk_uid='$vk_uid' ORDER BY tm_end DESC LIMIT 1",0))['tm_end'];
	$tm_end_last=($tm_end_last>$tm1) ? $tm_end_last : $tm1;
	$tm_end=$db->dt2($tm_end_last+(intval($base_prices[$product_id]['term'])*24*60*60) );
	print date("d.m.Y",$tm_end);
exit;

$res=$db->query("SELECT * FROM 0ctrl WHERE del=0");
$dbs=[];
while($r=$db->fetch_assoc($res)) {
	$dbs[]=$db->get_ctrl_database($r['id']);
}
print "dbs array created (".sizeof($dbs).")\n";

foreach($dbs AS $database) {
	if(empty($database))
		continue;
	print "DB=".$database."<br>";
	$db->connect($database);

	$res=$db->query("SELECT * FROM cards WHERE user_id!=0 AND tm_user_id=0 LIMIT 1000");
	while($r=$db->fetch_assoc($res)) {
		$uid=$r['uid'];
		if($tm_reg=$db->dlookup("tm","msgs","uid=$uid AND source_id=12")) {
			$db->query("UPDATE cards SET tm_user_id=$tm_reg WHERE uid='$uid'");
			//print "{$r['uid']} {$r['user_id']} $tm_reg<br>";
		} else {
			$db->query("UPDATE cards SET tm_user_id={$r['tm']} WHERE uid='$uid'");
			//print "{$r['uid']} {$r['user_id']} {$r['tm']}<br>";
		}
	}
	print "Done - ".$db->num_rows($res)."<br>";
}

exit;
$url = 'https://for16.ru/d/1000/pay_prodamus_callback.php';

// Параметры запроса
$data = array(
    'payment_status' => 'success',
    'order_num' => 349
);

// Initialize cURL session
$ch = curl_init();

// Set the URL and other options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

// Execute the request
$result = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    echo 'Request sent successfully.';
}

// Close the cURL session
curl_close($ch);
exit;

print $company_name."<br>";
print $company_data."<br>";
print $company_logo."<br>";
print $DB200."<br>";

exit;
$db=new db('vkt');

$res=$db->query("SELECT * FROM cards WHERE del=0 AND telegram_id>0");
$n=0;
while($r=$db->fetch_assoc($res)) {
	if(!$db->dlookup("id","tg_public_yoga","tg_id='{$r['telegram_id']}'")) {
		print "{$r['uid']} {$r['telegram_id']}<br>";
		$n++;
		$db->query("UPDATE cards SET fl=1 WHERE uid='{$r['uid']}'");
	}
}

print "Ok $n";
exit;

$file = '/var/www/vlav/data/www/wwl/scripts/code/winwinland_chat/users.csv';

// Read the file
$handle = fopen($file, 'r');
if (!$handle) {
    die('Unable to open file');
}

// Parse the CSV data
$users = [];
while (($data = fgetcsv($handle)) !== false) {
    $user = [
        'User_id' => $data[0],
        'Username' => $data[1],
        'Имя' => $data[2],
        'Пол' => $data[3],
        'Телефон' => $data[4],
        'Последняя активность (UTC)' => $data[5]
    ];
    $users[] = $user;
}

// Close the file
fclose($handle);

// Display the parsed data
foreach ($users as $user) {
    echo "User ID: " . $user['User_id'] . "<br>";
    echo "Username: " . $user['Username'] . "<br>";
    echo "Имя: " . $user['Имя'] . "<br>";
    echo "Пол: " . $user['Пол'] . "<br>";
    echo "Телефон: " . $user['Телефон'] . "<br>";
    echo "Последняя активность (UTC): " . $user['Последняя активность (UTC)'] . "<br>";
    echo "<br>";

    if(!$db->dlookup("id","tg_public_yoga","tg_id='{$user['User_id']}'")) {
		$db->query("INSERT INTO tg_public_yoga SET
				tm='".time()."',
				tg_id='{$user['User_id']}',
				tg_nic='".$db->escape($user['Username'])."',
				f_name='".$db->escape($user['Имя'])."',
				res=1
				");
	}
}
print "OK";

exit;

$db=new vkt('vkt');
$res=$db->query("SELECT * FROM 0ctrl WHERE del=0");
$dbs=[];
while($r=$db->fetch_assoc($res)) {
	$ctrl_id=$r['id'];
	$database=$db->get_ctrl_database($r['id']);
	$admin_uid=$r['uid'];
	$admin_tg=$db->dlookup("telegram_id","cards","uid='$admin_uid'");
	//print "$ctrl_id $admin_uid $admin_tg<br>";
	//print "DB=".$database."<br>";
	//include "products_exclude.inc.php";
	$db1=new db($database);
	$r1=$db1->fetch_assoc($db1->query("SELECT * FROM users WHERE id=3"));
	$err=$r1['telegram_id']!=$admin_tg?"NOT MATCH":"OK";
	if($r1['telegram_id']==5467180781)
		print "$err in $ctrl_id $admin_uid name=$name in_database={$r1['telegram_id']} 0ctrl=$admin_tg <br><br>";
}
print "DONE";


exit;

$res=$db->query("SELECT users.id AS id, cards.telegram_id AS tg_id FROM users JOIN cards ON klid=cards.id WHERE users.telegram_id=0 AND cards.telegram_id!=0 AND cards.telegram_id!=users.telegram_id");
while($r=$db->fetch_assoc($res)) {
	print "{$r['id']} {$r['tg_id']} <br>";
	$db->query("UPDATE users SET telegram_id='{$r['tg_id']}' WHERE id={$r['id']}");
}

print "Ok";
exit;

$str="RewriteRule ^evt/?$ https://for16.ru/d/1000/19/\?bc=1605962757 [R=301,L]";
$pattern = '/(?:RewriteRule\s\^)(.*?)\/\?\$.*?https:\/\/for16\.ru\/d\/1000\/(.*?)\/\\\\\?bc=(.*?)(?:\s\[R=301,L])/';

preg_match($pattern,$str,$m);
$db->print_r($m);


$pattern = '/(?:RewriteRule\s\^)(.*?)\/\?\$.*?https:\/\/for16\.ru\/d\/1000\/(.*?)\/\\\\\?bc=(.*?)(?:\s\[R=301,L])/';
$arr=file("/var/www/vlav/data/www/wwl/winwinland/.htaccess");
foreach($arr AS $str) {
	if(preg_match($pattern,$str,$m)) {
		$suf=$m[1];
		$bc_code=$m[3];
		print "$suf $bc_code <br>";
	}
}

exit;

function generateRandomStyle($number) {
    $colors = array(
        "#808080", "#FF0000", "#00FF00", "#FFFF00", "#0000FF", "#FF00FF", "#00FFFF", "#FFFFFF",
        "#000000", "#800000", "#008000", "#808000", "#000080", "#800080", "#008080", "#C0C0C0",
        "#FF0000", "#00FF00", "#FFFF00", "#0000FF", "#FF00FF", "#00FFFF", "#FFFFFF", "#800000",
        "#008000", "#808000", "#000080", "#800080", "#008080", "#C0C0C0", "#808080"
    );

    $index = ($number - 1) % 32; // Получаем индекс цвета в массиве
    $backgroundColor = $colors[$index];
    $textColor = ($index < 16) ? "#FFFFFF" : "#000000"; // Выбираем контрастный цвет текста

    $style = "style=\"background-color: $backgroundColor; color: $textColor;\"";
    return $style;
}
function generateDullColorPairs() {
    $colorPairs = array();

    for ($i = 0; $i < 64; $i++) {
        $red = mt_rand(0, 127);
        $green = mt_rand(0, 127);
        $blue = mt_rand(0, 127);

        $backgroundColor = sprintf("#%02x%02x%02x", $red, $green, $blue);
        $textColor = ($red + $green + $blue > 382) ? "#000000" : "#FFFFFF"; // Выбираем контрастный цвет текста

        $colorPairs[] = array(
            "background" => $backgroundColor,
            "text" => $textColor
        );
    }

    return $colorPairs;
}
	$arr=generateDullColorPairs();
	$n=5;
	foreach($arr AS $r) {
		$bg=$r['background'];
		$c=$r['text'];
		print "<div style='background-color:$bg;color:$c;'>$n=>\"background-color:$bg;color:$c;\", </div> \n";
		$n++;
	}

exit;
$n=1;
$db->query("UPDATE users SET access_level=$n WHERE id=1");
print "SET $n";

exit;

$res=$db->query("SELECT *, cards.id AS id
	FROM cards LEFT JOIN sources ON sources.id=cards.source_id
	WHERE cards.del=0
		AND dont_disp_in_new=0
		AND (user_id='1' OR man_id='1' OR man_id=0)
		ORDER BY tm_delay DESC, fl_newmsg DESC, tm_lastmsg DESC");
print "n=".$db->num_rows($res);

exit;

$db=new vkt_send($database);

$land_num=20;
$uid=-1002;
$res=$db->query("SELECT * FROM vkt_send_1 WHERE sid=12 AND land_num='$land_num'",0);
while($r=$db->fetch_assoc($res)) {
	$db->vkt_send_task_add($ctrl_id, $tm_event=intval(time()+$r['tm_shift']), $vkt_send_id=$r['id'],$vkt_send_type=3,$uid);
}
$uid=-1001;
$res=$db->query("SELECT * FROM vkt_send_1 WHERE sid=12 AND land_num='$land_num'",0);
while($r=$db->fetch_assoc($res)) {
	$db->vkt_send_task_add($ctrl_id, $tm_event=intval(time()+$r['tm_shift']), $vkt_send_id=$r['id'],$vkt_send_type=3,$uid);
}
print "Ok";
exit;

$res=$db->query("SELECT * FROM 0ctrl WHERE del=0");
$dbs=[];
while($r=$db->fetch_assoc($res)) {
	$dbs[]=$db->get_ctrl_database($r['id']);
}

foreach($dbs AS $database) {
	print "<h3>$database</h3>";
	$db=new db($database);
	print nl2br($db->dlookup("bot_first_msg","lands","bot_first_msg LIKE '%cabinet%'"));
}

exit;


	$msgs= [
		"test {{price2 48 in 23:59 [30]}} test",
		"test {{price2 tomorrow in 23:59 [30,33]}} test",
		"test {{price2 today in 23:59 [30,33]}} test",
		"test {{price2 after 12 [30,33]}} test",
	];
	foreach($msgs AS $msg) {
		if(preg_match("/{{price2\s+(.*?)\s+(in\s+)?(\d{2}:\d{2}|\d+)\s+\[([\d\,]+)\]}}/",$msg,$m)) {
			print_r($m);
			$pids=explode(",",$m[4]);
			if($m[1]=='today')
				$tm2=$db->dt1(time());
			elseif($m[1]=='tomorrow')
				$tm2=$db->dt1(time()+(24*60*60));
			elseif(intval($m[1]))
				$tm2=$db->dt1(time()+(intval($m[1])*60*60));
			elseif($m[1]=='after')
				$tm2=$db->dt1(time());

			if($t=$db->time2tm($m[3]))
				$tm2+=$t;
			else
				$tm2=time()+intval($m[3])*60*60;

			print "pids=$pids <br>";
			print_r($pids);
			print date("d/m/Y H:i",$tm0);
			print "<hr>";
		} else
			print "false";
	}

print "<br>Ok";
exit;

include_once "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/sendpulse.class.php";
//include_once "/var/www/vlav/data/www/wwl/prices.inc.php";
$db=new vkt_send("vkt");
$db->telegram_bot="vkt";
$db->db200=$DB200;

$test=true;
$sp=new sendpulse('vkt');

	$uid= -1002;

	$email='vlav@mail.ru';
	$product_id=30;
			$msg="Здравствуйте, через 2 дня у вас заканчивается доступ к платформе WinWinLand
			Продлите доступ на 30, 90 или 360 дней по ссылке $DB200/billing_pay.php
			";

			$db->vkt_send_tg_bot=$db->dlookup("tg_bot_msg","0ctrl","uid='$uid'");
			$db->vkt_send_msg($uid,$msg);

			if($db->validate_email($email)) {
				$sp->email_by_template($sp_template='avangard_pay_check_3.html',
									$email,
									$r['name'],
									$subj="🟡 [клиенту] через 2 дня у вас заканчивается доступ к продукту «{$r['order_descr']}»",
									$from_email='office@winwinland.ru',
									$from_name='WinWinLand',
									$uid,"$DB200/billing_pay.php");
			}

exit;

include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$vkt=new vkt('vkt');

$uid=-7624;

if(!$ctrl_id=$vkt->get_ctrl_id_by_uid($uid)) {
	//NEW COMPANY CREATING
	$ctrl_id=$vkt->create_ctrl_company($uid);
	//print "ctrl_id=$ctrl_id <br>";
	$vkt->create_ctrl_dir($ctrl_id);
	$vkt->create_ctrl_databases($ctrl_id);
	$ctrl_dir=$vkt->get_ctrl_dir($ctrl_id);
	$ctrl_db=$vkt->get_ctrl_database($ctrl_id);
	$ctrl_link=$vkt->get_ctrl_link($ctrl_id);
	$db->email($emails=array("vlav@mail.ru"), "WWL new company ctrl_id=$ctrl_id CREATED", "", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
	$passw=$vkt->passw_gen($len=10);

	$vkt->connect($ctrl_db);
	$vkt->query("UPDATE users SET
		passw='".md5($passw)."',
		real_user_name='".$vkt->escape($client_name)."',
		email='".$vkt->escape($client_email)."',
		comm='".$vkt->escape($passw)."'
		WHERE username='admin'");

	//add 2 weeks code here ...
	
	$db->connect('vkt');
	$db->query("UPDATE 0ctrl SET admin_passw='$passw' WHERE id='$ctrl_id'");
	if($db->avangard_payments_count($uid,$products_sarafan)==1) { //if new user
		$tm_end_for_new=$tm_end+(14*24*60*60);
		$db->query("UPDATE avangard SET tm_end='$tm_end_for_new' WHERE prodamus_id='$prodamus_id'");
	}
} else {
	//print "Company exists - $ctrl_id <br>";
}

print "DONE";
exit;

parse_str("bc=1002", $m);
print_r($m);
exit;

$res=$db->query("SELECT uid FROM msgs WHERE 1 GROUP BY uid");
while($r=$db->fetch_assoc($res)) {
	$uid=$r['uid'];
	if(!$db->dlookup("id","cards","uid=$uid")) {
		$db->query("DELETE FROM msgs WHERE uid=$uid");
		print "uid=$uid is not in cards 	<br>";
	}
}
print "<br>OK";

exit;
$products_sarafan=[30,31,32];
$uid=-7609;
$tm_end=1690750799;
$prodamus_id=13691107;
$last_pay_id=$db->avangard_last_pay_id($uid,$products_sarafan);
print "last_pay_id=$last_pay_id <br>";
if(!$db->avangard_last_pay_id($uid,$products_sarafan)) { //if new user
	$tm_end_for_new=$tm_end+(14*24*60*60);
	print "HERE_$tm_end_for_new 	<br>";
	//$db->query("UPDATE avangard SET tm_end='$tm_end_for_new' WHERE prodamus_id='$prodamus_id'");
}
print "OK";

exit;

$tm1=0;
$tm2=time();
$p=new partnerka(7580,'vkt');
$p->fill_op(7580,$tm1,$tm2);

print "\nOK";
exit;


$vkt=new vkt('vkt');

print $vkt->get_ctrl_link(37);

print "OK";
exit;

	
	$uid='-1002';
	$client_name='ВЛАДИМИР';
	$client_email='vlav@mail.ru';
	$sp_template=$db->dlookup("sp_template","product","id=30");;
	$vkt_link=$DB200."/cp.php?view=yes&filter=new";
	if($sp_template) {
		$sp=new unisender($unisender_secret,$from_email,$from_name);
		$db->connect('vkt');
		$passw=$db->dlookup("admin_passw","0ctrl","id='$ctrl_id'");
		$db->connect($database);
		$sp->email_by_template($client_email,$sp_template,['uid'=>$uid,'passw'=>$passw,'client_name'=>$client_name,'vkt_link'=>$vkt_link]);
		//$db->vktrade_send_skip_wa=true;
		$db->telegram_bot=$tg_bot_notif;
		$db->db200=$DB200;
		$db->vktrade_send_tg_bot=$tg_bot_msg;				
		$db->vktrade_send_wa($uid,"Благодарим, оплата получена. Инструкции по доступу отправлены вам на емэйл: $client_email. Если письмо не приходит, посмотрите в папках спам или рассылки. Вопросы можно задавать сюда. Всего наилучшего!");
	}


exit;

$db->query("SELECT * FROM 0ctrl_vkt_send_tasks WHERE 1");
while($r=$db->fetch_assoc($res)) {
	print "{$r['id']} ".date("d.m.Y H:i",$r['tm'])."<br>";
}


exit;

include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
$db->notify_user(-1002,"test");
$vk=new vklist_api;
$tg_bot_id=$tg_bot_notif;
$vk->send_telegram_alert("test", 282133533, $tg_bot_id);
print "ok";
exit;

$sp=new sendpulse('vkt');
				$sp->email_by_template($sp_template='avangard_pay_check_1.html',
									$email='vlav@mail.ru',
									'vlav',
									$subj="🟡 [клиенту] через 2 дня у вас заканчивается доступ к продукту «test»",
									$from_email='office@winwinland.ru',
									$from_name='WinWinLand',
									$uid=-1002,'order_descr');
print "ok";
exit;

$sp_template='sarafan_payment_success.html';
			$product_id=30;
			$jc_gid=$base_prices[$product_id]["jc"];
			$sp_book_id=$base_prices[$product_id]["sp"];
			$sp_template=$base_prices[$product_id]["sp_template"];
			$senler=$base_prices[$product_id]["senler"];
			$descr=$base_prices[$product_id]["descr"];
			$source_id=$base_prices[$product_id]["source_id"];
			$razdel=$base_prices[$product_id]["razdel"];

			$client_name='VLAV';
			$client_phone='79119841012';
			$client_email='vlav@mail.ru';
			$order_number='N-123';
			list($p,$order_id)=explode("-",$order_number);
			$sum=intval(50);

			$uid=-1002;
			$passw='qwerty';
			$ctrl_id=1;

			$db->connect('vkt');
			$passw=$db->dlookup("admin_passw","0ctrl","id='$ctrl_id'");
			if($sp_template) {
				$sp=new sendpulse('vkt');
				$sp->email_by_template($sp_template,
									$client_email,
									$client_name,
									$subj="🔶 $descr",
									$from_email='office@winwinland.ru',
									$from_name='WINWINLAND',
									$uid,$passw);
			print "HERE $sp_template $tg_bot_notif $descr<br>";
				//$db->vktrade_send_skip_wa=true;
				$db->telegram_bot=$tg_bot_notif;
				$db->db200=$DB200;
				$db->vktrade_send_tg_bot=$tg_bot_msg;				
				$db->vktrade_send_wa($uid,"Благодарим, оплата получена. Инструкции по доступу отправлены вам на емэйл: $client_email. Если письмо не приходит, посмотрите в папках спам или рассылки. Вопросы можно задавать сюда. Всего наилучшего!");
			}
print "OK";
exit;

include_once "/var/www/vlav/data/www/wwl/inc/tg_bot.class.php";
$db=new vkt('vkt');
$res=$db->query("SELECT * FROM 0ctrl WHERE tg_bot_msg!=''");
while($r=$db->fetch_assoc($res)) {
	$tg_bot_msg=$r['tg_bot_msg'];
	$ctrl_dir=$db->get_ctrl_dir($r['id']);
	//continue;
	$url='https://for16.ru/d/'.$ctrl_dir.'/tg_bot.php';
	print "{$r['id']} $ctrl_dir $url $tg_bot_msg <br>";
	$tg=new tg_bot($tg_bot_msg);
	if(!$tg->set_webhook($url))
		print "Установить вебхук не удалось. Вероятно токен бота неправильный. <br>";
	else
		print "OK <br>";
	sleep(1);
}

exit;
include_once "/var/www/vlav/data/www/wwl/inc/tg_bot.class.php";
$tg=new tg_bot($_POST['tg_bot_msg']);
if($ctrl_id) {
	if(!$tg->set_webhook('https://for16.ru/d/'.$ctrl_dir.'/tg_bot.php'))
		$msg="Установить вебхук не удалось. Вероятно токен бота неправильный.";
}


exit;

$res=mail("vlav@mail.ru","test123","test",[],"");
if($res)
	print "OK"; else print "FALSE";
exit;

include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/sendpulse.class.php";

$sp=new sendpulse('vkt');

$res=$sp->email_by_template($sp_template='avangard_pay_check_1.html',
					$email='vlav@mail.ru',
					'vlav',
					$subj="🟡 [клиенту] через 2 дня у вас заканчивается доступ к продукту «{$r['order_descr']}»",
					$from_email='office@winwinland.ru',
					$from_name='WinWinLand',
					$uid=-1002,"PRODUCT");

if($res)
	print "OK"; else print "FALSE";
exit;

?>
