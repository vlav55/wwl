<?

include_once('/var/www/vlav/data/www/wwl/inc/vkt.class.php');
$vkt=new vkt('vkt'); 

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
//~ if(isset($_SESSION['ctrl_id']))
	//~ session_destroy();
	
$ctrl_id=$vkt->get_ctrl_id_by_cwd();
$ctrl_dir=$vkt->get_ctrl_dir($ctrl_id);
$database=$vkt->get_ctrl_database($ctrl_id);

//$vkt->notify_me("ctrl_id=$ctrl_id db=$database");

//$DB200=($ctrl_id>1)?"https://for16.ru/d/$ctrl_dir":"https://for16.ru/d/1000";
$DB200=$vkt->get_db200($ctrl_dir);
$_SESSION['DB200']=$DB200;
$ctrl_url=$vkt->get_ctrl_url($ctrl_id);

$r=$vkt->fetch_assoc($vkt->query("SELECT * FROM 0ctrl WHERE id='$ctrl_id'"));
$uid_admin=$r['uid'];
$tg_admin=$vkt->dlookup("telegram_id","cards","uid='$uid_admin'");
//print "HERE_ $tg_admin";
$admin_passw=$r['admin_passw'];
$test_uid=$r['test_uid'];
$fee_1=$r['fee_1'];
$fee_2=$r['fee_2'];
$fee_hello=$r['fee_hello'];
$keep=$r['keep'] ? 0 : 1; //1 mean keep 
$hold=$r['hold'];
$fl_cabinet2=$r['fl_cabinet2'];
$tg_bot_notif=$r['tg_bot_notif'];
$tg_bot_msg=$r['tg_bot_msg'];
$tg_bot_msg_name=$r['tg_bot_msg_name'];
$bot_first_msg=$r['bot_first_msg'];
$bot_first_msg_p=$r['bot_first_msg_p'];
$vk_confirmation_token=$r['vk_confirmation_token'];
$senler_secret=$r['senler_secret'];
$senler_gid_partnerka=$r['senler_gid_partnerka'];
$senler_gid_land=$r['senler_gid_land'];
$vk_group_id=$r['vk_group_id'];
$VK_GROUP_ID=$vk_group_id;
$land=$r['land'];
$land_p=$r['land_p'];
$pixel_ya=$r['pixel_ya'];
$pixel_vk=$r['pixel_vk'];
$bizon_api_token=$r['bizon_api_token'];
$bizon_web_duration=$r['bizon_web_duration'];
$bizon_web_zachet_proc=$r['bizon_web_zachet_proc'];

$company_name=$r['company'];
$company_data=$r['company_data'];
$company_logo=file_exists("tg_files/logo.jpg")?"tg_files/logo.jpg":"";

$pp=$r['pp'];
$privacypolicy=$pp;
$oferta=$r['oferta'];
$dogovor=$oferta;
$agreement=$r['agreement'];
$oferta_referal=$r['oferta_referal'];
$unisender_secret=$r['unisender_secret'];
$email_from=$r['email_from'];
$email_from_name=$r['email_from_name'];

$pact_secret=$r['pact_secret'];
$pact_company_id=$r['pact_company_id'];

$vsegpt_secret=$r['vsegpt_secret'];
$vsegpt_model=$r['vsegpt_model'];
$vsegpt_delay_sec=$r['vsegpt_delay_sec'];

$insales_id=$r['insales_shop_id'];
$insales_shop=$r['insales_shop'];
$insales_token=$r['insales_token'];
$insales_status=$r['insales_status'];
$insales_bonuses=$r['insales_bonuses'];
$insales_delay_fee=$r['insales_delay_fee'];

$vkt_send_sid_arr=[12,23,25,26,30,31];
if(!empty($bizon_api_token))
	$vkt_send_sid_arr[]=[13,14,15,16];


$tm_pay_end_0ctrl=$r['tm_end'];

