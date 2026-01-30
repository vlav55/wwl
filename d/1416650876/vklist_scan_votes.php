#!/usr/bin/php -q
<?
include "/var/www/vlav/data/www/wwl/inc/vklist_send.class.php";
include "init.inc.php";

$vs=new vklist_scan_votes;
$vs->database=$database;
$vs->group_id="-".$VK_GROUP_ID;
$vs->target_group_id=$retarketing_target_group_id;
$vs->mode=$scan_votes_mode;  //0-add new to vklist; 1 -add to cards
$vs->razdel_exclude=$razdel_exclude;

$vs->scan_votes($vs->cli());



?>