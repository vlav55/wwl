<?
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt('vkt');
include "insales_app_credentials.inc.php";
include "insales_func.inc.php";
$db->print_r(insales_get_order($order_id=127504014));
?>
