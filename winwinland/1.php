<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db("vkt");
$db->telegram_bot="vkt";
$db->db200="https://for16.ru/d/1000";
chdir("/var/www/vlav/data/www/wwl/d/1000/");
include "init.inc.php";

$bc=0;
if(isset($_GET['bc'])) {
	$bc=intval($_GET['bc']);
}

$uid=0;
if(isset($_GET['uid'])) {
	$uid=$db->get_uid($_GET['uid']);
	if($db->is_md5($_GET['uid']))
		$disp_contacts=true;
}
if($uid)
	$_SESSION['vk_uid']=$uid;

if(isset($_SESSION['vk_uid'])) {
	$uid=intval($_SESSION['vk_uid']);
	if(empty($client_email)) {
		$r=$db->fetch_assoc($db->query("SELECT * FROM cards WHERE uid='$uid'"));
		if($r) {
			$client_phone=$r['mob']; $client_name=$r['name']; $client_email=$r['email'];
		}
	}
} else 
	$uid=0;
$uid_md5=($uid)?$db->uid_md5($uid):0;
	

?>
<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <title>Winwinland</title>

  <meta property="og:type" content="website" />
  <meta property="og:title" content="Winwinland—усилитель ваших продаж" />
  <meta property="og:description" content="Winwinland—усилитель ваших продаж" />
  <meta property="og:url" content="https://winwinland.ru" />
  <meta property="og:image" content="https://winwinland.ru/images/logo/wwl/logo-190.png" />
  <meta property="vk:image" content="https://winwinland.ru/images/logo/wwl/logo-190.png" />

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
            <a href="#service" class="header__a one active">О сервисе</a>
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
            <span>Winwinland —</span> <br />
            платформа для&nbsp;усиления <br /> сарафанного радио
          </h1>
        </div>
      </div>
      <div class="container"> 
          <div class="possibilities">
          <h2 class="possibilities__title">Вас рекомендуют&nbsp;&mdash; продажи растут!</h2>
          
          <div class="possibilities__inner">
            <div class="possibilities__left">
              <img src="img/service-img-3.png" alt="img">
            </div>
            <div class="possibilities__right">
              <div class="possibilities__item">
                <div class="possibilities__item-left">  1.</div>
                <div class="possibilities__item-right">
                  Друзья и&nbsp;клиенты сами регистрируются на&nbsp;платформе как&nbsp;партнеры для&nbsp;вашего бизнеса;
                </div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">2.</div>
                <div class="possibilities__item-right">
                  Winwinland стимулирует их&nbsp;активность. Партнеры размещают ваши рекламные материалы у себя с&nbsp;индивидуальной ссылкой для перехода;
                </div>
              </div>
              <div class="possibilities__item">
                <div class="possibilities__item-left">3.</div>
                <div class="possibilities__item-right">
                  Платформа учитывает всех пользователей, перешедших по&nbsp;этим ссылкам, и&nbsp;начисляет вознаграждение в&nbsp;момент покупки.
            </div></div>
            <div class="possibilities__item">
              <div class="possibilities__item-left">4.</div>
              <div class="possibilities__item-right">
                  Назначаем размер вознаграждений по каждой услуге/товару отдельно. Получаем больше заказов без лишних затрат на рекламу.
            </div>
            </div>
          </div>
        </div>
                  <div class="possibilities">
          <h2 class="possibilities__title">Освободите время для&nbsp;себя <br>и&nbsp;развивайте бизнес</h2>
          <h3 class="possibilities__suptitle title">
            Мы объединили функции, чтобы автоматически:
          </h3>
          <div class="function__items">
            <div class="function__item fi-1">
              <div class="function__item-img">
                <img src="img/function_2.svg" alt="img" loading="lazy">
              </div>
              <div class="function__item-text">Расширять базу потенциальных клиентов</div>
            </div>
            <!-- <div class="monetization__item arrow mi-2">
              <img src="img/monetization-arrow.svg" alt="arrow" loading="lazy">
            </div> -->
            
            <div class="function__item mi-2">
              <div class="function__item-img">
                <img src="img/function_1.svg" alt="img" loading="lazy">
              </div>
              <div class="function__item-text">Вести клиента к&nbsp;покупке</div>
            </div>
            <!-- <div class="monetization__item arrow mi-8">
              <img src="img/monetization-arrow.svg" alt="arrow" loading="lazy">
            </div> -->
            <div class="function__item mi-3">
              <div class="function__item-img">
                <img src="img/function_3.svg" alt="img" loading="lazy">
              </div>
              <div class="function__item-text">Продавать повторно и&nbsp;в&nbsp;несезон</div>
            </div>
          </div>
