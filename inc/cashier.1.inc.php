<?
include_once "/var/www/vlav/data/www/wwl/inc/top.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/cashier.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/yclients.class.php";

if($is_yclients_request = empty(array_diff(['salon_id','hash','entity_type','entity_id','user_id'], array_keys($_GET)))) {
	$salon_id=intval($_GET['salon_id']);
	$y=new yclients($salon_id);
	$ctrl_id=$y->get_ctrl_id_yclients();
	$ctrl_dir=$y->get_ctrl_dir($ctrl_id);
	$database=$y->get_ctrl_database($ctrl_id);
	chdir("/var/www/vlav/data/www/wwl/d/".$ctrl_dir);
	$db=new db($database);
	$direct_code=$db->dlookup("direct_code","users","klid='100'");
	$_GET['u']=$direct_code;
	//$y->notify_me("HERE $direct_code $salon_id $ctrl_id $ctrl_dir $database");
}

include "init.inc.php";
$c=new cashier($database,$ctrl_id,$ctrl_dir);
//$c->notify_me("HERE_".print_r($_GET,true));


$_SESSION['csrf_token_order'] = bin2hex(random_bytes(32)); // Unique token

if(isset($_GET['p']) && !empty($_GET['p']))
	$_POST['promocode']=$_GET['p'];
$land_num=(isset($_GET['l']) && intval($_GET['l'])) ? intval($_GET['l']) : 3;

$product_id=1; //(isset($_GET['pid']) && intval($_GET['pid'])) ? intval($_GET['pid']) : $db->dlookup("product_id","lands","del=0 AND land_num='$land_num'");

$title=($land_num)?$db->dlookup("land_name","lands","land_num='$land_num'"):'Проверка промокода';
$descr=$title;
$og_url="";
$favicon="https://for16.ru/images/favicon.png";
$thanks_pic=(file_exists("tg_files/thanks_pic_$land_num.jpg"))?"<img src='tg_files/thanks_pic_$land_num.jpg' class='img-fluid' >":"";

if(empty($thanks_pic)) {
	$thanks_pic=(file_exists("tg_files/logo200x50.jpg"))?"<img src='tg_files/logo200x50.jpg' class='img-fluid' >":"";
}

$sum=isset($_POST['sum']) ? intval($_POST['sum']) : 0;
$prefix=$c->get_prefix();
$promocode=isset($_POST['promocode']) ? mb_substr(trim($_POST['promocode']),0,128) : "";
if (!empty($prefix) && strpos($promocode, $prefix) === 0)
    $promocode = trim(substr($promocode, strlen($prefix)));
//$c->notify_me("HERE_$promocode");
//~ $mob=isset($_POST['mob']) ? $db->check_mob($_POST['mob']) : $db->check_mob($_GET['mob']);
//~ if(!$mob)
	//~ $mob=isset($_POST['mob1']) ? $db->check_mob($_POST['mob1']) : $db->check_mob($_GET['mob1']);

$mob_sources = [
    $_POST['mob1'] ?? '',
    $_POST['mob'] ?? '', 
    $_GET['mob'] ?? '',
    $_GET['mob1'] ?? ''
];
foreach($mob_sources as $source) {
    if($mob = $db->check_mob($source)) {
        break;
    }
}

$client_name=isset($_POST['client_name']) ? mb_substr($_POST['client_name'],0,64) : mb_substr($_GET['client_name'],0,64);

$class_promo=empty($promocode) &&  $_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['issue_card'])? "danger-placeholder text-danger" : "";
$class_sum=!$sum && $_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['issue_card']) ? "danger-placeholder text-danger" : "";
$class_mob=empty($mob) && $_SERVER['REQUEST_METHOD'] == 'POST' ? "danger-placeholder text-danger" : "";

$uid= ($mob) ?$db->dlookup("uid","cards","mob_search='$mob'") : false;

