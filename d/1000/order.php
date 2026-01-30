<?
include "/var/www/vlav/data/www/wwl/inc/order.1.inc.php";
exit;
?>

<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "init.inc.php";
$_SESSION['csrf_token_order'] = bin2hex(random_bytes(32)); // Unique token

$db=new db($database);

if(isset($_GET['bc'])) {
	$bc=intval($_GET['bc']);
}

$land_num=intval($_GET['land_num']);
$title=($land_num)?$db->dlookup("land_name","lands","land_num='$land_num'"):'Оплата';
$descr=$title;
$og_url="";
$favicon="https://for16.ru/images/favicon.png";


include "land_top.inc.php";

$thanks_pic=(file_exists("tg_files/thanks_pic_$land_num.jpg"))?"<img src='tg_files/thanks_pic_$land_num.jpg' class='img-fluid' >":"";
if(empty($thanks_pic)) {
	$thanks_pic=(file_exists("tg_files/logo200x50.jpg"))?"<img src='tg_files/logo200x50.jpg' class='img-fluid' >":"";
}
$fl_disp_city=($land_num)?$db->dlookup("fl_disp_city","lands","land_num='$land_num'"):0;
$fl_disp_city_rq=($land_num)?$db->dlookup("fl_disp_city_rq","lands","land_num='$land_num'"):0;
$fl_disp_comm=($land_num)?$db->dlookup("fl_disp_comm","lands","land_num='$land_num'"):0;
$label_disp_comm=($land_num)?$db->dlookup("label_disp_comm","lands","land_num='$land_num'"):0;

print "<div class='container my-3 mb-5 text-center' >$thanks_pic</div>";

$product_id=(isset($_GET['product_id']))?intval($_GET['product_id']):false;
if(!$product_id) {
	print "<div class='alert alert-danger' >Ошибка. Не найден продукт.</div>";
	include "../bottom.inc.php";
	exit;
}

$uid=0; $disp_contacts=false;
if(isset($_GET['uid'])) {
	$uid=$db->get_uid($_GET['uid']);
	if($db->is_md5($_GET['uid']))
		$disp_contacts=true;
}
//$db->notify_me($uid);
if($uid)
	$_SESSION['vk_uid']=$uid;


$client_email=(isset($_GET['client_email']))?$_GET['client_email']:"";
$client_name=(isset($_GET['client_name']))?$_GET['client_name']:"";
$client_phone=(isset($_GET['client_phone']))?$_GET['client_phone']:"";

if(isset($_SESSION['vk_uid'])) {
	$vk_uid=intval($_SESSION['vk_uid']);
	//print "HERE_$vk_uid";
	if(empty($client_email)) {
	//print "HERE_".$vk_uid." ".$client_email;
		$r=$db->fetch_assoc($db->query("SELECT * FROM cards WHERE uid='$vk_uid'"));
		if($r) {
			$client_phone=$r['mob']; $client_name=$r['name']; $client_email=$r['email'];
		}
	}
} else 
	$vk_uid=0;

if(!$disp_contacts) {
	$client_email="";
	$client_phone="";
	$client_name="";  
}

$uid=$vk_uid;

//print "1=$uid 2=$vk_uid"; exit;


if(isset($_GET['promocode']))
	unset($_SESSION['s_best2pay']);
if(isset($_GET['s'])) {
	if(intval($_GET['s'])) {
		//~ $sum=intval($_GET['s']);
		//~ $_SESSION['s_best2pay']=$sum;
	} else
		unset($_SESSION['s_best2pay']);
}
if(!isset($_SESSION['s_best2pay'])) {
	$sum=$db->price2_chk($uid,$product_id)?$base_prices[$product_id][2]:$base_prices[$product_id][1];
//	$sum=$db->yoga_check_price($vk_uid,$product_id);

//	$sum=$base_prices[$product_id][2];

	
} else
	$sum=intval($_SESSION['s_best2pay']);


