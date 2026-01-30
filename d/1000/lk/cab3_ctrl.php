<? include "cab_top.inc.php"; ?>
<?
	if($_SERVER['REQUEST_METHOD'] === 'POST') {
		if($_POST['action']=='update_licenses') {
			$client_uid=intval($_POST['uid']);
			$months=intval($_POST['license_months']);
			$tm1= ($client_tm_end<time()) ? time() : $client_tm_end;
			$tm = strtotime("+$months months", $tm1);
			//print date("d/m/Y",$client_tm_end); exit;
			if($billing_rest>=$months) {
				if(!$db->dlookup("id","0ctrl","del=0 AND uid='$client_uid'")) { // create WWL acc
					$c=new cashier($database,$ctrl_id,$ctrl_dir);
					$c->init_company($client_uid);
					//~ $vkt=new vkt('vkt');
					//~ if($client_ctrl_id=$vkt->vkt_create_account($client_uid,false)) {
						//~ $info=$db->cards_read_par($client_uid);
						//~ $company=$info['company']." ИНН".$info['inn'];
						//~ $db->query("UPDATE 0ctrl SET company='".$db->escape($company)."' WHERE id='$client_ctrl_id'");
						//~ $client_ctrl_dir=$db->dlookup("ctrl_dir","0ctrl","id='$client_ctrl_id'");
						//~ $client_database=$vkt->get_ctrl_database($client_ctrl_id);
						//~ $db->connect($client_database);
						//~ if(!$db->dlookup("id","product","del=0 AND id=1")) {
							//~ $db->query("INSERT INTO `product` (`id`, `sku`, `price0`, `price1`, `price2`, `descr`, `term`, `source_id`, `razdel`, `tag_id`, `installment`, `fee_1`, `fee_2`, `fee_cnt`, `stock`, `senler`, `sp`, `sp_template`, `jc`, `in_use`, `vid`, `del`) VALUES
							//~ (1, '', 0, 0, 0, 'Все продукты', 0, 0, 1, 0, 0, 10, 0, 0, 0, 0, 0, '', '', 0, 0, 0)");
							//~ print "<p class='alert alert-success' >Продукт по умолчанию создан</p>";
						//~ }
						//~ if(!$db->dlookup("id","lands","del=0 AND fl_partner_land=1 AND land_num=1")) {
							//~ if(!$db->dlookup("id","lands","id=1 OR id=2")) {
								//~ $tm=time();
								//~ $website="https://for16.ru/d/$client_ctrl_dir/1";
								//~ $db->query ("
									//~ INSERT INTO `lands` (`id`, `tm`, `user_id`, `land_num`, `fl_not_disp_in_cab`, `tm_scdl`, `tm_scdl_period`, `land_url`, `land_name`, `land_txt`, `thanks_txt`, `bot_first_msg`, `land_razdel`, `land_tag`, `fl_partner_land`, `fl_disp_phone`, `fl_disp_email`, `fl_disp_comm`, `label_disp_comm`, `fl_disp_phone_rq`, `fl_disp_email_rq`, `fl_disp_city`, `fl_disp_city_rq`, `product_id`, `btn_label`, `bizon_duration`, `bizon_zachot`, `land_type`, `del`) VALUES
							//~ (1, $tm, 0, 1, 1, 0, 0, 'https://for16.ru/d/$client_ctrl_dir/1', 'Партнерская программа', '<h2 style=\"text-align: center;\"><span style=\"font-family: arial, helvetica, sans-serif; color: #236fa1;\">Примите участие в партнерской программе</span></h2>', '<h2 style=\"text-align: center;\"><span style=\"font-family: arial, helvetica, sans-serif; color: #236fa1;\">Благодарим за регистрацию!</span></h2>\r\n<p style=\"text-align: center;\"><span style=\"font-family: arial, helvetica, sans-serif;\">Ваша партнерская ссылка и доступ в личный кабинет партнера придет к вам в телеграм. Подпишитесь по кнопке ниже:</span></p>\r\n<p style=\"text-align: center;\">&nbsp;</p>', 'Еще раз благодарим за регистрацию в партнерской программе\r\n\r\nВаша партнерская ссылка : $website/?bc={{partner_code}}\r\n\r\nЛичный кабинет: {{cabinet_link}}', 0, 0, 1, 1, 1, 0, '', 1, 0, 0, 0, 0, 'Регистрация', 0, 0, 1, 0),
							//~ (2, $tm, 0, 2, 0, 0, 0, '$website', 'Сайт компании', '', '', '', 0, 0, 0, 1, 0, 0, '', 1, 0, 0, 0, 0, 'Регистрация', 0, 0, 0, 0);
							//~ (3, $tm, 0, 3, 0, 0, 0, '$website', 'Оплата продукта', '', '', '', 3, 0, 0, 1, 0, 0, '', 1, 0, 0, 0, 1, 'Оплата', 0, 0, 0, 0);
									//~ ");
									//~ print "<p class='alert alert-success' >Шаблонные лэндинги созданы</p>";
							//~ }
						//~ }
						//~ $path_files="/var/www/vlav/data/www/wwl/d/$client_ctrl_dir/tg_files";
						//~ if(!file_exists($path_files."/land_pic_1.jpg")) {
							//~ copy("/var/www/vlav/data/www/wwl/scripts/insales/land_pic_1.jpg",$path_files.'/land_pic_1.jpg');
							//~ copy("/var/www/vlav/data/www/wwl/scripts/insales/thanks_pic_1.jpg",$path_files.'/thanks_pic_1.jpg');
							//~ copy("/var/www/vlav/data/www/wwl/scripts/insales/logo.jpg",$path_files.'/logo.jpg');
							//~ print "<p class='alert alert-success' >Баннеры и логитипы скопированы</p>";
						//~ }
						//~ if(!$db->dlookup("id","vkt_send_1","id=1")) {
							//~ $db->query("INSERT INTO vkt_send_1 SET
								//~ id = 1,
								//~ tm = '".time()."',
								//~ sid = 30,
								//~ name_send = 'Оплата услуги',
								//~ msg = '" . $db->escape($msg1) . "',
								//~ del = 1");
						//~ }
						//~ if(!$db->dlookup("id","vkt_send_1","id=2")) {
							//~ $db->query("INSERT INTO vkt_send_1 SET
								//~ id = 2,
								//~ tm = '".time()."',
								//~ sid = 26,
								//~ name_send = 'Начислен кэшбэк',
								//~ msg = '" . $db->escape($msg2) . "',
								//~ del = 1");
						//~ }
						//~ if(!$db->dlookup("id","vkt_send_1","id=3")) {
							//~ $db->query("INSERT INTO vkt_send_1 SET
								//~ id = 3,
								//~ tm = '".time()."',
								//~ sid = 26,
								//~ name_send = 'Отправить QR код',
								//~ msg = '" . $db->escape($msg3) . "',
								//~ del = 1");
						//~ }
						//~ $db->connect('vkt');
					//~ }
				}
				$db->query("UPDATE 0ctrl SET tm_end='$tm' WHERE uid='$client_uid'");
				if($client_uid!=$partner_uid)
					$p->users_billing_add($user_id,1,0,$months,$comm=null);
				show_modal("Лицензии переданы успешно в количестве: $months мес.", "Передача лицензий клиенту",  "primary","?client_uid=$client_uid");
			} else {
				show_modal("На вашем аккаунте недостаточно лицезий ($billing_rest), а нужно $months","Передача лицензий клиенту",  "warning","?client_uid=$client_uid");
			}
		}
	}
