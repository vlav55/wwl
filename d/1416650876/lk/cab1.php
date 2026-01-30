<?
include_once "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "../init.inc.php";
$t=new top(false);
$t->login();

$db=new db("vkt");
$db->telegram_bot="vkt";
$db->db200="https://for16.ru/d/1000";
//include_once "../prices.inc.php";
chdir("/var/www/vlav/data/www/wwl/d/1000/");
include "init.inc.php";

$user_id=$_SESSION['userid_sess'];
$klid=$db->get_klid($user_id);
$uid=$db->dlookup("uid","cards","id='$klid'");
$uid_md5=($uid)?$db->uid_md5($uid):0;

$email="";
$default_fee=10;
$default_fee2=0;
$klid=$db->dlookup("klid","users","id={$_SESSION['userid_sess']}");
$login=$db->dlookup("username","users","id={$_SESSION['userid_sess']}");
$real_user_name=$db->dlookup("real_user_name","users","id={$_SESSION['userid_sess']}");
//$klid=1704;
$fee=$db->dlookup("fee","users","id={$_SESSION['userid_sess']}");
$fee2=$db->dlookup("fee2","users","id={$_SESSION['userid_sess']}");

if(isset($_POST['do_details'])) {
	$db->query("UPDATE users SET bank_details='".$db->escape($_POST['msg'])."' WHERE klid='$klid'");
	print "<div class='alert alert-success' >Записано!</div>";
}
if(isset($_GET['do_chk_nalog'])) {
	if(isset($_GET['chk_nalog'])) {
		print "<div class='alert alert-success' ></div>";
		$db->query("UPDATE users SET chk_nalog=1 WHERE id={$_SESSION['userid_sess']}");
	} else {
		print "<div class='alert alert-danger' >Выплаты вознаграждения производятся только для самозанятых или ИП</div>";
		$db->query("UPDATE users SET chk_nalog=0 WHERE id={$_SESSION['userid_sess']}");
	}
}
$bank_details=$db->dlookup("bank_details","users","id={$_SESSION['userid_sess']}");
$chk_nalog_checked=$db->dlookup("chk_nalog","users","id={$_SESSION['userid_sess']}")?'CHECKED':'';
 
if(isset($_GET['spec'])) {
	if($typ=intval($_GET['spec'])) {
		$db->query("UPDATE partnerka_users SET typ='$typ' WHERE email='$email'",0);
		//print "<div class='alert alert-success' >Благодарим за уточнения!</div>";
	}
}
$r=$db->fetch_assoc($db->query("SELECT * FROM cards WHERE id=$klid",0));
include "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
$p=new partnerka($klid,$database);
$link=$p->get_partner_link($klid,'senler');
if(!$link)
	$link="не предусмотрено";
$link1=$p->get_partner_link($klid,'senler',1);
if(!$link1)
	$link1="не предусмотрено";
$link_land=$p->get_partner_link($klid,'land');
if(!$link_land)
	$link_land="не предусмотрено";
$link1_land=$p->get_partner_link($klid,'land',1);
if(!$link1_land)
	$link1_land="не предусмотрено";
$bc=$db->dlookup("bc","users","klid='$klid'");
if(!$bc) {
	$bc=0;
}

?>


