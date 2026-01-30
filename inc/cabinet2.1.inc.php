<?
include_once "/var/www/vlav/data/www/wwl/inc/top.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
//chdir("..");
//include_once "init.inc.php";
$t=new top(false);
$t->connect($database);

if(!isset($_SESSION['userid_sess']) || !intval($_SESSION['userid_sess'])) {
	if(!isset($_GET['u']) ) {
		// Configuration
		$botToken = $tg_bot_msg;
		//$db->notify_me("HERE $botToken");

		// Function to validate Telegram Mini App
		function isTelegramMiniApp($initData, $botToken) {
			if (empty($initData)) {
				return false;
			}
			
			parse_str($initData, $data);
			$hash = $data['hash'] ?? '';
			unset($data['hash']);
			
			ksort($data);
			$dataCheckArr = [];
			foreach ($data as $key => $value) {
				$dataCheckArr[] = $key . '=' . $value;
			}
			$dataCheckString = implode("\n", $dataCheckArr);
			
			$secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);
			$calculatedHash = hash_hmac('sha256', $dataCheckString, $secretKey);
			
			return hash_equals($calculatedHash, $hash);
		}

		// Check if data was sent
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$initData = $_POST['initData'] ?? '';
			
			if (isTelegramMiniApp($initData, $botToken)) {
				parse_str($initData, $data);
				$user = json_decode($data['user'] ?? '{}', true);

				if($direct_code=$db->dlookup("direct_code","users","del=0 AND telegram_id='".$user['id']."'")) {
					//$db->notify_me("HERE $direct_code");
					header("Location: ?u=$direct_code", true, 301);
					echo "<h2>Login error. Ask support</h2>";
				} else {
					print "Sorry, you have not private partner cabinet. Ask support pls...";
				}
				exit;
				
				echo "<h2>✅ Running in Telegram Mini App</h2>";
				echo "<p><strong>User ID:</strong> " . ($user['id'] ?? 'N/A') . "</p>";
				echo "<p><strong>Username:</strong> @" . ($user['username'] ?? 'N/A') . "</p>";
				echo "<p><strong>Name:</strong> " . ($user['first_name'] ?? 'N/A') . "</p>";
			} else {
				echo "<h2>❌ not logged</h2>";
			}
			exit;
		}
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<title>Telegram Mini App Detector</title>
			<script src="https://telegram.org/js/telegram-web-app.js"></script>
		</head>
		<body>
			<h1>Загрузка</h1>
			<div id="result">Checking...</div>

			<script>
				// Wait for Telegram WebApp to be ready
				setTimeout(() => {
					const tg = window.Telegram.WebApp;
					
					// Expand the app
					tg.expand();
					
					const initData = tg.initData;
					
					console.log('InitData:', initData); // Debug
					
					if (!initData) {
						document.getElementById('result').innerHTML = "<h2>❌ not logged</h2>"; //'<h2>❌ NOT in Telegram Mini App</h2><p>No initData available</p>';
						return;
					}
					
					fetch('', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/x-www-form-urlencoded',
						},
						body: 'initData=' + encodeURIComponent(initData)
					})
					.then(response => response.text())
					.then(data => {
						document.getElementById('result').innerHTML = data;
					})
					.catch(error => {
						document.getElementById('result').innerHTML = '<h2>❌ Error</h2><p>' + error + '</p>';
					});
				}, 500);
			</script>
		</body>
		</html>
		<?exit;?>

		<?
		//$t->notify_me(print_r($_GET,true));
		//~ if(isset($_GET['tg_id'])) {
			//~ if(!$tg_id=intval($_GET['tg_id'])) {
				//~ print "error 9";
				//~ exit;
			//~ }
			//~ $min_user_id=$ctrl_id==1 ? 0 : 2;
			//~ if(!$_GET['u']=$t->dlookup("direct_code","users","id>$min_user_id AND del=0 AND telegram_id='$tg_id'",0)) {
				//~ print "Sorry, you are not registered as a partner for the company!";
				//~ exit;
			//~ }
		//~

	}
}
//$db->notify_me(print_r($_GET,true));
if(!$t->login()) {
	//$db->get_direct_code($klid);
	session_destroy();
	sleep(3);
	die ("login error. Ask support pls");
}
if(!isset($_SESSION['userid_sess']) || !intval($_SESSION['userid_sess'])) {
	session_destroy();
	die ("login error. Ask support pls");
}

