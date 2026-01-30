<?
if (!function_exists('log1')) {
	function log1($msg) {
		file_put_contents("avangard_last_pay.log", date("d.m.Y H:i:s")."--->".$msg."\n",FILE_APPEND); //FILE_APPEND
	}
}
		if(!$db->dlookup("id","avangard","res=1 AND order_id='$order_id' AND order_id!=0") ) {
log1(__LINE__);
			$r=$db->fetch_assoc($db->query("SELECT * FROM avangard WHERE res=0 AND order_id='$order_id'"));
			$avangard_id=$r['id'];
			$uid=$r['vk_uid'];
			$product_id=$r['product_id'];
			$land_num=$r['land_num'];
			$jc_gid=$base_prices[$product_id]["jc"];
			$sp_book_id=$base_prices[$product_id]["sp"];
			$sp_template=$base_prices[$product_id]["sp_template"];
			$senler=$base_prices[$product_id]["senler"];
			$descr=$base_prices[$product_id]["descr"];
			$source_id=30; //$base_prices[$product_id]["source_id"];
			$promocode_id=$r['promocode_id'];

			if($razdel=$base_prices[$product_id]["razdel"])
				$db->query("UPDATE cards SET razdel='$razdel' WHERE uid='$uid'");
				
			if($tag_id=$base_prices[$product_id]["tag_id"])
				$db->tag_add($uid,$tag_id);

			$client_name=$r['c_name'];
			$client_phone=$r['phone'];
			$client_email=$r['email'];
			$order_number=$order_id;
			$sum=$r['amount'];
			if(!isset($commission_sum))
				$commission_sum=0;
			$sum1=$sum-$commission_sum;
			$ctrl_link="NAN";
			$tm_end=$r['tm_end'];
			$comm=trim($r['comm']);

			$db->query("UPDATE avangard SET tm_pay='".time()."',res=1,amount1='$sum1' WHERE order_id='$order_id'"); //IMPORTANT
	log1(__LINE__."UPDATE avangard SET  tm_pay='".time()."',res=1,amount1='$sum1' WHERE order_id='$order_id'");

			if($promocode_id) {
				$promocode_cnt=$db->dlookup("cnt","promocodes","id='$promocode_id'");
				if($promocode_cnt>0) {
					$promocode_cnt--;
					$db->query("UPDATE promocodes SET cnt='$promocode_cnt' WHERE id='$promocode_id'");
				}
			}

			if($jc_gid) {
				$jc=new justclick_api;
				$jc->login("vkt");
				if(!$jc->add_to_group($jc_gid,$client_email,$client_name,"https://VKT.ru/prodamus/thanks.php"))
					$db->email($emails=array("vlav@mail.ru"), "WWL JC ADD TO $jc_gid  ERROR (product_id=$product_id)", "jc for $order_number returned false", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
			}
			if($sp_book_id) {
				$sp=new sendpulse('vkt');
				if(!$sp->add($sp_book_id,$client_email))
					$db->email($emails=array("vlav@mail.ru"), "WWL SP ADD TO $sp_book_id  ERROR (product_id=$product_id)", "justclick_add_to_group for $order_number returned false", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
			} 

			//должно сработать до отправки письма!!!
			if($ctrl_id==1) {
				if($comm != 'partner') {
					$db->email($emails=array("vlav@mail.ru"), "WWL PAYMENT SUCCESS $descr", "Order_number $order_number $client_name $client_email $client_phone $sum", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=false);
					$products_sarafan=$products_winwinland; //see init.custom.php
					//$db->notify_me("HERE_".print_r($products_sarafan)); exit;
					if(in_array($product_id,$products_sarafan)) {
						log1(__LINE__."");
						include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
						$vkt=new vkt('vkt');
						
						if(!$client_ctrl_id=$vkt->get_ctrl_id_by_uid($uid)) {
							log1(__LINE__."");
							//NEW COMPANY CREATING
							$client_ctrl_id=$vkt->create_ctrl_company($uid);
							//print "ctrl_id=$client_ctrl_id <br>";
							$vkt->create_ctrl_dir($client_ctrl_id);
							$vkt->create_ctrl_databases($client_ctrl_id);
							$client_ctrl_dir=$vkt->get_ctrl_dir($client_ctrl_id);
							$client_ctrl_db=$vkt->get_ctrl_database($client_ctrl_id);
							$ctrl_link=$vkt->get_ctrl_link($client_ctrl_id);
							$db->email($emails=array("vlav@mail.ru"), "WWL new company ctrl_id=$client_ctrl_id CREATED", "", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
							$passw=$vkt->passw_gen($len=10);
							log1(__LINE__." ctrl_id=$client_ctrl_id");

							$vkt->connect($client_ctrl_db);
							$vkt->query("UPDATE users SET
								passw='".md5($passw)."',
								real_user_name='".$vkt->escape($client_name)."',
								email='".$vkt->escape($client_email)."',
								comm='".$vkt->escape($passw)."'
								WHERE username='admin'");

							log1(__LINE__." $passw");
							//add 2 weeks code here ...
							
							$db->connect('vkt');
							$db->query("UPDATE 0ctrl SET admin_passw='$passw' WHERE id='$client_ctrl_id'");
							if($db->avangard_payments_count($uid,$products_sarafan)==1 && $product_id!=20) { //if new user
								log1(__LINE__." first payment detected");
								$tm_end += 0; //14*24*60*60;
								//$db->query("UPDATE avangard SET tm_end='$tm_end' WHERE id='$avangard_id'");
								log1(__LINE__." tm_end corrected to $tm_end");
							}
						} else {
							//print "Company exists - $client_ctrl_id <br>";
							$client_ctrl_link=$vkt->get_ctrl_link($client_ctrl_id);
						}
						if(in_array($product_id,$products_yclients)) { // YCLIENTS
							include "/var/www/vlav/data/www/wwl/inc/yclients.class.php";
							if($salon_id=$db->is_yclients($client_ctrl_id)) {
								$y=new yclients($salon_id);
								$tm_end_ctrl=$y->dlookup("tm_end","0ctrl","id='$client_ctrl_id'");
								if($tm_end_ctrl<time())
									$tm_end_ctrl=time();
								$term=$y->dlookup("term","product","del=0 AND id='$product_id'");
								$tm_end=$y->dt2($tm_end_ctrl+($term*24*60*60));
								if($y->send_payment_webhook($salon_id, $sum, $tm_end)) {
									$y->notify_me("yclients pay_callback_common send_payment_webhook OK ctrl_id=$client_ctrl_id $salon_id $sum $tm_end $tm_end_ctrl");
									$y->query("UPDATE 0ctrl SET tm_end='$tm_end' WHERE id='$client_ctrl_id'");
								} else {
									$y->notify_me("yclients pay_callback_common send_payment_webhook ERROR ctrl_id=$client_ctrl_id $salon_id $sum $tm_end");
								}
							}
						}
					}
				} else {
					include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
					$p=new partnerka(false,$database);
					$klid=$db->get_klid_by_uid($uid);
					$user_id=$db->get_user_id($klid);
					$term=$db->dlookup("term","product","del=0 AND id='$product_id'");
					$months=round($term/30,0);
					$p->users_billing_add($user_id,1,$months,0,$comm=null);
				}
			}

			if(!empty($sp_template)) {
log1(__LINE__." sp_template=$sp_template");
				if(!empty($unisender_secret)) {
log1(__LINE__." unisender_secret=$unisender_secret");
					$db->connect('vkt');
					$passw=$db->dlookup("admin_passw","0ctrl","id='$ctrl_id'");
					$db->connect($database);
					$sp=new unisender($unisender_secret,$email_from,$email_from_name);
					$sp->email_by_template($client_email,$sp_template,
						$sp_arr=['uid'=>$db->uid_md5($uid),
						'passw'=>trim($passw),
						'name'=>trim($client_name),
						'vkt_link'=>$client_ctrl_link,
						'product'=>trim($descr),
						'sum'=>$sum
						]);
					$sp_res="email=$client_email sp_template=$sp_template \n".print_r($sp_arr,true)."\n".print_r($sp->res,true)."\n".print_r($sp->post_data,true);

					$db->telegram_bot=$tg_bot_notif;
					$db->db200=$DB200;
					$db->vktrade_send_tg_bot=$tg_bot_msg;				
					$db->vktrade_send_wa($uid,"Благодарим, оплата получена. Инструкции отправлены вам на емэйл: $client_email. Если письмо не приходит, посмотрите в папках спам или рассылки. Вопросы можно задавать сюда. Всего наилучшего!");
log1(__LINE__." email to user sent $sp_res");
				}
			}
			
			if($senler && $uid>0) {
				log1(__LINE__."");
				$s=new senler_api;
				if(!$s->subscribers_add($uid, $senler))
					$db->email($emails=array("vlav@mail.ru"), "WWL SENLER ADD TO $senler  ERROR (uid=$uid product_id=$product_id)", "subscribers_add for $order_number uid=$uid returned false", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
			}

			$db->avangard_tm_end_set($avangard_id,$tm_end);
			log1(__LINE__." tm_end saved as $tm_end");
			$db->save_comm($uid,0,"ОПЛАТА: $descr order_number=$order_number amount=$sum",$source_id,$vote_vk_uid=0,$mode=0, $force=false);

			if(!$land_num)
				$land_num=$db->dlookup("land_num","lands","del=0 AND product_id='$product_id'");
			if($land_num) {
				include_once "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
				$s=new vkt_send($database);
				$res=$db->query("SELECT * FROM vkt_send_1 WHERE del=0 AND (sid=30 OR sid=31) AND (land_num='$land_num' OR land_num=0)",0);
				while($r=$db->fetch_assoc($res)) {
					if($r['sid']==30)
						$s->vkt_send_task_add($ctrl_id, $tm_event=intval(time()+$r['tm_shift']), $vkt_send_id=$r['id'],$vkt_send_type=3,$uid,$order_id);
					elseif($r['sid']==31 && $tm_end)
						$s->vkt_send_task_add($ctrl_id, $tm_event=intval($tm_end+$r['tm_shift']), $vkt_send_id=$r['id'],$vkt_send_type=3,$uid,$order_id);
				}
				$res=$db->query("SELECT * FROM vkt_send_1 WHERE del=0 AND sid=23 AND (land_num='$land_num' OR land_num=0)",0);
				while($r=$db->fetch_assoc($res)) {
					$s->vkt_send_task_del($vkt_send_id=$r['id'],$ctrl_id,$uid,$order_id);
				}
			}

			
			$db->query("UPDATE cards SET tm_lastmsg='".time()."',tm_user_id='".time()."' WHERE uid='$uid'");
			$db->notify($uid,"ОПЛАТА: $sum р., ".$descr,'pay');
			$db->mark_new($uid,3);
			log1(__LINE__." notified");
			if($sum>10) {
				$db->tag_add($uid,1);
				log1(__LINE__." tag=1 added");
			}
			log1(__LINE__." finished");
		}

?>
