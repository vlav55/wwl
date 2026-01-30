<?
include_once "/var/www/vlav/data/www/wwl/inc/top.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/unisender.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
include "init.inc.php";

$t=new top($database,'Рассылка',false);
//~ if($database!='vkt') {
	//~ print "Ведутся технические работы, сервис рассылок будет доступен в скором времени в обновленном виде.
	//~ Приносим извинения за неудобства!";
	//~ $t->bottom();
	//~ exit;
//~ }

$db=new vkt_send($database);

$db->vkt_send_skip_wa=true;
//print "HERE_$database";
if($database!='vkt')
	$db->vkt_send_tg_bot=$tg_bot_msg; else $db->vkt_send_tg_bot='1451314745:AAHLX4MAf3M008jcAtWiQPCgJDi1-IZr28k'; //'vkt_manager_bot';

$vkt_send_id=0;
if(isset($_GET['vkt_send_id'])) {
	$vkt_send_id=intval($_GET['vkt_send_id']);
}
if(isset($_POST['vkt_send_id'])) {
	$vkt_send_id=intval($_POST['vkt_send_id']);
}
if(!$vkt_send_id) {
	print "<p class='alert alert-danger' >Ошибка vkt_send_id . Сообщите в техподдержку</p>";
	$t->bottom();
	exit;
}

