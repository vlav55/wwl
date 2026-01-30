<?
//создать на вашу почту пользователя и дать полные Права на раздел Расширения

//~ include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
//~ $db=new vkt('vkt');
//~ include "insales_app_credentials.inc.php";
//~ include "../insales_func.inc.php";

include "../go.php";

//$db->print_r(insales_get_account());
//print_r (insales_bonus_create($client_id=85752031, $amount=1234, $descr='Бонус при регистрации'));

exit;

$ctrl_dir=$db->get_ctrl_dir($ctrl_id);
$url="https://for16.ru/d/$ctrl_dir/insales_webhook.php";
insales_webhook_create($url,'orders/update');


?>
