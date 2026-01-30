<?
if(isset($_GET['telegram_register'])) {
	$db=new top(false,0,false,$favicon);
	$db->disp_header($w="",$css=false);
	$db->connect("vktrade");
	if(!isset($customer_id))
		$customer_id=intval($db->dlookup("id","customers","db='$database'"));
	if(!$customer_id) {
		print "<div alert alert-warning>Error. Ask customers service</div>";
		exit;
	}
	print "<h1>Подключение телеграм бота для уведомлений : {$_SESSION['username']}</h1>";
	$code=intval(rand(1000,999999));
	if(!$db->dlookup("code","telegram","code='$code'")) {
		print "<div class='well' >
				<ul>
				<li>У вас должен быть установлен в телеграм бот: <b>$TELEGRAM_BOT</b>, если он не установлен - закройте это окно и установите бот. (Чтобы установить бот, нужно найти его в телеграм и запустить.)</li> 
				<li>Отправьте боту сообщение с кодом: <b>$code</b>. 
				Вы получите в телеграм уведомление от бота об успешном подключении.
				</li>
				<li>После этого вы начнете получать в телеграм уведомления о новых сообщениях в системе</li>
				</ul>
				Сейчас вы можете закрыть это окно и продолжить работу, скопируйте и отправьте код, затем ожидайте информацию от бота.
				</div>";
		$tm=time();
		if(!$db->dlookup("id","telegram","user_id='{$_SESSION['userid_sess']}' AND customer_id='$customer_id' ")) {
			$db->query("INSERT INTO telegram SET code='$code', user_id='{$_SESSION['userid_sess']}',customer_id='$customer_id',tm='$tm',confirmed='0'");
		} else
			$db->query("UPDATE telegram SET code='$code',tm='$tm',confirmed='0' WHERE user_id='{$_SESSION['userid_sess']}' AND customer_id='$customer_id'");
	} else {
		print "<div alert alert-warning>Ошибка. Обновите страницу.</div>";
	}
	$db->bottom();
	exit;
}


$db=new top($database,0,true,$favicon);
if($db->userdata['access_level']>3)
	exit;
if(isset($_GET['edit']) || isset($_GET['del'])) {
	if($db->userdata['access_level']>1) {
		print "ERROR: 1 access_level only required1";
		exit;
	}
}
$db->db200=$DB200;
$bs=new bs;
print "<h1><span class='alert alert-info'>Управление пользователями</span></h1>";
print "<div class='well'><a href='?add=yes'>".$bs->button_add()."</a></div>";
if(sizeof($_GET)==0 && sizeof($_POST)==0)
	$_GET['view']="yes";

