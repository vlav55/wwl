<?
include_once "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
include "init.inc.php";
if(!isset($_POST['secret'])) {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// Check for CSRF token
		$csrf_chk=false;
		if(isset($_POST['csrf_token_land'])) {
			if(isset($_SESSION['csrf_token_land'])) {
				if($_SESSION['csrf_token_land']==$_POST['csrf_token_land']) {
					$csrf_chk=true;
					$db->query("DELETE FROM csrf WHERE token='{$_POST['csrf_token_land']}'");
				}
			}
			if(!$csrf_chk) {
				$res_csrf=$db->query("SELECT * FROM csrf WHERE token_name='csrf_token_land'");
				while($r_csrf=$db->fetch_assoc($res_csrf)) {
					if($_POST['csrf_token_land']==$r_csrf['token']) {
						$csrf_chk=true;
						$db->query("DELETE FROM csrf WHERE token='{$_POST['csrf_token_land']}'");
						break;
					}
				}
			}
		}  else {
			//$db->notify_me("HERE_".$_SERVER['HTTP_REFERER']);
			//$db->notify_me("HERE_$ctrl_id \n".print_r($_POST,true));
			//$db->vkt_email("thanks.1.inc.php csrf_token_land NOT_SET_POST_csrf_token_land  ctrl_id=$ctrl_id",print_r($GLOBALS,true));
			die("<h1>Поздравляем, вы успешно зарегистрированы</h1> (7)"); 
		}
		if (!$csrf_chk) {
			if(in_array($ctrl_id,[14]))
				die("error 6");
			else
				$db->vkt_email("thanks.1.inc.php csrf_token_land error ctrl_id=$ctrl_id",print_r($GLOBALS,true));
		}
	} else {
		die("<h1>Поздравляем, вы успешно зарегистрированы</h1> (8)");
		if(in_array($ctrl_id,[14]))
			die("error 8");
		else
			$db->vkt_email("thanks.1.inc.php csrf_token_land NOT_POST_REQUEST ctrl_id=$ctrl_id",print_r($GLOBALS,true));
	}
}

$db=new vkt_send($database);

//~ if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	//~ if(!isset($_POST['csrf_token']))
		//~ $_POST['csrf_token']=123;
    //~ if ( !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
		//~ $db->notify_me("CSRF ERROR 7 !!!: ".$_SERVER['PHP_SELF']."\n{$_SESSION['csrf_token']}\n".addslashes(print_r($_POST,true)) );
    //~ } else
		//~ $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
//~ }


//$db->print_r($_POST);

$title='Благодарим за регистрацию';
$descr=$title;
$og_image="";
$favicon="https://for16.ru/images/favicon.png";

$klid=0; $user_id=0; $uid=0; $bc=false;
if(isset($_POST['bc'])) {
	if($bc=$db->promocode_validate($_POST['bc'])) {
		if($klid=$db->get_klid_by_bc($bc)) {
			if(!$user_id=$db->get_user_id($klid)) {
				$p=new partnerka($klid,$database);
				$p->ctrl_id=$ctrl_id;
				$p_res=$p->partner_add($klid,'','');
				if(!$user_id=$p_res['user_id'])
					$klid=0;
			}
			$uid=0;
		}
	}
}


if(isset($_POST['phone']))
	$_POST['client_phone']=$_POST['phone'];
if(isset($_POST['email']))
	$_POST['client_email']=$_POST['email'];
if(isset($_POST['fio']))
	$_POST['client_name']=$_POST['fio'];

if(isset($_POST['regPhone']))
	$_POST['client_phone']=$_POST['regPhone'];
if(isset($_POST['regEmail']))
	$_POST['client_email']=$_POST['regEmail'];
if(isset($_POST['regName']))
	$_POST['client_name']=$_POST['regName'];

$client_name='';
if(isset($_POST['client_name']))
	$client_name=htmlspecialchars(mb_substr(trim($_POST['client_name']),0,64));


