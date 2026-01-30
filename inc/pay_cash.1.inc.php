<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/top.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/justclick_api.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
include "init.inc.php";

if(isset($_GET['land_num'])) {
	$land_num=intval($_GET['land_num']);
	$pay_system=$land_num==-1 ? "cash" : "bank";
	include_once "/var/www/vlav/data/www/wwl/inc/pay_common.1.inc.php";
	print "OK $land_num";
	exit;
}


$db=new top($database, 'Оплата от клиента', false);
$p=new partnerka(false,$database);

if($_SESSION['access_level']>3) {
	print "<p class='alert alert-danger' >Ошибка. нет прав ({$_SESSION['access_level']}).</p>";
	$db->bottom();
	exit;
}
$uid=intval($_GET['uid']);
if(!$db->dlookup("id","cards","uid='$uid'")) {
	print "<p class='alert alert-danger' >Ошибка: uid is not exists</p>";
	exit;
}

$p_rest=0;
if($db->is_partner_db($uid)) {
	$klid=$db->get_klid_by_uid($uid);
	$p_rest=$p->rest_fee($klid);
}

if(isset($_GET['del'])) {
	$id=intval($_GET['id']);
	if($db->dlookup("id","avangard","id='$id' AND (pay_system!='cash' AND pay_system!='bank')",0)) {
		print "<p class='alert alert-warning' >Это оплата через платежную систему, удалить невозможно</p>";
	} else
	print "<p class='alert alert-warning' >Подтведите удаление
		<a href='?do_del=yes&id=$id&uid=$uid' class='' target=''>удалить</a>
		<a href='?uid=$uid' class='' target=''>отменить</a></p>
		";
}

if(isset($_GET['do_del'])) {
	if($_SESSION['access_level']<=3) {
		$id=intval($_GET['id']);
		if($id && $uid) {
			$r=$db->fetch_assoc($db->query("SELECT * FROM avangard WHERE res=1 AND vk_uid='$uid' AND id='$id'",0));
			if($r) {
				if(!isset($_GET['do_del'])) {
					$dt=date('d.m.Y H:i',$r['tm']);
					print "<div class='card bg-light p-2' >
					<h3>Подтвердите удаление</h3>
					<p>$dt	{$r['c_name']} {$r['amount']}</p>
					<p><a href='?do_del=yes&id=$id&uid=$uid' class='btn btn-warning' target='' >Удалить</a></p>
					</div>";
				} else {
					$db->query("UPDATE avangard SET res=0 WHERE res=1 AND vk_uid=$uid AND id=$id",0);
					$user=$db->dlookup("real_user_name","users","id='{$_SESSION['userid_sess']}'");
					$msg="Удалена оплата на сумму {$r['amount']} : выполнил $user";
					$db->save_comm($uid,$_SESSION['userid_sess'],$msg);
					$db->notify($uid,$msg);
					print "<p class='alert alert-danger' >Удалено успешно</p>";
					sleep(3);
					print "<script>location='?view=yes&uid=$uid'</script>";
				}
			} else
				print "<p class='alert alert-danger' >Ошибка 2 $id. Сообщите техподдержке</p>";
		} else
			print "<p class='alert alert-danger' >Ошибка 1. Сообщите техподдержке</p>";
	} else
		print "<p class='alert alert-danger' >У вас нет прав на эту операцию</p>";
}