$sum_disp=0;
$partner_name="";
$promocode_id=0;
$promocode_ok=false;
if(!empty($promocode) && isset($_POST['chk'])) {
	if($sum && $mob) {
		if($sum_disp=$db->promocode_apply($prefix.$promocode,$sum,$product_id)) {
				$promocode_msg= "<div class='text-primary small' >Промокод <b>".$prefix.$promocode."</b> применен! Скидка ".($sum-$sum_disp)."₽</div>";
				$promocode_ok=true;
				$r=$db->fetch_assoc($db->query("SELECT *,promocodes.id AS promocode_id FROM promocodes
												JOIN cards ON cards.uid=promocodes.uid
												WHERE promocode LIKE '".$prefix.$promocode."'"));
				$partner_name=$r['name']." ".$r['surname'];
				$promocode_id=$r['promocode_id'];

			if($c->get_no_discount_for_owner() && $uid==$r['uid']) {
				$promocode_msg="<div class='text-danger small' >Это владелец карты. Промокод <b>".$prefix.$promocode."</b> по карте не действует для владельца!</div>";
				$sum_disp=0;
			}
		} else {
			$promocode_msg="<div class='text-danger small' >Промокод <b>".$prefix.$promocode."</b> не найден</div>";
		}
	} elseif(!$sum) {
		$promocode_msg="<div class='alert alert-danger mt-1 small' >Укажите стоимость <i class='fa fa-hand-o-down'></i></div>";
		print "<script>document.addEventListener('DOMContentLoaded', function() {
				var sumInput = document.getElementById('__sum');
				sumInput.focus();
				sumInput.select();
			});
			</script>";
	} elseif(!$mob) {
		$promocode_msg="";//"<div class='alert alert-danger mt-1 small' >Укажите телефон клиента <i class='fa fa-hand-o-down'></i></div>";
		print "<script>document.addEventListener('DOMContentLoaded', function() {
				var mobInput = document.getElementById('mob');
				mobInput.focus();
				mobInput.select();
			});
			</script>";
	}
}
if(isset($_POST['issue_card'])) {
	if($mob) {
       echo "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#issue_card_confirmationModal').modal('show');
        });
        </script>";
	}
}
if(isset($_GET['confirm_issue_card'])) { //disp messages
	$res=intval($_GET['res']);
	if($res==1) {
		$msg_res="<p class='alert alert-success' >Сообщение на $mob</p>";
		$msg_issue_card_confirmation= "<p class='alert alert-success' >Для $mob выдана и отправлена в $db->transport карта лояльности 2.0 (QR код на скидку)</p>";
	} elseif($res==2) {
		$msg_issue_card_confirmation= "<div class='card p-3 my-3  border-danger text-primary' >Нельзя повторить операцию по тому же номеру раньше, чем через 1 минуту. Попробуйте позже</div>";
	} elseif($res==5) { //trial
		$msg_res="<p class='alert alert-warning' >Не удалось отправить на $mob</p>";
		$msg_issue_card_confirmation= "<div  class='card p-3 my-3 border-warning text-primary' >
		Не настроена отправка карт лояльности (промокодов) клиентам, а также уведомлений о кэшбэках.
		<div><a href='https://winwinland.ru/pdf/winwinland_messengers_setup.pdf' class='btn btn-sm btn-primary' target='_blank'>Инструкция</a></div>
		</div>";
	}
	foreach($_SESSION['send_msg'] AS $msg) {
		$msg_issue_card_confirmation.= "<div class='card p-3 my-3 bg-light shadow' >$msg_res".nl2br(htmlspecialchars($msg))."</div>";
	}
}
if(isset($_POST['confirm_issue_card'])) {
	if($mob) {
		$res=$c->send_loyalty_card($mob,$client_name);
		if(!$res) {
			$msg_res="<p class='alert alert-warning' >Не удалось отправить на $mob</p>";
			$msg_res.="<div class='card p-2 m-2 small mute' >".(print_r($c->res,true))."</div>";
			$msg_issue_card_confirmation= "<p class='alert alert-danger' >Для $mob НЕ УДАЛОСЬ ОТПРАВИТЬ КАРТУ</p>";
		} elseif($res===3) {
			$msg_issue_card_confirmation= "<p class='alert alert-warning' >Для $mob НЕ ПОДКЛЮЧЕН ТЕЛЕГРАМ БОТ</p>";
		} else {
			header("Location: ?active_tab_2=yes&res=$res&confirm_issue_card=yes&mob=$mob");
			//print "<script>window.location.href = '?active_tab_2=yes&res=$res&confirm_issue_card=yes';</script>";
		}
	}
}
if(isset($_POST['withdraw_amount'])) {
	$klid=intval($_POST['klid']);
	$error_withdraw_amount=true;
	$msg_yclients="";
	$sum=intval($_POST['withdraw_amount']);
	$p_uid=$c->dlookup("uid","cards","id='$klid'");
	if(!$c->get_yclients_withdraw_cashback()) {
		$cashback_amount = $c->rest_fee($klid);
		$db->notify_me("sum=$sum cashback_amount=$cashback_amount"); exit;
		if($sum>$cashback_amount)
			$sum=$cashback_amount;
		$tm=$c->dlast("tm","partnerka_pay","klid=$klid AND sum_pay=$sum");
		if(time()>($tm+(5*60)) ) {
			$c->pay_fee($klid,$sum,$vid=1,$comm='списано кассиром');
			$c->save_comm($p_uid,0,"Выведен кэшбэк партнерские",28);
			$msg="С вашей карты списан кэшбэк в размере $sum р. Остаток: ".$c->rest_fee($klid)." р.";
			$c->send_wa($p_uid,$msg);
			$error_withdraw_amount=false;
		}
	} else {
		if($c->withdraw_cashback_to_yclients($klid, $sum)) {
			$msg="Вам зачислен кэшбэк размере $sum р. на вашу карту";
			$c->send_wa($p_uid,$msg);
			$msg_yclients="<p class='text-primary' >Кэшбэк выведен на карту в yclients</p>";
			$error_withdraw_amount=false;
		}
	}
	if(!$error_withdraw_amount) {
    ?>
		<div class="alert alert-success text-center">
			<i class="fa fa-check-circle fa-3x mb-3"></i>
			<p class="h4">Кэшбэк успешно списан</p>
			<p class="h2 text-success font-weight-bold"><?=$sum?> ₽</p>
			<p class="small">С клиента ID: <?=$klid?></p>
			<p class="small">Телефон: <?=$_POST['phone']?></p>
			<?=$msg_yclients?>
		</div>
    <?
	} else {
    ?>
		<div class="alert alert-warning text-center">
			<i class="fa fa-check-circle fa-3x mb-3"></i>
			<p class="h4">Ошибка при выводе кэшбэка на карту yclients. Кэшбэк не списан</p>
			<p class="small">С клиента ID: <?=$klid?></p>
			<p class="small">Телефон: <?=$_POST['phone']?></p>
		</div>
    <?
	}
    exit;
}

