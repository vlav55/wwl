<?
include_once "/var/www/vlav/data/www/wwl/inc/top.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/pact.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
include "init.inc.php";

if($_SESSION['access_level']>3) {
	print "<p class='alert alert-danger' >Нет доступа</p>";
	exit;
}

//$css="<script src='https://cdn.tiny.cloud/1/f2xzffdauodyzgkolvlho4tj9b7wf4iebzjolyv6rl3ihfdw/tinymce/6/tinymce.min.js' referrerpolicy='origin'></script>";
$css="<script src='https://for16.ru/tinymce/tinymce.min.js'></script>";
$top=new top($database,'Профиль');
$db=new vkt($database);

include "/var/www/vlav/data/www/wwl/inc/s_panel_upload_file.1.inc.php";

print "";

?>
<h2 class='text-center' >Профиль
	<button class="btn btn-link" data-toggle="collapse" data-target="#ctrl_id_encoded" aria-expanded="false" aria-controls="ctrl_id_encoded">
        <i class="fa fa-folder-open"></i>
    </button>
</h2>
<div class='collapse text-center' id='ctrl_id_encoded'>
	<p>аккаунт <?=$db->encode_ctrl_id($ctrl_id)?></p>
</div>
<?

if(isset($_POST['do_save_0'])) {
	if(intval($ctrl_id)) {
		$msg='';
		$db=new db('vkt');
		$company=substr($_POST['company'],0,255);
		$company_data=substr($_POST['company_data'],0,1000);
		$db->query("UPDATE 0ctrl SET
			company='".$db->escape($company)."',
			company_data='".$db->escape($company_data)."'
			WHERE id='$ctrl_id'");
		if($msg=='')
			$msg='ok';
		$msg="ok";
		print "<script>location='?saved=yes&section=0&msg=".urlencode($msg)."#section_0'</script>";
	} else {
		print "<p class='alert alert-danger' >Ошибка ctrl_id 0. Сообщите техподдержке.</p>";
	}
}

if(isset($_POST['do_save_1'])) {
	if(intval($ctrl_id)) {
		$msg='';
		$db=new db('vkt');

		$fee_1=floatval($_POST['fee_1']);
		$fee_2=floatval($_POST['fee_2']);
		$fee_1_old=$db->dlookup("fee_1","0ctrl","id=$ctrl_id");
		$fee_2_old=$db->dlookup("fee_2","0ctrl","id=$ctrl_id");

		if($fee_1_old!=$fee_1) {
			//~ $db->connect($database);
			//~ $db->query("UPDATE users SET fee='$fee_1' WHERE access_level=5");
		}
		if($fee_2_old!=$fee_2) {
			//~ $db->connect($database);
			//~ $db->query("UPDATE users SET fee2='$fee_2' WHERE access_level=5");
		}

		$keep=isset($_POST['keep'])?1:0;
		$fl_cabinet2=isset($_POST['fl_cabinet2'])?1:0;
		$db->connect('vkt');
		$db->query("UPDATE 0ctrl SET
			fee_hello='".intval($_POST['fee_hello'])."',
			hold='".intval($_POST['hold'])."',
			keep='".$keep."',
			fl_cabinet2='".$fl_cabinet2."'
			WHERE id='$ctrl_id'",0);

		if($msg=='')
			$msg='ok';
		$msg="ok";
		print "<script>location='?saved=yes&section=1&msg=".urlencode($msg)."#section_1'</script>";
	} else
		print "<p class='alert alert-danger' >Ошибка ctrl_id 1. Сообщите техподдержке.</p>";
}

if(isset($_POST['do_save_2'])) {
	if(intval($ctrl_id)) {
		$msg='';
		$db=new db('vkt');

		$vk_group_id=0;

		if(empty($_POST['vk_access_key'])) {
			$msg.='Ключ доступа сообщества ВК не задан. \n';
		}
		$vk=new vklist_api($db->dlookup("token","vklist_acc","id=2"));
		if(!empty($_POST['vk_group_url']) ) {
			if($vk_group_id=$vk->vk_get_group_id_by_domain($_POST['vk_group_url'])) {
				//print "HERE_$vk_group_id"; exit;
			} else {
				$msg.="Ссылка на группу вк указана с ошибкой или Ключ доступа сообщества ВК не задан или задан с ошибкой. \n";
				//$_POST['vk_group_url']='';
			}
		} else
			$msg.='Ссылка на сообщество ВК не указана. Должно быть https://vk.com/ваша_группа или ваша_группа.\n';
		
		if(!empty($_POST['vk_access_key'] && $vk_group_id) ) {
			$vk=new vklist_api($_POST['vk_access_key']);
			if($vk->vk_get_group_id_by_domain('yogahelpyou')) { //for test
				$vkt=new vkt($database);
				$access_token=$_POST['vk_access_key'];
				$ctrl_dir=$vkt->get_ctrl_dir($ctrl_id);
				$url="https://for16.ru/d/$ctrl_dir/vk_callback.php";
				$v='5.131';
				$senler_secret='';
				$vk_confirmation_token='';

				//get confirmation code
				$request_params = array(
				'group_id'=>$vk_group_id,
				'access_token' => $access_token,
				'v' => $v,
				);

				$get_params = http_build_query($request_params);

				$res=json_decode(file_get_contents('https://api.vk.com/method/groups.getCallbackConfirmationCode?'. $get_params),true);
				$vk_confirmation_token=$res['response']['code'];

				//print "vk_confirmation_token=$vk_confirmation_token <br>";
				if(empty($vk_confirmation_token))
					$msg.= "Не определился vk_confirmation_token.\n";
				else {
					$db->connect('vkt');
					$db->query("UPDATE 0ctrl SET vk_confirmation_token='$vk_confirmation_token' WHERE id='$ctrl_id'");
				}
				//GET Callback SERVERS LIST
				$request_params = array(
				'server_ids' =>'' ,
				'group_id'=>$vk_group_id,
				'access_token' => $access_token,
				'v' => $v,
				);

				$get_params = http_build_query($request_params);

				$res=json_decode(file_get_contents('https://api.vk.com/method/groups.getCallbackServers?'. $get_params),true);

				$vktrade_installed=false;
				$server_id=false;
				foreach($res['response']['items'] AS $r) {
					//$db->print_r($r); 
					if($r['title']=='Senler') {
						if(!empty($senler_secret=$r['secret_key'])) {
							$db->connect('vkt');
							$db->query("UPDATE 0ctrl SET senler_secret='$senler_secret' WHERE id='$ctrl_id'");
						} else
							$msg.='Секретный ключ SENLER пустой, хотя сенлер установлен. Ошибка.\n';
					}
					if($r['title']=='VKTRADE') {
						$server_id=$r['id'];
						$request_params = array( //update existing callback server
						'group_id'=>$vk_group_id,
						'server_id'=>$server_id,
						'url' => $url,
						'title'=>'VKTRADE',
						'secret_key'=>crc32($ctrl_id),
						'access_token' => $access_token,
						'v' => $v,
						);

						$get_params = http_build_query($request_params);

						$res=json_decode(file_get_contents('https://api.vk.com/method/groups.editCallbackServer?'. $get_params),true);
						if($res['response']==1) {
							$vktrade_installed=true;
						} else
							$msg.='Не удалось обновить callback сервер VKTRADE Ошибка. \n';
					}
				}
				if(empty($senler_secret))
					$msg.= "ok Senler не установлен в сообщество. Это предупреждение - ошибкой не является, если вы не используете сенлер\n";
				
				if(!$vktrade_installed) { //insert new callback server
						$request_params = array(
						'group_id'=>$vk_group_id,
						'url' => $url,
						'title'=>'VKTRADE',
						'secret_key'=>crc32($ctrl_id),
						'access_token' => $access_token,
						'v' => $v,
						);

						$get_params = http_build_query($request_params);

						$res=json_decode(file_get_contents('https://api.vk.com/method/groups.addCallbackServer?'. $get_params),true);
						if(isset($res['response']['server_id'])) {
							$server_id=intval($res['response']['server_id']);
							$vktrade_installed=true;
						} else
							$msg.='Не удалось установить callback сервер VKTRADE. Ошибка \n';
				}
				
				$db->connect('vkt');
				$db->query("UPDATE 0ctrl SET callback_server_id='$server_id' WHERE id='$ctrl_id'");

				//SET UP api version and events
				if($vktrade_installed) {
					$request_params = array(
					'group_id'=>$vk_group_id,
					'server_id'=>$server_id,
					'message_new'=>1,
					'message_allow'=>1,
					'message_deny'=>1,
					'access_token' => $access_token,
					'api_version' => $v,
					'v' => $v,
					);

					$get_params = http_build_query($request_params);

					$res=json_decode(file_get_contents('https://api.vk.com/method/groups.setCallbackSettings?'. $get_params),true);
					//$db->print_r($res);
					if($res['response']!=1)
						$msg.='Не удалось установить события для callback сервер VKTRADE. Ошибка. \n';
				}
			} else {
				$msg= "Ключ доступа сообщества ВК указан неверно!";
				$_POST['vk_access_key']='';
			}
		}

		$db->connect('vkt');
		$db->query("UPDATE 0ctrl SET
			vk_group_id='$vk_group_id',
			vk_group_url='".$db->escape($_POST['vk_group_url'])."'
			WHERE id='$ctrl_id'",0);
			//~ vk_confirmation_token='".$db->escape($_POST['vk_confirmation_token'])."',
			//~ senler_secret='".$db->escape($_POST['senler_secret'])."',
		$db=new db($database);
		//print "HERE $database"; exit;
		$db->query("UPDATE vklist_acc SET
			token='".$db->escape($_POST['vk_access_key'])."',
			tm='".time()."'
			WHERE id=2");
		if($msg=='')
			$msg='ok';
	//print "HERE_$msg"; exit;
		//$msg="test";
		print "<script>location='?saved=yes&section=2&msg=".urlencode($msg)."#section_2'</script>";
	} else
		print "<p class='alert alert-danger' >Ошибка ctrl_id 2. Сообщите техподдержке.</p>";
}

if(isset($_POST['do_save_3'])) {
	if(intval($ctrl_id)) {
		$msg='';
		$db=new db('vkt');

		if(!$senler_gid_partnerka=intval($_POST['senler_gid_partnerka'])) {
			preg_match("/s=([0-9]+)/",$_POST['senler_gid_partnerka'],$m);
			$senler_gid_partnerka=intval($m[1]);
		}
		if(!$senler_gid_partnerka)
			$msg.= 'Ссылка на группу подписчиков в сенлер ДЛЯ ПАРТНЕРОВ указана неверно. \n';

		if(!$senler_gid_land=intval($_POST['senler_gid_land'])) {
			preg_match("/s=([0-9]+)/",$_POST['senler_gid_land'],$m);
			$senler_gid_land=intval($m[1]);
		}
		if(!$senler_gid_land)
			$msg.= 'Ссылка на группу подписчиков в сенлер ОСНОВНОЙ ЛЭНДИНГ указана неверно. \n';

		$db->connect('vkt');
		$db->query("UPDATE 0ctrl SET
			senler_gid_partnerka='$senler_gid_partnerka',
			senler_gid_land='$senler_gid_land'
			WHERE id='$ctrl_id'",0);

		if($msg=='')
			$msg='ok';
		print "<script>location='?saved=yes&section=3&msg=".urlencode($msg)."#section_3'</script>";
	} else
		print "<p class='alert alert-danger' >Ошибка ctrl_id 3. Сообщите техподдержке.</p>";
}

if(isset($_POST['do_save_4'])) {
	if(intval($ctrl_id)) {
		$msg='';
		$db=new db('vkt');

		$db->connect('vkt');
		$db->query("UPDATE 0ctrl SET
			tg_bot_notif='".$db->escape($_POST['tg_bot_notif'])."'
			WHERE id='$ctrl_id'",0);

		if($msg=='')
			$msg='ok';
		print "<script>location='?saved=yes&section=4&msg=".urlencode($msg)."#section_4'</script>";
	} else
		print "<p class='alert alert-danger' >Ошибка ctrl_id 4. Сообщите техподдержке.</p>";
}

if(isset($_POST['do_save_5'])) {
	if(intval($ctrl_id)) {
		$msg='';
		$db=new db('vkt');

		$tg_bot_msg_name=preg_replace("|\@|i","",$_POST['tg_bot_msg_name']);
		$tg_bot_msg_name=preg_replace("|.*?\/|i","",$tg_bot_msg_name);
		$tg_bot_msg_off_income = isset($_POST['tg_bot_msg_off_income']) ? 1 : 0;
		if(preg_match("/bot/i",$tg_bot_msg_name)) {
			$db->query("UPDATE 0ctrl SET
				tg_bot_msg_name='".$db->escape($tg_bot_msg_name)."',
				tg_bot_msg='".$db->escape($_POST['tg_bot_msg'])."',
				tg_bot_msg_off_income='$tg_bot_msg_off_income'
				WHERE id='$ctrl_id'",0);
			
			include_once "/var/www/vlav/data/www/wwl/inc/tg_bot.class.php";
			$tg=new tg_bot($_POST['tg_bot_msg']);
			if($ctrl_id && !$tg_bot_msg_off_income) {
				if(!$tg->set_webhook('https://for16.ru/d/'.$ctrl_dir.'/tg_bot.php'))
					$msg="Установить вебхук не удалось. Вероятно токен бота неправильный.";
			}
		} else
			$msg="ошибка: имя чатбота должно включать часть- bot";

		if($msg=='')
			$msg='ok';
		print "<script>location='?saved=yes&section=5&msg=".urlencode($msg)."#section_5'</script>";
	} else
		print "<p class='alert alert-danger' >Ошибка ctrl_id 5. Сообщите техподдержке.</p>";
}

if(isset($_POST['do_save_6'])) {
	if(intval($ctrl_id)) {
		$msg='';
		$db=new db('vkt');
		$db->query("UPDATE 0ctrl SET
			land='".$db->escape(substr($_POST['land'],0,127))."',
			land_txt='".$db->escape(substr($_POST['land_txt'],0,10000))."',
			thanks_txt='".$db->escape(substr($_POST['thanks_txt'],0,10000))."',
			pp='".$db->escape(substr($_POST['pp'],0,256))."',
			oferta='".$db->escape(substr($_POST['oferta'],0,256))."',
			agreement='".$db->escape(substr($_POST['agreement'],0,256))."',
			oferta_referal='".$db->escape(substr($_POST['oferta_referal'],0,256))."',
			partnerka_adlink='".$db->escape(substr($_POST['partnerka_adlink'],0,256))."',
			pixel_ya='".intval($_POST['pixel_ya'])."',
			pixel_vk='".intval($_POST['pixel_vk'])."',
			bot_first_msg='".$db->escape(substr($_POST['bot_first_msg'],0,2000))."'
			WHERE id='$ctrl_id'");
		if($msg=='')
			$msg='ok';
		print "<script>location='?saved=yes&section=6&msg=".urlencode($msg)."#section_6'</script>";
	} else
		print "<p class='alert alert-danger' >Ошибка ctrl_id 6. Сообщите техподдержке.</p>";

}

if(isset($_POST['do_save_7'])) {
	if(intval($ctrl_id)) {
		$msg='';
		$db=new db('vkt');
		$db->query("UPDATE 0ctrl SET
			land_p='".$db->escape(substr($_POST['land_p'],0,127))."',
			land_txt_p='".$db->escape(substr($_POST['land_txt_p'],0,10000))."',
			thanks_txt_p='".$db->escape(substr($_POST['thanks_txt_p'],0,10000))."',
			bot_first_msg_p='".$db->escape(substr($_POST['bot_first_msg_p'],0,2000))."'
			WHERE id='$ctrl_id'");
		if($msg=='')
			$msg='ok';
		print "<script>location='?saved=yes&section=7&msg=".urlencode($msg)."#section_7'</script>";
	} else
		print "<p class='alert alert-danger' >Ошибка ctrl_id 7. Сообщите техподдержке.</p>";

}

if(isset($_POST['do_save_8'])) { //BIZON
	if(intval($ctrl_id)) {
		$msg='';
		$db=new db('vkt');
		$bizon_web_duration=intval($_POST['bizon_web_duration']);
		$bizon_web_zachet_proc=intval($_POST['bizon_web_zachet_proc']);
		$db->query("UPDATE 0ctrl SET
			bizon_api_token='".$db->escape($_POST['bizon_api_token'])."',
			bizon_web_zachet_proc='$bizon_web_zachet_proc',
			bizon_web_duration='$bizon_web_duration'
			WHERE id='$ctrl_id'");
		if($msg=='')
			$msg='ok';
		print "<script>location='?saved=yes&section=8&msg=".urlencode($msg)."#section_8'</script>";
	} else
		print "<p class='alert alert-danger' >Ошибка ctrl_id 8. Сообщите техподдержке.</p>";
}

if(isset($_POST['do_save_9_prodamus'])) { //PRODAMUS
	if(intval($ctrl_id)) {
		$db->connect($database);
		if($db->num_rows($db->query("SELECT * FROM pay_systems WHERE 1"))==0) {
			$db->query("INSERT INTO pay_systems SET prodamus_linktoform=''");
		}
		$url=trim($_POST['prodamus_linktoform']);
		if (!preg_match('/^https?:\/\//', $url)) {
			// Prepend "https://" if no scheme is found
			$url = 'https://' . ltrim($url, '/');
		}
		$db->query("UPDATE pay_systems SET
			prodamus_secret='".$db->escape(substr(trim($_POST['prodamus_secret']),0,128))."',
			prodamus_linktoform='".$db->escape(substr($url,0,128))."'
			WHERE 1
			");
		if($msg=='')
			$msg='ok';
		print "<script>location='?saved=yes&section=9&msg=".urlencode($msg)."#section_9_prodamus'</script>";
	} else
		print "<p class='alert alert-danger' >Ошибка ctrl_id 9. Сообщите техподдержке.</p>";
}
if(isset($_POST['do_save_9_alfabank'])) { //PRODAMUS
	if(intval($ctrl_id)) {
		$db->connect($database);
		if($db->num_rows($db->query("SELECT * FROM pay_systems WHERE 1"))==0) {
			$db->query("INSERT INTO pay_systems SET prodamus_linktoform=''");
		}
		$db->query("UPDATE pay_systems SET
			alfa_secret='".$db->escape(substr($_POST['alfabank_secret'],0,128))."',
			alfa_url='".$db->escape(substr($_POST['alfabank_url'],0,128))."',
			alfa_passw='".$db->escape(substr($_POST['alfabank_passw'],0,128))."'
			WHERE 1
			");
		if($msg=='')
			$msg='ok';
		print "<script>location='?saved=yes&section=9&msg=".urlencode($msg)."#section_9_alfa'</script>";
	} else
		print "<p class='alert alert-danger' >Ошибка ctrl_id 9. Сообщите техподдержке.</p>";
}
if(isset($_POST['do_save_9_yookassa'])) { //
	if(intval($ctrl_id)) {
		$db->connect($database);
		if($db->num_rows($db->query("SELECT * FROM pay_systems WHERE 1"))==0) {
			$db->query("INSERT INTO pay_systems SET prodamus_linktoform=''");
		}
		$db->query("UPDATE pay_systems SET
			yookassa_secret='".$db->escape(substr($_POST['yookassa_secret'],0,128))."',
			yookassa_passw='".$db->escape(substr($_POST['yookassa_passw'],0,128))."'
			WHERE 1
			");
		if($msg=='')
			$msg='ok';
		print "<script>location='?saved=yes&section=9&msg=".urlencode($msg)."#section_9_yookassa'</script>";
	} else
		print "<p class='alert alert-danger' >Ошибка ctrl_id 9. Сообщите техподдержке.</p>";
}
if(isset($_POST['do_save_9_robokassa'])) { //
	if(intval($ctrl_id)) {
		$db->connect($database);
		if($db->num_rows($db->query("SELECT * FROM pay_systems WHERE 1"))==0) {
			$db->query("INSERT INTO pay_systems SET prodamus_linktoform=''");
		}
		$db->query("UPDATE pay_systems SET
			robokassa_id='".$db->escape(substr(trim($_POST['robokassa_id']),0,128))."',
			robokassa_passw_1='".$db->escape(substr(trim($_POST['robokassa_passw_1']),0,128))."',
			robokassa_passw_2='".$db->escape(substr(trim($_POST['robokassa_passw_2']),0,128))."'
			WHERE 1
			");
		if($msg=='')
			$msg='ok';
		print "<script>location='?saved=yes&section=9&msg=".urlencode($msg)."#section_9_robokassa'</script>";
	} else
		print "<p class='alert alert-danger' >Ошибка ctrl_id 9. Сообщите техподдержке.</p>";
}

if(isset($_POST['do_save_10_unisender'])) { //UNISENDER
	if(intval($ctrl_id)) {
		$msg='';
		$db->connect('vkt');
		$db->query("UPDATE 0ctrl SET
			unisender_secret='".$db->escape(substr($_POST['unisender_secret'],0,64))."',
			email_from='".$db->escape(substr($_POST['email_from'],0,64))."',
			email_from_name='".$db->escape(substr($_POST['email_from_name'],0,64))."'
			WHERE id='$ctrl_id'");
		if($msg=='')
			$msg='ok';
		print "<script>location='?saved=yes&section=10&msg=".urlencode($msg)."#section_10'</script>";
	} else
		print "<p class='alert alert-danger' >Ошибка ctrl_id 10. Сообщите техподдержке.</p>";

}

if(isset($_POST['do_save_12_pact'])) { //PACT
	if(intval($ctrl_id)) {
		$msg='';
		$pact_secret=substr(trim($_POST['pact_secret']),0,128); //6dac370b7133847c9230239533b7a0a1667cfd1ff30ee9695a5861b7fe6b662aacdb3988b032656858b10231f937ad2025d96a34c862a54b83435e0282b2c318
		$pact_company_name=substr(trim($_POST['pact_company_name']),0,64);
		//$db->notify_me($pact_secret);
		$p=new pact();
		$p->login($pact_secret,0);
		if($pact_company_id=$p->get_company_id($pact_company_name)) {
			$db->connect('vkt');
			$db->query("UPDATE 0ctrl SET
				pact_secret='".$db->escape($pact_secret)."',
				pact_company_id='$pact_company_id'
				WHERE id='$ctrl_id'");
		} else
			$msg="Ошибка подключения PACT. Вероятно токен недействительный.";
		if($msg=='')
			$msg='ok';
		print "<script>location='?saved=yes&section=12&msg=".urlencode($msg)."#section_12'</script>";
	} else
		print "<p class='alert alert-danger' >Ошибка ctrl_id 12. Сообщите техподдержке.</p>";

}

if(isset($_POST['do_save_13_vsegpt'])) { //VSEGPT
	if(intval($ctrl_id)) {
		$msg='';
		$vsegpt_secret=substr($_POST['vsegpt_secret'],0,128);
		$vsegpt_model=substr($_POST['vsegpt_model'],0,1024);
		$vsegpt_delay_sec=intval($_POST['vsegpt_delay_sec']);
		$db->connect('vkt');
		$db->query("UPDATE 0ctrl SET
			vsegpt_secret='".$db->escape($vsegpt_secret)."',
			vsegpt_model='".$db->escape($vsegpt_model)."',
			vsegpt_delay_sec='$vsegpt_delay_sec'
			WHERE id='$ctrl_id'");
		if($msg=='')
			$msg='ok';
		print "<script>location='?saved=yes&section=13&msg=".urlencode($msg)."#section_13'</script>";
	} else
		print "<p class='alert alert-danger' >Ошибка ctrl_id 13. Сообщите техподдержке.</p>";

}

if(isset($_POST['do_save_16_insales'])) { 
	if(intval($ctrl_id)) {
		$msg='';
		$insales_shop_id=intval($_POST['insales_shop_id']);
		$insales_status=substr(trim($_POST['insales_status']),0,64);
		$db->connect('vkt');
		if(!$db->dlookup("id","0ctrl","del=0 AND insales_shop_id='$insales_shop_id' AND id != $ctrl_id") || !$insales_shop_id) {
			$db->query("UPDATE 0ctrl SET
				insales_shop_id='$insales_shop_id',
				insales_status='".$db->escape($insales_status)."'
				WHERE id='$ctrl_id'",0);
			if($msg=='')
				$msg='ok';
		} else
			$msg="Ошибка. Аккаунт $insales_shop_id ";
		print "<script>location='?saved=yes&section=16&msg=".urlencode($msg)."#section_16'</script>";
	} else
		print "<p class='alert alert-danger' >Ошибка ctrl_id 16. Сообщите техподдержке.</p>";

}


if(isset($_GET['saved'])) {
	if(!preg_match("|^ok|i",$_GET['msg']) && !empty($_GET['msg']))
		$msg= "<p class='alert alert-danger' >".nl2br(preg_replace('/^ok/','',$_GET['msg']))."</p>";
	else
		$msg= "<p class='alert alert-success' >Настройки сохранены. ".nl2br(preg_replace('/^ok/','',$_GET['msg']))."</p>";
}

if(1) { //init of variables
//init of variables
$db=new db($database);
$vk_access_key=$db->dlookup("token","vklist_acc","id=2");
//$vk_access_key='XXXXXXXXXXXXXXXXXXX';

$prodamus_secret=$db->dlookup("prodamus_secret","pay_systems","1");
$prodamus_linktoform=$db->dlookup("prodamus_linktoform","pay_systems","1");

$alfabank_secret=$db->dlookup("alfa_secret","pay_systems","1");
$alfabank_url=$db->dlookup("alfa_url","pay_systems","1");
$alfabank_passw=$db->dlookup("alfa_passw","pay_systems","1");

$yookassa_secret=$db->dlookup("yookassa_secret","pay_systems","1");
$yookassa_passw=$db->dlookup("yookassa_passw","pay_systems","1");

$robokassa_id=$db->dlookup("robokassa_id","pay_systems","1");
$robokassa_passw_1=$db->dlookup("robokassa_passw_1","pay_systems","1");
$robokassa_passw_2=$db->dlookup("robokassa_passw_2","pay_systems","1");


$db=new db('vkt');

$r=$db->fetch_assoc($db->query("SELECT * FROM 0ctrl WHERE id=$ctrl_id"));

$company=$r['company']; //$db->dlookup("company","0ctrl","id=$ctrl_id");
$company_data=$r['company_data']; //$db->dlookup("company_data","0ctrl","id=$ctrl_id");

$vk_group_url=$r['vk_group_url']; //$db->dlookup("vk_group_url","0ctrl","id=$ctrl_id");
$vk_confirmation_token=$r['vk_confirmation_token']; //$db->dlookup("vk_confirmation_token","0ctrl","id=$ctrl_id");
//$vk_confirmation_token='XXXXXXXXXXXXXXXXXXX';

$fee_1=$r['fee_1']; //$db->dlookup("fee_1","0ctrl","id=$ctrl_id");
$fee_2=$r['fee_2']; //$db->dlookup("fee_2","0ctrl","id=$ctrl_id");
$fee_hello=$r['fee_hello']; //$db->dlookup("fee_hello","0ctrl","id=$ctrl_id");
$hold=$r['hold']; //$db->dlookup("hold","0ctrl","id=$ctrl_id");
if(!$hold) {
	$hold=180;
	$db->query("UPDATE 0ctrl SET hold='$hold' WHERE id='$ctrl_id'");
}
$keep_checked=$r['keep']?"CHECKED":"";
$fl_cabinet2=$r['fl_cabinet2']?"CHECKED":"";

$senler_secret=$db->dlookup("senler_secret","0ctrl","id=$ctrl_id");
//$senler_secret='XXXXXXXXXXXXXXXXXXX';
$senler_gid_partnerka=$db->dlookup("senler_gid_partnerka","0ctrl","id=$ctrl_id");
$senler_gid_land=$db->dlookup("senler_gid_land","0ctrl","id=$ctrl_id");
$senler_link_partnerka=($senler_gid_partnerka)?"https://vk.com/app5898182_-$vk_group_id#s=$senler_gid_partnerka":'';
$senler_link_land=($senler_gid_land)?"https://vk.com/app5898182_-$vk_group_id#s=$senler_gid_land":'';

$tg_bot_notif=$db->dlookup("tg_bot_notif","0ctrl","id=$ctrl_id");
//$tg_bot_notif='XXXXXXXXXXXXXXXXXXX';
$tg_bot_msg=$db->dlookup("tg_bot_msg","0ctrl","id=$ctrl_id");
$tg_bot_msg_name=$db->dlookup("tg_bot_msg_name","0ctrl","id=$ctrl_id");
$tg_bot_msg_off_income=$db->dlookup("tg_bot_msg_off_income","0ctrl","id=$ctrl_id");

$uid_vkt=$db->dlookup("uid","0ctrl","id=$ctrl_id");
$uid_vkt_md5=$db->uid_md5($uid_vkt);
//print "HERE_$uid_vkt $uid_vkt_md5";
$tg_id=intval($db->dlookup("telegram_id","cards","uid='$uid_vkt'"));
$klid_vkt=$db->dlookup("id","cards","uid='$uid_vkt'");

$land_txt=$db->dlookup("land_txt","0ctrl","id=$ctrl_id");
$thanks_txt=$db->dlookup("thanks_txt","0ctrl","id=$ctrl_id");
$bot_first_msg=$db->dlookup("bot_first_msg","0ctrl","id=$ctrl_id");
$land=$db->dlookup("land","0ctrl","id=$ctrl_id");
$land_url=$land;

$pp=$db->dlookup("pp","0ctrl","id=$ctrl_id");
$oferta=$db->dlookup("oferta","0ctrl","id=$ctrl_id");
$agreement=$db->dlookup("agreement","0ctrl","id=$ctrl_id");
$oferta_referal=$db->dlookup("oferta_referal","0ctrl","id=$ctrl_id");
$partnerka_adlink=$db->dlookup("partnerka_adlink","0ctrl","id=$ctrl_id");

$pixel_ya=$db->dlookup("pixel_ya","0ctrl","id=$ctrl_id");
$pixel_vk=$db->dlookup("pixel_vk","0ctrl","id=$ctrl_id");
if(empty($land)) {
	$land="https://for16.ru/d/$ctrl_dir/1";
	$db->query("UPDATE 0ctrl SET land='".$db->escape($land)."' WHERE id='$ctrl_id'");
}

$land_txt_p=$db->dlookup("land_txt_p","0ctrl","id=$ctrl_id");
$thanks_txt_p=$db->dlookup("thanks_txt_p","0ctrl","id=$ctrl_id");
$bot_first_msg_p=$db->dlookup("bot_first_msg_p","0ctrl","id=$ctrl_id");
$land_p=$db->dlookup("land_p","0ctrl","id=$ctrl_id");
//$land_link_p=(empty($land_p))?"https://for16.ru/d/$ctrl_dir/2":$land_p;
if(empty($land_p)) {
	$land_p="https://for16.ru/d/$ctrl_dir/2";
	$db->query("UPDATE 0ctrl SET land_p='".$db->escape($land_p)."' WHERE id='$ctrl_id'");
}

$bizon_api_token=$db->dlookup("bizon_api_token","0ctrl","id=$ctrl_id");
$bizon_web_duration=$db->dlookup("bizon_web_duration","0ctrl","id=$ctrl_id");
if(!$bizon_web_zachet_proc=$db->dlookup("bizon_web_zachet_proc","0ctrl","id=$ctrl_id"))
	$bizon_web_zachet_proc=60;

$unisender_secret=$db->dlookup("unisender_secret","0ctrl","id=$ctrl_id");
$email_from=$db->dlookup("email_from","0ctrl","id=$ctrl_id");
$email_from_name=$db->dlookup("email_from_name","0ctrl","id=$ctrl_id");

$pact_secret=$db->dlookup("pact_secret","0ctrl","id=$ctrl_id");
$pact_company_id=$db->dlookup("pact_company_id","0ctrl","id=$ctrl_id");

$vsegpt_secret=$db->dlookup("vsegpt_secret","0ctrl","id=$ctrl_id");
$vsegpt_model=$db->dlookup("vsegpt_model","0ctrl","id=$ctrl_id");
$vsegpt_delay_sec=$db->dlookup("vsegpt_delay_sec","0ctrl","id=$ctrl_id");

$insales_shop_id=$r['insales_shop_id']; //$db->dlookup("insales_shop_id","0ctrl","id=$ctrl_id");
$insales_token=$r['insales_token']; //$db->dlookup("insales_token","0ctrl","id=$ctrl_id");
$insales_shop=$r['insales_shop']; //$db->dlookup("insales_shop","0ctrl","id=$ctrl_id");
$insales_status=$r['insales_status']; //$db->dlookup("insales_status","0ctrl","id=$ctrl_id");

$api_secret=!empty($r['api_secret']) ? $r['api_secret'] : $db->get_api_secret($ctrl_id);

$db=new db($database);
//~ if($klid_vkt==$_SESSION['userid_sess']) {
	//~ if($tg_id) {
		//~ $db->query("UPDATE users SET telegram_id='$tg_id' WHERE id='{$_SESSION['userid_sess']}'");
	//~ } else {
		//~ print "<p class='alert alert-warning' >Вы не подключены к телеграм боту техподдержки. Это нужно сделать <a href='https://t.me/vkt_support_bot?start=$uid_vkt_md5' class='' target=''>по ссылке</a>.</p>";
	//~ }
//~ }
$last_land_num=$db->dlast("land_num","lands","del=0");


$collapse='collapse';
}
?>

<div class='container' >

<p><a href='https://help.winwinland.ru/docs-category/profil/' class='' target='_blank'>документация и видео по настройкам</a></p>

<form method='POST' enctype='multipart/form-data'> <!--0-->
<div class='card bg-light py-3' id='section_0' >
<?
if(isset($_GET['saved'])) {
	if($_GET['section']==0) {
		print "$msg"; $collapse="";
	} else
		$collapse="collapse";
}
?>
	<h3 class='text-center' >
		Название и реквизиты <a href='#c0' data-toggle='collapse' ><span class="fa fa-folder-open"></span></a>
	</h3>
	<div class='<?=$collapse?>'  id='c0'>
	<div class='form-group top10'>
	<label for='company'>Название компании</label>
	<input type='text' id='company' name='company' value='<?=$company?>' class='form-control' >
	</div>
	<div class='form-group top10'>
	<label for='company_data'>Реквизиты</label>
	<textarea id='company_data' name='company_data' class='form-control' rows='5'><?=$company_data?></textarea>
	</div>
	<div><button type="submit" class="btn btn-primary" name='do_save_0' value='yes'>Записать</button></div>
	<input type='hidden' name='csrf_token' value='<?=$_SESSION['csrf_token']?>'>
	<input type='hidden' name='csrf_name' value='s_panel'>

	<label for='thanks_pic'>Загрузить логотип</label>
	<div><?s_panel_upload_file($upload_id='logo',$upload_dir='tg_files/',0,0.1,true);?></div>
	<small id='' class='form-text text-muted'>Рекомендуемый размер изображения 200x50 px (4:1 пропорции длина : ширина)</small>
	<br>
	<br>
	</div>
</div>
</form>

	
<form method='POST'> <!--1-->
<div class='card bg-light py-3' id='section_1' >
<?
if(isset($_GET['saved'])) {
	if($_GET['section']==1) {
		print "$msg"; $collapse="";
	} else
		$collapse="collapse";
}
?>
	<h3 class='text-center' >
<!--
		<a href='products.php' class='btn btn-warning btn-sm' target=''>Товары/услуги</a>
-->
		Настройка партнерской программы <a href='#c1' data-toggle='collapse' ><span class="fa fa-folder-open"></span></a>
	</h3>
	<div class='<?=$collapse?> p-3'  id='c1'>
		<div class='card p-3' >
			<div class='form-group'>
				<label for='fee_hello'>Приветственные баллы</label>
				<input type='text' id='fee_hello' name='fee_hello' value='<?=$fee_hello?>' class='form-control' >
			</div>
			<div class='form-group'>
				<label for='hold'>Срок закрепления приглашенных за партнером (дней)</label>
				<input type='text' id='hold' name='hold' value='<?=$hold?>' class='form-control' >
			</div>

			<div class='form-check'>
				<input type='checkbox' id='keep' name='keep' <?=$keep_checked?> class='form-check-input' > 
				<label for='keep' class='form-check-label' >передавать новому партнеру</label>
			</div>

			<div class='card_ p-2 my-3 small bg-light' >
				<p>* размеры партнерских вознаграждений указываются
				в <a href='products.php' class='' target='_blank'>продуктах</a>,
				<a href='reports/promocodes.php' class='' target='_blank'>промокодах</a> или индивидуальных профилях партнеров
				</p>
			</div>
		</div>

		<div class='form-check my-3'>
			<input  type='checkbox' id='fl_cabinet2' name='fl_cabinet2' <?=$fl_cabinet2?> class='form-check-input' > 
			<label for='keep' class='form-check-label' >Дизайн партнерского кабинета 2026</label>
<!--
			<p class='small text-muted' >скоро будет доступно</p>
-->
		</div>
		<button type="submit" class="btn btn-primary" name='do_save_1' value='yes'>Записать</button>
		<input type='hidden' name='csrf_token' value='<?=$_SESSION['csrf_token']?>'>
		<input type='hidden' name='csrf_name' value='s_panel'>
	</div>
</div>
</form>

<form method='POST'> <!--2-->
<div class='card bg-light py-3'  id='section_2'>
<?
if(isset($_GET['saved'])) {
	if($_GET['section']==2) {
		print "$msg"; $collapse="";
	} else
		$collapse="collapse";
}
?>
	<h3 class='text-center' >Настройки сообщества ВК <a href='#c2' data-toggle='collapse' ><span class="fa fa-folder-open"></span></a></h3>
	<div class='<?=$collapse?>'  id='c2'>

	<div class='form-group'>
	<label for='vk_group_url'>Ссылка на сообщество ВК</label>
	<input type='text' id='vk_group_url' name='vk_group_url' value='<?=$vk_group_url?>' class='form-control' >
	<small id='vk_group_url_help' class='form-text text-muted'>Должно быть https://vk.com/ваша_группа или ваша_группа</small>
	</div>

	<div class='form-group top10'>
	<label for='vk_access_key'>Ключ доступа сообщества ВК</label>
	<input type='text' id='vk_access_key' name='vk_access_key' value='<?=$vk_access_key?>' class='form-control' >
<!--
	<small id='vk_access_key_help' class='form-text text-muted'>Где взять</small>
-->
<!--
	<div class='card bg-light py-3 card bg-light py-3-sm' >
		<p>Callback API - Настройки сервера - Адрес: <span class='badge' ><?=$DB200?>/vk_callback.php</span></p>
	</div>
-->
	</div>

<!--
	<div class='form-group'>
	<label for='vk_confirmation_token'>Строка, которую должен вернуть сервер</label>
	<input type='text' id='vk_confirmation_token' name='vk_confirmation_token' value='<?=$vk_confirmation_token?>' class='form-control' >
	</div>
-->

	<p>Инструкция по настройке <a href='https://youtu.be/kGEggW3AK0M' class='' target='_blank'>на youtube</a></p>
	<div><button type="submit" class="btn btn-primary" name='do_save_2' value='yes'>Записать</button></div>
	<input type='hidden' name='csrf_token' value='<?=$_SESSION['csrf_token']?>'>
	<input type='hidden' name='csrf_name' value='s_panel'>
	</div>
</div>
</form>

<form method='POST'> <!--3-->
<div class='card bg-light py-3'  id='section_3'>
<?
if(isset($_GET['saved'])) {
	if($_GET['section']==3) {
		print "$msg"; $collapse="";
	} else
		$collapse="collapse";
}
?>
	<h3 class='text-center' >Настройки SENLER <a href='#c3' data-toggle='collapse' ><span class="fa fa-folder-open"></span></a></h3>
	<div class='<?=$collapse?>'  id='c3'>
<!--
	<div class='form-group top10'>
	<label for='senler_secret'>Секретный ключ SENLER</label>
	<input type='text' id='senler_secret' name='senler_secret' value='<?=$senler_secret?>' class='form-control' >
	</div>
-->

	<div class='form-group'>
	<label for='senler_gid_partnerka'>Группа подписчиков SENLER - ссылка на лэндинг по партнерской программе</label>
	<input type='text' id='senler_gid_partnerka' name='senler_gid_partnerka' value='<?=$senler_link_partnerka?>' class='form-control' >
	<small id='senler_gid_partnerka_help' class='form-text text-muted'>
		<?if($senler_gid_partnerka)
			print "Ссылка на партнерский лэндинг <a href='$senler_link_partnerka' class='' target='_blank'>$senler_link_partnerka</a>";
		?>
	</small>
<!--
	<small id='senler_gid_partnerka_help' class='form-text text-muted'>Где взять</small>
-->
	</div>

	<div class='form-group'>
	<label for='senler_gid_land'>Группа подписчиков SENLER - ссылка на основной лэндинг</label>
	<input type='text' id='senler_gid_land' name='senler_gid_land' value='<?=$senler_link_land?>' class='form-control' >
	<small id='senler_gid_partnerka_help' class='form-text text-muted'>
		<?if($senler_gid_land)
			print "Ссылка на основной лэндинг <a href='$senler_link_land' class='' target='_blank'>$senler_link_land</a>";
		?>
	</small>
<!--
	<small id='senler_gid_land_help' class='form-text text-muted'>Где взять</small>
-->
	</div>

	<div class='card bg-light py-3 card bg-light py-3-sm' >
		<p>WebHook API - Url: <span class='badge' ><?=$DB200?>/vk_senler_callback.php</span></p>
	</div>
	<p>Инструкция по настройке <a href='https://youtu.be/uYQfr7l_DQ8' class='' target='_blank'>на youtube</a></p>
	<div><button type="submit" class="btn btn-primary" name='do_save_3' value='yes'>Записать</button></div>
	<input type='hidden' name='csrf_token' value='<?=$_SESSION['csrf_token']?>'>
	<input type='hidden' name='csrf_name' value='s_panel'>
	</div>

</div>
</form>

<form method='POST'> <!--4-->
<div class='card bg-light py-3'  id='section_4'>
<?
if(isset($_GET['saved'])) {
	if($_GET['section']==4) {
		print "$msg"; $collapse="";
	} else
		$collapse="collapse";
}
?>
	<h3 class='text-center' >Служебный ТГ бот для уведомлений из CRM <a href='#c4' data-toggle='collapse' ><span class="fa fa-folder-open"></span></a></h3>
	<div class='<?=$collapse?>'  id='c4'>
	<div class='form-group top10'>
	<label for='tg_bot_notif'>Токен телеграм бота для уведомлений</label>
	<input type='text' id='tg_bot_notif' name='tg_bot_notif' value='<?=$tg_bot_notif?>' class='form-control' >
<!--
	<small id='tg_bot_notif_help' class='form-text text-muted'>Где взять</small>
-->
	<p><a href='https://help.winwinland.ru/docs/sluzhebnyy-tg-bot-dlya-uvedomleniy-iz-crm/' class='' target='_blank'>Инструкция по настройке </a>
	<?
	if(!empty($tg_bot_notif) && !empty($tg_bot_msg) ) { //
		print " | <a href='https://t.me/$tg_bot_msg_name?start=u{$_SESSION['userid_sess']}' class='' target='_blank'>Подключить бот</a>";
	}
	?>
	</p>
	<div><button type="submit" class="btn btn-primary" name='do_save_4' value='yes'>Записать</button></div>
	<input type='hidden' name='csrf_token' value='<?=$_SESSION['csrf_token']?>'>
	<input type='hidden' name='csrf_name' value='s_panel'>
	</div>
	</div>
</div>
</form>

<form method='POST'> <!--5-->
<div class='card bg-light py-3'  id='section_5'>
<?
if(isset($_GET['saved'])) {
	if($_GET['section']==5) {
		print "$msg"; $collapse="";
	} else
		$collapse="collapse";
}
?>
	<h3 class='text-center' >Настройка чат бота телеграм для переписки <a href='#c5' data-toggle='collapse' ><span class="fa fa-folder-open"></span></a></h3>
	<div class='<?=$collapse?>'  id='c5'>
		<div class='form-group top10'>
			<label for='tg_bot_msg_name'>Телеграм бот (например example_bot)</label>
			<input type='text' id='tg_bot_msg_name' name='tg_bot_msg_name' value='<?=$tg_bot_msg_name?>' class='form-control' >
			<br>
			<label for='tg_bot_msg'>Токен бота</label>
			<input type='text' id='tg_bot_msg' name='tg_bot_msg' value='<?=$tg_bot_msg?>' class='form-control' >

			<div class="form-group">
			  <div class="form-check">
				  <?$tg_bot_msg_off_income_checked=$tg_bot_msg_off_income ? "CHECKED" : "";?>
				<input class="form-check-input" type="checkbox" id="tg_bot_msg_off_income" name="tg_bot_msg_off_income" <?=$tg_bot_msg_off_income_checked?>>
				<label class="form-check-label" for="tg_bot_msg_off_income">
				  Не включать прием входящих
				</label>
			  </div>
			</div>
			
			<p><a href='https://help.winwinland.ru/docs/nastroyka-chat-bota-telegram-dlya-perepiski/' class='' target='_blank'>Инструкция по настройке </a></p>
			<div><button type="submit" class="btn btn-primary" name='do_save_5' value='yes'>Записать</button></div>
			<input type='hidden' name='csrf_token' value='<?=$_SESSION['csrf_token']?>'>
			<input type='hidden' name='csrf_name' value='s_panel'>

		</div>
	</div>
</div>
</form>

<div class='card bg-light py-3'>
	<h3 class='text-center' >
		Настройка лэндингов
		<a href='s_panel_lands.php' target='_blank' title='перейти в новом окне'><span class="fa fa-arrow-circle-right"></span></a>
	</h3>
</div>


<form method='POST' enctype='multipart/form-data'> <!--6-->
<div class='card bg-light py-3'  id='section_6'>
<?
if(isset($_GET['saved'])) {
	if($_GET['section']==6) {
		print "$msg"; $collapse="";
	} else
		$collapse="collapse";
}
?>
	<h3 class='text-center' >
		Ссылки на документы и настройка пикселей <a href='#c6' data-toggle='collapse' ><span class="fa fa-folder-open"></span></a>
	</h3>
	<div class='<?=$collapse?>'  id='c6'>
	<label for='pp'>Ссылка на документ с политикой конфиденциальности</label>
	<input type='text' id='pp' name='pp' value='<?=$pp?>' class='form-control'>
	<small id='' class='form-text text-muted'>Укажите ссылку на документ с политикой конфиденциальности</small>

	<label for='oferta'>Ссылка на договор (оферту)</label>
	<input type='text' id='oferta' name='oferta' value='<?=$oferta?>' class='form-control'>
	<small id='' class='form-text text-muted'>Укажите ссылку на документ с договором</small>

	<label for='agreement'>Ссылка на согласие на обработку персональных данных</label>
	<input type='text' id='agreement' name='agreement' value='<?=$agreement?>' class='form-control'>
	<small id='' class='form-text text-muted'>Укажите ссылку на документ с согласием на обработку данных</small>

	<label for='oferta_referal'>Ссылка на договор об участии в партнерской программе</label>
	<input type='text' id='oferta_referal' name='oferta_referal' value='<?=$oferta_referal?>' class='form-control'>
	<small id='' class='form-text text-muted'>Укажите ссылку на договор об участии в партнерской программе.
		Документ, на основании которого вы будете делать выплаты партнерам</small>

	<label for='partnerka_adlink'>Ссылка на материалы для партнеров</label>
	<input type='text' id='partnerka_adlink' name='partnerka_adlink' value='<?=$partnerka_adlink?>' class='form-control'>
	<small id='' class='form-text text-muted'>Ссылка на гугл-диск или другое место, где хранятся рекламные материалы для партнеров. </small>


	<br>
	<br>
	
	<label for='pixel_ya'>Номер счетчика яндекс метрики</label>
	<input type='text' id='pixel_ya' name='pixel_ya' value='<?=$pixel_ya?>' class='form-control'>
	<small id='' class='form-text text-muted'>Целое число, код счетчика будет сгенерирован автоматически на лэндинге</small>
	<br>
	<label for='pixel_vk'>Код счетчика VK</label>
	<input type='text' id='pixel_vk' name='pixel_vk' value='<?=$pixel_vk?>' class='form-control'>
	<small id='' class='form-text text-muted'>Например VK-RTRG-1234567-6o7ck</small>
	
	<br>
	<br>


	<div><button type="submit" class="btn btn-primary btn-lg btn-block" name='do_save_6' value='yes'>Записать</button></div>
	<input type='hidden' name='csrf_token' value='<?=$_SESSION['csrf_token']?>'>
	<input type='hidden' name='csrf_name' value='s_panel'>
	</div>
</div>
</form>

<form method='POST' enctype='multipart/form-data'> <!--8-->
<div class='card bg-light py-3'  id='section_8'>
<?
if(isset($_GET['saved'])) {
	if($_GET['section']==8) {
		print "$msg"; $collapse="";
	} else
		$collapse="collapse";
}
?>
	<h3 class='text-center' >Настройка вебинаров <a href='#c8' data-toggle='collapse' ><span class="fa fa-folder-open"></span></a></h3>
	<div class='<?=$collapse?>'  id='c8'>
<!--
	<p>Инструкция по настройке <a href='' class='' target='_blank'>на youtube</a></p>
-->
	<div class='form-group top10'>

	<label for='bizon_api_token'>BIZON365 API token</label>
	<input type='text' id='bizon_api_token' name='bizon_api_token' value='<?=$bizon_api_token?>' class='form-control' >
	<p class='small' >
	Для получения API ключа перейдите <a href='https://start.bizon365.ru/admin/users' class='' target='_blank'>по ссылке</a>. <br>
	1. Выберите пункт «Изменить данные и настроить доступ». <br>
	2. В открывшемся окне в строке «API-токен» нажмите на кнопку «Генерировать». <br>
	3. Скопируйте сгенерированный API-токен. <br>
	4. Нажмите кнопку «Сохранить изменения». <br>
	5. Вернитесь в окно настройки интеграции BIZON365 и вставьте API ключ в соответствующую строку. <br>
	6. Нажмите кнопку «Сохранить».
	</p>
	<br>
	<br>
	<label for='bizon_web_hook'>BIZON365 WEBHOOK</label>
	<input type='text' id='bizon_web_hook' name='bizon_web_hook' value='<?="$DB200/bizon.php"?>' class='form-control' >
	<p class='small' >
		1. Зайдите в Бизон - Вебинарные комнаты <br>
		2. Нажмите кнопку &quot;Настрока комнаты&quot; <br>
		3. Вкладки - &quot;больше настроек&quot; - &quot;Разное&quot; <br>
		4. Вкладка - &quot;После вебинара&quot; <br>
		5. <b>Вебхук после создания отчета</b> - скопировать BIZON365 WEBHOOK <br>
		6. Нажать &quot;Сохранить все настройки&quot; <br>
	</p>
	<p class='text-danger' >* Внимание: при создании новой вебинарной комнаты в сервисе BIZON365
	в поле <b>&quot;Укажите идентификатор новой комнаты:&quot;</b>
	указывается число - номер лэндинга, с которого ведется запись на вебинар!
	</p>
	<br>
	<br>
	<label for='bizon_web_duration'>Длительность вебинара в минутах И процент для зачета (*)</label> <br>
	<input type='text' id='bizon_web_duration' name='bizon_web_duration' value='<?=$bizon_web_duration?>' class='form-control' data-input style='display:inline;width:140px;'> мин &nbsp; И 
	<input type='text' id='bizon_web_zachet_proc' name='bizon_web_zachet_proc' value='<?=$bizon_web_zachet_proc?>' class='form-control' data-input style='display:inline;width:100px;'>% <br>
	<small>* процент от общей длительности вебинара, при котором считается, что вебинар просмотрен полностью</small>
	<br>
	<br>
	<div><button type="submit" class="btn btn-primary btn-lg btn-block" name='do_save_8' value='yes'>Записать</button></div>
	<input type='hidden' name='csrf_token' value='<?=$_SESSION['csrf_token']?>'>
	<input type='hidden' name='csrf_name' value='s_panel'>
	</div>
	</div>
</div>
</form>

<form method='POST' enctype='multipart/form-data'> <!--9-->
<div class='card bg-light py-3'  id='section_9'>
<?
if(isset($_GET['saved'])) {
	if($_GET['section']==9) {
		print "$msg"; $collapse="";
	} else
		$collapse="collapse";
}
?>
	<h3 class='text-center' >Настройка платежных систем <a href='#c9' data-toggle='collapse' ><span class="fa fa-folder-open"></span></a></h3>
	<div class='<?=$collapse?>'  id='c9'>
<!--
	<p>Инструкция по настройке <a href='' class='' target='_blank'>на youtube</a></p>
-->
	<div class='form-group top10 card p-3 mt-5'>

	<h4 class='text-center text-info'  id='section_9_prodamus'>Продамус <a href='#prodamus' data-toggle='collapse' > <img src="https://for16.ru/images/prodamus-40.png" alt=""> <span class="fa fa-folder-open"></span></a></h4>
	<div id='prodamus' class='collapse' >
		<label for='prodamus_secret'>Секретный ключ</label>
		<input type='text' id='prodamus_secret' name='prodamus_secret' value='<?=$prodamus_secret?>' class='form-control' >
		<p class='small' >Зайдите на вашу страницу Продамуса, режим редактирования,
		секретный ключ находится в настройках.
		Его нужно скопировать и вставить в это поле.
		</p>
		<br>
		<br>
		
		<label for='prodamus_linktoform'>Адрес платежной страницы</label>
		<input type='text' id='prodamus_linktoform' name='prodamus_linktoform' value='<?=$prodamus_linktoform?>' class='form-control' >
		<p class='small' >Выдается Продамусом. В поле вставляется ссылка вида &quot;https://ваш_проект.payform.ru/&quot;.
		</p>
		<p class='card_ p-1 small' >В Продамус в &quot;Настройки-Настройка уведомлений-URL адреса для уведомлений:&quot; указать: https://for16.ru/d/<?=$ctrl_dir?>/pay_prodamus_callback.php
		</p>
		<p class='small' >При зачислении оплаты можно автоматически отправлять емэйл.
			Шаблон письма, которое будет отправляться при зачислении оплаты, берется в <a href='products.php' class='' target='_blank'>справочнике товаров</a>,
			для товара, по которому произведена оплата.
		</p>
		<br>
		<br>
	<div><button type="submit" class="btn btn-primary btn-lg btn-block" name='do_save_9_prodamus' value='yes'>Записать</button></div>
	<input type='hidden' name='csrf_token' value='<?=$_SESSION['csrf_token']?>'>
	<input type='hidden' name='csrf_name' value='s_panel'>
	</div>
	<h4 class='text-center text-info'   id='section_9_alfa'>Paykeeper <img src="https://for16.ru/images/paykeeper.png" alt=""> <a href='#alfabank' data-toggle='collapse' ><span class="fa fa-folder-open"></span></a></h4>
	<div id='alfabank' class='collapse' >
		<p class='text-center mb-2' ><a href='https://docs.paykeeper.ru/cms/winwinland-crm/' class='' target='_blank'>Инструкция по подключению</a></p>
		<label for='alfabank_secret'>Секретный ключ</label>
		<input type='text' id='alfabank_secret' name='alfabank_secret' value='<?=$alfabank_secret?>' class='form-control' >
		<p class='small' >Находится в &quot;Личный кабинет - Настройки - Получение информации о платежах&quot;. Способ получения уведомления о платежах - выбрать &quot;POST-оповещения&quot;.
		</p>
		<br>
		<br>
		<label for='alfabank_passw'>Пароль к личному кабинету</label>
		<input type='text' id='alfabank_passw' name='alfabank_passw' value='<?=$alfabank_passw?>' class='form-control' >
		<br>
		<br>
		
		<label for='alfabank_url'>Адрес платежной страницы</label>
		<input type='text' id='alfabank_url' name='alfabank_url' value='<?=$alfabank_url?>' class='form-control' >
		<p class='small' >Ссылка на личный кабинет paykeeper вида &quot;https://ваш_домен.server.paykeeper.ru&quot;.
		</p>
		<p class='card p-2 my-2 small_' >В  &quot;Личный кабинет paykeeper - Настройки - Получение информации о платежах&quot; указать: <b>https://for16.ru/d/<?=$ctrl_dir?>/pay_alfa_callback.php</b>
		</p>
		<p class='small' >При зачислении оплаты можно автоматически отправлять емэйл.
			Шаблон письма, которое будет отправляться при зачислении оплаты, берется в <a href='products.php' class='' target='_blank'>справочнике товаров</a>,
			для товара, по которому произведена оплата.
		</p>
		<br>
		<br>
		<div><button type="submit" class="btn btn-primary btn-lg btn-block" name='do_save_9_alfabank' value='yes'>Записать</button></div>
		<input type='hidden' name='csrf_token' value='<?=$_SESSION['csrf_token']?>'>
		<input type='hidden' name='csrf_name' value='s_panel'>
	</div>

	<h4 class='text-center text-info'   id='section_9_yookassa'>Юкасса <img src="https://for16.ru/images/yookassa.png" alt=""> <a href='#yookassa' data-toggle='collapse' ><span class="fa fa-folder-open"></span></a></h4>
	<div id='yookassa' class='collapse' >
		<label for='yookassa_secret'>Секретный ключ</label>
		<input type='text' id='yookassa_secret' name='yookassa_secret' value='<?=$yookassa_secret?>' class='form-control' >
		<p class='small' >Находится в &quot;Личный кабинет - Интеграция - Ключи API - Секретный ключ&quot;.
		</p>
		<br>
		<br>
		<label for='yookassa_passw'>ShopID</label>
		<input type='text' id='yookassa_passw' name='yookassa_passw' value='<?=$yookassa_passw?>' class='form-control' >
		<br>
		<br>
		<p class='card p-2 my-2 small_' >
			В  &quot;Личный кабинет Интеграция — HTTP-уведомления &quot;
			<a href='https://yookassa.ru/my/http-notifications-settings' class='' target='_blank'>https://yookassa.ru/my/http-notifications-settings</a> 
			указать: <b>https://for16.ru/d/<?=$ctrl_dir?>/pay_yookassa_callback.php</b>
			<span class='small' >(в кабинете нажать кнопку &quot;Задать вручную&quot; и установить галочку &quot;О каких событиях уведомлять: payment.successed &quot;)</span>
		</p>
<!--
		<br>
		<br>
		
		<label for='yookassa_url'>Адрес платежной страницы</label>
		<input type='text' id='yookassa_url' name='yookassa_url' value='<?=$yookassa_url?>' class='form-control' >
		<p class='small' >Ссылка на личный кабинет paykeeper вида &quot;https://ваш_домен.server.paykeeper.ru&quot;.
		</p>
		<p class='card p-2 my-2 small_' >
			В  &quot;Личный кабинет paykeeper - Настройки - Получение информации о платежах&quot; указать: <b>https://for16.ru/d/<?=$ctrl_dir?>/pay_yookassa_callback.php</b>
		</p>
-->
		<p class='small' >При зачислении оплаты можно автоматически отправлять емэйл.
			Шаблон письма, которое будет отправляться при зачислении оплаты, берется в <a href='products.php' class='' target='_blank'>справочнике товаров</a>,
			для товара, по которому произведена оплата.
		</p>
		<br>
		<br>
		<div><button type="submit" class="btn btn-primary btn-lg btn-block" name='do_save_9_yookassa' value='yes'>Записать</button></div>
		<input type='hidden' name='csrf_token' value='<?=$_SESSION['csrf_token']?>'>
		<input type='hidden' name='csrf_name' value='s_panel'>
	</div>

	<h4 class='text-center text-info'   id='section_9_robokassa'>Робокасса <img src="https://for16.ru/images/robokassa.png" alt=""> <a href='#robokassa' data-toggle='collapse' ><span class="fa fa-folder-open"></span></a></h4>
		<div id='robokassa' class='collapse' >
		<p class='card_ p-2 my-4 font-weight-bold' >На стороне платёжной системы в разделе Меню → Управление → Мои магазины получить данные:
		</p>
		<label for='robokassa_id'>Идентификатор магазина</label>
		<input type='text' id='robokassa_id' name='robokassa_id' value='<?=$robokassa_id?>' class='form-control' >
		<br>
		<label for='robokassa_passw_1'>Пароль 1</label>
		<input type='text' id='robokassa_passw_1' name='robokassa_passw_1' value='<?=$robokassa_passw_1?>' class='form-control' >
		<br>
		<label for='robokassa_passw_2'>Пароль 2</label>
		<input type='text' id='robokassa_passw_2' name='robokassa_passw_2' value='<?=$robokassa_passw_2?>' class='form-control' >
		</p>
		<br>
		<p class='card p-2 my-2 small_' >
			В  &quot;Меню → Управление → Мои магазины прописать &quot; <br>
			Result URL: <b>https://for16.ru/d/<?=$ctrl_dir?>/pay_robokassa_callback.php</b> <br>
			Success URL:<b>https://for16.ru/d/<?=$ctrl_dir?>/pay_success.php</b> <br>
			Fail URL: <b>https://for16.ru/d/<?=$ctrl_dir?>/pay_fail.php</b> <br>
			(либо пропишите свои страницы успешной и неуспешной оплаты)
		</p>
		<p class='small' >При зачислении оплаты можно автоматически отправлять емэйл.
			Шаблон письма, которое будет отправляться при зачислении оплаты, берется в <a href='products.php' class='' target='_blank'>справочнике продуктов</a>,
			для товара, по которому произведена оплата.
		</p>
		<p class='small text-warning' >Важно! Названия продуктов при оплате через Робокассу не могут содержать кавычки. Убедитесь, что в <a href='products.php' class='' target='_blank'>справочнике продуктов</a> кавычки отсутствуют!
		</p>
		<br>
		<br>
		<div><button type="submit" class="btn btn-primary btn-lg btn-block" name='do_save_9_robokassa' value='yes'>Записать</button></div>
		<input type='hidden' name='csrf_token' value='<?=$_SESSION['csrf_token']?>'>
		<input type='hidden' name='csrf_name' value='s_panel'>
	</div>


	</div>


	
	</div>
</div>
</form>

<form method='POST' enctype='multipart/form-data'> <!--10-->
<div class='card bg-light py-3'  id='section_10'>
<?
if(isset($_GET['saved'])) {
	if($_GET['section']==10) {
		print "$msg"; $collapse="";
	} else
		$collapse="collapse";
}
?>
	<h3 class='text-center' >Настройка email рассылок <a href='#c10' data-toggle='collapse' ><span class="fa fa-folder-open"></span></a></h3>
	<div class='<?=$collapse?>'  id='c10'>
<!--
	<p>Инструкция по настройке <a href='' class='' target='_blank'>на youtube</a></p>
-->
	<div class='form-group top10 card p-3 mt-5'>

	<h2 class='text-center text-info' >UNISENDER GO</h2>
	
	<label for='unisender_secret'>Секретный ключ</label>
	<input type='text' id='unisender_secret' name='unisender_secret' value='<?=$unisender_secret?>' class='form-control' >
	<p class='small' >Должна быть настроена интеграция с сервисом email рассылок
		<a href='https://go1.unisender.ru/' class='' target=''>UNISENDER GO</a>
	</p>
	<p class='small' >Секретный ключ можно получить на
	<a href='https://go1.unisender.ru/ru/settings/security/api' class='' target='_blank'>странице настроек</a> unisender go. <br>
	До начала работы у вас должен быть <a href='https://go1.unisender.ru/ru/domain' class='' target=''>подтвержден домен</a> и настроены необходимые записи в DNS зоне. <br>
	Учтите также, что письма вы можете отправлять только от имени своего домена.
	</p>
	<br>
	<br>

	<label for='email_from'>Поле ОТ EMAIL (обратный емэйл по умолчанию) </label>
	<input type='text' id='email_from' name='email_from' value='<?=$email_from?>' class='form-control' >
	<label for='email_from_name'>Поле ОТ ИМЯ (от кого емэйл, по умолчанию)</label>
	<input type='text' id='email_from_name' name='email_from_name' value='<?=$email_from_name?>' class='form-control' >
	<p class='small' >* должна быть настроена интеграция с сервисом email рассылок <a href='#10' class='' target=''>UNISENDER GO</a></p></p>
	<p class='small' >Протестировать емэйл шаблоны unisender Go <a href='test_email.php' class='' target='_blank'>можно здесь</a></p></p>
	<br>
	<br>
	
	<div><button type="submit" class="btn btn-primary btn-lg btn-block" name='do_save_10_unisender' value='yes'>Записать</button></div>
	<input type='hidden' name='csrf_token' value='<?=$_SESSION['csrf_token']?>'>
	<input type='hidden' name='csrf_name' value='s_panel'>
	</div>


	
	</div>
</div>
</form>

<form method='POST' enctype='multipart/form-data'> <!--11-->
<div class='card bg-light py-3'  id='section_11'>
<?
if(isset($_GET['saved'])) {
	if($_GET['section']==11) {
		print "$msg"; $collapse="";
	} else
		$collapse="collapse";
}
?>
	<h3 class='text-center' >Настройка интеграции с виджетами Envybox <a href='#c11' data-toggle='collapse' ><span class="fa fa-folder-open"></span></a></h3>
	<div class='<?=$collapse?>'  id='c11'>
<!--
	<p>Инструкция по настройке <a href='' class='' target='_blank'>на youtube</a></p>
-->
	<div class='form-group top10 card p-3 mt-5'>
		<p>В сервисе envybox для каждого виджета <b>Настройки - вкладка Интеграции - WebHooks уведомления</b>
		необходимо прописать следующий URL: <b>https://for16.ru/d/<?=$ctrl_dir?>/envybox_webhook.php</b>
		и проставить все галочки.
		</p>
		<p>После этого все данные, оставленные посетителями в виджетах Envybox, будут занесены в CRM Winwinland, как регистрации с подписного лэндинга,
		с учетом партнерских кодов и utm меток.
		</p>
	</div>


	
	</div>
</div>
</form>

<form method='POST' enctype='multipart/form-data'> <!--12 WHATSAPP-->
<div class='card bg-light py-3'  id='section_12'>
<?
if(isset($_GET['saved'])) {
	if($_GET['section']==12) {
		print "$msg"; $collapse="";
	} else
		$collapse="collapse";
}
?>
	<h3 class='text-center' >Настройка интеграции с WHATSAPP <a href='#c12' data-toggle='collapse' ><span class="fa fa-folder-open"></span></a></h3>
	<div class='<?=$collapse?>'  id='c12'>
	<p>Интеграция с WHATSAPP осуществляется с помощью сервиса PACT, где необходимо зарегистрироваться
	<a href='https://app.pact.im/signup?ref=77810e179b951b83bd7a861f3fe67659' class='' target='_blank'>по ссылке</a>
	</p>
	<p>Сервис оплачивается отдельно на сайте сервиса и по расценкам сервиса.
	</p>
	<label for='pact_secret'>Секретный ключ</label>
	<input type='text' id='pact_secret' name='pact_secret' value='<?=$pact_secret?>' class='form-control' >
	<p class='small' >Секретный ключ можно получить в профиле на
	<a href='https://msg.pact.im/account' class='' target='_blank'>странице настроек</a>. <br>
	</p>
	<label for='pact_company_name'>Название компании</label>
	<input type='text' id='pact_company_name' name='pact_company_name' value='<?=$pact_company_name?>' class='form-control' >
	<p class='small' >Название компании, заданное в профиле сервиса Пакт. Если у вас только одна  компания, можно пропустить. </p>
	<p>Company ID - <b><?=$pact_company_id?></b></p>
	<br>

	<div class='form-group top10 card p-3 mt-5'>
		<p>В сервисе PACT : <b>Настройки - вкладка «О компании» - Webhook URL</b>
		необходимо прописать следующий URL: <b>https://for16.ru/d/<?=$ctrl_dir?>/pact_callback.php</b>
		</p>
	</div>
	<div><button type="submit" class="btn btn-primary btn-lg" name='do_save_12_pact' value='yes'>Записать</button></div>
	<input type='hidden' name='csrf_token' value='<?=$_SESSION['csrf_token']?>'>
	<input type='hidden' name='csrf_name' value='s_panel'>

	</div>
</div>
</form>


<form method='POST' enctype='multipart/form-data'> <!--13 VSEGPT-->
<div class='card bg-light py-3'  id='section_13'>
<?
if(isset($_GET['saved'])) {
	if($_GET['section']==13) {
		print "$msg"; $collapse="";
	} else
		$collapse="collapse";
}
?>
	<h3 class='text-center' >Настройка интеграции с API VSEGPT <a href='#c13' data-toggle='collapse' ><span class="fa fa-folder-open"></span></a></h3>
	<div class='<?=$collapse?>'  id='c13'>
	<label for='vsegpt_secret'>API TOKEN</label>
	<input type='text' id='vsegpt_secret' name='vsegpt_secret' value='<?=$vsegpt_secret?>' class='form-control' >
	<br>
	<label for='vsegpt_model'>API MODEL</label>
	<input type='text' id='vsegpt_model' name='vsegpt_model' value='<?=$vsegpt_model?>' class='form-control' >
	<br>
	<label for='vsegpt_delay_sec'>Задержка перед ответом, сек</label>
	<input type='text' id='vsegpt_delay_sec' name='vsegpt_delay_sec' value='<?=$vsegpt_delay_sec?>' class='form-control' >
	<br>

	<div><button type="submit" class="btn btn-primary btn-lg" name='do_save_13_vsegpt' value='yes'>Записать</button></div>
	<input type='hidden' name='csrf_token' value='<?=$_SESSION['csrf_token']?>'>
	<input type='hidden' name='csrf_name' value='s_panel'>

	</div>
</div>
</form>

<form method='POST' enctype='multipart/form-data'> <!--14 GETCOURSE-->
<div class='card bg-light py-3'  id='section_14'>
<?
if(isset($_GET['saved'])) {
	if($_GET['section']==14) {
		print "$msg"; $collapse="";
	} else
		$collapse="collapse";
}
?>
	<h3 class='text-center' >Настройка интеграции с GETCOURSE <a href='#c14' data-toggle='collapse' ><span class="fa fa-folder-open"></span></a></h3>
	<div class='<?=$collapse?> p-5'  id='c14'>
	<p>Интеграция предназначена для передачи в Винвинлэнд данных об оплатах, получаемых в Геткурс.
	Это позволяет хранить основную базу лидов в Винвинлэнд и не платить Геткурс за подписчиков.
	</p>
	<p>В Геткурс необходимо прописать процесс <a href='https://youtu.be/7qPN61whgDs?si=co4bA_OL98gM4aIt' class='' target='_blank'>по видео инструкции</a>
	указав там следующий адрес для callback оповещений:
	</p>
	<p class='card p-2 m-2 bg-light font-weight-bold' >
	<?
	print $DB200."/pay_getcourse.php?first_name={object.user.first_name}&last_name={object.user.last_name}&phone={object.user.phone}&email={object.user.email}&order_number={object.number}&offers={object.offers}&positions={object.positions}&payed_money={object.payed_money}&status={object.status}&payed_at={payed_at}";
	?>
	</p>
	</div>
</div>
</form>

<form method='POST' enctype='multipart/form-data'> <!--15 WEBHOOKS-->
<div class='card bg-light py-3'  id='section_15'>
<?
if(isset($_GET['saved'])) {
	if($_GET['section']==15) {
		print "$msg"; $collapse="";
	} else
		$collapse="collapse";
}
?>
	<h3 class='text-center' >Настройка интеграций по API и вебхук <a href='#c15' data-toggle='collapse' ><span class="fa fa-folder-open"></span></a></h3>
	<div class='<?=$collapse?> p-5'  id='c15'>
		<div class='card p-3 my-3 ' >
			<p class='font-weight-bold' >Формат запроса для добавления лида в WinWinLand:</p>
			<p>POST запрос на адрес: <b><span id='_api_reg'><?=$DB200."/dapi.php"?></span></b>
			<a href="javascript:copySpanContent('_api_reg');" class='text-secondary'><i class='fa fa-copy' ></i></a>
			</p>
			<p>После вызова pay_webhook.php возвращает строковое значение: 'ok '+uid записи, с которой поводилась операция,
			либо 'err' + описание ошибки
			</p>
			<p><a href='https://help.winwinland.ru/docs/otpravka-zaprosa-na-dobavlenie-lida-partnera/' class='' target='_blank'>описание</a></p>
		</div>
		<div class='card p-3 my-3 ' >
			<p class='font-weight-bold' >Формат запроса для добавления события оплаты в WinWinLand:</p>
			<p>POST запрос на адрес: <b><span id='_api_pay'><?=$DB200."/pay_webhook.php"?></span></b>
			<a href="javascript:copySpanContent('_api_pay');" class='text-secondary'><i class='fa fa-copy' ></i></a>
			</p>
			<p>После вызова pay_webhook.php возвращает строковое значение: 'ok' либо 'err' + описание ошибки
			</p>
			<p><a href='https://help.winwinland.ru/docs/otpravka-zaprosa-na-dobavlenie-oplaty/' class='' target='_blank'>описание</a></p>
		</div>
		<div class='card p-3 my-3 ' >
			<p class='font-weight-bold' >Формат вебхука, отправляемого WinWinLand на ваш url при различных триггерных событиях :</p>
			<p>
				<a href='https://help.winwinland.ru/docs/format-vebhuka/' class='' target='_blank'>
				Формат вебхука
				</a>
			</p>
			<p>
				<a href='https://help.winwinland.ru/docs/sozdanie-vebhuka/' class='' target='_blank'>
				Как запланировать Webhook (привязка к событиями в системе)
				</a>
			</p>
			<p>
				<a href='https://help.winwinland.ru/docs/kak-protestirovat-webhook/' class='' target='_blank'>
				Как протестировать вебхук
				</a>
			</p>
		</div>
		<p class='' >Секретное слово (secret) используемой в запросах и для проверки вебхука: 
			<b><span id='_api_secret' ><?=$api_secret?></span></b>
			<a href="javascript:copySpanContent('_api_secret');" class='text-secondary'><i class='fa fa-copy' ></i></a><br>
			<button id="generateSecret" class="btn btn-primary mt-3">Сгенерировать новый</button>
		</p>
	</div>
</div>
</form>

<form method='POST' enctype='multipart/form-data'> <!--16 INSALES-->
<div class='card bg-light py-3'  id='section_16'>
<?
if(isset($_GET['saved'])) {
	if($_GET['section']==16) {
		print "$msg"; $collapse="";
	} else
		$collapse="collapse";
}
?>
	<h3 class='text-center' >Настройка интеграции с INSALES <a href='#c16' data-toggle='collapse' ><span class="fa fa-folder-open"></span></a></h3>
	<div class='<?=$collapse?> p-5'  id='c16'>
	<p>Интеграция позволяет подключить партнерскую программу к интернет магазину на Insales.
	Партнерская ссылка может вести на основной сайт на Insales, при покупке на Insales партнеру
	начислится вознаграждение в WinWinLand.
	</p>
	<p>Вы можете создать продукты в WinWinLand с SKU такими же, как в Insales, и настроить по ним
	индивидуальные вознаграждения. Если при продаже оответствующий SKU в WinWinLand не найден, то
	будут применены вознаграждения по продукту с ID=1. Этот <a href='products.php' class='' target='_blank'>продукт</a> должен быть создан в WinWinLand
	обязательно..
	</p>
	<label for='insales_shop_id'><a href='/images/insales.png' class='' target='_blank'>Номер аккаунта</a> в Insales: </label>
	<input type='text' id='insales_shop_id' name='insales_shop_id' value='<?=$insales_shop_id?>' class='form-control' >
	<br>
	<label for='insales_status'>Статус заказа, при котором начислять партнерское вознаграждение: </label>
	<input type='text' id='insales_status' name='insales_status' value='<?=$insales_status?>' class='form-control' >
	<p class='small' >Статус заказа в inSales, при котором сделка считается завершенной и можно начислять партнерские. Например 'Доставлен' или 'Заказ выполнен'.
	Указывается в точности, как в админ панели inSales, с учетом регистра.
	</p>
	<br>
	<p>Insales token: <?=$insales_token?></p>
	<p>Insales shop: <?=$insales_shop?></p>

	<div><button type="submit" class="btn btn-primary btn-lg" name='do_save_16_insales' value='yes'>Записать</button></div>
	<input type='hidden' name='csrf_token' value='<?=$_SESSION['csrf_token']?>'>
	<input type='hidden' name='csrf_name' value='s_panel'>
	</div>
</div>
</form>



</div>

  <script> //tinymce
    tinymce.init({
      selector: 'textarea.tinymce',
		menubar: false,
		language: 'ru',
      plugins: 'colorpicker anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
      toolbar: 'undo redo |heading1 heading2 heading3 blocks fontfamily fontsize | bold italic underline strikethrough forecolor backcolor | align lineheight | checklist numlist bullist indent outdent | link image media | emoticons charmap | removeformat',
	  formats: {
		heading1: { block: 'h1', classes: 'text-center' },
		heading2: { block: 'h2', classes: 'text-center' },
		heading3: { block: 'h3', classes: 'text-center' },
	  },
      tinycomments_mode: 'embedded',
      tinycomments_author: 'Author name',
      mergetags_list: [
        { value: 'First.Name', title: 'First Name' },
        { value: 'Email', title: 'Email' },
      ]
    });
  </script>

<script>
$(document).ready(function(){
    $('[data-toggle="popover"]').popover(); // Инициализация popover

	$('#generateSecret').click(function() {
		event.preventDefault();
		var confirmation = confirm("Are you sure you want to generate a new API secret? Be careful change secret if you use api already!");
		if (confirmation) {
				$.ajax({
					url: 'jquery.php',
					type: 'POST',
					data: { ch_api_secret: 'yes', ctrl_id: <?=$ctrl_id?> },
					dataType: 'json',
					success: function(response) {
						console.log("response is "+response);
						$('#_api_secret').text(response.secret);
					}
				});
		}
	});
});
</script>
<script>
	function copySpanContent(span_id) {
		var spanElement = document.getElementById(span_id);
		var tempInput = document.createElement("input");
		tempInput.value = spanElement.textContent;
		document.body.appendChild(tempInput);
		tempInput.select();
		document.execCommand("copy");
		document.body.removeChild(tempInput);
		alert("Ссылка скопирована!");
	}
</script>

<?
$top->bottom();
?>
