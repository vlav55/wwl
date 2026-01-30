<?
include "../../api.class.php";
if($_SERVER['REQUEST_METHOD']==='POST') {
	if($uid=$db->get_uid($_POST['uid'])) {
		$bank_details=mb_substr($_POST['bank_details'],0,2048);
		if($klid=$db->dlookup("id","cards","uid='$uid'")) {
			$db->query("UPDATE users SET bank_details='".$db->escape($bank_details)."' WHERE klid='$klid'");
			http_response_code(200);
			print json_encode(['uid'=>$db->uid_md5($uid)]);
		} else {
			http_response_code(400);
			print json_encode(['error'=>'it is not partner']);
		}
	} else {
		http_response_code(400);
		print json_encode(['error'=>'uid not found']);
	}
	exit;
}
?>
