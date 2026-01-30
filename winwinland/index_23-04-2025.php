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
	<script src="https://for16.ru/scripts/insales/playerjs.js" type="text/javascript"></script>
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

		<h3 class="possibilities__suptitle title"></h3>
     
        <h2 class="rezonans__title">Как подключается расширение</h2>
		<br>
        <div class="possibilities__inner">
            <div class="possibilities__left">
              <img src="img/service-img-3.png" alt="img">
            </div>
            <div class="possibilities__right">
              <div class="possibilities__item">
                <div class="possibilities__item-left">  1.</div>
                <div class="possibilities__item-right">
				   Вы устанавливаете расширение <b>WinWinLand</b> для вашего интернет магазина.
				   <span style='font-weight:normal;' >Оно обеспечивает полную автоматизацию партнерской программы. Возможен быстрый запуск без&nbsp;программиста.</span>
                </div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">  2.</div>
                <div class="possibilities__item-right">
                  Партнеры регистрируются в программе сами или вы их заводите вручную.
					<span style='font-weight:normal;' >Это могут ваши клиенты, блогеры, лидеры мнений, сотрудники, партнеры по бизнесу и кроссмаркетингу.</span>
                </div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">3.</div>
                <div class="possibilities__item-right">
                  Они получают партнерские ссылки, личные кабинеты, вы настраиваете для них систему вознаграждений.
					<span style='font-weight:normal;' >Все это делается автоматически с помощью Winwinland.</span>
                </div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">4.</div>
                <div class="possibilities__item-right">
                  Партнеры вас рекомендуют знакомым, в соцсетях, в блогах, в сообществах.
					<span style='font-weight:normal;' >При этом они указывают свои индивидуальные промокоды или партнерские ссылки.</span>
				</div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">5.</div>
                <div class="possibilities__item-right">
                  Платформа учитывает всех пользователей, купивших по промокоду или перешедших по ссылке,
					<span style='font-weight:normal;' >закрепляет за партнерами и начисляет партнерам вознаграждение после продажи.</span>
				</div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">7.</div>
                <div class="possibilities__item-right">
                  Вы получаете лояльных клиентов с рекомендаций,
					<span style='font-weight:normal;' >а оплачиваете только за результат.</span>
				</div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">8.</div>
                <div class="possibilities__item-right">
                  Платформа помогает вам в работе. 
					<span style='font-weight:normal;' >Есть CRM система, модуль партнерских программ, создание лэндингов, чат ботов, рассылки, прием платежей с карт, искусственный интеллект и множество интеграций.</span>
				</div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">9.</div>
                <div class="possibilities__item-right">
                  Теперь вы экономите на рекламе и привлекаете клиентов с помощью партнерской программы, правила которой устанавливаете сами!
					<span style='font-weight:normal;' >С WinWinLand у вас теперь нет ограничений в ее развитии.</span>
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
                по которому он будет получать вознаграждение в виде бонусов вашего магазина.
				<span style='color:#EC00B8_; font-weight:bold;'>WinWinLand позволяет полностью автоматизировать эту процедуру</span>
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
				   file:"https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/Promo/winwinland_for_ecommerce/master.m3u8",
				   poster:"https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/Promo/winwinland_for_ecommerce/poster.jpg"
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
      <div class="container-swiper">
        <div class="swiper swiper-revievs">
          <h3 class="swiper__title title">Отзывы клиентов</h3>
          <div class="swiper-wrapper">
            <div class="swiper-slide">
              <div class="swiper-item">
                <div class="swiper-item__left">
                  <img src="img/skolkovo.png" alt="Елена Шарипова" loading="lazy">
                </div>
                <div class="swiper-item__right">
                  <div class="swiper-item__autor">
                    <b>Елена Шарипова</b> <br>
                    руководитель школы стартапов Сколково
                  </div>
					<div class="swiper-item__text">Я хочу поделиться своим опытом использования сервиса для создания партнерских программ WINWINLAND. С самого начала наше сотрудничество было плодотворным и результативным. За короткий срок нам удалось значительно увеличить количество участников в нашем акселераторе. Вместо запланированных 10 человек мы собрали 40 заинтересованных в обучении стартаперов, и это просто отличный результат! Партнерская программа была организована на высшем уровне, и это, безусловно, позволило нам привлечь заинтересованных и мотивированных людей.
					</div>
					<div class="swiper-item__text">Эффективность работы WINWINLAND меня приятно удивила. Партнерская программа была запущена всего за семь дней, что является настоящим достижением, особенно учитывая, что этот процесс проходил в летний период. За такой короткий срок мы получили не только количество, но и качество: участники проявили заинтересованность в дальнейшей работе. Отсутствие простоя и высокий уровень вовлеченности стали для нас важными факторами в успешной реализации программы.
					</div>
					<div class="swiper-item__text">В будущем мы планируем продвигать нашу школу стартапов еще более активно, используя стратегию партнерского маркетинга, которую разработали совместно с WINWINLAND. Уверена, что благодаря вашей поддержке и обширной партнерской сети мы сможем достичь новых высот и проложить путь к успешному развитию нашего проекта. Спасибо вам за помощь и профессионализм!
					</div>
					<div class="swiper-item__text">
						Ссылка на видео интервью в канале телеграм <a href='https://t.me/winwinland_ru/168' class='' target='_blank'>https://t.me/winwinland_ru/168</a>
					</div>
                </div>
              </div>
            </div>
            <div class="swiper-slide">
              <div class="swiper-item">
                <div class="swiper-item__left">
                  <img src="img/sheinin.png" alt="Константин Шейнин" loading="lazy">
                </div>
                <div class="swiper-item__right">
                  <div class="swiper-item__autor">
                    <b>Константин Шейнин</b> <br>
                    владелец школы медийности и ораторского мастерства
                  </div>
					<div class="swiper-item__text">Я рад поделиться своим опытом работы с сервисом WINWINLAND, который значительно упростил и улучшил мой процесс партнерского маркетинга в школе ораторского искусства. Я всегда считал, что рекомендательный маркетинг — это одна из самых эффективных форм продвижения, особенно когда у тебя есть качественный продукт. Партнерская программа позволяет моим клиентам зарабатывать до 5% от продажи, что стимулирует их активно делиться информацией о моих курсах и увеличивать нашу аудиторию.
					</div>
					<div class="swiper-item__text">С помощью WINWINLAND мы получили четкую и понятную систему управления партнерскими ссылками. Каждый партнер получает свою ссылку, благодаря которой мы можем отслеживать, откуда пришли клиенты. Этот уровень прозрачности и простоты обеспечивает эффективное взаимодействие с нашими амбассадорами. Коммуникация настроена таким образом, что каждый партнер чувствует свою вовлеченность и важность в нашей команде.
					</div>
					<div class="swiper-item__text">В процессе внедрения я был приятно удивлён качеством технической поддержки. Специалисты внимательно отвечали на все вопросы в чате, а в случае необходимости проводили видеозвонки, где объясняли все аспекты работы платформы. Это создало ощущение заботы и поддержки, что для меня очень важно. Я заметил улучшение результатов, так как взаимодействие с партнерами стало более активным и регулярным. WINWINLAND — это не просто инструмент, а надежный помощник в нашем бизнесе, который позволяет нам достигать поставленных целей. Я с нетерпением жду продолжения нашего сотрудничества!
					</div>
					<div class="swiper-item__text">
						Ссылка на видео интервью в канале телеграм <a href='https://t.me/winwinland_ru/181' class='' target='_blank'>https://t.me/winwinland_ru/181</a>
					</div>
                </div>
              </div>
            </div>
            <div class="swiper-slide">
              <div class="swiper-item">
                <div class="swiper-item__left">
                  <img src="img/anikieva.png" alt="Ольга Аникиева" loading="lazy">
                </div>
                <div class="swiper-item__right">
                  <div class="swiper-item__autor">
                    <b>Ольга Аникиева</b> <br>
                    Наставник в бизнесе и ЗОЖ, ТОП лидер Armelle, основатель клуба снижения веса АнтиЛопа
                  </div>
 
					<div class="swiper-item__text">Мы с командой постоянно увеличиваем базу за счет партнерской программы на платформе ВИНВИНЛЭНД, в которой участвуют не только члены клуба, но и лидеры мнений, блогеры. Рекламу при этом не даем.  
					</div>
					<div class="swiper-item__text">Женский клуб снижения веса АнтиЛопа - проект со стажем в 4 года.  
					Ежемесячно, благодаря партнерской программе, созданной на WinWinLand, поток участниц составляет 150 человек
					</div>
					<div class="swiper-item__text">
						Ссылка на видео интервью в канале телеграм <a href='https://t.me/winwinland_ru/196' class='' target='_blank'>https://t.me/winwinland_ru/181</a>
					</div>
                </div>
              </div>
            </div>
            <div class="swiper-slide">
              <div class="swiper-item">
                <div class="swiper-item__left">
                  <img src="img/komar.png" alt="Ольга Комар" loading="lazy">
                </div>
                <div class="swiper-item__right">
                  <div class="swiper-item__autor">
                    <b>Ольга Комар</b> <br>
                    Команда организаторов федеральных форумов «Содействие» и «Наследники»
                  </div>
 
					<div class="swiper-item__text">Благодарим WinWinLand за совместную работу🤝
					</div>
					<div class="swiper-item__text">Платформа предлагает мощные инструменты для внедрения партнерской программы,
					а партнерка - незаменима для рекомендаций при организации бизнес форумов. Так собирается намного больше участников.
					</div>
					<div class="swiper-item__text">
						Спасибо команде, которая помогла все настроить под наши нужды и рекомендуем всем, кто ищет надежное решение для бизнеса🚀
					</div>
					<div class="swiper-item__text">
						Ссылка на видео в канале телеграм <a href='https://t.me/winwinland_ru/202' class='' target='_blank'>https://t.me/winwinland_ru/202</a>
					</div>
                </div>
              </div>
            </div>
            <div class="swiper-slide">
              <div class="swiper-item">
                <div class="swiper-item__left">
                  <img src="img/slider-3.png" alt="Надежда Абаляева" loading="lazy">
                </div>
                <div class="swiper-item__right">
                  <div class="swiper-item__autor">
                    <b>Надежда Абаляева</b> <br>
                    сотрудник интеллектуального кооператива Альянс 78.2
                  </div>
                  <div class="swiper-item__text">
                    Возможности, которые даёт платформа winwinland мною были поняты сразу.
                    Мне по роду деятельности необходимо вести контакт с большим количеством людей. В такой работе важно
                    никого не забыть. Раньше приходилось вбивать в базу данных всех в ручную. Собирать и консолидировать
                    передаваемые контакты также самостоятельно.
                  </div>
                  <div class="swiper-item__text">
                    Теперь мне это делать не приходится. Пайщики кооператива могут при помощи QR-кода пригласить
                    единомышленника на наши встречи. Могу через рассылку отправлять большому количеству людей сообщения
                    разово и даже назначить время отправки. Лендинг помогает предоставить необходимую информацию гостю.
                    Планирую ссылки на лендинги соединять с календарём, чтобы люди самостоятельно записывались на
                    выбранную встречу.
                  </div>
                  <div class="swiper-item__text">
                    Если приходится работать с большим количеством контактов и рассылать большое количество информации,
                    то очень рекомендую данную платформу.
                    И стоимость очень демократичная. Благодарю основателей за данную платформу!
                  </div>
                </div>
              </div>
            </div>
            <div class="swiper-slide">
              <div class="swiper-item">
                <div class="swiper-item__left">
                  <img src="img/slider-4.png" alt="Надежда Сорова" loading="lazy">
                </div>
                <div class="swiper-item__right">
                  <div class="swiper-item__autor">
                    <b>Надежда Сорова</b> <br>
                    руководитель команды тех. агентства
                  </div>
                  <div class="swiper-item__text">
                    Долго искала и подбирала для себя оптимальный вариант, который устраивал бы в соотношении
                    цена/качество. Открыла для себя WinWinLand. Для меня, как для руководителя команды тех. агентства,
                    WWL стал отличным инструментом, который мы предлагаем клиентам.
                  </div>
                  <div class="swiper-item__text">
                    Особенно радует, что сервис подходит для новичков экспертов, которым нужен сайт, рассылки, CRM,
                    чат-бот для Telegram. Удобство, простота и скорость настройки платформы просто фантастические. А
                    реферальная ссылка, которую даем клиентам и объясняем для работы, приносит приятный бонус.
                  </div>
                </div>
              </div>
            </div>
            <div class="swiper-slide">
              <div class="swiper-item">
                <div class="swiper-item__left">
                  <img src="img/slider-5.png" alt="Оксана Лисицына" loading="lazy">
                </div>
                <div class="swiper-item__right">
                  <div class="swiper-item__autor">
                    <b>Оксана Лисицына</b> <br>
                    технический специалист
                  </div>
                  <div class="swiper-item__text">
                    Мне очень нравится работать с платформой WWL. Главный плюс для меня, как для технического
                    специалиста, в том, что все собрано на одной площадке: лендинг, CRM-система, сервис рассылки,
                    интеграция с Bizon365 и Tilda. Тут же можно создавать готовые скрипты и шаблоны сообщений. Все
                    просто, понятно и доступно.
                  </div>
                </div>
              </div>
            </div>
            <div class="swiper-slide">
              <div class="swiper-item">
                <div class="swiper-item__left">
                  <img src="img/slider-6.png" alt="Антонина Николаева" loading="lazy">
                </div>
                <div class="swiper-item__right">
                  <div class="swiper-item__autor">
                    <b>Антонина Николаева</b> <br>
                    графический дизайнер
                  </div>
                  <div class="swiper-item__text">
                    Меня зовут Антонина, я дизайнер-фрилансер. Год назад я запустила свой стартап, но, к сожалению,
                    трафик не шел. Я долго искала пути, которые привели бы меня к постоянному росту клиентов, но каждый
                    раз заходила в тупик.
                  </div>
                  <div class="swiper-item__text">
                    Но, встретив компанию WinWinLand, я наконец получила желаемое: постоянный рост клиентов, где большая
                    часть — именно моя целевая аудитория, удобную CRM-систему, в которой я без проблем могу отслеживать
                    все, что меня интересует, а также ответственных специалистов, которые в любой затруднительной
                    ситуации придут на помощь.
                  </div>
                </div>
              </div>
            </div>
            <div class="swiper-slide">
              <div class="swiper-item">
                <div class="swiper-item__left">
                  <img src="img/slider-7.png" alt="Люба Маркович" loading="lazy">
                </div>
                <div class="swiper-item__right">
                  <div class="swiper-item__autor">
                    <b>Люба Маркович</b> <br>
                    репетитор
                  </div>
                  <div class="swiper-item__text">
                    Раньше мне приходилось вести ежедневники и бесконечные списки уроков — структурировать расписание
                    было сложно. Головную боль доставляли и оповещения учеников о переносе уроков, неудобно было
                    собирать всех на разговорные клубы.
                  </div>
                  <div class="swiper-item__text">
                    Наконец мой проблема с планированием уроков решена. Теперь все ученики собраны в одном списке.
                    Лендинги и рассылки стали удобным решением, вошли в процесс повседневного планирования. Сейчас все
                    получают уведомления и не опаздывают.
                  </div>
                  <div class="swiper-item__text">
                    Отличная платформа. Спасибо, WinWinLand, за то, что теперь всё под контролем. Сейчас подключаю
                    коллег. Вместе будем в партнёрке.
                  </div>
                </div>
              </div>
            </div>

             <div class="swiper-slide">
              <div class="swiper-item">
                <div class="swiper-item__left">
                  <img src="img/slider-8.png" alt="Сергей Савченко" loading="lazy">
                </div>
                <div class="swiper-item__right">
                  <div class="swiper-item__autor">
                    <b>Сергей Савченко</b> <br>
                    амбассадор живых встреч в ТенЧат
                  </div>
                  <div class="swiper-item__text">
                    После изучения возможностей программы в теории, мне захотелось начать второй этап, и протестировать её сначала на себе, ведь в силу моих принципов я не могу рекомендовать что-то, пока сам не убедился в пользе и безопасности.
                  </div>
                  <div class="swiper-item__text">
                    Здесь я сделаю небольшое отступление, и напомню, что совсем недавно запустил своё первое массовое обучение по нейросетям для новичков, которые идеально подошли в качестве лояльных клиентов. Я предложил им стать партнёрами школы, по сути - соучредителями проекта, и в ручном режиме внёс их в CRM-систему WINWINLAND.
                  </div>
                  <div class="swiper-item__text">
                    Настроил первый лендинг, подключил рассылку в Telegram и ВК. Написал пост с приглашением, со ссылкой на лендинг. Менее чем за неделю, а именно за 4 дня было 32 новых регистрации.
                  </div>
                  <div class="swiper-item__text">
                    Да, я признаю, что такой результат получен не из-за красоты лендинга и не из-за понятности информации, а скорее всего из-за моей личной репутации и популярности, но кто мешает и вам добиваться того же самого?
                  </div>
                  <div class="swiper-item__text">
                    Важно сочетание всех мелочей.
                  </div>
                  <div class="swiper-item__text">
                    Конечно, я в процессе работы над оформлением, так же как и разработчики программы постоянно вносят улучшения.
                  </div>
                  <div class="swiper-item__text">
                    Но в целом, результаты превзошли мои ожидания и я очень доволен.
                  </div>
                  <div class="swiper-item__text">
                    Рекомендую ознакомится с возможностями программы.
                  </div>
                </div>
              </div>
            </div>

            <div class="swiper-slide">
              <div class="swiper-item">
                <div class="swiper-item__left">
                  <img src="img/slider-9.png" alt="Анастасия Смолина" loading="lazy">
                </div>
                <div class="swiper-item__right">
                  <div class="swiper-item__autor">
                    <b>Анастасия Смолина</b> <br>
                    эксперт по коммуникациям и основатель бизнес-клуба Смолиной
                  </div>
                  <div class="swiper-item__text">
                    Я начала применять в своей работе WinWinLand и хочу оставить отзыв.
                  </div>
                  <div class="swiper-item__text">
                    Их будет много: по мере того, как я буду понимать весь процесс и проходить его сама.
                  </div>
                  <div class="swiper-item__text">
                    Освободилось время у ассистента. Раньше она отправляла памятки и письма вручную: 30-20-40 сообщений. Сейчас просто нажатием одной клавиши ушли письма, ушла рассылка. Это очень супер. Это мне просто очень нравится. Моему ассистенту тоже.
                  </div>
                  <div class="swiper-item__text">
                    Благодарю.
                  </div>
                  <div class="swiper-item__text">
                    Очень рада быть в команде WinWinLand.
                  </div>
                </div>
              </div>
            </div>

            <div class="swiper-slide">
              <div class="swiper-item">
                <div class="swiper-item__left">
                  <img src="img/slider-10.png" alt="Анна Тибилова" loading="lazy">
                </div>
                <div class="swiper-item__right">
                  <div class="swiper-item__autor">
                    <b>Анна Тибилова</b> <br>
                    языковой коуч преподаватель английского языка для взрослых
                  </div>
                  <div class="swiper-item__text">
                    Я использую WinWinLand уже месяц. Сервис мне был сразу интуитивно понятен. Там, в принципе, не так сложно всё настроить и было достаточно легко разобраться. И я с помощью технической поддержки настроила свою систему самостоятельно.
                  </div>
                  <div class="swiper-item__text">
                    Какие задачи я сейчас решаю с помощью сервиса? Это привлечение новых партнёров. Я уже привлекла несколько партнёров из числа тех, кого я знала раньше. Я просто восстановила свои старые деловые связи и теперь эти люди - мои партнёры, готовые меня рекомендовать. Дальше я буду использовать Сервис для того, чтобы привлекать новых партнёров и клиентов с помощью партнёрской сети. 
                  </div>
                  <div class="swiper-item__text">
                    После самостоятельной настройки система требовала улучшения. Я ещё посетила практикум Михаила Талая, где мы соединяли маркетинг и систему WinWinLand. И получается, что мы начали с самых основных вопросов: где искать партнёров? Какой продукт предлагать? Что именно предлагать? Как выстраивать свою воронку? То есть, за счёт этого практикума, я не только донастроила систему, но ещё и пересмотрела свою продуктовую линейку.
                  </div>
                  <div class="swiper-item__text">
                    Это инструмент, который вдохновляет меня на поиск новых путей и новых продуктов. Это самое главное. Мне это больше всего нравится в WinWinLand. Когда я начала его использовать, я стала думать, как мне сделать лучше, какие мне ещё продукты ввести в свою продуктовую линейку, потому что я стала видеть какие-то пробелы и чего мне не хватает. То есть этот инструмент позволяет мне масштабнее и более системно мыслить. Это очень круто.
                  </div>
                  <div class="swiper-item__text">
                    Мне очень нравится. Я буду дальше использовать и я уверена, что WinWinLand принесёт мне новых партнёров и новых клиентов. Чего желаю и вам.
                  </div>
                </div>
              </div>
            </div>

            <div class="swiper-slide">
              <div class="swiper-item">
                <div class="swiper-item__left">
                  <img src="img/slider-11.png" alt="Александр Шамин" loading="lazy">
                </div>
                <div class="swiper-item__right">
                  <div class="swiper-item__autor">
                    <b>Александр Шамин</b> <br>
                    автор проекта Вектор Роста Pro
                  </div>
                  <div class="swiper-item__text">
                    Партнеры, кто еще не партнеры!
                  </div>
                  <div class="swiper-item__text">
                    Команда WinWinLand сделала мне не просто лендинг - мне сделали «индивидуальный пошив».
                  </div>
                  <div class="swiper-item__text">
                    По сути, полноценный сайт-каталог. Да еще и с подключенной CRM кнопками записи ко мне и уведомлениями о записях через CRM в телегу.
                  </div>
                  <div class="swiper-item__text">
                    ОЧЕНЬ удобно.  Ну и база сегментирована по продуктам, конечно.
                  </div>
                  <div class="swiper-item__text">
                    Короче,  я прям очень доволен.
                  </div>
                </div>
              </div>
            </div>

          </div>


          <div class="swiper-button-prev">
            <svg width="40" height="45" viewBox="0 0 40 45" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M31.5409 6.17583L6.16357 20.8274C4.83025 21.5972 4.83024 23.5217 6.16358 24.2915L31.5409 38.9431C32.8742 39.7129 34.5409 38.7507 34.5409 37.2111L34.5409 7.90788C34.5409 6.36829 32.8742 5.40603 31.5409 6.17583ZM3.66357 16.4973C-1.00309 19.1916 -1.00309 25.9274 3.66358 28.6217L29.0409 43.2733C33.7076 45.9676 39.5409 42.5997 39.5409 37.2111L39.5409 7.90788C39.5409 2.51928 33.7076 -0.848594 29.0409 1.84571L3.66357 16.4973Z"
                fill="#EC00B8" />
            </svg>
          </div>
          <div class="swiper-button-next">
            <svg width="40" height="44" viewBox="0 0 40 44" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M8.44446 38.3019L33.8218 23.6503C35.1551 22.8805 35.1551 20.956 33.8218 20.1862L8.44446 5.53455C7.11113 4.76476 5.44446 5.727 5.44446 7.26661L5.44446 36.5698C5.44446 38.1094 7.11112 39.0717 8.44446 38.3019ZM36.3218 27.9804C40.9884 25.2861 40.9884 18.5503 36.3218 15.856L10.9445 1.20443C6.27779 -1.48987 0.444458 1.87801 0.444458 7.26661L0.444456 36.5698C0.444456 41.9584 6.2778 45.3263 10.9445 42.632L36.3218 27.9804Z"
                fill="#EC00B8" />
            </svg>
          </div>
        </div>
      </div>
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
                  множестве компаний, от самых больших до самых маленьких. К вам будет приходить
                  в 10 и более раз больше клиентов от рекомендаций.
                </div>
                <div class="questions__item-bold">При подключении вы бесплатно получите:</div>
                <ul class="questions__item-ul">
                  <li class="questions__item-li">
                    обучающий курс по функциям сервиса,
                  </li>
                  <li class="questions__item-li">помощь с внедрением по вашему запросу</li>
                  <li class="questions__item-li">связь с техподдержкой</li>
                </ul>
                <div class="questions__item-bottom">
                  Мы признаем только стратегию win-win и атмосферу взаимопонимания, рассматриваем
                  ваше подключение к данному сервису, как начало сотрудничества.
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
                  Напротив, ВИНВИНЛЭНД интегрируется со всеми популярными CRM системами и может работать в качестве дополняющего модуля для оцифровки партнерской программы.
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
      Используя функции платформы Winwinland, я соглашаюсь <br>
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
