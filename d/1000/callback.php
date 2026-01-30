<?
require_once 'Hmac.php';

$secret_key = 'd8b79cf5e6b145250a94c1a1bd1c5521761d58f1f2ce0a968e2d01eb3b9d090a';
$headers = apache_request_headers();

try {
	if ( empty($_POST) ) {
		throw new Exception('$_POST is empty');
	}
	elseif ( empty($headers['Sign']) ) {
		throw new Exception('signature not found');
	}
	elseif ( !Hmac::verify($_POST, $secret_key, $headers['Sign']) ) {
		throw new Exception('signature incorrect');
	}

	http_response_code(200);
	file_put_contents("last_webhook.txt",print_r($_POST,true));
	echo 'success';
	include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
	$db=new db("vkt");
	$db->telegram_bot="vkt";
	$db->db200="https://1-info.ru/vkt/db";
	include_once "/var/www/vlav/data/www/wwl/inc/justclick_api.class.php";
	include_once "/var/www/vlav/data/www/wwl/inc/sendpulse.class.php";
	include_once "/var/www/vlav/data/www/wwl/inc/senler_api.class.php";
	include_once "../prices.inc.php";
	if($_POST['payment_status']=='success') {
		$prodamus_id=$_POST['order_id'];
		if(!$db->dlookup("id","avangard","prodamus_id='$prodamus_id'") ) {
			$uid=$_POST['vk_user_id'];
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
			list($p,$order_id)=explode("-",$order_number);
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

			$db->email($emails=array("vlav@mail.ru"), "VKT PAYMENT SUCCESS $descr", "Order_number $order_number $client_name $client_email $client_phone $sum", $from="noreply@1-info.ru",$fromname="VKT", $add_globals=false);
			if($jc_gid) {
				$jc=new justclick_api;
				$jc->login("vkt");
				if(!$jc->add_to_group($jc_gid,$client_email,$client_name,"https://VKT.ru/prodamus/thanks.php"))
					$db->email($emails=array("vlav@mail.ru"), "VKT JC ADD TO $jc_gid  ERROR (product_id=$product_id)", "jc for $order_number returned false", $from="noreply@1-info.ru",$fromname="VKT", $add_globals=true);
			}
			if($sp_book_id) {
				$sp=new sendpulse('vkt');
				if(!$sp->add($sp_book_id,$client_email))
					$db->email($emails=array("vlav@mail.ru"), "VKT SP ADD TO $sp_book_id  ERROR (product_id=$product_id)", "justclick_add_to_group for $order_number returned false", $from="noreply@1-info.ru",$fromname="VKT", $add_globals=true);
			}

			//должно сработать до отправки письма!!!
			$products_sarafan=[30,31,32];
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
					$db->email($emails=array("vlav@mail.ru"), "VKT new company ctrl_id=$ctrl_id CREATED", "", $from="noreply@1-info.ru",$fromname="VKT", $add_globals=true);
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
			if($sp_template) {
				$sp=new sendpulse('vkt');
				$sp->email_by_template($sp_template,
									$client_email,
									$client_name,
									$subj="🔶 $descr",
									$from_email='winwinland@1-info.ru',
									$from_name='WINWINLAND',
									$uid,$passw);
				//$db->vktrade_send_skip_wa=true;
				$db->vktrade_send_wa($uid,"Благодарим, оплата получена. Инструкции по доступу отправлены вам на емэйл: $client_email. Если письмо не приходит, посмотрите в папках спам или рассылки. Вопросы можно задавать сюда. Всего наилучшего!");
			}
			if($senler && $uid>0) {
				$s=new senler_api;
				if(!$s->subscribers_add($uid, $senler))
					$db->email($emails=array("vlav@mail.ru"), "VKT SENLER ADD TO $senler  ERROR (uid=$uid product_id=$product_id)", "subscribers_add for $order_number uid=$uid returned false", $from="noreply@1-info.ru",$fromname="VKT", $add_globals=true);
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


			if($product_id==20) { //leadgen
				$cost_per_lead=$db->fetch_assoc($db->query("SELECT * FROM leadgen_cost WHERE 1 ORDER BY tm DESC LIMIT 1"))['cost_per_lead'];
				$klid=$db->dlookup("id","cards","uid='$uid' ");
				$user_id=$db->dlookup("id","users","klid='$klid'");
				$amount=intval($sum/$cost_per_lead);
				$db->query("INSERT INTO leadgen_orders SET
							user_id='$user_id',
							tm='".time()."',
							amount='$amount',
							sum_pay='$sum'
							");
			}
			if($product_id==40) { //продление доступа
				$klid=$db->dlookup("id","cards","uid='$uid' ");
				$user_id=$db->dlookup("id","users","klid='$klid'");
				if($user_id) {
					//$tm=$db->fetch_assoc($db->query("SELECT tm FROM billing WHERE user_id='$user_id' AND payed!=10 ORDER BY tm DESC LIMIT 1"))['tm'];
					//$tm+=30*24*60*60;
					$tm=$db->dt1(time());
					$db->query("INSERT INTO billing SET
								user_id='$user_id',
								tm='$tm',
								payed=1,
								sum_pay='$sum'
								");
				} else
					$db->email($emails=array("vlav@mail.ru"), "VKT ERROR $descr", "Order_number $order_number $client_name $client_email $client_phone $sum", $from="noreply@1-info.ru",$fromname="VKT", $add_globals=false);
			}
		}
	} elseif($_POST['payment_status']=='order_denied' || $_POST['payment_status']=='order_canceled') {
		$uid=intval($_POST['vk_user_id']);
		if($uid) {
			$product_id=$_POST['customer_extra'];
			$descr=$base_prices[$product_id]["descr"];
			$payment_status_description=$_POST['payment_status_description'];
			$sum=intval($_POST['sum']);
			$db->notify($uid,"ОТКАЗ В РАССРОЧКЕ: $sum р., $descr \n$payment_status_description");
			$db->mark_new($uid,3);
		}
	}
}
catch (Exception $e) {
	http_response_code($e->getCode() ? $e->getCode() : 400);
	$err=sprintf('error: %s', $e->getMessage());
	file_put_contents("last_webhook_error.txt",$err);
}
?>
