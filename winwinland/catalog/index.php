<?
$pwd_id=1002;
include "../top_code.inc.php";

function print_cat($title,$products) {
	global $base_prices;
	//$res=$db->query("SELECT * FROM product WHERE del=0");
	$array = array();
	foreach ($products AS $pid) {
		$array[] = $base_prices[$pid];
	}

	// Разделение массива на группы по 3 значения для каждой колонки на большом экране
	$columns = array_chunk($array, 1);
	?>
	<div class='card p-3 my-5' style='border-radius: 20px; border-color:#555;' >
		<h1 class='text-center possibilities__title' ><?=$title?></h1>
		<div class="row">
		  <? foreach ($columns as $column) { ?>
			<div class="col-md-4" style="position: relative;">
				  <? foreach ($column as $row) { ?>
					<div class='card p-2 my-3 mx-1 bg-info_ text-white' style="background-color:#0094ff; border-radius: 15px; border-color:#a3d8ff; border-width:3px;">
						<div class="card-body  d-flex flex-column" style="position: relative; min-height: 100%; display: flex; flex-direction: column; background-color:#0094ff;  font-family: 'PT Sans', sans-serif;">
							<h3 class='text-center' ><?=$row['descr']?></h3>
							<h4 class='text-center font-weight-bold' >
								<span style="text-decoration: line-through;"><?=$row['0']?>&nbsp;р.</span>
								<?=$row['1']?>&nbsp;р.
							</h4>
							<br><br>
							<p class='mt-5 text-center'  style="position: absolute; bottom: 0; left: 0; right: 0; text-align: center;">
								<a href='#' class='btn btn-warning_ font-weight-bold py-2 px-3' style='border-radius: 20px; background-color:#ffd600;' target=''>Подробнее</a>
							</p>
						</div>
					</div>
				  <? } ?>
			</div>
		  <? } ?>
		</div>
	</div>
	<?
}

?>
<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <title>Каталог продукции WINWINLAND</title>

  <meta property="og:type" content="website" />
  <meta property="og:title" content="Winwinland—каталог продукции" />
  <meta property="og:description" content="Winwinland—каталог продукции" />
  <meta property="og:url" content="https://winwinland.ru/catalog" />
  <meta property="og:image" content="https://winwinland.ru/img/logo/winwin_og-2.jpg" />
  <meta property="vk:image" content="https://winwinland.ru/img/logo/winwin_og-2.jpg" />

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=PT+Serif:ital,wght@0,400;0,700;1,400&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
<!--
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
-->
  <link rel="stylesheet" href="../fonts/fonts.css">
  <link rel="stylesheet" href="../css/styles.css">

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

<body class="body">
  <header class="header">
    <div class="header__container">
      <a class="header__logo" href="index.php"><img src="/img/logo.svg" alt="logo">
      </a>
      <nav class="header__nav">
        <ul class="header__ul">
          <li class="header__li">
            <a href="../#service" class="header__a one active">О сервисе</a>
          </li>
          <li class="header__li">
            <a href="#rates" class="header__a two">Тарифы</a>
          </li>
          <li class="header__li">
            <a href="#partner" class="header__a three">Партнерская программа</a>
          </li>
          <li class="header__li">
            <a href="#questions" class="header__a four">Вопросы</a>
          </li>
        </ul>
      </nav>
      &nbsp;
