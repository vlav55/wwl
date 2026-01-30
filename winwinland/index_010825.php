<?
$pwd_id=1000;
include "top_code.inc.php";

?>
<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <title>Winwinland—сервис для увеличения продаж за счет создания партнерских программ</title>

  <meta property="og:type" content="website" />
  <meta property="og:title" content="Winwinland—сервис для увеличения продаж за счет создания партнерских программ" />
  <meta property="og:description" content="Winwinland—сервис для увеличения продаж за счет создания партнерских программ" />
  <meta property="og:url" content="https://winwinland.ru" />
  <meta property="og:image" content="https://winwinland.ru/og-image.jpg" />
  <meta property="vk:image" content="https://winwinland.ru/og-image.jpg" />

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=PT+Serif:ital,wght@0,400;0,700;1,400&family=Roboto:wght@400;500;700;900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
  
  <link rel="stylesheet" href="fonts/fonts.css">
  <link rel="stylesheet" href="css/styles.css">
	<script src="https://winwinland.ru/tube/playerjs.js" type="text/javascript"></script>
  <?include "wwl_pixels.inc.php";?>


</head>

<body class="body">
  <header class="header">
    <div class="header__container">
      <a class="header__logo" href="index.html"><img src="img/logo.svg" alt="logo">
      </a>
      <nav class="header__nav">
        <ul class="header__ul">
          <li class="header__li">
            <a href="product.php" class="header__a one active">О продукте</a>
          </li>
          <li class="header__li">
            <a href="#rates" class="header__a two">Тарифы</a>
          </li>
          <li class="header__li">
            <a href="#partner" class="header__a three">Партнерская программа</a>
          </li>
          <li class="header__li">
            <a href="#questions" class="header__a four">Контакты</a>
          </li>
        </ul>
      </nav>
      <a class="header__login" data-fancybox href="#login">Войти</a>
      <a class="header__mobile-login" data-fancybox href="#login">
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
    </div>
  </header>

  <main>
    <section class="service" id="service">
      <div class="service__top">
        <div class="service__top-wrapper">
          <h1 class="service__h1">
            <span class='service__h1_wwl' >Winwinland —</span> <br />
            <span class='service__h1_small' >платформа для автоматизации <br>партнерских программ</span>
          </h1>
        </div>
      </div>
      <div class="container"> 
          <div class="possibilities">
          <h2 class="possibilities__title" style='line-height:1.5;'>Как это работает:
			<div class="possibilities__item-left" style='line-height:1.3;'>
				<br>
				Автоматизируйте учёт партнёрских промокодов и вовлекайте партнёров в работу через прозрачную систему выплат
			</div>
<!--
			<span style='margin-top:10px; padding:10px; text-align:right;'>
				<a href='https://t.me/winwinland_ru' class='' target='_blank'><img src='/img/social/tg-32.png' alt='tg'></a>
				<a href='https://vk.com/winwinland_ru' class='' target='_blank'><img src='/img/social/vk-32.png' alt='vk'></a>
				<a href='https://www.youtube.com/@WINWINLAND-kx6np' class='' target='_blank'><img src='/img/social/youtube-32.png' alt='yt'></a>
			</span>
