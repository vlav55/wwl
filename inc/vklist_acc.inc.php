<?
$db=new top($database,"640px;",true,$favicon);
$bs=new bs;

if($db->userdata['access_level']==2) {
	$res=mysql_query("SELECT * FROM vklist_acc WHERE  del=0 ORDER BY num");
	while($r=mysql_fetch_assoc($res)) {
	if($r['last_error']>0)
		$l="color:red;"; else $l="";
		print "<div class='well well-sm' style='$l'>({$r['id']}) | <a href='https://vk.com/id{$r['vk_uid']}' target='_blank'>{$r['name']}</a> | {$r['login']} | {$r['passw']}</div>";
	}
	$db->bottom();
	exit;
}
if($db->userdata['access_level']>2) {
	$res=mysql_query("SELECT * FROM vklist_acc WHERE  del=0 ORDER BY num");
	while($r=mysql_fetch_assoc($res)) {
	if($r['last_error']>0)
		$l="color:red;"; else $l="";
		print "<div class='well well-sm' style='$l'>({$r['id']}) | <a href='https://vk.com/id{$r['vk_uid']}' target='_blank'>{$r['name']}</a></div>";
	}
	$db->bottom();
	exit;
	exit;
}

$test_uid_send_to=$VK_OWN_UID;

$app_id_mess="<span class='label label-success' >$app_id</span>";
if(!$app_id) {
//	$app_id = '5613049';// '5881486';
	$app_id_mess="<span class='label label-warning' >нет приложения</span>";
}
$app_id=6458769;
$scope = 'offline,wall,friends,video,audio,groups,ads';
$version='5.69';
$last_responce="";

print "<h1>ПРОМО АККАУНТЫ</h1>";
print "<div class='well'>
	<ul class='nav nav-pills'>
	<li class='active'><a href='?add=yes'>Добавить</a></li>
	<li class='active_'><a href='?view=yes'>Список</a></li>
	<li class='active_'><a href='?auth=yes' target='_blank'>Авторизация</a></li>
	<li class='active_'><a href='?app=yes' target='_blank'>Приложение:$app_id_mess</a></li>
	<li class='active_'><a href='javascript:wopen_1(\"https://1-info.ru/vktrade/doc/#acc\")'>Инструкция</a></li>
	<li class='active_'><a href='serv_acc.php'>Сервис</a></li>
	</ul>
	</div>";

if(sizeof($_GET)==0 && sizeof($_POST)==0)
	$_GET['view']="yes";
