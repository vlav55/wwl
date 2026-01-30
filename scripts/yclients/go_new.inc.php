<?
$video="Promo/loyalty_20_preza";
$og_image="https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/$video/poster.jpg";
include "top.inc.php";
?>
<!--
	<div class='mt-3' ><img src='https://for16.ru/images/logo-200.png' alt='logo' class=''  ></div>
-->
	<p class='font-weight-bold text-center' >WinWinLand Лояльность 2.0  - это источник горячих клиентов для  вашего бизнеса.</p>
	<p>Вы заинтересованы в продажах? Вам нужны клиенты, без затрат на рекламу, клиенты максимально лояльные и готовые платить снова и снова?
	</p>
	<p>Если да, то посмотрите видео. Его длительность 10 минут.
	Если решите попробовать, нажмите кнопку, для вас создастся аккаунт WinWinLand с бесплатным тестовым периодом 5 дней, а здесь откроется окно настроек.
	Впрочем - настроек совсем немного..
	</p>
	<p>Цены - от 2900 до 4500 р/месяц на филиал в зависимости от периода оплаты. Скрытых ограничений нет.
	Отдельно оплачивается отправка сообщений клиентам (whatsapp или смс, по расценкам провайдера).
	</p>
	<p class='text-center' >Смотрите. Решайте. Пробуйте!</p>

	<p class='text-center' ><a href='?install_app=yes&uid=&uid=<?=$uid?>' class='btn btn-warning btn-lg' target=''>Установить</a></p>
	<p class='text-center mt-2'>
		<a href='https://winwinland.ru/pdf/WinWinLand-Loyalnost-20-dlya-salonov-krasoty.pdf' class='' target='_blank'>Как это работает в бьюти салоне. Пошагово</a>
	</p>
	<p class='text-center mt-2'>
		<a href='#' class='text-muted' id="askQuestionLink">Задать вопрос</a>
	</p>

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

	<!-- Modal Window -->
	<div class="modal fade" id="questionModal" tabindex="-1" role="dialog" aria-labelledby="questionModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="questionModalLabel">Задать вопрос</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body p-0">
					<iframe src="https://ask.winwinland.ru" style="width: 100%; height: 700px; border: none;"></iframe>
				</div>
			</div>
		</div>
	</div>

	<style>
		.modal-sm,.modal-lg {
			max-width: 400px;
		}
	</style>

	<script>
	$(document).ready(function() {
		// Open modal when clicking "Задать вопрос" link
		$('#askQuestionLink').click(function(e) {
			e.preventDefault();
			$('#questionModal').modal('show');
		});
	});
	</script>

<?include "bottom.inc.php";?>	
