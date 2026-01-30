<?
$db=new top($database,0,false);
$db->connect("vktrade");
$bs=new bs;
print "<div class='alert alert-info' ><h2>Ретаргетинг ВК</h2></div>";

if(isset($_GET['removecab'])) {
	$db->query("UPDATE customers SET ad_token='',ad_cabinet='0',ad_target_group='' WHERE  id='$customer_id' ");
}

$r=$db->fetch_assoc($db->query("SELECT * FROM customers WHERE id='$customer_id'"));
$token=$r['ad_token'];
$cab_id=$r['ad_cabinet'];
$grp=$r['ad_target_group'];
$razdel_list=$r['ad_razdel'];
$tm_from=$r['ad_dt_from'];
$dt_from=($r['ad_dt_from'])?date("d.m.Y",$r['ad_dt_from']):"0";

if(isset($_GET['newcab'])) {
	$db->ad_get_vk_token(123,$database);
}

if(empty($token) ) {
	print "<div class='alert alert-danger' >Рекламный кабинет не подключен!</div>";
	print $bs->button_href($text="Поключить рекламный кабинет", $href="javascript:wopen_1(\"?newcab=yes\")", $style="primary");
	$db->bottom();
	exit;
} else {
	print "<div>".$bs->button_href($text="Поключить другой кабинет", $href="javascript:wopen_1(\"?removecab=yes\")", $style="default")."</div>";
}



if(isset($_GET['attach_cab'])) {
	$cab_id=intval($_GET['cab_id']);
	$db->query("UPDATE customers SET ad_cabinet='$cab_id',ad_target_group=0,ad_razdel='' WHERE id='$customer_id'");
	print "<div class='alert alert-info' >Рекламный кабинет подключен</div>";
}
if(isset($_GET['set_target'])) {
	//$db->print_r($_GET);
	if(isset($_GET['grp_targ'])) {
		$grp=intval($_GET['grp_targ']);
		$db->query("UPDATE customers SET ad_target_group='$grp' WHERE  id='$customer_id'");
		$razdel_list="";
		foreach($_GET['razdel'] AS $val)
			$razdel_list.=$val.",";
		//print $razdel_list;
		$tm_from=$db->date2tm($_GET['dt_from']);
		$dt_from=($r['ad_dt_from'])?date("d.m.Y",$tm_from):"0";
		$db->query("UPDATE customers SET ad_razdel='".$db->escape($razdel_list)."',ad_dt_from='$tm_from' WHERE  id='$customer_id'",0);
	}
}
if(isset($_GET['do_new_grp_targ'])) {
	$name=trim($_GET['new_grp_targ']);
	if(strlen($name)>3) {
		$vk=new vklist_api($token);
		$res=$vk->ad_create_target_group($name,$cab_id);
		if(isset($res['response']))
			print "<div class='alert alert-success' >Аудитория ретаргетинга <b>$name</b> создана!</div>";
		else
			$db->print_r($res);
	} else {
		print "<div class='alert alert-danger' >Название аудитории слишком короткое!</div>";
	}
}


$vk=new vklist_api($token);
$res=$vk->ad_get_cabinets();
//$db->print_r($res);
if(isset($res['error'])) {
	print "<div class='alert alert-danger' >Error : {$res['error']['error_code']} : {$res['error']['error_msg']}</div>";
	print $bs->button_href($text="Поключить рекламный кабинет", $href="javascript:wopen_1(\"?newcab=yes\")", $style="primary");
	$db->bottom();
	exit;
}
print $bs->table(array("ID","Название","Роль","Подключить"));
foreach($res['response'] AS $r) {
	if($r['account_status']!=1)
		continue;
	$c=($r['account_id']==$cab_id)?"success":"";
	print "<tr class='$c' >
			<td>{$r['account_id']}</td>
			<td>{$r['account_name']}</td>
			<td>{$r['access_role']}</td>
			<td>".$bs->button_href($text="Поключить", $href="?attach_cab=yes&cab_id={$r['account_id']}", $style="primary btn-xs")."</td>
		</tr>";
}
print "</table>";

