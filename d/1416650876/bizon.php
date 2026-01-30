<?
http_response_code(200);
$r = json_decode(file_get_contents('php://input'),true);
//file_put_contents("bizon_last_webhook.txt",print_r($r,true));
$wid=$r['webinarId'];
$webhook=1;

include "init.inc.php";

$token=$bizon_api_token; //"BZ9IVVrTHHZl5IN4rpBr-bcUVEr6rBZf9UEVSaBr-Xq8VNSTH";

//print $bizon_api_token; exit;

//~ $grp_jc=false; //"1592315369.605375434"; //Были на семинаре
//~ $sp_book_id_b=false; //1124391; //Были на семинаре
//~ $sp_book_id_c=false; //1124391; //Были на семинаре
//~ $sp_book_id_f=false; //1145618; //уже был раньше на семинаре
//~ $sp_book_id_d=false; //1145619; //был недолго
//~ $sp_book_id_d1=false; //1145620; //did not visit webinar, but was scheduled
//~ $grp_senler=false; //588656;  //не исп


$pids_for_discount=[10,11,12];
//define('database',$database);
//~ define('user','vlav@mail.ru');
//~ define('pass','BzuwBrDQH');

if(preg_match("/\:([0-9]+)\*/",$wid,$m)) {
	//print_r($m);
	$scdl_web_id=$m[1];
} else
	$scdl_web_id=1;
	
$room_regex="/\:$scdl_web_id\*/";

if($land_num=$db->dlookup("land_num","lands","del=0 AND land_num='$scdl_web_id'")) {
	$duration_min=$db->dlookup("bizon_duration","lands","del=0 AND land_num='$land_num'");
	$proc=$db->dlookup("bizon_zachot","lands","del=0 AND land_num='$land_num'");
	$duration_for_discount=intval($duration_min*$proc/100);
} else {
	$duration_min=$bizon_web_duration;
	$duration_for_discount=intval($bizon_web_duration*$bizon_web_zachet_proc/100);
}
if($duration_for_discount<0 || $duration_for_discount>$duration_min)
	$duration_for_discount=intval($duration_min*0.7);

$rid_b=2;
$sid_b=13;
$rid_c=1;
$sid_c=14;
$rid_f=$rid_b;
$sid_f=16; //уже был раньше на семинаре
$days_visited_webinar_limit=30; //количество дней назад, в течение которых контролируется, был ли человек на вебинаре ранее
$rid_d=4;
$sid_d=14; //был недолго
$sid_d1=15; //did not visit webinar, but was scheduled
$fl_newmsg_b=3;
$fl_newmsg_d=0;
$fl_b_notif=true;
$fl_c_notif=false;
$fl_d_notif=true;
$fl_f_notif=true;
$fl_d1_notif=true;


include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/msg.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/senler_api.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/justclick_api.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/sendpulse.class.php";
//include_once "../prices.inc.php";

include_once "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
$vkts=new vkt_send($database);

$db=new db($database);
$vk=new vklist_api();
$senler=new senler_api;
$jc=new justclick_api; //$jc->login("vkt");
$sp=new sendpulse("vkt");

print "Loading <br>\n";

include "/var/www/vlav/data/www/wwl/inc/bizon_scan.inc.php";
?>
