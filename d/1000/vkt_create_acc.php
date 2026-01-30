<?
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "init.inc.php";

$t=new top('vkt',"vkt_create_acc",false);
$db=new vkt('vkt');
$uid=intval($_GET['uid']);
if($_SESSION['access_level']<=3 && $db->database=='vkt' && $uid) {
	if(isset($_GET['do'])) {
		if(!$ctrl_id=$db->get_ctrl_id_by_uid($uid) ) {
			$ctrl_id=$db->vkt_create_account($uid,$product_id=0);
			print "<p class='alert alert-success' >Аккаунт создан $ctrl_id</p>";
		} else
			print "<p class='alert alert-info' >Аккаунт существует $ctrl_id</p>";
	} else
		print "<p class='text-center my-5' ><a href='?do=yes&uid=$uid' class='' target=''>Создать аккаунт ВВЛ</a> для $uid
			<br> после нажатия дождаться конца загрузки)
			</p>";
} else
	print "<p class='alert alert-danger' >Error </p>";
print "<br>Ok";
$t->bottom();
?>
