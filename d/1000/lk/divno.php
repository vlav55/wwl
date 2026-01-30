<?
//вызывается партнерский кабинет по ссылке вида https://your_site.ru/lk/?u=XXXXXXXXXXXXX
//где u=direct_code, выдаваемый системой при регистрации партнера (изменяется при изменении пароля)
//это постоянное значение, которое заменяет логин-пароль и позволяет партнеру входить в ЛК по прямому доступу

//вызывается партнерский кабинет по ссылке вида https://your_site.ru/lk/?u=XXXXXXXXXXXXX
//где u=direct_code, выдаваемый системой при регистрации партнера (изменяется при изменении пароля)
//это постоянное значение, которое заменяет логин-пароль и позволяет партнеру входить в ЛК по прямому доступу
function api_call($endpoint,$data,$method) {
	$client_secret="c4ca4238a0b923820dcc509a6f75849b"; //"ec8ce6abb3e952a85b8551ba726a1227";
	$client_id=19802; //"4356436";
	$url="https://api.winwinland.ru".$endpoint;
    $ch = curl_init();
    $data = http_build_query($data);
    if($method=='GET') {
		curl_setopt($ch, CURLOPT_URL, $url.'?'.$data);
	} elseif($method=='POST') {
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	} else
        return ['error'=>'method undefined'];
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode("$client_id:$client_secret") // Кодируем учетные данные в формате Base64
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    if ($response === false) {
        return ['error'=>'cURL Error: ' . curl_error($ch)];
    } else {
        return json_decode($response, true);
    }
}

$direct_code=$_GET['u']; //получаем direct_code
$data=[
	'direct_code' => $direct_code,
	];
$res=api_call('/partner/',$data,'GET');
if(!isset($res['uid'])) {
	die("Error login");
	exit;
}

$client_uid=$res['uid'];

if(isset($_POST['bank_details'])) { //обработка кнопки сохранение реквизитов
	$data=[
		'uid' => $client_uid,
		'bank_details'=>$_POST['bank_details'],
		];
	$res=api_call('/partner/details/',$data,'POST');
	if(isset($res['error'])) {
		die("Error receiving client info ".print_r($res,true)); 
	}
}
if(isset($_POST['cashout_sum'])) {
	$data=[
		'uid' => $client_uid,
		'msg'=>"Запрос на вывод средств: ".$_POST['cashout_sum'],
		];
	$res=api_call('/lead/notify/',$data,'POST');
	if(isset($res['error'])) {
		echo ("Error ".print_r($res,true)); 
	} else
		echo "<script>alert('Запрос отправлен успешно');</script>";
}

//получаем информацию о партнере
$data=[
	'client_uid' => $client_uid,
	];
$res=api_call('/lead/',$data,'GET');
if(isset($res['error'])) {
	die("Error receiving client info");
}
?>



