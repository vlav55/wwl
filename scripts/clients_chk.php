<?
$title="WWL";
include "../top.inc.php";
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";

$db=new vkt('vkt');
if(isset($_GET['disp_company'])) {
	$ctrl_id=intval($_GET['ctrl_id']);
	$company=$db->dlookup("company","0ctrl","id=$ctrl_id");

	?>
	<div class='container' >
	<h1><?=$company?></h1>
	<?
	$db->connect($db->get_ctrl_database($ctrl_id));
	$res=$db->query("SELECT * FROM lands WHERE del=0 AND land_txt!=''");
	while($r=$db->fetch_assoc($res)) {
		if($r['fl_partner_land']==1)
			$vid="🙋‍♀️";
		elseif($r['product_id']>0)
			$vid="📦";
		else
			$vid="⭐";
		$url=$r['land_url'];
		print "<h3>$vid <a href='$url' class='' target='_blank'>{$r['land_name']}</a></h3>";
	}
	?>
	</div>
	<?
	include "../bottom.inc.php";
	exit;
}

if(isset($_GET['tm_end_0ctrl_switch'])) {
	$ctrl_id=intval($_GET['ctrl_id']);
	$tm_end_0ctrl=$db->date2tm($_GET['dt_end_0ctrl_val']); //$db->dlookup("tm_end","0ctrl","id='$ctrl_id'");
	$tm_pay_end=$db->date2tm($_GET['dt_pay_end_val']);
	if($tm_end_0ctrl) {
		$db->query("UPDATE 0ctrl SET tm_end='$tm_end_0ctrl' WHERE id='$ctrl_id'");
	} else {
		$db->query("UPDATE 0ctrl SET tm_end=0 WHERE id='$ctrl_id'");
		if($tm_pay_end) {
			$avangard_id=intval($_GET['avangard_id']);
			//print "HERE_$avangard_id";
			$db->query("UPDATE avangard SET tm_end='$tm_pay_end' WHERE id='$avangard_id'");
		}
	}
}

print "<div class='container' >";
print "<h1>0ctrl list</h1>";
print "<p><a href='?view=yes' class='' target=''>View</a></p>";

$products=[30,31,32,33,34];
$res=$db->query("SELECT * FROM 0ctrl WHERE del=0");
print "<table class='table table-striped'>
	<thead>
		<tr>
			<th>#</th>
			<th>admin_name</th>
			<th>company</th>
			<th>last_pay</th>
		</tr>
	</thead>
	<tbody>";

$n=1;

$not_disp=[15,57,20,71,72,73,74];

while($r=$db->fetch_assoc($res)) {
	$ctrl_id=$r['id'];
	$ctrl_dir=$db->get_ctrl_dir($ctrl_id); 
	$ctrl_db=$db->get_ctrl_database($ctrl_id);
	$ctrl_link=$db->get_ctrl_link($ctrl_id);
	$uid=$r['uid'];
	$name=$db->dlookup("name","cards","uid='$uid'")." ".$db->dlookup("surname","cards","uid='$uid'");
	$mob=$db->dlookup("mob_search","cards","uid='$uid'");

	//~ $pay_end=$db->dlookup("tm_end","avangard"," res=1 AND vk_uid='$uid' AND
		//~ (product_id=30 OR product_id=31 OR product_id=32)");
	$tm_last_pay=$db->avangard_tm_last_pay($uid,$products);

	if($tm_last_pay<1690837200)
		continue;
	if(empty($r['company']))
		continue;
	if(in_array($ctrl_id,$not_disp))
		continue;



	$dt_last_pay=($tm_last_pay)?date("d.m.Y",$tm_last_pay):"-";

	$tm_pay_end=$db->avangard_tm_end($uid,$products);
	$avangard_id=$db->avangard_last_pay_id($uid,$products);
	$dt_pay_end_val=($tm_pay_end)?date("d.m.Y",$tm_pay_end):"-";
	$dt_pay_end="
				<input type='text' style='font-size:12px;width:80px;' id='text-input' name='dt_pay_end_val' value='$dt_pay_end_val'>
				<input type='hidden' name='avangard_id' value='$avangard_id'>
    		";

	$tm_end_0ctrl=$db->dlookup("tm_end","0ctrl","id='$ctrl_id'");
	$dt_end_0ctrl_val=$tm_end_0ctrl?date('d.m.Y',$tm_end_0ctrl):'-';
	$dt_end_0ctrl="
				<input type='text' style='font-size:12px;width:80px;' id='text-input' name='dt_end_0ctrl_val' value='$dt_end_0ctrl_val'>&nbsp;<button class='btn btn-info btn-sm' type='submit' name='tm_end_0ctrl_switch' value='yes'>Ok</button>
				<input type='hidden' name='ctrl_id' value='$ctrl_id'>
    		";

	$klid=$db->get_klid_by_uid($uid);
	$bc=$db->dlookup("bc","users","klid='$klid'");
	//print "klid=$klid uid=$uid bc=$bc<br>";
	$wwl_link=($bc)?"https://winwinland.ru/1/?bc=$bc":"";
	
	$tmp=$db->database;
	$db->connect($ctrl_db);
	if($vlav_klid=$db->dlookup("id","cards","telegram_id='315058329'")) {
		$vlav_bc="?bc=".$db->dlookup("bc","users","klid='$vlav_klid'");
	} else
		$vlav_bc="";
	$db->connect($tmp);

	print "<tr class='font18' >
		<td>$n</td>
		<td title='$mob'>$name</td>
		<td><a href='?disp_company=yes&ctrl_id={$r['id']}' class='' target='_blank'>{$r['company']}</a></td>
		<td>$dt_last_pay</td>
	</tr>";
	$n++;
}
print "</tbody></table></div>";
include "../bottom.inc.php";
?>
