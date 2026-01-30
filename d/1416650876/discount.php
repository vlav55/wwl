<?
include "/var/www/vlav/data/www/wwl/inc/discount.1.inc.php";
exit;

include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "/var/www/vlav/data/www/wwl/inc/justclick_api.class.php";
include "init.inc.php";
include "/var/www/vlav/data/www/wwl/prices.inc.php";

$db=new top($database,"640px;",false);
print "<p class='mb-4' ><a href='javascript:window.close();' class='btn btn-warning' target=''>Закрыть</a></p>";
$uid=intval($_GET['uid']);
$uid_md5=$db->uid_md5($uid);
$email=$db->dlookup("email","cards","uid='$uid'");
if(isset($_GET['pid'])) {
	if(!intval($_GET['pid'])) {
		print "<div class='alert alert-danger' >Не выбран товар!</div>";
		print "<a href='?uid=$uid' class='' target=''> назад </a>";
		exit;
	}
}
if(isset($_GET['ch_access'])) {
	$tm2=$db->date2tm($_GET['dt2']);
	$db->course_access_set($uid,$source_id=intval($_GET['source_id']),$tm1=time(),$tm2, $force=true );
	print "<div class='alert alert-success' >Доступ продлен до: ".date("d.m.Y",$tm2)."</div>";
	
}
if(isset($_GET['promocode_set'])) {
	$tm1=$db->dt1(time());
	$tm2=$db->dt2($db->date2tm($_GET['dt2']));
	$product_id=intval($_GET['pid']);
	$product=$base_prices[$product_id]['descr'];
	$price=$base_prices[$product_id][1];
	$promo1=intval($_GET['promo1']);
	$promo2=intval($_GET['promo2']);
	$promo3=intval($_GET['promo3']);
	$promocode_id=isset($_GET['promocode_id'])?intval($_GET['promocode_id']):false;
	$uid_promo=$uid;
	if(isset($_GET['promo_global_chk'])) {
		$uid_promo=0;
	}
	if(!isset($_GET['promocode']))
		$promocode=rand(1000,9999);
	else
		$promocode=mb_substr($_GET['promocode'],0,12);
	if($promo1>0 && $promo1<100) {
		if(!$promocode_id) {
			$db->promocode_add($promocode,$uid_promo,$tm1,$tm2,$product_id,$discount=$promo1,$price=0);
		} else {
			$db->query("UPDATE promocodes SET
				promocode='".$db->escape($promocode)."',
				tm2='$tm2',
				uid='$uid',
				discount='$promo1'
				WHERE id='$promocode_id'
				");
		}
		print "<div class='alert alert-success' >Успешно. Промокод <span class='badge' >$promocode</span> на скидку <span class='badge' >$promo1%</span> с {$_GET['dt1']} до {$_GET['dt2']} для <b>$product</b> сгенерирован</div>";
	} elseif($promo2<$price && $promo2>0) {
		if(!$promocode_id) {
			$db->promocode_add($promocode,$uid_promo,$tm1,$tm2,$product_id,$discount=$promo2,$price=0);
		} else {
			$db->query("UPDATE promocodes SET
				promocode='".$db->escape($promocode)."',
				tm2='$tm2',
				uid='$uid',
				discount='$promo2'
				WHERE id='$promocode_id'
				");
		}
		print "<div class='alert alert-success' >Успешно. Промокод <span class='badge' >$promocode</span> на скидку <span class='badge' >$promo2 руб</span> с {$_GET['dt1']} до {$_GET['dt2']} для <b>$product</b> сгенерирован</div>";
	} elseif($promo3>0) {
		if(!$promocode_id) {
			$db->promocode_add($promocode,$uid_promo,$tm1,$tm2,$product_id,$discount=0,$price=$promo3);
		} else {
			$db->query("UPDATE promocodes SET
				promocode='".$db->escape($promocode)."',
				tm2='$tm2',
				uid='$uid',
				price='$promo3'
				WHERE id='$promocode_id'
				");
		}
		print "<div class='alert alert-success' >Успешно. Промокод <span class='badge font18' >$promocode</span> на СПЕЦЦЕНУ <span class='badge font18' >$promo3 руб</span> до {$_GET['dt2']} для <b>$product</b> сгенерирован</div>";
	} else
		print "<div class='alert alert-warning' >Ошибка</div>";
}
if(isset($_GET['promocode_clr'])) {
	$db->query("DELETE FROM promocodes WHERE id=".intval($_GET['promocode_id']));
	print "<div class='alert alert-info' >Промокод удален</div>";
	print "<script>opener.location.reload();</script>";
}
if(isset($_GET['discount_clr'])) {
	$db->yoga_clr_discount($uid,intval($_GET['pid']));
	print "<div class='alert alert-info' >Спеццена удалена</div>";
	print "<script>opener.location.reload();</script>";
}
if(isset($_GET['discount_set'])) {
	$tm1=time(); //$db->dt1($db->date2tm($_GET['dt1']));
	$tm2=$db->dt2($db->date2tm($_GET['dt2']));
	//price2_set($uid,$price_id,$tm1,$tm2,$product_id)
	if($db->price2_set($uid,$price_id=2,$tm1,$tm2,$product_id=intval($_GET['pid']))) {
		$dt1=date("d.m.Y H:i",$tm1);
		$dt2=date("d.m.Y H:i",$tm2);
		$descr=$base_prices[$product_id]['descr'];
		print "<div class='alert alert-success' >Спеццена установлена для <span class='badge' >$descr</span> до <span class='badge' >$dt2</span></div>";
		print "<script>opener.location.reload();</script>";
	} else
		print "<div class='alert alert-danger' >Ошибка</div>";
}
$link_s1=""; $s1=0;
if(isset($_GET['get_link_s1'])) {
	$pid=intval($_GET['pid']);
	$s1=intval($_GET['s1']);
	if($s1)
		$link_s1="https://yogahelpyou.com/best2pay/order.php?s=0&product_id=$pid&uid=$uid_md5&s1=$s1";
	else
		$link_s1="error";
}
if(isset($_GET['do_gift'])) {
	$pid=intval($_GET['pid']);
	if(intval($_GET['chk_gift'])) {
		$jc=new justclick_api;
		$jc_gid=$base_prices[$pid]['jc'];
		if($jc->add_to_group($jc_gid,$email)) {
			print "<div class='alert alert-warning' >Предоставлен доступ к продукту: {$base_prices[$pid]['descr']}. Емэйл с инструкциями отправлен клиенту.</div>";
			$db->notify($uid,"Подарок - предоставлен доступ к продукту: {$base_prices[$pid]['descr']}");
			$db->yoga_email("Подарок - предоставлен доступ к продукту: {$base_prices[$pid]['descr']}","");
		} else
			print "<div class='alert alert-warning' >Ошибка JC предоставления доступа</div>";
	}
}

$name=$db->dlookup("name","cards","uid='$uid'");
$l_name=$db->dlookup("surname","cards","uid='$uid'");
print "<h2>Установки для: $name $l_name ($uid)</h2>";
print "<div class='card bg-light p-3' >
	<form>
	<div>
	По дату: <input  class='text-center' type='text' name='dt2' value='' id='datepicker1'>
	<span class='small' >(до 23:59 выбранной даты)</span>
	</div>
	<div><select name='pid' class='form-control'>
	<option value='0'>= не выбран товар =</option>
	";
//print_r($base_prices);
foreach($base_prices AS $product_id=>$r) {
	print "<option value='$product_id'>{$r['descr']}</option>";
}
print "</select>
	</div>

	<!--
	<div class='alert alert-danger' >
		<input  class='text-center' type='checkbox' name='chk_gift' value='1'> Сделать подарок (выберите товар)
		<button type='submit' name='do_gift' class='btn btn-info btn-sm' value='yes'>Ok</button>
	</div>
	
	<div class='alert alert-info' >
		<h4>Сформировать ссылку на платеж по рассрочке</h4>
		Сумма: <input  class='text-center' type='text' name='s1' value='$s1'>
		<button type='submit' name='get_link_s1' class='btn btn-info btn-sm' value='yes'>Получить ссылку</button>
		<div class='text-center' ><b>$link_s1</b></div>
	</div>
	-->
	
	<button type='submit' name='discount_set' class='btn btn-danger' value='yes'>Установить спеццену</button>
	<input type='hidden' name='uid' value='$uid'>

	<div class='mt-4' >
		<h4>Или сгенерировать промокод на</h4>
		<div>скидку в % : <input  class='text-center' type='text' name='promo1' value=''></div>
		<div>скидку в рублях : <input  class='text-center' type='text' name='promo2' value=''></div>
		<div>спеццену :  <input  class='text-center' type='text' name='promo3' value=''></div>
		<input type='checkbox' name='promo_global_chk' value='1'> для всех клиентов
		<button type='submit' name='promocode_set' class='btn btn-primary' value='yes'>Сгенерировать промокод</button>
	</div>

	</form>
	</div>
	";

//$jc=new justclick_api;
//$res=$jc->get_all_subscriptions($email);
//$db->print_r($res);

print "<div class='card bg-light p-3 my-3' >";
print "<h4 class='pt-3'>Установлены спеццены:</h4>";
foreach($base_prices AS $product_id=>$r) {
	//$price=$db->yoga_check_price($uid,$product_id);
	$tm=time();
	$price_id=$db->dlookup("price_id","discount","uid='$uid' AND product_id='$product_id' AND (dt2>$tm)",0);
	$price=$base_prices[$product_id][$price_id];
	if($price_id==2) {
		$dt1=date("d.m.Y",$db->dlookup("dt1","discount","uid='$uid' AND product_id='$product_id'"));
		$dt2=date("d.m.Y H:i",$db->dlookup("dt2","discount","uid='$uid' AND product_id='$product_id'"));
		print "<div class='alert alert-info' >{$r['descr']} <span class='badge font18'>$price р.</span>
			<s>{$r['1']}р.</s> по <span class='badge font18' >$dt2</span>
			<a href='?discount_clr=yes&uid=$uid&pid=$product_id' class='' target=''>
			<button class='btn btn-sm btn-info' >снять</button>
			</a>
			</div>";
	}
//		print "HERE_".$r['source_id'];
	if($tm1=$db->course_access_granted($uid,$r['source_id'])) {
		$tm1=$db->dlookup("tm1","course_access","uid='$uid' AND source_id='{$r['source_id']}'");
		if($tm1) {
			$dt1=date("d.m.Y",$tm1) ;
			$dt2=date("d.m.Y",$db->dlookup("tm2","course_access","uid='$uid' AND source_id='{$r['source_id']}'") );
			print "<div class='alert alert-warning' >Есть доступ к: {$r['descr']} <span class='badge font18' >до $dt2</span>";
			print "<form>
			<input type='text' name='dt2' value='$dt2' id='dp3' style='width:80px;' class='text-center' >
			<input type='hidden' name='source_id' value='{$r['source_id']}'>
			<input type='hidden' name='uid' value='$uid'>
			<button class='btn btn-warning btn-sm' type='submit' name='ch_access' value='yes'>продлить</button>
			</form>";
			print "</div>";
		}
	}
}
print "</div>";

print "<div class='card bg-light p-3 my-3' >";
print "<h4 class='pt-3' >Доступные промокоды</h4>";
$res=$db->query("SELECT * FROM promocodes WHERE uid='$uid' OR uid=0");
while($r=$db->fetch_assoc($res)) {
	$promocode=$r['promocode'];
	$dt2=date("d.m.Y",$r['tm2']);
	$descr=$base_prices[$r['product_id']]['descr'];
	$price=$r['price'];
	$discount=$r['discount'];
	$private=""; //$r['uid']?"<badge class='_' >индивидуальный</badge>":"<badge>общий</badge>";
	print "<div class='alert alert-warning' >
		<form class='form-inline' >
		<span class='badge bg-warning font18 mx-1' >
		<input type='text' class='form-control text-center' name='promocode' value='$promocode' style='width:100px;'>
		</span>
		<b>$descr</b>
		$private";
	if($price) 
		print "&nbsp на цену <span class='badge font18'>
			<input type='text' class='form-control text-center' name='promo3' value='$price' style='width:100px;'>
			</span>р.
			";
	if(!$price && $discount && $discount<100) 
		print "&nbsp на скидку <span class='badge font18'>
			<input type='text' class='form-control text-center' name='promo1' value='$discount' style='width:100px;'>
			</span>%.
			";
	if(!$price && $discount && $discount>=100) 
		print "&nbsp на скидку <span class='badge font18'>
			<input type='text' class='form-control text-center' name='promo2' value='$discount' style='width:100px;'>
			</span>руб.
			";
	print "до
		<span class='badge font18' >
		<input type='text' class='form-control text-center' name='dt2' value='$dt2' style='width:150px;' >
		</span>
		<input type='hidden' name='promocode_id' value='{$r['id']}'>
		<input type='hidden' name='pid' value='{$r['product_id']}'>
		<input type='hidden' name='uid' value='{$r['uid']}'>
		<button type='submit' name='promocode_set' value='yes' class='btn btn-sm btn-info m-1' >
			<i class='fa fa-save' ></i>
		</button>
		<a href='?promocode_clr=yes&uid=$uid&promocode_id={$r['id']}' class='btn btn-sm btn-danger' target=''>
			<i class='fa fa-trash m-1' ></i>
		</a>
		</form>
		</div>";
	
}
print "</div>";

?>
<script>
	$('#dp3').datepicker({
		weekStart: 1,
		daysOfWeekHighlighted: "6,0",
		autoclose: true,
		todayHighlight: true,
		format: 'dd.mm.yyyy',
		language: 'ru',
	});
</script>
<?
$db->bottom();
?>
