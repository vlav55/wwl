<?
include_once "/var/www/vlav/data/www/wwl/inc/top.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/cashier.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/wa_boom.class.php";
include "init.inc.php";
$c=new cashier($database,$ctrl_id,$ctrl_dir);
$klid_cashier=100;
//$c->print_r($c->init_pars);

$uid=$db->get_uid_by_klid($db->get_klid($_SESSION['userid_sess']));
//print "uid=$uid <br>";

if(isset($_GET['ch_cashier_link'])) {
	$passw=$db->passw_gen($len=16);
	$md5=md5($passw);
	$c->query("UPDATE users SET passw='".$db->escape($md5)."',comm='$passw' WHERE klid='$klid_cashier'");
	$d=$c->get_direct_code($klid_cashier);
	$c->query("UPDATE users SET direct_code='".$db->escape($d)."' WHERE klid='$klid_cashier'");
	$cashier_url=$c->get_cashier_url();
	print "<p class='alert alert-success' >Новая ссылка для кассира: <a href='$cashier_url' class='' target='_blank'>$cashier_url</a></p>";
}
if(isset($_POST['do_save_main'])) {
	$c->set_prefix($_POST['prefix']);
	if(!$c->set_website($_POST['website']))
		print "<p class='alert alert-warning' >Не удалось сохранить вебсайт ".htmlspecialchars($_POST['website'])."</p>";
	
	$c->set_msg(1,$_POST['msg1']);
	$c->set_msg(2,$_POST['msg2']);
	$c->set_msg(3,$_POST['msg3']);

	$c->set_fee($_POST['fee']);
	$c->set_discount($_POST['discount']);
	$c->set_no_discount_for_owner(isset($_POST['no_discount_for_owner']) ?1 :0);
	$c->set_yclients_withdraw_cashback(isset($_POST['yclients_withdraw_cashback']) ? 1 : 0);
	$c->create_short_links_table();

	if(intval($_POST['land_num_1']))
		$c->init_pars['land_num_1']=intval($_POST['land_num_1']) ;
	if(intval($_POST['land_num_2']))
		$c->init_pars['land_num_2']=intval($_POST['land_num_2']) ;
	if(intval($_POST['land_num_3']))
		$c->init_pars['land_num_3']=intval($_POST['land_num_3']) ;
	if(intval($_POST['product_id']))
		$c->init_pars['product_id']=intval($_POST['product_id']) ;
	if(isset($_POST['cmd']))
		$c->init_pars['cmd']=trim($_POST['cmd']) ;
	//$c->print_r($c->init_pars);
	$c->set_init_pars($c->init_pars);
	$c=new cashier($database,$ctrl_id,$ctrl_dir);
}

if(isset($_POST['save_apikey'])) {
	if($c->save_apikey('wa_pact',substr(trim($_POST['wa_api_key_pact']),0,128))) {
		print "<p class='alert alert-success' >APIKEY WHATSAPP PACT сохранен успешно</p>";
	}
	if(trim($_POST['wa_api_key_boom'])) {
		if($phone=$c->check_mob($_POST['phone_boom'])) {
			if($c->save_apikey('wa_boom',$_POST['wa_api_key_boom'],$phone)) {
				print "<p class='alert alert-success' >APIKEY WHATSAPP BOOM сохранен успешно</p>";
			}
		} else
			print "<p class='alert alert-warning' >Неверный формат телефона</p>";
	}
}
if (isset($_POST['send_test']) && $_POST['send_test'] == 'yes') {
	$test_phone = isset($_POST['test_phone']) ? $db->check_mob($_POST['test_phone']) : '';
	$test_message = isset($_POST['test_message']) ? $_POST['test_message'] : '';
	$test_message_id = isset($_POST['test_message_id']) ? intval($_POST['test_message_id']) : 0;
	$message_type = isset($_POST['message_type']) ? $_POST['message_type'] : $c->transport;
	if ($test_phone && $test_message) {
		$_SESSION['test_phone']=$test_phone;
		if($test_message_id==1) {
			$res=$c->send_cashback_notice($db->dlookup("id","cards","del=0 AND mob_search='$test_phone'"));
		}
		if($test_message_id==2 || $test_message_id==3) {
			$res=$c->send_loyalty_card($test_phone,$name=null);
		}
		// Send success response
		header('Content-Type: application/json');
		echo json_encode(['status' => 'success', 'res'=>$res]);
		exit;
	} else {
		header('Content-Type: application/json');
		echo json_encode(['status' => 'error', 'message' => 'Invalid phone or message']);
		$_SESSION['test_phone']="";
		exit;
	}
}


