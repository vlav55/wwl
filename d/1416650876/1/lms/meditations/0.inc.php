<?
include "../arr_m.inc.php";
$num=intval($_GET['num']);
$m3u8=$arr_m[$num]['hls'];
include "../../lms_top.inc.php";
?>
<div class='container' >
	<p class='mb-4 mt-3' ><a href='../index.php?<?=$md5?>' class='' target=''>Все медитации</a></p>
	<h1>Медитативная практика «<?=$arr_m[$num]['title']?>»</h1>
	<?
	include "../../video.inc.php";
	?>
</div>
<?
include "../../lms_bottom.inc.php";
?>
