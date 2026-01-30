<div class="s_bottom_  p-3 w-75 mx-auto" style='font-size:14px; margin-top:160px;'>
	<hr>
	<div class='s_bottom_card' >
		<div class='row' >
			<div class='col-sm-0 s_bottom_card_b' >
			</div>
			<div class='col-sm-0' >
			</div>
			<div class='col-sm-12 text-center text-secondary' >
				<div class='s_bottom_card_ip' >&copy; <?=date("Y")?> ИП Авштолис В.И. ИНН 380506954258
					Россия, г.Санкт-Петербург
				</div>
				<div class='s_bottom_card_text_'>
					Копирование материалов сайта без разрешения запрещено.
				</div>
				<div class='s_bottom_card_href'>
				<a href='../1/privacypolicy.pdf' target='_blank'>Политика конфиденциальности</a> |
				<a href='../1/dogovor.pdf' target='_blank'>Пользовательское соглашение</a> |
				<a href='../1/contacts.pdf' target='_blank'>Контактные данные</a>
				</div>
			</div>
		</div>
	</div>
</div>

</div>

<script>
// SINGLE initialization for ALL videos on the page
(function() {
    // Store all video IDs that need initialization
    var videoIds = [];
    
    // Check which videos exist on the page
    if(document.getElementById('my-video0')) videoIds.push('my-video0');
    if(document.getElementById('my-video1')) videoIds.push('my-video1');
    if(document.getElementById('my-video-add')) videoIds.push('my-video-add');
    
    console.log('Found videos to initialize:', videoIds);
    
    // Initialize each video
    videoIds.forEach(function(videoId) {
        try {
            var player = videojs(videoId, {
                html5: {
                    vhs: {
                        overrideNative: true,
                        enableLowInitialPlaylist: true
                    }
                }
            });
            
            // Add quality selector if available
            if(typeof player.qualitySelectorHls === 'function') {
                player.qualitySelectorHls();
            }
            
            console.log('Successfully initialized:', videoId);
            
        } catch(e) {
            console.error('Failed to initialize', videoId, ':', e.message);
        }
    });
    
    // Global keyboard controls (applies to active player)
    document.addEventListener('keydown', function(e) {
        var players = videojs.getPlayers();
        var playerIds = Object.keys(players);
        
        if(playerIds.length === 0) return;
        
        // Use the first found player
        var activePlayer = players[playerIds[0]];
        
        if(e.keyCode === 37) { // Left arrow
            activePlayer.currentTime(activePlayer.currentTime() - 10);
        } else if(e.keyCode === 39) { // Right arrow
            activePlayer.currentTime(activePlayer.currentTime() + 10);
        }
    });
    
})();
</script>

</body>
</html>
