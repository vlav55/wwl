<?
//создать на вашу почту пользователя и дать полные Права на раздел Расширения
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt('vkt');
include "insales_app_credentials.inc.php";
include "insales_func.inc.php";
$title="Winwinland for inSales ИНструкция по установке модуля";
include "land_top.inc.php";
?>
	<div class='' >
		<h4 class='text-center mt-5'  style="color:#EC00B8;" >Инструкция по настройке</h4>
		
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
		<p>Это правильный подход, который применяют все успешные бизнесы. Не тратьте свое время, займитесь лучше продажами!
		</p>
		<div class='card p-3 my-3 bg-light font-weight-bold' ><p>Закажите настройку, не тратьте свое время и получите образцовую партнерскую программу для своего магазина, которая действительно работает!
		Отправьте заявку <a href='https://ask.winwinland.ru/?utm_source=insales' class='' target='_blank'>ask.winwinland.ru</a></p>
		</div>

		<h3>Сколько стоит</h3>
		<p>Минимальная абонентская плата за использование сервиса WinWinLand составляет 1900р/мес.
		Стоимость фиксированная и не зависит от количества лидов, партнеров, вознаграждений и других факторов.
		Осталось только оплатить и один раз настроить. См цены <a href='#rates' class='' target=''>здесь.</a>
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
		<br>
		<h2 class='text-center' >Как как работает программа - демонстрация</h2>
		<div class="youtube my-4">
			<div id="player3"></div>
			<script>
			   var player = new Playerjs({id:"player3",
				   file:"https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/manual/winwinland_integration_insales/master.m3u8",
				   poster:"https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/manual/winwinland_integration_insales/poster.jpg"
				   });
			</script>
		</div>

		<br><br>
		<h1 class='text-center mt-5' >Пошаговая инструкция по настройке приложения</h1>
		<p class='text-center font-weight-bold'  style="color:#EC00B8;">Вам доступен 14 дней бесплатный тестовый период без ограничения функционала</p>
	</div>

	<?

	$ctrl_id=119;
	
	$company=$db->dlookup("company","0ctrl","id=$ctrl_id");
	$url=$db->get_ctrl_link($ctrl_id,"last_10");
	$database=$db->get_ctrl_database($ctrl_id);
	$insales_status=$db->dlookup("insales_status","0ctrl","id=$ctrl_id");
	$dir=$db->get_ctrl_dir($ctrl_id);
	$uid=$db->dlookup("uid","0ctrl","id='$ctrl_id'");
	$tm_end=time()+(10*24*60*60);
	$dt_end=date("d.m.Y",$tm_end);
	$price_abon=$db->dlookup("price1","product","id=32");
	$price_nastr=$db->dlookup("price1","product","id=34");
	$price_tracking=$db->dlookup("price1","product","id=37");

	$r_ctrl=$db->fetch_assoc($db->query("SELECT * FROM 0ctrl WHERE id='$ctrl_id'"));

	$db=new vkt($database);
	$user_id=3;
	$direct_code=$db->dlookup("direct_code","users","id=$user_id");
	if(empty($direct_code)) {
		$db->query("UPDATE users SET direct_code='".$db->get_direct_code($user_id)."' WHERE id=$user_id");
		$direct_code=$db->dlookup("direct_code","users","id=$user_id");
	}
	$url.="&u=$direct_code";
	
	//print "ctrl_id=$ctrl_id $company $url<br>";
?>
<!--
	<h2 class='text-center' >Видео инструкция</h2>
-->

	<div class="youtube my-4">
		<div id="player2"></div>
		<script>
		   var player2 = new Playerjs({id:"player2",
			   file:"  https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/manual/winwinland_insales_howto/master.m3u8",
			   poster:"https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/manual/winwinland_insales_howto/poster.jpg"
			   });
		</script>
	</div>

<!--
	<h2 class='text-center' >Пошаговая инструкция по настройке</h2>
-->

