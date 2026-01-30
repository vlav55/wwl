#!/usr/bin/php -q
<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db('vkt');
$db->telegram_bot="vkt";
$db->db200="https://1-info.ru/vkt/db";
$db->vktrade_send_tg_bot='vkt_manager_bot';

include "/var/www/vlav/data/www/wwl/inc/delay_notify.inc.php";
print "ok\n";
?>