$db=new db("vkt");
$partnerka_adlink=$db->dlookup("partnerka_adlink","0ctrl","id='$ctrl_id'");
$company=$db->dlookup("company","0ctrl","id='$ctrl_id'");
$db->connect($database);

$user_id=$_SESSION['userid_sess'];
if(!$klid=$db->get_klid($user_id)) {
	$db->notify_me("cabinet2.inc.php error: ctrl_id=$ctrl_id get_klid($user_id) returned 0");
}
if( ($user_id<=3 && $database!='vkt') || (!$db->is_partner_db($db->dlookup("uid","cards","id='$klid'")) && $user_id>3) ) {
	session_destroy();
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

$p=new partnerka($klid,$database);
$link=$p->get_partner_link($klid,'senler');
$link1=$p->get_partner_link($klid,'senler',1);


// Count total referal links
$totalLinks = 0;
if($link) $totalLinks++;
if($link1) $totalLinks++;
$res=$db->query("SELECT * FROM lands WHERE del=0 AND fl_not_disp_in_cab=0");
$totalLinks += $db->num_rows($res);

//Count of promocodes
$res_promocodes=$db->query("SELECT promocode, MIN(product_id) AS product_id,COUNT(product_id) AS cnt_pid, cnt,tm2,price,discount,fee_1,fee_2
                FROM promocodes WHERE uid='$uid' AND tm2>'".time()."'
                GROUP BY promocode,cnt,tm2,price,discount,fee_1,fee_2");
$promoCount = $db->num_rows($res_promocodes);


// Check for VIP link
if($ctrl_id==1) {
    $pattern = '/(?:RewriteRule\s\^)(.*?)\/\?\$.*?https:\/\/wwl\.winwinland\.ru\/(.*?)\/?bc=(.*?)(?:\s\[R=301,L])/';
    $arr=file("/var/www/vlav/data/www/wwl/winwinland/.htaccess");
    foreach($arr AS $str) {
        if(preg_match($pattern,trim($str),$m)) {
            if($bc==$m[3]) {
                $suf=$m[1];
                $totalLinks++;
                break;
            }
        }
    }
}




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

$tm1=0; $tm2=time();
$cnt_reg_all=$db->cnt_reg($klid,$tm1,$tm2);
$sum_buy_all=$db->sum_buy($klid,$tm1,$tm2,0);
$sum_fee_all=$db->sum_fee($klid,$tm1,$tm2,0);
$sum_pay_all=$db->sum_pay($klid,$tm1,$tm2,0);

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

$rest_all=$sum_fee_all-$sum_pay_all;
$rest_fee=$db->rest_fee($klid);

$referralCount = $cnt_reg_all;


if(DEMO) {
// Calculations for 224 total registrations with specific recent activity:
// Total registrations: 224
// Conversion rate: 1 sale per 4.21 referrals
// Average sale value: 26,514 ₽

// Example values for a successful partner - Commission is 36.3% of sales
$cnt_reg_all = 224; // Total registrations
$sum_buy_all = 1405242; // ₽ - Total sales (1,405,242 rounded)
$sum_fee_all = 510102; // ₽ - 36.3% commission of sales (1,405,242 * 0.363 = 510,102.85)
$sum_pay_all = 480000; // ₽ - Paid out
$rest_all = $sum_fee_all - $sum_pay_all; // ₽ - Balance due (30,102)

// Year to date (from Jan 1 current year) - assuming 60% of total are from this year
$year_reg = round(224 * 0.6); // 134 registrations
$year_sales = round(134 / 4.21) * 26514; // 32 sales × 26,514 = 848,448
$cnt_reg_year = 134;
$sum_buy_year = 848448; // ₽
$sum_fee_year = 307986; // ₽ - 36.3% commission (848,448 * 0.363 = 307,986.62)
$sum_pay_year = 290000; // ₽

// This month (current month) - 1 registration, 9 payments
$cnt_reg_this_month = 1;
// 9 payments this month from previous referrals
$month_sales_from_payments = 9 * 26514; // 9 sales × 26,514 = 238,626
$sum_buy_this_month = 238626; // ₽
$sum_fee_this_month = 86621; // ₽ - 36.3% commission (238,626 * 0.363 = 86,621.24)
$sum_pay_this_month = 0; // Not paid yet this month

// Last month (previous month) - 37 registrations, 12 payments
$cnt_reg_last_month = 37;
// 12 payments last month
$last_month_sales = 12 * 26514; // 12 sales × 26,514 = 318,168
$sum_buy_last_month = 318168; // ₽
$sum_fee_last_month = 115495; // ₽ - 36.3% commission (318,168 * 0.363 = 115,494.98)
$sum_pay_last_month = 110000; // ₽

// This week (current week, Mon-Sun) - assuming some of this month's 9 payments
$week_payments = 3; // 3 of the 9 payments happened this week
$week_sales = $week_payments * 26514; // 3 sales × 26,514 = 79,542
$cnt_reg_this_week = 0; // No new registrations this week (the 1 registration was today)
$sum_buy_this_week = 79542; // ₽
$sum_fee_this_week = 28874; // ₽ - 36.3% commission (79,542 * 0.363 = 28,873.75)
$sum_pay_this_week = 0;

// Yesterday - no activity
$cnt_reg_yesterday = 0;
$sum_buy_yesterday = 0; // ₽
$sum_fee_yesterday = 0; // ₽
$sum_pay_yesterday = 0;

// Today - 1 registration (but no payments yet)
$cnt_reg_today = 1;
$sum_buy_today = 0; // ₽ - No sales today from this new registration
$sum_fee_today = 0; // ₽
$sum_pay_today = 0;

// Current pending balance (available for withdrawal)
$rest_fee = 30102; // ₽ - Available for withdrawal (matches rest_all)

// Additional metric: conversion rate
$conversion_rate = (1 / 4.21) * 100; // 23.75%
}

if(isset($_POST['tech_support'])) {
	$q=mb_substr($_POST['q'],0,512);
	$db->notify($uid,"Вопрос из ЛК партнера: ".$q);
	header("Location: " . $_SERVER['PHP_SELF'], true, 302);
}

if(isset($_POST['do_details'])) {
	$db->query("UPDATE users SET bank_details='".$db->escape($_POST['msg'])."' WHERE klid='$klid'");
	print "<div class='alert alert-success' >Записано!</div>";
	header("Location: " . $_SERVER['PHP_SELF'], true, 302);
}
$bank_details=$db->dlookup("bank_details","users","id={$_SESSION['userid_sess']}");
 
if(isset($_GET['spec'])) {
	if($typ=intval($_GET['spec'])) {
		$db->query("UPDATE partnerka_users SET typ='$typ' WHERE email='$email'",0);
		//print "<div class='alert alert-success' >Благодарим за уточнения!</div>";
	}
}
$r=$db->fetch_assoc($db->query("SELECT * FROM cards WHERE id=$klid"));

$bc=$db->dlookup("bc","users","klid='$klid'");
if(!$bc) {
	$bc=0;
}

foreach(['jpg','png','PNG','JPG'] AS $ext) {
	$pic='tg_files/logo.'.$ext;
	if(file_exists($pic))
		break;
}

$company_logo=file_exists($pic) ? $pic : "https://for16.ru/images/logo.jpg";

if(!empty($company_logo) ) {

	// Путь к исходному изображению
	$sourceImage = $company_logo;

	// Создание изображения с помощью imagecreatefromjpeg

    if ($image_info=getimagesize($company_logo)) {
        $width = $image_info[0];
        $height = $image_info[1];
        //$db->notify_me("HERE $width $height");
	}
	if($width >300 || $height >100) {
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
	} else
		$destinationImage = $company_logo;
}
if($ctrl_id==1)
	$destinationImage = 'tg_files/logo-200.png';
?>


<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="https://winwinland.ru/img/logo/favicon.png" type="image/x-icon">
  <title><?=isset($title)?$title:"Партнерский кабинет"?></title>

  <meta property="og:type" content="website" />
  <meta property="og:title" content="Партнерский кабинет" />
  <meta property="og:description" content="<?=$company_name?>" />
  <meta property="og:image" content="<?=$DB200."/".$company_logo?>" />
  <meta property="vk:image" content="<?=$DB200."/".$company_logo?>" />

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=PT+Serif:ital,wght@0,400;0,700;1,400&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

<!-- CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- Your custom CSS -->
<link rel="stylesheet" href="https://for16.ru/winwinland/fonts/fonts.css">
<link rel="stylesheet" href="https://for16.ru/winwinland/css/styles.css">
<link rel="stylesheet" href="https://for16.ru/css/cab3.css">

<!-- JavaScript (in HEAD or before closing BODY) -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>

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
      <a class="header__logo" href="#top"><img src="<?=$DB200?>/<?=$destinationImage?>" alt="logo">
      </a>
<!--
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
-->
      <a href='' class="button_wwl_help"  data-toggle="modal" data-target="#techSupportModal">Техподдержка</a>
    </div>
  </header>

  <main>
      <div class="container">
		<div class='service' ></div>

		<div class='row mt-5'>
			<!-- Left Column - Partner Info & Training -->
			<div class='col-md-4 mb-0'>
				<div class='d-flex flex-column h-100 '>
					<!-- Picture and Info Row -->
					<div class='d-flex mb-2 mt-2'>
						<!-- Picture on Left -->
						<div class='flex-shrink-0 mr-3'>
							<img src="https://winwinland.ru/img/partner_pic.svg" alt="" class="img-fluid" style="max-width: 100px; height: auto;">
						</div>
						
						<div class='flex-grow-1 mr-3 mt-0' style='min-width: 0;'> <!-- Crucial for flex children -->
							<p class="text-muted small mb-1">Партнер <?=!DEMO ? $user_id : 47?></p>
							<h5 class='mb-1' style='white-space: nowrap; overflow: hidden; text-overflow: ellipsis;'>
								<b><?=!DEMO ? htmlspecialchars($r['name']).' '.htmlspecialchars($r['surname']) : 'Наталья Семененко'?></b>
							</h5>
							<p class="m-0 text-secondary small text-truncate"><?=!DEMO ? htmlspecialchars($r['mob_search']) : '79059441432'?></p>
							<?if(!empty(trim($r['email']))) { ?>
								<p class="m-0 text-secondary small text-truncate"><?=!DEMO ? htmlspecialchars($r['email']) : 'nata7sem@bk.ru'?></p>
							<?}?>
						</div>

					</div>
					
					<!-- Training Button - Full width under picture and info -->
					<div class='mt-2'>
<a class="button_wwl w-100 text-center d-block" href="javascript:void(0);" data-toggle="modal" data-target="#comingSoonModal">
    <i class="fa fa-graduation-cap mr-2"></i>Обучение
</a>
					</div>
				</div>
			</div>
			<!-- Middle Column - License Balance -->
			<div class='col-md-5'>
				<h4 class='mb-3 d-none d-md-block'>Баланс</h4>
				<div class='card font_rounded p-4 border-1 shadow-sm_ mb-0 mt-md-0 mt-3'>
					<div class='d-flex justify-content-between align-items-center mb-1'>
						<p class='font-weight-bold_ mb-0'>Начислено</p>
						<p class='text-primary mb-0'><?=formatNumber($sum_fee_all)?> ₽
							<a href="#" class="text-info ml-2" data-toggle="modal" data-target="#earningsDetailsModal" title="Детализация начислений" style="text-decoration: none;">
								<i class="fa fa-info-circle"></i>
							</a>
						</p>
					</div>
					<div class='d-flex justify-content-between align-items-center mb-1'>
						<p class='font-weight-bold_ mb-0'>Выплачено</p>
						<p class='text-primary mb-0'><?=formatNumber($sum_pay_all)?> ₽
							<a href="#" class="text-info ml-2" data-toggle="modal" data-target="#paymentsDetailsModal" title="Детализация выплат" style="text-decoration: none;">
								<i class="fa fa-info-circle"></i>
							</a>
						</p>
        			</div>
					<div class='d-flex justify-content-between align-items-center mb-2'>
						<h5 class='font-weight-bold mb-0'>К выплате</h5>
						<h5 class='text-primary mb-0'><?=formatNumber($rest_all)?> ₽</h5>
					</div>
				</div>
			</div>

			<!-- Right Column - Help -->
			<div class='col-md-3 mt-md-4 mt-3'>
				<div class='d-flex flex-column h-100 justify-content-center mt-0 mb-0 pb-0'>
					<a href="#" class="button_wwl w-100 text-center d-block mb-0 mt-md-4 mt-0" data-toggle="modal" data-target="#fee_summary" onclick="return false;">
						<i class="fa fa-bar-chart mr-2"></i>Сводка
					</a>
					<div class='mt-2 pb-0 mb-0 mt-0'>
						<button class='btn btn-primary w-100 mb-3 py-3 mt-3' data-toggle="modal" data-target="#cashoutModal" onclick="event.preventDefault();">
							<i class="fa fa-credit-card mr-2"></i>Вывести средства
						</button>
					</div>
				</div>
			</div>
		</div>

		<?if(!empty($oferta_referal)) {?>
			<p class='small text-muted' >Договор об участии в партнерской программе находится <a href='<?=$oferta_referal?>' class='' target='_blank'>по ссылке</a>. 
			Приняв участие в партнерской программе и пользуясь этим партнерским кабинетом вы подтверждаете согласие с договором.
			</p>
		<? } ?>

		<div class="d-flex flex-column flex-md-row flex-wrap justify-content-center align-items-stretch mb-4">
			<?php if($totalLinks > 0): ?>
				<button type="button" class="button_wwl mb-4 btn-lg py-3 d-flex align-items-center justify-content-center mb-2 mb-md-0 mr-md-2 w-100" style="flex: 1 0 0;" data-toggle="modal" data-target="#partnerLinksModal">
					<i class="fa fa-link mx-2 flex-shrink-0 text-primary"></i>
					<span class="text-nowrap flex-grow-1 text-center font-weight-normal">Мои ссылки</span>
					<span class="badge badge-pill badge-light mx-2 flex-shrink-0 font-weight-normal"><?=$totalLinks?></span>
				</button>
			<?php endif; ?>
			
			<?php if($promoCount > 0): ?>
				<button type="button" class="button_wwl  btn-lg py-3 d-flex align-items-center justify-content-center w-100" style="flex: 1 0 0;" data-toggle="modal" data-target="#promocodesModal">
					<i class="fa fa-ticket mx-2 flex-shrink-0 text-warning font-weight-normal"></i>
					<span class="text-nowrap flex-grow-1 text-center font-weight-normal">Мои промокоды</span>
					<span class="badge badge-pill badge-light mx-2 flex-shrink-0 font-weight-normal"><?=$promoCount?></span>
				</button>
			<?php endif; ?>
		</div>

		<?php if($referralCount > 0 || 1): ?>
			<div class="d-block d-md-inline-block mb-3 mr-md-2 w-100 w-md-auto">
				<button type="button" class="button_wwl btn-lg py-3 d-flex align-items-center justify-content-center w-100" data-toggle="modal" data-target="#referralsModal">
					<i class="fa fa-users mx-2 flex-shrink-0 font-weight-normal text-primary"></i>
					<span class="text-nowrap flex-grow-1 text-center font-weight-normal">Рефералы 1-го уровня</span>
					<span class="badge badge-pill badge-light ml-2 flex-shrink-0 font-weight-normal"><?= $referralCount ?></span>
				</button>
			</div>
		<?php endif; ?>

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
		
			<?if($insales_id && $ctrl_id!=170 &&1==2) {
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
				<div class='collapse <?=$show?>' id='a_orders'>
				<div class='table-responsive' >
				<table class='table table-condensed'  style="margin: 0 auto; display: inline-table;">
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
	  </div>
    <?
	function formatNumber($number) {
	  $number = strrev($number); // Reverse the number
	  $number = str_split($number, 3); // Split into groups of three digits
	  $number = implode(";psbn&", $number); // Join the groups with dots
	  $number = strrev($number); // Reverse the number back to its original order
	  return $number;
	}
	?>

  </main>
  
<br><br><br><br><br>
  <footer class="footer">
<!--
    <h2 class="footer__title"><?=$company?></h2>
-->
    <div class="footer__company"><?=$company?></div>
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

<!--
  <div class="login" id="login">
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
-->

  <a class="burger" onclick="event.stopPropagation()">
    <span class="burger__line burger__line-first"></span>
    <span class="burger__line burger__line-second"></span>
    <span class="burger__line burger__line-third"></span>
  </a>


<!-- Withdraw Modal -->
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
                    <p>Остаток к выплате: <b><?=$rest_fee?> ₽</b>
                    <a href="#" class="ml-2 btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#paymentDetailsModal" onclick="return false;">реквизиты для вывода</a></p>
                    <div class="form-group">
                        <label for="amountInput">Укажите сумму для вывода:</label>
                        <input type="number" name='amount' value='' class="form-control" id="amountInput" placeholder="Введите сумму">
                    </div>
					<?if($insales_id && (isset($insales_lk_cashout_button) ? $insales_lk_cashout_button : true) ) { ?>
						<div class="form-group">
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="insales_checkbox" name="use_insales_bonuses" checked>
								<label class="custom-control-label" for="insales_checkbox">
									<i class="fa fa-gift mr-1 text-success"></i>Вывести бонусами магазина
								</label>
								<small class="form-text text-muted d-block">
									Средства будут зачислены на ваш счет в магазине в виде бонусов
								</small>
							</div>
						</div>
					<?} ?>
                    <div id="responseMessage" class="alert d-none"></div> <!-- Message Placeholder -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Отменить</button>
                    <button type="button" id="submitCashout" class="btn btn-primary">Отправить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="paymentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentDetailsModalLabel">
                    <i class="fa fa-bank mr-2"></i>Реквизиты для выплаты вознаграждения
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class='' id='details'>  
                    <form method='POST' action='?uid=<?=$uid_md5?>&u=<?=$_GET['u']?>'>
                        <div class="form-group">
                            <label for="bankDetailsTextarea"><i class="fa fa-credit-card mr-1"></i>Укажите ваши реквизиты:</label>
                            <textarea class="form-control" style='margin-bottom:5px;' rows="3" id="bankDetailsTextarea" name='msg'><?=$bank_details?></textarea>
                            <small class="form-text text-muted">
                                <i class="fa fa-info-circle mr-1"></i>Введите банковские реквизиты для получения выплат
                            </small>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button class='btn btn-primary mr-1 mt-1' name='do_details' value='yes'>
                                <i class="fa fa-save mr-1"></i>Сохранить реквизиты
                            </button>
                            <button type="button" class='btn btn-outline-secondary mt-1' data-dismiss="modal">
                                <i class="fa fa-times mr-1"></i>Закрыть
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Simple Tech Support Modal -->
<div class="modal fade" id="techSupportModal" tabindex="-1" role="dialog" aria-labelledby="techSupportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="techSupportModalLabel">
                    <i class="fa fa-headphones mr-2"></i>Техподдержка
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
<!--
                <div class="text-center mb-3">
                    <img src="<?=$DB200."/".$destinationImage?>" alt="logo" class="img-fluid mb-3" style="max-height: 60px;">
                    <h5>Ваш вопрос</h5>
                </div>
-->
                
                <form action="" enctype="multipart/form-data" method="POST">
                    <div class="form-group">
                        <input class="form-control" name="q" type="text" placeholder="Введите ваш вопрос" required>
                        <input type='hidden' name='uid' value='<?=$uid?>'>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-secondary m-1 btn-sm" data-dismiss="modal">
                            <i class="fa fa-times mr-1"></i>Отмена
                        </button>
                        <button class="btn btn-primary m-1 btn-sm" type="submit" name='tech_support' value='yes'>
                            <i class="fa fa-paper-plane ml-1"></i>Отправить
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Simple Training Modal -->
<div class="modal fade" id="comingSoonModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-body py-5">
                <div class="mb-4">
                    <i class="fa fa-clock text-warning fa-4x"></i>
                </div>
                <h4 class="text-dark mb-3">Скоро будет доступно</h4>
                <p class="text-muted mb-4">
                    Раздел находится в разработке
                </p>
                <button type="button" class="btn btn-primary rounded-pill px-4" data-dismiss="modal">
                    Хорошо
                </button>
            </div>
        </div>
    </div>
</div>

<?include "/var/www/vlav/data/www/wwl/inc/cabinet2_summary.inc.php";?>
<?include "/var/www/vlav/data/www/wwl/inc/cabinet2_earning.inc.php";?>
<?include "/var/www/vlav/data/www/wwl/inc/cabinet2_paid.inc.php";?>
<?include "/var/www/vlav/data/www/wwl/inc/cabinet2_links.inc.php";?>
<?include "/var/www/vlav/data/www/wwl/inc/cabinet2_promocodes.inc.php";?>
<?include "/var/www/vlav/data/www/wwl/inc/cabinet2_referals.inc.php";?>




<script>
$(document).ready(function() {
	$('#submitCashout').on('click', function() {
		// Get the amount input value
		var amount = $('#amountInput').val();
		
		// CHANGE: Check if "Вывести бонусами магазина" checkbox is checked using new id
		var useInsalesBonuses = $('#insales_checkbox').is(':checked');
		
		// Disable the button to prevent multiple submissions
		$(this).prop('disabled', true);

		// Check if the amount is specified
		if (amount && amount <= <?=$rest_fee?>) {
			// Prepare data object
			var ajaxData = {
				amount: amount, 
				uid: '<?= $uid ?>' // Pass the user ID as necessary
			};
			
			// CHANGE: Add the appropriate flag based on checkbox state
			if (useInsalesBonuses) {
				ajaxData.cashout_insales = true; // For store bonuses
				// CHANGE: Also send the checkbox value
				ajaxData.use_insales_bonuses = 1;
			} else {
				ajaxData.cashout = true; // For regular bank withdrawal
				// CHANGE: Also send the checkbox value
				ajaxData.use_insales_bonuses = 0;
			}
			
			// Make an AJAX POST request using jQuery
			$.ajax({
				url: 'jquery.php', // URL to your PHP file
				type: 'POST',
				data: ajaxData,
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
			$('#responseMessage').removeClass('d-none alert-success').addClass('alert alert-danger').text('Пожалуйста, введите допустимую сумму для вывода.'); // Set the error message
			$(this).prop('disabled', false); // Re-enable the button
		}
	});
});


</script>
<script>
function showContacts(phone, email, telegram, vk) {
    let message = '';
    
    if (phone) message += `Телефон: ${phone}\n`;
    if (email) message += `Email: ${email}\n`;
    if (telegram) message += `Telegram: @${telegram}\n`;
    if (vk) message += `ВК: vk.com/${vk}\n`;
    
    if (!message) {
        message = 'Контактные данные отсутствуют';
    }
    
    alert(message);
}
</script>
<?
//~ unset($_SESSION['userid_sess']);
//~ unset($_SESSION['username']);
//print "Ok";
?>
</body>

</html>

