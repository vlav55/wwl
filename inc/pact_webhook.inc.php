<?
http_response_code(200);
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "init.inc.php";
$db=new db($database);

if(!isset($do_not_notify))
	$do_not_notify=false;
function get_promo_code($msg) {
	if(preg_match("/ПРОМО-([0-9]+)/ius",$msg,$m)) {
		return intval($m[1]);
	}
	return false;
}
function get_klid($msg) {
	global $promo_code,$db,$mob;
	if($promo_code) {
		$tm1=time()-(1*24*60*60);
		if($uid=$db->dlookup("uid","leadgen_leads","promo_code='$promo_code' AND tm>$tm1")) {
			$mob_cards=$db->check_mob($db->dlookup("mob_search","cards","uid='$uid'"));
			$mob_webhook=$db->check_mob($mob);
			if($mob_cards!=$mob_webhook) {
				$db->query("UPDATE cards SET mob='$mob_webhook',mob_search='$mob_webhook' WHERE uid='$uid' ");
				$db->save_comm($uid,0,"Клиент отправил промокод в вотсап с другого номера: $mob_webhook");
			//	$db->formula_email("CHECK pact webhook uid=$uid mob_cards=$mob_cards mob_webhook=$mob_webhook","Клиент отправил промокод в вотсап с другого номера: $mob_webhook");
				return false;
			}
		}
		$klid=$db->dlookup("klid","leadgen_log","code='$promo_code' AND res=0");
		//$db->notify_user(1,"$promo_code $klid");
		if($klid) {
			return $klid;
		} else {
		//	$db->formula_email("error webhook get_klid code=$promo_code","wrong promo code");
			return false;
		}
	}
	if(preg_match("/(^[0-9]+$)|(^[0-9]+-[0-9]+$)|(^[0-9]+-[0-9]+-[0-9]+$)/",$msg) ) {
		list($bc,$utm)=explode("-",$msg);
		return(intval($bc));
	}
	return false;
}
function get_utm($msg) {
	global $promo_code,$db,$uid,$mob;
	if($promo_code) {
		$r=$db->fetch_assoc($db->query("SELECT * FROM leadgen_log WHERE code='$promo_code' AND res=0"));
		if(!$r)
			return false;
		$db->query("DELETE FROM utm WHERE uid='$uid' AND promo_code=$promo_code");
		$utm_campaign=$r['utm_campaign'];
		$utm_content=$r['utm_content'];
		$utm_medium=$r['utm_medium'];
		$utm_source=$r['utm_source'];
		$utm_term=$r['utm_term'];
		$utm_ab=$r['utm_ab'];
		$db->query("INSERT INTO utm SET
				uid='$uid',
				tm='".time()."',
				utm_campaign='".$db->escape($utm_campaign)."',
				utm_content='".$db->escape($utm_content)."',
				utm_medium='".$db->escape($utm_medium)."',
				utm_source='".$db->escape($utm_source)."',
				utm_term='".$db->escape($utm_term)."',
				utm_ab='".$db->escape($utm_ab)."',
				pwd_id='2',
				promo_code='$promo_code',
				mob='$mob' ");
		$db->query("UPDATE leadgen_log SET res=1 WHERE code='$promo_code'");
		$tm=time()-(1*24*60*60);
		$db->query("DELETE FROM leadgen_log WHERE tm<'$tm' ");
		return false;
	}
	if(preg_match("/(^[0-9]+$)|(^[0-9]+-[0-9]+$)|(^[0-9]+-[0-9]+-[0-9]+$)/",$msg) ) {
		list($bc,$utm)=explode("-",$msg);
		return intval($utm);
	}
	return false;
}
function get_fl1000($msg) {
	global $promo_code;
	if($promo_code)
		return 1000;
	if(preg_match("/^[0-9]+-[0-9]+-[0-9]+$/",$msg) ) {
		list($bc,$utm,$fl1000)=explode("-",$msg);
		return intval($fl1000);
	}
	return false;
}

$r = json_decode(file_get_contents('php://input'),true);
if($r['type']=="message" && $r['event']="new") {
		//$db->notify_user(1,print_r($r,true));
	$conversation_id=intval($r['data']['conversation_id']);
	if($conversation_id && isset($r['data']['channel_type']) ) {
		if($r['data']['channel_type']=="whatsapp") {
			$ack=intval($r['data']['ack']);
	//$db->query("INSERT INTO pact_log SET tm='".time()."',ack='$ack',webhook='".$db->escape(print_r($r,true))."'");
			$channel_id=intval($r['data']['channel_id']);

			$state=$db->fetch_assoc( $db->query("SELECT state FROM pact_state WHERE channel_id='$channel_id' ORDER BY tm DESC LIMIT 1") )['state'];
			if(!$state) {
				$db->query("INSERT INTO pact_state SET channel_id='$channel_id',tm='".time()."',state=1");
				$db->query("UPDATE users SET pact_channel_online='".time()."' WHERE pact_channel_id='$channel_id'");
			}
			

			if(preg_match("/pact_conversation_test/",$r['data']['message'])) {
				file_put_contents("pact_conversation_test.txt",time());
				print "ok";
				exit;
			}
			if(preg_match("/test_formula_([0-9]+)/",$r['data']['message'],$m) ) {
				$test_id=intval($m[1]);
			//$db->notify_user(1,$m[1]);
				$test_phone=$db->check_mob($r['data']['external_public_id']);
				$db->query("UPDATE pact_test SET res=1 WHERE id='$test_id'");
			//$db->formula_email("channel test OK $test_phone",$test_id);
				print "ok";
				exit;
			}

file_put_contents("message.txt",print_r($r,true));
			$promo_code=get_promo_code($r['data']['message']);
	//	$db->notify_user(1,$promo_code);
			if($r['data']['income']==1) { //$r['data']['income']==1
				$outg=(intval($r['data']['income'])==1)?0:1;
				$mob=$db->check_mob($r['data']['external_public_id']);
				if(!$mob && $outg==0) {
					file_put_contents("ERROR.txt",print_r($r,true));
					$db->formula_email("ERROR in pact_webhook.inc.php mob=false","r=". nl2br(print_r($r,true))  );
					exit;
				}
				$uid=$db->dlookup("uid","cards","pact_conversation_id='$conversation_id'"); //MAIN SEARCH
				if(!$uid && $mob)
					$uid=$db->dlookup("uid","cards","mob_search='$mob'");
				$new_reg=false;
				if($uid) { //IN CARDS
					if($mob)
						$db->query("UPDATE cards SET mob='$mob',mob_search='$mob' WHERE uid='$uid'");
					//$db->query("UPDATE cards SET wa_allowed=1 WHERE uid='$uid'");
				} elseif(!$outg) { //NOT IN CARDS AND INCOMING
					if($user_id=$db->dlookup("user_id","cards","mob_search='$mob'") ) {
						if($db->dlookup("pact_conversation_id","cards","mob_search='$mob'")==0) { //если есть в базе, но ни с кем не общается
							$db->query("UPDATE cards SET pact_conversation_id='$conversation_id',wa_allowed=1 WHERE mob_search='$mob' ");
						} else {
							print "ok";	exit;
						}
						//$db->query("UPDATE cards SET pact_conversation_id='$conversation_id' WHERE uid='$uid'");
					} else {
						//INSERT
						$klid=get_klid($r['data']['message']);
						if(1==2 && $klid && $user_id=$db->dlookup("id","users","klid='$klid'")) {
						} else {
							//~ if($channel_id=intval($r['data']['channel_id']) ) {
								//~ if($klid=$db->dlookup("klid","users","pact_channel_id='$channel_id'")) {
									//~ $user_id=$db->dlookup("id","users","klid='$klid'");
								//~ } else {
									//~ $klid=0; $user_id=0;
								//~ }
							//~ }
							 ////// HERE SHOULD BE TREATING IF NOT IN CARDS !!!!!!!!!!!
							if($db->database=='vkt')
								$db->notify_chat(-4698221513,"СООБЩЕНИЕ В ВОТСАП ОТ НОВОГО ЛИДА $mob (НЕТ В БАЗЕ)\n".$r['data']['message']);
							print "ok";
							exit;
						}
						$uid=$db->get_unicum_uid();
						$uid_md5=$db->uid_md5($uid);
//		file_put_contents("HERE_3.txt","uid=$uid channel_id=$channel_id user_id=$user_id klid=$klid cid=$conversation_id");
						$db->query("INSERT INTO cards SET 
								uid='$uid',
								uid_md5='$uid_md5',
								name='".$db->escape($mob)."',
								mob='$mob',
								mob_search='$mob',
								acc_id=2,
								razdel='4',
								source_id='0',
								fl_newmsg=0,
								tm_lastmsg=".time().",
								tm=".time().",
								user_id='$user_id',
								pact_conversation_id='$conversation_id',
								utm_affiliate='$klid',
								wa_allowed=1
								");
						if($name=$db->dlookup("name","cards_wa_name","cid='$conversation_id'"))
							if(!is_numeric($name))
								$db->query("UPDATE cards SET name='".$db->escape($name)."' WHERE pact_conversation_id='$conversation_id'");
						sleep(1);
						$db->save_comm($uid,0,"РЕГИСТРАЦИЯ PPL",12);
						if(!$do_not_notify)
							$db->notify($uid,"⭐ РЕГИСТРАЦИЯ НОВЫЙ ЛИД");
						if($klid==get_klid($r['data']['message']) ) {
							if($db->dlookup("id","users","klid='$klid'")) {
								$new_reg=true;
								$p=new papa_bot;
								$p->mess_ask_name($uid);
								$tm1=$db->dt1(time());
								if($utm=get_utm($r['data']['message']) ) {
									if( !$db->dlookup("id","utm","tm>$tm1 AND utm_term='$utm' AND uid='$uid'") )
										$db->query("INSERT INTO utm SET uid='$uid',tm='".time()."',utm_term='$utm',pwd_id='3'");
								}
								if($old_user_id=get_fl1000($r['data']['message'])) {
									include $leadgen_path;
									$lg=new leadgen;
									$lg->lead_redistribute($uid,$klid,$old_user_id,$promo_code);
								} else
									$db->formula_email("lead_not_redistributed $uid $user_id (new)",$r['data']['message']);
							}
						}
					}
				}

				if(!isset($user_id))
					$user_id=0;
				if(!$user_id) {
					if($uid) {
						$user_id=$db->dlookup("user_id","cards","uid='$uid'");
					}
					//~ file_put_contents("HERE.txt","c=$channel_id user_id=$user_id klid=$klid");
					//~ if($user_id=$db->dlookup("id","users","pact_channel_id='$channel_id'")) {
						//~ $utm_aff=$db->dlookup("klid","users","pact_channel_id='$channel_id'");
						//~ if($db->dlookup("user_id","cards","uid='$uid'")==0) {
							//~ $db->query("UPDATE cards SET user_id='$user_id',utm_affiliate='$utm_aff' WHERE uid='$uid'");
						//~ } else { //CONFLICT
						//~ }
					//~ }
				}
				if(!$outg) {
					$db->query("UPDATE cards SET wa_allowed=1 WHERE uid='$uid' ");
					$klid=get_klid($r['data']['message']);
					if($klid && !$new_reg) { ////// IF IN CARDS, NOT A NEW REG!
						if($user_id=$db->dlookup("id","users","klid='$klid'")) {
							$user_id_cards=$db->dlookup("user_id","cards","uid='$uid'");
							$card_id=$db->dlookup("id","cards","uid='$uid'");
						//	$db->formula_email("123 card_id=$card_id uid=$uid klid=$klid 1=$user_id 2=$user_id_cards","");
							if($user_id != $user_id_cards) {
								if($user_id_cards!=0) { //CONFLICT
									if($klid != $card_id) {  //check if it is not partner himself
										if($db->dlookup("del","users","id='$user_id_cards'") ==1) {
											//if old partner  not work pass to another
											$db->query("UPDATE cards SET user_id='$user_id',utm_affiliate='$klid',comm='chk1 $cards_id $klid' WHERE uid='$uid'");
											$user_id_cards=$user_id;
										} else {
											//
			//file_put_contents("C1.txt","uid=$uid channel_id=$channel_id user_id=$user_id klid=$klid user_id_cards=$user_id_cards cid=$conversation_id");
											$p=new papa_bot;
											$p->mess_if_linked($uid,$user_id,$user_id_cards);
											if($utm=get_utm($r['data']['message']) ) {
												$tm1=$db->dt1(time());
												if( !$db->dlookup("id","utm","tm>$tm1 AND utm_term='$utm' AND uid='$uid'") )
													$db->query("INSERT INTO utm SET uid='$uid',tm='".time()."',utm_term='$utm',pwd_id='4'");
											}
											if(get_fl1000($r['data']['message'])) {
												include $leadgen_path;
												$lg=new leadgen;
												$lg->lead_redistribute($uid,$klid,$user_id_cards,$promo_code);
											}
										}
									}
								} else {
									//~ $db->query("UPDATE cards SET user_id='$user_id',utm_affiliate='$klid' WHERE uid='$uid'");
									//~ $user_id_cards=$user_id;
									$db->query("UPDATE cards SET user_id='$user_id',utm_affiliate='$klid',comm='chk2 $cards_id $klid' WHERE uid='$uid'");
									$user_id_cards=$user_id;
								}
							}
							if($user_id == $user_id_cards) {
								$new_reg=true;
								$db->save_comm($uid,0,"РЕГИСТРАЦИЯ PPL ПОВТОРНАЯ",39);
								if(!$do_not_notify)
									$db->notify($uid,"⭐ РЕГИСТРАЦИЯ ПОВТОРНАЯ");
								$p=new papa_bot;
								$p->mess_ask_hello($uid);
								$tm1=$db->dt1(time());
								if($utm=get_utm($r['data']['message']) ) {
									if( !$db->dlookup("id","utm","tm>$tm1 AND utm_term='$utm' AND uid='$uid'") )
										$db->query("INSERT INTO utm SET uid='$uid',tm='".time()."',utm_term='$utm',pwd_id='5'");
								}
								if($old_user_id=get_fl1000($r['data']['message'])) {
									include $leadgen_path;
									$lg=new leadgen;
									$lg->lead_redistribute($uid,$klid,$old_user_id,$promo_code);
								} else
									$db->formula_email("lead_not_redistributed $uid $user_id (exists)",$r['data']['message']);
							}
						}
					}
				}
				//~ if($uid==-8084) {
					//~ file_put_contents("8084.txt",print_r($r,true));					
				//~ }
				$source_id=get_klid($r['data']['message'])?123:0; 
				$db->query("INSERT INTO msgs SET
						uid='$uid',
						acc_id='101',
						mid='".intval($r['data']['external_id'])."',
						tm='".time()."',
						msg='".$db->escape($r['data']['message'])."',
						outg='$outg',
						user_id='$user_id',
						source_id='$source_id'
						");
				$msgs_id=$db->insert_id();
				$attach_url="";
				foreach($r['data']['attachments'] AS $a) {
					$db->query("INSERT INTO msgs_attachments SET
						msgs_id='$msgs_id',
						url='".$db->escape($a['url'])."'");
					$attach_url.=$a['url']."\n";
				}
//$db->notify_user(1,"uid=$uid mob=$mob user_id=$user_id klid=$klid channel_id='$channel_id'");
				if($user_id) {
					if($db->dlookup("pact_channel_id","users","id='$user_id'") != $channel_id ) {
						$db->query("UPDATE users SET pact_channel_id='$channel_id' WHERE id='$user_id' ");
					}
				}
				if(!$outg && !$new_reg) {
					$msg=$r['data']['message'];
					$msg1=$msg;
					$fl_notify=true;
					if(file_exists("bot.inc.php")) {
						$fl_wa=true;
						include_once("bot.inc.php");
					}
					if($fl_notify) {
						if(!$new_reg) {
							if(!get_promo_code($msg1)) {
								$db->mark_new($uid,2);
								$db->notify($uid,"WA: ".$msg1."\n".$attach_url,'msg');
							}
						}
					}

					//~ if(!wa_bot($uid,$msg=$r['data']['message']) ) {
						//~ if(!$new_reg) {
							//~ if(!get_promo_code($r['data']['message'])) {
								//~ $db->mark_new($uid,2);
								//~ $db->notify($uid,"whatsapp: ".$r['data']['message']);
							//~ }
						//~ }
					//~ }
				}
			}
		}
	}
}
if($r['type']=="conversation") {
	if($r['data']['channel_type']=="whatsapp") {
//file_put_contents("conversation_$pact_mob.txt",print_r($r,true));
		$cid=$r['data']['external_id'];
		$client_name=trim($db->dlookup("name","cards","pact_conversation_id='$cid'") );
		if(empty($client_name) || is_numeric($client_name))
			$db->query("UPDATE cards SET name='".$db->escape($r['data']['name'])."' WHERE pact_conversation_id='$cid'");
		$db->query("INSERT INTO cards_wa_name SET cid='$cid',name='".$db->escape($r['data']['name'])."',ava='".$db->escape($r['data']['avatar_url'])."' ");
		
		//~ $pact_mob=$db->check_mob($r['data']['sender_external_id']);
		//~ if($klid=$db->dlookup("id","cards","mob_search='$pact_mob'") ) {
			//~ //$db->query("UPDATE cards SET name='".$db->escape($r['data']['name'])."' WHERE mob_search='$pact_mob'");
			//~ $qstr="INSERT INTO cards_wa_name SET mob='$pact_mob',name='".$db->escape($r['data']['name'])."',ava='".$db->escape($r['data']['avatar_url'])."' ";
			//~ $db->query($qstr);
		//~ }
	}
}
if($r['type']=="system" && $r['severity']=="critical") {
	$channel_id=intval($r['data']['details']['entity_id']);
	if($r['data']['message']=='phone offline' || $r['data']['message']=='unavailable') { //OFFLINE
	//	file_put_contents("system_err.txt","channel_id=$channel_id\n".print_r($r,true));
	//	$db->query("DELETE FROM pact_state WHERE channel_id='$channel_id' ");
		$db->query("INSERT INTO pact_state SET channel_id='$channel_id',tm='".time()."', state=0 ");
	} elseif($r['data']['message']=='phone online') { //ONLINE
	//	file_put_contents("system_ok.txt","channel_id=$channel_id\n".print_r($r,true));
	//	$db->query("DELETE FROM pact_state WHERE channel_id='$channel_id' ");
		$db->query("INSERT INTO pact_state SET channel_id='$channel_id',tm='".time()."', state=1 ");
		$db->query("UPDATE users SET pact_channel_online='".time()."' WHERE pact_channel_id='$channel_id'");
	}
}
if($r['type']=="qr_code") {
	$company_id=$r['company_id'];
	$channel_id=$r['channel_id'];
	$data=$r['data'];
	file_put_contents("qr_codes/qr_$company_id.base64", $data);
}
//~ file_put_contents("QR test 1.txt",print_r($r,true));
//~ if($r['type']=="system" && $r['severity']=="information"  && $r['data']['message']=="authorized") {
//~ file_put_contents("QR test 2.txt",print_r($r,true));
	//~ if($r['data']['details']['entity']=='channel') {
		//~ $channel_id=$r['data']['details']['entity_id'];
//~ file_put_contents("QR test 3.txt id=$channel_id",print_r($r,true));
		//~ $db->query("UPDATE users SET pact_channel_attached_tm='".time()."' WHERE pact_channel_id='$channel_id'");
	//~ }
//~ }
print "ok";

?>
