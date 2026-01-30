<?
	//~ include "/var/www/vlav/data/www/wwl/inc/lk_cabinet.1.inc.php";
	//~ exit;
define('DEMO',false);

include_once "/var/www/vlav/data/www/wwl/inc/db.class.php"; 
chdir("..");
include_once "init.inc.php";

if(!$fl_cabinet2) {
	include "/var/www/vlav/data/www/wwl/inc/lk_cabinet.1.inc.php";
} else
	include "/var/www/vlav/data/www/wwl/inc/cabinet2.1.inc.php";
?>