<h3 class="possibilities__suptitle title">
            Winwinland сделает рутину за вас!
          </h3>
     
        <h2 class="rezonans__title">
          </br>
          Используем передовые техники продаж
        </h2>
        
        <div class="traffic">
          <div class="traffic__left">
            <!-- <h3 class="traffic__title title">
              Ведите трафик на Winwinland — получайте больше заказов
            </h3> -->
            <ul class="traffic__ul">
              <li class="traffic__li">
                Внедряем лидмагниты и&nbsp;форму регистрации на&nbsp;ваш сайт. <span style='font-weight:normal;' >Используем встроенный конструктор для&nbsp;анонсов мероприятий, промо-страниц, карточек товаров и&nbsp;электронных визиток. Быстрый запуск без&nbsp;программиста.</span>
              </li>
              <li class="traffic__li">
               Контакты всех клиентов сами попадают в&nbsp;CRM. Доступ к&nbsp;базе из&nbsp;любой точки через интернет, история и&nbsp;отчеты по&nbsp;продажам. 
              </li>
              <li class="traffic__li">
               Упрощаем оплату ваших товаров и&nbsp;услуг. Прием платежей с&nbsp;карт прямо на&nbsp;вашем сайте и&nbsp;банковские рассрочки для дорогих продуктов. 
              </li>
              <li class="traffic__li">Настраиваем напоминания и&nbsp;авторассылки для клиентов. Платформа отправит их&nbsp;своевременно: по&nbsp;событию или по&nbsp;времени. Рассылка в&nbsp;Вконтакте и&nbsp;Телеграм&nbsp;&mdash; бесплатно.</li>
            </ul>
            <div>
        <h3 class="possibilities__suptitle title">
            <span style="color:#EC00B8;">Настраиваем один раз&nbsp;&mdash; монетизируете постоянно</span>
          </h3>
          </div>
          </div>
          <div class="traffic__right">
            <img src="img/service-img-1.png" alt="img" loading="lazy">
          </div>

        </div>
        <div class="traffic__bottom traffic__bottom-hidden">
        </div>
        <a href="#rates" class="service__link">Выбрать тариф</a>
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
              <li class="settings__li">Свой штат специалистов</li>
              <li class="settings__li">Полная настройка платформы для удобства вашего бизнеса. Внедрение техник «запусков» и «разогрева» клиентской базы.
