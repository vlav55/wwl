<?
include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
class robot {
	var $database;
	var $sex_allowed=false;
	var $send=false;
	var $razdel_yes=2;
	var $razdel_no=4;
	var $razdel_info=2;
	var $arr_no=array(
	"нет",
	"не продаю",
	"незаходил",
	"не заходил",
	"не интересует",
	"не интересно",
	);
	var $arr_yes=array(
	"да\z",
	"да ",
	"даа",
	"да,",
	"да\)",
	"да!",
	"интересно",
	"Интересует",
	"Интересуюсь",
	"Давай",
	"Можно попробовать",
	);
	var $arr_info=array(
	"где",
	"цена",
	"стоимость",
	"информац",
	"условия",
	"сколько стоит",
	"условия",
	"сколько",
	"Расскажите",
	);
	
	var $mess_yes="Какая марка авто, год, состояние, фото?";
	
	var $mess_no="Тогда извините за беспокойство, при необходимости обращайтесь";
	
	var $mess_info="Какая марка авто, год, состояние, фото?";
	
	function log($msg) {
		if(1) {
			print $msg;
		} else
			print $msg."<br>";
		
	}
	
	function filter($mess,$arr,$mess_max_size=150) {
		if(strlen($mess)>=$mess_max_size)
			return false;
		foreach($arr AS $str) {
			//$this->log( "HERE_".mb_strtolower($mess,"CP1251");
			if(preg_match("|".mb_strtolower($str,"utf8")."|is",mb_strtolower($mess,"utf8")))
				return true;
		}
		return false;
	}
	function answer($mess,$razdel,$uid,$acc_id,$sex_allowed=false,$send=false) {
		if(!$send) {
			$this->log( "send=false - fake mode\n");
		}
		$db=new db;
		if($mess=="")
			return true;
		$token=$db->dlookup("token","vklist_acc","id=$acc_id");
		if($send)
			sleep(rand(5,10));
		//return "uid=$uid acc_id=$acc_id razdel=$razdel ".$mess;
		
		$vk=new vklist_api($token);
		$blocked=$vk->vk_is_user_blocked($uid,$use_stop_words=false, $sex_allowed);
		if($blocked==0) {
			if($send) {
				$res=$vk->vk_msg_send($uid, $mess,$fake=false, $chat_id=false, $attachment=false);
				
			} else {
				$this->log( "fake mode : no real sending\n");
				$res=0;
			}
			
			if($res==0) {
				if($send) {
					$db->query("UPDATE cards SET razdel=$razdel,fl_newmsg=0,tm_lastmsg=".time()." WHERE uid=$uid");
					$db->query("INSERT INTO msgs SET uid=$uid,acc_id=$acc_id,mid=0,tm=".time().",user_id=0,msg='".$db->escape($mess)."',outg=1,imp=2,new=0");
				}
				$this->log( "answer : $uid - message sent\n");
				return true;
			} else {
				$this->log( "answer : $uid sending error $res\n");
				return false;
			} 
		} else {
			$this->log( "answer : error : user blocked : res=$blocked\n");
			return false;
		}
		return false;
	}
	function run () {
		if(file_exists("robot.lock")) {
			if(filemtime("robot.lock")<(time()-(1*60*60))) {
				unlink("robot.lock");
			} else {
				$this->log( "locked by robot.lock\n");
				exit;
			}
		}
		touch("robot.lock");
		//$this->log( "HERE_".filemtime("robot.lock");exit;
		
		$db=new db($this->database);
		$tm_chk=time()-(60);
		$res=$db->query("SELECT msgs.uid AS uid,msgs.acc_id AS acc_id,name,surname 
				FROM cards 
				JOIN msgs ON msgs.uid=cards.uid 
				WHERE del=0 AND outg=0 AND fl_newmsg>0 AND msgs.tm<$tm_chk
				GROUP BY msgs.uid HAVING COUNT(msgs.uid)>=1
				ORDER BY msgs.tm DESC 
				LIMIT 10");
		$n=1;
		while($r=mysql_fetch_assoc($res)) {
			$this->log( "\n{$r['uid']} ===========================\n");
			$res1=$db->query("SELECT * FROM msgs WHERE uid={$r['uid']} AND outg=0 ORDER by tm LIMIT 1");
			while($r1=mysql_fetch_assoc($res1)) {
				$dt=date("d.m.Y H:i",$r1['tm']);
				$outg=($r1['outg']==0)?"<<":">>";
				$this->log( "{$r1['msg']}\n");
				if($this->filter($r1['msg'],$this->arr_no)) {
					$f="<span class='label label-danger'>NO</span>";
					$mess=$this->mess_no; $razdel=$this->razdel_no;
				} elseif($this->filter($r1['msg'],$this->arr_info)) {
					$f="<span class='label label-info'>INFO</span>";
					$mess=$this->mess_info; $razdel=$this->razdel_info;
				} elseif($this->filter($r1['msg'],$this->arr_yes)) {
					$f="<span class='label label-success'>YES</span>";
					$mess=$this->mess_yes;  $razdel=$this->razdel_yes;
				} else {
					$this->log("<span class='label label-warning'>PASSED</span>");
					continue;
				}
				/*
				$this->log( "<div class='well well-sm row'>
					<div class='col-sm-1'>$f</div>
					<div class='col-sm-2'>
						{$r['surname']} {$r['name']}
						<span class='badge'><a style='color:white;' href='javascript:wopen(\"msg.php?uid={$r['uid']}\")'>{$r['acc_id']}</a></span> 
					</div>
					<div class='col-sm-2'>$dt </div>
					<div class='col-sm-3'>{$r1['msg']}</div>
					<div class='col-sm-4'>".answer($mess,$razdel,$r['uid'],$r['acc_id'])."</div>
					</div>";
				*/
				$uid=$r['uid'];
				$uid="198746774";
				$this->log( "$n $f $uid {$r['acc_id']} res=".$this->answer($mess,$razdel,$uid,$r['acc_id'],$this->sex_allowed,$this->send)."\n");
				$n++;
			}
		}
		
		$this->log( "\n\n");
		unlink("robot.lock");
	}
}
class vklist_scan_votes {
	var $database="";
	var $group_id=-0; //"-147195022"
	var $token;
	var $target_group_id=false;
	var $mode=0; //0-add new to vklist; 1 -add to cards
	var $razdel_exclude=array('3','8','11','12');

	function cli() {
		global $argc,$argv;
		$db=new db($this->database);
		if($argc==1) {
			print "USE vklist_scan_votes vote_vk_uid(for example 109) [token] \n\n";
			exit;
		}
		if($argc==2) {
			$vote_vk_uid=$argv[1];
			$r=$db->get_first_working_acc();
			$this->token=$r['token'];
			print "\n-------------------------------\n";
			print "Starting at ".date("d.m.Y H:i")."\n";
			print "For scanning use acc_id={$r['id']} ".$r['name']."\n";
			//print "Be sure that this accaunt participated in the vote othercase you will get - Error. polls_getvoters :\n";
		}
		if($argc==3) {
			$vote_vk_uid=trim($argv[1]);
			$this->token=$argv[2];
		}
		return $vote_vk_uid;
	}
	