$products_tm_pay_end=[];
$res=$vkt->query("SELECT * FROM product WHERE term>0 AND del=0");
while($r=$vkt->fetch_assoc($res)) {
	$products_tm_pay_end[]=$r['id'];
}

$tm_pay_end=$vkt->avangard_tm_end($uid_admin,$products_tm_pay_end);
$tm=$vkt->dlast("tm_end","avangard","res=1 AND ticket='$ctrl_id'");
if($tm>$tm_pay_end)
	$tm_pay_end=$tm;
$dt_pay_end=date("d.m.Y",$tm_pay_end);
if($tm_pay_end<time() ) {
	$c='danger';
} elseif($tm_pay_end<(time()+(1*24*60*60)) )
	$c='danger';
elseif($tm_pay_end<(time()+(5*24*60*60)) )
	$c='warning';
else
	$c='info';

//$tm_pay_end_0ctrl=1703077740;
$msg_pay_info=(!$tm_pay_end_0ctrl && @$_SESSION['access_level']<=3 )
	?"<span class='badge badge-$c' >оплачено до: $dt_pay_end</span> <span></span><a href='billing_pay.php' class='' target='_blank'>продлить</a></span>"
	:"";

//$yclients_salon_id=$vkt->dlookup("tool_key","0ctrl_tools","tool='yclients' AND ctrl_id='$ctrl_id'");

////////////in database
$db=new db($database);
$res=$db->query("SELECT * FROM product WHERE del=0");
$base_prices=array();
while($r=$db->fetch_assoc($res)) {
	$base_prices[$r['id']]=[
		0=>$r['price0'],
		1=>$r['price1'],
		2=>$r['price2'],
		'descr'=>$r['descr'],
		'term'=>$r['term'],
		'stock'=>$r['stock'],
		'jc'=>$r['jc'],
		'sp'=>0,
		'sp_template'=>$r['sp_template'],
		'source_id'=>$r['source_id'],
		'razdel'=>$r['razdel'],
		'tag_id'=>$r['tag_id'],
		'use'=>$r['in_use'],
		'vid'=>$r['vid'],
		'installment'=>$r['installment'],
		'fee_1'=>$r['fee_1'],
		'fee_2'=>$r['fee_2'],
	];
}
$where_pids_consider=1;

$res=$db->query("SELECT users.id AS id, cards.telegram_id AS tg_id FROM users JOIN cards ON klid=cards.id WHERE users.telegram_id=0 AND cards.telegram_id!=0 AND cards.telegram_id!=users.telegram_id");
while($r=$db->fetch_assoc($res)) {
	//print "{$r['id']} {$r['tg_id']} <br>";
	$db->query("UPDATE users SET telegram_id='{$r['tg_id']}' WHERE id={$r['id']}");
}

//$db->notify_me("HERE_$ctrl_id");
/////////////root mysql access required
$db->connect($database,true);
$db->query("CREATE TABLE IF NOT EXISTS `0ctrl_tools` (
    `id` int NOT NULL AUTO_INCREMENT,
    `tool` varchar(64) NOT NULL,
    `ctrl_id` int NOT NULL,
    `tool_key` varchar(64) NOT NULL,
    `tool_val` text NOT NULL,
    PRIMARY KEY (`id`),
    KEY `tool` (`tool`),
    KEY `tool_key` (`tool_key`),
    KEY `ctrl_id` (`ctrl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$db->query("CREATE TABLE IF NOT EXISTS `cards_add` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uid` int NOT NULL,
  `par` varchar(16) NOT NULL,
  `val` varchar(255) NOT NULL,
  `val_text` text NOT NULL,
  KEY `uid` (`uid`),
  KEY `par` (`par`),
  KEY `val` (`val`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

///////////////


$db->connect($database);
if(file_exists("init.custom.php")) {
	include_once("init.custom.php");
} elseif(file_exists("../init.custom.php")) {
	include_once("../init.custom.php");
}



?>
