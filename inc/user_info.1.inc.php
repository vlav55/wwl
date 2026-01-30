<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "init.inc.php";
$db=new top($database,"640px;",false);
print "<div class='container card bg-light p-3' >";
$user_id=intval($_GET['user_id']);
$klid=$db->dlookup("klid","users","id='$user_id'");
$bc=$db->dlookup("bc","users","id='$user_id'");
$uid=$db->dlookup("uid","cards","id='$klid'");
$link_senler="https://vk.com/app5898182_-212991289#s=$senler_gid_land&utm_term=$bc";
if($user_id && $_GET['mode']==1) {
	$r=$db->fetch_assoc($db->query("SELECT * FROM users WHERE id='$user_id'",0));
	$dt_style="style='font-weight:normal;'";
	$dd_style="style='margin-left:40px;'";
	print "	<dl class='dl-horizontal_' style='padding:5px; font-size:20px; color:#555;' >
			<dt $dt_style >ID партнера</dt>
				<dd $dd_style><span class='badge bg-danger text-white' ><b>$user_id</b></span></dd>
			<dt $dt_style >Код партнера</dt>
				<dd $dd_style><b>$klid</b></dd>
			<dt $dt_style >Логин</dt>
				<dd $dd_style><b>{$r['username']}</b></dd>
			<dt $dt_style>Имя</dt>
				<dd $dd_style><b>".$db->disp_name_cp($db->dlookup("name","cards","uid='$uid'")." ".$db->dlookup("surname","cards","uid='$uid'"))."</b></dd>
			<dt $dt_style>Найти в CRM</dt>
				<dd $dd_style><a href='msg.php?uid=$uid' class='' target='_blank'>$DB200/msg.php?uid=$uid</a></dd> 
			</dl>
	";
} elseif($user_id && $_GET['mode']==2) {
	$r=$db->fetch_assoc($db->query("SELECT * FROM users WHERE id='$user_id'",0));
	$dt_style="style='font-weight:normal;'";
	$dd_style="style='margin-left:40px;'";
	print "	<dl class='dl-horizontal_' style='padding:5px; font-size:20px; color:#555;' >
			<dt $dt_style >ID менеджера</dt>
				<dd $dd_style><span class='badge bg-danger text-white' ><b>$user_id</b></span></dd>
			<dt $dt_style >Код менеджера</dt>
				<dd $dd_style><b>$klid</b></dd>
			<dt $dt_style >Логин</dt>
				<dd $dd_style><b>{$r['username']}</b></dd>
			<dt $dt_style>Имя</dt>
				<dd $dd_style><b>".$db->disp_name_cp($db->dlookup("name","cards","uid='$uid'")." ".$db->dlookup("surname","cards","uid='$uid'"))."</b></dd>
			<dt $dt_style>Найти в CRM</dt>
				<dd $dd_style><a href='msg.php?uid=$uid' class='' target='_blank'>$DB200/msg.php?uid=$uid</a></dd>
			</dl>
	";
} elseif($_GET['mode']==3) {
	$uid=intval($_GET['user_id']);
	$res=$db->query("SELECT * FROM avangard WHERE vk_uid='$uid' AND res=1",0);
	$dt_style="style='font-weight:normal;'";
	$dd_style="style='margin-left:40px;'";
	print "	<dl class='dl-horizontal_' style='padding:5px; font-size:20px; color:#555;' >
			<dt $dt_style>Имя</dt>
				<dd $dd_style><b>".$db->disp_name_cp($db->dlookup("name","cards","uid='$uid'")." ".$db->dlookup("surname","cards","uid='$uid'"))."</b></dd>
			<dt $dt_style>Найти в CRM</dt>
				<dd $dd_style><a href='msg.php?uid=$uid' class='' target='_blank'>$DB200/msg.php?uid=$uid</a></dd>
			</dl>
	";

	while($r=$db->fetch_assoc($res)) {
		$dt=date("d.m.Y",$r['tm']);
		print "<div class='card p-3 bg-info text-white' >
			<p class='badge' >$dt</p>
			<p>{$r['pay_system']}</p>
			<p>{$r['order_number']}</p>
			<p>{$r['order_descr']}</p>
			<p class='font-weight-bold' >{$r['amount']} р.</p>
		</div>";
	}
}
print "</div>";
$db->bottom();
?>