?>
<?
if(isset($_POST['check_cashback_ajax'])) {
    $phone = isset($_POST['phone']) ? $db->check_mob($_POST['phone']) : "";
    
    if($phone) {
        if(!$klid=$c->dlookup("id","cards","mob_search='$phone'")) {
			?>
			<div class="alert alert-warning text-center">
				<i class="fa fa-info-circle fa-3x mb-3"></i>
				<p class="h4">Кэшбэк не найден</p>
				<p class="small">Для телефона: <?=$phone?></p>
				<p class="small">Не найден в клиентской базе</p>
			</div>
			<?
			exit;
		}

        $cashback_amount = $c->rest_fee($klid);
        //$db->notify_me("HERE_$klid $cashback_amount $db->database");
        
		if($cashback_amount > 0) {
		?>
		<div class="alert alert-success text-center">
			<i class="fa fa-check-circle fa-3x mb-3"></i>
			<p class="h4">Доступный кэшбэк</p>
			<p class="h2 text-success font-weight-bold"><?=$cashback_amount?> ₽</p>
			<p class="small">Для телефона: <?=$phone?></p>
		<div class="card mt-3">
			<div class="card-header bg-light">
				<h5 class="card-title mb-0">Списать кэшбэк</h5>
			</div>
			<div class="card-body p-1">
				<form id="cashback_withdraw_form" method="POST" action='?'>
					<div class="form-group">
						<label for="withdraw_amount" class="font-weight-bold">Сумма:</label>
						<div class="input-group">
							<input type="number" 
								   class="form-control" 
								   id="withdraw_amount" 
								   name="withdraw_amount" 
								   min="1" 
								   max="<?=$cashback_amount?>" 
								   value="<?=$cashback_amount?>"
								   required>
							<div class="input-group-append">
								<span class="input-group-text">₽</span>
							</div>
						</div>
						<small class="form-text text-muted">
							Максимальная доступная сумма: <?=$cashback_amount?> ₽
						</small>
					</div>
					<input type="hidden" name="klid" value="<?=$klid?>">
					<input type="hidden" name="phone" value="<?=$phone?>">
					<input type="hidden" name="csrf_token_order" value="<?=$_SESSION['csrf_token_order']?>">
					<button type="submit" class="btn btn-success btn-block">
						<i class="fa fa-paper-plane mr-2"></i>Списать
					</button>
				</form>
			</div>
		</div>
		</div>
		<?
		} else {
		?>
		<div class="alert alert-warning text-center">
			<i class="fa fa-info-circle fa-3x mb-3"></i>
			<p class="h4">Кэшбэк не найден</p>
			<p class="small">Для телефона: <?=$phone?></p>
			<p class="small">У вас пока нет накопленного кэшбэка.</p>
		</div>
		<?
		}
	} else {
	?>
	<div class="alert alert-danger text-center">
		<i class="fa fa-exclamation-triangle fa-3x mb-3"></i>
		<p class="h4">Ошибка</p>
		<p>Неверный номер телефона</p>
	</div>
	<?
	}
    exit; // Important: stop execution after AJAX response
}


