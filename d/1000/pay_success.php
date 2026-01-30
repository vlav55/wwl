<?
include_once "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "init.inc.php";
$title="Успешная оплата";
include "land_top.inc.php";
?>
<div class='container' >
	<?if(file_exists("tg_files/logo.jpg")) {?>
	<p class='text-center my-2 py-2' ><img src='tg_files/logo.jpg' class='img' style='width:200px;' ></p>
	<?}?>
	<h1 class='text-center text-success ' >Успешная оплата</h1>
</div>

<?
include "land_bottom.inc.php";

?>
