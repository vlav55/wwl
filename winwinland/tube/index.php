<?
$video = substr(trim($_SERVER['QUERY_STRING']), 0, 128);
$poster_url = "https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/$video/poster.jpg";
$og_image = $poster_url;
$og_url = "https://winwinland.ru/tube/?<?=$video?>";
$title = "WinWinLand TUBE $video";
$descr = $title;
$video_url = "https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/$video/master.m3u8";

include "land_top.inc.php";
?>
<div id="warningMessage" class="alert alert-warning" style="display: none; text-align: center; margin-top: 20px;">
    Ошибка загрузки видео плэера. Попробуйте открыть <a href='<?="https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>' class='' target=''>эту ссылку</a> в другом браузере.
</div>

<div id="playerContainer">
    <!-- Container will be populated by JavaScript after checking image dimensions -->
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Playerjs === 'undefined') {
        // Show the warning message if Playerjs is not defined
        document.getElementById('warningMessage').style.display = 'block';
        document.getElementById('playerContainer').style.display = 'none';
        return;
    }
    
    var posterUrl = "<?=$poster_url?>";
    var videoUrl = "<?=$video_url?>";
    var playerContainer = document.getElementById('playerContainer');
    
    // Create a hidden image to check dimensions
    var img = new Image();
    img.onload = function() {
        var isVertical = this.height > this.width;
        var playerId = "player_" + Date.now();
        
        if (isVertical) {
            // Create vertical video container
            var verticalContainer = document.createElement('div');
            verticalContainer.style.cssText = "width: 100%; max-width: 360px; height: 640px; margin: 0 auto; background: #000;";
            
            var playerDiv = document.createElement('div');
            playerDiv.id = playerId;
            playerDiv.style.cssText = "width: 100%; height: 100%;";
            
            verticalContainer.appendChild(playerDiv);
            playerContainer.appendChild(verticalContainer);
            
            // Initialize player for vertical video
            var player = new Playerjs({
                id: playerId,
                file: videoUrl,
                poster: posterUrl,
                ratio: "9/16",
                autoplay: 0
            });
        } else {
            // Create horizontal video container
            var horizontalContainer = document.createElement('div');
            horizontalContainer.className = "youtube my-4";
            
            var playerDiv = document.createElement('div');
            playerDiv.id = playerId;
            
            horizontalContainer.appendChild(playerDiv);
            playerContainer.appendChild(horizontalContainer);
            
            // Initialize player for horizontal video
            var player = new Playerjs({
                id: playerId,
                file: videoUrl,
                poster: posterUrl,
                autoplay: 0
            });
        }
    };
    
    img.onerror = function() {
        // If image fails to load, default to horizontal layout
        var playerId = "player_" + Date.now();
        var horizontalContainer = document.createElement('div');
        horizontalContainer.className = "youtube my-4";
        
        var playerDiv = document.createElement('div');
        playerDiv.id = playerId;
        
        horizontalContainer.appendChild(playerDiv);
        playerContainer.appendChild(horizontalContainer);
        
        var player = new Playerjs({
            id: playerId,
            file: videoUrl,
            poster: posterUrl,
            autoplay: 0
        });
    };
    
    img.src = posterUrl;
});
</script>
<br><br><br>
<?
include "land_bottom.inc.php";
?>
