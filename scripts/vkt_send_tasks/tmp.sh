#!/usr/bin/php -q
<?
include '/var/www/vlav/data/www/wwl/inc/vkt_send.class.php';
$db=new vkt_send('vkt');
$db->ctrl_id=170;
$db->pact_secret='';
$db->pact_company_id=0;
$db->vkt_send_task_0ctrl(1,170,-1097,1769786087,0);
unlink ('task_1_170_-1097.php');
?>
			