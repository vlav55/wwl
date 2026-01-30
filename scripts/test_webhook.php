<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "/var/www/vlav/data/www/wwl/inc/lava.class.php";
$db=new db('vkt');
// API Key
$apiKey = 'NQYv5NebjrkwdfbYzQteK6IXWpocaKIUYVcFg818fLRwJPsgpmViKtheKnKefDVk';

$db->notify_me("test_webhook_1 ");
$jsonData = file_get_contents('php://input');
if($jsonData) {
	// Decode the JSON data into a PHP associative array
	$data = json_decode($jsonData, true);
	file_put_contents("test_webhook.log",print_r($data,true));
	$db->notify_me("test_webhook_2 ");
	print "webhook Ok";
	exit;
}
print "OK";
?>
