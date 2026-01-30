<?
include "/var/www/vlav/data/www/wwl/inc/bs.class.php";
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "/var/www/vlav/data/www/wwl/inc/pact.class.php";
include "/var/www/vlav/data/www/wwl/inc/simple_db.inc.php";
include "init.inc.php";

$db=new top($database,0,false,$favicon);
$bs=new bs;
print "<h3>Профиль</h3>";
$user_id=intval($_SESSION['userid_sess']);

if(!$klid=$db->dlookup("klid","users","id=$user_id")) {
	print "<div class='alert alert-danger' >Ошибка: $user_id</div>";
	exit;
}

print "<div class='well' ><h4 >Ваша партнерская ссылка на лэндинг: <b>https://formula12.ru/?bc=$klid</b></h4></div>";
if(sizeof($_GET)==0 && sizeof($_POST)==0)
	$_GET['view']="yes";
if(isset($_GET['id'])) {
	if(intval($_GET['id']) != $user_id) {
		print "<div class='alert alert-danger' >Ошибка - {$_GET['id']}</div>";
		exit;
	}
}

function pact_chk($token) {
//	return true;
	
	global $database;
	$db=new db($database);
	if(empty($token))
		return false;
	$p=new pact;
	$p->token=$token;
	$cid=$p->get_company_id();
	$channel_id=$p->get_channel_id();
	//print "HERE_$channel_id $cid<br>";
	if($cid) {
		if($db->dlookup("pact_company_id","users","id={$_SESSION['userid_sess']}")!=$cid) {
			$db->query("UPDATE users SET pact_company_id='$cid' WHERE id={$_SESSION['userid_sess']}");
		}
		if($channel_id!=$db->dlookup("pact_channel_id","users","id={$_SESSION['userid_sess']}")) {
			$db->query("UPDATE users SET pact_channel_id='$channel_id' WHERE id={$_SESSION['userid_sess']}");
		}
		return $cid;
	}
	return false;
}

if(isset($_GET['tg_reconnect'])) {
	$db->query("UPDATE users SET telegram_id=0 WHERE id='$user_id'");
	print "<div class='alert alert-info' >Подключите чат бот телеграм по инструкциям ниже</div>";
	$_GET['view']='yes';
}

