<?
$num=($_GET['num']);
if($num=='beginners')
	$m3u8="https://306db23b-28a8-4747-9e82-03c375b47fa8.selstorage.ru/nidra/nidra_beginners/master.m3u8";
if($num=='advanced_1')
	$m3u8="https://306db23b-28a8-4747-9e82-03c375b47fa8.selstorage.ru/nidra/nidra_advanced%231/master.m3u8";
if($num=='advanced_2')
	$m3u8="https://306db23b-28a8-4747-9e82-03c375b47fa8.selstorage.ru/nidra/nidra_advanced%232/master.m3u8";

include "../lms_top.inc.php";
?>
<div class='container' >
	<p class='mb-4 mt-3' ><a href='index.php?<?=$md5?>' class='' target=''>Курс для продолжающих</a></p>
	<h1>Практика глубокого расслабления <b><?=$num?></b></h1>
	<?
	include "../video.inc.php";
	?>

	<h3 class='mt-5' >
		<i>Желаю хорошей практики йоги! <br>
		Викторов А.В.</i>
	</h3>
</div>
<?
include "../lms_bottom.inc.php";
?>