?>		
<div class="mt-5">
	<h2 title='вернуться'>
		<a href='cab3.php' class='' target=''>
			<img src='https://winwinland.ru/img/out.svg' alt=''>
		</a>
	</h2>
</div>
<?include "cab3_client_info.inc.php";?>

<!-- License Management Modal -->
<div class="modal fade" id="licenseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Управление лицензиями</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="?client_uid=<?=$client_uid?>">
                <div class="modal-body">
                    <!-- Client Info Summary -->
                    <div class="alert alert-info">
                        <h6 class="mb-1">Клиент: <strong><?= htmlspecialchars($arr['contact_person'] ?? '') ?></strong></h6>
                        <small>Компания: <?= htmlspecialchars($arr['company'] ?? '') ?></small>
                    </div>

                    <!-- Current License Status -->
                    <div class="current-license mb-4">
                        <h6>Текущий статус:</h6>
                        <div class="card">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Лояльность 2.0</span>
                                    <span class="badge <?= $arr['has_wwl_acc'] ? 'badge-success' : 'badge-secondary' ?>">
                                        <?= $arr['has_wwl_acc'] ? 'Активен' : 'Не активен' ?>
                                    </span>
                                </div>
                                <?php if ($arr['has_wwl_acc'] && !empty($arr['payed_till'])): ?>
                                    <div class="mt-1">
                                        <small class="text-muted">Оплачено до: <?= date('d.m.Y', $arr['payed_till']) ?></small>
                                    </div>
                                <?php elseif ($arr['has_wwl_acc']): ?>
                                    <div class="mt-1">
                                        <small class="text-warning">Нет данных об оплате</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Add Licenses Form -->
                    <div class="add-licenses">
                        <h6>Передать лицензии клиенту:</h6>
                        <div class="form-group">
                            <label for="licenseMonths">Количество месяцев</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="licenseMonths" 
                                   name="license_months" 
                                   min="1" 
                                   max="36"
                                   value="1"
                                   required>
                            <small class="form-text text-muted">Введите количество месяцев (1-36)</small>
                        </div>
                    </div>

                    <!-- Hidden fields -->
                    <input type="hidden" name="uid" value="<?= $arr['uid'] ?>">
                    <input type="hidden" name="action" value="update_licenses">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light"  data-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-plus-circle"></i> Добавить лицензии
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function chk_licenses(uid) {
    // Show the modal
    $('#licenseModal').modal('show');
}
</script>

<? include "cab_bottom.inc.php"; ?>		
