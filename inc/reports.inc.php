<?
$menu=(@$_GET['no_menu'])?false:true;
	
$db=new top($database,0,$menu,$favicon);
print "<p>Ведутся технические работы</p>";
$db->bottom();
exit;


$vr=new vklist_reports($database);

$custom=(@$_GET["custom"])?"active":"";
$active_vk_group_by_days=(@$_GET["vk_group_by_days"])?"active":"";
$stat_by_days_newonly=(@$_GET["stat_by_days_newonly"])?"active":"";
$stat_for_period_newonly=(@$_GET["stat_for_period_newonly"])?"active":"";
$stat_by_days=(@$_GET["stat_by_days"])?"active":"";
$stat_for_period=(@$_GET["stat_for_period"])?"active":"";
$stat_by_users=(@$_GET["stat_by_users"])?"active":"";
$msgs_by_users=(@$_GET["msgs_by_users"])?"active":"";
$listsend_by_days=(@$_GET["listsend_by_days"])?"active":"";
if(!@$_GET['no_menu']) {
	?>
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<ul class="nav navbar-nav">
				<li class="<?=$custom?>"><a href="?custom=yes">CUSTOM</a></li>
				<li class="<?=$stat_by_days_newonly?>"><a href="?stat_by_days_newonly=yes">STAT_BY_DAYS_NEWONLY</a></li>
				<li class="<?=$stat_for_period_newonly?>"><a href="?stat_for_period_newonly=yes">STAT_FOR_PERIOD_NEWONLY</a></li>
				<li class="<?=$stat_by_days?>"><a href="?stat_by_days=yes">STAT_BY_DAYS</a></li>
				<li class="<?=$stat_for_period?>"><a href="?stat_for_period=yes">STAT_FOR_PERIOD</a></li>
				<li class="<?=$stat_by_users?>"><a href="?stat_by_users=yes">STAT_BY_USERS</a></li>
				<li class="<?=$active_vk_group_by_days?>"><a href="?vk_group_by_days=yes">Группа ВК по дням</a></li>
				<li class="<?=$listsend_by_days?>"><a href="?listsend_by_days=yes">Рассылки</a></li>
			</ul>
		</div>
	</nav>
	<?
}
print "<div class='container'>";


if(isset($_GET['custom'])) {
	print "<p><a href='reports_custom.php' class='' target='_blank'>reports_custom.php</a></p>";
}

if(@$_GET['listsend_by_days']) {
	print "<h2><div class='alert alert-info'>Статистика по рассылкам по дням (за 10 дней)</div></h2>";
	print "<div class='card bg-light p-3'>
		</div>";
	$vr=new vklist_reports("$database");
	$days=10;
	$tm=time();
	for($i=0; $i<$days;$i++) {
		$vr->listsend_by_days($tm);
		$tm-=24*60*60;
	}
}

if(@$_GET['msgs_by_users']) {
	$vr->msgs_by_users();
}

