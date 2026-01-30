<?
include "../api.class.php";
if($_SERVER['REQUEST_METHOD']==='GET') {
	$pid=(isset($_GET['product_id']) && intval($_GET['product_id'])) ? $db->dlookup("id","product","id=".intval($_GET['product_id'])) : 0;
	if(!$pid) {
		print json_encode(['error'=>'product_id not found']);
		http_response_code(400);
		exit;
	}
	http_response_code(200);
	print json_encode($arr);
	exit;
?>
