<?
//создать на вашу почту пользователя и дать полные Права на раздел Расширения
session_start();
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt('vkt');
$db->telegram_bot=$db->dlookup("tg_bot_notif","0ctrl","id=1");
$db->db200="https://for16.ru/d/1000";

include "insales_app_credentials.inc.php";
include "insales_func.inc.php";
$title="Winwinland for inSales";
include "land_top.inc.php";
$domen="https://app-insales.winwinland.ru";

$_GET['insales_id']=5967624;
$_GET['shop']='myshop-cvx987.myinsales.ru';

if(isset($_GET['shop']))
	$_SESSION['insales_shop']=mb_substr(trim($_GET['shop']),0,128);
if(isset($_GET['insales_id'])) {
	$_SESSION['insales_id']=intval($_GET['insales_id']);
}
$shop=isset($_SESSION['insales_shop']) ? $_SESSION['insales_shop'] : 0;
$insales_id=isset($_SESSION['insales_id']) ? $_SESSION['insales_id'] : 0;
$product_id=21;
$land_num_insales=9;

if(!$insales_id || !$shop) {
	print "Error: no insales_id or no shop found. Run it from inSales application only";
	exit;
}

include_once "/var/www/vlav/data/www/wwl/inc/insales.class.php";
$in=new insales($insales_id,$shop);

