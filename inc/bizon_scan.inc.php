<?
http_response_code(200);
//authBizon(); //НЕ НУЖНО, АВТОРИЗАЦИЯ ЧЕРЕЗ ТОКЕН
print "BIZON SCANNING STARTED \n";

if(!isset($wid))
	$wid=get_new_webinar($room_regex);
print $wid."<br>\n";
// exit;
if($wid) {
	//$db->print_r(get($wid));
	//$res=get("27379:go*2019-12-03T12:00:00");
	//$res=get("27379:go*2019-12-04T12:30:00");
	$res=get($wid);
	print_r($res);
	file_put_contents("bizon_webhook.txt",$res);
//	exit;

log1("duration_min=$duration_min duration_for_discount=$duration_for_discount");

	$result=json_decode($res['report']['report'], true);
	if(!is_array($result)) {
		print "Result is not array. Probably data for $wid is not prepared yet. \n";
		log1("Result is not array. Probably data for $wid is not prepared yet");
		$db->yoga_email("BIZON converting error - Result is not array. Probably data for $wid is not prepared yet", print_r($res, true));
		exit;
	}
	$msgs=json_decode($res['report']['messages'], true);
	$msgsTS=json_decode($res['report']['messagesTS'], true);
	$uids_visited=array();
	$num_senler_b=0; $num_senler_c=0; $num_jc_b=0; $num_d=0;
	$num_crm_inserted=0; $num_crm_edited=0;
	foreach($result['usersMeta'] AS $userid=>$r) {
		$uid=false;
		$email=false;
		print "userid=$userid <br>";

		$i=0; $msgs_out="";
		foreach($msgs[$userid] AS $msg) {
			$msgs_out.= intval($msgsTS[$userid][$i]/(60*60)).":".
				intval($msgsTS[$userid][$i]/60).":".
				intval($msgsTS[$userid][$i]%60)." ".$msg."\n";
			$i++;
		}
		print $msgs_out;

//print "HERE_"; exit;
//		exit;

		$phone=(isset($r['phone']))?$db->check_mob($r['phone']):false;

		if(isset($r['cu1'])) {
			if($db->is_md5($r['cu1']))
				$uid=$db->dlookup("uid","cards","uid_md5='{$r['cu1']}'");
			elseif(intval($r['cu1']))
				$uid=intval($r['cu1']);
		}

		$uid=$db->dlookup("uid","cards","uid='$uid'");

		if(!$uid && isset($r['utm_term'])) {
			if($db->is_md5($r['utm_term']))
				$uid=$db->dlookup("uid","cards","uid_md5='{$r['cu1']}'");
			elseif(intval($r['utm_term']))
				$uid=intval($r['utm_term']);
		}

		$uid=$db->dlookup("uid","cards","uid='$uid'");
			
		if(!$uid ) {
			if(isset($r['email']) && filter_var($r['email'], FILTER_VALIDATE_EMAIL)) {
				$uid=$db->dlookup("uid","cards","del=0 AND email='{$r['email']}'");
				if(!$uid) {
					if($phone) {
						$uid=$db->dlookup("uid","cards","mob_search='$phone'");
					}
				}
			}
		}
		
		
		if(!$uid) {
			$uid=$db->get_unicum_uid();
		}

		if(isset($r['email']) && filter_var($r['email'], FILTER_VALIDATE_EMAIL))
			$email=trim($r['email']);

		//print_r($r);
		if(!$email) {
			$email=trim($db->dlookup("email","cards","uid='$uid'"));
			if(empty($email))
				$email=false;
		}

		if(!$uid || !$db->dlookup("id","cards","uid='$uid'")) {
			if($phone) {
				$uid=$db->dlookup("uid","cards","mob_search='$phone'");
				if(!$uid) {
					log1("uid=false continued phone=$phone is Ok but not in cards");
					$db->yoga_email("BIZON converting error - phone=$phone is Ok but not in cards", print_r($r, true));
					continue;
				}
			} else {
				log1("uid=false continued no utm_term,phone or email");
				$db->yoga_email("BIZON converting error - no utm_term,phone or email", print_r($r, true));
				continue;
			}
		}

		if($phone) { //correct phone if missing
			if(empty($db->dlookup("mob","cards","uid='$uid'"))) {
				$db->query("UPDATE cards SET mob='$phone',mob_search='$phone' WHERE uid='$uid' ");
			}
		}
		if($email) { //correct email if missing
			if(empty($db->dlookup("email","cards","uid='$uid'"))) {
				$db->query("UPDATE cards SET email='$email' WHERE uid='$uid' ");
			}
		}
		if(!$email) 
			$email="noemail@1-info.ru";
		
		log1("START FOR uid=$uid email=$email phone=$phone");

		$utm="";
		if(isset($r['utm_source']))
			$utm.="utm_source='".$db->escape($r['utm_source'])."',";
		if(isset($r['utm_campaign']))
			$utm.="utm_campaign='".$db->escape($r['utm_campaign'])."',";
		if(isset($r['utm_content']))
			$utm.="utm_content='".$db->escape($r['utm_content'])."',";
		if(isset($r['utm_medium']))
			$utm.="utm_medium='".$db->escape($r['utm_medium'])."',";
		if(isset($r['utm_term']))
			$utm.=""; //"utm_term='".$db->escape($r['utm_term'])."',";
		if(!empty($utm)) {
			$utm=substr($utm,0,strlen($utm)-1);
		//	print $utm;
			$pwd_id=16;
			$db->query("INSERT INTO utm SET uid='$uid',tm='".time()."',$utm,pwd_id='$pwd_id'");
		}
		
		//~ if($db->dlookup("razdel","cards","uid='$uid'")==3) { //if A
			//~ print "uid=$uid is A, passed \n";
			//~ log1("razdel=A continued");
			//~ continue;
		//~ }
		if(isset($r['username'])) {
			$exist_name=$db->dlookup("name","cards","uid='$uid'");
			if(empty($exist_name) || intval($exist_name) ) {
				$db->query("UPDATE cards SET name='".$db->escape($r['username'])."' WHERE uid='$uid'");
				$exist_name=$r['username'];
			}
		}
		if(isset($r['city'])) {
			$city=$r['city'];
			$db->query("UPDATE cards SET city='".$db->escape($city)."' WHERE uid='$uid'");
		} else $city=false;

		if(isset($r['country']))
			$country=$r['country']; else $country=false;

		if(isset($r['view']) && isset($r['viewTill'])) {
			$dur_min=intval(($r['viewTill']-$r['view'])/1000/60);
			$dur_percent=intval($dur_min/$duration_min*100);
			$dt_from=date("H:i",$r['view']/1000);
			$dt_till=date("H:i",$r['viewTill']/1000);
		} else {
			$dur_min=false; $dur_percent=false;$dt_from="n/a";$dt_till="n/a";
		}


		$razdel=$rid_d; //D
		$source_id=$sid_d; 
		$fl_newmsg=$fl_newmsg_d;
		$uids_visited[]=$uid;

		$tm_2days=$db->dt1(time()-(2*24*60*60));
		if(!isset($days_visited_webinar_limit) )
				$days_visited_webinar_limit=30;
		$tm_visited_webinar_limit=$db->dt1(time()-($days_visited_webinar_limit*24*60*60));
		$fl_visited_before=$db->fetch_assoc($db->query("SELECT tm FROM msgs
			WHERE uid='$uid' AND source_id='$sid_b' AND tm<'$tm_2days' AND tm>'$tm_visited_webinar_limit'
			ORDER BY id DESC LIMIT 1"));
		$finished=false;

		if(!isset($duration_discount))
			$duration_discount=1*24*60*60;
		if(isset($r['finished'])) {
			$finished=1;
			$razdel=$rid_b;
			$source_id=$sid_b;
			$num_jc_b++;
			$fl_newmsg=$fl_newmsg_b;
			log1("fl finished=1");
			
			foreach($pids_for_discount AS $pid)
				$res_discount=$db->yoga_set_discount($uid,$price_id=2,time(),time()+($duration_discount),$pid);
			log1("razdel=B res_discount=$res_discount");
		} else {
			if($dur_min>$duration_for_discount) {
				if(!$fl_visited_before) {
					foreach($pids_for_discount AS $pid)
						$res_discount=$db->yoga_set_discount($uid,$price_id=2,time(),time()+($duration_discount),$pid);
					log1("razdel=B res_discount=$res_discount");
					$razdel=$rid_b;
					$source_id=$sid_b;
				} else {
					log1("visited before");
					$razdel=$rid_f;
					$source_id=$sid_f;
				}
				$num_jc_b++;
				$fl_newmsg=$fl_newmsg_b;
			} else {
				$source_id=$sid_d;
				$num_d++;
				$fl_newmsg=$fl_newmsg_d;
			}
		}

		if($land_num>0) {
			$res_vkts=$db->query("SELECT * FROM vkt_send_1 WHERE del=0 AND sid='$source_id' AND (land_num='$land_num' OR land_num=0)",0);
			while($r_vkts=$db->fetch_assoc($res_vkts)) {
				$vkts->vkt_send_task_add($ctrl_id, $tm_event=intval(time()+$r_vkts['tm_shift']), $vkt_send_id=$r_vkts['id'],$vkt_send_type=3,$uid);
			}
		}

		if(isset($r['messages_num']))
			$msg_num=intval($r['messages_num']); else $msgnum=0;

		$comm=($source_id==$sid_b)?"БЫЛ НА ВЕБИНАРЕ- ЗАЧЕТ\n":"БЫЛ НА ВЕБИНАРЕ МАЛО ВРЕМЕНИ\n";
		if($source_id==$sid_f)
			$comm="БЫЛ НА ВЕБИНАРЕ ПОВТОРНО";
		$comm.="{$r['username']} $city $country $dur_min минут $dur_percent%; $dt_from - $dt_till; $msg_num сообщ; БЫЛ ДО КОНЦА=$finished; \n";
		$comm.="СООБЩЕНИЯ НА ВЕБИНАРЕ (час:мин:сек): \n".$msgs_out."\n";
		log1("comm=$comm");


		$razdel_orig=$db->dlookup("razdel","cards","uid='$uid'");
		if(!in_array($razdel_orig, array(4,0))) //not allowed change razdel if not D
			$razdel=$razdel_orig;
		$db->query("UPDATE cards SET fl_newmsg='$fl_newmsg',
						razdel='$razdel',
						source_id='$source_id',
						tm_lastmsg='".time()."'
						WHERE uid='$uid'");
		$db->query("UPDATE cards SET
						tm_schedule='0',
						scdl_opt='0',
						scdl_fl=0
						WHERE uid='$uid' AND scdl_web_id='$scdl_web_id'");
		$insert_id=$db->save_comm($uid,0,$comm,$source_id,$vote_vk_uid=$dur_percent,$mode=0, $force=false);
		if(intval($insert_id))
			$db->query("UPDATE msgs SET custom='$scdl_web_id',new='$msg_num' WHERE id=$insert_id"); //saving scdl_web_id
		print "uid=$uid is in CARDS. MARKED AS VISITED WEBINAR \n";
		log1("uid=$uid is in CARDS. MARKED AS VISITED WEBINAR");
		$num_crm_edited++;

		$tm1=time()-(15*60);
		if($db->dlookup("id","msgs","uid=$uid AND source_id=$sid_b AND tm>$tm1")) { //если за последние 15 минут есть запись, что он был на вебинаре
			$db->query("DELETE FROM msgs WHERE tm>$tm1 AND uid=$uid AND (source_id=$sid_d OR source_id=$sid_d1)");
		}
		
		
		if($uid>0) {
			log1("uid>0 SENLER");
			//~ if($senler->subscribers_add($uid, $grp_senler)) {
				//~ print "SENLER  uid=$uid added to B OK \n";
				//~ log1("SENLER uid=$uid added to B OK");
				//~ $num_senler_b++;
			//~ } else {
				//~ print "SENLER uid=$uid added to B ERROR \n";
				//~ log1("SENLER uid=$uid added to B ERROR");
				//~ $db->email($emails=array("vlav@mail.ru"), "BIZON: SENLER uid=$uid added to B ERROR", print_r($r, true), $from="noreply@yogahelpyou.com",$fromname="YOGAHELPYOU", $add_globals=false);
			//~ }
		}
		if(!$jc->add_to_group($grp_jc,$email)) {
			$db->email($emails=array("vlav@mail.ru"), "BIZON scan JC add_to_group webinar-visited error: uid=$uid email=$email ", "justclick_add_to_group  returned false", $from="noreply@yogahelpyou.com",$fromname="YOGAHELPYOU", $add_globals=true);
			$db->save_comm($uid,0,"ОШИБКА JC",$source_id,$vote_vk_uid=0,$mode=0, $force=false);
			log1("add to JC error");
		}
		log1("email=$email add to JC webinar-visited OK");

		$sp_var_value='-';
		if($source_id==$sid_b) {
			$sp_book_id=$sp_book_id_b;
			$sp_var_value='b';
			if($fl_b_notif)
				$db->notify($uid,"🔊 Был на вебинаре - зачет (B)");
		} elseif($source_id==$sid_c) {
			$sp_book_id=$sp_book_id_c;
			$sp_var_value='b';
			if($fl_c_notif)
				$db->notify($uid,"🔊 Был на вебинаре - зачет (C)");
		} elseif($source_id==$sid_f) {
			$sp_book_id=$sp_book_id_f;
			$sp_var_value='b';
			if($fl_f_notif)
				$db->notify($uid,"🔊 Был на вебинаре повторно");
		} elseif($source_id==$sid_d) {
			$sp_book_id=$sp_book_id_d;
			$sp_var_value='c';
			if($fl_d_notif)
				$db->notify($uid,"🔈 Был на вебинаре МАЛО ВРЕМЕНИ");
		} elseif($source_id==$sid_d1) {
			$sp_book_id=$sp_book_id_d1;
			$sp_var_value='d';
		} else {
			$sp_book_id=false;
		}
		if($sp->add($sp_book_id,$email,$phone,$exist_name,$uid,$db->uid_md5($uid)))
			log1("email=$email add to sendpulse $sp_book_id OK");
		else
			log1("email=$email add to sendpulse ERROR\n".print_r($sp->err,true));
		//~ print "123 $sp_book_main $email $sp_var $sp_var_value\n";
//~ $sp->update_vars((int)1339502,'vlav@mail.ru',['webinar'=>'x123']);
//~ print "555HERE \n";
		//~ if($sp_book_main && $email)
			//~ $sp->update_vars($sp_book_main,$email,[$sp_var=>$sp_var_value]);
	}

	$msg="num_senler_b=$num_senler_b num_senler_c=$num_senler_c num_jc_b=$num_jc_b num_d=$num_d num_crm_added=$num_crm_inserted num_crm_edited=$num_crm_edited";
	log1("Finished. $msg");

	$chk=$num_senler_b+$num_senler_c+$num_jc_b+$num_d+$webhook;
	if($chk>0) {
		if($db->dlookup("id","bizon","webinar_id='".$db->escape($wid)."'"))
			$db->query("UPDATE bizon SET attempt=0 WHERE webinar_id='".$db->escape($wid)."'");
		else
			$db->query("INSERT INTO bizon SET tm='".time()."',activity='$chk',webinar_id='".$db->escape($wid)."'");
		print "Finished. $wid marked as proceed \n";
		$db->yoga_email("BIZON $wid procced success","$msg\n <pre>". print_r($result, true) ."</pre>");
		after_finish($scdl_web_id,$wid);

	} else {
		$attempt=intval($db->dlookup("attempt","bizon","webinar_id='".$db->escape($wid)."'"));
		if(!$attempt) {
			$db->query("INSERT INTO bizon SET tm='".time()."', attempt='1',webinar_id='".$db->escape($wid)."'");
			after_finish($scdl_web_id,$wid);
		} else {
			$attempt++;
			$db->query("UPDATE bizon SET attempt='$attempt' WHERE webinar_id='".$db->escape($wid)."'");
			print "Finished. chk=$chk SEEMS SOMETHING WRONG \n";
			$db->yoga_email("BIZON $wid procced ERROR (chk will repeat according crontab)","$msg\n <pre>". print_r($result, true) ."</pre>", $from="noreply@yogahelpyou.com");
		}
	}
}
function after_finish($scdl_web_id,$wid) {
	global $db,$sid_d1,$sp,$sp_book_id_d1,$vkts,$land_num,$ctrl_id,$fl_d1_notif;

	preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$wid,$match);
	list($y,$m,$d)=explode("-",$match[0]);
	preg_match("/[0-9]{2,2}:[0-9]{2,2}:[0-9]{2,2}/",$wid,$match);
	list($h,$i,$s)=explode(":",$match[0]);
	$tm_web_start=mktime($h,$i,$s,$m,$d,$y);
	//$db->yoga_email("bizon_scan test wid=$wid tm=$tm_web_start","scdl_web_id=$scdl_web_id");

	if(!intval($tm_web_start) ) {
		$db->yoga_email("bizon scan after_finish ERROR $wid","scdl_web_id=$scdl_web_id wid=$wid tm_web_start=$tm_web_start\n".print_r($match,true));
		return false;
	}

	$res=$db->query("SELECT * FROM cards WHERE cards.del=0 AND scdl_web_id='$scdl_web_id' AND tm_schedule='$tm_web_start' AND tm_schedule>0",0);
	while($r=$db->fetch_assoc($res)) {
		$uid=$r['uid'];
		$email=$r['email'];
		$dt=date("d.m.Y H:i",$r['tm_schedule']);
		$db->query("UPDATE cards SET tm_schedule='0',scdl_opt='0',scdl_fl=0 WHERE uid='$uid'");
		$source_id=$sid_d1;
		$db->save_comm($uid,0,"НЕ БЫЛ НА ВЕБИНАРЕ, НО ЗАПИСЫВАЛСЯ",$source_id);

		if($fl_d1_notif)
			$db->notify($uid,"🔈 НЕ был на вебинаре, но был записан");

		if($land_num>0) {
			$res_vkts=$db->query("SELECT * FROM vkt_send_1 WHERE sid='$source_id' AND (land_num='$land_num' OR land_num=0)",0);
			while($r_vkts=$db->fetch_assoc($res_vkts)) {
				$vkts->vkt_send_task_add($ctrl_id, $tm_event=intval(time()+$r_vkts['tm_shift']), $vkt_send_id=$r_vkts['id'],$vkt_send_type=3,$uid);
			}
		}

		print "d - $uid $dt \n";
		log1("after_finish");
		if($sp_book_id_d1) {
			if($sp->add($sp_book_id_d1,$email,$r['mob_search'],$r['name'],$uid,$db->uid_md5($uid)))
				log1("email=$email add to sendpulse $sp_book_id_d1 OK");
			else
				log1("email=$email add to sendpulse ERROR\n".print_r($sp->err,true));
		}
	}
}

// В 01:00 1 раз в день сканируем кто НЕ был ВЧЕРА на вебинаре
//~ $db=new db("yogacenter");
//~ $s=new senler_api();
//~ if(intval(date("H"))==01) { //ONCE PER DAY
	//~ $tm1=$db->dt1(time()-(24*60*60));
	//~ $tm2=$db->dt2(time()-(24*60*60));
	//~ $res=$db->query("SELECT * FROM cards WHERE tm_schedule='$tm1'");
	//~ while($r=$db->fetch_assoc($res)) {
		//~ $uid=intval($r['uid']);
		//~ //print "{$r['uid']} ".date("d/m/Y",$r['tm_schedule'])."<br>";
		//~ if($db->dlookup("id","msgs","uid='$uid' AND source_id='12' AND tm>='$tm1' AND tm<='$tm2'")) {
			//~ //print "{$r['name']} {$r['surname']} <br>";
			//~ continue;
		//~ }
		//~ if(!$s->subscribers_del($uid, $s->subscriptions['webinar_repeat']))
			//~ print "Error DEL senler subscription - webinar_repeat\n";
		//~ usleep(100000);
		//~ if(!$s->subscribers_add($uid, $s->subscriptions['webinar_repeat']))
			//~ print "Error ADD senler subscription - webinar_repeat\n";
		//~ if(intval($r['scdl_opt'])==20)
			//~ $tm_seminar="20:00"; else $tm_seminar="10:00";
		//~ $dt_seminar=date("d.m.Y", $tm1)." $tm_seminar";
		//~ $db->save_comm($uid,$user_id=0,$comm="НЕ БЫЛ НА СЕМИНАРЕ $dt_seminar и будет приглашен повторно",$source_id=14,$vote_vk_uid=0,$mode=0, $force=false);
	//~ //	$db->query("UPDATE cards SET fl_newmsg=1,tm_lastmsg=".time().",source_id='14',tm_schedule='0',scdl_opt='0',scdl_fl=0 WHERE uid='$uid'",0);
		//~ if(!empty($r['mob'])) 
			//~ $to_new="fl_newmsg=1, tm_lastmsg=".time().","; else $to_new="";
		//~ $db->query("UPDATE cards SET $to_new source_id='14',tm_schedule='0',scdl_opt='0',scdl_fl=0 WHERE uid='$uid'",0);
		//~ print "uid=$uid Did not visit seminar $dt_seminar.  SCDL cleared. ADDED TO SENLER webinar_repeat <br>\n";
	//~ }
//~ }
//~ //

function log1($msg) {
	global $db,$uid,$email;
	if(!isset($uid))
		$uid=0;
	if(!isset($email))
		$email=0;
	$tm=time();
	$dt=date("d.m.Y");
	$db->query("INSERT INTO bizon_log SET uid='$uid',email='".$db->escape($email)."',tm='$tm',dt='$dt',msg='".$db->escape($msg)."'");
}
function get_new_webinar($room_regex) {
	global $db;
//	return "27379:go*2020-03-11T20:00:00";

	//$db=new db(database);
	$res=getlist();
	//print_r($res);
	foreach($res['list'] AS $w) {
		if(!preg_match($room_regex,$w['webinarId'])) {
			print "{$w['webinarId']} - is not corresponds ".$room_regex." \n";
			continue;
		}
		//print "{$w['webinarId']} <br>";
		if($db->dlookup("attempt","bizon","activity=0 AND webinar_id='{$w['webinarId']}' AND attempt<10")) {
			return $w['webinarId'];
		}
		if(!$db->dlookup("id","bizon","webinar_id='{$w['webinarId']}'")) {
			return $w['webinarId'];
		}
	}
	return false;
}

function authBizon() {
    $user = user;
    $pass = pass;
    
    $ua = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13';
    $cookie = fopen("cookie.txt","w+");
    $ch = curl_init("https://online.bizon365.ru/api/v1/auth/login");
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch,CURLOPT_POST,1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query(array(
        'username' => $user,
	    'password' => $pass)));
    curl_setopt($ch,CURLOPT_USERAGENT,$ua);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_COOKIEJAR,$_SERVER['DOCUMENT_ROOT']."/bizon365/cookie.txt");
    curl_setopt($ch,CURLOPT_HEADER,1);
    $res = curl_exec($ch);
 
    curl_close($ch);
    fclose($cookie);
}
function getlist() {
	global $token;
    $url = "https://online.bizon365.ru/api/v1/webinars/reports/getlist?skip=0&limit=20";
    $ch = curl_init($url);
    $ua = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13';
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Token: '.$token]);
	//curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Token: BZ9IVVrTHHZl5IN4rpBr-bcUVEr6rBZf9UEVSaBr-Xq8VNSTH']);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
    curl_setopt($ch,CURLOPT_USERAGENT,$ua);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_HEADER,1);
   // curl_setopt($ch,CURLOPT_COOKIEFILE,$_SERVER['DOCUMENT_ROOT']."/bizon365/cookie.txt");
    $res = curl_exec($ch);
    curl_close($ch);
        
    $pos1 = strpos($res, '{');
    $pos2 = strrpos($res, '}');
    $resjson = substr($res, $pos1, $pos2);
        
    $json = json_decode($resjson, true);
    return $json;
}
function get($wid){
	global $token;
    $url = "https://online.bizon365.ru/api/v1/webinars/reports/get?webinarId=$wid";
   // print $url;
    $ch = curl_init($url);
    $ua = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13';
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Token: '.$token]);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
    curl_setopt($ch,CURLOPT_USERAGENT,$ua);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_HEADER,1);
//    curl_setopt($ch,CURLOPT_COOKIEFILE,$_SERVER['DOCUMENT_ROOT']."/bizon365/cookie.txt");
    $res = curl_exec($ch);
    curl_close($ch);
        
    $pos1 = strpos($res, '{');
    $pos2 = strrpos($res, '}');
    $resjson = substr($res, $pos1, $pos2);
        
    $json = json_decode($resjson, true);
    return $json;
}
?>