$wa_api_key_boom=$c->get_apikey('wa_boom')['apikey'];
$phone_boom=$c->get_apikey('wa_boom')['phone'];
$r=$c->get_apikey('wa_pact');
$wa_api_key_pact=$r['apikey'];
$wa_api_key_trial=$r['trial'];

$cashier_link=$c->get_cashier_url();

$prefix=$c->get_prefix(); //$db->dlookup("tool_val","0ctrl_tools","tool='promo_prefix' AND tool_key='1'");
$website=$c->get_website();

//~ $msg1=trim($db->dlookup("msg","vkt_send_1","id=1"));
//~ $msg2=trim($db->dlookup("msg","vkt_send_1","id=2"));
//~ $msg3=trim($db->dlookup("msg","vkt_send_1","id=3"));
$msg1=$c->get_msg(1);
$msg2=$c->get_msg(2);
$msg3=$c->get_msg(3);

$fee=$c->get_fee(); //$db->dlookup("fee_1","product","id=1");
$fee_percent_checked=$fee<100 ? 'checked' : '';
$fee_rur_checked=$fee>=100 ? 'checked' : '';

$discount=$c->get_discount(); //$db->dlookup("discount","product","id=1");
$discount_percent_checked=$discount<100 ? 'checked' : '';
$discount_rur_checked=$discount>=100 ? 'checked' : '';

$no_discount_for_owner_checked=	$c->get_no_discount_for_owner() ? "CHECKED" : "";


function disp_sample($n) {
	global $c;
	?>
	<p class='small mute' >
		<a href='#collapse_msg<?=$n?>' class='mr-2' data-toggle='collapse'>
			<i class="fa fa-eye"></i> пример
		</a>
		<a href='#' class='mr-2' class='text-success' onclick="document.getElementById('msg<?=$n?>_textarea').value = document.querySelector('#collapse_msg<?=$n?> small').textContent; $('#collapse_msg<?=$n?>').collapse('hide'); return false;">
			<i class="fa fa-paste"></i> вставить пример
		</a>
        <a href='#' class='mr-2' data-toggle='modal' data-target='#testModal' onclick="setTestMessage(<?=$n?>)">
            <i class="fa fa-whatsapp fa-lg" style="color__: #25D366;"></i> тест себе
        </a>
	</p>
	<div id='collapse_msg<?=$n?>' class='collapse card p-2' >
		<small class='lh-1 mb-0 d-block text-info'><?=nl2br(htmlspecialchars($c->get_msg_default($n)));?></small>
	</div>
	<?
}


$t=new top($database,'Настройки',false);

?>
<div class='container my-3 mb-5 text-center' ><img src='https://winwinland.ru/img/logo/logo-200.png' class='img' title='<?=$ctrl_id?>'></div>
<?
	//print "<div class='small mute text-center' >$ctrl_id</div>";
