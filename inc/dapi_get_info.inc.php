<?
$order_num=$_POST['order'];
$uid=(isset($_POST['client_uid'])) ? $db->dlookup("uid","cards","del=0 AND uid='".$db->get_uid($_POST['client_uid'])."'") : 0;
$uid=(!$uid && isset($_POST['client_phone'])) ? $db->dlookup("uid","cards","del=0 AND mob_search='".$db->check_mob($_POST['client_phone'])."'") : $uid;
$uid=(!$uid && isset($_POST['client_email'])) ? $db->dlookup("uid","cards","del=0 AND email='".$db->escape(trim($_POST['client_email']))."'") : $uid;

//print "uid=$uid"; exit;

$db->vkt_send_msg_order_id=$db->dlookup("order_id","avangard","order_number='".$db->escape($order_num)."'");
$db->ctrl_id=$ctrl_id;
$arr=$db->get_webhook_data($uid,$action);
print(json_encode($arr));
$dapi_msg="ok ". $db->uid_md5($uid)."\n";
//print $dapi_msg;
file_put_contents("dapi.log",$dapi_msg,FILE_APPEND);
exit;
?>