$sum_striked=$sum;
$sum_disp_=$sum_disp ? $sum_disp : $sum;

$t=new top($database,'Проверить промокод',false);
?>
<!-- YCLIENTS Integration Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if running inside an iframe (in YCLIENTS)
    if (window.parent !== window) {
        // We're inside an iframe - send message to YCLIENTS
        try {
            window.parent.postMessage({
                type: 'iframe_ready',
                payload: { success: true }
            }, '*');
            //console.log('iframe_ready sent to YCLIENTS');
            
            // Check after 5 seconds if still hidden
            setTimeout(function() {
                if (document.body.offsetParent === null) {
                    //console.warn('Iframe may be hidden in YCLIENTS');
                }
            }, 5000);
        } catch (error) {
            //console.error('Error sending to YCLIENTS:', error);
        }
        
        // Listen for messages from YCLIENTS
        window.addEventListener('message', function(event) {
            //console.log('Message from YCLIENTS:', event.data);
        });
    } else {
        // Running standalone - no YCLIENTS integration needed
        //console.log('Running standalone (not in YCLIENTS iframe)');
    }
});
</script>
<style>
/* Limit width on desktop screens */
@media (min-width: 481px) {
    .container-fluid {
        max-width: 480px;
        margin: 20px auto; /* 20px top/bottom indent */
        background: white;
        min-height: calc(100vh - 40px);
        box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        border-radius: 12px;
        overflow: hidden; /* Prevents content from overflowing rounded corners */
    }
    
    body {
        background: #f8f9fa;
    }
}
</style>

<style>
:root {
    --primary-color: #3774f3; 
    --primary-dark: #2d63d4;
    --primary-light: rgba(55, 116, 243, 0.1);
    --primary-border: rgba(55, 116, 243, 0.3);
}

/* Change primary color for buttons */
.btn-primary {
    background-color: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
}

.btn-primary:hover,
.btn-primary:focus,
.btn-primary:active {
    background-color: var(--primary-dark) !important;
    border-color: var(--primary-dark) !important;
}

.btn-outline-primary {
    color: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
}

.btn-outline-primary:hover,
.btn-outline-primary:focus,
.btn-outline-primary:active {
    background-color: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
    color: white !important;
}

/* Change primary color for alerts */
.alert-primary {
    background-color: var(--primary-light) !important;
    border-color: var(--primary-border) !important;
    color: var(--primary-color) !important;
}

/* For outlined alerts */
.alert-outline-primary {
    background-color: transparent !important;
    border-color: var(--primary-color) !important;
    color: var(--primary-color) !important;
}

/* Change text-primary utility class */
.text-primary {
    color: var(--primary-color) !important;
}

/* Change border-primary utility class */
.border-primary {
    border-color: var(--primary-color) !important;
}

/* Change background-primary utility class */
.bg-primary {
    background-color: var(--primary-color) !important;
}
</style>

<style>
.danger-placeholder::placeholder {
    color: #dc3545 !important;
    opacity: 1;
}
</style>

<style>
body, 
div, span, p, a, 
button:not([class*="fa"]), 
input, textarea, select, label,
h1, h2, h3, h4, h5, h6,
.btn:not([class*="fa"]), 
.form-control, 
.card, 
.modal, 
.alert {
    font-family: 'SFNSRounded', Tahoma, Verdana, Arial, sans-serif !important;
}
</style>


<style>
.nav-tab-custom {
    border: none;
    border-bottom: 3px solid transparent;
    border-radius: 0;
    padding: 0px 20px;
    text-decoration: none;
    color: #6c757d;
    font-weight: 500;
    transition: all 0.3s ease;
    background: transparent;
}
.nav-tab-custom.active {
    background-color: transparent;
    border-bottom-color: var(--primary-color);
    color: var(--primary-color);
    text-decoration: none;
}
.nav-tab-custom:hover {
    background-color: rgba(55, 116, 243, 0.05);
    border-bottom-color: rgba(55, 116, 243, 0.3);
    text-decoration: none;
    color: var(--primary-color);
}
.nav-tab-custom.active:hover {
    background-color: rgba(55, 116, 243, 0.05);
    border-bottom-color: var(--primary-dark);
    text-decoration: none;
}
</style>