?>
<div class="container">
	<div class="row mt-0">
		<div class="col-lg-8 mx-auto">
			<!-- Settings Form -->
			<form id="settingsForm" method='POST' action='#'>
				<!-- Promo Code Prefix -->
				<div class="form-group mb-2">
					<label for="promoPrefix" class="d-flex align-items-center">
						Префикс промокода
						<a href='https://help.winwinland.ru/docs-category/loyalty-20/' target='_blank'>
							<i class="fa fa-question-circle ml-2 text-muted" title="Справка по настройкам"></i>
						</a>
					</label>
					<input type="text" class="form-control" id="promoPrefix" value="<?=$prefix?>" name="prefix"
						   placeholder="Введите префикс">
				</div>

				<script>
				$(document).ready(function(){
					$('[data-toggle="tooltip"]').tooltip();
				});
				</script>
				
				<div class="form-group mb-0">
					<label for="website" class="d-flex align-items-center" title=''>
						Сайт
					</label>
					<input type="text" class="form-control" id="website" value="<?=$website?>" name="website"
						   placeholder="вебсайт">
				</div>
				<style>
					div.fee_settings div {
						margin-top:0; margin-bottom:0;
						padding-top:0; padding-bottom:0;
					}
				</style>

				<div class="form-group mt-0">
					<a href="#additionalSettings" class="btn btn-link text-decoration-none" data-toggle="collapse">
						<i class="fa fa-cogs mr-2"></i>Дополнительно
					</a>
					
					<div id="additionalSettings" class="collapse mt-2">
						<div class="card p-3">
							<div class="form-group">
								<label for="land_num_1">Лэндинг 1</label>
								<select class="form-control" id="land_num_1" name="land_num_1">
								<?
									$res=$db->query("SELECT * FROM lands WHERE del=0 AND land_num!='$db->land_num_2' AND land_num!='$db->land_num_3' ORDER BY land_num");
									while($r=$db->fetch_assoc($res)) {
										$sel=($r['land_num']==$c->land_num_1) ? 'SELECTED' : '';
										?><option name='land_num_<?=$r['land_num']?>' value='<?=$r['land_num']?>' <?=$sel?> ><?='('.$r['land_num'].') '.$r['land_name']?></option><?
									}
								?>
								</select>
							</div>
							<div class="form-group">
								<label for="land_num_2">Лэндинг 2</label>
								<select class="form-control" id="land_num_2" name="land_num_2">
								<?
									$res=$db->query("SELECT * FROM lands WHERE del=0 AND land_num!='$db->land_num_1' AND land_num!='$db->land_num_3' ORDER BY land_num");
									while($r=$db->fetch_assoc($res)) {
										$sel=($r['land_num']==$c->land_num_2) ? 'SELECTED' : '';
										?><option name='land_num_<?=$r['land_num']?>' value='<?=$r['land_num']?>' <?=$sel?> ><?='('.$r['land_num'].') '.$r['land_name']?></option><?
									}
								?>
								</select>
							</div>
							<div class="form-group">
								<label for="land_num_3">Лэндинг 3</label>
								<select class="form-control" id="land_num_3" name="land_num_3">
								<?
									$res=$db->query("SELECT * FROM lands WHERE del=0 AND land_num!='$db->land_num_1' AND land_num!='$db->land_num_2' ORDER BY land_num");
									while($r=$db->fetch_assoc($res)) {
										$sel=($r['land_num']==$c->land_num_3) ? 'SELECTED' : '';
										?><option name='land_num_<?=$r['land_num']?>' value='<?=$r['land_num']?>' <?=$sel?> ><?='('.$r['land_num'].') '.$r['land_name']?></option><?
									}
								?>
								</select>
							</div>

							<div class="form-group">
								<label for="product_id">PRODUCT ID</label>
								<select class="form-control" id="product_id" name="product_id">
								<?
									$res=$db->query("SELECT * FROM product WHERE del=0 AND id>0 ORDER BY id");
									while($r=$db->fetch_assoc($res)) {
										$sel=($r['id']==$c->init_pars['product_id']) ? 'SELECTED' : '';
										?><option name='product_id' value='<?=$r['id']?>' <?=$sel?> ><?='('.$r['id'].') '.$r['descr']?></option><?
									}
								?>
								</select>
							</div>

							<div class="form-group">
								<label for="cmd">Команда</label>
								<textarea class="form-control" id="cmd" name="cmd"><?=htmlspecialchars($c->init_pars['cmd'])?></textarea>
								<p class='small text-muted' >если команда не указана, то промокод создается автоматически для указанного выше продукта и условий.
								Если указана, то условия могут быть переписаны командой. Например:
								<div class='card p-2 small text-muted' >{{promocode PROMO_auto for_discount 10 4320 23:59 [1] 20 5 0}}<br>
									{{promocode last_promocode for_discount 20 4320 23:59 [2,3] 10 5 0}}
								</div>
								</p>
							</div>

						</div>
					</div>
				</div>

				<label class="mb-1">Настройка вознаграждений</label>
				<div class="card p-2">
					<!-- Cashback Settings -->
					<div class="form-group my-0 fee_settings">
						<div class="row align-items-center">
							<div class="col-md-4 col-6">
								<label class="form-label fw-bold">Кэшбек</label>
							</div>
							<div class="col-md-4 col-6">
								<input type="number" class="form-control form-control-sm" name="fee" value="<?=$fee?>" 
									   placeholder="0" maxlength="4">
							</div>
							<div class="col-md-4 col-6 ">
								<div class="d-flex justify-content-start justify-content-sm-center">
									<div class="form-check me-2">
										<input class="form-check-input" type="radio" name="feeType" id="feePercent" value="" <?=$fee_percent_checked?>>
										<label class="form-check-label" for="feePercent">%</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="feeType" id="feeRub" value="" <?=$fee_rur_checked?>>
										<label class="form-check-label" for="feeRub">₽</label>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Promo Code Discount -->
					<div class="form-group my-0 fee_settings">
						<div class="row align-items-start ">
							<div class="col-sm-4 col-6">
								<label class="form-label fw-bold">Скидка по промокоду</label>
							</div>
							<div class="col-sm-4 col-6">
								<input type="number" class="form-control form-control-sm" value="<?=$discount?>" name="discount"
									   placeholder="0" maxlength="4">
								<div class="form-check mt-1">
									<input class="form-check-input" type="checkbox" <?=$no_discount_for_owner_checked?> id="no_discount_for_owner" name="no_discount_for_owner" value="1" <?=isset($no_discount_for_owner) && $no_discount_for_owner ? 'checked' : ''?>>
									<label class="form-check-label text-muted small" for="no_discount_for_owner">
										Скидка не действует для владельца карты
									</label>
								</div>
							</div>
							<div class="col-sm-4 col-6">
								<div class="d-flex justify-content-start justify-content-sm-center">
									<div class="form-check me-2">
										<input class="form-check-input" type="radio" name="discountType" id="discountPercent" value="" <?=$discount_percent_checked?>>
										<label class="form-check-label" for="discountPercent">%</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="discountType" id="discountRub" value="rub" <?=$discount_rur_checked?>>
										<label class="form-check-label" for="discountRub">₽</label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>



                <!-- Cashback Notification -->
                <div class="form-group">
                    <label class="mb-2">
                        Сообщение о начислении кэшбека
                    </label>
                   <textarea class="form-control text-muted" id='msg1_textarea' rows="3" name="msg1" style="font-size: 0.875rem;"
                              placeholder="Текст сообщения..."><?=htmlspecialchars($msg1)?>
                    </textarea>
                    <small class='form-text text-muted mb-1 d-block' >* чтобы вставить в сообщение сумму начисленного кэшбэка за эту операцию, используйте - {{cashback}}
                    Чтобы вставить весь не использованный кэшбэк - {{cashback_all}}
                    </small>
                    <?disp_sample(1)?>
                </div>

                <!-- New Card Notification -->
                <div class="form-group">
                    <label class="mb-2">
                        Сообщение о выдаче карты новому клиенту
                     </label>
                    <textarea class="form-control text-muted"  id='msg2_textarea' rows="3"  name="msg2" style="font-size: 0.875rem;"
                              placeholder="Текст сообщения..."><?=htmlspecialchars($msg2)?>
                    </textarea>
                    <small class='form-text text-muted mb-1 d-block' >* чтобы вложить промокод в виде текста используйте - {{promocode}} </small>
                    <?disp_sample(2)?>
                </div>

                <!-- Add new Card Notification -->
                <div class="form-group">
                    <label class="mb-2">
                        QR код для нового клиента
                    </label>
                    <textarea class="form-control text-muted"  id='msg3_textarea' rows="3"  name="msg3" style="font-size: 0.875rem;"
                              placeholder="Текст сообщения..."><?=htmlspecialchars($msg3)?>
                    </textarea>
                    <small class='form-text text-muted mb-1 d-block'>* чтобы вложить QR код укажите {{qrcode}}, промокод в виде текста - {{promocode}} </small>
                    <?disp_sample(3)?>
                </div>
                
				<label class="mb-2">Как отправлять сообщения</label>
				<div class="card p-3 form-group ">
					<div class='row my-0' >
						<div class='col-md-6 my-0' >
							<div class="mt-0 d-flex align-items-center my-0">
								<div class="form-check my-0">
									<input class="form-check-input" type="radio" name="messageType" id="tgOption" value="tg" checked>
									<label class="form-check-label" for="tgOption">Telegram</label>
								</div>
								<div class="form-check mr-3 my-0">
									<input class="form-check-input" type="radio" name="messageType" id="whatsappOption" value="whatsapp">
									<label class="form-check-label" for="whatsappOption">WhatsApp</label>
								</div>
								<div class="form-check my-0">
									<input class="form-check-input" type="radio" name="messageType" id="smsOption" value="sms">
									<label class="form-check-label" for="smsOption">SMS</label>
								</div>
							</div>
						</div>
						<div class='col-md-6 d-flex align-items-end my-0' >
							<button type='button' class='btn btn-outline-primary w-100' data-toggle="modal" data-target="#apiKeyModal">
								<i class="fa fa-cog mr-2"></i>Настроить
							</button>
						</div>
					</div>
				</div>

				<?
				if($salon_id=$c->check_yclients($ctrl_id)) {
				?>
				<label class="mb-2">YCLIENTS</label>
				<div class="form-group">
					<div class="card p-3">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="yclients_withdraw_cashback"
								   name="yclients_withdraw_cashback" value="1" 
								   <?=($c->get_yclients_withdraw_cashback() ? 'checked' : '')?>>
							<label class="form-check-label fw-bold" for="yclients_withdraw_cashback">
								Выводить кэшбэк автоматически на карту в yclients
							</label>
							<div class="form-text text-muted small">
								При необходимости карты клиентам будут создаваться автоматически
							</div>
						</div>
					</div>
				</div>
				<style>
				/* Apply the same styles as radio buttons to this checkbox */
				#yclients_withdraw_cashback[type="checkbox"] {
					-webkit-appearance: none !important;
					appearance: none !important;
					width: 20px !important;
					height: 20px !important;
					border: 1px solid #ddd !important;
					border-radius: 4px !important;
					background: white !important;
					position: relative !important;
					margin-top: 0 !important;
					margin-left: 0 !important;
					margin-right: 8px !important;
					cursor: pointer;
				}

				#yclients_withdraw_cashback[type="checkbox"]:checked {
					background-color: #007bff !important;
					border-color: #007bff !important;
				}

				#yclients_withdraw_cashback[type="checkbox"]:checked::before {
					content: "✓";
					position: absolute;
					color: white;
					font-size: 14px;
					font-weight: bold;
					top: 50%;
					left: 50%;
					transform: translate(-50%, -50%);
				}

				#yclients_withdraw_cashback[type="checkbox"]:focus {
					border-color: #007bff !important;
					box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
					outline: none !important;
				}
				</style>
				<?} ?>


                <!-- Action Buttons -->
				<div class="form-group">
					<button type="submit" class="btn btn-outline-primary p-2" name='do_save_main' value='yes'>
						<i class="fa fa-save mr-2"></i>
						<span class="d-none_ d-sm-inline_">Сохранить</span>
					</button>
					<button type="button" class="btn btn-outline-secondary p-2 ml-1" data-toggle="modal" data-target="#shareLinkModal">
						<i class="fa fa-user-plus mr-2"></i>
						<span class="d-none d-sm-inline">Ссылка для кассира</span>
					</button>
					<a href='cp.php?logout=yes' target='_blank' class="btn btn-outline-info p-2 ml-1" >
						<i class="fa fa-dashboard mr-2"></i>
						<span class="d-none_ d-sm-inline_">CRM</span>
					</a>
				</div>
            </form>
        </div>
    </div>