Создание вебинаров и автовебинаров под ключ.</li>
              <li class="settings__li">Сопровождение и консультации по маркетингу.</li>
            </ul>
            <div class="settings__bottom">
              <img src="img/service-img-5.svg" alt="img" loading="lazy">
              <br><br><span>Подстроим под ваши привычки и предпочтения</span>
              <img src="img/service-img-6.svg" alt="img" loading="lazy">
            </div>
          </div>
        </div>
        <div class="settings__bottom-hidden">
          <img src="img/service-img-5.svg" alt="img" loading="lazy">
          <span>Подстроим под ваши привычки и&nbsp;предпочтения</span>
          <img src="img/service-img-6.svg" alt="img" loading="lazy">
        </div>
        <div class="monetization">
          <h3 class="monetization__title title">Используйте проверенные схемы для монетизации</h3>
          <div class="monetization__items">
            <div class="monetization__item mi-1">
              <div class="monetization__item-img">
                <img src="img/monetization-img-1.svg" alt="img" loading="lazy">
              </div>
              <div class="monetization__item-text">Трафик</div>
            </div>
            <div class="monetization__item arrow mi-2">
              <img src="img/monetization-arrow.svg" alt="arrow" loading="lazy">
            </div>
            <div class="monetization__item mi-3">
              <div class="monetization__item-img">
                <img src="img/monetization-img-2.svg" alt="img" loading="lazy">
              </div>
              <div class="monetization__item-text">Лендинг от Winwinland</div>
            </div>
            <div class="monetization__item arrow mi-4">
              <img src="img/monetization-arrow.svg" alt="arrow" loading="lazy">
            </div>
            <div class="monetization__item mi-5">
              <div class="monetization__item-img">
                <img src="img/monetization-img-3.svg" alt="img" loading="lazy">
              </div>
              <div class="monetization__item-text">
                Заявки <br />
                в CRM
              </div>
            </div>
            <div class="monetization__item arrow mi-6">
              <img src="img/monetization-arrow.svg" alt="arrow" loading="lazy">
            </div>
            <div class="monetization__item mi-7">
              <div class="monetization__item-img">
                <img src="img/monetization-img-4.svg" alt="img" loading="lazy">
              </div>
              <div class="monetization__item-text">Рассылка для прогрева</div>
            </div>
            <div class="monetization__item arrow mi-8">
              <img src="img/monetization-arrow.svg" alt="arrow" loading="lazy">
            </div>
            <div class="monetization__item mi-9">
              <div class="monetization__item-img">
                <img src="img/monetization-img-5.svg" alt="img" loading="lazy">
              </div>
              <div class="monetization__item-text">Рост продаж!</div>
            </div>
          </div>
        </div>
        <div class="versality">
          <h3 class="versality__title title">Универсально для экспертов и предпринимателей</h3>
          <div class="versality__inner">
            <ul class="versality__ul">
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-6.svg" alt="img" loading="lazy">
                </div>
                <span>Эксперты по питанию, спорту, уходу за телом</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-2.svg" alt="img" loading="lazy">
                </div>
                <span>Психологи, коучи, тарологи</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-10.svg" alt="img" loading="lazy">
                </div>
                <span>Учителя, репетиторы и наставники</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-11.svg" alt="img" loading="lazy">
                </div>
                <span>Кондитеры и кафе</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-5.svg" alt="img" loading="lazy">
                </div>
                <span>Врачи и ветпомощь</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-12.svg" alt="img" loading="lazy">
                </div>
                <span>Мастера-ремесленники</span>
              </li>
            </ul>
            <ul class="versality__ul">
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-7.svg" alt="img" loading="lazy">
                </div>
                <span>Бьюти сервис</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-8.svg" alt="img" loading="lazy">
                </div>
                <span>Дизайнеры</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-9.svg" alt="img" loading="lazy">
                </div>
                <span>Ремонтники и строители</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-3.svg" alt="img" loading="lazy">
                </div>
                <span>Автосервисы</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-4.svg" alt="img" loading="lazy">
                </div>
                <span>Юридические услуги</span>
              </li>
              <li class="versality__li">
                <div class="versality__li-img">
                  <img src="img/versatility-1.svg" alt="img" loading="lazy">
                </div>
                <span>Недвижимость</span>
              </li>
            </ul>
          </div>
        </div>
        <div class="youtube">
          <a data-fancybox href="https://www.youtube.com/watch?v=1_PvarjEwP8">
            <img src="img/youtube.jpg" alt="video" loading="lazy">
          </a>
          <iframe class="youtube__hidden" width="370" height="190" src="https://www.youtube.com/embed/1_PvarjEwP8"
            title="YouTube video player" frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen></iframe>
        </div>
        <a href="#rates" class="service__link">Выбрать тариф</a>
      </div>
      <div class="container-swiper">
        <div class="swiper swiper-revievs">
          <h3 class="swiper__title title">Отзывы клиентов</h3>
          <div class="swiper-wrapper">
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
                  <img src="img/slider-2.png" alt="Евгений Евтухов" loading="lazy">
                </div>
                <div class="swiper-item__right">
                  <div class="swiper-item__autor">
                    <b>Евгений Евтухов</b> <br>
                    предприниматель, инвестор, независимый директор
                  </div>
                  <div class="swiper-item__text">
                    Здравствуйте, меня зовут Евгений Евтухов. Я предприниматель, реализую множество проектов. У каждого
                    проекта своя целевая аудитория, свои каналы продвижения и т.д. Как структурировать эти проекты по
                    своей ЦА и не забыть пригласить на мероприятие?
                  </div>
                  <div class="swiper-item__text">
                    С этим мне очень помогла платформа «WINWINLAND»!
                    В программе работать легко, удобно, а если возникнут проблемы, затруднения, специалисты всегда на
                    связи. Разработать свой чат-бот в Telegram оказалось легко и просто, как и создать лендинг на любое
                    событие.
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
                  <img src="img/slider-1.png" alt="girl" loading="lazy">
                </div>
                <div class="swiper-item__right">
                  <div class="swiper-item__text">
                    Раньше я никогда не задумывалась над тем, почему на самом деле люди
                    рекомендуют мои занятия и откуда берется сарафанное радио. Оно просто работало
                    и я всегда воспринимала это, как должное. Но настал момент, когда отток
                    усилился, денег у людей стало меньше, а конкурентов больше. Пробовали давать
                    рекламу, но это не то. С рекламы идут люди, которые как пришли, так и ушли.
                    Плюс за нее платить надо и кто-то должен ее настраивать. И в этот момент мне
                    порекомендовали этот сервис для усиления сарафанного радио.
                  </div>
                  <div class="swiper-item__text">
                    Я не знала, насколько простой и эффективной окажется эта техника, насколько
                    популярной станет наша студия, и насколько увеличится мой доход из-за того,
                    что сарафан заработал в полную силу. Результаты были на самом деле
                    ошеломляющими. Через 3 месяца мой доход вырос в 2,5 раза при том, что расходы
                    не увеличились, а уменьшились. Это из-за того, что меньше денег стало уходить
                    на бесполезную рекламу, при этом также высвободилось время, которое я теперь
                    трачу не на уговоры, а на постоянных клиентов, людей, которые ценят меня, а я
                    их. Ко мне стали приходить люди заинтересованные, кому действительно нужны
                    наши услуги (а не те, которые умеют только негативить).
                  </div>
                  <div class="swiper-item__text">
                    Я не ожидала, насколько охотно они будут рассказывать каждому встречному о
                    своих успехах и обо мне, о том, с каким восторгом они будут делать отзывы и
                    рекомендовать меня и мой бизнес...
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

