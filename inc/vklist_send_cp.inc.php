<?

$menu=(isset($_GET['vsc_edit_msg']) || isset($_GET['vsc_do_edit_msg']) || isset($_GET['vsc_test']))?false:true;
$db=new top($database,"640px;",$menu,$favicon);
$bs=new bs;

if(@$_GET['vsc_test']) {
	if($db->userdata['access_level']>2) {
		print "<div class='alert alert-warning' >Access prohibited. min=2</div>";
		exit;
	}
	print $bs->button_close();
	print "<h2><div class='alert alert-info'>Тест рассылки отправлен на : <a href='https://vk.com/id$VK_OWN_UID' target='_blank'>https://vk.com/id$VK_OWN_UID</a></div></h2>";
	$msg=$db->dlookup("msg","vklist_groups","id={$_GET['group_id']}");
	print "<div class='well'>".nl2br($msg)."</div>";
	$vk=new vklist_api;
	$vk->token=$db->dlookup("token","vklist_acc","del=0 AND last_error=0 AND fl_acc_not_allowed_for_new=0");
	
	if($vk->token) {
		print "<div class='alert alert-success'>Код результата: ".$vk->vk_msg_send($VK_OWN_UID, $msg).". Проверьте сообщения в ВК.</div>";
	} else
		print "<div class='alert alert-danger'>Ошибка - нет доступных промо аккаунтов</div>";
	$db->bottom();
	exit;
}
if(@$_GET['vsc_do_edit_mgs']) {
	if($db->userdata['access_level']>2) {
		print "<div class='alert alert-warning' >Access prohibited. min=2</div>";
		exit;
	}
	print "<h2><div class='alert alert-info'>Записано</div></h2>";
	$db->query("UPDATE vklist_groups SET msg='".$db->escape($_GET['msg'])."' WHERE id={$_GET['group_id']}",0);
	sleep(1);
	print "<script>opener.location.reload();window.close()</script>";
	$db->bottom();
	exit;
}
if(@$_GET['vsc_cancel']) {
	print "<script>window.close()</script>";
}
if(@$_GET['vsc_edit_msg']) {
	print $bs->button_close();
	print "<h2><div class='alert alert-info'>Редактивать сообщение рассылки</div></h2>";
	print "<form>";
	print "<div class='form-group'><textarea class='form-control' name='msg' rows='7'>".$db->dlookup("msg","vklist_groups","id={$_GET['group_id']}")."</textarea></div>";
	print "<button type='submit' class='btn btn-primary' name='vsc_do_edit_mgs' value='yes'>Сохранить</button>&nbsp;";
	print "<button type='submit' class='btn btn-default' name='vsc_cancel' value='yes'>Отменить</button>";
	print "<input type='hidden' name='group_id' value='{$_GET['group_id']}'>";
	print "</form>";
	$db->bottom();
	exit;
}

$s=new vklist_send;
$s->connect($database);
if($db->userdata['access_level']==1)
	print "<div class=''><a href='vklist.php'>Все рассылки</a></div>";
$s->vklist_send_cp();


$db->bottom();

?>