<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Партнерский кабинет</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      background: #EAEAEA;
      color: #191c1d;
      font-family: Open Sans, Arial, sans-serif;
      margin: 0;
      font-size: 18px;
    }
    input {
		border: 1px solid #ccc; /* Sets a 1px solid border with a light grey color */
		padding:7px;
		font-size: 18px;
	}
    textarea {
		border: 1px solid #ccc; /* Sets a 1px solid border with a light grey color */
		padding:7px;
		font-size: 18px;
		width:100%;
	}
    .cabinet-container {
      max-width: 900px;
      margin: 42px auto 0 auto;
      padding: 0 28px;
      display: flex;
      flex-direction: column;
      gap: 35px;
    }
    .cabinet-block, .cabinet-table-block, .cabinet-summary {
      background: #fff;
      border-radius: 0px;
	  font-family: Open Sans;
      box-shadow: 0 4px 24px rgba(220,208,195,0.11);
      padding: 32px 32px 28px 32px;
    }
    .cabinet-title {
	  font-family: Cormorant;
      font-size: 1.50em;
      font-weight: bold;
      margin-bottom: 18px;
      color: #78170D;
    }
	.cabinet-credentials {
	font-family: Open Sans;
	font-style: thin;
	font-size: 14;
	color: #202020;
	}
    .cabinet-table-title {
      font-size: 1.50em;
      font-weight: bold;
	  font-family: Cormorant;
      margin-bottom: 18px;
      color: #78170D;
    }
    .cabinet-btn-group {
      display: flex;
      gap: 16px;
      margin-top: 18px;
	  padding-left: 4px;
	  padding-right: 4px;
    }
    .cabinet-btn {
      background: #78170D;
      color: #fff;
      min-width: 190px;
	  padding: 18px 0;
      font-family: Open Sans;
	  font-size: 0.80em;
      font-weight: 400;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background .18s;
	  padding-right: 4px;
	  padding-left: 4px;
    }
    .btn-small {
      background: #0000FF;
      color: #fff;
      #min-width: 190px;
	  padding: 5px 0;
      font-family: Open Sans;
	  font-size: 0.80em;
      font-weight: 400;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background .18s;
	  padding-right: 4px;
	  padding-left: 4px;
    }
    .cabinet-btn:hover { background: #B63735;}
    .cabinet-table-responsive { width: 100%; overflow-x: auto;}
    .cabinet-table {
      width:100%;
	  font-family: Open Sans;
      border-collapse:collapse;
      background:#fff;
      border-radius:11px;
      font-size:1em;
      min-width:340px;
    }
 	
    .cabinet-table th, .cabinet-table td {
      padding:16px 12px;
      border-bottom:1px solid #C89EA1;
      text-align:left;
      font-weight: 300;
      font-size: 0.8em;
    }
    .cabinet-table th {
      background:#C89EA1;
	  font-family: Open Sans;
      font-weight:500;
      font-size:0.80em;
    }
    .cabinet-summary {
	  font-family: Open Sans;
      font-size: 1.0em;
      padding-top: 16px;
      text-align: left;
    }
    .cabinet-summary strong { font-weight: bold;}
	font-family: Open Sans;
    @media(max-width:990px){
      .cabinet-container{max-width: 99vw; padding:0 8px;}
      .cabinet-block, .cabinet-table-block, .cabinet-summary{padding:16px 8px;}
      .cabinet-btn{font-size:0.98em; min-width: 110px; padding: 12px 0;}
      .cabinet-table th,.cabinet-table td{padding:9px 6px;}
    }
    @media(max-width:600px){
      body { font-size:16px;}
      .cabinet-table th, .cabinet-table td { font-size: 0.97em;}
      .cabinet-title, .cabinet-table-title { font-size:1em;}
      .cabinet-btn-group{flex-direction:column;gap:8px;}
      .cabinet-btn{width:100%;}
    }
	

	
  </style>
</head>
<body>
  <div class="cabinet-container">
    <div class="cabinet-block">
      <div class="cabinet-title">Партнерский кабинет</div>
	  <div class="cabinet-credentials">
      <span>Имя: <?=$res['first_name']." ".$res['last_name']?></span>
      <div>Телефон: <?=$res['phone']?></div>
      <div>E-mail: <?=$res['email']?></div>
      <div>Партнерский код: <?=$res['partner_code']?></div>
    <form method='POST'>
      <div>Реквизиты для выплаты вознаграждения:</div>
      <textarea name='bank_details'><?=$res['bank_details']?></textarea>
      
	  </div>
	  
      <div class="cabinet-btn-group">
        <button class="cabinet-btn" type='submit' name='save_bank_details' value='yes'>Сохранить</button>
        <button class="cabinet-btn" type='submit' name='cashout_request' value='yes'>Вывести средства</button>
        <button class="cabinet-btn">Вывести бонусами магазина</button>
      </div>
	</form>
	<?
	if(isset($_POST['cashout_request'])) { //обработка кнопки вывода средств
		?>
		<div class="cabinet-btn-group">
			<form method='POST'>
				<p>Всего доступно: <?=$res['partner_data']['rest_all']?>р. <br>
				Укажите сумму для вывода</p>
				<input type='number' name='cashout_sum' value=''>
				<button type='submit' class="btn-small">Отправить запрос</button>
			</form>
		</div>
		<?
	}
	?>
    </div>
    <div class="cabinet-block">
      <div class="cabinet-title">Партнерские ссылки</div>
      <div>ТГ:</div>
        <a href="https://divno.myinsales.ru/?bc=<?=$res['partner_code']?>" target="_blank">перейти на лэндинг</a>
        <span style="word-break:break-all; margin-left:8px;">https://divno.myinsales.ru/?u=<?=$res['partner_code']?></span>
        <button class="cabinet-btn" style="display:inline-block; margin-left:8px;" onclick="navigator.clipboard.writeText('https://divno.myinsales.ru/?bc=<?=$res['partner_code']?>')">скопировать ссылку</button>
      
      <small style="display:block; margin-top:8px;">
        По этим ссылкам ваши знакомые могут зарегистрироваться и будут закреплены за вами. Просто разместите эту ссылку на своих страницах в соцсетях, друзьям и знакомым и расскажите им о нас.
      </small>
    </div>
    <div class="cabinet-table-block">
      <div class="cabinet-table-title">Проценты вознаграждений</div>
      <div class="cabinet-table-responsive">
        <table class="cabinet-table">
          <tr>
            <th>Наименование</th>
            <th>Цена</th>
            <th>Уровень 1</th>
            <th>Уровень 2</th>
            <th>На сколько продаж начисл вознагр</th>
          </tr>
          <tr>
            <td>Все продукты</td>
            <td>-</td>
            <td>10%</td>
            <td>0%</td>
            <td>без огр</td>
          </tr>
        </table>
      </div>
    </div>
    <div class="cabinet-table-block">
      <div class="cabinet-table-title">Реферальные промокоды</div>
      <div class="cabinet-table-responsive">
        <table class="cabinet-table">
          <tr>
            <th>Промокод</th>
            <th>Действует по</th>
            <th>Осталось активаций</th>
            <th>Для продукта</th>
            <th>На спеццену</th>
            <th>На скидку</th>
            <th>Вознагр 1</th>
            <th>Вознагр 2</th>
          </tr>
		<?foreach($res['promocodes'] AS $r) {
			$d=($r['discount']>0 && $r['discount']<100) ? "%" : "р.";
			$d=$r['discount'] ? $d : false;
			$f1=($r['fee_1']>0 && $r['fee_1']<100) ? "%" : "р.";
			$f1=$r['fee_1'] ? $f1 : false;
			$f2=($r['fee_2']>0 && $r['fee_2']<100) ? "%" : "р.";
			$f2=$r['fee_2'] ? $f2 : false;
			?>
          <tr>
            <td><?=$r['promocode']?></td>
            <td><?=date("d.m.Y H:i",$r['tm2'])?></td>
            <td><?=$r['cnt']==-1?'без огр':$r['cnt']?></td>
            <td><?=$r['product_descr']?></td>
            <td><?=!empty($r['price']) ? $r['price'] : '-'?></td>
            <td><?=!empty($r['discount']) ? $r['discount'] : '-'?></td>
            <td><?=!empty($r['fee_1']) ? $r['fee_1'] : '-'?></td>
            <td><?=!empty($r['fee_2']) ? $r['fee_2'] : '-'?></td>
          </tr>
        <?}?>
        </table>
      </div>
    </div>
    <div class="cabinet-table-block">
      <div class="cabinet-table-title">Сводка</div>
      <div class="cabinet-table-responsive">
		  <?$r=$res['partner_data']?>
        <table class="cabinet-table">
          <tr>
            <th>Сегодня</th>
            <th>Вчера</th>
            <th>Неделя</th>
            <th>Месяц</th>
            <th>Прошлый месяц</th>
            <th>С начала года</th>
          </tr>
          <tr>
            <td>Количество регистраций</td>
            <td><?=$r['cnt_reg_today']?></td>
            <td><?=$r['cnt_reg_yesterday']?></td>
            <td><?=$r['cnt_reg_this_month']?></td>
            <td><?=$r['cnt_reg_last_month']?></td>
            <td><?=$r['cnt_reg_year']?></td>
          </tr>
          <tr>
            <td>Сумма оплат</td>
            <td><?=$r['sum_buy_today']?></td>
            <td><?=$r['sum_buy_yesterday']?></td>
            <td><?=$r['sum_buy_this_month']?></td>
            <td><?=$r['sum_buy_last_month']?></td>
            <td><?=$r['sum_buy_year']?></td>
          </tr>
          <tr>
            <td>Сумма комиссий</td>
            <td><?=$r['sum_fee_today']?></td>
            <td><?=$r['sum_fee_yesterday']?></td>
            <td><?=$r['sum_fee_this_month']?></td>
            <td><?=$r['sum_fee_last_month']?></td>
            <td><?=$r['sum_fee_year']?></td>
          </tr>
          <tr>
            <td>Выплачено</td>
            <td><?=$r['sum_pay_today']?></td>
            <td><?=$r['sum_pay_yesterday']?></td>
            <td><?=$r['sum_pay_this_month']?></td>
            <td><?=$r['sum_pay_last_month']?></td>
            <td><?=$r['sum_pay_year']?></td>
          </tr>
        </table>
      </div>
    </div>
    <div class="cabinet-summary">
      <strong>Итого:</strong> начислено <b><?=$r['sum_fee_all']?></b>, выплачено <b><?=$r['sum_pay_all']?></b>, остаток к выплате <b><?=$r['rest_all']?></b>
    </div>
    <div class="cabinet-table-block">
      <div class="cabinet-table-title">Заказы от рефералов</div>
      <div class="cabinet-table-responsive">
        <table class="cabinet-table">
          <tr>
            <th>Дата</th>
            <th>Имя</th>
            <th>Сумма заказа</th>
            <th>Оплачен</th>
          </tr>
          <?foreach($res['fee_orders'] AS $r) {?>
			  <tr>
				<td><?=date("d.m.Y H:i",$r['tm'])?></td>
				<td><?=$r['referal_name']?></td>
				<td><?=$r['sum']?></td>
				<td><?=$r['paid'] ? 'да' : 'нет'?></td>
			  </tr>
          <?}?>
        </table>
      </div>
    </div>
    <div class="cabinet-table-block">
      <div class="cabinet-table-title">Начисления</div>
      <div class="cabinet-table-responsive">
        <table class="cabinet-table">
		  <tr>
			<th>Дата</th>
			<th>Чья продажа</th>
			<th>Имя</th>
			<th>Сумма</th>
			<th>% вознагр.</th>
			<th>Начислено партнеру</th>
			<th>Продукт</th>
          </tr>
          <?foreach($res['fee_detailed'] AS $r) {?>
			  <tr>
				<td><?=date("d.m.Y H:i",$r['tm'])?></td>
				<td><?=$r['level']==1 ? 'собств' : $r['partner_name']?></td>
				<td><?=$r['first_name']." ".$r['last_name']?></td>
				<td><?=$r['sum']?></td>
				<td><?=$r['fee_percent']?></td>
				<td><?=$r['fee_sum']?></td>
				<td><?=$r['order_descr']?></td>
			  </tr>
          <?}?>
        </table>
      </div>
    </div>
    <div class="cabinet-table-block">
      <div class="cabinet-table-title">Выплаты</div>
      <div class="cabinet-table-responsive">
        <table class="cabinet-table">
		  <tr>
			<th>Дата</th>
			<th>Сумма</th>
			<th>Вид</th>
			<th>Комментарий</th>
          </tr>
          <?foreach($res['fee_paid'] AS $r) {?>
			  <tr>
				<td><?=date("d.m.Y",$r['tm'])?></td>
				<td><?=$r['sum_pay']?></td>
				<td><?=$r['vid']==1 ? 'руб' : 'баллы'?></td>
				<td><?=$r['comm']?></td>
			  </tr>
          <?}?>
        </table>
      </div>
    </div>
    <small style="display:block; margin:36px auto 18px auto;max-width:900px;">
      Используя функции партнерского кабинета, я соглашаюсь с
      <a href="https://divno.me/privacy.pdf" target="_blank">Политикой конфиденциальности</a> и условиями
      <a href="https://for16.ru/d/319261745/lk/cabinet.php?u=9094413e288394ebb48d5217bb4c6029" target="_blank">ДОГОВОРА ОФЕРТЫ ОБ УЧАСТИИ В ПАРТНЕРСКОЙ ПРОГРАММЕ</a>
    </small>
  </div>
</body>
</html>
