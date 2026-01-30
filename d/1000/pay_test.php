<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "init.inc.php";
$db=new db($database);

//$db->print_r($_POST); exit;

if(isset($_POST['go_submit'])) { 
	$pay_system="test";
	include_once "/var/www/vlav/data/www/wwl/inc/pay_common.1.inc.php";
	$db->print_r($_POST);
	$db->print_r($db->query("SHOW COLUMNS FROM promocodes"));
	print "OK";
}

exit;

$mrh_login = "NEOHIGHSCHOOLCRM"; //$db->dlookup("robokassa_id","pay_systems","1");     // login
$mrh_pass1 = "GUGA1uar7lqDZ9m5b1IR"; //$db->dlookup("robokassa_passw_1","pay_systems","1"); // merchant pass1
$mrh_pass2 = "s8Ou6xTuQpoy9eGm7tu6"; //$db->dlookup("robokassa_passw_2","pay_systems","1"); // merchant pass2

if(isset($_POST['go_submit'])) { 
	$pay_system="robokassa";
	include_once "/var/www/vlav/data/www/wwl/inc/pay_common.1.inc.php";

	//print_r($_POST);

	$_GET['summ']=$sum;
	$_GET['inv_id']=$order_id;

    //https://docs.robokassa.ru/fiscalization/?sphrase_id=153795        

# Создание платежа #
if(!isset($_GET['inv_id']) || empty($_GET['inv_id'])) 	die('Укажите номер заказа (inv_id)');
if(!isset($_GET['summ']) || empty($_GET['summ'])) 		die('Укажите сумма платежа (summ)');

// order properties
$inv_id = $_GET['inv_id'];	// Номер заказа, должен быть уникальным
$out_summ = $_GET['summ']; 	// сумма
$inv_desc = $descr;

$receipt = json_encode(array(
    "sno" => "usn_income",
    "items" => array(
        array(
            "name" => $descr,
            "quantity" => 1,
            "sum" => $sum,
            "cost" => $sum,        
            "payment_method" => "full_prepayment",
            "nomenclature_code" => $product_id,
            "tax"=>"none"
        )
    )
));

//print($receipt); exit;

$crc = md5("$mrh_login:$out_summ:$inv_id:$receipt:$mrh_pass1"); // добавил $receipt
$url = "https://auth.robokassa.ru/Merchant/Index.aspx?MerchantLogin=$mrh_login&".
  "OutSum=$out_summ&InvId=$inv_id&Description=$inv_desc&SignatureValue=$crc&receipt=$receipt";


header("Location: $url");
# Создание платежа #
}

function send_tg($text) {
	return;
	global $user, $sum, $txn_id, $txn_date;
	$token = "862036054:AAHrc0xX5G52ZS67yY89k90Q30LEd5i-ycI";
	$chatid = "-4006998170";
	connect("https://api.telegram.org/bot$token/sendMessage", "chat_id=$chatid&text=".urlencode($text)."&parse_mode=html", 0);
}
function connect($link, $post=null, $head=1, $header=null, $req=false) {

	$ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL,$link);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
	if($req!==false) 
	//curl_setopt($ch, CURLOPT_PUT, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $req);
	curl_setopt($ch, CURLOPT_HEADER, $head);
	if($header) curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	if($post !== null)
	curl_setopt($ch, CURLOPT_POST, 1);
	if($post !== null)
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $otvet = curl_exec($ch);
    curl_close($ch);
	return $otvet;
}

function get_list_ip($ip_addr_cidr){ $ip_arr = explode("/", $ip_addr_cidr); $bin = ""; for($i=1;$i<=32;$i++) { $bin .= $ip_arr[1] >= $i ? '1' : '0'; } $ip_arr[1] = bindec($bin); $ip = ip2long($ip_arr[0]); $nm = $ip_arr[1]; $nw = ($ip & $nm); $bc = $nw | ~$nm; $bc_long = ip2long(long2ip($bc)); for($zm=1;($nw + $zm)<=($bc_long - 1);$zm++) { $ret[]=long2ip($nw + $zm); } return $ret; } 



?>
