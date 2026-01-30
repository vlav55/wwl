<?
include_once "/var/www/vlav/data/www/wwl/inc/top.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/cashier.class.php";
include "init.inc.php";
$db=new top($database,'Проведение оплаты', false);
?>
<div class='container' >
<div class='text-center mb-5' ><img src='https://winwinland.ru/img/logo/logo-200.png' style="width: 200px; height: auto;"></div>
<?
function msg($msg, $border='warning') {
	print "<div class='card border-$border p-3 mt-3' ><p>$msg</p></div>";
}

//msg( "Технические работы, попробуйте через минуту");msg( "Технические работы, попробуйте через минуту",'warning');print "</div>"; exit;
if($mob=$db->check_mob($_POST['phone'])) {
	$client_name=isset($_POST['client_name']) ? mb_substr($_POST['client_name'],0,64) : null;
	$c=new cashier($database,$ctrl_id,$ctrl_dir);
	$pay_system="cashier";
	$client_uid=$db->dlookup("uid","cards","del=0 AND mob_search='$mob'");
	$sum= intval($_POST['sum_disp']) ? intval($_POST['sum_disp']) : 0;
	$tm_chk=time()-(5*60);
	if($client_uid && $tm=$db->dlookup("tm","avangard","vk_uid='$client_uid' AND res=1 AND amount='$sum' AND tm>'$tm_chk'")) {
		//~ msg( "Оплата уже проведена в ".date("d.m.Y H:i:s",$tm),'warning');
		//~ $db->bottom();
		//~ print "<h2 title='вернуться'><a href='cashier.php' class='' target=''><img src='https://winwinland.ru/img/out.svg' alt=''></a></h2>";
		//~ exit;
	}
	$vk_uid=$client_uid; // may be need for pay_common

	include_once "/var/www/vlav/data/www/wwl/inc/pay_common.1.inc.php"; //klid and vk_uid defined here

	$db->query("UPDATE avangard SET res=1 WHERE id='$avangard_id'");
	$promocode=$db->dlookup("promocode","promocodes","id='$promocode_id'");
	$p_name=$db->dlookup("name","cards","id='$klid'")." ".$db->dlookup("surname","cards","id='$klid'");
	$p_uid=$db->dlookup("uid","cards","id='$klid'");
	$db->tag_add($vk_uid,1); //vk_uid - come from include

	msg( "Оплата проведена успешно от <b>$client_name</b> на сумму <b>$sum р.</b> по промокоду <b>$promocode</b>",'primary');

	if($klid) {
		//~ if($c->send_cashback_notice($klid))
			//~ $msg_="и отправлено об этом уведомление в whatsapp";
		//~ else
			//~ $msg_="Уведомление в whatsapp отправить не удалось";
		//~ msg( "Владельцу карты <b>$p_name</b> ($klid) начислен кэшбэк в размере <b>".$c->last_fee($klid, $minutes_from_now=5)."₽</b> $msg_");
		$res=$c->send_cashback_notice($klid);
		print "<div class='card p-3 my-3 border-primary' ><div>Владельцу карты <b>$p_name</b> ($klid)
				начислен кэшбэк в размере <b>".$c->last_fee($klid, $minutes_from_now=5)."₽</b>
					</div>
				</div>";
	//$c->notify_me("HERE_$res");
		if($res==1) {
			$msg_res="<p class='alert alert-success' >Сообщение на $mob</p>";
			$cashback_notice= "<p class='alert alert-success' >Сообщение для $mob успешно отправлено</p>";
		} elseif($res==2) {
			$cashback_notice= "<div class='card p-3 my-3  border-danger text-primary' >Нельзя повторить операцию по тому же номеру раньше, чем через 1 минуту. Попробуйте позже</div>";
		} elseif($res==3) {
			$url = "https://t.me/$tg_bot_msg_name?start=".$c->uid_md5($p_uid);
			$cashback_notice = "<div class='card p-3 my-3 border-danger text-black'>
				Не удалось отправить уведомление о кэшбэке для $mob, так как у него не подключен телеграм бот.
				Для подключения попросите клиента пройти по ссылке: 
				<div class='input-group input-group-sm mt-2'>
					<input type='text' class='form-control' value='$url' readonly id='copyUrl'>
					<div class='input-group-append'>
						<button class='btn btn-outline-secondary btn-sm' type='button' onclick='document.getElementById(\"copyUrl\").select();document.execCommand(\"copy\");this.innerHTML=\"<i class=fa fa-check></i>\";setTimeout(()=>this.innerHTML=\"<i class=fa fa-copy></i>\",1500)'>
							<i class='fa fa-copy'></i>
						</button>
					</div>
				</div>
			</div>";
		} elseif($res==5) { //trial
			$msg_res="<p class='alert alert-warning' >Не удалось отправить на $mob</p>";
			$cashback_notice= "<div  class='card p-3 my-3 border-warning text-primary' >
			Не настроена отправка уведомлений о кэшбэках.
			<div><a href='https://winwinland.ru/pdf/winwinland_messengers_setup.pdf' class='btn btn-sm btn-primary' target='_blank'>Инструкция</a></div>
			</div>";
		} elseif($res==0) {
			$msg_res="<p class='alert alert-warning' >Не удалось отправить на $mob</p>";
			$msg_res.="<div class='card p-2 m-2 small mute' >".(print_r($c->res,true))."</div>";
			$cashback_notice= "<p class='alert alert-danger' >Для $mob НЕ УДАЛОСЬ ОТПРАВИТЬ СООБОЩЕНИЕ</p>";
		}
		foreach($_SESSION['send_msg'] AS $msg) {
			$cashback_notice.= "<div class='card p-3 my-3 bg-light shadow' >$msg_res".nl2br(htmlspecialchars($msg))."</div>";
		}
		print "<div class='card p-2 my-3' >
			<h5>Отправка уведомления о кэшбэке</h5>
			$cashback_notice
			</div>";
	} else {
		msg( "Использовался промокод владельца карты, кэшбэк за собственные покупки не начисляется");
	}

	if($mob && isset($_POST['fl_send_loyalty_card'])) {
		//~ if($c->send_loyalty_card($mob,$client_name))
			//~ msg( "Для <b>$mob</b> создан и отправлен в whatsapp личный промокод для распространения - <b>".$c->get_promocode()."</b>");
		//~ else
			//~ msg( "Для <b>$mob</b> НЕ УДАЛОСЬ ОТПРАВИТЬ ПРОМОКОД.",'warning');

		$res=$c->send_loyalty_card($mob,$client_name);
		if($res===true)
			$res=1;
		if($res==1) {
			$msg_res="<p class='alert alert-success' >Сообщение на $mob</p>";
			$msg_issue_card_confirmation= "<p class='alert alert-success' >Для $mob выдана и отправлена в whatsapp карта лояльности 2.0 (QR код на скидку)</p>";
		} elseif($res==2) {
			$msg_issue_card_confirmation= "<div class='card p-3 my-3  border-danger text-primary' >Нельзя повторить операцию по тому же номеру раньше, чем через 1 минуту. Попробуйте позже</div>";
		} elseif($res==3) {
			$msg_issue_card_confirmation= "<div class='card p-3 my-3  border-danger text-primary' >
				У клиента ($mob) не подключен телеграм бот и ему не удалось отправить карту лояльности.<br>
				<a href='cashier.php' class='btn btn-primary btn-sm' target=''>Отправить</a>
				</div>";
		} elseif($res==5) { //trial
			$msg_res="<p class='alert alert-warning' >Не удалось отправить на $mob</p>";
			$msg_issue_card_confirmation= "<div  class='card p-3 my-3 border-warning text-primary' >
			Не настроена отправка карт лояльности (промокодов).
			<div><a href='https://winwinland.ru/pdf/winwinland_messengers_setup.pdf' class='btn btn-sm btn-primary' target='_blank'>Инструкция</a></div>
			</div>";
		} else {
			$msg_res="<p class='alert alert-warning' >Не удалось отправить на $mob</p>";
			$msg_issue_card_confirmation= "<p class='alert alert-danger' >Для $mob НЕ УДАЛОСЬ ОТПРАВИТЬ КАРТУ</p>";
		}
		foreach($_SESSION['send_msg'] AS $msg) {
			$msg_issue_card_confirmation.= "<div class='card p-3 my-3 bg-light shadow' >$msg_res".nl2br(htmlspecialchars($msg))."</div>";
		}
		print "<div class='card p-2 my-3' >
			<h5>Отправка карты лояльности (промокода)</h5>
			$msg_issue_card_confirmation
			</div>";
	}
} else
	print "<p class='alert alert-danger' >Ошибка: некорректный мобильный ($mob)</p>";

print "<p class='mt-3'  title='вернуться'><a href='cashier.php' class='btn btn-primary' target=''>Вернуться</a></p>";
print "</div>";
$db->bottom();
?>