<div class="container">
    <div class="d-flex align-items-center mt-0 mb-0 justify-content-between">
        <!-- Tabs on the left -->
        <?
			$active_tab_1 = "active"; 
			$active_tab_2 = "";
			$active_tab_3 = "";
			$active_content_1 = "show active";
			$active_content_2 = "";
			$active_content_3 = "";
			
			if(isset($_POST['confirm_issue_card']) || isset($_GET['active_tab_2'])) {
				$active_tab_1 = ""; 
				$active_tab_2 = "active";
				$active_tab_3 = "";
				$active_content_1 = "";
				$active_content_2 = "show active";
				$active_content_3 = "";
			}
			if(isset($_POST['support_submit'])) {
				$active_tab_1 = ""; 
				$active_tab_2 = "";
				$active_tab_3 = "active";
				$active_content_1 = "";
				$active_content_2 = "";
				$active_content_3 = "show active";
			}
        ?>
        <div class="flex-shrink-0 d-flex align-items-center">
            <div class="nav" role="tablist">
                <a class="nav-tab-custom mr-1 <?=$active_tab_1?> px-3 small" data-toggle="tab" href="#promocode-content">
                    Промокод
                </a>
                <a class="nav-tab-custom mr-1 <?=$active_tab_2?> px-3 small" data-toggle="tab" href="#client-content">
                    Клиент
                </a>
                <a class="nav-tab-custom mr-1 px-2 small <?=$active_tab_3?>" data-toggle="tab" href="#support-content">
                    <i class="fa fa-question-circle-o text-black font-weight-bold"></i>
                </a>
            </div>
        </div>
        
        <!-- Logo on the right -->
        <div class="flex-shrink-0">
            <img src='https://winwinland.ru/img/logo/logo-200.png' style="width: 80px; height: auto;" title='<?=$ctrl_id?>'>
        </div>
    </div>
</div>

