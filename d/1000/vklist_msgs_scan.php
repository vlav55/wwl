#!/usr/bin/php -q
<?
include "/var/www/vlav/data/www/wwl/inc/vklist_send.class.php";
include "init.inc.php";
include "/var/www/html/pini/1info/vkt/scripts/vk/filter_msgs.inc.php";
$m=new m1;
$m->database=$database;
$m->db200=$DB200; //need for url in notifications
$m->telegram_bot=$TELEGRAM_BOT;
$m->razdel_do_not_notify=$razdel_do_not_notify;
$m->scan();
?>
