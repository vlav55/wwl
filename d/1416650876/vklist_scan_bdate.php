#!/usr/bin/php -q
<? 
include "/var/www/vlav/data/www/wwl/inc/vklist_send.class.php";
include "init.inc.php";
$serv=new vklist_bdate;
$serv->run($customer_id);

?>
