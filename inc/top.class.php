<?php 
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/users_log.class.php";
//include_once "/var/www/vlav/data/www/wwl/inc/msg.class.php";
class top extends db {
	var $userdata,$disp_menu,$gid,$admin_uid;
	var $title="CRM";
	var $favicon="images/favicon.png";
	var $domen="https://winwinland.ru";
	var $prices="https://for16.ru/prices.php";
	var $db200='.';
	
	function __construct($db=false,$title=false,$disp_menu=true, $favicon=false, $ask_passw=true,$gid=false,$admin_uid=false) {
			//~ print "технические работы";
			//~ exit;

		$this->domen="https://".$_SERVER['SERVER_NAME'];
		if(isset($_SESSION['DB200']))
			$this->db200=$_SESSION['DB200'];
		$this->test_microtime(__LINE__);
		$this->disp_menu=$disp_menu;
		$this->gid=$gid;
		$this->admin_uid=$admin_uid;
		if($title && !intval($title) && $title!="640px;") {
			$this->title=$title;
		}
		if($favicon)
			$this->favicon=$favicon; else $this->favicon="https://for16.ru/images/favicon.png";
		if($db) {
			$this->top_run($db,$ask_passw);
		}
		$this->test_microtime(__LINE__);
	
	}
	function nota() {
		//print "<div class='alert alert-warning' >Объявление: ВК сегодня активно что то меняет в своих программах, поэтому возможны сбои и ошибки в отображении информации из ВК. Просьба отнестись к их экспериментам с пониманием.</div>";
	}
	function top_run($db,$ask_passw=true) {
		$this->connect($db);

		//$this->notify_me("HERE_".$_SESSION['u_logged']);
		if(	basename($_SERVER['SCRIPT_NAME'])!='cabinet.php' && 
			basename($_SERVER['SCRIPT_NAME'])!='cab_test.php' && 
			basename($_SERVER['SCRIPT_NAME'])!='cabinet2.php' && 
			strpos($_SERVER['SCRIPT_NAME'],'cashier')===false
			) {
			if(isset($_SESSION['u_logged'])) {
				session_destroy();
				print "<script>location.reload();</script>";
			}
		}

		$this->disp_header();
		$this->group_info($this->gid,$this->admin_uid);
		if($ask_passw) {
			$this->login();
		} else {
			$_SESSION['userid_sess']=0;
			$_SESSION['passwd_md5_sess']="";
			$_SESSION['username']="guest";
			$_SESSION['real_user_name']="guest";
			$_SESSION['access_level']=5;
			$_SESSION['user_acc_id']=0;
			$this->userdata=array('user_id'=>0,'username'=>'guest','access_level'=>5);
		session_destroy();
		}
		$this->nota();
	$this->test_microtime(__LINE__);
		$this->menu();
		
		$access_level=$_SESSION['access_level'];
		//$access_level=4;
		$page=basename($_SERVER['SCRIPT_NAME']);
		$page_level=$this->get_access_level_by_page($page);
		if($access_level > $page_level && $page_level) {
			$this->notify_me("get_access_level_by_page db=$this->database $page ".$_SESSION['access_level']." blocked");
			print "<p class='alert alert-info' >Извините, у вас нет доступа к этой странице. Возможно права доступа автоматически изменились, если вы заходили в личный кабинет или в приложение кассира. В этом случае нажмите <a href='?logout=yes' class='btn btn-info btn-sm' target=''>-Выйти-</a> </p>";
			$this->bottom();
			exit;
		}
	}
	function check_payment($gid=false) {
		return true;
		global $database;
		$db=new db("vktrade");
		$r=$db->fetch_assoc($db->query("SELECT * FROM customers WHERE gid='$gid'"));
		$db->connect($database);
		if($r) {
			if(!$r['tm_expire'])
				return true;
			if($db->dt2($r['tm_expire'])<time()) {
				//uds game check
				$payed_by_uds=false;
				if(!empty($r['uds_api_key'])) {
					$tm0=$db->dt1(time());
					$tm_expire=$r['tm_expire']+(365*24*60*60);
					if($tm_expire<time())
						return false;
					if( $tm0 != $r['uds_last_check_tm']) {
						include_once ("/var/www/vlav/data/www/wwl/inc/uds_game.class.php");
						$uds=new uds_game($r['uds_api_key']);
						$res=$uds->get_company_info();
						//print($r['uds_api_key']);
						if($res) {
							$db->connect("vktrade");
							$db->query("UPDATE customers SET uds_last_check_tm='$tm0' WHERE id='{$r['id']}'",0);
							$db->connect($database);
							$payed_by_uds=$tm_expire;
						} else
							$payed_by_uds=false;	
					} else
						$payed_by_uds=$tm_expire;
				}
				if($payed_by_uds) {
					return "<span class='badge badge-warning' >Оплачено подключением к uds game до ".date("d.m.Y",$payed_by_uds)."</span>";
				} else
					return false;
			}
			$dt=date("d.m.Y",$r['tm_expire']);
			if( ($r['tm_expire']-time()) < (3*24*60*60)  ) 
				$l_style="badge-danger"; else $l_style="badge-success";
			return "<span class='badge $l_style' >Оплачено до <b>$dt</b> <span class='badge badge-primary' ><a href='payment.php' class='white' target=''>оплатить</a></span></span>";
		} else {
			return false;
		}
	}
	function group_info($gid,$uid) {
		return false;
		global $no_disp_group_info_in_top;
		if(!$gid) 
			return;
		//$vk=new vklist_api;
		//$info=$vk->vk_group_getinfo($gid);
		//~ if($uid)
			//~ $uid_name=$vk->vk_get_name_by_uid($uid); else $uid_name="";
		//$this->print_r($info);
		if($gid==158439126) {
			//print $this->check_payment($gid);
		}
		$check_payment=$this->check_payment($gid);
		$check_payment=true;
		//$this->here($info);
		if(!$no_disp_group_info_in_top)
		print "<div class='alert alert-info' > 
				<a href='https://vk.com/vktrade200' class='' target='_blank'><img src='https://for16.ru/images/favicon.png' class=''></a>
				&nbsp;
				<span class='badge badge-primary' >{$info['screen_name']}</span> 
				<span class='badge badge-primary' >{$info['name']}</span> 
				<span class='badge badge-info' >{$info['city']}</span> 
				<span class='badge badge-info' >$uid_name</span>
				".$check_payment."
				
			</div>";
			
		if(!$check_payment) {
			print "<div class='alert alert-danger' >
					Оплаченный период закончился. Необходимо внести абонентскую плату.
					<a href='payment.php' class='' target=''>оплатить</a></span>
					</div>";
			print "<div class='alert alert-success' >Если вы подключены к uds game, то необходимо продлить подписку, вктрейд на срок акции включится автоматически! </div>";
			if(strpos($_SERVER['SCRIPT_NAME'],"payment.php")===false)
					exit;
		}
	}
	function disp_header($w="",$css=false) {
		global $css;
		?>
		<!DOCTYPE html>
		<html  lang="ru">
		<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title><?=$this->title?></title>
		<link href="<?=$this->favicon?>" rel="icon">
		<style type='text/css'>
			div {border-collapse:collapse;border-width:0px;border-style:solid;border-color:#555;
				margin:5px 0; padding:3px;}
			div.tbl_agent {border-width:1px;}
			span.agent_vacant {border-collapse:collapse;border-width:1px;border-style:solid;border-color:#555; padding:1px; margin:1px; color:green;}
			span.agent_busy {border-collapse:collapse;border-width:1px;border-style:solid;border-color:#555; padding:1px; margin:1px;  color:red;}
			span.num_visited_shop_0 { background-color:#555; width:10px; color:white; padding:2px;}
			span.num_visited_shop_0 a {color:white;}
			span.num_visited_shop_1 { background-color:#555; width:10px; color:yellow; padding:2px;}
			span.num_visited_shop_1 a {color:yellow;}
			.disp_form TD {padding:3px;}
			.color-box {width: 20px; height: 20px; cursor: pointer; border-radius: 10px; padding: 10px}
			.tag-option {align-items: center; white-space: nowrap; background-color: #000; color: #fff; display: inline-flex; padding: 0.25rem 0.5rem; border-radius: 10px; margin: 0.25rem;}
			/* .dropdown-menu {width: auto; min-width: 10rem; max-width: 20rem; display: flex; flex-wrap: wrap; padding: 5px; border: 1px solid #ced4da; border-radius: .25rem;} */
			.tag-container {display: flex; align-items: center; }
			#color-dropdown {position: relative; display: grid; grid-template-columns: repeat(8, 1fr); gap: 1px;}
			#edit-color-dropdown {position: relative; display: grid; grid-template-columns: repeat(8, 1fr); gap: 1px;}
			#existing-tags div {color: #fff;}
		</style>

<!--
		<script src="https://for16.ru/css/calendar_ru.js" type="text/javascript"></script>
-->
		<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="//cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="//cdn.jsdelivr.net/npm/bootstrap-timepicker@0.5.2/css/bootstrap-timepicker.min.css" rel="stylesheet" />
<script src="//code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/bootstrap-timepicker@0.5.2/js/bootstrap-timepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!--
		<link rel="stylesheet" href="/css/bootstrap-3.3.7/css/bootstrap.min.css">
		<script src="/css/bootstrap-3.3.7/js/jquery.min.js"></script>
		<script src="/css/bootstrap-3.3.7/js/bootstrap.min.js"></script>		
-->

		<!-- DatePicker -->
		<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.standalone.min.css" rel="stylesheet"/>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
		<style type="text/css">
			.datepicker td, .datepicker th {
				width: 2.5em;
				height: 1.5em;
			}
			.datepicker.dropdown-menu {
				font-size:14px;
			}
	   </style>


		<link rel='StyleSheet' href='https://for16.ru/css/vkt_db.css'>
		<link rel='StyleSheet' href='https://for16.ru/css/buttons.css'>
		<link rel='StyleSheet' href='https://for16.ru/fonts/fonts.css'>
		
		<?
		if($css)
			print "$css\n";
		?>
		<SCRIPT>
		function ins_text(text,obj,cr="\n") {
			obj.focus();
			obj.value=""+obj.value.substr(0,obj.selectionStart)+text+""+obj.value.substr(obj.selectionStart,obj.value.length-obj.selectionStart)+cr;
			obj.focus();
			pos=obj.value.length; //obj.selectionStart;
			obj.setSelectionRange(pos,pos);
		}
		function block(id) {
			//alert(id);
			if (document.getElementById(id).style.display == "none") {
				document.getElementById(id).style.display = "block";
			} else {
				document.getElementById(id).style.display = "none";
			}
		}
		function setAllCheckboxes(divId, sourceCheckbox) {
			divElement = document.getElementById(divId);
			inputElements = divElement.getElementsByTagName('input');
			for (i = 0; i < inputElements.length; i++) {
				if (inputElements[i].type != 'checkbox')
				continue;
				inputElements[i].checked = sourceCheckbox.checked;
			}
		}
		function wopen(url) {
			w1=window.open(url, "w1", "left=200,top=100,width=800,height=900");
		}
		function wopen_s(url) {
			w1=window.open(url, "w1", "left=300,top=200,width=600,height=600");
		}
		function wopen_1(url) {
			w1=window.open(url, "w2", "left=300,top=120,width=800,height=800");
		}
		</SCRIPT>
		
		</head>
		<body>
		<div class="container-fluid">
		<?
		if($this->is_localhost())
			print "<h1 style='background-color:yellow; padding:5px;'>LOCALHOST. OLD INFORMATION. DO NO CHANGES HERE </h1>";
	}
	function login_form() {
		//$p1=(isset($_GET['uid']))?"uid={$_GET['uid']}":"";
		//print_r($GLOBALS);
		if(strpos(basename($_SERVER['PHP_SELF']),"cab")===0)
			return false;
		print "	<form method='POST' action='?'>
			<div class='form-group'>
				<badge for='username'>User:</badge>
				<input type='text' class='form-control' id='username' name='username' value=''>
			</div>
			<div class='form-group'>
				<badge for='pwd'>Password:</badge>
				<input type='password' class='form-control' id='pwd' name='passw'>
			</div>
			<button type='submit' class='btn btn-primary' name='do_login' value='yes'>Ok</button>
			<input type='hidden' name='uri' value='{$_SERVER['REQUEST_URI']}'>
			<input type='hidden' name='csrf_token' value='{$_SESSION['csrf_token']}'>
			<input type='hidden' name='csrf_name_' value='login'>
			</form>";
		sleep(1);
		exit;
		return false;
	}
	function login() {
		global $database,$ctrl_id;
		if(isset($_GET['logout'])) {
			unset($_SESSION['userid_sess']);
			session_destroy();
		}

		if(isset($_SESSION['userid_sess']) && $_SESSION['userid_sess'] ) {
			if(isset($_SESSION['username']) && isset($_SESSION['access_level']) ) {
				$this->userdata=array('user_id'=>$_SESSION['userid_sess'],
					'username'=>$_SESSION['username'],
					'access_level'=>$_SESSION['access_level'],
					'acc_id'=>$_SESSION['user_acc_id']);
				return true;
			}
		}

		if ($this->fetch_assoc($this->query("SHOW COLUMNS FROM users LIKE 'chk_nalog'"))) {
			$this->query("ALTER TABLE users CHANGE COLUMN chk_nalog tm_locked INT NOT NULL DEFAULT 0");
			$this->query("ALTER TABLE users ADD INDEX idx_tm_locked (tm_locked)");
		}
		
		$l=new users_log($database);

		if(isset($_GET['u'])) {
			if((
				basename($_SERVER['SCRIPT_NAME'])!='cabinet.php' && 
				basename($_SERVER['SCRIPT_NAME'])!='cab_test.php' && 
				basename($_SERVER['SCRIPT_NAME'])!='cabinet2.php' && 
				strpos($_SERVER['SCRIPT_NAME'],'cashier')===false
				)) {
				if($this->database=='vkt') {
					print "access denied for you ";
					exit;
				}
			} else {
				$_SESSION['u_logged']=time();
			}
			//print "ведутся технические работы до 15:00"; exit;
			if($this->is_md5($_GET['u'])) {
				$u=$this->dlookup("username","users","del=0 AND direct_code='{$_GET['u']}'",0);
				if(!$user_id=$this->dlookup("id","users","del=0 AND username='$u' AND tm_locked<'".time()."'")) {
					//~ session_destroy();
					//~ print "account locked";
					//~ return false;
				}
				$md5=$this->dlookup("passw","users","del=0 AND direct_code='{$_GET['u']}'",0);
				$r=$this->fetch_assoc($this->query("SELECT * FROM users WHERE del=0 AND username='".$this->escape($u)."' AND passw='$md5'"));
				if($r) {
					//$this->notify_me("HERE_".basename($_SERVER['SCRIPT_NAME']));
					$u_allowed=['cashier.php','cashier_setup.php'];
					if($r['fl_allowlogin']==1 || strpos($_SERVER['SCRIPT_NAME'],"/lk/")!==false || in_array(basename($_SERVER['SCRIPT_NAME']),$u_allowed)) {
						$_SESSION['userid_sess']=$r['id'];
						//$_SESSION['passwd_md5_sess']=$md5;
						$_SESSION['username']=$r['username'];
						$_SESSION['real_user_name']=$r['real_user_name'];
						$_SESSION['access_level']=$r['access_level'];
						$_SESSION['user_acc_id']=$r['acc_id'];
						$token=md5(rand(100000,999999));
						$_SESSION['token']=$token;
						$this->query("UPDATE users SET tm_lastlogin=".time().",token='".$this->escape($token)."' WHERE id={$r['id']}");
						$l->log_attempt($r['username'], $r['id'], true, 'success_direct_code', 'Logged ok by direct_code','direct_code');

						//~ if(strpos($_POST['uri'],"logout")===false)
							//~ print "<script>location='$this->domen".$_POST['uri']."'</script>";
					} else {
						$l->log_attempt($_GET['u'], $r['id'], false, 'fl_login_off', 'fl_login_off when logged by direct_code','direct_code');
						unset($_SESSION['userid_sess']);
						unset($_SESSION['username']);
						unset($_SESSION['access_level']);
						unset($_SESSION['user_acc_id']);
						session_destroy();
						//unset($_SESSION['passwd_md5_sess']);
					}
				} else {
					$l->log_attempt($_GET['u'], null, false, 'wrong_direct_code', 'Incorect direct code','direct_code');
					unset($_SESSION['userid_sess']);
					unset($_SESSION['username']);
					unset($_SESSION['access_level']);
					unset($_SESSION['user_acc_id']);
					session_destroy();
				}
			} else {
				$l->log_attempt($_GET['u'], null, false, 'wrong_direct_code', 'direct_code is not an md5','direct_code');
				unset($_SESSION['userid_sess']);
				unset($_SESSION['username']);
				unset($_SESSION['access_level']);
				unset($_SESSION['user_acc_id']);
				session_destroy();
			}
		}
		if(isset($_POST['do_login'])) {
			$access_level=false;
			//~ if($_POST['username']=='fox_admin' && $_POST['passw']=='cfar7tKSCl^^') {
				//~ $_POST['passw']='fokova#142586';
				//~ $_POST['username']='vlav';
				//~ $access_level=3;
			//~ }
			$md5=md5($_POST['passw']);
			$username=mb_substr($_POST['username'],0,32);
			$r=$this->fetch_assoc($this->query("SELECT * FROM users WHERE del=0 AND username='".$this->escape($username)."' AND passw='$md5'",0));
			if($r) {
				if($r['passw']==md5('admin')) {
					//~ $this->connect('vkt');
					//~ $pas=$this->dlookup("admin_passw","0ctrl","id='$ctrl_id'");
					//~ $this->connect($database);
					//~ $md5=md5($pas);
					//~ $this->query("UPDATE users SET passw='$md5' WHERE id={$r['id']}");
					//~ $r=$this->fetch_assoc($this->query("SELECT * FROM users WHERE del=0 AND username='".$this->escape($username)."' AND passw='$md5'",0));
				}
				if($r['fl_allowlogin']==1) {
					$_SESSION['userid_sess']=$r['id'];
					//$_SESSION['passwd_md5_sess']=$md5;
					$_SESSION['username']=$r['username'];
					$_SESSION['real_user_name']=$r['real_user_name'];
					$_SESSION['access_level']=(!$access_level)?$r['access_level']:$access_level;
					$_SESSION['user_acc_id']=$r['acc_id'];
					$token=md5(rand(100000,999999));
					$_SESSION['token']=$token;
					$this->query("UPDATE users SET tm_lastlogin=".time().",token='".$this->escape($token)."' WHERE id={$r['id']}");
					$l->log_attempt($r['username'], $r['id'], true, 'success', 'Logged ok');
					if(strpos($_POST['uri'],"logout")===false)
						print "<script>location='$this->domen".$_POST['uri']."'</script>";
				} else {
					$l->log_attempt($username, $r['id'], false, 'fl_login_off', 'fl_login to CRM turned off');
					unset($_SESSION['userid_sess']);
					unset($_SESSION['username']);
					unset($_SESSION['access_level']);
					unset($_SESSION['user_acc_id']);
					session_destroy();
					//unset($_SESSION['passwd_md5_sess']);
				}
			} else {
				if($user_id=$this->dlookup("id","users","del=0 AND username='$username'"))
					$l->log_attempt($username, $user_id, false, 'wrong_password', 'Password verification failed');
				elseif($this->dlookup("id","users","del=0 AND passw='$md5'"))
					$l->log_attempt('nonexistent_user', null, false,  'user_not_found', 'Username does not exist in database');
				unset($_SESSION['userid_sess']);
				unset($_SESSION['username']);
				unset($_SESSION['access_level']);
				unset($_SESSION['user_acc_id']);
				session_destroy();
				//unset($_SESSION['passwd_md5_sess']);
			}
		}
		if(!isset($_SESSION['userid_sess']) ) {
			//print "event";
			session_destroy();
			$this->login_form();
			return false;
		}
		
		$home_ips=array("94.242.24.53");
		$allowed_users=array("vlav"=>"94.242.24.53","ponomareva"=>"194.28.141.44");
		if(array_key_exists($_SESSION['username'],$allowed_users) && @$allowed_users[$_SESSION['username']]==$this->get_ip()) {
			
		} else {
			$token=$this->dlookup("token","users","id='".intval($_SESSION['userid_sess'])."'");
			if($_SESSION['token']!=$token) {
				session_destroy();
				$this->login_form();
				return false;
			}
		}
		$this->userdata=array('user_id'=>$_SESSION['userid_sess'],'username'=>$_SESSION['username'],'access_level'=>$_SESSION['access_level'],'acc_id'=>$_SESSION['user_acc_id']);
		return true;
	}
	function menu_add() {return "";}
	function menu_local_reports() {
		return "";
		$active=(strpos($_SERVER["DOCUMENT_ROOT"],"plan.php")!==false)?"active":"";
		return "<li class='$active'><a href='plan.php'>Отчеты по занятиям</a></li>";
	}
	function menu_reports() {
		global $a6;
		print "<li class='nav-item dropdown $a6'>
					<a class='nav-link dropdown-toggle' data-toggle='dropdown' href='#'>Отчеты</span>
						<span class='caret'></span>
					</a>
					<div class='dropdown-menu'>";

		if($_SESSION['access_level']<=3)
			print "<a class='dropdown-item'  href='$this->db200/lk/report_pay.php'>Начисления партнерам</a>";

		if($_SESSION['access_level']<=3)
			print "<a class='dropdown-item'  href='$this->db200/reports/leads_by_partners.php'>Регистрации по партнерам</a>";

		if($_SESSION['access_level']<=4)
			print "<a class='dropdown-item'  href='$this->db200/reports/managers_report.php?today=yes'>Отчеты по менеджерам</a>";

		if($this->database=='vkt') {
			if($_SESSION['access_level']<=4)
				print "<a class='dropdown-item'  href='$this->db200/reports/calls_report.php?today=yes'>Отчет по звонкам</a>";
			if( $_SESSION['access_level']==1) {
				print "<a class='dropdown-item'  href='$this->db200/reports/sales_reports.php'>Отчеты по продажам</a>";
			}	
		} else
			if($_SESSION['access_level']<=3)
				print "<a class='dropdown-item'  href='$this->db200/reports/sales_reports.php'>Отчеты по продажам</a>";

		if($_SESSION['access_level']<=3)
			print "<a class='dropdown-item'  href='$this->db200/reports/reg_report.php'>Отчеты по регистрациям</a>";

		if($_SESSION['access_level']<=3)
			print "<a class='dropdown-item'  href='$this->db200/avangard_search.php'>Найти платеж</a>";

		if($_SESSION['access_level']<=4)
			print "<a class='dropdown-item'  href='$this->db200/reports/promocodes.php'>Промокоды</a>";

		print "</div>
			</li>
			";
	}
	function menu_setup_add() {}

	function top_info() {}
	function menu() {
		global $msg_pay_info,$ctrl_id, $tg_admin, $tg_bot_notif,$tg_bot_msg,$tg_bot_msg_name;
		$access_level=$this->userdata['access_level'];
		$user_id=$this->userdata['user_id'];
		if($this->disp_menu) {
			if($access_level<=5) {
				$res=$this->query("SELECT * FROM vklist_acc WHERE del=0 AND last_error>0");
				while($r=$this->fetch_assoc($res)) {
					//print "<div class='alert alert-danger'> Аккаунт <b>{$r['id']} {$r['name']}</b> не работает! Код ошибки: {$r['last_error']}</div>";
					
				}
			}
			if(@$_GET['clear_fr_capcha_uid'])
				$this->query("UPDATE vklist_acc SET fr_capcha_uid=0 WHERE id={$_GET['acc_id']}");
			/*	
			$res=$this->query("SELECT * FROM vklist_acc WHERE del=0 AND fr_capcha_uid>0");
			while($r=$this->fetch_assoc($res)) {
				print "<div class='alert alert-danger'> Аккаунт <b>{$r['id']} {$r['name']}</b> 
					требует подтверждения что вы не робот для добавления в друзья. 
					Для этого зайдите в этот промо-аккаунт : {$r['login']} {$r['passw']}
					И из него отправьте запрос в друзья пользователю <a href='https://vk.com/id{$r['fr_capcha_uid']}' target='_blank' >https://vk.com/id{$r['fr_capcha_uid']}</a>.
					когда сделаете перейдите <a href='?clear_fr_capcha_uid=yes&acc_id={$r['id']}'>по этой ссылке</a>.
				</div>";
				
			}
			*/
			$tm=$this->dt1(time());
	$this->test_microtime(__LINE__);
			//$cnt_msgs=$this->num_rows($this->query("SELECT id FROM msgs WHERE (outg=1) AND source_id=0 AND user_id=$user_id AND tm>$tm"));
	$this->test_microtime(__LINE__);
			//$cnt_users=$this->fetch_row($this->query("SELECT COUNT(cnt_uid) FROM (SELECT COUNT(uid) AS cnt_uid FROM msgs WHERE  (outg=1) AND source_id=0 AND user_id=$user_id AND tm>$tm GROUP BY uid) AS q1 WHERE 1",0))[0];
	$this->test_microtime(__LINE__);
	//$this->print_r($this->runtime_log);
			//$cnt_users=($cnt_users)?$cnt_users:0;
			$cnt_msgs="n/a";
			$cnt_users="n/a";
			
			$a1=$a2=$a3=$a4=$a5=$a6=$a7=$a_logout=$a_acc=$a_users="";
			if(strpos($_SERVER["SCRIPT_NAME"],"cp.php")!==false)
				$a1="active";
			if(strpos($_SERVER["SCRIPT_NAME"],"search.php")!==false)
				$a2="active";
			if(strpos($_SERVER["SCRIPT_NAME"],"msgs_templates.php")!==false)
				$a3="active";
			if(strpos($_SERVER["SCRIPT_NAME"],"razdel.php")!==false)
				$a4="active";
			if(strpos($_SERVER["SCRIPT_NAME"],"vkt_send_list.php")!==false)
				$a5="active";
			if(strpos($_SERVER["SCRIPT_NAME"],"reports.php")!==false)
				$a6="active";
			if(strpos($_SERVER["SCRIPT_NAME"],"vklist_acc.php")!==false)
				$a_acc="active";
			if(strpos($_SERVER["SCRIPT_NAME"],"users.php")!==false)
				$a_users="active";
			if(strpos($_SERVER["SCRIPT_NAME"],"sources.php")!==false)
				$a_sources="active";
			if(@$_GET['logout']=="yes")
				$a_logout="active";


			if(isset($_GET['ch_access_4'])) {
				if($_SESSION['access_level']<=3) {
					$_SESSION['access_level']=4;
					print "<script>location='?';</script>";
				}
			}

			//$ch_access_4=($_SESSION['access_level']<=3) ?"<span title='стать менеджером (перелогиниться для входа админом)'><a href='?ch_access_4=yes' class='' target=''><i class='	fa fa-check-circle-o' ></i></a></span>":"";

			//$telegram_icon=($r['telegram_id']>0)?"telegram_1.png":"telegram_2.png";
			$support="<a href='https://t.me/vkt_support_bot?start=ask_support_$ctrl_id' class='' target='_blank'>техподдержка</a>";
			if($_SESSION['access_level']>3 && $ctrl_id!=108)
				$support="";
			//~ if($ctrl_id!=101)
				//~ $support="";
			$user_title="<span class='badge badge-info'>{$_SESSION['username']}</span>";
			if($_SESSION['username']=='vlav')
				$user_title="<span class='badge badge-info'>support ($ctrl_id-$access_level)</span>";

			if(1) { //$this->database=='vkt1_119') {
				if($user_id==3) {
					if($tg_admin && $this->database!='vkt') {
						if($tg_admin != $this->dlookup("telegram_id","users","id=3")) {
							$this->query("UPDATE users SET telegram_id='$tg_admin' WHERE id=3");
						}
					}
				}
				if(!empty($tg_bot_notif) && (!empty($tg_bot_msg) && !empty($tg_bot_msg_name)) ) {
					if(!$this->dlookup("telegram_id","users","id='$user_id'")) {
						if($klid=$this->get_klid($user_id)) 
							$uid=$this->uid_md5($this->dlookup("uid","cards","id='$klid'"));
						else 
							$uid=md5('admin');
						print "<p class='alert alert-warning' >Служебный бот недоступен - 
						<a href='https://t.me/$tg_bot_msg_name?start=$uid' class='' target='_blank'>подключить</a>
						</p>";
					}
				} elseif(empty($tg_bot_msg) || empty($tg_bot_msg_name)) {
					print "<p class='alert alert-warning' >Бот для переписки не подключен - 
					<a href='https://help.winwinland.ru/docs/nastroyka-chat-bota-telegram-dlya-perepiski/' class='' target='_blank'>подключить</a>
					</p>";
				} else {
					print "<p class='alert alert-warning' >Служебный бот не подключен 
					<a href='https://help.winwinland.ru/docs/sluzhebnyy-tg-bot-dlya-uvedomleniy-iz-crm/' class='' target='_blank'>подключить</a>
					</p>";
				}
			}

			print "<div class=''>
						$user_title
						$ch_access_4
						$support
						<!--<span style='margin-left:10px; color:#555;' > инструкция по работе <a href='https://youtu.be/6Ydbrd3-lA8' class='' target='_blank'>на youtube</a> </span>-->
						<span style='margin-left:10px; color:#555;' > <a href='https://help.winwinland.ru/docs/lichnyy-kabinet/' class='' target='_blank'><i class='fa fa-question-circle' ></i></a> </span>
						&nbsp;&nbsp; $msg_pay_info
					</div> ";


			//~ if(preg_match("/cp\.php/",$_SERVER['SCRIPT_NAME'])) {
				//~ print "<div class='well well-sm' >";
				//~ if($_SESSION['access_level']<=3) {
					//~ $res=$this->query("SELECT * FROM users WHERE del=0 AND access_level>=1 AND fl_allowlogin=1");
					//~ $out="";
					//~ while($r=$this->fetch_assoc($res)) {
						//~ $out.="".$this->users_report_day($r['id'],time(),1);
					//~ }
				//~ } else			
					//~ $out=$this->users_report_day($_SESSION['userid_sess'],time(),3);
				//~ print nl2br($out);
				//~ print "</div>";
			//~ }


			print "<nav class='navbar navbar-expand-lg navbar-light bg-light'>
				  <!--brand-->
				  <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarNav' aria-controls='navbarNav' aria-expanded='false' aria-label='Toggle navigation'>
					<span class='navbar-toggler-icon'>
				  </button>
				<div  class='collapse navbar-collapse' id='navbarNav'>
				<ul class='navbar-nav'>";
			print "<li class='nav-item' title='dashboard'><a class='nav-link'  href='$this->db200/dash.php'><i class='fa fa-bars' ></i></a></li>";
			print "<li class='nav-item $a1'><a class='nav-link $a1'  href='$this->db200/cp.php?view=yes&filter=tasks'>Контрольная панель</a></li>";
			//print "<li class='$a2'><a href='javascript:wopen(\"search.php\")'>Поиск</a></li>";

			if($access_level<=3)
				print "<li class='nav-item $a5'><a class='nav-link $a5' href='$this->db200/vkt_send_list.php'>Рассылка</a></li>";

			if($access_level>0) {
				//print $this->menu_local_reports();
				//print "<li class='$a5'><a href='vklist_send_cp.php'>Рассылка</a></li>";
				$this->menu_reports();
			}

			if($access_level==1) {
				print "<li class='nav-item $a4'><a class='nav-link $a_sources' href='javascript:wopen(\"$this->db200/sources.php\")'>Sources</a></li>";
				print "<li class='nav-item $a_acc'><a class='nav-link $a_acc' href='$this->db200/vklist_acc.php'>Аккаунты</a></li>";
				print "<li class='nav-item $a_users'><a class='nav-link $a_users' href='$this->db200/users.php?view=yes'>Users</a></li>";
			}
			print 	$this->menu_add();

			print "<li class='nav-item dropdown'>
						<a class='nav-link dropdown-toggle' data-toggle='dropdown' href='#'>Настройки</span>
							<span class='caret'></span>
						</a>
						<div class='dropdown-menu'>";
		//	if($access_level==1)	print "				<li><a href='javascript:wopen(\"ad_retargeting.php\")'>Ретаргетинг</a></li>";
		//	if($access_level==1)	print "				<li><a href='javascript:wopen(\"serv_votes.php\")'>Сканирование опросов</a></li>";
									//print "				<li><a href='javascript:wopen(\"users.php?telegram_register=yes\")'>Подключение телеграм бота</a></li>";
			if($access_level<=3) {
				print "<a class='dropdown-item' href='$this->db200/s_panel.php?view=yes' target='_blank'>Профиль</a>";
				print "<a class='dropdown-item' href='$this->db200/s_panel_lands.php' target='_blank'>Лэндинги</a>";
				print "<a class='dropdown-item' href='$this->db200/products.php' target='_blank'>Продукты</a>";
				print "<a class='dropdown-item' href='javascript:wopen(\"$this->db200/razdel.php\")'>Этапы</a>";
				print "<a class='dropdown-item' href='javascript:wopen(\"$this->db200/tags.php\")'>Тэги</a>";
				print "<a class='dropdown-item' href='javascript:wopen(\"$this->db200/msgs_templates.php\")'>Шаблоны</a>";
			}
			print "<a class='dropdown-item' href='javascript:wopen(\"$this->db200/s_panel_notif.php\")'>Уведомления</a>";
			if(1) { //$this->database=='vkt' && $_SESSION['userid_sess']==1) {
				print "<a class='dropdown-item' href='#'  data-toggle='modal' data-target='#passwordResetModal'>Изменить пароль</a>";
			}
			if($_SESSION['access_level'] <=3) {
				if(file_exists('cashier_setup.php'))
					print "<a class='dropdown-item' href='cashier_setup.php' target='_blank'>Лояльность 2.0</a>";
			}
			$this->menu_setup_add();
			print "			</div>
				</li>";
			
			print "<li class='nav-item $a_logout'><a class='nav-link' href='?logout=yes'>Выйти</a></li>";
			print "</ul>
			</div>
			</nav>
			";
		}
	}
	function get_access_level_by_page($page,$l=1) {
		if($pages=file("/var/www/vlav/data/www/wwl/inc/pages_access_levels.txt")) {
			foreach($pages AS $p) {
				list($pg,$l1,$l2)=explode(" ",trim($p));
				if(trim($pg)==trim($page)) {
					if($l==1)
						return intval($l1);
					elseif($l==2)
						return intval($l2);
					else
						return 1;
				}
			}
		}
		return 1;
	}
	function bottom() {
		?>
		</div>
		<?
			$pages_support=['s_panel.php',
				's_panel_lands.php',
				'products.php',
				'msgs_templates.php',
				'sales_script_names.php',
				'sales_script_items.php',
				'vkt_send_list.php',
				'report_pay.php',
				'billing_pay.php',
				];
			$pages_support=[];
			if(in_array(basename($_SERVER['SCRIPT_NAME']),$pages_support)) {
		?>
		<link rel="stylesheet" href="https://cdn.envybox.io/widget/cbk.css">
		<script type="text/javascript" src="https://cdn.envybox.io/widget/cbk.js?wcb_code=54eb8c34f04f7b7e317f1b6a9d396f4e" charset="UTF-8" async></script>
		<?}?>

    <!-- Modal -->
    <div class="modal fade" id="passwordResetModal" tabindex="-1" role="dialog" aria-labelledby="passwordResetModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordResetModalLabel">Сброс пароля</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h3 class='pb-5' >Куда отправить новый пароль?</h3>
                    <div  class="d-flex flex-column">
                    <?
                    $user_id=intval($_SESSION['userid_sess']);
                    if($user_id >3) {
						$klid=$this->get_klid($user_id);
						$email=$this->dlookup("email","cards","id='$klid'");
						$tg=$this->dlookup("telegram_id","users","klid='$klid'");
					} elseif($user_id==3) { //admin
						include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
						$tmp=$this->database;
						$vkt=new vkt('vkt');
						$ctrl_id=$vkt->get_ctrl_id_by_db($tmp);
						$admin_uid=$vkt->dlookup("uid","0ctrl","id='$ctrl_id'");
						$email=$vkt->dlookup("email","cards","uid='$admin_uid'");
						$tg=$vkt->dlookup("telegram_id","cards","uid='$admin_uid'");
						//$this->notify_me("HERE_ $email $tg ".$ctrl_id);
						$this->connect($tmp);
					}
                    $err=true;

                    if($this->validate_email($email)) {
						$err=false;
						list($user, $domain) = explode('@', $email);
						$length = strlen($user);
						if ($length > 2) {
							$obfuscatedUser = substr($user, 0, 2) . str_repeat('.', $length - 2);
						} else {
							$obfuscatedUser = $user;
						}
						$email_= $obfuscatedUser . '@' . $domain;
                    ?>
					<p id="ch_passw_info_email" style="display: none;" class='alert alert-info' ></p>
                    <div id='ch_passw_email_' class='card p-3 m-2' >
						<p class='text-center' ><?=$email_?></p>
						<button type="button" class="btn btn-primary w-100 my-2" id="ch_passw_email">Email</button>
					</div>
                    
                    <? } ?>

                    <?if($tg) {
						$err=false;
					?>
					<p id="ch_passw_info_tg" style="display: none;" class='alert alert-info' ></p>
                    <div id='ch_passw_tg_' class='card p-3 m-2' ><button type="button" class="btn btn-info w-100 my-2" id="ch_passw_tg">Telegram</button></div>
                    <? } ?>
                    <?
                    if($err)
						print "<p class='alert alert-warning' >Не найден емэйл или телеграм, чтобы отправить пароль. Свяжитесь с администратором</p>";
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

		
		</body>

    <script>
        $(document).ready(function () {
            // Handle Email button click
            $('#ch_passw_email').click(function () {
                $.ajax({
                    url: 'jquery.php',
                    type: 'POST',
                    data: {
						ch_passw: 'yes',
                        action: 'ch_passw_email',
                        user_id: '<?= $_SESSION['userid_sess'] ?>'
                    },
                    success: function (response) {
                        $('#ch_passw_info_email').show();
                        $('#ch_passw_info_email').html(response); // Assuming the response contains a success message
						$('#ch_passw_email_').hide();
						$('#ch_passw_tg_').hide();
                    },
                    error: function () {
                        alert('Error sending email. Please try again.');
                    }
                });
            });

            // Handle Telegram button click
            $('#ch_passw_tg').click(function () {
                $.ajax({
                    url: 'jquery.php',
                    type: 'POST',
                    data: {
 						ch_passw: 'yes',
                        action: 'ch_passw_tg',
                        user_id: '<?= $_SESSION['userid_sess'] ?>'
                    },
                    success: function (response) {
                        $('#ch_passw_info_tg').show();
                        $('#ch_passw_info_tg').html(response); // Assuming the response contains a success message
						$('#ch_passw_email_').hide();
						$('#ch_passw_tg_').hide();
                    },
                    error: function () {
                        alert('Error sending to Telegram. Please try again.');
                    }
                });
            });
        });
    </script>


			<script>
			$('#datepicker').datepicker({
				weekStart: 1,
				daysOfWeekHighlighted: "6,0",
				monthNames: ['Январь', 'Февраль', 'Март', 'Апрель','Май', 'Июнь', 'Июль', 'Август', 'Сентябрь','Октябрь', 'Ноябрь', 'Декабрь'],
				dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
				autoclose: true,
				todayHighlight: true,
				format: 'dd.mm.yyyy',
				language: 'ru',
			});
			$('#datepicker').datepicker("setDate", new Date());
			$('#datepicker1').datepicker({
				weekStart: 1,
				daysOfWeekHighlighted: "6,0",
				monthNames: ['Январь', 'Февраль', 'Март', 'Апрель','Май', 'Июнь', 'Июль', 'Август', 'Сентябрь','Октябрь', 'Ноябрь', 'Декабрь'],
				dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
				autoclose: true,
				todayHighlight: true,
				format: 'dd.mm.yyyy',
			});
			$('#datepicker1').datepicker("setDate", new Date());
			</script>



		</html>
		<?
		//exit;
	}
}
?>
