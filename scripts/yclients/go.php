<?
//~ print file_get_contents("https://winwinland.ru/tube/Promo/loyalty_20_preza"); exit;
//~ print file_get_contents("https://for16.ru/d/169655664/cashier_setup.php?u=1ef3411bdb1e5d3c05b1c82b8a60b654"); exit;
include "/var/www/vlav/data/www/wwl/inc/yclients.class.php";
include "/var/www/vlav/data/www/wwl/inc/cashier.class.php";

//print_r($_GET);
//Array ( [salon_ids] => Array ( [0] => 1504761 ) [user_data] => ) IF 

//~ $y=new yclients($_GET['salon_id']);
//~ print_r($y->user_data_decode($_GET['user_data'], $_GET['user_data_sign']));
//~ exit;

if(isset($_GET['install_app'])) { //install for trial 5 days
	$uid=intval($_GET['uid']);
	$vkt=new vkt('vkt');
	$data=$vkt->cards_read_par($uid);
	$company=$data['company'];
	$salon_id=$data['yclients'];
	$c=new cashier(false,false,false);
	if(!$ctrl_id=$c->dlookup("id","0ctrl","del=0 AND uid='$uid'")) { // create WWL acc
		$ctrl_id=$c->init_company($uid); //create vkt account with all lands and others init
		$c->notify_me("+ yclients go.php wwl acc created ctrl_id=$ctrl_id");
	}
	if($ctrl_id && !$c->get_init_pars()) {
		$vkt->notify_me("- yclients go.php error ctrl_id=$ctrl_id : $c->get_init_pars()==false");
	}
	$vkt->ctrl_days_end_set($ctrl_id,$days=5);
	$vkt->query("UPDATE 0ctrl SET company='".$vkt->escape($company)."', company_data='".json_encode($data)."' WHERE id='$ctrl_id'");
	chdir($vkt->get_ctrl_path($ctrl_id));
	include("init.inc.php");
	$c=new cashier($database,$ctrl_id,$ctrl_dir);
	//$c->print_r($data);
	$c->ctrl_tool_set($ctrl_id,'yclients','salon_id',$salon_id);
	$cashier_setup_url=$c->get_cashier_setup_url();
	//print "HERE_install_app $uid $salon_id $ctrl_id $ctrl_dir ".getcwd()."<br> $cashier_setup_url";
	$c->notify_me("+ yclients go.php app completely installed and wwl account created");
	$tg_bot_notif=$c->dlookup("tg_bot_notif","0ctrl","id=1");
	$DB200="https://for16.ru/d/1000";
	$c->notify($uid,"Создал аккаунт WWL из yclients - готов к работе");
//	include "cashier_setup.php";
}

if(isset($_GET['salon_ids'])) { //Array ( [salon_ids] => Array ( [0] => 1504761 ) [user_data] => )
	$_GET['salon_id']=$_GET['salon_ids'][0];
	$_GET['user_data']=$_GET['salon_ids']['user_data'];
	$_GET['user_data_sign']=$_GET['salon_ids']['user_data_sign'];
}