//~ $price1=$base_prices[$product_id][1];
//~ if(intval($price1)==intval($sum))
	//~ $price1=$base_prices[$product_id][0];
$price1=$base_prices[$product_id][0];

$_SESSION['s_best2pay']=$sum;

$fee_1=0; $fee_2=0;
$promocode_id=0;
if(isset($_GET['promocode'])) {
	$tm=time();
	$promo=mb_substr($_GET['promocode'],0,128);
	if($r=$db->fetch_assoc($db->query("SELECT * FROM promocodes
		WHERE product_id='$product_id'
			AND (tm1<='$tm' AND tm2>='$tm')
			AND promocode='$promo' AND cnt!=0 ORDER BY id DESC LIMIT 1",0))
		)
	{
		$promocode_id=$r['id'];

		if($r['uid']) {
			$fee_1=$r['fee_1'];
			$fee_2=$r['fee_2'];
		}
		$sum=$base_prices[$product_id][1];
		$dt2_promocode=date('d.m.Y H:i',$r['tm2']);
		if($r['discount']>0) {
			$d=intval($r['discount']);
			$promocode_msg="<div class='alert alert-success mt-1 small' >Промокод применен. Ваша скидка $d%. <br>* действует до $dt2_promocode</div>";
			if($d<100) {
				$sum=intval($sum*(100-$d)/100);
			} elseif($d<$sum) {
				$sum -=intval($d);
				$promocode_msg="<div class='alert alert-success mt-1 small' >Промокод применен. Ваша скидка $d р. <br>* действует до $dt2_promocode</div>";
			}
		} else {
			$sum=intval($r['price']);
			$promocode_msg="<div class='alert alert-success mt-1 small font16' >Промокод применен. Новая цена $sum р. <br>* действует до $dt2_promocode</div>";
			//print $promocode_msg;
		}
	} else
		$promocode_msg="<div class='alert alert-danger mt-1 small' >Промокод не найден</div>";

}

$fee_pay=null;
if(isset($_GET['fee_pay'])) {
	$fee_pay=intval($_GET['fee_pay']);
	$sum-=$fee_pay;
	if($sum<0)
		$fee_pay+=$sum;
}

if($sum<0)
	$sum=0;
$sum_disp=$sum;

$s1=0; //rassrochka
$s1_hidden="display:none;";
if(isset($_GET['s1'])) 
	$s1=intval($_GET['s1']);
if($s1) {
	$promo_hidden="display:none;";
	$s1_hidden="";
	$sum=$s1;
	//$sum_disp=$base_prices[$product_id][2];
}

$order_description=$base_prices[$product_id]['descr'];

$q=0;
if(isset($_GET['q'])) {
	$q=intval($_GET['q']);
}

//$disp_cnt=$db->yoga_get_stock($uid,$product_id);
$disp_cnt=($q)?"<span class='text-danger' >$q</span>":"<span class='text-danger' >мало</span>";


if($sum==$base_prices[$product_id][2] || $s1)
	$promo_hidden="display:none;"; else $promo_hidden="";

$tm=time();
if($db->dlookup("id","promocodes","tm2>$tm") )
		$promo_hidden="";

$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
if (!stristr($useragent, 'Bot')) {
	if($db->database!='vkt' && $uid!=-1002) {
		$db->save_comm($uid,0,"Зашел на страницу заказа: $order_description",22,$product_id);
		$db->notify($uid, "❗ Зашел на страницу заказа: $order_description");
		$db->mark_new($uid,1);
	}
}

$policy_checked=($ctrl_id != 74) ?"CHECKED" : "";


?>
<div class='container' >
<!--
	<div class='p-5' ><a href='/' class='' target=''><img src='/images/logo/course-300x100.png'></a></div>
-->
	<h1><?=$order_description?></h1>


	<?
	$tm_end_timer=0;
	if(intval($_GET['t'])>0) {
		$tm_end_timer=intval($_GET['t']);
	} else
		$tm_end_timer=$db->dlookup("dt2","discount","uid='$uid' AND (product_id='$product_id')",0);
	if($tm_end_timer) {
		
	}
	$disp_timer=$tm_end_timer>time()?'block':'none';
	?>

	<div class="timer text-center mx-auto"  style='width:300px;display:<?=$disp_timer?>;'>
		Скидка действует:
	  <div class="timer__items">
		<div class="timer__item timer__days" style='font-size:20px;'>00</div>
		<div class="timer__item timer__hours" style='font-size:20px;'>00</div>
		<div class="timer__item timer__minutes" style='font-size:20px;'>00</div>
		<div class="timer__item timer__seconds" style='font-size:20px;'>00</div>
	  </div>
	</div>

	
	<div class='m-md-5 p-3 font32 BS shadow text-secondary' >Стоимость: <span class='striked text-warning' ><?=($price1>$sum_disp)?$price1." р.":""?></span> <span class='text-dark' ><?=$sum_disp?>&nbsp;р.</span>
			<?=$promocode_msg?>
<!--
		<br>
		Осталось мест: <span class='text-dark' ><?=$disp_cnt?></span>
-->
		<div class='mt-2 p-3' style='<?=$promo_hidden?>'>
			<form class='form-inline' >
			  <div class="form-group">
				<input type="text" class="form-control" id="promocode" name="promocode" value="" placeholder="Промокод">
				<input type='hidden' name='land_num' value=<?=$land_num?> >
			  </div>
			  <button type="submit" class="btn btn-success btn-sm ml-1" id="go" name="go" value="yes">Применить</button>
			  <input type='hidden' name='product_id' value='<?=$product_id?>'>
			  <input type='hidden' name='uid' value='<?=$db->uid_md5($uid)?>'>
			</form>
			<?
			$k_fee=0.75;
			if($disp_contacts && $k_fee!=0.0) {
				include "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
				$klid=$db->get_klid_by_uid($uid);
				$p=new partnerka($klid,$database);
				$rest_fee=(float)$p->rest_fee($klid,0,time())*$k_fee;
				?>
				<div class='mt-4' >
					<p><?=$client_name?>, вы можете оплатить баллами: <?=$rest_fee?></p>
					<form class='form-inline' >
						<div class="form-group">
							<input type="text" class="form-control" id="fee_pay" name="fee_pay" value="<?=$fee_pay?>" placeholder="Сумма баллами">
						</div>
						<button type="submit" class="btn btn-info btn-sm ml-1" id="go" name="go" value="yes">Применить</button>
						<input type='hidden' name='land_num' value=<?=$land_num?> >
						<input type='hidden' name='product_id' value='<?=$product_id?>'>
						<input type='hidden' name='uid' value='<?=$db->uid_md5($uid)?>'>
					</form>
				</div>
			<? } ?>
		</div>
		<div class='mt-2' style='<?=$s1_hidden?>'>
		Платеж по программе рассрочки: <span class='text-primary' ><?=$s1?> р.</span>
		</div>
	</div>
	<div class='mt-5' >
		<form action="#" method="POST" accept-charset="UTF-8" id="f1">
		  <div class="form-group">
			<label for="client_name">Фамилия и имя:</label>
			<input type="text" class="form-control" id="client_name" name="fio" value="<?=$client_name?>">
		  </div>
		  <div class="form-group">
			<label for="client_phone">Номер телефона:</label>
			<input type="text" class="form-control" id="client_phone" name="phone" value="<?=$client_phone?>">
		  </div>
		  <div class="form-group">
			<label for="client_email">Ваш е-мэйл:
			</label>
			<input type="email" class="form-control" id="client_email" name="email" value="<?=$client_email?>">
<!--
			<div class='font18 text-success' >Важно: укажите действующий емэйл, к которому есть доступ.</div>
-->
		  </div>
		<?if($fl_disp_city) { ?>
		  <div class="form-group">
			<label for="client_city">Город:</label>
			<input type="text" class="form-control" id="client_city" name="city" value="<?=$client_city?>">
			<div id="cityList" style='display:<?=$fl_disp_city?>;'></div>
		  </div>
		<?}?>
		<?if($fl_disp_comm) { ?>
		  <div class="form-group">
			<label for="client_comm"><?=$label_disp_comm?></label>
			<textarea class="form-control" id="client_comm" name="comm" rows='5'></textarea>
		  </div>
		<?}?>
		  <div class="form-group form-check">
			<label class="form-check-label">
			  <input class="form-check-input" type="checkbox" id='chk3'  <?=$policy_checked?> > <a href='<?=$agreement?>' class='' target='_blank'>согласен на обработку персональных данных</a>
			</label>
		  </div>
		  <div class="form-group form-check">
			<label class="form-check-label">
			  <input class="form-check-input" type="checkbox" id='chk1'  <?=$policy_checked?> > <a href='<?=$privacypolicy?>' class='' target='_blank'>согласен с политикой конфиденциальности</a>
			</label>
		  </div>
		  <div class="form-group form-check">
			<label class="form-check-label">
			  <input class="form-check-input" type="checkbox" id='chk2'  <?=$policy_checked?> > <a href='<?=$dogovor?>' class='' target='_blank'>я прочитал(-а) Условия Договора и согласен(-на) с условиями</a>
			</label>
		  </div>
		<?if(!empty($db->dlookup("prodamus_secret","pay_systems","1")) && $ctrl_id!=1) { ?>
			<button type="button" class="btn btn-primary btn-lg" id="go_prodamus" value='yes'>
			  Оплатить
			  <img src="https://for16.ru/images/prodamus-32.png" alt="Prodamus Icon" width="32" height="32">
			</button>
		<? } ?>
		<?if(!empty($db->dlookup("alfa_secret","pay_systems","1"))) { ?>
			<button type="button" style='background-color:#cc1626; line-height:1.0;' class="btn btn-danger btn-lg" id="go_alfa" value='yes'>
			  Оплатить<br>
			  <img src="https://for16.ru/images/paykeeper1.png" alt="">
			</button>
			
		<? } ?>
		<?if(!empty($db->dlookup("yookassa_secret","pay_systems","1"))) { ?>
			<button type="button" class="btn btn-warning btn-lg" id="go_yookassa" value='yes'>
			  Оплатить
			  <img src="https://for16.ru/images/yoo-32.png" alt="">
			</button>
		<? } ?>
		<?if(!empty($db->dlookup("robokassa_id","pay_systems","1"))) { ?>
			<button type="button" class="btn btn-warning btn-lg" id="go_robokassa" value='yes'>
			  Оплатить
			  <img src="https://for16.ru/images/robokassa-1.svg" width='80' alt="">
			</button>
		<? } ?>
		<?if($db->dlookup("fl_disp_lava","pay_systems","1")==1) { ?>
			<button type="button" class="btn btn-primary btn-lg" id="go_lava" value='yes'>
			  Оплатить
<!--
			  <img src="https://for16.ru/images/lava-1.svg" width='80' alt="">
-->
			</button>
		<? } ?>

		<? if($_SESSION['username']=='vlav') {
			print "<button type='button' class='btn btn-warning btn-lg' id='go_test' value='yes'>pay_test</button>";
			}
		?>

			<input type="hidden" name="go_submit" value="yes"/>
			<input type="hidden" name="vk_uid" value="<?=md5($vk_uid)?>"/>
			<input type="hidden" name="product_id" value="<?=$product_id?>"/>
			<input type="hidden" name="sum_disp" value="<?=$sum_disp?>"/>
			<input type="hidden" name="bc" value="<?=$bc?>"/>
			<input type="hidden" name="land_num" value=<?=$land_num?> />
			<input type="hidden" name="fee_1" value=<?=$fee_1?> />
			<input type="hidden" name="fee_2" value=<?=$fee_2?> />
			<input type="hidden" name="promocode_id" value=<?=$promocode_id?> />
			<input type="hidden" name="fee_pay" value=<?=$fee_pay?> />
			<input type='text' name='tzoffset' value='0' id='tzoffset' style='display:none;'>
			<input type="hidden" name="csrf_token_order" value="<?php echo $_SESSION['csrf_token_order']; ?>">

		</form>
	</div>

	<div class='card my-5 p-3 text-left font18 bg-light border border-0 rounded small' >При оплате заказа банковской картой ввод реквизитов карты происходит
	в банковской системе электронных платежей.
	Представленные Вами данные полностью защищены и никто,
	включая нашу компанию, не может их получить.
	</div>

	
	<div class='text-left mt-5 p3' >
	<img class='p-1' src="https://for16.ru/images/pay-1.svg" height='48' >
	<img class='p-1' src="https://for16.ru/images/pay-2.svg" height='48' >
	<img class='p-1' src="https://for16.ru/images/pay-3.svg" height='48' >
	<img class='p-1' src="https://for16.ru/images/pay-4.svg" height='48' >
	<img class='p-1' src="https://for16.ru/images/pay-5.svg" height='48' >
	<img class='p-1' src="https://for16.ru/images/pay-6.svg" height='48' >
	</div>
</div>

<script type="text/javascript">
	//console.log('test');
	function check_input() {
		if($("#client_name").val().trim()=="") {
			alert("Необходимо указать ваше имя!");
		} else if($("#client_phone").val().trim()=="") {
			alert("Укажите, пожалуйста, телефон для связи!");
		} else if($("#client_email").val().trim()=="") {
			alert("Укажите, пожалуйста, email!");
		} else if(!$("#chk1").is(":checked")) {
			alert("Необходимо согласиться с обработкой персональных данных !");
		} else if(!$("#chk2").is(":checked")) {
			alert("Необходимо согласиться с договором оферты !");
		} else if(!$("#chk3").is(":checked")) {
			alert("Необходимо подтвердить согласие на обработку персональных данных !");
		} else {
			return(true);
		}
		return(false);
	}
	$("#go_prodamus").click(function() {
		if(check_input()) {
			$('#f1').attr('action', 'pay_prodamus.php').submit();
		}
	});
	$("#go_alfa").click(function() {
		if(check_input()) {
			$('#f1').attr('action', 'pay_alfa.php').submit();
		}
	});
	$("#go_yookassa").click(function() {
		if(check_input()) {
			$('#f1').attr('action', 'pay_yookassa.php').submit();
		}
	});
	$("#go_robokassa").click(function() {
		if(check_input()) {
			$('#f1').attr('action', 'pay_robokassa.php').submit();
		}
	});
	$("#go_lava").click(function() {
		if(check_input()) {
			$('#f1').attr('action', 'pay_lava.php').submit();
		}
	});
	$("#go_test").click(function() {
		if(check_input()) {
			$('#f1').attr('action', 'pay_test.php').submit();
		}
	});

	$(document).ready(function(){

		var tzOffset = new Date().getTimezoneOffset();
		document.getElementById('tzoffset').value = tzOffset;

		$('#client_city').keyup(function(){
			console.log('click');
			var msgs_city = $(this).val();
			if(msgs_city != ''){
				$.ajax({
					url:"jquery.php",
					method:"POST",
					data:{msgs_city:msgs_city},
					success:function(data){
						$('#cityList').fadeIn();
						$('#cityList').html(data);
					}
				});
			}
		});
		$(document).on('click', 'li', function(){
			$('#client_city').val($(this).text());
			$('#cityList').fadeOut();
		});
	});


</script>



<?
include "/var/www/vlav/data/www/wwl/inc/timer_script.inc.php";
?>
  <footer class="footer text-center small">
	<hr class='mx-5' >
    <p class="footer__title muted small"><?=$company_name?></p>
  </footer>
<?
include "land_bottom.inc.php";
?>
