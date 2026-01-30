<?
exit;
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "/var/www/vlav/data/www/wwl/inc/pact.class.php";
include "init.inc.php";



$db=new top($database,"Подключение вотсап",false);
$user_id=$_SESSION['userid_sess'];
$login=$db->dlookup("username","users","id='$user_id'");
$real_user_name=$db->dlookup("real_user_name","users","id='$user_id'");
$klid=$db->dlookup("klid","users","id='$user_id'");
$company_id=$db->dlookup("pact_company_id","users","id='$user_id'");
$channel_id=$db->dlookup("pact_channel_id","users","id='$user_id'");
$wa_user_name=$db->dlookup("wa_user_name","users","id='$user_id'");
$pact_phone=$db->dlookup("pact_phone","users","id='$user_id'");

$ok=true;

if(!$company_id && sizeof($_GET)==0) {
	print "<div class='alert alert-warning' >
		Вы подключается канал вотсап и с этого момента начнется 30 дневный тестовый период.
		Отменить или перенести его будет нельзя.
		Если вы уверены, что готовы к работе, то нажмите кнопку ниже или посоветуйтесь с вашим наставником.
		</div>";
	print "<div><a href='?start=yes' class='btn btn-primary' target=''>Продолжить</a></div>";
	$db->bottom();
	exit;
}?>
<div class='container' style='font-size:18px;'>

<!--
<div class='text-center mt-5 mb-5' style='padding:20px;'><img src='qr_codes/qr_55458.png' class='img-responsive'   style='max-width:400px;' ></div>
-->

<h2>Подключение вотсап</h2>

<p><b>На этой странице вы можете подключить свой рабочий номер вотсап к CRM.</b>
</p><br>
<div class='well' >
	<p>Login: <?=$login?></p>
	<p>Имя вотсап: <?=$wa_user_name?></p>
	<p>Телефон вотсап: <?=$pact_phone?></p>
	<p>company_id: <?=$company_id?></p>
	<p>channel_id: <?=$channel_id?></p>
	<p><a href='test_wa.php' class='btn btn-primary' target='_blank'>Проверить подключение</a> (откроется в новом окне)</p>
	
</div>
<br>

<?
if(empty($wa_user_name)) {
	print "<div class='alert alert-danger' >Не заполнено имя в вотсап. <a href='users5.php' class='' target=''>перейти в профиль</a>
	</div>";
	$ok=false;
}
if(empty($pact_phone)) {
	print "<div class='alert alert-danger' >Не заполнен телефон вотсап. <a href='users5.php' class='' target=''>перейти в профиль</a>
	</div>";
	$ok=false;
}
if(!$ok) {
	$db->bottom();
	exit;
}

$res=$db->check_wa($user_id);
if($res) {
	print "<div class='alert alert-success' >Канал вотсап $channel_id подключен успешно. <br>
		Протестируйте его, отправив партнерский код с другого телефона (но не со своего личного!) и убедитесь,
		что чатбот отвечает.
		</div>";
} else {
	print "<div class='alert alert-danger' >Ошибка: подключение не было успешным либо еще не установилось.
	</div>";
}


?>
<h2>Как подключить вотсап</h2>

<p >Подключение к вотсап выполняется на сайте компании PACT.IM
они предоставляют нам услуги связи с вотсап.
</p>
<p>Для подключения необходимо выполнить шаги:
</p>
<p>1. Зарегистрироваться на сайте <a href='https://app.pact.im/signup?ref=77810e179b951b83bd7a861f3fe67659' class='' target='_blank'>по ссылке</a>
</p>
<p>2. Зайти в настройки (иконка настроек, меню слева, внизу), Вкладка "О компании", поле <b>Webhook URL</b>
вставить в это поле адрес: <b>https://formula12.ru/scripts/pact/webhook.php</b> и нажать кнопку "Сохранить"
</p>
<p>3. Зайти в профиль (вверху справа, нажать на свою фамилию и там выбрать пункт <b>Настройки</b>).
В настройках внизу скопировать <b>Private token</b> (длинное цифробуквенное значение).
</p>
<p>4. Зайти в ЦРМ - Настройки - Профиль. Нажать "Редактировать" и вставить токен в поле: "<b>ПАКТ ТОКЕН</b>"
</p>
<p>5. Там же должен быть занесен ваш рабочий номер телефона (который для вотсап).
</p>
<p>6. Нажать "Сохранить". Должна появиться зеленая надпись ОК рядом с токеном пакт.
</p>
<p>7. Теперь можно переходить к подключению. Заходим опять на сайт Пакт. Переходим в настройки (слева внизу)
Выбираем верхний пункт "Whatsapp" для подключения (не бизнес, а обычный вотсап).
Далее читаем инструкцию и нажимаем кнопку "Следующий шаг". На третьем шаге переставляем галочку. Должно быть выбрано - "Синхронизировать сообщения не старше дня".
Выводим QR код и сканируем его. Должна появиться надпись "загрузка" и иконка вращения вместо qr кода.
Ждать окончания не обязательно.
</p>
<p>8. Чтобы проверить подключение перейдите на другом телефоне по своей партнерской ссылке и отправьте ваш партнерский код в вотсап.
Должен ответить чат бот. Это значит, что все в порядке.
</p>
<p ><b>Подробно процедура подключения показана на видео <a href='https://youtu.be/aRfJJvoPCTk' class='' target='_blank'>https://youtu.be/aRfJJvoPCTk</a></b>
</p>
<p>❗❗❗ <b>Все вопросы задаем в поддержку Пакт. Иконка сообщений справа внизу на сайте Пакта.</b>
</p>
<?

