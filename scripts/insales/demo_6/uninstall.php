<?
//~ Array
//~ (
    //~ [shop] => myshop-cpc885.myinsales.ru
    //~ [token] => e0ba59ec2ed785b2152c1fbfa3b48a9c
    //~ [insales_id] => 5790531
//~ )
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt('vkt');
include "insales_app_credentials.inc.php";
include "../insales_func.inc.php";

insales_webhook_del(insales_get_webhook($insales_id));
http_response_code(200);

?>
