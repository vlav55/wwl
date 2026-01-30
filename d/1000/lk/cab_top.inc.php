<?
include_once "/var/www/vlav/data/www/wwl/inc/top.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/cashier.class.php";
chdir("..");
if(isset($_GET['not_logged'])) {
	include "land_top.inc.php";
	?>
		<div class="modal fade" id="warningModal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content border-warning">
					<div class="modal-header bg-warning text-dark">
						<h5 class="modal-title">
							<i class="fa fa-exclamation-triangle mr-2"></i>Ошибка авторизации
						</h5>
						<button type="button" class="close" data-dismiss="modal">
							<span>&times;</span>
						</button>
					</div>
					<div class="modal-body text-center py-4">
						<p class="text-muted mt-3">Зайдите в кабинет партнера по вашей персональной ссылке</p>
					</div>
					<div class="modal-footer justify-content-center">
						<button type="button" class="btn btn-warning" data-dismiss="modal">Ок</button>
					</div>
				</div>
			</div>
		</div>
		<script>
		$(document).ready(function() {
			$('#warningModal').modal('show');
		});
		</script>
	<?
	include "land_bottom.inc.php";
	exit;
}
function show_modal($msg, $title = "Information", $type = "info",$redirect_url=null) {
    $icons = [
        'info' => 'fa-info-circle',
        'success' => 'fa-check-circle', 
        'warning' => 'fa-exclamation-triangle',
        'error' => 'fa-times-circle'
    ];
    
    $colors = [
        'info' => 'bg-primary',
        'success' => 'bg-success',
        'warning' => 'bg-warning', 
        'error' => 'bg-danger'
    ];
    
    $icon = $icons[$type] ?? 'fa-info-circle';
    $color = $colors[$type] ?? 'bg-primary';
    ?>
    <div class="modal fade" id="messageModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header <?= $color ?> text-white border-0">
                    <div class="modal-title w-100 text-center">
                        <i class="fa <?= $icon ?> fa-3x mb-3"></i>
                        <h4 class="mb-0"><?= htmlspecialchars($title) ?></h4>
                    </div>
                </div>
                <div class="modal-body text-center py-4">
                    <p class=" text-dark mb-3"><?= htmlspecialchars($msg) ?></p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn <?= $color ?> text-white px-4 py-2" data-dismiss="modal" onclick="window.location.href = '<?=$redirect_url?>'">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var modal = new bootstrap.Modal(document.getElementById('messageModal'));
        modal.show();
    });
    </script>
    <?php
}
include_once "init.inc.php";
$t=new top(false);
?>
<?
//$t->notify_me(print_r($_GET,true));

if(isset($_GET['u']))
	$_SESSION['u']=$_GET['u'];
if(isset($_SESSION['u']))
	$_GET['u']=$_SESSION['u'];
if(!$t->login()) {
	header("Location: ?not_logged=yes");
}

$db=new db("vkt");
$partnerka_adlink=$db->dlookup("partnerka_adlink","0ctrl","id='$ctrl_id'");
$company=$db->dlookup("company","0ctrl","id='$ctrl_id'");
//print "HERE_$partnerka_adlink"; exit;
//$db->telegram_bot="vkt";
//$db->db200="https://for16.ru/d/1000";
//include_once "../prices.inc.php";
//chdir("/var/www/vlav/data/www/wwl/d/1000/");

//chdir("..");

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

$partner_uid=$db->dlookup("uid","cards","id='$klid'");
$partner_uid_md5=$db->uid_md5($partner_uid);

$r=$db->fetch_assoc($db->query("SELECT * FROM cards WHERE id=$klid",0));

$p=new partnerka($klid,$database);
$rest_fee=$p->rest_fee($klid);
$billing_rest=$p->users_billing_rest($user_id,1);

