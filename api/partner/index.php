<?
include "../api.class.php";

if($_SERVER['REQUEST_METHOD']==='GET') {
	$direct_code=(isset($_GET['direct_code']) && !empty($_GET['direct_code'])) ?substr($_GET['direct_code'],0,32) : "";
	$login=(isset($_GET['login']) && !empty($_GET['login'])) ?substr($_GET['login'],0,32) : "";
	$passw=(isset($_GET['passw']) && !empty($_GET['passw'])) ?substr($_GET['passw'],0,32) : "";
	if(!empty($direct_code)) {
		if($klid=$db->dlookup("klid","users","del=0 AND direct_code='$direct_code'")) {
			$uid=$db->dlookup("uid_md5","cards","id='$klid'");
			$access_level=$db->dlookup("access_level","users","klid='$klid'");
			http_response_code(200);
			print(json_encode(['uid'=>$uid,'access_level'=>$access_level]));
		} else {
			http_response_code(400);
			print (json_encode(['error'=>'direct_code not match']));
		}
	} elseif(!empty($login) && !empty($passw)) {
		$md5=md5($passw);
		if($klid=$db->dlookup("klid","users","del=0 AND username='$login' AND passw='$md5' AND fl_allowlogin=1")) {
			$uid=$db->dlookup("uid_md5","cards","id='$klid'");
			$access_level=$db->dlookup("access_level","users","klid='$klid'");
			http_response_code(200);
			print(json_encode(['uid'=>$uid,'access_level'=>$access_level]));
		} else
			http_response_code(400);
			print (json_encode(['error'=>'login or password not match']));
	} else {
		http_response_code(400);
		print json_encode(['error'=>'input data incorrect']);
	}
	exit;
}

?>