if($cab_id) {
	$res=$vk->ad_get_target_groups($cab_id);
	if(isset($res['error'])) {
		print "<div class='alert alert-danger' >Error of getting target groups: {$res['error']['error_code']} : {$res['error']['error_msg']}</div>";
		$db->bottom();
		exit;
	}
	print "<h2>Аудитории ретаргетинга</h2>";
	print "<div class='well' >Укажите аудиторию ретаргетинга и отметьте разделы, клиенты из которых будут автоматически добавляться в эту группу.</div>";
	print "<div class='well well-sm'>
			<form class='form-inline' >
			  <div class='form-group'>
				<label for='new_grp_targ'>Создать аудиторию:</label>
				<input type='text' class='form-control' id='new_grp_targ' name='new_grp_targ' value=''>
				<button type='submit' class='btn btn-default' name='do_new_grp_targ' value='yes'>Создать</button>
			  </div>
			</form>
			</div>";
	print "<form>";
	print $bs->table(array("Выбор","ID","Название","Размер"));
	foreach($res['response'] AS $r) {
		$checked=($grp==$r['id'])?"checked":"";
		$c=($grp==$r['id'])?"success":"";
		print "<tr class='$c' >
			<td><input type='radio' name='grp_targ' value='{$r['id']}' $checked></td>
			<td>{$r['id']}</td>
			<td>{$r['name']}</td>
			<td>{$r['audience_count']}</td>
			</tr>";
	}
	print "</table>";
	
	$db->connect($database);
	$razd_arr=explode(",",$razdel_list);
	//$db->print_r($razd_arr);
	$res=$db->query("SELECT * FROM razdel WHERE del=0 ORDER BY razdel_name");
	print $bs->table(array("Выбор","Раздел"));
	while($r=$db->fetch_assoc($res)) {
		$checked="";
		foreach($razd_arr AS $val) {
			if($val==$r['id']) {
				$checked="checked";
				break;
			}
		}
		print "<tr>
				<td><input type='checkbox' name='razdel[]' value='{$r['id']}' $checked></td>
				<td>{$r['razdel_name']}</td>
			</tr>";
	}
	print "</table>";

	print "<div class='well well-sm' >Выгружать с даты последей активности (0-выгружать все):<input id='dt_from'  class='form-control_ text-center' type='text' name='dt_from' value='$dt_from' onfocus='this.select();lcs(this)' onclick='event.cancelBubble=true;this.select();lcs(this)'></div>";

	print "<button type='submit' name='set_target' value='yes' class='btn btn-primary'>Установить</button>";
	print "</form>";

	//CHECKING
	$n=0;
	foreach($razd_arr AS $razdel) {
		if(!intval($razdel))
			continue;
		//print "razdel=$razdel\n";
		$res=$db->query("SELECT msgs.uid AS uid FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						WHERE cards.del=0 AND razdel='$razdel' AND msgs.tm>'$tm_from'
						GROUP BY msgs.uid
						",0);
		while($r=$db->fetch_assoc($res)) {
			$n++;
		}
	}
	print "<div>Всего по этим условиям на данный момент в аудиторию ретаргетинга будет выгружено: <span class='badge' >$n</span></div>";
	///

}
if(sizeof($razd_arr)!=0)
	print "<div class='alert alert-success' >Готово. Настройки изменены.
		Выбранная аудитория ретаргетинга автоматически пополняется из указанных разделов (с периодичностью 1 раз/час).
		Теперь вы можете настроить в рекламном кабинете ВК показ рекламы,
		указав в настройках рекламной кампании <b>Дополнительные параметры - Аудитории ретаргетинга</b> выбранную  аудиторию.
		</div>";
//$db->print_r($res);

$db->bottom();


?>
