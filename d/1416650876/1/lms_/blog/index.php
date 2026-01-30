<?
include "seminar_cut.inc.php";
include "../lms_top.inc.php";

?>
<div class='container' >
	<p class='mb-4 mt-3' ><a href='../?<?=$md5?>' class='' target=''>Все курсы</a></p>
	<h1 class='text-center' >База знаний по йоге</h1>
	<p class='text-center' ><a href='https://yogahelpyou.ru/3/' class='' target='_blank'>Как попасть на пробное занятие</a></p>

	<?
		$title="Семинар по классической йоге";
		$m3u8="promo/seminar/master.m3u8";
	?>
	<h2><?=$title?></h2>
<!--
	<p>Семинар по йоге длительностью два часа с Андреем Викторовым и Владимиром Авштолисом</p>
	<p>На семинаре подробно рассказывается что такое йога, чем она отличается от физкультуры, какие эффекты можно ожидать для здоровья и как правильно заниматься йогой дома.
	Также затрагиваются аспекты восьмичастной йоги Патанджали и рассматривается связь с первоисточникам.
	</p>
-->
	<p>Рекомендуем к просмотру, если вы всерьез интересуетесь йогой и здоровьем.</p>
	<ul class="list-group mt-5 text-left" style="list-style-type: none;">
		<li class="list-group-item d-flex justify-content-between align-items-left" style="list-style-type: none;">
			<h3 class='text-secondary' >Запись семинара</h3>
			<a href='disp_video.php?<?=$md5?>&title=<?=$title?>&hls=<?=$m3u8?>' class='' target=''>
				<i class="fa fa-arrow-circle-right" style="font-size:36px"></i>
			</a>
		</li>
	</ul>


	<h2>Нарезка - фрагменты еще одного семинара по йоге</h2>
	<ul class="list-group mt-5 text-left" style="list-style-type: none;">
	<?
		foreach($seminar_cut AS $key=>$r) {
			?>
			<li class="list-group-item d-flex justify-content-between align-items-left" style="list-style-type: none;">
				<h3 class='text-secondary' ><?=$r['title']?></h3>
				<a href='disp_video.php?<?=$md5?>&title=<?=$r['title']?>&hls=<?=$r['url']?>' class='' target=''>
					<i class="fa fa-arrow-circle-right" style="font-size:36px"></i>
				</a>
			</li>
			<?

		}
	?>
	</ul>
	
</div>
<?
include "../lms_bottom.inc.php";
?>
