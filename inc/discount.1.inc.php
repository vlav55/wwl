<?
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
if(isset($_GET['promocode_set']) || isset($_GET['promocode_edit'])) {
	$tm1=$db->dt1(time());
	//$tm2=$db->dt2($db->date2tm($_GET['dt2']));
    $dateString = $_GET['dt2']; // Get the date from the query string
	$tm2=$db->dt2(time());
    // Validate the date format (optional)
    if (DateTime::createFromFormat('Y-m-d', $dateString) !== false) {
        // Append the time to the date string for 23:59
        $dateTimeString = $dateString . ' 23:59:00';
        
        // Convert to timestamp
        $tm2 = strtotime($dateTimeString);
    } else {
        echo "<p class='alert alert-danger' >Invalid date format.</p>";
    }
	$product_id=intval($_GET['pid']);
	$product=$base_prices[$product_id]['descr'];
	$price=$base_prices[$product_id][1];
	if($insales_id)
		$price="1000000000";
	$promo1=intval($_GET['promo1']);
	$promo2=intval($_GET['promo2']);
	$promo3=intval($_GET['promo3']);
	$cnt=intval($_GET['cnt']) ? intval($_GET['cnt']) : -1;
	$fee_1=floatval($_GET['fee_1']);
	$fee_2=floatval($_GET['fee_2']);
	$promocode_id=isset($_GET['promocode_id'])?intval($_GET['promocode_id']):false;
	$uid_promo=$uid;
	//$db->notify_me("$promo1 $promo2 $promo3");
	$err=false;
	if(isset($_GET['promo_global_chk'])) {
		$uid_promo=0;
	}
	if(!isset($_GET['promocode']) || empty($_GET['promocode']) )
		$promocode=rand(1000,9999);
	else
		$promocode=$db->promocode_validate($_GET['promocode']);
	if(empty($promocode))
		$err="Промокод пустой либо состоит из запрещенных символов";
	if(!preg_match('/^[\p{L}\d\-\_]+$/u', $promocode)) {
		//$err="Промокод может включать только символы, цифры и знак тире";
	}
	if($db->dlookup("id","promocodes","promocode='$promocode' AND product_id='$product_id' AND uid='$uid_promo' AND tm2>".time()) && isset($_GET['promocode_set']))
		$err="Промокод <b>$promocode</b> уже существует";
	if($uid_err=$db->dlookup("uid","promocodes","promocode LIKE '$promocode' AND uid!='$uid_promo'")) {
		$err="Промокод <b>$promocode</b> уже занят <a href='javascript:parent.opener.location=\"msg.php?uid=$uid_err\";window.close();' class='' target=''>здесь</a>";
	}
	if(!$err) {
		if($promo1>0 && $promo1<100) {
			if(!$promocode_id) {
				$promocode_id=$db->promocode_add($promocode,$uid_promo,$tm1,$tm2,$product_id,$discount=$promo1,$price=0);
				if($insales_id) {
					include_once "/var/www/vlav/data/www/wwl/inc/insales.class.php";
					$in=new insales($insales_id,$insales_shop);
					if($ctrl_id==167) {
						$in->id_app="winwinland_demo_11";
						$in->secret_key='e5697c177c0f51497d069969e170dbcb';
						$in->get_credentials();
					}
					//$db->notify_me("1=$in->id_app 2=$in->secret_key");
					$in->ctrl_id=$ctrl_id;
					$p=['code'=>$promocode,
						'disabled'=>false,
						'act_once'=>false,
						'expired_at'=>date("Y-m-d",$tm2),
						'type_id'=>1,
						'discount'=>$discount,
						];
					$res=$in->create_promocode($p);
					if(!isset($res['error']) || $res['http_code']==422)
						print "<p class='alert alert-success' >Промокод синхронизирован с inSales</p>";
					else {
						print "<p class='alert alert-warning' >Ошибка синхронизации с inSales</p>";
						print_r($res);
					}
				}
			} else {
				$db->query("UPDATE promocodes SET
					promocode='".$db->escape($promocode)."',
					tm2='$tm2',
					uid='$uid',
					discount='$promo1'
					WHERE id='$promocode_id'
					");
				if($insales_id) {
					print "<p class='alert alert-warning' >Вы изменили промокод или условия его действия.
					При необходимости внесите изменения в бэкофисе inSales!
					</p>";
				}
			}
			print "<div class='alert alert-success' >Успешно. Промокод <span class='badge' >$promocode</span> на скидку <span class='badge' >$promo1%</span> до ".date("d.m.Y H:i",$tm2)." для <b>$product</b> сгенерирован</div>";
			
		} elseif($promo2<$price && $promo2>0) {
			if(!$promocode_id) {
				$promocode_id=$db->promocode_add($promocode,$uid_promo,$tm1,$tm2,$product_id,$discount=$promo2,$price=0);
				if($insales_id) {
					include_once "/var/www/vlav/data/www/wwl/inc/insales.class.php";
					$in=new insales($insales_id,$insales_shop);
					if($ctrl_id==167) {
						$in->id_app="winwinland_demo_11";
						$in->secret_key='e5697c177c0f51497d069969e170dbcb';
						$in->get_credentials();
					}
					$in->ctrl_id=$ctrl_id;
					$p=['code'=>$promocode,
						'disabled'=>false,
						'act_once'=>false,
						'expired_at'=>date("Y-m-d",$tm2),
						'type_id'=>2,
						'discount'=>$discount,
						];
					$res=$in->create_promocode($p);
					if(!isset($res['error']) || $res['http_code']==422)
						print "<p class='alert alert-success' >Промокод синхронизирован с inSales</p>";
					else {
						//~ print "<p class='alert alert-warning' >Ошибка синхронизации с inSales</p>";
						//~ print_r($res);
					}
				}
			} else {
				$db->query("UPDATE promocodes SET
					promocode='".$db->escape($promocode)."',
					tm2='$tm2',
					uid='$uid',
					discount='$promo2'
					WHERE id='$promocode_id'
					");
			}
			print "<div class='alert alert-success' >Успешно. Промокод <span class='badge' >$promocode</span> на скидку <span class='badge' >$promo2 руб</span> до ".date("d.m.Y H:i",$tm2)." для <b>$product</b> сгенерирован</div>";
		} elseif($promo3>=0 || isset($_GET['for_price_0'])) {
			if(!$promocode_id) {
				$promocode_id=$db->promocode_add($promocode,$uid_promo,$tm1,$tm2,$product_id,$discount=0,$price=$promo3);
			} else {
				$db->query("UPDATE promocodes SET
					promocode='".$db->escape($promocode)."',
					tm2='$tm2',
					uid='$uid',
					price='$promo3'
					WHERE id='$promocode_id'
					");
			}
			print "<div class='alert alert-success' >Успешно. Промокод <span class='badge font18' >$promocode</span> на СПЕЦЦЕНУ <span class='badge font18' >$promo3 руб</span> до ".date("d.m.Y H:i",$dt2)." для <b>$product</b> сгенерирован</div>";
		} else {
			$err="Ошибка";
		}
		if(!$err && $promocode_id) {
			$db->query("UPDATE promocodes SET fee_1='$fee_1',fee_2='$fee_2',cnt='$cnt' WHERE id='$promocode_id'");
			print "<div class='alert alert-success' >Вознаграждения для этого промокода установлены как 1=$fee_1 и 2=$fee_2</div>";
			$cnt_= $cnt==-1 ? "без ограничений" : "$cnt раз";
			print "<div class='alert alert-success' >Количество использований - $cnt_</div>";
		}
	}
	if($err)
		print "<div class='alert alert-warning' >$err</div>";
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
	//$tm2=$db->dt2($db->date2tm($_GET['dt2']));
    $dateString = $_GET['dt2']; // Get the date from the query string
    if (DateTime::createFromFormat('Y-m-d', $dateString) !== false) {
        // Append the time to the date string for 23:59
        $dateTimeString = $dateString . ' 23:59:00';
        
        // Convert to timestamp
        $tm2 = strtotime($dateTimeString);
    } else {
        echo "<p class='alert alert-danger' >Invalid date format.</p>";
    }
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
$name=$db->disp_name_cp($db->dlookup("name","cards","uid='$uid'")." ".$db->dlookup("surname","cards","uid='$uid'"));
?>
<h2>Установки для: <?=$name?></h2>
<div class='card bg-light p-3' >
	<form>
	<div>
	По дату: <input  class='text-center' type='date' name='dt2' value='<?=date("Y-m-d")?>'>
	<span class='small' >(до 23:59 выбранной даты)</span>
	</div>

	<div>
		<select name='pid' class='form-control text-danger' id="productSelect" onchange="updateFees()">
		<option  value='0'>= не выбран товар =</option>
	<?
	//print_r($base_prices);
	foreach($base_prices AS $product_id=>$r) {
		print "<option value='$product_id' fee_1='{$r['fee_1']}' fee_2='{$r['fee_2']}'>{$r['descr']}</option>";
	}
	?>
		</select>
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
	
	<? if(1 || !$insales_id) { ?>
	<button type='submit' name='discount_set' class='btn btn-info' value='yes'>Установить спеццену</button>
	<? } ?>
	<input type='hidden' name='uid' value='<?=$uid?>'>

<!--
	<div class='mt-4' >
		<h4>Или сгенерировать промокод на</h4>
		<div>скидку в % : <input  class='text-center' type='text' name='promo1' value=''></div>
		<div>скидку в рублях : <input  class='text-center' type='text' name='promo2' value=''></div>
		<div>спеццену :  <input  class='text-center' type='text' name='promo3' value=''></div>
		<input type='checkbox' name='promo_global_chk' value='1'> для всех клиентов
		<button type='submit' name='promocode_set' class='btn btn-primary' value='yes'>Сгенерировать промокод</button>
	</div>
-->
	<?$insales_disabled=($insales_id && $ctrl_id!=1) ? "DISABLED" : ""?>
	<h4 class="mb-3 mt-5">Сгенерировать промокод</h4>
	<div class="form-group mb-0 d-flex align-items-center">
		<label for="promocode" class="mr-2 mb-0">Промокод:</label>
		<input class="form-control text-center w-50" type="text" id="promocode" name="promocode" value="">
	</div>
	<div class="d-flex">
		<div class="form-group mb-0 mr-3">
			<label for="promo1" class="col-form-label"><br>Скидка в %:</label>
			<input class="form-control text-center" type="text" id="promo1" name="promo1" value="">
		</div>
		<div class="form-group mb-0 mr-3">
			<label for="promo2" class="col-form-label">ИЛИ скидка в рублях:</label>
			<input class="form-control text-center" type="text" id="promo2" name="promo2" value="">
		</div>
		<div class="form-group mb-0 mr-3">
			<label for="promo3" class="col-form-label"><br>ИЛИ спеццена:</label>
			<input <?=$insales_disabled?> class="form-control text-center" type="text" id="promo3" name="promo3" value="">
		</div>
		<div class="form-group mb-0">
			<label for="cnt" class="col-form-label">Сколько раз действует (0-без огр):</label>
			<input <?=$insales_disabled?> class="form-control text-center" type="text" id="cnt" name="cnt" value="0">
		</div>
	</div>
<!--
	<div class="form-check form-row align-items-center my-0">
		<div class="col-sm-4">
			<input <?=$insales_disabled?> type="checkbox" class="form-check-input" id="promo_global_chk" name="promo_global_chk" value="1">
		</div>
		<label class="form-check-label col-sm-8" for="promo_global_chk">для всех</label>
	</div>
-->
	<? if($db->is_partner_db($uid)) {
		$fee_1=0; $fee_2=0;
		if($insales_id) {
			$fee_1=$db->dlookup("fee_1","product","id=1");
			$fee_2=$db->dlookup("fee_2","product","id=1");
		}
		?>
		<div class='card p-2 m-2' >
			<p>Партнерское вознаграждение для партнера <b><?=$name?></b> при продаже с этим промокодом:</p>
			<div class="d-flex">
				<p class="mr-3">уровень 1:
					<input class="form-control text-center" type="number" id="fee_1" name="fee_1" value="<?=$fee_1?>">
				</p>
				<p>уровень 2:
					<input class="form-control text-center" type="number" id="fee_2" name="fee_2" value="<?=$fee_2?>">
				</p>
			</div>
			<p class='small' >* значение интерпретируется в %, если меньше 100 и в рублях, если больше 100</p>
		</div>
		<?
	} else {
		?>
		<input type='hidden' name='fee_1' value='0'>
		<input type='hidden' name='fee_2' value='0'>
		<?
	}
	?>
	<button type="submit" name="promocode_set" class="btn btn-info mt-3" value="yes">Сгенерировать промокод</button>

	</form>
</div>

<script>
function updateFees() {
    // Get the select element
    var select = document.getElementById('productSelect');

    // Get the selected option
    var selectedOption = select.options[select.selectedIndex];

    // Get the fee_1 and fee_2 attributes
    var fee1 = selectedOption.getAttribute('fee_1');
    var fee2 = selectedOption.getAttribute('fee_2');

    // Check if the element with id='fee_1' exists before updating its value
    var fee1Input = document.getElementById('fee_1');
    if (fee1Input && fee1Input.value === '') {
        fee1Input.value = fee1;
    }

    // Check if the element with id='fee_2' exists before updating its value
    var fee2Input = document.getElementById('fee_2');
    if (fee2Input && fee2Input.value === '') {
        fee2Input.value = fee2;
    }
}
</script>

<?
//$jc=new justclick_api;
//$res=$jc->get_all_subscriptions($email);
//$db->print_r($res);

if(!$insales_id) {
	print "<div class='card bg-light p-3 my-3' >";
	print "<h4 class='pt-3'>Установлены спеццены для: <b>$name</b>:</h4>";
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
}

print "<div class='card bg-light p-3 my-3' >";
print "<h4 class='pt-3' >Партнерские промокоды для: <b>$name</b></h4>";
$db->query("DELETE FROM promocodes WHERE tm2<".time());
$res=$db->query("SELECT * FROM promocodes WHERE uid='$uid' OR uid=0 ORDER BY id DESC");
while($r=$db->fetch_assoc($res)) {
	$promocode=$r['promocode'];
	$dt2=date("Y-m-d",$r['tm2']);
	$descr=$base_prices[$r['product_id']]['descr'];
	$price=$r['price'];
	$discount=$r['discount'];
	$fee_1=$r['fee_1'];
	$fee_2=$r['fee_2'];
	$private=""; //$r['uid']?"<badge class='_' >индивидуальный</badge>":"<badge>общий</badge>";
	print "<div class='alert alert-warning' >
		<form class='form-inline' >";
	print "<span class='badge bg-warning_ font18 mx-1' >
		<input type='text' class='form-control text-center' name='promocode' value='$promocode' style='width:200px;' readonly>
		</span> для &nbsp;
		<b>$descr</b>
		$private";
	if($price) 
		print "&nbsp на цену <span class='badge font18'>
			<input type='text' class='form-control text-center' name='promo3' value='$price' style='width:100px;'>
			</span>р.
			";
	if(!$price && !$discount) 
		print "&nbsp на цену <span class='badge font18'>
			<input type='text' class='form-control text-center' name='promo3' value='$price' style='width:100px;'>
			<input type='hidden' name='for_price_0' value='yes'>
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
		<input type='date' class='form-control text-center' name='dt2' value='$dt2' style='width:150px;' >
		</span>
		";
	if($r['cnt']!=-1)
		print "осталось использований:
			<span class='badge font18' >
			<input type='number' class='form-control text-center' name='cnt' value='{$r['cnt']}' style='width:60px;' >
			</span>
			";
	if($db->is_partner_db($uid)) {
		$fee_1_= $fee_1<100 ? "%" : "руб.";
		print " Вознаграждение уровень 1 : <span class='badge font18'>
			<input type='text' class='form-control text-center' name='fee_1' value='$fee_1' style='width:50px;'> 
			</span>$fee_1_ ,
			";
	}
	if($db->is_partner_db($uid)) {
		$fee_2_= $fee_2<100 ? "%" : "руб.";
		print " уровень 2 : <span class='badge font18'>
			<input type='text' class='form-control text-center' name='fee_2' value='$fee_2' style='width:50px;'> 
			</span>$fee_2_
			&nbsp;&nbsp;";
	}
	print "
		<input type='hidden' name='promocode_id' value='{$r['id']}'>
		<input type='hidden' name='pid' value='{$r['product_id']}'>
		<input type='hidden' name='uid' value='{$r['uid']}'>
		<button type='submit' name='promocode_edit' value='yes' class='btn btn-sm btn-info m-1' >
			<i class='fa fa-save' ></i>
		</button>
		<a href='?promocode_clr=yes&uid=$uid&promocode_id={$r['id']}' class='btn btn-sm btn-secondary' target=''>
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
