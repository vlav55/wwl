<?
$title="ОПЛАТА ДОСТУПА К WINWINLAND";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "init.inc.php";
$client_ctrl_id=$ctrl_id;
$client_ctrl_dir=$ctrl_dir;
$client_db=$database;
$client_uid_admin=$uid_admin;



$db=new db('vkt');
$res=$db->query("SELECT * FROM product WHERE 1");
$base_prices=array();
while($r=$db->fetch_assoc($res)) { //DO NOT DELETE!
	$base_prices[$r['id']]=[
		0=>$r['price0'],
		1=>$r['price1'],
		2=>$r['price2'],
		'descr'=>$r['descr'],
		'term'=>$r['term'],
		'stock'=>$r['stock'],
		'jc'=>$r['jc'],
		'sp'=>0,
		'sp_template'=>$r['sp_template'],
		'source_id'=>$r['source_id'],
		'razdel'=>$r['razdel'],
		'use'=>$r['in_use'],
		'vid'=>$r['vid'],
		'installment'=>$r['installment'],
	];
}

if(!$client_uid_admin) {
	print "<p class='alert alert warning' >Ошибка доступа. Сообщите в поддержку </p>";
	exit;
}

//$db->print_r($base_prices);
$uid=$client_uid_admin;
//$name=$db->dlookup("name","cards","uid='$uid'")." ".$db->dlookup("surname","cards","uid='$uid'");
//$mob=$db->dlookup("mob","cards","uid='$uid'");
//$email=$db->dlookup("email","cards","uid='$uid'");
//print "uid=$uid";
$tm_pay_end=$db->avangard_tm_end($uid,[30,31,32]);
$dt_pay_end=date('d.m.Y',$tm_pay_end);

$mode=true;
if(isset($_GET['uid'])) {
	if(!$_GET['uid']) {
		$uid_md5=0;
		$mode=false;
	}
}

$base=1;

include "land_top.inc.php";

?>
<div class='container' >
	<div class='text-center mt-4' ><img src='https://winwinland.ru/img/logo/logo-200-w.png' alt='' class='image ' ></div>
<!--
<p class='text-right' ><a href='javascript:window.close()' class='btn btn-warning' target=''>Закрыть</a></p>
-->
<?if($mode) { ?>
<h2 class='text-center mt-5' >Продление доступа</h2>
<p class='text-center'>Доступ заканчивается: <b><?=$dt_pay_end?></b></p>
<br><br>

<!--
<div class='card_ bg-light_ p-3' >
	<h3>Администратор:</h3> 
	<div class='' >
	<b><?=$name?></b><br>
	<b><?=$mob?></b><br>
	<b><?=$email?></b><br>
	</div>
</div>
-->

<br><br>
<?} else {?>
	<h2 class='text-center mt-5' >Оплата доступа WINWINLAND</h2>
<? } ?>
<p>После выбора пакета можно будет оплатить с карты, либо скачать счет на юрлицо.</p>
<div class='row' >
	<div class='col-sm-4 my-2' >
		<div class='card bg-light text-center p-3 h-100 ' >
			<?
				$pid=32;
				$descr=$base_prices[$pid]['descr'];	
				$price=$base_prices[$pid][$base];	
				$term=$base_prices[$pid]['term'];
				$price_1month=$price/intval($term/30);
				print "<p>$descr</p>";
				print "<h3 class='bg-info text-white rounded py-2' >$price_1month&nbsp;р/мес</h3>";
				print "<p class='small' >* при оплате за $term дней (<b>$price&nbsp;р.</b>) </p>";
				print "<p class='mt-4' ><a href='https://winwinland.ru/order.php?s=$price&t=0&product_id=$pid&ctrl_id=$client_ctrl_id' class='btn btn-danger' target=''>Оплатить</a></p>";
			?>
		</div>
	</div>
	<div class='col-sm-4 my-2' >
		<div class='card bg-light text-center p-3 h-100 ' >
			<?
				$pid=35;
				$descr=$base_prices[$pid]['descr'];	
				$price=$base_prices[$pid][$base];
				$term=$base_prices[$pid]['term'];
				$price_1month=$price/intval($term/30);
				print "<p>$descr</p>";
				print "<h3 class='bg-info text-white rounded py-2' >$price_1month&nbsp;р/мес</h3>";
				print "<p class='small' >* при оплате за $term дней (<b>$price&nbsp;р.</b>) </p>";
				print "<p class='mt-4'><a href='https://winwinland.ru/order.php?s=$price&t=0&product_id=$pid&ctrl_id=$client_ctrl_id' class='btn btn-danger' target=''>Оплатить</a></p>";
			?>
		</div>
	</div>
	<div class='col-sm-4 my-2' >
		<div class='card bg-light text-center p-3 h-100 ' >
			<?
				$pid=31;
				$descr=$base_prices[$pid]['descr'];	
				$price=$base_prices[$pid][$base];	
				$term=$base_prices[$pid]['term'];
				$price_1month=$price/intval($term/30);
				print "<p>$descr</p>";
				print "<h3 class='bg-info text-white rounded py-2' >$price_1month&nbsp;р/мес</h3>";
				print "<p class='small' >* при оплате за $term дней (<b>$price&nbsp;р.</b>) </p>";
				print "<p class='mt-4'><a href='https://winwinland.ru/order.php?s=$price&t=0&product_id=$pid&ctrl_id=$client_ctrl_id' class='btn btn-danger' target=''>Оплатить</a></p>";
			?>
		</div>
	</div>
	<div class='col-sm-4 my-2' >
		<div class='card bg-light text-center p-3 h-100 ' >
			<?
				$pid=30;
				$descr=$base_prices[$pid]['descr'];	
				$price=$base_prices[$pid][$base];	
				$term=$base_prices[$pid]['term'];
				$price_1month=$price/intval($term/30);
				print "<p>$descr</p>";
				print "<h3 class='bg-info text-white rounded py-2' >$price_1month&nbsp;р/мес</h3>";
				print "<p class='small' >* при оплате за $term дней (<b>$price&nbsp;р.</b>) </p>";
				print "<p class='mt-4'><a href='https://winwinland.ru/order.php?s=$price&t=0&product_id=$pid&ctrl_id=$client_ctrl_id' class='btn btn-danger' target=''>Оплатить</a></p>";
			?>
		</div>
	</div>
</div>
</div>

			<div class='col-sm-12 text-center text-secondary' >
				<br>
				<hr class='my-5' >
				<div class='s_bottom_card_ip' >&copy; <?=date("Y")?> ООО ВИНВИНЛЭНД, ИНН 7811786250
					РФ, г.Санкт-Петербург
				</div>
				<div class='s_bottom_card_href'>
				<a href='/privacypolicy.pdf' target='_blank'>Политика конфиденциальности</a> |
				<a href='/dogovor.pdf' target='_blank'>Пользовательское соглашение</a> |
				<a href='/agreement.pdf' target='_blank'>Согласие на обработку персональных данных</a>
				</div>
			</div>


<?


//$db->bottom();
include "land_bottom.inc.php";
?>
