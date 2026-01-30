<?
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
include "init.inc.php";
$title=$company_name;
include "land_top.inc.php";

$db=new vkt($ctrl_db);

	?>
	<div class='container' >
	<div class='text-center mt-5' ><img src='<?=$company_logo?>' class='img-fluid' style='margin:0 auto;' alt=''></div>
	<h1 class='text-center' ><?=$company_name?></h1>
	<?
	$res=$db->query("SELECT * FROM lands WHERE del=0 AND land_txt!='' AND fl_not_disp_in_cab=0");
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
	include "land_bottom.inc.php";
?>