<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <title>Партнерский кабинет</title>

  <meta property="og:type" content="website" />
  <meta property="og:title" content="Партнерский кабинет" />
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
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body class="body">
  <header class="header">
    <div class="header__container">
      <a class="header__logo" href="#top"><img src="img/logo.svg" alt="logo">
      </a>
      <nav class="header__nav">
        <ul class="header__ul">
          <li class="header__li">
            <a href="#service" class="header__a one active">Проценты</a>
          </li>
          <li class="header__li">
            <a href="#rates" class="header__a two">Ссылки</a>
          </li>
          <li class="header__li">
            <a href="#partner" class="header__a three">Материалы</a>
          </li>
          <li class="header__li">
            <a href="#questions" class="header__a four">Сводка</a>
          </li>
        </ul>
      </nav>
      <a class="header__login" data-fancybox href="#login">Вопрос</a>
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
    <section class="service" id='top'>
      <div class="container">
        <div class="possibilities">
			<div>	
			  <h2 class="possibilities__title text-center">Партнерский кабинет</h2>
				<p>Имя: <b><?=$r['name']?> <?=$r['surname']?></b></p>
				<p>Телефон: <b><?=$r['mob_search']?></b></p>
				<p>E-mail: <b><?=$r['email']?></b></p>
				<p>Партнерский код: <b><?=$bc?></b></p>

				<p>Реквизиты для выплаты вознаграждения:</p>
				<div class='' id='details'>  
					<form method='POST' action='?uid=<?=$uid_md5?>'>
						<div>
						  <textarea class="login-input" style='margin-bottom:5px;' rows="5" id="comment" name='msg'><?=$bank_details?></textarea>
						 <button class='button1' name='do_details' value='yes'>Сохранить</button>
						</div>
					</form>
				</div>
			</div>
			
			<div class='mb-5' id='service'></div>
			<div>
				<h3 class='possibilities__suptitle title text-center' >Проценты вознаграждений
					<a href='#parner_fee' data-toggle='collapse' class='' title='Развернуть'>
						<i class="fa fa-folder-open-o"></i>
					</a>
				</h3>
				<?
					$res=$db->query("SELECT * FROM product WHERE (fee_1>0 OR fee_2>0)");
					$collapse=($db->num_rows($res)>5)?"collapse":"";
				?>
				<div class='<?=$collapse?>' id='parner_fee'>
					<table class='table table-striped' >
						<thead>
							<tr>
								<th>Наименование</th>
								<th>Цена обычная</th>
								<th>Цена со скидкой</th>
								<th>Уровень 1,%</th>
								<th>Уровень 2,%</th>
							</tr>
						</thead>
					<?
					while($r=$db->fetch_assoc($res)) {
						print "<tr>
							<td>{$r['descr']}</td>
							<td>{$r['price1']}р.</td>
							<td>{$r['price2']}р.</td>
							<td>{$r['fee_1']}%</td>
							<td>{$r['fee_2']}%</td>
						</tr>";
					}
					print "<tr class='font-weight-bold' >
						<td>Остальные продукты</td>
						<td></td>
						<td></td>
						<td>$fee%</td>
						<td>$fee2%</td>
					</tr>";
					?>
					
					</table>
				</div>
			</div>
			
			<div class='mb-5' id='rates'></div>
			<div>
				<h3 class='possibilities__suptitle title py-4 text-center'>
					Партнерские ссылки
					<?
					$res=$db->query("SELECT * FROM lands WHERE del=0 AND product_id=0");
					$collapse=($db->num_rows($res)>5)?"collapse":"";
					?>
					<a href='#parner_links' data-toggle='collapse' class='' title='Развернуть'>
						<i class="fa fa-folder-open-o"></i>
					</a>
				</h3>
				<div class='card p-3 <?=$collapse?>' id='parner_links'>
					<?
					print "<div class='p-2' >
							<div>
							<i class='' >ВК</i> Ваша партнерская ссылка для ВК
							<a href='$link' target='_blank' title='перейти на лэндинг в новом окне'><span class='fa fa-arrow-circle-right'></span></a>
							<div class=''><span  class=''>
								$link
							</span></div>
							</div>
						</div>";
					print "<div class='p-2' >
							<div>
							<i class='' >ВК (партнерский лэндинг)</i> Ссылка для приглашения в партнерскую программу для ВК
							<a href='$link1' target='_blank' title='перейти на лэндинг в новом окне'><span class='fa fa-arrow-circle-right'></span></a>
							<div class=''><span  class=''>
								$link1
							</span></div>
							</div>
						</div>";
					
					while($r=$db->fetch_assoc($res)) {
						$link=str_replace("https:/","https://",str_replace("//","/","{$r['land_url']}/?bc=$bc"));
						$label_partner_land=($r['fl_partner_land'])?"<b>(партнерский лэндинг)</b>":"";
						print "<div class='p-2' >
								<div>
								<i class='' >Лэндинг #{$r['land_num']} $label_partner_land</i> {$r['land_name']}
								<a href='$link' target='_blank' title='перейти на лэндинг в новом окне'><span class='fa fa-arrow-circle-right'></span></a>
								<div class=''><span  class=''>
									$link
								</span></div>
								</div>
							</div>";
					}

					?>
					<p class='small mute' >* По этим ссылкам ваши знакомые могут зарегистрироваться и будут закреплены за вами.
					Все просто, прозрачно и понятно — просто разместите эту ссылку на своих
					страницах в соцсетях, передайте ее друзьям и знакомым и расскажите им о нас.
					</p>
					<p class='small mute' >** По этой ссылкам с пометкой <b>партнерский лэндинг</b> ваши знакомые смогут не только зарегистрироваться, но и принять участие в данной партнерской программе, как и вы.
					</p>
				</div>
			</div>

			<div class='mb-5' id='questions'></div>
			<div class=''>
				<h3 class='p-4 text-center possibilities__suptitle title ' >Сводка</h3>
				
				<?
				include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
				$db=new partnerka($klid,$database);
				$db->sids_for_cnt_reg=[12];
				$email=$db->dlookup("email","users","klid='$klid'");
				$bank_details=$db->dlookup("bank_details","users","klid='$klid'");

				$wday=date("N")-1;
				$year=date("Y"); $month=date("m");
				$last_month=($month==1)?12:$month-1;
				$year1=($last_month==12)?$year-1:$year;
				//print "01.$last_month.$year1"; exit;
				$month=(intval($month)<10)?"0".intval($month):$month;

				$tm1=$db->date2tm("01.01.$year"); $tm2=time();
				$cnt_reg_year=$db->cnt_reg($klid,$tm1,$tm2);
				$sum_buy_year=$db->sum_buy($klid,$tm1,$tm2,0);
				$sum_fee_year=$db->sum_fee($klid,$tm1,$tm2,0);
				$sum_pay_year=$db->sum_pay($klid,$tm1,$tm2,0);

				$tm1=$db->date2tm("01.$month.$year"); $tm2=time();
				$cnt_reg_this_month=$db->cnt_reg($klid,$tm1,$tm2); 
				$sum_buy_this_month=$db->sum_buy($klid,$tm1,$tm2);
				$sum_fee_this_month=$db->sum_fee($klid,$tm1,$tm2);
				$sum_pay_this_month=$db->sum_pay($klid,$tm1,$tm2,0);

				$tm1=$db->date2tm("01.$last_month.$year1"); $tm2=$db->date2tm("01.$month.$year");
				$cnt_reg_last_month=$db->cnt_reg($klid,$tm1,$tm2);
				$sum_buy_last_month=$db->sum_buy($klid,$tm1,$tm2);
				$sum_fee_last_month=$db->sum_fee($klid,$tm1,$tm2);
				$sum_pay_last_month=$db->sum_pay($klid,$tm1,$tm2,0);
				
				$tm1=$db->dt1(time()-($wday*24*60*60)); $tm2=time();
				$cnt_reg_this_week=$db->cnt_reg($klid,$tm1,$tm2);
				$sum_buy_this_week=$db->sum_buy($klid,$tm1,$tm2);
				$sum_fee_this_week=$db->sum_fee($klid,$tm1,$tm2);
				$sum_pay_this_week=$db->sum_pay($klid,$tm1,$tm2,0);

				$tm1=$db->dt1(time()-(1*24*60*60)); $tm2=$db->dt2(time()-(1*24*60*60));
				$cnt_reg_yesterday=$db->cnt_reg($klid,$tm1,$tm2);
				$sum_buy_yesterday=$db->sum_buy($klid,$tm1,$tm2,0);
				$sum_fee_yesterday=$db->sum_fee($klid,$tm1,$tm2,0);
				$sum_pay_yesterday=$db->sum_pay($klid,$tm1,$tm2,0);

				$tm1=$db->dt1(time()); $tm2=time();
				$cnt_reg_today=$db->cnt_reg($klid,$tm1,$tm2);
				$sum_buy_today=$db->sum_buy($klid,$tm1,$tm2);
				$sum_fee_today=$db->sum_fee($klid,$tm1,$tm2);
				$sum_pay_today=$db->sum_pay($klid,$tm1,$tm2,0);
				?>
				<table class='table' >
					<thead>
						<tr>
							<th></th>
							<th>Сегодня</th>
							<th>Вчера</th>
							<th>Неделя</th>
							<th>Месяц</th>
							<th>Прошлый месяц</th>
							<th>С начала года</th>
						</tr>
					</thead>
					<tbody>
						<tr><td>Количество регистраций</td><td><?=$cnt_reg_today?></td><td><?=$cnt_reg_yesterday?></td><td><?=$cnt_reg_this_week?></td><td><?=$cnt_reg_this_month?></td><td><?=$cnt_reg_last_month?></td><td><?=$cnt_reg_year?></td></tr>
						<tr><td>Сумма оплат</td><td><?=$sum_buy_today?></td><td><?=$sum_buy_yesterday?></td><td><?=$sum_buy_this_week?></td><td><?=$sum_buy_this_month?></td><td><?=$sum_buy_last_month?></td><td><?=$sum_buy_year?></td></tr>
						<tr><td>Сумма комиссий</td><td><?=$sum_fee_today?></td><td><?=$sum_fee_yesterday?></td><td><?=$sum_fee_this_week?></td><td><?=$sum_fee_this_month?></td><td><?=$sum_fee_last_month?></td><td><?=$sum_fee_year?></td></tr>
						<tr><td>Выплачено</td><td><?=$sum_pay_today?></td><td><?=$sum_pay_yesterday?></td><td><?=$sum_pay_this_week?></td><td><?=$sum_pay_this_month?></td><td><?=$sum_pay_last_month?></td><td><?=$sum_pay_year?></td></tr>
					</tbody>
				</table>
			</div>

			<div class='mb-5' id='pay'></div>
			<div class='' >
				<?
				//~ print "
					//~ <h2>Зарегистрировались по партнерской ссылке</h2>
					//~ <p>(последние 50 рефералов)</p>";
				//~ $res=$db->query("SELECT * FROM cards WHERE utm_affiliate='$klid' ORDER BY tm DESC LIMIT 50",0);
				//~ print "<table class='table table-striped'>
						//~ <thead>
						  //~ <tr>
							//~ <th>№</th>
							//~ <th>Дата первой рег</th>
							//~ <th>Имя</th>
							//~ <th>Город</th>
							//~ <th>Был на вебинаре</th>
						  //~ </tr>
						//~ </thead>
						//~ <tbody>
						//~ ";
				//~ $n=1;
				//~ while($r=$db->fetch_assoc($res)) {
					//~ $reg=($db->dlookup("id","msgs","uid='{$r['uid']}' AND source_id='12'"))?"Да":"Нет <i class='far fa-question-circle' style='font-size:14px' title='Очень важно, чтобы человек посмотрел семинар. Поэтому нужно объяснить важность семинара и убедить потратить на  просмотр время!'></i>";
					//~ print "<tr>
						//~ <td>$n</td>
						//~ <td>".date("d.m.Y",$r['tm'])."</td>
						//~ <td>{$r['name']}</td>
						//~ <td>".$r['city']."...</td>
						//~ <td>$reg</td>
						//~ </tr>";
					//~ $n++;
				//~ }
				//~ print "</tbody></table>";

				print "<h3 p-4 text-center possibilities__suptitle title style='text-align:center;'>Начисления
						<a href='#a1' data-toggle='collapse' class='' title='развернуть'>
							<i class='fa fa-folder-open-o'></i>
						</a>
					</h3>\n";
				print "<div id='a1' class='collapse' >\n";
				//~ if($levels==2)
					//~ print "<div class='alert alert-info' >Установлен учет только второго уровня оплат ($fee2%)</div>";
				print "<table class='table table-striped'>\n
						<thead>
						  <tr>
							<th>№</th>
							<th>Дата</th>
							<th>Чья продажа</th>
							<th>Имя</th>
							<th>ОПЛАТА от клиента</th>
							<th>% вознагр.</th>
							<th>Начислено партнеру</th>
							<th>Продукт</th>
						  </tr>
						</thead>\n
						<tbody>\n
						";
				$n=1;
				$res=$db->query("SELECT * FROM partnerka_op WHERE klid_up='$klid' ORDER BY tm DESC LIMIT 50 ");
				while($r=$db->fetch_assoc($res)) {
					$name=$db->dlookup("name","cards","uid='{$r['uid']}'")." ".$db->dlookup("surname","cards","uid='{$r['uid']}'");
					$sum=$r['amount'];
					$fee=$r['fee'];
					if($r['avangard_id'])
						$product=$db->dlookup("order_descr","avangard","id='{$r['avangard_id']}'");
					elseif($r['product_id']==1001)
						$product="ПРИВЕТСТВЕННЫЕ БАЛЛЫ";
					if($r['level']==1) {
						$vid="собств";
					} else {
						$vid=$db->dlookup("real_user_name","users","klid='{$r['klid']}'");
					}
					print "<tr>
						<td>$n</td>
						<td>".date("d.m.Y",$r['tm'])."</td>
						<td>$vid</td>
						<td>".$name."</td>
						<td>$sum</td>
						<td>$fee%</td>
						<td>".round($sum*$fee/100,0)."</td>
						<td>$product</td>
						</tr>\n";
					$n++;
				}
				print "</tbody></table>\n";
				print "</div>\n";

				?>
		<!--
				<div class='mt-5' ><a href='#' class='' target=''>Выгрузить в csv</a></div>
		-->
			</div>

			<div class='mb-5' id=''></div>
			<div>
				<?
				print "<h3 p-4 text-center possibilities__suptitle title style='text-align:center;'>Выплаты
						<a href='#a2' data-toggle='collapse' class='' title='развернуть'>
							<i class='fa fa-folder-open-o'></i>
						</a>
					</h3>\n";
				print "<div id='a2' class='collapse' >";
				
				print "<table class='table table-striped'>
						<thead>
						  <tr>
							<th>Дата</th>
							<th>Сумма</th>
							<th>Вид</th>
							<th>Комментарий</th>
						</tr>
						</thead>
						<tbody>";
				$res=$db->query("SELECT * FROM partnerka_pay WHERE klid=$klid AND sum_pay>0 ORDER BY tm DESC");
				while($r=$db->fetch_assoc($res)) {
					$dt=date("d.m.Y",$r['tm']);
					$vid=($r['vid']==1)?"банк":"зачет";
					print "<tr>
						<td>$dt</td>
						<td>{$r['sum_pay']}</td>
						<td>$vid</td>
						<td>{$r['comm']}</td>
						</tr>";
				}
				print "</tbody></table>";
				print "</div>";
				?>
			</div>
		</div>
	  </div>
	</section>
    <?
	function formatNumber($number) {
	  $number = strrev($number); // Reverse the number
	  $number = str_split($number, 3); // Split into groups of three digits
	  $number = implode('.', $number); // Join the groups with dots
	  $number = strrev($number); // Reverse the number back to its original order
	  return $number;
	}
	?>

  </main>
  
