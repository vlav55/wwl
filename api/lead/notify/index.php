<?
include "../../api.class.php";
if($_SERVER['REQUEST_METHOD']==='POST') {
	if(!isset($_POST['uid']) || !$db->get_uid($_POST['uid'])) {
		print json_encode(['error'=>'uid missing or incorrect']);
		http_response_code(400);
		exit;
	}
	$uid=$db->get_uid($_POST['uid']);
	$msg=mb_substr($_POST['msg'],0,4096);
	$db->notify($uid,$msg);
	print json_encode(['success'=>'mesage sent to: '. $uid]);
	http_response_code(200);
	exit;
}