	function scan_votes($vote_vk_uid) {
		if(!intval($vote_vk_uid)) {
			print "ERROR : vote_vk_uid=false \n";
			return false;
		}
		$token=$this->token;
		$db=new db($this->database);
		$vk=new vklist_api($token);

		$gid=$this->group_id;
		$post_url="https://vk.com/wall-$gid"."_$vote_vk_uid";
		$answ= $vk->polls_getinfo("$gid","$gid"."_$vote_vk_uid"); //-116176094_109
	//	print_r($answ);
		
		$url = 'https://api.vk.com/method/polls.addVote';
		$params=array('v'=>'5.80', 'access_token'=>$this->token, 'owner_id'=>$gid,'poll_id'=>$answ['poll_id'],'answer_id'=>$answ['answers'][0]['id'],'is_board'=>'0');
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params))))),true);
		print_r($res);
		if(isset($res['error'])) {
		}


		$uids=array();
		usleep(300000);
		if($answ) {
			$var=0;
			foreach($answ['answers'] AS $a) {
				$u=$vk->polls_getvoters($gid,$answ['poll_id'], $a['id']);
				print "polls_getvoters($gid,{$answ['poll_id']},{$a['id']})\n";
				usleep(300000);
				print "----------\n";
				print $a['text']."\n";
				//print_r($u); exit;
				$var++;
				$source_vote=$vote_vk_uid."_".$var;
				print "source_vote=$source_vote\n";
				
				//$group_name="vote_$vote_vk_uid $var - ".$a['text'];
				$group_name="vote_$vote_vk_uid - ".$a['text'];
				$group_name_utf=$group_name;
				if($this->mode==0) { //ADD group to vklist groups
					$res=$db->query("SELECT * FROM vklist_groups WHERE group_name='".$db->escape($group_name)."'");	
					if($db->num_rows($res)==0) {
						$db->query("INSERT INTO vklist_groups SET group_name='".$db->escape($group_name)."',tm=".time().",fl_send_msg=1,vote='$source_vote'");
						$group_id=mysql_insert_id();
						print "$group_id $group_name_utf - group ADDED to vklist_groups\n";
					} else {
						$r=$db->fetch_assoc($res);
						$group_id=$r['id'];
						print "$group_id $group_name_utf - group PASSED - already in vklist_groups\n";
						if($r['vote']=='')
							$db->query("UPDATE vklist_groups SET vote='$source_vote' WHERE id=$group_id");
					}
					$group_tm=$db->dlookup("tm","vklist_groups","id=$group_id");
					print "group_tm = $group_tm ".date("d.m.Y",$group_tm)."\n";
				}

				if(!$u) {
					print "Error. polls_getvoters : ".$vk->last_response."\n\n"; continue;
				}
				//print "HERE_".$this->target_group_id; exit;
				//print_r($u);
				
				foreach($u AS $uid) {
					if($db->dlookup("uid","vklist_scan_votes","uid='$uid' AND vote_id='$vote_vk_uid'")) {
						print "$uid is in vklist_scan_votes for  vote_id='$vote_vk_uid' - PASSED\n";
						continue;
					}
					$db->query("INSERT INTO vklist_scan_votes SET uid='$uid',tm='".time()."',vote_id='$vote_vk_uid'");
					
					//$r=$db->fetch_assoc($db->query("SELECT razdel,source_id,source_vote FROM cards WHERE uid=$uid"));
					$r=$db->fetch_assoc($db->query("SELECT razdel,source_id FROM cards WHERE uid=$uid"));
					$tm=time();
					print "$uid ";
					if($r) { // IN CARDS       //if($r && $razdel!=4)
						print "IN CARDS - ";
						$razdel=$r['razdel'];
						$razdel_exclude=$this->razdel_exclude;
						//$r1=$db->fetch_assoc($db->query("SELECT uid,razdel,source_id FROM cards WHERE uid=$uid AND source_vote='$source_vote'"));
						//$source_id=$db->get_source_id_by_priority($uid,$r['source_id']);
						if(!in_array($razdel,$razdel_exclude)) {
							//if(!$db->dlookup("uid","msgs","uid=$uid AND vote='$source_vote'") && !$db->dlookup("uid","vklist","uid=$uid AND vote='$source_vote'") ) {
								if($db->dlookup("fl_newmsg","cards","uid=$uid")!=2)
									$db->query("UPDATE cards SET fl_newmsg=1,tm_lastmsg=$tm  WHERE uid=$uid");	
								$db->save_comm($uid,0, date("d.m.Y")." Проголосовал(а) в опросе: $group_name ",4,$source_vote);
							//	$db->save_comm($uid,0, date("d.m.Y")." Проголосовал(а) в опросе: <a href='$post_url' class='' target='_blanc'>$post_url</a> ",4,$source_vote);
								print "$uid - marked to go to new messages\n";
								$uids[]=$uid;
							//}
						} else {
							//$db->query("UPDATE cards SET source_vote='$source_vote' WHERE uid=$uid");	
							print "$uid - but in razdel_exclude - do nothing\n";
						}
						//exit;
					} else {
						print "NOT IN CARDS - ";
						if($this->mode==0) {
							print "mode=0 (destination is VKLIST) ";
							$tm_msg=$db->dlookup("tm_msg","vklist","uid=$uid");
							if($tm_msg===false) {
								$db->query("INSERT INTO vklist SET uid=$uid,group_id=$group_id,tm_cr=".time().",vote='$source_vote'");
								$uids[]=$uid;
								print "$uid - ADDED to vklist\n";
							} elseif($tm_msg<($group_tm-(7*24*60*60)) AND $tm_msg!=1) {
								print "$uid - IN VKLIST, MESSAGE WAS SENT BUT BEFORE GROUP CREATED-7 DAYS (".date("d.m.Y",$tm_msg).") - cleared tm_msg for sending\n";
								$db->query("UPDATE vklist SET group_id=$group_id,tm_msg=0,tm_friends=0,tm_wall=0,res_msg=0,blocked=0,vote='$source_vote' WHERE uid=$uid AND tm_msg!=1");
								$uids[]=$uid;
							} else {
								print "$uid - last sent at ($tm_msg) ".date("d.m.Y",$tm_msg)." - it is closer to today then 7 days. Passed.\n";
							}
						} else {
							usleep(300000);
							$name=$vk->vk_get_name_by_uid($uid);
							print "mode=1 (destination is CARDS) - $uid $name $source_vote - is NOT in cards - added\n";
							list($n1,$n2)=explode(" ",$name);
							$acc_id=$db->get_default_acc();
							$db->query("INSERT INTO cards SET 
									uid=$uid,
									acc_id=$acc_id,
									name='".$db->escape($n1)."',
									surname='".$db->escape($n2)."',
									razdel=0,
									source_id=4,
									fl_newmsg=1,
									tm_lastmsg=".time().",
									source_vote='$source_vote',
									tm=".time()
									);
							//$db->save_comm($uid,0, date("d.m.Y")." проголосовал(а) в опросе: <a href='$post_url' class='' target='_blanc'>$post_url</a>",4,$source_vote);
							$db->save_comm($uid,0, date("d.m.Y")." Проголосовал(а) в опросе: $group_name ",4,$source_vote);
							//exit;
						}
					}	
					/*
					*/
					//exit;
				}
				print "SCANNED ".sizeof($u)." - DONE\n";
			}
		} else 
			print $vk->last_response."\n\n";
		if(sizeof($uids)>0)
			new ad_target($this->target_group_id,$uids);

		$serv=new vklist_bdate;
		$serv->check_bdate();
		$serv->vklist_scan_groups_2_bdate();
		$this->scan_likes($vote_vk_uid);
	}
	function create_group($group_name) {
		global $db;
		$res=$db->query("SELECT * FROM vklist_groups WHERE group_name='".$db->escape($group_name)."'");	
		if($db->num_rows($res)==0) {
			$db->query("INSERT INTO vklist_groups SET group_name='".$db->escape($group_name)."',tm=".time().",fl_send_msg=1,vote='$source_vote'");
			$group_id=mysql_insert_id();
			print "create_group : $group_name ($group_id) - group ADDED to vklist_groups\n";
		} else {
			$r=$db->fetch_assoc($res);
			$group_id=$r['id'];
			print "create_group : $group_name ($group_id) - group PASSED - already in vklist_groups\n";
			if($r['vote']=='')
				$db->query("UPDATE vklist_groups SET vote='$source_vote' WHERE id=$group_id");
		}
	//	$group_tm=$db->dlookup("tm","vklist_groups","id=$group_id");
	//	print "create_group : $group_name ($group_id) group_tm = $group_tm ".date("d.m.Y",$group_tm)."\n";
		return $group_id;
	}
	function do_action($uid,$source_vote,$comm,$group_name,$group_id,$source_id) {
		global $db;
		$r=$db->fetch_assoc($db->query("SELECT razdel,source_id FROM cards WHERE uid=$uid"));
		$tm=time();
		if($r) { // IN CARDS       //if($r && $razdel!=4)
			print "IN CARDS - ";
			$razdel=$r['razdel'];
			$razdel_exclude=$this->razdel_exclude;
			//$r1=$db->fetch_assoc($db->query("SELECT uid,razdel,source_id FROM cards WHERE uid=$uid AND source_vote='$source_vote'"));
			//$source_id=$db->get_source_id_by_priority($uid,$r['source_id']);
			if(!in_array($razdel,$razdel_exclude)) {
				//if(!$db->dlookup("uid","msgs","uid=$uid AND vote='$source_vote'") && !$db->dlookup("uid","vklist","uid=$uid AND vote='$source_vote'") ) {
					if($db->dlookup("fl_newmsg","cards","uid=$uid")!=2)
						$db->query("UPDATE cards SET fl_newmsg=1,tm_lastmsg='$tm',source_id='$source_id'  WHERE uid='$uid'");	
					$db->save_comm($uid,0, date("d.m.Y")." $comm: $group_name ",$source_id,$source_vote);
					print "$uid - marked to go to new messages\n";
				//}
			} else {
				//$db->query("UPDATE cards SET source_vote='$source_vote' WHERE uid=$uid");	
				print "$uid - but in razdel_exclude - do nothing\n";
			}
			//exit;
		} else {
			print "NOT IN CARDS - ";
			if($this->mode==0) {
				print "mode=0 (destination is VKLIST) ";
				$tm_msg=$db->dlookup("tm_msg","vklist","uid=$uid");
				$group_tm=$tm_msg+(7*24*60*60)+1; //fake to force adding to vklist
				if($tm_msg===false) {
					$db->query("INSERT INTO vklist SET uid=$uid,group_id=$group_id,tm_cr=".time().",vote='$source_vote'");
					print "$uid - ADDED to vklist\n";
				} elseif($tm_msg<($group_tm-(7*24*60*60)) AND $tm_msg>1) {
					print "$uid - IN VKLIST, MESSAGE WAS SENT BUT BEFORE GROUP CREATED-7 DAYS (".date("d.m.Y",$tm_msg).") - cleared tm_msg for sending\n";
					$db->query("UPDATE vklist SET group_id=$group_id,tm_msg=0,tm_friends=0,tm_wall=0,res_msg=0,blocked=0,vote='$source_vote' WHERE uid=$uid AND tm_msg!=1");
				}
			} else {
				usleep(300000);
				$vk=new vklist_api;
				$name=$vk->vk_get_name_by_uid($uid);
				print "mode=1 (destination is CARDS) - $uid $name $source_vote - is NOT in cards - added\n";
				list($n1,$n2)=explode(" ",$name);
				$acc_id=$db->get_default_acc();
				$db->query("INSERT INTO cards SET 
						uid=$uid,
						acc_id=$acc_id,
						name='".$db->escape($n1)."',
						surname='".$db->escape($n2)."',
						razdel=0,
						source_id='$source_id',
						fl_newmsg=1,
						tm_lastmsg=".time().",
						source_vote='$source_vote',
						tm=".time()
						);
				$db->save_comm($uid,0, date("d.m.Y")." $comm: $group_name",$source_id,$source_vote);
				//exit;
			}
		}	
	}
	function scan_likes($item_id) {
	//	if($this->database !="vktrade")
		//	return;
		if(!intval($item_id)) {
			print "ERROR : scan_likes : item_id=false \n";
			return false;
		}
		print "\nSCAN LIKES STARTED\n";
		$gid=-intval($this->group_id);
		$token=$this->token;
		$db=new db($this->database);
		$vk=new vklist_api($token);
		print "gid=$gid item_id=$item_id\n";
		$r=$vk->likes_getlist($gid,$item_id);
		if(sizeof($r)==0) {
			print "scan_likes : NO LIKES ON item_id=$item_id\n";
			return false;
		}
		$post_url="https://vk.com/wall-$this->group_id"."_$item_id";
		//$group_name="Лайкнули на посте <a href='$post_url' class='' target='_blanc'>$post_url</a>";
		$group_name="Лайкнули на посте $gid"."_"."$item_id";
		$group_id=0;
		if($this->mode==0) { //ADD group to vklist groups
			$group_id=$this->create_group($group_name);
		}
	//	print_r($r); 
		foreach($r AS $uid) {
			if(!$db->dlookup("uid","vklist_scan_likes","uid='".intval($uid)."'")) {
				$db->query("INSERT INTO vklist_scan_likes SET uid='".intval($uid)."',gid='$gid',item_id='$item_id',tm='".time()."' ");
				$comm="Поставил(а) лайк на посте $gid"."_"."$item_id";
				$this->do_action($uid,$item_id,$comm,$group_name,$group_id,$source_id=10);
			} else
				print "scan_likes : uid=$uid : ALREADY SCANNED\n";
		}
	}
	/*
	 * INSERT INTO `vktrade`.`sources` (`id`, `source_name`, `priority`, `del`) VALUES ('10', 'Лайкнул', '50', '0');
	 * INSERT INTO `vktrade`.`sources` (`id`, `source_name`, `priority`, `del`) VALUES ('11', 'Сделал репост', '50', '0');

	 */

}
class vklist_msgs_scan {
	var $database="";
	var $telegram_bot="";
	var $do_not_notify=false;
	var $db200="";
	var $razdel_do_not_notify=array(3);
	var $fl_newmsg=2;
	var $razdel=4;
	var $vktrade_uid=false;