<br><br><br><br><br>
  <footer class="footer">
    <h2 class="footer__title">Контакты</h2>
    <div class="footer__company">ООО «ВинВинЛэнд»</div>
    <a class="footer__link" href="tel:8124251296">(812) 425-12-96</a>
    <div class="footer__links">
      Используя функции сервиса Winwinland, я соглашаюсь <br> c <a href="https://winwinland.ru/privacypolicy.pdf"
        target="_blank" rel="noopener noreferrer">Политикой конфиденциальности</a> и условиями <a
        href="https://winwinland.ru/dogovor.pdf" target="_blank" rel="noopener noreferrer">Договора-оферты</a>
    </div>
    <img src="img/footer-1.svg" alt="img" loading="lazy">
  </footer>

  <div class="scrollUp">
    <a href="#service"><img src="img/arrow-up.svg" alt="scrollUp"> </a>
  </div>

  <div class="login" id="login">
    <img class="login__img" src="img/modal-1.svg" alt="img" loading="lazy">
    <h3 class="login__title">Ваш вопрос</h3>
    <form class="login__form form" action="" enctype="multipart/form-data" method="POST">
      <div class="login__item" id='login_div'>
        <input class="login__name login-input" name="q" type="text" placeholder="">
        <input type='hidden' name='uid' value='<?=$uid?>'>
      </div>
      <button class="login__btn" type="submit" name='send' value='yes'>Отправить</button>
    </form>
  </div>

  <div class="mobile-menu" id="mobile-menu">
    <nav class="mobile-menu__nav" onclick="event.stopPropagation()">
      <ul class="mobile-menu__ul">
        <li class="mobile-menu__li">
          <a class="mobile-menu__link" href="#service">Проценты</a>
        </li>
        <li class="mobile-menu__li">
          <a class="mobile-menu__link" href="#rates">Ссылки</a>
        </li>
        <li class="mobile-menu__li">
          <a class="mobile-menu__link" href="#partner">Материалы</a>
        </li>
        <li class="mobile-menu__li">
          <a class="mobile-menu__link" href="#questions">Сводка</a>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
</body>

</html>
