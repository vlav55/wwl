<?
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
include "init.inc.php";

$t=new top($database, 'Партнер инфо', false);
$db=new partnerka(false,$database);
$db->db200=$DB200;

if($_SESSION['access_level']>5) {
	print "<p class='alert alert-danger' >Ошибка. нет прав ({$_SESSION['access_level']}).</p>";
	$t->bottom();
	exit;
}
$uid=intval($_GET['uid']);
if(!$uid) {
	print "<p class='alert alert-danger' >Ошибка uid.</p>";
	$t->bottom();
	exit;
}
print "<p class='text-left' >
		<a href='javascript:window.opener.location.reload();window.close();' class='btn btn-warning btn-sm' target=''>Закрыть</a>
		<a href='javascript:history.back();' class='btn btn-light btn-sm' target=''>Назад</a>
	</p>";

$name=$db->disp_name_cp($db->dlookup("name","cards","uid='$uid'").' '.$db->dlookup("surname","cards","uid='$uid'"));
$mob=$db->dlookup("mob_search","cards","uid='$uid'");
$email=$db->dlookup("email","cards","uid='$uid'");
if(!$klid=$db->dlookup("id","cards","uid='$uid'")) {
	print "<p class='alert alert-danger' >Ошибка klid.</p>";
	$t->bottom();
	exit;
}

print "<h4 class='card p-2 bg-light mt-4 text-center' >$name</h4> ";


if(isset($_GET['make_partner'])) {
	if(!$db->is_partner_db($uid)) {
		$db->fee_hello=$fee_hello;
		$db->fee=$fee_1;
		$db->fee2=$fee_2;
		$db->ctrl_id=$ctrl_id;
		$r=$db->partner_add($klid,$email,$name,$username_pref='partner_');
		$db->save_comm($uid,$user_id,false,25);
		print "<div class='alert alert-success' ><h3>$name - сделан партнером</h3></div>";
	} else
		print "<div class='alert alert-warning' ><h3>$name - это партнер</h3></div>";
}

if(isset($_GET['remove_partner'])) {
	if($db->is_partner_db($uid)) {
		$cnt=$db->num_rows($db->query("SELECT * FROM cards WHERE del=0 AND utm_affiliate='$klid' "));
			
		print "<p class='alert alert-warning' >За партнером <b>$name</b> закреплено <b>$cnt</b> клиентов.
		При снятии статуса партнера все закрепления будут сняты. Удаляем партнера?
		<a href='?do_remove_partner=yes&uid=$uid' class='btn btn-danger btn-sm' target=''>Удалить</a>
		<a href='?cancel=yes&uid=$uid' class='btn btn-outline-secondary btn-sm' target=''>Отменить</a>
		</p>";
	} else
		print "<div class='alert alert-warning' ><h3>$name - это не партнер</h3></div>";
}
if(isset($_GET['do_remove_partner'])) {
	if($klid) {
		$db->partner_del($klid);
		print "<div class='alert alert-success' ><h3>$name - удален из партнеров</h3></div>";
	}
}

