<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/justclick_api.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/sendpulse.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/unisender.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/senler_api.class.php";

include "init.inc.php";
//include_once "../../prices.inc.php";

$db=new db($database); 

	$mrh_pass2 = $db->dlookup("robokassa_passw_2","pay_systems","1"); // merchant pass2
	$out_summ = $_REQUEST["OutSum"];
	$inv_id = $_REQUEST["InvId"];
	$crc = strtoupper($_REQUEST["SignatureValue"]);

	$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2"));

	if ($my_crc != $crc)
	{
	 echo "bad sign\n";
	 send_tg("<b>Signature not valid</b> $inv_id\r\nSum: $out_summ\r\nSig: <code>$crc</code>\r\nValid sig: <code>$my_crc</code>");
	 exit();
	}

	// успех
	$order_id=$inv_id;
	$commission_sum= 0;

	include "/var/www/vlav/data/www/wwl/inc/pay_callback_common.1.inc.php";

	send_tg("<b>New transaction</b>: $inv_id\r\nSum: $out_summ");
	die("OK$inv_id\n");

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
