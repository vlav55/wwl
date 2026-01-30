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
      <span>Имя: Дмитрий Ан</span>
      <div>Телефон: 79266020200</div>
      <div>E-mail: dimair24@gmail.com</div>
      <div>Партнерский код: 4194326291</div>
      <div>Реквизиты для выплаты вознаграждения:</div>
	  </div>
      <div class="cabinet-btn-group">
        <button class="cabinet-btn">Сохранить</button>
        <button class="cabinet-btn">Вывести средства</button>
        <button class="cabinet-btn">Вывести бонусами магазина</button>
      </div>
    </div>
    <div class="cabinet-block">
      <div class="cabinet-title">Партнерские ссылки</div>
      <div>ТГ:</div>
        <a href="https://divno.myinsales.ru/?bc=4194326291" target="_blank">перейти на лэндинг</a>
        <span style="word-break:break-all; margin-left:8px;">https://divno.myinsales.ru/?bc=4194326291</span>
        <button class="cabinet-btn" style="display:inline-block; margin-left:8px;" onclick="navigator.clipboard.writeText('https://divno.myinsales.ru/?bc=4194326291')">скопировать ссылку</button>
      
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
          <tr>
            <td>krasno4719</td>
            <td>16.09.2026 23:59</td>
            <td>без огр.</td>
            <td>Все продукты</td>
            <td>-</td>
            <td>5%</td>
            <td>10%</td>
            <td>0%</td>
          </tr>
        </table>
      </div>
    </div>
    <div class="cabinet-table-block">
      <div class="cabinet-table-title">Сводка</div>
      <div class="cabinet-table-responsive">
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
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
          </tr>
          <tr>
            <td>Сумма оплат</td>
            <td>0</td>
            <td>26 590</td>
            <td>26 590</td>
            <td>26 590</td>
            <td>0</td>
          </tr>
          <tr>
            <td>Сумма комиссий</td>
            <td>0</td>
            <td>2 659</td>
            <td>2 659</td>
            <td>2 659</td>
            <td>0</td>
          </tr>
          <tr>
            <td>Выплачено</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
          </tr>
        </table>
      </div>
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
          <tr>
            <td>23.09.2025</td>
            <td>Dmitriy</td>
            <td>26 590</td>
            <td>да</td>
          </tr>
        </table>
      </div>
    </div>
    <div class="cabinet-summary">
      <strong>Итого:</strong> начислено <b>2 659</b>, выплачено <b>0</b>, остаток к выплате <b>2 659</b>
    </div>
    <small style="display:block; margin:36px auto 18px auto;max-width:900px;">
      Используя функции партнерского кабинета, я соглашаюсь с
      <a href="https://divno.me/privacy.pdf" target="_blank">Политикой конфиденциальности</a> и условиями
      <a href="https://for16.ru/d/319261745/lk/cabinet.php?u=9094413e288394ebb48d5217bb4c6029" target="_blank">ДОГОВОРА ОФЕРТЫ ОБ УЧАСТИИ В ПАРТНЕРСКОЙ ПРОГРАММЕ</a>
    </small>
  </div>
</body>
</html>
