<?
$pwd_id=1003;
include "../top_code.inc.php";
$db=new vkt('vkt');
$res=$db->query("SELECT * FROM 0ctrl WHERE del=0 AND id>1");
$databases=[];
while($r=$db->fetch_assoc($res)) {
	$ctrl_db=$db->get_ctrl_database($r['id']);
	$databases[]=['id'=>$r['id'],'uid'=>$r['uid'],'db'=>$ctrl_db,'company'=>$r['company'],'tm_end'=>$db->dlookup("tm_end","avangard","vk_uid={$r['uid']}")];
	//print "$ctrl_db <br>";
}


?>
<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <title>Случайный лэндинг на WINWINLAND</title>

  <meta property="og:type" content="website" />
  <meta property="og:title" content="Случайный лэндинг на WINWINLAND" />
  <meta property="og:description" content="Случайный лэндинг на WINWINLAND" />
  <meta property="og:url" content="https://winwinland.ru/catalog/cases.php" />
  <meta property="og:image" content="https://winwinland.ru/img/logo/winwin_og-2.jpg" />
  <meta property="vk:image" content="https://winwinland.ru/img/logo/winwin_og-2.jpg" />

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=PT+Serif:ital,wght@0,400;0,700;1,400&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
<!--
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
-->
  <link rel="stylesheet" href="../fonts/fonts.css">
  <link rel="stylesheet" href="../css/styles.css">

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

<body class="body">
  <header class="header">
    <div class="header__container">
      <a class="header__logo" href="/index.php"><img src="/img/logo.svg" alt="logo">
      </a>
      <nav class="header__nav">
        <ul class="header__ul">
          <li class="header__li">
            <a href="../#service" class="header__a one active">О сервисе</a>
          </li>
          <li class="header__li">
            <a href="https://winwinland.ru/#rates" class="header__a two">Тарифы</a>
          </li>
          <li class="header__li">
            <a href="https://winwinland.ru/#partner" class="header__a three">Партнерская программа</a>
          </li>
          <li class="header__li">
            <a href="https://winwinland.ru/consult" class="header__a four">Вопросы</a>
          </li>
        </ul>
      </nav>
      &nbsp;
    </div>
  </header>

  <main>
    <section class="service" id="service" style=''>
      <div class="service__top">
        <div class="service__top-wrapper_1">
          <h1 class="service__h1_1">
            <span>Winwinland —</span> <br />
            случайный лэндинг
          </h1>
        </div>
      </div>
    </section>
	<div class="container">
	<?
	$arr=[];
	foreach($databases AS $d) {
		if(empty($d['company']))
			continue;
		$company=!empty($d['company'])?$d['company']:"???";
		$dt_end=($d['tm_end']>time())?"<span class='bg-success p-2 text-white rounded' >".date("d.m.Y",$d['tm_end'])."</span>":"<span class='bg-danger p-2 text-white rounded' >".date("d.m.Y",$d['tm_end'])."</span>";
		$db->connect($d['db']);
		$res=$db->query("SELECT * FROM lands WHERE del=0 AND fl_not_disp_in_cab=0");
		while($r=$db->fetch_assoc($res)) {
			if(empty($r['land_txt']))
				continue;
			if($r['fl_partner_land']==1)
				$vid="🙋‍♀️";
			elseif($r['product_id']>0)
				$vid="📦";
			else
				$vid="⭐";
			$arr[]=['company'=>$company,'land'=>"$vid <a href='{$r['land_url']}' class='text-white' target='_blank'>{$r['land_name']} &gt;&gt;&gt;</a>"];
		}
	}
	
	$n=rand(0,sizeof($arr)-1);
	?>
		<div class='card p-3 py-5 m-5 text-white font-weight-bold'  style='background-color:#0094ff; border-radius: 15px; border-color:#a3d8ff; border-width:3px;'>
			<p class='text-center' style='color:#FFFF00;'><?=$arr[$n]['company']?></p>
			<h3 class='text-center my-5 possibilities__suptitle title'><?=$arr[$n]['land']?></h3>
			<div class='text-center' >
				<a href='javascript:location.reload()' class='btn btn-warning' target=''>Следующий</a>
			</div>
		</div>
		
		<p class='px-5' ><a href='/consult/' class='' target=''><button id='__consult' class="pay__button p-2" type="button">Записаться на консультацию</button></a></p>
	</div>
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
    <img src="../img/footer-1.svg" alt="img" loading="lazy">
  </footer>

<script type="text/javascript">
	$("#login_form_submit").click(function() {
		//console.log("HERE_");
		$('#login_form').attr('action', 'goto_crm.php').submit();
	});
</script>

<script type="text/javascript">
	console.log('test');
	$("#go_submit").click(function() {
		console.log("HERE_");
		//alert($("#c_name").val());
		if($("#client_name").val().trim()=="") {
			alert("Необходимо указать ваше имя!");
		} else if($("#client_phone").val().trim()=="") {
			alert("Укажите, пожалуйста, телефон для связи!");
		} else if(!$("#chk1").is(":checked")) {
			alert("Необходимо согласиться с обработкой персональных данных !");
		} else {
			$('#f1').attr('action', '?').submit();
		}
	});
</script>

	
  
</body>

</html>
