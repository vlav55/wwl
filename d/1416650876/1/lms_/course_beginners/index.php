<?
$arr_first_pid=[39,40,41,42,43];
for($n=0; $n<=4; $n++) {
	$dir=$n;
	if(!file_exists($dir.'/') || isset($_GET['refresh'])) {
		if(mkdir($dir.'/') || isset($_GET['refresh'])) {
			copy("./0.php",$dir.'/index.php');
			$pid_str=$arr_first_pid[$n].",".file_get_contents("./pid.txt");
			file_put_contents($dir.'/pid.txt',$pid_str);
		}
	}
}

include "../lms_top.inc.php";
?>
<div class='container' >
	<h1>Курс &#171;Классическая йога для начинающих&#187;</h1>
	


<style type="text/css">
ul {
    list-style-type: none;
}
</style>
<ul class="list-group mt-5 text-left" style="list-style-type: none;">
	<li class="list-group-item d-flex justify-content-between align-items-left" style="list-style-type: none;"><h3 class='text-secondary' >Комплекс для начинающих №0</h3><a href='0/?<?=$md5?>&num=0' class='' target=''><i class="fa fa-arrow-circle-right" style="font-size:36px"></i></a></li>
	<li class="list-group-item d-flex justify-content-between align-items-left" style="list-style-type: none;"><h3 class='text-secondary' >Комплекс для начинающих №1</h3><a href='1/?<?=$md5?>&num=1' class='' target=''><i class="fa fa-arrow-circle-right" style="font-size:36px"></i></a></li>
	<li class="list-group-item d-flex justify-content-between align-items-left" style="list-style-type: none;"><h3 class='text-secondary' >Комплекс для начинающих №2</h3><a href='2/?<?=$md5?>&num=2' class='' target=''><i class="fa fa-arrow-circle-right" style="font-size:36px"></i></a></li>
	<li class="list-group-item d-flex justify-content-between align-items-left" style="list-style-type: none;"><h3 class='text-secondary' >Комплекс для начинающих №3</h3><a href='3/?<?=$md5?>&num=3' class='' target=''><i class="fa fa-arrow-circle-right" style="font-size:36px"></i></a></li>
	<li class="list-group-item d-flex justify-content-between align-items-left" style="list-style-type: none;"><h3 class='text-secondary' >Комплекс для начинающих №4</h3><a href='4/?<?=$md5?>&num=4' class='' target=''><i class="fa fa-arrow-circle-right" style="font-size:36px"></i></a></li>
</ul>


<?
include "../lms_bottom.inc.php";
?>
