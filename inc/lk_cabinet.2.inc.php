<?
$title="Партнерский кабинет";
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/top.class.php";
include_once "../init.inc.php";

$top=new top($database,"640px;",false);
$db=new db($database);
//include "../logo.inc.php";
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
$link_vk_1=$p->get_partner_link($klid,'senler');
$link_vk_2=$p->get_partner_link($klid,'senler',1);
$link_land_1=$p->get_partner_link($klid,'land');
$link_land_2=$p->get_partner_link($klid,'land',1);
$bc=$db->dlookup("bc","users","klid='$klid'");
if(!$bc) {
	$bc=0;
	//~ print "<p class='alert alert-warning' >Партнерский кабинет: вы не зарегистрированы в партнерской программе ({$_SESSION['userid_sess']} $ctrl_id)</p>";
	//~ $db->bottom(); 	exit;
}
?>
<div class='container' style='font-size:18px;' >
	<?
	?>
	
	<h2 class='text-center py-4' >Партнерский кабинет</h2>
	<div class='card bg-light p-3' >
		<p>Имя: <b><?=$r['name']?> <?=$r['surname']?></b></p>
		<p>Телефон: <?=$r['mob']?></p>
		<p>Login: <?=$login?></p>
		<p><?=$real_user_name?></p>
		<p>Партнерский код: <b><?=$bc?></b></p>

		<p>Реквизиты для выплаты вознаграждения <a href='#details' data-toggle='collapse' class='' target=''>развернуть</a></p>
		<div class='collapse pt-2 pb-3' id='details'>
			<form method='POST' action='?uid=<?=$uid_md5?>'>
				<div class="form-group">
				  <label for="comment">Реквизиты:</label>
				  <textarea class="form-control" rows="5" id="comment" name='msg'><?=$bank_details?></textarea>
					<p class='small' >ФИО получателя, номер счета, БИК, название и корсчет Банка.</p>
				</div>
				<button class='btn btn-primary ' name='do_details' value='yes'>Сохранить</button>
			</form>
		</div>


	</div>
	<br>


	<h3 class='py-4 text-center' >Вознаграждение</h3>
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
	$res=$db->query("SELECT * FROM product WHERE (fee_1>0 OR fee_2>0)");
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
<!--
	<div>
		<p>- первый уровень <b><?=$fee?>%</b></p>
		<p>- второй уровень <b><?=$fee2?>%</b></p>
	</div>
-->
	<br>

	<h3 class='py-4 text-center' >Партнерские ссылки <a href='#parner_links' data-toggle='collapse' class='' target=''>(развернуть)</a></h3>

<div class='card p-3 collapse' id='parner_links'>
	<?
	if($link_vk_1)
	print "<div class='card p-2 bg-light' >
			<div>
			<i class='badge bg-info p-2' >ВК</i> Ваша партнерская ссылка для ВК
			<a href='$link_vk_1' target='_blank' title='перейти на лэндинг в новом окне'><span class='fa fa-arrow-circle-right'></span></a>
			<div class='card p-2 bg-light'><span p-2'>
				$link_vk_1
			</span></div>
			</div>
		</div>";
	if($link_vk_2)
	print "<div class='card p-2 bg-light' >
			<div>
			<i class='badge bg-info p-2' >ВК (партнерский лэндинг)</i> Ссылка для приглашения в партнерскую программу для ВК
			<a href='$link_vk_2' target='_blank' title='перейти на лэндинг в новом окне'><span class='fa fa-arrow-circle-right'></span></a>
			<div class='card p-2 bg-light'><span  class='badge p-2'>
				$link_vk_2
			</span></div>
			</div>
		</div>";
	
	//$res=$db->query("SELECT * FROM lands WHERE del=0 AND product_id=0");
	$res=$db->query("SELECT * FROM lands WHERE del=0");
	while($r=$db->fetch_assoc($res)) {
		$link=str_replace("https:/","https://",str_replace("//","/","{$r['land_url']}/?bc=$bc"));
		$label_partner_land=($r['fl_partner_land'])?"(партнерский лэндинг)":"";
		$product_land=($r['product_id'])?"(товарный лэндинг)":"";
		print "<div class='card p-2 bg-light' >
				<div>
				<i class='badge p-2' >Лэндинг #{$r['land_num']} $label_partner_land $product_land</i> {$r['land_name']}
				<a href='$link' target='_blank' title='перейти на лэндинг в новом окне'><span class='fa fa-arrow-circle-right'></span></a>
				<div class='card p-2 bg-light'><span  class='badge p-2'>
					$link
				</span></div>
				</div>
			</div>";
	}

