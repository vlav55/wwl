<?
include "./arr_m.inc.php";
foreach($arr_m AS $dir=>$r) {
	if(!file_exists($dir.'/') || isset($_GET['refresh'])) {
		if(mkdir($dir.'/') || isset($_GET['refresh'])) {
			copy("./0/index.php",$dir.'/index.php');
			file_put_contents($dir.'/pid.txt',$r['pid']);
		}
	}
}
$title="Медитации YOGAHELPYOU";
include "../lms_top.inc.php";
?>
<div class='container' >
	<p class='mb-4 mt-3' ><a href='../index.php?<?=$md5?>' class='' target=''>Все комплексы</a></p>
	<h1>Медитативные практики раджа-йоги</h1>
	


<style type="text/css">
ul {
    list-style-type: none;
}
</style>
<ul class="list-group mt-5 text-left" style="list-style-type: none;">
	<? foreach($arr_m AS $dir=>$r) { ?>
	<li class="list-group-item d-flex justify-content-between align-items-left" style="list-style-type: none;">
		<h3 class='text-secondary' >Медитация «<?=$r['title']?>»</h3>
		<a href='<?=$dir?>/?<?=$md5?>&num=<?=$dir?>' class='' target=''>
			<i class="fa fa-arrow-circle-right" style="font-size:36px"></i>
		</a>
	</li>
	<? } ?>
</ul>


<?
include "../lms_bottom.inc.php";
?>
