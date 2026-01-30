<?
exit;
chdir("lk/");
include_once "/var/www/vlav/data/www/wwl/inc/top.class.php";
include_once "../init.inc.php";
$t=new top(false);

?>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
	<script>
		window.Telegram.WebApp.ready();
		const user = window.Telegram.WebApp.initDataUnsafe.user;

		// Function to get query parameters
		function getQueryParam(param) {
			const urlParams = new URLSearchParams(window.location.search);
			return urlParams.get(param);
		}

		// Check for the presence of the 'u' and 'tg_id' parameters
		const uParam = getQueryParam('u');
		const tgIdParam = getQueryParam('tg_id');
		const tgIdParamInt = parseInt(tgIdParam, 10);
		// Correctly check if 'user' is not undefined
		if (typeof user !== 'undefined' && user) {
			const userId = user.id;
			// Reload the page with ?tg_id=userId if neither 'u' nor 'tg_id' parameter is present
			if (!uParam && !tgIdParam) {
				// Build a new URL with the tg_id parameter
				const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + "?tg_id=" + userId;
				document.writeln("<a href='newUrl'>"+newUrl+"</a><br>You are redirecting soon... or tap link");
				window.location.href = newUrl; // Redirect to the new URL
			} else if(tgIdParamInt !== userId) {
				document.writeln(tgIdParamInt+" "+userId);
				window.location.href = "?tg_id=0";
			}
		}
	</script>
<?
//$t->notify_me(print_r($_GET,true));
if(isset($_GET['tg_id'])) {
	if(!$tg_id=intval($_GET['tg_id'])) {
		print "error 9";
		exit;
	}
	if(!$_GET['u']=$t->dlookup("direct_code","users","id>2 AND del=0 AND telegram_id='$tg_id'")) {
		print "Sorry, you are not registered as a partner for the company!";
		exit;
	}
}


$t->login();

$db=new db("vkt");
$partnerka_adlink=$db->dlookup("partnerka_adlink","0ctrl","id='$ctrl_id'");
$company=$db->dlookup("company","0ctrl","id='$ctrl_id'");
//print "HERE_$partnerka_adlink"; exit;
//$db->telegram_bot="vkt";
//$db->db200="https://for16.ru/d/1000";
//include_once "../prices.inc.php";
//chdir("/var/www/vlav/data/www/wwl/d/1000/");

chdir("..");
//include "init.inc.php";
$db->connect($database);

$user_id=$_SESSION['userid_sess'];
$klid=$db->get_klid($user_id);
if( ($user_id<=3 && $database!='vkt') || (!$db->is_partner_db($db->dlookup("uid","cards","id='$klid'")) && $user_id>3) ) {
	print "<p class='alert alert-warning' >Ошибка входа в партнерский кабинет. Ссылка недействительна! ($user_id $kli)</p>";
	exit;
	print "<script>location='$DB200/dash.php'</script>";
	exit;
}

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

include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
$p=new partnerka($klid,$database);
$rest_fee=$p->rest_fee($klid);

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
$p=new partnerka($klid,$database);

$link=$p->get_partner_link($klid,'senler');
$link1=$p->get_partner_link($klid,'senler',1);

$bc=$db->dlookup("bc","users","klid='$klid'");
if(!$bc) {
	$bc=0;
}

$company_logo=file_exists("tg_files/logo.jpg")?"tg_files/logo.jpg":"";