if(isset($_GET['save_fee'])) {
	if($_SESSION['access_level']<=3) {
		$fee_1=floatval($_GET['fee_1']);
		$fee_2=floatval($_GET['fee_2']);
		$db->query("UPDATE users SET fee='$fee_1', fee2='$fee_2' WHERE klid='$klid'");
		print "<p class='alert alert-success' >Записано!</p>";
	} else
		print "<p class='alert alert-danger' >нет доступа</p>";
}
if(isset($_GET['del_spec_item'])) {
	if($_SESSION['access_level']<=3) {
		$pid=intval($_GET['pid']);
		$db->query("DELETE FROM partnerka_spec WHERE uid='$uid' AND pid='$pid'");
		print "<p class='alert alert-success' >Удалено!</p>";
	} else
		print "<p class='alert alert-danger' >нет доступа</p>";
}
if(isset($_GET['do_add_spec_item'])) {
	if($_SESSION['access_level']<=3) {
		$pid=intval($_GET['pid']);
		$fee_1=floatval($_GET['fee_1']);
		$fee_2=floatval($_GET['fee_2']);
		$fee_cnt=floatval($_GET['fee_cnt']);
		if($pid) {
			$db->set_personal_fee($uid,$pid,$fee_1,$fee_2,$fee_cnt);
			print "<p class='alert alert-success' >Добавлено!</p>";
		}
	} else
		print "<p class='alert alert-danger' >нет доступа</p>";
}
if(isset($_GET['add_spec_item']) || isset($_GET['edit_spec_item'])) {
	if($_SESSION['access_level']<=3) {
		$pid=isset($_GET['pid'])?intval($_GET['pid']):0;
		$fee_1=$db->dlookup("fee_1","partnerka_spec","pid='$pid' AND uid='$uid'");
		$fee_2=$db->dlookup("fee_2","partnerka_spec","pid='$pid' AND uid='$uid'");
		$fee_cnt=$db->dlookup("fee_cnt","partnerka_spec","pid='$pid' AND uid='$uid'");
		
		?>
		<div class='card p-3 bg-light my-4' >
			<h3>Добавить индив условия</h3>
			<form>
				<div class='form-group' >
					<label for='__pid'>Товар / Услуга</label>
					<select class='form-control' name='pid' id='__pid'>
						<option value='0'>=выберите товар=</option>
						<?
						$res=$db->query("SELECT * FROM product WHERE del=0");
						while($r=$db->fetch_assoc($res)) {
							$sel=($r['id']==$pid)?"SELECTED":"";
							print "<option value='{$r['id']}' $sel>{$r['id']} {$r['descr']}</option>";
						}
						?>
					</select>
				</div>
				<div class='form-group' >
					<label for='__fee_1'>Вознаг. уровень 1, %</label>
					<input type='text' id='__fee_1' class='form-control' name='fee_1' value='<?=$fee_1?>'>
				</div>
				<div class='form-group' >
					<label for='__fee_2'>Вознаг. уровень 2, %</label>
					<input type='text' id='__fee_2' class='form-control' name='fee_2' value='<?=$fee_2?>'>
				</div>
				<div class='form-group' >
					<label for='__fee_cnt'>На сколько продаж начислять вознагр (0-без огр)</label>
					<input type='text' id='__fee_cnt' class='form-control' name='fee_cnt' value='<?=$fee_cnt?>'>
				</div>
				<input type='hidden' name='uid' value='<?=$uid?>'>
				<div><button type='submit' name='do_add_spec_item' value='yes' class='btn btn-primary ' >Записать</button></div>
			</form>
		</div>
		<?
	} else
		print "<p class='alert alert-danger' >нет доступа</p>";
}