</div>


<?//MODALS?>
<!-- API Key Modal Window -->
<div class="modal fade" id="apiKeyModal" tabindex="-1" role="dialog" aria-labelledby="apiKeyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" action="" id="apiSettingsForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="apiKeyModalLabel">Настройки API интеграций</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="whatsappApiKey" class="form-label">API ключ WHATSAPP
                    <span class='small mute d-block' ><a href='https://winwinland.ru/pdf/winwinland_messengers_setup.pdf' class='' target='_blank'><i class="fa fa-info-circle"></i> инструкция</a></span>
                    </label>
                    <div class='card p-2' >
						<div class="form-group">
							<input type="text" class="form-control" id="whatsappApiKey" value="<?=!$wa_api_key_trial?$wa_api_key_pact:''?>" name="wa_api_key_pact" placeholder="Введите ваш API ключ PACT">
							<div class="form-text small mute">Введите ключ API для интеграции с сервисом PACT.IM
							<?if($wa_api_key_trial) {?>
								<div class="form-text small mute">В данный момент сообщения whatsapp отправляются с аккаунта WinWinLand.
								Подключить свой номер whatsapp вы можете на сервисе pact.im <a href='https://winwinland.ru/pdf/winwinland_messengers_setup.pdf' class='' target='_blank'>по ссылке</a>
								Инструкция находится <a href='https://winwinland.ru/pdf/winwinland_messengers_setup.pdf' class='' target='_blank'>здесь</a>
								Сервис платный, расценки на их сайте.
								</div>
							<?}?>
							</div>
						</div>
