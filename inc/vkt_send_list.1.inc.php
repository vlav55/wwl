<?
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
include "init.inc.php";

$t=new top($database,'Рассылка');

if($_SESSION['username']!='vlav') {
	//~ print "ведутся технические работы";
	//~ $t->bottom();
	//~ exit;
}

$db=new vkt_send($database);

if(isset($_GET['mode'])) {
	$_SESSION['vkt_send_list_mode']=intval($_GET['mode']);
}
if(!isset($_SESSION['vkt_send_list_mode']))
	$_SESSION['vkt_send_list_mode']=1;
$active=[1=>'',2=>'',3=>'',];

if(!isset($_SESSION['filter_land_num']))
	$_SESSION['filter_land_num']=0;
if(isset($_GET['set_filter_land'])) {
	$_SESSION['filter_land_num']=intval($_GET['filter_land_num']);
}
if(!isset($_SESSION['filter_sid']))
	$_SESSION['filter_sid']=0;
if(isset($_GET['filter_sid'])) {
	$_SESSION['filter_sid']=intval($_GET['filter_sid']);
}

switch($_SESSION['vkt_send_list_mode']) {
	case 1:
		$active[1]='active';
		$where="sid=0 AND land_num=0";
		break;
	case 2:
		$active[2]='active';
		$w_add=($_SESSION['filter_land_num'])?"land_num={$_SESSION['filter_land_num']}":"1";
		$where="sid=0 AND land_num>0  AND $w_add";
		break;
	case 3:
		$active[3]='active';
		$w_add=($_SESSION['filter_sid'])?"sid={$_SESSION['filter_sid']}":"1";
		$w_add.=($_SESSION['filter_land_num'])?" AND land_num={$_SESSION['filter_land_num']}":" AND 1";
		$where="(sid=-1 OR sid>0) AND $w_add";
		break;
}

if(isset($_GET['add_new'])) {
	switch($_SESSION['vkt_send_list_mode']) {
		case 1:
			$db->query("INSERT INTO vkt_send_1 SET tm='".time()."',name_send='Новая рассылка'");
			break;
		case 2:
			if($last_land_num=$db->dlast("land_num","lands","del=0 AND tm_scdl>0")) {
				$db->query("INSERT INTO vkt_send_1 SET tm='".time()."',land_num='$last_land_num',name_send='Новая рассылка'");
			} else
				print "<p class='alert alert-danger' >У вас нет ни одного лэндинга с установленным временем мероприятия. Сначала создайте мероприятие, затем можно будет привязать к нему рассылку.</p>";
			break;
		case 3:
			$db->query("INSERT INTO vkt_send_1 SET tm='".time()."',land_num='$last_land_num',sid='-1',name_send='Новая рассылка',del='1'");
			//~ if($last_land_num=$db->dlast("land_num","lands","del=0 AND tm_scdl>0")) {
				//~ $db->query("INSERT INTO vkt_send_1 SET tm='".time()."',land_num='$last_land_num',sid='-1',name_send='Новая рассылка',del='1'");
			//~ } else
				//~ print "<p class='alert alert-danger' >У вас нет ни одного лэндинга с установленным временем мероприятия. Сначала создайте мероприятие, затем можно будет привязать к нему рассылку.</p>";
			break;
	}
	if($new_id=$db->insert_id()) 
		print "<script>location='?view=yes&ok=1&msg=Новая рассылка добавлена&id=$new_id'</script>";
	//~ else
		//~ print "<script>location='?view=yes&ok=0&msg=Ошибка. Обратитесь к разработчикам&id=0'</script>";
}
if(isset($_GET['msg'])) {
	$s=!intval($_GET['ok'])?'danger':'success';
	print "<p class='alert alert-$s' >".substr($_GET['msg'],0,128)."</p>";
}
if(isset($_GET['del'])) {
	$vkt_send_id=intval($_GET['id']);
	$name_send=$db->dlookup("name_send","vkt_send_1","id='$vkt_send_id'");
	print "<p class='alert alert-warning' >Удалить рассылку <b>$name_send</b> ?
	<a href='?id=$vkt_send_id&do_del=yes' class='btn btn-danger btn-sm' target=''>Подтвердить</a>
	<a href='?cancel=yes' class='btn btn-info btn-sm' target=''>нет</a>
	</p>";
}
if(isset($_GET['do_del'])) {
	$db->chk_column('vkt_send_1', 'fl_cashier', 'tinyint', $index = true);
	if($db->dlookup("id","vkt_send_1","id='$vkt_send_id' AND fl_cashier=0")) {
		$vkt_send_id=intval($_GET['id']);
		$db->query("DELETE FROM vkt_send_1 WHERE id='$vkt_send_id'");
		$db->vkt_send_task_del($vkt_send_id,$ctrl_id,0);
		print "<p class='alert alert-info' >Удалено</p>";
	} else
		print "<p class='alert alert-warning' >Рассылка используется системой Лояльность 2.0, удаление невозможно</p>";
}
if(isset($_GET['mode3_start'])) {
	$vkt_send_id=intval($_GET['id']);
	$db->query("UPDATE vkt_send_1 SET del=0 WHERE id='$vkt_send_id'");
}
if(isset($_GET['mode3_pause'])) {
	$vkt_send_id=intval($_GET['id']);
	$db->query("UPDATE vkt_send_1 SET del=1 WHERE id='$vkt_send_id'");
}