$db->bottom();
exit;


if(isset($_GET['qr_code'])) {
	$qr="https://formula12.ru/scripts/pact/qr_codes/qr_$company_id.base64";
	$db->query("UPDATE users SET pact_channel_online=0 WHERE id='$user_id'");
	//print "$qr";
	print "<h3>Сосканируйте QR код</h3>";
	print "<p>Внимание: срок жизни qr кода несколько секунд, действуйте быстро!</p>";
	print "<div class='alert alert-info small' >Если вы не видите ниже QR кода, обновите страницу.
	Если код не появляется, то подождите 10 минут и выполните
	<a href='?re_connect=yes' class='btn btn-info' target=''>переподключение</a>.
	</div>";
	if(file_exists("/var/www/html/pini/formula12/scripts/pact/qr_codes/qr_$company_id.base64")) {
		$f1="";
		$n=100;
		while(1) {
			$file = file_get_contents($qr); //your data in base64 'data:image/png....';
			if($f1!=$file) {
				$img = str_replace('data:image/png;base64,', '', $file);
				file_put_contents("qr_codes/qr_$company_id.png", base64_decode($img));
			}
		//	sleep(1);
			if(!$n--)
				break;
			break;
		}
		print "<br><br>";
		print "<div class='text-center mt-5 mb-5' style='padding:20px;'><img src='qr_codes/qr_$company_id.png' id='img_qr' class='img-responsive'   style='max-width:400px;' ></div>";
		print "<br><br><br><br><br>";
		
		?>
		<script>
		setTimeout(function(){
		   window.location.reload(1);
		   //document.getElementById("img_qr").src="qr_codes/qr_<?=$company_id?>.png";
		   //console.log("step");
		}, 3000);
		//~ setInterval( function() {
			//~ $('#img_qr').attr('src', 'qr_codes/qr_<?=$company_id?>.png');
			//~ //document.getElementById("img_qr").src="qr_codes/qr_<?=$company_id?>.png";
			//~ console.log("step");
		//~ }, 3000); //repeat function every 5000 milliseconds
		</script>
		<?
	} else {
		print "<p class='alert alert-primary' >Обновите страницу. Если после нескольких обновлений QR код не появился, то обратитесь к наставнику.</p>";
		print "<div><a href='javascript:location.reload();' class='btn btn-success btn-lg' target=''>Обновить страницу</a></div>";
	}
	print "<div class='alert alert-info' >
		Если при сканировании кода было сообщение об ошибке: <br>
		<span class='small' >&laquo;Не удалось распознать QR код, убедитесь, что вы находитесь в приложении Whatsapp Web&raquo;</span> <br>
		- то это скорее всего означает, что код устарел и вы не успели его сосканировать. Нажмите ниже кнопку - Проверить.
		</div>";
	print "<div class='alert alert-success' >
		Подождите 60 секунд и нажмите кнопку ниже, чтобы проверить состояние подключения:
		</div>";
	print "<div class='p-2 text-center' ><a href='?check_connection=yes' class='btn btn-primary btn-lg' target=''>ПРОВЕРИТЬ ПОДКЛЮЧЕНИЕ</a></div>";
	$db->bottom();
	exit;
}

