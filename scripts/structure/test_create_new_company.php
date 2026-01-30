<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db('vkt');
include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$vkt=new vkt('vkt');

$uid=-1002;

$passw=$vkt->passw_gen($len=10);
$real_name=$db->dlookup("name","cards","uid='$uid'").' '.$db->dlookup("surname","cards","uid='$uid'");
$email=$db->dlookup("email","cards","uid='$uid'");

if(!$ctrl_id=$vkt->get_ctrl_id_by_uid($uid)) {
	$ctrl_id=$vkt->create_ctrl_company($uid);
	//print "ctrl_id=$ctrl_id <br>";
	$vkt->create_ctrl_dir($ctrl_id);
	$vkt->create_ctrl_databases($ctrl_id);
	$ctrl_dir=$vkt->get_ctrl_dir($ctrl_id);
	$ctrl_db=$vkt->get_ctrl_database($ctrl_id);
	print "<br>".$vkt->get_ctrl_link($ctrl_id)."<br>";

	$vkt->connect($ctrl_db);
	$vkt->query("UPDATE users SET
		passw='".md5($passw)."',
		real_user_name='".$vkt->escape($real_name)."',
		email='".$vkt->escape($email)."',
		comm='".$vkt->escape($passw)."'
		WHERE username='admin'");
	//$db->email($emails=array("vlav@mail.ru"), "WWL new company ctrl_id=$ctrl_id CREATED", "", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
} else {
	//print "Company exists - $ctrl_id <br>";
}

$db->connect('vkt');
$db->query("UPDATE 0ctrl SET admin_passw='$passw' WHERE id='$ctrl_id'");
print "Ok uid=$uid ctrl_id=$ctrl_id dir=$ctrl_dir db=$ctrl_db";
?>