if($client_name) {

	if(isset($_POST['land_num']))
		$land_num=intval($_POST['land_num']);
	if(!$land_num)
		$land_num=1;
	$land_url=$db->dlookup("land_url","lands","land_num='$land_num'");

	if(isset($_POST['client_phone']))
		if(!$mob=$db->check_mob($_POST['client_phone']))
			$mob="";
	if($db->dlookup("fl_disp_phone_rq","lands","land_num='$land_num' AND del=0") && empty($mob)) {
		//print "<script>location='$land_url/?bc=$bc&err=phone_required'</script>";
		//print "<p class='alert alert-warning' >Ошибка регистрации - требуется номер телефона</p>";
		if(!isset($_POST['secret'])) 
			header("Location: $land_url/?bc=$bc&err=phone_required");
		else
			print "err - phone number required according settings of landing $land_num";
		exit;
	}


	if(isset($_POST['client_email']))
		$email=($db->validate_email($_POST['client_email']))?trim($_POST['client_email']):""; else $email="";
	if($db->dlookup("fl_disp_email_rq","lands","land_num='$land_num' AND del=0") && empty($email)) {
		//print "<script>location='$land_url/?bc=$bc&err=email_required'</script>";
		//print "<p class='alert alert-warning' >Ошибка регистрации - требуется email</p>";
		if(!isset($_POST['secret'])) 
			header("Location: $land_url/?bc=$bc&err=email_required according settings of landing $land_num");
		else
			print "err - email required";
		exit;
	}

	$city="";$city_sql="";
	if(isset($_POST['regCity']))
		if(!empty($_POST['regCity'])) {
			$city=mb_substr(trim($_POST['regCity']),0,32);
			$city_sql="city='".$db->escape(trim($city))."',";
		}


	if(isset($_POST['regComm']))
		$comm=mb_substr(trim($_POST['regComm']),0,1024); else $comm="";

	if(!$uid) {
		if(isset($_POST['uid'])) {
			$uid=$db->get_uid($_POST['uid']);
		}
	}
	if(!$uid) {
		if(isset($_POST['vk_id']))
			$uid=$db->get_uid(intval($_POST['vk_id']));
	}
	if(!empty($mob) && !$uid) {
		$uid=$db->dlookup("uid","cards","mob_search='$mob' AND del=0");
	}
	if(!empty($email) && !$uid) {
		$uid=$db->dlookup("uid","cards","email='$email' AND del=0");
	}
	$test_cyrillic=false;
	foreach($_POST AS $key=>$val) {
		if($db->is_cyrillic($val))
			$test_cyrillic=true;
	}
	$test_cyr=isset($test_cyr) ? $test_cyr : 1; //may set in dapi
	//$db->notify_me("test_cyr=$test_cyr $test_cyrillic");
	if($test_cyr && !$test_cyrillic && !empty($comm) ) {
		//$db->notify_me("thanks.php test_cyr=false ctrl_id=$ctrl_id \n $comm");
		if(!isset($_POST['secret'])) {
			$title="Thank you";
			include "land_top.inc.php";
			?>
			<div>
				<h2 class='text-center mt-5' >Благодарим за регистрацию</h2>
				<p class='text-center my-5' >Мы с вами свяжемся в ближайшее время.</p>
			</div>
			<?
			$db->notify_me("thaks.php ctrl_id=$ctrl_id test_cyr=false \n".$comm);
			include "land_bottom.inc.php";
		} else
			print "err - test_cyrillic is wrong. You may switch it off by passing test_cyr=0 in post";
		exit;
	}
	if(!$uid) {
		$uid=$db->get_unicum_uid();
		$vk_id=isset($_POST['vk_id']) ? intval($_POST['vk_id']) : 0;
		$uid_md5=$db->uid_md5($uid);

		$db->query("INSERT INTO cards SET 
				uid='$uid',
				uid_md5='$uid_md5',
				name='".$db->escape(trim($client_name))."',
				email='".$db->escape(strtolower(trim($email)))."',
				mob='$mob',
				mob_search='$mob',
				$city_sql
				acc_id=2,
				razdel='4',
				source_id='0',
				fl_newmsg=0,
				tm_lastmsg=".time().",
				tm=".time().",
				user_id='$user_id',
				vk_id='$vk_id',
				pact_conversation_id=0,
				utm_affiliate='$klid',
				wa_allowed=0
				",0);
		if(!$mob && empty($email)) {
			//print "<p class='alert alert-danger' >Отсутствуют контактные данные!</p>";
		}
	} else {
		$db->query("UPDATE cards SET fl_gpt=0 WHERE uid='$uid'");
		if(empty($db->dlookup("mob_search","cards","uid='$uid' AND del=0")) && $db->check_mob($mob) )
			$db->query("UPDATE cards SET mob='$mob',mob_search='$mob' WHERE uid='$uid'");
		if(empty($db->dlookup("email","cards","uid='$uid' AND del=0")) && !empty($email) )
			$db->query("UPDATE cards SET email='".$db->escape($email)."' WHERE uid='$uid'");
		if(!empty($city))
			$db->query("UPDATE cards SET city='".$db->escape($city)."' WHERE uid='$uid'");
	}

	if($uid) {
		$db->connect('vkt');
		//~ $land_num=0;
		//~ if(isset($_POST['land_num']))
			//~ $land_num=intval($_POST['land_num']);
		//~ if(!$land_num)
			//~ $land_num=1;
		if($land_num==1 && !$db->dlookup("id","lands","land_num='1'")) {
			$thanks_pic=(file_exists('tg_files/thanks_pic.jpg'))?"<img src='tg_files/thanks_pic.jpg' class='img-fluid' >":""; //$db->dlookup("thanks_pic","0ctrl","id=$ctrl_id");
			$thanks_txt=$db->dlookup("thanks_txt","0ctrl","id=$ctrl_id AND del=0");
			$land_name="Лэндинг_1";
			$tm_scdl=0;
			$land_razdel=0;
			$land_tag=0;
			$land_man_id=0;
			$fl_partner_land=0;
		} elseif($land_num==2 && !$db->dlookup("id","lands","land_num='2' AND del=0")) {
			$thanks_pic=(file_exists('tg_files/thanks_pic_p.jpg'))?"<img src='tg_files/thanks_pic_p.jpg' class='img-fluid' >":""; //$db->dlookup("thanks_pic","0ctrl","id=$ctrl_id");
			$thanks_txt=$db->dlookup("thanks_txt_p","0ctrl","id=$ctrl_id");
			$land_name="Лэндинг_2";
			$tm_scdl=0;
			$land_razdel=0;
			$land_tag=0;
			$land_man_id=0;
			$fl_partner_land=1;
		} else {
			$db->connect($database);
			$thanks_pic=(file_exists("tg_files/thanks_pic_$land_num.jpg"))?"<img src='tg_files/thanks_pic_$land_num.jpg' class='img-fluid'  style='max-width: 100%; width: auto; height: auto;'>":"";
			$thanks_txt=$db->dlookup("thanks_txt","lands","land_num='$land_num' AND del=0");
			$land_name=$db->dlookup("land_name","lands","land_num='$land_num' AND del=0");
			$tm_scdl=$db->dlookup("tm_scdl","lands","land_num='$land_num' AND del=0");
			$land_razdel=$db->dlookup("land_razdel","lands","land_num='$land_num' AND del=0");
			$land_tag=$db->dlookup("land_tag","lands","land_num='$land_num' AND del=0");
			$land_man_id=$db->dlookup("land_man_id","lands","land_num='$land_num' AND del=0");
			$fl_partner_land=$db->dlookup("fl_partner_land","lands","land_num='$land_num' AND del=0");
			$fl_not_notify=$db->dlookup("fl_not_notify","lands","land_num='$land_num' AND del=0");
			$bot_first_msg=$db->dlookup("bot_first_msg","lands","land_num='$land_num' AND del=0");
		}

		$db->connect($database);

		if(isset($make_partner))
			$fl_partner_land=$make_partner;

		if($fl_partner_land) {
			include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
			$vkt=new partnerka(false,$database);
			$crm_uid=$uid;
			$vkt->save_comm($crm_uid,$user_id,false,25);

			$partner_klid=$vkt->dlookup("id","cards","uid='$crm_uid' AND del=0");
			$f_name=$vkt->dlookup("name","cards","uid='$crm_uid' AND del=0");
			$l_name=$vkt->dlookup("surname","cards","uid='$crm_uid' AND del=0");
			$vkt->fee_hello=$fee_hello;
			$vkt->fee=$fee_1;
			$vkt->fee2=$fee_2;
			$vkt->ctrl_id=$ctrl_id;

			$r=$vkt->partner_add($partner_klid,$email='',$l_name.' '.$f_name,$username_pref='partner_');
			$partner_user_id=$vkt->get_user_id($partner_klid);
			//$vk->vk_msg_send(198746774, "P uid=$crm_uid u=$partner_user_id k=$partner_klid ".print_r($r,true));
			//$vk->vk_msg_send(198746774, print_r($r,true));

			$bc=$vkt->dlookup("bc","users","klid='$partner_klid' AND del=0");
			$direct_code_link=$vkt->get_direct_code_link($partner_klid);
			$partner_link=$vkt->get_partner_link($partner_klid,'land');

			//$vkt->query("UPDATE cards SET razdel='2' WHERE uid='$uid'"); //B
		}

		$referer=parse_url($_SERVER['HTTP_REFERER'])['path'];

		$icon_partner=($fl_partner_land)?"🙋‍♀️":"";

		$db->save_comm_tm_ignore=5*60;
	//$db->save_comm_tm_ignore=0;
		$hold_chk=$db->hold_chk($uid); //untill all card_hold_tm filled
		if($db->save_comm($uid,0,"⭐$icon_partner Регистрация с лэндинга: $land_num",12,$user_id)) {
			$db->save_comm($uid,0,$land_name,1000+$land_num);

			if($user_id) {
				$db->hold_set($uid,time()+($hold*24*60*60));
				$db->query("UPDATE cards SET tm_user_id='".time()."' WHERE uid='$uid'");
			}

			$card_keep=$db->dlookup("card_keep","cards","uid='$uid'");
			$keep=$card_keep ? $card_keep : $keep;
			if(!$hold_chk && $user_id) {
				$db->query("UPDATE cards SET user_id='$user_id',tm_user_id='".time()."',utm_affiliate='$klid' WHERE uid='$uid'");
			}
			if(!$keep && $user_id) {
				$db->query("UPDATE cards SET user_id='$user_id',tm_user_id='".time()."',utm_affiliate='$klid' WHERE uid='$uid'");
			}
			
			if(!empty($comm))
				$db->save_comm($uid,0,$comm,0,0,0,true); //save_comm($uid,$user_id,$comm,$source_id=0,$vote_vk_uid=0,$mode=0, $force=false)

			if($tm_scdl) {
				$db->query("UPDATE cards SET tm_schedule='$tm_scdl',scdl_web_id='$land_num' WHERE uid='$uid'");
			}
			if($land_razdel) {
				$db->query("UPDATE cards SET razdel='$land_razdel' WHERE uid='$uid'");
			}
			if($land_tag) {
				if(!$db->dlookup("id","tags_op","tag_id='$land_tag' AND uid='$uid'"))
					$db->query("INSERT INTO tags_op SET tag_id='$land_tag',uid='$uid',tm='".time()."'");
			}
			if($land_man_id) {
				if(!$db->dlookup("man_id","cards","uid='$uid'"))
					$db->query("UPDATE cards SET man_id='$land_man_id' WHERE uid='$uid'");
			}

			if(isset($_POST['tzoffset'])) {
				$tzoffset=intval($_POST['tzoffset']);
				$db->query("UPDATE cards SET tzoffset='$tzoffset' WHERE uid='$uid'");
			}

			if(!$fl_not_notify) {
				$comm=$comm ? "\n$comm" : "";
				$db->notify($uid,"⭐$icon_partner ($land_num) $land_name".$comm,'reg');
			}

			$utm_campaign=(isset($_POST['utm_campaign']) && $_POST['utm_campaign']!='null')?$_POST['utm_campaign']:"";
			$utm_content=(isset($_POST['utm_content']) && $_POST['utm_content']!='null')?$_POST['utm_content']:"";
			$utm_medium=(isset($_POST['utm_medium']) && $_POST['utm_medium']!='null')?$_POST['utm_medium']:"";
			$utm_source=(isset($_POST['utm_source']) && $_POST['utm_source']!='null')?$_POST['utm_source']:"";
			$utm_term=(isset($_POST['utm_term']) && $_POST['utm_term']!='null')?$_POST['utm_term']:"";
			$utm_ab=(isset($_POST['utm_ab']) && $_POST['utm_ab']!='null')?$_POST['utm_ab']:"";
			if(!empty($utm_campaign) ||
				!empty($utm_content) ||
				!empty($utm_medium) ||
				!empty($utm_source) ||
				!empty($utm_term) ||
				!empty($utm_ab) ) {
				//~ if(isset($land_num)) //if utm set land_num=0 that cause wrong par pass to telegram bot, I don't know why
					//~ $land_num=0;
				$db->query("INSERT INTO utm SET
						uid='$uid',
						tm='".time()."',
						utm_campaign='".$db->escape($utm_campaign)."',
						utm_content='".$db->escape($utm_content)."',
						utm_medium='".$db->escape($utm_medium)."',
						utm_source='".$db->escape($utm_source)."',
						utm_term='".$db->escape($utm_term)."',
						utm_ab='".$db->escape($utm_ab)."',
						pwd_id='$land_num',
						promo_code='0',
						mob='$mob' ");
			}

			if(isset($_POST['cards0ctrl_id'])) {
				if($cards0ctrl_id=intval($_POST['cards0ctrl_id'])) {
					if(!$db->dlookup("uid","cards0ctrl","uid='$uid' AND ctrl_id='$cards0ctrl_id'")) {
						$db->query("INSERT INTO cards0ctrl SET
									uid='$uid',
									ctrl_id='$cards0ctrl_id'
									");
					}
				}
			}

			$res=$db->query("SELECT * FROM vkt_send_1 WHERE del=0 AND sid=12 AND (land_num='$land_num' OR land_num=0)",0);
			while($r=$db->fetch_assoc($res)) {
				$db->vkt_send_task_add($ctrl_id, $tm_event=intval(time()+$r['tm_shift']), $vkt_send_id=$r['id'],$vkt_send_type=3,$uid);
			}
			if($insales_id) {
				//~ include_once "/var/www/vlav/data/www/wwl/inc/insales.class.php";
				//~ $in=new insales($insales_id,$insales_shop);
				//~ if($ctrl_id==167) {
					//~ $in->id_app="winwinland_demo_11";
					//~ $in->secret_key='e5697c177c0f51497d069969e170dbcb';
					//~ $in->get_credentials();
				//~ }
				//~ $in->ctrl_id=$ctrl_id;
				//~ if(!$res=$in->search_client($search)) {
					//~ $res=$in->create_client($client_name, $mob, $email, $password = null);
				//~ }
				//~ if($in_id=$res['id']) {
					//~ if(!$db->dlookup("id","cards2other","uid='$uid' AND tool='insales' AND tool_uid='$in_id'"))
						//~ $db->query("INSERT INTO cards2other SET uid='$uid',tool='insales',tool_uid='$in_id'");
				//~ }
			}
		}
		$tm=time()-(24*60*60);
		$db->query("DELETE FROM telegram WHERE tm<'$tm'");
		$tg_code=rand(100,99999);
		$n=10000;
		while($db->dlookup("id","telegram","code='$tg_code'")) {
			$tg_code=rand(100,99999);
			if(!$n--)
				break;
		}
		//$tmp=$db->database;
		//$db->connect('vkt');
		$db->query("INSERT INTO telegram SET
				tm='".time()."',
				uid='$uid',
				code='$tg_code',
				user_id='$land_num',
				confirmed='0'
				");
		//$db->connect($tmp);
	}
	//~ else
		//~ print "<p class='alert alert-danger' >Ошибка. Обратитесь в техподдержку!</p>";
}
		//code for TG 

$msg=($uid)?$db->prepare_msg($uid,$thanks_txt):$thanks_txt;
//$msg=$db->make_link_clickable($msg);

?>

<? if(!isset($_POST['secret'])) { ?>

	<?
	if(empty($thanks_pic) && empty($msg) && !empty($bot_first_msg)) {
		if(empty($tg_bot_msg)) {
			print "<p>Не подключен чат-бот для переписки</p>"; 
		} else {
			header("Location: https://t.me/$tg_bot_msg_name?start=$tg_code");
		}
		exit;
	}
	?>

	<? include "land_top.inc.php"; ?>

	<div class='text-center' ><?=$thanks_pic?></div>
	<div class='container' >
	<div class='pt-5 pb-2 text-center' >
		<?=$msg?>
	</div>
	<?if(!empty($bot_first_msg)) {
		$btn_disabled=empty($tg_bot_msg) ? "onclick='alert(\"Не подключен чат-бот\");event.preventDefault();'" : "";
		?>
	<div class='text-center mb-5' ><a <?=$btn_disabled?> aria-disabled="true" href='https://t.me/<?=$tg_bot_msg_name?>?start=<?=$tg_code?>' class='btn btn-info btn-lg' target='_blank'>Телеграм</a></div>
	<?}?>
	<?
	//print "uid=$uid tg_code=$tg_code<br>";
	//$db->print_r($_POST);
	
	?>
	<? include "land_bottom.inc.php";?>

<?
} elseif($_POST['secret']=='consult') {
	//header("Location: https://winwinland.ru/x/?uid=$uid", true, 301);
	header("Location: https://winwinland.ru/consult/?uid=$uid", true, 301);
	exit;
} else { //if dapi call
}
//$db->notify_me("HERE_".getcwd());

?>