if(isset($_GET['client_uid']) && intval($_GET['client_uid'])) {
	$client_uid=intval($_GET['client_uid']);
}
$client_ctrl_id=false; $client_tm_end=0;
if($client_uid) {
	if($client_ctrl_id=$db->dlookup("id","0ctrl","uid='$client_uid'")) {
		//~ $tm1=$db->avangard_tm_end($client_uid,$products_tm_pay_end);
		//~ $tm2=$db->dlookup("tm_end","0ctrl","uid='$client_uid'");
		//~ $client_tm_end=$tm1>$tm2 ? $tm1 : $tm2;
		//~ //print "$tm1 $tm2 $client_tm_end"; exit;
		//~ $vkt=new vkt('vkt');
		//~ $client_ctrl_dir=$db->dlookup("ctrl_dir","0ctrl","id='$client_ctrl_id'");
		//~ $client_database=$vkt->get_ctrl_database($client_ctrl_id);
		
		//~ $db->connect($client_database);
		//~ if(!$cashier_direct_code=$db->dlookup("direct_code","users","del=0 AND klid='100'")) {
			//~ if(!$db->dlookup("id","cards","del=0 AND id=100")) {
				//~ $cashier_uid=$db->cards_add(['first_name'=>'Кассир_1']);
				//~ $db->query("UPDATE cards SET id='100' WHERE uid='$cashier_uid'");
			//~ }
			//~ $p=new partnerka(false,$client_database);
			//~ $p->ctrl_id=$client_ctrl_id; //need to avoid error in partner_add
			//~ $p->partner_add(100,"","Кассир",$username_pref='cashier_');
			//~ $p->set_access_level(100,7);
			//~ $cashier_direct_code=$db->dlookup("direct_code","users","del=0 AND klid='100'");
		//~ }
		//~ $cashier_link="https://for16.ru/d/$client_ctrl_dir/cashier.php?u=$cashier_direct_code";

		//~ if(!$cashier_setup_direct_code=$db->dlookup("direct_code","users","del=0 AND klid='101'")) {
			//~ if(!$db->dlookup("id","cards","del=0 AND id=101")) {
				//~ $cashier_setup_uid=$db->cards_add(['first_name'=>'Настройка кассира']);
				//~ $db->query("UPDATE cards SET id='101' WHERE uid='$cashier_setup_uid'");
			//~ }
			//~ $p=new partnerka(false,$client_database);
			//~ $p->ctrl_id=$client_ctrl_id; //need to avoid error in partner_add
			//~ $p->partner_add(101,"","Настройка кассира",$username_pref='setup_');
			//~ $p->set_access_level(101,6);
			//~ $cashier_setup_direct_code=$db->dlookup("direct_code","users","del=0 AND klid='101'");
		//~ }
		//~ $cashier_setup_link="https://for16.ru/d/$client_ctrl_dir/cashier_setup.php?u=$cashier_setup_direct_code";
		//~ $path="/var/www/vlav/data/www/wwl/d/$client_ctrl_dir";
		//~ $path_from="/var/www/vlav/data/www/wwl/d/1000";
		//~ if(!file_exists("$path/cashier_setup.php")) {
			//~ copy("$path_from/cashier_setup.php","$path/cashier_setup.php");
			//~ copy("$path_from/cashier.php","$path/cashier.php");
		//~ }
		//~ $db->connect('vkt');
	}
}

//~ $msg1="По вашему промокоду была совершена покупка и Вам начислен  кэшбэк в размере *{{cashback}}*.
//~ Всего баллов на вашем счете: {{cashback_all}}.
//~ Потратить баллы вы можете в нашем салоне по адресу: 
//~ Ждем Вас и спасибо, что делитесь промокодом!
//~ ";

//~ $msg2="Благодарим за посещение салона, вам у нас очень понравилось и мы решили подарить вам промокод *{{promocode}}*.
//~ Он действует для всех и дает скидку 10% вам и каждому, кому вы его передадите. 
//~ Но это не обычный промокод! 
//~ Кроме скидки лично вам начисляется еще и кэшбэк в размере 10%, который будет увеличиваться при каждой оплате по промокоду и вы сможете использовать его при оплате наших услуг. 
//~ Делитесь промокодом с друзьями, дарите им скидку, и получайте кэшбэк со всех их покупок, оплачивайте кэшбэком наши услуги!";