<div class='container pt-0' >
    <div class="tab-content pb-0 mb-0" id="mainTabsContent">
			<div class="mb-5 pb-0 tab-pane fade <?=$active_content_1?>" id="promocode-content" role="tabpanel" aria-labelledby="promocode-tab">
			<form method='POST' action='?#p1' >
				<p class='h4 mt-0'  id='p1'>Проверка промокода</p>
				<div class="form-group mb-0">
					<label for="__mob1" class="mb-0" >Мобильный телефон</label>
					<input type="phone" class="form-control form-control-sm <?=$class_mob?>" id="__mob1" name="mob1" value="<?=$mob?>" placeholder="Мобильный"  onfocus="this.select()">
					<div class="custom-control custom-checkbox mt-1 pb-0 mb-0">
						<input type="checkbox" class="custom-control-input" id="fl_send_loyalty_card" name="fl_send_loyalty_card" value="1" <?=($_SERVER['REQUEST_METHOD'] !== 'POST' || isset($_POST['fl_send_loyalty_card'])) ? 'checked' : ''?>>
						<label class="custom-control-label small text-muted" for="fl_send_loyalty_card">
							Отправить карту клиенту
						</label>
					</div>
				</div>
				<div class="form-group mb-0">
					<label for="__sum" class="mb-0" >Стоимость</label>
					<input type="number" class="form-control form-control-sm <?=$class_sum?>" id="__sum" name="sum" value="<?=$sum?>" placeholder="Сумма"  onfocus="this.select()">
				</div>
				<div class="form-group mb-0">
					<label for="__promocode" class="mb-0">Промокод клиента</label>
					<div class="input-group input-group-sm mx-0 px-0 pt-0 mt-0">
						<div class="input-group-prepend m-0 pr-0">
							<span class="input-group-text bg-light text-muted border-right-0 m-0">
								<?=$prefix?>
							</span>
						</div>
						<input type="number" class="mx-0 form-control form-control-sm  <?=$class_promo?>" id="__promocode" name="promocode" value="<?=$promocode?>" placeholder="Промокод">
					</div>
				</div>
				<span class='' ><?=$promocode_msg?></span>
				<div class='mt-1 text-center' >
					<button type="submit" class="w-100 py-2 btn text-white border-0 gradient shadow" id="chk" name="chk" value="yes">
						<i class="fa fa-check-circle mr-2"></i>Проверить промокод
					</button>
				</div>

				<p class='h4 mt-3' >Сумма к оплате</p>
				<div class='card p-2 shadow' >
					<div class="d-flex justify-content-between align-items-center my-0">
						<span>Стоимость услуги:</span>
						<span class="font-weight-bold_"><?=$sum ?$sum.'&nbsp;р.' : '-'?></span>
					</div>
					<div class="d-flex justify-content-between align-items-center">
						<span>Промокод: <?=$promocode_ok ? "<span class='ml-2 font-weight-bold_' >".$prefix.$promocode."</span>" : ''?></span>
						<?
						    if($promocode) {
								$discount_amount = $sum - $sum_disp;
								$discount_percent = round(($discount_amount / $sum) * 100, 0);
							}
						?>
						<span class="font-weight-bold_"><?=$promocode_ok ? $discount_amount."&nbsp;р. <span class='text-primary' >(".$discount_percent."%)</span>" : "-" ?></span>
					</div>
					<hr class='my-1' >
					<div class="d-flex justify-content-between align-items-center">
						<span>Итого:</span>
						<span class="font-weight-bold"><?=$sum_disp_ ? $sum_disp_.'&nbsp;р.' : '-'?></span>
					</div>
					<?
					if($sum_disp && $mob )
						print "<button type='submit' class='btn btn-primary btn-lg m-1 gradient mb-3' id='go' name='go' value='yes'
						formaction='pay_cashier.php'>Провести оплату</button>";
					?>
				</div>
				<input type="hidden" name="product_id" value="<?=$product_id?>">
				<input type="hidden" name="land_num" value="<?=$land_num?>" />
				<input type="hidden" name="phone" value="<?=$mob?>" />
				<input type="hidden" name="fio" value="<?=$client_name?>" />
				<input type="hidden" name="sum_disp" value="<?=$sum_disp?>" />
				<input type="hidden" name="promocode_id" value="<?=$promocode_id?>" />
				<input type="hidden" name="csrf_token_order" value="<?=$_SESSION['csrf_token_order']?>" />
			</form>
			</div>
			
			<div class="tab-pane fade <?=$active_content_2?>" id="client-content" role="tabpanel" aria-labelledby="client-tab">
				<p class='mb-0 h4'  for="client_name">Клиент</p>
				<?
					if($msg_issue_card_confirmation) print $msg_issue_card_confirmation;
				?>
				<form method='POST' action='?'>
					<div class="form-group mb-1">
						<label for="client_name" class="mb-0" >Имя клиента</label>
						<input type="text" class="form-control form-control-sm" id="client_name" name="client_name" value="<?=$client_name?>" placeholder="Имя">
					</div>
					<div class="form-group mb-1">
						<label for="mob" class="mb-0" >Мобильный телефон</label>
						<input type="phone" class="form-control form-control-sm <?=$class_mob?>" id="mob" name="mob" value="<?=$mob?>" placeholder="Телефон *">
					</div>
					<div class="row w-100 mx-0">
						<div class="col-6 px-1">
							<button type="button" class="btn btn-outline-primary w-100 btn-sm small mb-1" id="issue_card" name="issue_card" value="yes" data-toggle="modal" data-target="#issue_card_confirmationModal">
								<i class="fa fa-id-card-o" aria-hidden="true"></i>
								Выдать<br>карту
							</button>
						</div>
						<div class="col-6 px-1">
							<button type="button" class="btn btn-outline-secondary w-100 btn-sm small mb-1" id="check_cashback" name="check_cashback" value="yes" data-toggle="modal" data-target="#check_cashback_modal">
								<i class="fa fa-credit-card" aria-hidden="true"></i>
								Проверить<br>кэшбэк
							</button>
						</div>
					</div>
				</form>
			</div>
			<div class="tab-pane fade <?=$active_content_3?>" id="support-content" role="tabpanel" aria-labelledby="client-tab">
				<p class='mb-0 h4'  for="client_name">Техподдержка</p>
				<p class='small mute text-left' >(<?=$ctrl_id?>)</p>
				<?
				if(isset($_POST['support_submit'])) {
					if(!empty(trim($_POST['msg']))) {
						$msg=mb_substr(trim($_POST['msg']),0,1024);
						$name=mb_substr(trim($_POST['client_name']),0,128);
						$mob=mb_substr(trim($_POST['mob']),0,32);
						$salon_id_=intval($_POST['salon_id']);
						
						$c->notify_chat(-4799845674,"Вопрос из приложения кассира ($ctrl_id $salon_id_)\n$name $mob\n". $msg);
						print "<p class='alert alert-primary' >Ваше сообщение отправлено, с вами свяжемся по данному вопросу в ближайшее время</p>";
					} else
						print "<p class='alert alert-warning' >Введите сообщение</p>";
				}
				?>
				<form method='POST' action='?'>
					<div class="form-group mb-1">
						<label for="msg" class="mb-0" >Сообщение</label>
						<textarea class="form-control form-control-sm " id="msg" name="msg" value="" placeholder="" rows='5'></textarea>
					</div>
					<div class="form-group mb-1">
						<label for="mob" class="mb-0" >Телефон для связи</label>
						<input type="phone" class="form-control form-control-sm " id="mob" name="mob" value="" placeholder="Телефон *">
					</div>
					<div class="form-group mb-1">
						<label for="client_name" class="mb-0" >Ваше имя</label>
						<input type="text" class="form-control form-control-sm" id="client_name" name="client_name" value="" placeholder="Имя">
					</div>
					<button type="submit" class="mt-1 ml-2 btn btn-outline-primary btn-sm small mb-1" id="support_submit" name="support_submit" value="yes">
						<i class="fa fa-support" aria-hidden="true"></i>
						Отправить
					</button>
					<input type='hidden' name='ctrl_id' value='<?=$ctrl_id?>' >
					<input type='hidden' name='salon_id' value='<?=$salon_id?>' >
					<input type='hidden' name='cashier_id' value='<?=$_SESSION['userid_sess']?>' >
				</form>
			</div>
	</div>
