<?
http_response_code(200);
$txt=file_get_contents("gk_callback.txt");
$txt=print_r($_GET,true)."\n\n".$txt;
file_put_contents("gk_callback.txt",$txt);

include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db("papa");
$db->pact_token="papa";
$db->telegram_bot="papavdekrete"; $db->db200="https://1-info.ru/f12/db";

if(strpos($_GET['action'],'payment')!==false) {

	$gk_id=intval($_GET['id']);
	$order_id=intval($_GET['offers']);
	$amount=intval((preg_replace("/([\s]+)|(руб\.)/ui","",$_GET['payed_money'])));
	$gk_cost_money=intval($_GET['cost_money_value']);
	$gk_status=($_GET['status']=='Завершен')?1:0;
	$order_descr=$_GET['positions'];
	$f_name=$_GET['user_first_name'];
	$l_name=$_GET['user_last_name'];
	$gk_uid=$_GET['user_id'];
	$mob=$db->check_mob($_GET['user_phone']);
	$city=$_GET['user_city'];
	$email=$_GET['user_email'];

	$qnt=1;
	preg_match("/x ([0-9]+)/",$order_descr,$match);
	if(intval($match[1]) )
		$qnt=intval($match[1]);


	if(preg_match("/(Автоворонка)|(Мечта)/iu",$order_descr)) {
		$source_id=15; $product_id=1;
	} elseif(preg_match("/Азбука/iu",$order_descr)) {
		$source_id=16; $product_id=2;
	} elseif(preg_match("/Школа/iu",$order_descr)) {
		$source_id=17; $product_id=3;
	} elseif(preg_match("/наставник/iu",$order_descr)) {
		$source_id=18; $product_id=4;
	} elseif(preg_match("/лидов/iu",$order_descr)) {
		$source_id=109; $product_id=5;
	} else {
		$db->papa_email("Error: product_id is not defined",print_r($_GET,true));
	}

	$uid=intval($_GET['user_uid']);
	if(!$uid) {
		$uid=$db->dlookup("uid","cards","mob_search='$mob'");
		if(!$uid) {
			$db->papa_email("Error: uid for $mob is not in db",print_r($_GET,true));
			$uid=$db->get_unicum_uid();
			//$uid=$r['vk_uid'];
			$uid_md5=$db->uid_md5($uid);
			$tm=time();
			$db->query("INSERT INTO cards SET 
					uid='$uid',
					uid_md5='$uid_md5',
					name='".$db->escape($f_name)."',
					surname='".$db->escape($l_name)."',
					mob='$mob',
					mob_search='$mob',
					email='".$db->escape($email)."',
					acc_id=2,
					razdel='3',
					source_id='1',
					fl_newmsg=0,
					tm_lastmsg=".time().",
					tm=$tm,
					user_id='3',
					pact_conversation_id='0',
					utm_affiliate='1004',
					wa_allowed=1
					",0);
					
			//~ $db->save_comm($uid,0,"Добавлено из ГК \n $order_descr",$source_id);
			//~ $db->query("UPDATE avangard SET vk_uid='$uid' WHERE id={$r['id']}");
			print "$n INSERTED $uid $phone $f_name $l_name\n";
		}
	}

	if(!$db->dlookup("order_id","avangard","gk_id='$gk_id'")) {
		$db->query("INSERT INTO avangard SET
			tm='".time()."',
			product_id='$product_id',
			order_id='$order_id',
			order_descr='".$db->escape($order_descr)."',
			amount='$amount',
			qnt='$qnt',
			c_name='".$db->escape($f_name."_".$l_name)."',
			phone='$mob',
			email='".$db->escape($email)."',
			gk_id='$gk_id',
			gk_uid='$gk_uid',
			gk_cost_money='$gk_cost_money',
			gk_status='$gk_status',
			vk_uid='$uid'
			");
		if($source_id==109) {
			$klid=$db->dlookup("id","cards","uid='$uid'");
			$user_id=$db->dlookup("id","users","klid='$klid'");
			$db->query("INSERT INTO leadgen_orders SET user_id='$user_id',tm='".time()."', amount='".($qnt*10)."'");
		}
		$db->save_comm($uid,0,"$order_descr",$source_id);
		$db->notify($uid, "ОПЛАТА ($amount) :".$order_descr);
		$db->papa_email("Новая оплата uid=$uid sum=$amount",print_r($_GET,true));
		$db->query("UPDATE cards SET razdel='3' WHERE uid='$uid'");
		if(empty($db->dlookup("city","cards","uid='$uid'")) && !empty($city) )
			$db->query("UPDATE cards SET city='".$db->escape($city)."' WHERE uid='$uid'");
		if(empty($db->dlookup("name","cards","uid='$uid'")) || intval($name) ) {
			$db->query("UPDATE cards SET name='".$db->escape($f_name)."' WHERE uid='$uid'");
			$db->query("UPDATE cards SET surname='".$db->escape($l_name)."' WHERE uid='$uid'");
		}
		if(empty($db->dlookup("email","cards","uid='$uid'"))  )
			$db->query("UPDATE cards SET email='".$db->escape($email)."' WHERE uid='$uid'");
		if(empty($db->dlookup("mob","cards","uid='$uid'"))  ) 
			$db->query("UPDATE cards SET mob='".$db->escape($mob)."',mob_search='$mob' WHERE uid='$uid'");
		$db->papa_email("СОБЫТИЕ uid=$uid sum=$amount",print_r($_GET,true));
	}
}
print "ok";
?>
