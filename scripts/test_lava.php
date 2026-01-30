<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "/var/www/vlav/data/www/wwl/inc/lava.class.php";
$db=new db('vkt');
// API Key
$apiKey = 'NQYv5NebjrkwdfbYzQteK6IXWpocaKIUYVcFg818fLRwJPsgpmViKtheKnKefDVk';
$_POST['email']="va@winwinland.ru";
$_POST['go_submit']="yes";
$_POST['sum_disp']=1230;
$lava = new Lava($apiKey);
//~ $products = $lava->get_products();
//~ $db->print_r($products);

chdir("../d/3090726665/");
//chdir("../d/1000/");
include "init.inc.php";
$db=new db($database);
print "HERE_".	$order_id=$db->get_next_avangard_orderid();


include "/var/www/vlav/data/www/wwl/inc/pay_lava.1.inc.php";

$res=$lava->get_invoice('va@winwinland.ru', 'd4726459-1217-4155-a40e-36ba294ffbdf');
$db->print_r($res);
//print "<script>location='$url'</script>";
print $lava->err;

?>