if($_SESSION['vkt_send_list_mode']==1)
	$help="<a  class='' href='https://help.winwinland.ru/docs/rassylka/' class='' target='_blank'><i class='fa fa-question-circle' ></i></a>";
elseif($_SESSION['vkt_send_list_mode']==2)
	$help="<a  class='' href='https://help.winwinland.ru/docs/rassylka-po-meropriyatiyu/' class='' target='_blank'><i class='fa fa-question-circle' ></i></a>";
elseif($_SESSION['vkt_send_list_mode']==3)
	$help="<a  class='' href='https://help.winwinland.ru/docs/rassylka-po-sobytiyu/' class='' target='_blank'><i class='fa fa-question-circle' ></i></a>";

print "<h2>Рассылки $help</h2>";

?>
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
  <li role="presentation" class="<?=$active[1]?> nav-item"><a  class='nav-link <?=$active[1]?>' href="?mode=1" >Разовая</a></li>
  <li role="presentation" class="<?=$active[2]?> nav-item"><a class='nav-link <?=$active[2]?>' href="?mode=2">По мероприятию</a></li>
  <li role="presentation" class="<?=$active[3]?> nav-item"><a class='nav-link <?=$active[3]?>' href="?mode=3">По событию</a></li>
</ul>
<?

print "<br><div>
	<a href='?add_new=yes' class='btn btn-primary' target=''>Новая</a>
	<a href='javascript:location.reload();' class='btn btn-warning' target=''>Обновить список</a>
	</div>
	";

if($_SESSION['vkt_send_list_mode']==2 || $_SESSION['vkt_send_list_mode']==3) {
	print "<div>
	<form>
	Фильтр по лэндингу: <select name='filter_land_num' class='form-control' style='display:inline;width:300px;'>";
	$res=$db->query("SELECT * FROM lands WHERE del=0 ORDER BY land_num");
	print "<option value='0'>не установлено</option>";
	while($r=$db->fetch_assoc($res)) {
		$sel=($_SESSION['filter_land_num']==$r['land_num'])?"SELECTED":"";
		print "<option value='{$r['land_num']}' $sel>{$r['land_num']} {$r['land_name']}</option>";
	}
	print "</select>
	<button type='submit' class='btn btn-warning btn-sm'  name='set_filter_land' value='yes'>Установить</button>
	</form>
	</div>";
}
if($_SESSION['vkt_send_list_mode']==3) {
	print "<div>
	<form>
	Фильтр по событию: <select name='filter_sid' class='form-control' style='display:inline;width:300px;'>";
	$res=$db->query("SELECT * FROM sources WHERE del=0");
	print "<option value='0'>не установлено</option>";
	while($r=$db->fetch_assoc($res)) {
		if(!in_array($r['id'],$vkt_send_sid_arr))
			continue;
		$sel=($_SESSION['filter_sid']==$r['id'])?"SELECTED":"";
		print "<option value='{$r['id']}' $sel>{$r['id']} {$r['source_name']}</option>";
	}
	print "</select>
	<button type='submit' class='btn btn-warning btn-sm'  name='set_filter_land' value='yes'>Установить</button>
	</form>
	</div>";
}