if(@$_GET['auth']) {
	if(!$app_id) {
		print "<div class='alert alert-warning' >Перед авторизацией аккаунтов необходимо <a href='?app=yes' class='' target=''>создать приложение</a></div>";
		$db->bottom();
		exit;
	}
	$link="https://oauth.vk.com/authorize?client_id=$app_id&display=page&redirect_uri=https://oauth.vk.com/blank.html&scope=$scope&response_type=token&v=5.37";
	print "<div class='well'>
		<h2>Авторизация промо-аккаунта в контакте</h2>
		<p>Созданный промо аккаунт необходимо авторизовать в ВК, чтобы от его имени вы могли отправлять сообщения. Для этого:</p>
		<ul>
		<li><b>Выйдите</b> из ВК</li>
		<li>Зайдите в ВК <b>под этим аккаунтом</b></li>
		<li>Нажмите кнопку ниже <b>&quot;Авторизовать&quot;</b>. Откроется окно с разрешениями - дайте разрешения.</li>
		<li>Затем Вы увидите пустое окно браузера с надписью <span class='blue'>Пожалуйста, не копируйте данные из адресной строки для сторонних сайтов. Таким образом Вы можете потерять доступ к Вашему аккаунту.</span></li>
		<li>Скопируйте адресную строку браузера и вставьте ее в поле <b>токен</b> в вктрэйд</li>
		<li>Сохраните данные</li>
		<li>Нажмите кнопку <b>Тест</b> и протестируйте подключенный аккаунт. Ошибка 5 означает неправильный токен, повторите операцию.</li>
		</ul>
		</div>";
	print "<a href='$link' target='_blank'>".$bs->button("primary","Авторизовать")."</a>";
	print "<br><br> или <a href='$link' target='_blank'>".$bs->button_close($pos="left",$text="Закрыть окно")."</a>";
}
if(@$_GET['do_app']) {
	$app_id=intval($_GET['app_id']);
	if($app_id) {
		$db->connect("vktrade");
		$db->query("UPDATE customers SET app='$app_id' WHERE id='$customer_id'");
		print "<div class='alert alert-success' >Записано</div>";
		print "<a href='vklist_acc.php' class='' target=''><button type='button' class='btn btn-primary'>Ok</button></a>";
		$db->bottom();
		exit;
	}
	
}
if(@$_GET['app']) {
	?>
	<div class='well' >
		<h2>Подключение промо аккаунтов - создание приложения</h2>
		Работа с рабочими аккаунтами идет через приложение ВК,
		поэтому перед авторизацией аккаунтов необходимо создать приложение.
		<ol>
		<li>Заходите на свою страницу вк и нажимаете ссылку слева внизу - Разработчикам. <a href='https://1-info.ru/vktrade/doc/images/app/1.jpg' class='' target='_blank'>см.скриншот</a></li>
		<li>Выбираете - Мои приложения. <a href='https://1-info.ru/vktrade/doc/images/app/2.jpg' class='' target='_blank'>см.скриншот</a></li>
		<li>Создать приложение. <a href='https://1-info.ru/vktrade/doc/images/app/3.jpg' class='' target='_blank'>см.скриншот</a></li>
		<li>Указываете название (произвольно) и нажимаете Поключить приложение. Подтверждаете по смс с привязанного к аккаунту телефона. <a href='https://1-info.ru/vktrade/doc/images/app/4.jpg' class='' target='_blank'>см.скриншот</a></li>
		<li>Сохранить изменения. <a href='https://1-info.ru/vktrade/doc/images/app/5.jpg' class='' target='_blank'>см.скриншот</a></li>
		<li>Далее заходите в настройки и копируете ID приложения. <a href='https://1-info.ru/vktrade/doc/images/app/6.jpg' class='' target='_blank'>см.скриншот</a></li>
		<li>Вставляете скопированный номер ниже и нажимаете Submit. Теперь можно <a href='?auth=yes' class='' target=''>авторизовывать</a> аккаунты (получать токены).</li>
		
		</ol>
	</div>
	<?
	print "
	<form class='form-inline' action='#'>
	  <div class='form-group'>
		<label for='app'>ID приложения:</label>
		<input type='app' class='form-control' id='app' name='app_id' value=''>
	  </div>
	  <button type='submit' class='btn btn-default' name='do_app' value='yes'>Submit</button>
	</form>
	";
}
if(@$_GET['do_send_msg']) {
	if(trim($_GET['test_uid_send_to'])!="") {
		$token=$_GET['token'];
		//print "<h1>{$_POST['uid']} : Message sent</h1>";
		//$uid=vk_get_uid_by_domain($_POST['uid']);
		//vk_msg_send($uid, $_POST['mess']);
		$vk=new vklist_api($token);
		$uid=$vk->get_uid_from_url($_GET['test_uid_send_to']);
		$vk->vk_msg_send($uid, $_GET['mess']);
		print "<p>Result: $vk->last_response</p>";
	} else print "<p class='red'>User id was not specified</p>";
	print "<hr>";
}
if(@$_GET['send']) {
	$r=$db->fetch_assoc($db->query("SELECT *,vklist_acc.id AS id FROM vklist_acc WHERE vklist_acc.id='".intval($_GET['id'])."'"));
	$uid=intval($r['vk_uid']);
	print "<h2><div class='alert alert-info'>Send message from ($uid) {$r['name']}</div></h2>";
	$mess="Тестовое сообщение от аккаунта ($uid {$r['name']}";
	print "<form name='f_send_handmode' method='GET' action='?lastid={$r['id']}&view=yes'>
	Получатель: (<a href='https://vk.com/id$test_uid_send_to' target='_blank'>https://vk.com/id$test_uid_send_to</a>)<br> 
	<input type='text' name='test_uid_send_to' value='https://vk.com/id$test_uid_send_to' style='width:400px;'><br>
	СООБЩЕНИЕ<br>
	<textarea name='mess' style='width:400px;height:150px;'>$mess</textarea><br><br>
	<input type='hidden' name='token' value='{$r['token']}'><br>
	<input type='submit' class='btn btn-primary' name='do_send_msg' value='Send message'>
	</form>";
}
if(@$_GET['last_error_clr']) {
	$this->query("UPDATE vklist_acc SET last_error=0,last_error_msg='' WHERE id={$_GET['id']}");
	$_GET['view']="yes";
}
if(@$_GET['switch_acc_notuse']) {
	if($_GET['fl']==1)
		$db->query("UPDATE vklist_acc SET fl_acc_allowed=0 WHERE id={$_GET['id']}");
	else
		$db->query("UPDATE vklist_acc SET fl_acc_allowed=1 WHERE id={$_GET['id']}");
	$_GET['view']="yes"; $_GET['lastid']=$_GET['id'];
}
if(@$_GET['switch_fl_acc_not_allowed_for_new']) {
	if($_GET['fl']==1)
		$db->query("UPDATE vklist_acc SET fl_acc_not_allowed_for_new=0 WHERE id={$_GET['id']}");
	else
		$db->query("UPDATE vklist_acc SET fl_acc_not_allowed_for_new=1 WHERE id={$_GET['id']}");
	$_GET['view']="yes"; $_GET['lastid']=$_GET['id'];
}
if(@$_GET['tm_next_send_clr']) {
	mysql_query("UPDATE vklist_acc SET tm_next_send_msg=0 WHERE id={$_GET['id']}");
	$_GET['view']="yes"; $_GET['lastid']=$_GET['id'];
}
if(@$_GET['do_chproject']) {
	//print "HERE_{$_GET['project_id']}";
	mysql_query("UPDATE vklist_acc SET project_id={$_GET['project_id']} WHERE id={$_GET['id']}");
	print "<script>location='?view=yes&lastid={$_GET['id']}'</script>";
}


class db1 extends simple_db {
	function view() {
		$bs=new bs;
		$res=$this->query("SELECT * FROM vklist_acc WHERE del=0");
		$n=1;
		print "<table class='table table-bordered table-striped table-hover'>";
		print "<thead><tr>
			<th>№ п/п (num)</th>
			<th>ID</th>
			<th>Разрешен для рассылки</th>
			<th>Запрещен для отправки сообщений новым</th>
			<th>Это аккаунт группы</th>
			<th>ВК UID</th>
			<th>ВК логин/пароль</th>
			<th>ВК кол-во банов</th>
			<th>ВК Имя</th>
			<th>Коммент</th>
			<th>Использование запрещено до</th>
			<th>Управление</th>
			</tr></thead>";
		while($r=$this->fetch_assoc($res)) {
			if($r['id']==@$_GET['lastid'])
				$c="background-color:yellow;"; else $c="";
			if($r['last_error']>0)
				$l="color:red;"; else $l="";
			$tm_next_send=($r['tm_next_send_msg']>0)?date("d.m.Y H:i",$r['tm_next_send_msg']):"0";
			$fl_acc_not_allowed_for_new=($r['fl_acc_not_allowed_for_new']==1)?"X":" ";
			$fl_allow_read_from_all=($r['fl_allow_read_from_all']==1)?"X":" ";
			print "<tr style='$c $l'>
				<td  style='text-align:center;'>$n ({$r['num']})</td>
				<td  style='text-align:center;'><span class='badge'>{$r['id']}</span></td>
				<td style='text-align:center;'><b><a href='?switch_acc_notuse=yes&id={$r['id']}&fl={$r['fl_acc_allowed']}'>{$r['fl_acc_allowed']}</a></b></td>
				<td style='text-align:center;'><b><a href='?switch_fl_acc_not_allowed_for_new=yes&id={$r['id']}&fl={$r['fl_acc_not_allowed_for_new']}'>{$r['fl_acc_not_allowed_for_new']}</a></b></td>
				<td style='text-align:center;'>$fl_allow_read_from_all</td>
				<td style='text-align:center;'>{$r['vk_uid']}</td>
				<td>{$r['login']}<br>{$r['passw']}</td>
				<!--<td style='text-align:center;'>XXXXXXXXXXXX</td>
				<td>XXXXXXX<br>XXXXXXX</td>-->
				<td style='text-align:center;'>{$r['ban_cnt']}</td>
				<!--<td>{$r['email']}<br>{$r['email_passw']}</td>-->
				<td><a href='https://vk.com/id{$r['vk_uid']}' target='_blank'>{$r['name']}</a></td>
				<td>{$r['comm']}</td>
				<td>$tm_next_send <a href='?tm_next_send_clr=yes&id={$r['id']}'> (очистить)</a></td>";
			print 	"
				<td>
					<a href='?send=yes&id={$r['id']}' title='send in hand mode'>".$bs->button("primary","Test")."</a>
					<a href='?edit=yes&id={$r['id']}'>правка</a>
					<a href='?del=yes&id={$r['id']}'>удал</a>
				</td>
				</tr>";
			$n++;
		}
		print "</table>";
	}
 	function conv($key,$val) {
 		if($key=="vk_uid") {
 			if(!is_numeric($val)) {
 				$vk=new vklist_api;
				//$vk->token=$this->dlookup("token","vklist_acc","del=0 AND last_error=0");
 				$val=$vk->get_uid_from_url($val);
 			}
 			if(!is_numeric($val)) {
				$val=0;
			}
 		}
 		if($key=="token") {
			if(preg_match("|#access_token=([\w]+)&expires_in=|",$val,$m)) {
				$val=$m[1];
			}
		}
 		return $val;
 	}
	function after_do_edit($id) {
		$_GET['lastid']=$id;
		$r=$this->fetch_assoc($this->query("SELECT * FROM vklist_acc WHERE id='$id'"));
		if(intval($r['fl_allow_read_from_all'])==1) {
			$this->query("UPDATE vklist_acc SET fl_acc_allowed='0', fl_acc_not_allowed_for_new='0' WHERE id ='$id'",0);
			$this->query("UPDATE vklist_acc SET fl_allow_read_from_all='0' WHERE id !='$id'",0);
		}
	}
	function after_do_add($id) {
		$_GET['lastid']=$id;
		$r=$this->fetch_assoc($this->query("SELECT * FROM vklist_acc WHERE id='$id'"));
		if(intval($r['fl_allow_read_from_all'])==1) {
			$this->query("UPDATE vklist_acc SET fl_acc_allowed='0', fl_acc_not_allowed_for_new='0' WHERE id ='$id'",0);
			$this->query("UPDATE vklist_acc SET fl_allow_read_from_all='0' WHERE id !='$id'",0);
		}
	}
}
$db1=new db1;
$db1->charset="utf8mb4";
$db1->connect(mysql_user,mysql_passw, $database);
$db1->init_table("vklist_acc");
$db1->view_query="SELECT * FROM vklist_acc WHERE del=0 ORDER BY tm DESC";
	//function add_field($label,$key,$val,$type,$w)
	//var $chk=""; //validate fields - non_empty,unicum,date,time
$fld=$db1->add_field("Разрешен для рассылки:","fl_acc_allowed",0,"checkbox",400);
$fld=$db1->add_field("Не использовать для отправки новым:","fl_acc_not_allowed_for_new",0,"checkbox",400);
$fld=$db1->add_field("Аккаунт группы:","fl_allow_read_from_all",0,"checkbox",400);
$fld=$db1->add_field("Порядок использования <span class='glyphicon glyphicon-question-sign' title='Устанавливать необязательно. Любое число от 0. Первым для отправки новым будет выбираться аккаунт с наименьшим числом.'></span>:","num","0","text",40);
$fld=$db1->add_field("ID VK <span class='glyphicon glyphicon-question-sign' title='только число - например для https://vk.com/id454984229 - здесь вводить 454984229'></span>:","vk_uid","","text",400);
$fld=$db1->add_field("LOGIN ВК:","login","","text",400);
$fld=$db1->add_field("Пароль ВК:","passw","","text",400);
//$fld=$db1->add_field("email:","email","","text",400);
//$fld=$db1->add_field("email_passw:","email_passw","","text",400);
$fld=$db1->add_field("Имя Фамилия:","name","","text",400);
$fld=$db1->add_field("Комментарий:","comm","","textarea",400);
$fld=$db1->add_field("Токен:","token","","text",400); $fld->chk="unicum";
//$fld=$db->add_field("msg_txt:","msg_txt","","textarea",400);$fld->style="style='width:400px; height:150px;'";
//$fld=$db->add_field("wall_txt:","wall_txt","","textarea",400);$fld->style="style='width:400px; height:150px;'";
//$fld=$db->add_field("friends_txt:","friends_txt","","textarea",400);$fld->style="style='width:400px; height:150px;'";
//$fld=$db1->add_field("project_id:","project_id","","select",400); $fld->rowsource="SELECT id,project FROM vklist_projects WHERE del=0 ORDER BY project";
//$fld=$db1->add_field("tm_next_send_msg:","tm_next_send_msg","","text",400);
//$fld=$db1->add_field("tm_next_send_wall:","tm_next_send_wall","","text",400);
//$fld=$db1->add_field("tm_next_send_fr:","tm_next_send_fr","","text",400);
$fld=$db1->add_field("Код ошибки:","last_error","0","text",400);
$fld=$db1->add_field("Количество банов:","ban_cnt","0","text",400);
//$fld=$db1->add_field("last_mid:","last_mid","","text",400);
$fld=$db1->add_field("","tm",time(),"hidden",400);
$db1->run();

$db->bottom();

?>