if(isset($_GET['do_re_connect'])) {
	$p=new pact('yogahelpyou');
	//print "HERE";
	unlink("/var/www/html/pini/formula12/scripts/pact/qr_codes/qr_$company_id.base64");
	unlink("qr_codes/qr_$company_id.png");
	$res=$p->delete_channel($company_id,$channel_id);
	$channel_id=0;
	$db->query("UPDATE users SET pact_channel_id='$channel_id' WHERE id='$user_id'");
	print "<div class='alert alert-info' >Переподключение: $res</div>";
}
if(isset($_GET['re_connect'])) {
	print "<div class='alert alert-warning' >
		Внимание: Подождите 10 минут с момента неудачной попытки подключения и только затем нажмите кнопку!
		<div class='m-2' ><a href='?do_re_connect=yes' class='btn btn-primary' target=''>Переподключить</a></div>
		</div>";
	$db->bottom();
	exit;
}
if(isset($_GET['do_connect'])) {
	$p=new pact('yogahelpyou');
	if(!$company_id) {
		$company_id=$p->create_company($login,"https://formula12.ru/scripts/pact/webhook.php",$pact_phone,"$real_user_name $wa_user_name");
		if($company_id) {
			$token=$p->token;
			$db->query("UPDATE users SET pact_company_id='$company_id',pact_token='$token' WHERE id='$user_id'");
			print "<div class='alert alert-success' >Компания успешно создана: $company_id. Бесплатный период начался.</div>";
			$tm_start=$db->dt1(time());
			$db->query("INSERT INTO billing SET user_id='$user_id',tm='".$tm_start."',payed=2");
		} else
			print "<div class='alert alert-danger' >Ошибка: не удалось создать компанию. Сообщите наставнику.</div>";
		
	}
	
	$p->company_id=$company_id;
	$channel_id=$p->get_channel_id();
	$p->delete_channel($company_id,$channel_id);
	$channel_id=0;
	$db->query("UPDATE users SET pact_channel_id=0 WHERE id='$user_id'");
	
	if(!$channel_id) {
		$channel_id=$p->create_channel($company_id);
		if($channel_id==402)
			print "<div class='alert alert-danger' >Сервис не оплачен. Обратитесь к наставнику.</div>";
		if(!$channel_id || $channel_id==402) {
			print "<div class='alert alert-danger' >Ошибка: не удалось создать канал. Обратитесь к наставнику и скопируйте сообщение на экране.</div>";
		} else {
			$db->query("UPDATE users SET pact_channel_id='$channel_id' WHERE id='$user_id'");
			print "<div class='alert alert-success' >Канал вотсап успешно создан: $channel_id</div>";
			//sleep(5);
			print "<p><b>Нажмите на кнопку ниже и сосканируйте телефоном QR код.</b></p>";
			print "<p>Внимание: срок жизни qr кода несколько секунд, убедитесь,
				что у вас уже запущен на телефоне сканер кодов и, когда высветится код, действуйте быстро!</p>";
			//print "<div class='alert alert-warning' >Увеличьте заранее яркость экрана, чтобы QR код хорошо считывался! </div>";
			print "<div><a href='?qr_code=yes' class='btn btn-success' target=''>Открыть QR код</a></div>";
			$db->bottom();
			exit;
		}
	}
}
?>

<p>✅ Приготовьте мобильный телефон: подключите Wi-Fi и запустите WhatsApp;</p><br>
<p>✅ Выберите Настройки -> WhatsApp Web</p><br>
<p class='small well' >Если у вас ниже зеленой кнопки &laquo;Привязка устройства&raquo; есть список компьютеров,
где устройство привязано, то нужно их отключить. Нажать на каждое, затем нажать - выйти.
</p><br>
<p class='alert alert-danger' >Внимание! На телефоне ниже кнопки &laquo;Привязка устройства&raquo;
появилась также ссылка &laquo;Бета версия для нескольких устройств. Позволяет привязать до четырех устройств&raquo;.
Эту ссылку использовать нельзя!
</p>
<br>

<p>✅ Нажмите на телефоне кнопку &laquo;Привязка устройства&raquo;. У вас должен появиться сканер штрих-кодов.</p><br>
<p class='alert alert-info' >Важно! QR код сканируется только один раз. После сканирования нужно подождать и ничего не делать.
Второй раз один и тот же код сканировать нельзя, он уже недействителен!</p>
<p>✅ Нажмите кнопку: <a href='?do_connect=yes' class='' target=''><button class='btn btn-success btn-lg' >Привязать вотсапп</button></a>

<?
	if($channel_id>0) {
		print " или <a href='?re_connect=yes' class='' target=''><button class='btn btn-warning btn-lg' >Переподключить</button></a>";
	}
?>
</p>

	<br><br>
	<div class='small well text-info' >
		* В течение 30 дней с момента подключения услуга предоставляется бесплатно, далее стоимость использования CRM+whatsapp составляет 1500р в месяц. 
	</div>
</div>
<?

$db->bottom();
?>