$res=$db->query("SELECT * FROM vkt_send_1 WHERE $where ORDER BY tm DESC LIMIT 100",0); 
print "<table class='table table-striped table-hover' >
	<thead><tr>
		<th>Дата создания</th>
		<th>ID</th>
		<th>Название</th>
		<th>Время рассылки</th>
		<th>В очереди</th>
		<th>ред</th>
		<th>копир</th>
		<th>удал</th>
		<th>Разослано</th>
		<th>Результат</th>
	</tr></thead>
	<tbody>
";
while($r=$db->fetch_assoc($res)) {
	$vkt_send_id=intval($r['id']);
	$dt=date('d.m.Y H:i',$r['tm']);
	$del=($r['del'])?"Выполнено":"В обработке";
	$dublicate="<a title='скопировать в новую' href='javascript:wopen(\"vkt_send.php?vkt_send_id=$vkt_send_id&dublicate=yes\")' class='' target='' $disabled><span class='	fa fa-copy'></span></a>";
	$edit=(!$r['del'])?"<a title='НАСТРОИТЬ' href='javascript:wopen(\"vkt_send.php?vkt_send_id=$vkt_send_id&view=yes\")' class='' target='' $disabled><span class='fa fa-edit font20' ></span></a>":"<span class='fa fa-edit' disabled></span>";
	$cnt=$db->fetch_assoc($db->query("SELECT COUNT(uid) AS cnt FROM vkt_send_log WHERE vkt_send_id='$vkt_send_id' AND (res_vk=1 OR res_tg=1 OR res_email=1 OR res_wa=1)",0))['cnt'];
	$dt_scdl=($r['vkt_send_tm']>0)?date('d.m.Y H:i',$r['vkt_send_tm']):'-';
	$dt_scdl_land=$r['land_num']?"<a href='$DB200/{$r['land_num']}' class='' target='_blank' title=''>{$r['land_num']}</a>":"любой лэндинг";
	$dt_scdl=($_SESSION['vkt_send_list_mode']==3)?$db->print_time_shift($r['tm_shift'])." с момента наступления события: <b>".
		$db->dlookup("source_name","sources","id='{$r['sid']}'")."</b> на лэндинге: <b>
			".$dt_scdl_land."
		</b>":$dt_scdl;
	if($db->vkt_send_task_chk($vkt_send_id,$ctrl_id)) {
		$planned="<span class='	fa fa-check-circle' ></span>";
	} else {
		if($_SESSION['vkt_send_list_mode']==1 && $r['del']==1)
			$planned="<span class='badge p-1 bg-warning' >выполнено</span>";
		else
			$planned="не запланир";
	}
	if ( $_SESSION['vkt_send_list_mode']==3 ) {
		$edit="<a title='НАСТРОИТЬ' href='javascript:wopen(\"vkt_send.php?vkt_send_id=$vkt_send_id&view=yes\")' class='' target='' $disabled><span class='fa fa-edit font20' ></span></a>";
		if($r['del']) {
			$del="на паузе";
			$planned="<a href='?mode3_start=yes&id=$vkt_send_id' class='' target='' title='запустить'>на паузе</a>";
		} else {
			$planned="<a href='?mode3_pause=yes&id=$vkt_send_id' class='' target='' title='остановить'>выполняется</a>";
		}
	}
	print "<tr>
		<td title='$vkt_send_id'>$dt</td>
		<td><span class='badge' >$vkt_send_id</span></td>
		<td title='$vkt_send_id'>{$r['name_send']}</td>
		<td>$dt_scdl</td>
		<td>$planned</td>
		<td>$edit</td>
		<td>$dublicate</td>
		<td><a href='?del=yes&id=$vkt_send_id' class='' target=''><span class='	fa fa-trash' ></span></a></td>
		<td>$cnt <span class='fa fa-signal' ></span></td>
		<td>$del</td>
	</tr>";
}
print "</tbody></table>";

$t->bottom();
?>
