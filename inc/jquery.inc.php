<?
session_start();
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
//include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
$db=new db();
$db->connect($database);
$db->telegram_bot="vkt";

function date2tm($str) {
	$dmy=explode(".",$str);
	$tm=mktime(0,0,0,$dmy[1],$dmy[0],$dmy[2]);
	if($str!=date("d.m.Y",$tm)) {
		print "<p class='red'>Ошибка в формате даты : <b>$str</b>. Должно быть dd.mm.YYYY</p>";
		return false;
	}
	return $tm;
}


if(isset($_GET['uid'])) {
	$uid=intval($_GET['uid']);
} else
	$uid=0;

if(@$_GET['get_unicum_uid']) {
	print $db->get_unicum_uid();
}
if(isset($_GET['leadgen_stop_global'])) { //from users.inc.php
	$user_id=intval($_GET['user_id']);
	$fl=intval($_GET['fl']);
	if($user_id)
		$db->query("UPDATE users SET leadgen_stop_global='$fl' WHERE id='$user_id' ");
}
if(@$_GET['lock_off']) {
	$db->query("UPDATE cards SET lock_tm=0 WHERE uid='$uid'");
}
if(@$_GET['set_fl']) {
	//print "<p>HERE</p>";
	$fl=intval($_GET['fl']);
	$db->query("UPDATE cards SET fl='$fl' WHERE id='".intval($_GET['id'])."'");
}
if(@$_GET['save_comm']) {
	$user_id=intval($_GET['user_id']);
	if(isset($_GET['comm1']))
		$comm1=",comm1='".$db->escape(mb_substr($_GET['comm1'],0,2048))."'"; else $comm1="";
	$mob_search=$db->check_mob(trim($_GET['mob']));
	$mob_search=$mob_search?$mob_search:0;
	$db->query("UPDATE cards SET
						mob='".$db->escape($_GET['mob'])."',
						mob_search='".$db->escape($mob_search)."',
						pact_conversation_id=0,
						email='".$db->escape($_GET['email'])."',
						telegram_nic='".$db->escape(preg_replace("/^@/", "", $_GET['telegram_nic']))."'
						$comm1
						WHERE uid='$uid'");

	if($db->dlast("msg","msgs","uid='$uid'") != $_GET['comm']) {					
		$db->save_comm($uid,$user_id,$_GET['comm'],0,0,0,true);
	}
	if(isset($_GET['touch'])) {
		$touch=intval($_GET['touch']);
		if($touch>300) {
			$touch_res=$db->dlookup("source_name","sources","id='$touch'");
			$db->save_comm($uid,$user_id,"Результат касания: $touch_res",$touch);
		}
	}
}
if(@$_GET['set_comm']) {
	//print "<p>HERE</p>";
	$db->query("UPDATE cards SET tm_lastmsg='".time()."',comm='".$db->escape($_GET['comm'])."' WHERE id='$uid'");
}
if(@$_GET['scdl_set']) {
	$scdl_opt=intval($_GET['scdl_opt']);
	if($scdl_opt>time()) {
		$scdl_funnel=$_GET['scdl_funnel'];
		if($scdl_funnel)
			$db->query("INSERT INTO funnels SET tm='".time()."',uid='$uid',funnel='$scdl_funnel'");
		$scdl_opt=intval($_GET['scdl_opt']);
		$scdl_web_id=intval($_GET['scdl_web']);
		if(!$scdl_web_id)
			$scdl_web_id=1;
		if($scdl_opt==9)
			$tm+=9*60*60;
		elseif($scdl_opt==12)
			$tm+=12*60*60;
		elseif($scdl_opt==1440)
			$tm+=14*60*60+40*60;
		elseif($scdl_opt==1720)
			$tm+=17*60*60+20*60;
		elseif($scdl_opt==20)
			$tm+=20*60*60;
		else
			$tm+=$scdl_opt;
		$db->query("UPDATE cards SET tm_lastmsg='".time()."',tm_schedule='$tm',scdl_opt='$scdl_opt',scdl_web_id='$scdl_web_id',scdl_fl=0 WHERE uid='$uid'");
		$db->save_comm($uid,0,"УСТАНОВКА В РАСПИСАНИЕ ОПЕРАТОРОМ НА ".date("d.m.Y H:i",$tm),100,'manual');
		$db->notify($uid,"🗓 УСТАНОВЛЕН В РАСПИСАНИЕ НА ".date("d.m.Y H:i",$tm));
	}
}
if(@$_GET['scdl_clr']) {
	$db->query("UPDATE cards SET tm_lastmsg='".time()."',tm_schedule=0,scdl_opt=0,scdl_fl=0 WHERE uid='$uid'");
	$db->save_comm($uid,0,"ОЧИСТКА РАСПИСАНИЯ ОПЕРАТОРОМ",103);
}
if(@$_GET['delay_set']) {
	if($tm=date2tm($_GET['dt'])) {
		$db->query("UPDATE cards SET tm_lastmsg='".time()."',tm_delay='$tm' WHERE uid='$uid'");
		$db->mark_new($uid,0);
	}
}
if(@$_GET['chk_cp']) {
	if($_GET['chk_cp']=="on")
		$db->query("UPDATE cards SET fl=1 WHERE uid='$uid'");
	else
		$db->query("UPDATE cards SET fl=0 WHERE uid='$uid'");
}
if(@$_GET['delay_clr']) {
	$db->query("UPDATE cards SET tm_lastmsg='".time()."',tm_delay=0 WHERE uid='$uid'");
}

if(isset($_GET['ch_razdel'])) {
	//print "<p>HERE</p>";
	if(intval($uid)) {
		$db->query("UPDATE cards SET tm_lastmsg='".time()."',razdel={$_GET['razdel']} WHERE uid='$uid'");
		$tm=time();
		$razdel_name=$db->dlookup("razdel_name","razdel","id='".intval($_GET['razdel'])."'");
		$user_name=$_SESSION['username']; //$db->dlookup("username","users","id={$_GET['user_id']}");
		$db->query("INSERT INTO msgs SET 
						uid='$uid',
						razdel_id='".intval($_GET['razdel'])."',
						imp=11,
						outg=2,
						tm=$tm,
						user_id='".intval($_GET['user_id'])."',
						msg='".$db->escape("Раздел изменен на $razdel_name пользователем $user_name")."'");
	}
}
if(isset($_POST['manage_checkboxes'])) {
	$ids=explode(",",$_POST['ids']);
	if($_POST['mode']=='clr_all') {
		$db->query("UPDATE cards SET fl='0' WHERE 1");
	}
	if($_POST['mode']=='set_all_razd' || $_POST['mode']=='clr_all_razd') {
		foreach($ids AS $id) {
			$id=intval($id);
			if(!$id)
				continue;
			if($_POST['mode']=='set_all_razd')
				$db->query("UPDATE cards SET fl='1' WHERE id='$id'");
			elseif($_POST['mode']=='clr_all_razd')
				$db->query("UPDATE cards SET fl='0' WHERE id='$id'");
		}
	}
}

if(isset($_GET['partner_set_access_level'])) {
	$access_level=intval($_GET['access_level']);
	if($db->is_md5($_GET['uid'])) {
		$uid_md5=$_GET['uid'];
		$klid=$db->dlookup("id","cards","uid_md5='$uid_md5'");
		$db->query("UPDATE users SET access_level='$access_level' WHERE klid='$klid'");
	}
}
if(isset($_GET['set_no_login'])) {
	$fl=intval($_GET['fl'])?1:0;
	if($db->is_md5($_GET['uid'])) {
		$uid_md5=$_GET['uid'];
		$klid=$db->dlookup("id","cards","uid_md5='$uid_md5'");
		$db->query("UPDATE users SET fl_allowlogin='$fl' WHERE klid='$klid'");
	}
}

if(isset($_GET['pay_cash'])) {
	$pid=intval($_GET['pid']);
	$res=$db->query("SELECT * FROM product WHERE id='$pid'");
	$row = $db->fetch_assoc($res);
    $amount = $row["price1"];
	$r=$db->fetch_assoc($db->query("SELECT MAX(order_id) AS oid FROM avangard WHERE 1"));
	$order_id=$r['oid']+1;
	echo json_encode(["amount" => $amount,"order_id"=>$order_id]);
	exit;
}

if(isset($_GET['get_razdel_list'])) {
	$result = $db->query("SELECT id, razdel_name FROM razdel WHERE del=0 AND id>0 ORDER BY razdel_num,razdel_name");

	$razdels = array();
	while ($row = $db->fetch_assoc($result)) {
		preg_match('/#[a-fA-F0-9]{6}/', $db->get_style_by_razdel($row['id']), $m);
	  $razdel = array(
		'id' => $row['id'],
		'razdel_name' => $row['razdel_name'],
		'razdel_bg' =>  $m[0]
	  );
	  array_push($razdels, $razdel);
	}

	// Отправляем список разделов в формате JSON
	echo json_encode($razdels);
	exit;
}
if(isset($_GET['update_razdel'])) {
	$cardsId = intval($_GET['cardsId']);
	$razdelId = intval($_GET['razdelId']);
	$db->query("UPDATE cards SET razdel='$razdelId' WHERE id='$cardsId'");
	print $db->get_style_by_razdel($razdelId);
	exit;
}
if(isset($_GET['show_user_info'])) {
	$user_id = intval($_GET['userId']);
	$res=$db->query("SELECT * FROM users WHERE id='$user_id'");
	$arr=[];
	while($r=$db->fetch_assoc($res)) {
		$arr[]=['user_id'=>$r['id'],
				'klid'=>$r['klid'],
				'login'=>$r['username'],
				'name'=>$r['real_user_name'],
			];
	}
	print $arr;
	exit;
}

if(isset($_POST["msgs_city"])){
    $search = mb_substr($_POST["msgs_city"],0,10);

    // Поиск городов в таблице
    $sql = "SELECT DISTINCT city FROM cards WHERE city LIKE '%$search%'";
    $result = $db->query($sql);

    // Вывод результатов
    if($db->num_rows($result) > 0){
        echo "<ul class='list-group'>";
        while($row = $db->fetch_assoc($result)){
            echo "<li class='list-group-item'>".$row['city']."</li>";
        }
        echo "</ul>";
    } else{
        //echo "<p class='text-muted'>Город не найден</p>";
    }
    exit;
}
if(isset($_POST["msgs_sel_user_id"])){
    $search = $_POST["msgs_sel_user_id"];

    // Поиск городов в таблице
    $sql = "SELECT real_user_name FROM users WHERE del=0 AND fl_allowlogin=1 AND real_user_name LIKE '%$search%'";
    $result = $db->query($sql);

    // Вывод результатов
    if($db->num_rows($result) > 0){
        echo "<ul class='list-group'>";
        while($row = $db->fetch_assoc($result)){
            echo "<li class='list-group-item'>".$row['real_user_name']."</li>";
        }
        echo "</ul>";
    } else{
        //echo "<p class='text-muted'>Город не найден</p>";
    }
    exit;
}

if(isset($_POST['userInput'])) {
	$userInput = mb_substr($_POST['userInput'],0,10);
	$access_level=intval($_POST['access_level']);
	$user_id=intval($_POST['user_id']);
	$userList = array();
	if($access_level<=3) {
		$list_from=($db->database=='vkt') ? 2 :3;
		$sql = "SELECT id, real_user_name FROM users WHERE del=0 AND id>$list_from AND real_user_name LIKE '%$userInput%' ORDER BY real_user_name LIMIT 10";
		$result = $db->query($sql);
		if ($db->num_rows($result) > 0) {
		  while($row = $db->fetch_assoc($result)) {
			$userList[] = array('id' => $row['id'], 'real_user_name' => $row['real_user_name']." ({$row['id']})");
		  }
		}
	} else {
		include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
		$p=new partnerka(false,$db->database);
		$res=$p->get_all_partners($user_id, 0);
		$userList[] = array('id' => 0, 'real_user_name' => 'ВСЕ');
		$userList[] = array('id' => $user_id, 'real_user_name' => 'ТОЛЬКО СВОИ');
		foreach($res AS $id=>$r) {
			if(preg_match("/$userInput/iu",$r['name']))
				$userList[] = array('id' => $id, 'real_user_name' => "{$r['name']} ($id)");
		}
	}

	// Возвращение списка пользователей в формате JSON
	echo json_encode($userList);
	exit;
}

if($_GET['ch_land_num']=='yes') {
	$db->ch_land_num(intval($_GET['oldvalue']),intval($_GET['newvalue']));
	exit;
}
if(isset($_GET['cp_disp_tags'])) {
	$uid = intval($_GET['uid']);

	// Your query to fetch tags
	$res_tags = $db->query("SELECT * FROM tags_op JOIN tags ON tags.id=tag_id WHERE uid='$uid'");
	$tags = "";
	while ($r_tags = $db->fetch_assoc($res_tags)) {
		$tags_bg = $r_tags['tag_color'];
		$tags_color = $db->get_contrast_color($r_tags['tag_color']);
		$tags .= "<span class='p-1 mx-1 rounded small' style='background-color:$tags_bg; color:$tags_color;'>{$r_tags['tag_name']}</span>";
	}
	$tags.= "<a href='#' class='' data-toggle='modal' data-target='#msgTagsModal' data-uid='$uid'>
		<i class='fa fa-plus'></i>
			</a>";

	// Output the tags
	echo $tags;
	
	exit;
}
if (isset($_POST['users_notif'])) {
    $userId = intval($_POST['user_id']);
    $key = substr($_POST['key'],0,8);
    $val = isset($_POST['val']) ? intval($_POST['val']) : 0; // Get the value from the POST request
	//$db->notify_me("$userId, $key, $val");
    // Call your method to set notification
    if($db->users_notif_set($userId, $key, $val))
		echo json_encode(['status' => 'success', 'value' => $val]);
	else
		echo json_encode(['status' => 'error', 'value' => $val]);
    exit;
}
if (isset($_POST['ch_bc'])) {
	$id=intval($_POST['id']);
	$bc=mb_substr(trim($_POST['bc']),0,32);
	$db->query("UPDATE users SET bc='".$db->escape($bc)."' WHERE id='$id'");
	exit;
}
if (isset($_POST['ch_api_secret'])) {
	$tmp=$db->database;
	$db->connect('vkt');
	$api_secret=md5(bin2hex(random_bytes(16)));
	$ctrl_id=intval($_POST['ctrl_id']);
	//$db->notify_me($ctrl_id);
	$db->query("UPDATE 0ctrl SET api_secret='".$db->escape($api_secret)."' WHERE id='$ctrl_id'");
	print json_encode($response = array('secret' => $api_secret,'ctrl_id'=>$ctrl_id));
	$db->connect($tmp);
	exit;
}
if (isset($_POST['ch_passw'])) {
	$user_id=intval($_POST['user_id']);
	if($user_id == $_SESSION['userid_sess']) {
		$klid=0;
		$passw=$db->passw_gen($len=16);
		//$passw="fokova#142586";
		$md5=md5($passw);
		$tmp=$db->database;
		include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
		$vkt=new vkt('vkt');
		$ctrl_id=$vkt->get_ctrl_id_by_db($tmp);
		$admin_uid=$vkt->dlookup("uid","0ctrl","id='$ctrl_id'");
		$email=$vkt->dlookup("email","cards","uid='$admin_uid'");
		$tg=$vkt->dlookup("telegram_id","cards","uid='$admin_uid'");
		$tg_bot_msg_name=$db->dlookup("tg_bot_msg_name","0ctrl","id='1'");
		$tg_bot_msg=$db->dlookup("tg_bot_msg","0ctrl","id='1'");
		if($user_id==3) {
			$db->query("UPDATE 0ctrl SET admin_passw='".$db->escape($passw)."' WHERE id='$ctrl_id'");
		}
		$db->connect($tmp);
		if($user_id >3 ) {
			$klid=$db->get_klid($user_id);
			$email=$db->dlookup("email","cards","id='$klid'");
			$tg=$db->dlookup("telegram_id","users","klid='$klid'");
		}
		$db->notify_me("CH_PASSW jquery.php $ctrl_id $email $tg $passw $tg_bot_msg_name");
		$msg="Для $login установлен новый пароль: $passw";
		if($_POST['action']=='ch_passw_tg') {
			include_once "/var/www/vlav/data/www/wwl/inc/tg_bot.class.php";
			$t=new tg_bot($tg_bot_msg);
			if($t->send_msg($tg,$msg)) {
				print "Пароль отправлен вам в телеграм <a href='https://t.me/$tg_bot_msg_name' class='' target='_blank'>$tg_bot_msg_name</a>";
				$db->query("UPDATE users SET passw='".$db->escape($md5)."',comm='$passw' WHERE id='$user_id'");
				$d=$db->get_direct_code($klid);
				$db->query("UPDATE users SET direct_code='".$db->escape($d)."' WHERE id='$user_id'");
				print "<br>Обратите внимание, что прямая ссылка на партнерский кабинет также изменилась.
					Получите новую ссылку через бот или у администратора";
			} else {
				print "<p class='alert alert-warning' >Ошибка отправки в тг: $tg_bot_msg_name</p>";
			}
			session_destroy();
		}
		if($_POST['action']=='ch_passw_email') {
			$email=$db->dlookup("email","cards","id='$klid'");
			print "Пароль отправлен на электронную почту: $email";
			if($db->email([$email],"Новый пароль",$msg,"support@winwinland.ru","support@winwinland.ru")) {
				$db->query("UPDATE users SET passw='".$db->escape($md5)."',comm='$passw' WHERE id='$user_id'");
				$d=$db->get_direct_code($klid);
				$db->query("UPDATE users SET direct_code='".$db->escape($d)."' WHERE id='$user_id'");
				print "<br>Обратите внимание, что прямая ссылка на партнерский кабинет также изменилась.
					Получите новую ссылку через бот или у администратора";
			} 
			session_destroy();
		}
	} else {
		$db->notify_me("ch_passw jquery error. user_id=$user_id userid_sess={$_SESSION['userid_sess']}");
		print "Ошибка";
	}
	exit;
}
include "jquery_tag_ops.inc.php";
?>
