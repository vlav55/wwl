<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
class partnerka extends db {
	//var $products_exclude=[];
	var $products_include_only=[1001];
	var $sids_for_cnt_reg=[12];
	var $fee=0;
	var $fee2=0;
	var $fee_hello=0;
	var $levels=1;
	function __construct($klid=false, $db) {
		$this->connect($db);
		if(!$klid)
			return;
		$this->fee=$this->dlookup("fee","users","klid='$klid'");
		$this->fee2=$this->dlookup("fee2","users","klid='$klid'");
		$this->levels=$this->dlookup("levels","users","klid='$klid'");
	}
	function first_date_of_prev_month($month,$year) {
		$last_month=($month==1)?12:$month-1;
		$year1=($last_month==12)?$year-1:$year;
		return "01.$last_month.$year1";
	}
	function cnt_reg($klid,$tm1,$tm2) {
		$where_sids="";
		foreach($this->sids_for_cnt_reg AS $sid)
			$where_sids.="msgs.source_id=$sid OR ";
		$where_sids.=" 1=2"; 
		//~ $cnt= $this->fetch_assoc($this->query("SELECT COUNT(uid) AS cnt FROM (SELECT cards.uid AS uid FROM msgs JOIN cards ON cards.uid=msgs.uid WHERE cards.del=0 AND cards.id!=$klid AND  ($where_sids) AND utm_affiliate='$klid' AND msgs.tm>=$tm1 AND msgs.tm<=$tm2 GROUP BY cards.uid) AS q1"))['cnt'];
        $q="SELECT cards.id
          FROM cards
          WHERE cards.del=0 AND cards.id!='$klid' AND utm_affiliate = '$klid' AND tm_user_id BETWEEN $tm1 AND $tm2";
		$cnt=$this->num_rows($this->query($q));
          
		return $cnt;
	}
	function sum_pay($klid,$tm1,$tm2,$debug=0) { //выплачено партнеру
		$sum=$this->fetch_assoc($this->query("SELECT SUM(sum_pay) AS s FROM partnerka_pay WHERE tm>=$tm1 AND tm<=$tm2 AND klid='$klid' AND sum_pay>0"))['s'];
		if(!$sum)
			$sum=0;
		return $sum;
	}
	function sum_buy($klid,$tm1,$tm2,$debug=0) { //сумма продаж
		$sum=$this->fetch_assoc($this->query("SELECT SUM(amount) AS s FROM partnerka_op WHERE tm>=$tm1 AND tm<=$tm2 AND klid_up='$klid'"))['s'];
		if(!$sum)
			$sum=0;
		return $sum;
	}
	function sum_fee($klid,$tm1,$tm2,$debug=0) { //начислено вознаграждение
		$sum=$this->fetch_assoc($this->query("SELECT SUM(fee_sum) AS s FROM partnerka_op WHERE tm>=$tm1 AND tm<=$tm2 AND klid_up='$klid'"))['s'];
		if(!$sum)
			$sum=0;
		return $sum;
	}
	function rest_fee($klid) {
		//print $this->sum_pay($klid,0,time());
		return intval( $this->sum_fee($klid,0,time()) - $this->sum_pay($klid,0,time()) );
	}
	function last_fee($klid, $minutes_from_now=5) {
		return $this->dlast("fee_sum","partnerka_op","klid_up='$klid' AND tm>".(time()-($minutes_from_now*60)));
	}
	function pay_fee($klid,$sum,$vid,$comm) {
		if(!$this->dlookup("id","users","klid='$klid'"))
			return false;
		if(!$sum)
			return false;
		$this->query("INSERT INTO partnerka_pay SET
					klid='$klid',
					tm='".time()."',
					sum_pay='".intval($sum)."',
					vid='".intval($vid)."',
					comm='".$this->escape(mb_substr($comm,0,1024))."'
					");
		return $this->insert_id();
	}
	public $mute=false;
	function fill_level($klid,$tm1,$tm2,$level,$klid_up,$ctrl_id=0) {
		$fee=0; //($level==1)?$this->fee:$this->fee2;
		$klid_up=($level==1)?$klid:$klid_up;
		if(!$klid_up)
			return [];
		$add="1";

		//$add = ($ctrl_id>=160) ? "1" : "cards.id!=$klid";
		$add = ($ctrl_id==170) ? "1" : "cards.id!=$klid";

		//~ $q="SELECT 
					//~ avangard.id AS avangard_id,
					//~ avangard.tm AS tm,
					//~ cards.uid AS uid,
					//~ amount1,
					//~ product_id,
					//~ product.fee_1 AS fee_1,
					//~ product.fee_2 AS fee_2,
					//~ avangard.fee_1 AS fee_1_a,
					//~ avangard.fee_2 AS fee_2_a,
					//~ product.fee_cnt AS fee_cnt
					//~ FROM avangard
					//~ JOIN cards ON uid=avangard.vk_uid
					//~ JOIN product ON product.id=product_id
					//~ WHERE cards.id!=$klid AND cards.del=0
						//~ AND avangard.tm>=$tm1
						//~ AND avangard.tm<=$tm2
						//~ AND avangard.res=1
						//~ AND utm_affiliate='$klid'
						//~ AND utm_affiliate!=0
						//~ AND $add";
		$q="SELECT 
					avangard.id AS avangard_id,
					avangard.tm AS tm,
					cards.uid AS uid,
					amount1,
					product_id,
					product.fee_1 AS fee_1,
					product.fee_2 AS fee_2,
					avangard.fee_1 AS fee_1_a,
					avangard.fee_2 AS fee_2_a,
					product.fee_cnt AS fee_cnt
					FROM avangard
					JOIN cards ON uid=avangard.vk_uid
					JOIN product ON product.id=product_id
					WHERE cards.del=0
						AND avangard.tm>=$tm1
						AND avangard.tm<=$tm2
						AND avangard.res=1
						AND utm_affiliate='$klid'
						AND utm_affiliate!=0
						AND $add";
		$res=$this->query($q,0);

		$num=0;
		$ids=[];
		while($r=$this->fetch_assoc($res)) {
			
			$uid=$r['uid'];
			if(!$this->hold_chk($uid)) {
				if(!$this->mute)
					print "HOLD CHECKED FALSE for uid=$uid. PASSED \n";
				continue;
			}

			//print "uid=$uid \n";
			$fee=0;
			$sum=$r['amount1'];

			if($level==1 && $r['fee_1']>0)
				$fee=$r['fee_1'];
			if($level==2 && $r['fee_2']>0)
				$fee=$r['fee_2'];
			$fee_cnt=$r['fee_cnt'];

			if($r['fee_1_a']>0 || $r['fee_2_a']>0 ) {
				if($level==1 && $r['fee_1_a']>0)
					$fee=$r['fee_1_a'];
				if($level==2 && $r['fee_2_a']>0)
					$fee=$r['fee_2_a'];
				$fee_cnt=0;
			}

			if($fee==0)
				continue;

			$avangard_id=$r['avangard_id'];
			if($this->dlookup("id","partnerka_op","avangard_id='$avangard_id' AND level='$level'") ) {
				//print "already treated. PASSED \n";
				continue;
			}

	//~ if($uid==100675340) {
		//~ print "$klid,$tm1,$tm2,$level,$klid_up,$ctrl_id | avangard_id={$r['avangard_id']} sum=$sum fee=$fee {$r['fee_1']} {$r['fee_1_a']} {$r['product_id']} <br>";
		//~ //$this->notify_me($q);
		//~ exit;
	//~ }

			$tm=$r['tm'];
			$dt=date("d.m.Y",$tm);
			$product_id=$r['product_id'];

			if( !$r['fee_1_a'] && !$r['fee_2_a'] ) {
				if($uid && $product_id) {
					if($level==1) {
						if($r_spec=$this->fetch_assoc($this->query("SELECT partnerka_spec.fee_1 AS fee_1,partnerka_spec.fee_cnt AS fee_cnt FROM partnerka_spec
								JOIN cards ON partnerka_spec.uid=cards.uid
								WHERE cards.id='$klid' AND pid='$product_id' ")))
							$fee=$r_spec['fee_1'];
							$fee_cnt=$r_spec['fee_cnt'];
					} else {
						if($r_spec=$this->fetch_assoc($this->query("SELECT partnerka_spec.fee_2 AS fee_2,partnerka_spec.fee_cnt AS fee_cnt FROM partnerka_spec
								JOIN cards ON partnerka_spec.uid=cards.uid
								WHERE cards.id='$klid_up' AND pid='$product_id' ")))
							$fee=$r_spec['fee_2'];
							$fee_cnt=$r_spec['fee_cnt'];
					}
				}
			}

			if($fee_cnt>0) {
				if($this->num_rows($this->query("SELECT id FROM partnerka_op
													WHERE klid_up='$klid_up',klid='$klid,uid='$uid'")) > $fee_cnt)
					continue;
			}

			$fee_sum=($fee<100)?intval($sum*$fee/100):$fee;

			if(!$this->mute)
				print "ctrl_id=$ctrl_id klid_up=$klid_up klid=$klid avangard_id=$avangard_id dt=$dt
					uid=$uid pid=$product_id sum=$sum fee=$fee fee_sum=$fee_sum <br> \n";
			$num++;
			$this->query("INSERT INTO partnerka_op SET
						klid_up='$klid_up',
						klid='$klid',
						avangard_id='$avangard_id',
						uid='$uid',
						product_id='$product_id',
						amount='$sum',
						fee='$fee',
						fee_sum='$fee_sum',
						tm='$tm',
						level='$level'
						");
			$ids[]=['id'=>$this->insert_id(),
				'klid_up'      => $klid_up,
				'klid'         => $klid,
				'avangard_id'  => $avangard_id,
				'uid'          => $uid,
				'product_id'   => $product_id,
				'amount'       => $sum,
				'fee'          => $fee,
				'fee_sum'      => $fee_sum,
				'tm'           => $tm,
				'level'        => $level,
				];

			if($ctrl_id) {
				include_once "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
				$s=new vkt_send($this->database);
				$res_t=$this->query("SELECT * FROM vkt_send_1 WHERE del=0 AND sid=26",0);
				while($r_t=$this->fetch_assoc($res_t)) {
					$uid_p=$this->dlookup("uid","cards","id='$klid'");
					$s->vkt_send_task_add($ctrl_id, $tm_event=intval(time()+$r_t['tm_shift']), $vkt_send_id=$r_t['id'],$vkt_send_type=3,$uid_p);
				}
			}

			if(!$this->mute)
				print "DONE $num <br>\n";
		}
		return $ids;
	}
	function fill_level___($klid,$tm1,$tm2,$level,$klid_up) {
		$fee=($level==1)?$this->fee:$this->fee2;
		$klid_up=($level==1)?$klid:$klid_up;
		$add="(";
		foreach($this->products_include_only AS $pid)
			$add.= "product_id=$pid OR ";
		$add.="1=2)";
		//~ foreach($this->products_exclude AS $pid)
			//~ $add.="product_id!=$pid AND ";
		$res=$this->query("SELECT *,avangard.id AS avangard_id, avangard.tm AS tm
					FROM avangard JOIN cards ON uid=avangard.vk_uid
					WHERE cards.id!=$klid
						AND avangard.tm>=$tm1
						AND avangard.tm<=$tm2
						AND avangard.res=1
						AND utm_affiliate='$klid'
						AND $add",0);
		//print "fill_level klid=$klid \n";
		while($r=$this->fetch_assoc($res)) {
		//	print "HERE \n";
			$avangard_id=$r['avangard_id'];
			$uid=$r['uid'];
			$sum=$r['amount1'];
			$tm=$r['tm'];
			$dt=date("d.m.Y",$tm);
			$product_id=$r['product_id'];
			$fee_sum=intval($sum*$fee/100);
			print "$klid_up $klid $avangard_id $dt $uid $product_id $sum $fee $fee_sum ";
			if(!$this->dlookup("id","partnerka_op","avangard_id='$avangard_id' AND level='$level'") ) {
				$this->query("INSERT INTO partnerka_op SET
							klid_up='$klid_up',
							klid='$klid',
							avangard_id='$avangard_id',
							uid='$uid',
							product_id='$product_id',
							amount='$sum',
							fee='$fee',
							fee_sum='$fee_sum',
							tm='$tm',
							level='$level'
							");
				print "DONE \n";
			} else
				print "already treated. PASSED \n";
		}
	}
	function fill_op($klid,$tm1,$tm2,$ctrl_id=0) {
		//print "HERE_$klid";
		$ids=$this->fill_level($klid,$tm1,$tm2,1,0,$ctrl_id);
		$res=$this->query("SELECT cards.id AS klid2 FROM cards JOIN users ON cards.id=users.klid WHERE cards.del=0 AND utm_affiliate='$klid'",0);
		while($r=$this->fetch_assoc($res)) {
			$klid2=$r['klid2'];
		//	print "HERE_$klid <br>";
			$arr=$this->fill_level($klid2,$tm1,$tm2,2,$klid,$ctrl_id);
			//if(is_array($ids) && is_array($arr))
			$ids=array_merge($ids, $arr);
		}
		return $ids;
	}
	function get_all_partners($user_id, $users_del=0) {
		$arr=array();
		$del=($users_del==0)?"users.del=0":"1";
		$res=$this->query("SELECT *,users.id AS user_id FROM cards
						JOIN users ON klid=cards.id
						WHERE cards.user_id='$user_id' AND $del",0);
		while($r=$this->fetch_assoc($res)) {
			$arr[$r['user_id']]['klid']=$r['klid'];
			$arr[$r['user_id']]['login']=$r['username'];
			$arr[$r['user_id']]['name']=$r['real_user_name'];
			$arr[$r['user_id']]['mob']=$r['mob_search'];
			$arr[$r['user_id']]['tg']=$r['tg_nick'];
			$arr[$r['user_id']]['pact_phone']=$r['pact_phone'];
		}
		return $arr;
	}
	function is_partner($user_id,$user_id_partner) {
		$res=$this->query("SELECT *,users.id AS user_id FROM cards
						JOIN users ON klid=cards.id
						WHERE users.del=0 AND users.id='$user_id_partner' AND cards.user_id='$user_id' ");
		if($this->num_rows($res)==0)
			return false;
		return true;
	}
	function is_access_allowed($user_id,$uid) { //доступ к 1-й линии
		if($_SESSION['access_level']<=4)
			return true;
		$user_id_uid=$this->dlookup("user_id","cards","uid='$uid' ");
		if($user_id==$user_id_uid)
			return true;
		if($this->is_partner($user_id,$user_id_uid) )
			return true;
		return false;
	}
	function get_mentor($user_id) {
		$klid=$this->dlookup("klid","users","id='$user_id'");
		if(!$klid)
			return false;
		return $this->dlookup("user_id","cards","id='$klid'");
	}
	function generate_bc($klid) {
		return crc32($klid);
	}
	function partner_del($klid) {
		if(!$klid)
			return false;
		//$this->query("UPDATE users SET del=1 WHERE klid='$klid'");
		$this->query("UPDATE cards SET user_id=0,utm_affiliate=0,tm_user_id='".time()."' WHERE utm_affiliate='$klid'");
		$this->query("DELETE FROM users WHERE klid='$klid'");
		return true;
	}
	function get_access_level($klid) {
		return $this->dlookup("access_level","users","del=0 AND klid='$klid'");
	}
	function set_access_level($klid,$access_level) {
		return $this->query("UPDATE users SET access_level='$access_level' WHERE klid='$klid'");
	}
	function partner_add($klid,$email='',$client_name='',$username_pref='partner_') {
		$uid=$this->dlookup("uid","cards","id='$klid'");
		//~ if(!$uid)
			//~ return false;
		if(!$telegram_id=$this->dlookup("telegram_id","cards","id='$klid'"))
			$telegram_id=0;
		if(empty($client_name)) {
			$client_name=trim($this->dlookup("surname","cards","id='$klid'")." ".$this->dlookup("name","cards","id='$klid'"));
		}
		if(!$user_id=$this->dlookup("id","users","del=0 AND klid='$klid'") ) {
			$passw=$this->passw_gen(10);
			$email=($this->validate_email($email))?"email='".$this->escape(strtolower($email))."',":''   ;
			$bc=$this->generate_bc($klid);
			$this->query("INSERT INTO users SET
						klid='$klid',
						real_user_name='".$this->escape($client_name)."',
						$email
						passw='".md5($passw)."',
						access_level=5,
						telegram_id='$telegram_id',
						comm='".$this->escape($passw)."',
						fl_notify_if_new=1,
						fl_notify_if_other=1,
						fl_allowlogin=0,
						fee='$this->fee',
						fee2='$this->fee2',
						bc='".$this->escape($bc)."'
						");
			$user_id=$this->insert_id();

			$direct_code=$this->get_direct_code($klid);
			$this->query("UPDATE users SET direct_code='$direct_code' WHERE id='$user_id'");
			
			$username=strpos($username_pref,"cashier",0)===false ? $username_pref.$user_id : $username_pref;
			$this->query("UPDATE users SET username='".$this->escape($username)."' WHERE id='$user_id'");
			//$this->mark_new($uid,3);
			//$this->query("UPDATE cards SET razdel='3' WHERE id='$klid'");
			//$this->notify($uid,"КЛИЕНТ ПЕРЕВЕДЕН В СТАТУС ПАРТНЕРА");
			//$this->save_comm($uid,0,"ПЕРЕВЕДЕН В СТАТУС ПАРТНЕРА",15);

			//$tm_start=$this->dt1(time());
			//$this->query("INSERT INTO billing SET user_id='$user_id',tm='".$tm_start."',payed=2");
			$new=1;
			
			if($this->fee_hello && $uid) {
				$this->query("INSERT INTO partnerka_op SET
							klid_up='$klid',
							klid='$klid',
							avangard_id='0',
							uid='$uid',
							product_id='-1',
							amount='$this->fee_hello',
							fee='100',
							fee_sum='$this->fee_hello',
							tm='".time()."',
							level='1'
							");
			}
			$ctrl_id=$this->ctrl_id;
			if($ctrl_id && $uid) {
				$res_t=$this->query("SELECT * FROM vkt_send_1 WHERE del=0 AND sid=25",0);
				while($r_t=$this->fetch_assoc($res_t)) {
					$uid_p=$this->dlookup("uid","cards","id='$klid'");
					$this->vkt_send_task_add($ctrl_id, $tm_event=intval(time()+$r_t['tm_shift']), $vkt_send_id=$r_t['id'],$vkt_send_type=3,$uid_p);
				}
			} elseif($uid)
				$this->notify_me("partner_add vkt_send_task_add error: ctrl_id=0 -  $klid,$email,$client_name,");
					
		} else {
			$passw=trim($this->dlookup("comm","users","klid='$klid'"));
			$username=trim($this->dlookup("username","users","klid='$klid'"));
			$this->query("UPDATE users SET del=0,real_user_name='".$this->escape($client_name)."' WHERE klid='$klid'");
			$new=0;
		}
		return(['user_id'=>$user_id,'user'=>$username,'passw'=>$passw,'new'=>$new]);
	}
	function get_partner_cabinet($klid) {
		return $this->get_direct_code_link($klid);
	}
	function get_partner_link($klid,$t='senler',$vid=0,$land_num=1) { //0-land 1-partnerka
		global 
		$senler_gid_land,
		$senler_gid_partnerka,
		$vk_group_id,
		$land,
		$land_p;

		$bc=$this->dlookup("bc","users","klid='$klid'");
		if(!$bc)
			$bc=0;
		if($t=='senler') {
			if(!empty($senler_gid_land) && !empty($senler_gid_partnerka)  && !empty($vk_group_id)) {
				if($vid==0)
					return "https://vk.com/app5898182_-$vk_group_id#s=$senler_gid_land&utm_term=$bc";
				if($vid==1)
					return "https://vk.com/app5898182_-$vk_group_id#s=$senler_gid_partnerka&utm_term=$bc";
			} else
				return false;
		}
		if($t=='land') {
			if(!empty($land) && !empty($land_p) && $land_num==1) {
				if($vid==0)
					return $land."/?bc=$bc";
				if($vid==1)
					return $land_p."/?bc=$bc";
			} else
				return false;
		}
	}
	function users_billing_add($user_id,$vid,$credit,$debit,$comm=null) {
		$this->query("INSERT INTO users_billing SET
						user_id='$user_id',
						tm='".time()."',
						vid='".$this->escape($vid)."',
						credit='$credit',
						debit='$debit',
						comm='".$this->escape($comm)."'
					");
		return $this->insert_id();
	}
	function users_billing_rest($user_id,$vid) {
		$sum_credit=$this->fetch_assoc($this->query("SELECT SUM(credit) AS s FROM users_billing WHERE user_id='$user_id' AND vid='$vid'"))['s'];
		$sum_debit=$this->fetch_assoc($this->query("SELECT SUM(debit) AS s FROM users_billing WHERE user_id='$user_id' AND vid='$vid'"))['s'];
		return $sum_credit-$sum_debit;
	}
}


?>
