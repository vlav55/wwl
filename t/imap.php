#!/usr/bin/php -q
<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db('t');
$DOMEN="https://t.1-info.ru/";
include "func.inc.php";


$res=$db->query("SELECT * FROM tasks WHERE del=0 AND tm>0 AND tm<".time());
while($r=$db->fetch_assoc($res)) {
	$s=preg_split("/[\n\r]+/",$r['task']);
	//$subj=mb_strtoupper($s[0],"CP1251");
	$subj=mb_strtoupper($s[0],"UTF-8");
	$msg="";

//	$msg="<a href='$DOMEN?do_settime=yes&tm=to10&id={$r['id']}'>to 10:00</a> | ";
//	$msg.="<a href='$DOMEN?do_settime=yes&tm=to18sms&sms=yes&id={$r['id']}'>to 18:00</a> | ";
//	$msg.="<a href='$DOMEN?settime=yes&id={$r['id']}'>settime</a> | ";
	$msg.="<a href='$DOMEN?do_settime=yes&id={$r['id']}&tm0=".(time()+(2*60*60))."'>2hours</a> | ";
	$msg.="<a href='$DOMEN?do_settime=yes&id={$r['id']}&tm0=".(time()+(3*24*60*60))."'>3days</a> | ";
	$msg.="<a href='$DOMEN?do_settime=yes&id={$r['id']}&tm0=".(time()+(7*24*60*60))."'>week</a> | ";
	$msg.="<a href='$DOMEN?do_settime=yes&id={$r['id']}&tm0=".(time()+(14*24*60*60))."'>2weeks</a> | ";
	$msg.="<a href='$DOMEN?do_settime=yes&id={$r['id']}&tm0=".(time()+(30*24*60*60))."'>month</a> | ";
	$msg.="<a href='$DOMEN?view=yes&id={$r['id']}#r_{$r['id']}'>VIEW</a> | ";
	$msg.="<a href='$DOMEN?do_clrtime=yes&id={$r['id']}'>clr_time</a> | ";
	$msg.="<a href='$DOMEN?del=yes&id={$r['id']}'>del</a> | ";

	$task=preg_replace('/(http:\/\/|https:\/\/)?(www)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w\.-\?\%\&]*)?/i', 
		'<a href="\1\2\3.\4$5" target=\'_blank\'>\\1\\2\\3.\\4$5</a>', 
		$r['task']);
	$task=preg_replace("/[\"\']+/","",$task);

	if($db->email(["vlav@mail.ru"], $subj, $msg.$task, $from="info@winwinland.ru",$fromname="t", $add_globals=false))
		print "Email notification sent: $msg\n";

	$botToken="1451314745:AAHLX4MAf3M008jcAtWiQPCgJDi1-IZr28k";
	$chatId=315058329; 
	$url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=".urlencode("$task\n$DOMEN?view=yes&id={$r['id']}#r_{$r['id']}");
	file_get_contents($url);


$access_token = '9xopuzlqztm0h6e748l77ftohch9uoxw9vsx9s87';
$api_url = 'https://callapi-jsonrpc.novofon.ru/v4.0';
$virtual_phone_number='78124251296';
$contact_number='79119841012';

$data = [
    'jsonrpc' => '2.0',
    'method' => 'start.informer_call',
    'id' => 'test_' . time(),
    'params' => [
        'access_token' => $access_token,
        'virtual_phone_number' => $virtual_phone_number,
        'contact' => $contact_number,
        'contact_message' => [
            'type' => 'tts',
            'value' => $task
        ]
    ]
];

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));

$result = curl_exec($ch);
curl_close($ch);

echo $result;

	
	
	//print "Email notification sent: ".mb_convert_encoding ( $msg,"utf8","cp1251")."\n";
	$next_day=24*60*60;
	//~ if(jddayofweek ( cal_to_jd(CAL_GREGORIAN, date("m"),date("d"), date("Y")) , 0 )==5) //if Fryday
		//~ $next_day=$next_day*3; //to Monday

	//mysql_query("UPDATE tasks SET prior=".get_top().", tm=0, notif=0 WHERE id={$r['id']}") or die(mysql_error());
	//mysql_query("UPDATE tasks SET  tm=".(time()+$next_day).", notif=1 WHERE id={$r['id']}") or die(mysql_error());
	$db->query("UPDATE tasks SET  tm=".(get_tm("to10")).", notif=1 WHERE id={$r['id']}");
}
print "SCANNED\n";

?>