	function scan_new() {
		$this->scan();
	}
	function msgs_do_something($uid,$msg) {
	}
	function msgs_do_something2($uid,$msg) {
	}
	function scan() {
	//exit;
		$db=new db($this->database);
		$db->db200=$this->db200;
		$db->razdel_do_not_notify=$this->razdel_do_not_notify;
	//	print "razdel_do_not_notify:\n";
	//	print_r($db->razdel_do_not_notify);
		$db->telegram_bot=$this->telegram_bot;
		$last_response="";
		
		print "New msgs scan started : $this->database <br>\n";
		$res_acc=$db->query("SELECT * FROM vklist_acc WHERE del=0 AND token!='' AND token!='0' AND fl_allow_read_from_all='1'");
		$n=0;
		while($r_acc=$db->fetch_assoc($res_acc)) {
			$acc_id=$r_acc['id']; 
			$last_mid=$r_acc['last_mid']; 
			$last_mid_1=$last_mid;
			$first_run=($last_mid==0)?true:false;
			print "CHECKING ".date("d.m.Y H:i")." acc_id=$acc_id ({$r_acc['name']}) last_mid=$last_mid<br>\n";
			$vk=new vklist_api($db->dlookup("token","vklist_acc","id=$acc_id"));
			$vk->last_mid=0;
			$arr=$vk->vk_get_new_conversations($last_mid);
			if(!$arr) {
				if($vk->error_code==5 || $vk->error_code==8) {
					print "\nacc $acc_id banned : ".$vk->error_code."\n";
					if($r_acc['last_error']==0)
						$db->query("UPDATE vklist_acc SET last_error=".$vk->error_code.",fl_acc_allowed=0  WHERE id=$acc_id");
				}
				continue;
			}
			if($r_acc['last_error']!=0)
				$db->query("UPDATE vklist_acc SET last_error=0,fl_acc_allowed=0  WHERE id=$acc_id");
			//$db->print_r($arr);
			foreach($arr AS $msg) {
				//if user in cards
				$vk_id=intval($msg['uid']);
				//print "HERE";
				if($uid=$db->dlookup("uid","cards","vk_id='$vk_id' OR uid='$vk_id'")) { 
					$this->msgs_do_something($uid,$msg);
					print "FOUND IN CARDS | uid=$uid<br>\n";
					$db->query("UPDATE cards SET acc_id=$acc_id, tm_lastmsg=".time().", fl_newmsg='".$this->fl_newmsg."' WHERE uid='$uid'");
					if(!$this->do_not_notify) 
						$db->notify($uid,$msg['body']."\n(1) last_mid=$vk->last_mid");
					$n++;
				} else { //IF IN VKLIST OR IN VKLIST2 OR GRP ACC
					if($r_acc['fl_allow_read_from_all']==1) { //СООБЩЕНИЯ ГРУППЫ
						if(!$first_run) {
							$uid=$vk_id;
							$name=explode(" ",$vk->vk_get_name_by_uid($msg['uid'])); sleep(1);
							$fl1=($this->fl_newmsg>0)?3:0;
							$db->query("INSERT INTO cards SET 
									uid='$uid',
									uid_md5='".$db->uid_md5($uid)."',
									acc_id='{$r_acc['id']}',
									name='".$db->escape(trim($name[0]))."',
									surname='".$db->escape(trim($name[1]))."',
									razdel='".$this->razdel."',
									source_id=9,
									fl_newmsg='$fl1',
									tm_lastmsg=".time().",
									tm=".time().",
									vk_id='$uid'
									");
							print "VK GROUP MESSAGE - New uid=$uid added from {$r_acc['name']}\n";
							$n++;
							if(!$this->do_not_notify)
								$db->notify($uid,$msg['body']."\n(1) last_mid=$vk->last_mid",$r_acc['id']);
						}
					}

					//~ $r=$db->fetch_assoc($db->query("SELECT * FROM vklist WHERE uid='$uid'"));
					//~ if($r) { //if user in vklist
						//~ $db->query("UPDATE vklist SET tm_msg=1 WHERE uid='$uid' AND tm_msg=0"); //stop to send ad 
						//~ //add to CARDS
						//~ $name=explode(" ",$vk->vk_get_name_by_uid($uid));
						//~ sleep(1);
						//~ $source_id=($r['vote']!="")?"4":"3";
						//~ $db->query("INSERT INTO cards SET 
								//~ uid='$uid',
								//~ uid_md5='".$db->uid_md5($uid)."',
								//~ acc_id='$acc_id',
								//~ name='".$db->escape($name[0])."',
								//~ surname='".$db->escape($name[1])."',
								//~ tm='".time()."',
								//~ tm_lastmsg='".time()."',
								//~ fl_newmsg='".$this->fl_newmsg."',
								//~ razdel='".$this->razdel."',
								//~ source_id='$source_id'
								//~ ");
						//~ if(!$this->do_not_notify)
							//~ print "FOUND IN VKLIST - New uid to cards added : uid=$uid";
						//~ $n++;
					//~ } elseif($this->is_in_vklist2($uid)) { //if user in vklist2
						//~ //$db->query("UPDATE vklist SET tm_msg=1 WHERE uid='$uid' AND tm_msg=0"); //stop to send ad 
						//~ //add to CARDS
						//~ $name=explode(" ",$vk->vk_get_name_by_uid($uid));
						//~ sleep(1);
						//~ $source_id=($r['vote']!="")?"4":"3";
						//~ $db->query("INSERT INTO cards SET 
								//~ uid='$uid',
								//~ uid_md5='".$db->uid_md5($uid)."',
								//~ acc_id='$acc_id',
								//~ name='".$db->escape($name[0])."',
								//~ surname='".$db->escape($name[1])."',
								//~ tm='".time()."',
								//~ tm_lastmsg='".time()."',
								//~ fl_newmsg='".$this->fl_newmsg."',
								//~ razdel='".$this->razdel."',
								//~ source_id='$source_id'
								//~ ");
						//~ print "FOUND IN VKLIST2 - New uid to cards added : uid=$uid";
						//~ if(!$this->do_not_notify)
						//~ $n++;
					//~ } elseif($r_acc['fl_allow_read_from_all']==1) { //СООБЩЕНИЯ ГРУППЫ
						//~ if(!$first_run) {
							//~ $name=explode(" ",$vk->vk_get_name_by_uid($msg['uid'])); sleep(1);
							//~ $fl1=($this->fl_newmsg>0)?3:0;
							//~ $db->query("INSERT INTO cards SET 
									//~ uid='$uid',
									//~ uid_md5='".$db->uid_md5($uid)."',
									//~ acc_id='{$r_acc['id']}',
									//~ name='".$db->escape(trim($name[0]))."',
									//~ surname='".$db->escape(trim($name[1]))."',
									//~ razdel='".$this->razdel."',
									//~ source_id=9,
									//~ fl_newmsg='$fl1',
									//~ tm_lastmsg=".time().",
									//~ tm=".time().""
									//~ );
							//~ print "VK GROUP MESSAGE - New uid=$uid added from {$r_acc['name']}\n";
							//~ $n++;
							//~ if(!$this->do_not_notify)
								//~ $db->notify($msg['uid'],$msg['body'],$r_acc['id']);
						//~ }
					//~ }
				}
				$last_mid=$vk->last_mid;
				$this->msgs_do_something2($uid,$msg);
			}
			//print ($last_mid-$last_mid_1)." new messages from all<br>\n";
			print $n." new messages from all<br>\n";
			$last_mid++;
			$db->query("UPDATE vklist_acc SET last_mid=$last_mid WHERE id=$acc_id",0);
			print "last_mid for acc=$acc_id updated : $last_mid<br>\n";
			print "----------<br>\n";
			usleep(300000);
			//break;
		}
		//print "<script>opener.location.reload();</script>\n";
	}
	function is_in_vklist2($uid) {
		global $db;
		$db->connect("vklist2");
		$res=$db->dlookup("id","vklist2","uid='$uid'");
		$db->connect($this->database);
		return $res;
	}
	function scan__() {
		$db=new db($this->database);
		$db->db200=$this->db200;
		$db->razdel_do_not_notify=$this->razdel_do_not_notify;
		print "razdel_do_not_notify:\n";
		print_r($db->razdel_do_not_notify);
		$db->telegram_bot=$this->telegram_bot;
		$last_response="";
		
		print "New msgs scan started : $this->database <br>\n";
		$res_acc=$db->query("SELECT * FROM vklist_acc WHERE del=0 AND token!='' AND token!=0");
		$n=0;
		while($r_acc=$db->fetch_assoc($res_acc)) {
			$acc_id=$r_acc['id']; 
			$last_mid=$r_acc['last_mid']; 
			$last_mid_1=$last_mid;
			print date("d.m.Y H:i")." acc_id=$acc_id ({$r_acc['name']}) last_mid=$last_mid<br>\n";
			$vk=new vklist_api($db->dlookup("token","vklist_acc","id=$acc_id"));
			$arr=$vk->vk_messages_get_all($last_mid);
			if(isset($arr['error'])) {
				if($arr['error']['error_code']==5) {
					print "\nacc $acc_id banned : ".$arr['error']['error_code']."\n";
					if($r_acc['last_error']==0)
						$db->query("UPDATE vklist_acc SET last_error={$arr['error']['error_code']},fl_acc_allowed=0  WHERE id=$acc_id");
				}
				continue;
			}
			if($r_acc['last_error']!=0)
				$db->query("UPDATE vklist_acc SET last_error=0,fl_acc_allowed=0  WHERE id=$acc_id");
			//$db->print_r($arr);
			foreach($arr['response']['items'] AS $msg) {
				if($msg['date']==0)
					continue;
				$msg['mid']=$msg['id'];
				$msg['uid']=$msg['user_id'];
				/*if($msg['uid']=="198746774") //avsh
					continue;*/
					
				//if user in cards
				if($r=$db->fetch_assoc($db->query("SELECT id,acc_id,name,surname,razdel,dont_disp_in_new FROM cards WHERE uid='{$msg['uid']}'"))) { 
					/*if($r['acc_id']==1 && $acc_id!=1) //if last_msg from Julia do not rewrite
						continue;
					*/
					print "FOUND IN CARDS | mid={$msg['mid']} | date=".date("d.m.Y H:i",$msg['date'])." | uid={$msg['uid']}<br>\n";
					$db->query("UPDATE cards SET acc_id=$acc_id, tm_lastmsg={$msg['date']}, fl_newmsg=2 WHERE uid={$msg['uid']}");
					$db->query("INSERT INTO msgs (uid,acc_id,mid,tm,user_id,msg,outg,imp,new) VALUES ({$msg['uid']},$acc_id,{$msg['mid']},{$msg['date']},0,'".$db->escape($msg['body'])."',0,0,1)");
					print "msgs recorded<br>\n";
					if($r['razdel']==5 && 	$r['dont_disp_in_new']==0) { //Other
						//$this->notify_if_other($msg['uid']);
						//$vk->vk_msg_send("vladimir_avshtolis", "R=OTHER! New message from - ".$r['name']." ".$r['surname']." - ".$db->db200."/cp.php?view=yes&filter=new",$fake=false, $chat_id=false, $attachment=false);
					}
					$db->notify($msg['uid'],$msg['body']);
					$n++;
				} else { 
					$r=$db->fetch_assoc($db->query("SELECT * FROM vklist WHERE uid='{$msg['uid']}'"));
					if($r) { //if user in vklist
						$db->query("UPDATE vklist SET tm_msg=1 WHERE uid={$msg['uid']} AND tm_msg=0"); //stop to send ad 
						$db->query("UPDATE vklist SET tm_friends=1 WHERE uid={$msg['uid']} AND tm_friends=0"); //stop to send ad 
						$db->query("UPDATE vklist SET tm_wall=1 WHERE uid={$msg['uid']} AND tm_wall=0"); //stop to send ad 
						//add to CARDS
						$name=explode(" ",$vk->vk_get_name_by_uid($msg['uid'])); sleep(1);
						$source_id=($r['vote']!="")?"4":"3";
						//mysql_query("INSERT INTO cards (uid,name,surname,tm) VALUES ({$msg['uid']},'".$db->escape($name[0])."','".$db->escape($name[1])."',".time().")");
						$db->query("INSERT INTO cards SET 
								uid='{$msg['uid']}',
								acc_id='$acc_id',
								name='".$db->escape($name[0])."',
								surname='".$db->escape($name[1])."',
								tm='".time()."',
								tm_lastmsg={$msg['date']},
								fl_newmsg=2,
								source_id='$source_id'
								");
						print "New uid to cards added : uid={$msg['uid']}";
						
						print "FROM VKLIST | mid={$msg['mid']} | date=".date("d.m.Y H:i",$msg['date'])." | uid={$msg['uid']}<br>\n";
						//$db->query("UPDATE cards SET acc_id=$acc_id,tm_lastmsg={$msg['date']}, fl_newmsg=2 WHERE uid={$msg['uid']}");
						$db->query("INSERT INTO msgs (uid,acc_id,mid,tm,user_id,msg,outg,imp,new) VALUES ({$msg['uid']},$acc_id,{$msg['mid']},{$msg['date']},0,'".$db->escape($msg['body'])."',0,0,1)");
						print "msgs recorded<br>\n";
						//$vk->vk_ourchat_send("New message from - ".$name[0]." ".$name[1]." - ".$db->db200."/cp.php?view=yes&filter=new");
						$db->notify($msg['uid'],$msg['body']);
						$n++;
					} elseif($r_acc['fl_allow_read_from_all']==1) { //СООБЩЕНИЯ ГРУППЫ
						$name=explode(" ",$vk->vk_get_name_by_uid($msg['uid'])); sleep(1);
						$db->query("INSERT INTO cards SET 
								uid={$msg['uid']},
								acc_id='{$r_acc['id']}',
								name='".$db->escape(trim($name[0]))."',
								surname='".$db->escape(trim($name[1]))."',
								razdel=0,
								source_id=9,
								fl_newmsg=3,
								tm_lastmsg=".time().",
								tm=".time().""
								);
						print "New uid={$msg['uid']} added from {$r_acc['name']}\n";
						$db->query("INSERT INTO msgs (uid,acc_id,mid,tm,user_id,msg,outg,imp,new) VALUES ({$msg['uid']},{$r_acc['id']},{$msg['mid']},{$msg['date']},0,'".$db->escape($msg['body'])."',0,0,1)");
						print "msgs recorded<br>\n";
						$n++;
						$db->notify($msg['uid'],$msg['body']);
					}
				}
				if($msg['mid']>$last_mid)
					$last_mid=$msg['mid'];
			}
			print ($last_mid_1-$last_mid)." new messages from all<br>\n";
			if($last_mid>$last_mid_1) {
				$db->query("UPDATE vklist_acc SET last_mid=$last_mid WHERE id=$acc_id");
				print "last_mid for acc=$acc_id updated : $last_mid<br>\n";
			}
			print "----------<br>\n";
			usleep(300000);
		}
		if($n>0) {
			//$this->notify("vklist_msgs_scan : $n new messages in ".$db->db200."/cp.php?view=yes&filter=new");
			//$vk=new vklist_api("fb948a490bd420cffa2c9fdb8f88fed359bcc9e8da2a4c915f838143dcd393da212ec142b476ce5b6d8f2"); //vlav's token
			//$vk->vk_msg_send("vladimir_avshtolis", "vklist_msgs_scan : $n new messages in ".$db->db200."/cp.php?view=yes&filter=new",$fake=false, $chat_id=false, $attachment=false);
		}
		print "<script>opener.location.reload();</script>\n";
	}
}
class vklist_send extends db {
	var $vk;
	var $database="";
	var $domain="";
	var $hour_of_start_sending=9;
	var $hour_of_end_sending=23;
	var $interval_min=10800; //3*60*60
	var $uid_julia;
	var $sex_allowed=1; //false - any sex allowed; 1-female only; 2-male only; 0- not specified
	var $friends=array();
	var $stop_words=array();
	var $ctrl_id=0;
	var $min_age_limit=16;
	var $max_age_limit=59;
	var $allow_if_in_cards=true;
	var $time_not_early_then_for_autosend=2*60*60; //15*24*60*60;
	var $send_add_to_friends_request_allowed=false;
	
	function vklist_send_($token="") {
		$this->vk=new vklist_api($token);
	}
	function log($msg) {
		if(@$_GET['browser'])
			print "$msg<br>";
		else 
			print $msg;
	}
	function check_if_friend($uid) {
		if(sizeof($this->friends)==0) {
			if(isset($this->uid_julia)) {
				$vk=new vklist_api();
				$r=$this->get_first_working_acc();
				$vk->token=$r['token'];
				$this->friends=$vk->vk_friends_getlist_for_uid($this->uid_julia);
				$this->log("Got FRIENDS LIST for $this->uid_julia = ".sizeof($this->friends)." items\n");
			} else {
				$this->log("Error. Not set uid_julia\n"); 
				exit;
			}
		}
		if(in_array($uid,$this->friends)) { //FRIEND
			$this->query("UPDATE vklist SET tm_msg=1,tm_wall=1,tm_friends=1 WHERE uid='$uid'");
			$tm=time();
			$dt=$this->dt1($tm);
			//$this->query("INSERT INTO vklist_log (tm,dt,acc_id,group_id,uid,mode,err,response) VALUES ($tm,$dt,0,0,'$uid',1,1000,'it is a friend')");
			$this->vklist_log(0,0,$uid,1000,"it is a friend");
			$this->log( "Friend : $uid : not processed\n");
			return true;
		}
		return false;
	}
	function check_in_cards($uid) {
		if($this->num_rows($this->query("SELECT id FROM cards WHERE uid='$uid'"))>0) { //IN CARDS
			$tm=time();
			$dt=$this->dt1($tm);
			$this->query("UPDATE vklist SET tm_msg=1,tm_wall=1,tm_friends=1 WHERE uid='$uid'");
			//$this->query("INSERT INTO vklist_log (tm,dt,acc_id,group_id,uid,mode,err,response) VALUES ($tm,$dt,0,0,'$uid',1,1001,'already in cards')");
			$this->vklist_log(0,0,$uid,1001,"already in cards");
			$this->log( "In cards : $uid : not processed\n");
			return true;
		}
		return false;
	}
	function check_if_in_stopwords_list($uid) {
		if(!$this->user_chk($uid)) { //stop word list
			$this->query("UPDATE vklist SET tm_msg=1,tm_wall=1,tm_friends=1,res_msg=10 WHERE uid='$uid'");
			$tm=time();
			$dt=$this->dt1($tm);
			//$this->query("INSERT INTO vklist_log (tm,dt,acc_id,group_id,uid,mode,err,response) VALUES ($tm,$dt,0,0,'$uid',1,1010,'in stop word list')");
			$this->vklist_log(0,0,$uid,1010,"in stop word list");
			$this->log( "In stop word list : $uid : not processed\n");
			return true;
		}
		return false;
	}
	function user_chk($uid) {
		if(sizeof($this->stop_words)==0) {
			$this->stop_words=file("vklist_send.stop_words.txt");
			if(!$this->stop_words) {
				$this->log( "warning : vklist_send.stop_words.txt NOT FOUND \n");
				$this->stop_words=array();
			} else 
				$this->log( "vklist_send.stop_words.txt loaded : ".sizeof($this->stop_words)." items\n");
		}
		$url = 'https://api.vk.com/method/users.get';
		$token=$this->dlookup("token","vklist_acc","del=0 AND last_error=0");
		$flds="bdate,sex,status,city,country,photo_200,photo_100,photo_50,about,activities,career,has_photo,interests,occupation";
		$params=array("version"=>5.62,'user_ids'=>$uid,'fields'=>$flds, 'access_token'=>$token);
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params))))),true);
		if(isset($res['error'])) {
			print ( "user_chk : error getting user info\n");
			return false;
		}
		//print_r($res['response']);
		foreach($this->stop_words AS $word) {
			if(trim($word)=="")
				continue;
			if(isset($res['response'][0]['deactivated']))
				return false;
			$p1=(isset($res['response'][0]['status']))?$res['response'][0]['status']:"";
			$p2=(isset($res['response'][0]['occupation']['name']))?$res['response'][0]['occupation']['name']:"";
			$str= mb_strtolower($p1." ".$p2,"utf8");
			$word=mb_strtolower(trim($word),"utf8");
			//print $str."<br>";
			//print $word."<br>";
			if(strpos($str,$word)!==false)
				return false;
		}
		return true;
	}
	function get_acc_id($uid) {
		$uid_acc_id=$this->dlookup("acc_id","cards","uid='$uid'");
		if(!$uid_acc_id)
			$uid_acc_id=0;
		$r=$this->fetch_assoc($this->query("SELECT id FROM vklist_acc WHERE id!=$uid_acc_id AND tm_next_send_msg<".time()." AND del=0 AND fl_acc_allowed=1 AND last_error=0 AND ban_cnt<3 LIMIT 1"));
		if($r)
			return $r['id']; else return false;
	}

	function vklist_log($acc_id,$group_id,$uid,$err,$response) {
		$tm=time();
		$dt=$this->dt1($tm);
		$this->query("INSERT INTO vklist_log (tm,dt,acc_id,group_id,uid,mode,err,response,ctrl_id) VALUES ($tm,$dt,$acc_id,$group_id,$uid,1,$err,'".$this->escape($response)."',".$this->ctrl_id.")");
	}
	function send($uid,$message, $acc_id=0,$gid=0,$group_name="", $n=0) {
		$tm=time();
		$dt=$this->dt1($tm);
		$token=$this->dlookup("token","vklist_acc","id=$acc_id");
		if(!$token) {
			$this->log("Error : token=$token for acc_id=$acc_id can't receive\n");
			return false;
		}
		$vk=new vklist_api($token);
		$vk->min_age_limit=$this->min_age_limit;
		$vk->max_age_limit=$this->max_age_limit;

		$block=$vk->vk_is_user_blocked($uid,false,$this->sex_allowed);
		$this->log("Check vk_is_user_blocked = $block\n");
		if($block == -1) {
			$this->log("ERROR: ACCOUNT $acc_id RETURNED ERROR CODE: ".$vk->error_code."\n"); 
			if($this->vk->error_code==5) {
				$this->log("ERROR: ACCOUNT $acc_id IS BANNED : ".$vk->error_code."\n"); 
				//$locked_accounts[]=$acc_id;
				$this->query("UPDATE vklist_acc SET last_error='5' WHERE id=$acc_id");
			}
			return false;
		}
		//print "$block\n";
		if($block == 0) {
			/////////////////////////
			sleep(1);
			$err=0;
			//////////////////////////////////////////////////
			$err=$vk->vk_msg_send($uid, $message,$fake=false);
			sleep(rand(1,5));
			//$err=$vk->vk_msg_send("vladimir_avshtolis", $message,$fake=false);
			//////////////////////////////////////////////////

			////////////////
			//$this->query("INSERT INTO vklist_log (tm,dt,acc_id,group_id,uid,mode,err,response) VALUES ($tm,$dt,$acc_id,$gid,'$uid',1,$err,'".$vk->last_response."')");
			$this->vklist_log($acc_id,$gid,$uid,$err,$vk->last_response);
			$this->log("$n | ".date("d.m.Y H:i",time())." | acc_id=$acc_id | group=$gid $group_name | uid=$uid | $message | res=$err | interval_if_error=$this->interval_min\n");
			if($err>0) { //ERROR
				$this->log(" --- last error : ".$this->vk->last_response."\n");
				if($err==10) { //internal server error
					return false;
				}
				if($err==5) {//token
					$this->log("ERROR: ACCOUNT $acc_id IS BANNED : ".$this->vk->error_code."\n"); 
					$this->query("UPDATE vklist_acc SET last_error='5' WHERE id=$acc_id");
				} elseif($err==7) {
					$this->log("ERROR: ACCOUNT $acc_id ACHIVED DAILY LIMIT : ".$this->vk->error_code."\n"); 
				} else 
					$this->log("ERROR: ACCOUNT $acc_id ERROR CODE : ".$this->vk->error_code."\n"); 
				$tm_next_send_msg=time()+($this->interval_min);
				$dt_next_send_msg=date("d.m.Y H:i",$tm_next_send_msg);
				$this->query("UPDATE vklist_acc SET tm_next_send_msg='$tm_next_send_msg' WHERE id=$acc_id");
				$this->vklist_log($acc_id,$gid,$uid,$err,"ACC $acc_id is LOCKED till $dt_next_send_msg ($this->interval_min)");
				//$locked_accounts[]=$acc_id;
				$this->log("$acc_id is LOCKED until $dt_next_send_msg\n");
				//return true; //sleep(1);
			} else { //OK
				$this->query("UPDATE vklist SET tm_msg=$tm,res_msg=$err,blocked=0 WHERE uid='$uid'");
				$this->query("INSERT INTO msgs SET tm=".time().",uid=$uid,acc_id=$acc_id,mid=0,user_id=0,msg='".$this->escape($message)."',outg=1,imp=3");
			}
		} elseif($block==1) {	//blocked but it is possible to add to friends
			$this->query("UPDATE vklist SET tm_msg=1,res_msg=$block,blocked=$block WHERE uid=$uid");
			if($this->send_add_to_friends_request_allowed) {
				//$this->query("INSERT INTO vklist_log (tm,dt,acc_id,group_id,uid,mode,err,response) VALUES ($tm,$dt,$acc_id,$gid,'$uid',1,$block,'User account is blocked=$block')");
				$this->vklist_log($acc_id,$gid,$uid,$block,"User account is blocked but it is possible to add to friends - REQUEST SENT");
				$this->log("$n | ".date("d.m.Y H:i")." | acc_id=$acc_id | group=$gid $group_name | uid=$uid | res=$block | blocked but it is possible to add to friends - REQUEST SENT\n");
				if($vk->vk_friends_add($uid , $message)!=0) {
						$this->query("UPDATE vklist_acc SET fr_capcha_uid=$uid WHERE id=".$acc_id);
						$this->query("UPDATE cards SET request_to_friends_sent=1,acc_id=$acc_id WHERE uid=$uid");
						$this->log( "add_to_friends : CAPTCHA NEEDED acc_id=$acc_id  uid=$uid \n");
				}
			} else {
				$this->vklist_log($acc_id,$gid,$uid,$block,"User account is blocked but it is possible to add to friends - OPTION IS SWITCHED OFF");
				$this->log("$n | ".date("d.m.Y H:i")." | acc_id=$acc_id | group=$gid $group_name | uid=$uid | res=$block | blocked but it is possible to add to friends - OPTION send_add_to_friends_request_allowed IS SWITCHED OFF\n");
			}
		} elseif($block==2) {	//blocked by user completely
			$this->query("UPDATE vklist SET tm_msg=1,res_msg=$block,blocked=$block WHERE uid=$uid");
			//$this->query("INSERT INTO vklist_log (tm,dt,acc_id,group_id,uid,mode,err,response) VALUES ($tm,$dt,$acc_id,$gid,'$uid',1,$block,'User account is blocked=$block')");
			$this->vklist_log($acc_id,$gid,$uid,$block,"User account is blocked completely");
			$this->log("$n | ".date("d.m.Y H:i")." | acc_id=$acc_id | group=$gid $group_name | uid=$uid | res=$block | blocked by user completely\n");
		} elseif($block==3) {	//banned by user
			$this->query("UPDATE vklist SET tm_msg=1,res_msg=$block,blocked=$block WHERE uid=$uid");
			//$this->query("INSERT INTO vklist_log (tm,dt,acc_id,group_id,uid,mode,err,response) VALUES ($tm,$dt,$acc_id,$gid,'$uid',1,$block,'User account is blocked=$block')");
			$this->vklist_log($acc_id,$gid,$uid,$block,"User account is banned by user");
			$this->log("$n | ".date("d.m.Y H:i")." | acc_id=$acc_id | group=$gid $group_name | uid=$uid | res=$block | banned by user\n");
		} elseif($block==4) {	//sex=MAN
			$this->query("UPDATE vklist SET tm_msg=1,res_msg=$block,blocked=$block WHERE uid=$uid");
			//$this->query("INSERT INTO vklist_log (tm,dt,acc_id,group_id,uid,mode,err,response) VALUES ($tm,$dt,$acc_id,$gid,'$uid',1,$block,'User account is blocked=$block')");
			$this->vklist_log($acc_id,$gid,$uid,$block,"User account is blocked because SEX != ".$this->sex_allowed);
			$this->log("$n | ".date("d.m.Y H:i")." | acc_id=$acc_id | group=$gid $group_name | uid=$uid | res=$block | SEX != ".$this->sex_allowed."\n");
		} elseif($block==5) {	// too young
			$this->query("UPDATE vklist SET tm_msg=1,res_msg=$block,blocked=$block WHERE uid=$uid");
			//$this->query("INSERT INTO vklist_log (tm,dt,acc_id,group_id,uid,mode,err,response) VALUES ($tm,$dt,$acc_id,$gid,'$uid',1,$block,'User account is blocked=$block')");
			$this->vklist_log($acc_id,$gid,$uid,$block,"User account is blocked because too young");
			$this->log("$n | ".date("d.m.Y H:i")." | acc_id=$acc_id | group=$gid $group_name | uid=$uid | res=$block | too young\n");
		}
		return true;
	}
	function prepare_msg($msg) {
		//for converting of msg see - $vk->vk_msg_send($uid, $message,$fake=false);
		if(trim($msg)=="")
			return false;
		return $msg;
	}
	function stop_send($ctrl_id) {
			$this->query("UPDATE vklist_ctrl SET tm_finished=".time()." WHERE id=".$ctrl_id);
			$this->query("UPDATE vklist_ctrl SET tm_finished=".time().",stop=1 WHERE tm_finished=0");
			$this->query("UPDATE vklist_groups SET fl_autosend=0 WHERE 1");
	}
	function sendlist($cnt_limit, $group_id) {
		$tm=time()-(1*60);
		if($this->dlookup("busy","vklist_ctrl","busy>$tm")) {
			$this->log("busy - exited\n");
			return false;
		}
		if($this->dlookup("stop","vklist_ctrl","id=".$this->ctrl_id)!=0) {
			$this->stop_send($this->ctrl_id);
			$this->vklist_log(0,0,0,2000,"STOPPED BY USER");
			$this->log("STOPPED BY USER\n");
			return false;
		}
		$res=$this->query("SELECT *
				FROM vklist JOIN vklist_groups ON vklist_groups.id=group_id 
				WHERE del=0 AND fl_send_msg=1 AND tm_msg=0 AND blocked=0 AND group_id=$group_id 
				LIMIT $cnt_limit");
		$this->log("cnt_limit=$cnt_limit\n");
		$this->log( "Got records : ".$this->num_rows($res)."\n");
		if($this->num_rows($res)==0) {
			$this->query("UPDATE vklist_ctrl SET tm_finished=".time()." WHERE id=".$this->ctrl_id);
			$this->log("gid=$group_id stopped because no records for sending\n");
		}


		$err=0; $count_err=0; 
		$n=1;
		while($r=mysql_fetch_assoc($res)) {

			if($this->dlookup("stop","vklist_ctrl","id=".$this->ctrl_id)!=0) {
				$this->stop_send($this->ctrl_id);
				$this->vklist_log(0,0,0,2000,"STOPPED BY USER");
				$this->log("STOPPED BY USER\n");
				return false;
			}

			$uid=intval($r['uid']);
			if($r['fl_autosend']==1 && $r['tm_cr']<(time()-$this->time_not_early_then_for_autosend)) {
				//$this->stop_send($this->ctrl_id);
				$this->log("uid=$uid is passed because older then $this->time_not_early_then_for_autosend sec \n");
				//$this->vklist_log(0,$group_id,$uid,1100,"passed because older then 1 hour");
				sleep(2);
				continue;
			}

			$message=$this->prepare_msg($r['msg']);
			if(!$message) {
				$this->log("Error : empty message in group : ".$r['group_name']." Exiting\n\n");
				continue;
				//exit;
			}
			$this->query("UPDATE vklist_ctrl SET tm_finished=".time().",busy=".time()." WHERE id=".$this->ctrl_id);
			//exit;
			//$uid=198746774; /////////////////////////
			$this->log("\nUID = $uid\n");
			
			if($this->check_if_friend($uid))
				continue;
				
			if(!$this->allow_if_in_cards) {
				if($this->check_in_cards($uid))
					continue;
			}
			if($this->check_if_in_stopwords_list($uid))
				continue;
			
			$acc_id=$this->get_acc_id($uid);
			if(!$acc_id) {
				$this->log("NOT FOUND WORKING ACCOUNTS - EXITING\n\n\n"); exit;
				$this->vklist_log(0,0,0,1005,"NOT FOUND WORKING ACCOUNTS - EXITING");
			}
			$this->send($uid,$message, $acc_id, $gid=$group_id,$group_name=$r['group_name'],$n);
			sleep(rand(5,15));
			$n++;
			if($n>=$cnt_limit) {
				$this->log("CNT_LIMIT=$cnt_limit reached. Exiting.\n");
				break;
			}
		}
	}
	function auto_send() {
		if($this->database != "vkt_japonika"
			&& $this->database != "vktrade"
			 && $this->database != "izba_top"
			 )
			return false;
		$res=$this->query("SELECT *, vklist_groups.id AS id
						FROM vklist_groups JOIN vklist ON vklist_groups.id=group_id
						WHERE fl_autosend='1' AND tm_msg=0 AND blocked=0
						GROUP BY vklist_groups.id");
		$num=$this->num_rows($res);
		$this->log("auto_send() START for $this->database  Selected for autosending $num groups\n");
		while($r=$this->fetch_assoc($res)) {
			$gid=intval($r['id']);
			print "autosend for gid=$gid \n";
			if($this->num_rows($this->query("SELECT *
					FROM vklist JOIN vklist_groups ON vklist_groups.id=group_id 
					WHERE del=0 AND fl_send_msg=1 AND tm_msg=0 AND blocked=0 AND group_id=$gid ")) ==0 ) {
						
				$this->log("for gid=$gid no records for sending. Passing.\n");
				continue;
			}
			$tm2=time()-$this->time_not_early_then_for_autosend;
			if($this->num_rows($this->query("SELECT uid FROM vklist WHERE group_id='$gid' AND tm_cr>='$tm2'",0)) ==0) {
				$this->log("for gid=$gid no uids for sending NOT OLDER then $this->time_not_early_then_for_autosend seconds. Passing.\n");
				continue;
			}
			$cnt_limit=20;
			$this->query("INSERT INTO vklist_ctrl SET user_id='0', tm=".time().", group_id='$gid',cnt=$cnt_limit");
			break;
		}
	}
	function run() {
		$r=$this->fetch_assoc($this->query("SELECT * FROM vklist_ctrl WHERE tm_finished=0 LIMIT 1"));
		if($r) {
			print "START SENDING FOR gid={$r['group_id']} cnt={$r['cnt']} \n";
			$this->ctrl_id=$r['id'];
			$this->sendlist($r['cnt'],$r['group_id']);
		} else {
			$this->auto_send();
		}
	}
	function vklist_send_cp() {
		global $VK_OWN_UID;
		print "<h2>Управление рассылкой</h2>";
		$allowed=2;
		$r=$this->fetch_assoc($this->query("SELECT COUNT(id) AS cnt FROM vklist_acc WHERE tm_next_send_msg<".time()." AND del=0 AND fl_acc_allowed=1"));
		$cnt_accs=$r['cnt'];
		
		if(@$_GET['do_mark_sent']) {
			if($_SESSION['access_level']>$allowed) {
				print "<div class='alert alert-warning' >Access prohibited. $allowed {$_SESSION['access_level']}</div>";
				exit;
			}
			$gid=intval($_GET['gid']);
			$this->query("UPDATE vklist SET tm_msg=1,res_msg=5 WHERE group_id='$gid'");
			print "<h2><div class='alert alert-info'>Группа очищена (все содержимое помечено, как отправленное)</div></h2>";
			print "<hr>";
		}
		if(@$_GET['mark_sent']) {
			$gid=intval($_GET['gid']);
			$group_name=$this->dlookup("group_name","vklist_groups","id='$gid'");
			print "<div class='well' ><h2><div class='alert alert-danger'>Очистить группу : $group_name ?</div></h2>
				Все содержимое группы для рассылки на данный момент будет помечено, как отправленное</div>
				<a href='?do_mark_sent=yes&gid=$gid' class='' target=''><button type='button' class='btn btn-primary'>Очистить</button></a>
				<a href='?view=yes' class='' target=''><button type='button' class='btn btn-primary'>Отменить</button></a>
				";
			print "<hr>";
		}
		if(@$_GET['do_stop']) {
			print "<div class='alert alert-danger'><h2>Остановка рассылки</h2></div>";
			$this->query("UPDATE vklist_ctrl SET stop=1 WHERE id={$_GET['ctrl_id']}");
			//exit;
		}
		if(@$_GET['do_sending']) {
			if($_SESSION['access_level']>$allowed) {
				print "<div class='alert alert-warning' >Access prohibited. $allowed {$_SESSION['access_level']}</div>";
				exit;
			}
			if($cnt_accs>0) {
				$cnt_limit=$_GET['cnt_limit'];
				if(is_numeric($cnt_limit) && $cnt_limit>0) {
					foreach($_GET['chk'] AS $gid) {
						$gid=intval($gid);
						print "gid=$gid<br>";
						$this->query("INSERT INTO vklist_ctrl SET user_id='".$_SESSION['userid_sess']."', tm=".time().", group_id='$gid',cnt=$cnt_limit");
					}
				}
				print "<script>location='?#'</script>";
			} else
				print "<div class='alert alert-warning'>Нет аккаунтов для рассылки, попробовать позже</div>";
		}
		if(@$_GET['set_auto_mode']) {
			$gid=intval($_GET['gid']);
			$fl=$this->dlookup("fl_autosend","vklist_groups","id='$gid'");
			$fl=($fl==0)?"1":"0";
			$this->query("UPDATE vklist_groups SET fl_autosend='$fl' WHERE id='$gid'",0);
			if($fl==1) {
				print "<div class='alert alert-warning' >Установлен режим авторассылки для группы ($gid)! Сообщение по этой группе будет автоматически рассылаться в непрерывном режиме (если эта опция подключена для вашей компании)</div>";
			} else {
				$r=$this->fetch_assoc($this->query("SELECT id FROM vklist_ctrl WHERE group_id='$gid' ORDER BY id DESC LIMIT 1"));
				$ctrl_id=$$r['id'];
				$this->query("UPDATE vklist_ctrl SET tm_finished='".time()."' WHERE id='$ctrl_id' ");
				print "<div class='alert alert-warning' >Снят режим авторассылки для группы ($gid) ctrl=$ctrl_id!</div>";
			}
		}
		
		$fl_sending_in_progress=false;
		$tm=time()-60;
		if($r=$this->fetch_assoc($this->query("SELECT *,vklist_ctrl.id AS ctrl_id FROM vklist_ctrl JOIN vklist_groups ON vklist_groups.id=group_id WHERE tm_finished=0 OR tm_finished>$tm",0))) {
			print "<div class='well' >";
			print "<div class='alert alert-info'><h2>Идет рассылка для {$r['group_name']}</h2></div>";
			print "<p>Лимит: {$r['cnt']}  ctrl_id={$r['ctrl_id']}</p>";
			print "<div class='well'>".nl2br($r['msg'])."</div>";
			print "<p>Аккаунтов для рассылки : $cnt_accs</p>";
			print "<button type='button' class='btn btn-primary' onclick='location.reload()'>Обновить</button>&nbsp;&nbsp;&nbsp;";
			print "<button type='button' class='btn btn-danger' onclick='location=\"?do_stop=yes&ctrl_id={$r['ctrl_id']}\"'>ОСТАНОВИТЬ ВСЕ РАССЫЛКИ</button>";
			if($r['stop']!=0)
				print "<div class='alert alert-info'>Рассылка останавливается, подождите минуту</div>";
			print "</div>";
			//$this->print_vklist_log($r['ctrl_id']);
			//$this->print_vklist_log();
			$fl_sending_in_progress=true;
		}

		print "<form><table class='table table-striped'>";
		print "<thead><th>№</th><th>Кол-во</th><th>Группа</th><th>Сообщение</th><th>Разослать</th><th>Авто режим</th></thead><tbody>";
		$res=$this->query("SELECT group_id,group_name,COUNT(uid) AS cnt,msg,fl_autosend FROM vklist JOIN vklist_groups ON group_id=vklist_groups.id WHERE tm_msg=0 AND blocked=0 AND fl_send_msg=1 GROUP BY group_id ORDER BY tm DESC");
		$n=1; $num_all=0;
		while($r=$this->fetch_assoc($res)) {
			if(trim($r['msg']=="")) {
				$disabled="disabled";
				$r['msg']="НЕТ СООБЩЕНИЯ ДЛЯ РАССЫЛКИ ";
			} else 
				$disabled="";
			$auto_send_checked=($r['fl_autosend']==0)?"":"CHECKED";
			print "<tr>
				<td>".($n++)."</td>
				<td><a href='javascript:wopen(\"vklist_info.php?gid={$r['group_id']}\")' title='просмотреть'><span class='badge' >{$r['cnt']}</span></a>
					<span class=''>
						<a href='?mark_sent=yes&gid={$r['group_id']}' title='не рассылать и пометить отправленными'>
							<button type='button' class='btn btn-default btn-xs'>очистить</button>
						</a>
					</span>
					<span class=''>
						<a href='javascript:wopen(\"msg.php?get_from_vklist=yes&gid={$r['group_id']}\")' title='отправить вручную'>
							<button type='button' class='btn btn-default btn-xs'>Hand</button>
						</a>
					</span>
				</td>
				<td>{$r['group_name']}</td>
				<td>".nl2br(htmlspecialchars($r['msg']))."
					&nbsp;<a href='javascript:wopen(\"?vsc_edit_msg=yes&group_id={$r['group_id']}\")'><button type='button' class='btn btn-primary btn-xs'>Сообщение</button></a>
					&nbsp;<a href='javascript:wopen(\"?vsc_test=yes&group_id={$r['group_id']}\")' title='отправить тест на https:vk.com/id$VK_OWN_UID'><button type='button' class='btn btn-primary btn-xs'>Тест</button></a>
				</td>
				<td><input type='checkbox' name='chk[]' value='{$r['group_id']}' $disabled ></td>
				<td><input type='checkbox' name='chk1' $auto_send_checked onclick='location=\"?set_auto_mode=yes&gid={$r['group_id']}\"' $disabled ></td>
				</tr>";
			$num_all+=$r['cnt'];
		}
		$res=$this->query("SELECT * FROM vklist_groups WHERE del=0 AND fl_send_msg=1 ORDER BY tm DESC");
		while($r=$this->fetch_assoc($res)) {
			if($this->dlookup("id","vklist","group_id={$r['id']} AND tm_msg=0 AND blocked=0"))
				continue;
			if(trim($r['msg']=="")) {
				$disabled="disabled";
				$r['msg']="НЕТ СООБЩЕНИЯ ДЛЯ РАССЫЛКИ ";
			} else 
				$disabled="";
			$auto_send_checked=($r['fl_autosend']==0)?"":"CHECKED";
			print "<tr>
				<td>".($n++)."</td>
				<td><span class='badge' >0</span></td>
				<td>{$r['group_name']}</td>
				<td>".nl2br(htmlspecialchars($r['msg']))."
					&nbsp;<a href='javascript:wopen(\"?vsc_edit_msg=yes&group_id={$r['id']}\")'><button type='button' class='btn btn-primary btn-xs'>Сообщение</button></a>
					&nbsp;<a href='javascript:wopen(\"?vsc_test=yes&group_id={$r['id']}\")' title='отправить тест на https:vk.com/id$VK_OWN_UID'><button type='button' class='btn btn-primary btn-xs'>Тест</button></a>
				</td>
				<td><input type='checkbox' name='chk[]' value='{$r['id']}' DISABLED ></td>
				<td><input type='checkbox' name='chk1' $auto_send_checked onclick='location=\"?set_auto_mode=yes&gid={$r['id']}\"' $disabled ></td>
				</tr>";
		}
		
		print "</tbody></table>";

		if(!$fl_sending_in_progress) {
			print "<p class='form-control-static'>Всего : $num_all</p>";
			print "<p>Аккаунтов для рассылки : $cnt_accs</p>";
			print "<div class='form-group'><label for='cnt_limit'>Разослать сообщений</label><input style='width:50px;' class='form-control' type='text' id='cnt_limit'  name='cnt_limit' value='20'></div><br>";
			print "<button type='submit' class='btn btn-primary' name='do_sending' value='yes'>Sending</button>";
		}
		print "</form><br><br>";


		print "<div class='alert alert-info'><h3>Последние 50 операций рассылки</h3></div>";
		$this->print_vklist_log();
//print "HERE_";
		
	}
	function print_vklist_log($ctrl_id=false) {
		print "<table class='table table-striped table-hover' >";
		print "<thead><th>#</th><th>Time</th><th>UID</th><th>ACC_ID</th><th>GROUP_ID</th><th>RES</th><th>DESCR</th></thead>";
		print "<tbody>";
		if($ctrl_id)
			$res=$this->query("SELECT * FROM vklist_log WHERE ctrl_id=$ctrl_id ORDER BY tm DESC");
		else
			$res=$this->query("SELECT * FROM vklist_log WHERE 1 ORDER BY tm DESC LIMIT 50");
		$n=1;
		while($r=$this->fetch_assoc($res)) {
			print "<tr><td>".($n++)."</td><td>".date("d.m.Y H:i:s",$r['tm'])."</td><td><a href='https://vk.com/id{$r['uid']}' class='' target='_blank'>{$r['uid']}</a></td><td>{$r['acc_id']}</td><td>{$r['group_id']}</td><td>{$r['err']}</td><td>{$r['response']}</td></tr>";
		}
		print "</tbody></table>";
	}
}

class vklist_group_chk {
	var $friends_uid="70412844";
	var $VK_GROUP_ADDED="2"; //vklist entry for vk_group_added
	var $database;
	var $delay_if_notif=604800; //7x24x60x60 - update vklist for sending if last sent was before
	var $add_to_vklist_if_from_spb_only=true;
	var $add_if_city_only=false; //VK city_id
	var $add_if_country_only=false; //VK country_id
	var $add_if_sex_only=false; //'M' or 'F'
	var $add_if_city_or_country_not_specified=true; //
	var $target_group_id=false;
	var $mode=0; //0- add to vklist, 1- to cards
	function group_chk($gid) {
		//$gid="116176094";
		//~ if($gid!=32652526)
			//~ return;
		$db=new db($this->database);
		$vk=new vklist_api();
		$r=$db->get_first_working_acc();
		print "get_first_working_acc : acc_id={$r['id']} {$r['name']}\n";
		$vk->token=$vk->tokens['vlav'];
		$frnds=$vk->vk_friends_getlist_for_uid($this->friends_uid);
		print("vk_friends_getlist_for_uid : friends_uid=$this->friends_uid : count=".sizeof($frnds)."\n");
		$members=$vk->vk_group_getmembers($gid,$cnt=1000,$limit=10000);
		print "vk_group_getmembers : gid=$gid : count=".sizeof($members)."\n";
		if($vk->error_code) {
			print "vk_group_getmembers : error : ".$vk->error_code."\n";
			print $vk->last_response."\n\n";
			exit;
		}
		//print_r($members);
		$info=$vk->vk_group_getinfo($gid);
		if($vk->error_code) {
			print "vk_group_getinfo : error : ".$this->error_code."\n";
			exit;
		}
		//print_r($info);
		print "{$info['screen_name']} / {$info['name']} / {$info['city']} / {$info['country']}\n";
		$n=0;
		print "members=".sizeof($members)."\n"; 
		if($db->num_rows($db->query("SELECT id FROM vklist_scan_groups WHERE 1"))==0) {
			$this->mode=0;
			print "MODE switched to 0 because it is FIRST RUN\n";
		}
		//exit;
		$n=0;
		foreach($members AS $r) {
			$uid=$r['id'];
			if(isset($r['deactivated'])) {
				print "$uid -passed - deactivated\n";
				continue;
			}
			if($db->num_rows($db->query("SELECT uid FROM vklist_scan_groups WHERE uid=$uid AND gid=$gid"))>0) {
				print "$uid -passed - ALREADY in vklist_scan_groups\n";
				continue;
			}
			$bdate=(isset($r['bdate']))?$r['bdate']:"";
			if(!empty($bdate)) {
				$b=explode(".",$bdate);
				if(sizeof($b)==3) {
					$age=intval(date("Y")-$b[2]);
				} else $age=0;
			} else $age=0;
			$uid=$r['id'];
			$first_name=$r['first_name']; 
			$last_name=$r['last_name'];
			if(!$db->dlookup("id","cards","uid='$uid' ")) {
				$db->query("INSERT INTO cards SET 
						uid='$uid',
						acc_id=2,
						name='".$db->escape($first_name)."',
						surname='".$db->escape($last_name)."',
						razdel=4,
						source_id=5,
						fl_newmsg=0,
						tm_lastmsg=".time().",
						tm=".time()."
						");
				$db->save_comm($uid,0,"Добавлен в базу, потому что вступил в группу",5);
				print " - ADDED TO CARDS\n";
			}
			print "$uid age=$age\n";
			if($age>10 AND $age<90) {
				$db->query("UPDATE cards SET age='$age' WHERE uid='$uid' ");
			}

			$bdate=$db->vk2bdate($bdate);
			if(intval($bdate)!=0) {
				$db->query("UPDATE cards SET birthday='$bdate' WHERE uid='$uid' ");
			}
			$db->query("INSERT INTO vklist_scan_groups SET
								gid='$gid',
								uid='$uid',
								tm='".time()."'
								");

			$n++;
			continue;
			
			//print "HERE_".$this->add_to_vklist_if_from_spb_only; exit;
			if($this->add_to_vklist_if_from_spb_only) {
				$this->add_if_country_only=1;
				$this->add_if_city_only=2;
				/*if($r['country']!=0 && $r['country']!=1) {
					print "passed because country={$r['country']}\n";
					continue;
				}
				if($r['city']!=0 && $r['city']!=2) {
					print "passed because city={$r['city']}\n";
					continue;
				}*/
			}
			if(!isset($r['city'])) {
				$r['city']['title']="n/a";
				$r['city']['id']=0;
			}
			if(!isset($r['country'])) {
				$r['country']['title']="n/a";
				$r['country']['id']=0;
			}
			print "$n UID=$uid ";
			
			$first_name=$r['first_name']; 
			$last_name=$r['last_name'];
			if($first_name=="")
				$first_name="n/a";
			if($last_name=="")
				$last_name="n/a";
			$name=$first_name." ".$last_name;
			$city=$r['city']['title'];
			$country=$r['country']['title'];
			$sex=($r['sex']==1)?"F":"M";
			
			print "$name country=$country ({$r['country']['id']}) city=$city ({$r['city']['id']}) sex=$sex ";
			
			if($this->add_if_country_only) {
				if(isset($r['country']) && @$r['country']['id']>0) {
					if( $r['country']['id']!=$this->add_if_country_only) {
						print "passed because country={$r['country']['id']} (allowed is add_if_country_only=$this->add_if_country_only)\n";
						continue;
					}
				} elseif(!$this->add_if_city_or_country_not_specified) {
					print "passed because country=$country (add_if_city_or_country_not_specified==FALSE)\n";
					continue;
				}
			}
			if($this->add_if_city_only) {
				if(isset($r['city']['id']) && @$r['city']['id']>0) {
					if($r['city']['id']!=$this->add_if_city_only) {
						print "passed because city={$r['city']['id']} (allowed is add_if_city_only=$this->add_if_city_only)\n";
						continue;
					}
				} elseif(!$this->add_if_city_or_country_not_specified) {
					print "passed because city=$city (add_if_city_or_country_not_specified==FALSE)\n";
					continue;
				}
			}
			if($this->add_if_sex_only) {
				if($sex!=$this->add_if_sex_only) {
					print "passed because sex=$sex (allowed is add_if_sex_only=$this->add_if_sex_only)\n";
					continue;
				}
			}
			$bdate=(isset($r['bdate']))?$r['bdate']:"";
			if($bdate!="") {
				$b=explode(".",$bdate);
				if(sizeof($b)==3) {
					$age=date("Y")-$b[2];
				} else $age=0;
			} else $age=0;
			if(!in_array($uid,$frnds)) {
				if(!$r1=$db->fetch_assoc($db->query("SELECT * FROM cards WHERE uid=$uid"))) { //NOT IN IN CARDS
					print " - is NOT IN cards ";
					$comm="Вступил в группу {$info['name']} / $city / $country / $sex / $bdate ";
					//$comm.=($r1['comm']!="")?"\n".$r1['comm']:"";
					if($this->mode==0) {
						print "(MODE=0) ";
						$r_vk=$db->fetch_assoc($db->query("SELECT * FROM vklist WHERE uid=$uid"));
						if(!$r_vk) { //IF NOT IN VKLIST
							$db->query("INSERT INTO vklist SET uid=$uid,group_id='$this->VK_GROUP_ADDED',tm_cr=".time());
							print " - and is NOT IN vklist -ADDED TO VKLIST\n";
						} else { //IF IN VKLIST
							$db->query("UPDATE vklist SET tm_msg=0,group_id='$this->VK_GROUP_ADDED' WHERE uid=$uid");
							if($r_vk['tm_msg']>1 && $r_vk['tm_msg']<(time()-$this->delay_if_notif)) {
								$db->query("UPDATE vklist SET tm_msg=0,group_id='$this->VK_GROUP_ADDED' WHERE uid=$uid");
								print " - IN vklist, but added more than ".($this->delay_if_notif/(24*60*60))." days ago - UPDATED vklist\n";
							} else {
								print " - IN vklist - PASSED\n";
								continue;
							}
						}
					} else {
						print "(MODE=1) ";
						$acc_id=$db->get_default_acc();
						$db->query("INSERT INTO cards SET 
								uid='$uid',
								acc_id=$acc_id,
								name='".$db->escape($first_name)."',
								surname='".$db->escape($last_name)."',
								razdel=0,
								source_id=5,
								fl_newmsg=0,
								tm_lastmsg=".time().",
								tm=".time()."
								");
						$db->query("UPDATE vklist SET tm_msg=1, blocked=1 WHERE uid=$uid");
						$db->save_comm($uid,0,$comm,5);
						print " - ADDED TO CARDS and marked as new\n";
					}
				} else {
					$comm="Вступил в группу / $city / $country / $sex / $bdate";
					if($r1['fl_newmsg']!=3)
						$db->query("UPDATE cards SET fl_newmsg=0,tm_lastmsg=".time()." WHERE uid=$uid");
					$db->save_comm($uid,0, $comm,5);
					print " - IN CARDS, marked as new in cards\n";
				}
				//~ $bdate=$db->vk2bdate($bdate);
				//~ $db->query("INSERT INTO vklist_scan_groups SET
									//~ gid='$gid',
									//~ uid='$uid',
									//~ first_name='".$db->escape($first_name)."',
									//~ last_name='".$db->escape($last_name)."',
									//~ city_id='{$r['city']['id']}',
									//~ country_id='{$r['country']['id']}',
									//~ sex='$sex',
									//~ bdate='$bdate',
									//~ tm='".time()."',
									//~ d='".date("d")."',
									//~ m='".date("m")."',
									//~ y='".date("Y")."',
									//~ age='$age'
									//~ ");
				//~ print "$uid - added to vklist_scan_groups\n";
				//~ if($this->target_group_id) {
					//~ new ad_target($this->target_group_id,array($uid));
					//~ print "$uid - added retarketing entry\n";
					//~ sleep(1);
				//~ }
				//sleep(1);
				$n++;
			} else {
				print " - FRIEND passed\n";
				//$n++;
			}
			$bdate=$db->vk2bdate($bdate);
			$db->query("INSERT INTO vklist_scan_groups SET
								gid='$gid',
								uid='$uid',
								tm='".time()."'
								");
			print "$uid - added to vklist_scan_groups\n";
		}
		print "Finished for gid=$gid. Added new members: $n\n\n";
	}
}
class ad_target {
	function ad_target($target_group_id,$contacts_arr) {
		if(!$target_group_id || sizeof($contacts_arr)==0)
			return false;
		$vk=new vklist_api();
		$vk->token=$vk->tokens['vlav'];
		//$vk->ad_get_target_groups();
		$res=$vk->ad_add_target_contacts($target_group_id,$contacts_arr);
		if(!$res)
			print "ERROR ad_target returned false : $target_group_id \n";
		else
			print "ad_target : added $res to target_group_id=$target_group_id\n";
	}
}
class vklist_bdate extends db {
	function check_bdate($limit=100) {
		print "CHECK_BDATE <br>\n";
	//	print $database." <br>\n";
		$vk=new vklist_api;
		$res=$this->query("SELECT uid,birthday FROM cards WHERE birthday='' LIMIT $limit");
		$cnt=$this->num_rows($res);
		print "records for correcting: $cnt <br>\n";
		while($r=$this->fetch_assoc($res)) {
			$info=$vk->vk_get_userinfo($r['uid']);
			if($info) {
				if(!isset($info['bdate']))
					$info['bdate']="";
				$bdate=$this->vk2bdate($info['bdate']);
				print $bdate."<br> \n";
				$this->query("UPDATE cards SET birthday='$bdate' WHERE uid='{$r['uid']}'");
			}
			usleep(100000);
		}
	}
	function vklist_scan_groups_2_bdate() {
		return;
		$res=$this->query("SELECT id,bdate FROM vklist_scan_groups WHERE 1 ");
		$cnt=$this->num_rows($res);
		print "records for correcting: $cnt <br>\n";
		while($r=$this->fetch_assoc($res)) {
			if(strpos($r['bdate'],".")!==false || empty($r['bdate']) ) {
				$bdate=$this->vk2bdate($r['bdate']);
				print $r['bdate']." $bdate\n";
				$this->query("UPDATE vklist_scan_groups SET bdate='$bdate' WHERE id={$r['id']}");
			}
		}
		
	}
	function log($msg) {
		print "$msg \n";
	}
	function run($customer_id) {
		print "BDATE SCANNING <br>\n";
		$this->connect("vktrade");
		$fl_scan_grp=false;
		$r=$this->fetch_assoc($this->query("SELECT * FROM customers WHERE id='$customer_id'"));
		$bdate_mode=$r['bdate_mode'];
		$razd_arr=explode(",",$r['bdate_razdel']);
		$bdate_days_before=intval($r['bdate_days_before']);
		if($bdate_days_before>30)
			$bdate_days_before=30;
		$bdate_time=intval($r['bdate_time']);
		if(!$bdate_time || $bdate_time>23)
			$bdate_time=12;
		$bdate_grp=intval($r['bdate_grp']);
		if($bdate_grp==0) {
			print "Error: bdate_grp is not specified: $bdate_grp <br>\n";
			return false;
		}

		$this->connect($r['db']);
		$this->query("DELETE FROM vklist WHERE group_id='$bdate_grp' ");
		$q="SELECT * FROM cards WHERE (razdel=-1";
		print_r($razd_arr);
		print "<br>\n";
		print "bdate_days_before=$bdate_days_before <br>\n";
		foreach($razd_arr AS $rid) {
			if($rid==0)
				$fl_scan_grp=true;
			else
				$q.=" OR razdel='$rid'";
		}
		$q.=")";
		$bdate=$this->tm2bdate($this->bdate2tm($this->bdate_now())-($bdate_days_before*24*60*60));
		$q.=" AND (birthday='$bdate')";
	//	print $q."<br>\n";
		$res=$this->query($q);
		$cnt=$this->num_rows($res);
		print "Birthdays in CARDS in selected razdels = $cnt <br>\n";
		while($r=$this->fetch_assoc($res)) {
			$this->query("INSERT INTO vklist SET uid='{$r['uid']}',group_id='$bdate_grp',tm_cr='".time()."' ");
			$this->log("Added from CARDS: uid={$r['uid']} gid=$bdate_grp bdate=$bdate");
		}
		if($fl_scan_grp) {
			$q1="SELECT * FROM `vklist_scan_groups` WHERE bdate='$bdate'";
		//	print $q1."<br>\n";
			$res=$this->query($q1);
			$cnt=$this->num_rows($res);
			print "Birthdays in vk group members = $cnt <br>\n";
			while($r=$this->fetch_assoc($res)) {
				if(!$this->dlookup("uid","vklist","group_id='$bdate_grp' AND uid='{$r['uid']}'")) {
					$this->query("INSERT INTO vklist SET uid='{$r['uid']}',group_id='$bdate_grp',tm_cr='".time()."' ");
					$this->log("Added from vk group: uid={$r['uid']} gid=$bdate_grp bdate=$bdate");
				}
			}
		}

	}
}
?>
