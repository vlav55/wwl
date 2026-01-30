<?
include_once('/var/www/vlav/data/www/wwl/inc/db.class.php');
class funnel extends db {
	var $force_if_not_wa_allowed=false;
	var $attach=false;
	var $source_id=2;
//	var $msg_ask_name,$msg_ask_hello;
	var $web_id=0;
	var $msg_1_1="ваша ссылка на каталог асан йоги https://yogahelpyou.com/trip/catalog_asan.php?uid=#uid \nЕсли ссылка не кликабельная - добавьте этот номер в контакты или скопируйте ее в браузер";
	var $msg_1_2="По поводу семинара - на семинаре будет супер важная информация о йоге и о здоровье, которую вы не получите больше нигде.

*Вебинар проходит завтра:*
9:00
12:00
14:40
17:20
20:00

по МОСКОВСКОМУ времени.
длит 1.5 часа, это бесплатно

На какое время вас записать?";

	var $msg_2="Ок, вы записаны на завтра на {tm} ссылку я пришлю сюда перед началом.
Сразу *добавьте мой номер в контакты*, чтобы ссылка у вас открылась.";

	function __construct($db='yogacenter') {
		$this->connect($db);
	}
	function set_funnel($uid,$funnel_name,$lang='ru') {
		$this->query("UPDATE cards SET lang='$lang' WHERE uid='$uid'");
		$tm=$this->dlast("tm","funnels","uid='$uid' AND funnel='$funnel_name'");
		if( (time()-$tm) >(60)) {
			$this->query("INSERT INTO funnels SET funnel='".$this->escape($funnel_name)."',tm='".time()."',uid='$uid'");
			return $this->insert_id();
		}
		return false;
	}
	function get_last_funnel($uid) {
		return $this->dlast("funnel","funnels","uid='$uid'");
	}
	function get_lang($uid) {
		$lang=$this->dlookup("lang","cards","uid='$uid'");
		return !empty($lang)?$lang:'ru';
	}
	function send($uid,$msg,$num=0) {
		//function do_send_wa($uid,$msg,$source_id=0,$num=0, $attach=false, $force_if_not_wa_allowed=false)
		$acc_id=$this->dlast("acc_id","msgs","uid='$uid' AND outg=1");
		if($acc_id==101)
			$this->vktrade_send_wa_only=true;
		elseif($acc_id==103)
			$this->vktrade_send_tg_only=true;
		elseif($acc_id==2)
			$this->vktrade_send_vk_only=true;
		if(!$res=$this->vktrade_send_wa($uid,$msg,$this->source_id,$num,$this->attach,$this->force_if_not_wa_allowed))
			$this->notify($uid,"❗ошибка отправки: $msg");
		return $res;
	}
	function get_last_num($uid) {
		$tm_last_incoming=$this->fetch_assoc($this->query("SELECT tm FROM msgs_hook WHERE uid='$uid'"))['tm'];
		if( (time()-intval($tm_last_incoming)) <10) {
			$this->save_comm($uid,0,"get_last_id=0 (1)",106);
			return 0;
		}
		$this->query("DELETE FROM msgs_hook WHERE uid='$uid'");
		$this->query("INSERT INTO msgs_hook SET tm='".time()."',uid='$uid'");

		$tm1=time()-(48*60*60);
		$q="SELECT * FROM msgs
			WHERE uid=$uid AND outg!=0 AND source_id=2 AND tm>$tm1
			ORDER BY tm DESC LIMIT 1";
		$res=$this->query($q,0);
		$r=$this->fetch_assoc($res);
		if(!$r)  {
			$this->save_comm($uid,0,"get_last_id=0 (3 false) ",106);
			return 0;
		}

		$tm_last_outg=$this->fetch_assoc($this->query("SELECT tm FROM msgs WHERE uid='$uid' AND outg=1 ORDER BY tm DESC LIMIT 1"))['tm'];
		if(!$tm_last_outg)
			$tm_last_outg=0;
		
		if($tm_last_outg>($r['tm']+45) ) {
			$this->save_comm($uid,0,"get_last_id=0 (2 $tm_last_outg {$r['tm']})",106);
			return 0;
		}

		$this->save_comm($uid,0,"get_last_id=".intval($r['new']),106);
		return intval($r['new']);
	}
	function mess_ask_hello($uid) {
		sleep(5);
		$uid_md5=$this->dlookup("uid_md5","cards","uid='$uid'");
		if($this->get_last_funnel($uid)=='sleep_testdrive') {
			$msg="Здравствуйте! Ссылка на практику глубокого расслабления для сна https://yogahelpyou.com/lms/sleep/?$uid_md5
если ссылка не кликабельная, добавьте этот номер в контакты. Также ссылку мы отправили на указанный при регистрации емэйл.";
			$sid_promo_sent=128;
			$this->course_access_set($uid,110,$this->dt1(time()),$this->dt2(time()+(6*24*60*60)));
			$this->save_comm($uid,0,'Отправлен промокод',$sid_promo_sent);
			$this->force_if_not_wa_allowed=true;
			$this->send($uid,$msg,1);

			sleep(5);
			$msg2="Выберите время, когда вам удобно посмотреть вебинар \"Как восстановить полноценный сон и перестать тревожиться с помощью медитаций раджа-йоги\".
Вебинар важен, потому что наши методики позволят вам решить проблемы со сном, вы будете высыпаться, выглядеть лучше и здоровее. 

*Вебинар проходит завтра по расписанию:*
9:00
12:00
14:40
17:20
20:00

по МОСКОВСКОМУ времени.
длит 1 час, это бесплатно

На какое время вас записать?";
			$this->send($uid,$msg2,2);
			return true;
		}
		return false;
	}
	function mess_ask_name($uid) {
		$uid_md5=$this->dlookup("uid_md5","cards","uid='$uid'");
		if($this->get_last_funnel($uid)=='sleep_testdrive') {
			$this->mess_ask_hello($uid);
			return true;
		}
		sleep(5);
		return false;
	}
	function mess_if_linked($uid,$user_id,$user_id_cards) {
	}
	function add_action_after_reg($uid,$web_id,$msg) {
		if($web_id==2 || $web_id==3) {
			$lang=$this->get_lang($uid);
			if($lang=='ru')
				$this->save_comm($uid,0,"ФЛАГ ДЛЯ ОТПРАВКИ ПРОБНОГО ДОСТУПА К МЕДИТАЦИИ RU",146,'funnel_bot');
			elseif($lang=='en')
				$this->save_comm($uid,0,"ФЛАГ ДЛЯ ОТПРАВКИ ПРОБНОГО ДОСТУПА К МЕДИТАЦИИ EN",147,'funnel_bot');
		}
	} 
	function run_bot($uid,$msg) {
		//return false;
//~ $this->telegram_bot="vktrade";
//~ $this->notify_user(1,"HERE=$last_num");
//file_put_contents("test.txt","$uid $last_num");
		$last_num=$this->get_last_num($uid);
		if($last_num==1) {
			$email=$this->dlookup("email","cards","uid='$uid'");
			$name=$this->dlookup("name","cards","uid='$uid'");
			if(empty($name) || preg_match("/[0-9]+/",$name) ) {
				$arr=explode(" ",$msg);
				$name="";
				foreach($arr AS $word) {
					if($name=$this->validate_name($word)) {
						$this->query("UPDATE cards SET name='".$this->escape($name)."' WHERE uid='$uid'");
						break;
					}
				}
			}
			$out=$this->msg_1_1;
			$this->send($uid,$out,$num=2);
			sleep(0);
			$out=$this->msg_1_2;
			$this->send($uid,$out,$num=2);
			return true;
		}
		if($last_num==2) {
			if(!preg_match("/[0-9]{1,2}[\s\.\:\,\-]{1,1}[0-9]{1,2}/s",$msg,$m))
				preg_match("/[0-9]{1,2}/s",$msg,$m);
			if(isset($m[0])) {
				$times=[9=>9*60*60,
						900=>9*60*60,
						12=>12*60*60,
						1200=>12*60*60,
						20=>20*60*60,
						2000=>20*60*60,
						1440=>14*60*60+40*60,
						1720=>17*60*60+20*60,
						1420=>14*60*60+40*60,
						1740=>17*60*60+20*60,
						14=>14*60*60+40*60,
						1400=>14*60*60+40*60,
						17=>17*60*60+20*60,
						1700=>17*60*60+20*60];
				$key=preg_replace("/[\s\.\:\,\-]+/i","",$m[0]);
				if(in_array($key,array_keys($times)) ) {
					//$this->notify($uid,"TEST vote=$last_num uid=$uid tm=".$times[$key]." msg=$msg");
						$tm=$this->dt1(time()+(24*60*60))+$times[$key];
						if(intval(date("H")) <=3)
							$tm=$this->dt1(time())+$times[$key];
						else
							$tm=$this->dt1(time()+(24*60*60))+$times[$key];
						$tm_val=date("H-i",$tm);
						$out=str_replace("{tm}",$tm_val,$this->msg_2);
						sleep(5);
						$this->send($uid,$out,$num=3);
						$this->query("UPDATE cards SET tm_schedule='$tm',scdl_fl=0,scdl_web_id='$this->web_id' WHERE uid='$uid'");


						$dt=date('d.m.Y H:i',$tm);
						$this->save_comm_custom_fl=$this->web_id;
						$this->save_comm($uid,0,"УСТАНОВКА В РАСПИСАНИЕ funnel_bot НА $dt",100,'funnel_bot');
						$this->add_action_after_reg($uid,$this->web_id,$msg);

						//~ if($uid>0) {
							//~ include "/var/www/vlav/data/www/wwl/inc/senler_api.class.php";
							//~ $s=new senler_api;
							//~ $s->subscribers_add($uid, 1601605);
						//~ }

						return true;
				}
			}
		}

		if($last_num==3) {
			if(preg_match("/спасибо|спс|благодарю|отлично|хорошо|ok|ок|да|договорились/ius",$msg)) {
				$out="пожалуйста. на связи";
				sleep(5);
				$this->send($uid,$out,$num=4);
				return true;
			}
		}

		if($last_num!=2) {
			//~ if(preg_match("/спасибо|благодарю|договорились/ius",$msg)) {
				//~ $out="🙏";
				//~ sleep(5);
				//~ $this->send($uid,$out,$num=0);
				//~ return true;
			//~ }
		}
		return false;
	}
	function goto_funnel($sid_entrance=149, $sid_prev=110, $days_prev_reg=10, $days_all_limit=14, $days_last_msg_limit=2) {
		//goto $sid_entrance
		//не в расписании ни на какой вебинар
		//зарегились на $sid_prev более, чем $days_prev_reg назад
		//и ничего не получали и не отправляли в msgs в течение days_last_msg_limit
		//и $sid_entrance или $sid_prev - появился не ранее, чем в последние $days_all_limit
		
		$tm_prev_reg=$this->dt1(time()-($days_prev_reg*24*60*60));
		$tm_last_msg_limit=$this->dt1(time()-($days_last_msg_limit*24*60*60));
		$tm_all_limit=$this->dt1(time()-($days_all_limit*24*60*60));
		$dt_prev_reg=date('d.m.Y',$tm_prev_reg);
		$dt_last_msg_limit=date('d.m.Y',$tm_last_msg_limit);
		$dt_all_limit=date('d.m.Y',$tm_all_limit);
		print "goto_funnel:
			entrance=$sid_entrance
			sid_prev=$sid_prev
			days_all_limit=$days_all_limit
			days_last_msg_limit=$days_last_msg_limit
			dt_prev_reg=$dt_prev_reg
			dt_last_msg_limit=$dt_last_msg_limit
			dt_all_limit=$dt_all_limit
			\n";
		$res=$this->query("SELECT msgs.id AS id, msgs.tm AS tm,msgs.uid AS uid FROM cards JOIN msgs ON msgs.uid=cards.uid
			WHERE cards.del=0 AND tm_schedule=0 AND msgs.source_id='$sid_prev' AND msgs.tm<$tm_prev_reg
			GROUP BY msgs.uid ORDER BY msgs.tm DESC",0);
		$n=0;
		while($r=$this->fetch_assoc($res)) {
			$uid=$r['uid'];
			$dt=date('d.m.Y',$r['tm']);
			if($this->dlookup("tm","msgs","uid='$uid' AND source_id='$sid_entrance' AND  msgs.tm<$tm_prev_reg",0)) {
				//Passed - there was entrance in last tm_prev_reg days
				continue;
			}
			if( ($tm_last_msg=$this->dlast("tm","msgs","uid='$uid'")) >= $tm_last_msg_limit) {
				//print "1 $uid PASSED because there was ANY msgs in last $days_last_msg_limit days<br>";
				continue;
			}
			$dt_last_msg=date('d.m.Y',$tm_last_msg);
			if(!$tm_last_msg) //or if there was no msgs
				continue;
			if($this->dlast("tm","msgs","uid='$uid' AND (source_id='$sid_prev' OR source_id='$sid_entrance') AND tm<$tm_all_limit",0)) {
				//print "2 $uid PASSED because it occured > $days_all_limit days<br>";
				continue;
			}
			$n++;
	//		$this->save_comm($uid,0,NULL,$sid_entrance);
			print "$n SET sid_entrance=$sid_entrance for UID=$uid sid_prev checked at $dt last_msg checked at $dt_last_msg<br>\n";

			if($n==100) {
				print "STOPPED because n=100";
				$db->yoga_email("WARNING (spam protection) funnel.class.php goto_funnel n=100 limit reached","CHECK!!");
				break;
			}
		}
		/////////
	}
	
}?>
