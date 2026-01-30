<?
include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
class vkt_send extends vkt {
	var $vkt_send_skip_wa=true; //ignore_wa
	var $vkt_send_wa_only=false; //send wa only, not tg
	var $vkt_send_tg_only=false; //send tg only, not wa
	var $vkt_send_vk_only=false; //send tg only, not wa
	var $vkt_send_tg_video_note=false;
	var $vkt_send_tg_photo=false;
	var $vkt_send_tg_video=false;
	var $vkt_send_tg_audio=false;
	var $vkt_send_vk_photo=false;
	var $vkt_send_vk_video=false;
	var $vkt_send_res=[];
	var $vkt_send_test=false; //false or filename where to add uids instead of sending
	var $vkt_send_at_uids_ban=[];
	var $vkt_send_tg_id=false;
	var $vkt_send_vk_id=false;
	var $fl_vk=0;
	var $fl_tg=0;
	var $fl_wa=0;
	var $fl_email=0;
	var $pact_secret=false;
	var $pact_company_id=false;

	function get_tmm() {
		// Get the current system time in microseconds as a float
		$timeInMicrosecondsFloat = microtime(true);

		// Split the float into seconds and microseconds
		list($seconds, $microseconds) = explode(' ', $timeInMicrosecondsFloat);

		// Combine seconds and microseconds into a single integer value
		return $timeInMicrosecondsInt = (int)($seconds * 1000000 + $microseconds);
	}
	function log_tg($tg_id) {
		$tmm=$this->get_tmm();
		$this->query("INSERT INTO vkt_send_log_1 SET tmm='$tmm', tg_id='".intval($tg_id)."'");
	}
	function log_vk($vk_id) {
		$tmm=$this->get_tmm();
		$this->query("INSERT INTO vkt_send_log_1 SET tmm='$tmm', vk_id='".intval($vk_id)."'");
	}
	function log_email($email) {
		$tmm=$this->get_tmm();
		$this->query("INSERT INTO vkt_send_log_1 SET tmm='$tmm', email='".$this->escape($email)."'");
	}

	function vkt_send_get_tg_id($uid) {
		if($this->vkt_send_tg_id)
			return $this->vkt_send_tg_id;
		else
			return $this->dlookup("telegram_id","cards","uid='$uid'");
	}
	function vkt_send_get_vk_id($uid) {
		if($this->vkt_send_vk_id)
			return $this->vkt_send_vk_id;
		$vk_id=$this->dlookup("vk_id","cards","uid='$uid'");
		if(!$vk_id && $uid>0)
			$this->query("UPDATE cards SET vk_id='$uid' WHERE uid='$uid'");
		return $vk_id;
	}
	var $vkt_send_msg_user_id=0;
	var $vkt_send_msg_order_id=0;
	function vkt_send_prepare_msg($uid,$msg) {
		return $this->prepare_msg($uid,$msg,$this->vkt_send_msg_order_id); 
	}
	function vkt_send_msg($uid,$msg,$source_id=3,$num=0,$attach=false,$force_if_not_wa_allowed=false) {

		if(in_array($uid,$this->vkt_send_at_uids_ban)) {
			print "$uid BANNED in vkt_send_at_uids_ban <br> \n";
			return false;
		}

		$msg1=trim($this->vkt_send_prepare_msg($uid,$msg));
		if( empty($msg1) && !empty(trim($msg)) )
			return "Ok send cmd only"; //it is cmd
		$msg=$msg1;

		if($this->vkt_send_test) {
			$file=$this->vkt_send_test;
			$this->vkt_send_res=['wa'=>3,'vk'=>3,'tg'=>3,'email'=>3];
			print "uid=$uid vkt_send_test=$file - not realy sent \n";
			return file_put_contents($file, date('d.m.Y H:i:s')." ".$uid . PHP_EOL, FILE_APPEND);
		}

		if($this->fl_wa)
			$this->vkt_send_wa_only=true;
		
		$this->vkt_send_res=['wa'=>0,'vk'=>0,'tg'=>0,'email'=>0];
		//TG
		if(!$this->vkt_send_wa_only && !$this->vkt_send_vk_only ) {
			if($this->fl_tg) {
				if($this->vkt_send_get_tg_id($uid)) {
					if($this->vkt_send_tg_photo) {
						if($this->vkt_send_tg($uid,$msg,$source_id,$num,$attach)) {
							$this->vkt_send_res['tg']=1;
						} else
							$this->vkt_send_res['tg']=2;
						$this->vkt_send_tg_photo=false;
					} elseif($this->vkt_send_tg_video) {
						if($this->vkt_send_tg($uid,$msg,$source_id,$num,$attach)) {
							$this->vkt_send_res['tg']=1;
						} else
							$this->vkt_send_res['tg']=2;
						$this->vkt_send_tg_video=false;
					} elseif($this->vkt_send_tg_video_note) {
						$this->vkt_send_tg($uid,NULL,3,0,false);
						$this->vkt_send_tg_video_note=false;
					} elseif($this->vkt_send_tg_audio) {
						$this->vkt_send_tg($uid,NULL,3,0,false);
						$this->vkt_send_tg_audio=false;
					} else {
						if($this->vkt_send_tg($uid,$msg,$source_id,$num,$attach)) {
							$this->vkt_send_res['tg']=1;
						} else
							$this->vkt_send_res['tg']=2;
					}
				}
			} else
				$this->vkt_send_res['tg']=5;
		}
		//VK
		if(!$this->vkt_send_tg_only && !$this->vkt_send_wa_only) {
			if($this->fl_vk) {
				if($this->vkt_send_get_vk_id($uid)) {
					if($this->vkt_send_vk_photo)
						$this->vkt_send_vk($uid,NULL,3,0,false);
					elseif($this->vkt_send_vk_video)
						$this->vkt_send_vk($uid,NULL,3,0,false);
					$this->vkt_send_vk_photo=false;
					$this->vkt_send_vk_video=false;
					if($this->vkt_send_vk($uid,$msg,$source_id,$num,$attach)) {
						$this->vkt_send_res['vk']=1;
					} else
						$this->vkt_send_res['vk']=2;
				}
			} else
				$this->vkt_send_res['vk']=5;
		}
		
		//WA
		//go to wa only if not success sending tg or vk
		if( ($this->vkt_send_res['vk']==1 || $this->vkt_send_res['tg']==1)
			&& $this->vkt_send_skip_wa
			&& !$this->fl_wa)
			return true;
		if(!$this->vkt_send_tg_only && !$this->vkt_send_vk_only && !$this->vkt_send_skip_wa) {
			if($this->fl_wa) {
				include_once "/var/www/vlav/data/www/wwl/inc/pact.class.php";
				if(!$this->dlookup("wa_allowed","cards","uid='$uid'")) {
					//return false;
				}
				$wa=new pact($this->pact_secret,$this->pact_company_id);
				if($attach)
					$wa->attach=$attach;
				$save_outg=true;
				if($attach || !empty($msg)) {
	//$this->notify_me("HERE_ $this->pact_secret , $this->pact_company_id");
					$res=$wa->send($this,$uid,$msg,$this->vkt_send_msg_user_id,$num,$source_id,$save_outg,$force_if_not_wa_allowed=1);
					if(!$res) {
						//$this->print_log($uid, print_r($wa->send_msg_error,true)." \n");
						$this->vkt_send_res['wa']=2;
					} else
						$this->vkt_send_res['wa']=1;
					return $res;
				} else {
					//$this->print_log($uid, "WA send error - msg is empty \n");
					return false;
				}
			}
			return false;
		}
		return false;
	}

