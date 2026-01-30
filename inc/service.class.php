<?php
$INC_PATH="/var/www/vlav/data/www/wwl/inc";
include_once ($INC_PATH."/simple_db.inc.php");
@session_start();
include "db.class.php";
include "func.inc.php";
include "vklist_api.inc.php";
class service extends db{
	function opencart_log($uid,$domen) {
		print "<p><a href='msg.php?uid=$uid'>Back to the messenger</a></p>";
		$res=$this->query("SELECT * FROM opencart_log WHERE uid=$uid ORDER BY tm DESC");
		$day=0;
		while($r=mysql_fetch_assoc($res)) {
			if($day!=date("d",$r['tm'])) {
				$day=date("d",$r['tm']);
				print "<hr>";
			}
			$dt=date("d.m.Y H:i",$r['tm']);
			print "$dt | <a href='$domen?{$r['url']}' target='_blank'>{$r['title']}</a><br>";
		}
		//$id=$this->dlookup("id","cards","uid=$uid");
		//print "<script>opener.location='cp.php?view=yes&uid=$uid#r_$id';</script>";
	}
}

?>