class db1 extends simple_db {
	function view() {
		global $database;
		$r=$this->fetch_assoc($this->query($this->view_query,0));
		$dt_style="style='font-weight:normal;'";
		$dd_style="style='margin-left:40px;'";

		$code=intval(rand(1000,999999)); $n=100;
		while($this->dlookup("code","telegram","code='$code'")) {
			$code=intval(rand(1000,999999));
			if(!$n--)
				exit;
		}
		$db=new db("vktrade");
		if(!isset($customer_id))
			$customer_id=intval($db->dlookup("id","customers","db='$database'"));
		if(!$customer_id) {
			print "<div alert alert-warning>Error. Ask customers service</div>";
			exit;
		}
		$tm=time();
		if(!$code_saved=$db->dlookup("code","telegram","user_id='{$_SESSION['userid_sess']}' AND customer_id='$customer_id' ",0)) {
			$db->query("INSERT INTO telegram SET code='$code', user_id='{$_SESSION['userid_sess']}',customer_id='$customer_id',tm='$tm',confirmed='0'");
		} else {
			$db->query("UPDATE telegram SET tm='$tm',confirmed='0' WHERE user_id='{$_SESSION['userid_sess']}' AND customer_id='$customer_id'");
			$code=$code_saved;
		}

		$tg_notif=(!$r['telegram_id'])?"<span class='alert alert-danger' >Не подключено</span><div class='well small' >
		Подключение телеграм бота для уведомлений: <br>
1. У вас должен быть запущен в телеграм бот: <a href='http://t.me/formula12_bot' class='' target='_blank'>formula12_bot</a>,
если он не установлен - перейдите по ссылке и запустите бот.
(Либо найдите его в телеграм и запустите.)<br>
2. Отправьте боту сообщение с кодом: <b>$code</b>. Вы получите сообщение от бота об успешном подключении. <br>
3. После этого вы начнете получать в телеграм уведомления о новых событиях
4. Закройте это окно и зайдите снова в CRM Настройки-Профиль.
</div>":"
<span class='label label-success' >OK</span>
<a href='?tg_reconnect=yes' class='btn btn-sm btn-info' target=''>переподключить чат бот</a>";
		$is_token_ok=(pact_chk($r['pact_token']))?"<span class='label label-success' >OK</span>":"<span class='alert alert-danger' >Токен неправильный либо нет подключения к Пакт</span>";
		$pact_token=(!empty($r['pact_token']))?"$is_token_ok".substr($r['pact_token'],0,12)."...":"Не указан";
		$garant=($r['garant'])?"<span class='label label-success' >ДА</span>":"<span class='label label-danger' >Нет</span>";
		$wa_user_name=(!empty($r['wa_user_name']))?$r['wa_user_name']:"<span class='label label-danger' >УКАЖИТЕ ИМЯ В ВОТСАП!</span>";
		$leadgen=($r['leadgen'])?"<span class='label label-success' >ДА</span>":"<span class='label label-info' >Нет</span>";
		$uid_md5=$this->dlookup("uid_md5","cards","id='{$r['klid']}'",0);
		$wa_phone=(is_numeric($r['pact_phone']) )?"<span class='label label-success' >{$r['pact_phone']}</span>":"<span class='label label-danger' >Не указан телефон вотсап (рабочий тел)</span>";
		print "<div class='well' >
			<dl class='dl-horizontal_' style='padding:5px; font-size:20px; color:#555;' >
			<dt $dt_style >User</dt>
				<dd $dd_style><b>{$r['username']}</b></dd>
			<dt $dt_style>ФИО</dt>
				<dd $dd_style><b>{$r['real_user_name']}</b></dd>
			<dt $dt_style>Ник в телеграме</dt>
				<dd $dd_style><b>{$r['tg_nick']}</b></dd>
			<dt $dt_style>Имя в рабочем whatsapp</dt>
				<dd $dd_style><b>$wa_user_name</b></dd>
			<dt $dt_style>Номер whatsapp</dt>
				<dd $dd_style><b>$wa_phone</b></dd>
			<dt $dt_style>Токен ПАКТ</dt>
				<dd $dd_style>
					<b>$pact_token</b>
					<p class='mar10' ><a href='https://app.pact.im/signup?ref=77810e179b951b83bd7a861f3fe67659' class='' target='_blank'>Ссылка для подключения ПАКТ</a></p>
					<p class='mar10' >Webhook URL <span class='label label-warning pad10' >https://formula12.ru/scripts/pact/webhook.php</span></p>
					<br>
				</dd>
			<!--<dt $dt_style>Партнерский код геткурса</dt>
				<dd $dd_style><b>{$r['gk_code']}</b></dd>-->
			<!--<dt $dt_style>Номер пикселя FB</dt>
				<dd $dd_style><b>{$r['fb_pixel']}</b></dd>-->
			<dt $dt_style>Телеграм уведомления</dt>
				<dd $dd_style>$tg_notif</dd>
			<!--<dt $dt_style>Показывать секцию с гарантиями</dt>
				<dd $dd_style>$garant</dd>-->
			<!--<dt $dt_style>Продавать лиды</dt>
				<dd $dd_style>$leadgen</dd>-->
			<!--<dt $dt_style>Отзыв</dt>
				<dd $dd_style><a href='https://formula12.ru/references/add.php?uid=$uid_md5' class='' target='_blank'>Заполнить отзыв</a></dd>
			</dl>-->
			</div>";
		print "<div><a class='btn btn-primary' href='?edit=yes&id={$_SESSION['userid_sess']}' class='' target=''>Редактировать</a>
			<a class='btn btn-success' href='pact.php' class='' target=''>Инструкция как подключить вотсап</a>
			</div>";
	}
	function prepare_fld($key,$val,$style,$type) {
		if($type=='text') {
			if($key=="passw")
				$val="";
			return "<input class='edit_".$key."' type='text' name='".$key."' value='".$val."' $style>";
		}
	}
 	function conv($key,$val) {
 		if($key=="passw") {
 			if(strlen(trim($val))==0) {
 				if(!$this->error) {
					$r=$this->fetch_assoc($this->query("SELECT passw FROM users WHERE id=$this->id"));
					$val=$r['passw'];
					if(trim($val)=="") {
						$this->error=true;
						$this->error_mess="Error: Can't to be empty <b>".$fld->label."</b>";
					}
 				} else
 					$val="";
 				//print "HERE_$val";
 				//exit;
 			} else
 				$val=md5($val);
 		}
 		if($key=="pact_phone") {
			$db=new db;
			$val=$db->check_mob($val);
		}
 		if($key=="tg_nick") {
			$val=preg_replace("/\@/","",$val);
		}
 		//print "HERE_$key $val<br>";
 		return $val;
 	}
}

