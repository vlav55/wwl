<?
$secret = 'CVVkeu7HXWGuuB7oq';
$path = parse_url($m3u8, PHP_URL_PATH);
$expires = time() + (3*60*60);
$link = "$expires$path $secret";
$md5 = md5($link, true);
$md5 = base64_encode($md5);
$md5 = strtr($md5, '+/', '-_');
$md5 = str_replace('=', '', $md5);
$par = "?md5={$md5}&expires={$expires}";
$url= "https://cdn.yogahelpyou.com".$path.$par;
if(!isset($my_video_id))
	$my_video_id=0;
?>
<!--
    <link href="https://vjs.zencdn.net/7.17.0/video-js.css" rel="stylesheet">
    <script src="https://vjs.zencdn.net/7.17.0/video.js"></script>
-->
	<link href="https://unpkg.com/video.js/dist/video-js.min.css" rel="stylesheet">
	<script src="https://unpkg.com/video.js/dist/video.min.js"></script>
	<script src="https://yogahelpyou.ru/1/lms/video/videojs-quality-selector-hls.min.js"></script>

    <style>
        .video-js {
            position: relative;
            width: 1280px; /* Adjust video player width */
            height: 720px; /* Adjust video player height */
        }

        .vjs-big-play-button {
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%);
            z-index: 1; /* Ensure play button appears above video controls */
        }
    </style>

<? if($tm_end>time() || $first_pid==0 || strpos($url, "%D0%A2%D0%91/master.m3u8") )  { ?>

    
    <!-- Video Player Container -->
<div class="embed-responsive embed-responsive-16by9 video-wrapper">
    <video id="my-video<?=$my_video_id?>" class="video-js vjs-default-skin embed-responsive-item" controls preload="auto" oncontextmenu="return false">
        <source src="<?=$url?>" type="application/x-mpegURL">
    </video>
</div>

	<?if($first_pid==0) {?>
	<div class='mt-4 card p-3' >
	<div class="embed-responsive embed-responsive-1by1 video-wrapper mx-auto" style="width:180px;">
		<video id="my-video-add" class="video-js vjs-default-skin embed-responsive-item" controls preload="auto" oncontextmenu="return false">
			<source src="https://cdn.yogahelpyou.com/promo/lms_no_access/master.m3u8"
			type="application/x-mpegURL">
		</video>
	</div>
	<p class='pt-3 pb-1 mb-1 text-center' ><a href='https://yogahelpyou.ru/3/' class='' target='_blank'>Как попасть на пробное занятие</a></p>
	</div>
	<? } ?>

	<div class='my-4' >
		<?if($uid) {?>
		<p class='font-weight-bold' >Вы можете задать любой вопрос экспертам курса, либо связаться с техподдержкой, одним из следующих способов:</p>
		В телеграм по ссылке <a href='https://t.me/yogahelpyou_bot' class='' target=''>https://t.me/yogahelpyou_bot</a> <br>
		В ВК по ссылке <a href='https://vk.me/yogahelpyou' class='' target=''>https://vk.me/yogahelpyou</a> <br>
		В вотсап на номер: <a href='https://wa.me/79916782085' class='' target=''>79916782085</a> <br>
		Или на емэйл: info@yogahelpyou.com <br>
		<? } ?>
	</div>
<?
} else {
	print "<div class='alert alert-info mt-3' >$client_name - извините, у вас нет доступа. ($first_pid)";
	switch($first_pid) {
		case 1: $buy_link="https://yogahelpyou.ru/1/"; break;
		case 70:
		case 71:
		case 72:
		case 73:
		 $buy_link="https://yogahelpyou.ru/4/"; break;
		case 13: $buy_link="https://yogahelpyou.ru/3/"; break;
		case 1011: $buy_link="https://yogahelpyou.ru/116/"; break;
		case 1012: $buy_link="https://yogahelpyou.ru/117/"; break;
		case 1013: $buy_link="https://yogahelpyou.ru/118/"; break;
		case 1014: $buy_link="https://yogahelpyou.ru/119/"; break;
		case 1015: $buy_link="https://yogahelpyou.ru/120/"; break;
		case 1016: $buy_link="https://yogahelpyou.ru/121/"; break;
		case 1017: $buy_link="https://yogahelpyou.ru/122/"; break;
		case 103: $buy_link="https://yogahelpyou.ru/123/"; break;
		case 104: $buy_link="https://yogahelpyou.ru/124/"; break;
		default:
			$buy_link="https://yogahelpyou.ru/order.php?s=0&t=0&product_id=$first_pid&land_num=0";
	}
	$descr=$db->dlookup("descr","product","id='$first_pid'");
	print "<p class='my-4 font-weight-bold' >Узнать подробнее о видео курсе «".$descr."» и приобрести его можно 
	<a href='$buy_link' class='' target='_blank'>по ссылке</a>
	</p>";
	?>
	<div class="embed-responsive embed-responsive-1by1 video-wrapper mx-auto" style="width:180px;">
		<video id="my-video<?=$my_video_id?>" class="video-js vjs-default-skin embed-responsive-item" controls preload="auto" oncontextmenu="return false">
			<source src="https://cdn.yogahelpyou.com/promo/lms_no_access/master.m3u8"
			type="application/x-mpegURL">
		</video>
	</div>
	<?
	print "<p class='py-3 text-center' ><a href='https://yogahelpyou.ru/3/' class='' target=''>Пробное занятие</a></p>";
	if($tm_end>0)
		print "<div class='text-secondary font14 pl-3' >окончание ".date("d.m.Y H:i",$tm_end)."</div>";
	print "</div>";
}
?>

    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var player = videojs('my-video<?=$my_video_id?>', {
                controlBar: {
                    children: [
                        'playToggle',
                        'currentTimeDisplay',
                        'progressControl',
                        'durationDisplay',
                        'volumePanel',
                        'fullscreenToggle',
                        'playbackRate'
                    ]
                }
            });

			player.qualitySelectorHls();

            // Enable seeking by ±10 seconds with left and right arrow keys
            document.addEventListener('keydown', function(e) {
                if (e.keyCode === 37) { // Left arrow key
                    player.currentTime(player.currentTime() - 10);
                } else if (e.keyCode === 39) { // Right arrow key
                    player.currentTime(player.currentTime() + 10);
                }
            });
            player.ready(function() {
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var player = videojs('my-video-add', {
                controlBar: {
                    children: [
                        'playToggle',
                        'currentTimeDisplay',
                        'progressControl',
                        'durationDisplay',
                        'volumePanel',
                        'fullscreenToggle',
                        'playbackRate'
                    ]
                }
            });

            // Enable seeking by ±10 seconds with left and right arrow keys
            document.addEventListener('keydown', function(e) {
                if (e.keyCode === 37) { // Left arrow key
                    player.currentTime(player.currentTime() - 10);
                } else if (e.keyCode === 39) { // Right arrow key
                    player.currentTime(player.currentTime() + 10);
                }
            });
            player.ready(function() {
            });
        });
    </script>
