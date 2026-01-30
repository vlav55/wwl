<?
exit
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
//include "/var/www/html/pini/formula12/scripts/leadgen/leadgen.class.php";
include "init.inc.php";

$db=new top($database,0,"Тест вотсап",$favicon);
$user_id=intval($_SESSION['userid_sess']);
if(!$user_id)
	exit;
$login=$db->dlookup("username","users","id='$user_id'");
$phone=$db->dlookup("pact_phone","users","id='$user_id'");
$channel_id=$db->dlookup("pact_channel_id","users","id='$user_id'");
$klid=$db->dlookup("klid","users","id='$user_id'");
if(!$channel_id) {
	print "<div class='alert alert-danger' >Вотсап не подключен</div>";
	$db-bottom(); exit;
}
print "<h2>Тест подключения вотсап</h3>";
print "<h3>$login</h3>
	<p>рабочий телефон: <span class='badge' >$phone</span></p>
	<p>код партнера: <span class='badge' >$klid</span></p>
	<p>канал: <span class='badge' >$channel_id</span></p>";

$l=new leadgen();
$res=$l->test_channel($klid);


$state=$db->fetch_assoc($db->query("SELECT state FROM pact_state WHERE channel_id='$channel_id' ORDER BY tm DESC LIMIT 1") )['state'];
if($state) {
	print "<div class='alert alert-success' >КАНАЛ ВОТСАП РАБОТАЕТ</div>";
} else {
	print "<div class='alert alert-danger' >КАНАЛ ВОТСАП НЕ РАБОТАЕТ</div>";
	print "<div class='well' >Канал не работает. Что делать:
		<p>1. Возможно это кратковременный сбой. Подождите пару минут и <a href='?' class='' target='' >проверьте еще раз</a>.</p>
		<p>2. Если не помогает, то отсутствует подключение вотсап, необходимо <a href='pact.php' class='' target='_blank' >переподключить (сосканировать QR код)</a>.</p>
		<p>3. Если постоянно идут ошибки и подключение регурно пропадает.</p>
		<p>В нормальном состоянии телефон, подключенный единожды, стоит на зарядке и подключение работает месяцами.
		Если стабильности нет, то чаще всего лучшее решение - использовать для этой цели другой телефон,
		либо выполнить условия от поставщика сервиса: <br>
		</p>
		<div class='well' >
		Для корректной работы WhatsApp в Пакт без отключений и сбоев должны быть соблюдены все условия:<br>
1) Ваш телефон постоянно подключён к Wi-Fi, сигнал Wi-Fi стабилен;<br>
2) WhatsApp не подключён к другим сервисам и нигде не используется кроме вашего телефона, в том числе в WhatsApp Web;<br>
3) Телефон постоянно подключён к зарядному устройству, уровень заряда - 100%;<br>
4) Режим энергосбережения на телефоне отключён;<br>
5) WhatsApp запущен поверх всех приложений (на экране телефона окно чатов WhatsApp, а не рабочий стол);<br>
6) Отключена автоматическая блокировка экрана (экран телефона всегда включён);<br>
7) На телефоне не используется VPN;<br>
8) Телефон не используется для совершения/принятия звонков;<br>
9) WhatsApp и ОС телефона обновлены до последней версии (Андроид не ниже 7.0., IPhone использовать не рекомендуется).<br>
10) Приложению WhatsApp запрещен доступ к контактам, сохраненным в телефоне, или на телефоне очищена телефонная книга <br>
		</div>
		
		</div>";
}


$db->bottom();
?>