$db1=new db1;
$db1->charset="utf8mb4";
$db1->connect( mysql_user, mysql_passw,$database);
//$db1->connect ("vlav", "fokova#142586","fishing");
$db1->init_table("users");
$db1->view_query="SELECT * FROM users WHERE id='{$_SESSION['userid_sess']}' ORDER BY username DESC";
	//function add_field($label,$key,$val,$type,$w)
	//var $chk=""; //validate fields - non_empty,unicum,date,time
//$fld=$db1->add_field("Login:","username","","text",400); $fld->chk="unicum";
//$fld=$db1->add_field("Пароль:","passw","","text",400); //$fld->chk="non_empty";
$fld=$db1->add_field("ФИО (полностью):","real_user_name","","text",400);
$fld=$db1->add_field("НИК В ТЕЛЕГРАМЕ (без @):","tg_nick","","text",400);
$fld=$db1->add_field("ИМЯ В РАБОЧЕМ WHATSAPP:","wa_user_name","","text",400);
//$fld=$db1->add_field("email_from_name:","email_from_name","","text",400);
//$fld=$db1->add_field("email:","email","","text",400);
$fld=$db1->add_field("НОМЕР ТЛФ WHATSAPP:","pact_phone","","text",400);
$fld=$db1->add_field("ПАКТ ТОКЕН:","pact_token","","text",400);
//$fld=$db1->add_field("ГЕТКУРС код партнера:","gk_code","","text",400);
//$fld=$db1->add_field("НОМЕР ПИКСЕЛЯ FB:","fb_pixel","","text",400);
//$fld=$db1->add_field("Показывать секцию с гарантиями:","garant","","checkbox",400);
//$fld=$db1->add_field("Продавать лиды:","leadgen","","checkbox",400);


//$fld=$db1->add_field("uid vk:","uid","","text",400);
//$fld=$db1->add_field("Аккаунт (если привязан):","acc_id",0,"text",400); $fld->chk="numeric";
//$fld=$db1->add_field("Доступ (1-5):","access_level",3,"text",400); $fld->chk="numeric";
//$fld=$db1->add_field("token:","token","","text",400);
//$fld=$db1->add_field("telegram_id:","telegram_id",0,"text",400); $fld->chk="numeric";
//$fld=$db1->add_field("sip:","sip",0,"text",400); $fld->chk="numeric";
//$fld=$db1->add_field("callback_url:","callback_url",0,"text",400); 
//$fld=$db1->add_field("Уведомления 1:","fl_notify_if_new","","checkbox",400);
//$fld=$db1->add_field("Уведомления 2:","fl_notify_if_other","","checkbox",400);
//$fld=$db1->add_field("Уведомлять только о своей переписке:","fl_notify_of_own_only",0,"checkbox",400);
//$fld=$db1->add_field("Вход разрешен:","fl_allowlogin","","checkbox",400);
//$fld=$db1->add_field("Комментарий:","comm","","textarea",400);
$db1->run();
$db->bottom();
?>