?>

	<p class='small mute' >По этим ссылкам ваши знакомые могут зарегистрироваться и будут закреплены за вами.
	Все просто, прозрачно и понятно — просто разместите эту ссылку на своих
	страницах в соцсетях, передайте ее друзьям и знакомым и расскажите им о нас.
	</p>
	<p class='small mute' >По этой ссылкам с пометкой <b>партнерский лэндинг</b> ваши знакомые смогут не только зарегистрироваться, но и принять участие в данной партнерской программе, как и вы.
	</p>

</div>

<!--
	<div class='mb-5 mt-5' id='mat'>
		<h2>Материалы для соцсетей и рассылок</h2>
		<p>Шаблоны для рассылки email, материалы для размещения постов в соцсетях и другой рекламы</p>

		<p>Если вы не можете найти подходящий формат, нужен текст, есть предложения или пожелания
		по  рекламным материалам, то 
		<a href='#moder2' class='ml-2' data-toggle='collapse'>отправьте нам запрос</a> .
		</p>
		<div class='collapse pt-2 pb-3' id='moder2'>
			<form method='POST' action='?uid=<?=$uid_md5?>'>
				<div class="form-group">
				  <label for="comment">Ваш комментарий:</label>
				  <textarea class="form-control" rows="5" id="comment" name='msg'></textarea>
				</div>
				<button class='btn btn-primary ' name='do_moder' value='yes'>Отправить</button>
			</form>
		</div>

		<div>
			<a href='samples/person_insta.php?uid=<?=$uid_md5?>' class='btn btn-info' target='_blank'>Примеры для соцсетей</a>
			<a href='samples/blogger.php?uid=<?=$uid_md5?>' class='btn btn-info' target='_blank'>Примеры для блогера</a>
			<a href='samples/cpa.php?uid=<?=$uid_md5?>' class='btn btn-info' target='_blank'>Примеры для пратнеров</a>
		</div>

	</div>
-->

	<hr>
	
	<div class='mt-5' >
		<h2 class='p-4 text-center' >Сводка</h2>
		
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
	<h5>Итого: начислено <span class='border border-secondary  p-1 rounded' ><?=$sum_fee_all?></span> - выплачено <span class='border border-secondary p-1 rounded' ><?=$sum_pay_all?></span> - остаток к выплате <span class='border border-secondary  p-1 rounded' ><?=$rest_all?></span></h5>
	<hr>
		
	</div>
<!--
	<div><a href='#report1' class='' data-toggle='collapse' target=''>Отчет по начислениям и выплатам</a></div>
	<div class='collapse' id='report1'>
		<h3>Нет данных</h3>
	</div>
-->

	<div class='_' >
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

		print "<h2><a href='#a1' data-toggle='collapse' class='' target=''>Начисления (+)</a></h2>";
		print "<div id='a1' class='collapse' >";
		if($levels==2)
			print "<div class='alert alert-info' >Установлен учет только второго уровня оплат ($fee2%)</div>";
		print "<table class='table table-striped'>
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
				</thead>
				<tbody>
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
				</tr>";
			$n++;
		}
		print "</tbody></table>";
		print "</div>";

		?>
<!--
		<div class='mt-5' ><a href='#' class='' target=''>Выгрузить в csv</a></div>
-->
	</div>

	<div>
		<?
		print "<h2><a href='#a2' data-toggle='collapse' class='' target=''>Выплаты (+)</a></h2>";
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
<?
$top->bottom();
?>
