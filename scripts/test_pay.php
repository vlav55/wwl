<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
chdir("../d/1000/"); //wwl
//chdir("../d/1110503342/"); //244
include "init.inc.php";
$c=new db($database);
$_SESSION['csrf_token_order'] = bin2hex(random_bytes(32));
$promocode='WinWinLand4243';
$phone='79119841012';
$_POST = [
    'csrf_token_order'=>$_SESSION['csrf_token_order'],
    'product_id'=>1,
    'sku'=>'',
    'phone'=>$phone,
    'email'=>'',
    'fio'=>'',
    'city'=>'',
    'comm'=>'',
    'comm_pay_cash'=>'',
    'tm_pay_cash'=>'',
    'land_num'=>3,
    'bc'=>$promocode,
    'vk_uid'=>0,
    'sum_disp'=>10,
    'promocode_id'=>0,
    'fee_pay'=>0, //pay by fee/ Not implemented yet
    'tzoffset'=>0,
];
$pay_system='test';
include_once "/var/www/vlav/data/www/wwl/inc/pay_common.1.inc.php";
print "<br>order_id=$order_id";
include_once "/var/www/vlav/data/www/wwl/inc/pay_callback_common.1.inc.php";

print "<br>Ok $order_id";
?>