<?


	$website=$db->dlookup("land_url","lands","del=0 AND land_num=2");
	if(!$website)
		$website="https://".$shop;

	if(!$db->dlookup("id","product","del=0 AND id=1")) {
	}
	$fee1=$db->dlookup("fee_1","product","del=0 AND id=1");
	$fee2=$db->dlookup("fee_2","product","del=0 AND id=1");
	$fee_cnt=$db->dlookup("fee_cnt","product","del=0 AND id=1");

	if(!$db->dlookup("id","lands","del=0 AND fl_partner_land=1 AND land_num=1")) {
	}

	if($_GET['ch_website']) {
	}
	if(isset($_GET['ch_insales_status'])) {
	}
	if($_GET['ch_fee']) {
	}
	
	$path_files="/var/www/vlav/data/www/wwl/d/$dir/tg_files";
	if(!file_exists($path_files."/land_pic_1.jpg")) {
	}
	
	$r=$db->fetch_assoc($db->query("SELECT * FROM lands WHERE del=0 AND fl_partner_land=1 AND land_num=1"));
	$land_url=$r['land_url']; $land_name=$r['land_name'];
	$land_url="https://for16.ru/d/1135663375/4";
	$land_url_name="пример партнерского лэндинга";
	$land_url_2="https://yourshop.ru";
	$website=$land_url_2;
	$url="#";
	
	?>

	<h3>1. Укажите статус заказа</h3>
	<p>Cтатус заказа для начисления партнерского вознаграждения.</p>
	<p>В InSales есть статусы заказов, которые отображают состояние заказа: оплата, отгрузка, доставка, возврат и так далее.
	</p>
	<p>У нас стоит задача в какой-то момент начислить партнерское вознаграждение партнеру, который привел этого клиента. Заказы переводятся в различные статусы, либо вручную, либо автоматически, но вам надо указать статус, когда сделка выполнена и заказ уже точно доставлен, по нему не может быть возвратов, претензий или конфликтов. То есть можно начислить партнерское вознаграждение. 
	</p>
	<p>Здесь указывается этот статус, который копируется из статусов аккаунта InSales, нажимаем кнопку "Сохранить". Как только заказ перейдет в этот статус, партнер получит вознаграждение. 
	</p>
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

	<h3>2. Установите размер и условия начисления партнерского вознаграждения</h3>
	<p>Партнерское вознаграждение двухуровневое. Первый уровень: если партнер привел клиента, который что-то купил, он получает вознаграждение в 10%. Если партнер привел второго партнера, который привел клиента, то второй партнер получит 10%, а первый партнер, который привел этого партнера, получит 3%. 
	</p>
	<p>На сколько продаж начислять вознаграждение? Можно на одну продажу. Клиент закрепляется за партнером. На все его покупки начисляются вознаграждения партнеру. Можно ограничить количество продаж, на которые начисляются вознаграждения, либо оставить ноль — это будет без ограничений. 
	</p>
	<p>Также партнерские вознаграждения можно устанавливать в рублях или в процентах, не забывайте нажать кнопку "Сохранить". Есть срок привязки клиента к партнеру. То есть клиент привязывается к партнеру, и при всех его покупках партнеру начисляется вознаграждение. 
	</p>
	<p>Он привязывается на определенный срок. Если в течение этого срока покупок не было, то автоматически отвязывается, и партнер ничего не получит. Как изменить этот срок — это делается в CRM. Остальные операции вынесены на интерфейс. 
	</p>
	<p>Необходимо зайти в CRM Настройки, Профиль, Настройки вознаграждений. Здесь можно указать срок закрепления приглашенных за партнером. Приветственные баллы: вы можете начислить партнеру приветственные баллы при регистрации. Это деньги, которые он увидит на своем счете. 
	</p>
	<p>Если необходимо передать клиента новому партнеру, можно установить соответствующую галочку. Если в будущем клиент зайдет по ссылке другого партнера, то он передастся новому партнеру. Если галочка не будет стоять, то останется за старым. Нажмите "Записать", если вы что-то изменили.
	</p>
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
			<p>1. <a href='#howto_hold' class="image-link" data-image="https://for16.ru/scripts/insales/crm.png" onclick="openImage(this)">Зайти</a> в CRM</p>
			<p>2. Меню - Настройки-Профиль-Настройка доп условий вознаграждений</p>
			<p>3. Раскрыть и установить срок закрепления приглашенных за партнером</p>
			<p>4. По желанию можно указать приветственные баллы, которые автоматически начисляются партнеру при регистрации</p>
			<p>5. А также поставить или убрать галочку - передавать ли клиента новому партнеру, если вновь зашел в магазин, но по другой партнерской ссылке.</p>
		</div>

		<a href='#' class='' data-target='#howto_vip' data-toggle="collapse" >
			<i class='fa fa-info-circle' ></i> как задать отдельные вознаграждения для продукта
		</a>
		<div class='collapse card p-2 my-3' id='howto_vip'>
			<p>1. <a href='#howto_hold' class="image-link" data-image="https://for16.ru/scripts/insales/crm.png" onclick="openImage(this)">Зайти</a> в CRM</p>
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
			<p>1. <a href='#howto_hold' class="image-link" data-image="https://for16.ru/scripts/insales/crm.png" onclick="openImage(this)">Зайти</a> в CRM</p>
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

	<h3>3. Укажите сайт магазина</h3>
	<p>Ссылка на сайт интернет-магазина. Первоначально здесь указана ссылка  на ваш магазин по умолчанию, взятая из аккаунта inSales. Если привязан свой домен, то, чтобы ссылка выглядела красиво, необходимо изменить её. Просто поменяйте ссылку на центральный вход в интернет-магазин и нажмите "Сохранить". 
	</p>
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
					<input type="url" class="form-control w-100" id="website" value="<?=$website?>" name="website" placeholder="Сайт">
				</div>
				<button type="submit" class="btn btn-primary mb-2 ml-2  align-self-end" name="ch_website" value="yes">
					<i class='fa fa-save'></i>
				</button>
			</form>
			<p class='small' >полный адрес, начиная с https://</p>
		</div>
	</div>

	<h3>4. Посмотрите партнерский лэндинг и, при необходимости, отредактируйте</h3>
	<p>Ссылка на лендинг для регистрации партнеров значит следующее: у вас есть лендинг, созданный ВИНВИНЛЭНД автоматически при создании аккаунта, на котором могут регистрироваться партнеры. 
	</p>
	<p>Вот так выглядит этот лендинг: здесь загружена картинка, краткая надпись, кнопка регистрации. Вводится имя, телефон — партнер зарегистрируется в системе, вы его увидите в CRM ВИНВИНЛЭНД, а сам партнер получит партнерскую ссылку, которую он должен распространять, и доступ в партнерский кабинет, где он будет видеть все свои операции. 
	</p>
	<p>Это обеспечивает доверие со стороны партнера и прозрачность работы всей системы. Вы можете изменить картинку и текст этого лендинга. 
	</p>
	<p>Заходите в CRM, настройки, лендинги. Раскрываете лендинг партнерской программы. 
	</p>
	<p>Здесь можно на форме ввода запрашивать телефон, email, город, изменить данные для формы ввода
	</p>
	<p>Загрузить другое изображение для лендинга, изменить текст лендинга, написать подробные условия партнерской программы и изменить текст кнопки регистрации. 
	</p>
	<p>Далее идет страница благодарности — это страница, куда попадает партнер после регистрации. 
	</p>
	<p>Также необходимо будет настроить телеграм бота, на который подпишется клиент, и который отправит ему эти ссылки. Это просто сделать, не пугайтесь. 
	</p>
	<p>Бот пришлет сообщение партнеру после регистрации: он получит свою партнерскую ссылку. Если вы изменили настройки и указали свой домен, то здесь будет ваш домен и доступ к партнерскому кабинету. Нажмите "Сохранить".
	</p>
	<p>Также вы можете привязать свой домен и получить красивый адрес лендинга. Для этого обратитесь в техподдержку. 
	</p>	
	<div class='card my-3 p-2' >
		<div>
		<b>Ссылка на лэндинг для регистрации партнеров:</b>
		<span id='land_url'><a href='<?=$land_url?>' target='_blank'><?=$land_url_name?></a></span>
		<a href='javascript:copySpanContent(\"land_url\");' class='text-info' target='' title='скопировать ссылку'>
			<i class='fa fa-copy' ></i> 
		</a>
		</div>
		<a href='#' class='' data-target='#howto_land' data-toggle="collapse" ><i class='fa fa-info-circle' ></i> как изменить картинку и текст лэндинга</a>
		<div class='collapse card p-2 my-3' id='howto_land'>
			<p>1. <a href='#howto_land' class="image-link" data-image="https://for16.ru/scripts/insales/crm.png" onclick="openImage(this)">Зайти</a> в CRM</p>
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

	<h3>5. Загрузите логотип</h3>
	<p>Логотип, который видят партнеры в личном кабинете. Партнеры после регистрации получают доступ в личный кабинет, где они видят ваш логотип. По умолчанию это такая картинка, но загрузить свой логотип можно через CRM. Как это делается:
	</p>
	<p>Зайдите в CRM, настройки, профиль, название и реквизиты. Здесь заполните название компании и нажмите "Записать". 
	</p>
	<p>Затем загрузите логотип. Рекомендуемый размер 250 на 50 пикселей, тогда он красиво будет выглядеть в партнерском кабинете. 
	</p>	
	<div class='card my-3 p-2' >
		<p><b>Логотип</b>, который видят партнеры в личном кабинете:</p>
		<img src='<?="https://for16.ru/d/$dir/tg_files/logo.jpg"?>' alt='logo' style='width:200px;' >
		<a href='#' class='' data-target='#howto_logo' data-toggle="collapse" ><i class='fa fa-info-circle' ></i> как загрузить свой логотип</a>
		<div class='collapse card p-2 my-3' id='howto_logo'>
			<p>1. <a href='#howto_logo' class="image-link" data-image="https://for16.ru/scripts/insales/crm.png" onclick="openImage(this)">Зайти</a> в CRM</p>
			<p>2. Меню - Настройки-Профиль-Название и реквизиты</p>
			<p>3. Подготовьте файл с логотипом (оптимальный размер 200х50 px) и выгрузите его</p>
			<p>4. Также укажите название компании и реквизиты</p>
			
		</div>
	</div>

	<h3>6. Укажите ссылки на юридические документы</h3>
	<p>Документы. Ссылки на них должны быть, когда партнер регистрируется на партнерском лендинге. Политика об обработке персональных данных, пользовательское соглашение, согласие на получение информационных материалов — это ссылки, по которым находятся эти документы. 
	</p>
	<p>Как их настроить? Зайти в CRM, в меню настроить. Вот мы заходим в CRM, 
	настройки-профиль-документы, и здесь указываем ссылки на все документы, включая договор об участии в партнерской программе. 
	Ваши партнеры регистрируются, и поэтому с ними нужно какое-то соглашение, регламентирующее участие в партнерской программе, на основании которого вы будете им делать выплаты. 
	</p>
	<p>Также возможно указать ссылку на материалы для партнеров. Это, например, ссылка на Google Диск, где находятся различные ваши изображения, логотипы и так далее. Партнеры должны иметь возможность использовать эти материалы, чтобы рекомендовать вас.
	</p>	
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
			<p>1. <a href='#howto_docs' class="image-link" data-image="https://for16.ru/scripts/insales/crm.png" onclick="openImage(this)">Зайти</a> в CRM</p>
			<p>2. Меню - <a href='#docs' class="image-link" data-image="https://for16.ru/scripts/insales/demo6.png" onclick="openImage(this)">Настройки-Профиль-Ссылки на документы и настройка пикселей</a></p>
		</div>
	</div>

	<h3>7. Создайте два чатбота телеграм</h3>
	<p>Создание чатботов Telegram. Для чего нужны чатботы? Есть два чатбота. 
	Во-первых — это чатбот для переписки с партнерами. Партнер подписывается на этот чатбот при регистрации на партнерском лендинге и получает туда все данные, такие как ссылку для рекомендаций и ссылку для своего доступа к партнерскому кабинету. 
	</p>
	<p>Также этот чатбот служит для переписки. Партнер в этот бот может задать вам вопрос, который вы увидите в CRM-системе, и на который придет вам уведомление, чтобы вы ответили на него. Таким образом осуществляется коммуникация с партнерами.
	</p>
	<p>Также с помощью этого бота можно делать рассылки, работать с партнерами, с партнерской базой, что крайне важно, потому что с партнерами надо постоянно быть в контакте, чтобы они активно работали, становились вашими амбасадорами  и вас рекомендовали еще больше. 
	</p>
	<p>Здесь указано, как его создать этот бот. Это достаточно просто сделать. 
	</p>
	<p>Еще важно, чтобы ботов создавал владелец административного аккаунта, поскольку бот создается в чьем-то аккаунте Telegram. И нужно, чтобы это был ваш аккаунт, чтобы у вас потом наемный сотрудник, например, не уволился и не забрал этих ботов с собой. То есть он должен сделать это в вашем аккаунте каком-то. Это важно.
	</p>
	<p>Второй бот — служебный бот. Он делается для того, чтобы получать различные уведомления. Это удобно, в него приходят уведомления о сделках, по партнерским ссылкам, о сообщениях от партнеров и другая полезная важная информация. К сожалению, чатботы Telegram автоматически создать не получится, это придется сделать вручную, но здесь есть подробные инструкции, как их создать.
	</p>	
	<div class='card my-3 p-2' >
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

	<h2>8. New! Мощнейший инструмент клиентской партнерки. Бусткоды или карты лояльности 2.0</h2>
	<div class='card my-3 p-2 bg-secondary text-white' >
		Выдавайте каждому клиенту карту лояльности 2.0, которая имеет двойное действие - дает не только скидку,
		но начисляет кэшбэк владельцу. Эффект снежного кома без вашего постоянного участия. Клиенты лишь делятся бусткодом.
		<p>См. подробнее <a href='https://winwinland.ru/pdf/winwinland_ecom_boostcodes_offer.pdf' class='text-white' target='_blank'>здесь</a>.</p>
		
	</div>


	<h2 class='text-center' >Как протестировать</h2>

	<p class='card p-3 my-3 bg-light font-weight-bold' >Закажите настройку, не тратьте свое время и получите образцовую партнерскую программу для своего магазина, которая действительно работает!</p>
	
	<p id='1'>1. Зарегистрируйтесь на партнерском лэндинге <a href='<?=$land_url?>' target='_blank'><?=$land_url_name?></a>,
	там подключите телеграм бота и получите в тг партнерскую ссылку и доступ в личный кабинет.
	Итак вы стали партнером.
	</p>
	<p id='2'>2. Перейдите по партнерской ссылке в ваш магазин и сделайте заказ.
	Укажите телефон и емэйл нового покупателя
	(отличные от тех, с которыми вы регистрировались на партнерском лэндинге в п.1).
	</p>
	<p id='3'>3. Измените в админ панели inSales статус этого заказа на <b><?=$insales_status?></b>.
	В этот момент заказ считается выполненным и партнеру будет начислено вознаграждение.
	</p>
	<p id='4'>4. <a href='#4' class="image-link" data-image="https://for16.ru/scripts/insales/crm.png" onclick="openImage(this)">Зайдите в WinWinLand CRM</a> и убедитесь,
	что появился <a href='#4' class="image-link" data-image="https://for16.ru/scripts/insales/demo3.png" onclick="openImage(this)">партнер (п.1) и клиент, купивший продукт</a> (п.2).
	Зайдите в клиента (кликнуть по имени) и убедитесь, что он
	<a href='#4' class="image-link" data-image="https://for16.ru/scripts/insales/demo1.png" onclick="openImage(this)" class='' target=''>закреплен за партнером и проведена оплата</a> продукта,
	который он купил по п.2.
	</p>
	<p id='5'>5. Зайдите в <a href='#5' class="image-link" data-image="https://for16.ru/scripts/insales/demo2.png" onclick="openImage(this)">отчет по партнерским начислениям</a> и обновите его, нажав на кнопку <b>Обновить начисления</b>.
	</p>
	<p id='6'>6. Вы <a href='#6'  class="image-link" data-image="https://for16.ru/scripts/insales/demo4.png" onclick="openImage(this)">видите в отчете</a> партнера и сумму начислений, можно посмотреть детализацию, кликнув на сумме.
	</p>
	<p id='7'>7. Откройте <a href='#7'  class="image-link" data-image="https://for16.ru/scripts/insales/demo5.png" onclick="openImage(this)">отчет по продажам</a>, нажмите вкладку По партнерам и откроется сводка продаж по партнерам.
	</p>

	<div id='rates'>
		<h2 class='text-center' >Стоимость</h2>
		<a href='#' class='' data-target='#prices' data-toggle="collapse" >
			<i class='fa fa-info-circle' ></i> тарифы
		</a>
        <div class="collapse card-body p-3 my-3 show" id="prices">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Абонентская плата, 12 месяцев:
                    <span><b><?=$price_abon?> р.</b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Услуга по настройке под ключ:
                    <span><b><?=$price_nastr?> р.</b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Услуга по внедрению партнерской программы в ваш бизнес + трекинг, 3 мес:
                    <span><b><?=$price_tracking?> р.</b></span>
                </li>
            </ul>
            <div class="mt-3">
                <p>Если у вас есть вопросы по тарифам, задайте вопрос из формы ниже.
                </p>
            </div>
        </div>
		
	</div>

	<div id='q'>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = htmlspecialchars(mb_substr(trim($_POST["name"]),0,32));
        $phone = htmlspecialchars(mb_substr(trim($_POST["phone"]),0,16));
        $email = htmlspecialchars(mb_substr(trim($_POST["email"]),0,64));
        $message = htmlspecialchars(mb_substr(trim($_POST["message"]),0,1024));

        $errors = [];

        if (empty($name)) {
            $errors[] = "Имя обязательно.";
        }

        if (empty($phone)) {
            $errors[] = "Телефон обязателен.";
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Введите корректный емэйл.";
        }

        if (empty($message)) {
            $errors[] = "Ваш вопрос обязателен.";
        }

        if (empty($errors)) {
            $to = "info@winwinland.ru";
            $subject = "inSales doc.php | Новая заявка от $name";
            $body = nl2br("Имя: $name\nТелефон: $phone\nЕмэйл: $email\n\nВаш вопрос:\n$message");
			$db->email(["info@winwinland.ru"],$subject,$body);
			print "<p class='alert alert-success' >Сообщение отправлено!</p>";
        }
    }
    ?>
	<div class='' id='support'>
		<p >Если необходима доработка партнерской программы под ваши условия или помощь в запуске,
		пожалуйста, напишите нам!
		</p>
    <h2 class="mt-5 text-center">Задать вопрос</h2>
    <form action="#q" method="post" class="mt-4" onsubmit="return validateForm();">
        <div class="form-group">
            <label for="name">Имя</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="phone">Телефон</label>
            <input type="tel" class="form-control" id="phone" name="phone" required>
        </div>
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="message">Ваш вопрос</label>
            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Отправить</button>
    </form>
   	</div>


        <script>
        function validateForm() {
            let name = document.getElementById("name").value.trim();
            let phone = document.getElementById("phone").value.trim();
            let email = document.getElementById("email").value.trim();
            let message = document.getElementById("message").value.trim();
            let errors = [];

            if (name === "") {
                errors.push("Имя обязательно.");
            }
            if (phone === "") {
                errors.push("Телефон обязателен.");
            }
            if (email === "" || !validateEmail(email)) {
                errors.push("Введите корректный емэйл.");
            }
            if (message === "") {
                errors.push("Ваш вопрос обязателен.");
            }

            if (errors.length > 0) {
                let errorMessages = errors.join("\n");
                alert(errorMessages);
                return false; // Остановить отправку формы
            }
            return true; // Разрешить отправку формы
        }

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
    </script>
    
	</div>

	
	<br><br><br>

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
<?
//print "webhook_id = ".insales_get_webhook($insales_id);
include "land_bottom.inc.php";
?>
