<?
$_SESSION['back_url']="https://winwinland.ru/loyalty20";
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
  
  <link rel="stylesheet" href="fonts/fonts.css">
  <link rel="stylesheet" href="css/styles.css">

  <link rel="stylesheet" href="img/partners/css.css">
  <link rel="stylesheet" href="img/partners/style.css">

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">


  <?include "wwl_pixels.inc.php";?>
</head>

<body class="body">
  <header class="header">
    <div class="header__container">
      <a class="header__logo" href="<?=isset($_SESSION['back_url']) ? $_SESSION['back_url'].'#rates' : 'index.php#rates'?>"><img src="img/logo.svg" alt="logo">
      </a>
      <nav class="header__nav">
        <ul class="header__ul">
          <li class="header__li">
            <a href="/loyalty20/" class="header__a one ">Продукт</a>
          </li>
          <li class="header__li">
            <a href="<?=isset($_SESSION['back_url']) ? $_SESSION['back_url'].'#rates' : 'index.php#rates'?>" class="header__a two">Тарифы</a>
          </li>
          <li class="header__li">
            <a href="<?=isset($_SESSION['back_url']) ? $_SESSION['back_url'].'#partner' : 'index.php#partner'?>" class="header__a three active">Партнеры</a>
          </li>
          <li class="header__li">
            <a href="<?=isset($_SESSION['back_url']) ? $_SESSION['back_url'].'#questions' : 'index.php#questions'?>" class="header__a four">FAQ</a>
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
    <div class="service__top">
      <div class="service__top-wrapper">
        <h1 class="service__h1">
          <span class="service__h1_wwl">Winwinland —</span> <br>
          <span class="service__h1_small">платформа для автоматизации <br>партнерских программ</span>
        </h1>
      </div>
    </div>

    <section class="partners">
      <div class="container">
        <div class="partners__inner">
          <h1 class="partners__title">Наши партнеры</h1>
          <div class="partners__items">
            <div class="partners__item">
              <a href="https://www.retailcrm.ru/?partner=RCI-689EFA69" class="partners__item-link" target="_blank">
                <img src="./img/partners/cont.svg" alt="" class="partners__item-link-img">
              </a>
              <img src="./img/partners/partners-item-img-1.svg" alt="" class="partners__item-img">
              <img src="./img/partners/partners-item-bg-1.svg" alt="" class="partners__item-bg">
              <p class="partners__item-title"><font dir="auto" style="vertical-align: inherit;"><font dir="auto" style="vertical-align: inherit;">CRM-маркетинг</font></font></p>
              <p class="partners__item-text">Помогает компаниям увеличивать <br> и&nbsp;автоматизировать продажи <br>
                из&nbsp;интернет-магазинов, соцсетей, <br> мессенджеров и&nbsp;офлайн-точек</p>
            </div>
            <div class="partners__item">
              <a href="#" class="partners__item-link">
                <img src="./img/partners/cont.svg" alt="" class="partners__item-link-img">
              </a>
              <img src="./img/partners/partners-item-img-2.svg" alt="" class="partners__item-img">
              <img src="./img/partners/partners-item-bg-2.svg" alt="" class="partners__item-bg">
              <p class="partners__item-title">Управление бизнесом</p>
              <p class="partners__item-text">Облачная комплексная платформа <br> для&nbsp;совместной работы и&nbsp;управления <br>
                бизнесом</p>
            </div>
            <div class="partners__item">
              <a href="#" class="partners__item-link">
                <img src="./img/partners/cont.svg" alt="" class="partners__item-link-img">
              </a>
              <img src="./img/partners/partners-item-img-3.png" alt="" class="partners__item-img">
              <img src="./img/partners/partners-item-bg-3.svg" alt="" class="partners__item-bg">
              <p class="partners__item-title"><font dir="auto" style="vertical-align: inherit;"><font dir="auto" style="vertical-align: inherit;">Обучение</font></font></p>
              <p class="partners__item-text">Изучение и&nbsp;отработка на&nbsp;практике <br> знаний и&nbsp;инструментов для&nbsp;запуска
                <br> бизнеса</p>
            </div>
            <div class="partners__item">
              <a href="#" class="partners__item-link">
                <img src="./img/partners/cont.svg" alt="" class="partners__item-link-img">
              </a>
              <img src="./img/partners/partners-item-img-4.svg" alt="" class="partners__item-img">
              <img src="./img/partners/partners-item-bg-4.svg" alt="" class="partners__item-bg">
              <p class="partners__item-title"><font dir="auto" style="vertical-align: inherit;"><font dir="auto" style="vertical-align: inherit;">приобретение</font></font></p>
              <p class="partners__item-text">Программный продукт для&nbsp;приёма <br> оплаты на&nbsp;веб-сайте. Включает модули
                <br> для&nbsp;работы с&nbsp;интернет-эквайрингом <br> и&nbsp;электронными деньгами, веб- <br>интерфейс для&nbsp;организации
                работы <br> персонала предприятия с&nbsp;онлайн- <br>платежами</p>
            </div>
            <div class="partners__item">
              <a href="#" class="partners__item-link">
                <img src="./img/partners/cont.svg" alt="" class="partners__item-link-img">
              </a>
              <img src="./img/partners/partners-item-img-5.svg" alt="" class="partners__item-img">
              <img src="./img/partners/partners-item-bg-5.svg" alt="" class="partners__item-bg">
              <p class="partners__item-title"><font dir="auto" style="vertical-align: inherit;"><font dir="auto" style="vertical-align: inherit;">Онлайн-оформление заказа</font></font></p>
              <p class="partners__item-text">Сервис приёма платежей, агрегатор<br> платёжных инструментов
                для&nbsp;онлайн-<br> платежей. Выступает посредником<br> между&nbsp;продавцом и&nbsp;покупателем,<br> позволяет
                удалённо оплачивать товары<br> и&nbsp;услуги</p>
            </div>
            <div class="partners__item">
              <a href="#" class="partners__item-link">
                <img src="./img/partners/cont.svg" alt="" class="partners__item-link-img">
              </a>
              <img src="./img/partners/partners-item-img-6.svg" alt="" class="partners__item-img">
              <img src="./img/partners/partners-item-bg-6.svg" alt="" class="partners__item-bg">
              <p class="partners__item-title"><font dir="auto" style="vertical-align: inherit;"><font dir="auto" style="vertical-align: inherit;">ЭЛЕКТРОННАЯ КОММЕРЦИЯ</font></font></p>
              <p class="partners__item-text">Создайте интернет-магазин, <br> синхронизируйте товары и&nbsp;заказы <br>
                на&nbsp;всех маркетплейсах, ведите диалоги <br> с&nbsp;клиентами в&nbsp;одном окне</p>
            </div>
            <div class="partners__item">
              <a href="#" class="partners__item-link">
                <img src="./img/partners/cont.svg" alt="" class="partners__item-link-img">
              </a>
              <img src="./img/partners/partners-item-img-7.svg" alt="" class="partners__item-img">
              <img src="./img/partners/partners-item-bg-7.svg" alt="" class="partners__item-bg">
              <p class="partners__item-title">ОБРАЗОВАНИЕ</p>
              <p class="partners__item-text">Создавайте онлайн-курсы, собирайте <br> базу, принимайте оплаты <br>
                через&nbsp;Get&nbsp;Модуль, управляйте <br> процессами легко и&nbsp;быстро</p>
            </div>
            <div class="partners__item">
              <a href="#" class="partners__item-link">
                <img src="./img/partners/cont.svg" alt="" class="partners__item-link-img">
              </a>
              <img src="./img/partners/partners-item-img-8.svg" alt="" class="partners__item-img">
              <img src="./img/partners/partners-item-bg-8.svg" alt="" class="partners__item-bg">
              <p class="partners__item-title">ПЛАТЕЖИ</p>
              <p class="partners__item-text">Принимайте оплату в&nbsp;интернете <br> и&nbsp;в&nbsp;розничных точках&nbsp;— быстро, надёжно
                <br> и&nbsp;с&nbsp;минимальными издержками</p>
            </div>
            <div class="partners__item">
              <a href="#" class="partners__item-link">
                <img src="./img/partners/cont.svg" alt="" class="partners__item-link-img">
              </a>
              <img src="./img/partners/partners-item-img-9.svg" alt="" class="partners__item-img">
              <img src="./img/partners/partners-item-bg-9.svg" alt="" class="partners__item-bg">
              <p class="partners__item-title"><font dir="auto" style="vertical-align: inherit;"><font dir="auto" style="vertical-align: inherit;">CMS</font></font></p>
              <p class="partners__item-text">Это&nbsp;комплексная система управления <br> контентом. Она&nbsp;предназначена <br>
                для&nbsp;создания и&nbsp;управления веб-сайтами <br> различной направленности: от&nbsp;простых <br> сайтов‑визиток
                до&nbsp;крупных интернет- <br> магазинов и&nbsp;корпоративных порталов.</p>
            </div>
          </div>
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
          <a class="mobile-menu__link" href="/product.php">О продукте</a>
        </li>
        <li class="mobile-menu__li">
          <a class="mobile-menu__link" href="/#rates">Тарифы</a>
        </li>
        <li class="mobile-menu__li">
          <a class="mobile-menu__link" href="/#partner">Партнеры</a>
        </li>
        <li class="mobile-menu__li">
          <a class="mobile-menu__link" href="/#questions">Контакты</a>
        </li>
      </ul>
    </nav>
  </div>

  <a class="burger" onclick="event.stopPropagation()">
    <span class="burger__line burger__line-first"></span>
    <span class="burger__line burger__line-second"></span>
    <span class="burger__line burger__line-third"></span>
  </a>


	<script type="text/javascript">
		$("#login_form_submit").click(function() {
		});
	</script>

</body>

</html>
