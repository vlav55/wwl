<?
	if(isset($_GET['id'])) {
		if(!intval($_GET['id']))
			exit;
	} else
		exit;
	include "top.inc.php";
	print "<div class='container' >";
	?>
		<div class='text-left mt-3' ><a href='javascript:window.close()' class='' target=''>
			<button type="button" class="btn btn-warning">Закрыть</button>
		</a></div>

	<?
	print "<div class='mt-5' ><img src='../1/images/logo.png'></div>";
	$id=intval($_GET['id']);

	$r=$db->fetch_assoc($db->query("SELECT * FROM refs WHERE id='$id'"));
	print "<div class='text-center' >
	<img src='../1/images/ref_pics/{$r['pic']}'
	class='rounded-circle'
	style='width:135px; height:auto ;'>
	</div>";

	print "<div class='refs_w-h1 text-center' >".nl2br($r['ref_name'])."</div>";
	print "<div class='refs_w-age text-center' >= ".nl2br($r['age'])." лет =</div>";
	print "<div class='refs_w-descr' >".nl2br($r['ref_text'])."</div>";
	print "</div>";
	include "bottom.inc.php";
?>
