<?
if(preg_match("/Telegram/i",$_SERVER['HTTP_USER_AGENT'])) {
	exit;
}
$pwd_id=1002;
include "../top_code.inc.php";
if(!$uid) {
	header("Location: https://winwinland.ru/consult/?bc=$bc", true, 301);
	exit;
}

$name=($uid)?$db->dlookup("name","cards","uid='$uid'").", ":"";
$db->tag_add($uid,26);

if($uid!=-1002)
	$db->notify($uid,"Воронка STEP_1");

//~ if($uid) {
	//~ include_once "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
	//~ $db=new vkt_send('vkt');
	//~ $land_num=7;
	//~ $res=$db->query("SELECT * FROM vkt_send_1 WHERE sid=12 AND land_num='$land_num'",0);
	//~ while($r=$db->fetch_assoc($res)) {
		//~ $db->vkt_send_task_add($ctrl_id=1, $tm_event=intval(time()+$r['tm_shift']), $vkt_send_id=$r['id'],$vkt_send_type=3,$uid);
	//~ }
//~ }

?>
<?include "top.inc.php";?>
  <main>
    <section class="service" id="service" style='padding-top:0;'>
      <div class="service__top">
        <div class="service__top-wrapper">
          <h1 class="service__h1">
            <span class='service__h1_wwl' >Winwinland —</span> <br />
            <span class='service__h1_small' >сервис для создания <br>партнерских программ</span>
          </h1>
        </div>
      </div>
    </section>
	  <div class="container">
		<div class="possibilities">
			<h2 class="possibilities__title purple my-5 text-center">Благодарим за проявленный интерес!</h2>
<!--
			<h3 class="possibilities__suptitle title">
				<span class='orange' ><?=$name?></span> посмотрите видео, где Юлия простым языком объясняет как именно Винвинлэнд поможет вам увеличить продажи.
			</h3>

			<div class='my-5' >
				<?
					//~ $url="https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/winwinland_promo_0924/master.m3u8";
					//~ $poster="images/p1.jpg";
					//~ include "video.inc.php";
				?>
			</div>

			
			<h3 class="possibilities__suptitle title orange">Оплатить подключение и получить 2 недели в подарок можно <a href='https://winwinland.ru/#rates' class='' target=''>по ссылке</a>
			</h3>
-->

			<div class='' >
				<h2 class="possibilities__title title orange text-center">
					Какие имеются тарифы и что входит в стоимость
				</h2>
				<div class='video_cycle' style="margin:0 auto;">
				<div style="position: relative; padding-top: 100%; width: 100%;">
					<iframe src="https://kinescope.io/embed/n9sg9D7VpQZsvaA9MKDytK" allow="autoplay; fullscreen; picture-in-picture; encrypted-media; gyroscope; accelerometer; clipboard-write;" frameborder="0" allowfullscreen style="position: absolute; width: 100%; height: 100%; top: 0; left: 0;"></iframe>
				</div>
				</div>

				<div class='card_ my-3 mt-5 px-4' >
				<h3 class="possibilities__suptitle title blue text-center mt-4 mb-3">
					1. Абонентская плата
				</h3>
				<table class='table table-striped' >
					<thead>
						<tr>
							<th>Длительность</th>
							<th>Стоимость</th>
							<th>За месяц</th>
							<th>Оплатить</th>
						</tr>
					</thead>
					<tbody>
						<?
						$pids=[30,31,35,32];
						foreach($pids AS $pid) {
							$price=$db->dlookup("price1","product","id='$pid'");
							$term=$db->dlookup("term","product","id='$pid'");
						?>
						<tr>
							<td><?=$term?> дней</td>
							<td><?=$price;?>р</td>
							<td><?=intval($price/($term/30))?>р</td>
							<td><a href='https://winwinland.ru/order.php?s=0&t=0&product_id=<?=$pid?>&uid=<?=$uid?>' class='' target=''><img src='../img/social/goto-24.png' alt=''></a></td>
						</tr>
						<? } ?>
					</tbody>
				</table>
				<h4 class='text-left possibilities__suptitle x_comment mt-3' >* тарифы фиксированы и не зависят от количества партнеров, рекомендаций, лидов в базе, рассылок и прочего. За указанную сумму доступен полный функционал без ограничений.</h4>
				</div>

				<div class='card my-3 px-4' >
				<h3 class="possibilities__suptitle title orange text-center mt-5 pt-4 mb-3">
					2. Подключение + настройка <br><br>
					<?
					$pid=33;
					$price=$db->dlookup("price1","product","id='$pid'");
					$term=$db->dlookup("term","product","id='$pid'");
					?>
					<div class='text-black' style='color:black;'>
					<?=$price?>p
					</div>
				</h3>
				<h4 class='text-center possibilities__suptitle ' >
					<a href='https://winwinland.ru/order.php?s=0&t=0&product_id=<?=$pid?>&uid=<?=$uid?>' class='' target=''>
						оплатить <img src='../img/social/goto-24.png' alt=''>
					</a>
				</h4>
				<h4 class='text-left possibilities__suptitle  x_comment mt-3' >* пакет включает абонентскую плату <?=$term?> дней </h4>
				</div>

				<div class='card my-3 px-4' >
				<h3 class="possibilities__suptitle title purple text-center mt-5 pt-4 mb-3">
					3. Подключение + настройка + трекинг внедрения <br><br>
					<?
					$pid=37;
					$price=$db->dlookup("price1","product","id='$pid'");
					$term=$db->dlookup("term","product","id='$pid'");
					?>
					<div class='text-black' style='color:black;'>
					<?=$price?>p
					</div>
				</h3>
				<h4 class='text-center possibilities__suptitle ' >
					<a href='https://winwinland.ru/order.php?s=0&t=0&product_id=<?=$pid?>&uid=<?=$uid?>' class='' target=''>
						оплатить <img src='../img/social/goto-24.png' alt=''>
					</a>
				</h4>
				<h4 class='text-left possibilities__suptitle x_comment mt-3' >* пакет включает абонентскую плату <?=$term?> дней </h4>
				</div>

			</div>
			<br><br>
			
			<?if($uid) {?>
			<p class="possibilities__suptitle title mt-5">Задать любой вопрос нам можно с помощью нашего <a href='https://t.me/vkt_support_bot?start=<?=$uid_md5?>' class='' target='_blank'>чат бота телеграм</a> 
			</p>
			<? } ?>
			<p class="possibilities__suptitle title ">Подписывайтесь на наши соцсети. Там вы найдете много примеров как Winwinland помогает бизнесам находить клиентов с помощью партнерских программ
			</p>
			<div class='text-center' >
				<a href='https://t.me/winwinland_ru' class='mx-3' target='_blank'><img src='/img/social/Telegram.png' alt='img' width='64'></a>
				<a href='https://vk.com/winwinland_ru' class='mx-3' target='_blank'><img src='/img/social/VK.png' alt='img' width='64'></a>
				<a href='https://tenchat.ru/julietavshtolis' class='mx-3' target='_blank'><img src='/img/social/tenchat.png' alt='img' width='64'></a>
			</div>

