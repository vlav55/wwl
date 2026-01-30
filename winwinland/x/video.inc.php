<?
if(!isset($video_id))
	$video_id=1;
if(isset($poster))
	$poster="poster='$poster'";
?>

<link href="https://unpkg.com/video.js/dist/video-js.min.css" rel="stylesheet">
<script src="https://unpkg.com/video.js/dist/video.min.js"></script>
<script src="https://yogahelpyou.ru/1/lms/video/videojs-quality-selector-hls.min.js"></script>

<!-- Video Player Container -->
<style>
	.video-js {
		position: relative;
		width: 1280px; /* Adjust video player width */
		height: 720px; /* Adjust video player height */
	}

	.vjs-big-play-button__ {
		position: absolute !important;
		top: 50% !important;
		left: 50% !important;
		transform: translate(-50%, -50%);
		z-index: 1; /* Ensure play button appears above video controls */
		background-color: rgba(0, 0, 0, 0.3) !important; /* Цвет фона кнопки */
		color: #fff !important; /* Цвет текста */
		border-radius: 50% !important; /* Округленные края */
		width: 70px !important; /* Ширина кнопки */
		height: 70px !important; /* Высота кнопки */
		font-size: 60px !important; /* Размер шрифта */
	}

</style>

<div class="embed-responsive embed-responsive-16by9 video-wrapper">
    <video id="my-video<?=$video_id?>"
		class="video-js vjs-default-skin embed-responsive-item"
		controls preload="auto"
		oncontextmenu="return false"
		<?=$poster?>
		>
        <source src="<?=$url?>" type="application/x-mpegURL">
    </video>
</div>
   
<script>
	document.addEventListener('DOMContentLoaded', function() {
		var player = videojs('my-video<?=$video_id?>');
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