if(!empty($company_logo)) {

	// Путь к исходному изображению
	$sourceImage = $company_logo;

	// Создание изображения с помощью imagecreatefromjpeg
	$image = imagecreatefromjpeg($sourceImage);

	// Масштабирование изображения по ширине до 200 пикселей
	$width = 200;
	$height = floor(imagesy($image) * ($width / imagesx($image)));
	$scaledImage = imagescale($image, $width, $height);

	// Обрезка изображения до размера 200x50 пикселей посередине
	$targetWidth = 200;
	$targetHeight = 50;
	$cropX = 0;
	$cropY = floor(($height - $targetHeight) / 2);
	$croppedImage = imagecrop($scaledImage, ['x' => $cropX, 'y' => $cropY, 'width' => $targetWidth, 'height' => $targetHeight]);

	// Путь для сохранения обрезанного изображения
	$destinationImage = 'tg_files/logo200x50.jpg';

	// Сохранение изображения с помощью imagejpeg
	imagejpeg($croppedImage, $destinationImage);

	// Освобождение памяти
	imagedestroy($image);
	imagedestroy($scaledImage);
	imagedestroy($croppedImage);

	// HTML-код для отображения обрезанного изображения
	//~ echo '<img src="' . $destinationImage . '" alt="Обрезанное изображение">';
}

?>


<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="https://winwinland.ru/img/logo/favicon.png" type="image/x-icon">
  <title>Партнерский кабинет</title>

  <meta property="og:type" content="website" />
  <meta property="og:title" content="Партнерский кабинет" />
  <meta property="og:description" content="<?=$company_name?>" />
  <meta property="og:image" content="<?=$DB200."/".$company_logo?>" />
  <meta property="vk:image" content="<?=$DB200."/".$company_logo?>" />

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=PT+Serif:ital,wght@0,400;0,700;1,400&family=Roboto:wght@400;500;700;900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
  
  <link rel="stylesheet" href="https://for16.ru/winwinland/fonts/fonts.css">
  <link rel="stylesheet" href="https://for16.ru/winwinland/css/styles.css">
  
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>

 <script>
    function copySpanContent(span_id) {
      // Get the span element by its ID
      var spanElement = document.getElementById(span_id);

      // Create a temporary input element
      var tempInput = document.createElement("input");

      // Set the value of the input element to the content of the span
      tempInput.value = spanElement.textContent;

      // Append the input element to the document
      document.body.appendChild(tempInput);

      // Select the content of the input element
      tempInput.select();

      // Copy the selected content to the clipboard
      document.execCommand("copy");

      // Remove the temporary input element
      document.body.removeChild(tempInput);

      // Alert the user that the content has been copied
      alert("Ссылка скопирована!");
    }
  </script>

</head>

