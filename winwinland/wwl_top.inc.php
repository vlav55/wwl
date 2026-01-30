<?
$pwd_id=1000;
$real_path="/var/www/vlav/data/www/wwl/winwinland";
$root_url="https://winwinland.ru";
include "$real_path/top_code.inc.php";

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
  
  <link rel="stylesheet" href="https://winwinland.ru/fonts/fonts.css">
  <link rel="stylesheet" href="https://winwinland.ru/css/styles.css">
	<script src="https://winwinland.ru/tube/playerjs.js" type="text/javascript"></script>
  <?include "$real_path/wwl_pixels.inc.php";?>


</head>

<body class="body">
  <header class="header">
    <div class="header__container">
      <a class="header__logo" href="<?=isset($_SESSION['back_url']) ? $_SESSION['back_url'] : 'index.php'?>"><img src="https://winwinland.ru/img/logo.svg" alt="logo">
      </a>
      <nav class="header__nav">
        <ul class="header__ul">
<!--
          <li class="header__li">
            <a href="https://winwinland.ru/product.php" class="header__a one active">О продукте</a>
          </li>
-->
          <li class="header__li">
			<?
			if(strpos($_SERVER['SCRIPT_NAME'],'loyalty20')===false)
				print "<a href='https://winwinland.ru/loyalty20/' class='header__a one'>Лояльность 2.0</a>";
			else
				print "<a href='https://winwinland.ru/' class='header__a one'>Возможности</a>";
            ?>
          </li>
          <li class="header__li">
            <a href="#rates" class="header__a two">Тарифы</a>
          </li>
          <li class="header__li">
            <a href="https://winwinland.ru/partners.php" class="header__a three">Партнеры</a>
          </li>
          <li class="header__li">
            <a href="#questions" class="header__a four">FAQ</a>
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