	var $vkt_send_tg_bot=false;
	function vkt_send_tg($uid,$msg,$source_id=0,$num=0,$attach=false) {
		$tg_id=$this->vkt_send_get_tg_id($uid);
		if($tg_id) {
			include_once "/var/www/vlav/data/www/wwl/inc/tg_bot.class.php";
			$tg=new tg_bot($this->vkt_send_tg_bot);
			usleep(33334);
			$ok=false;
			if($this->vkt_send_tg_video_note) {
				$this->log_tg($tg_id);
				if($tg->send_video_note($tg_id,$this->vkt_send_tg_video_note)) {
					$outg=1;
					$fname=basename($this->vkt_send_tg_video_note);
					$this->query("INSERT INTO msgs SET
								uid='$uid',
								acc_id=103,
								tm=".time().",
								user_id=$this->vkt_send_msg_user_id,
								msg='ОТПРАВЛЕНО КРУГЛОЕ ВИДЕО: $fname',
								outg=$outg,
								vote='$num',
								new='".intval($num)."',
								source_id='$source_id'					
								",0);
					$ok=true;
					//return true;
				} else {
					//$this->save_comm($uid,0,"Error sending TG video_note",1002);
				}
			} elseif($this->vkt_send_tg_photo) {
				$this->log_tg($tg_id);
				if($tg->send_photo($tg_id,$this->vkt_send_tg_photo,mb_substr($msg,0,1012))) {
					$outg=1;
					$fname=basename($this->vkt_send_tg_photo);
					$this->query("INSERT INTO msgs SET
								uid='$uid',
								acc_id=103,
								tm=".time().",
								user_id=$this->vkt_send_msg_user_id,
								msg='ОТПРАВЛЕНО ФОТО: $fname',
								outg=$outg,
								vote='$num',
								new='".intval($num)."',
								source_id='$source_id'					
								",0);
					$ok=true;
					//print "HERE_"; exit;
					return true;
				} else {
					//$this->save_comm($uid,0,"Error sending TG photo",1003);
				}
			} elseif($this->vkt_send_tg_video) {
				//print "HERE_$this->vkt_send_tg_video";
				$this->log_tg($tg_id);
				if($tg->send_video($tg_id,$this->vkt_send_tg_video,mb_substr($msg,0,1024))) {
					$outg=1;
					$fname=basename($this->vkt_send_tg_video);
					$this->query("INSERT INTO msgs SET
								uid='$uid',
								acc_id=103,
								tm=".time().",
								user_id=$this->vkt_send_msg_user_id,
								msg='ОТПРАВЛЕНО ВИДЕО: $fname',
								outg=$outg,
								vote='$num',
								new='".intval($num)."',
								source_id='$source_id'					
								",0);
					$ok=true;
					return true;
				} else {
					//$this->save_comm($uid,0,"Error sending TG video",1004);
				}
			} elseif($this->vkt_send_tg_audio) { 
				//print "HERE_$this->vkt_send_tg_video";
				$this->log_tg($tg_id);
				if($tg->send_audio($tg_id,$this->vkt_send_tg_audio)) {
					$outg=1;
					$fname=basename($this->vkt_send_tg_audio);
					$this->query("INSERT INTO msgs SET
								uid='$uid',
								acc_id=103,
								tm=".time().",
								user_id=$this->vkt_send_msg_user_id,
								msg='ОТПРАВЛЕНО ВИДЕО: $fname',
								outg=$outg,
								vote='$num',
								new='".intval($num)."',
								source_id='$source_id'					
								",0);
					$ok=true;
					//return true;
				} else {
					//$this->save_comm($uid,0,"Error sending TG video",1004);
				}
			}
			if(!empty($msg)) {
				$this->log_tg($tg_id);
				if($tg->send_msg($tg_id,$msg)) {
					$outg=($source_id)?2:1;
					$outg=1;
					$this->query("INSERT INTO msgs SET
								uid='$uid',
								acc_id=103,
								tm=".time().",
								user_id=$this->vkt_send_msg_user_id,
								msg='".$this->escape($msg)."',
								outg=$outg,
								vote='$num',
								new='".intval($num)."',
								source_id='$source_id'					
								",0);
					$ok=true;
					//return true;
				} else {
					//$this->save_comm($uid,0,"Error sending TG message",1001);
				}
			}
		}
		return $ok;
	}

	function vkt_send_vk($uid,$msg,$source_id=3,$num=0,$attach=false) {
		$vk_uid=$this->vkt_send_get_vk_id($uid);
		if($vk_uid>0) {
			if($this->vkt_send_vk_video)
				$attach=$this->vkt_send_vk_video;
			if($this->vkt_send_vk_photo)
				$attach=$this->vkt_send_vk_photo;

			include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
			$vk=new vklist_api($this->vk_token);
			usleep(33334);
			$this->log_vk($vk_uid);
			if(!$res=$vk->vk_msg_send($vk_uid, $msg, $fake=false, $chat_id=false, $attach, $peer_id=false)) {
				$outg=1;
				$txt=($attach)?"VIDEO ".$attach."\n".$msg:$msg;
				$this->query("INSERT INTO msgs SET
							uid='$uid',
							acc_id=2,
							tm=".time().",
							user_id=$this->vkt_send_msg_user_id,
							msg='".$this->escape($txt)."',
							outg=$outg,
							vote='$num',
							new='".intval($num)."',
							source_id='$source_id'					
							",0);
				return true;
			} else {
				//$this->save_comm($uid,0,"Error sending VK message",1010);
			}
		}
		return false;
	}

	var $vkt_send_filer_log_out="";
	function vkt_send_filer_log($n) {
		$this->vkt_send_filer_log_out.=$n." ".microtime()."\n";
	}
	function vkt_send_filter($vkt_send_id) {
		global $arr_leads,$arr_clients,$arr_partners;
		if(!$r=$this->fetch_assoc($res=$this->query("SELECT * FROM vkt_send_1 WHERE id='$vkt_send_id'"))) {
			print "<p class='alert alert-danger' >Ошибка 3f. Обратитесь к разработчикам</p>";
			return false;
		}
$this->vkt_send_filer_log(__LINE__);
		//print $this->database;
		$name_send=$r['name_send'];
		$tm1=$r['tm1'];
		$tm2=$r['tm2'];
		$dt1=date("d.m.Y",$r['tm1']);
		$dt2=date("d.m.Y",$r['tm2']);
		$fl_clients_checked=$r['fl_clients']?'checked':'';
		$fl_partners_checked=$r['fl_partners']?'checked':'';
		$fl_leads_checked=$r['fl_leads']?'checked':'';
		$fl_razdel=intval($r['fl_razdel']);
		$fl_land=intval($r['fl_land']);
		$msg=$r['msg'];
		$land_num=$r['land_num'];
		$sid=$r['sid'];

		$arr_res=[];
		if($land_num>0 && !$sid) { //mode 2
			$res=$this->query("SELECT * FROM cards WHERE scdl_web_id='$land_num' AND del=0",0);
			while($r=$this->fetch_assoc($res)) {
				$arr_res[]=$r['uid'];
			}
			return $arr_res;
		} elseif($land_num>0 && $sid>0) { //mode 3
			return [];
		}
$this->vkt_send_filer_log(__LINE__);

		
		//mode 1
		$arr_cards=[];
		$res2=$this->query("SELECT * FROM cards WHERE del=0 AND cards.tm>='$tm1' AND cards.tm<='$tm2'",0);
		while($r2=$this->fetch_assoc($res2)) {
			$arr_cards[$r2['uid']]=[ 'vk'=>$r2['vk_id'],
								'tg'=>$r2['telegram_id'],
								'wa'=>$r2['mob_search'],
								'email'=>$r2['email'],
								'fl'=>$r2['fl'],
								];
		}
		$arr_land=[];
		$res2=$this->query("SELECT uid,source_id FROM msgs WHERE source_id>1000 AND source_id<2000",0);
		while($r2=$this->fetch_assoc($res2)) {
			if(!in_array(['uid' => $r2['uid'], 'land_num' => intval($r2['source_id']-1000)], $arr_land))
				$arr_land[]=['uid'=>$r2['uid'],'land_num'=>intval($r2['source_id']-1000)];
		}

		$arr_tags=[];
		$res2=$this->query("SELECT uid,tag_id FROM tags_op WHERE 1",0);
		while($r2=$this->fetch_assoc($res2)) {
			$arr_tags[]=['uid'=>$r2['uid'],'tag_id'=>$r2['tag_id']];
		}

		$arr_not_send=[];
		$q="SELECT cards.uid AS uid FROM cards
			JOIN tags_op ON cards.uid=tags_op.uid
			JOIN tags ON tags.id=tags_op.tag_id
			WHERE cards.del=0 AND tags.fl_not_send=1
			GROUP BY uid";
		$res_not_send=$this->query($q);
		while($r_not_send=$this->fetch_row($res_not_send)) {
			$arr_not_send[]=$r_not_send[0];
		}
	//	print_r($arr_not_send); exit;
		
		$arr_res=[];
		$where_razdel=($r['fl_razdel']>0)?"1 AND cards.razdel={$r['fl_razdel']} AND":"";

		//CLIENTS
		$q="SELECT vk_uid FROM avangard
			JOIN cards ON cards.uid=vk_uid
			JOIN msgs ON msgs.uid=vk_uid
			WHERE $where_razdel avangard.res=1
			AND avangard.tm>='$tm1' AND avangard.tm<='$tm2'
			AND (telegram_id>0 OR vk_id>0 OR cards.email!='')
			AND cards.del=0
			GROUP BY vk_uid";
		$res1=$this->query($q,0);
		//$this->notify_me($q);
		$arr_clients=[];
		while($r1=$this->fetch_row($res1)) {
			$uid=$r1[0];
			//~ if($this->dlast("id","vkt_send_log","uid='$uid' AND ( (res_tg=2 AND res_vk!=1) OR (res_tg!=1 AND res_vk=2) )") )
				//~ continue;
			$arr_clients[]=$uid;
		}
		$arr_clients=array_diff($arr_clients,$arr_not_send);
$this->vkt_send_filer_log(__LINE__);

		//PARTNERS
		$res1=$this->query("SELECT cards.uid FROM `cards`
				JOIN msgs ON cards.uid=msgs.uid
				WHERE  $where_razdel msgs.source_id=25 AND	msgs.tm>='$tm1' AND msgs.tm<='$tm2'
				AND (telegram_id>0 OR vk_id>0 OR cards.email!='') AND cards.del=0
				GROUP BY cards.uid
				",0);
		$arr_partners=[];
		while($r1=$this->fetch_row($res1)) {
			$uid=$r1[0];
			//~ if($this->dlast("id","vkt_send_log","uid='$uid' AND ( (res_tg=2 AND res_vk!=1) OR (res_tg!=1 AND res_vk=2) )") )
				//~ continue;
			$arr_partners[]=$uid;
		}
		$arr_partners=array_diff($arr_partners,$arr_not_send);
$this->vkt_send_filer_log(__LINE__);

		//OTHER
		//~ $res1=$this->query("SELECT cards.uid FROM `cards`
				//~ JOIN msgs ON cards.uid=msgs.uid
				//~ WHERE  $where_razdel msgs.source_id=12 AND	msgs.tm>='$tm1' AND msgs.tm<='$tm2'
				//~ AND (telegram_id>0 OR vk_id>0 OR cards.email!='') AND cards.del=0
				//~ GROUP BY cards.uid
				//~ ",0);
		$res1=$this->query("SELECT cards.uid FROM `cards`
				WHERE  $where_razdel cards.tm>='$tm1' AND cards.tm<='$tm2'
				AND (telegram_id>0 OR vk_id>0 OR cards.email!='') AND cards.del=0
				",0);
		//$res1=$this->query("SELECT uid FROM cards WHERE del=0 AND (telegram_id>0 OR vk_id>0 OR cards.email!='') ");
		$arr_leads=[];
		while($r1=$this->fetch_row($res1)) {
			$uid=$r1[0];
			//~ if($this->dlast("id","vkt_send_log","uid='$uid' AND ( (res_tg=2 AND res_vk!=1) OR (res_tg!=1 AND res_vk=2) )") )
				//~ continue;
			$arr_leads[]=$uid;
		}
	//	$this->notify_me(sizeof($arr_leads));

$this->vkt_send_filer_log(__LINE__);
		$arr_leads=array_diff($arr_leads,$arr_not_send);
		$arr_leads=array_diff($arr_leads,$arr_clients);
		$arr_leads=array_diff($arr_leads,$arr_partners);
$this->vkt_send_filer_log(__LINE__);

		if($r['fl_clients'])
			$arr_res=array_merge($arr_res, $arr_clients);
		if($r['fl_partners'])
			$arr_res=array_merge($arr_res, $arr_partners);
		if($r['fl_leads'])
			$arr_res=array_merge($arr_res, $arr_leads);
$this->vkt_send_filer_log(__LINE__);

		if($r['fl_land']) {
			foreach($arr_res AS $key=>$val) {
			//~ $this->notify_me("HERE_".$val." ".$r['fl_land']);
			//~ break;
				if(!in_array(['uid' => $val, 'land_num' => $r['fl_land']], $arr_land))
					unset($arr_res[$key]);
				//~ if($arr_land[$val]!=$r['fl_land'])
					//~ unset($arr_res[$key]);
				//~ if(!$this->dlookup("id","msgs","uid='$val' AND source_id=".intval($r['fl_land']+1000) ) )
					//~ unset($arr_res[$key]);
			}
		}
		if($r['fl_tag']) {
			foreach($arr_res AS $key=>$val) {
				if(!in_array(['uid'=>$val,'tag_id'=>$r['fl_tag']],$arr_tags))
					unset($arr_res[$key]);
				//~ if($arr_tags[$val]!=$r['fl_tag'])
					//~ unset($arr_res[$key]);
			}
		}
//$this->notify_me("HERE_".print_r($arr_cards[-19093],true));
		if($r['fl_chk']) {
			foreach($arr_res AS $key=>$val) {
				if($arr_cards[$val]['fl']!=1)
					unset($arr_res[$key]);
				//~ if(!$this->dlookup("id","cards","uid='$val' AND fl=1 AND del=0") ) 
					//~ unset($arr_res[$key]);
			}
		}

$this->vkt_send_filer_log(sizeof($arr_res));
$this->vkt_send_filer_log(__LINE__);
		
		$arr_res_ok=[];
		foreach($arr_res AS $uid) {
			if(!in_array($uid,$arr_res_ok))
				$arr_res_ok[]=$uid;
		}
$this->vkt_send_filer_log(__LINE__);
//file_put_contents("vkt_send_filer_log.txt",$this->vkt_send_filer_log_out);
		return $arr_res_ok;
	}

	function vkt_send_filter_cnt_vk($arr_res) {
		$res=$this->query("SELECT uid,vk_id FROM cards WHERE del=0 AND vk_id>0");
		$arr_vk=[];
		while($r=$this->fetch_assoc($res)) {
			if(!in_array($r['uid'],$arr_res))
				continue;
			$arr_vk[]=$r['uid'];
		}
		return $arr_vk;
		
		foreach($arr_res AS $uid) {
			if($this->dlookup("vk_id","cards","uid='$uid'")>0) {
				//~ if($this->dlookup("res_vk","vkt_send_log","uid=$uid AND res_vk!=1"))
					//~ continue;
				$arr_vk[]=$uid;
			}
		}
		return $arr_vk;
	}
	function vkt_send_filter_cnt_tg($arr_res) {
		$res=$this->query("SELECT uid,telegram_id FROM cards WHERE del=0 AND telegram_id>0");
		$arr_tg=[];
		while($r=$this->fetch_assoc($res)) {
			if(!in_array($r['uid'],$arr_res))
				continue;
			$arr_tg[]=$r['uid'];
		}
		return $arr_tg;

		$arr_tg=[];
		foreach($arr_res AS $uid) {
			if($this->dlookup("telegram_id","cards","uid='$uid'")>0) {
				//~ if($this->dlookup("res_tg","vkt_send_log","uid=$uid AND res_tg!=1"))
					//~ continue;
				$arr_tg[]=$uid;
			}
		}
		return $arr_tg;
	}
	function vkt_send_filter_cnt_email($arr_res) {
		$res=$this->query("SELECT uid,email FROM cards WHERE del=0 AND email!=''");
		$arr_email=[];
		while($r=$this->fetch_assoc($res)) {
			if(!in_array($r['uid'],$arr_res))
				continue;
			$arr_email[]=$r['uid'];
		}
		return $arr_email;

		$arr_email=[];
		foreach($arr_res AS $uid) {
			if($this->dlookup("id","cards","email!=''")>0) {
				//~ if($this->dlookup("res_email","vkt_send_log","uid=$uid AND res_email!=1"))
					//~ continue;
				$arr_email[]=$uid;
			}
		}
		return $arr_email;
	}

	
	function vkt_send_msg__test($uid,$msg) {
		file_put_contents("/var/www/vlav/data/www/wwl/scripts/vkt_send_task_0ctrl.log","\n*****\n$uid\n".$msg,FILE_APPEND);
	}
	
	function vkt_send_task_0ctrl($vkt_send_id,$ctrl_id,$uid_if_mode3=0,$tm_event=0,$order_id=0) {
		global $tg_bot_notif,$vsegpt_secret,$vsegpt_model;
		if(!$vkt_send_id || !$ctrl_id)
			return false;

		$this->vkt_send_tg_bot=$this->dlookup("tg_bot_msg","0ctrl","id='$ctrl_id'");
		$tg_bot_notif=$this->dlookup("tg_bot_notif","0ctrl","id='$ctrl_id'");
		//$uid_if_mode3=$this->dlookup("uid","0ctrl_vkt_send_tasks","vkt_send_id='$vkt_send_id' AND ctrl_id='$ctrl_id'");
		$database=$this->get_ctrl_database($ctrl_id);
		$this->connect($database);
		$this->db200=$this->get_db200($this->get_ctrl_dir($ctrl_id));

		$r=$this->fetch_assoc($this->query("SELECT * FROM vkt_send_1 WHERE id='$vkt_send_id'"));
		$vkt_send_tm=$r['vkt_send_tm'];
		$sid=$r['sid'];
		$dt=date('d.m.Y H:i',$vkt_send_tm);
		$this->fl_tg=$r['fl_tg'];
		$this->fl_vk=$r['fl_vk'];
		$this->fl_email=$r['fl_email'];
		
		$land_num=$r['land_num'];
		$land_name=($land_num) ? $this->dlookup("land_name","lands","land_num='$land-num' AND del=0"):"";
		$product_id=($land_num) ? $this->dlookup("product_id","lands","del=0 AND land_num='$land_num'"):0;
		$product_descr=($product_id)?$this->dlookup("descr","product","del=0 AND id='$product_id'"):0;

		print "vkt_send_now started. vkt_send_tm=$dt vkt_send_id=$vkt_send_id<br>\n";

		if(!$sid) {
			if(!$vkt_send_tm || $vkt_send_tm>time()) {
				$this->vkt_send_task_del($vkt_send_id,$ctrl_id,$uid_if_mode3);
				return false;
			}
		}

		$email_template=$r['email_template'];
		$email_from=$r['email_from'];
		$email_from_name=$r['email_from_name'];
		$uni=false;
		if(!empty($email_template)) {
			$this->connect('vkt');
			$api_key=$this->dlookup("unisender_secret","0ctrl","id='$ctrl_id'");
			$this->connect($database);
			if(!empty($api_key)) {
				include_once "/var/www/vlav/data/www/wwl/inc/unisender.class.php";
				$uni=new unisender($api_key,$email_from,$email_from_name);
				include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
			}
		}

		$this->vk_token=$this->get_vk_token();

		$vkt_send_vk_photo=(!empty($r['vk_attach']))?$r['vk_attach']:false;
		$vkt_send_tg_photo=(!empty($r['tg_image']))?$r['tg_image']:false;
		$vkt_send_tg_video_note=(!empty($r['tg_video_note']))?$r['tg_video_note']:false;
		$vkt_send_tg_video=(!empty($r['tg_video']))?$r['tg_video']:false;
		$vkt_send_tg_audio=(!empty($r['tg_audio']))?$r['tg_audio']:false;
		$msg=$r['msg'];
		if(strpos($msg,"{{gpt")!==false && !$uid_if_mode3) //gpt allowed in mode 3 only!
			return false;

		if(!$sid)
			$res_arr=$this->vkt_send_filter($vkt_send_id);
		else
			$res_arr=[$uid_if_mode3];

		//print_r($res_arr);

		$tm1=time()-(1*5*60); //not in use!  time prevent repeated actions!!!!
		foreach($res_arr AS $uid) {
			
			$r_uni=$this->fetch_assoc($this->query("SELECT *,cards.id AS id
													FROM cards
													JOIN razdel ON cards.razdel=razdel.id
													WHERE uid='$uid' AND razdel.fl_not_send=0"));
			$w="1=2 ";
			if($tg_id=intval($r_uni['telegram_id']))
				$w.= " OR (tg_id='$tg_id')";
			if($vk_id=intval($r_uni['vk_id']))
				$w.= " OR (vk_id='$vk_id')";
			if($wa_id=intval($r_uni['pact_conversation_id']))
				$w.= " OR (wa_id='$wa_id')";
			$email=($this->validate_email($r_uni['email']))?$r_uni['email']:"";
			if($this->validate_email($email))
				$w.=" OR (email='$email')";

			//if($log_id=$this->dlookup("id","vkt_send_log","uid='$uid' AND tm>'$tm1' AND ($w)")) {
			if($log_id=$this->dlookup("id","vkt_send_log","uid='$uid' AND vkt_send_id='$vkt_send_id' AND tm_event='$tm_event'")) {
				print "uid=$uid already in log, passed \n";
				//~ $this->query("UPDATE vkt_send_log SET
							//~ res_vk='10',
							//~ res_tg='10',
							//~ res_wa='10',
							//~ res_email='10'
							//~ WHERE id='$log_id'");
				continue;
			}

			$this->query("INSERT INTO vkt_send_log SET
				vkt_send_id='$vkt_send_id',
				uid='$uid',
				tm='".time()."',
				tm_event='$tm_event',
				tg_id='$tg_id',
				vk_id='$vk_id',
				wa_id='$wa_id',
				email='".$this->escape($email)."',
				res_vk='0',
				res_tg='0',
				res_wa='0',
				res_email='0'
				");
			$insert_id=$this->insert_id();

	//$uid=-1001;

			$this->vkt_send_vk_photo=$vkt_send_vk_photo;
			$this->vkt_send_tg_photo=$vkt_send_tg_photo;
			$this->vkt_send_tg_video_note=$vkt_send_tg_video_note;
			$this->vkt_send_tg_video=$vkt_send_tg_video;
			$this->vkt_send_tg_audio=$vkt_send_tg_audio;
			
			if(strpos($msg,"{{gpt")!==false) {
				$this->connect('vkt');
				$vsegpt_secret=$this->dlookup("vsegpt_secret","0ctrl","id=$ctrl_id");
				$vsegpt_model=$this->dlookup("vsegpt_model","0ctrl","id=$ctrl_id");
				$this->connect($database);
			}

			$this->vkt_send_msg_order_id=$order_id;
			$this->ctrl_id=$ctrl_id;
			$this->vkt_send_msg($uid,$msg); ////
	//$this->print_r($this->vkt_send_res);

			$res_email=0;
			if($uni!==false) {
				if(!empty($email)) {
					$klid=$r_uni['id'];
					$ctrl_dir=$this->get_ctrl_dir($ctrl_id);
					$this->db200="https://for16.ru/d/$ctrl_dir";
					$client_name=(!empty($r_uni['name']))?$r_uni['name']:"_";
					$phone=(!empty($r_uni['mob_search']))?$r_uni['mob_search']:"_";
					$uid_md5=(!empty($r_uni['uid_md5']))?$r_uni['uid_md5']:"_";
					$cabinet_link=$this->get_direct_code_link($klid);
					if(!$cabinet_link || empty($cabinet_link) )
						$cabinet_link="_";
					$partner_code=$this->get_bc($klid);
					if(!$partner_code || empty($partner_code))
						$partner_code="_";
					$vars=['name'=>$client_name,
						'client_name'=>$client_name,
						'email'=>$email,
						'phone'=>$phone,
						'uid'=>$uid_md5,
						'cabinet_link'=>$cabinet_link,
						'partner_code'=>$partner_code,
						'product_id'=>$product_id,
						'product'=>$product_descr,
						'land_num'=>$land_num,
						'land_name'=>$land_name,
						'promocode'=>$this->promocode_get_last($uid)
						];

					$this->log_email($email);

					if($this->fl_email)
						$res_email=($uni->email_by_template($email,$email_template,$vars))?1:2;
					else
						$res_email=5;
					//~ file_put_contents("vkt_send_email.log",
						//~ "\n--- $email\n".print_r($uni->res,true)."\n".print_r($vars,true),
						//~ FILE_APPEND);
				}
			}
			print "uid=$uid proceed \n";

			if(!isset($this->vkt_send_res['vk']))
				$this->vkt_send_res['vk']=0;
			if(!isset($this->vkt_send_res['tg']))
				$this->vkt_send_res['tg']=0;
			if(!isset($this->vkt_send_res['wa']))
				$this->vkt_send_res['wa']=0;
			$this->query("UPDATE vkt_send_log SET
						res_vk='{$this->vkt_send_res['vk']}',
						res_tg='{$this->vkt_send_res['tg']}',
						res_wa='{$this->vkt_send_res['wa']}',
						res_email='$res_email'
						WHERE id='$insert_id'");
			//~ $this->query("INSERT INTO vkt_send_log SET
				//~ vkt_send_id='$vkt_send_id',
				//~ uid='$uid',
				//~ tm='".time()."',
				//~ res_vk='{$this->vkt_send_res['vk']}',
				//~ res_tg='{$this->vkt_send_res['tg']}',
				//~ res_wa='{$this->vkt_send_res['wa']}',
				//~ res_email='$res_email'
				//~ ");
	//break;
		}
		if(!$sid)
			$this->query("UPDATE vkt_send_1 SET del=1 WHERE id='$vkt_send_id'");

		$this->vkt_send_task_del($vkt_send_id,$ctrl_id,$uid_if_mode3);
		print "vkt_send_task_del($vkt_send_id,$ctrl_id,$uid_if_mode3) \n";
		print "ok\n";
		return true;
	}
	function vkt_send_task_add($ctrl_id, $tm_event, $vkt_send_id,$vkt_send_type,$uid=0,$order_id=0) { //vkt_send_type - not use!!
		if($this->dlookup("del","vkt_send_1","id='$vkt_send_id'")) //del==1
			return false;
		if(date("H:i",$tm_event)=='23:59')
			$tm_event-=(24-10)*60*60; //correct to 10:00 MSK
		if($tm_event<time())
			return false;
		$this->vkt_send_task_del($vkt_send_id,$ctrl_id,$uid);
		$tmp=$this->database;
		$this->connect('vkt');
		//~ if($this->dlookup("id","0ctrl_vkt_send_tasks","ctrl_id='$ctrl_id'
													//~ AND vkt_send_id='$vkt_send_id'
													//~ AND vkt_send_type='$vkt_send_type'
													//~ AND uid='$uid'"))
			//~ return false;
	//$this->notify_me("vkt_send_task_add $ctrl_id, $tm_event, $vkt_send_id,$vkt_send_type,$uid");
		$this->query("INSERT INTO 0ctrl_vkt_send_tasks SET
			tm='$tm_event',
			ctrl_id='$ctrl_id',
			vkt_send_id='$vkt_send_id',
			vkt_send_type='$vkt_send_type',
			uid='$uid',
			order_id='$order_id'
			",0);
		$insert_id=$this->insert_id();
	//	print "HERE $insert_id"; exit;
		$this->connect($tmp);
		return $insert_id;
	}
	function vkt_send_task_chk($vkt_send_id,$ctrl_id) {
		//~ if($this->dlookup("id","vkt_send_1","id='$vkt_send_id' AND sid>0",0))
			//~ return true;
		$tmp=$this->database;
		$this->connect('vkt');
		$res=$this->dlookup("id","0ctrl_vkt_send_tasks","ctrl_id='$ctrl_id' AND vkt_send_id='$vkt_send_id'");
		$this->connect($tmp);
		return $res;
	}
	function vkt_send_task_fname($vkt_send_id,$ctrl_id,$uid=0) {
		return "task_".$vkt_send_id."_".$ctrl_id."_".$uid.".php";
	}
	function vkt_send_task_del($vkt_send_id,$ctrl_id,$uid=0,$order_id=0) {
		$tmp=$this->database;
		$this->connect('vkt');
		if($uid) {
			$add=$order_id ? "AND order_id='$order_id'" : "";
			$this->query("DELETE FROM 0ctrl_vkt_send_tasks WHERE ctrl_id='$ctrl_id' AND vkt_send_id='$vkt_send_id' AND uid='$uid' $add");
		} else
			$this->query("DELETE FROM 0ctrl_vkt_send_tasks WHERE ctrl_id='$ctrl_id' AND vkt_send_id='$vkt_send_id'");
		$fname=$this->vkt_send_task_fname($vkt_send_id,$ctrl_id,$uid);
		unlink("/var/www/vlav/data/www/wwl/scripts/vkt_send_tasks/$fname");
		//~ if($ctrl_id==101)
			//~ $this->notify_me("vkt_send_task_del(vkt_send_id=$vkt_send_id,ctrl_id=$ctrl_id,uid=$uid)");
		$this->connect($tmp);
		return true;
	}
	function vkt_send_mode($vkt_send_id) {
		//~ if($this->get_database_by_cwd() != $this->database)
			//~ return false;
		$r=$this->fetch_assoc($this->query("SELECT * FROM vkt_send_1 WHERE id='$vkt_send_id'"));
		if($r['land_num'] && !$r['sid'])
			return 2;
		elseif($r['sid'])
			return 3;
		else
			return 1;
	}
	function save_vkt_send_tm($vkt_send_id,$ctrl_id) {
		$r=$this->fetch_assoc($this->query("SELECT * FROM vkt_send_1 WHERE id='$vkt_send_id'"));
		$tm_shift=$r['tm_shift'];
		if($r['land_num'] && !$r['sid']) { //mode=2
			$land_num=$r['land_num'];
			$tm_scdl=$this->dlookup("tm_scdl","lands","land_num='{$r['land_num']}'");
			if($tm_scdl===false)
				return false;
			$vkt_send_tm= $tm_scdl+$tm_shift;
			if($vkt_send_tm<time())
				return false;
			$this->query("UPDATE vkt_send_1 SET vkt_send_tm='$vkt_send_tm' WHERE id='$vkt_send_id'");
			$this->query("UPDATE cards SET tm_schedule='$tm_scdl' WHERE del=0 AND scdl_web_id='$land_num'");

			$tmp=$this->database;
			$this->connect('vkt');
			$this->query("UPDATE 0ctrl_vkt_send_tasks SET tm='$vkt_send_tm' WHERE vkt_send_id='$vkt_send_id' AND ctrl_id='$ctrl_id'");
			$this->connect($tmp);
		} elseif($r['land_num'] && $r['sid']) {
			return false;
			$tm_sid=$this->dlast("tm","msgs","source_id='{$r['sid']}'");
			if($tm_sid===false)
				return false;
			$vkt_send_tm= $tm_sid+$tm_shift;
			if($vkt_send_tm<time())
				return false;
			$this->query("UPDATE vkt_send_1 SET vkt_send_tm='$vkt_send_tm' WHERE id='$vkt_send_id'");
		}
		return $vkt_send_tm;
	}
	function print_time_shift($tm_shift) {
		if(!$tm_shift)
			return "сразу ";
		$dt="через ";
		$days_shift=intval($tm_shift/(24*60*60));
		if($days_shift)
			$dt="$days_shift дней ";
		$tm_rest=$tm_shift-($days_shift*24*60*60);
		$hours_shift=intval($tm_rest/(60*60));
		if($hours_shift)
			$dt.="$hours_shift часов ";
		$tm_rest=$tm_shift-($days_shift*24*60*60)-($hours_shift*60*60);
		$min_shift=intval($tm_rest/60);
		if($min_shift)
			$dt.="$min_shift минут";
		return "$dt";
	}
}

?>
