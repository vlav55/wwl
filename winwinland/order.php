<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db("vkt");
$db->telegram_bot="vkt";
$db->db200="https://for16.ru/d/1000";
//include_once "../prices.inc.php";
chdir("/var/www/vlav/data/www/wwl/d/1000/");
include "init.inc.php";
$_SESSION['csrf_token_order'] = bin2hex(random_bytes(32)); // Unique token
//print_r($_SESSION); exit;
$title="Оформление заказа";
$descr="$title";

$product_id=(isset($_GET['product_id']))?intval($_GET['product_id']):false;
if(!$product_id) {
	print "<div class='alert alert-danger' >Ошибка. Не найден продукт.</div>";
	include "../bottom.inc.php";
	exit;
}

if(!$custom=(isset($_GET['c'])) ? mb_substr($_GET['c'],0,16) : null)
	$custom=(isset($_GET['custom'])) ? mb_substr($_GET['custom'],0,16) : null;

$uid=0; $disp_contacts=false; $readonly="";
if(isset($_GET['uid'])) {
	$uid=$db->get_uid($_GET['uid']);
	if($db->is_md5($_GET['uid'])) {
		$disp_contacts=true;
		$readonly="readonly";
	}
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

if(isset($_GET['bc']))
	$bc=$db->promocode_validate($_GET['bc']);
elseif(isset($_SESSION['bc']))
	$bc=$db->promocode_validate($_SESSION['bc']);
else $bc=0;

if(isset($_GET['ctrl_id'])) {
	if($client_ctrl_id=intval($_GET['ctrl_id'])) {
		if($uid1=$db->dlookup("uid","0ctrl","id='$client_ctrl_id'"))
			$uid=$uid1;
	}
} else
	$client_ctrl_id=0;


if(!$disp_contacts) {
	$client_email="";
	$client_phone="";
	$client_name="";  
}

if(isset($_POST['promocode']))
	unset($_SESSION['s_best2pay']);
if(isset($_GET['s'])) {
	if(intval($_GET['s'])) {
		$sum=intval($_GET['s']);
		$_SESSION['s_best2pay']=$sum;
	} else
		unset($_SESSION['s_best2pay']);
}
if(!isset($_SESSION['s_best2pay'])) {
	$sum=$db->price2_chk($uid,$product_id)?$base_prices[$product_id][2]:$base_prices[$product_id][1];
} else
	$sum=intval($_SESSION['s_best2pay']);

//print "HERE_$sum ".$db->price2_chk($uid,$product_id); exit;

$price1=$db->price2_chk($uid,$product_id)?$base_prices[$product_id][1]:$base_prices[$product_id][0];

//$_SESSION['s_best2pay']=$sum;

$fee_1=0; $fee_2=0;
$promocode_id=0;
if(isset($_GET['promocode'])) {
	$tm_pay_cash=intval($_GET['tm_pay_cash']);
	$comm_pay_cash=mb_substr($_GET['comm_pay_cash'],0,512);
	$sum_pay_cash=intval($_GET['sum_pay_cash']);
	$sum__=$db->promocode_apply($_GET['promocode'],$base_prices[$product_id][1],$product_id);
	if($sum__ !==false) {
		$promocode_id=$db->promocode_apply_info['id'];
		$promocode_msg= $db->promocode_apply_info['msg'];
		$sum=$sum__;
	} else
		$promocode_msg="<div class='alert alert-danger mt-1 small' >Промокод не найден</div>";
}

if(isset($_POST['promocode___'])) {
	$tm=time();
	$promo=mb_substr($_POST['promocode'],0,128);
	if($r=$db->fetch_assoc($db->query("SELECT * FROM promocodes
		WHERE product_id='$product_id'
			AND (tm1<='$tm' AND tm2>='$tm')
			AND promocode='$promo' ORDER BY id DESC LIMIT 1",0))
		)
	{
		if($r['uid']) {
			$fee_1=$r['fee_1'];
			$fee_2=$r['fee_2'];
		}
		$sum=$base_prices[$product_id][1];
		$dt2_promocode=date('d.m.Y H:i',$r['tm2']);
		if($r['discount']>0) {
			$d=intval($r['discount']);
			$promocode_msg="<div class='alert alert-success mt-1 small' >Промокод применен. Ваша скидка $d%. <br>* действует до $dt2_promocode</div>";
			if($d<100) {
				$sum=intval($sum*(100-$d)/100);
			} elseif($d<$sum) {
				$sum -=intval($d);
				$promocode_msg="<div class='alert alert-success mt-1 small' >Промокод применен. Ваша скидка $d р. <br>* действует до $dt2_promocode</div>";
			}
		} else {
			$sum=intval($r['price']);
			$promocode_msg="<div class='alert alert-success mt-1 small font16' >Промокод применен. Новая цена $sum р. <br>* действует до $dt2_promocode</div>";
			//print $promocode_msg;
		}
	} else
		$promocode_msg="<div class='alert alert-danger mt-1 small' >Промокод не найден</div>";
	//print "HERE_$sum"; exit;
}


$sum_disp=$sum;

$s1=0; //rassrochka
$s1_hidden="display:none;";
$promo_hidden=false;
if(isset($_GET['s1'])) 
	$s1=intval($_GET['s1']);
if($s1) {
	$promo_hidden=true; //"display:none;";
	$s1_hidden="";
	$sum=$s1;
	//$sum_disp=$base_prices[$product_id][2];
}

$order_description=explode(',',$base_prices[$product_id]['descr'])[0];
$order_term=$base_prices[$product_id]['term'];
$disp_cnt=$db->yoga_get_stock($uid,$product_id);
$disp_cnt="<span class='text-danger' >мало</span>";


$tm=time();
if($db->dlookup("id","promocodes","tm2>$tm AND  (product_id='$product_id' || product_id=1)") && $sum!=$base_prices[$product_id][2])
	$promo_hidden=false;
else
	$promo_hidden=true;

if($uid && $uid!=-1002) { //send invoice
	//$db->vktrade_send($uid, "order", $test=true);
	$db->save_comm($uid,0,"Зашел на страницу заказа: $order_description",22,$product_id);
	$db->notify($uid, "❗ Зашел на страницу заказа: $order_description");
	$db->mark_new($uid,1);
}

?>
<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title><?=$title?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=PT+Serif:ital,wght@0,400;0,700;1,400&family=Roboto:wght@400;500;700;900&display=swap"
      rel="stylesheet">
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
    <link rel="stylesheet" href="css/styles.css">

  <?include "wwl_pixels.inc.php";?>

  </head>
  <body class="body">
    <header class="header">
      <div class="header__container">
        <a class="header__logo" href="index.php"><img src="img/logo.svg" alt="logo"></a>
        <nav class="header__nav">
          <ul class="header__ul">
            <li class="header__li">
              <a href="<?=isset($_SESSION['back_url']) ? $_SESSION['back_url'] : 'index.php'?>" class="header__a one">О сервисе</a>
            </li>
            <li class="header__li">
              <a href="<?=isset($_SESSION['back_url']) ? $_SESSION['back_url'].'#rates' : 'index.php#rates'?>" class="header__a two active">Тарифы</a>
            </li>
            <li class="header__li">
              <a href="<?=isset($_SESSION['back_url']) ? $_SESSION['back_url'].'#rates' : 'index.php#partner'?>" class="header__a three">Партнерам</a>
            </li>
            <li class="header__li">
              <a href="https://ask.winwinland.ru" target='_blank' class="header__a four">Вопросы</a>
            </li>
          </ul>
        </nav>
        <a class="header__login" data-fancybox="" href="#login">Войти</a>
        <a class="header__mobile-login" data-fancybox href="#login">
          <svg
            width="25"
            height="25"
            viewBox="0 0 25 25"
            fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <circle cx="12.9595" cy="12.7576" r="12" fill="#7982A1" />
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M12.9597 5.11027C11.4595 5.11027 10.2434 6.27657 10.2434 7.71528C10.2434 9.15399 11.4595 10.3203 12.9597 10.3203C14.4598 10.3203 15.6759 9.15399 15.6759 7.71528C15.6759 6.27657 14.4598 5.11027 12.9597 5.11027ZM8.98974 7.71528C8.98974 5.61255 10.7671 3.90796 12.9597 3.90796C15.1522 3.90796 16.9296 5.61255 16.9296 7.71528C16.9296 9.818 15.1522 11.5226 12.9597 11.5226C10.7671 11.5226 8.98974 9.818 8.98974 7.71528Z"
              fill="white" />
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M10.9081 14.328C9.15626 14.328 7.73608 15.69 7.73608 17.3701C7.73608 17.4584 7.75289 17.5206 7.77052 17.5569C7.7854 17.5875 7.79816 17.5962 7.80881 17.6017C8.29595 17.856 9.66179 18.3357 12.9597 18.3357C16.2575 18.3357 17.6234 17.856 18.1105 17.6017C18.1212 17.5962 18.1339 17.5875 18.1488 17.5569C18.1664 17.5206 18.1833 17.4584 18.1833 17.3701C18.1833 15.69 16.7631 14.328 15.0112 14.328H10.9081ZM6.48242 17.3701C6.48242 15.026 8.46388 13.1257 10.9081 13.1257H15.0112C17.4555 13.1257 19.4369 15.026 19.4369 17.3701C19.4369 17.8005 19.2745 18.3631 18.7097 18.6578C17.9617 19.0482 16.3569 19.538 12.9597 19.538C9.56247 19.538 7.95766 19.0482 7.20959 18.6578C6.64487 18.3631 6.48242 17.8005 6.48242 17.3701Z"
              fill="white" />
          </svg>
        </a>
      </div>
    </header>

    <main class="main main-one">
      <section class="rate">
        <div class="container">
          <a class="rate__link" href="<?=isset($_SESSION['back_url']) ? $_SESSION['back_url'] : 'index.php'?> ">
            <svg
              width="30"
              height="15"
              viewBox="0 0 30 15"
              fill="none"
              xmlns="http://www.w3.org/2000/svg">
              <path
                d="M0.657394 6.80388C0.26687 7.1944 0.26687 7.82757 0.657394 8.21809L7.02136 14.5821C7.41188 14.9726 8.04505 14.9726 8.43557 14.5821C8.82609 14.1915 8.82609 13.5584 8.43557 13.1678L2.77872 7.51099L8.43557 1.85413C8.82609 1.46361 8.82609 0.830443 8.43557 0.439919C8.04505 0.0493941 7.41188 0.0493941 7.02136 0.439919L0.657394 6.80388ZM29.5376 6.51099L1.3645 6.51099V8.51099L29.5376 8.51099V6.51099Z"
                fill="#7982A1" />
            </svg>
            <span>К тарифам</span>
          </a>
          <h1 class="rate__title">
            <?=$order_description?>
            <span class="rate__month">
				<?
				if($order_term)
					print $order_term==30 ? "1 месяц" : "$order_term дней";
				?>
			</span>
          </h1>
          <div class="rate__price">
            <span class="rate__cost"> Стоимость: </span>
            <?
				function formatNumber($number) {
				  $number = strrev($number); // Reverse the number
				  $number = str_split($number, 3); // Split into groups of three digits
				  $number = implode('.', $number); // Join the groups with dots
				  $number = strrev($number); // Reverse the number back to its original order
				  return $number;
  				}
            ?>
            <span class="rate__rub" style='text-decoration: line-through;color: #888;text-decoration-thickness: 2px;text-decoration-style: solid;'> <?=formatNumber($price1)?>₽ </span>&nbsp;&nbsp;
            <span class="rate__rub"> <?=formatNumber($sum)?> ₽ </span>
          </div>
          <?
			if($tm=$db->price2_chk_timeto($uid,$product_id)) {
				print "<div style='margin-bottom:20px;'><div style='background-color:#06DCFF;padding:5px 5px 5px 5px;'>$client_name, у вас установлена спеццена.<br> Скидка действует до: <b>".date("d.m.Y H:i",$tm)."</b></div></div>";
			} elseif(!in_array($product_id,[]) && !$promo_hidden) { //не абонентская плата
          ?>
          <form class="promo" action="?<?=$_SERVER['QUERY_STRING']?>" enctype="multipart/form-data" method="GET">
            <div class="promo__left">
              <div class="promo__left-div1">У меня есть промокод:</div>
              <div class="promo__left-not-found"><?=$promocode_msg?></div>
            </div>
            <div class="promo__right">
              <input type="text" class="promo__right-input" placeholder="Промокод" name='promocode' value=''>
              <button type="submit" class="promo__right-btn">Применить</button>
			  <input type='hidden' name='product_id' value='<?=$product_id?>'>
			  <input type='hidden' name='uid' value='<?=$db->uid_md5($uid)?>'>
            </div>
          </form>
          <? } ?>

          <?
			$readonly_name=(!empty($readonly) && !empty($client_name)) ? "readonly" : "";
			$readonly_phone=(!empty($readonly) && !empty($client_phone)) ? "readonly" : "";
			$readonly_email=(!empty($readonly) && !empty($client_email)) ? "readonly" : "";
          ?>
          <form id='f1' class="pay form" action="#" enctype="multipart/form-data" method="POST">
            <div class="login__item">
              <input class="login__name login-input"
				id="client_name"
				name="fio" type="text"
				value="<?=$client_name?>"
				<?=$readonly_name?>
				placeholder="ФИО">
            </div>
            <div class="login__item">
              <input
				id="client_phone"
                class="login__phone_ login-input"
                name="phone"
                type="tel"
                value="<?=$client_phone?>"
				<?=$readonly_phone?>
                placeholder="Телефон">
            </div>
            <div class="login__item short">
              <input
				id="client_email"
                class="login__email login-input"
                name="email"
                type="email"
                value="<?=$client_email?>"
				<?=$readonly_email?>
                placeholder="Эл. почта">
            </div>
            <div class="pay__text-1">
				<?if(empty($readonly)) {?>
              Укажите действующий емэйл, к которому есть доступ. На этот емэйл будут отправлены
              данные для входа на платформу
              <?} else {?>
				Вы видите данные, на которые зарегистрирован аккаут в ВИНВИНЛЭНД.
				Они не редактируются, для изменения обратитесь в
				<a href='https://t.me/vkt_support_bot?start=<?=$db->uid_md5($uid)?>' class='' target='_blank'>техподдержку</a>.
				При оплате картой, данные карты будут вводиться на следующем экране,
				а деньги будут зачислены в оплату этого аккаунта.
				<?}?>
            </div>
            <div class="pay__text-2">
              При оплате заказа банковской картой, ввод реквизитов карты происходит в системе
              электронных платежей. Представленные вами платежные данные полностью
              защищены и никто, включая нашу организацию, не может их получить.
            </div>
            <div class="pay__checkbox" style='margin-bottom:20px;'>
              <div class="checkbox-wrapper">
                <input id="chk1" class="input__checkbox" type="checkbox" checked_ name="agree" />
              </div>
              <div class="pay__checkbox-right">
                Приступая к оплате, я соглашаюсь на
                <a href="https://winwinland.ru/agreement.pdf" target="_blank" rel="noopener noreferrer">
					Обработку персональных данных
				</a>
              </div>
            </div>
            <div class="pay__checkbox" style='margin-bottom:20px;'>
              <div class="checkbox-wrapper">
                <input id="chk3" class="input__checkbox" type="checkbox" checked_ name="agree" />
              </div>
              <div class="pay__checkbox-right">
                Приступая к оплате, я соглашаюсь
                <a href="https://winwinland.ru/privacypolicy.pdf" target="_blank" rel="noopener noreferrer">
					с политикой конфиденциальности
				</a>
              </div>
            </div>
            <div class="pay__checkbox">
              <div class="checkbox-wrapper">
                <input id="chk2" class="input__checkbox" type="checkbox" checked_ name="agree" />
              </div>
              <div class="pay__checkbox-right">
                Приступая к оплате, я соглашаюсь c
                <a href="https://winwinland.ru/dogovor.pdf" target="_blank" rel="noopener noreferrer">
					Условиями Договора
				</a>
              </div>
            </div>

			<input type="hidden" name="go_submit" value="yes"/>
			<input type="hidden" name="vk_uid" value="<?=$uid?>"/>
			<input type="hidden" name="product_id" value="<?=$product_id?>"/>
			<input type="hidden" name="sum_disp" value="<?=$sum_disp?>"/>
			<input type="hidden" name="bc" value="<?=$bc?>"/>
			<input type="hidden" name="client_ctrl_id" value="<?=$client_ctrl_id?>"/>
			<input type="hidden" name="promocode_id" value=<?=$promocode_id?> />
			<input type="hidden" name="fee_1" value=<?=$fee_1?> />
			<input type="hidden" name="fee_2" value=<?=$fee_2?> />
			<input type="hidden" name="csrf_token_order" value="<?php echo $_SESSION['csrf_token_order']; ?>">
			<input type="hidden" name="custom" value="<?=$custom?>" />

			<h3  style='margin-bottom:10px;'>
				<a style='color:#2196f3;' href='https://wwl.winwinland.ru/invoice.php?ctrl_id=<?=$ctrl_id?>&pids[0]=<?=$product_id?>&product_qnt[<?=$product_id?>]=1&product_price[<?=$product_id?>]=<?=$sum?>' class='' target='_blank'>Выписать счет для оплаты от юрлица</a>
				&nbsp;&nbsp;&nbsp;
				<a style='color:#ff008a;' href='' class=''  id='go_prodamus' target='_blank'>Оплатить картой иностранного банка</a>
			</h3>
            <button id='go_alfa' class="pay__button" type="button">Перейти к оплате</button>
          </form>
			<div class='text-left mt-5 p3'  style='text-align:center; margin-top:40px;'>
				<img class='p-1' src="img/mc.png" height='64' style='height:64px;'>
				<img class='p-1' src="img/visa.png" height='64'  style='height:64px;'>
				<img class='p-1' src="img/mir.png" height='64'  style='height:64px;'>
				<img class='p-1' src="img/verified.png" height='64'  style='height:64px;'>
				<img class='p-1' src="img/MIRaccept.png" height='64'  style='height:64px;'>
			</div>
        </div>
      </section>
    </main>

    <footer class="footer">
      <h2 class="footer__title">Контакты</h2>
      <div class="footer__company">АО «ВИНВИНЛЭНД»</div>
      <a class="footer__link" href="tel:8124251296">(812) 425-12-96</a>
      <div class="footer__links">
        Используя функции сервиса Winwinland, я соглашаюсь <br> c <a href="https://winwinland.ru/privacypolicy.pdf" target="_blank" rel="noopener noreferrer">Политикой конфиденциальности</a> и условиями <a href="https://winwinland.ru/dogovor.pdf" target="_blank" rel="noopener noreferrer">Договора-оферты</a>
      </div>
      <img src="img/footer-1.svg" alt="img" loading="lazy">
    </footer>

    <div class="login" id="login">
      <img class="login__img" src="img/modal-1.svg" alt="img" loading="lazy">
      <h3 class="login__title">Панель управления</h3>
      <form class="login__form form" action="#" enctype="multipart/form-data" method="POST">
        <div class="login__item">
          <input class="login__email login-input" name="Email" type="email" placeholder="Почта">
        </div>
        <div class="login__item">
          <input
            class="login__password login-input"
            name="Password"
            type="password"
            placeholder="Пароль">
        </div>
        <button class="login__btn" type="submit">Войти</button>
      </form>
      <div class="login__agree">
        Чтобы получить доступ, оформите любой
        <a class="back-to-home" href="index.php">Тарифный план</a>
      </div>
    </div>

    <div class="mobile-menu" id="mobile-menu">
      <nav class="mobile-menu__nav" onclick="event.stopPropagation()">
        <ul class="mobile-menu__ul">
          <li class="mobile-menu__li">
            <a class="mobile-menu__link one" href="<?=isset($_SESSION['back_url']) ? $_SESSION['back_url'] : 'index.php'?>">О сервисе</a>
          </li>
          <li class="mobile-menu__li">
            <a class="mobile-menu__link two" href="<?=isset($_SESSION['back_url']) ? $_SESSION['back_url'].'#rates' : 'index.php#rates'?>">Тарифы</a>
          </li>
          <li class="mobile-menu__li">
            <a class="mobile-menu__link three" href="<?=isset($_SESSION['back_url']) ? $_SESSION['back_url'].'#rates' : 'index.php#partner'?>">Партнерам</a>
          </li>
          <li class="mobile-menu__li">
            <a class="mobile-menu__link four" href="https://ask.winwinland.ru" target='_blank'>Вопросы</a>
          </li>
        </ul>
      </nav>
    </div>

    <a class="burger" onclick="event.stopPropagation()">
      <span class="burger__line burger__line-first"></span>
      <span class="burger__line burger__line-second"></span>
      <span class="burger__line burger__line-third"></span>
    </a>



    <script
      src="https://code.jquery.com/jquery-3.6.4.min.js"
      integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8="
      crossorigin="anonymous"></script>
    <script src="
    https://cdn.jsdelivr.net/npm/just-validate@4.2.0/dist/just-validate.production.min.js
    "></script>
    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/inputmask.min.js"
      integrity="sha512-czERuOifK1fy7MssE4JJ7d0Av55NPiU2Ymv4R6F0mOGpyPUb9HkP9DcEeE+Qj9In7hWQHGg0CqH1ELgNBJXqGA=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script src="js/main.js"></script>

<script type="text/javascript">
	console.log('test');
	$("#go_alfa").click(function() {
		console.log("HERE_");
		//alert($("#c_name").val());
		if($("#client_name").val().trim()=="") {
			alert("Необходимо указать ваше имя!");
		} else if($("#client_phone").val().trim()=="") {
			alert("Укажите, пожалуйста, телефон для связи!");
		} else if($("#client_email").val().trim()=="") {
			alert("Укажите, пожалуйста, email!");
		} else if(!$("#chk1").is(":checked")) {
			alert("Необходимо согласиться с обработкой персональных данных !");
		} else if(!$("#chk3").is(":checked")) {
			alert("Необходимо согласиться с политикой конфиденциальности !");
		} else if(!$("#chk2").is(":checked")) {
			alert("Необходимо согласиться с условиями договора !");
		} else {
			$('#f1').attr('action', 'https://for16.ru/d/1000/pay_alfa.php').submit();
		}
	});
	$("#go_prodamus").click(function() {
		event.preventDefault();
		//alert($("#c_name").val());
		if($("#client_name").val().trim()=="") {
			alert("Необходимо указать ваше имя!");
		} else if($("#client_phone").val().trim()=="") {
			alert("Укажите, пожалуйста, телефон для связи!");
		} else if($("#client_email").val().trim()=="") {
			alert("Укажите, пожалуйста, email!");
		} else if(!$("#chk1").is(":checked")) {
			alert("Необходимо согласиться с обработкой персональных данных !");
		} else if(!$("#chk3").is(":checked")) {
			alert("Необходимо согласиться с политикой конфиденциальности !");
		} else if(!$("#chk2").is(":checked")) {
			alert("Необходимо согласиться с условиями договора !");
		} else {
			$('#f1').attr('action', 'https://for16.ru/d/1000/pay_prodamus.php?k=1.12').submit();
		}
	});
</script>

  </body>
</html>