class db1 extends simple_db {
	function prepare_fld($key,$val,$style,$type) {
		if($type=='text') {
			if($key=="passw")
				$val="";
			return "<input class='edit_".$key."' type='text' name='".$key."' value='".$val."' $style>";
		}
	}
	function view() {
		global $DB200;
		$res=$this->query("SELECT *,users.comm AS comm,users.id AS id,cards.uid AS uid
			FROM users JOIN cards ON cards.id=users.klid
			WHERE cards.del=0 AND users.del=0 ORDER BY fl_allowlogin DESC, users.id DESC");
		print "<table class='table table-striped table-hover' >
			<thead>
				<tr>
					<th>#</th>
					<th>user_id</th>
					<th>Login</th>
					<th>Код</th>
					<th>WA_phone</th>
					<th title='Разрешена покупка лидов'>Лиды</th>
					<th>Фамилия и Имя</th>
					<th>Телефон</th>
					<th>ТГ</th>
					<th>Email_from</th>
					<th>Доступ</th>
					<th>Телеграм</th>
					<th>SIP ID</th>
					<th>callaback_url</th>
					<th>Увед 1</th>
					<th>Увед 2</th>
					<th>Увед только о своей пере-писке</th>
					<th>Вход разрешен</th>
					<th>Комментарий</th>
					<th>Действия</th>
				</tr>
			</thead>
			<tbody>";
		$n=1;
		while($r=$this->fetch_assoc($res)) {
			$id=$r['id'];
			$uid=$r['uid'];
			$checked_global=(!$r['leadgen_stop_global'])?"checked":"";
			$checked_user=(!$r['leadgen_stop_user_action'])?"checked":"";
				
			print "<tr>
					<td>$n</td>
					<td>{$r['id']}</td>
					<td><a href='$DB200/msg.php?uid=$uid' class='' target='_blank'>{$r['username']}</a></td>
					<td>{$r['klid']}</td>
					<td>{$r['pact_phone']}</td>
					<td>
						<input type='checkbox' id='{$r['id']}' name='leadgen_chk' $checked_global title='покупка лидов разрешена'>
						<input type='checkbox' $checked_user disabled title='юзер НЕ поставил покупку лидов на паузу'>
					</td>
					<td>{$r['real_user_name']}</td>
					<td>{$r['mob_search']}</td>
					<td><a href='https://t.me/{$r['tg_nick']}' class='' target='_blank'>{$r['tg_nick']}</a></td>
					<td>{$r['email_from_name']}</td>
					<td>{$r['access_level']}</td>
					<td>{$r['telegram_id']}</td>
					<td>{$r['sip']}</td>
					<td>{$r['callback_url']}</td>
					<td>{$r['fl_notify_if_new']}</td>
					<td>{$r['fl_notify_if_other']}</td>
					<td>{$r['fl_notify_of_own_only']}</td>
					<td>{$r['fl_allowlogin']}</td>
					<td>{$r['comm']}</td>
					<td>
						<a href='?edit=yes&id=$id' class='' target=''>edit</a>
						<a href='?del=yes&id=$id' class='' target=''>del</a>
					</td>
				</tr>";
			$n++;
		}
		print "</tbody></table>";
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
 		//print "HERE_$key $val<br>";
 		return $val;
 	}
 	function view_val($key,$val,$id) {
 		if($key=="passw")
 			$val="XXX";
 		return $val;
 	}
	
}

$db1=new db1;
$db1->charset="utf8mb4";
$db1->connect( mysql_user, mysql_passw,$database);
//$db1->connect ("vlav", "fokova#142586","fishing");
$db1->init_table("users");
$db1->view_query="SELECT * FROM users WHERE del=0 AND id>0 AND username!='vlav' ORDER BY username DESC";
	//function add_field($label,$key,$val,$type,$w)
	//var $chk=""; //validate fields - non_empty,unicum,date,time
$fld=$db1->add_field("Login:","username","","text",400); $fld->chk="unicum";
$fld=$db1->add_field("klid:","klid","","text",400); $fld->chk="unicum";
$fld=$db1->add_field("Пароль:","passw","","text",400); //$fld->chk="non_empty";
$fld=$db1->add_field("Реальное имя:","real_user_name","","text",400);
$fld=$db1->add_field("email_from_name:","email_from_name","","text",400);
//$fld=$db1->add_field("uid vk:","uid","","text",400);
//$fld=$db1->add_field("Аккаунт (если привязан):","acc_id",0,"text",400); $fld->chk="numeric";
$fld=$db1->add_field("Доступ (1-5):","access_level",3,"text",400); $fld->chk="numeric";
//$fld=$db1->add_field("token:","token","","text",400);
$fld=$db1->add_field("telegram_id:","telegram_id",0,"text",400); 
$fld=$db1->add_field("sip:","sip",0,"text",400); $fld->chk="numeric";
$fld=$db1->add_field("callback_url:","callback_url",0,"text",400); 
$fld=$db1->add_field("Уведомления 1:","fl_notify_if_new","","checkbox",400);
$fld=$db1->add_field("Уведомления 2:","fl_notify_if_other","","checkbox",400);
$fld=$db1->add_field("Уведомлять только о своей переписке:","fl_notify_of_own_only",0,"checkbox",400);
$fld=$db1->add_field("Вход разрешен:","fl_allowlogin","","checkbox",400);
$fld=$db1->add_field("Комментарий:","comm","","textarea",400);

$fld=$db1->add_field("Партнерский код ГК:","gk_code",0,"text",400);
$fld=$db1->add_field("Pixel FB:","fb_pixel",0,"text",400);
$fld=$db1->add_field("Телефон PACT:","pact_phone",0,"text",400); 
$fld=$db1->add_field("Токен PACT:","pact_token",0,"text",400);
$fld=$db1->add_field("Показывать блок с гарантиями:","garant","","checkbox",400);


$db1->run();

?>
<script>
	$("input[name='leadgen_chk']").change(function(){
		var id=$(this).attr('id');
		if(this.checked)
			var fl=0;  else var fl=1;
		var url='leadgen_stop_global=yes&user_id='+id+'&fl='+fl;
		console.log(url);
		//setup the ajax call
		$.ajax({
			type:'GET',
			url:'jquery.php',
			data:url
		});
	});
</script>
<?

$db->bottom();
?>