//if( $insales_id==5967624 || !$ctrl_id=$db->dlookup("id","0ctrl","del=0 AND insales_shop_id='$insales_id'",0) ) {
if( !$ctrl_id=$db->dlookup("id","0ctrl","del=0 AND insales_shop_id='$insales_id'",0) ) {

	if(isset($_GET['send'])) {
		$error=false;
		$client=mb_substr(trim($_GET['client']),0,32);
		$phone=$db->check_mob($_GET['phone']) ? $db->check_mob($_GET['phone']) : "";
		$email=$db->validate_email($_GET['email']) ? $_GET['email'] : "";

		$r=[
			'tm'=>0, //for new uid - tm=time() if 0
			'first_name'=>$client,
			'last_name'=>'',
			'phone'=>$phone,
			'email'=>$email,
			'city'=>'',
			'razdel'=>'37', //testing
			'comm1'=>$website,
			'test_cyrillic'=>false
		];

		if($uid=$db->cards_add($r,$update_if_exist=false)) {
			$db->tag_add($uid,27);
			print "<p class='alert alert-success' >Регистрация в WinWinLand пройдена успешно ($uid)</p>";
			$notification_title="🔵INSALES лид зарегистрировался\nСайт магазина: https://".$shop."\nID магазина: $insales_id";
			$db->save_comm($uid, 0, $notification_title);
			$db->mark_new($uid,3);
			$db->notify($uid,$notification_title);
		} else {
			$db->notify_me("INSALES ошибка регистрации лида \nСайт магазина: $shop\nID магазина: $insales_id");
		}

		if($ctrl_id=$db->vkt_create_account($uid,$product_id)) {
			$in->ctrl_id=$ctrl_id;
			print "<p class='alert alert-success' >Триал 14 дней аккаунт WinWinLand создан ($uid)</p>";

			$token=trim(file_get_contents("$insales_id.token"));

			$db->query("UPDATE 0ctrl SET
				insales_shop_id='$insales_id',
				insales_shop='".$db->escape($shop)."',
				insales_token='".$db->escape($token)."'
				WHERE id='$ctrl_id'
				",0);
				
			$passw=md5($token.$secret_key);
			$credentials = base64_encode("$id_app:$passw");
			$ctrl_dir=$db->get_ctrl_dir($ctrl_id);
			$url="https://for16.ru/d/$ctrl_dir/insales_webhook.php";
			$res=insales_webhook_create($url, $event='orders/update');
			if($res===true) {
				$res=insales_webhook_create($url, $event='orders/create');
				$notification_title="🔵INSALES приложение установлено\nСайт магазина: $shop\nID магазина: $insales_id";
                $db->save_comm($uid, 0, $notification_title,51,$insales_id,0,true);
                $db->mark_new($uid,3);
                $db->notify($uid,$notification_title);

				include_once "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
				$s=new vkt_send('vkt');
				$res_s=$db->query("SELECT * FROM vkt_send_1 WHERE del=0 AND sid=12 AND land_num='$land_num_insales'",0);
				while($r=$db->fetch_assoc($res_s)) {
					$s->vkt_send_task_add(1, $tm_event=intval(time()+$r['tm_shift']), $vkt_send_id=$r['id'],$vkt_send_type=3,$uid);
				}
				$res_s=$db->query("SELECT * FROM vkt_send_1 WHERE del=0 AND sid=31 AND land_num='$land_num_insales'",0);
				while($r=$db->fetch_assoc($res_s)) {
					$s->vkt_send_task_add(1, $tm_event=intval(time()+(14*24*60*60)+$r['tm_shift']), $vkt_send_id=$r['id'],$vkt_send_type=3,$uid);
				}

				$db->notify_me("INSALES APP webhook created. uid=$uid ctrl_id=$ctrl_id insales_id=$insales_id ");
				print "<p class='alert alert-success' >Вебхук inSales создан</p>";
				print "<p>Установка приложения прошла успешно. <a href='javascript:location.reload()' class='btn btn-primary' target=''>Продолжить</a></p>";
				
			} else {
				$db->notify_me("INSALES ошибка установки приложения: uid=$uid ctrl_id=$ctrl_id insales_id=$insales_id ");
				http_response_code(210);
			}
		} else
			print "<p class='alert alert-danger' >Ошибка: аккаунт WinWinLand создать не удалось ($uid)</p>";
	}

	if(!isset($_GET['send']) || $error) {
	$insales_token=trim(file_get_contents("$insales_id.token"));
	if(!$insales_token) {
		$db->notify_me("INSALES ERROR : insales_token not found. ctrl_id=$ctrl_id");
		print "<p class='alert alert-danger' >Ошибка при установке приложения (токен не восстановлен)</p>";
		exit;
	}
	if(isset($_GET['user_email'])) {
		$user_email=$db->validate_email($_GET['user_email']) ? $_GET['user_email'] : "";
		$db->notify_chat(-4698221513,"INSALES - shop admin email detected - $user_email \n".print_r($_GET,true) );
		file_put_contents("insales_emails.log","$insales_id,$shop,$user_email\n",FILE_APPEND);
	}
	
	//print "token=$insales_token";
	?>
	<div class='' >
		<h2 class='text-center' >WinWinLand. Лояльность 2.0 </h2>

		<h2 class='text-center' >Что это вам даст</h2>
		<p>Расширение WinWinLand приведет покупателей в ваш магазин без предоплат и расходов на рекламу.
		Вы можете задействовать весь самый современный функционал партнерских программ, подключить ресурсы,
		которые у вас уже есть и которые вы не использовали, расширить свои возможности и даже вывести в свой магазин
		покупателей из маркетплэйсов.
		</p>
		<h3>В вашем распоряжении:</h3>
		<ul>
			<li><b>Партнерские ссылки.</b> Регистрация партнеров, выдача им индивидуальных ссылок и партнерских кабинетов.
			Общие или индивидуальные настройки вознаграждений на двух уровнях.
			Закрепление за партнером на настраиваемый срок.
			CRM, материалы для партнеров, вебинары с партнерами, рассылки партнерам, учет и аналитика.
			</li>
			<li><b>Партнерские промокоды.</b> Теперь у вас есть инструмент, чтобы договариваться с блогерами.
			Автоматический расчет вознаграждения по промокоду на двух уровнях.
			Личные кабинеты обеспечивают доверие инфлюенсеров.
			</li>
			<li><b>Бусткоды.</b> Уникальное решение для создание настоящей автоматической воронки продаж.
			Выдавайте бусткод при каждой покупке и работайте с армией микроблогеров, которыми станут все ваши покупатели.
			Работает полностью автоматически и не имеет аналогов.
			Выплата вознаграждения бонусами вашего магазина.<br>
			<a href='https://t.me/winwinland_ru/400' class='' target='_blank'>отзыв клиента, который это использует</a>
			</li>
		</ul>

		<h3>Как разобраться</h3>
		<p>Нет времени, вам нужно продавать, а не копаться с настройками?
		</p>
		<p>Это правильный подход, который применяют все успешные бизнесы. Закажите настройку
		и не тратьте свое время, займитесь лучше продажами!
		</p>

		<h3>Сколько стоит</h3>
		<p>Минимальная абонентская плата за использование сервиса WinWinLand составляет 1900р/мес.
		Стоимость фиксированная и не зависит от количества лидов, партнеров, вознаграждений и других факторов.
		Осталось только оплатить и один раз настроить.
		</p>


		<h2 class='text-center' >Как платить за результат</h2>

		<div class="youtube my-4">
			<div id="player"></div>
			<script>
			   var player = new Playerjs({id:"player",
				   file:"https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/Promo/WinWinLand_ecommerce_2/master.m3u8",
				   poster:"https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/Promo/WinWinLand_ecommerce_2/poster.jpg"
				   });
			</script>
		</div>

		<h2 class='text-center' >Как установить приложение</h2>
		<p class='text-center font-weight-bold alert alert-success' >Вам доступен 14 дней бесплатный тестовый период без ограничения функционала</p>
		<p class='text-center' >Пройдите несложную регистрацию и все будет сделано автоматически</p>
		<form id='f1'>
			<div class="form-group">
				<label for="client">Ваш имя</label>
				<input type="text" class="form-control" value="<?=$client?>" id="client" name="client" placeholder="ваше имя" required>
			</div>
			<div class="form-group">
				<label for="phone">Телефон</label>
				<input type="tel" class="form-control" value="<?=$phone?>" id="phone" name="phone" placeholder="телефон" required>
			</div>
			<div class="form-group">
				<label for="email">E-mail</label>
				<input type="email" class="form-control" value="<?=$email?>" id="email" name="email" placeholder="email" required>
			</div>
			<input type='hidden' name='insales_id' value='<?=$insales_id?>'>
			<input type='hidden' name='shop' value='<?=$shop?>'>
			<input type='hidden' name='send' value='yes'>
			
			<button type="submit" class="btn btn-primary">Отправить</button>
			<p class='text-danger mt-2' >* После отправки формы будет создан бесплатный аккаунт WinWinLand, это может занять некоторое время, дождитесь пожалуйста загрузки страницы!</p>
		</form>
		<br><br>
	</div>
    <script>
        document.getElementById('f1').addEventListener('submit', function(event) {
            event.preventDefault();

            // Получаем значения полей
            const client = document.getElementById('client').value;
            const phone = document.getElementById('phone').value;
            const email = document.getElementById('email').value;

            // Проверка валидности
            if (client.length === 0) {
                alert("Пожалуйста, введите ваше имя.");
                return;
            }

            const phonePattern = /^[\+\s\-0-9]{10,18}$/;
            if (!phonePattern.test(phone)) {
                alert("Введите корректный номер телефона (10-15 цифр).");
                return;
            }

            if (!validateEmail(email)) {
                alert("Введите корректный адрес электронной почты.");
                return;
            }

            // Если все проверки пройдены, отправляем форму (можно реализовать AJAX вызов)
            //alert("Форма успешно отправлена!");
            this.submit(); // Раскомментируйте для реальной отправки формы
        });

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(String(email).toLowerCase());
        }
    </script>

    <? } ?>

	<?
} else { //if account exists
	$in->ctrl_id=$ctrl_id;
	//~ if(!$in->check_webhooks($insales_id)) {
		//~ if($in->error_code == 423) {
			//~ print "<p class='alert alert-info my-5' >Ваш аккаунт inSales $insales_id заблокирован</p>";
			//~ include "land_bottom.inc.php";
			//~ exit;
		//~ }
 		//~ $ctrl_dir=$db->get_ctrl_dir($ctrl_id);
		//~ $url="https://for16.ru/d/$ctrl_dir/insales_webhook.php";
		//~ $res1=$in->webhook_create($url, $event='orders/update');
		//~ $res2=$in->webhook_create($url, $event='orders/create');
		//~ if($in->check_webhooks($insales_id)) {
			//~ $db->notify_me("INSALES webhook created Ok in go.php 246 1=$res1 2=$res2. ctrl_id=$ctrl_id shop=$shop");
			//~ http_response_code(200);
		//~ } else {
			//~ $db->notify_me("INSALES webhook create ERROR in go.php 246 1=$res1 2=$res2. ctrl_id=$ctrl_id shop=$shop");
			//~ print "<p class='alert alert-danger my-4' >Не установлен вебхук. <a href='https://t.me/vkt_support_bot?start=ask_support_$ctrl_id' class='' target=''>Обратитесь в техподдержку</a></p>";
		//~ }
	//~ }
	
	$company=$db->dlookup("company","0ctrl","id=$ctrl_id");
	$url=$db->get_ctrl_link($ctrl_id,"last_10");
	$database=$db->get_ctrl_database($ctrl_id);
	$insales_status=$db->dlookup("insales_status","0ctrl","id=$ctrl_id");
	$insales_delay_fee=$db->dlookup("insales_delay_fee","0ctrl","id=$ctrl_id");
	$dir=$db->get_ctrl_dir($ctrl_id);
	$uid=$db->dlookup("uid","0ctrl","id='$ctrl_id'");
	$tm_end=$db->avangard_tm_end($uid,[$product_id]);
	$dt_end=date("d.m.Y",$tm_end);
	$price_abon=$db->dlookup("price1","product","id=32");
	$price_nastr=$db->dlookup("price1","product","id=33");
	$price_tracking=$db->dlookup("price1","product","id=37");

	$r_ctrl=$db->fetch_assoc($db->query("SELECT * FROM 0ctrl WHERE id='$ctrl_id'"));

	$db=new vkt($database);
	$user_id=3;
	$direct_code=$db->dlookup("direct_code","users","id=$user_id");
	if(empty($direct_code)) {
		$db->query("UPDATE users SET direct_code='".$db->get_direct_code($user_id)."' WHERE id=$user_id");
		$direct_code=$db->dlookup("direct_code","users","id=$user_id");
	}
	//$url.="&u=$direct_code";
	
	//print "ctrl_id=$ctrl_id $company $url<br>";
?>
	<p class='alert  alert-success text-center mt-3' >Расширение WinWinLand.Лояльность 2.0  успешно установлено (<?=$ctrl_id?> <?=$webhook_id?>)</p>
	<h2 class='text-center' ><a href='<?=$url?>' class='btn btn-primary btn-lg' target='_blank'>Войти в CRM</a></h2>
	<p class='text-center' ><a href='<?="$domen/doc.php"?>' class='' target='_blank'>Документация по установке</a></p>

	<h2 class='text-center' >Что это вам даст</h2>
	<p>Расширение WinWinLand приведет покупателей в ваш магазин без предоплат и расходов на рекламу.
	Вы можете задействовать весь самый современный функционал партнерских программ, подключить ресурсы,
	которые у вас уже есть и которые вы не использовали, расширить свои возможности и даже вывести в свой магазин
	покупателей из маркетплэйсов.
	</p>
	<h3>В вашем распоряжении:</h3>
	<ul>
		<li><b>Партнерские ссылки.</b> Регистрация партнеров, выдача им индивидуальных ссылок и партнерских кабинетов.
		Общие или индивидуальные настройки вознаграждений на двух уровнях.
		Закрепление за партнером на настраиваемый срок.
		CRM, материалы для партнеров, вебинары с партнерами, рассылки партнерам, учет и аналитика.
		</li>
		<li><b>Партнерские промокоды.</b> Теперь у вас есть инструмент, чтобы договариваться с блогерами.
		Автоматический расчет вознаграждения по промокоду на двух уровнях.
		Личные кабинеты обеспечивают доверие инфлюенсеров.
		</li>
		<li><b>Бусткоды.</b> Уникальное решение для создание настоящей автоматической воронки продаж.
		Выдавайте бусткод при каждой покупке и работайте с армией микроблогеров, которыми станут все ваши покупатели.
		Работает полностью автоматически и не имеет аналогов.
		Выплата вознаграждения бонусами вашего магазина.<br>
		<a href='https://t.me/winwinland_ru/400' class='' target='_blank'>отзыв клиента, который это использует</a>
		</li>
	</ul>

	<h3>Как разобраться</h3>
	<p>Нет времени, вам нужно продавать, а не копаться с настройками?
	</p>
	<p>Это правильный подход, который применяют все успешные бизнесы. Закажите настройку за 45000 р.
	и не тратьте свое время, займитесь лучше продажами! <br>
	<a href='#'  data-target='#nastr' data-toggle="collapse" >что входит в настройку</a>
	</p>

	<div class='collapse' id='nastr' >
		<pre>
1. Техническая интеграция
   - Настройка API-подключения к вашему магазину на платформе inSales
   - Интеграция системы промокодов с вашей корзиной
   - Настройка автоматической генерации уникальных промокодов
   - Подключение системы учета транзакций

2. Настройка партнерской программы
   - Создание структуры вознаграждений
   - Настройка правил начисления баллов/бонусов
   - Установка условий использования промокодов
   - Кастомизация партнерских кабинетов (логотип заказчика)

3. Автоматизация процессов
   - Настройка автоматической рассылки промокодов новым клиентам
   - Настройка уведомлений для партнеров
   - Создание чат бота для системных уведомлений

4. Обучение персонала
   - Проведение обучающего вебинара для сотрудников
   - Предоставление инструкций по управлению системой
   - Базовые рекомендации по развитию партнерской сети

5. Техническая поддержка
   - Сопровождение в течение первого месяца работы
   - Помощь в решении возникающих вопросов
   - Корректировка настроек по необходимости

После завершения настройки система готова к полноценной работе.
Дальнейшее обслуживание осуществляется в рамках абонентской платы.
		</pre>
	</div>

	<h3>Сколько стоит</h3>
	<p>Минимальная абонентская плата за использование сервиса WinWinLand составляет 1900р/мес.
	Стоимость фиксированная и не зависит от количества лидов, партнеров, вознаграждений и других факторов.
	Осталось только оплатить и один раз настроить.
	</p>

	
	<h2 class='text-center' >Если вы все же решили сделать все сами. Как настроить расширение</h2>

	<div class="youtube my-4">
		<div id="player"></div>
		<script>
		   var player = new Playerjs({id:"player",
			   file:  "https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/manual/winwinland_insales_howto/master.m3u8",
			   poster:"https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/manual/winwinland_insales_howto/poster.jpg"
			   });
		</script>
	</div>

	<p class='text-left font-weight-normal mt-5' >Подпишитесь на
		<a href='https://t.me/vkt_support_bot?start=<?=$db->uid_md5($uid)?>' class='' target=''>наш чатбот телеграм</a> для получения важных уведомлений сервиса,
		а также чтобы задать вопрос техподдержке.
	</p>
	<div class='my-4' >
		<?
		$days=floor(($tm_end-time())/(24*60*60)); 
		$days_=($days>4) ? "$days дней" : "$days дня";
		//print "tm_end=$tm_end $dt_end $product_id <a href='https://for16.ru/d/1000/msg.php?uid=$uid' class='' target='_blank'>$uid</a> <br>"
		?>
		<?if($days<5) { ?>
		Тестовый период заканчивается через <b><?=$days_?></b>.
		<a href='#' class='' data-target='#prices' data-toggle="collapse" >
			<i class='fa fa-info-circle' ></i> тарифы
		</a>
		<div class='collapse card p-2 my-3' id='prices'>
			<p>1. Абонентская плата, 12 месяцев: <b><?=$price_abon?>р.</b> <a href='https://winwinland.ru/order.php?uid=<?=$db->uid_md5($uid)?>&product_id=32' class='' target='_blank'>оплатить</a></p>
			<p>2. Услуга по настройке под ключ: <b><?=$price_nastr?>р.</b>  <a href='https://winwinland.ru/order.php?uid=<?=$db->uid_md5($uid)?>&product_id=33' class='' target='_blank'>оплатить</a></p>
			<p>3. Услуга по внедрению партнерской программы в ваш бизнес + трекинг, 3 мес: <b><?=$price_tracking?>р.</b>  <a href='https://winwinland.ru/order.php?uid=<?=$db->uid_md5($uid)?>&product_id=37' class='' target='_blank'>оплатить</a></p>
			<p>Если у вас есть вопросы по тарифам, обратитесь в <a href='https://t.me/vkt_support_bot?start=<?=$db->uid_md5($uid)?>' class='' target='_blank'>техподдержку</a>.</p>
		</div>
		<?}?>
		
	</div>

	<h2 class='text-center' >Настройки</h2>

<?


	$website=$db->dlookup("land_url","lands","del=0 AND land_num=2");
	if(!$website)
		$website="https://".$shop;

	if(!$db->dlookup("id","product","del=0 AND id=1")) {
		$db->query("INSERT INTO `product` (`id`, `sku`, `price0`, `price1`, `price2`, `descr`, `term`, `source_id`, `razdel`, `tag_id`, `installment`, `fee_1`, `fee_2`, `fee_cnt`, `stock`, `senler`, `sp`, `sp_template`, `jc`, `in_use`, `vid`, `del`) VALUES
		(1, '', 0, 0, 0, 'Все продукты', 0, 0, 1, 0, 0, 10, 3, 0, 0, 0, 0, '', '', 0, 0, 0)");
		print "<p class='alert alert-success' >Продукт по умолчанию создан</p>";
	}
	$fee1=$db->dlookup("fee_1","product","del=0 AND id=1");
	$fee2=$db->dlookup("fee_2","product","del=0 AND id=1");
	$fee_cnt=$db->dlookup("fee_cnt","product","del=0 AND id=1");

	if(!$db->dlookup("id","lands","del=0 AND fl_partner_land=1 AND land_num=1")) {
		if(!$db->dlookup("id","lands","id=1 OR id=2")) {
			$db->query ("
				INSERT INTO `lands` (`id`, `tm`, `user_id`, `land_num`, `fl_not_disp_in_cab`, `tm_scdl`, `tm_scdl_period`, `land_url`, `land_name`, `land_txt`, `thanks_txt`, `bot_first_msg`, `land_razdel`, `land_tag`, `fl_partner_land`, `fl_disp_phone`, `fl_disp_email`, `fl_disp_comm`, `label_disp_comm`, `fl_disp_phone_rq`, `fl_disp_email_rq`, `fl_disp_city`, `fl_disp_city_rq`, `product_id`, `btn_label`, `bizon_duration`, `bizon_zachot`, `land_type`, `del`) VALUES
		(1, 1735760432, 0, 1, 1, 0, 0, 'https://for16.ru/d/$dir/1', 'Партнерская программа', '<h2 style=\"text-align: center;\"><span style=\"font-family: arial, helvetica, sans-serif; color: #236fa1;\">Примите участие в партнерской программе</span></h2>', '<h2 style=\"text-align: center;\"><span style=\"font-family: arial, helvetica, sans-serif; color: #236fa1;\">Благодарим за регистрацию!</span></h2>\r\n<p style=\"text-align: center;\"><span style=\"font-family: arial, helvetica, sans-serif;\">Ваша партнерская ссылка и доступ в личный кабинет партнера придет к вам в телеграм. Подпишитесь по кнопке ниже:</span></p>\r\n<p style=\"text-align: center;\">&nbsp;</p>', 'Еще раз благодарим за регистрацию в партнерской программе\r\n\r\nВаша партнерская ссылка : $website/?bc={{partner_code}}\r\n\r\nЛичный кабинет: {{cabinet_link}}', 0, 0, 1, 1, 1, 0, '', 1, 0, 0, 0, 0, 'Регистрация', 0, 0, 1, 0),
		(2, 1735789936, 0, 2, 0, 0, 0, '$website', 'Сайт компании', '', '', '', 0, 0, 0, 1, 0, 0, '', 1, 0, 0, 0, 0, 'Регистрация', 0, 0, 0, 0);
				");
				print "<p class='alert alert-success' >Шаблонные лэндинги созданы</p>";
		}
	}

	

	if(isset($_GET['ch_website'])) {
		$website=mb_substr(trim($_GET['website']),0,64);
		$headers = @get_headers($website); // Suppress errors and fetch headers
		if( !is_array($headers) || (strpos($headers[0], '200')===false && strpos($headers[0], '301')===false) ) { // Check for 200 OK
			//print_r($headers);
			print "<p class='alert alert-warning' >Сайт недоступен или неправильно указан</p>";
			$website="https://".$shop;
		} else {
			$website = preg_replace("/\?.*$/", "", $website);
			$db->query("UPDATE lands SET land_url='".$db->escape($website)."' WHERE del=0 AND land_num=2");
			$bot_first_msg=$db->dlookup("bot_first_msg","lands","land_num=1");
			//print ($bot_first_msg);
			$bot_first_msg=preg_replace("/http[^\s]*\?bc=\{\{partner_code\}\}/",$website."?bc={{partner_code}}",$bot_first_msg);
			$db->query("UPDATE lands SET bot_first_msg='".$db->escape($bot_first_msg)."' WHERE del=0 AND land_num=1");
			//print ("<br><br>".$bot_first_msg);
			print "<p class='alert alert-success' >Сайт изменен</p>";
		}
	}
	if(isset($_GET['ch_insales_status'])) {
		if(($insales_status=mb_substr(trim($_GET['insales_status']),0,32))!="") {
			$db=new vkt('vkt');
			$insales_delay_fee=intval($_GET['insales_delay_fee']);
			$db->query("UPDATE 0ctrl SET insales_status='".$db->escape($insales_status)."',insales_delay_fee='$insales_delay_fee' WHERE id='$ctrl_id'");
			print "<p class='alert alert-success' >Статус изменен на: $insales_status, $insales_delay_fee дней</p>";
			$db=new vkt($database);
		}
	}
	if(isset($_GET['ch_fee'])) {
		$fee1=intval($_GET['fee1']);
		$fee2=intval($_GET['fee2']);
		$fee_cnt=intval($_GET['fee_cnt']);
		$db->query("UPDATE product SET fee_1='$fee1',fee_2='$fee2', fee_cnt='$fee_cnt' WHERE id=1");
		print "<p class='alert alert-success' >Вознаграждения по умолчанию изменены</p>";
	}
	
	$path_files="/var/www/vlav/data/www/wwl/d/$dir/tg_files";
	if(!file_exists($path_files."/land_pic_1.jpg")) {
		copy("/var/www/vlav/data/www/wwl/scripts/insales/land_pic_1.jpg",$path_files.'/land_pic_1.jpg');
		copy("/var/www/vlav/data/www/wwl/scripts/insales/thanks_pic_1.jpg",$path_files.'/thanks_pic_1.jpg');
		copy("/var/www/vlav/data/www/wwl/scripts/insales/logo.jpg",$path_files.'/logo.jpg');
				print "<p class='alert alert-success' >Баннер лэндинга скопирован</p>";
	}
	
	$r=$db->fetch_assoc($db->query("SELECT * FROM lands WHERE del=0 AND fl_partner_land=1"));
	$land_url=$r['land_url']; $land_name=$r['land_name'];
	$land_url_2=$db->dlookup("land_url","lands","del=0 AND id=2");

	if(!$db->dlookup("id","vkt_send_1","id=1"))
		$db->query("INSERT INTO `vkt_send_1` (`id`, `tm`, `vkt_send_tm`, `tm_shift`, `land_num`, `sid`, `name_send`, `msg`, `email_template`, `email_from`, `email_from_name`, `vk_attach`, `tg_image`, `tg_video`, `tg_video_note`, `tg_audio`, `tm1`, `tm2`, `fl_clients`, `fl_partners`, `fl_leads`, `fl_tg`, `fl_vk`, `fl_email`, `fl_razdel`, `fl_land`, `fl_tag`, `fl_chk`, `del`) VALUES
(1, ".time().", 0, 0, 0, -1, 'Начисления партнерам (служебная рассылка)', '{{fee_pay}}', '', '', '', '', '', '', '', '', ".time().", ".time().", 0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0);
");
	?>

	<div class='my-3 card p-2' id='status'>
    <form class="form" action="#status">
        <? if(empty($insales_status))
                print "<p class='alert alert-warning' >Необходимо указать статус, при достижении которого можно начислять вознаграждение партнеру!</p>";
        ?>
        <div class="d-flex">
            <!-- First Block -->
            <div class="form-group mr-3">
                <label for="insales_status" class="w-100">
                    <b>Статус заказа для начисления партнерского вознаграждения:</b>
                </label>
                <div>
                    <input type="text" 
                           class="form-control" 
                           id="insales_status" 
                           value="<?=$insales_status?>" 
                           name="insales_status" 
                           placeholder="???">
                </div>
            </div>
            
            <!-- Second Block -->
            <div class="form-group mr-3">
                <label for="insales_delay_fee" class="w-100">
                    <b>Количество дней задержки:</b>
                </label>
                <div>
                    <input type="number" 
                           class="form-control" 
                           id="insales_delay_fee" 
                           value="<?=$insales_delay_fee?>" 
                           name="insales_delay_fee" 
                           placeholder="дней">
                </div>
            </div>

            <!-- Button -->
            <div class="form-group d-flex align-items-end">
                <button type="submit" 
                        class="btn btn-primary" 
                        name="ch_insales_status" 
                        value="yes">
                    <i class='fa fa-save'></i>
                </button>
            </div>
        </div>
    </form>
    <p class='small'>При ручном или автоматическом переводе заказа в этот статус партнеру будет начислено вознаграждение</p>
    <p class='small'>Количество дней задержки - вознаграждение будет начислено через указанное количество дней после изменения статуса
    (обычно 14), чтобы гарантировать, что возвратов по заказу не будет. 
    </p>
	</div>

	<div class='my-3 card p-2' id='go_fee'>
		<form class="form-inline d-flex" action='#go_fee'>
			<div class="form-group flex-grow-1 mb-2">
				<label for="fee" class="mr-2"><b>Партнерское вознаграждение</b></label>
				<div class='w-100 my-2' id='fee'>
				Уровень 1 (% или руб): <input type="number" class="form-control w-10 mx-2" id="fee1" value="<?=$fee1?>" name="fee1">
				Уровень 2 (% или руб): <input type="number" class="form-control w-10 mx-2" id="fee2" value="<?=$fee2?>" name="fee2">
				</div>
				<div class='w-100 my-2' >
				На сколько продаж начислять вознаграждение (0 - без огр.): <input type="number" class="form-control w-10 mx-2" id="fee_cnt" value="<?=$fee_cnt?>" name="fee_cnt">
				</div>
				<button type="submit" class="btn btn-primary my-2 mb-2 ml-2  align-self-end" name="ch_fee" value="yes">
					<i class='fa fa-save'></i>
				</button>
			</div>
		</form>
		<p class='small' >считается в % или рублях, если значение больше 100</p>

		<a href='#' class='' data-target='#howto_hold' data-toggle="collapse" >
			<i class='fa fa-info-circle' ></i> как изменить срок привязки клиента к партнеру
		</a>
		<div class='collapse card p-2 my-3' id='howto_hold'>
			<p>1. <a href='<?=$url?>' class='' target='_blank'>Зайти</a> в CRM</p>
			<p>2. Меню - Настройки-Профиль-Настройка доп условий вознаграждений</p>
			<p>3. Раскрыть и установить срок закрепления приглашенных за партнером</p>
			<p>4. По желанию можно указать приветственные баллы, которые автоматически начисляются партнеру при регистрации</p>
			<p>5. А также поставить или убрать галочку - передавать ли клиента новому партнеру, если вновь зашел в магазин, но по другой партнерской ссылке.</p>
		</div>

		<a href='#' class='' data-target='#howto_vip' data-toggle="collapse" >
			<i class='fa fa-info-circle' ></i> как задать отдельные вознаграждения для продукта
		</a>
		<div class='collapse card p-2 my-3' id='howto_vip'>
			<p>1. <a href='<?=$url?>' class='' target='_blank'>Зайти</a> в CRM</p>
			<p>2. Меню - <a href='#howto_vip' class="image-link" data-image="https://for16.ru/scripts/insales/demo_vip_1.png" onclick="openImage(this)">Настройки-Продукты</a></p>
			<p>3. Вы видите один продукт, который называется &quot;Все продукты&quot;
			и у него не задан SKU. Вознаграждения по партнерской программе, установленные для этого продукта,
			будут применяться для всех продуктов из магазина, по умолчанию.
			Если теперь мы <a href='#howto_vip' class="image-link" data-image="https://for16.ru/scripts/insales/demo_vip_2.png" onclick="openImage(this)">добавим новый продукт в WinWinLand</a>
			и <a href='#howto_vip' class="image-link" data-image="https://for16.ru/scripts/insales/demo_vip_3.png" onclick="openImage(this)">укажем у него SKU</a>
			совпадающий с артикулом (SKU) в магазине,
			то <a href='#howto_vip' class="image-link" data-image="https://for16.ru/scripts/insales/demo_vip_4.png" onclick="openImage(this)">вознаграждения будут браться по этому продукту</a>.
			Таким образом можно настроить индивидуальные условия по партнерской программе для
			отдельных продуктов.
			</p>
		</div>

		<a href='#' class='' data-target='#howto_vip2' data-toggle="collapse" >
			<i class='fa fa-info-circle' ></i> как задать индивидуальные вознаграждения для партнеров
		</a>
		<div class='collapse card p-2 my-3' id='howto_vip2'>
			<p>1. <a href='<?=$url?>' class='' target='_blank'>Зайти</a> в CRM</p>
			<p>2. <a href='#howto_vip2' class="image-link" data-image="https://for16.ru/scripts/insales/demo_vip2_1.png" onclick="openImage(this)">Найти партнера в crm</a>
			и зайти в его карточку.
			</p>
			<p>3. Нажать на кнопку <a href='#howto_vip2' class="image-link" data-image="https://for16.ru/scripts/insales/demo_vip2_2.png" onclick="openImage(this)">&quot;Партнер инфо&quot;</a>
			И нажать кнопку <a href='#howto_vip2' class="image-link" data-image="https://for16.ru/scripts/insales/demo_vip2_3.png" onclick="openImage(this)">&quot;По товарам&quot;</a>
			</p>
			<p>Далее выбрать товар и задать по нему <a href='#howto_vip2' class="image-link" data-image="https://for16.ru/scripts/insales/demo_vip2_4.png" onclick="openImage(this)">индивидуальные вознаграждения</a>,
			 которые будут применяться только к этому партнеру.
			</p>
		</div>
	</div>

	<div class='card my-3 p-2' id='website'>
		<div>
		<b>Ссылка на сайт магазина:</b>
		<a href='<?=$land_url_2?>' target='_blank'><?=$land_url_2?></a>
		</div>
		<a href='#' class='' data-target='#website' data-toggle="collapse" ><i class='fa fa-info-circle' ></i> изменить</a>
		<div class='collapse card p-2 my-3' id='website'>
			<form class="form-inline d-flex" action='#website'>
				<div class="form-group flex-grow-1 mb-2">
					<label for="website" class="mr-2"><b>Сайт интернет магазина</b></label>
					<? if($website=="\$website")
							$db->notify_me("INSALES ERROR website=\$website");
					?>
					<input type="url" class="form-control w-100" id="website" value="<?=$website?>" name="website" placeholder="Сайт">
				</div>
				<button type="submit" class="btn btn-primary mb-2 ml-2  align-self-end" name="ch_website" value="yes">
					<i class='fa fa-save'></i>
				</button>
			</form>
			<p class='small' >полный адрес, начиная с https://</p>
		</div>
	</div>

	<div class='card my-3 p-2' >
		<div>
			<b>Ссылка на лэндинг для регистрации партнеров:</b>
			<span id='land_url'><a href='<?=$land_url?>' target='_blank'><?=$land_url?></a></span>
			<a href='javascript:copySpanContent("land_url");' class='text-info' target='' title='скопировать ссылку'>
				<i class='fa fa-copy' ></i> 
			</a>
		</div>
		<a href='#' class='' data-target='#howto_land' data-toggle="collapse" ><i class='fa fa-info-circle' ></i> как изменить картинку и текст лэндинга</a>
		<div class='collapse card p-2 my-3' id='howto_land'>
			<p>1. <a href='<?=$url?>' class='' target='_blank'>Зайти</a> в CRM</p>
			<p>2. Меню - Настройки-Профиль-Лэндинги</p>
			<p>3. Раскрыть партнерский лэндинг и прокрутить вниз до загрузки изображения и ввода текста лэндинга</p>
			<p>4. Загрузить другое изображение (лучше брать формат JPG размер 900px по ширине)</p>
			<p>5. Отредактировать ниже текст лэндинга</p>
			<p>6. Также ниже можно изменить текст и изображение страницы благодарности и первое сообщение чат бота при регистрации</p>
			<p>7. Если вы хотите использовать для регистрации партнеров свой лэндинг или страницу сайта,
			на нее можно добавить код для интеграции с WinWinLand. Как это сделать проконсультируйтесь
			с техподдержкой.
			</p>
			<p>8. Также вы можете привязать свой домен и получить красивый адрес лэндинга. Для этого обратитесь в <a href='#support' class='' target=''>техподдержку</a>.</p>
		</div>
	</div>

	<div class='card my-3 p-2' >
		<p><b>Логотип</b>, который видят партнеры в личном кабинете:</p>
		<img src='<?="https://for16.ru/d/$dir/tg_files/logo.jpg"?>' alt='logo' style='width:200px;' >
		<a href='#' class='' data-target='#howto_logo' data-toggle="collapse" ><i class='fa fa-info-circle' ></i> как загрузить свой логотип</a>
		<div class='collapse card p-2 my-3' id='howto_logo'>
			<p>1. <a href='<?=$url?>' class='' target='_blank'>Зайти</a> в CRM</p>
			<p>2. Меню - Настройки-Профиль-Название и реквизиты</p>
			<p>3. Подготовьте файл с логотипом (оптимальный размер 200х50 px) и выгрузите его</p>
			<p>4. Также укажите название компании и реквизиты</p>
			
		</div>
	</div>

	<div class='card my-3 p-2' id='docs'>
		<?
		$pp=(!empty($r_ctrl['pp'])) ? "<a href='{$r_ctrl['pp']}' class='' target='_blank'>{$r_ctrl['pp']}</a>" : "<span class='text-danger' >не указан</span>";
		$oferta=(!empty($r_ctrl['oferta'])) ? "<a href='{$r_ctrl['oferta']}' class='' target='_blank'>{$r_ctrl['oferta']}</a>" : "<span class='text-danger' >не указан</span>";
		$agreement=(!empty($r_ctrl['agreement'])) ? "<a href='{$r_ctrl['agreement']}' class='' target='_blank'>{$r_ctrl['agreement']}</a>" : "<span class='text-danger' >не указан</span>";
		$oferta_referal=(!empty($r_ctrl['oferta_referal'])) ? "<a href='{$r_ctrl['oferta_referal']}' class='' target='_blank'>{$r_ctrl['oferta_referal']}</a>" : "<span class='text-danger' >не указан</span>";
		$partnerka_adlink=(!empty($r_ctrl['partnerka_adlink'])) ? "<a href='{$r_ctrl['partnerka_adlink']}' class='' target='_blank'>{$r_ctrl['oferta_referal']}</a>" : "<span class='text-danger' >не указан</span>";
		?>
		<b>Документы:</b>
		<p>Политика об обработке персональных данных: <?=$pp?></p>
		<p>Пользовательское соглашение: <?=$oferta?></p>
		<p>Согласие на получение информационных материалов: <?=$agreement?></p>
		<p>Партнерское соглашение: <?=$oferta_referal?></p>
		<p>Материалы для партнеров: <?=$partnerka_adlink?></p>
		<a href='#' class='' data-target='#howto_docs' data-toggle="collapse" ><i class='fa fa-info-circle' ></i> как настроить</a>
		<div class='collapse card p-2 my-3' id='howto_docs'>
			<p>1. <a href='<?=$url?>' class='' target='_blank'>Зайти</a> в CRM</p>
			<p>2. Меню - <a href='#docs' class="image-link" data-image="https://for16.ru/scripts/insales/demo6.png" onclick="openImage(this)">Настройки-Профиль-Ссылки на документы и настройка пикселей</a></p>
		</div>
	</div>


	<div class='card my-3 p-2' id='bots' >
		<?
		if(empty($r['tg_bot_notif']))
			print "<p class='alert alert-warning' >Не настроен чат бот ТГ для уведомлений из crm!</p>";
		if(empty($r['tg_bot_msg']))
			print "<p class='alert alert-warning' >Не настроен чат-бот ТГ для переписки с партнерами и лидами!</p>";
		?>
		<b>Создание чат-ботов телеграм</b>
		<p>Для комфортной работы вам понадобятся два чат-бота.</p>
		
		<p>1. Бот для переписки. На этот бот подписывается партнер при регистрации на лэндинге,
		в бот приходят ему необходимые для работы ссылки, он может написать свой вопрос в бот,
		вопрос попадет CRM и вы его увидите. Также этот бот служит для переписки с партнерами
		из CRM WinWinLand и осуществления рассылок.
		<a href='https://help.winwinland.ru/docs/nastroyka-chat-bota-telegram-dlya-perepiski/' class='' target='_blank'>Как создать и подключить бот для переписки</a>.
		<br>(Меню - Настройки-Профиль-Настройка чат бота телеграм для переписки)
		</p>
		<p>2. Служебный бот. Полезен для комфортной работы, в этот бот приходят уведомления о входящих
		сообщениях от партнеров, уведомления о сделках по партнерским ссылкам и пр.
		<a href='https://help.winwinland.ru/docs/sluzhebnyy-tg-bot-dlya-uvedomleniy-iz-crm/' class='' target='_blank'>Как создать и подключить служебный бот</a>.
		<br>(Меню - Настройки-Профиль-Служебный ТГ бот для уведомлений из CRM)
		</p>
	</div>


	<h2 class='text-center' >Как протестировать</h2>
<!--
	<p class='text-center' >Посмотрите видео: Как работает партнерская программа на WinWinLand в связке с inSales</p>

	<div class="youtube my-4">
		<div id="player2"></div>
		<script>
		   var player2 = new Playerjs({id:"player2",
			   file:"https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/winwinland-insales-ok/master.m3u8",
			   poster:"https://for16.ru/scripts/insales/winwinland-insales-ok.jpg"
			   });
		</script>
	</div>
-->
	<p id='1'>1. Зарегистрируйтесь на партнерском лэндинге <a href='<?=$land_url?>' target='_blank'><?=$land_url?></a>,
	там подключите телеграм бота (которого вы ранее <a href='#bots' class='' target=''>создали</a> и прописали в WinWinLand) и получите в тг партнерскую ссылку и доступ в личный кабинет.
	Итак вы стали партнером.
	</p>
	<p id='2'>2. Перейдите по партнерской ссылке в ваш магазин и сделайте заказ.
	Укажите телефон и емэйл нового покупателя
	(отличные от тех, с которыми вы регистрировались на партнерском лэндинге в п.1).
	</p>
	<p id='3'>3. Измените в админ панели inSales статус этого заказа на <b><?=$insales_status?></b>.
	В этот момент заказ считается выполненным и партнеру будет начислено вознаграждение.
	</p>
	<p id='4'>4. <a href='<?=$url?>' class='' target='_blank'>Зайдите в WinWinLand CRM</a> и убедитесь,
	что появился <a href='#4' class="image-link" data-image="https://for16.ru/scripts/insales/demo3.png" onclick="openImage(this)">партнер (п.1) и клиент, купивший продукт</a> (п.2).
	Зайдите в клиента (кликнуть по имени) и убедитесь, что он
	<a href='#4' class="image-link" data-image="https://for16.ru/scripts/insales/demo1.png" onclick="openImage(this)" class='' target=''>закреплен за партнером и проведена оплата</a> продукта,
	который он купил по п.2.
	</p>
	<p id='5'>5. <span class='text-danger' >После того, как сделка по партнерской ссылке совершена, вознаграждение партнеру моментально не начисляется</span>.
	Зайдите в <a href='#5' class="image-link" data-image="https://for16.ru/scripts/insales/demo2.png" onclick="openImage(this)">отчет по партнерским начислениям</a> и обновите его, нажав на кнопку <b>Обновить начисления</b>.
	Либо автоматически начисления партнерам обновятся на следующие сутки.
	</p>
	<p id='6'>6. Вы <a href='#6'  class="image-link" data-image="https://for16.ru/scripts/insales/demo4.png" onclick="openImage(this)">видите в отчете</a> партнера и сумму начислений, можно посмотреть детализацию, кликнув на сумме.
	</p>
	<p id='7'>7. Откройте <a href='#7'  class="image-link" data-image="https://for16.ru/scripts/insales/demo5.png" onclick="openImage(this)">отчет по продажам</a>, нажмите вкладку По партнерам и откроется сводка продаж по партнерам.
	</p>
	<div class='card p-3' id='support'>
	<p>Полная документация доступна <a href='https://help.winwinland.ru' class='' target='_blank'>по ссылке</a> (знак вопроса вверху слева в CRM).
	</p>
	<p><b>Задать вопрос техподдержке</b> вы можете, написав в <a href='https://t.me/vkt_support_bot?start=ask_support_<?=$ctrl_id?>' class='' target='_blank'>телеграм бота</a>.
	</p>
	<p >Если необходима доработка партнерской программы под ваши условия или помощь в запуске,
	напишите, пожалуйста, в техподдержку!
	</p>
	</div>
	<br><br><br>


	
	<?
}
?>
<?
//print "webhook_id = ".insales_get_webhook($insales_id);
include "land_bottom.inc.php";

exit;

$db->print_r(insales_get_account());

print $webhook_id = insales_get_webhook($insales_id);
exit;

$ctrl_dir=$db->get_ctrl_dir($ctrl_id);
$url="https://for16.ru/d/$ctrl_dir/insales_webhook.php";
insales_webhook_create($url,'orders/update');


?>