<!--
      <a class="header__login" data-fancybox href="#">Войти</a>
      <a class="header__mobile-login" data-fancybox href="#">
        <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="12.9595" cy="12.7576" r="12" fill="#7982A1" />
          <path fill-rule="evenodd" clip-rule="evenodd"
            d="M12.9597 5.11027C11.4595 5.11027 10.2434 6.27657 10.2434 7.71528C10.2434 9.15399 11.4595 10.3203 12.9597 10.3203C14.4598 10.3203 15.6759 9.15399 15.6759 7.71528C15.6759 6.27657 14.4598 5.11027 12.9597 5.11027ZM8.98974 7.71528C8.98974 5.61255 10.7671 3.90796 12.9597 3.90796C15.1522 3.90796 16.9296 5.61255 16.9296 7.71528C16.9296 9.818 15.1522 11.5226 12.9597 11.5226C10.7671 11.5226 8.98974 9.818 8.98974 7.71528Z"
            fill="white" />
          <path fill-rule="evenodd" clip-rule="evenodd"
            d="M10.9081 14.328C9.15626 14.328 7.73608 15.69 7.73608 17.3701C7.73608 17.4584 7.75289 17.5206 7.77052 17.5569C7.7854 17.5875 7.79816 17.5962 7.80881 17.6017C8.29595 17.856 9.66179 18.3357 12.9597 18.3357C16.2575 18.3357 17.6234 17.856 18.1105 17.6017C18.1212 17.5962 18.1339 17.5875 18.1488 17.5569C18.1664 17.5206 18.1833 17.4584 18.1833 17.3701C18.1833 15.69 16.7631 14.328 15.0112 14.328H10.9081ZM6.48242 17.3701C6.48242 15.026 8.46388 13.1257 10.9081 13.1257H15.0112C17.4555 13.1257 19.4369 15.026 19.4369 17.3701C19.4369 17.8005 19.2745 18.3631 18.7097 18.6578C17.9617 19.0482 16.3569 19.538 12.9597 19.538C9.56247 19.538 7.95766 19.0482 7.20959 18.6578C6.64487 18.3631 6.48242 17.8005 6.48242 17.3701Z"
            fill="white" />
        </svg>
      </a>
-->
    </div>
  </header>

  <main>
    <section class="service" id="service" style=''>
      <div class="service__top">
        <div class="service__top-wrapper_1">
          <h1 class="service__h1_1">
            <span>Winwinland —</span> <br />
            каталог продукции
          </h1>
        </div>
      </div>
    </section>
	<div class="container">
		<?print_cat("Абонентская плата за доступ к платформе",[30,31,35,32]);?>
		<?print_cat("Услуги по внедрению и сопровождению продукта",[33,34,52]);?>
		<br><br><br>		
	</div>
  </main>
  <script>
	$(window).on('load', function() {
	  // Executed when the window is loaded
	  resizeCards(); // Call the function to calculate and set the height of the cards

	  // Executed when the window is resized
	  $(window).resize(resizeCards);
	});

	function resizeCards() {
	  $(".row").each(function() {
		$(this).find(".card").css('height', ''); // Remove previous height values

		var maxHeight = 0;
		$(this).find(".card").each(function() {
		  var height = $(this).outerHeight();
		  if (height > maxHeight) {
			maxHeight = height;
		  }
		});

		$(this).find(".card").outerHeight(maxHeight);
	  });
	}
  </script>
  <footer class="footer">
    <h2 class="footer__title">Контакты</h2>
    <div class="footer__company">ООО «ВинВинЛэнд»</div>
    <a class="footer__link" href="tel:8124251296">(812) 425-12-96</a>
    <div class="footer__links">
      Используя функции платформы Winwinland, я соглашаюсь <br>
      c <a href="https://winwinland.ru/privacypolicy.pdf" target="_blank" rel="noopener noreferrer">Политикой конфиденциальности</a>, <br>
       условиями <a href="https://winwinland.ru/dogovor.pdf" target="_blank" rel="noopener noreferrer">Договора-оферты</a> <br>
       и подтверждаю <a href="https://winwinland.ru/agreement.pdf" target="_blank" rel="noopener noreferrer">Согласие на обработку персональных данных</a>
    </div>
    <img src="../img/footer-1.svg" alt="img" loading="lazy">
  </footer>


<!--
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"
    integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/just-validate@4.2.0/dist/just-validate.production.min.js"></script>
  <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
-->
<!--
  <script src="../js/main.js"></script>
-->

<script type="text/javascript">
	$("#login_form_submit").click(function() {
		//console.log("HERE_");
		$('#login_form').attr('action', 'goto_crm.php').submit();
	});
</script>

<script type="text/javascript">
	console.log('test');
	$("#go_submit").click(function() {
		console.log("HERE_");
		//alert($("#c_name").val());
		if($("#client_name").val().trim()=="") {
			alert("Необходимо указать ваше имя!");
		} else if($("#client_phone").val().trim()=="") {
			alert("Укажите, пожалуйста, телефон для связи!");
		} else if(!$("#chk1").is(":checked")) {
			alert("Необходимо согласиться с обработкой персональных данных !");
		} else {
			$('#f1').attr('action', '?').submit();
		}
	});
</script>

	
  
</body>

</html>