<!--
						<div class="form-group mb-1">
							<input type="text" class="form-control" id="whatsappApiKey_boom" value="<?=$wa_api_key_boom?>" name="wa_api_key_boom" placeholder="Введите ваш API ключ BOOM">
							<div class="form-text small mute">Введите ключ API для интеграции с сервисом BOOM</div>
							<input type="text" class="form-control" id="phone_boom" name="phone_boom" value='<?=$phone_boom?>' placeholder="Номер телефона whatsapp">
						</div>
-->
                    </div>
                
                    <div class="form-group">
                        <label for="smsApiKey" class="form-label">API ключ SMS шлюза</label>
                        <input type="text" class="form-control" id="smsApiKey" name="sms_api_key" placeholder="Введите ваш API ключ">
                        <div class="form-text small mute">Введите ключ API для интеграции с сервисом SMS</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary" id="saveApiKey" name="save_apikey" value="yes">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Share Link Modal -->
<div class="modal fade" id="shareLinkModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-user-plus mr-2"></i>Добавить кассира
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Отправьте эту ссылку кассиру для работы в системе:</p>
                
                <!-- Link Input -->
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="shareLink" 
                           value="<?=$cashier_link?>" 
                           readonly>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" onclick="copyLink()">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <small class="text-muted">Кассир перейдет по ссылке и будет работать в системе.</small>
                <small class="text-muted">Либо, альтернативно, кассир может зайти в кабинет
                <a href='#cashier_login' data-toggle='collapse' class='' target=''>по логину/паролю </a></small>
                <div class='card p-3 collapse' id='cashier_login' >
					<p><?=$cashier_login=$db->dlookup("username","users","klid='$klid_cashier'")?></p>
					<p><?=$cashier_passw=$db->dlookup("comm","users","klid='$klid_cashier'")?></p>
                </div>
                <div>
					<a href='?ch_cashier_link=yes' class='btn btn-outline-secondary btn-sm' target=''>Сгенерировать новую ссылку</a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-outline-primary" onclick="shareLink()">
                    <i class="fa fa-share mr-2"></i>Поделиться
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Test Modal -->
<div class="modal fade" id="testModal" tabindex="-1" role="dialog" aria-labelledby="testModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="testModalLabel">
                    <i class="fa fa-paper-plane"></i> Отправить тест себе
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="testForm">
                    <div class="form-group">
                        <label for="testPhone">Телефон</label>
                        <input type="tel" class="form-control" id="testPhone" name="testPhone" value="<?=isset($_SESSION['test_phone']) ? $_SESSION['test_phone'] : "" ?>" placeholder="Введите ваш телефон" required>
                    </div>
                    <input type="hidden" id="testMessageId" name="testMessageId" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" onclick="submitTest()">
                    <i class="fa fa-paper-plane"></i> Отправить
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Custom Alert Modal -->
<div class="modal fade" id="customAlertModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Информация</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Content will be inserted here dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentMessageId = 0;
let lastTestPhone = '';