if(@$_GET['stat_by_users_detailed']) {
	$vr=new vklist_reports("$database");
	$newonly=(isset($_GET['newonly']))?true:false;
	$vr->stat_by_users_detailed($_GET['tm'],$_GET['days'],$_GET['razdel_name'],$_GET['source_name'],$_GET['username'],$newonly);
}
if(@$_GET['stat_by_list_groups']) {
	$vr=new vklist_reports("$database");
	$vr->stat_by_list_groups($_GET['tm'],$_GET['days']);
}
if(@$_GET['stat_by_users']) {
	print "<h2><div class='alert alert-info'>Статистика по менеджерам по дням (за 10 дней)</div></h2>";
	print "<div class='card bg-light p-3'>Отчет показывает количество клиентов, которым в заданный период отправлялись исходящие сообщения.
		</div>";
	$vr=new vklist_reports("$database");
	$days=10;
	$tm=time();
	for($i=0; $i<$days;$i++) {
		$vr->stat_by_users($tm,0);
		$tm-=24*60*60;
	}
}
if(@$_GET['stat_by_days']) {
	print "<h2><div class='alert alert-info'>Статистика по событиям по дням (за 10 дней)</div></h2>";
	print "<div class='card bg-light p-3'>Отчет показывает количество клиентов по разделам, 
		которые в заданные даты проявляли какую либо активность.
		(заходы на лэндинг, участие в опросах, ответы на рассылку, вступление в группы, ручное добавление в базу)<br>
		Источник - указывает откуда клиент появился в базе. 
		Событие, которое привело к появлению в этом отчете, можно посмотреть в комментарии в расшифровке, если нажать на количество.
		</div>";
	$vr=new vklist_reports("$database");
	$days=10;
	$tm=time();
	for($i=0; $i<$days;$i++) {
		$vr->stat($tm);
		$tm-=24*60*60;
	}
}
if(@$_GET['stat_for_period']) {
	print "<h2><div class='alert alert-info'>Статистика по событиям за период</div></h2>";
	print "<div class='card bg-light p-3'>Отчет показывает количество клиентов по разделам, 
		которые в заданные даты проявляли какую либо активность.
		(заходы на лэндинг, участие в опросах, ответы на рассылку, вступление в группы, ручное добавление в базу).<br>
		Источник - указывает откуда клиент появился в базе. 
		Событие, которое привело к появлению в этом отчете, можно посмотреть в комментарии в расшифровке, если нажать на количество.
		</div>";
	if(!isset($_GET['days']))
		$days=30; else $days=$_GET['days'];
	$min_tm=$db->fetch_row($db->query("SELECT tm FROM cards WHERE del=0 ORDER BY tm ASC LIMIT 1"))[0];
	$max_days=round((time()-$min_tm)/(24*60*60),0);
	print "<h2><div class='alert alert-info'>СТАТИСТИКА ЗА $days ДНЕЙ</div></h2>";
	print "<div class='card bg-light p-3'>
		<a href='?stat_for_period=yes&days=3'>за 3 дня</a> .
		<a href='?stat_for_period=yes&days=7'>за 7 дней</a> .
		<a href='?stat_for_period=yes&days=14'>за 14 дней</a> .
		<a href='?stat_for_period=yes&days=30'>за 30 дней</a> .
		<a href='?stat_for_period=yes&days=60'>за 60 дней</a> .
		<a href='?stat_for_period=yes&days=180'>за 180 дней</a> .
		<a href='?stat_for_period=yes&days=$max_days'>Весь период</a> .
		</div>";
	$vr=new vklist_reports("$database");
	$vr->stat(time(),$days);
}
if(@$_GET['stat_by_days_newonly']) {
	print "<h2><div class='alert alert-info'>Статистика по событиям по дням (за 10 дней)</div></h2>";
	print "<div class='card bg-light p-3'>Отчет показывает количество НОВЫХ клиентов по разделам, 
		которые попали в базу в заданные даты.
		(заходы на лэндинг, участие в опросах, ответы на рассылку, вступление в группы, ручное добавление в базу).
		</div>";
	$vr=new vklist_reports("$database");
	$days=10;
	$tm=time();
	for($i=0; $i<$days;$i++) {
		$vr->stat_newonly($tm);
		$tm-=24*60*60;
	}
}
if(@$_GET['stat_for_period_newonly']) {
	print "<h2><div class='alert alert-info'>Статистика по событиям за период</div></h2>";
	print "<div class='card bg-light p-3'>Отчет показывает количество НОВЫХ клиентов по разделам, 
		которые попали в базу в заданный период времени.
		(заходы на лэндинг, участие в опросах, ответы на рассылку, вступление в группы, ручное добавление в базу)
		</div>";
	if(!isset($_GET['days']))
		$days=30; else $days=$_GET['days'];
	$min_tm=$db->fetch_row($db->query("SELECT tm FROM cards WHERE del=0 ORDER BY tm ASC LIMIT 1"))[0];
	$max_days=round((time()-$min_tm)/(24*60*60),0);
	print "<h2><div class='alert alert-info'>СТАТИСТИКА ЗА $days ДНЕЙ</div></h2>";
	print "<div class='card bg-light p-3'>
		<a href='?stat_for_period_newonly=yes&days=3'>за 3 дня</a> .
		<a href='?stat_for_period_newonly=yes&days=7'>за 7 дней</a> .
		<a href='?stat_for_period_newonly=yes&days=14'>за 14 дней</a> .
		<a href='?stat_for_period_newonly=yes&days=30'>за 30 дней</a> .
		<a href='?stat_for_period_newonly=yes&days=60'>за 60 дней</a> .
		<a href='?stat_for_period_newonly=yes&days=180'>за 180 дней</a> .
		<a href='?stat_for_period_newonly=yes&days=$max_days'>Весь период</a> .
		</div>";
	$vr=new vklist_reports("$database");
	$vr->stat_newonly(time(),$days);
}


