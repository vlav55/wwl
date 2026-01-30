<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db('vkt');
$db->vkt_email("script /var/www/vlav/data/www/wwl/d/insales_webhook.php not found or unable to stat",print_r($GLOBALS,true));
//$db->notify_me("TEST");
print "ok";
?>