if(!$db->is_partner_db($uid)) {
	print "<p><a href='?make_partner=yes&uid=$uid' class='btn btn-primary' target=''>Сделать партнером</a></p>";
} else {
	print "<p class='alert alert-warning' >
		Это партнер
		<a href='?remove_partner=yes&uid=$uid' class='btn btn-danger btn-sm' target=''>Убрать из партнеров</a>
		</p>
		";
	$fee_1=$db->dlookup("fee","users","klid='$klid'");
	$fee_2=$db->dlookup("fee2","users","klid='$klid'");
	$bc=$db->dlookup("bc","users","klid='$klid'");
	$user_id=$db->get_user_id($klid);
	$mentor=$db->get_mentor($user_id);
	if($mentor)
		$m="<i class='badge bg-danger p-2' >$mentor</i> ".$db->disp_name_cp($db->dlookup("real_user_name","users","id='$mentor'"));
	else
		$m="верхний уровень";
	$direct_code_link=$db->get_direct_code_link($klid);
	$partner_link=$db->get_partner_link($klid,'land');
	$partner_link_senler=$db->get_partner_link($klid,'senler');
	
	print "<div class='card_ bg-light p-3' >
		<p>Номер партнера: <span class='badge bg-info p-2 font18' >$user_id</span> находится под: <span class='badge bg-light p-2 font18' >$m</span></p>
		<!--<p>Партнерский код: <span class='badge bg-info p-2 font18 text-white'>$bc</span></p>-->
		";
	?>

    <div>Партнерский код: 
        <span class='badge bg-info p-2 font18 text-white' id="partnerCodeDisplay"><?php echo $bc; ?></span>
        <input type="text" id="bc_edit" value="<?php echo $bc; ?>" class="form-control  w-auto" style="display:none;">
    </div>
    <?if($_SESSION['access_level']<=3) { ?>
	<script>
	$(document).ready(function() {
		$('#partnerCodeDisplay').click(function() {
			$(this).hide(); // Hide the display
			$('#bc_edit').show().focus(); // Show the input field
		});

		$('#bc_edit').keypress(function(e) {
			if (e.which == 13) { // Enter key pressed
				var newBc = $(this).val();
				var userId = "<?php echo $user_id; ?>"; // Get user ID from PHP
				$.ajax({
					type: "POST",
					url: "jquery.php", // The PHP file that will process the request
					data: { bc: newBc, id: userId, ch_bc: 'yes' },
					success: function(response) {
						$('#partnerCodeDisplay').text(newBc).show(); // Update the display
						$('#bc_edit').hide(); // Hide the input
					},
					error: function() {
						alert('Error saving the new code.');
					}
				});
			}
		});
	});
	</script>
	<?}?>
    <?
	if($_SESSION['access_level']<=3)
		print "
			<p>Вход в личный кабинет:
			<div class='card p-2 bg-light'><span class='badge bg-light p-2'>$direct_code_link</b></span></div>
			</p>
			<br>
			";
	print "<p>Партнерские ссылки для активных лэндингов: <a href='#__links' class='' data-toggle='collapse'>развернуть</a></p>";

	print "<div class='collapse' id='__links'>";
	if($partner_link_senler) {
	print "<div class='card p-2 bg-light'>
			<div>
			<i class='badge bg-info p-2 text-white' >senler</i> Партнерская ссылка ВК (лэндинг senler):
			<a href='$partner_link_senler' target='_blank' title='перейти на лэндинг в новом окне'><span class='fa fa-arrow-circle-right'></span></a>
			<div class='card p-2 bg-light'>
				<div>
				<span class='badge bg-light p-2' id='vk1'>$partner_link_senler</span>
				<a href='javascript:copySpanContent(\"vk1\");' target='' title='скопировать ссылку'>
				<i class='fa fa-copy'></i>
				</div>
			</div>
			</div>
		</div>";
	}
	//~ print "<div class='card p-2 bg-light'>
			//~ <div>
			//~ <i class='badge bg-info p-2' >1</i> Лэндинг по умолчанию:
			//~ <a href='$partner_link' target='_blank' title='перейти на лэндинг в новом окне'><span class='fa fa-arrow-circle-right'></span></a>
			//~ <div class='card p-2 bg-light'><span class='badge bg-warning p-2'>
				//~ $partner_link
			//~ </span></div>
			//~ </div>
		//~ </div>";
	$res=$db->query("SELECT * FROM lands WHERE del=0 AND  fl_not_disp_in_cab=0");
	while($r=$db->fetch_assoc($res)) {
		$link=str_replace("https:/","https://",str_replace("//","/","{$r['land_url']}/?bc=$bc"));
		$label_partner_land=($r['fl_partner_land'])?"(партнерский лэндинг)":"";
		print "<div class='card p-2 bg-light' >
				<div>
				<i class='badge bg-info p-2 text-white' >{$r['land_num']} $label_partner_land</i> {$r['land_name']}
				<a href='$link' target='_blank' title='перейти на лэндинг в новом окне'><span class='fa fa-arrow-circle-right'></span></a>
				<div class='card p-2 bg-light'>
					<div>
					<span  class='badge bg-light p-2' id='link_{$r['id']}'>$link</span>
					<a href='javascript:copySpanContent(\"link_{$r['id']}\");' target='' title='скопировать ссылку'>
					<i class='fa fa-copy'></i>
					</a>
					</div>
				</div>
				</div>
			</div>";
	}
	print "</div>";
	print "</div>";
	?>
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

	print "<div class='card p-3 bg-light' id='__fee'>
		<h3>Вознаграждения</h3>";

	if(isset($_GET['add_fee'])) {
		$fee=intval($_GET['add_fee']);
		if($fee) {
			//$klid_up=$db->dlookup("klid","users","id='{$_SESSION['userid_sess']}'");
			$klid_up=$klid;
			$comm=mb_substr(trim($_GET['comm']),0,512);
			$db->query("INSERT INTO partnerka_op SET
						klid_up='$klid_up',
						klid='$klid',
						avangard_id='0',
						uid='$uid',
						product_id='-1',
						amount='$fee',
						fee='100',
						fee_sum='$fee',
						tm='".time()."',
						level='1',
						comm='".$db->escape($comm)."'
						");
			print "<p class='alert alert-info' >Начисление в сумме $fee р. проведено успешно. См <a href='#svodka' class='' target=''>отчет</a></p>";
		}
	}
	if($_SESSION['access_level']<=3) {
	?>
		<div>
		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#feeModal" id="add_fee">
		  Начислить сейчас
		</button>
		<p><a href='#svodka' class='' target=''>Сводка по начислениям и выплатам</a></p>
		</div>
		<!-- Modal -->
		<div class="modal fade" id="feeModal" tabindex="-1" role="dialog" aria-labelledby="feeModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<h5 class="modal-title" id="feeModalLabel">Начислить вознаграждение</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			  </div>
			  <div class="modal-body">
				<form id='feeForm'>
				  <div class="form-group">
					<label for="add_fee">Введите сумму</label>
					<input type="text" class="form-control" id="add_fee" name="add_fee">
				  </div>
				  <div class="form-group">
					<label for="comm">Комментарий</label>
					<input type="text" class="form-control" id="comm" name="comm">
				  </div>
					<input type="hidden" name="uid" value='<?=$uid?>'>
				</form>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
				<button type="submit" class="btn btn-primary" onclick="document.getElementById('feeForm').submit()">Записать</button>
			  </div>
			</div>
		  </div>
		</div>
	<?
	}
	print "
		<div class='card p-2 bg-light' >
		<h4>Индивидуальные условия</h4>
		";
	if($_SESSION['access_level']<=3)
		print "<p><a href='?add_spec_item=yes&uid=$uid' target='' class='btn btn-primary btn-sm' >Добавить</a></p>
			";

	$res=$db->query("SELECT *,partnerka_spec.fee_1 AS fee1, partnerka_spec.fee_2 AS fee2, partnerka_spec.fee_cnt AS fee_cnt FROM partnerka_spec
			JOIN product ON product.id=pid
			WHERE product.del=0 AND uid='$uid' ORDER BY pid");
	if($db->num_rows($res)) {
		print "<table class='table table-striped table-condensed small' >
			<thead>
				<tr>
					<th>Продукт</th>
					<th>Наименование</th>
					<th>Цена 1</th>
					<th>Цена 2</th>
					<th>Вознаг 1, % или фикс</th>
					<th>Вознаг 2, % или фикс</th>
					<th>На сколько продаж начислять вознагр</th>
					<th>Действия</th>
				</tr>
			</thead>
			<tbody>
			";
		while($r=$db->fetch_assoc($res)) {
			if($_SESSION['access_level']<=3) {
				$ctrl="
				<a href='?edit_spec_item=yes&uid=$uid&pid={$r['pid']}' class='' target='' title='изменить'><i class='fa fa-edit' ></i></a>
				<a href='?del_spec_item=yes&uid=$uid&pid={$r['pid']}' class='' target='' title='удалить'><i class='fa fa-remove' ></i></a>
				";
			} else
				$ctrl=" ";
			print "<tr>
			<td>{$r['pid']}</td>
			<td>{$r['descr']}</td>
			<td>{$r['price1']}</td>
			<td>{$r['price2']}</td>
			<td>{$r['fee1']}</td>
			<td>{$r['fee2']}</td>
			<td>{$r['fee_cnt']}</td>
			<td>$ctrl
			</td>
			</tr>";
		}
		print "</tbody></table>";
		print "<p class='small text-info' >* ВОЗНАГРАЖДЕНИЕ: если значение меньше 100,
					то оно интерпретируется, как процент. 100 и больше -
					как фиксированная сумма вознаграждения</p>";
	}
	print "</div>";
	
	if($_SESSION['access_level'] <=3 ) {
	/*	print "<div class='card p-3 bg-light' ><div style=' width:300px;'>
			<h4>По умолчанию:</h4>
			<form>
				<label for='fee_1'>Вознаграждение уровень 1, % или фикс</label>
				<input type='text' id='fee1' name='fee_1' value='$fee_1' class='form-control'> 
				<label for='fee_2'>Вознаграждение уровень 2, % или фикс</label>
				<input type='text' id='fee2' name='fee_2' value='$fee_2' class='form-control'>
				<input type='hidden' value='$uid' name='uid'>
				<button type='submit' name='save_fee' value='yes' class='btn btn-primary' >Сохранить</button>
			</form>
			</div>
			</div>
			";
	*/
		print "</div>";

	$bank_details=$db->dlookup("bank_details","users","id='$user_id'");
	print "<div class='card p-2'>
		<a href='#__bank_details' data-toggle='collapse' class='' target=''>Банковские реквизиты</a>";
	print "<div id='__bank_details' class='card p-2 collapse' >".nl2br($bank_details)."</div>";
	print "</div>";

	print "<div class='card p-2 my-3 bg-light' >
		<h3>Доступ в CRM</h3>
		<p>Ссылка: <a href='$DB200/cp.php?view=yes$filter=last_10' class='' target='_blank'>$DB200/cp.php?view=yes&filter=new</a></p>
		<div class='card p-2' >
		Логин: ".$db->get_user_login($user_id)." <br>
		Пароль: ".$db->get_user_passw($user_id)." <br>
		</div>";
		?>

		<?
		if($_SESSION['access_level']<=3) {
			$access_level=$db->dlookup("access_level","users","id='$user_id'");
			?>
			<div class="form-group">
				<div id='access_level_res'></div>
			  <label for="set_access_level">Установить роль</label>
			  <div class="form-check">
				<?$checked=($access_level==3)?"CHECKED":"";?>
				<input class="form-check-input" type="radio" name="set_access_level" id="admin-radio" value="3" <?=$checked?> >
				<label class="form-check-label" for="admin-radio">
				  Админ
				</label>
			  </div>
			  <div class="form-check">
				<?$checked=($access_level==4)?"CHECKED":"";?>
				<input class="form-check-input" type="radio" name="set_access_level" id="manager-radio" value="4" <?=$checked?> >
				<label class="form-check-label" for="manager-radio">
				  Менеджер
				</label>
			  </div>
			  <div class="form-check">
				<?$checked=($access_level==5)?"CHECKED":"";?>
				<input class="form-check-input" type="radio" name="set_access_level" id="minimal-radio" value="5" <?=$checked?> >
				<label class="form-check-label" for="minimal-radio">
				  Минимальный
				</label>
			  </div>
			</div>
			
			<div class="form-check">
			  <div class="form-check">
				<?
				$checked=($db->dlookup("fl_allowlogin","users","id='$user_id'",0))?"CHECKED":"";
				?>
				<input class="form-check-input" type="checkbox" name="set_no_login" id="set_no_login" <?=$checked?> >
				<label class="form-check-label" for="set_no_login">
				  Разрешить вход в CRM
				</label>
			  </div>
			</div>

			<script>
			$(document).ready(function() {
			  $('input[name="set_access_level"]').change(function() {
				var selectedValue = $(this).val(); // получение выбранного значения
				$.get('jquery.php', { 
				  access_level: selectedValue, 
				  partner_set_access_level: 'yes',
				  uid: '<?=$db->uid_md5($uid)?>'
				}, function(response) {
				  console.log('GET запрос выполнен успешно');
				  $('#access_level_res').html('<p class=\"alert alert-success\" >Уровень доступа изменен</p>');
				  // действия при успешном выполнении GET запроса
				}).fail(function() {
				  console.log('При выполнении GET запроса произошла ошибка');
				  // действия при ошибке выполнения GET запроса
				});
			  });
			  $('input[name="set_no_login"]').change(function() {
				var checked;
				if ($(this).prop('checked'))
					checked=1; // получение выбранного значения
				else
					checked=0; // получение выбранного значения
				console.log("HERE_"+checked);
				$.get('jquery.php', { 
				  fl: checked, 
				  set_no_login: 'yes',
				  uid: '<?=$db->uid_md5($uid)?>'
				}, function(response) {
				  console.log('GET запрос выполнен успешно');
				  if(checked==1)
						$('#access_level_res').html('<p class=\"alert alert-success\" >Доступ в CRM разрешен</p>');
					else
						$('#access_level_res').html('<p class=\"alert alert-success\" >Доступ в CRM ЗАПРЕЩЕН</p>');
				  // действия при успешном выполнении GET запроса
				}).fail(function() {
				  console.log('При выполнении GET запроса произошла ошибка');
				  // действия при ошибке выполнения GET запроса
				});
			  });
			});
			</script>
			<?
			}

		if($ctrl_id===1 && $_SESSION['access_level']<=3) {
			if(isset($_GET['add_licenses_cnt'])) {
				$cnt=intval($_GET['add_licenses_cnt']);
				$db->users_billing_add($user_id,$vid=1,$credit=$cnt,$debit=0,$comm="добавлено вручную {$_SESSION['userid_sess']}") ;
				print "<script>location='?uid=$uid#add_licenses'</script>";
			}
			?>
			<div class='card p-2 text-info' id='add_licenses'>
				<h3>Управление лицензиями</h3>
				<p class='alert alert-info' >На остатке лицензий: <?= $db->users_billing_rest($user_id,$vid=1)?> мес.</p>
				<form class="form-inline" action="#add_licenses">
					<div class="form-group mr-2 mb-2">
						<label for="add_licenses_cnt" class="mr-2">ADD Licenses for months:</label>
						<input type="number" 
							   class="form-control" 
							   id="add_licenses_cnt" 
							   name="add_licenses_cnt" 
							   min="1" 
							   value="1"
							   style="width: 100px;">
					</div>
					<input type="hidden" name="uid" value="<?= $uid ?>">
					<button type="submit" class="btn btn-primary mb-2">
						<i class="fa fa-plus"></i> Add
					</button>
				</form>
				<p>https://for16.ru/d/1000/lk/cab3.php?u=<?=$db->get_direct_code($klid)?></p>
				
			</div>
			<?
		}
			
		print "</div>";
		
		$tm1=0; $tm2=time();
		$p=$db;
		print "<div class='card p-3' id='svodka'>";
		print "<h2>Сводка по партнерским начислениям и выплатам</h2>";
		print "<p>Сумма продаж: <b>".$p->sum_buy($klid,$tm1,$tm2)." р.</b> </p>";
		print "<p>Выплачено: <b>".$p->sum_pay($klid,$tm1,$tm2)."  р.</b> <a href='lk/report_pay_detailed_2.php?klid=$klid' class='btn btn-info' target='_blank'>детализация</a></p>";
		print "<p>Начислено: <b>".$p->sum_fee($klid,$tm1,$tm2)."  р.</b> <a href='lk/report_pay_detailed.php?klid=$klid' class='btn btn-info' target='_blank'>детализация</a></p>";
		print "<p>Остаток: <b>".$p->rest_fee($klid,$tm1,$tm2)."  р.</b> </p>";
		print "</div>";
	} else {
		print "<div class='card p-3 bg-light' ><div style=' width:300px;'>
			<h4>По умолчанию:</h4>
			<form>
				<label for='fee_1'>Вознаграждение уровень 1, % или фикс</label>
				<input type='text' id='fee1' name='fee_1' value='$fee_1' class='form-control' disabled> 
				<label for='fee_2'>Вознаграждение уровень 2, % или фикс</label>
				<input type='text' id='fee2' name='fee_2' value='$fee_2' class='form-control' disabled>
			</form>
			</div>
			</div>
			";
		print "</div>";
	}
}

//print "<p>under construction</p>";

?>
<?

$t->bottom();

?>
