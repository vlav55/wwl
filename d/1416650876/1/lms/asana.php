<?
$grant_all=true;
include "lms_top.inc.php";
print "<h4 class='text-center' >Каталог асан</h4>";
$asana=trim($_GET['asana']);
if(!array_key_exists($asana,$asanas)) {
	print "<p class='alert alert-warning' >Асана: $asana не найдена</p>";
	include "lms_bottom.inc.php";
	exit;
}
print "<div class='container' >";
print "<h1 class='text-center text-danger' >$asana</h1>";
print "<h2 class='text-center' >Описание</h2>";
print "<p>".nl2br($asanas[$asana]['DESCR'])."</p>";
print "<h2 class='text-center' >Особенности</h2>";
print "<p>".nl2br($asanas[$asana]['FEAT'])."</p>";
print "<h2 class='text-center' >Упрощение</h2>";
print "<p>".nl2br($asanas[$asana]['SIMPLE'])."</p>";
print "<h2 class='text-center' >Видео</h2>";
$m3u8=$asanas[$asana]['M38U'];
include "video.inc.php";

print "</div>";
include "lms_bottom.inc.php";
?>
