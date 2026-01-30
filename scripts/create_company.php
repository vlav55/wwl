<?
$title="create_company";
include "../top.inc.php";
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/sendpulse.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/unisender.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/senler.class.php";
chdir("/var/www/vlav/data/www/wwl/d/1000");
include_once "init.inc.php";
print "<div class='container' >";
print "<h1>Create company</h1>";

if(!isset($_GET['uid'])) {
	print "Specify uid and sum <br>";
	print "?pid=33&uid=12345&sum=10 <br>";
	$res=$db->query("SELECT * FROM product WHERE del=0 AND price1>0");
	while($r=$db->fetch_assoc($res)) {
		print "{$r['id']} {$r['descr']} {$r['price1']} <br>";
	}
	exit;
}
$uid=intval($_GET['uid']);
$pid=intval($_GET['pid']);
$sum=intval($_GET['sum']);

if(!$pid || !$uid || !$sum) {
	print "Error";
	exit;
}

$db=new vkt('vkt');
if(!$name=$db->dlookup("surname","cards","uid='$uid'")." ".$db->dlookup("name","cards","uid='$uid'")) {
	print "$uid not found!"; exit;
}

print "<h1>Creating company for uid=$uid $name</h1>";

	$_POST['vk_user_id']=$uid;
	$_POST['customer_extra']=$pid;
	$_POST['vk_user_name']=$name;
	$_POST['customer_phone']=$db->dlookup("mob_search","cards","uid='$uid'");
	$_POST['customer_email']=$db->dlookup("email","cards","uid='$uid'");
	$_POST['order_num']="00-$uid";
	$_POST['sum']=$sum;
	$_POST['commission_sum']=0;

