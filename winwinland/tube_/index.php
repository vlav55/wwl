<?
$video=substr(trim($_SERVER['QUERY_STRING']),0,128);
$og_image="https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/$video/poster.jpg";
$og_url="https://winwinland.ru/tube/?<?=$video?>";
$title="WinWinLand TUBE $video";
$descr=$title;
include "land_top.inc.php";
?>
<div id="warningMessage" class="alert alert-warning" style="display: none; text-align: center; margin-top: 20px;">
    Ошибка загрузки видео плэера. Попробуйте открыть <a href='<?="https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>' class='' target=''>эту ссылку</a> в другом браузере.
</div>
<div id="playerContainer">
	<div class="youtube my-4">
		<div id="player"></div>
	</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Playerjs === 'undefined') {
        // Show the warning message if Playerjs is not defined
        document.getElementById('warningMessage').style.display = 'block';
        // Optionally hide the player container
        document.getElementById('playerContainer').style.display = 'none';
    } else {
	   var player = new Playerjs({id:"player",
		   file:"https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/<?=$video?>/master.m3u8",
		   poster:"https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/<?=$video?>/poster.jpg"
		   });
    }
});
</script>
<br><br><br>
<?
include "land_bottom.inc.php";
?>