-->
		  </h2>
		  <br>
        <div class="possibilities__inner">
            <div class="possibilities__left">
              <img src="img/service-img-3.png" alt="img">
            </div>
            <div class="possibilities__right">
              <div class="possibilities__item">
                <div class="possibilities__item-left">  1.</div>
                <div class="possibilities__item-right">
                  Партнёр получает уникальный промокод или ссылку;
                </div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">  2.</div>
                <div class="possibilities__item-right">
                  Распространяет среди своей аудитории;
                </div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">3.</div>
                <div class="possibilities__item-right">
                  Клиенты совершают покупки с промокодом;
                </div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">4.</div>
                <div class="possibilities__item-right">
                  Система автоматически фиксирует продажи;
				</div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">5.</div>
                <div class="possibilities__item-right">
                  Партнёр получает вознаграждение;
				</div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">6.</div>
                <div class="possibilities__item-right">
                  В вашем распоряжении полный учет и аналитика по партнерской программе в одном месте;
				</div>
              </div>
			</div>
        </div>

        <?
        //if(basename(__FILE__)!="index.php")
			include "news.inc.php";
        ?>

		<div class="possibilities">
			<h2 class="possibilities__title">Больше продаж<br>для вашего бизнеса</h2>
			<h3 class="possibilities__suptitle title" style="color:#EC00B8;">
				Автоматизируйте партнерскую программу чтобы:
			</h3>
			<div class="function__items">
				<div class="function__item fi-1">
					<div class="function__item-img">
						<img src="img/function_2.svg" alt="img" loading="lazy">
					</div>
					<div class="function__item-text">Получать больше горячих клиентов от рекомендаций</div>
				</div>
				<div class="function__item mi-2">
					<div class="function__item-img">
						<img src="img/function_1.svg" alt="img" loading="lazy">
					</div>
					<div class="function__item-text">Сделать партнеров лояльными с помощью личных кабинетов</div>
				</div>
				<div class="function__item mi-3">
					<div class="function__item-img">
						<img src="img/function_3.svg" alt="img" loading="lazy">
					</div>
					<div class="function__item-text">Развивать партнерку без ограничений за счет клиентов и блогеров</div>
				</div>
			</div>
        </div>

		<h3 class="possibilities__suptitle title" id="auto_funnel"></h3>
     
        <h2 class="rezonans__title">Первая автоворонка продаж на промокодах</h2>
		<br>
        <div class="possibilities__inner">
            <div class="possibilities__left">
              <img src="img/service-img-3.png" alt="img">
            </div>
            <div class="possibilities__right">
              <div class="possibilities__item">
                <div class="possibilities__item-left">  1.</div>
                <div class="possibilities__item-right">
				   Все любят промокоды.
				   <span style='font-weight:normal;' >Никто не отказывается от промокода, потому что он дает скидку.</span>
                </div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">  2.</div>
                <div class="possibilities__item-right">
                  Партнерский промокод дает еще вознаграждение владельцу.
					<span style='font-weight:normal;' >Независимо от того, кто применил промокод, владелец получает вознаграждение.</span>
                </div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">3.</div>
                <div class="possibilities__item-right">
                  Вознаграждение стимулирует владельца распространять свой промокод как можно шире
					<span style='font-weight:normal;' >Двойная выгода для владельца - это возможность получить для себя и скидку и вознаграждение, то есть сэкономить 2 раза.</span>
                </div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">4.</div>
                <div class="possibilities__item-right">
                  Ваша задача - выдавать промокод каждому клиенту.
					<span style='font-weight:normal;' >Промокоды можно отправлять автоматически рассылке, либо печатать и вкладывать в товар.</span>
				</div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">5.</div>
                <div class="possibilities__item-right">
                  Вознаграждение владельцам промокодов можно выплачивать баллами магазина,
					<span style='font-weight:normal;' >которыми можно оплачивать продукты этом же магазине.</span>
				</div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">6.</div>
                <div class="possibilities__item-right">
                  Если промокоды выдавать КАЖДОМУ покупателю,
					<span style='font-weight:normal;' > то каждый пятый будет активно их распространять,
					привлекая других клиентов, каждый из которых также получит свой промокод,
					из которых каждый пятый будет их активно распространять и так далее.
					</span>
				</div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">7.</div>
                <div class="possibilities__item-right">
                  Так работает автоматическая воронка продаж на промокодах.
					<span style='font-weight:normal;' >
					Ключевое здесь то, что клиенты продают другу другу сами, за счет промокодов.
					Это саморазвивающаяся экосистема, или WinWinLand.
					Мы предоставляем интеграции с любыми платформами,
					чтобы реализовать технологии партнерских промокодов или ссылок.</span>
				</div>
              </div>
			</div>
        </div>

        
<!--
        <div class="traffic">
          <div class="traffic__left">
			<h3 class="possibilities__suptitle title" id="questions">
				<span style="color:#EC00B8;">Настраиваете один раз&nbsp;&mdash; монетизируете постоянно</span>
			</h3>
          </div>
          <div class="traffic__right"><img src="img/service-img-1.png" alt="img" loading="lazy"></div>
        </div>