function setTestMessage(messageId) {
    currentMessageId = messageId;
}

function submitTest() {
    var phone = document.getElementById('testPhone').value;
    if (!phone) {
        alert('Пожалуйста, введите телефон');
        return;
    }
    
    // Save to variable
    lastTestPhone = phone;
    
    // Get message from corresponding textarea
    var message = '';
	var messageType = '<?=$c->transport?>';//document.querySelector('input[name="messageType"]:checked').value;
    switch(currentMessageId) {
        case 1:
            message = document.getElementById('msg1_textarea').value;
            break;
        case 2:
            message = document.getElementById('msg2_textarea').value;
            break;
        case 3:
            message = document.getElementById('msg3_textarea').value;
            break;
    }
    
    if (!message) {
        alert('Сообщение пустое');
        return;
    }
    
    $.post(window.location.href, {
        test_phone: phone,
        test_message: message,
        test_message_id: currentMessageId,
        message_type: messageType,
        send_test: 'yes'
    }, function(data) {
        $('#testModal').modal('hide');
        //alert('Тест отправлен на ' + phone + ' через ' + messageType);
		console.log(data);
		if(data.status === 'success') {
			if(data.res ==1) {
				$('#customAlertModal .modal-body').html('Тест отправлен на ' + phone + ' через ' + messageType);
				$('#customAlertModal').modal('show');
			} else if(data.res ==3) {
				$('#customAlertModal .modal-body').html('Ошибка: не подключен телеграм бот для: ' + phone);
				$('#customAlertModal').modal('show');
			} else if(data.res == 5) {
				//alert('Не настроена отправка карт лояльности (промокодов) клиентам, а также уведомлений о кэшбэкам. <a href="#" class="btn btn-sm btn-primary">Инструкция</a>');
				$('#customAlertModal .modal-body').html('Не настроена отправка карт лояльности (промокодов) клиентам, а также уведомлений о кэшбэках. <br><a href="#" class="btn btn-sm btn-primary mt-2">Инструкция</a>');
				$('#customAlertModal').modal('show');
   			} else if(data.res == 0) {
				alert('Ошибка отправки сообщения');
			} else {
				alert('Тест отправлен на ' + phone + ' через ' + messageType);
			}
			console.log('Result:', data.res);
		} else {
			alert('Ошибка: ' + (data.message || 'Неизвестная ошибка'));
		}
        
        // Don't clear the form here if you want to remember the phone
        // $('#testPhone').val('');
    }).fail(function() {
        alert('Ошибка при отправке');
    });
}