if(@$_GET['vk_group_by_days']) {
	print "<h2><div class='alert alert-info'>Вступление в группу по дням</div></h2>";
	$gid=$VK_GROUP_ID;
	
	////////////////
	$res=$db->query("SELECT * FROM vklist_scan_groups WHERE d=0");
	while($r=$db->fetch_assoc($res)) {
		$db->query("UPDATE vklist_scan_groups SET d=".date("d",$r['tm']).",m=".date("m",$r['tm']).",y=".date("Y",$r['tm'])." WHERE id={$r['id']}");
	}
	//~ $res=$db->query("SELECT * FROM vklist_scan_groups WHERE bdate!=''");
	//~ while($r=$db->fetch_assoc($res)) {SELECT y,m,d,uid, COUNT(uid) AS cnt FROM vklist_scan_groups WHERE gid='160316160' GROUP BY y,m,d ORDER BY tm DESC
		//~ if($r['bdate']!="") {
			//~ $b=explode(".",$r['bdate']);
			//~ if(sizeof($b)==3) {
				//~ $age=date("Y")-$b[2];
			//~ } else $age=0;
		//~ } else $age=0;
		//~ $db->query("UPDATE vklist_scan_groups SET age=$age WHERE id={$r['id']}");
	//~ }
	/////////////
	
	if(@$_GET['for_date']) {
		print "<p><a href='javascript:history.back();'>back</a></p>";
		$dt1=$db->dt1($_GET['for_date']);
		$dt2=$db->dt2($_GET['for_date']);
		$vk=new vklist_api();
		$res=$db->query("SELECT * FROM vklist_scan_groups WHERE gid='$gid' AND tm>=$dt1 AND tm<=$dt2 ORDER BY tm DESC",0);
		while($r=$db->fetch_assoc($res)) {
			if(empty($r['first_name'])) {
				$u=$vk->vk_get_userinfo($r['uid']);
				//$db->print_r($u);
				$db->query("UPDATE vklist_scan_groups SET
					first_name='".$db->escape($u['first_name'])."',
					last_name='".$db->escape($u['last_name'])."',
					sex='".$db->escape($u['sex'])."',
					city_id='".$db->escape($u['city']['id'])."',
					country_id='".$db->escape($u['country']['id'])."',
					city='".$db->escape($u['city']['title'])."',
					country='".$db->escape($u['country']['title'])."',
					bdate='".$u['bdate']."'
					WHERE id='{$r['id']}'");
			}
			print date("d.m.Y",$r['tm'])." <a href='https://vk.com/id{$r['uid']}' target='_blank'>{$r['first_name']} {$r['last_name']}</a> / {$r['bdate']} / {$r['sex']} / {$r['city']} / {$r['country']}<br>";
		}
	} else {
		$res=$db->query("SELECT y,m,d,uid, COUNT(uid) AS cnt FROM vklist_scan_groups WHERE gid='$gid' GROUP BY y,m,d ORDER BY tm DESC",0);
		while($r=$db->fetch_assoc($res)) {
			$tm=mktime(0,0,0,$r['m'],$r['d'],$r['y']);
			print "<div class=''><a href='?vk_group_by_days=yes&for_date=$tm'>{$r['d']}.{$r['m']}.{$r['y']}</a> <span class='badge'>{$r['cnt']}</span></div>";
		}
	}
}


print "</div>";
$db->bottom();

?>