<?include "section_prices_1.php";?>

    <section class="partner" id="partner">
      <div class="container">
        <h2 class="partner__title-hidden">Партнерская программа</h2>
        <div class="partner__suptitle-hidden">
          У нас предусмотрена партнерская программа для специалистов маркетинга, рекламы и
          web-дизайна на особых условиях.
        </div>
        <div class="partner__inner">
          <div class="partner__left">
            <h2 class="partner__title">Партнерская программа</h2>
            <div class="partner__suptitle">
              Особые условия для экспертов и специалистов маркетинга, рекламы и
              web-дизайна
            </div>
            <div class="partner__div1">
              Приведите трех человек и пользуйтесь платформой бесплатно или выводите деньги на карту.

            </div>
            <div class="partner__div2">
              Подключайтесь и зарабатывайте до 40% с каждого абонентского платежа от ваших рефералов.
            </div>
            <div class="partner__bottom">
              Регистрация в качестве партнеров &mdash; бесплатно
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
        <a class="partner__link" href="https://winwinland.ru/partnerka/?bc=<?=$bc?>"> Подробно о партнерской программе </a>
      </div>
    </section>

    <section class="questions" id="questions">
      <div class="container">
        <h2 class="questions__title">Ответы на частые вопросы</h2>
        <div class="questions__items">
          <div class="questions__item">
            <a class="questions__item-title">Будет ли сервис работать в моём бизнесе?</a>
            <div class="questions__item-content">
              <div class="questions__item-left">
                <div class="questions__item-top">
                  Да, при желании с вашей стороны. Партнерская программа многократно проверена во
                  множестве компаний, от самых больших до самых маленьких. Хороший сервис
                  рекомендуют всегда.
                </div>
                <div class="questions__item-bold">При подключении вы бесплатно получите:</div>
                <ul class="questions__item-ul">
                  <li class="questions__item-li">
                    обучающий курс по работе с CRM, по настройкам модуля управления клиентской
                    лояльностью (сарафанным радио),
                  </li>
                  <li class="questions__item-li">помощь с внедрением по вашему запросу</li>
                  <li class="questions__item-li">консультации от Техподдержки в Телеграм</li>
                </ul>
                <div class="questions__item-bottom">
                  Мы признаем только стратегию win-win и атмосферу взаимопонимания, рассматриваем
                  ваше подключение к данному сервису, как начало сотрудничества.
                </div>
              </div>
            </div>
          </div>
          <div class="questions__item">
            <a class="questions__item-title">Я работаю одна, у меня даже нет фирмы?</a>
            <div class="questions__item-content">
              <div class="questions__item-left">
                <div class="questions__item-top">
                  Вы более всего заинтересованы в притоке клиентов по сарафанному радио и в рекомендациях, так как не
                  можете себе позволить тратить большие бюджеты на рекламу. Стоимость сервиса доступна всем и работать
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
                  Не будет, WinWinLand не мешает работе других систем.
                </div>
              </div>
            </div>
          </div>
          <div class="questions__item">
            <a class="questions__item-title">Можно ли принимать платежи автоматически?</a>
            <div class="questions__item-content">
              <div class="questions__item-left">
                <div class="questions__item-top">
                  WinWinLand интегрирован с платежными системами Продамус и Best2pay. Возможно также сделать интеграцию
                  с другими системами по вашей заявке. Кроме того, вы можете проводить платежи вручную.
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
                  время
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
            <a class="questions__item-title">Можно ли оплатить через электронные кошельки?</a>
            <div class="questions__item-content">
              <div class="questions__item-left">
                <div class="questions__item-top">
                  Да, оплатить можно любым способом. К вашему распоряжению десятки вариантов.
                </div>
              </div>
            </div>
          </div>
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
          <div class="questions__item">
            <a class="questions__item-title">Я сомневаюсь, что мне это нужно. Что делать?</a>
            <div class="questions__item-content">
              <div class="questions__item-left">
                <div class="questions__item-top">
                  Ничего. Сомневаетесь - не подключайтесь. Всегда есть возможность решить любой вопрос альтернативным
                  способом, прибегнув к помощи специалистов, либо не решать его вообще.
                </div>
              </div>
            </div>
          </div>
          <div class="questions__item">
            <a class="questions__item-title">Вы работаете официально?</a>
            <div class="questions__item-content">
              <div class="questions__item-left">
                <div class="questions__item-top">
                  Да. Все необходимые документы представлены в самом низу этой страницы.
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
    <h2 class="footer__title">Контакты</h2>
    <div class="footer__company">ООО «ВинВинЛэнд»</div>
    <a class="footer__link" href="tel:8124251296">(812) 425-12-96</a>
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
    <form class="login__form form" action="mail.php" enctype="multipart/form-data" method="POST">
      <div class="login__item">
        <input class="login__email login-input" name="Email" type="email" placeholder="Эл. почта">
      </div>
      <div class="login__item">
        <input class="login__password login-input" name="Password" type="password" placeholder="Пароль">
      </div>
      <button class="login__btn" type="submit">Войти</button>
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
          <a class="mobile-menu__link" href="#service">О сервисе</a>
        </li>
        <li class="mobile-menu__li">
          <a class="mobile-menu__link" href="#rates">Тарифы</a>
        </li>
        <li class="mobile-menu__li">
          <a class="mobile-menu__link" href="#partner">Партнерская программа</a>
        </li>
        <li class="mobile-menu__li">
          <a class="mobile-menu__link" href="#questions">Вопросы</a>
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
</body>

</html>
