<?
include_once "/var/www/vlav/data/www/wwl/inc/yclients.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/cashier.class.php";
$salon_id=997441; //'1504761';
$y=new yclients($salon_id);
$uid=$y->get_uid_yclients();
$ctrl_id=$y->get_wwl_acc($uid);
chdir($y->get_ctrl_path($ctrl_id));
include("init.inc.php");
$c=new cashier($database,$ctrl_id,$ctrl_dir);
$cashier_setup_url=$c->get_cashier_setup_url();
$cashier_url=$c->get_cashier_url();
print "salon_id=$salon_id ctrl_id=$ctrl_id uid=$uid cashier_url=$cashier_url <br><br>";



$n=1;

$phone='79006464263';
//$res = $y->get_clients($phone);
//$res=$y->get_card_types($salon_id);
//$res=$y->issue_card($phone, $card_number='12345', $card_type_id='99288');
//print $card_id= $y->get_cards_by_phone($phone, $card_type_id);

//print $card_type_id=$y->get_card_type_id();
$client_id=$y->get_client_id($phone);
print "HERE_$client_id";
//~ $card_id=$y->get_card_by_client_id($client_id,$card_type_id);
//~ $res=$y->manual_card_transaction($card_id, $amount=1000, $comm = "test");
//$res=$y->cashback_withdraw($phone,$sum='1234',$card_number='1111111');
//$res=$y->get_company(1504761);
//$res=$y->send_payment_webhook($salon_id, $payment_sum=1000, $tm_period_to=(time()+(7*24*60*60)), $currency_iso = "RUB", $tm_payment = null, $tm_period_from = null);
$y->print_r($res);
exit;
//
//$res=del($salon_id);
$y->print_r($res);

function del($salon_id) {
	$y=new yclients($salon_id);
	$uid=$y->get_uid_yclients();
	$ctrl_id=$y->get_wwl_acc($uid);
	$ctrl_db=$y->get_ctrl_database($ctrl_id);
	if($uid && $ctrl_id) {
		$y->connect('vkt');
		$y->query("DELETE FROM 0ctrl_tools WHERE ctrl_id='$ctrl_id' AND tool='yclients'");
		$y->query("DELETE FROM cards_add WHERE uid='$uid' AND par='yclients'");
	}
	print "ok $uid $ctrl_id $ctrl_db yclients acc deleted";
}

?>