if(isset($_POST['do_save____'])) {
	if(1) { //$s=intval(preg_replace('~\D+~','', $_POST['sum']))
		$s=intval($_POST['sum']);
		//$db->notify_me("HERE_".$s); exit;
		$tm1=$db->dt1(time());
		if($tm=$db->date2tm($_POST['dt1'])+( time()-$db->dt1(time()) ) ) {
			$order_id=$db->get_next_avangard_orderid();
			if(!$db->dlookup("order_id","avangard","order_id='$order_id'") ) {
				$order_number=empty(trim($_POST['order_number'])) ? $order_id : trim($_POST['order_number']);
				$name=$db->dlookup("name","cards","uid='$uid'").' '.$db->dlookup("surname","cards","uid='$uid'");
				$phone=$db->dlookup("mob_search","cards","uid='$uid'");
				$email=$db->dlookup("email","cards","uid='$uid'");
				$product_id=intval($_POST['product_id']);
				$order_descr=$db->dlookup("descr","product","id='$product_id'");
				$term=intval($db->dlookup("term","product","id='$product_id'"));
				//$tm_end=$tm+($term*24*60*60);
				$tm_end_last=$db->fetch_assoc($db->query("SELECT vk_uid,tm_end FROM `avangard` WHERE res=1 AND product_id='$product_id' AND vk_uid='$uid' ORDER BY tm_end DESC LIMIT 1",0))['tm_end'];
				$tm_end_last=($tm_end_last>time()) ? $tm_end_last : time();
				$tm_end=$db->dt2($tm_end_last+(intval($term*24*60*60) ));
				
			//	print date("d/m/Y H:i",$tm_end); exit;
				$db->query("INSERT INTO avangard SET
						tm='$tm',
						pay_system='cash',
						product_id='$product_id',
						order_id='$order_id',
						order_number='".$db->escape($order_number)."',
						order_descr='".$db->escape($order_descr)."',
						amount='$s',
						amount1='$s',
						c_name='".$db->escape($name)."',
						phone='".$db->escape($phone)."',
						email='".$db->escape($email)."',
						vk_uid='$uid',
						res=1,
						tm_end='$tm_end',
						comm='".$db->escape(mb_substr($_POST['comm'],0,1024))."'
						");
				$user=$db->dlookup("real_user_name","users","id='{$_SESSION['userid_sess']}'");
				$msg="Проведена вручную оплата продукта: $order_descr : На сумму $s : выполнил $user";

				if($land_num=$db->dlookup("land_num","lands","del=0 AND product_id='$product_id'")) {
					include_once "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
					$s=new vkt_send($database);
					$res=$db->query("SELECT * FROM vkt_send_1 WHERE del=0 AND (sid=30 OR sid=31) AND (land_num='$land_num' OR land_num=0)",0);
					while($r=$db->fetch_assoc($res)) {
						if($r['sid']==30)
							$s->vkt_send_task_add($ctrl_id, $tm_event=intval(time()+$r['tm_shift']), $vkt_send_id=$r['id'],$vkt_send_type=3,$uid,$order_id);
						elseif($r['sid']==31 && $tm_end)
							$s->vkt_send_task_add($ctrl_id, $tm_event=intval($tm_end+$r['tm_shift']), $vkt_send_id=$r['id'],$vkt_send_type=3,$uid,$order_id);
					}
				}

				$db->save_comm($uid,$_SESSION['userid_sess'],$msg);
				if($s>10) {
					$db->tag_add($uid,1);
				}
				$db->notify($uid,$msg);
				print "<p class='alert alert-success' >Операция добавлена успешно!</p>";
				sleep(3);
				print "<script>location='?view=yes&uid=$uid'</script>";
			} else
				print "<p class='alert alert-danger' >Ошибка: на эту сумму $s по этому клиенту сегодня уже была проведена оплата. Заказ номер - $order_number</p>";
		} else
				print "<p class='alert alert-danger' >Ошибка: Неправильный формат даты. Нужно dd.mm.YYY</p>";
	} else
		print "<p class='alert alert-danger' >Ошибка: сумма оплаты =0 или не числовой формат</p>";
	
}

print "<div>";
print "<div class='text-right' ><a href='javascript:window.close()' class='btn btn-warning' target=''>Закрыть</a></div>";
print "<h2>Оплата от клиента</h2>";
?>
<form method='POST' action='?uid=<?=$uid?>' class='form-horizontal'  id="f1">

	<div class='card bg-light p-2' >
		<h3 class='text-center' >Ручной ввод оплаты</h3>

		<div class='row' >
			<div class='form-group my-0 col-sm-6'>
			<label for='datepicker' class='' >Дата</label>
			<div class=''><input  class='text-center form-control' type='text' name='dt_pay_cash' value='' id='datepicker'></div>
			</div>

			<div class='form-group my-0 col-sm-6'>
<!--
			<label for='num' class='' >Номер заказа</label>
			<div class='' ><input type='text' id='' name='order_number' value='' class='form-control' placeholder='оставить пустым - автоматически'></div>
-->
			</div>
		</div>
		
		<div class='form-group row my-0'>
			<label for='__product_id' class='col-sm-2 col-form-label'>Продукт:</label>
			<div class='col-sm-10' >
				<select name='product_id'  class='form-control' id='__product_id'>
					<option value='0'>-выберите продукт-</option>
				<?
				$res1=$db->query("SELECT * FROM product WHERE del=0");
				//print "<option value='0'>=не привязывать=</option> \n";
				while($r1=$db->fetch_assoc($res1)) {
					$sel=($r1['id']==$product_id)?"SELECTED":"";
					print "<option value='{$r1['id']}' $sel>({$r1['id']}) {$r1['descr']}</option> \n";
				}
				?>
			</select>
			</div>
		</div>

<!--
		<div class='form-group row'>
		<label for='descr' class='col-sm-2 col-form-label' >Описание</label>
		<div class='col-sm-10' ><input type='text' id='descr' name='descr' value='' class='form-control' ></div>
		</div>
-->

		<div class='form-group row my-0'>
		<label for='sum' class='col-sm-2 col-form-label' >СУММА</label>
		<div class='col-sm-10' ><input type='text' id='__sum' name='sum_pay_cash' value='0' class='form-control' ></div>
		</div>
		<div>
		<textarea id='__comm' name='comm_pay_cash' class='form-control' rows='3' placeholder='Комментарий'></textarea>
		</div>

		<?
		if($p_rest) {
			print "<div class='card p-1 bg-light'><p  ><b>Это партнер</b>. Остаток начислений по партнерской  программе: <b>$p_rest р.</b> <a href='partner.php?uid=$uid' class='' target='_blank'>детализация</a></p></div>";
		}
		?>

		<input type='hidden' name='land_num' value='-1'>
		<input type='hidden' name='uid' value='<?=$db->uid_md5($uid)?>'>

	</div>
	<div>
		<button type="button" class="btn btn-primary" name='do_save' value='yes' id='go'>Провести</button>
	</div>
</form>

<script>
	function check_input() {
		if($("#__product_id").val().trim()==0) {
			alert("Не выбран продукт!");
		} else if($("#__sum").val().trim()==0) {
			alert("Укажите сумму!");
		} else {
			return(true);
		}
		return(false);
	}
	$("#go").click(function() {
		if(check_input()) {
			$('#f1').attr('action', 'order.php').submit();
		}
	});
</script>

<script>
// Получаем элементы списка и текстового поля
var select = document.getElementById("__product_id");
var sumInput = document.getElementById("__sum");
var order_idInput= document.getElementById("__num");

// Исполняем код при изменении значения списка
select.addEventListener("change", function() {
  // Получаем выбранное значение списка
  var selectedValue = select.value;

  // Отправляем AJAX-запрос к серверу
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      // Получаем значение поля amount из ответа сервера
      // Заносим значение в текстовое поле
      sumInput.value = JSON.parse(xhr.responseText).amount;
      order_idInput.value=JSON.parse(xhr.responseText).order_id;
    }
  };
  xhr.open("GET", "jquery.php?pay_cash=yes&pid=" + selectedValue, true);
  xhr.send();
});
</script>
<?
$res=$db->query("SELECT *,avangard.id AS id
			FROM avangard LEFT JOIN product ON product_id=product.id
			WHERE res=1 AND vk_uid=$uid ORDER BY avangard.tm DESC",0);
print "<table class='table table-striped' >
	<thead>
		<tr>
			<th>Дата</th>
			<th>Номер</th>
			<th>Товар/услуга</th>
			<th>Сумма</th>
			<th>Вид</th>
			<th>Коммент</th>
			<th> </th>
		</tr>
	</thead>
	";
while($r=$db->fetch_assoc($res)) {
	$id=$r['id'];
	$uid=$r['vk_uid'];
	$dt=date("d.m.Y",$r['tm']);
	$currency=($r['pay_system']);
	print "<tr>
			<td>$dt</td>
			<td>{$r['order_number']}</td>
			<td>{$r['order_descr']}</td>
			<td>{$r['amount']}</td>
			<td>$currency</td>
			<td>".nl2br(htmlspecialchars($r['comm']))."</td>
			<td><a href='?del=yes&id=$id&uid=$uid' class='' target=''><span class='fa fa-trash-o'></span></a></td>
		</tr>";
}
print "</table>";
print "</div>";
$db->bottom();

?>