-->

        <h2 class="rezonans__title" style="color:#EC00B8;">
          </br>
          МЫ РЕКОМЕНДУEМ
        </h2>
        
        <h3 class="traffic__title-hidden title">
          Сделайте клиентов партнерами
        </h3>
        <div class="traffic" style='margin-bottom:0px;'>
          <div class="traffic__left">
            <h3 class="traffic__title title">
				Сделайте клиентов партнерами
            </h3>
            <ul class="traffic__ul">
              <li class="traffic__li">
                При каждой покупке отправляйте клиенту индивидуальный промокод,
                по которому он будет получать вознаграждение при его использовании.
				<span style='color:#EC00B8_; font-weight:bold;'>WinWinLand позволяет полностью автоматизировать эту технологию.</span>
              </li>
            </ul>
          </div>
          <div class="traffic__right">
            <img src="img/news.png" alt="img" loading="lazy">
          </div>
        </div>

        
        <br><a href="consult/?<?=$par_url?>" class="service__link" >Связаться с нами</a>

        <h2 class="settings__title-hidden title">
          100% гарантия на успешное внедрение!
        </h2>
        <div class="settings">
          <div class="settings__left">
            <img src="img/service-img-4.png" alt="img" />
          </div>
          <div class="settings__right">
            <h2 class="settings__title title2">
              100% гарантия на&nbsp;успешное внедрение!
            </h2>
            <ul class="settings__ul">
              <li class="settings__li">Свой штат программистов и маркетологов</li>
              <li class="settings__li">Полная настройка ПОД КЛЮЧ программного обеспечения сервиса для удобства вашего бизнеса.
              Запуск автоматической воронки продаж</li>
              <li class="settings__li">Техподдержка сервиса и консультации по партнерским программам и маркетингу.</li>
            </ul>
            <h3 class="settings__bottom">
              <img src="img/service-img-5.svg" alt="img" loading="lazy">
              <br><br><span>Настроим партнерскую программу под ваши потребности</span>
              <img src="img/service-img-6.svg" alt="img" loading="lazy">
            </h3>
          </div>
        </div>
        <div class="settings__bottom-hidden">
          <img src="img/service-img-5.svg" alt="img" loading="lazy">
          <span>Настроим партнерскую программу под ваши потребности</span>
          <img src="img/service-img-6.svg" alt="img" loading="lazy">
        </div>

        <div class="monetization">
          <h3 class="monetization__title title">Используйте проверенные схемы для монетизации</h3>
          <div class="monetization__items">
            <div class="monetization__item mi-1">
              <div class="monetization__item-img">
                <img src="img/monetization-img-1.svg" alt="img" loading="lazy">
              </div>
              <div class="monetization__item-text">Пригласите в партнерскую программу</div>
            </div>
            <div class="monetization__item arrow mi-2">
              <img src="img/monetization-arrow.svg" alt="arrow" loading="lazy">
            </div>
            <div class="monetization__item mi-3">
              <div class="monetization__item-img">
                <img src="img/monetization-img-2.svg" alt="img" loading="lazy">
              </div>
              <div class="monetization__item-text">Раздайте партнерам персональные промокоды</div>
            </div>
            <div class="monetization__item arrow mi-4">
              <img src="img/monetization-arrow.svg" alt="arrow" loading="lazy">
            </div>
            <div class="monetization__item mi-5">
              <div class="monetization__item-img">
                <img src="img/monetization-img-3.svg" alt="img" loading="lazy">
              </div>
              <div class="monetization__item-text">Партнеры рекомендуют ваши продукты</div>
            </div>
            <div class="monetization__item arrow mi-6">
              <img src="img/monetization-arrow.svg" alt="arrow" loading="lazy">
            </div>
            <div class="monetization__item mi-7">
              <div class="monetization__item-img">
                <img src="img/monetization-img-4.svg" alt="img" loading="lazy">
              </div>
              <div class="monetization__item-text">Покупатели получают скидки</div>
            </div>
            <div class="monetization__item arrow mi-8">
              <img src="img/monetization-arrow.svg" alt="arrow" loading="lazy">
            </div>
            <div class="monetization__item mi-9">
              <div class="monetization__item-img">
                <img src="img/monetization-img-5.svg" alt="img" loading="lazy">
              </div>
              <div class="monetization__item-text">Партнерам начисляется вознаграждение</div>
            </div>
          </div>
        </div>
        <div class="versality">
          <h3 class="versality__title title">Универсально для интернет магазинов на любых платформах</h3>
