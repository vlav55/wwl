<?
//создать на вашу почту пользователя и дать полные Права на раздел Расширения
include "../go.php";
exit;

include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt('vkt');
include "insales_app_credentials.inc.php";
include "../insales_func.inc.php";

//$db->print_r($_GET);

//$db->print_r(insales_get_account());
print "тест начисления бонуса:  ";
print_r (insales_bonus_create($client_id=85599207, $amount=1234, $descr='Бонус при регистрации'));

print "<br>webhook_id=". insales_get_webhook($insales_id);
exit;

$ctrl_dir=$db->get_ctrl_dir($ctrl_id);
$url="https://for16.ru/d/$ctrl_dir/insales_webhook.php";
insales_webhook_create($url,'orders/update');


?>
