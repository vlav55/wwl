<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "init.inc.php";

$db=new top($database,'Добавить в CRM',false,$favicon);
$db->telegram_bot=$TELEGRAM_BOT;
if(!isset($mob))
	$mob="";
if(!isset($name))
	$name="";
if(!isset($vk_profile))
	$vk_profile=""; else $vk_profile.="\n";
if(!isset($vk_public))
	$vk_public=""; else $vk_public.="\n";
if(!isset($comm))
	$comm="";

print "<h2>Добавить в CRM вручную</h2>";

if(isset($_GET['do_add'])) {

	if(!isset($_SESSION['add_new_tm']))
		$_SESSION['add_new_tm']=0;
	
	$r['phone']=$db->check_mob($_GET['phone']);
	$r['email']=($db->validate_email($_GET['email']))?trim($_GET['email']):"";
	$r['first_name']=trim($_GET['name']);
	$r['last_name']=trim($_GET['last_name']);
	$r['city']=trim($_GET['city']);
	$r['comm1']=trim(mb_substr($_GET['comm'],0,1024));
	$r['tg_nic']=preg_replace("/^@/", "", mb_substr(trim($_GET['telegram_nic']),0,32));
	$r['man_id']=($_SESSION['access_level']==4) ? $_SESSION['userid_sess'] : 0;

	$mob=$db->check_mob($_GET['phone']);
	$email=($db->validate_email($_GET['email']))?trim($_GET['email']):"";
	$name=trim($_GET['name']);
	$tg_nic=preg_replace("/^@/", "", mb_substr(trim($_GET['telegram_nic']),0,32));
	if(!empty($mob) && $uid=$db->dlookup("uid","cards","del=0 AND mob_search='$mob'") ) {
		print "<div class='alert alert-success' >Телефон <b>$mob</b> уже есть в базе. <a href='javascript:opener.location=\"$DB200/cp.php?str=$mob&view=yes&filter=Search\";window.close();' class='' target='_parent'>Найти в CRM</a>.</div>";
	} elseif (!empty($email) && $uid=$db->dlookup("uid","cards","del=0 AND email='$email'") ) {
		print "<div class='alert alert-success' >Email <b>$email</b> уже есть в базе. <a href='javascript:opener.location=\"$DB200/cp.php?str=$email&view=yes&filter=Search\";window.close();' class='' target='_parent'>Найти в CRM</a>.</div>";
	} elseif (!empty($tg_nic) && $uid=$db->dlookup("uid","cards","del=0 AND telegram_nic='$tg_nic'") ) {
		print "<div class='alert alert-success' >Телеграм <b>@$tg_nic</b> уже есть в базе. <a href='javascript:opener.location=\"$DB200/cp.php?str=$tg_nic&view=yes&filter=Search\";window.close();' class='' target='_parent'>Найти в CRM</a>.</div>";
	} elseif(!empty($name)) {
		$delay_sec=30;
	//	$db->notify_me("HERE_".$_SESSION['add_new_tm']);
		if((time()-$_SESSION['add_new_tm']) >$delay_sec ) {
			$user_id=$_SESSION['userid_sess'];
			$klid=$db->get_klid($user_id);
			$user_id=0;
			$klid=0;

			$uid=$db->cards_add($r,$update_if_exist=false);
	
			$uid_md5=$db->uid_md5($uid);
			$db->save_comm($uid,$_SESSION['userid_sess'],"ДОБАВИЛ ВРУЧНУЮ {$_SESSION['real_user_name']} ({$_SESSION['userid_sess']})",1);
			$db->notify($uid,"Добавлен вручную");
			$_SESSION['add_new_tm']=time();
			print "<div class='alert alert-success' >Добавлен в CRM: <b>$name $mob $email</b>. <a href='javascript:opener.location=\"$DB200/cp.php?str=$uid&view=yes&filter=Search\";window.close();' class='' target='_parent'>Найти</a></div>";
		} else {
			print "<div class='alert alert-warning' >Ошибка. Добавлять подписчиков можно не чаще, чем 1 раз в $delay_sec секунд</div>";
		}
	} else
		print "<div class='alert alert-danger' >Ошибка: Не указано имя</div>";
}

?>
<div class='container'>
    <form>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="name">Имя (*)</label>
                <input type='text' id='name' name='name' value='<?=$r['first_name']??"";?>' class='form-control'>
            </div>
            <div class="form-group col-md-6">
                <label for="last_name">Фамилия</label>
                <input type='text' id='last_name' name='last_name' value='<?=$r['last_name']??""?>' class='form-control'>
            </div>
        </div>
        
        <div class="form-row">
			<div class="form-group col-md-6">
				<label for="phone">Телефон</label>
				<input type='text' id='phone' name='phone' value='<?=$r['phone']??""?>' class='form-control'>
			</div>

			<div class="form-group col-md-6">
				<label for="email">Email</label>
				<input type='text' id='email' name='email' value='<?=$r['email']??""?>' class='form-control'>
			</div>
        </div>

		<div class="form-row">
			<div class="form-group col-md-6">
				<label for="telegram_nic">Telegram</label>
				<input type='text' id='telegram_nic' name='telegram_nic' value='<?=$r['tg_nic']??""?>' class='form-control'>
			</div>
			<div class="form-group col-md-6">
				<label for="telegram_nic">Город</label>
				<input type='text' id='city' name='city' value='<?=$r['city']??""?>' class='form-control'>
			</div>
		</div>
        
		<div class="form-group">
			<label for="comm">Комментарий</label>
			<textarea id='comm' name='comm' class='form-control' rows='5'><?=$r['comm1']??""?></textarea>
		</div>

        <button type='submit' name='do_add' value='yes' class='btn btn-primary'>Добавить</button>
    </form>
</div>
<?

$db->bottom();
?>