<!--
          <p>К вам идут люди по сарафанному радио?</p>
-->
          <div class="versality__inner">
            <ul class="versality__ul">
<!--
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-4.svg" alt="img" loading="lazy">
                </div>
                <span>Все, к кому идут люди по сарафанному радио</span>
              </li>
-->
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-3.svg" alt="img" loading="lazy">
                </div>
                <span>Продукты питания</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-10.svg" alt="img" loading="lazy">
                </div>
                <span>Электроника и гаджеты</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-9.svg" alt="img" loading="lazy">
                </div>
                <span>Одежда и обувь</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-11.svg" alt="img" loading="lazy">
                </div>
                <span>Косметика и парфюмерия</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-5.svg" alt="img" loading="lazy">
                </div>
                <span>Товары для дома и сада</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-2.svg" alt="img" loading="lazy">
                </div>
                <span>Здоровое питание и товары для похудения</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-6.svg" alt="img" loading="lazy">
                </div>
                <span>Товары для хобби и творчества</span>
              </li>
            </ul>
            <ul class="versality__ul">
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-7.svg" alt="img" loading="lazy">
                </div>
                <span>Онлайн-курсы и обучение</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-8.svg" alt="img" loading="lazy">
                </div>
                <span>Подарки и уникальные сувениры</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-12.svg" alt="img" loading="lazy">
                </div>
                <span>Автотовары, туризм и отдых</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-4.svg" alt="img" loading="lazy">
                </div>
                <span>Различные услуги</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-1.svg" alt="img" loading="lazy">
                </div>
                <span>Другие продукты, которые хочется рекомендовать</span>
              </li>
            </ul>
          </div>
        </div>
        <div class="youtube">
			<div id="player"></div>
			<script>
			   var player = new Playerjs({id:"player",
				   file:"https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/Promo/WinWinLand_ecommerce_2/master.m3u8",
				   poster:"https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/Promo/WinWinLand_ecommerce_2/poster.jpg"
				   });
			</script>
			<!-- https://www.youtube.com/embed/1_PvarjEwP8-->
			<!-- https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/wwl_on_site_1.mp4-->
<!--
          <a data-fancybox href="https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/WWL_clip3_720p.mp4">
            <img src="img/winwinland_intro.jpg" alt="video" loading="lazy">
          </a>
		  <video class="youtube__hidden" width="370" height="208" poster="img/winwinland_intro.jpg" controls>
			<source src="https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/WWL_clip3_720p.mp4" type="video/mp4">
		  </video>
-->
<!--
          <iframe class="youtube__hidden" width="370" height="190" src="https://www.youtube.com/embed/1_PvarjEwP8"
            title="WINWINLAND CDN video player" frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen >
          </iframe>
-->
        </div>
<!--
        <a href="#rates" class="service__link">Выбрать тариф</a>
-->
        <a href="https://winwinland.ru/pdf/winwinland_for_ecommerce.pdf" class="service__link" >Скачать презентацию</a>
      </div>
      <?include "refs.inc.php";?>
    </section>

<?include "section_prices.inc.php";?>

    <section class="partner" id="partner">
      <div class="container">
        <h2 class="partner__title-hidden">Партнерская программа Winwinland</h2>
        <div class="partner__suptitle-hidden">
              Собственная партнерка Winwinland. Особые условия для VIP партнеров!
        </div>
        <div class="partner__inner">
          <div class="partner__left">
            <h2 class="partner__title">Партнерская программа Winwinland</h2>
            <div class="partner__suptitle">
              Собственная партнерка Winwinland. Особые условия для VIP партнеров!
            </div>
<!--
            <div class="partner__div1">
              Приведите трех человек и пользуйтесь платформой бесплатно или выводите деньги на карту.

            </div>