//~ $msg3="{{qrcode}}
//~ Промокод на скидку 10% в салоне - *{{promocode}}*
//~ Ждем Вас по адресу: ";
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

	<link rel="stylesheet" href="cab3.css">

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
      <a class="header__logo" href="#top"><img src="<?=$DB200?>/tg_files/logo200x50.jpg" alt="logo">
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
      <a href='https://t.me/vkt_support_bot?start=<?=$db->uid_md5($partner_uid)?>' class="button_wwl_help"  target='_blank'>Техподдержка</a>
    </div>
  </header>

  <main>
    <section class="service" id='top'>
      <div class="container">
		<div class='row mt-5'>
			<!-- Left Column - Partner Info & Training -->
			<div class='col-md-4 mb-4'>
				<div class='d-flex flex-column h-100'>
					<!-- Picture and Info Row -->
					<div class='d-flex mb-3'>
						<!-- Picture on Left -->
						<div class='flex-shrink-0 mr-3'>
							<img src="https://winwinland.ru/img/partner_pic.svg" alt="" class="img-fluid" style="max-width: 100px; height: auto;">
						</div>
						
						<!-- Partner Info on Right -->
						<div class='flex-grow-1 mr-3 mt-2'>
							<p class="text-muted small mb-1">Партнер <?=$user_id?></p>
							<h5 class='mb-1'><b><?=$r['name']?> <?=$r['surname']?></b></h5>
							<?if($user_id==271) {
								$r['mob_search']="79919998877";
								$r['email']="mike_654@gmail.com";
							}
							?>
							<p class="m-0 text-secondary small"><?=$r['mob_search']?></p>
							<?if(!empty(trim($r['email']))) { ?>
								<p class="m-0 text-secondary small"><?=$r['email']?></p>
							<?}?>
						</div>
					</div>
					
					<!-- Training Button - Full width under picture and info -->
					<div class='mt-3'>
						<a class="button_wwl w-100 text-center d-block" href="https://help.winwinland.ru/docs/obuchayuschiy-kurs-marketolog-partnerskih-programm/" target='_blank'>
							<i class="fa fa-graduation-cap mr-2"></i>Обучение
						</a>
					</div>
				</div>
			</div>
			<!-- Middle Column - License Balance -->
			<div class='col-md-5 mb-4'>
				<h4 class='mb-3'>На балансе лицензий</h4>
				<div class='card font_rounded p-4 border-1 shadow-sm_'>
					<div class='d-flex justify-content-between align-items-center mb-3 mt-2'>
						<div class='font-weight-bold text-primary'>WinWinLand</div>
						<img src='https://winwinland.ru/img/switcher_on.svg' alt='' class='img-fluid' style="width: 40px;">
					</div>
					<div class='d-flex justify-content-between align-items-center mb-3'>
						<h5 class='font-weight-bold mb-0'>Лояльность 2.0</h5>
						<h5 class='text-primary mb-0'><?=$billing_rest?> мес</h5>
					</div>
				</div>
			</div>

			<!-- Right Column - Help -->
			<div class='col-md-3 mt-4'>
				<div class='d-flex flex-column h-100 justify-content-center mt-0'>
					<a class="button_wwl w-100 text-center d-block mb-0 mt-4" href="https://help.winwinland.ru/docs/kabinet-marketologa-instruktsiya/" target='_blank'>
						<i class="fa fa-question-circle mr-2"></i>Справка
					</a>
					<!-- Buy License Button -->
					<div class='mt-2 pb-4 mt-2'>
						<button class='btn btn-primary w-100 mb-5 py-3 mt-3' onclick="buy_license()">
							<i class="fa fa-shopping-cart mr-2"></i>Купить лицензии
						</button>
					</div>
				</div>
			</div>
		</div>