if(isset($_GET['salon_id']) || isset($salon_id) ) {
	$salon_id=isset($_GET['salon_id']) ? intval($_GET['salon_id']) : $salon_id;
	
	$y=new yclients($salon_id);
	if(!$uid=$y->get_uid_yclients()) { //is app confirmed and fixed in cards_add, but ctrl acc not created yet by $_GET['install_app'] button press
		if(!$user_data=$y->user_data_decode($_GET['user_data'], $_GET['user_data_sign'])) {
			$y->notify_me("yclients go.php wrong user_data_sign"); exit;
		}
		if($y->install($salon_id,['https://for16.ru/scripts/yclients/callback.php'])) { //send api confirmation to yclients first install event
			//~ Array
			//~ (
				//~ [id] => 13485189
				//~ [name] => VLADIMIR AVSHTOLIS
				//~ [phone] => 79119841012
				//~ [email] => vlav@mail.ru
				//~ [is_approved] => 1
				//~ [avatar] => https://be.cdn.yclients.com/images/no-master.png
				//~ [salon_name] => АО "ВИНВИНЛЭНД"
			//~ )
			$par=[
				'first_name'=>$user_data['name'],
				'phone'=>$user_data['phone'],
				'email'=>$user_data['email'],
				'comm1'=>"YCLIENTS | ".$user_data['salon_name']." | $salon_id",
				];
			$uid=$y->cards_add($par,false);
			$user_data['uid']=$uid;
			$user_data['tm']=time();
			$y->set_uid_yclients($uid,$user_data);
			$y->save_comm($uid,0,"Установил приложение в yclients",0,$salon_id);
			$y->tag_add($uid,43);

			$tg_bot_notif=$y->dlookup("tg_bot_notif","0ctrl","id=1");
			$DB200="https://for16.ru/d/1000";
			$y->notify($uid,"Установил приложение в yclients, но аккаунт WWL еще не создал. Для этого надо нажать кнопку -Установить-");
//print "HERE_123_$uid <br>";
		}
//print "HERE_124_$uid <br>";
	}
//print "HERE_125_$uid <br>";
	
	if(!$ctrl_id=$y->get_wwl_acc($uid)) {
		include "go_new.inc.php";
	} else {
		$y->install($salon_id);
		$tm_end=$y->tm_end_licence($ctrl_id);
		if($tm_end<time()) {
			?>
			<?include "top.inc.php"; ?>
			<div class='card my-5 p-3 text-center' >
			<p class='' >Приложение WinWinLand Лояльность 2.0 установлено, но подписка на сервис закончилась (<?="$uid $ctrl_id"?>)</p>
			<p class='alert alert-warning' >Дата окончания подписки: <b><?=date("d.m.Y",$tm_end)?></b>. <br>
			Продлить подписку можно <a href='https://winwinland.ru/loyalty20/?uid=<?=$y->uid_md5($uid)?>#rates' class='' target='_blank'>по ссылке</a> либо обратитесь к вашему маркетологу.
			</p>
			<p>Обратиться в службу клиентской поддержки WinWinLand можно <a href='https://ask.winwinland.ru/?from=<?=$y->uid_md5($uid)?>' class='' target='_blank'>по ссылке</a>.
			</p>
			</div>
			<?include "bottom.inc.php";?>
			<?
		} else {
			chdir($y->get_ctrl_path($ctrl_id));
			include("init.inc.php");
			$c=new cashier($database,$ctrl_id,$ctrl_dir);
			//$c->print_r($data);
			$cashier_setup_url=$c->get_cashier_setup_url();
			?>

			<?include "top.inc.php"; ?>
			<div class='card my-5 p-3 text-center' >
			<p class='' >Приложение WinWinLand Лояльность 2.0 установлено и работает (<?="$uid $ctrl_id"?>)</p>
			<p>Дата окончания подписки: <b><?=date("d.m.Y",$tm_end)?></b>. <br>
			Продлить подписку можно <a href='https://winwinland.ru/loyalty20/?uid=<?=$y->uid_md5($uid)?>#rates' class='' target='_blank'>по ссылке</a> либо обратитесь к вашему маркетологу.
			</p>
			<p>Обратиться в службу клиентской поддержки WinWinLand можно <a href='https://ask.winwinland.ru/?from=<?=$y->uid_md5($uid)?>' class='' target='_blank'>по ссылке</a>.
			</p>
			<p>
				<a href='<?=$cashier_setup_url?>' class='btn btn-warning mr-2 my-1' target='_blank'>Настройка</a>
				<a href='<?=$c->get_cashier_url()?>' class='btn btn-warning my-1' target='_blank'>Приложение кассира</a>
				<a href='https://help.winwinland.ru/docs-category/loyalty-20/' target='_blank' class='my-1' >
					<i class="fa fa-question-circle ml-2 text-muted" title="Справка по настройкам"></i>
				</a>
			</p>
			</div>
			<?include "go_work.inc.php";?>
			<?include "bottom.inc.php";?>

			<?
		}
	}
}


?>