// Populate phone when modal opens
$(document).ready(function() {
    $('#testModal').on('show.bs.modal', function() {
        if (lastTestPhone) {
            document.getElementById('testPhone').value = lastTestPhone;
        }
    });
});
</script>


<script>
function copyLink() {
    const linkInput = document.getElementById('shareLink');
    linkInput.select();
    linkInput.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    // Show feedback
    const copyBtn = event.target.closest('button');
    const originalText = copyBtn.innerHTML;
    
    copyBtn.innerHTML = '<i class="fa fa-check"></i>';
    copyBtn.classList.replace('btn-outline-secondary', 'btn-success');
    
    setTimeout(() => {
        copyBtn.innerHTML = originalText;
        copyBtn.classList.replace('btn-success', 'btn-outline-secondary');
    }, 3000);
}
function shareLink() {
    const link = document.getElementById('shareLink').value;
    
    if (navigator.share) {
        // Use Web Share API if available
        navigator.share({
            title: 'Регистрация кассира',
            text: 'Перейдите по ссылке для регистрации в системе',
            url: link
        });
    } else {
        // Fallback - copy to clipboard
        copyLink();
        alert('Ссылка скопирована в буфер обмена. Отправьте ее кассиру.');
    }
}
</script>


<style>
.form-check-input[type="radio"] {
    -webkit-appearance: none !important;
    appearance: none !important;
    width: 20px !important;
    height: 20px !important;
    border: 1px solid #ddd !important; /* Thinner border like other inputs */
    border-radius: 4px !important;
    background: white !important;
    position: relative !important;
    margin-top: 0 !important;
    margin-left: 0 !important;
    margin-right: 8px !important;
    cursor: pointer;
}

.form-check-input[type="radio"]:checked {
    background-color: #007bff !important;
    border-color: #007bff !important; /* Same border color when checked */
}

.form-check-input[type="radio"]:checked::before {
    content: "✓";
    position: absolute;
    color: white;
    font-size: 14px;
    font-weight: bold;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

/* Focus state to match other inputs */
.form-check-input[type="radio"]:focus {
    border-color: #007bff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    outline: none !important;
}
</style>

<?
$t->bottom();
?>
