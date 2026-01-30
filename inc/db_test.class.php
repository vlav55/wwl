<?php
include_once "bs.class.php";
define("mysql_user","vlav");
define("mysql_passw","fokova#142586");
define("disp_mysql_errors","0"); //0 - do not display; 1-display just info; 2 - display with query and mysql_error()
class db {
	var $debug=0;
	var $db200="";
	var $phpver=5;
	var $acc_id_hohlova=37;
	public static  $conn=null;
	var $razdel_exclude_for_save_comm=array(2,3,8,9,11,12,7);
	var $razdel_do_not_notify=array();
	var $telegram_bot="vktrade";
	var $database;
	var $last_query;
	var $runtime_log;
	
	function __construct($database=false) {
		if($database) {
			$this->connect($database);
			if($database=='papa') {
				$this->pact_token="papa";
				$this->telegram_bot="papavdekrete";
				$this->db200="https://1-info.ru/f12/db";
			}
		} else
			$this->check_php_version();
	}
	function get_current_database() {
		$r=$this->_fetch_row($this->query("SELECT DATABASE()")); return $r['0']; 
	}
	function test_microtime($line) {
		return;
		$this->runtime_log[microtime()]=$line;
	}
	function get_user_ip()	{
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
		$ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = '0';
		
		return trim($ipaddress);
	}
	function get_city_by_geoip($ip) {
	}
	function check_is_ip_banned() {
		$ip=$this->get_user_ip();
		@$banned_ip=json_decode(file_get_contents("tmp/banned_ip"),true);
		if($banned_ip) {
			foreach($banned_ip AS $key=>$item) {
				if($item['tm']< (time()-(1*60*60)) ) {
					unset($banned_ip[$key]);
				}
				if($item['ip']==$ip) {
					print "error=6";
					exit;
				}
			}
			file_put_contents("tmp/banned_ip",json_encode($banned_ip));
			//$this->print_r($banned_ip);
		}
	}
	function ban_ip() {
		$ip=$this->get_user_ip();
		@$banned_ip=json_decode(file_get_contents("tmp/banned_ip"),true);
		$banned_ip[]=array("ip"=>$ip,"tm"=>time());
		if(!file_exists("tmp"))
			if(!mkdir("tmp"))
				$this->email($emails=array("vlav@mail.ru"), "ATT warning - possible hacking attempt err=5 chk=$chk", "ERROR - CAN NOT RECORD BANNED IP IN ".getcwd()."/tmp DIR - mkdir error\n\n", $from="vktrade@1-info.ru",$fromname="VKTRADE", $add_globals=true);
		file_put_contents("tmp/banned_ip",json_encode($banned_ip));
	}
	function before_connect() {
		$error=false;
		$chk_numeric=array('id','klid','acc_id','kredit','debit','sum','list_mode');
		$chk="";
		if(isset($_GET['uid']) ) {
			if(!empty($_GET['uid'])) {
				if(!is_numeric($_GET['uid']) ) {
					if(!$this->is_md5($_GET['uid'])) {
						$chk=$_GET['uid'];
						$error=true;
					}
				}
			}
		}
		foreach($chk_numeric AS $val) {
			if(isset($_GET[$val])) {
				if($_GET[$val]=="\$uid")
					$_GET[$val]=0;
				if(!is_numeric($_GET[$val]) ) {
					$_GET['val']=0;
					$chk=$_GET[$val];
					$error=true; 
				} else $_GET[$val]=intval($_GET[$val]);
			}
			if(isset($_POST[$val])) {
				if(!is_numeric($_POST[$val]) ) {
					$_POST['val']=0;
					$chk=$_POST[$val];
					$error=true; 
				} else $_POST[$val]=intval($_POST[$val]);
			} 
		}
		if($error) {
			print "error 5"; 
			$this->email($emails=array("vlav@mail.ru"), "warning - possible hacking attempt err=5 chk=$chk", "", $from="vktrade@1-info.ru",$fromname="VKTRADE", $add_globals=true);
			$this->ban_ip();
			exit;
		}
	}
	function check_php_version() {
		$v=explode(".",phpversion());
		//(print_r)($v);
		$this->phpver=$v[0];
		//print phpversion()."<br>";
		//print $this->phpver."<br>";
	}
	function connect($db=false) {
		$this->check_php_version();
		$this->check_is_ip_banned();
		$this->before_connect();
		$this->database=$db;
		//print "here_".$this->phpver."<br>";
		$chk_arr=array("\<\?","\?\>","\<script","\<\/script","select","update ","insert","drop ","truncate","union","delete");
		$match="#";
		foreach($chk_arr AS $item)
			$match.="($item)|";
		$match.="(drop)#si";
		foreach($_GET AS $key=>$val) {
			$res=preg_replace($match,"XXX",$val);
			$_GET[$key]=$res;
		}
		foreach($_POST AS $key=>$val) {
			$res=preg_replace($match," ",$val);
			$_POST[$key]=$res;
		}
		$glb=mb_strtolower(print_r($_REQUEST,true),"utf-8");
		//print $glb;
		foreach($chk_arr AS $chk) {
			if(strpos($glb,"mysql_error")!==false) //to pass queries errors
				break;
			if(strpos($glb,$chk)!==false) {
				$this->email($emails=array("vlav@mail.ru"), "warning - possible hacking attempt err=5 chk=$chk", "", $from="vktrade@1-info.ru",$fromname="VKTRADE", $add_globals=true);
				print "error=5";
				$this->ban_ip();
				exit;
			}
		}
		if($this->phpver<7) {
			db::$conn=mysql_connect ("localhost", mysql_user, mysql_passw); // or die ("conn :: Database connect error!");
			if(!db::$conn) {
				$cnt_errors=5;
				while( intval(mysql_errno())==2002 || intval(mysql_errno())==2014  || intval(mysql_errno())==2013) {
					sleep(3);
					db::$conn=mysql_connect ("localhost", mysql_user, mysql_passw);
					if($this->conn!==false)
						break;
					$cnt_errors--;
					if($cnt_errors==0)
						break;
					//$this->email($emails=array("vlav@mail.ru"), "mysql_error ", "mysql_error\n$qstr\n".mysql_error()." (".mysql_errno().") cnt_errors=$cnt_errors\n", $from="vktrade@1-info.ru",$fromname="VKTRADE", $add_globals=true);
				}
				if(!db::$conn) {
					print "conn :: Database connect error!";
					if( intval(mysql_errno())!=2002 && intval(mysql_errno())!=2014  && intval(mysql_errno())!=2013 )
						$this->email($emails=array("vlav@mail.ru"), "mysql_error ", "query\nmysql_error\n$qstr\n".mysql_error()." (".mysql_errno().") (final)\n", $from="vktrade@1-info.ru",$fromname="VKTRADE", $add_globals=true);
					exit;
				}
			}
			
			if($db) {
				mysql_select_db ($db, db::$conn) or die ("error select db: $db");
				/*mysql_query ("set character_set_results='cp1251'");
				mysql_query ("set collation_connection='cp1251_general_ci'");
				mysql_query("set character_set_client='cp1251'");*/
				mysql_query ("set character_set_results='utf8mb4'");
				mysql_query ("set collation_connection='utf8mb4_general_ci'");
				mysql_query("set character_set_client='utf8mb4'");
			}
		} else {
			db::$conn=mysqli_connect ("localhost", mysql_user, mysql_passw) or die ("conn :: Database connect error!");
			if($db) {
				mysqli_select_db (db::$conn,$db) or die ("error select db: $db");
				/*mysqli_query (db::$conn,"set character_set_results='cp1251'");
				mysqli_query (db::$conn,"set collation_connection='cp1251_general_ci'");
				mysqli_query(db::$conn,"set character_set_client='cp1251'");*/
				mysqli_query (db::$conn,"set character_set_results='utf8mb4'");
				mysqli_query (db::$conn,"set collation_connection='utf8mb4_general_ci'");
				mysqli_query(db::$conn,"set character_set_client='utf8mb4'");
			}
		}
	}
	function query($qstr,$print_query=0,$disp_errors=false) {
		$this->last_query=$qstr;
			//print "_v=".$this->phpver."_ <br>";
		$disp_mysql_errors=($disp_errors===false)?disp_mysql_errors:$disp_errors;
		if($this->is_localhost())
			$disp_mysql_errors=2;
		if($this->debug==0) {
			if($print_query>0)
				print "<p class='alert alert-danger'>$qstr</p>";
			if($this->phpver<7) {
				$res=mysql_query($qstr);
				if(!$res) {
					$cnt_errors=5;
					while( intval(mysql_errno())==2006 || intval(mysql_errno())==2014  || intval(mysql_errno())==2013) {
						sleep(3);
						$res=mysql_query($qstr);
						if($res!==false)
							break;
						$cnt_errors--;
						if($cnt_errors==0)
							break;
						//$this->email($emails=array("vlav@mail.ru"), "mysql_error ", "mysql_error\n$qstr\n".mysql_error()." (".mysql_errno().") cnt_errors=$cnt_errors\n", $from="vktrade@1-info.ru",$fromname="VKTRADE", $add_globals=true);
					}
					if(!$res) {
						if($disp_mysql_errors==2)
							$err="mysql_error: $qstr<br>\n".mysql_error();
						elseif($disp_mysql_errors==1)
							$err="mysql error";
						else
							$err="";
						print "<div class='alert alert-danger'>$err</div>";
					//	if( intval(mysql_errno())!=2006 && intval(mysql_errno())!=2014  && intval(mysql_errno())!=2013 )
							$this->email($emails=array("vlav@mail.ru"), "mysql_error ", "mysql_error\n$qstr\n".mysql_error()." (".mysql_errno().") (final)\n".nl2br(print_r(debug_backtrace (),true))."\n\nGLOBALS\n", $from="vktrade@1-info.ru",$fromname="VKTRADE", $add_globals=true);
						exit;
					}
				}
			} else {
				$res=mysqli_query(db::$conn,$qstr);
				if(!$res) {
					if($disp_mysql_errors==2)
						$err="mysql_error: $qstr<br>\n".mysqli_error(db::$conn);
					elseif ($disp_mysql_errors==1)
						$err="mysqli error";
					print "<div class='alert alert-danger'>$err</div>";
					exit;
				}
			}
			return $res;
		} else
			print "<p>$qstr</p>";
	}
	function fetch_assoc($res) {
		if($this->phpver<7) 
			return mysql_fetch_assoc($res); else return mysqli_fetch_assoc($res);
	}
	function fetch_row($res) {
		if($this->phpver<7) 
			return mysql_fetch_row($res); else return mysqli_fetch_row($res);
	}
	function num_rows($res) {
		if($this->phpver<7) 
			return mysql_num_rows($res); else return mysqli_num_rows($res); 
	}
	function escape($str) {
		if($this->phpver<7) 
			return mysql_real_escape_string($str); else return mysqli_real_escape_string(db::$conn,$str);
	}
	function getLastId() {
		if($this->phpver<7) 
			return mysql_insert_id(); else return mysqli_insert_id(db::$conn);
	}
	function insert_id() {
		if($this->phpver<7) 
			return mysql_insert_id(); else return mysqli_insert_id(db::$conn);
	}
	function dlookup($fld,$table,$query,$disp=0) {
		$res=$this->fetch_row($this->query("SELECT $fld FROM $table WHERE $query",$disp));
		if($res===false)
			return false; else return @$res[0];
	}
	function current_db() {
		$res=$this->query("select database()");
		return mysql_result($res,0);
	}
	function get_style_by_razdel($razdel) {
		$r=array(
		0=>"color:#5bc0de; background-color:#FFFFEE;",
		1=>"color:#FFF; background-color:#E3E32B;", //C
		2=>"color:#FFF; background-color:#008000;", //B
		3=>"color:#FFF; background-color:#FFA500;", //A
		4=>"color:white; background-color:#A52A2A;", //D
		5=>"color:#FFF; background-color:#1818A9", //E
		6=>"color:FFF; background-color:#333;", //F
		7=>"color:#666; background-color:#F2DEDE;",
		8=>"color:black; background-color:yellow;",
		9=>"color:#666; background-color:#FCF8E3;",
		10=>"color:#666; background-color:#FCF8E3;", 
		11=>"color:#666; background-color:#FCF8E3;",
		12=>"color:#666; background-color:#FCF8E3;",
		13=>"color:#666; background-color:#FCF8E3;", 
		);
		if(isset($r['razdel']))
			return $r[$razdel];
		else
			return "color:#333;background-color:#EEE";
	}
	function dt1($tm) {
		return mktime(0,0,0,date("m",$tm),date("d",$tm),date("Y",$tm));
	}
	function dt2($tm) {
		return mktime(23,59,59,date("m",$tm),date("d",$tm),date("Y",$tm));
	}
	function date2tm($str) {
		$dmy=explode(".",$str);
		$m=($dmy[1]<10)?"0".intval($dmy[1]):intval($dmy[1]);
		$d=($dmy[0]<10)?"0".intval($dmy[0]):intval($dmy[0]);
		$tm=mktime(0,0,0,$m,$dmy[0],$dmy[2]);
		$res="$d.$m.{$dmy[2]}";
		if($res!=date("d.m.Y",$tm)) {
			//print "$res \n";
			//print "date2tm:Ошибка в формате даты : <b>$str</b>. Должно быть dd.mm.YYYY";
			return false;
		}
		return $tm;
	}
	function time2tm($str) {
		$str=trim($str);
		if($str=="0")
			return 0;
		$hm=explode(":",$str);
		$tm=intval($hm[0])*60*60+intval($hm[1])*60;
		$tm1=intval($tm/(60*60));
		$tm2=intval( ($tm%(60*60))/60 );
		if($str!="$tm1:$tm2") {
			return false;
		}
		//print "$str $tm1:$tm2"; exit;
		return $tm;
	}
	function wday($tm) {
		$wday=array("ВС","ПН","ВТ","СР","ЧТ","ПТ","СБ");
		return $wday[date("w",$tm)];
	}
	function vk2bdate($vk_bdate) {
		if(empty($vk_bdate))
			return "0000";
		$arr=explode(".",$vk_bdate);
		$d=0; $m=0;
		if(sizeof($arr)>=2) {
			$d=$arr[0];	$m=$arr[1];
		if(intval($d)<10)
			$d="0".intval($d);
		if(intval($m)<10)
			$m="0".intval($m);
			return "$d$m";
		} else
			return "0000";
	}
	function bdate2tm($bdate) {
		if($bdate=="0000")
			return false;
		return mktime(0,0,0,substr($bdate,2,2),substr($bdate,0,2),0);
	}
	function tm2bdate($tm) {
		if(!$tm)
			return false;
		$d=date("d",$tm);	
		$m=date("m",$tm);	
		if(intval($d)<10)
			$d="0".intval($d);
		if(intval($m)<10)
			$m="0".intval($m);
		return "$d$m";
	}
	function bdate_now() {
		$tm=time();
		$d=date("d",$tm);	
		$m=date("m",$tm);	
		if(intval($d)<10)
			$d="0".intval($d);
		if(intval($m)<10)
			$m="0".intval($m);
		return "$d$m";
	}
	function merge_cards($uid1,$uid2,$test=false) {
		if($uid1<0 || $uid2>0)
			return false;
		$r1=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE uid='$uid1'"));
		if(!$r1)
			return false;
		$r2=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE uid='$uid2'"));
		if(!$r2)
			return false;
		if(!empty($r1['email']) && !empty($r2['email']) && trim(strtolower($r1['email']))!=trim(strtolower($r2['email'])) )
			return false;
	//	$this->email(array("vlav@mail.ru"), "MERGE UIDS uid1=$uid1  email1={$r1['email']} uid2=$uid2 email2={$r2['email']}","",	$from="noreply@yogahelpyou.com",$fromname="YOGAHELPYOU", $add_globals=true);
		if($test)
			return;
		if($r2['fl_newmsg']>$r1['fl_newmsg']) {
			$this->query("UPDATE cards SET fl_newmsg='{$r2['fl_newmsg']}',tm_lastmsg='{$r2['tm_lastmsg']}' WHERE uid='$uid1'");
		}
		if($r2['user_id']>0 && $r1['user_id']==0)
			$this->query("UPDATE cards SET user_id='{$r2['user_id']}' WHERE uid='$uid1'");
		if($r2['razdel']!=4 && $r1['razdel']==4)
			$this->query("UPDATE cards SET razdel='{$r2['razdel']}' WHERE uid='$uid1'");
		if($r1['source_id']==0 && $r2['source_id']>0)
			$this->query("UPDATE cards SET source_id='{$r2['source_id']}' WHERE uid='$uid1'");
		elseif($r1['source_id']>0 && $r2['source_id']==0)
			$this->query("UPDATE cards SET source_id='{$r1['source_id']}' WHERE uid='$uid1'");
		if(empty($r1['name']))
			$this->query("UPDATE cards SET name='".$this->escape($r2['name'])."' WHERE uid='$uid1'");
		if(empty($r1['mob']))
			$this->query("UPDATE cards SET mob='".$this->escape($r2['mob'])."' WHERE uid='$uid1'");
		if(empty($r1['mob_search']))
			$this->query("UPDATE cards SET mob_search='".$this->escape($r2['mob_search'])."' WHERE uid='$uid1'");
		if(empty($r1['email']))
			$this->query("UPDATE cards SET email='".$this->escape($r2['email'])."' WHERE uid='$uid1'");
		if(!intval($r1['pact_conversation_id']) )
			$this->query("UPDATE cards SET pact_conversation_id='".$r2['pact_conversation_id']."' WHERE uid='$uid1'");
		if(!intval($r1['insta']) )
			$this->query("UPDATE cards SET insta='".$r2['insta']."' WHERE uid='$uid1'");
		if(!intval($r1['pact_insta_cid']) )
			$this->query("UPDATE cards SET pact_insta_cid='".$r2['pact_insta_cid']."' WHERE uid='$uid1'");

		if(!intval($r1['tm_delay']) )
			$this->query("UPDATE cards SET tm_delay='".$r2['tm_delay']."' WHERE uid='$uid1'");
		if(!intval($r1['scdl_fl']) )
			$this->query("UPDATE cards SET scdl_fl='".$r2['scdl_fl']."' WHERE uid='$uid1'");
		if(!intval($r1['scdl_opt']) )
			$this->query("UPDATE cards SET scdl_opt='".$r2['scdl_opt']."' WHERE uid='$uid1'");
		if(!intval($r1['tm_schedule']) )
			$this->query("UPDATE cards SET tm_schedule='".$r2['tm_schedule']."' WHERE uid='$uid1'");
		if(!intval($r1['birthday']) )
			$this->query("UPDATE cards SET birthday='".$r2['birthday']."' WHERE uid='$uid1'");
		if(!intval($r1['age']) )
			$this->query("UPDATE cards SET age='".$r2['age']."' WHERE uid='$uid1'");
		if(!intval($r1['stage']) )
			$this->query("UPDATE cards SET stage='".$r2['stage']."' WHERE uid='$uid1'");
		if(!intval($r1['anketa']) )
			$this->query("UPDATE cards SET anketa='".$r2['anketa']."' WHERE uid='$uid1'");
		if(!intval($r1['tm_user_id']) )
			$this->query("UPDATE cards SET tm_user_id='".$r2['tm_user_id']."' WHERE uid='$uid1'");
		if(!intval($r1['got_calls']) )
			$this->query("UPDATE cards SET got_calls='".$r2['got_calls']."' WHERE uid='$uid1'");
		if(!intval($r1['utm_affiliate']) )
			$this->query("UPDATE cards SET utm_affiliate='".$r2['utm_affiliate']."' WHERE uid='$uid1'");
		if(!intval($r1['tzoffset']) )
			$this->query("UPDATE cards SET tzoffset='".$r2['tzoffset']."' WHERE uid='$uid1'");
		if(!intval($r1['tm_first_time_opened']) )
			$this->query("UPDATE cards SET tm_first_time_opened='".$r2['tm_first_time_opened']."' WHERE uid='$uid1'");
		if(!intval($r1['wa_allowed']) )
			$this->query("UPDATE cards SET wa_allowed='".$r2['wa_allowed']."' WHERE uid='$uid1'");
		if($r2['anketa']==1)
			$this->query("UPDATE cards SET anketa=1  WHERE uid='$uid1'");
		if(!empty($r2['comm']))
			$this->save_comm1($uid1,$r2['comm']);
		$this->query("UPDATE cards SET uid_md5='".$this->uid_md5($uid1)."' WHERE uid='$uid1'");
		$this->save_comm($uid1,0,"MERGE CARDS. INSERTED AND THEN DELETED uid={$r2['uid']} {$r2['name']} {$r2['mob']} {$r2['email']} user_id={$r2['user_id']}",$source_id=23,$vote_vk_uid=0,$mode=0, $force=false);
		$this->query("UPDATE msgs SET uid='$uid1'  WHERE uid='$uid2'");
		$this->query("UPDATE utm SET uid='$uid1'  WHERE uid='$uid2'");
		$this->query("UPDATE pixel SET uid='$uid1'  WHERE uid='$uid2'");
		$this->query("UPDATE quiz SET uid='$uid1'  WHERE uid='$uid2'");
		$this->query("UPDATE ppl SET uid='$uid1'  WHERE uid='$uid2'");
		$this->query("UPDATE vktrade_send_log SET uid='$uid1'  WHERE uid='$uid2'");
		$this->query("UPDATE course_access SET uid='$uid1'  WHERE uid='$uid2'");
		$this->query("UPDATE discount SET uid='$uid1'  WHERE uid='$uid2'");
		$this->query("UPDATE promocodes SET uid='$uid1'  WHERE uid='$uid2'");

		$this->query("DELETE FROM cards WHERE uid='$uid2'");
		$this->query("UPDATE avangard SET vk_uid='$uid1'  WHERE vk_uid='$uid2'");
		return true;
	}
	function get_promocode_vktrade2vk($minutes_alive=60) {
		$tm=time()-($minutes_alive*60);
		$this->query("DELETE FROM vktrade2vk WHERE tm<'$tm'");
		$code=rand(100,999);
		while($this->dlookup("promocode","vktrade2vk","promocode='$code'"))
			$code=rand(100,999);
		$this->query("INSERT INTO vktrade2vk SET promocode='$code',tm='".time()."'");
		return $code;
	}
	function get_avangard_stock($product_id) {
	}
	function users_report_day($user_id,$tm,$days,$min_dur=60+30) {
		$out="($user_id) ".$this->dlookup("real_user_name","users","id=$user_id")." ";
		for($tm=time();$tm>(time()-($days*24*60*60));$tm-=(1*24*60*60)) {
			$tm1=$this->dt1($tm);
			$tm2=$this->dt2($tm);
			$dt=date("d.m.Y",$tm);
			$out.= "$dt ";

			$res=$this->query("SELECT COUNT(uid) AS cnt FROM msgs WHERE source_id=39 AND user_id='$user_id' AND (tm>='$tm1' AND tm<='$tm2') GROUP BY uid",0);
			$all_leads=$this->num_rows($res);
			$out.="ВСЕГО ЛИДОВ: ".$all_leads." ";

			$r=$this->fetch_assoc($this->query("SELECT COUNT(uid) AS cnt FROM msgs WHERE imp!=12 AND  user_id='$user_id' AND (tm>='$tm1' AND tm<='$tm2')",0));
			$out.="контактов: {$r['cnt']} | ";

			$r=$this->fetch_assoc($this->query("SELECT COUNT(id) AS cnt FROM msgs WHERE imp!=12 AND  user_id='$user_id' AND source_id='19' AND (tm>='$tm1' AND tm<='$tm2')",0));
			$out.="ЗВОНКИ: все-{$r['cnt']} ";
			$calls=$r['cnt'];

			$r=$this->fetch_assoc($this->query("SELECT COUNT(id) AS cnt FROM msgs WHERE  imp!=12 AND user_id='$user_id' AND source_id='19' AND vote>$min_dur AND (tm>='$tm1' AND tm<='$tm2')",0));
			$calls_percent=(intval($calls))?round(intval($r['cnt'])/intval($calls)*100,0):"-";
			$out.="в тч>".($min_dur/60)." м. -{$r['cnt']} ($calls_percent%)  ";

			$r=$this->fetch_assoc($this->query("SELECT SUM(vote) AS dur FROM msgs WHERE  imp!=12 AND user_id='$user_id' AND source_id='19' AND vote>0 AND (tm>='$tm1' AND tm<='$tm2')",0));
			$min=intval($r['dur']/60);
			//~ $hour=intval($r['dur']/60/60);
			//~ $min-=$hour*60;
			$out.="Всего>0 $min"."мин | ";

			$r=$this->fetch_assoc($this->query("SELECT COUNT(id) AS cnt FROM msgs WHERE imp!=12 AND  user_id='$user_id' AND outg=1 AND (tm>='$tm1' AND tm<='$tm2')",0));
			$out.="СООБЩ: все-{$r['cnt']} ";
			$num=$this->num_rows($this->query("SELECT COUNT(uid) AS cnt FROM msgs WHERE imp!=12 AND  user_id='$user_id' AND outg=1 AND (tm>='$tm1' AND tm<='$tm2') GROUP BY uid",0));
			$out.="по клиентам-$num ";

			//$non_checked=$db->dlookup("uid","cards","tm_first_time_opened=0");
			$out.="non checked: ".$this->fetch_assoc($this->query("SELECT COUNT(uid) AS cnt FROM cards WHERE tm>=$tm1 AND tm<=$tm2 AND user_id='$user_id' AND  tm_first_time_opened=0",0))['cnt']." ($all_leads)";

			$out.= "\n";
		}
		return $out;
	}
	function validate_name($str) { //Возвращает имя с большой буквы или ложь
		$str=trim($str);
		return $this->dlookup("name","russian_names","name='$str'");
	}