<body class="body">
  <header class="header">
    <div class="header__container">
      <a class="header__logo" href="#top"><img src="<?=$DB200."/".$destinationImage?>" alt="logo">
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
				<?if(!empty(trim($r['email']))) { ?><p>E-mail: <b><?=$r['email']?></b></p> <?}?>
				<p>Партнерский код: <b><?=$bc?></b></p>
			<?if(!empty($oferta_referal)) {?>
				<p>Договор об участии в партнерской программе <a href='<?=$oferta_referal?>' class='' target=''>находится по ссылке</a> <br>
				<span class='small' >Приняв участие в партнерской программе и пользуясь этим партнерским кабинетом вы подтверждаете согласие с договором.</span>
				</p>
			<? } ?>
				<p>Реквизиты для выплаты вознаграждения:</p>
				<div class='' id='details'>  
					<form method='POST' action='?uid=<?=$uid_md5?>&u=<?=$_GET['u']?>'>
						<div>
						  <textarea class="login-input" style='margin-bottom:5px;' rows="3" id="comment" name='msg'><?=$bank_details?></textarea>
						 <button class='button1' name='do_details' value='yes'>Сохранить</button>
						 <button class='button1 bg-primary'  data-toggle="modal" data-target="#cashoutModal" onclick="event.preventDefault();">Вывести средства</button>
						</div>
					</form>
				</div>
			</div>
			
			<div class='mb-1' id='service'></div>
			<?
				$res=$db->query("SELECT * FROM product WHERE id>0 AND del=0 AND (fee_1>0 OR fee_2>0)");
				$collapse=($db->num_rows($res)>5)?"collapse":"";

				$disp_links=$db->num_rows($db->query("SELECT * FROM lands WHERE del=0 AND fl_not_disp_in_cab=0")) ? true : false;
				if($link || $link1) //vk
					$disp_links=true;
				
			?>
			<?if($disp_links) {?>
			<div>
				<h3 class='pt-5 possibilities__suptitle title text-center' >Проценты вознаграждений
					<a href='#partner_fee' data-toggle='collapse' class='' title='Развернуть'>
						<i class="fa fa-folder-open"></i>
					</a>
				</h3>
				<div style="margin: 0 auto; display: table;">
				<div class='<?=$collapse?>' id='partner_fee'>
					<table class='table table-striped table-responsive' >
						<thead>
							<tr>
								<th>Наименование</th>
								<th>Цена</th>
<!--
								<th>Цена со скидкой</th>
-->
								<th>Уровень 1</th>
								<th>Уровень 2</th>
								<th>На сколько продаж начисл вознагр (**)</th>
							</tr>
						</thead>
					<?
					while($r=$db->fetch_assoc($res)) {
						if(!$fee1=$db->dlookup("fee_1","partnerka_spec","uid='$uid' AND pid='{$r['id']}'"))
							$fee1=$r['fee_1'];
						if(!$fee2=$db->dlookup("fee_2","partnerka_spec","uid='$uid' AND pid='{$r['id']}'"))
							$fee2=$r['fee_2'];
						if(!$fee_cnt=$db->dlookup("fee_cnt","partnerka_spec","uid='$uid' AND pid='{$r['id']}'"))
							$fee_cnt=$r['fee_cnt'];
						$fee1=($fee1<=100) ? $fee1."%" : $fee1."р.";
						$fee2=($fee2<=100) ? $fee2."%" : $fee2."р.";
						$fee_cnt=(!$fee_cnt) ? "без огр" : $fee_cnt;

						$price1=($r['price1']) ? $r['price1']."р." : "-";
						$price2=($r['price2']) ? $r['price2']."р." : "-";
						
						print "<tr>
							<td>{$r['descr']}</td>
							<td>$price1</td>
							<!-- <td>$price2</td> -->
							<td>$fee1</td>
							<td>$fee2</td>
							<td>$fee_cnt</td>
						</tr>";
					}
					//~ print "<tr class='font-weight-bold' >
						//~ <td>Остальные продукты</td>
						//~ <td></td>
						//~ <td></td>
						//~ <td>$fee</td>
						//~ <td>$fee2</td>
						//~ <td>$fee_cnt</td>
					//~ </tr>";
					?>
					
					</table>
<!--
					<p class="small text-info" >* если значение меньше 100,
					то оно интерпретируется, как процент. 100 и больше -
					как фиксированная сумма вознаграждения</p>
					<p class="small text-info" >** если значение 1 - вознаграждение начисляется только с первой продажи
					этого продукта, 2 - с двух первых продаж и т.д.. 0 - без ограничений</p>
-->
				</div>
				</div>
			</div>
			
			<div class='mb-1' id='rates'></div>
			<div>
				<h3 class='possibilities__suptitle title text-center'>
					Партнерские ссылки
					<?
					$res=$db->query("SELECT * FROM lands WHERE del=0 AND fl_not_disp_in_cab=0");
					$collapse=($db->num_rows($res)>3)?"collapse":"";
					?>
					<a href='#partner_links' data-toggle='collapse' class='' title='Развернуть'>
						<i class="fa fa-folder-open"></i>
					</a>
				</h3>
					<?
					
				if($ctrl_id==1) {
					$pattern = '/(?:RewriteRule\s\^)(.*?)\/\?\$.*?https:\/\/for16\.ru\/d\/1000\/(.*?)\/\\\\\?bc=(.*?)(?:\s\[R=301,L])/';
					$arr=file("/var/www/vlav/data/www/wwl/winwinland/.htaccess");
					$suf=false;
					foreach($arr AS $str) {
						if(preg_match($pattern,$str,$m)) {
							if($bc==$m[3]) {
								$suf=$m[1];
							}
							//print "$suf $bc_code <br>";
							
						}
					}
					if($suf) {
						print "<div class='p-3 bg-primary text-white' >
								<h3>Вы ВИП партнер. Ваша основная ссылка для рекомендаций ВИНВИНЛЭНД:</h3>
								<span  class='h3' id='link_vip'>https://winwinland.ru/$suf</span>
								<a href='javascript:copySpanContent(\"link_vip\");' class='text-white' target='' title='скопировать ссылку'>
									<i class='fa fa-copy' ></i> 
								</a>
							</div>";
					}
				}

					
				print "<div class='card_ py-3 $collapse' id='partner_links'>";
					if($link)
					print "<div class='p-2 my-1 card' >
							<div>
							<i class=' font-weight-bold badge p-2 border bg-white mr-3' >ВК</i> Ваша партнерская ссылка для ВК
							<a href='$link' target='_blank' title='перейти на лэндинг в новом окне'><span class='fa fa-arrow-circle-right'></span></a>
							<div class=''><span  class='' id='vk1'><b>$link</b></span>
							<a href='javascript:copySpanContent(\"vk1\");' target='' title='скопировать ссылку'>
							<i class='fa fa-copy'></i>
							</a></div>
							</div>
						</div>";
					if($link1)
					print "<div class='p-2 my-1 card' >
							<div>
							<i class=' font-weight-bold  badge p-2 bg-white mr-3 border' >ВК (партнерский лэндинг)</i> Ссылка для приглашения в партнерскую программу для ВК
							<a href='$link1' target='_blank' title='перейти на лэндинг в новом окне'><span class='fa fa-arrow-circle-right'></span></a>
							<div class=''><span  class='' id='vk2'><b>$link1</b></span>
							<a href='javascript:copySpanContent(\"vk2\");' class='' target='' title='скопировать ссылку'>
							<i class='fa fa-copy' ></i>
							</a>
							</div>
							</div>
						</div>";
					
					while($r=$db->fetch_assoc($res)) {
						$arr = parse_url($r['land_url']);
						//$db->notify_me(print_r($arr,true));
						$link= (isset($arr['scheme']) ? $arr['scheme'] . '://' : '') .
							   (isset($arr['host']) ? $arr['host'] : '') .
							   (isset($arr['port']) ? ':' . $arr['port'] : '') .
							   (isset($arr['path']) ? $arr['path'] : '');
						if(isset($arr['query']))
							$link.="?{$arr['query']}$bc";
						else
							$link.="/?bc=$bc";
						//$link=str_replace("https:/","https://",str_replace("//","/","{$r['land_url']}/?bc=$bc"));
						$label_partner_land=($r['fl_partner_land'])?"<b>(партнерский лэндинг)</b>":"";
						print "<div class='p-2 my-1 card' >
								<div>
								<i class='font-weight-bold  badge p-2 bg-white mr-3 border' >ТГ: $label_partner_land</i> {$r['land_name']}
								<a href='$link' target='_blank' title='перейти на лэндинг в новом окне'><span class='fa fa-arrow-circle-right'></span></a>
								<div class=''>
									<span  class='' id=link_{$r['id']}><b>$link</b></span>
									<a href='javascript:copySpanContent(\"link_{$r['id']}\");' class='' target='' title='скопировать ссылку'>
										<i class='fa fa-copy' ></i>
									</a>
								</div>
								</div>
							</div>";
					}

					?>
					<p class='small mute' >По этим ссылкам ваши знакомые могут зарегистрироваться и будут закреплены за вами.
					Все просто, прозрачно и понятно — просто разместите эту ссылку на своих
					страницах в соцсетях, передайте ее друзьям и знакомым и расскажите им о нас.
					</p>
					<?if($link1 || $db->dlookup("id","lands","del=0 AND fl_partner_land=1 AND fl_not_disp_in_cab=0")) {?>
					<p class='small mute' >По ссылке с пометкой <b>партнерский лэндинг</b> ваши знакомые смогут не только зарегистрироваться, но и принять участие в данной партнерской программе, как и вы.
					</p>
					<?}?>
				</div>
			</div>
			<? } ?>

			<?
			$res=$db->query("SELECT * FROM promocodes WHERE uid='$uid' AND tm2>".time()."  ORDER BY id DESC");
			if($db->num_rows($res)) {
				$collapse=($db->num_rows($res)>3)?"collapse":"";
				?>
			<div class='mb-4' id='rates'></div>
			<h3 class='possibilities__suptitle title text-center'>
				Реферальные промокоды
				<a href='#partner_promocodes' data-toggle='collapse' class='' title='Развернуть'>
					<i class="fa fa-folder-open"></i>
				</a>
			</h3>
			<div  style="margin: 0 auto; display: table;">
			<div  class='card_ py-3 <?=$collapse?>' id='partner_promocodes'>
				<table class="table table-responsive">
					<thead>
						<tr>
							<th>Промокод</th>
							<th>Действует по</th>
							<th>Для продукта</th>
							<th>На спеццену</th>
							<th>На скидку</th>
							<th>Вознагр 1</th>
							<th>Вознагр 2</th>
						</tr>
					</thead>
					<tbody>
						<?php
						while ($r = $db->fetch_assoc($res)) {
							$promocode = htmlspecialchars($r['promocode']);
							$dt2 = date("d.m.Y H:i", $r['tm2']);
							$descr = htmlspecialchars($base_prices[$r['product_id']]['descr']);
							$price = htmlspecialchars($r['price']);
							$discount = htmlspecialchars($r['discount']);
							$price = !$r['discount'] ? $price : "-";
							$discount .= $r['discount']<=100 ? "%" : "р.";
							$discount = !$r['price'] ? $discount : "-";
							$fee_1 = htmlspecialchars($r['fee_1']);
							$fee_1 .= $r['fee_1'] > 100 ? "р." : "%";
							$fee_2 = htmlspecialchars($r['fee_2']);
							$fee_2 .= $r['fee_2'] > 100 ? "р." : "%";
						?>
							<tr>
								<td><?php echo "<b>$promocode</b>"; ?></td>
								<td><?php echo $dt2; ?></td>
								<td><?php echo $descr; ?></td>
								<td><?php echo $price;?>р.</td>
								<td><?php echo $discount;?></td>
								<td><?php echo $fee_1; ?></td>
								<td><?php echo $fee_2; ?></td>
							</tr>
						<?php
						}
						?>
					</tbody>
				</table>
			</div>
			</div>
			<?}?>

			<?if(!empty($partnerka_adlink)) {?>
			<div class='mb-5' id='partner'></div>
			<div>
				<h3 class='text-center possibilities__suptitle title ' >Материалы для рекомендаций</h3>
				<p><span class='font-weight-bold  bg-info_ p-2' ><a href='<?=$partnerka_adlink?>' class='text-white_' target='_blank'>По ссылке</a></span> находится подборка материалов о нашей компании.
				Выберите понравившийся и дополните вашей партнерской ссылкой или промокодом.
				</p>
				<p>Публикуйте в соцсетях и чатах, таким образом вы сможете рассказать о нас.
				Благодаря партнерской ссылке или промокоду рефералы закрепятся за вами и, после оплаты от клиента, вам начислится вознаграждение.
				</p>
			</div>
			<?}?>

			<div class='mb-5' id='questions'></div>
			<div class=''   style="">
				<h3 class='text-center possibilities__suptitle title ' >Сводка</h3>
				
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

				$tm1=0; $tm2=time();
				$sum_fee_all=$db->sum_fee($klid,$tm1,$tm2);
				$sum_pay_all=$db->sum_pay($klid,$tm1,$tm2,0);
				$rest_all=$sum_fee_all-$sum_pay_all;
				?>
				<div class='table-responsive' >
				<table class='table'  style="margin: 0 auto; display: inline-table;">
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
				<p class='font-weight-bold text-height-2' >Итого: начислено <span class='border border-secondary  p-1 rounded' ><?=$sum_fee_all?></span> -  выплачено <span class='border border-secondary  p-1 rounded' ><?=$sum_pay_all?></span> - остаток к выплате <span class='border border-secondary p-1 rounded' ><?=$rest_all?></span></p>
			</div>
			<br>

			<?if($insales_id && $ctrl_id!=170) {
				$res=$db->query("SELECT order_number,avangard.tm,SUM(amount) AS s,SUM(res) AS res,MAX(c_name) AS name
					FROM avangard
					JOIN cards ON cards.uid=vk_uid
					WHERE amount>0 AND cards.utm_affiliate=$klid
					GROUP BY order_number,avangard.tm,order_descr
					ORDER BY avangard.tm
					DESC LIMIT 20;");
				$show=$db->num_rows($res)<=5 ? "show" : ""; 
			?>
			<div class='mb-4' id='orders'></div>
			<div class='mb-1 text-center' id=''>
				<h3 class='text-center possibilities__suptitle title' style='text-align:center;'>Заказы от рефералов
						<a href='#a_orders' data-toggle='collapse' class='' title='развернуть'>
							<i class='fa fa-folder-open'></i>
						</a>
				</h3>
				<div style="margin: 0 auto; display: table;">
				<div class='collapse <?=$show?>' id='a_orders'>
				<table class='table table-responsive table-condensed' >
					<thead>
						<tr>
							<th>Дата</th>
							<th>Имя</th>
							<th>Сумма заказа</th>
							<th>Оплачен</th>
						</tr>
					</thead>
					<tbody>
						<?
						while($r=$db->fetch_assoc($res)) {
							$payed=$r['res'] ? "<i class='fa fa-check-circle'></i>" : "";
							?>
							<tr>
								<td><?=date("d.m.Y",$r['tm'])?></td>
								<td><?=$r['name']?></td>
								<td><?=formatNumber($r['s'])?></td>
								<td class='text-info' ><?=$payed?></td>
							</tr>
							<?
						}
						?>
					</tbody>
				</table>
				</div>
				</div>
			</div>
			<?}?>

			<div class='mb-4' id='pay'></div>
			<div class=''   style="margin: 0 auto; display: table;">
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

				print "<h3 class='text-center possibilities__suptitle title' style='text-align:center;'>Начисления
						<a href='#a1' data-toggle='collapse' class='' title='развернуть'>
							<i class='fa fa-folder-open'></i>
						</a>
					</h3>\n";
				print "<div  style='margin: 0 auto; display: table;'>
				<div  id='a1' class='collapse' >\n";
				//~ if($levels==2)
					//~ print "<div class='alert alert-info' >Установлен учет только второго уровня оплат ($fee2%)</div>";
				print "<table class='table table-striped table-responsive'>\n
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
					if($r['avangard_id']>0)
						$product=$db->dlookup("order_descr","avangard","id='{$r['avangard_id']}'");
					if($r['product_id']==1001)
						$product="ПРИВЕТСТВЕННЫЕ БАЛЛЫ";
					if($r['product_id']==-1)
						$product="НАЧИСЛЕНО";
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
						<!--<td>".round($sum*$fee/100,0)."</td>-->
						<td>{$r['fee_sum']}</td>
						<td>$product</td>
						</tr>\n";
					$n++;
				}
				print "</tbody></table>\n";
				print "</div>
				</div>\n";

				?>
			</div>

			<div class='mb-4' id=''></div>
			<div>
				<?
				print "<h3 class='text-center possibilities__suptitle title' style='text-align:center;'>Выплаты
						<a href='#a2' data-toggle='collapse' class='' title='развернуть'>
							<i class='fa fa-folder-open'></i>
						</a>
					</h3>\n";
				
				print "<div style='margin: 0 auto; display: table;'>
				<div  id='a2' class='collapse' >";
				
				print "<table class='table table-striped table-responsive'>
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
				print "</div>
				</div>";
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
    <h2 class="footer__title"><?=$company?></h2>
    <div class="footer__company"></div>
    <div class="footer__links">
      Используя функции партнерского кабинета, я соглашаюсь <br> c <a href="<?=$pp?>"
        target="_blank" rel="noopener noreferrer">Политикой конфиденциальности</a> и условиями 
        <br>
        <a href="<?=$oferta_referal?>" target="_blank" rel="noopener noreferrer">ДОГОВОРА ОФЕРТЫ ОБ УЧАСТИИ В ПАРТНЕРСКОЙ ПРОГРАММЕ</a>
    </div>
    <img src="https://winwinland.ru/img/footer-1.svg" alt="img" loading="lazy">
  </footer>

  <div class="scrollUp">
    <a href="#service"><img src="https://winwinland.ru/img/arrow-up.svg" alt="scrollUp"> </a>
  </div>

  <div class="login" id="login">