if(!$ctrl_id=$db->dlookup("id","0ctrl","uid='$uid'") ) {
	$uid=intval($_POST['vk_user_id']);
	if(!$uid) 
		$uid=$db->get_unicum_uid();
	$product_id=$_POST['customer_extra'];
	$jc_gid=$base_prices[$product_id]["jc"];
	$sp_book_id=$base_prices[$product_id]["sp"];
	$sp_template=$base_prices[$product_id]["sp_template"];
	$senler=$base_prices[$product_id]["senler"];
	$descr=$base_prices[$product_id]["descr"];
	$source_id=$base_prices[$product_id]["source_id"];
	$razdel=$base_prices[$product_id]["razdel"];
	if(!$razdel) {
		if($uid) {
			$razdel=$db->dlookup("razdel","cards","uid='$uid'");
		} else
			$razdel=4;
	}
	$client_name=trim($_POST['vk_user_name']);
	$client_phone=trim($_POST['customer_phone']);
	$client_email=trim($_POST['customer_email']);
	$order_number=$_POST['order_num'];
	//list($p,$order_id)=explode("-",$order_number);
	$order_number=time()+$uid;
	$sum=intval($_POST['sum']);
	$sum1=$sum-intval($_POST['commission_sum']);

	$tm_end_last=$db->fetch_assoc($db->query("SELECT vk_uid,tm_end FROM `avangard` WHERE res=1 AND product_id='$product_id' AND vk_uid='$uid' ORDER BY tm_end DESC LIMIT 1"))['tm_end'];
	$tm_end_last=($tm_end_last>time()) ? $tm_end_last : time();
	$tm_end=$db->dt2($tm_end_last+(intval($base_prices[$product_id]['term'])*24*60*60) );

	$db->query("INSERT INTO avangard SET
				tm='".time()."',
				product_id='$product_id',
				order_id='$order_id',
				order_number='".$db->escape($order_number)."',
				order_descr='".$db->escape($descr)."',
				amount='$sum',
				amount1='$sum1',
				c_name='".$db->escape($client_name)."',
				phone='".$db->escape($client_phone)."',
				email='".$db->escape($client_email)."',
				vk_uid='$uid',
				res=1,
				prodamus_id='$prodamus_id',
				tm_end='$tm_end'
				");

	$db->email($emails=array("vlav@mail.ru"), "WWL PAYMENT SUCCESS $descr", "Order_number $order_number $client_name $client_email $client_phone $sum", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=false);
	if($jc_gid) {
		$jc=new justclick_api;
		$jc->login("vkt");
		if(!$jc->add_to_group($jc_gid,$client_email,$client_name))
			$db->email($emails=array("vlav@mail.ru"), "WWL JC ADD TO $jc_gid  ERROR (product_id=$product_id)", "jc for $order_number returned false", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
	}
	if($sp_book_id) {
		$sp=new sendpulse('vkt');
		if(!$sp->add($sp_book_id,$client_email))
			$db->email($emails=array("vlav@mail.ru"), "WWL SP ADD TO $sp_book_id  ERROR (product_id=$product_id)", "justclick_add_to_group for $order_number returned false", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
	}

	//должно сработать до отправки письма!!!
	$products_sarafan=$products_winwinland;
	if(in_array($product_id,$products_sarafan)) {
		include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
		$vkt=new vkt('vkt');
		
		if(!$ctrl_id=$vkt->get_ctrl_id_by_uid($uid)) {
			$ctrl_id=$vkt->create_ctrl_company($uid);
			//print "ctrl_id=$ctrl_id <br>";
			$vkt->create_ctrl_dir($ctrl_id);
			$vkt->create_ctrl_databases($ctrl_id);
			$ctrl_dir=$vkt->get_ctrl_dir($ctrl_id);
			$ctrl_db=$vkt->get_ctrl_database($ctrl_id);
			//print "<br>".$vkt->get_ctrl_link($ctrl_id)."<br>";
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
		} else {
			//print "Company exists - $ctrl_id <br>";
		}
	}
	
	
	$db->connect('vkt');
	$passw=$db->dlookup("admin_passw","0ctrl","id='$ctrl_id'");
	if(!empty($sp_template)) {
		if(!empty($unisender_secret)) {
			$sp=new unisender($unisender_secret,$email_from,$email_from_name);

			//~ $sp->email_by_template($client_email,$sp_template,
				//~ ['uid'=>$uid,
				//~ 'passw'=>$passw,
				//~ 'client_name'=>$client_name,
				//~ 'vkt_link'=>$db->get_ctrl_link($ctrl_id)]);

			$sp->email_by_template('vlav@mail.ru',$sp_template,
				['uid'=>$db->uid_md5($uid),
				'passw'=>$passw,
				'client_name'=>$client_name,
				'vkt_link'=>$db->get_ctrl_link($ctrl_id)]);

			print "email ONLY sent: vlav@mail.ru <br>";
			print "You have to send email to client from CRM <br>";

			//$db->vktrade_send_skip_wa=true;
	//		$db->vktrade_send_wa($uid,"Благодарим, оплата получена. Инструкции по доступу отправлены вам на емэйл: $client_email. Если письмо не приходит, посмотрите в папках спам или рассылки. Вопросы можно задавать сюда. Всего наилучшего!");
		} else {
			print "<p class='alert alert-danger' >Не указан секретный ключ unisender</p>";
		}
	}
	if($senler && $uid>0) {
		$s=new senler_api;
		if(!$s->subscribers_add($uid, $senler))
			$db->email($emails=array("vlav@mail.ru"), "VKT SENLER ADD TO $senler  ERROR (uid=$uid product_id=$product_id)", "subscribers_add for $order_number uid=$uid returned false", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
	}

	$comm="ОПЛАТА: $descr order_number=$order_number amount=$sum";
	$mob_search=$db->check_mob($client_phone);
	if($db->dlookup("id","cards","uid='$uid'")) {
		$db->query("UPDATE cards SET
			mob='".$db->escape($client_phone)."',
			mob_search='".$db->escape($mob_search)."',
			email='".$db->escape($client_email)."',
			fl_newmsg='3',
			razdel='$razdel',
			source_id='$source_id',
			tm_lastmsg=".time()."
			WHERE uid='$uid'");
		$db->save_comm($uid,0,$comm,$source_id,$vote_vk_uid=0,$mode=0, $force=false);
	} else {
		$db->query("INSERT INTO cards SET 
				name='".$db->escape($client_name)."',
				mob='".$db->escape($client_phone)."',
				mob_search='".$db->escape($mob_search)."',
				email='".$db->escape($client_email)."',
				uid='$uid',
				uid_md5='".$db->uid_md5($uid)."',
				acc_id=2,
				acc_id_orig=2,
				razdel='$razdel',
				source_id='$source_id',
				user_id=0,
				fl_newmsg=3,
				tm_lastmsg=".time().",
				tm=".time()
				);
		$db->save_comm($uid,0,$comm,$source_id,$vote_vk_uid=0,$mode=1, $force=true);
	}
	//~ if(isset($_SESSION['utm_affiliate'])) {
		//~ $utm_affiliate=intval($_SESSION['utm_affiliate']);
		//~ //if(!$db->dlookup("utm_affiliate","cards","uid='$uid'"))
		//~ $utm_affiliate=intval($_SESSION['utm_affiliate']);
		//~ if($utm_affiliate && $utm_affiliate!=$db->dlookup("id","cards","uid='$uid'"))
			//~ $db->query("UPDATE cards SET utm_affiliate='$utm_affiliate' WHERE uid='$uid'");
	//~ }
	$db->notify($uid,"ОПЛАТА: $sum р., ".$descr);
	$db->mark_new($uid,3);

} else
	print "Already created ctrl_id=$ctrl_id <br>";
print "Ok";
include "../bottom.inc.php";

?>
