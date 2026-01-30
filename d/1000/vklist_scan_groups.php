#!/usr/bin/php -q
<? //VKTRADE
include "/var/www/vlav/data/www/wwl/inc/vklist_send.class.php";
include "init.inc.php";


$gc=new vklist_group_chk;
$gc->database=$database;
$gc->friends_uid=$DO_NOT_TOUCH_FRIENDS; //

$gc->add_to_vklist_if_from_spb_only=$add_to_vklist_if_from_spb_only;
$gc->add_if_city_only=$add_if_city_only; //
$gc->add_if_country_only=$add_if_country_only; //VK country_id RUSSIA=1
$gc->add_if_sex_only=$add_if_sex_only; //'M' or 'F'
$gc->add_if_city_or_country_not_specified=$add_if_city_or_country_not_specified; //


$gc->target_group_id=$retarketing_target_group_id;
$gc->mode=$scan_groups_mode; //0- add to vklist, 1- to cards
$gc->VK_GROUP_ADDED=$VK_GROUP_ADDED;
$gc->delay_if_notif=$delay_if_notif; //7x24x60x60 - update vklist for sending if last sent was before

//RUN
$gc->group_chk($VK_GROUP_ID);


?>
