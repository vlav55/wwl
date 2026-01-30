<?
$ip=$_SERVER['REMOTE_ADDR'];
if(!in_array($ip,['80.88.53.239','95.167.31.126','2.56.204.5','176.15.164.192','78.37.219.44'])) {
	print "Access denied";
	exit;
}
?>
