<?
header("Location:https://winwinland.ru");
exit;
include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$vkt=new vkt("vkt");
$title="Вход";
include "../top.inc.php";
$email=$vkt->validate_email($_POST['email'])?$_POST['email']:false;
if($email) {
	if($uid=$vkt->dlookup("uid","cards","email='$email'")) {
		if($ctrl_id=$vkt->get_ctrl_id_by_uid($uid)) {
			$link=$vkt->get_ctrl_link($ctrl_id);
			print "<script>location='$link'</script>";
			print "<p><a href='$link' class='btn btn-primary' target=''>войти в CRM</a></p>";
		} else
			print "<p class='alert alert-danger' >Емэйл не найден (2)</p>";
	} else
		print "<p class='alert alert-danger' >Емэйл не найден (1)</p>";
} else {
	print "<p class='alert alert-danger' >Адрес эл почты указан в неверном формате</p>";
}


//include "../bottom.inc.php"; 
?>