</div>
<?

if(empty($class_sum) && empty($class_promo) && !empty($class_mob)) {
	?>
	<script>document.addEventListener('DOMContentLoaded', function() {
			var sumInput = document.getElementById('mob');
			sumInput.focus();
			sumInput.select();
		});
	</script>
	<?
}

?>
<!-- Modal for issue_card_confirmationModal-->
<div class="modal fade" id="issue_card_confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="issue_card_confirmationModal">
                    <i class="fa fa-check-circle mr-2"></i>Подтверждение выпуска карты
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fa fa-id-card-o text-success fa-3x mb-3"></i>
                    <p class="h5">Выпустить карту лояльности 2.0 для</p>
                    <p class="h4 text-success font-weight-bold" id="modal-client-name">-</p>
                    <p class="h4 text-success font-weight-bold" id="modal-client-phone">-</p>
                    <p class="h5">и отправить ее клиенту?</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Отмена</button>
                <form method='POST' action='?' style="display: inline;">
                    <input type="hidden" name="client_name" value="">
                    <input type="hidden" name="mob" value="">
                    <input type="hidden" name="confirm_issue_card" value="yes">
                    <input type="hidden" name="product_id" value="1">
                    <input type="hidden" name="land_num" value="3">
                    <input type="hidden" name="csrf_token_order" value="<?=$_SESSION['csrf_token_order']?>">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-check mr-1"></i>Да, выпустить
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    // Handle issue_card button click
    $('#issue_card').click(function(e) {
        var clientName = $('#client_name').val();
        var mob = $('#mob').val(); // Changed from #__mob to #mob
        
        // Validate phone field
        if (!mob) {
            // Focus on phone field and add red styling
            $('#mob').addClass('danger-placeholder text-danger') // Changed from #__mob to #mob
                      .focus()
                      .select();
            e.preventDefault(); // Prevent modal from opening
            return false;
        } else {
            // Remove red styling if phone is entered
            $('#mob').removeClass('danger-placeholder text-danger'); // Changed from #__mob to #mob
        }
        
        // Update modal content with form values
        $('#modal-client-name').text(clientName || 'Не указано');
        $('#modal-client-phone').text(mob);
        
        // Update hidden form fields in modal
        $('#issue_card_confirmationModal input[name="client_name"]').val(clientName);
        $('#issue_card_confirmationModal input[name="mob"]').val(mob);
    });
    
    // Remove red styling when user starts typing in phone field
    $('#mob').on('input', function() { // Changed from #__mob to #mob
        if ($(this).val()) {
            $(this).removeClass('danger-placeholder text-danger');
        }
    });
});
</script>

<!-- Modal for checking cashback -->
<div class="modal fade" id="check_cashback_modal" tabindex="-1" role="dialog" aria-labelledby="checkCashbackModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-info">
            <div class="modal-header text-right p-1 justify-content-end">
				<button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal" id="close_cashback_modal">Закрыть</button>
            </div>
            <div class="modal-body">
                <div id="cashback_result">
                    <div class="text-center">
                        <i class="fa fa-spinner fa-spin fa-3x mb-3 text-info"></i>
                        <p class="h5">Проверяем кэшбэк для телефона:</p>
                        <p class="h4 text-info font-weight-bold" id="cashback_phone_display"></p>
                        <p>Пожалуйста, подождите...</p>
                    </div>
                </div>
            </div>
<!--
            <div class="modal-footer">
			</div>
-->
        </div>
    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="disp_msg_modal" tabindex="-1" role="dialog" aria-labelledby="disp_msg_modal_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="disp_msg_modal_label">Уведомление</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="disp_msg_modal_body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<script>
function disp_msg_modal(msg, title = 'Уведомление') {
    // Update modal content
    $('#disp_msg_modal_label').text(title);
    $('#disp_msg_modal_body').html(msg);
    
    // Show modal
    $('#disp_msg_modal').modal('show');
}
</script>