-->
            <div class="partner__div2">
              Приглашаем к сотрудничеству интеграторов, маркетинговые агентства и
              компании, обслуживающие интернет-магазины.
            </div>
            <div class="partner__bottom">
				<br>
              Для вас &mdash; самые выгодные условия за внедрение нашей платформы
            </div>
          </div>
          <div class="partner__right">
            <img src="img/partner-1.png" alt="img" loading="lazy">
          </div>
        </div>
        <div class="partner__bottom-hidden">
          Подключайтесь и зарабатывайте до <span>40%</span> с платежей каждого приведенного
          клиента.
        </div>
        <a class="partner__link" href="https://winwinland.ru/partnerka/?bc=<?=$bc?>"> Регистрация в партнерской программе </a>
      </div>
    </section>

    <section class="questions" id="questions___">
      <div class="container">
        <h2 class="questions__title">Ответы на частые вопросы</h2>
        <div class="questions__items">
          <div class="questions__item">
            <a class="questions__item-title">Будет ли сервис работать в моём бизнесе?</a>
            <div class="questions__item-content">
              <div class="questions__item-left">
                <div class="questions__item-top">
                  Да. Партнерская программа многократно проверена во
                  множестве компаний, от самых больших до самых маленьких, включая коучей, экспертов, тренеров.
                </div>
                <div class="questions__item-bold">Но есть одно обязательное условие</div>
                <ul class="questions__item-ul">
                  <li class="questions__item-li">качество продукции и услуг должно быть на уровне</li>
                  <li class="questions__item-li">клиенты должны быть довольны и хотеть вас рекомендовать</li>
                </ul>
                <div class="questions__item-bottom">
                  Мы признаем только стратегию win-win и атмосферу взаимопонимания, рассматриваем
                  ваше подключение к данному сервису, как начало сотрудничества.
                </div>
              </div>
            </div>
          </div>
          <div class="questions__item">
            <a class="questions__item-title">Можно ли  самому настроить платформу или нужно куда то обращаться?</a>
            <div class="questions__item-content">
              <div class="questions__item-left">
                <div class="questions__item-top">
                  Для настройки программист не требуется, есть подробная документация, видео по настройке и техподдержка.
                  Систему вполне можно настроить самостоятельно, однако у нас есть также услуга по настройке под ключ.
                </div>
              </div>
            </div>
          </div>
          <div class="questions__item">
            <a class="questions__item-title">Возможен ли учет вознаграждений по промокодам?</a>
            <div class="questions__item-content">
              <div class="questions__item-left">
                <div class="questions__item-top">
                  Да. Система позволяет выдавать индивидуальные промокоды для партнеров и ведет расчет вознаграждений по ним полностью автоматически.
                  Также можно выплачивать партнерам вознаграждение баллами магазина.
                </div>
              </div>
            </div>
          </div>
          <div class="questions__item">
            <a class="questions__item-title">Я работаю одна, у меня даже нет сотрудников?</a>
            <div class="questions__item-content">
              <div class="questions__item-left">
                <div class="questions__item-top">
                  Вы более всего заинтересованы в притоке клиентов по сарафанному радио и в рекомендациях, так как не
                  можете себе позволить тратить на рекламу. Стоимость сервиса доступна для вас и работать
                  он будет у вас также хорошо, как и в любой другой компании, независимо от размера бизнеса, ограничений
                  нет.
                </div>
              </div>
            </div>
          </div>
          <div class="questions__item">
            <a class="questions__item-title">
              У нас уже есть CRM система, не будет ли конфликтов?
            </a>
            <div class="questions__item-content">
              <div class="questions__item-left">
                <div class="questions__item-top">
                  Напротив, WinWinLand интегрируется с популярными CRM системами и CMS для интернет магазинов.
                  WinWinLand  может работать как в качестве дополняющего модуля для оцифровки партнерской программы,
                  так и полностью автономно, включая прием платежей с карт и рассылки на емэйл и в чатботы.
                </div>
              </div>
            </div>
          </div>
          <div class="questions__item">
            <a class="questions__item-title">
              Может ли WinWinLand выплачивать вознаграждения партнерам?
            </a>
            <div class="questions__item-content">
              <div class="questions__item-left">
                <div class="questions__item-top">
                  Нет, WinWinLand делает расчет вознаграждений, вопрос выплат
                  целиком касается вашей бухгалтерии. Также мы можем рекоменждовать сервисы,
                  которые делают массовые выплаты самозанятым, но договорные отношения с ними все равно остаются за вами.
                </div>
              </div>
            </div>
          </div>
          <div class="questions__item">
            <a class="questions__item-title">Позволяет ли система принимать платежи с карт?</a>
            <div class="questions__item-content">
              <div class="questions__item-left">
                <div class="questions__item-top">
                  Да, вы сможете подключить такие платежные системы, как  юкасса, продамус, робокасса и пэйкипер, который включает эквайринги более 30 банков.
                </div>
              </div>
            </div>
          </div>
          <div class="questions__item">
            <a class="questions__item-title">В каком виде я получу доступ?</a>
            <div class="questions__item-content">
              <div class="questions__item-left">
                <div class="questions__item-top">
                  В течение 10-20 минут после оплаты вы на указанный при оплате email получите все необходимые ссылки и
                  логин с паролем на сервис. Доступ предоставляется на оплаченный период, продлить доступ можно в любое
                  время на 3, 6 или 12 месяцев.
                </div>
              </div>
            </div>
          </div>
          <div class="questions__item">
            <a class="questions__item-title">Я не из России, могу ли я сделать покупку?</a>
            <div class="questions__item-content">
              <div class="questions__item-left">
                <div class="questions__item-top">
                  Да, вы можете оплатить через банковскую карту или по специальной ссылке.
                </div>
              </div>
            </div>
          </div>
          <div class="questions__item">
            <a class="questions__item-title">Как оплатить банковским переводом от юрлица?</a>
            <div class="questions__item-content">
              <div class="questions__item-left">
                <div class="questions__item-top">
                  Вы можете скачать счет на странице оплаты. Просто выберите нужный тариф. Закрывающие документы предоставляются.
                </div>
              </div>
            </div>
          </div>
