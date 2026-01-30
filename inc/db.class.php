<?php
include_once "bs.class.php";

define("disp_mysql_errors","0"); //0 - do not display; 1-display just info; 2 - display with query and mysql_error()
class db extends db_pdo {
	function __construct($database=false) {
		if($database) 
			$this->connect($database); else $this->check_php_version();
	}
}
class db_mysqli {
	var $debug=0;
	var $db200="";
	var $phpver=7;
	var $acc_id_hohlova=37;
	public static  $conn=null;
	var $razdel_exclude_for_save_comm=[]; //array(2,3,8,9,11,12,7);
	var $razdel_do_not_notify=array();
	var $telegram_bot="";
	var $database;
	var $last_query;
	var $runtime_log;
	var $ctrl_id;
	
	function __construct($database=false) {
		if($database) 
			$this->connect($database); else $this->check_php_version();
	}
	function get_current_database() {
		$r=$this->fetch_row($this->query("SELECT DATABASE()"));
		return $r['0'];
	}
	function test_microtime($line) {
		//return; // Uncomment to disable
		$this->runtime_log[] = ['tmm' => microtime(true), 'line' => $line];
	}
	function print_runtime_log() {
		$last=0; $dur=0;
		foreach($this->runtime_log AS $r) {
			if(!$last) {
				$last=$r['tmm'];
				$dur=$last;
			}
			print $r['line']." ".number_format($r['tmm']-$last, 3, '.', '')." sec.ms<br>";
			$last=$r['tmm'];
			$dur+=$r['tmm'];
		}
		print "Duration: ".number_format($dur, 3, '.', '')." sec.ms<br>";
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
				print date("d.m.Y H:i:s",$item['tm']);
				if($item['tm']< (time()-(10*60)) ) {
					unset($banned_ip[$key]);
				}
				if($item['ip']==$ip) {
					print " error=6";
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
				$this->email($emails=array("vlav@mail.ru"), "ATT warning 1 - possible hacking attempt err=5 chk=$chk", "ERROR - CAN NOT RECORD BANNED IP IN ".getcwd()."/tmp DIR - mkdir error\n\n", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
		file_put_contents("tmp/banned_ip",json_encode($banned_ip));
	}
	function before_connect() {
		$error=false;
		$chk_numeric=array('id','klid','acc_id','kredit','debit','list_mode');
		$chk="";
		if(isset($_GET['uid']) ) {
			//~ if($_GET['uid'] == 0 && !$this->is_md5($_GET['uid']) ) {
				//~ if(isset($_POST['uid'])) {
					//~ if($_POST['uid']==0)
						//~ $this->email($emails=array("vlav@mail.ru"), "WARNING db.class.php before_connect: POST uid == 0 ", "", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
				//~ } else
					//~ $this->email($emails=array("vlav@mail.ru"), "WARNING db.class.php before_connect: GET uid == 0 ", "", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
			//~ }
			$uids_var=['\$uid','%userid%','{{uid}}','{%uid%}','#uid'];
			if(!empty($_GET['uid'])) {
				if(!is_numeric($_GET['uid']) ) {
					if(!$this->is_md5($_GET['uid'])) {
						if(!in_array($_GET['uid'],$uids_var) ) {
							$chk=$_GET['uid'];
							$error=true;
						} else
							$_GET['uid']=0;
					}
				}
			}
		}
		foreach($chk_numeric AS $val) {
			if(isset($_GET[$val])) {
				if(empty($_GET[$val]))
					$_GET[$val]=0;
				if(!is_numeric($_GET[$val]) ) {
					$_GET[$val]=0;
					$chk=$val.'='.$_GET[$val];
					$error=true; 
				} else $_GET[$val]=intval($_GET[$val]);
			}
			if(isset($_POST[$val])) {
				if(empty($_POST[$val]))
					$_POST[$val]=0;
				if(!is_numeric($_POST[$val]) ) {
					$chk=$val.'='.$_POST[$val];
					$_POST[$val]=0;
					$error=true; 
				} else $_POST[$val]=intval($_POST[$val]);
			} 
		}
		if($error) {
			$this->email($emails=array("vlav@mail.ru"), "warning 2 - possible hacking attempt err=5 chk:$chk", "", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
			//$this->ban_ip();
			print "error 5-1"; 
			exit;
		}
	}
	function check_php_version() {
		return 8;
		$v=explode(".",phpversion());
		//(print_r)($v);
		$this->phpver=$v[0];
		//print phpversion()."<br>";
		//print $this->phpver."<br>";
	}
	function get_mysql_env($root=false) {
		$fname=!$root ? "/var/www/vlav/data/.env" : "/var/www/vlav/data/.env_r";
		$env = file_get_contents($fname);
		if (!$env) {
			die('Invalid configuration file');
		}
		$r=json_decode($env,true);
		return $r;
	}
	function connect($db=false,$root=false) {
		if($db=='vkt1_')
			exit;
		$this->check_php_version();
		$this->check_is_ip_banned();
		$this->before_connect();
		if($db != 'vkt')
			$last_database=$db;
		$this->database=$db;
		//print "here_".$this->phpver."<br>";
		$chk_arr=array("\/#","\/\*","outfile","dumpfile","into","benchmark","sleep","or","nvOpzp","true,false","\%27","\<\?","\?\>","\<script","\<\/script","select","update ","insert","drop ","truncate","union","delete");
		$chk_arr=array("outfile","dumpfile","into","benchmark","sleep","nvOpzp","true,false","\%27","\<\?","\?\>","\<script","\<\/script","select","update ","insert","drop ","truncate","union","delete");
		$match="#";
		foreach($chk_arr AS $item)
			$match.="($item)|";
		$match.="(drop)#si";
		foreach($_GET AS $key=>$val) {
			$res=preg_replace($match,"XXX",$val);
			$_GET[$key]=$res;
		}
		foreach($_POST AS $key=>$val) {
			$res=preg_replace($match,"XXX",$val);
			$_POST[$key]=$res;
		}
		$glb=mb_strtolower(print_r($_REQUEST,true),"utf-8");
		//print $glb;
		foreach($chk_arr AS $chk) {
			if(strpos($glb,"mysql_error")!==false) //to pass queries errors
				break;
			if(strpos($glb,$chk)!==false) {
				$this->email($emails=array("vlav@mail.ru"),
					"warning 3 - possible hacking attempt err=5 chk=$chk",
					nl2br("$glb\n\nPOST:\n".print_r($_POST,true)."\n\n"."GET:\n".print_r($_GET,true)."\n\n"),
					$from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
					$this->ban_ip();
				print "error=5-2";
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
					//$this->email($emails=array("vlav@mail.ru"), "mysql_error ", "mysql_error\n$qstr\n".mysql_error()." (".mysql_errno().") cnt_errors=$cnt_errors\n", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
				}
				if(!db::$conn) {
					print "conn :: Database connect error!";
					if( intval(mysql_errno())!=2002 && intval(mysql_errno())!=2014  && intval(mysql_errno())!=2013 )
						$this->email($emails=array("vlav@mail.ru"), "mysql_error ", "query\nmysql_error\n$qstr\n".mysql_error()." (".mysql_errno().") (final)\n", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
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
			$r=$this->get_mysql_env($root);
			$mysql_user=$r['DB_USER'];
			$mysql_passw=$r['DB_PASSW'];
			db::$conn=mysqli_connect ("localhost", $mysql_user, $mysql_passw);
			if($db) {
				try {
						mysqli_select_db (db::$conn,$db);
					}  catch (mysqli_sql_exception $e) {
						print "error select db";
						$this->email($emails=array("vlav@mail.ru"), "mysql_error ", "mysql_error\n db=$this->database\n $qstr\n ".$e->getMessage()." (".mysqli_errno(db::$conn).") (final)\n".nl2br(print_r(debug_backtrace (),true))."\n\nGLOBALS\n", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
					}
					mysqli_query (db::$conn,"set character_set_results='utf8mb4'");
					mysqli_query (db::$conn,"set collation_connection='utf8mb4_general_ci'");
					mysqli_query(db::$conn,"set character_set_client='utf8mb4'");
			} else
				return false;
		}
		if($db) {
			$this->vk_token=$this->get_vk_token();
		}
	}

	function chk_empty_fields($query) {
	//	return false;
		$arr=['name'];
		preg_match_all("/\bUPDATE cards SET\s+(.+?)\s+WHERE\b/i", $query, $matches);
		if(isset($matches[1][0])) {
			$assignments = $matches[1][0];
			$assignmentsArr = explode(",", $assignments);
			
			$pairs = [];
			foreach($assignmentsArr as $assignment) {
				$parts = explode("=", $assignment);
				$field = trim($parts[0]);
				$value = trim($parts[1], " '");
				if(in_array($field,$arr) &&  empty($value))
					return true;
			}
		}
		return false;
	}
	function chk_column($table, $column, $type, $index = false) {
		// Проверяем существование колонки
		$check = $this->fetch_assoc($this->query(
			"SELECT COUNT(*) as cnt FROM INFORMATION_SCHEMA.COLUMNS 
			 WHERE TABLE_SCHEMA = DATABASE() 
			   AND TABLE_NAME = '$table' 
			   AND COLUMN_NAME = '$column'"
		));
		
		if ($check['cnt'] == 0) {
			// Колонки нет - создаем
			$type_upper = strtoupper($type);
			if (strpos($type_upper, 'NOT NULL') === false) {
				// NOT NULL еще нет - добавляем
				$type .= ' NOT NULL';
			}
			$sql = "ALTER TABLE `$table` ADD COLUMN `$column` $type";
			$this->query($sql);
			
			// Добавляем индекс если нужно
			if ($index) {
				$index_name = "idx_{$table}_{$column}";
				$this->query("ALTER TABLE `$table` ADD INDEX `$index_name` (`$column`)");
			}
			
			return true; // Колонка была создана
		}
		
		return false; // Колонка уже существует
	}
	function query($qstr,$print_query=0,$disp_errors=false) {
		//$this->last_query=$qstr;
		$err=false;
			//print "_v=".$this->phpver."_ <br>";
		$disp_mysql_errors=($disp_errors===false)?disp_mysql_errors:$disp_errors;
		if($this->is_localhost())
			$disp_mysql_errors=2;
		if($this->debug==0) {
			if($print_query>0) {
				$this->notify_me($qstr,false);
				//print "<p class='alert alert-danger'>$qstr</p>";
			}
			if($this->chk_empty_fields($qstr)) {
				$this->notify_me("db=$this->database : chk_empty_fields: $qstr");
				return false;
			}
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
						//$this->email($emails=array("vlav@mail.ru"), "mysql_error ", "mysql_error\n$qstr\n".mysql_error()." (".mysql_errno().") cnt_errors=$cnt_errors\n", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
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
							$this->email($emails=array("vlav@mail.ru"), "mysql_error ", "mysql_error\n$qstr\n".mysql_error()." (".mysql_errno().") (final)\n".nl2br(print_r(debug_backtrace (),true))."\n\nGLOBALS\n", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
						exit;
					}
				}
			} else {
				try {
					$res=mysqli_query(db::$conn,$qstr);
				}  catch (mysqli_sql_exception $e) {
					$this->email($emails=array("vlav@mail.ru"), "mysql_error ", "mysql_error\n db=$this->database\n $qstr\n ".$e->getMessage()." (".mysqli_errno(db::$conn).") (final)\n".nl2br(print_r(debug_backtrace (),true))."\n\nGLOBALS\n", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
					$this->notify_me("mysql_error db=$this->database\n".$e->getMessage());
				}
				//~ if(!$res) {
					//~ if($disp_mysql_errors==2)
						//~ $err="mysql_error: $qstr<br>\n".mysqli_error(db::$conn);
					//~ elseif ($disp_mysql_errors==1)
						//~ $err="mysqli error";
					//~ print "<div class='alert alert-danger'>$err</div>";
					//~ $this->email($emails=array("vlav@mail.ru"), "mysql_error ", "mysql_error\n db=$this->database\n $qstr\n ".mysqli_error(db::$conn)." (".mysqli_errno(db::$conn).") (final)\n".nl2br(print_r(debug_backtrace (),true))."\n\nGLOBALS\n", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
					//~ exit;
				//~ }
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
	function fetch_array($res) {
		if($this->phpver<7) 
			return mysql_fetch_array($res); else return mysqli_fetch_array($res);
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
	function affected_rows() {
		return mysqli_affected_rows(db::$conn); 
	}
	function dlookup($fld,$table,$query,$disp=0) {
		$res=$this->fetch_row($this->query("SELECT $fld FROM $table WHERE $query",$disp));
		//$this->notify_me("SELECT $fld FROM $table WHERE $query");
		if($res===false)
			return false;
		elseif(isset($res[0]))
			return $res[0];
		return false;
	}
	function dlast($fld,$table,$where,$disp=0) {
		$res= $this->fetch_row($this->query("SELECT $fld FROM $table WHERE $where ORDER BY id DESC LIMIT 1",$disp))[0];
		if($res===false)
			return false; else return $res;
	}
	function current_db() {
		$res=$this->query("select database()");
		return mysql_result($res,0);
	}
	function get_style_by_razdel($razdel) {
		$r=array(
		0=>"background-color:#FFFFEE;color:#5bc0de;",
		1=>"background-color:#E3E32B;color:#000000;", //C
		2=>"background-color:#008000;color:#FFFFFF;", //B
		3=>"background-color:#FFA500;color:#FFFFFF;", //A
		4=>"background-color:#A52A2A;color:#FFFFFF;", //D
		5=>"background-color:#1818A9;color:#FFFFFF;", //E
		6=>"background-color:#7f1b55;color:#FFFFFF;",
		7=>"background-color:#7c7e32;color:#FFFFFF;",
		8=>"background-color:#2d794c;color:#FFFFFF;",
		9=>"background-color:#6b4245;color:#FFFFFF;",
		10=>"background-color:#74096b;color:#FFFFFF;",
		11=>"background-color:#1a497a;color:#FFFFFF;",
		12=>"background-color:#7f6a33;color:#FFFFFF;",
		13=>"background-color:#4a3f00;color:#FFFFFF;",
		14=>"background-color:#4b3251;color:#FFFFFF;",
		15=>"background-color:#5a6b5b;color:#FFFFFF;",
		16=>"background-color:#486449;color:#FFFFFF;",
		17=>"background-color:#1e554f;color:#FFFFFF;",
		18=>"background-color:#6c1c21;color:#FFFFFF;",
		19=>"background-color:#45125d;color:#FFFFFF;",
		20=>"background-color:#1e7012;color:#FFFFFF;",
		21=>"background-color:#23726f;color:#FFFFFF;",
		22=>"background-color:#0b4a6f;color:#FFFFFF;",
		23=>"background-color:#562544;color:#FFFFFF;",
		24=>"background-color:#260c44;color:#FFFFFF;",
		25=>"background-color:#622325;color:#FFFFFF;",
		26=>"background-color:#512072;color:#FFFFFF;",
		27=>"background-color:#655d37;color:#FFFFFF;",
		28=>"background-color:#065826;color:#FFFFFF;",
		29=>"background-color:#635f44;color:#FFFFFF;",
		30=>"background-color:#044573;color:#FFFFFF;",
		31=>"background-color:#745439;color:#FFFFFF;",
		32=>"background-color:#723a24;color:#FFFFFF;",
		33=>"background-color:#7a2149;color:#FFFFFF;",
		34=>"background-color:#4a175a;color:#FFFFFF;",
		36=>"background-color:#074a6a;color:#FFFFFF;",
		35=>"background-color:#0c6023;color:#FFFFFF;",
		37=>"background-color:#726427;color:#FFFFFF;",
		38=>"background-color:#4f673e;color:#FFFFFF;",
		39=>"background-color:#72573e;color:#FFFFFF;",
		40=>"background-color:#3e2969;color:#FFFFFF;",
		41=>"background-color:#633e33;color:#FFFFFF;",
		42=>"background-color:#4d2716;color:#FFFFFF;",
		43=>"background-color:#0b5c4c;color:#FFFFFF;",
		44=>"background-color:#387f75;color:#FFFFFF;",
		45=>"background-color:#504146;color:#FFFFFF;",
		46=>"background-color:#367c3c;color:#FFFFFF;",
		47=>"background-color:#230b52;color:#FFFFFF;",
		48=>"background-color:#354326;color:#FFFFFF;",
		49=>"background-color:#4a4e72;color:#FFFFFF;",
		50=>"background-color:#582e63;color:#FFFFFF;",
		51=>"background-color:#7d5445;color:#FFFFFF;",
		52=>"background-color:#6c7674;color:#FFFFFF;",
		53=>"background-color:#5f3f11;color:#FFFFFF;",
		54=>"background-color:#047a57;color:#FFFFFF;",
		55=>"background-color:#2d2a58;color:#FFFFFF;",
		56=>"background-color:#115b6f;color:#FFFFFF;",
		57=>"background-color:#33135e;color:#FFFFFF;",
		58=>"background-color:#081930;color:#FFFFFF;",
		59=>"background-color:#696100;color:#FFFFFF;",
		60=>"background-color:#697e76;color:#FFFFFF;",
		61=>"background-color:#383c55;color:#FFFFFF;",
		62=>"background-color:#391b15;color:#FFFFFF;",
		63=>"background-color:#0f7b3c;color:#FFFFFF;",
		64=>"background-color:#087171;color:#FFFFFF;",
		65=>"background-color:#60570d;color:#FFFFFF;",
		66=>"background-color:#640b33;color:#FFFFFF;",
		67=>"background-color:#285135;color:#FFFFFF;",
		68=>"background-color:#1c754e;color:#FFFFFF;",
		69=>"background-color:#026c44;color:#FFFFFF;",
		);
		return $r[$razdel];
	}
	function dt1($tm) {
		if(!$tm)
			return false;
		return mktime(0,0,0,date("m",$tm),date("d",$tm),date("Y",$tm));
	}
	function dt2($tm) {
		if(!$tm)
			return false;
		return mktime(23,59,59,date("m",$tm),date("d",$tm),date("Y",$tm));
	}
	function date2tm($str) {
		if(empty($str) || !$str)
			return false;
		$dmy=explode(".",$str);
		foreach($dmy AS $val)
			if(!intval($val))
				return false;
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
		if($tm1<10)
			$tm1='0'.$tm1;
		if($tm2<10)
			$tm2='0'.$tm2;
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
	function merge_cards($uid1,$uid2) {
		//return false;
		//~ if($uid1<0 || $uid2>0)
			//~ return false;
		$r1=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE del=0 AND uid='$uid1'"));
		if(!$r1)
			return false;
		$r2=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE del=0 AND uid='$uid2'"));
		if(!$r2)
			return false;
		if( trim(strtolower($r1['email']))!=trim(strtolower($r2['email'])) ) {
			if($r1['mob_search'] != $r1['mob_search'])
				return false;
		}

		$user_id_1=$this->dlookup("id","users","klid={$r1['id']}");
		$user_id_2=$this->dlookup("id","users","klid={$r2['id']}");

		if($user_id_1 && $user_id_2) {
			print "<p class='alert alert-danger' >Ошибка объединения. $uid1 $uid2 - оба партнеры</p>";
			return false;
		}
		if(!$user_id_1 && $user_id_2) {
			$tmp=$uid1; $uid1=$uid2; $uid2=$tmp;
			$r1=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE del=0 AND uid='$uid1'"));
			$r2=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE del=0 AND uid='$uid2'"));
		}
		
		$this->vkt_email("MERGE UIDS uid1=$uid1  email1={$r1['email']} uid2=$uid2 email2={$r2['email']}","");

		print "$uid1 $uid2";
		//exit;


		if($r2['fl_newmsg']>$r1['fl_newmsg']) 
			$this->query("UPDATE cards SET fl_newmsg='{$r2['fl_newmsg']}',tm_lastmsg='{$r2['tm_lastmsg']}' WHERE uid='$uid1'");
		if($r2['user_id']>0 && $r1['user_id']==0)
			$this->query("UPDATE cards SET user_id='{$r2['user_id']}',utm_affiliate='".$r2['utm_affiliate']."' WHERE uid='$uid1'");
		//~ if(!intval($r1['utm_affiliate']) )
			//~ $this->query("UPDATE cards SET utm_affiliate='".$r2['utm_affiliate']."' WHERE uid='$uid1'");
		if($r2['razdel']!=4 && $r1['razdel']==4)
			$this->query("UPDATE cards SET razdel='{$r2['razdel']}' WHERE uid='$uid1'");
		if($r2['razdel']==3 && $r1['razdel']<=6) //if A
			$this->query("UPDATE cards SET razdel='{$r2['razdel']}' WHERE uid='$uid1'");
		if($r1['source_id']==0 && $r2['source_id']>0)
			$this->query("UPDATE cards SET source_id='{$r2['source_id']}' WHERE uid='$uid1'");

		if( (empty($r1['name']) || is_numeric($r1['name'])) && !empty($r2['name']) )
			$this->query("UPDATE cards SET name='".$this->escape($r2['name'])."',surname='".$this->escape($r2['surname'])."' WHERE uid='$uid1'");
		if(empty($r1['mob']))
			$this->query("UPDATE cards SET mob='".$this->escape($r2['mob'])."' WHERE uid='$uid1'");
		if(empty($r1['mob_search']))
			$this->query("UPDATE cards SET mob_search='".$this->escape($r2['mob_search'])."' WHERE uid='$uid1'");
		if(empty($r1['email']))
			$this->query("UPDATE cards SET email='".$this->escape($r2['email'])."' WHERE uid='$uid1'");
		if(empty($r1['city']))
			$this->query("UPDATE cards SET city='".$this->escape($r2['city'])."' WHERE uid='$uid1'");
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
		if(!intval($r1['tzoffset']) )
			$this->query("UPDATE cards SET tzoffset='".$r2['tzoffset']."' WHERE uid='$uid1'");
		if(!intval($r1['tm_first_time_opened']) )
			$this->query("UPDATE cards SET tm_first_time_opened='".$r2['tm_first_time_opened']."' WHERE uid='$uid1'");
		if(!intval($r1['wa_allowed']) )
			$this->query("UPDATE cards SET wa_allowed='".$r2['wa_allowed']."' WHERE uid='$uid1'");
		if(!intval($r1['telegram_id']) ) {
			$this->query("UPDATE cards SET telegram_id='".$r2['telegram_id']."' WHERE uid='$uid1'");
			$this->query("UPDATE cards SET telegram_nic='".$r2['telegram_nic']."' WHERE uid='$uid1'");
		}
		if(!intval($r1['vk_id']) ) 
			$this->query("UPDATE cards SET vk_id='".$r2['vk_id']."' WHERE uid='$uid1'");
		if(!intval($r1['funnel_id']) ) 
			$this->query("UPDATE cards SET funnel_id='".$r2['funnel_id']."' WHERE uid='$uid1'");
		if($r2['anketa']==1)
			$this->query("UPDATE cards SET anketa=1  WHERE uid='$uid1'");

		$this->query("UPDATE cards SET comm='".$this->escape($r1['comm'])."\n".$this->escape($r2['comm'])."'  WHERE uid='$uid1'");
		$this->query("UPDATE cards SET comm1='".$this->escape($r1['comm1'])."\n".$this->escape($r2['comm1'])."'  WHERE uid='$uid1'");

		$this->query("UPDATE cards SET uid_md5='".$this->uid_md5($uid1)."' WHERE uid='$uid1'");
		$this->query("UPDATE msgs SET uid='$uid1'  WHERE uid='$uid2'");
		$this->query("UPDATE utm SET uid='$uid1'  WHERE uid='$uid2'");
		//$this->query("UPDATE pixel SET uid='$uid1'  WHERE uid='$uid2'");
		//$this->query("UPDATE quiz SET uid='$uid1'  WHERE uid='$uid2'");
		//$this->query("UPDATE ppl SET uid='$uid1'  WHERE uid='$uid2'");
		//~ $this->query("UPDATE vktrade_send_log SET uid='$uid1'  WHERE uid='$uid2'");
		//~ $this->query("UPDATE vktrade_send_at_log SET uid='$uid1'  WHERE uid='$uid2'");
		//~ $this->query("UPDATE vktrade_send_at_msgs SET uid='$uid1'  WHERE uid='$uid2'");
		//~ $this->query("UPDATE vktrade_send_unsubscribe SET uid='$uid1'  WHERE uid='$uid2'");
		//$this->query("UPDATE course_access SET uid='$uid1'  WHERE uid='$uid2'");
		//$this->query("UPDATE course_log SET uid='$uid1'  WHERE uid='$uid2'");
		$this->query("UPDATE discount SET uid='$uid1'  WHERE uid='$uid2'");
		$this->query("UPDATE promocodes SET uid='$uid1'  WHERE uid='$uid2'");
		$this->query("UPDATE avangard SET vk_uid='$uid1'  WHERE vk_uid='$uid2'");
		$this->query("UPDATE avangard_stock SET uid='$uid1'  WHERE uid='$uid2'");
		//$this->query("UPDATE msgs_hook SET uid='$uid1'  WHERE uid='$uid2'");
		//$this->query("UPDATE head_control SET uid='$uid1'  WHERE uid='$uid2'");
		//$this->query("UPDATE funnels SET uid='$uid1'  WHERE uid='$uid2'");
		//$this->query("UPDATE cart SET uid='$uid1'  WHERE uid='$uid2'");
		$this->query("UPDATE bizon_log SET uid='$uid1'  WHERE uid='$uid2'");
		//$this->query("UPDATE ban SET uid='$uid1'  WHERE uid='$uid2'");
		//$this->query("UPDATE anketa_google SET uid='$uid1'  WHERE uid='$uid2'");

		$this->save_comm($uid1,0,"MERGED CARDS $uid1 AND $uid2 TO $uid1");
		$this->query("UPDATE cards SET del=1 WHERE uid='$uid2'");
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
		$out="($user_id) ".$this->dlookup("real_user_name","users","del=0 AND id=$user_id")." ";
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
			$out.="non checked: ".$this->fetch_assoc($this->query("SELECT COUNT(uid) AS cnt FROM cards WHERE cards.del=0 AND  tm>=$tm1 AND tm<=$tm2 AND user_id='$user_id' AND  tm_first_time_opened=0",0))['cnt']." ($all_leads)";

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
		return $this->check_mob($mob);
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
			$this->query("UPDATE cards SET fl_newmsg='$fl', tm_lastmsg='".time()."' WHERE uid='$uid'");
		} else
			$this->query("UPDATE cards SET fl_newmsg=0,fl_gpt=0,tm_lastmsg='".time()."' WHERE uid='$uid'");
	}
	function save_comm1($uid,$comm,$replace=false) {
		if(!$replace) {
			$comm_old=$this->dlookup("comm1","cards","uid=$uid");
			if($comm_old===false)
				return false;
			if(strpos($comm_old,$comm)!==false)
				return true;
			$comm=(trim($comm_old)!="")?$comm_old."\n---------\n".$comm:$comm;
		}
		$this->query("UPDATE cards SET tm_lastmsg='".time()."',comm1='".$this->escape($comm)."' WHERE uid=$uid",0);
		return true;
	}

	var $save_comm_tm_ignore=15; //60*60;
	var $save_comm_custom_fl=false;
	function save_comm($uid,$user_id,$comm,$source_id=0,$vote_vk_uid=0,$mode=0, $force=false) { //mode=1 - shift down mode=0 - replace FORCE=show in cards
		if(!intval($uid))
			return false;
		$last_tm=$this->dlast("tm","msgs","uid='$uid' AND source_id='$source_id' AND vote='$vote_vk_uid'");
		if( ((time()-$last_tm)<=$this->save_comm_tm_ignore)  && $source_id>0) {
			return false;
		}
		if($this->dlookup("id","msgs","uid='$uid' AND outg<2 AND tm=".time())) //pass 1 second make able display it in msgs.class
			sleep(1);
		//~ if(empty($comm))
			//~ $comm=$this->dlookup("source_name","sources","id='$source_id'");
		if($comm===false)
			$comm=$this->dlookup("source_name","sources","id='$source_id'");
		if($mode==1) {
			$old_comm=$this->dlookup("comm","cards","uid=$uid");
			//$comm=date("d/m H:i")." >> ".$comm."\n".$old_comm;
			$comm=$comm."\n".$old_comm;
		}
		$razdel_exclude=$this->razdel_exclude_for_save_comm;
		if(trim($comm)!="")
			$dt="(".date("d.m.Y").") "; else $dt="";
		//~ if(trim($comm)=="")
			//~ return false;
		$dt="";
		if(!$force) {
			$this->query("UPDATE cards SET tm_lastmsg='".time()."' WHERE uid='$uid'",0);
			if(!in_array($this->dlookup("razdel","cards","uid=$uid"),$razdel_exclude)) {
				//$this->query("UPDATE cards SET comm='$dt".$this->escape($comm)."' WHERE uid=$uid");
			} 
		} else {
			$this->query("UPDATE cards SET tm_lastmsg='".time()."',tm_comm='".time()."',comm='".$this->escape(mb_substr($comm,0,4096))."' WHERE uid='$uid'",0);
		}
		$imp=($source_id>0)?"10":"12";
		$custom_fl=($this->save_comm_custom_fl)?intval($this->save_comm_custom_fl):0;
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
				source_id='$source_id',
				custom='$custom_fl'
				",0);
		$insert_id=$this->insert_id();
		if($source_id>0 && $this->dlookup("use_in_cards","sources","id='$source_id'"))
			$this->query("UPDATE cards SET source_id='$source_id' WHERE uid=$uid");

		if($source_id && $this->database=='vkt') {
			if($weight=$this->dlookup("fl_active","sources","id='$source_id'")) {
				$cnt=$this->dlookup("cnt_active","cards","uid='$uid'")+$weight;
				$this->query("UPDATE cards SET cnt_active='$cnt',tm_last_active='".time()."' WHERE uid=$uid");
			}
		}
		
	//print "HERE_$source_id res=$res";
		return $insert_id; 
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
		return $this->fetch_assoc($this->query("SELECT * FROM cards WHERE cards.del=0 AND  uid='$uid'"));
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
	function check_mob_pass1($mob) {
		$res=str_replace([' ', '-', '(', ')','+','.'], '', trim($mob));
		if(strlen($res)==10)
			return "7".$res;
		if(strlen($res)<10)
			return $res;
		if(strlen($res)==11 && substr($res,0,1)=='8')
			return "7".substr($res, 1, strlen($res)-1);
		return $res;
	}
	function check_mob($mob) {
		if(empty(trim($mob)))
			return false;
		$mob=explode(',',$mob)[0];
		$mob=$this->check_mob_pass1($mob);
		if(strlen($mob)>=11 && strlen($mob)<=15) {
			if(is_numeric($mob)) {
				if(!intval($mob))
					return false;
				return $mob;
			}
		}
		return false;
	}
	function email($emails=array(), $subj, $body, $from="info@winwinland.ru",$fromname="WWL", $add_globals=false) {	
			include_once('/var/www/vlav/data/www/wwl/inc/unisender.class.php');
			$uni=new unisender('6s1414bffqhg69c1ggzw79wrtgw6zstdbd4k161o',$from,$fromname);
			if($add_globals) {
				$globals="<pre>".print_r($GLOBALS,true)."</pre>";
			} else
				$globals="";
			$body=preg_replace("/\{\{/","",$body);
			$globals=preg_replace("/\{\{/","",$globals);
			return $uni->email($emails[0],$subj,$body."\n\n".$globals);
	}
	function here($mess="",$exit=false) {
		if(@$_SESSION['username']=='vlav' || !isset($_SESSION['username'])) {
			print "<div class='alert alert-danger' >HERE_$mess</div>\n";
			if($exit)
				exit;
		}
	}
	function uid_md5($uid) {
		if(!$uid)
			return 0;
		if($this->is_md5($uid)) {
			if(!$uid=$this->get_uid($uid))
				return 0;
		}
		$n=$this->dlookup("uid_md5_n","cards","uid='$uid'");
		if(!$n)
			$n=0;
		return md5($uid.($uid*14+$n));
	}
	function notify_me($msg,$addslashes=true) {
		include_once "/var/www/vlav/data/www/wwl/inc/tg_bot.class.php";
		$tg=new tg_bot('f12');
		$msg=$addslashes ? addslashes($msg) : $msg ;
		$tg->send_msg(315058329,$msg);
	}
	function notify_chat($chat_id,$msg) {
		include_once "/var/www/vlav/data/www/wwl/inc/tg_bot.class.php";
		$tg=new tg_bot('f12');
		return $tg->send_msg($chat_id,addslashes($msg));
	}
	function notify_user($user_id,$msg) {
		$telegram_bot=$this->telegram_bot;
		$tg_id=$this->dlookup("telegram_id","users","del=0 AND id='$user_id'");
		if(!$tg_id)
			return false;
		if($this->dlookup("del","users","del=0 AND id='$user_id'")!=0)
			return false;
		include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
		$vk=new vklist_api;
		$vk->send_telegram_alert($msg, $tg_id, $vk->telegram_bots[$telegram_bot]);
	}
	function notify_log($step,$uid,$msg,$access_level,$user_id,$cards_user_id) {
		return;
		$str=$this->database." ".date("d.m.Y H:i:s")."  ($step) uid=$uid access=$access_level user_id=$user_id mentor=$cards_user_id \n";
		file_put_contents("notify.log",$str,FILE_APPEND);
		return;
		$fname=time()."-$uid-$user_id-$cards_user_id";
		//file_put_contents("/var/www/html/pini/yogahelpyou/tmp/$fname",$str);
	}
	function notify_new_lead($uid,$msg) {
		//return;
		$this->notify($uid,$msg);
	}
	function notify($uid,$msg,$key="") {
		global $tg_bot_notif,$ctrl_url,$DB200;
		if($tg_bot_notif)
			$this->telegram_bot=$tg_bot_notif;
		if(empty($this->telegram_bot))
			return false;
		if($DB200)
			$this->db200=$DB200;
		//$uid==-1 - common notification
		if(!intval($uid))
			return false;
		if(empty($this->db200)) {
			$this->db200="";
		}
		$notified=[];
		$razdel_exclude_A=$this->razdel_do_not_notify;
		$telegram_bot=$this->telegram_bot;
		include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
		$vk=new vklist_api;
		$tg_bot_id=isset($vk->telegram_bots[$telegram_bot])?$vk->telegram_bots[$telegram_bot]:$telegram_bot;
		$uid=intval($uid);
		if($uid!=-1) {
			$r=$this->fetch_assoc($this->query("SELECT *  FROM cards LEFT JOIN razdel ON cards.razdel=razdel.id WHERE cards.del=0 AND uid=$uid"));
			if(!$r)
				return false;
		} else {
			$r=array('razdel'=>0,'user_id'=>0,'surname'=>'','name'=>'','razdel-name'=>'-');
		}
		if($r['dont_disp_in_new']!=0)
			return false;

		if($this->database=='vkt') {
			if($cards0ctrl_id=$this->dlookup("ctrl_id","cards0ctrl","uid='$uid'")) {
				$r_cards0ctrl=$this->fetch_assoc($this->query("SELECT * FROM 0ctrl
															JOIN cards ON 0ctrl.uid=cards.uid
															WHERE 0ctrl.id='$cards0ctrl_id'"));
				
				$msg="💬 Ask for support from ($cards0ctrl_id) {$r_cards0ctrl['company']} ({$r_cards0ctrl['surname']} {$r_cards0ctrl['name']})\n$msg";
			}
		}
			
		if(in_array($r['razdel'],$razdel_exclude_A)) { //IF NOT NOTIFYED IN MAIN CONDITIONS (other,A)
			$msg="{$r['surname']} {$r['name']} ({$r['razdel_name']} - $uid)\n ".$this->db200."/msg.php?uid=$uid\n$msg";
			$res1=$this->query("SELECT * FROM users WHERE del=0 AND telegram_id!='' AND fl_notify_if_other=1 AND fl_allowlogin=1");
			while($r1=$this->fetch_assoc($res1)) {
				if(!in_array($r1['telegram_id'],$notified)) {
					$vk->send_telegram_alert($msg, $r1['telegram_id'], $tg_bot_id);
					$notified[]=$r1['telegram_id'];
					$this->notify_log($step=1,$uid,$msg,$r1['access_level'],$r1['id'],$cards_user_id=$r['user_id']);
				}
			}
		} else  {
			$user_id=($user_id)?$user_id:$r['user_id'];
			$real_user_name=$this->dlookup("real_user_name","users","del=0 AND id='$user_id'");
			if(empty($real_user_name))
				$real_user_name=$this->dlookup("username","users","del=0 AND id='$user_id'");
			$user_name= $real_user_name!='n/a' ? $real_user_name : "-";
			$man_info=($r['man_id']) ? $this->dlookup("real_user_name","users","del=0 AND id='{$r['man_id']}'") : "-";
			if($uid)
				$msg="{$r['name']} {$r['surname']}  ($uid:{$r['razdel_name']} | $user_name | $man_info) \n ".$this->db200."/msg.php?uid=$uid\n$msg";
			else
				$msg="";
			$res1=$this->query("SELECT * FROM users WHERE del=0 AND telegram_id!='' AND fl_notify_if_new=1 AND fl_allowlogin=1");
			while($r1=$this->fetch_assoc($res1)) {
				if($r1['access_level']>4) {
					if($r1['id']==$r['user_id']) {
						if(!in_array($r1['telegram_id'],$notified)) {
							$vk->send_telegram_alert("🟢 ".$msg, $r1['telegram_id'], $tg_bot_id);
							$notified[]=$r1['telegram_id'];
							$this->notify_log($step=2,$uid,$msg,$r1['access_level'],$r1['id'],$cards_user_id=$r['user_id']);
						}

						$user_id_5=$r1['id'];
						if($klid_5=$this->dlookup("klid","users","del=0 AND id='$user_id_5'")) {
							$user_id_mentor=$this->dlookup("user_id","cards","id='$klid_5'");
							if($tg_id_mentor=$this->dlookup("telegram_id","users","del=0 AND id='$user_id_mentor'")) {
								$vk->send_telegram_alert("🟡 ".$msg, $tg_id_mentor, $tg_bot_id);
							}
						}

					}
				} else {
					if($r1['fl_notify_of_own_only']==1) {
						$res_msg=$this->query("SELECT user_id FROM msgs WHERE outg=1 AND uid='$uid' ORDER BY id DESC LIMIT 5");
						while($r_msg=$this->fetch_assoc($res_msg)) {
							if($r_msg['user_id']==$r1['id']) {
								if(!in_array($r1['telegram_id'],$notified)) {
									$vk->send_telegram_alert($msg, $r1['telegram_id'], $tg_bot_id);
									$notified[]=$r1['telegram_id'];
									$this->notify_log($step=3,$uid,$msg,$r1['access_level'],$r1['id'],$cards_user_id=$r['user_id']);
								}
								break;
							}
						}
					} else {
						$add = ($r['fl_gpt']>0) ? "⚛️" : "";
						$add.=($r['user_id']==$r1['id'])?"⭐":"";
						if(!in_array($r1['telegram_id'],$notified)) {
							//if($r1['access_level']==4 && $r1['id']!=$r['man_id'] && $r['man_id']) { //notify manager for FREE leads also
							if($r1['access_level']==4 && $r1['id']!=$r['man_id'] ) {
									continue;
							}
							if($this->users_notif_get($r1['id'], $key)) {
								$vk->send_telegram_alert($add.$msg, $r1['telegram_id'], $tg_bot_id);
								$this->notify_log($step=4,$uid,$msg,$r1['access_level'],$r1['id'],$cards_user_id=$r['user_id']);
							}
							$notified[]=$r1['telegram_id'];
						}
					}
				}
			}
		}
	}
	function get_price_tm2($uid,$product_id) {
		if(!$uid=intval($uid))
			return false;
		$tm=time();
		if($price_id=$this->dlookup("price_id","discount","uid='$uid' AND product_id='$product_id' AND (dt1<$tm AND dt2>$tm)",0)) {
			if($price_id==2)
				return $this->dlookup("dt2","discount","price_id=2 AND uid='$uid' AND product_id='$product_id' AND (dt1<$tm AND dt2>$tm)");
			return false;
		}
		return false;
	}
	function yoga_is_price2($uid,$product_id=1) {
		global $base_prices;
		if($base_prices[$product_id][2]==$this->yoga_check_price($uid,$product_id))
			return true;
		else
			return false;
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

	function yoga_disp_price($uid,$product_id) {
		global $base_prices;
		$chk=$this->yoga_check_price($uid,$product_id);
		if($chk==$base_prices[$product_id]['2']) {
			$price1=$base_prices[$product_id]['1'];
			$price=$base_prices[$product_id]['2'];
			$name=$this->dlookup("name","cards","uid='$uid'");
			$dt=date("d.m.Y H:i",$dt2=$this->dlookup("dt2","discount","uid='$uid' AND dt1<".time()));
			$dt=($dt2)?"<span class='font14' style='line-height:1.1;' >действительно до $dt</span>":"";
			return "<div class='alert alert-success mb-0' >
						<div class='font14 pt-2 pb-2' >У вас есть спецпредложение <br> ($dt)</div>
						<span class='shop_price' >$price&nbsp;р.</span> <br>
					</div>";
		} else
			return "<span class='shop_price' >".$base_prices[$product_id]['1'].'&nbsp;р.'."</span>";
	}

	function price2_set($uid,$price_id,$tm1,$tm2,$product_id) {
		return $this->yoga_set_discount($uid,$price_id,$tm1,$tm2,$product_id);
	}
	function price2_clr($uid,$product_id) {
		$this->yoga_clr_discount($uid,$product_id);
	}
	function price2_chk_for_any($uid) {
		$tm=time();
		return ($this->dlookup("price_id","discount","uid='$uid' AND (dt1<$tm AND dt2>$tm)")==2)?true:false;
	}
	function price2_chk($uid,$product_id) {
		$tm=time();
		return ($this->dlookup("price_id","discount","uid='$uid' AND product_id='$product_id' AND (dt1<$tm AND dt2>$tm)",0)==2)?true:false;
	}
	function price2_chk_timeto($uid,$product_id) {
		$tm=time();
		if($this->price2_chk($uid,$product_id))
			return $this->dlookup("dt2","discount","uid='$uid' AND product_id='$product_id' AND (dt1<$tm AND dt2>$tm)");
		else
			return false;
	}

	function yoga_clr_discount($uid,$product_id) {
		$this->query("DELETE FROM discount WHERE dt2<".time(),0);
		$this->query("DELETE FROM discount WHERE uid='$uid' AND product_id='$product_id'",0);
	}
	function yoga_set_discount($uid,$price_id,$tm1,$tm2,$product_id) {
		global $base_prices; //prices.inc.php - array(0=>8990,1=>5990,2=>3990)
		if(!intval($uid)) {
			//print "yoga_set_discount: UID is not defined <br>\n";
			return false;
		}
		//print "HERE_ $uid $tm1 $tm2 $product_id"; exit;
		if(!intval($tm1))
			$tm1=time();
		if(!intval($tm2))
			return false;
		if(!intval($product_id))
			return false;
		//$tm1=$this->dt1($tm1);
		//~ $tm2=$this->dt2($tm2);
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
	public $promocode_apply_info=[];
	function promocode_apply($promocode,$sum,$pid) {
		$tm=time();
		$promo=mb_substr($promocode,0,128);
		if($r=$this->fetch_assoc($this->query("SELECT * FROM promocodes
			WHERE (product_id='$pid' OR product_id=1)
				AND (tm1<='$tm' AND tm2>='$tm')
				AND promocode LIKE '$promo' AND cnt!=0 ORDER BY id DESC LIMIT 1"))
			) {
			$promocode_id=$r['id'];
			if($r['uid']) {
				$fee_1=$r['fee_1'];
				$fee_2=$r['fee_2'];
			}
			$dt2_promocode=date('d.m.Y H:i',$r['tm2']);
			if($r['discount']>0) {
				$d=intval($r['discount']);
				if($d<100) {
					$sum=intval($sum*(100-$d)/100);
					$this->promocode_apply_info['msg']="<div class='alert alert-success mt-1 small' >Промокод применен. Ваша скидка $d%. <br>* действует до $dt2_promocode</div>";
					$this->promocode_apply_info['type']='discount';
					$this->promocode_apply_info['base']='percent';
				} elseif($d<$sum) {
					$sum -=intval($d);
					$this->promocode_apply_info['msg']="<div class='alert alert-success mt-1 small' >Промокод применен. Ваша скидка $d р. <br>* действует до $dt2_promocode</div>";
					$this->promocode_apply_info['type']='discount';
					$this->promocode_apply_info['base']='value';
				}
			} else {
				$sum=intval($r['price']);
				$this->promocode_apply_info['msg']="<div class='alert alert-success mt-1 small font16' >Промокод применен. Новая цена $sum р. <br>* действует до $dt2_promocode</div>";
				$this->promocode_apply_info['type']='price';
				$this->promocode_apply_info['base']='value';
			}
			$this->promocode_apply_info['id']=$promocode_id;
			return $sum;
		} else
			return false;
	}
	function promocode_get_fee($uid, $promocode_id, $promocodes_fee_arr = []): array {
		if (!$uid) {
			return [];
		}
		
		// Get actual usage count from database
		$avangard_cnt = $this->fetch_assoc($this->query("SELECT COUNT(*) as cnt FROM avangard WHERE vk_uid='$uid' AND res=1 AND amount>0 AND promocode_id='$promocode_id'"))['cnt'] ?? 0;
		
		// Determine which key to use (user-specific or default)
		$key = isset($promocodes_fee_arr[$uid]) ? $uid : 0;
		
		// If no fee structure exists for this key
		if (!isset($promocodes_fee_arr[$key])) {
			return [];
		}
		
		// Check if exact count exists
		if (isset($promocodes_fee_arr[$key][$avangard_cnt])) {
			$data = $promocodes_fee_arr[$key][$avangard_cnt];
			$effective_cnt = $avangard_cnt;
		} else {
			// Overlimit - find last available count
			$last_available_cnt = max(array_keys($promocodes_fee_arr[$key]));
			
			// Check if we should deny by overlimit
			if (isset($promocodes_fee_arr[$key][$last_available_cnt]['deny_by_overlimit']) && 
				$promocodes_fee_arr[$key][$last_available_cnt]['deny_by_overlimit']) {
				return ['overlimit' => true];
			}
			
			// Use last available values
			$data = $promocodes_fee_arr[$key][$last_available_cnt];
			$effective_cnt = $last_available_cnt;
		}
		
		// Return unified success response
		return [
			'cnt'           => (int)$effective_cnt,
			'for_zero_price' => $data['zero_price'] ?? 0,
			'for_price'      => $data['price'] ?? 0,
			'for_discount'   => $data['discount'] ?? 0,
			'fee_1'          => $data['fee_1'] ?? 0,
			'fee_2'          => $data['fee_2'] ?? 0,
			'hold_days'      => $data['hold_days'] ?? 0,
			'keep'           => $data['keep'] ?? 0,
		];
	}
	function promocode_get_last($uid) {
		return $this->dlast("promocode","promocodes","uid=$uid AND tm2>".time());
	}
	function promocode_gen($pref) {
		$promocode = $pref;
		for ($digits = 4; $digits < 12; $digits++) {
			$min = pow(10, $digits - 1); // minimum value
			$max = pow(10, $digits) - 1; // maximum value
			$n = rand($min, $max);
			$n_ = $min;
			while ($this->dlookup("id", "promocodes", "promocode='" . $promocode . $n . "'")) {
				$n = rand($min, $max);
				if (!$n_--) {
					break;
				}
			}
			if ($n_) {
				break;
			}
		}
		return $promocode.$n;
	}
	function promocode_validate($promocode) {
		if (!is_string($promocode)) {
			return '';
		}
		
		// Remove all non-printable characters (including control chars)
		$promocode = preg_replace('/[[:^print:]]/u', '', $promocode);
		
		// Remove dangerous characters that could be used in SQL/XSS
		$dangerous_chars = ['<', '>', '/', '\\', "'", '"', ';', '=', '(', ')', '*', '%', '&', '|', '`', '$'];
		$promocode = str_replace($dangerous_chars, '', $promocode);
		
		// Remove multiple spaces and trim
		$promocode = preg_replace('/\s+/', ' ', $promocode);
		$promocode = trim($promocode);
		
		// Convert to uppercase (common for promocodes)
		//$promocode = strtoupper($promocode);
		
		// Limit length (adjust as needed)
		$max_length = 50;
		if (mb_strlen($promocode) > $max_length) {
			$promocode = mb_substr($promocode, 0, $max_length);
		}
		
		return $promocode;
	}
	function promocode_add($promocode,$uid,$tm1,$tm2,$product_id,$discount,$price=0,$fee_1=0,$fee_2=0,$cnt=-1,$hold=0,$keep=0) {
		global $ctrl_id,$insales_id,$insales_shop;
		if(empty($promocode=$this->promocode_validate($promocode)))
			return false;

		$tm1=intval($tm1); //$this->dt1($tm1);
		$tm2=intval($tm2); //$this->dt2($tm2);
		if(!$tm2)
			return false;
		if($this->dlookup("id","promocodes","promocode LIKE '$promocode' AND uid!='$uid'")) {
			return false;
		}
		//$this->query("DELETE FROM promocodes WHERE tm2<".time());
		$this->query("DELETE FROM promocodes WHERE product_id='$product_id' AND uid='$uid' AND promocode LIKE '$promocode'");

		//~ if(!$last_id=$this->dlookup("id","promocodes","uid='$uid'
						//~ AND product_id='".intval($product_id)."'
						//~ AND discount='".intval($discount)."'
						//~ AND price='".intval($price)."'
						//~ AND fee_1='".floatval($fee_1)."'
						//~ AND fee_2='".floatval($fee_2)."'
						//~ ")) {
		if(!$last_id=$this->dlookup("id","promocodes","uid='$uid'
								AND product_id='".intval($product_id)."'
								")) {
							
			$hold=intval($hold);
			$keep=$keep ? 1: 0;
			$fl_fix_partner=$hold ?1 :0;
			
			$this->query("INSERT INTO promocodes SET
				promocode='".$this->escape($promocode)."',
				uid='".intval($uid)."',
				tm1='$tm1',
				tm2='$tm2',
				product_id='".intval($product_id)."',
				discount='".intval($discount)."',
				price='".intval($price)."',
				fee_1='".floatval($fee_1)."',
				fee_2='".floatval($fee_2)."',
				cnt='".intval($cnt)."',
				fl_fix_partner='$fl_fix_partner',
				hold='$hold',
				keep='$keep'
				");
			$promocode_id=$this->insert_id();
			
			if ($insales_id && $discount) {
				include_once "/var/www/vlav/data/www/wwl/inc/insales.class.php";
				$in = new insales($insales_id, $insales_shop);
				$in->ctrl_id = $ctrl_id ? $ctrl_id : $this->ctrl_id;
				$type_id = $discount < 100 ? 1 : 2;
				$p = [
					'code' => $promocode,
					'disabled' => false,
					'act_once' => false,
					'expired_at' => date("Y-m-d", $tm2),
					'type_id' => $type_id,
					'discount' => $discount,
				];
				$res = $in->create_promocode($p);
				if (isset($res['error']) && $res['http_code'] != 422) { //422- already exists
					$this->notify_me("promocode_add : insales : ERROR ctrl_id=$ctrl_id $insales_id $insales_shop \n".print_r($res,true));
				}
			}
			return $promocode_id;
		} else {
			$this->query("UPDATE promocodes SET
				tm1='$tm1',
				tm2='$tm2',
				cnt='".intval($cnt)."',
				fl_fix_partner='$fl_fix_partner',
				hold='$hold',
				keep='$keep'
				WHERE id=$last_id
				");
			return $last_id;
		}
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
	function vkt_email($subj,$msg) {
		$this->email($emails=array("vlav@mail.ru"), $subj,nl2br($msg), $from="info@winwinland.ru",$fromname="WWL", $add_globals=false);
	}
	function yoga_email($subj,$msg) {
		$this->email($emails=array("vlav@mail.ru"), $subj,$msg, $from="info@winwinland.ru",$fromname="YOGAHELPYOU", $add_globals=false);
	}
	function papa_email($subj,$msg) {
		$this->email($emails=array("vlav@mail.ru"), $subj,$msg, $from="info@winwinland.ru",$fromname="PAPAVDEKRETE", $add_globals=false);
	}
	function formula_email($subj,$msg) {
		$this->email($emails=array("vlav@mail.ru"), $subj,$msg, $from="info@winwinland.ru",$fromname="F12", $add_globals=false);
	}

	function course_access_granted($uid,$source_id) {
		return $this->dlookup("tm","msgs","source_id='$source_id' AND uid='$uid'",0); 
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
			//return true;
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
	function course_log_chk($uid,$url,$days=7) {
		//возвращат со скольких айпи были заходы на курс с данным урлом
		$tm1=$this->dt1(time()-($days*24*60*60));
		$tm2=time();
		$res=$this->query("SELECT COUNT(ip) FROM
			(SELECT ip FROM `course_log`
			WHERE uid='$uid' AND referer LIKE '".$this->escape($url)."'
			AND tm>=$tm1 AND tm<=$tm2
			AND del=0
			GROUP BY ip) AS q1");
		return $this->fetch_row($res)[0];
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
	function random_string($length=6) {
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'; // You can modify this to include numbers or special characters if needed.
		$randomString = substr(str_shuffle(str_repeat($characters, ceil($length / strlen($characters)))), 0, $length);
		return $randomString;
	}
	function get_webinar_tm($project) {
		if($project=='style-inside') {
			$fname="https://style-inside.ru/webinar_tm.txt";
		}
		return intval(file_get_contents($fname));
	}

	function is_partner_db($uid) {
		if($this->dlookup("id","users","del=0 AND klid='".$this->dlookup("id","cards","uid='$uid'")."'"))
			return true; else return false;
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

	
	var $vktrade_send_path="";
	var $vktrade_send_email_from_name="";
	var $vktrade_send_email_from="info@winwinland.ru";
	var $vktrade_send_testemail_to="vlav@mail.ru";
	var $vktrade_send_testvk_to="198746774";
	var $vk_token="54ab01de9f02b3a1d8d08bb5cd0350edfc02fecaf66f66067d9d66ffe21fb3e2949a5877d098d872696f5";
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
	var $pact_token="";
	
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
	function vktrade_send_vk___($uid,$msg) {
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
	var $vktrade_send_skip_wa=false; //ignore_wa
	var $vktrade_send_wa_only=false; //send wa only, not tg
	var $vktrade_send_tg_only=false; //send tg only, not wa
	var $vktrade_send_vk_only=false; //send tg only, not wa
	var $vktrade_send_tg_video_note=false;
	var $vktrade_send_tg_photo=false;
	var $vktrade_send_tg_video=false;
	var $vktrade_send_tg_audio=false;
	var $vktrade_send_vk_photo=false;
	var $vktrade_send_vk_video=false;
	var $vktrade_send_res=[];
	var $vktrade_send_wa_test=false; //false or filename where to add uids instead of sending
	function vktrade_send_wa($uid,$msg,$source_id=3,$num=0,$attach=false,$force_if_not_wa_allowed=false) {
		if(in_array($uid,$this->vktrade_send_at_uids_ban)) {
			print "$uid BANNED in vktrade_send_at_uids_ban <br> \n";
			return false;
		}

		if($this->vktrade_send_wa_test) {
			$file=$this->vktrade_send_wa_test;
			$this->vktrade_send_res=['wa'=>3,'vk'=>3,'tg'=>3,'email'=>3];
			print "uid=$uid vktrade_send_wa_test=$file - not realy sent \n";
			return file_put_contents($file, date('d.m.Y H:i:s')." ".$uid . PHP_EOL, FILE_APPEND);
		}

		$this->vktrade_send_res=['wa'=>0,'vk'=>0,'tg'=>0,'email'=>0];
		if(!$this->vktrade_send_wa_only && !$this->vktrade_send_vk_only ) {
			if($this->vktrade_send_tg_photo) {
				$this->vktrade_send_tg($uid,NULL,3,0,false);
				$this->vktrade_send_tg_photo=false;
			} elseif($this->vktrade_send_tg_video) {
				$this->vktrade_send_tg($uid,NULL,3,0,false);
				$this->vktrade_send_tg_video=false;
			} elseif($this->vktrade_send_tg_video_note) {
				$this->vktrade_send_tg($uid,NULL,3,0,false);
				$this->vktrade_send_tg_video_note=false;
			} elseif($this->vktrade_send_tg_audio) {
				$this->vktrade_send_tg($uid,NULL,3,0,false);
				$this->vktrade_send_tg_audio=false;
			}
			usleep(100000);
			if($this->get_tg_id($uid)) {
				if($this->vktrade_send_tg($uid,$msg,$source_id,$num,$attach)) {
					$this->vktrade_send_res['tg']=1;
				} else
					$this->vktrade_send_res['tg']=2;
			}
		}
		if(!$this->vktrade_send_tg_only && !$this->vktrade_send_wa_only) {
			if($this->get_vk_id($uid)) {
				if($this->vktrade_send_vk_photo)
					$this->vktrade_send_vk($uid,NULL,3,0,false);
				elseif($this->vktrade_send_vk_video)
					$this->vktrade_send_vk($uid,NULL,3,0,false);
				$this->vktrade_send_vk_photo=false;
				$this->vktrade_send_vk_video=false;
				usleep(100000);
				if($this->vktrade_send_vk($uid,$msg,$source_id,$num,$attach)) {
					$this->vktrade_send_res['vk']=1;
				} else
					$this->vktrade_send_res['vk']=2;
			}
		}
		
		if($this->vktrade_send_res['vk']==1 || $this->vktrade_send_res['tg']==1)
			return true;
			
		if(!$this->vktrade_send_tg_only && !$this->vktrade_send_vk_only && !$this->vktrade_send_skip_wa) {
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
			if($attach || !empty($msg)) {
				$res=$wa->send($this,$uid,$msg,$user_id=0,$num,$source_id,$save_outg,$force_if_not_wa_allowed);
				if(!$res) {
					$this->print_log($uid, print_r($wa->send_msg_error,true)." \n");
					$this->vktrade_send_res['wa']=2;
				} else
					$this->vktrade_send_res['wa']=1;
				return $res;
			} else {
				$this->print_log($uid, "WA send error - msg is empty \n");
				return false;
			}
		}
		return false;
	}

	var $vktrade_send_tg_bot='yogahelpyou_bot';
	function get_tg_id($uid) {
		return $this->dlookup("telegram_id","cards","uid='$uid'");
	}
	function get_vk_id($uid) {
		$vk_id=$this->dlookup("vk_id","cards","uid='$uid'");
		if(!$vk_id && $uid>0)
			$this->query("UPDATE cards SET vk_id='$uid' WHERE uid='$uid'");
		return $vk_id;
	}
	function vktrade_send_tg($uid,$msg,$source_id=0,$num=0,$attach=false) {
		$tg_id=$this->get_tg_id($uid);
		if($tg_id) {
			include_once "/var/www/vlav/data/www/wwl/inc/tg_bot.class.php";
			//print "HERE $this->vktrade_send_tg_bot";
			$tg=new tg_bot($this->vktrade_send_tg_bot);
			$ok=false;
			if($this->vktrade_send_tg_video_note) {
				if($tg->send_video_note($tg_id,$this->vktrade_send_tg_video_note)) {
					$outg=1;
					$fname=basename($this->vktrade_send_tg_video_note);
					$this->query("INSERT INTO msgs SET
								uid='$uid',
								acc_id=103,
								tm=".time().",
								user_id=0,
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
			} elseif($this->vktrade_send_tg_photo) {
				if($tg->send_photo($tg_id,$this->vktrade_send_tg_photo)) {
					$outg=1;
					$fname=basename($this->vktrade_send_tg_photo);
					$this->query("INSERT INTO msgs SET
								uid='$uid',
								acc_id=103,
								tm=".time().",
								user_id=0,
								msg='ОТПРАВЛЕНО ФОТО: $fname',
								outg=$outg,
								vote='$num',
								new='".intval($num)."',
								source_id='$source_id'					
								",0);
					$ok=true;
					//return true;
				} else {
					//$this->save_comm($uid,0,"Error sending TG photo",1003);
				}
			} elseif($this->vktrade_send_tg_video) {
				//print "HERE_$this->vktrade_send_tg_video";
				if($tg->send_video($tg_id,$this->vktrade_send_tg_video)) {
					$outg=1;
					$fname=basename($this->vktrade_send_tg_video);
					$this->query("INSERT INTO msgs SET
								uid='$uid',
								acc_id=103,
								tm=".time().",
								user_id=0,
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
			} elseif($this->vktrade_send_tg_audio) {
				//print "HERE_$this->vktrade_send_tg_video";
				if($tg->send_audio($tg_id,$this->vktrade_send_tg_audio)) {
					$outg=1;
					$fname=basename($this->vktrade_send_tg_audio);
					$this->query("INSERT INTO msgs SET
								uid='$uid',
								acc_id=103,
								tm=".time().",
								user_id=0,
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
				if($tg->send_msg($tg_id,$msg)) {
					$outg=($source_id)?2:1;
					$outg=1;
					$this->query("INSERT INTO msgs SET
								uid='$uid',
								acc_id=103,
								tm=".time().",
								user_id=0,
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

	function vktrade_send_vk($uid,$msg,$source_id=3,$num=0,$attach=false) {
		$vk_uid=$this->get_vk_id($uid);
		if($vk_uid>0) {
			if($this->vktrade_send_vk_video)
				$attach=$this->vktrade_send_vk_video;
			if($this->vktrade_send_vk_photo)
				$attach=$this->vktrade_send_vk_photo;

			include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
			$vk=new vklist_api($this->vk_token);
			if(!$res=$vk->vk_msg_send($vk_uid, $msg, $fake=false, $chat_id=false, $attach, $peer_id=false)) {
				$outg=2;
				$txt=($attach)?"VIDEO ".$attach."\n".$msg:$msg;
				$this->query("INSERT INTO msgs SET
							uid='$uid',
							acc_id=2,
							tm=".time().",
							user_id=0,
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

	function vktrade_send_prepare_msg($uid,$msg) {
		$r=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE uid='$uid'"));
		if(!$r)
			return $msg;
		$name=$r['name'];
		$uid_md5=$r['uid_md5'];
		$email=$r['email'];
		$phone=$r['mob_search'];
		$klid=$r['id'];
		$msg=preg_replace("|\%username\%|s",$name,$msg);
		//$msg=preg_replace("|\%userid\%|s",$uid_to,$msg);
		$msg=preg_replace("|\%userid\%|s",$uid_md5,$msg);
		$msg=preg_replace("|\#uid_md5|s",$uid_md5,$msg);
		$msg=preg_replace("|\#name|s",$name,$msg);
		//$msg=preg_replace("|\#uid|s",$uid_to,$msg);
		$msg=preg_replace("|\#uid|s",$uid_md5,$msg);
		$msg=preg_replace("|\#email|s",$email,$msg);
		$msg=preg_replace("|\#phone|s",$phone,$msg);
		$msg=preg_replace("|\#klid|s",$klid,$msg);
		return $msg;
	}


	var $vkt_send_id=false;
	var $vktrade_send_at_notify=false;
	var $vktrade_send_at_mark_new=0;
	var $vktrade_send_at_num=0;
	var $vktrade_send_at_sid_exclude=[];
	var $vktrade_send_at_sid_exclude_1=[]; //array[sid=>tm_from] tm_from - check from this tm //если хоть один попадает - пропускаем
	var $vktrade_send_at_attach=false;
	var $vktrade_send_at_attach_arr=[]; // user_id=>attach_id
	var $razdel_ban=[5,6,7];
	var $fl_if_not_scheduled=false;
	var $vktrade_send_at_vote=0; //for any purpose
	var $vktrade_send_at_setsid=false; //for any purpose
	var $vktrade_send_at_promocode=false; //promocode_add($promocode,$uid,$tm1,$tm2,$product_id,$discount,$price=0)
	var $vktrade_send_at_uids_ban=[29362230];
	
	var $sp_project=false;
	var $sp_subj='';
	var $sp_from_email='';
	var $sp_from_name='';

	var $vktrade_send_at_funnel=false;
	var $vktrade_send_at_cmd='';
	
	function print_log($uid,$msg) {
		print "$msg \n";
		if($uid) {
			$dt=date("d.m.Y H:i:s");
			$this->query("INSERT INTO vktrade_send_at_msgs SET uid='$uid',msg='".$this->escape($msg)."',tm='".time()."',dt='$dt'");
		}
	}
	
	function vktrade_send_at($msgs,$source_id,$hm=0,$dt=0,$scan_min_ago,$scan_days_ago,$dt_from=0,$dt_to=0,$force=false,$test2me_uid=false) {
		return;
		// $hm+$dt - дата и время точное во сколько запустить рассылку, 1 раз
		// $scan_min_ago - сканировать интервал в одну минуту, начиная с scan_min_ago минут назад
		// $scan_days_ago -  сканировать интервал в один день, начиная с scan_days_ago дней назад
		// $scan_days_ago=-1 - весь период
		// $dt_from,$dt_to - запрос работает с этой даты, по эту дату
		// $force
		// $test2me_uid if NOT false - send one message to uid immidiatelly
		global $argv;
		$cmd = (isset($argv[0]) && isset($argv[1]))?basename($argv[0])." ".$argv[1]:'';
		
		$this->query("DELETE FROM vktrade_send_at_msgs WHERE tm<".(time()-(30*24*60*60)));
		
		if(!is_array($msgs)) {
			print "msgs is no array! [wa=>mess]";
			return;
		}
		if($this->vktrade_send_at_funnel) {
			include_once "/var/www/vlav/data/www/wwl/inc/funnel.class.php";
			$fnl=new funnel($this->database); 
		}
		if(isset($msgs['email']) && !empty($msgs['email']) ) {
			include_once "/var/www/vlav/data/www/wwl/inc/sendpulse.class.php";
			$sp=new sendpulse($this->sp_project);
		}
		if($test2me_uid) {
			if(isset($msgs['wa'])) {
				if($this->vktrade_send_at_num>0)
					$this->set_chat_bot($test2me_uid,$num=$this->vktrade_send_at_num,2,$bot_id=101);
				if($this->vktrade_send_wa($test2me_uid,$msg=$this->vktrade_send_prepare_msg($test2me_uid,$msgs['wa']),3,0,$this->vktrade_send_at_attach))
					$this->print_log($test2me_uid, "Test WA message sent to uid=$test2me_uid OK <br>\n");
			}
			if(isset($msgs['email']) && !empty($msgs['email'])) {
				$email_test=$this->dlookup("email","cards","uid='$test2me_uid'");
				$sp->email_by_template($template_id=$msgs['email'],
									$to_email=$email_test,
									$to_name=$this->dlookup("name","cards","uid='$test2me_uid'"),
									$this->sp_subj,
									$this->sp_from_email,
									$this->sp_from_name,
									$this->uid_md5($test2me_uid) );
				$this->print_log($test2me_uid, "Test EMAIL message sent to uid=$test2me_uid email=$email_test OK <br>\n");
			}
			//promocode_add($promocode,$uid,$tm1,$tm2,$product_id,$discount,$price=0)
			if($this->vktrade_send_at_promocode) {
				$promocode=rand(10000,99999);
				foreach($this->vktrade_send_at_promocode['products'] AS $pid=>$price) {
					$this->promocode_add($promocode,
						$test2me_uid,
						$this->vktrade_send_at_promocode['tm1'],
						$this->vktrade_send_at_promocode['tm2'],
						$pid,
						0,
						$price
						);
				}
				$msg="🎁 Ваш промокод: $promocode \n";
				if($this->vktrade_send_wa($test2me_uid,$msg=$this->vktrade_send_prepare_msg($test2me_uid,$msg),3,0,false) )
					$this->print_log($test2me_uid, "Test WA message sent to uid=$test2me_uid OK <br>\n");
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
				$this->print_log(0, "Не подошло время. Сейчас ".date("d.m.Y H:i",$tm1).", а запуск в ".date("d.m.Y H:i",$tm1)."-".date("d.m.Y H:i",$tm2)."  Выход\n");
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
					$this->print_log(0, "hm=$hm time has not come yet\n");
					return false;
				}
			}
		}
//print "HERE_".date('d/m/Y H:i',$tm1)."\n";
		if($scan_days_ago>0) //
			$scan_min_ago=0;

		if($scan_min_ago>0) {
			$tm1=(intval(time()/60)-$scan_min_ago)*60;
			$tm2=$tm1+60;
		}

		if($scan_days_ago==-1) {
			$tm1=0;
			$tm2=time();
		}

		$tm1_=$tm1;
		$tm2_=$tm2;
		if($scan_min_ago>0) {
			$tm1_=$tm1+60;
			$tm2_=$tm2-60;
		}

	
		print "\n";
		$this->print_log(0, "source_id=$source_id \n");
		$this->print_log(0, "scan_min_ago: $scan_min_ago\n");
		$this->print_log(0, "tm_from ".date("d.m.Y H:i",$tm_from)."\n");
		$this->print_log(0, "tm_to ".date("d.m.Y H:i",$tm_to)." \n");
		$this->print_log(0, "tm1 ".date("d.m.Y H:i",$tm1)." $tm1 \n");
		$this->print_log(0,"tm2 ".date("d.m.Y H:i",$tm2)." $tm2 \n");

		
		$where_exclude="source_id=-1";
		foreach($this->vktrade_send_at_sid_exclude AS $sid_exclude)
			$where_exclude.=" OR source_id=$sid_exclude";
		//print $where_exclude; exit;
		
		$where="source_id='$source_id' AND (tm>=$tm_from AND tm>=$tm1 AND tm<=$tm2 AND tm<=$tm_to)";
			
		$res=$this->query("SELECT * FROM msgs WHERE $where ORDER BY tm DESC" ,0);
		$n=0; $n_ok=0; $n_email_ok=0;
		while($r=$this->fetch_assoc($res)) {
			$dt=date("d.m.Y H:i",$r['tm']);
			$uid=$r['uid'];
			$user_id=$this->dlookup("user_id","cards","uid='$uid'");
			$this->print_log($uid, "$n $dt $uid $user_id");
			if($force) {
				print "";
				if($this->dlookup("id","msgs","uid='$uid' AND source_id='$source_id' AND tm>'{$r['tm']}'",0)) {
					$this->print_log($uid, "PASSED: uid=$uid source_id=$source_id at $dt IS NOT LAST OCCURENCE. Continued \n");
					continue;
				}
				if($this->vktrade_send_at_funnel) {
					$uid_funnel=$fnl->get_last_funnel($uid);
					if($uid_funnel != $this->vktrade_send_at_funnel) {
						$this->print_log($uid, "PASSED: vktrade_send_at_funnel=$this->vktrade_send_at_funnel uid=$uid is in funnel $uid_funnel. Continued \n");
						continue;
					}
				}
				if(in_array($this->dlookup("razdel","cards","uid='$uid'"),$this->razdel_ban) && $uid!=-1002) {
					$this->print_log($uid, "PASSED: uid=$uid is in razdel_ban list. Continued \n");
					continue;
				}
				if($this->fl_if_not_scheduled) {
					if( $this->dlookup("tm_schedule","cards","uid='$uid'") ) {
						$this->print_log($uid, "PASSED: uid=$uid is scheduled. Continued \n");
						continue;
					}
				}
				if($this->dlookup("id","msgs","uid='$uid' AND ($where_exclude)")) {
					$this->print_log($uid, "PASSED: uid=$uid is in vktrade_send_at_sid_exclude list. Continued \n");
					continue;
				}

				foreach($this->vktrade_send_at_sid_exclude_1 AS $sid_exclude=>$tm_from) {
					if($this->dlookup("id","msgs","uid='$uid' AND tm>'$tm_from' AND source_id='$sid_exclude'")) {
						$this->print_log($uid, "PASSED: uid=$uid is in vktrade_send_at_sid_exclude_1 list (uid='$uid' AND tm>'$tm_from' AND source_id='$sid_exclude'). Continued \n");
						continue;
					}
				}

				if(isset($msgs['wa']) ) {
					$msg_wa=$this->vktrade_send_prepare_msg($uid,$msgs['wa']);
	//~ var $vktrade_send_tg_video_note=false;
	//~ var $vktrade_send_tg_photo=false;
	//~ var $vktrade_send_tg_video=false;
	//~ var $vktrade_send_vk_photo=false;
	//~ var $vktrade_send_vk_video=false;
					$msg_md5=md5($msg_wa.$this->vktrade_send_tg_video_note.$this->vktrade_send_tg_video.$this->vktrade_send_tg_photo.$this->vktrade_send_vk_video.$this->vktrade_send_vk_photo);
					if($this->dlookup("id","vktrade_send_at_log","uid=$uid AND source_id=$source_id AND tm1>=$tm1 AND tm2<=$tm2 AND msg_md5='$msg_md5'",0)) {
						$this->print_log($uid, " WA PASSED because in vktrade_send_at_log\n");
						continue;
					}
					if($this->vktrade_send_at_num>0)
						$this->set_chat_bot($uid,$num=$this->vktrade_send_at_num,2,$bot_id=101);
					if(isset($this->vktrade_send_at_attach_arr[$user_id]) )
						$this->vktrade_send_at_attach=[$this->vktrade_send_at_attach_arr[$user_id]];
					if($this->vktrade_send_at_attach)
						$this->print_log($uid, "attach: ".print_r($this->vktrade_send_at_attach,true)." \n");
					$result=$this->vktrade_send_wa($uid,$msg=$msg_wa,3,$num=0,$this->vktrade_send_at_attach);
					//$result=true;
					if($result) {
						$this->print_log($uid, "SENT WA message  to uid=$uid OK \n");
						$n_ok++;
						$this->save_comm($uid,0,"Отправлена рассылка",101,$this->vktrade_send_at_vote);
						if(intval($this->vktrade_send_at_setsid))
							$this->save_comm($uid,0,false,$this->vktrade_send_at_setsid);
						if($this->vktrade_send_at_mark_new>0)
							$this->mark_new($uid,$this->vktrade_send_at_mark_new);
						if( !empty($this->vktrade_send_at_notify) ) {
							$this->notify($uid,$this->vktrade_send_at_notify);
							//$this->save_comm($uid,0,"notify:".$this->vktrade_send_at_notify,101);
						}
					} else {
						$this->print_log($uid, "SENT WA message  to uid=$uid vktrade_send_wa ERROR \n");
						//$this->notify($uid, "❗Ошибка отправки: $msg");
					}
					$file_name=empty($this->vktrade_send_at_cmd)?$cmd:$this->vktrade_send_at_cmd;
					$this->query("INSERT INTO vktrade_send_at_log SET uid='$uid', tm='".time()."',tm1=$tm1_, tm2=$tm2_, source_id='$source_id',msg_md5='$msg_md5',fname='$file_name',res_wa='$result'");
					$this->query("INSERT INTO vkt_send_log SET
						vkt_send_id='$vkt_send_id',
						uid='$uid',
						tm='".time()."',
						res_vk='{$this->vktrade_send_res['vk']}',
						res_tg='{$this->vktrade_send_res['tg']}',
						res_wa='{$this->vktrade_send_res['wa']}'
						");
				}
				if(isset($msgs['email']) && !empty($msgs['email']) ) {
					$msg_md5=md5(intval($msgs['email']));
					$tm=time();
					if($this->dlookup("id","vktrade_send_at_log","uid=$uid AND source_id=$source_id AND tm1>=$tm1 AND tm2<=$tm2 AND msg_md5='$msg_md5' AND res_email=1",0)) {
						$this->print_log($uid, " EMAIL PASSED because in vktrade_send_at_log\n");
						continue;
					}
					$res_email=$sp->email_by_template($template_id=$msgs['email'],
										$to_email=$this->dlookup("email","cards","uid='$uid'"),
										$to_name=$this->dlookup("name","cards","uid='$uid'"),
										$this->sp_subj,
										$this->sp_from_email,
										$this->sp_from_name,
										$this->uid_md5($uid) );
					$n_email_ok++;
					$file_name=empty($this->vktrade_send_at_cmd)?$cmd:$this->vktrade_send_at_cmd;
					$this->query("INSERT INTO vktrade_send_at_log SET uid='$uid', tm='".time()."',tm1=$tm1_, tm2=$tm2_, source_id='$source_id',msg_md5='$msg_md5',fname='$file_name',res_email='$res_email'");
				}

				if($this->vktrade_send_at_promocode) {
					$promocode=rand(10000,99999);
					foreach($this->vktrade_send_at_promocode['products'] AS $pid=>$price) {
						$this->promocode_add($promocode,
							$uid,
							$this->vktrade_send_at_promocode['tm1'],
							$this->vktrade_send_at_promocode['tm2'],
							$pid,
							0,
							$price
							);
					}
					$msg="🎁 Ваш промокод: $promocode \n";
					if($this->vktrade_send_wa($uid,$msg=$this->vktrade_send_prepare_msg($uid,$msg),3,0,false) )
						$this->print_log($uid, "PROMOCODE message sent to uid=$uid OK <br>\n");
				}

			}
			$n++;
		}
		$this->print_log(0, "SENT wa_ok=$n_ok email_ok=$n_email_ok ( from all =$n) \n");
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
		return false;
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
				$bg_msg_badge="secondary";
			elseif($fl_newmsg==4)
				$bg_msg_badge="primary";
			elseif($fl_newmsg==3)
				$bg_msg_badge="success";
			elseif($fl_newmsg==2)
				$bg_msg_badge="danger";
			elseif($fl_newmsg==1)
				$bg_msg_badge="warning";
			elseif($fl_newmsg==0)
				$bg_msg_badge="info";	
			return $bg_msg_badge;
			//~ if($fl_newmsg>4)
				//~ $bg_msg_badge="background-color:#FFFF00;";
			//~ elseif($fl_newmsg==4)
				//~ $bg_msg_badge="background-color:#6060FF;";
			//~ elseif($fl_newmsg==3)
				//~ $bg_msg_badge="background-color:#56b849;";
			//~ elseif($fl_newmsg==2)
				//~ $bg_msg_badge="background-color:#d9534f;";
			//~ elseif($fl_newmsg==1)
				//~ $bg_msg_badge="background-color:#f0ad4e;";
			//~ elseif($fl_newmsg==0)
				//~ $bg_msg_badge="background-color:##7F7F7F;";	
			//~ return $bg_msg_badge;
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
		$res_p=$this->query("SELECT * FROM c_persons JOIN cards ON cards.uid=c_persons.uid WHERE cards.del=0 AND cid=$customer_id AND c_persons.del=0 ORDER BY fl_contact DESC");
		while($r_p=$this->fetch_assoc($res_p)) {
			print "<div class='card bg-light p-2' >{$r_p['surname']} {$r_p['name']}</div>";
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
		$this->db200="";
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
				$this->notify($uid,$msg="");
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
		return 2;
		$r=$this->fetch_assoc($this->query("SELECT * FROM vklist_acc WHERE del=0 AND last_error=0 AND fl_allow_read_from_all=0 AND token!='' ORDER BY num,id LIMIT 1"));
		if($r)
			return $r;
		else {
				print "<div class='alert alert-danger' >Нет подключенных работающих аккаунтов</div> \n"; 
				return false;
			}
	}
	function get_vk_token() {
		if($this->num_rows($this->query("SHOW TABLES LIKE 'vklist_acc'"))==1)
			return $this->dlookup("token","vklist_acc","id=2");
		else
			return false;
		
		//~ if($this->num_rows($this->query("SHOW TABLES LIKE 'vklist_acc'"))==1)
			//~ return $this->dlookup("token","vklist_acc","fl_allow_read_from_all=1");
		//~ else
			//~ return false;
	}
	function make_link_clickable($text) {
		//return $text;
		return preg_replace('/(http:\/\/|https:\/\/)?(www)?([\da-z\.\-]+)\.([a-z\.]{2,6})([\/\w\.\=\?\%\&\#\-\;\+]*)?/i', 
		' <a href="\1\2\3.\4$5" target=\'_blank\'>\\1\\2\\3.\\4$5</a>', 
		$text);
	}
	function get_cwd_by_ctrl_id($ctrl_id) {
		$tmp=$this->database;
		$this->connect('vkt');
		$ctrl_dir=$this->dlookup("ctrl_dir","0ctrl","id='$ctrl_id'");
		$this->connect($tmp);
		return "/var/www/vlav/data/www/wwl/d/$ctrl_dir";
	}
	function get_for16_url($ctrl_dir) {
		return "https://for16.ru/d/$ctrl_dir";
	}
	function get_for16_cwd($ctrl_dir) {
		return "/var/www/vlav/data/www/wwl/d/$ctrl_dir";
	}
	function get_user_id($klid) {
		if(!$klid)
			return(0);
		return $this->dlookup("id","users","del=0 AND klid='$klid'");
	}
	function get_klid($user_id) {
		if(!$user_id)
			return(0);
		return $this->dlookup("klid","users","del=0 AND id='$user_id'");
	}
	function get_klid_by_uid($uid) { //for partners only!!!
		if(!$uid)
			return(0);
		return $this->dlookup("id","cards","del=0 AND uid='$uid'");
	}
	function get_klid_by_bc($bc) {
		if(!$bc)
			return(0);
		if($klid=$this->dlookup("klid","users","del=0 AND bc='$bc'"))
			return $klid;
		$tm=time();
		if($uid=$this->dlookup("uid","promocodes","tm1<$tm AND tm2>$tm AND promocode LIKE '".$this->escape($bc)."'")) {
			return $this->get_klid_by_uid($uid);
		}
		return 0;
	}
	function get_uid_by_klid($klid) {
		return $this->dlookup("uid","cards","del=0 AND id='$klid'");
	}
	function get_uid($uid) {
		if(!$uid)
			return(false);
		if($this->is_md5($uid))
			return $this->dlookup("uid","cards","del=0 AND uid_md5='$uid'");
		if(intval($uid)>0) {
			if($crm_uid=$this->dlookup("uid","cards","del=0 AND vk_id='$uid'"))
				return $crm_uid;
			$crm_uid=$this->dlookup("uid","cards","del=0 AND uid='$uid'");
			if( $crm_uid) {
				$this->query("UPDATE cards SET vk_id='$uid' WHERE del=0 AND uid='$uid'");
				return $crm_uid;
			}
			return false;
		} elseif(intval($uid)<0)
			return $this->dlookup("uid","cards","del=0 AND uid='".intval($uid)."'");
		return false;
	}
	function avangard_tm_end_set($avangard_id,$tm_end) {
		global $ctrl_id;
		//~ if($tm_end<time())
			//~ return false;
		$this->query("UPDATE avangard SET tm_end='$tm_end' WHERE id='$avangard_id'",0);

		$uid=$this->dlookup("vk_uid","avangard","id='$avangard_id'");
		if(!$uid)
			return false;
		if(!$land_num=$this->dlookup("land_num","avangard","id='$avangard_id'")) {
			$land_num=$this->fetch_assoc($this->query("SELECT lands.land_num AS land_num
				FROM lands
				JOIN avangard ON avangard.product_id=lands.product_id
				WHERE avangard.id='$avangard_id' AND lands.del=0
				"))['land_num'];
		}
		if(!intval($land_num))
			return true;
		$res=$this->query("SELECT * FROM vkt_send_1 WHERE del=0 AND sid=31 AND (land_num='$land_num' OR land_num=0)");
		include_once "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
		$vkt_send=new vkt_send($this->database);
		while($r=$this->fetch_assoc($res)) {
			$vkt_send_id=$r['id'];
			$tm_event=intval($tm_end+$r['tm_shift']);
			$vkt_send->vkt_send_task_del($vkt_send_id,$ctrl_id,$uid);
	//	$this->notify_me("$ctrl_id, $tm_event, $vkt_send_id,3,$uid");
			$vkt_send->vkt_send_task_add($ctrl_id, $tm_event, $vkt_send_id,$vkt_send_type=3,$uid);
		}
		return true;
	}
	function avangard_last_pay_id($uid,$pids) {
		if(sizeof($pids)==0)
			return false;
		$w="(1=2 ";
		foreach($pids AS $pid)
			$w.="OR product_id=$pid ";
		$w.=")";
		return $this->fetch_row($this->query("SELECT id FROM avangard WHERE res=1 AND vk_uid='$uid' AND $w ORDER BY tm_end DESC LIMIT 1"))[0];
	}
	function avangard_payments_count($uid,$pids) {
		if(sizeof($pids)==0)
			return false;
		$w="(1=2 ";
		foreach($pids AS $pid)
			$w.="OR product_id=$pid ";
		$w.=")";
		return $this->num_rows($this->query("SELECT id FROM avangard WHERE res=1 AND vk_uid='$uid' AND $w"));
	}
	function avangard_ch_tm_end___($avangard_id,$new_tm_end) {
		$r=$this->fetch_assoc($this->query("SELECT * FROM avangard WHERE id='$avangard_id'"));
		if(!$product_id=$this->dlookup("product_id","avangard","id='$avangard_id'"))
			return false;
		$this->query("UPDATE avangard SET tm_end='$new_tm_end' WHERE is='$avangard_id'");
		$land_num=$this->dlookup("land_num","lands","del=0 AND product_id='$product_id'");
		if(!$vkt_send_id=$this->dlookup("id","vkt_send_1","del=0 AND sid=31 AND land_num='$land_num'"))
			return false;
		$tm_shift=$this->dlookup("tm_shift","vkt_send_1","id='$vkt_send_id'");
		$tm_event=intval($new_tm_end+$tm_shift);

		$this->connect('vkt');
		$this->query("UPDATE 0ctrl_vkt_send_tasks
					SET tm='$tm_event'
					WHERE vkt_send_id='$vkt_send_id' AND vkt_send_type=3");
		return true;
	}
	function avangard_tm_end($uid,$pids) {
		$w="(1=2 ";
		foreach($pids AS $pid)
			$w.="OR product_id=$pid ";
		$w.=")";
		$res=$this->query("SELECT tm_end FROM avangard
											WHERE res=1 AND vk_uid='$uid' AND $w
											ORDER BY tm_end DESC 
											LIMIT 1");
		if(!$this->num_rows($res))
			return 0;
		return $this->fetch_assoc($res)['tm_end'];
	}
	function tm_end_licence($ctrl_id) {
		if($this->database!='vkt') {
			$tmp=$this->database;
			$this->connect('vkt');
		}
		$ctrl_tm_end=$this->dlookup("tm_end","0ctrl","id='$ctrl_id'");
		$res=$this->query("SELECT id FROM product WHERE del=0 AND id BETWEEN 20 AND 40");
		$pids=[];
		while($r=$this->fetch_assoc($res)) {
			$pids[]=$r['id'];
		}
		$products_yclients=[130, 131, 132, 135];
		foreach ($products_yclients as $pid_yclients) {
			$pids[] = $pid_yclients;
		}
		$avangard_tm_end=$this->avangard_tm_end($this->dlookup("uid","0ctrl","id='$ctrl_id'"),$pids);
		if(isset($tmp)) {
			$this->connect($tmp);
		}
		$tm_end=$ctrl_tm_end>$avangard_tm_end ? $ctrl_tm_end : $avangard_tm_end;
		if($tm_end>$ctrl_tm_end)
			$this->query("UPDATE 0ctrl SET tm_end='$tm_end' WHERE id='$ctrl_id'");
	//$this->notify_me("tm_end_licence $ctrl_id ctrl_tm_end=$ctrl_tm_end avangard_tm_end=$avangard_tm_end tm_end=$tm_end");
		return $tm_end;
	}
	function avangard_tm_last_pay($uid,$pids) {
		$w="(1=2 ";
		foreach($pids AS $pid)
			$w.="OR product_id=$pid ";
		$w.=")";
		return $this->fetch_row($this->query("SELECT tm FROM avangard WHERE res=1 AND vk_uid='$uid' AND $w ORDER BY tm_end DESC LIMIT 1"))[0];
	}
	function get_direct_code($klid) {
		$r=$this->fetch_assoc($this->query("SELECT * FROM users WHERE klid='$klid'"));
	 return md5($r['id'].$r['passw']);
	}
	function get_direct_code_link($klid) {
		return $this->db200.'/lk/cabinet.php?u='.$this->get_direct_code($klid);
	}
	function get_tz_by_city($city) {
		$city=mb_strtolower(trim($city), 'UTF-8');
		if(preg_match("/москва|мск|санкт-петербург|спб|питер|петербург/",$city))
			return 3;
		$this->connect('vkt');
		if(!$tz=$this->dlookup("timezone","tz_info","city='".$city."'",0))
			$tz=0;
		$this->connect($this->database);
		return -($tz*60);
	}
	function prepare_msg_codes() {
		return "{{client_name}} - имя
{{email}} - емэйл
{{phone}} - телефон
{{uid}} - id в CRM
{{cabinet_link}} - ссылка на партнерский кабинет
{{partner_code}} - партнерский код
{{direct_code}} - код для входа в кабинет или в CRM (?u={{direct_code}}, заменяет логин и пароль)
{{partner_login}} - логин партнера для входа в CRM
{{partner_passw}} - пароль партнера для входа в CRM
{{promocode}} - промокод, принадлежащий клиенту
";
	}
	function set_personal_fee($uid,$pid,$fee_1,$fee_2,$fee_cnt) {
		$uid=$this->dlookup("uid","cards","del=0 AND uid='".intval($uid)."'");
		$pid=$this->dlookup("id","product","del=0 AND id='".intval($pid)."'");
		if(!$uid || !$pid)
			return false;
		$fee_1=floatval($fee_1);
		$fee_2=floatval($fee_2);
		$fee_cnt=floatval($fee_cnt);
		if($pid) {
			if(!$this->dlookup("id","partnerka_spec","uid='$uid' AND pid='$pid'")) {
				$this->query("INSERT INTO partnerka_spec SET
					uid='$uid',
					pid='$pid',
					fee_1='$fee_1',
					fee_2='$fee_2',
					fee_cnt='$fee_cnt'
				");
			} else {
				$this->query("UPDATE partnerka_spec SET
					fee_1='$fee_1',
					fee_2='$fee_2',
					fee_cnt='$fee_cnt'
					WHERE uid='$uid' AND pid='$pid'
				");
			}
		}
		return true;
	}
	function get_api_secret($ctrl_id) {
		//$this->notify_me("get_api_secret ctrl_id=$ctrl_id");
		return md5($ctrl_id);
	}
	function get_webhook_data($uid,$action) {
		if(!$uid || !$r=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE del=0 AND uid='$uid'")))
			return ['error'=>"uid=$uid is not found."];
		$r1=$this->fetch_assoc($this->query("SELECT * FROM users WHERE del=0 AND klid='{$r['id']}'"));
		$arr=[
			'secret'=> md5($uid.trim($r['name']).trim($r['mob_search']).$this->get_api_secret($this->ctrl_id)),
			'action'=>$action,
			'uid'=>$r['uid'],
			'first_name'=>$r['name'],
			'last_name'=>$r['surname'],
			'phone'=>$r['mob_search'],
			'email'=>$r['email'],
			'city'=>$r['city'],
			'telegram_id'=>$r['telegram_id'],
			'telegram_nic'=>$r['telegram_nic'],
			'vk_id'=>$r['vk_id'],
			'tm'=>$r['tm'],
			'comm'=>$r['comm1'],
			'partner_id'=>$r['user_id'],
			'is_partner'=>$r1 ? 1:0,
			'partner_code'=>$r1 ?$r1['bc'] : "",
			'cabinet_link'=>$r1 ?$this->db200."/lk/cabinet.php/".$r1['direct_code'] : "",
			'direct_code'=>$r1['direct_code'],
			'bank_details'=>$r1['bank_details'],
			];
		if($r1) { //IF PARTNER!!!
			$partner_data=[];
			$klid=$r1['klid'];
			include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
			$db=new partnerka($klid,$this->database);
			$db->sids_for_cnt_reg=[12];

			$wday=date("N")-1;
			$year=date("Y"); $month=date("m");
			$last_month=($month==1)?12:$month-1;
			$year1=($last_month==12)?$year-1:$year;
			//print "01.$last_month.$year1"; exit;
			$month=(intval($month)<10)?"0".intval($month):$month;

			$tm1=$db->date2tm("01.01.$year"); $tm2=time();
			$cnt_reg_year=$db->cnt_reg($klid,$tm1,$tm2);
			$sum_buy_year=$db->sum_buy($klid,$tm1,$tm2,0);
			$sum_fee_year=$db->sum_fee($klid,$tm1,$tm2,0);
			$sum_pay_year=$db->sum_pay($klid,$tm1,$tm2,0);

			$tm1=$db->date2tm("01.$month.$year"); $tm2=time();
			$cnt_reg_this_month=$db->cnt_reg($klid,$tm1,$tm2); 
			$sum_buy_this_month=$db->sum_buy($klid,$tm1,$tm2);
			$sum_fee_this_month=$db->sum_fee($klid,$tm1,$tm2);
			$sum_pay_this_month=$db->sum_pay($klid,$tm1,$tm2,0);

			$tm1=$db->date2tm("01.$last_month.$year1"); $tm2=$db->date2tm("01.$month.$year");
			$cnt_reg_last_month=$db->cnt_reg($klid,$tm1,$tm2);
			$sum_buy_last_month=$db->sum_buy($klid,$tm1,$tm2);
			$sum_fee_last_month=$db->sum_fee($klid,$tm1,$tm2);
			$sum_pay_last_month=$db->sum_pay($klid,$tm1,$tm2,0);
			
			$tm1=$db->dt1(time()-($wday*24*60*60)); $tm2=time();
			$cnt_reg_this_week=$db->cnt_reg($klid,$tm1,$tm2);
			$sum_buy_this_week=$db->sum_buy($klid,$tm1,$tm2);
			$sum_fee_this_week=$db->sum_fee($klid,$tm1,$tm2);
			$sum_pay_this_week=$db->sum_pay($klid,$tm1,$tm2,0);

			$tm1=$db->dt1(time()-(1*24*60*60)); $tm2=$db->dt2(time()-(1*24*60*60));
			$cnt_reg_yesterday=$db->cnt_reg($klid,$tm1,$tm2);
			$sum_buy_yesterday=$db->sum_buy($klid,$tm1,$tm2,0);
			$sum_fee_yesterday=$db->sum_fee($klid,$tm1,$tm2,0);
			$sum_pay_yesterday=$db->sum_pay($klid,$tm1,$tm2,0);

			$tm1=$db->dt1(time()); $tm2=time();
			$cnt_reg_today=$db->cnt_reg($klid,$tm1,$tm2);
			$sum_buy_today=$db->sum_buy($klid,$tm1,$tm2);
			$sum_fee_today=$db->sum_fee($klid,$tm1,$tm2);
			$sum_pay_today=$db->sum_pay($klid,$tm1,$tm2,0);

			$tm1=0; $tm2=time();
			$sum_fee_all=$db->sum_fee($klid,$tm1,$tm2);
			$sum_pay_all=$db->sum_pay($klid,$tm1,$tm2,0);
			$rest_all=$sum_fee_all-$sum_pay_all;

			$partner_data=[
				'cnt_reg_year' => $cnt_reg_year,
				'sum_buy_year' => $sum_buy_year,
				'sum_fee_year' => $sum_fee_year,
				'sum_pay_year' => $sum_pay_year,
				'cnt_reg_this_month' => $cnt_reg_this_month,
				'sum_buy_this_month' => $sum_buy_this_month,
				'sum_fee_this_month' => $sum_fee_this_month,
				'sum_pay_this_month' => $sum_pay_this_month,
				'cnt_reg_last_month' => $cnt_reg_last_month,
				'sum_buy_last_month' => $sum_buy_last_month,
				'sum_fee_last_month' => $sum_fee_last_month,
				'sum_pay_last_month' => $sum_pay_last_month,
				'cnt_reg_this_week' => $cnt_reg_this_week,
				'sum_buy_this_week' => $sum_buy_this_week,
				'sum_fee_this_week' => $sum_fee_this_week,
				'sum_pay_this_week' => $sum_pay_this_week,
				'cnt_reg_yesterday' => $cnt_reg_yesterday,
				'sum_buy_yesterday' => $sum_buy_yesterday,
				'sum_fee_yesterday' => $sum_fee_yesterday,
				'sum_pay_yesterday' => $sum_pay_yesterday,
				'cnt_reg_today' => $cnt_reg_today,
				'sum_buy_today' => $sum_buy_today,
				'sum_fee_today' => $sum_fee_today,
				'sum_pay_today' => $sum_pay_today,
				'sum_fee_all' => $sum_fee_all,
				'sum_pay_all' => $sum_pay_all,
				'rest_all' => $rest_all
			];
			$arr['partner_data']=$partner_data;
			$res=$this->query("SELECT 
									*,
									cards_client.uid AS client_uid, 
									cards_client.name AS client_name, 
									cards_client.surname AS client_surname, 
									partnerka_op.tm AS tm, 
									cards_partner.uid AS partner_uid, 
									cards_partner.name AS partner_name, 
									cards_partner.surname AS partner_surname 
								FROM partnerka_op 
								JOIN cards AS cards_client ON partnerka_op.uid = cards_client.uid 
								JOIN cards AS cards_partner ON partnerka_op.klid = cards_partner.id 
								LEFT JOIN avangard ON avangard.id = partnerka_op.avangard_id 
								WHERE partnerka_op.klid_up = '$klid' 
								ORDER BY partnerka_op.tm DESC; ");
			$fee_detailed=[];
			while($r=$this->fetch_assoc($res)) {
				if($r['avangard_id']>0)
					$product=$r['order_descr'];
				if($r['product_id']==1001)
					$product="ПРИВЕТСТВЕННЫЕ БАЛЛЫ";
				if($r['product_id']==-1)
					$product="НАЧИСЛЕНО";
				if($r['level']==1) {
					$vid="собств";
				} else {
					$vid=$r['partner_name']." ".$r['partner_surname'];
				}
				$fee_detailed[]=['client_uid'=>$r['client_uid'],
								'tm'=>$r['tm'],
								'first_name'=>$r['name'],
								'last_name'=>$r['surname'],
								'order_number'=>$r['order_number'],
								'order_descr'=>$product,
								'sum'=>$r['amount'],
								'fee_percent'=>$r['fee'],
								'fee_sum'=>$r['fee_sum'],
								'level'=>$r['level'],
								'partner_uid'=>$r['partner_uid'],
								'partner_name'=>$vid,
								'comm'=>$r['comm'],
					];
				$s+=$r['fee_sum'];
			}
			$arr['fee_detailed']=$fee_detailed;

			$fee_paid=[];
			$res=$db->query("SELECT * FROM partnerka_pay WHERE klid=$klid AND sum_pay>0 ORDER BY tm DESC");
			while($r=$db->fetch_assoc($res)) {
				$fee_paid[]=['tm'=>$r['tm'],
							'sum_pay'=>$r['sum_pay'],
							'vid'=>$r['vid'],
							'comm'=>$r['comm'],
					];
			}
			$arr['fee_paid']=$fee_paid;
			$res=$this->query("SELECT order_number,avangard.tm,SUM(amount) AS s,SUM(res) AS res,MAX(c_name) AS name
				FROM avangard
				JOIN cards ON cards.uid=vk_uid
				WHERE amount>0 AND cards.utm_affiliate=$klid
				GROUP BY order_number,avangard.tm,order_descr
				ORDER BY avangard.tm
				DESC LIMIT 50;");
			$fee_orders=[];
			while($r=$db->fetch_assoc($res)) {
				$paid=$r['res'] ? "1" : "0";
				$fee_orders[]=[
								'tm'=>$r['tm'],
								'referal_uid'=>$r['vk_uid'],
								'referal_name'=>$r['name'],
								'sum'=>$r['s'],
								'paid'=>$paid,
							];
			}
			$arr['fee_orders']=$fee_orders;
		} //IF PARTNER END

		$order=false; $products=[];
		if($this->vkt_send_msg_order_id) {
			if($r=$this->fetch_assoc($this->query("SELECT * FROM avangard WHERE order_id='$this->vkt_send_msg_order_id' AND res=1"))) {
				$sum=0;
				$order=[
					'tm' => $r['tm'],
					'pay_system' => $r['pay_system'],
					'sku' => $r['sku'],
					'order_id' => $r['order_id'],
					'order_number' => $r['order_number'],
					'client_name' => $r['c_name'],
					'phone' => $r['phone'],
					'email' => $r['email'],
					'uid' => $r['vk_uid'],
					'res' => 1,
					'currency' => $r['currency'],
					'land_num' => $r['land_num'],
					'comm' => $r['comm'],
					];
					$res1=$this->query("SELECT * FROM avangard WHERE order_id='$this->vkt_send_msg_order_id' AND res=1",0);
					while($r1=$this->fetch_assoc($res1)) {
						$products[]=[
							'product_id' => $r1['product_id'],
							'product_descr' => $r1['order_descr'],
							'amount' => $r1['amount'],
							'tm_end' => $r1['tm_end'],
							'fee_1' => $r1['fee_1'],
							'fee_2' => $r1['fee_2'],
							];
						$sum+=$r1['amount'];
					}
					$order['products']=$products;
					$order['sum']=$sum;
				}
		}
		$arr['current_order']=$order;

		$res=$this->query("SELECT *,promocodes.id AS id FROM promocodes
						JOIN product ON product.id=product_id
						WHERE product.del=0 AND uid='$uid' AND tm2>".time());
		$promo=[];
		while($r=$this->fetch_assoc($res)) {
			$discount=($r['discount']>0 && $r['discount']<100) ? $r['discount']."%" : $r['discount']."р.";
			$discount=$r['discount'] ? $discount : '';
			$fee_1=($r['fee_1']>0 && $r['fee_1']<100) ? $r['fee_1']."%" : $r['fee_1']."р.";
			$fee_1=$r['fee_1'] ? $fee_1 : '';
			$fee_2=($r['fee_2']>0 && $r['fee_2']<100) ? $r['fee_2']."%" : $r['fee_2']."р.";
			$fee_2=$r['fee_2'] ? $fee_2 : '';
			$promo[]=['id'=>$r['id'],
					'tm1'=>$r['tm1'],
					'tm2'=>$r['tm2'],
					'uid'=>$r['uid'],
					'product_id'=>$r['product_id'],
					'product_descr'=>$r['descr'],
					'discount'=>$discount,
					'price'=>$r['price'] ? $r['price'].'р.' : '',
					'promocode'=>$r['promocode'],
					'fee_1'=>$fee_1,
					'fee_2'=>$fee_2,
					'cnt'=>$r['cnt'],
				];
		}
		$arr['promocodes']=$promo;
		
		return $arr;
	}
	function send_webhook($url,$arr) {
			$jsonData = json_encode($arr);
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response
			curl_setopt($ch, CURLOPT_POST, true); // Set request method to POST
			curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Attach JSON data
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json', // Set content type to application/json
				'Content-Length: ' . strlen($jsonData) // Set content length
			]);
			$response = curl_exec($ch);
			if ($response === false) {
				return('cURL Error: ' . curl_error($ch));
			} else {
				$tmp=$this->database;
				$this->connect('vkt');
				if($this->ctrl_id) {
					$ctrl_dir=$this->dlookup("ctrl_dir","0ctrl","id=$this->ctrl_id");
					file_put_contents("/var/www/vlav/data/www/wwl/d/$ctrl_dir/last_webhook.log","\n".date("d.m.Y H:i:s")." $this->ctrl_id \n".print_r($arr,true));
				}
				return("Response: " . $response);
			}
			curl_close($ch);
	}
	function prepare_msg_webhook($uid,$msg) {
		//"test {{webhook url pay}} test";
		$pat="/{{webhook\s+(https?:\/\/[^\s]+)\s+(\w+)}}/u";
		if(preg_match($pat,$msg,$m)) {
			//$this->notify_me(print_r($m,true));
			$url=mb_substr(trim($m[1]),0,128);
			$action=mb_substr(trim($m[2]),0,32);
			$msg=preg_replace($pat,"",$msg);
			//$db->notify_me("HERE_$msg");
			//$this->print_r($arr);

			$arr=$this->get_webhook_data($uid,$action);
			$response = $this->send_webhook($url,$arr);
			$this->notify_me("prepare_msg_webhook ctrl_id=$this->ctrl_id $url " . $response);
		}
		return $msg;
	}
	function prepare_msg_promocode__($uid,$msg) {
		$pat = '/\{\{promocode\s+([\w\-]+)\s+(for_price|for_discount)\s+([0-9]+)\s+(\w+)\s+(\d\d:\d\d|\d\d|\d)\s+\[([\d\,]+)\]\s+([\d]+)\s+([\d]+)\s+([\d]+)\}\}/u';
		if(preg_match($pat,$msg,$m)) {
			//~ "test {{promocode promo12345 for_price 20000 48 23:59 [30] 20 5 0}} test", //30 - product_id fee1 fee2 cnt=0 means unlimited
			//~ "test {{promocode promo12345 for_price 20000 tomorrow 23:59 [30] 20 5 0}} test", //30 - product_id
			//~ "test {{promocode promo12345 for_price 20000 today 23:59 [30] 20 5 0}} test", //30 - product_id
			//~ "test {{promocode promo12345 for_price 20000 for 12 [30] 20 5 0}} test", //after 12 hours
			//print_r($m);
			$promocode=$m[1];
			if(preg_match("/([\w\-]+)_auto/u",$promocode,$m1)) {
				//print_r($m1);
				$promocode=$m1[1];
				for($digits=4; $digits<12; $digits++) {
					$min = pow(10, $digits - 1); // minimum value
					$max = pow(10, $digits) - 1; // maximum value
					$n=rand($min,$max);
					$n_=$min;
					while($this->dlookup("id","promocodes","promocode='".$promocode.$n."'")) {
						$n=rand($min,$max);
						if(!$n_--)
							break;
					}
					if($n_)
						break;
				}
				$promocode.=$n;
				//print "HERE_$p $n $promocode <br>";
			}
			$price=($m[2]=='for_price') ? $m[3] : 0;
			$discount=($m[2]=='for_discount') ? $m[3] : 0;
			$pid_arr=explode(',',$m[6]);
			if($m[4]=='today')
				$tm2=$this->dt1(time());
			elseif($m[4]=='tomorrow')
				$tm2=$this->dt1(time()+(24*60*60));
			elseif(intval($m[4]))
				$tm2=$this->dt1(time()+(intval($m[4])*60*60));
			elseif($m[4]=='for')
				$tm2=time();

			if($t=$this->time2tm($m[5]))
				$tm2+=$t;
			else
				$tm2=time()+intval($m[5])*60*60;

			$fee_1=$m[7];
			$fee_2=$m[8];
			$cnt=intval($m[9]) ? intval($m[9]) : -1;

			$ctrl_id=$this->ctrl_id;
			$tmp=$this->database;
			$this->connect('vkt');
			$insales_id= $this->dlookup("insales_shop_id","0ctrl","id='$ctrl_id'");
			$insales_shop= $this->dlookup("insales_shop","0ctrl","id='$ctrl_id'");
			$this->connect($tmp);
			//$this->notify_me("HERE_$ctrl_id $insales_id $insales_shop");
			
			foreach($pid_arr AS $pid) {
				$this->promocode_add($promocode,$uid,time(),$tm2,$pid,$discount,$price,$fee_1,$fee_2,$cnt);
				//$this->notify_me( "HERE_$promocode,uid=$uid,tm2=$tm2,pid=$pid,discount=$discount,price=$price $fee_1 $fee_2 cnt=$cnt");
				if($insales_id && $discount) { //insales understands discounts only
					include_once "/var/www/vlav/data/www/wwl/inc/insales.class.php";
					$in=new insales($insales_id,$insales_shop);
					$in->ctrl_id=$ctrl_id;
					$type_id=$discount<100 ? 1 : 2;
					$p=['code'=>$promocode,
						'disabled'=>false,
						'act_once'=>false,
						'expired_at'=>date("Y-m-d",$tm2),
						'type_id'=>$type_id,
						'discount'=>$discount,
						];
					$res=$in->create_promocode($p);
					if(!isset($res['error']) ) { //|| $res['http_code']==422
						//$this->notify_me("prepare_msg_promocode OK $ctrl_id $insales_id $insales_shop\n".print_r($res,true));
						//print "<p class='alert alert-success' >Промокод синхронизирован с inSales</p>";
					} else {
						$this->notify_me("prepare_msg_promocode for insales ERROR $ctrl_id $insales_id $insales_shop");
					}
				}
			}
			$msg=preg_replace($pat,$promocode,$msg);
		}
		return $msg;
	}
	function prepare_msg_promocode_save($uid, $msg) {
		$pat = '/\{\{promocode_save\s+([\w\-]+)_auto\}\}/u';
		if (preg_match($pat, $msg, $m)) {
			$promocode = $m[1];
			for ($digits = 4; $digits < 12; $digits++) {
				$min = pow(10, $digits - 1); // minimum value
				$max = pow(10, $digits) - 1; // maximum value
				$n = rand($min, $max);
				$n_ = $min;
				while ($this->dlookup("id", "promocodes", "promocode='" . $promocode . $n . "'")) {
					$n = rand($min, $max);
					if (!$n_--) {
						break;
					}
				}
				if ($n_) {
					break;
				}
			}
			$promocode .= $n;
			$fname=$this->get_cwd_by_ctrl_id($this->ctrl_id)."/last_promocode.txt";
			if(!file_put_contents($fname, $promocode))
				$this->notify_me("prepare_msg_promocode_save save error. ctrl_id=$this->ctrl_id fname=$fname");
			$msg = preg_replace($pat, "", $msg);
		}
		return $msg;
	}
	function prepare_msg_promocode($uid, $msg) {
		if(preg_match("/\{\{promocode\}\}/",$msg)) {
			$promocode=$this->dlast("promocode","promocodes","uid='$uid' AND tm2>'".time()."'");
			$msg = preg_replace("/\{\{promocode\}\}/", $promocode, $msg);
			return $msg;
		}
		//~ "test {{promocode promo12345 for_price 20000 31.12.2025 23:59 [30] 20 5 0 hide|show 180 0}} test", //30 - product_id fee1 fee2 cnt=0 means unlimited hide|show hold keep=1 or 0 not keep
		//~ "test {{promocode promo12345 for_price 20000 48 23:59 [30] 20 5 0}} test", //30 - product_id fee1 fee2 cnt=0 means unlimited
		//~ "test {{promocode promo12345 for_price 20000 tomorrow 23:59 [30] 20 5 0}} test", //30 - product_id
		//~ "test {{promocode promo12345 for_price 20000 today 23:59 [30] 20 5 0}} test", //30 - product_id
		//~ "test {{promocode promo12345 for_price 20000 for 12 [30] 20 5 0}} test", //after 12 hours
		// Define the pattern to match the promocode commands
		//$pat = '/\{\{promocode\s+([\w\-]+)\s+(for_price|for_discount)\s+([0-9]+)\s+([\w\.]+)\s+(\d\d:\d\d|\d\d|\d)\s+\[([\d\,]+)\]\s+([\d]+)\s+([\d]+)\s+([\d]+)(?:\s+(hide))?\}\}/u';
		$pat = '/\{\{promocode\s+([\w\-]+)\s+(for_price|for_discount)\s+([0-9]+)\s+([\w\.]+)\s+(\d\d:\d\d|\d\d|\d)\s+\[([\d\,]+)\]\s+([\d]+)\s+([\d]+)\s+([\d]+)(?:\s+(hide|show))?(?:\s+(\d+))?(?:\s+(1|0))?\}\}/u';

		// Use a loop to find all matches and process them
		while (preg_match($pat, $msg, $m)) {
			//$this->notify_me(print_r($m,true));
			$promocode = $m[1];
			
			if($promocode=="saved_promocode") {
				$fname=$this->get_cwd_by_ctrl_id($this->ctrl_id)."/last_promocode.txt";
				if(!$promocode=trim(file_get_contents($fname))) {
					$promocode = $m[1];
				}
			} elseif($promocode=="last_promocode") {
				if(!$promocode=$this->dlast("promocode","promocodes","uid='$uid'")) {
					$promocode="LAST_PROMOCODE_ERROR";
				}
			} elseif (preg_match("/([\w\-]+)_auto/u", $promocode, $m1)) {
				$promocode = $m1[1];
				for ($digits = 4; $digits < 12; $digits++) {
					$min = pow(10, $digits - 1); // minimum value
					$max = pow(10, $digits) - 1; // maximum value
					$n = rand($min, $max);
					$n_ = $min;
					while ($this->dlookup("id", "promocodes", "promocode='" . $promocode . $n . "'")) {
						$n = rand($min, $max);
						if (!$n_--) {
							break;
						}
					}
					if ($n_) {
						break;
					}
				}
				$promocode .= $n;
			}

			// Determine price and discount
			$price = ($m[2] == 'for_price') ? $m[3] : 0;
			$discount = ($m[2] == 'for_discount') ? $m[3] : 0;

			// Parse product IDs
			$pid_arr = explode(',', $m[6]);

			// Calculate the time settings
			if ($m[4] == 'today') {
				$tm2 = $this->dt1(time());
			} elseif ($m[4] == 'tomorrow') {
				$tm2 = $this->dt1(time() + (24 * 60 * 60));
			} elseif ($m[4] == 'for') {
				$tm2 = time();
			} elseif(preg_match('/[\d]{2}\.[\d]{2}\.[\d]{4}/',$m[4])) {
				$dateTime = DateTime::createFromFormat('d.m.Y', $m[4]);
				$tm2 = $this->dt1($dateTime->getTimestamp());
			} elseif (preg_match('/\d+/',$m[4])) {
				$tm2 = $this->dt1(time() + (intval($m[4]) * 60 * 60));
			}
			//$this->notify_me("HERE_{$m[4]}_".date("d.m.Y H:i:s",$tm2));

			// Adjusting time according to the included hour/minute
			if ($t = $this->time2tm($m[5])) {
				$tm2 += $t;
			} else {
				$tm2 = time() + intval($m[5]) * 60 * 60;
			}

			// Fees and counts
			$fee_1 = $m[7];
			$fee_2 = $m[8];
			$cnt = intval($m[9]) ? intval($m[9]) : -1;
			
			$hide = isset($m[10]) && $m[10] == 'hide';
			$hold = isset($m[11]) && intval($m[11]) ? intval($m[11])  : 0;
			$keep = isset($m[12]) && intval($m[12]) ? 1 : 0;

			//~ // Managing the database operations
			//~ $ctrl_id = $this->ctrl_id;
			//~ $tmp = $this->database;
			//~ $this->connect('vkt');
			
			//~ // Getting necessary IDs
			//~ $insales_id = $this->dlookup("insales_shop_id", "0ctrl", "id='$ctrl_id'");
			//~ $insales_shop = $this->dlookup("insales_shop", "0ctrl", "id='$ctrl_id'");
			//~ $this->connect($tmp);

			// Processing each product ID
			foreach ($pid_arr as $pid) {
				
				if($insert_id=$this->promocode_add($promocode, $uid, time(), $tm2, $pid, $discount, $price, $fee_1, $fee_2, $cnt,$hold,$keep)) {
					$promocode=$this->dlookup("promocode","promocodes","id=$insert_id");
				} else
					$promocode="PROMOCODE_ERROR";
			}
			// Replace the matched promocode pattern with the actual promocode in the message
			$msg = preg_replace($pat, !$hide?$promocode:"", $msg, 1); // Change 0 to 1 to replace only 1 at a time
		}
		return $msg;
	}
	function prepare_msg_price2($uid,$msg) {
		$pat = '/\{\{price2\s+(\w+)\s+(\d\d:\d\d|\d\d|\d)\s+\[([\w\,]+)\]\}\}/';
		if(preg_match($pat,$msg,$m)) {
			//print_r($m);
			//~ "test {{price2 48 23:59 [30]}} test", //30 - product_id
			//~ "test {{price2 tomorrow 23:59 [30]}} test", //30 - product_id
			//~ "test {{price2 today 23:59 [30]}} test", //30 - product_id
			//~ "test {{price2 for 12 [30]}} test", //after 12 hours
			$pids=explode(",",$m[3]);
			//print_r($pids); exit;
			if($m[1]=='today')
				$tm2=$this->dt1(time());
			elseif($m[1]=='tomorrow')
				$tm2=$this->dt1(time()+(24*60*60));
			elseif(intval($m[1]))
				$tm2=$this->dt1(time()+(intval($m[1])*60*60));
			elseif($m[1]=='for')
				$tm2=time();

			if($t=$this->time2tm($m[2]))
				$tm2+=$t;
			else
				$tm2=time()+intval($m[2])*60*60;
			$dt2=date("d.m.Y H:i",$tm2);
			foreach($pids AS $pid) {
				//print "<br>$this->price2_set($uid,2,time(),$dt2,$pid);";
				$this->price2_set($uid,2,time(),$tm2,$pid);
			}
			$msg=preg_replace($pat,"",$msg);
		}
		return $msg;
	}
	public $prepare_msg_attach_file_name=null;
	function prepare_msg_send_loyalty_card($uid,$msg) {
		$pat="/\{\{send_loyalty_card\}\}/";
		if(preg_match($pat,$msg)) {
			include_once "/var/www/vlav/data/www/wwl/inc/cashier.class.php";
			include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
			$vkt=new vkt($this->database);
			$c=new cashier($this->database,$this->ctrl_id,$vkt->get_ctrl_dir($this->ctrl_id));
			$c->send_loyalty_card($this->dlookup("mob_search","cards","uid='$uid'"),$this->dlookup("name","cards","uid='$uid'"));
			return ""; //preg_replace($pat, "", $msg);
		}
		return $msg;
	}
	function prepare_msg($uid,$msg,$order_id=0) {
		$r=$this->fetch_assoc($this->query("SELECT id,name,email,mob_search FROM cards WHERE uid='$uid'"));
		if(!$uid || !$klid=$r['id']) {
			return preg_replace("/\{\{.*?\}\}/","",$msg);
		}
		$uid_md5=$this->uid_md5($uid);
		if(isset($_SESSION['userid_sess'])) {
			$reflink_gk=$this->dlookup("gk_code","users","del=0 AND id='{$_SESSION['userid_sess']}'");
			$reflink=$this->dlookup("klid","users","del=0 AND id='{$_SESSION['userid_sess']}'");
		} else {
			$reflink_gk=""; $reflink="";
		}
		$direct_code_link=$this->get_direct_code_link($klid);
		$direct_code=$this->dlookup("direct_code","users","del=0 AND klid='$klid'");;
		$partner_cabinet=$direct_code_link;
		$bc=$this->get_bc($klid);
		$user_id=$this->get_user_id($klid);
		$user_login=$this->get_user_login($user_id);
		$user_passw=$this->get_user_passw($user_id);
		
		//$msg=preg_replace("|#[0-9]+|s","",$msg);
		$msg=preg_replace("|#reflink_gk|s",$reflink_gk,$msg);
		$msg=preg_replace("|#reflink|s",$reflink,$msg);
		$msg=preg_replace("|#uid_md5|s",$uid_md5,$msg);
		$msg=preg_replace("|#uid|s",$uid_md5,$msg);
		if(isset($_SESSION['real_user_name']))
			$msg=preg_replace("|#user_name|s",$_SESSION['real_user_name'],$msg);
		$msg=preg_replace("|#video_[0-9]+|s","",$msg);
		$msg=preg_replace("|#audio_[0-9]+|s","",$msg);
		$msg=preg_replace("|#image_[0-9]+|s","",$msg);

		$msg=str_replace("{{client_name}}",$r['name'],$msg);
		$msg=str_replace("{{email}}",$r['email'],$msg);
		$msg=str_replace("{{phone}}",$r['mob_search'],$msg);
		$msg=str_replace("{{uid_md5}}",$uid_md5,$msg);
		$msg=str_replace("{{uid}}",$uid_md5,$msg);
		$msg=str_replace("{{cabinet_link}}",$direct_code_link,$msg);
		$msg=str_replace("{{direct_code}}",$direct_code,$msg);
		$msg=str_replace("{{partner_code}}",$bc,$msg);

		$msg=str_replace("{%client_name%}",$r['name'],$msg);
		$msg=str_replace("{%email%}",$r['email'],$msg);
		$msg=str_replace("{%phone%}",$r['mob_search'],$msg);
		$msg=str_replace("{%uid_md5%}",$uid_md5,$msg);
		$msg=str_replace("{%uid%}",$uid_md5,$msg);
		$msg=str_replace("%userid%",$uid_md5,$msg);
		$msg=str_replace("{%cabinet_link%}",$direct_code_link,$msg);
		$msg=str_replace("{%partner_code%}",$bc,$msg);

		$msg=str_replace("{{partner_login}}",$user_login,$msg);
		$msg=str_replace("{{partner_passw}}",$user_passw,$msg);
		$msg=str_replace("{%partner_login%}",$user_login,$msg);
		$msg=str_replace("{%partner_passw%}",$user_passw,$msg);

		$msg=str_replace("{{order_id}}",$order_id,$msg);


		$p="/\{\{today\}\}/";
		if(preg_match($p,$msg,$m)) {
			$msg=preg_replace($p,date("d.m.Y"),$msg);
		}
		if(preg_match("/\{\{bot_set\s+(\S+)\}\}/",$msg,$m)) {
			$step=mb_substr($m[1],0,6);
			$msg=str_replace("{{bot_set $step}}","",$msg);
			$this->bot_set($uid,$step);
			//$msg="CHK=".$this->bot_chk($uid);
		}
		if(preg_match("/\{\{wa_only\}\}/",$msg,$m)) {
			if(isset($this->vkt_send_skip_wa))
				$this->vkt_send_skip_wa=false;
			$this->fl_wa=true;
			$msg=preg_replace("/\{\{wa_only\}\}/","",$msg);
		}
		if(preg_match("/\{\{notify (.*?)\}\}/",$msg,$m)) {
			$this->notify($uid,$m[1]);
			$msg=preg_replace("/\{\{notify\s+(.*?)\}\}/","",$msg);
		}
		if(preg_match("/\{\{notify_me (.*?)\}\}/",$msg,$m)) {
			$this->notify_me("notify_me: ".$m[1]);
			$msg=preg_replace("/\{\{notify_me\s+(.*?)\}\}/","",$msg);
		}
		if(preg_match("/\{\{fee_pay\}\}/",$msg,$m)) {
			$this->prepare_msg_fee_pay($uid);
			$msg=preg_replace("/\{\{fee_pay\}\}/","",$msg);
		}
		if(preg_match("/\{\{fee_refresh\}\}/",$msg,$m)) {
			$this->prepare_msg_fee_refresh($uid);
			$msg=preg_replace("/\{\{fee_refresh\}\}/","",$msg);
		}
		if(preg_match("/\{\{rest_fee\}\}/",$msg,$m)) {
			$rest_fee=$this->prepare_msg_fee_rest($uid);
			$msg=preg_replace("/\{\{rest_fee\}\}/",$rest_fee,$msg);
		}
		if(preg_match("/\{\{cashback_all\}\}/",$msg,$m)) {
			$rest_fee=$this->prepare_msg_fee_rest($uid);
			$msg=preg_replace("/\{\{cashback_all\}\}/",$rest_fee,$msg);
		}
		if(preg_match("/\{\{last_fee\}\}/",$msg,$m)) {
			$last_fee=$this->prepare_msg_fee_last($uid);
			$msg=preg_replace("/\{\{last_fee\}\}/",$last_fee,$msg);
		}
		if(preg_match("/\{\{cashback\}\}/",$msg,$m)) {
			$last_fee=$this->prepare_msg_fee_last($uid);
			$msg=preg_replace("/\{\{cashback\}\}/",$last_fee,$msg);
		}
		if(preg_match("/\{\{pay_bonus\s+(insales|bitrix)\s+(\d+)\}\}/",$msg,$m)) {
			$this->prepare_msg_pay_bonus($uid,trim($m[1]),intval($m[2]));
			$msg=preg_replace("/\{\{pay_bonus\s+(insales|bitrix)\s+(\d+)\}\}/","",$msg);
		}
		if(preg_match("/\{\{mark_new\s+(\d)\}\}/",$msg,$m)) {
			$this->mark_new($uid,$m[1]);
			$msg=preg_replace("/\{\{mark_new\s+(\d)\}\}/","",$msg);
		}
		$p="/\{\{add_event\s+\[([\d\,]+)\]\s+((\d\d\.\d\d.\d\d\d\d \d\d\:\d\d)|now)\}\}/";
		//{{add_event [4,5,6] 09.05.2024 11:00}}
		if(preg_match($p,$msg,$m)) {
			$arr=explode(',',$m[1]);
			$dt=$m[2];
			if(trim($dt)=='now') {
				$tm=time();
			} else {
				$date_obj = DateTime::createFromFormat('d.m.Y H:i', $dt);
				$tm = $date_obj->getTimestamp();
			}
			if($tm) {
				include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
				include_once "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
				$vkt=new vkt($this->database);
				$s=new vkt_send($this->database);
				$ctrl_id=$vkt->get_ctrl_id_by_db($this->database);
				foreach($arr AS $vkt_send_id) {
					$tm_shift_event=$this->dlookup("tm_shift","vkt_send_1","id='$vkt_send_id'");
					$s->vkt_send_task_del($vkt_send_id,$ctrl_id,$uid);
					$s->vkt_send_task_add($ctrl_id, $tm+$tm_shift_event, $vkt_send_id,3,$uid);
					$this->notify_me("add_event vkt_send_id=$vkt_send_id ctrl_id=$ctrl_id uid=$uid tm_event=".date("d.m.Y H:i",$tm+$tm_shift_event));
				}
			}
			$msg=preg_replace($p,"",$msg);
		}
		$p="/\{\{remove_event\s+\[([\d\,]+)\]\}\}/";
		if(preg_match($p,$msg,$m)) {
			$vkt_send_id=$m[1];
			$arr=explode(',',$m[1]);
			if($vkt_send_id) {
				include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
				include_once "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
				$vkt=new vkt($this->database);
				$s=new vkt_send($this->database);
				$ctrl_id=$vkt->get_ctrl_id_by_db($this->database);
				foreach($arr AS $vkt_send_id) {
					$s->vkt_send_task_del($vkt_send_id,$ctrl_id,$uid);
					$this->notify_me("remove_event vkt_send_id=$vkt_send_id ctrl_id=$ctrl_id uid=$uid");
				}
			}
			$msg=preg_replace($p,"",$msg);
		}
		
		$p="/\{\{gpt\s+(.*?)\}\}/s";
		if(preg_match($p,$msg,$m)) {
			$prompt=$m[1];
			global $vsegpt_secret,$vsegpt_model;
			$api_key = $vsegpt_secret;
			$this->query("INSERT INTO msgs SET
						uid='$uid',
						acc_id=10,
						tm=".time().",
						user_id='0',
						msg='".$this->escape($prompt)."',
						outg=0
						");
			if(trim($prompt)=='off' || trim($prompt)=='stop') { //{{gpt off}}
				$this->query("UPDATE cards SET fl_gpt=0 WHERE uid='$uid'");
				$msg=preg_replace($p,"",$msg);
			} else { //{{gpt prompt text}}
				$arr=$this->gpt_get_messages($uid,$limit=50);
				$arr[]=['role' => 'user', 'content' => $prompt];
				if($say= $this->vsegpt($api_key,$arr,$vsegpt_model)) {
					$msg=preg_replace($p,$say,$msg);
					$this->query("UPDATE cards SET fl_gpt='".time()."' WHERE uid='$uid'");
				} else
					$msg=preg_replace($p,"",$msg);
			}
		}
		
		$msg=$this->prepare_msg_manager($uid,$msg);
		$msg=$this->prepare_msg_price2($uid,$msg);
		$msg=$this->prepare_msg_promocode_save($uid,$msg);
		$msg=$this->prepare_msg_promocode($uid,$msg);
		$msg=$this->prepare_msg_webhook($uid,$msg);
		$msg=$this->prepare_msg_send_loyalty_card($uid,$msg);
		return $msg;
	}
	function prepare_msg_pay_bonus($uid,$platform,$amount) {
		global $insales_id,$insales_shop;
		if($platform=='insales') {
			if($insales_id) {
				include_once "/var/www/vlav/data/www/wwl/inc/insales.class.php";
				$in=new insales($insales_id,$insales_shop);
				$client_id=$this->dlookup("tool_uid","cards2other","tool='insales' AND uid='$uid'");
				$res=$in->bonus_create($client_id, $amount, $descr='WinWinLand');
				$this->notify_me(print_r($res,true));
			}
		}
	}
	function prepare_msg_fee_refresh($uid) {
		return $this->prepare_msg_fee_pay($uid);
	}
	function prepare_msg_fee_pay($uid) {
		//~ $vkt=new vkt($this->database);
		//~ $ctrl_id=$vkt->get_ctrl_id_by_db($this->database);
		//$this->notify_me("fee_pay uid=$uid ctrl_id=$this->ctrl_id");
		$this->query("DELETE FROM vkt_send_log WHERE uid='$uid' AND vkt_send_id=1 AND (email='' AND tg_id=0 AND vk_id=0 AND wa_id=0)");
		//exit;
		include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
		$klid=$this->dlookup("id","cards","uid='$uid'");
		$p=new partnerka($klid,$this->database);
		$p->fill_op($klid,0,$this->dt2(time()), $this->ctrl_id);
		if($klid_up=$this->dlookup("utm_affiliate","cards","id='$klid'"))
			$p->fill_op($klid_up,0,$this->dt2(time()), $this->ctrl_id);
		//$this->notify_me("INSALES fill_op for klid=$klid klid_up=$klid_up done OK");
	}
	function prepare_msg_fee_rest($uid) {
		//~ $vkt=new vkt($this->database);
		//~ $ctrl_id=$vkt->get_ctrl_id_by_db($this->database);
		//$this->notify_me("fee_pay uid=$uid ctrl_id=$this->ctrl_id");
		//exit;
		include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
		$klid=$this->dlookup("id","cards","uid='$uid'");
		$p=new partnerka($klid,$this->database);
		return $p->rest_fee($klid);
	}
	function prepare_msg_fee_last($uid) {
		//~ $vkt=new vkt($this->database);
		//~ $ctrl_id=$vkt->get_ctrl_id_by_db($this->database);
		//$this->notify_me("fee_pay uid=$uid ctrl_id=$this->ctrl_id");
		//exit;
		include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
		$klid=$this->dlookup("id","cards","uid='$uid'");
		$fee_sum=$this->dlast("fee_sum","partnerka_op","klid_up='$klid' AND tm>".(time()-(1*24*60*60)));
		return $fee_sum ? $fee_sum : 0;
	}
	function prepare_msg_manager($uid,$msg) {
		$p="/\{\{manager_assign\s+([\d]+)\}\}/s";
		if(preg_match($p,$msg,$m)) {
			$man_id=intval($m[1]);
			if($this->dlookup("id","users","del=0 AND id='$man_id'")!==false || $man_id==0 )
				$this->query("UPDATE cards SET man_id='$man_id' WHERE uid='$uid'");
			//$this->notify_me("HERE_$man_id");
		}
		return $msg=preg_replace($p,"",$msg);
	}

	function get_bc($klid) {
		return $this->dlookup("bc","users","del=0 AND klid='$klid'");
	}
	function get_user_login($user_id) {
		return $this->dlookup("username","users","del=0 AND id='$user_id'");
	}
	function get_user_passw($user_id) {
		return $this->dlookup("comm","users","del=0 AND id='$user_id'");
	}

	function bot_set($uid,$step) {
		$this->save_comm($uid,0,"SET BOT",4,$step);
	}
	function bot_chk($uid) {
		$tm_last=$this->fetch_assoc($this->query("SELECT source_id,vote,tm FROM msgs WHERE uid='$uid' ORDER BY id DESC LIMIT 1"))['tm'];
		if(!$r=$this->fetch_assoc($this->query("SELECT source_id,vote,tm FROM msgs WHERE uid='$uid' AND source_id=4 ORDER BY id DESC LIMIT 1")))
			return false;
		$tm_bot=$r['tm'];
		$step=$r['vote'];
		if( ($tm_last-$tm_bot)<5)
			return $step;
		return false;
	}

	function tag_create($tag_id,$tag_name, $tag_color, $fl_not_send=0) {
		if(!$this->dlookup("id","tags","id='$tag_id'")) {
			if(!$this->dlookup("id","tags","tag_name='$tag_name' AND del=0")) {
				$this->query("INSERT INTO tags SET
						id='$tag_id',
						tag_name='".$this->escape($tag_name)."',
						tag_color='".$this->escape($tag_color)."',
						fl_not_send='$fl_not_send'
						");
				return $this->insert_id();
			}
		}
		return false;
	}

	function tag_add($uid,$tag_id) {
		if(intval($uid) && intval($tag_id)) {
			if($this->dlookup("id","tags_op","uid='$uid' AND tag_id='$tag_id'"))
				return false;
			if($this->dlookup("id","tags","id='$tag_id'")) {
				if(isset($_SESSION['userid_sess']))
					$user_id=intval($_SESSION['userid_sess']); else $user_id=0;
				$tm=time();
				$this->query("INSERT INTO tags_op SET uid='$uid',tag_id='$tag_id',tm='$tm',user_id='$user_id'");
				return $this->insert_id();
			}
		}
		return false;
	}

	function tag_del($uid,$tag_id) {
		if(intval($uid) && intval($tag_id)) {
			if(!$this->dlookup("id","tags_op","uid='$uid' AND tag_id='$tag_id'"))
				return false;
			$this->query("DELETE FROM tags_op WHERE uid='$uid' AND tag_id='$tag_id'");
			return true;
		}
		return false;
	}


	function get_contrast_color($backgroundColor) {
	  $red = hexdec(substr($backgroundColor, 1, 2));
	  $green = hexdec(substr($backgroundColor, 3, 2));
	  $blue = hexdec(substr($backgroundColor, 5, 2));

	  $brightness = ($red * 299 + $green * 587 + $blue * 114) / 1000;

	  return ($brightness > 125) ? 'black' : 'white';
	}

	function get_next_avangard_orderid() {
		if ($r = $this->fetch_assoc($this->query("SELECT MAX(CAST(order_id AS UNSIGNED)) AS oid FROM avangard WHERE order_id REGEXP '^[0-9]+$'"))) {
			return $r['oid'] + 1; // Increment the maximum numeric order_id
		} else {
			return 1; // If no valid numeric order_id found, return 1
		}
		//~ if($r=$this->fetch_assoc($this->query("SELECT MAX(order_id) AS oid FROM avangard WHERE 1")))
			//~ return $r['oid']+1;
		//~ else
			//~ return 1;
	}
	function get_hold_period__($user_id) {
		global $hold;
		return $hold;
	}
	function get_hold_period($user_id) {
		include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
		$vkt=new vkt('vkt');
		$hold=$vkt->dlookup("hold","0ctrl","id=".$vkt->get_ctrl_id_by_db($this->database));
		$this->connect($this->database);
		if(!$hold) {
			$this->notify_me("error get_hold_period $this->database $user_id");
		}
		return $hold;
	}
	function hold_clr($uid) {
		//return;
		//print "hold_clr $uid <br>";
		if($old_user_id=$this->dlookup("user_id","cards","uid='$uid'")) {
			$this->query("UPDATE cards SET tm_user_id=0,user_id=0,utm_affiliate=0,pact_conversation_id=0,card_hold_tm=0 WHERE uid='$uid'");
			$this->save_comm($uid,0,"Сброс закрепления за партнером hold_clr ($old_user_id)",123,$old_user_id);
			//$this->notify_me("hold_clr uid=$uid user_id=$old_user_id db=$this->database");
		}
	}
	function hold_set($uid,$tm) {
		$this->query("UPDATE cards SET card_hold_tm='$tm' WHERE del=0 AND uid='$uid'");
	}
	function hold_chk($uid) {
		if(!$r=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE del=0 AND uid='$uid'")))
			return false;
		if(!$r['user_id'])
			return false;
		if($r['card_hold_tm']>0 && $r['card_hold_tm']<=time()) {
			$this->hold_clr($uid);
			return false;
		}
		if($r['card_hold_tm']>time())
			return $r['card_hold_tm'];

		//if($r['card_hold_tm']===0)
			
		$tm_user_id=$r['tm_user_id'];
		$hold_days=$this->get_hold_period($r['user_id']);

		if(!$tm_user_id) { //last user action in msgs
			if(!$tm_user_id=$this->dlast("tm","msgs","uid='$uid' AND (source_id=12 || source_id=1)"))
				$tm_user_id=$this->dlookup("tm","msgs","uid='$uid'");
			if(!$tm_user_id)
				return false;
		}
		$card_hold_tm=$tm_user_id+($hold_days*24*60*60);
		$this->query("UPDATE cards SET tm_user_id='$tm_user_id',card_hold_tm='$card_hold_tm' WHERE uid='$uid' ");
		if($card_hold_tm<=time()) {
			$this->hold_clr($uid);
			return false;
		}
		return $card_hold_tm;
		
		//~ $tm=time()- ($hold_days*24*60*60);
		//~ $dt=date("d.m.Y",$tm);
		//~ //print "$uid hold_days=$hold_days tm_user_id=".date("d.m.Y",$tm_user_id)." tm=$dt user_id={$r['user_id']} <br>\n";
		//~ if($tm_user_id<$tm) { //save new hold with new_user_id
			//~ $this->hold_clr($uid);
			//~ return false;
		//~ }
		//~ return true;
	}

	function ch_land_num($old_num,$new_num) {
		if(!$new_num || !$old_num)
			return false;
		if($this->dlookup("id","lands","del=0 AND land_num='$new_num'"))
			return false;
		rename($new_num, $new_num."_".time());
		if(!rename($old_num,$new_num))
			return false;
		$this->query("UPDATE lands SET land_num='$new_num' WHERE land_num='$old_num'");
		$this->query("UPDATE vkt_send_1 SET land_num='$new_num' WHERE land_num='$old_num'");
		
		$r=parse_url($this->dlookup("land_url","lands","land_num='$new_num'"));
		$land_url=$r['scheme']."://".$r['host']."/".$new_num;
		$this->query("UPDATE lands SET land_url='$land_url' WHERE land_num='$new_num'");

		return $new_num;
	}

	function is_cyrillic($msg) {
		return (preg_match('/[\p{Cyrillic}]/u',$msg));
	}

	var $vsegpt_err="";
	function vsegpt($api_key,$messages,$model='openai/gpt-3.5-turbo') {
		$url = 'https://api.vsegpt.ru/v1/chat/completions';
		$headers = array(
			'Content-Type: application/json',
			'Authorization: Bearer ' . $api_key
		);

		$data = array(
			'model' => $model,
			'messages' =>$messages
		);
			//~ 'messages' => array(
				//~ array('role' => 'user', 'content' => $prompt)
			//~ )

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			$this->vsegpt_err= "ERROR ".curl_error($ch);
			curl_close($ch);
			return false;
		} else {
			curl_close($ch);
			if(isset(json_decode($response,true)['choices'][0]['message']['content']))
				return json_decode($response,true)['choices'][0]['message']['content'];
			else
				return $response;
		}
	}
	function gpt_get_messages($uid,$limit=50) {
		if($last_id=$this->dlast("id","msgs","uid='$uid' AND acc_id=10")) {
			$res=$this->query("SELECT * FROM msgs
					WHERE uid='$uid' AND id>=$last_id AND (outg=0 OR outg=1 OR outg=2)
					ORDER BY id ASC",0);
		} else {
			$num_rows=$this->num_rows($this->query("SELECT id FROM msgs WHERE uid='$uid' AND (outg=0 OR outg=1 OR outg=2)"));
			$from=$num_rows-$Limit;
			if($from<0)
				$from=0;
			$res=$this->query("SELECT * FROM msgs
					WHERE uid='$uid' AND source_id=0 AND (outg=0 OR outg=1)
					ORDER BY id ASC
					LIMIT $from,$limit");
		}
		$arr[]=['role' => 'system', 'content' => "You are a large language model.
Carefully heed the user's instructions.
Respond without Markdown."];
		while($r=$this->fetch_assoc($res)) {
			$role=($r['outg']==0)?"user":"assistant";
			$arr[]=['role' => $role, 'content' => $r['msg']];
		}
		return $arr;
	}
	function trim_comm($comm, $len) {
		// Ensure the input is a string and the length is a positive integer
		if (!is_string($comm) || !is_int($len) || $len < 0) {
			return '';
		}

		// If the comment is already shorter than the specified length, return it as is
		if (mb_strlen($comm, 'UTF-8') <= $len) {
			return $comm;
		}

		// Cut the comment to the desired length
		$truncated = mb_substr($comm, 0, $len, 'UTF-8');

		// Find the last space in the truncated string
		$last_space_pos = mb_strrpos($truncated, ' ', 0, 'UTF-8');

		// If a space is found, return the substring up to the last space
		if ($last_space_pos !== false) {
			return mb_substr($truncated, 0, $last_space_pos, 'UTF-8')." ...";
		}

		// If no space is found, return an empty string or the truncated version (depends on your needs)
		return $comm;
	}
	function cards_add_par($uid,$par,$val,$val_text=null) {
		$this->query("DELETE FROM cards_add WHERE uid='$uid' AND par='$par'");
		$this->query("INSERT INTO cards_add SET
						uid='$uid',
						par='$par',
						val='".$this->escape($val)."',
						val_text='".$this->escape($val_text)."'
					");
		return $this->insert_id();
	}
	function cards_read_par($uid) {
		$arr=[];
		$res=$this->query("SELECT * FROM cards_add WHERE uid='$uid'");
		while($r=$this->fetch_assoc($res)) {
			if(!empty($r['val']))
				$arr[$r['par']]=$r['val'];
			else
				$arr[$r['par']]=$r['val_text'];
		}
		return $arr;
	}
	public $fl_cards_add; //1-new added, 0 - old
	function cards_add($r,$update_if_exist=false) {
		/*
		$r=[
			'tm'=>0, //for new uid - tm=time() if 0
			'uid'=>0, //если не найдет в базе то выход с ошибкой
			'man_id'=>0,
			'first_name'=>'Вася',
			'last_name'=>'Иванов',
			'phone'=>'+7-000-9999999',
			'email'=>'123456789@mail.ru',
			'city'=>'СПб',
			'tg_id'=>'123456789', //if not 0 will be added
			'tg_nic'=>'qwerty', //if not empty will be added
			'vk_id'=>'123456789', //if not 0 will be added
			'razdel'=>'3', //default 4 (D)  will added
			'source_id'=>'1', //0
			'user_id'=>'0',
			'klid'=>'0',
			'wa_allowed'=>'0',
			'comm1'=>'12345',
			'tz_offset'=>'3',
			'test_cyrillic'=>false
		];
		*/
		$this->fl_cards_add=0;
		if(isset($r['uid'])) {
			if($uid=$this->get_uid(intval($r['uid']))) {
				if(!$this->dlookup("id","cards","uid='$uid'"))
					return false;
				$uid_md5= $uid ? $this->uid_md5($uid) : 0;
				$vk_id = $uid>0 ? $uid : 0;
			}
		} else
			$uid=false;
		if(!$uid)
			if(isset($r['vk_id']))
				$uid=$this->get_uid(intval($r['vk_id']));
		$first_name=!empty(trim($r['first_name'])) ? mb_substr(trim($r['first_name']),0,32) : "";
		$last_name=!empty(trim($r['last_name'])) ? mb_substr(trim($r['last_name']),0,32) : "";

		$mob="";
		if(isset($r['phone']))
			if(!$mob=$this->check_mob($r['phone']))
				$mob="";

		$email="";
		if(isset($r['email']))
			$email=($this->validate_email($r['email'])) ? strtolower(trim($r['email'])) : "";

		$city="";
		if(isset($r['city']))
			if(!empty(trim($r['city']))) {
				$city=mb_substr(trim($r['city']),0,32);
				$city_sql="city='".$this->escape($city)."',";
			}

		$razdel=(isset($r['razdel'])) ? intval($r['razdel']) : 0;
		$source_id=(isset($r['source_id'])) ? intval($r['source_id']) : 0;

		$wa_allowed=(isset($r['wa_allowed'])) ? intval($r['wa_allowed']) : 0;
		$tz_offset=(isset($r['tz_offset'])) ? intval($r['tz_offset']) : 0;
		$vk_id=(isset($r['vk_id'])) ? intval($r['vk_id']) : 0;
		$tg_id=(isset($r['tg_id'])) ? intval($r['tg_id']) : 0;
		$tg_nic=(isset($r['tg_nic'])) ? mb_substr(trim($r['tg_nic']),0,64) : "";

		$user_id=(isset($r['user_id'])) ? intval($r['user_id']) : 0;
		$klid=(isset($r['klid'])) ? intval($r['klid']) : 0;
		if($klid!=$this->get_klid($user_id)) {
			$this->notify_me("cards_add error: klid=$klid not corresponds user_is=$user_id");
			return false;
		}

		$man_id=(isset($r['man_id'])) ? intval($r['man_id']) : 0;

		$comm1="";
		if(isset($r['comm1']))
			$comm1=mb_substr(trim($r['comm1']),0,4096);

		if(!empty($mob) && !$uid) {
			$uid=$this->dlookup("uid","cards","mob_search='$mob' AND del=0");
		}
		if(!empty($email) && !$uid) {
			$uid=$this->dlookup("uid","cards","email='$email' AND del=0");
		}

		$test_cyrillic=false;
		if(isset($r['test_cyrillic']))
			$test_cyrillic=$r['test_cyrillic'];
		foreach($r AS $key=>$val) {
			if($this->is_cyrillic($val))
				$test_cyrillic=true;
		}
		if(!$test_cyrillic && !empty($comm) ) {
			return false;
		}
		if(!$uid) {
			$uid=$this->get_unicum_uid();
			$uid_md5=$this->uid_md5($uid);
			$tm=isset($r['tm']) ? intval($r['tm']) : time();
			if(!$tm)
				$tm=time();
			$razdel=$razdel ? $razdel : 4;
			$q="INSERT INTO cards SET
					uid='$uid',
					uid_md5='$uid_md5',
					name='".$this->escape($first_name)."',
					surname='".$this->escape($last_name)."',
					email='".$this->escape($email)."',
					mob='$mob',
					mob_search='$mob',
					$city_sql
					acc_id=2,
					razdel='$razdel',
					source_id='$source_id',
					fl_newmsg=0,
					tm_lastmsg=".time().",
					tm='$tm',
					man_id='$man_id',
					user_id='$user_id',
					vk_id='$vk_id',
					telegram_id='$tg_id',
					telegram_nic='".$this->escape($tg_nic)."',
					comm1='".$this->escape($comm1)."',
					utm_affiliate='$klid',
					tzoffset='$tz_offset',
					wa_allowed='$wa_allowed'
					";
			$this->query($q,0);
			$this->fl_cards_add=1;
			if(!$mob && empty($email)) {
			}
		} else { //IF EXISTS IN CARDS
			if(!empty($first_name))
				$this->query("UPDATE cards SET name='".$this->escape($first_name)."' WHERE uid='$uid' AND del=0 AND name=''");
			if(!empty($last_name))
				$this->query("UPDATE cards SET surname='".$this->escape($last_name)."' WHERE uid='$uid' AND del=0 AND surname=''");
			if($this->check_mob($mob) )
				$this->query("UPDATE cards SET mob='$mob',mob_search='$mob' WHERE uid='$uid' AND del=0 AND mob='' AND mob_search=''");
			if(!empty($email) )
				$this->query("UPDATE cards SET email='".$this->escape($email)."' WHERE uid='$uid' AND del=0 AND email=''");
			if(!empty($city))
				$this->query("UPDATE cards SET city='".$this->escape($city)."' WHERE uid='$uid' AND del=0 AND city=''");
			if(!empty($comm1)) {
				$this->query("UPDATE cards SET comm1='".
					$this->escape($comm1."\n".$this->dlookup("comm1","cards","uid='$uid' AND del=0"))
					."' WHERE uid='$uid'");
			}
			if($user_id && $klid)
				$this->query("UPDATE cards SET user_id='$user_id',utm_affiliate='$klid' WHERE uid='$uid' AND del=0 AND user_id=0");
			if($tz_offset)
				$this->query("UPDATE cards SET tzoffset='$tz_offset' WHERE uid='$uid' AND del=0 AND tz_offset=0");
			if($tg_id)
				$this->query("UPDATE cards SET telegram_id='$tg_id' WHERE uid='$uid' AND del=0 AND telegram_id=0");
			if($tg_nic)
				$this->query("UPDATE cards SET telegram_nic='$tg_nic' WHERE uid='$uid' AND del=0 AND telegram_nic=''");
			if($vk_id)
				$this->query("UPDATE cards SET vk_id='$vk_id' WHERE uid='$uid' AND del=0 AND vk_id=0");
			if($source_id)
				$this->query("UPDATE cards SET source_id='$source_id' WHERE uid='$uid' AND del=0 AND source_id=0");
			if($razdel!=2 && $razdel)
				$this->query("UPDATE cards SET razdel='$razdel' WHERE uid='$uid' AND del=0 AND razdel=0");
		}
		if($update_if_exist) {
			if(!empty($first_name))
				$this->query("UPDATE cards SET name='".$this->escape($first_name)."' WHERE uid='$uid'");
			if(!empty($last_name))
				$this->query("UPDATE cards SET surname='".$this->escape($last_name)."' WHERE uid='$uid'");
			if($this->check_mob($mob) )
				$this->query("UPDATE cards SET mob='$mob',mob_search='$mob' WHERE uid='$uid'");
			if(!empty($email) )
				$this->query("UPDATE cards SET email='".$this->escape($email)."' WHERE uid='$uid'");
			if(!empty($city))
				$this->query("UPDATE cards SET city='".$this->escape($city)."' WHERE uid='$uid'");
			if($user_id && $klid)
				$this->query("UPDATE cards SET user_id='$user_id',utm_affiliate='$klid' WHERE uid='$uid' AND user_id=0");
			if($tz_offset)
				$this->query("UPDATE cards SET tzoffset='$tz_offset' WHERE uid='$uid'");
			if($tg_id)
				$this->query("UPDATE cards SET telegram_id='$tg_id' WHERE uid='$uid'");
			if($tg_nic)
				$this->query("UPDATE cards SET telegram_nic='$tg_nic' WHERE uid='$uid'");
			if($vk_id)
				$this->query("UPDATE cards SET vk_id='$vk_id' WHERE uid='$uid'");
			if($source_id && $source_id)
				$this->query("UPDATE cards SET source_id='$source_id' WHERE uid='$uid'");
			if($razdel!=2 && $razdel)
				$this->query("UPDATE cards SET razdel='$razdel' WHERE uid='$uid'");
			if(isset($r['comm1']))
				$this->query("UPDATE cards SET comm1='".$this->escape($comm1)."' WHERE uid='$uid'");
		}
		return $uid;
	}
	function split_fio($fio) {
		$tmp=$this->database;
		$this->connect('vkt');
		// Use preg_split to split the string by one or more spaces
		$parts = preg_split('/\s+/', trim($fio));
		//$this->notify_me(print_r($parts,true));
		// Check if we got at least two parts
		if (count($parts) < 2) {
			return [
				'f_name' => trim($fio),
				'l_name' => "",
				'm_name' => "",
			];
		}
		if (count($parts) ==3)
			$m_name=trim($parts[2]); else $m_name="";

		if (count($parts) >3)
			return [
				'f_name' => $parts[0],
				'l_name' => $parts[2],
				'm_name' => $parts[1],
			];

		
		// Return the first and last name
		$parts[0]=mb_convert_case(mb_strtolower(trim($parts[0])), MB_CASE_TITLE);
		$parts[1]=mb_convert_case(mb_strtolower(trim($parts[1])), MB_CASE_TITLE);
		
		if($this->dlookup("id","russian_names","name='".$parts[0]."'")) {
			$this->connect($tmp);
			return [
				'f_name' => $parts[0],
				'l_name' => $parts[1],
				'm_name' => $m_name,
			];
		} elseif($this->dlookup("id","russian_names","name='".$parts[1]."'")) {
			$this->connect($tmp);
			return [
				'f_name' => $parts[1],
				'l_name' => $parts[0],
				'm_name' => $m_name,
			];
		} else {
			$this->connect($tmp);
			return [
				'f_name' => $parts[0],
				'l_name' => $parts[1],
				'm_name' => $m_name,
			];
		} 
	}
	function ctrl_tool_set($ctrl_id,$tool,$key,$val) {
		if($ctrl_id) {
			if($this->database!='vkt') {//insales
				$tmp=$this->database;
				$this->connect('vkt');
			}
		}
		if(!$ctrl_id) $ctrl_id=0;
		$this->query("DELETE FROM 0ctrl_tools WHERE tool='$tool' AND ctrl_id='$ctrl_id' AND tool_key='$key'");
		$this->query("INSERT INTO 0ctrl_tools SET tool='$tool',ctrl_id='$ctrl_id',tool_key='$key',tool_val='".$this->escape($val)."'");
		if($ctrl_id) {
			if($this->database!='vkt') {
				$this->connect($tmp);
			}
		}
		return $this->insert_id();
	}
	function ctrl_tool_get($ctrl_id,$tool,$key) {
		if($ctrl_id) {
			$tmp=$this->database;
			$this->connect('vkt');
		}
		if(!$ctrl_id) 
			$val=$this->dlookup("tool_val","0ctrl_tools","tool='$tool' AND tool_key='$key'");
		else
			$val=$this->dlookup("tool_val","0ctrl_tools","ctrl_id='$ctrl_id' AND tool='$tool' AND tool_key='$key'");
		if($ctrl_id) {
			$this->connect($tmp);
		}
		return $val;
	}
	function ctrl_tool_get_key($ctrl_id,$tool) {
		if($ctrl_id) {
			$tmp=$this->database;
			$this->connect('vkt');
		}
		if(!$ctrl_id)
			$val=$this->dlookup("tool_key","0ctrl_tools","tool='$tool'");
		else
			$val=$this->dlookup("tool_key","0ctrl_tools","tool='$tool' AND ctrl_id='$ctrl_id'");
		if($ctrl_id) {
			$this->connect($tmp);
		}
		return $val;
	}
	function ctrl_tool_search($tool,$tool_key,$search) {
		return $this->dlookup("ctrl_id","0ctrl_tools","tool='$tool' AND tool_key='$tool_key'");
	}
	function users_notif_set($user_id,$key,$val) {
		$this->query("DELETE FROM users_notif WHERE user_id='$user_id' AND fl_key='$key'");
		$this->query("INSERT INTO users_notif SET fl_val='$val', user_id='$user_id', fl_key='$key'");
		return $this->insert_id();
	}
	function users_notif_get($user_id,$key) {
		if(empty($key))
			return 1;
		$val=$this->dlookup("fl_val","users_notif","user_id='$user_id' AND fl_key='$key'");
		if($val===false)
			$val=1;
		return $val;
	}
	function tbl_delayed($r) {
		$tm=$r['tm_delay'];
		$today=$this->dt1(time());
		$tomorrow=$this->dt2(time())+1;
		switch($r['tm_delay_imp']) {
			case 0: $tm_delay_imp=""; break;
			case 1: $tm_delay_imp="<i class='fa fa-bookmark' style='color:#FFA500;'></i>"; break;
			case 2: $tm_delay_imp="<i class='fa fa-bookmark' style='color:#DC0000;'></i>"; break;
		}
		if($tm>0) {
			if($tm<$today) {
				$bg="grey"; $text="white";
				$dt=date("H:i",$tm)=="00:00"?date("d.m.Y",$tm):date("d.m.Y H:i",$tm);
				$title="просрочено!!!";
			} elseif($tm>=$today && $tm<$tomorrow) {
				$bg="danger"; $text="white";
				$dt=date("H:i",$tm)=="00:00"?"сегодня":"сегодня в ".date("H:i",$tm);
				$title=date("d.m.Y H:i",$tm);
			} elseif($tm>=$tomorrow && $tm<($tomorrow+(24*60*60))) {
				$bg="warning"; $text="black";
				$dt=date("H:i",$tm)=="00:00"?"завтра":"завтра в ".date("H:i",$tm);
				$title=date("d.m.Y H:i",$tm);
			} else {
				$bg="primary"; $text="white";
				$dt=date("H:i",$tm)=="00:00"?date("d.m.Y",$tm):date("d.m.Y H:i",$tm);
				$dt=date("d.m.Y H:i",$tm);
				$title=date("d.m.Y H:i",$tm);
			}
			return "$tm_delay_imp <span class='badge bg-$bg text-$text p-2' title='$title'>".$dt."</span>";
		} 
		return "$tm_delay_imp";
	}
	function format_time($seconds) {
		$seconds=intval($seconds);
		// Ensure the input is a positive integer
		if (!is_int($seconds) || $seconds < 0) {
			return "00:00";
		}

		// Calculate hours, minutes, and seconds
		$hours = floor($seconds / 3600);
		$minutes = floor(($seconds % 3600) / 60);
		$seconds = $seconds % 60;

		// Format the time based on whether hours are present
		if ($hours > 0) {
			return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
		} else {
			return sprintf('%02d:%02d', $minutes, $seconds);
		}
	}
	function format_money($number, $currency = '₽') {
		return number_format($number, 0, '', ' ') . ' ' . $currency;
	}
	function is_yclients($ctrl_id) {
		$salon_id=$this->ctrl_tool_get($ctrl_id,'yclients','salon_id');
		return $salon_id ? $salon_id : false;
	}
	function create_short_links_table() {
		if(!$this->num_rows($this->query("SHOW TABLES LIKE 'short_links'"))) {
			try {
				$this->connect($this->database,true);
				$sql = "CREATE TABLE IF NOT EXISTS short_links (
					id INT AUTO_INCREMENT PRIMARY KEY,
					hash VARCHAR(10) UNIQUE NOT NULL,
					params_json TEXT NOT NULL,
					created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
					expires_at TIMESTAMP NULL,
					clicks INT DEFAULT 0,
					INDEX idx_hash (hash),
					INDEX idx_expires (expires_at)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
				";
				$this->query($sql);
				$this->connect($this->database,false);
				return true;
			} catch (Exception $e) {
				$this->err_msg[] = "Database error: " . $e->getMessage();
				return false;
			}
		}
		return true;
	}
	function generate_short_link($params = [], $url, $expire_days = 30) {
		$this->create_short_links_table();
		// Serialize the entire params array
		$serialized_params = json_encode($params, JSON_UNESCAPED_UNICODE);
		
		if ($serialized_params === false) {
			$this->err_msg[] = "Failed to encode parameters";
			return false;
		}
		
		// Generate unique hash
		do {
			$hash = substr(md5(uniqid() . microtime()), 0, 8);
		} while ($this->dlookup("id", "short_links", "hash='$hash'"));
		
		// Calculate expiration date
		$expires_at = date('Y-m-d H:i:s', strtotime("+$expire_days days"));
		
		// Save to database with serialized params
		$this->query("INSERT INTO short_links SET
			hash = '$hash',
			params_json = '" . $this->escape($serialized_params) . "',
			expires_at = '$expires_at',
			created_at = NOW()");
		
		return $url . "?$hash";
		return $hash;
	}

	function resolve_short_link($hash) {
		$this->create_short_links_table();
		// Get serialized params
		$result = $this->fetch_assoc($this->query("
			SELECT params_json, expires_at 
			FROM short_links 
			WHERE hash = '" . $this->escape($hash) . "'
			AND (expires_at IS NULL OR expires_at > NOW())
		"));
		
		if ($result && !empty($result['params_json'])) {
			// Increment click counter
			$this->query("UPDATE short_links SET clicks = clicks + 1 WHERE hash = '" . $this->escape($hash) . "'");
			
			// Return the decoded params array
			return json_decode($result['params_json'], true);
		}
		
		return false;
	}

	// Helper to get full URL with parameters
	function get_short_link_url($hash) {
		$params = $this->resolve_short_link($hash);
		if (!$params) return false;
		
		// Build URL with all parameters
		$base_url = strtok($this->get_cashier_url(), '?');
		$query_string = http_build_query($params);
		
		return $base_url . '?' . $query_string;
	}



	const hide_contacts=false;
	function disp_mob($mob) {
		if($_SESSION['username']=='admin1' && self::hide_contacts)
			return "7(номер скрыт)";
		return $mob;
	}
	function disp_email($email) {
		if($_SESSION['username']=='admin1' && self::hide_contacts)
			return "емэйл скрыт";
		return $email;
	}
	function disp_tg($tg) {
		if($_SESSION['username']=='admin1' && self::hide_contacts)
			return "тг скрыт";
		return $tg;
	}
	function disp_surname($surname) {
		if($_SESSION['username']=='admin1' && self::hide_contacts)
			return "$surname";
		return $surname;
	}
	function disp_name_cp($name) {
		if($_SESSION['username']=='julia' && self::hide_contacts) {
			if(isset($_GET['fio_clr'])) {
				unset($_SESSION['fio_subst']);
				unset($_SESSION['fio_samples']);
				unset($_GET['fio_clr']);
			}
			if(!isset($_SESSION['fio_samples'])) {
				include "/var/www/vlav/data/www/wwl/d/1000/fio_samples.inc.php";
				$_SESSION['fio_samples']=$fio_samples;
			}
			if(!isset($_SESSION['fio_subst']))
				$_SESSION['fio_subst']=[];
			//print "HERE_".array_key_first($_SESSION['fio_samples']); exit;
			//	print "HERE_ ".print_r($_SESSION['fio_subst'])."<br>";
			$name=trim($name);
			if(!array_key_exists($name,$_SESSION['fio_subst'])) {
				if(($key=array_key_first($_SESSION['fio_samples']))===false)
					return "name";
				$_SESSION['fio_subst'][$name]=$_SESSION['fio_samples'][$key];
				//print "HERE_$key ".$_SESSION['fio_samples'][$key];
				unset($_SESSION['fio_samples'][$key]);
			}
			return $_SESSION['fio_subst'][$name];
			
			if(is_numeric(trim($name)))
				return "NAME";
		}
		return $name;
	}
	//////////////////////
}

class db_pdo extends db_mysqli {
    public static $pdo = null;
    public $database = '';
    public $last_query = '';
    public $disp_mysql_errors = 0;
    private $auto_convert_queries = true;
    
    public function connect($db = false, $root = false) {
        //$this->check_php_version();
       // $this->check_is_ip_banned();
       // $this->before_connect();
        $this->database = $db;
        
        try {
            $r = $this->get_mysql_env($root);
            $mysql_user = $r['DB_USER'];
            $mysql_passw = $r['DB_PASSW'];

            //$this->notify_me("$mysql_user $mysql_passw");
            
            $dsn = "mysql:host=localhost;" . ($db ? "dbname=$db;" : "") . "charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            self::$pdo = new PDO($dsn, $mysql_user, $mysql_passw, $options);
            
            if ($db) {
                //$this->vk_token = $this->get_vk_token_pdo();
            }
            
            return true;
            
        } catch (PDOException $e) {
            $this->pdo_log("Database connection failed: " . $e->getMessage());
            return false;
        }
    }
    
    private function pdo_log($msg) {
        error_log($msg);
        $this->notify_me("mysql error: $msg");
       return;
    }
    
	public function query($qstr, $params = [], $disp_errors = false) {
		$this->last_query = $qstr;
		$disp_mysql_errors = ($disp_errors === false) ? $this->disp_mysql_errors : $disp_errors;
		if($params==1) {
			$this->notify_me($qstr);
			$params=[];
		}
		
		if ($this->is_localhost()) {
			$disp_mysql_errors = 2;
		}
		
		if ($this->chk_empty_fields($qstr)) {
			$this->notify_me("db=$this->database : chk_empty_fields: $qstr");
			return false;
		}
		
		try {
			// 1. Manual parameters
			if (!empty($params)) {
				$stmt = self::$pdo->prepare($qstr);
				$stmt->execute($params);
				return $stmt;
			}
			
			// 2. Try auto-conversion with fallback
			if ($this->auto_convert_queries && $this->shouldConvertQuery($qstr)) {
				$converted = $this->convertToParameterizedQuery($qstr);
				
				if ($converted['has_parameters']) {
					try {
						// First try converted query
						//$this->print_r($converted);
						$stmt = self::$pdo->prepare($converted['sql']);
						$stmt->execute($converted['params']);
						return $stmt;
					} catch (PDOException $e) {
						// Converted failed - silently fallback to original
						// No logging to avoid spamming on minor conversion issues
					}
				}
			}
			
			// 3. Original query (fallback or no conversion)
		//$this->notify_me($_SERVER['SCRIPT_NAME']."\n".$qstr);
			return self::$pdo->query($qstr);
		} catch (PDOException $e) {
			return $this->handle_query_error($e, $qstr, $params, $disp_mysql_errors);
		}
	}
    
    public function num_rows($stmt) {
        if (!$stmt || $stmt === true) {
            return 0;
        }
        $data = $stmt->fetchAll();
        $stmt->execute(); // Reset cursor
        return count($data);
    }
    
    public function escape($string) {
		return str_replace("'", '', $string);
        if (!is_string($string)) {
            return $string;
        }
        $quoted = self::$pdo->quote($string);
        return substr($quoted, 1, -1); // Remove quotes
    }
    
    // Private helper methods
    private function shouldConvertQuery($qstr) {
        $skip_patterns = [
            '/^SHOW\s+/i',
            '/^DESC\s+/i',
            '/^DESCRIBE\s+/i',
            '/^EXPLAIN\s+/i',
            '/^CREATE\s+/i',
            '/^ALTER\s+/i',
            '/^DROP\s+/i'
        ];
        
        foreach ($skip_patterns as $pattern) {
            if (preg_match($pattern, $qstr)) {
                return false;
            }
        }
        return true;
    }
    
    private function convertToParameterizedQuery($sql) {
        $result = ['sql' => $sql, 'params' => [], 'has_parameters' => false];
        
        if ($this->debug) {
            echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; margin: 5px;'>";
            echo "<strong>=== Auto-converting quoted strings to parameters ===</strong><br>";
            echo "Original SQL: " . htmlspecialchars($sql) . "<br>";
        }
        
        // Find all quoted strings in the SQL
        $pattern = '/(\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\')/';
        
        $matches = [];
        preg_match_all($pattern, $sql, $matches, PREG_OFFSET_CAPTURE);
        
        if (empty($matches[0])) {
            if ($this->debug) {
                echo "No quoted strings found - skipping conversion<br>";
                echo "</div>";
            }
            return $result;
        }
        
        if ($this->debug) {
            echo "Found " . count($matches[0]) . " quoted string(s)<br>";
        }
        
        $param_count = 0;
        $new_sql = $sql;
        $params = [];
        
        // Process in REVERSE order (so positions don't shift)
        $string_matches = $matches[0];
        usort($string_matches, function($a, $b) {
            return $b[1] - $a[1]; // Sort by position descending
        });
        
        foreach ($string_matches as $match) {
            $quoted_value = $match[0];
            $position = $match[1];
            
            // Skip if it's in a SQL keyword context (table names, etc.)
            if ($this->isInSqlKeywordContext($sql, $position)) {
                if ($this->debug) {
                    echo "Skipping at position $position: appears to be in SQL keyword context<br>";
                }
                continue;
            }
            
            $param_name = ":param_" . $param_count++;
            
            // Replace the quoted string with parameter
            $new_sql = substr_replace($new_sql, $param_name, $position, strlen($quoted_value));
            
            // Remove quotes for binding
            $params[$param_name] = substr($quoted_value, 1, -1);
            
            if ($this->debug) {
                echo "Replaced '$quoted_value' with $param_name at position $position<br>";
                echo "Parameter value: '" . $params[$param_name] . "'<br>";
            }
        }
        
        if ($param_count > 0) {
            $result['sql'] = $new_sql;
            $result['params'] = $params;
            $result['has_parameters'] = true;
        }
        
        if ($this->debug) {
            echo "<hr><strong>Converted SQL:</strong><br>";
            echo "<pre>" . htmlspecialchars($result['sql']) . "</pre>";
            echo "<strong>Parameters:</strong><br>";
            echo "<pre>" . htmlspecialchars(print_r($result['params'], true)) . "</pre>";
            echo "</div>";
        }
        
        return $result;
    }
    
    private function isInSqlKeywordContext($sql, $position) {
        $context_start = max(0, $position - 30);
        $context_length = 60;
        $context = substr($sql, $context_start, $context_length);
        
        // Check if the quoted string is part of SQL keywords (not values)
        $keyword_patterns = [
            '/\bFROM\s+\'[^\']*\'/i',      // FROM 'table_name'
            '/\bINTO\s+\'[^\']*\'/i',      // INTO 'table_name'  
            '/\bTABLE\s+\'[^\']*\'/i',     // TABLE 'table_name'
            '/\bDATABASE\s+\'[^\']*\'/i',  // DATABASE 'db_name'
            '/\bINDEX\s+\'[^\']*\'/i',     // INDEX 'index_name'
            '/\bKEY\s+\'[^\']*\'/i',       // KEY 'key_name'
            '/\bVALUES?\s*\(\s*\'[^\']*\'/i', // VALUE('string') or VALUES('string')
        ];
        
        foreach ($keyword_patterns as $pattern) {
            if (preg_match($pattern, $context)) {
                return true;
            }
        }
        
        return false;
    }
    
    private function handle_query_error($e, $qstr, $params, $disp_mysql_errors) {
        $errorCode = $e->getCode();
        $errorMessage = $e->getMessage();
        
        // Retry logic for connection errors
        if (in_array($errorCode, ['2006', '2014', '2013'])) {
            for ($cnt_errors = 5; $cnt_errors > 0; $cnt_errors--) {
                sleep(3);
                try {
                    $this->connect($this->database);
                    return !empty($params) ? 
                        $this->query($qstr, $params, 0, $disp_mysql_errors) : 
                        self::$pdo->query($qstr);
                } catch (PDOException $retryException) {
                    continue;
                }
            }
        }
        
        // Error reporting
        if ($disp_mysql_errors == 2) {
            $err = "PDO Error: $qstr<br>\n" . $errorMessage;
            if (!empty($params)) $err .= "<br>Params: " . print_r($params, true);
        } elseif ($disp_mysql_errors == 1) {
            $err = "Database error";
        } else {
            $err = "";
        }
        
        if (!empty($err)) {
            echo "<div class='alert alert-danger'>$err</div>";
        }
        
        $this->pdo_log("Database error: " . $errorMessage . " in query: " . $qstr);
        
        if ($disp_mysql_errors > 0) {
            exit;
        }
		$this->safe_email_notification($e, $qstr, $params);        
        return false;
    }
	private function safe_email_notification($e, $qstr, $params) {
		try {
			$errorCode = $e->getCode();
			$errorMessage = $e->getMessage();
			
			// Build email message
			$message = "PDO Database Error\n";
			$message .= "===================\n\n";
			
			$message .= "Database: " . $this->database . "\n";
			$message .= "Error Code: " . $errorCode . "\n";
			$message .= "Error Message: " . $errorMessage . "\n\n";
			
			$message .= "Query:\n" . $qstr . "\n\n";
			
			if (!empty($params)) {
				$message .= "Parameters:\n" . print_r($params, true) . "\n\n";
			}
			
			$message .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
			$message .= "Script: " . ($_SERVER['SCRIPT_NAME'] ?? 'Unknown') . "\n";
			$message .= "IP Address: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "\n\n";
			
			// Add backtrace
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
			$message .= "Backtrace:\n" . print_r($backtrace, true) . "\n\n";
			
			// Add globals if requested (simplified version)
			$message .= "GET Parameters:\n" . print_r($_GET, true) . "\n\n";
			$message .= "POST Parameters:\n" . print_r($_POST, true) . "\n\n";
			
			// Send email using parent class method
			if (method_exists($this, 'email')) {
				$this->email(
					$emails = array("vlav@mail.ru"),
					"PDO Database Error " . date('Y-m-d H:i:s'),
					$message,
					$from = "noreply@winwinland.ru",
					$fromname = "WWL PDO",
					$add_globals = true
				);
			} else {
				// Fallback: log error if email method doesn't exist
				error_log("PDO Error (email method not found): " . $message);
			}
			
		} catch (Exception $emailError) {
			// If email fails, log it
			error_log("Failed to send PDO error email: " . $emailError->getMessage());
		}
	}    
    private function get_vk_token_pdo() {
        $stmt = $this->query("SHOW TABLES LIKE 'vklist_acc'");
        if ($stmt && $stmt->rowCount() > 0) {
            $stmt = $this->query("SELECT * FROM vklist_acc WHERE id=1");
            if ($stmt && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $row['access_token'];
            }
        }
        return '';
    }
    
    // Additional helper methods for convenience
	// Fetch as numeric array
	function fetch_row($stmt) {
		if (!$stmt || $stmt === true || !($stmt instanceof PDOStatement)) {
			return false;
		}
		return $stmt->fetch(PDO::FETCH_NUM);
	}

	// Fetch as both associative and numeric array
	function fetch_array($stmt) {
		if (!$stmt || $stmt === true || !($stmt instanceof PDOStatement)) {
			return false;
		}
		return $stmt->fetch(PDO::FETCH_BOTH);
	}

	// Fetch as associative array (already have this as fetch_assoc)
	function fetch_assoc($stmt) {
		if (!$stmt || $stmt === true || !($stmt instanceof PDOStatement)) {
			return false;
		}
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	// Fetch as object
	function fetch_object($stmt) {
		if (!$stmt || $stmt === true || !($stmt instanceof PDOStatement)) {
			return false;
		}
		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	// Get all rows as numeric arrays
	function fetch_all_rows($stmt) {
		if (!$stmt || $stmt === true || !($stmt instanceof PDOStatement)) {
			return [];
		}
		return $stmt->fetchAll(PDO::FETCH_NUM);
	}

	// Get all rows as associative arrays
	function fetch_all_assoc($stmt) {
		if (!$stmt || $stmt === true || !($stmt instanceof PDOStatement)) {
			return [];
		}
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
    
    public function lastInsertId() {
        return self::$pdo->lastInsertId();
    }
    public function insert_id() {
        return self::$pdo->lastInsertId();
    }
    function affected_rows($stmt = null) {
        if ($stmt instanceof PDOStatement) {
            return $stmt->rowCount();
        }
        return 0;
    }
    
    public function beginTransaction() {
        return self::$pdo->beginTransaction();
    }
    
    public function commit() {
        return self::$pdo->commit();
    }
    
    public function rollBack() {
        return self::$pdo->rollBack();
    }
	public function table_exists($table_name) {
		$table_name = str_replace(["'", '"', '`', ';'], '', $table_name);
		$stmt = $this->query("SHOW TABLES LIKE '$table_name'");
		return ($stmt && $this->num_rows($stmt) > 0);
	}    
}

?>
