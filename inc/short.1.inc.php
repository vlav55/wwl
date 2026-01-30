<?
include_once "/var/www/vlav/data/www/wwl/inc/cashier.class.php";
include "init.inc.php";
$db=new cashier($database,$ctrl_id,$ctrl_dir);
if($r=$db->resolve_short_link(key($_GET))) {
	$err=true;
	if(isset($r['m']) && $db->check_mob($r['m'])) {
		$mob=$db->check_mob($r['m']);
		$err=false;
	}
	if(isset($r['e']) && $db->validate_email($r['e'])) {
		$email=trim($r['e']);
		$err=false;
	}
	if(isset($r['uid']) && intval($r['uid'])) {
		$uid=intval($r['uid']);
		$err=false;
	}
	if(!$err && $uid) {
		$pars=$db->get_init_pars();
		$land_num=$pars['land_num_1'];
		$tm=time()-(24*60*60);
		$db->query("DELETE FROM telegram WHERE tm<'$tm'");
		$tg_code=rand(100,99999);
		$n=10000;
		while($db->dlookup("id","telegram","code='$tg_code'")) {
			$tg_code=rand(100,99999);
			if(!$n--)
				break;
		}
		$db->query("INSERT INTO telegram SET
				tm='".time()."',
				uid='$uid',
				code='$tg_code',
				user_id='$land_num',
				confirmed='3'
				");
		header("Location: https://t.me/$tg_bot_msg_name?start=".$tg_code);
	} else
		print "link is not correct";
} else
	print "link is expired or not correct";
?>
