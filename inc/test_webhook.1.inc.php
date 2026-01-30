<?
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "init.inc.php";
$db=new top($database,"Test webhook",false);
if(!$uid=intval($_GET['uid'])) {
	print "<p class='alert alert-warning' >uid required</p>";
	exit;
}

$task_key = 'last_executed_time';

if (isset($_SESSION[$task_key])) {
    $time_since_last_execution = (time() - $_SESSION[$task_key]);

    if ($time_since_last_execution < 60) {
        echo "This task can only be executed once every minute. Please wait for ".(60-$time_since_last_execution)." seconds.";
		$db->bottom();
        exit;
    }
}
if(isset($_GET['go'])) {
	$url=mb_substr($_GET['url'],0,128);
	$action=mb_substr($_GET['action'],0,128);
	$order_id=intval($_GET['order']);
	$db->vkt_send_msg_order_id=$order_id;
	$db->ctrl_id=$ctrl_id;
	$arr=$db->get_webhook_data($uid,$action);
	$response = $db->send_webhook($url,$arr);

	print "<p class='alert alert-success' >Webhook sent to $url - ";

	if ($response === false) {
		print('cURL Error: ' . curl_error($ch));
	} else {
		print('Response: ' . $response);
	}
	print "</p>";
	print "<br><p>Example of code to receive webhook (php)</p>";
	print "<div class='card p-3 my-4 bg-light' >".(nl2br(htmlspecialchars(
		"\$jsonData = file_get_contents('php://input');
		if(\$jsonData) {
			// Decode the JSON data into a PHP associative array
			\$data = json_decode(\$jsonData, true);
			file_put_contents('your_log_file.log',print_r(\$data,true));
			print 'ok'; //response to server
		}
		")))."</div>";

	print "<p>Below is what was sent:</p>";
	print "<div class='card p-3 my-4' >";
	$db->print_r($arr);
	print "</div>";
	$_SESSION[$task_key] = time();
}
?>
<div class="container mt-5">
	<h2>Test webhook for uid=<?=$uid?></h2>
	<form action="" method="GET">
		<div class="form-group">
			<label for="url">URL:</label>
			<input type="url" class="form-control" id="url" name="url" value='<?=$url?>' required>
		</div>
		<div class="form-group">
			<label for="action">Action (32 characters max):</label>
			<input type="text" class="form-control" id="action" name="action" maxlength="32" value='<?=$action?>' required>
		</div>
		<div class="form-group">
			<label for="action">Last payed orders:</label>
			<SELECT class="form-control" id="order" name="order" >
				<?$res=$db->query("SELECT * FROM avangard WHERE amount>0 AND res=1 AND vk_uid=$uid ORDER BY id DESC LIMIT 50");
				print "<option value='0'>-not considered-</option> \n";
				while($r=$db->fetch_assoc($res)) {
					print "<option name='order_id' value='{$r['order_id']}'>{$r['order_number']} {$r['order_descr']} {$r['amount']}</option> \n";
				}
				?>
			</SELECT>
		</div>
		<input type='hidden' name='uid' value='<?=$uid?>' >
		<button type="submit" class="btn btn-primary" name='go' avlue='yes'>Send</button>
	</form>
</div>
<?
$db->bottom();
?>