<!--
			<table class='table' >
				<thead>
					<tr>
						<th>Продукт</th>
						<th>Период, мес</th>
						<th>Стоимость за&nbsp;1&nbsp;месяц</th>
						<th>К&nbsp;оплате</th>
						<th>Оплатить</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<?
						$pid=30;
						$m=round($base_prices[$pid]['term']/30,0);
						?>
						<td><?=$base_prices[$pid]['descr']?></td>
						<td><?=$m?></td>
						<td><?=round($base_prices[$pid][1]/$m,0)?></td>
						<td><?=$base_prices[$pid][1]?></td>
						<td><a href='https://winwinland.ru/order.php?s=0&t=0&product_id=<?=$pid?>&uid=<?=$uid_md5?>' class='' target=''>Оплатить</a></td>
					</tr>
					<tr>
						<?
						$pid=31;
						$m=round($base_prices[$pid]['term']/30,0);
						?>
						<td><?=$base_prices[$pid]['descr']?></td>
						<td><?=$m?></td>
						<td><?=round($base_prices[$pid][1]/$m,0)?></td>
						<td><?=$base_prices[$pid][1]?></td>
						<td>Оплатить</td>
					</tr>
					<tr>
						<?
						$pid=35;
						$m=round($base_prices[$pid]['term']/30,0);
						?>
						<td><?=$base_prices[$pid]['descr']?></td>
						<td><?=$m?></td>
						<td><?=round($base_prices[$pid][1]/$m,0)?></td>
						<td><?=$base_prices[$pid][1]?></td>
						<td>Оплатить</td>
					</tr>
					<tr>
						<?
						$pid=32;
						$m=round($base_prices[$pid]['term']/30,0);
						?>
						<td><?=$base_prices[$pid]['descr']?></td>
						<td><?=$m?></td>
						<td><?=round($base_prices[$pid][1]/$m,0)?></td>
						<td><?=$base_prices[$pid][1]?></td>
						<td>Оплатить</td>
					</tr>
				</tbody>
			</table>
-->
			<br><br><br><br>
		</div>
		<?
			//print getcwd();
			//~ chdir("/var/www/vlav/data/www/wwl/winwinland/");
			//~ include "section_prices.inc.php";
		?> 
	</div>
  </main>

<? include "bottom.inc.php"; ?>