$mode=$db->vkt_send_mode($vkt_send_id);
if(isset($_GET['dublicate'])) {
	$r=$db->fetch_assoc($db->query("SELECT * FROM vkt_send_1 WHERE id='$vkt_send_id'"));
	$db->query("INSERT INTO vkt_send_1 SET
			tm='".time()."',
			vkt_send_tm=0,
			tm_shift=0,
			land_num='".$r['land_num']."',
			sid='".$r['sid']."',
			name_send='".$db->escape($r['name_send'])." - копия',
			msg='".$db->escape($r['msg'])."',
			email_template='".$db->escape($r['email_template'])."',
			email_from='".$db->escape($r['email_from'])."',
			email_from_name='".$db->escape($r['email_from_name'])."',
			vk_attach='".$db->escape($r['vk_attach'])."',
			tg_image='".$db->escape($r['tg_image'])."',
			tg_video='".$db->escape($r['tg_video'])."',
			tg_video_note='".$db->escape($r['tg_video_note'])."',
			tg_audio='".$db->escape($r['tg_audio'])."',
			tm1='".$r['tm1']."',
			tm2='".$r['tm2']."',
			fl_clients='".$r['fl_clients']."',
			fl_partners='".$r['fl_partners']."',
			fl_leads='".$r['fl_leads']."',
			fl_razdel='".$r['fl_razdel']."',
			fl_vk='".$r['fl_vk']."',
			fl_tg='".$r['fl_tg']."',
			fl_email='".$r['fl_email']."',
			fl_tag='".$r['fl_tag']."',
			fl_chk='".$r['fl_chk']."',
			fl_land='".$r['fl_land']."'
			");
	$vkt_send_id=$db->insert_id();
	print "<script>opener.location.reload();</script>";
	print "<p class='alert alert-success' >Добавлено и скопировано</p>";
}

if(isset($_POST['btn_save']) || isset($_POST['btn_test']) || isset($_POST['btn_send'])) {
	if(preg_match("#(photo|video)\-[0-9]+_[0-9]+#i",$_POST['vk_attach'],$m))
		$vk_attach=$m[0]; else $vk_attach="";
	$err=false;
	$name_send=(mb_substr($_POST['name_send'],0,128));
	if(empty($name_send)) {
		print "<p  class='alert alert-danger' >Не указано название рассылки</p>";
		$err=true;
	}
	$tm1=$db->date2tm($_POST['dt1']);
	$tm2=$db->dt2($db->date2tm($_POST['dt2']));
	$tg_image=mb_substr($_POST['tg_image'],0,255);
	$tg_video=mb_substr($_POST['tg_video'],0,255);
	$tg_video_note=mb_substr($_POST['tg_video_note'],0,255);
	$tg_audio=mb_substr($_POST['tg_audio'],0,255);
	$vkt_send_tm=$db->date2tm($_POST['dt_delay'])+$db->time2tm($_POST['tm_delay']);
	if($db->date2tm($_POST['dt_delay'])===false || $db->time2tm($_POST['tm_delay'])==false) {
		if($mode==1) {
			print "<p class='alert alert-danger'>Дата или время отправки указаны с ошибкой</p>";
			$vkt_send_tm=time()+(60*60);
			$err=true;
		}
			
	} else {
		if($vkt_send_tm<time())
			$vkt_send_tm=time()+(5*60);
	}
	$msg=(mb_substr($_POST['msg'],0,32000));
	if(empty($msg)) {
		//~ print "<p  class='alert alert-danger' >Сообщение для рассылки пустое</p>";
		//~ $err=true;
	}
	$email_template=(mb_substr($_POST['email_template'],0,128));
	$email_from=(mb_substr($_POST['email_from'],0,128));
	if(!$db->validate_email($email_from) && !empty($email_from)) {
		//print "<p class='alert alert-warning' >Обратный емэйл в неверном формате</p>";
		$email_from="";
	}
	$email_from_name=(mb_substr($_POST['email_from_name'],0,24));
	$fl_clients=$_POST['fl_clients']?1:0;
	$fl_partners=$_POST['fl_partners']?1:0;
	$fl_leads=$_POST['fl_leads']?1:0;

	$fl_tg=$_POST['fl_tg']?1:0;
	$fl_vk=$_POST['fl_vk']?1:0;
	$fl_email=$_POST['fl_email']?1:0;
	
	$fl_razdel=intval($_POST['fl_razdel']);
	$fl_land=intval($_POST['fl_land']);
	$fl_tag=intval($_POST['fl_tag']);
	$fl_chk=$_POST['fl_chk']?1:0;

	$tm_shift=(intval($_POST['days_shift'])*24*60*60)+(intval($_POST['hours_shift'])*60*60)+(intval($_POST['min_shift'])*60);
	if($_POST['shift_direction']=='shift_backward') {
		if($tm_shift>0)
			$tm_shift=-$tm_shift;
	} else {
		if($tm_shift<0)
			$tm_shift=-$tm_shift;
	}

	$land_num=($mode!=1)?intval($_POST['land_num']):0;
	$land_name=($land_num) ? $db->dlookup("land_name","lands","land_num='$land-num' AND del=0"):"";
	$product_id=($land_num) ? $db->dlookup("product_id","lands","del=0 AND land_num='$land_num'"):0;
	$product_descr=($product_id)?$db->dlookup("descr","product","del=0 AND id='$product_id'"):0;

	$sid=($mode==3)?intval($_POST['sid']):0;
	if($mode==2 || $mode==3) {
		$fl_tg=1;
		$fl_vk=1;
		$fl_email=1;
	}

	$test_uid=0;
	if(!empty($_POST['test_uid'])) {
		if(!$test_uid=intval($_POST['test_uid'])) {
			if(preg_match("/\?uid=([\-0-9]+)/i",$_POST['test_uid'],$m)) {
				$test_uid=intval($m[1]);
				//print_r($m);
			}
		}
	}
	$db->connect('vkt');
	$db->query("UPDATE 0ctrl SET test_uid='$test_uid' WHERE id='$ctrl_id'");
	$db->connect($database);
	
	
	//print "HERE_$test_uid";
	if(!$err) {
		//$db->vkt_send_task_del($vkt_send_id,$ctrl_id);
		$db->query("UPDATE vkt_send_1 SET 
				name_send='".$db->escape($name_send)."',
				tm1='$tm1',
				tm2='$tm2',
				fl_clients='$fl_clients',
				fl_partners='$fl_partners',
				fl_leads='$fl_leads',
				fl_vk='$fl_vk',
				fl_tg='$fl_tg',
				fl_email='$fl_email',
				fl_razdel='$fl_razdel',
				fl_land='$fl_land',
				fl_chk='$fl_chk',
				fl_tag='$fl_tag',
				vkt_send_tm='$vkt_send_tm',
				tm_shift='$tm_shift',
				land_num='$land_num',
				sid='$sid',
				msg='".$db->escape($msg)."',
				email_template='".$db->escape($email_template)."',
				email_from='".$db->escape($email_from)."',
				email_from_name='".$db->escape($email_from_name)."',
				vk_attach='".$db->escape($vk_attach)."',
				tg_image='".$db->escape($tg_image)."',
				tg_video='".$db->escape($tg_video)."',
				tg_video_note='".$db->escape($tg_video_note)."',
				tg_audio='".$db->escape($tg_audio)."'
				WHERE id='$vkt_send_id'
				",0);
		print "<p class='alert alert-success' >Записано!</p>";
		print "<script>opener.location.reload();</script>";
		if($mode!=3) {
			if(!$db->save_vkt_send_tm($vkt_send_id,$ctrl_id) && $mode!=1)
				print "<p class='alert alert-danger' >Ошибка: рассылка не может быть запалнирована на уже прошедшее время!</p>";
		}
	}
}

if(!$r=$db->fetch_assoc($res=$db->query("SELECT * FROM vkt_send_1 WHERE id='$vkt_send_id'"))) {
	print "<p class='alert alert-danger' >Ошибка 3. Обратитесь к разработчикам</p>";
	$db->bottom();
	exit;
}

if(!$r['tm1']) {
	$r['tm1']=$db->dlookup("tm","cards","del=0");
	$db->query("UPDATE vkt_send_1 SET tm1='{$r['tm1']}' WHERE id='$vkt_send_id'");
}
if(!$r['tm2']) {
	$r['tm2']=$db->dlast("tm","cards","del=0");
	$db->query("UPDATE vkt_send_1 SET tm2='{$r['tm2']}' WHERE id='$vkt_send_id'");
}
if($mode==1) {
	//$db->vkt_send_filter($vkt_send_id);
}

if(isset($_POST['btn_test'])) {
	//print "HERE_$db->database $uid_admin"; exit;
	$db->connect('vkt');
	if($test_uid=$db->dlookup("test_uid","0ctrl","id='$ctrl_id'")) {
		//$test_uid=$uid_admin;
		$db->connect($database);
		$tg_test=$db->dlookup("telegram_id","cards","uid='$test_uid'");
		$tg_test_nic=$db->dlookup("telegram_nic","cards","uid='$test_uid'");
		$tg_test=($tg_test)?$tg_test:"нет";
		$tg_test_nic=($tg_test)?$tg_test_nic:"нет";
		$vk_test=$db->dlookup("vk_id","cards","uid='$test_uid'");
		$vk_test_url=($vk_test)?"<a href='https://vk.com/id$vk_test' class='' target='_blank'>https://vk.com/id$vk_test</a>":"нет";
		$name=$db->dlookup("name","cards","uid='$test_uid'");
		$mob=$db->dlookup("mob_search","cards","uid='$test_uid'");
		$email=trim($db->dlookup("email","cards","uid='$test_uid'"));
		$klid=$db->dlookup("id","cards","uid='$test_uid'");
		$test_uid_md5=$db->dlookup("uid_md5","cards","uid='$test_uid'");
		$db->connect($database);
		$db->vkt_send_tg_bot=$tg_bot_msg;
		$db->db200=$DB200;
		print "<div class='container' >
			<div class='card bg-light' >
			<p>Отправляем тест на: <b>$name ($mob)</b></p>
			<p>Телеграм: <b>$tg_test_nic id=$tg_test</b></p>
			<p>ВК: <b>$vk_test_url</b></p>
			<p>Email: <b>$email</b></p>
			<br>";

		if(!empty($vk_attach)) {
			$db->vkt_send_vk_photo=$vk_attach;
		}
		if(!empty($tg_image)) {
			$db->vkt_send_tg_photo=$tg_image;
		}
		if(!empty($tg_video_note)) {
			$db->vkt_send_tg_video_note=$tg_video_note;
		}
		if(!empty($tg_video)) {
			$db->vkt_send_tg_video=$tg_video;
		}
		if(!empty($tg_audio)) {
			$db->vkt_send_tg_audio=$tg_audio;
		}

		$db->vkt_send_tg_id=$tg_test;	
		$db->vkt_send_vk_id=$vk_test;

		$db->fl_tg=1;
		$db->fl_vk=1;
		$db->pact_secret=$pact_secret;
		$db->pact_company_id=$pact_company_id;
		$db->ctrl_id=$ctrl_id;
		$db->db200=$DB200;
		if($db->vkt_send_msg($test_uid,$msg))
			print "<p class='alert alert-success' >Тест отправлен в мессенджеры</p>";
		elseif(!empty($msg))
			print "<p class='alert alert-danger' >Ошибка отправки в мессенджеры</p>";
		$res_email=0;
		if(!empty($email_template)) {
			$uni=new unisender($unisender_secret,$email_from,$email_from_name);
			//$r_uni=$db->fetch_assoc($db->query("SELECT * FROM cards WHERE uid='$test_uid'"));
			if($db->validate_email($email)) {
				$db->db200=$DB200;
				$vars=['client_name'=>$name,
					'name'=>$name,
					'email'=>$email,
					'phone'=>$mob,
					'uid'=>$test_uid_md5,
					'cabinet_link'=>$db->get_direct_code_link($klid),
					'partner_code'=>$db->get_bc($klid),
					'product_id'=>$product_id,
					'product'=>$product_descr,
					'land_num'=>$land_num,
					'land_name'=>$land_name,
					'promocode'=>$db->promocode_get_last($test_uid)
					];
				$db->log_email($email);
				if($uni->email_by_template($email,$email_template,$vars)) {
					print "<p class='alert alert-success' >Тест емэйл отправлен на $email <br>
					".print_r($uni->res,true)."
					</p>";
					$res_email=1;
				} else {
					$res_email=2;
					print "<p class='alert alert-warning' >Ошибка отправки емэйл: $email <br>
					".print_r($uni->res,true)."
					</p>";
					$db->print_r($vars);
				}
			}
		}
		print "</div>
		</div>";
		//$db->print_r($db->vkt_send_res);
		//~ $db->query("INSERT INTO vkt_send_log SET
			//~ uid='$test_uid',
			//~ tm='".time()."',
			//~ res_vk='{$db->vkt_send_res['vk']}',
			//~ res_tg='{$db->vkt_send_res['tg']}',
			//~ res_wa='{$db->vkt_send_res['wa']}',
			//~ res_email='$res_email'
			//~ ");
	} else {
		print "<p class='alert alert-warning' >Укажите - Кому отправлять <b>Тест себе</b></p>";
	}
}

$name_send=$r['name_send'];
$tm1=$r['tm1'];
$tm2=$r['tm2'];
$dt1=date("d.m.Y",$r['tm1']);
$dt2=date("d.m.Y",$r['tm2']);
$fl_clients_checked=$r['fl_clients']?'checked':'';
$fl_partners_checked=$r['fl_partners']?'checked':'';
$fl_leads_checked=$r['fl_leads']?'checked':'';
$fl_tg_checked=$r['fl_tg']?'checked':'';
$fl_vk_checked=$r['fl_vk']?'checked':'';
$fl_email_checked=$r['fl_email']?'checked':'';
$fl_razdel=intval($r['fl_razdel']);
$fl_land=intval($r['fl_land']);
$fl_tag=intval($r['fl_tag']);
$fl_chk=intval($r['fl_chk']);
$vkt_send_tm=$r['vkt_send_tm'];
if(!$vkt_send_tm) {
	if($mode==1)
		$vkt_send_tm=time()+(60*60);
	else
		$vkt_send_tm=$db->save_vkt_send_tm($vkt_send_id,$ctrl_id);
}
$dt_delay=date('d.m.Y',$vkt_send_tm);
$tm_delay=date('H:i',$vkt_send_tm);
$tm_shift=$r['tm_shift'];
$msg=$r['msg'];

$land_num=$r['land_num'];
$sid=$r['sid'];

$email_template=$r['email_template'];
$email_from=$r['email_from'];
$email_from_name=$r['email_from_name'];

$vk_attach=$r['vk_attach'];
$tg_image=$r['tg_image'];
$tg_video=$r['tg_video'];
$tg_video_note=$r['tg_video_note'];
$tg_audio=$r['tg_audio'];

$arr_res=$db->vkt_send_filter($vkt_send_id);
//print_r($arr_res);
$arr_vk=$db->vkt_send_filter_cnt_vk($arr_res);
$arr_tg=$db->vkt_send_filter_cnt_tg($arr_res);
$arr_email=$db->vkt_send_filter_cnt_email($arr_res);

$n_vk=(!empty($msg) && $r['fl_vk'])?sizeof($arr_vk):0;
$n_tg=(!empty($msg) && $r['fl_tg'])?sizeof($arr_tg):0;
$n_email=(!empty($email_template) && $r['fl_email'])?sizeof($arr_email):0;
$n_all=sizeof($arr_res);

if(isset($_POST['btn_del'])) {
	$db->query("UPDATE vkt_send_1 SET del=1 WHERE id='$vkt_send_id'");
	$db->vkt_send_task_del($vkt_send_id,$ctrl_id,0);
	unlink("vkt_send_task_$vkt_send_id.php");
	print "<script>opener.location.reload();</script>";
	print "<p class='alert alert-warning' >Рассылка удалена <a href='javascript:window.close()' class='btn btn-primary btn-sm' target=''>Закрыть окно</a></p>";
	$db->bottom();
	exit;
}

if(isset($_POST['btn_send'])) {
	if($mode==1 && $vkt_send_tm>time()) {
		if($db->vkt_send_task_add($ctrl_id, $vkt_send_tm, $vkt_send_id,$vkt_send_type=1)) {
			$dt=date('d.m.Y H:i',$vkt_send_tm);
			print "<p class='alert alert-warning' >Внимание! Запланирована  рассылка, согласно фильтрам:
				VK-<span class='badge' >$n_vk</span>
				TG-<span class='badge' >$n_tg</span>
				EMAIL-<span class='badge' >$n_email</span>
				<!--ВСЕГО-<span class='badge' >$n_all</span> <br>-->
				Время рассылки <b>$dt</b> <br>
				Вы можете еще отредактировать рассылку, удалить ее, перенести на другое время или закрыть это окно и ждать выполнение рассылки:
				<a href='javascript:window.close()' class='btn btn-primary btn-sm' target=''>Закрыть окно</a>
			 </p>";
		} else {
			print "<p class='alert alert-warning' >Ошибка. Запланировать рассылку не удалось!</p>";
			$db->vkt_send_task_del($vkt_send_id,$ctrl_id,$uid=0);
		}
	} elseif($mode==2 && $vkt_send_tm>time()) {
		if($db->vkt_send_task_add($ctrl_id, $vkt_send_tm, $vkt_send_id,$vkt_send_type=2)) {
			$dt=date('d.m.Y H:i',$vkt_send_tm);
			print "<p class='alert alert-warning' >Внимание! Запланирована  рассылка по мероприятию на лэндинге <b>$land_num</b>
				Время рассылки <b>$dt</b>. Все зарегистрировавшиеся на это мероприятие получат рассылку.<br>
				Вы можете еще отредактировать рассылку, удалить ее, перенести на другое время или закрыть это окно и ждать выполнение рассылки:
				<a href='javascript:window.close()' class='btn btn-primary btn-sm' target=''>Закрыть окно</a>
			 </p>";
		} else {
			print "<p class='alert alert-warning' >Ошибка. Запланировать рассылку не удалось!</p>";
			$db->vkt_send_task_del($vkt_send_id,$ctrl_id,$uid=0);
		}
	} elseif($mode==3) {
		$db->query("UPDATE vkt_send_1 SET del=0 WHERE id='$vkt_send_id'");

		print "<p class='alert alert-warning' >Внимание! Запланирована  рассылка :".
			$db->print_time_shift($tm_shift)." с момента наступления события: <b>".
			$db->dlookup("source_name","sources","id='{$r['sid']}'")."</b> на лэндинге: <b>{$r['land_num']}</b>".
			"<br>
			Вы можете еще отредактировать рассылку, удалить ее, перенести на другое время или закрыть это окно и ждать выполнение рассылки:
			<a href='javascript:window.close()' class='btn btn-primary btn-sm' target=''>Закрыть окно</a>
		 </p>";
			print "<script>opener.location='vkt_send_list.php?view=yes'</script>";
	}
}
if(isset($_GET['do_btn_send'])) {
	exit;
	//~ print "<h2>Начинаем рассылку!</h2>";
	//~ $tm1=$db->dt1(time());
	//~ foreach($arr_res AS $uid) {
		//~ $name=$db->dlookup("name","cards","uid='$uid'");
		//~ $mob=$db->dlookup("mob","cards","uid='$uid'");
		//~ if($tm=$db->dlookup("tm","vkt_send_log","uid='$uid' AND tm>='$tm1'")
			//~ || $db->dlookup("tm","vkt_send_at_log","uid='$uid' AND tm>='$tm1'")) {
			//~ print "<p class='alert alert-warning' >
				//~ <a href='msg.php?uid=$uid' class='' target='_blank'>$name</a>
				//~ пропускаем: рассылка сегодня уже делалась -
				//~ ".date("d.m.Y H:i",$tm).". 
				//~ </p>";
			//~ continue;
		//~ }

		//~ if(!empty($vk_attach)) {
			//~ $db->vkt_send_vk_photo=$vk_attach;
		//~ }
		//~ if(!empty($tg_image)) {
			//~ $db->vkt_send_tg_photo=$tg_image;
		//~ }
		//~ if(!empty($tg_video_note)) {
			//~ $db->vkt_send_tg_video_note=$tg_video_note;
		//~ }
		//~ if(!empty($tg_video)) {
			//~ $db->vkt_send_tg_video=$tg_video;
		//~ }
		//~ if(!empty($tg_audio)) {
			//~ $db->vkt_send_tg_audio=$tg_audio;
		//~ }

		//~ $db->vkt_send_wa($uid,$msg);

		//~ //$db->print_r($db->vkt_send_res);
		//~ $db->query("INSERT INTO vkt_send_log SET
			//~ uid='$uid',
			//~ tm='".time()."',
			//~ res_vk='{$db->vkt_send_res['vk']}',
			//~ res_tg='{$db->vkt_send_res['tg']}',
			//~ res_wa='{$db->vkt_send_res['wa']}'
			//~ ");
		//~ $v=($db->vkt_send_res['vk']!=1 && $db->vkt_send_res['tg']!=1)?"danger":"success";

		//~ if($db->vkt_send_res['tg']==1)
			//~ $res_tg='OK';
		//~ elseif($db->vkt_send_res['tg']==2)
			//~ $res_tg='Х';
		//~ else
			//~ $res_tg='-';

		//~ if($db->vkt_send_res['vk']==1)
			//~ $res_vk='OK';
		//~ elseif($db->vkt_send_res['vk']==2)
			//~ $res_vk='Х';
		//~ else
			//~ $res_vk='-';

		//~ print "<p class='alert alert-$v' >
			//~ <a href='msg.php?uid=$uid' class='' target='_blank'>$name</a>
			//~ ТГ-<span class='badge' >$res_tg</span>
			//~ ВК-<span class='badge' >$res_vk</span>
			//~ </p>";
//~ //		break;
	//~ }
}
?>
<div class='container' >
	<h2><?=$name_send?></h2>
	<div class='card bg-light p-2' >
		<div class='' >По текущим фильтрам:
		VK-<span class='badge badge-secondary' ><?=$n_vk?></span>
		TG-<span class='badge badge-secondary' ><?=$n_tg?></span>
		EMAIL-<span class='badge badge-secondary' ><?=$n_email?></span>
<!--
		ВСЕГО-<span class='badge badge-secondary' ><?=$n_all?></span>
-->
		</div>
	</div>
	<form class='' method='POST' action='?' >
		<div class='form-group'>
			<label for='name_send'>Название</label>
			<input class='form-control' type='text' name='name_send' value='<?=$name_send?>' id='name_send'>
		</div>

	<?if($mode==1) {
		?>	
		<div class='card bg-light p-2' >
			<div class='form-group text-center' >
					<label for='dt1'>С даты</label>
					<input class='form-control text-center' style='display:inline;width:120px;' type='text' name='dt1' value='<?=$dt1?>' id='dt1'>
					<label for='dt2'>По дату</label>
					<input class='form-control text-center' style='display:inline;width:120px;' type='text' name='dt2' value='<?=$dt2?>' id='dt2'>
			</div>

			<div class='pl-3' >
				<?
				//~ $cnt_leads_vk=sizeof($db->vkt_send_filter_cnt_vk($arr_leads));
				//~ $cnt_leads_tg=sizeof($db->vkt_send_filter_cnt_tg($arr_leads));
				//~ $cnt_leads_email=sizeof($db->vkt_send_filter_cnt_email($arr_leads));
				?>
				<div class='form-check'>
					<input type='checkbox' class='form-check-input' id='fl_clients' name='fl_clients' <?=$fl_clients_checked?> >
					<label class='form-check-label' for='fl_clients'>только клиенты <span class='badge badge-secondary' title='учтены только те, кто подписан на чатбот или есть емэйл'><?=sizeof($arr_clients)?></span></label>
					&nbsp;&nbsp;&nbsp;<br>
					<input type='checkbox' class='form-check-input' id='fl_partners'  name='fl_partners'<?=$fl_partners_checked?> >
					<label class='form-check-label' for='fl_partners'>только партнеры <span class='badge badge-secondary'  title='учтены только те, кто подписан на чатбот или есть емэйл'><?=sizeof($arr_partners)?></span></label>
					&nbsp;&nbsp;&nbsp;<br>
					<input type='checkbox' class='form-check-input' id='fl_leads'  name='fl_leads'<?=$fl_leads_checked?> >
					<label class='form-check-label' for='fl_leads'>только подписчики (не партнеры, не клиенты) <span class='badge badge-secondary'  title='учтены только те, кто подписан на чатбот или есть емэйл'><?=sizeof($arr_leads)?></span></label>

					<div class=' my-3' >
					<input type='checkbox' class='form-check-input' id='fl_tg' name='fl_tg' <?=$fl_tg_checked?> >
					<label class='form-check-label' for='fl_tg'>только TG <span class='badge badge-secondary' title='отправлять в телеграм'></span></label>
					&nbsp;&nbsp;&nbsp;<br>
					<input type='checkbox' class='form-check-input' id='fl_vk'  name='fl_vk'<?=$fl_vk_checked?> >
					<label class='form-check-label' for='fl_vk'>только VK <span class='badge badge-secondary'  title='отправлять в ВК'></span></label>
					&nbsp;&nbsp;&nbsp;<br>
					<input type='checkbox' class='form-check-input' id='fl_email'  name='fl_email'<?=$fl_email_checked?> >
					<label class='form-check-label' for='fl_email'>только EMAIL <span class='badge badge-secondary'  title='отправлять на емэйл'></span></label>
					</div>

					<br>
					<label for='fl_razdel'>По сегменту:</label>
					<select id='fl_razdel'  name='fl_razdel' class='form-control' >
					<?
					$res=$db->query("SELECT * FROM razdel WHERE del=0 AND id>0");
					print "<option value='-1' >все сегменты</option>";
					while($r=$db->fetch_assoc($res)) {
						$sel=($r['id']==$fl_razdel)?"SELECTED":"";
						print "<option value='{$r['id']}' $sel>{$r['razdel_name']}</option>";
					}
					?>
					</select>
					
					<label for='fl_land'>По лэндингу:</label>
					<select id='fl_land'  name='fl_land' class='form-control' >
					<?
					$res=$db->query("SELECT * FROM lands WHERE del=0 AND land_num>0 AND product_id=0 ORDER BY land_num");
					print "<option value='0' >любой лэндинг</option>";
					while($r=$db->fetch_assoc($res)) {
						$sel=($r['land_num']==$fl_land)?"SELECTED":"";
						print "<option value='{$r['land_num']}' $sel>({$r['land_num']}) {$r['land_name']}</option>";
					}
					?>
					</select>

					<label for='fl_tag'>По тэгу:</label>
					<select id='fl_tag'  name='fl_tag' class='form-control' >
					<?
					$res=$db->query("SELECT * FROM tags WHERE del=0 ORDER BY tag_name");
					print "<option value='0' >любой тэг</option>";
					while($r=$db->fetch_assoc($res)) {
						$sel=($r['id']==$fl_tag)?"SELECTED":"";
						print "<option value='{$r['id']}' $sel>{$r['tag_name']}</option>";
					}
					?>
					</select>

					<div class="form-check card p-2 mt-3 pl-5">
						<?$chk=($fl_chk)?"CHECKED":"";?>
					  <input class="form-check-input " type="checkbox" id="fl_chk" name='fl_chk' <?=$chk?> >
					  <label class="form-check-label" for="fl_chk">
						Только отмеченные (чекбокс)
					  </label>
					</div>

				</div>
			</div>
		</div>
		<div class='card bg-warning text-center p-2' >
			<div class='form-group'>
				<label for='dt_delay'>Время отправки</label>
				<input class='form-control text-center' style='display:inline;width:120px;' name='dt_delay' id='dt_delay' value='<?=$dt_delay?>' >
				<input class='form-control text-center' style='display:inline;width:100px;' name='tm_delay' id='tm_delay' value='<?=$tm_delay?>' >
			</div>
		</div>
	<?} elseif($mode==2) {
		$days_shift=intval($tm_shift/(24*60*60));
		$tm_rest=$tm_shift-($days_shift*24*60*60);
		$hours_shift=intval($tm_rest/(60*60));
		$tm_rest=$tm_shift-($days_shift*24*60*60)-($hours_shift*60*60);
		$min_shift=intval($tm_rest/60);
		$dt_scdl=date('d.m.Y H:i',$db->dlookup("tm_scdl","lands","land_num='$land_num'"));
	?>
		<div class='card bg-light form-group p-3'>
			<div>
				<label for='days_shift'>Отправить</label>
				<select name='shift_direction' class='form-control'  style='display:inline;width:160px;' >
					<?$sel=($tm_shift>0)?"SELECTED":"";?>
					<option value='shift_forward' <?=$sel?>>через </option>
					<?$sel=($tm_shift<0)?"SELECTED":"";?>
					<option value='shift_backward' <?=$sel?>>до события за </option>
				</select>
				<div>
				<input class='form-control text-center' style='display:inline;width:60px;' name='days_shift' id='days_shift' value='<?=abs($days_shift)?>' > дней
				<input class='form-control text-center' style='display:inline;width:60px;' name='hours_shift' id='hours_shift' value='<?=abs($hours_shift)?>' > часов
				<input class='form-control text-center' style='display:inline;width:60px;' name='min_shift' id='min_shift' value='<?=abs($min_shift)?>' > минут
				</div>
				<p class='py-3' >относительно времени мероприятия <b><?=$dt_scdl?></b> на лэндинге:</p>
				<select class='form-control text-center' style='' name='land_num'>
					<?
					$res=$db->query("SELECT * FROM lands WHERE del=0 AND tm_scdl>0 ORDER BY land_num");
					while($r=$db->fetch_assoc($res)) {
						$sel=($land_num==$r['land_num'])?"SELECTED":"";
						print "<option value='{$r['land_num']}' $sel >{$r['land_num']} {$r['land_name']}</option>";
					}
					?>
				</select>
				<h3 class='text-center mt-3' >то есть в: <b><?=($vkt_send_tm)?date('d.m.Y H:i',$vkt_send_tm):"<span class='badge badge-danger p-1' >ошибка - дата в прошедшем времени</span>"?></b></h3>
				<button type='submit' name='btn_save' value='go' class='btn btn-info btn-sm' >Сохранить</button>&nbsp
			</div>
		</div>
	<?} elseif($mode==3) { //3
		$days_shift=intval($tm_shift/(24*60*60));
		$tm_rest=$tm_shift-($days_shift*24*60*60);
		$hours_shift=intval($tm_rest/(60*60));
		$tm_rest=$tm_shift-($days_shift*24*60*60)-($hours_shift*60*60);
		$min_shift=intval($tm_rest/60);
	?>
		<div class='card bg-light form-group p-3'>
			<div>
				<label for='days_shift'>Отправить</label>
				<select name='shift_direction' class='form-control'  style='display:inline;width:160px;' >
					<?$sel=($tm_shift>0)?"SELECTED":"";?>
					<option value='shift_forward' <?=$sel?>>через </option>
					<?$sel=($tm_shift<0)?"SELECTED":"";?>
					<option value='shift_backward' <?=$sel?>>до события за </option>
				</select>
				<div>
				<input class='form-control text-center' style='display:inline;width:60px;' name='days_shift' id='days_shift' value='<?=abs($days_shift)?>' > дней
				<input class='form-control text-center' style='display:inline;width:60px;' name='hours_shift' id='hours_shift' value='<?=abs($hours_shift)?>' > часов
				<input class='form-control text-center' style='display:inline;width:60px;' name='min_shift' id='min_shift' value='<?=abs($min_shift)?>' > минут
<!--
				<br> В <input type='time' class='form-control text-center' style='display:inline;width:100px;' name='in_time' id='in_time' value='<?=$in_time?>' > МСК
-->
				</div>
				<br>
				<p class='py-3' >относительно события:</p>
				<?
					print "<select name='sid' class='form-control' style=''>";
					$res1=$db->query("SELECT * FROM sources WHERE del=0");
					print "<option value='-1'>не установлено</option>";
					while($r1=$db->fetch_assoc($res1)) {
						if(!in_array($r1['id'],$vkt_send_sid_arr))
							continue;
						$sel=($sid==$r1['id'])?"SELECTED":"";
						print "<option value='{$r1['id']}' $sel>{$r1['id']} {$r1['source_name']}</option>";
					}
					print "</select>";
				?>
				
				
				<p class='py-3' >на лэндинге:</p>
				<select class='form-control text-left' style='' name='land_num'>
					<?
					$res2=$db->query("SELECT * FROM lands WHERE del=0 ORDER BY land_num");
					print "<option value='0' $sel >= любой лэндинг =</option>";
					while($r2=$db->fetch_assoc($res2)) {
						$sel=($land_num==$r2['land_num'])?"SELECTED":"";
						print "<option value='{$r2['land_num']}' $sel >{$r2['land_num']} {$r2['land_name']}</option>";
					}
					?>
				</select>
			</div>
		</div>
	<?}?>
		<div class='form-group'>
			<label for='msg'>Текст сообщения:
			<p class='card p-2 bg-light my-2 small text-muted' >Подстановки: <?=nl2br($db->prepare_msg_codes())?></p>
			</label>
			<textarea class='form-control' name='msg' id='msg' rows='12' ><?=$msg?></textarea>
		</div>

		<div class='card p-2 my-2' >
		<div class='form-group'>
			<label for='email_template'>Шаблон email Unisender</label>
			<input class='form-control' type='text' name='email_template' value='<?=$email_template?>' id='email_template'>
			<small class='mute' ><a href='https://go1.unisender.ru/ru/templates/list' class='' target='_blank'>Шаблон email Unisender</a>. (например: 7ceb7438-0947-11ee-ae0b-763d6d0eb430)</small>
		</div>
		<div class='form-group'>
			<label for='email_from'>Поле ОТ: Email отправителя</label>
			<input class='form-control' type='text' name='email_from' value='<?=$email_from?>' id='email_from'>
			<small class='mute' >Домен отправителя должен быть <a href='https://go1.unisender.ru/ru/domain' class='' target='_blank'>зарегистрирован в UNISENDER</a>. Можно оставить пустым, тогда значение будет взято из шаблона.</small>
		</div>
		<div class='form-group'>
			<label for='email_from_name'>Поле ОТ: Имя отправителя</label>
			<input class='form-control' type='text' name='email_from_name' value='<?=$email_from_name?>' id='email_from_name'>
			<small class='mute' >Можно оставить пустым, тогда значение будет взято из шаблона.</small>
		</div>
		</div>
		<div class='form-group'>
			<label for='vk_attach'>Вложение ВК</label>
			<input class='form-control' type='text' name='vk_attach' value='<?=$vk_attach?>' id='vk_attach'>
			<small class='mute' >Ссылка на фото или видео в сообществе ВК в общем доступе</small>
		</div>
		
		<div class='form-group'>
			<label for='tg_image'>Изображение ТГ</label>
			<input class='form-control' type='text' name='tg_image' value='<?=$tg_image?>' id='tg_image'>
		</div>
		
		<div class='form-group'>
			<label for='tg_video'>Видео ТГ</label>
			<input class='form-control' type='text' name='tg_video' value='<?=$tg_video?>' id='tg_video'>
		</div>
		
<!--
		<div class='form-group'>
			<label for='tg_video_note'>Видео в кружочке ТГ</label>
			<input class='form-control' type='text' name='tg_video_note' value='<?=$tg_video_note?>' id='tg_video_note'>
		</div>
		
-->
		<div class='form-group'>
			<label for='tg_audio'>Аудио ТГ</label>
			<input class='form-control' type='text' name='tg_audio' value='<?=$tg_audio?>' id='tg_audio'>
		</div>

		<input type='hidden' name='vkt_send_id' value='<?=$vkt_send_id?>'>
		<div>
			<button type='submit' name='btn_save' value='go' class='btn btn-info btn-lg' >Сохранить</button>&nbsp
			<button type='submit' name='btn_test' value='go' class='btn btn-success btn-lg' >Тест себе</button>&nbsp;
			<?$disabled=""; //($mode==3)?"DISABLED":""; ?>
			<button type='submit' name='btn_send' value='go' class='btn btn-primary' <?=$disabled?>>Запланировать</button>&nbsp;
			<button type='submit' name='btn_del' value='go' class='btn btn-danger' >Удалить</button>&nbsp;
			<a href='javascript:opener.location.reload();window.close();' class='' target=''><button type='button' name='btn_cancel' value='go' class='btn btn-warning' >Закрыть</button></a>
		</div>

		<div class='card p-3' >
			<div class='form-group'>
				<?
					$db->connect('vkt');
					$test_uid=$db->dlookup("test_uid","0ctrl","id='$ctrl_id'");
					$test_uid_url=($test_uid)?$DB200."/msg.php?uid=".$test_uid:"";
					$db->connect($database);
				?>
				<label for='test_uid'>Кому отправлять Тест себе <span class='badge badge-secondary p1' >
					<a data-toggle='popover'
						title='Тест себе'
						data-content='Вставьте ссылку на карточку в CRM куда вы хотели бы отправлять тестовые сообщения.'
						>?
					</a></span></label>
				<p class='small' >Вставьте ссылку на карточку в CRM куда вы хотели бы отправлять тестовые сообщения.</p>
				<input class='form-control' type='text' name='test_uid'	value='<?=$test_uid_url?>' id='test_uid'>
				<p>
					<?
					//print "HERE_$test_uid_disp";
					if($test_uid) {
						if(!$test_uid=$db->dlookup("uid","cards","uid='$test_uid'")) {
							print "<script>document.getElementById('test_uid').value = '';</script>";
							$db->connect('vkt');
							if(!$test_uid=$db->dlookup("test_uid","0ctrl","id='$ctrl_id'"))
								$test_uid=false;
						}
					} else {
						print "<script>document.getElementById('test_uid').value = '';</script>";
						$db->connect('vkt');
						if(!$test_uid=$db->dlookup("test_uid","0ctrl","id='$ctrl_id'"))
							$test_uid=false;
					}
					if($test_uid) {
						$tg_test=$db->dlookup("telegram_id","cards","uid='$test_uid'");
						$tg_test_nic=$db->dlookup("telegram_nic","cards","uid='$test_uid'");
						$tg_test=($tg_test)?$tg_test:"нет";
						$tg_test_nic=($tg_test)?$tg_test_nic:"нет";
						$vk_test=$db->dlookup("vk_id","cards","uid='$test_uid'");
						$vk_test_url=($vk_test)?"<a href='https://vk.com/id$vk_test' class='' target='_blank'>https://vk.com/id$vk_test</a>":"нет";
						$name=$db->dlookup("name","cards","uid='$test_uid'");
						$mob=$db->dlookup("mob","cards","uid='$test_uid'");
						$email=$db->dlookup("email","cards","uid='$test_uid'");
						$email=!empty($email)?$email:"нет";
						$db->connect($database);
						print "<div class='card p-2' >
							<p>Отправляем тест на: <b>$name ($mob)</b></p>
							<p>Телеграм: <b>$tg_test_nic id=$tg_test</b></p>
							<p>ВК: <b>$vk_test_url</b></p>
							<p>Email: <b>$email</b></p>
							</div>
							";
					} else
						print "<p class='alert alert-warning' >Не указано кому отправлять тест</p>"; 
					?>
				</p>
			</div>
		</div>
		
	</form>
</div>

<div class='container' >
	<h2>Выбрано для рассылки</h2>
	<p>первые 50 контактов</p>
	<?
	print "<table class='table table-condenced table-striped' >
	<thead>
		<tr>
			<th>№</th>
			<th>Имя</th>
			<th>Email</th>
			<th>ВК</th>
			<th>ТГ</th>
			<th>посл. рассылка</th>
		</tr>
	</thead>";
	$n=1;
	if(empty($email_template) && !empty($msg)) {
		$arr=$db->vkt_send_filter_cnt_vk($arr_res);
		$arr=array_merge($arr,$db->vkt_send_filter_cnt_tg($arr_res));
	} elseif(!empty($email_template) && empty($msg)) {
		$arr=$db->vkt_send_filter_cnt_email($arr_res);
	} elseif(empty($email_template) && empty($msg)) {
		$arr=[];
	} else
		$arr=$arr_res;
	rsort($arr);
	foreach($arr AS $uid) {
		$r=$db->fetch_assoc($db->query("SELECT * FROM cards WHERE uid='$uid'"));
		$name=$r['surname'].' '.$r['name'];
		$mob=$r['mob_search'];
		$email=!empty($r['email'])?"&#x2713;":"-";
		$tg=$r['telegram_id']?"&#x2713;":"-";
		$vk=$r['vk_id']?"&#x2713;":"-";
		$tm_last=$db->dlookup("tm","vkt_send_log","uid='$uid' AND (res_vk=1 OR res_tg=1 OR res_wa=1)");
		$dt_last=$tm_last?date("d.m.Y",$tm_last):"-";

		print "<tr>
			<td title='$uid'>$n</td>
			<td title='$mob {$r['email']}'>$name</td>
			<td>$email</td>
			<td>$vk</td>
			<td>$tg</td>
			<td>$dt_last</td>
		</tr>";
		if($n++==50)
			break;
	}
	print "</table>";
	?>
</div>


<script>
$('#dt1').datepicker({
	weekStart: 1,
	daysOfWeekHighlighted: "6,0",
	monthNames: ['Январь', 'Февраль', 'Март', 'Апрель','Май', 'Июнь', 'Июль', 'Август', 'Сентябрь','Октябрь', 'Ноябрь', 'Декабрь'],
	dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
	autoclose: true,
	todayHighlight: true,
	format: 'dd.mm.yyyy',
	language: 'ru',
});
$('#dt2').datepicker({
	weekStart: 1,
	daysOfWeekHighlighted: "6,0",
	monthNames: ['Январь', 'Февраль', 'Март', 'Апрель','Май', 'Июнь', 'Июль', 'Август', 'Сентябрь','Октябрь', 'Ноябрь', 'Декабрь'],
	dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
	autoclose: true,
	todayHighlight: true,
	format: 'dd.mm.yyyy',
	language: 'ru',
});
$('#dt_delay').datepicker({
	weekStart: 1,
	daysOfWeekHighlighted: "6,0",
	monthNames: ['Январь', 'Февраль', 'Март', 'Апрель','Май', 'Июнь', 'Июль', 'Август', 'Сентябрь','Октябрь', 'Ноябрь', 'Декабрь'],
	dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
	autoclose: true,
	todayHighlight: true,
	format: 'dd.mm.yyyy',
	language: 'ru',
});
</script>

<script>
$(function () {
  $('[data-toggle="popover"]').popover()
})
</script>

<?
$t->bottom();
?>