<!--
          <div class="questions__item">
            <a class="questions__item-title">Безопасно ли оплачивать пластиковой картой?</a>
            <div class="questions__item-content">
              <div class="questions__item-left">
                <div class="questions__item-top">
                  Да, это абсолютно безопасно. У нас заключены договоры с проверенными и надежными платежными системами,
                  такими как Тинькоф, Продамус, Best2pay и другими, через которые и идет прием платежей. Все они
                  действуют в соответствии с законодательством и контролируются органами финансового надзора.
                </div>
              </div>
            </div>
          </div>
-->
          <div class="questions__item">
            <a class="questions__item-title">Я сомневаюсь, что мне это нужно. Что делать?</a>
            <div class="questions__item-content">
              <div class="questions__item-left">
                <div class="questions__item-top">
                  Ничего. Сомневаетесь - не подключайтесь. Всегда есть возможность решить любой вопрос альтернативным
                  способом, либо не решать его вообще.
                </div>
              </div>
            </div>
          </div>
          <div class="questions__item">
            <a class="questions__item-title">Где посмотреть документы?</a>
            <div class="questions__item-content">
              <div class="questions__item-left">
                <div class="questions__item-top">
                  Все необходимые документы представлены в самом низу этой страницы.
                  Документация и видео обучение также будут доступны после подключения.
                  Информация о деятельности в сфере IT и разрабатываемом ПО доступна
                  в меню &quot;О продукте&quot;
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="questions__images">
          <img src="img/question-1.svg" alt="img" loading="lazy" width="148" height="92">
          <img src="img/question-2.svg" alt="img" loading="lazy" width="148" height="92">
          <img src="img/question-3.svg" alt="img" loading="lazy" width="116" height="72">
        </div>
      </div>
    </section>
  </main>

  <footer class="footer">
    <h2 class="footer__title" style='margin-bottom:20px;'>Контакты</h2>
    <div class="footer__company"  style='margin:10px 0 10px;'>
		<a href='contacts_ao.pdf' class='footer__link' target=''>АО «ВИНВИНЛЭНД»</a>
	</div>
	<div class='small' >ИНН 7810961157 ОГРН 1247800054050 г.Санкт-Петербург </div>

    <div class="footer__links" style='margin:10px 0 10px;'><a href='product.php' class='' target=''>Деятельность в сфере IT</a></div>

    <span id="email" style='margin:10px 0 10px;'></span>
	<script>
		// This function will create a mailto link and display the email
		function displayEmail() {
			var user = "info";
			var domain = "winwinland.ru";
			var emailAddress = user + "@" + domain;
			document.getElementById("email").innerHTML = `<a  class="footer__link" href="mailto:${emailAddress}">${emailAddress}</a>`;
		}

		displayEmail();
	</script>
    <a class="footer__links" href="tel:8124251296">+7 (812) 425-12-96</a>

    <div class="footer__links">
      Используя функции платформы АО ВИНВИНЛЭНД, я соглашаюсь <br>
      c <a href="https://winwinland.ru/privacypolicy.pdf" target="_blank" rel="noopener noreferrer">Политикой конфиденциальности</a>, <br>
       условиями <a href="https://winwinland.ru/dogovor.pdf" target="_blank" rel="noopener noreferrer">Договора-оферты</a> <br>
       и подтверждаю <a href="https://winwinland.ru/agreement.pdf" target="_blank" rel="noopener noreferrer">Согласие на обработку персональных данных</a>
    </div>
    <img src="img/footer-1.svg" alt="img" loading="lazy">
  </footer>

  <div class="scrollUp">
    <a href="#service"><img src="img/arrow-up.svg" alt="scrollUp"> </a>
  </div>

  <div class="login" id="login">
    <img class="login__img" src="img/modal-1.svg" alt="img" loading="lazy">
    <h3 class="login__title">Панель управления</h3>
    <form id="login_form" class="login__form form" action="goto_crm.php" enctype="multipart/form-data" method="POST">
      <div class="login__item">
        <input class="login__email login-input" name="email" type="email" placeholder="Эл. почта">
      </div>