<!--
    <img class="login__img" src="https://winwinland.ru/img/modal-1.svg" alt="img" loading="lazy">
-->
    <img class="login__img" src="<?=$DB200."/".$destinationImage?>" alt="img" loading="lazy">
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
  <script src="/d/1000/lk/js/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>


<!-- Modal -->
<div class="modal fade" id="cashoutModal" tabindex="-1" role="dialog" aria-labelledby="cashoutModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cashoutModalLabel">Заявка на вывод средств</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="cashoutForm">
                <div class="modal-body">
                    <p>Остаток к выплате: <b><?=$rest_fee?></b></p>
                    <div class="form-group">
                        <label for="amountInput">Укажите сумму для вывода:</label>
                        <input type="number" name='amount' value='' class="form-control" id="amountInput" placeholder="Введите сумму">
                    </div>
                    <div id="responseMessage" class="alert d-none"></div> <!-- Message Placeholder -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
                    <button type="button" id="submitCashout" class="btn btn-primary">Отправить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#submitCashout').on('click', function() {
        // Get the amount input value
        var amount = $('#amountInput').val();
        
        // Disable the button to prevent multiple submissions
        $(this).prop('disabled', true);

        // Check if the amount is specified
        if (amount) {
            // Make an AJAX POST request using jQuery
            $.ajax({
                url: 'jquery.php', // URL to your PHP file
                type: 'POST',
                data: {
                    cashout: true, // Your flag to check in PHP
                    amount: amount, 
                    uid: '<?= $uid ?>' // Pass the user ID as necessary
                },
                success: function(response) {
                    $('#responseMessage').removeClass('d-none alert-danger').addClass('alert alert-success').text(response); // Update the response message
                    $('#amountInput').val(''); // Clear the input field
                    $('#submitCashout').prop('disabled', false); // Re-enable the button
                },
                error: function() {
                    $('#responseMessage').removeClass('d-none').addClass('alert alert-danger').text('Произошла ошибка.'); // Error message
                    $('#submitCashout').prop('disabled', false); // Re-enable the button
                }
            });
        } else {
            // If no amount is specified, show an error
            $('#responseMessage').removeClass('d-none alert-success').addClass('alert alert-danger').text('Пожалуйста, введите сумму для вывода.'); // Set the error message
            $(this).prop('disabled', false); // Re-enable the button
        }
    });
});
</script>
<?
unset($_SESSION['userid_sess']);
unset($_SESSION['username']);
//print "Ok";
?>
</body>

</html>