<!-- Short Link Modal -->
<div class="modal fade" id="shortLinkModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fa fa-link mr-2"></i>Получить промокод
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div class='card p-3 mb-3 border-primary bg-light' id="modalShortLinkContainer">
                    <!-- Short link will appear here -->
                </div>
                <div id="modalQrCodeContainer">
                    <!-- QR code will appear here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" onclick="copyModalLink()">
                    <i class="fa fa-copy"></i> Копировать
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showShortLinkModal(shortLink, qrCodeImage = '', phoneNumber = '') {
    // Set short link
    let message = phoneNumber 
        ? `<p>Промокод для номера ${phoneNumber} будет отправлен в телеграм по ссылке:</p>`
        : `<p>Промокод будет отправлен в телеграм по ссылке:</p>`;
    $('#modalShortLinkContainer').html(`
		 ${message}
        <input type="text" class="form-control border-0 text-center bg-light" 
               id="modalShortLink" value="${shortLink}" readonly
               style="font-size: 14px;">
        <small class="text-muted mt-1 d-block">
            <i class="fa fa-info-circle"></i> Действует 30 дней
        </small>
    `);
    
    // Set QR code
    if (qrCodeImage) {
        $('#modalQrCodeContainer').html(`
            <img src="${qrCodeImage}" class="img-fluid mt-3" style="max-width: 200px;">
            <p class="small text-muted mt-2">
                <i class="fa fa-qrcode"></i> Отсканируйте QR-код
            </p>
        `);
    } else {
        $('#modalQrCodeContainer').html('');
    }
    
    // Show modal
    $('#shortLinkModal').modal('show');
}

function copyModalLink() {
    var link = 'Для получения промокода подключите телеграм по ссылке: '+$('#modalShortLink').val();
    navigator.clipboard.writeText(link).then(() => {
        var btn = $('#shortLinkModal .btn-primary');
        var original = btn.html();
        btn.html('<i class="fa fa-check"></i> Скопировано');
        setTimeout(() => btn.html(original), 1500);
    });
}
</script>

<script>
$(document).ready(function() {
    // Handle withdraw form submission via AJAX
    $(document).on('submit', '#cashback_withdraw_form', function(e) {
        e.preventDefault(); // Prevent normal form submission
        
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-2"></i>Обработка...');
        
        $.ajax({
            type: 'POST',
            url: '', // Current file
            data: form.serialize(),
            success: function(response) {
                $('#cashback_result').html(response);
            },
            error: function() {
                $('#cashback_result').html(
                    '<div class="alert alert-danger text-center">' +
                    '<i class="fa fa-exclamation-triangle fa-2x mb-2"></i>' +
                    '<p>Ошибка при списании кэшбэка. Попробуйте еще раз.</p>' +
                    '</div>'
                );
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>

<script>
$(document).ready(function() {
    // Handle close button click
    $('#close_cashback_modal').on('click', function() {
        var phone = $('#mob').val();
        //location.href = '?mob=' + encodeURIComponent(phone);
    });
    // When check_cashback modal is shown, make the AJAX call
    $('#check_cashback_modal').on('show.bs.modal', function (e) {
        var phone = $('#mob').val();
        $('#cashback_phone_display').text(phone);
        
        $.ajax({
            type: 'POST',
            url: '', // Current file
            data: {
                check_cashback_ajax: 'yes',
                phone: phone,
                csrf_token_order: '<?=$_SESSION['csrf_token_order']?>'
            },
            success: function(response) {
                $('#cashback_result').html(response);
            },
            error: function() {
                $('#cashback_result').html(
                    '<div class="alert alert-danger text-center">' +
                    '<i class="fa fa-exclamation-triangle fa-2x mb-2"></i>' +
                    '<p>Ошибка при проверке кэшбэка. Попробуйте еще раз.</p>' +
                    '</div>'
                );
            }
        });
    });
});
</script>

<!-- Minimal YCLIENTS visibility script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const yclientsOrigins = [
        'https://yclients.com',
        'https://yclients.helpdeskeddy.com'
    ];
    
    // Send to each possible YCLIENTS origin
    yclientsOrigins.forEach(origin => {
        try {
            window.parent.postMessage({
                type: 'iframe_ready',
                payload: { success: true }
            }, origin);
            //console.log('Sent to:', origin);
        } catch(e) {
            //console.log('Failed to send to:', origin);
        }
    });

    // Only send message if inside iframe
    if (window.parent !== window) {
        // Send iframe_ready message once
        window.parent.postMessage({
            type: 'iframe_ready',
            payload: { success: true }
        }, '*');
        
        //console.log('iframe_ready sent - waiting for YCLIENTS to show iframe');
        
    }
});
</script>
<?

if (isset($_SESSION['show_modal_script'])) {
    echo $_SESSION['show_modal_script'];
    unset($_SESSION['show_modal_script']);
}

$t->bottom();
?>