<!--
      <div class="login__item">
        <input class="login__password login-input" name="password" type="password" placeholder="Пароль">
      </div>
-->
      <button class="login__btn" type="submit" form="login_form" id="login_form_submit">Войти</button>
    </form>
    <div class="login__agree">
      Чтобы получить доступ, оформите любой
      <a href="#rates" onclick="$.fancybox.close();">Тарифный план</a>
    </div>
  </div>

  <div class="mobile-menu" id="mobile-menu">
    <nav class="mobile-menu__nav" onclick="event.stopPropagation()">
      <ul class="mobile-menu__ul">
        <li class="mobile-menu__li">
          <a class="mobile-menu__link" href="product.php" >О продукте</a>
        </li>
        <li class="mobile-menu__li">
          <a class="mobile-menu__link" href="#rates">Тарифы</a>
        </li>
        <li class="mobile-menu__li">
          <a class="mobile-menu__link" href="#partner">Партнерская программа</a>
        </li>
        <li class="mobile-menu__li">
          <a class="mobile-menu__link" href="#questions">Контакты</a>
        </li>
      </ul>
    </nav>
  </div>

  <a class="burger" onclick="event.stopPropagation()">
    <span class="burger__line burger__line-first"></span>
    <span class="burger__line burger__line-second"></span>
    <span class="burger__line burger__line-third"></span>
  </a>

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"
    integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
  <script src="
    https://cdn.jsdelivr.net/npm/just-validate@4.2.0/dist/just-validate.production.min.js
    "></script>
  <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
  <script src="js/main.js"></script>

	<script type="text/javascript">
		$("#login_form_submit").click(function() {
			//console.log("HERE_");
			$('#login_form').attr('action', 'goto_crm.php').submit();
		});
	</script>

<!--
	<link rel="stylesheet" href="https://cdn.envybox.io/widget/cbk.css">
	<script type="text/javascript" src="https://cdn.envybox.io/widget/cbk.js?wcb_code=f94ec5afad5c76fadf45f19e859fea38" charset="UTF-8" async></script>
-->

</body>

</html>
