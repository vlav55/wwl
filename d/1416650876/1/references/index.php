<?
$title="Отзывы о занятиях классической йогой YOGAHELPYOU";
chdir("..");
include "top.inc.php";
print "<div class='p-5' ><a href='/' class='' target=''><img src='https://for16.ru/d/1416650876/1/images/logo.png' alt='logo' class='img-fluid' ></a></div>\n";
$uid=$_SESSION['vk_uid'];
$res=$db->query("SELECT * FROM refs_new WHERE del=0");
print "<div class='p-3' >\n";
$n=4;
while($r=$db->fetch_assoc($res)) {
	if($n==4)
		print "<div class='row' >\n";
		
	print "<div class='col-sm-6 col-md-3 text-center pb-5' >
			<div class='' ><a href='{$r['dir']}/' class='' target=''><img src='{$r['dir']}/pic.jpg' class='img-thumbnail'></a></div>
			<div class='mt-2 font-weight-bold PT text-secondary' >{$r['first_name']} {$r['last_name']}</div>
			<div> <span class='PT text-secondary' >возраст:</span> <span class='ROBOTO text-info font24' > {$r['age']}</span></div>
			<!--<div>{$r['brief']}</div>-->
			<div><small><a href='{$r['dir']}/' class='' target=''>смотреть</a></small></div>
		</div>\n";
		
	if($n--==1) {
		print "</div>\n"; $n=4;
	}
}
if($n!=4) {
	for($i=$n; $i>0; $i--)
		print "<div class='col-sm-3' >
			&nbsp;
			</div>\n";
	print "</div>\n";
}
print "</div>\n";

print "<div class='text-center mt-5' ><a href='/#refs_main' class='' target=''>Больше отзывов</a></div>";
print "<div class='text-center mt-3' ><a href='https://www.youtube.com/playlist?list=PLnAhj0coCijTJlOvu9g6cPBtxdDCYtBtN' class='' target=''>Видео отзывы</a></div>";

include "../bottom.inc.php";
?>