	function validate_email($email) {
		 return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	function validate_phone($mob) {
		$res=str_replace([' ', '-', '(', ')','+','.'], '', $mob);
		if(strlen($res)<=9)
			return false;
		return is_numeric($res);
	}
	function mb1251 ($str) {
		return mb_convert_encoding($str,"cp1251","utf8");
	}
	function mb_utf8 ($str) {
		return mb_convert_encoding($str,"utf8", "cp1251");
	}
	function sum2int($str) {
		if(is_numeric($str)) {
			if($str<=0) {
				print "<div class='alert alert-danger'>Ошибка: сумма <b>$str</b> не может быть нулем</div>";
				$err=true;
			}
			return (int)$str;
		} else {
			print "<div class='alert alert-danger'>Ошибка: сумма <b>$str</b> не цифровое значение</div>";
			return false;
		}
	}
	function sum2int_0($str) {
		if(is_numeric($str)) {
			return (int)$str;
		} else {
			print "<div class='alert alert-danger'>Ошибка: сумма <b>$str</b> не цифровое значение</div>";
			return false;
		}
	}
	function get_notes($uid) {
		$res=$this->query("SELECT *,msgs.tm AS tm FROM msgs JOIN users ON users.id=msgs.user_id WHERE msgs.uid=$uid AND outg=2 ORDER BY msgs.tm ASC");
		$arr=array();
		while($r=$this->fetch_assoc($res)) {
			$arr[]=array("note"=>$r['msg'],"tm"=>$r['tm'],"user_name"=>$r['username']);
		}
		return $arr;
	}
	function mark_new($uid,$fl=1) {
		if($fl>0) {
			if($this->dlookup("fl_newmsg","cards","uid='$uid'") <intval($fl) )
				$this->query("UPDATE cards SET fl_newmsg='$fl', tm_lastmsg='".time()."' WHERE uid='$uid'");
		} else
			$this->query("UPDATE cards SET fl_newmsg=0, tm_lastmsg='".time()."' WHERE uid='$uid'");
	}
	function save_comm1($uid,$comm) {
		$comm_old=$this->dlookup("comm1","cards","uid=$uid");
		if($comm_old===false)
			return false;
		if(strpos($comm_old,$comm)!==false)
			return true;
		$comm=(trim($comm_old)!="")?$comm_old."\n---------\n".$comm:$comm;
		$this->query("UPDATE cards SET tm_lastmsg='".time()."',comm1='".$this->escape($comm)."' WHERE uid=$uid",0);
		return true;
	}
	function save_comm($uid,$user_id,$comm,$source_id=0,$vote_vk_uid=0,$mode=0, $force=false) { //mode=1 - shift down mode=0 - replace FORCE=save in cards
		if(!intval($uid))
			return false;
		if($this->dlookup("id","msgs","uid='$uid' AND tm=".time())) //pass 1 second make able display it in msgs.class
			sleep(1);
		if($mode==1) {
			$old_comm=$this->dlookup("comm","cards","uid=$uid");
			//$comm=date("d/m H:i")." >> ".$comm."\n".$old_comm;
			$comm=$comm."\n".$old_comm;
		}
		$razdel_exclude=$this->razdel_exclude_for_save_comm;
		if(trim($comm)!="")
			$dt="(".date("d.m.Y").") "; else $dt="";
		if(trim($comm)=="")
			return false;
		$dt="";
		if(!$force) {
			if(!in_array($this->dlookup("razdel","cards","uid=$uid"),$razdel_exclude)) {
				//$this->query("UPDATE cards SET comm='$dt".$this->escape($comm)."' WHERE uid=$uid");
			} 
		} else
			$this->query("UPDATE cards SET tm_lastmsg='".time()."',comm='$dt".$this->escape($comm)."' WHERE uid=$uid",0);
		$imp=($source_id>0)?"10":"12";
		$this->query("INSERT INTO msgs SET 
				uid='$uid',
				acc_id=0,
				mid=0,
				tm=".time().",
				user_id='$user_id',
				msg='".$this->escape($comm)."',
				outg=2,
				imp=$imp,
				vote='$vote_vk_uid',
				source_id='$source_id'",0);
		if($source_id>0 && $this->dlookup("use_in_cards","sources","id='$source_id'"))
			$this->query("UPDATE cards SET source_id='$source_id' WHERE uid=$uid");
		return $this->insert_id(); 
	}
	function print_r($arr) {
		print "<pre>";
		print_r($arr);
		print "</pre>";
		return;
	}
	function get_source_priority($uid) {
		$r=$this->fetch_assoc($this->query("SELECT priority FROM sources JOIN cards ON source_id=sources.id WHERE uid=$uid"));
		if(!$r)
			return false;
		return($r['priority']);
	}
	function get_source_id_by_priority($uid,$new_source_id) {
		$s_priority=$this->dlookup("priority","sources","id=$new_source_id");
		$s_priority_orig=$this->get_source_priority($uid);
		$source_id_orig=$this->dlookup("source_id","cards","uid=$uid");
		return ($s_priority_orig > $s_priority)?$source_id_orig:$new_source_id;
	}
	function in_cards($uid) {
		return $this->fetch_assoc($this->query("SELECT * FROM cards WHERE uid='$uid'"));
	}
	function get_default_acc() {
		return 0;
		$acc_id=$this->dlookup("id","vklist_acc","del=0 AND ban_cnt=0");
		if($acc_id)
			return $acc_id; else return 1;
	}
	
	function vk_auth_javascript() {
		?>
		<script src="http://vk.com/js/api/openapi.js" type="text/javascript"></script>
		<script type="text/javascript">
		VK.init({
			apiId: (5956978)
		});
		function authInfo(response) {
			if (response.session) {
			var id = response.session.mid;
			}
			VK.Api.call('users.get', {uids: id, fields: 'contacts'}, function(r) {
			if (r.response) {
				var first_name="";
				var last_name="";
				var mob="";
				if (r.response[0].first_name) {
					first_name=r.response[0].first_name;
					last_name=r.response[0].last_name;
				}
				if (r.response[0].mobile_phone) {
					mob=r.response[0].mobile_phone;
				} else if (r.response[0].home_phone) {
					mob=r.response[0].home_phone;
				}
				console.log('uid='+id+'&first_name='+first_name+'&last_name='+last_name+'&mob='+mob);
				location='?uid='+id+'&first_name='+first_name+'&last_name='+last_name+'&mob='+mob;
				/*
				$.ajax({
					type:'GET',
					url:'jquery.php',
					data:'uid='+id+'&first_name='+first_name+'&last_name='+last_name+'&mob='+mob,
					success: function(data){
						//$('#vk_request').html("<div class='alert alert-success'><h3>Спасибо. Мы с вами свяжемся через вк в ближайшее время!</h3></div>");
						location.reload();
					}				
				});
				*/
			}
			});
		
		}
				//    VK.Auth.getLoginStatus(authInfo);
		</script>
		<?
	}
	function vk_auth_button($txt='Зайти через ВК',$type='primary') {
		return "<button type='button' class='btn btn-$type' onclick='VK.Auth.login(authInfo);return(false);'>$txt</button>";
	}
	
	function get_unicum_uid() {
		$last_uid=$this->dlookup("uid","last_uid","id=0");
		if(!$last_uid) {
			$last_uid=-1000;
			$this->query("INSERT INTO last_uid SET id=0,uid='$last_uid'");
		}
		$uid=intval($last_uid)-1;
		
		$this->query("UPDATE last_uid SET uid='$uid' WHERE id=0");
		return $uid;
		
		$r=$this->fetch_assoc($this->query("SELECT MIN(uid) AS min_uid FROM cards WHERE 1"));
		if($r['min_uid']>0)
			$r['min_uid']=-100;
		return intval($r['min_uid'])-1;
	}
	function passw_gen($len=10) {
		$chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
		$cnt=strlen($chars)-1;
		$p="";
		while($len--)
			$p.=$chars[rand(0,$cnt)];
		return $p;
	}
	function check_mob($mob) {
		$res=str_replace([' ', '-', '(', ')','+','.'], '', $mob);
		if(strlen($res)==10)
			return "7".$res;
		if(strlen($res)<10)
			return $res;
		if(strlen($res)==11 && substr($res,0,1)=='8')
			return "7".substr($res, 1, strlen($res)-1);
		return $res;
	}
	function email($emails=array(), $subj, $body, $from="vktrade@1-info.ru",$fromname="VKTRADE", $add_globals=false) {	
			include_once('/var/www/vlav/data/www/wwl/inc/phpMailer/class.phpmailer.php');
			$mail= new PHPMailer();
			$mail->IsSMTP(); // telling the class to use SMTP
			$mail->Host="localhost"; //"1-info.ru"; // SMTP server
			$mail->ContentType="text/html";
			$mail->CharSet="utf-8";
			$mail->AltBody="";
			$mail->From=$from;
			$mail->FromName=$fromname;
			foreach($emails AS $email)
				$mail->AddAddress($email, "");
			$mail->Subject='=?utf-8?B?'.base64_encode($subj).'?=';
			
			if($add_globals) {
				/*$globals="<pre>".
					"GLOBALS\n".print_r($GLOBALS,true)."\n<hr>\n".
					"_SERVER\n".print_r($_SERVER,true)."\n<hr>\n".
					"_SESSION\n".print_r($_SESSION,true)."\n<hr>\n".
					"_COOKIE\n".print_r($_COOKIE,true)."\n<hr>\n".
					"_ENV\n".print_r($_ENV,true)."\n<hr>\n".
					"</pre>";*/
				$globals="<pre>".print_r($GLOBALS,true)."</pre>";
			} else
				$globals="";
			$mail->MsgHTML("<html><head></head></html><body>$body\n\n$globals\n"."</body></html>");
			return $mail->Send();
	}
	function here($mess="",$exit=false) {
		if(@$_SESSION['username']=='vlav' || !isset($_SESSION['username'])) {
			print "<div class='alert alert-danger' >HERE_$mess</div>\n";
			if($exit)
				exit;
		}
	}
	function uid_md5($utm_affiliate) {
		return md5($utm_affiliate.($utm_affiliate*14));
	}
	function notify_user($user_id,$msg) {
		$telegram_bot=$this->telegram_bot;
		$tg_id=$this->dlookup("telegram_id","users","id='$user_id'");
		if(!$tg_id)
			return false;
		include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
		$vk=new vklist_api;
		$vk->send_telegram_alert($msg, $tg_id, $vk->telegram_bots[$telegram_bot]);
	}
	function notify_log($uid,$msg,$access_level,$user_id,$cards_user_id) {
		return;
		$str="uid=$uid access=$access_level user_id=$user_id cards_user_id=$cards_user_id \n$msg\n";
		$fname=time()."-$uid-$user_id-$cards_user_id";
		file_put_contents("/var/www/html/pini/yogahelpyou/tmp/$fname",$str);
	}
	function notify($uid,$msg,$acc_id=0,$user_id=0) {
		//$uid==-1 - common notification
		if(!intval($uid))
			return false;
		if(empty($this->db200)) {
			//if( strpos(getcwd(),"yogacenter_vkt")!==false || strpos(getcwd(),"yogahelpyou")!==false )
			$this->db200="https://1-info.ru/yogacenter_vkt/db";
		}
	//print "HERE_";
		$razdel_exclude_A=$this->razdel_do_not_notify;
		$telegram_bot=$this->telegram_bot;
		include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
		//$this->db200="http://1-info.ru/yogacenter_vkt/db";
		$vk=new vklist_api;
		$uid=intval($uid);
	//print "HERE_$telegram_bot <br>";
		//~ if($this->dlookup("id","kurator_notify","uid='$uid' AND kurator_id=1")) {
			//~ $vk->token=$vk->tokens['yogahelpyou'];
			//~ $msg="Вопрос от клиента https://1-info.ru/yogacenter_vkt/db/msg.php?uid=$uid";
			//~ if($vk->vk_msg_send($vk->uids['viktorov'], $msg)==0)
				//~ print "<div class='alert alert-info' >Уведомление отправлено и будут отправляться при каждом сообщении от клиента</div>";
		//~ }
		if($uid!=-1) {
			$r=$this->fetch_assoc($this->query("SELECT *  FROM cards JOIN razdel ON cards.razdel=razdel.id WHERE uid=$uid"));
		} else {
			$r=array('razdel'=>0,'user_id'=>$user_id,'surname'=>'','name'=>'','razdel-name'=>'-');
		}
		if($r['dont_disp_in_new']!=0)
			return;
		if(in_array($r['razdel'],$razdel_exclude_A)) { //IF NOT NOTIFYED IN MAIN CONDITIONS (other,A)
//	print "HERE_2";
			//$msg="($acc_id) New message from OTHER uid= $uid : ( {$r['razdel_name']} ) {$r['surname']} {$r['name']} \n ".$this->db200."/cp.php?str=$uid&view=yes&filter=Search\n$msg";
			$msg="($acc_id) {$r['surname']} {$r['name']} ({$r['razdel_name']} - $uid)\n ".$this->db200."/msg.php?uid=$uid\n$msg";
			$res1=$this->query("SELECT * FROM users WHERE del=0 AND telegram_id!='' AND fl_notify_if_other=1 AND fl_allowlogin=1");
			while($r1=$this->fetch_assoc($res1)) {
				$this->notify_log($uid,$msg,$r1['access_level'],$r1['id'],$cards_user_id=$r['user_id']);
				$vk->send_telegram_alert($msg, $r1['telegram_id'], $vk->telegram_bots[$telegram_bot]);
			}
		} else  {
			//print "HERE_2";
			$user_name=($user_id)?$user_id:$r['user_id'];
			//$msg="($acc_id) New message from uid= $uid : ( {$r['razdel_name']} ) {$r['surname']} {$r['name']} \n ".$this->db200."/cp.php?str=$uid&view=yes&filter=Search\n$msg";
			if($uid)
				$msg="($acc_id) {$r['surname']} {$r['name']} ({$r['razdel_name']} - $uid - $user_name) \n ".$this->db200."/msg.php?uid=$uid\n$msg";
			else
				$msg="https://1-info.ru/yogacenter_vkt/db/cp.php?view=yes&filter=new";
			$res1=$this->query("SELECT * FROM users WHERE del=0 AND telegram_id!='' AND fl_notify_if_new=1 AND fl_allowlogin=1");
			while($r1=$this->fetch_assoc($res1)) {
				//$vk->send_telegram_alert("telegram_id={$r1['telegram_id']} r1['access_level'] = {$r1['access_level']} r1['acc_id']={$r1['acc_id']} acc_id=$acc_id", 315058329, $this->telegram_bot);
			//	print "HERE {$r1['id']} {$r['user_id']}"; exit;
				if($r1['access_level']>3) {
					if($r1['id']==$r['user_id']) {
						$this->notify_log($uid,$msg,$r1['access_level'],$r1['id'],$cards_user_id=$r['user_id']);
						$vk->send_telegram_alert("l=5 ".$msg, $r1['telegram_id'], $vk->telegram_bots[$telegram_bot]);
					}
				} else {
					if($r1['fl_notify_of_own_only']==1) {
						$res_msg=$this->query("SELECT user_id FROM msgs WHERE outg=1 AND uid='$uid' ORDER BY id DESC LIMIT 5");
						while($r_msg=$this->fetch_assoc($res_msg)) {
							if($r_msg['user_id']==$r1['id']) {
								$this->notify_log($uid,$msg,$r1['access_level'],$r1['id'],$cards_user_id=$r['user_id']);
								$vk->send_telegram_alert("1".$msg, $r1['telegram_id'], $vk->telegram_bots[$telegram_bot]);
								break;
							}
						}
					} else
						$this->notify_log($uid,$msg,$r1['access_level'],$r1['id'],$cards_user_id=$r['user_id']);
						$vk->send_telegram_alert("2".$msg, $r1['telegram_id'], $vk->telegram_bots[$telegram_bot]);
				}
			}
		}
	}
	function yoga_check_price($uid,$product_id=1) {
	global $base_prices;
	$uid=intval($uid);
	$tm=time();
	if($price_id=$this->dlookup("price_id","discount","uid='$uid' AND product_id='$product_id' AND (dt1<$tm AND dt2>$tm)",0))
		return $base_prices[$product_id][$price_id];
	else
		return $base_prices[$product_id][1];
	}

	function yoga_clr_discount($uid,$product_id) {
		$this->query("DELETE FROM discount WHERE dt2<".time(),0);
		$this->query("DELETE FROM discount WHERE uid='$uid' AND product_id='$product_id'",0);
	}
	function yoga_set_discount($uid,$price_id,$tm1,$tm2,$product_id=0) {
		global $base_prices; //prices.inc.php - array(0=>8990,1=>5990,2=>3990)
		//print "HERE_"; exit;
		if(!intval($uid)) {
			//print "yoga_set_discount: UID is not defined <br>\n";
			return false;
		}
		if(!intval($tm1))
			$tm1=time();
		if(!intval($tm2))
			return false;
		if(!intval($product_id))
			return false;
		$tm1=$this->dt1($tm1);
		$tm2=$this->dt2($tm2);
		$this->yoga_clr_discount($uid,$product_id);
		$this->query("INSERT INTO discount SET
					uid='$uid',
					dt1='$tm1',
					dt2='$tm2',
					product_id='$product_id',
					price_id='$price_id'
					",0);
		return true;
	}
	function yoga_get_stock($uid,$product_id, $period=7*24*60*60) {
		global $base_prices;
		if(!$uid)
			return $base_prices[$product_id]['stock'];
		
		$r=$this->fetch_assoc($this->query("SELECT * FROM avangard_stock
							WHERE uid='$uid' AND product_id='$product_id' AND tm>".(time()-$period)."
							ORDER BY id DESC LIMIT 1"));
		if(!$r)
			$amount=$base_prices[$product_id]['stock'];
		else {
			$amount=$r['amount'];
			if($r['tm']>(time()-(10*60)) )
				return $amount;
		}
		
		if($amount>=10)
			$amount-=intval($amount/5); else $amount--;
//		print "HERE_".$amount;
		if($amount<=0)
			$amount=2;
		$this->query("INSERT INTO avangard_stock SET
					tm='".time()."',
					product_id='".intval($product_id)."',
					uid='".intval($uid)."',
					amount='$amount'
					");
		return $amount;
	}
	function promocode_add($promocode,$uid,$tm1,$tm2,$product_id,$discount,$price=0) {
		$tm1=intval($tm1); //$this->dt1($tm1);
		$tm2=intval($tm2); //$this->dt2($tm2);
		if(!$tm1 || !$tm2)
			return false;
		$this->query("DELETE FROM promocodes WHERE tm2<".time());
		$this->query("INSERT INTO promocodes SET
			promocode='".$this->escape($promocode)."',
			uid='".intval($uid)."',
			tm1='$tm1',
			tm2='$tm2',
			product_id='".intval($product_id)."',
			discount='".intval($discount)."',
			price='".intval($price)."'
			");
		return true;
	}
	function yoga_ppl_set($uid,$step) {
		if(!$uid)
			return false;
		if($uid==1)
			return true; //for testing
		$tm1=$this->dt1(time());
		if($this->dlookup("id","ppl","uid=$uid AND step=$step AND tm>$tm1"))
			return false;
		if($step==1)
			$source_id=41;
		elseif($step==2)
			$source_id=42;
		elseif($step==3)
			$source_id=43;
		elseif($step==4)
			$source_id=44;
		elseif($step==11)
			$source_id=45;
		elseif($step==12)
			$source_id=46;
		elseif($step==13)
			$source_id=47;
		elseif($step==14)
			$source_id=48;
		elseif($step==20)
			$source_id=49;
		elseif($step==21)
			$source_id=49;
		elseif($step==100)
			$source_id=50;
		else
			$source_id=0;
			
		if($step<10)
			$msg="PPL-$step";
		elseif($step>10 AND $step<20)
			$msg="PL-$step (бесплатный курс день ".($step-10).")";
		elseif($step>=20 AND $step<30)
			$msg="L-$step НАЧАЛО ПРОДАЖ";
		elseif($step==100)
			$msg="РЕГИСТРАЦИЯ НА БОНУСНУЮ МЕДИТАЦИЮ";

		if($step==21)
			$msg="L-$step ЗАБРАЛ ПОДАРОК - 7 ПРАКТИК НА 30 ДНЕЙ";
		//print "HERE_ $uid _ $msg";
		if($source_id) {
			$this->save_comm($uid,0,"$msg",$source_id);
			$this->query("UPDATE cards SET source_id='$source_id' WHERE uid='$uid'");
		}
		//~ if($ppl_id=$this->dlookup("id","ppl","step='$step' AND uid='$uid'")) {
			//~ $this->query("UPDATE ppl SET tm='".time()."' WHERE id='$ppl_id'");
			//~ return false;
		//~ }
		$this->query("INSERT INTO ppl SET uid='$uid',tm='".time()."',step='$step'");
		return(true);
	}
	function yoga_email($subj,$msg) {
		$this->email($emails=array("vlav@mail.ru"), $subj,$msg, $from="noreply@yogahelpyou.com",$fromname="YOGAHELPYOU", $add_globals=false);
	}
	function papa_email($subj,$msg) {
		$this->email($emails=array("vlav@mail.ru"), $subj,$msg, $from="noreply@1-info.ru",$fromname="PAPAVDEKRETE", $add_globals=false);
	}
	function formula_email($subj,$msg) {
		$this->email($emails=array("vlav@mail.ru"), $subj,$msg, $from="noreply@1-info.ru",$fromname="F12", $add_globals=false);
	}

	function course_access_granted($uid,$source_id) {
		return $this->dlookup("tm","msgs","source_id='$source_id' AND uid='$uid'"); 
	}
	function course_access_set($uid,$source_id,$tm1,$tm2,$force=false) {
		//print "HERE1_$uid";
		if(!$force) {
			if($this->dlookup("id","course_access","uid='$uid' AND source_id='$source_id'"))
				return false;
		} else
			$this->query("DELETE FROM course_access WHERE uid='$uid' AND source_id='$source_id'",0);
		$this->query("INSERT INTO course_access SET
			uid='$uid',
			source_id='$source_id',
			tm1='$tm1',
			tm2='$tm2'
			",0); 
		return true;
	}
	function course_access_chk($uid,$source_id_arr) {
		if($uid==198746774) {
			return true;
		}
		foreach($source_id_arr AS $source_id) {
			if($r=$this->fetch_assoc($this->query("SELECT * FROM course_access
										WHERE uid='$uid' AND source_id='$source_id'
											AND tm1<='".time()."' AND tm2>='".time()."' ",0))) {
				
				return $r;
			}
		}
		return false;
	}
	function course_access_finished_tm($uid,$source_id) {
		return $this->fetch_assoc($this->query("SELECT * FROM course_access
										WHERE uid='$uid' AND source_id='$source_id' ORDER BY tm2 DESC LIMIT 1",0))['tm2'];
	}
	function is_banned($uid) {
		return $this->dlookup("id","ban","uid='$uid'");
	}
	function is_client($uid) {
		if($uid==198746774)
			return false;
		return $this->fetch_assoc($this->query("SELECT msgs.id FROM msgs
												JOIN sources ON sources.id=source_id
												WHERE uid='$uid' AND fl_client=1"));
	}
	function is_md5($md5) {
		return (preg_match("/^[a-f0-9]{32}$/i",$md5));
	}
	function get_webinar_tm($project) {
		if($project=='style-inside') {
			$fname="https://style-inside.ru/webinar_tm.txt";
		}
		return intval(file_get_contents($fname));
	}
	
	function partnerka_balance($klid) {
		if(!intval($klid))
			return false;
		$sum_p=$this->fetch_assoc($this->query("SELECT SUM(fee_sum) AS s FROM partnerka_op WHERE klid_up='$klid'",0))['s'];
		$sum_r=$this->fetch_assoc($this->query("SELECT SUM(sum_pay) AS s FROM partnerka_pay WHERE klid='$klid'",0))['s'];
		return $sum_p-$sum_r;
	}
	function check_wa($user_id) {
		$state=$this->fetch_assoc( $this->query("SELECT state FROM pact_state
					JOIN users ON  users.pact_channel_id=pact_state.channel_id
					WHERE users.id='$user_id' ORDER BY pact_state.tm DESC LIMIT 1") )['state'];
		//print "HERE_$id";
		if($state==0)
			$state=false;
		return $state;
	}

	
	var $vktrade_send_path="/var/www/html/pini/yogahelpyou/scripts/msgs/";
	var $vktrade_send_email_from_name="Онлайн школа йоги Андрея Викторова";
	var $vktrade_send_email_from="info@yogahelpyou.com";
	var $vktrade_send_testemail_to="vlav@mail.ru";
	var $vktrade_send_testvk_to="198746774";
	var $vk_token="928f721cd4a644bccc85be2d954164e0d61ddba195e37cd5eafb1f8b0ed9dddee82dd041e0c8725740be2";
	var $vktrade_unsubscribed=array('email'=>false,'vk'=>false,'wa'=>false);
	var $vktrade_send_unsubscribe_email="<br><br><br>
	<p style='color:#888888; font-size:12px;'><small>Вы получили это сообщение, потому что подписаны на рассылку от школы йоги Андрея Викторова.
	Вы в любой момент можете <a style='color:#888888;' href='https://yogahelpyou.com/unsubscribe.php?#par' class='' target=''>отписаться</a>
	</small></p>
	";
	var $vktrade_send_unsubscribe_vk="\n\n\n-----------------
	отписаться https://yogahelpyou.com/unsubscribe.php?#par \n\n\n\n
	-


	";
	var $vktrade_send_pass_clients=true;
	var $vktrade_send_pass_ban=true;
	var $pact_token="yogahelpyou";
	
	function vktrade_send($uid,$fname,$test=true) {
		$dir_vk=$this->vktrade_send_path."./vk/";
		$dir_email=$this->vktrade_send_path."./email/";
		$dir_wa=$this->vktrade_send_path."./wa/";
		if($test) {
			$email=$this->vktrade_send_testemail_to;
			$name="TEST";
			$uid_to=$this->vktrade_send_testvk_to;
		} else {
			if(!intval($uid)) {
				if($this->validate_email($uid)) {
					$email=$uid;
					$uid=$this->dlookup("uid","cards","email='$email'");
					if(!$uid)
						return false;
				} else
					return false;
			}
			$email=$this->dlookup("email","cards","uid='$uid'");
			$name=$this->dlookup("name","cards","uid='$uid'");
			$uid_to=intval($uid);
			if($this->vktrade_send_pass_ban)
				if($this->is_banned($uid_to))
					return false;
			if($this->vktrade_send_pass_clients)
				if($this->is_client($uid_to))
					return false;
		}
		$uid_md5=$this->dlookup("uid_md5","cards","uid='$uid'");
		if(empty($uid_md5)) {
			$uid_md5=$this->uid_md5($uid);
			$this->query("UPDATE cards SET uid_md5='$uid_md5' WHERE uid='$uid'");
		}
		$res=array('email'=>0,'vk'=>0,'wa'=>0);

		if($this->dlookup("id","vktrade_send_unsubscribe","uid='$uid'  AND messenger_id=1"))
			$this->vktrade_unsubscribed['email']=true;
		if($this->dlookup("id","vktrade_send_unsubscribe","uid='$uid' AND messenger_id=2"))
			$this->vktrade_unsubscribed['vk']=true;
		$this->vktrade_send_unsubscribe_email=preg_replace("|#par|s","uid=$uid_md5&s_id=0&m_id=1",$this->vktrade_send_unsubscribe_email);
		$this->vktrade_send_unsubscribe_vk=preg_replace("|#par|s","uid=$uid_md5&s_id=0&m_id=2",$this->vktrade_send_unsubscribe_vk);


		if(!is_array($fname)) {
			$path=$dir_email.$fname.".html";
			$arr=file($path);
		} else {
			if(isset($fname['email']))
				$arr=explode("\n",$fname['email']);
			else
				$arr=false;
		}
		if($arr) {
			$subj=trim($arr[0]);
			$subj=preg_replace("|\{\\\$name\}|",$name,$subj);
			$subj=preg_replace("|\{\\\$email\}|",$email,$subj);
			$body=preg_replace("|\#name|s",$name,$subj);
			$body=preg_replace("|\#email|s",$email,$subj);
			
			$body="";
			for($n=1; $n<sizeof($arr); $n++) {
				$body.=$arr[$n];
			}
			$body=preg_replace("|\{\\\$name\}|s",$name,$body);
			$body=preg_replace("|\{\\\$email\}|s",$email,$body);
			$body=preg_replace("|\#uid_md5|s",$uid_md5,$body);
			$body=preg_replace("|\#name|s",$name,$body);
			//$body=preg_replace("|\#uid|s",$uid_to,$body);
			$body=preg_replace("|\#uid|s",$uid_md5,$body);
			$body=preg_replace("|\#email|s",$email,$body);
			$body_s=$body."\n отписаться ";

			$body .=$this->vktrade_send_unsubscribe_email;

			if($this->validate_email($email)) {
				if($this->vktrade_send_email($email,$subj,$body)) {
					//print "$email email sent Ok <br>\n";
					$res['email']=1;
					$this->query("INSERT INTO msgs SET
								uid='$uid',
								acc_id='100',
								tm='".time()."',
								msg='".$this->escape($subj."".preg_replace("|[\n]+|s","\n",strip_tags($body_s)))."',
								outg='1',
								source_id=3
								",0);
				}
			} else {
				print ""; //"$email email is incorrect <br>\n";
			}
		} else 
			print ""; //"File $path read error or not email send considered <br> \n";

		//SEND VK
		if($uid_to>0) {
			if(!is_array($fname)) {
				$path=$dir_vk.$fname.".txt";
				$msg=file_get_contents($path);
			} elseif(isset($fname['vk']))
				$msg=$fname['vk'];
			else
				$msg=false;
			if($msg) { 
				$msg=preg_replace("|\%username\%|s",$name,$msg);
				//$msg=preg_replace("|\%userid\%|s",$uid_to,$msg);
				$msg=preg_replace("|\%userid\%|s",$uid_md5,$msg);
				$msg=preg_replace("|\#uid_md5|s",$uid_md5,$msg);
				$msg=preg_replace("|\#name|s",$name,$msg);
				//$msg=preg_replace("|\#uid|s",$uid_to,$msg);
				$msg=preg_replace("|\#uid|s",$uid_md5,$msg);
				$msg=preg_replace("|\#email|s",$email,$msg);

				$msg.=$this->vktrade_send_unsubscribe_vk;

				if($this->vktrade_send_vk($uid_to,$msg)) {
					//print "$uid  vk msg sent Ok <br>\n";
					$res['vk']=1;
				}
			} else 
				print ""; //"File $path read error <br> \n";
		}
		//SEND WA
		if(!is_array($fname)) {
			$path=$dir_wa.$fname.".txt";
			$msg=file_get_contents($path);
		} elseif(isset($fname['wa']))
			$msg=$fname['wa'];
		else
			$msg=false;
		if($msg) { 
			$msg=preg_replace("|\%username\%|s",$name,$msg);
			//$msg=preg_replace("|\%userid\%|s",$uid_to,$msg);
			$msg=preg_replace("|\%userid\%|s",$uid_md5,$msg);
			$msg=preg_replace("|\#uid_md5|s",$uid_md5,$msg);
			$msg=preg_replace("|\#name|s",$name,$msg);
			//$msg=preg_replace("|\#uid|s",$uid_to,$msg);
			$msg=preg_replace("|\#uid|s",$uid_md5,$msg);
			$msg=preg_replace("|\#email|s",$email,$msg);

			//$msg.=$this->vktrade_send_unsubscribe_vk;

			if($this->vktrade_send_wa($uid_to,$msg)) {
				//print "$uid  vk msg sent Ok <br>\n";
				$res['wa']=1;
			}
		}
		 //~ else 
			//~ print "WA File $path read error <br> \n";

		if(is_array($fname))
			$fname="array";
		$this->query("INSERT INTO vktrade_send_log SET
					uid='$uid',
					tm='".time()."',
					source_id=0,
					fname='".$this->escape($fname)."',
					dt='".$this->escape(date("d.m.Y"))."',
					tm1='".$this->escape(date("H:i"))."'
					");
		$id=$this->insert_id();
		if($res['email'])
			$this->query("UPDATE vktrade_send_log SET res_email=1 WHERE id=$id");
		if($res['vk'])
			$this->query("UPDATE vktrade_send_log SET res_vk=1 WHERE id=$id");
		if($res['wa'])
			$this->query("UPDATE vktrade_send_log SET res_wa=1 WHERE id=$id");

		return $res;
	}
	function vktrade_send_email($email,$subj,$body) {
		if($this->vktrade_unsubscribed['email']) {
			print "$email UNSUBSCRIBED \n";
			return 2;
		}
		include_once('/var/www/vlav/data/www/wwl/inc/phpMailer/class.phpmailer.php');
		$mail= new PHPMailer();
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->Host="localhost"; //"1-info.ru"; // SMTP server
		$mail->ContentType="text/html";
		$mail->CharSet="utf-8";
		$mail->AltBody="";
		$mail->From=$this->vktrade_send_email_from;
		$mail->FromName=$this->vktrade_send_email_from_name;
		$mail->AddAddress($email, "");
		$mail->Subject='=?utf-8?B?'.base64_encode($subj).'?=';
		
		$mail->MsgHTML($body);
		return $mail->Send();
	}
	function vktrade_send_vk($uid,$msg) {
		if($this->vktrade_unsubscribed['vk']) {
			print "$uid UNSUBSCRIBED \n";
			return false;
		}
		include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
		$vk=new vklist_api($this->vk_token);
		if($res=$vk->vk_msg_send($uid, $msg, $fake=false, $chat_id=false, $attachment=false, $peer_id=false)) {
			print "VK send error $res <br> \n";
			return false;
		}
		return true;
	}
	function vktrade_send_unsubscribe($uid,$source_id,$messenger_id) {
		$uid=intval($uid);
		if(!$uid)
			return false;
		$source_id=intval($source_id);
		$messenger_id=intval($messenger_id);
		if(!$messenger_id)
			return false;
		$this->query("INSERT INTO vktrade_send_unsubscribe SET uid='$uid',source_id='$source_id',messenger_id='$messenger_id'");
		return true;
	}
	function vktrade_send_wa($uid,$msg,$source_id=3,$num=0,$attach=false) {
		include_once "/var/www/vlav/data/www/wwl/inc/pact.class.php";
		if(!$this->dlookup("wa_allowed","cards","uid='$uid'")) {
			print ""; //"$uid is NOT WA_ALLOWED \n";
			return false;
		}
		$wa=new pact($this->pact_token);
		if($attach)
			$wa->attach=$attach;
		$save_outg=($this->database!="papa")?true:false;
		$save_outg=false;
		return $wa->send($this,$uid,$msg,$user_id=0,$num,$source_id,$save_outg);
	}

	function vktrade_send_prepare_msg($uid,$msg) {
		$r=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE uid='$uid'"));
		if(!$r)
			return $msg;
		$name=$r['name'];
		$uid_md5=$r['uid_md5'];
		$email=$r['email'];
		$phone=$r['mob_search'];
		$msg=preg_replace("|\%username\%|s",$name,$msg);
		//$msg=preg_replace("|\%userid\%|s",$uid_to,$msg);
		$msg=preg_replace("|\%userid\%|s",$uid_md5,$msg);
		$msg=preg_replace("|\#uid_md5|s",$uid_md5,$msg);
		$msg=preg_replace("|\#name|s",$name,$msg);
		//$msg=preg_replace("|\#uid|s",$uid_to,$msg);
		$msg=preg_replace("|\#uid|s",$uid_md5,$msg);
		$msg=preg_replace("|\#email|s",$email,$msg);
		$msg=preg_replace("|\#phone|s",$phone,$msg);
		return $msg;
	}
	var $vktrade_send_at_notify=false;
	var $vktrade_send_at_mark_new=0;
	var $vktrade_send_at_num=0;
	var $vktrade_send_at_sid_exclude=[];
	var $vktrade_send_at_attach=false;
	var $vktrade_send_at_attach_arr=[]; // user_id=>attach_id
	var $razdel_ban=[5,6];
	var $fl_if_not_scheduled=false;
	function vktrade_send_at($msgs,$source_id,$hm=0,$dt=0,$scan_min_ago,$scan_days_ago,$dt_from=0,$dt_to=0,$force=false,$test2me_uid=false) {
		// $hm+$dt - дата и время точное во сколько запустить рассылку, 1 раз
		// $scan_min_ago - сканировать интервал в одну минуту, начиная с scan_min_ago минут назад
		// $scan_days_ago -  сканировать интервал в один день, начиная с scan_days_ago дней назад
		// $scan_days_ago=-1 - весь период
		// $dt_from,$dt_to - запрос работает с этой даты, по эту дату
		// $force
		// $test2me_uid if NOT false - send one message to uid immidiatelly
		
		if(!is_array($msgs)) {
			print "msgs is no array! [wa=>mess]";
			return;
		}
		if($test2me_uid) {
			if(isset($msgs['wa'])) {
				if($this->vktrade_send_at_num>0)
					$this->set_chat_bot($test2me_uid,$num=$this->vktrade_send_at_num,2,$bot_id=101);
				if($this->vktrade_send_wa($test2me_uid,$msg=$this->vktrade_send_prepare_msg($test2me_uid,$msgs['wa']),3,0,$this->vktrade_send_at_attach))
					print "Test WA message sent to uid=$test2me_uid OK \n";
			}
			return;
		}
		if($hm && $dt) {
			$tm_min=($hm)?$this->time2tm($hm):0;
			$tm_at=($dt)?$this->date2tm($dt)+$tm_min:0;
			if($tm_at) {
				$tm1=$tm_at;
				$tm2=$tm_at+(5*60);
			}

			if(time()<$tm1 || time()>$tm2) {
				print "Не подошло время. Сейчас ".date("d.m.Y H:i",$tm1).", а запуск в ".date("d.m.Y H:i",$tm1)."-".date("d.m.Y H:i",$tm2)."  Выход\n";
				return;
			}
		} else {
			$tm1=0;$tm2=0;
		}

		$tm_from=($dt_from)?$this->date2tm($dt_from):0;
		$tm_to=($dt_to)?$this->date2tm($dt_to):(time()+1);

		if($scan_days_ago>=0) {
			$tm1=$this->dt1(time()-($scan_days_ago*24*60*60));
			$tm2=$this->dt2(time()-($scan_days_ago*24*60*60));
			if($hm) {
				$tm_event=$this->dt1(time())+$this->time2tm($hm);
				if( time()<$tm_event ) {
					print "hm=$hm time has not come yet\n";
					return false;
				}
			}
		}
		if($scan_min_ago>0) {
			$tm1=(intval(time()/60)-$scan_min_ago)*60;
			$tm2=$tm1+60;
		}
		if($scan_days_ago==-1) {
			$tm1=0;
			$tm2=time();
		}


	
		print "\n";
		print "source_id=$source_id \n";
		print "scan_min_ago: $scan_min_ago\n";
		print "tm_from ".date("d.m.Y H:i",$tm_from)."\n";
		print "tm_to ".date("d.m.Y H:i",$tm_to)."\n";
		print "tm1 ".date("d.m.Y H:i",$tm1)."\n";
		print "tm2 ".date("d.m.Y H:i",$tm2)."\n";

		
		$where_exclude="source_id=-1";
		foreach($this->vktrade_send_at_sid_exclude AS $sid_exclude)
			$where_exclude.=" OR source_id=$sid_exclude";
		//print $where_exclude; exit;
		
		$where="source_id='$source_id' AND (tm>=$tm_from AND tm>=$tm1 AND tm<=$tm2 AND tm<=$tm_to)";
		$res=$this->query("SELECT * FROM msgs WHERE $where ORDER BY tm DESC" ,0);
		$n=1;
		while($r=$this->fetch_assoc($res)) {
			$dt=date("d.m.Y H:i",$r['tm']);
			$uid=$r['uid'];
			$user_id=$this->dlookup("user_id","cards","uid='$uid'");
			print "$n $dt {$r['uid']} ";
			if($force) {
				if(in_array($this->dlookup("razdel","cards","uid='$uid'"),$this->razdel_ban) ) {
					print "PASSED: uid=$uid is in razdel_ban list. Continued";
					continue;
				}
				if($this->fl_if_not_scheduled) {
					if( $this->dlookup("tm_schedule","cards","uid='$uid'") ) {
						print "PASSED: uid=$uid is scheduled. Continued";
						continue;
					}
				}
				if($this->dlookup("id","msgs","uid='$uid' AND ($where_exclude)")) {
					print "PASSED: uid=$uid is in vktrade_send_at_sid_exclude list. Continued";
					continue;
				}
				$msg_wa=$this->vktrade_send_prepare_msg($uid,$msgs['wa']);
				$msg_md5=md5($msg_wa);
				if($this->dlookup("id","vktrade_send_at_log","uid=$uid AND source_id=$source_id AND tm1>=$tm1 AND tm2<=$tm2 AND msg_md5='$msg_md5'",0)) {
					print " PASSED because in vktrade_send_at_log\n";
					continue;
				}
				if($this->vktrade_send_at_num>0)
					$this->set_chat_bot($uid,$num=$this->vktrade_send_at_num,2,$bot_id=101);
				if(isset($this->vktrade_send_at_attach_arr[$user_id]) )
					$this->vktrade_send_at_attach=[$this->vktrade_send_at_attach_arr[$user_id]];
				print "attach: $this->vktrade_send_at_attach \n";
				$result=$this->vktrade_send_wa($uid,$msg=$msg_wa,3,$num=0,$this->vktrade_send_at_attach);
				//$result=true;
				if($result) {
					print "SENT WA message  to uid=$uid OK \n"; 
					$this->save_comm($uid,0,"Отправлена рассылка",101);
					if($this->vktrade_send_at_mark_new>0)
						$this->mark_new($uid,$this->vktrade_send_at_mark_new);
					if( !empty($this->vktrade_send_at_notify) ) {
						$this->notify($uid,$this->vktrade_send_at_notify);
						//$this->save_comm($uid,0,"notify:".$this->vktrade_send_at_notify,101);
					}
				} else
					print "SENT WA message  to uid=$uid vktrade_send_wa ERROR \n";

				$file_name=basename(__FILE__);
				$this->query("INSERT INTO vktrade_send_at_log SET uid='$uid', tm='".time()."',tm1=$tm1, tm2=$tm2, source_id='$source_id',msg_md5='$msg_md5',fname='$file_name',res='$result'");
			}
			$n++;
		}
		print "\n";
	}
	function set_chat_bot($uid,$num=0,$source_id=3,$bot_id=101) {
		$this->query("INSERT INTO msgs SET
						uid='$uid',
						acc_id='$bot_id',
						mid='0',
						tm='".time()."',
						msg='CHAT BOT SETTING AT $num',
						outg='2',
						source_id='$source_id',
						vote='$num',
						new='$num'
						");
		return $this->insert_id();
	}
	
	function is_localhost() {
		$res=(file_exists("/server.flag"))?false:true;
		return $res;
	}
	///////////////////////////
	function get_ops_info($id) {
		$res=$this->query("SELECT COUNT(debit) AS cnt, MIN(tm) AS tm1, MAX(tm) AS tm2 FROM ops WHERE klid=$id AND debit>0 AND fake=0");
		if(!$res || $this->num_rows($res)==0)
			return false;
		$r=$this->fetch_assoc($res);
		return array('tm1'=>$r['tm1'],'tm2'=>$r['tm2'],'cnt'=>$r['cnt']);
	}
	function print_ops($klid,$ctrl=true,$lastid=0) {
		$r=mysql_fetch_assoc(mysql_query("SELECT (SUM(kredit)-SUM(debit)) AS dif FROM `ops` WHERE klid=$klid"));
		//if($r['dif']==0) return;
		print "<h1>Остаток на сегодня :{$r['dif']}</h1>";
		
		$res=mysql_query("SELECT * FROM ops WHERE klid=$klid ORDER BY tm ASC,kredit DESC");
		print "<HR><table class='ops' width='100%'>";
			print "<tr style='color:blue;'><td style='width:80px;text-align:center;'>Дата</td>
			<td style='width:60px;'>Приход</td>
			<td style='width:60px;'>Расход</td>
			<td style='width:80px;'>Остаток в нак.</td>
			<td style='width:60px;'>Цена за занятие</td>
			<td style='width:60px;'>Осталось занятий</td>
			<td style='width:20px;'>fake</td>
			<td style='text-align:left;'>Комментарий</td>
			<td style='width:50px;'>Уд.</td></tr>";
		$ost=0; $price=0;
		while($r=mysql_fetch_assoc($res)) {
			if($r['id']==$lastid)
				$cur="class='ops_tr_cur'"; else $cur="class='ops_tr'";
			if(@$_GET['ops_edit'] && @$_GET['id']==$r['id'] && $ctrl) {
				print "<form><tr $cur id='r_{$r['id']}'>
				<td style='width:80px;text-align:center;'><input type='text' name='dt' value='".date("d.m.Y",$r['tm'])."' style='width:80px;text-align:center;' onfocus='this.select();lcs(this)' onclick='event.cancelBubble=true;this.select();lcs(this)'></td>
				<td style='width:60px;'><input type='text' name='kredit' value='{$r['kredit']}' style='width:60px;text-align:center;'></td>
				<td style='width:60px;'><input type='text' name='debit' value='{$r['debit']}' style='width:60px;text-align:center;'></td>
				<td style='width:80px;'>&nbsp;</td>
				<td style='width:60px;'><input type='text' name='price' value='{$r['price']}' style='width:60px;text-align:center;'></td>
				<td style='width:60px;'>&nbsp;</td>";
				if($r['fake']==1)
					$chk="checked"; else $chk="";
				print "<td style='width:60px;'><input type='checkbox' name='fake' $chk style='width:60px;text-align:center;'></td>
				<td style='text-align:left;'><textarea name='comm' style='width:150px;height:40px;text-align:left;'>{$r['comm']}</textarea></td>
				<td style='width:50px;'>
					<input type='hidden' name='id' value='{$r['id']}'>
					<input type='submit' name='do_ops_edit' value='save'>
				</td></tr></form>";
			} else {
				//if($r['kredit']>0)
				$price=$r['price'];
				if($price==0) {
					$r1=mysql_fetch_assoc(mysql_query("SELECT * FROM ops WHERE klid=$klid AND tm<={$r['tm']} AND price>0 ORDER BY tm DESC"));
					if($r1) {
						$price=$r1['price']; 
						mysql_query("UPDATE ops SET price='$price' WHERE id={$r['id']}");
					} else $price=0;
					//print "HERE_$price"; 
				}
				$ost=$ost+$r['kredit']-$r['debit'];
				if($price>0)
					$cnt=round($ost/$price,0); else $cnt="-";
				print "<tr $cur id='r_{$r['id']}'>
				<td style='width:80px;text-align:center;'>".date("d.m.Y",$r['tm'])."</td>
				<td style='width:60px;'>{$r['kredit']}</td>
				<td style='width:60px;'>{$r['debit']}</td>
				<td style='width:80px;'>$ost</td>
				<td style='width:60px;'>$price</td>
				<td style='width:60px;'>".$cnt."</td>
				<td style='width:60px;'>{$r['fake']}</td>
				<td style='text-align:left;'>".nl2br($r['comm'])."</td>";
				print "<td style='width:50px;'>";
				if($ctrl)
					print "<a href='?ops_edit=yes&id={$r['id']}#r_{$r['id']}'>edit</a> <a href='?ops_del=yes&id={$r['id']}'>del</a>"; else print "&nbsp;";
				print "</td></tr>";
			}
		}
		print "<table>";
		print "<script>location.hash='r_$lastid'</script>";
	}
	function bg_msg_badge($fl_newmsg,$tm_delay=0) {
			if($fl_newmsg>4)
				$bg_msg_badge="background-color:#FFFF00;";
			elseif($fl_newmsg==4)
				$bg_msg_badge="background-color:#6060FF;";
			elseif($fl_newmsg==3)
				$bg_msg_badge="background-color:#56b849;";
			elseif($fl_newmsg==2)
				$bg_msg_badge="background-color:#d9534f;";
			elseif($fl_newmsg==1)
				$bg_msg_badge="background-color:#f0ad4e;";
			elseif($fl_newmsg==0)
				$bg_msg_badge="background-color:##7F7F7F;";	
			//~ if($tm_delay!=0 && $tm_delay<time())
				//~ $bg_msg_badge="background-color:#5bc0de;";	
			return $bg_msg_badge;
	}
	function customer_stat($customer_id,$mode=0) {
		$r=$this->fetch_assoc($this->query("SELECT * FROM customers WHERE id=$customer_id"));
		if(!$r)
			return false;
		if($r['db']=="") {
			print "<div class='alert alert-danger'>Не указана в customers база данных</div>";
			return false;
		}
		$database=$r['db'];
		$tm_cr=$r['tm_cr'];
		$bs=new bs;
		print "<h3><div class='alert alert-info' >{$r['group_name']} : $database</div></h3>";
		$res_p=$this->query("SELECT * FROM c_persons JOIN cards ON cards.uid=c_persons.uid WHERE cid=$customer_id AND c_persons.del=0 ORDER BY fl_contact DESC");
		while($r_p=$this->fetch_assoc($res_p)) {
			print "<div class='well well-sm' >{$r_p['surname']} {$r_p['name']}</div>";
		}
		$this->connect($database);
		$res=$this->query("SELECT razdel_name,COUNT(uid) AS cnt FROM cards JOIN razdel ON razdel.id=razdel WHERE cards.del=0 GROUP BY razdel ORDER BY razdel_name");
		print $bs->table(array("Раздел","Количество клиентов"));
		while($r=$this->fetch_assoc($res)) {
			print "<tr><td>{$r['razdel_name']}</td><td>{$r['cnt']}</td></tr>";
		}
		print "</table>";
		
		$tm_limit=time()-(30*24*60*60);
		$res=$this->query("SELECT * FROM msgs WHERE outg=1 AND tm>$tm_limit AND tm>$tm_cr ORDER BY tm DESC");
		$dt0=date("d.m.Y"); $n=1;
		print $bs->table(array("Дата","Отправлено сообщений"));
		while($r=$this->fetch_assoc($res)) {
			$dt=date("d.m.Y",$r['tm']);
			//print "<div class='well well-sm' >$dt0 $dt - {$r['uid']}</div>";
			if($dt0!=$dt) {
				print "<tr><td>$dt0</td><td>$n</td></tr>";
				$dt0=$dt;
				$n=1;
			} else
				$n++;
			
		}
		print "</table>";
	}
	
	function ad_get_vk_token($user_id,$database) {
		$this->connect("vktrade");
		$cid=$this->dlookup("id","customers","db='$database'");
		if(!$cid) {
			print "<div>Error: cid getting error</div>";
			return false;
		}
	//	print "$cid";
		$client_id="6414595";
		$state="$cid";
		$url="https://oauth.vk.com/authorize?client_id=6414595&display=page&redirect_uri=https://1-info.ru/vktrade/db/callback.php&scope=offline,ads&response_type=code&v=5.73&state=$state";
		//$res=$this->file_get_contents_curl($url);
	//	print $url." <br>";
		print "<script>location='$url'</script>";
		//print "$res";
	}
	function file_get_contents_curl($url) {
		if(function_exists('curl_init')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$output = curl_exec($ch);
			echo curl_error($ch);
			curl_close($ch);
			return $output;
		} else {
			print "file_get_contents";
			return file_get_contents($url);
		}
	}
	function touch_from_outside($md5,$uid,$mode=0,$vklist_group_id=0) {
		$uid=intval($uid);
		if(!$uid)
			return false;
		if(strlen($md5)!=32)
			return false;
		$this->connect("vktrade");
		$database=$this->dlookup("db","customers","md5='$md5'");
		$dir=$this->dlookup("dir","customers","md5='$md5'");
		if(!$database)
			return false;
		include_once("/var/www/vlav/data/www/wwl/inc/vklist_api.class.php");
		$vk=new vklist_api;
		$u=$vk->vk_get_userinfo($uid);
		$first_name=$u['first_name'];
		$last_name=$u['last_name'];
		$city=$u['city']['title'];
		$comm="ЗАШЕЛ НА ЛЭНДИНГ";

		$this->connect($database);
		$this->db200="https://1-info.ru/$dir/db";
		$fl_newmsg=$this->dlookup("fl_newmsg","cards","uid=$uid");
		if($fl_newmsg===false) {
			if($mode!=0) {
				if($this->dlookup("tm_msg","vklist","uid=$uid")!=1) {
					$this->query("DELETE FROM vklist WHERE uid=$uid");
					$this->query("INSERT INTO vklist SET uid=$uid, group_id='".$vklist_group_id."',tm_cr=".time());
				} else
					return false;
			} else {
				$acc_id=$this->get_default_acc();
				$this->query("INSERT INTO cards SET 
						uid=$uid,
						acc_id=$acc_id,
						name='".$this->escape(trim($first_name))."',
						surname='".$this->escape(trim($last_name))."',
						city='".$this->escape(trim($city))."',
						razdel=0,
						source_id=6,
						fl_newmsg=1,
						tm_lastmsg=".time().",
						tm=".time().""
						);
				//print "HERE_$uid";
				$this->save_comm($uid,0,"NEW:".$comm,$source_id=6);
				$this->notify($uid,$msg="",$acc_id=0);
			}
		} else {
			if((int)$fl_newmsg!=3) {
				//print "HERE_$fl_newmsg";
				$this->query("UPDATE cards SET tm_lastmsg='".time()."',fl_newmsg='1',source_id=6 WHERE uid=$uid");
			}
			$this->save_comm($uid,0,$comm,6);
		}
		return true;
		//~ print "$database $first_name $last_name $city";
		//~ $this->print_r($u);
	}
	function get_ip() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) $ip=$_SERVER['HTTP_CLIENT_IP'];
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		else $ip=$_SERVER['REMOTE_ADDR'];
		return ($ip)?$ip:false;
	}
	function get_first_working_acc() {
		return true;
		$r=$this->fetch_assoc($this->query("SELECT * FROM vklist_acc WHERE del=0 AND last_error=0 AND fl_allow_read_from_all=0 AND token!='' ORDER BY num,id LIMIT 1"));
		if($r)
			return $r; else {
				print "<div class='alert alert-danger' >Нет подключенных работающих аккаунтов</div> \n"; 
				return false;
			}
	}
}
?>
