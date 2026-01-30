<?
include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
class msg__ extends db {
	var $userdata,$uid,$acc_id;
	var $acc_id_name="";
	var $acc_id_name_href="";
	var $token=0;
	var	$style_uid="background-color: #FFFF6F;";
	var	$style_acc_id="background-color: #C9FFF9;";
	var	$style_acc_id_julia="background-color: #FFD2D2;";
	var $allow_change_acc=false;
	var $photo_50="";
	var $disabled="";
	var $is_friend=0;
	var $can_write_private_message=1;
	var $can_send_friend_request=1;
	var $blacklisted=0;
	var $send_talk_to_email=array("vlav@mail.ru");
	var $send_talk_to_email_from="noreply@winwinland.ru";
	var $send_talk_to_vk=array();
	var $msg_add_to_friends="";
	var $save_images=false;
	var $save_msgs=false;
	var $error_code=0;
	var $vklist_mode=false; //run from vklist, no client in cards
	var $vklist_mode_gid=0; //vklist group id where sending from
	var $request_to_friends_as_default=true;
	var $vkgrp_acc=false;
	var $gid=0;
	var $email_from="info@yogahelpyou.com";
	var $email_from_name="Школа йоги Андрея Викторова";
	var $email_subj="Re:Йога";
	var $pact_token="yogahelpyou";
	var $tg_bot="yogahelpyou_bot";
	var $sid_visited_webinar=[13,14,16];

	function lock_chk($uid) {
		if(!is_numeric($uid))
			return false;
		$this->disabled="";
//		if($this->vklist_mode)
	//		return true;
		$timeout=15*60;
		$user_id=$this->userdata['user_id'];
		if(!$r=$this->fetch_assoc($this->query("SELECT lock_tm, lock_user_id,username,real_user_name
					FROM cards 
					JOIN users ON users.id=lock_user_id 
					WHERE cards.uid=$uid AND lock_user_id>0 AND lock_user_id!=$user_id")))
			return true;
		$lock_tm=$r['lock_tm'];
		if($timeout-(time()-$lock_tm)<=0) {
		//print "HERE_"; exit;
			$this->query("UPDATE cards SET lock_tm=0, lock_user_id=0 WHERE uid=$uid");
			return true;
		}
		$sec=(int)((time()-$lock_tm)/60)." min ". (int)((time()-$lock_tm)%60) ." sec";
		$user_name=$r['username']!='vlav'?$r['real_user_name']:"service engineer";
		print "<div class='alert alert-danger'>Blocked by <b>$user_name ($user_id)</b> at : ".date("d.m.Y H:i",$lock_tm)." for ".($timeout/60)." min - wait for ".($timeout-(time()-$lock_tm))." seconds</div>";
		$this->disabled="disabled";
		return false;
	}
	function lock_on($uid) {
		if($this->vklist_mode)
			return true;
		$this->query("UPDATE cards SET lock_tm=".time().",lock_user_id=".$this->userdata['user_id']." WHERE uid=$uid");
	}
	function lock_off($uid) {
		if($this->vklist_mode)
			return true;
		$this->query("UPDATE cards SET lock_tm=0,lock_user_id=0 WHERE uid=$uid");
	}
	function discount_card($uid) {
		return "";
	}
	function msg_info_specprice($uid) {
		if($this->dlookup("price_id","discount","uid='$uid' AND dt2>='".time()."'",0))
			$price_msg="<div><a href='javascript:wopen_1(\"discount.php?uid=$uid\")' class='' target=''><button class='btn btn-sm btn-danger' >Спеццена установлена</button></a></div>";
		else
			$price_msg="<div><a href='javascript:wopen_1(\"discount.php?uid=$uid\")' class='' target=''><button class='btn btn-sm btn-info' >Установить спеццену</button></a></div>";
		print "<dt>Скидки и спеццены</dt>";
		print "<dd>$price_msg
				</dd>";
	}
	function ch_user_id ($uid,$user_id_from,$user_id_to) {
		//~ $this->mark_new($uid_to,1);
		//~ $this->notify($uid_to,"Вам назначен лид");
		$klid=$this->dlookup("klid","users","id='$user_id_to'");
		$this->query("UPDATE cards SET user_id='$user_id_to',pact_conversation_id=0,utm_affiliate='$klid' WHERE uid='$uid'",0);
	}
	function tbl_agent($user_id) {
		//print "HERE_".$this->userdata['access_level'];
		//~ print "<div class='card bg-light_ text-right'>
			//~ <input type='submit' class='btn btn-info' value='Закрыть' onclick='location=\"?window_close=yes&uid=$this->uid\";'>
			//~ <input type='submit' class='btn btn-info' value='Закрыть и оставить непрочитанным' onclick='location=\"?window_close_and_leave_unread=yes&uid=$this->uid\";'>
		//~ </div>";
		if(isset($_GET['ch_user_id'])) {
			//print "HERE {$_GET['sel_user_id']}";
			$user_id=intval($_GET['sel_user_id']);
			$old_user_id=$this->dlookup("user_id","cards","uid='$this->uid'");
			$login=$this->dlookup("username","users","id='$user_id'");
			$name=$this->dlookup("real_user_name","users","id='$user_id'");
			$old_login=$this->dlookup("username","users","id='$old_user_id'");
			$old_name=$this->dlookup("real_user_name","users","id='$old_user_id'");
			$admin_login=$this->dlookup("username","users","id='{$_SESSION['userid_sess']}'");
			$admin_name=$this->dlookup("real_user_name","users","id='{$_SESSION['userid_sess']}'");
			if($user_id) {
				$this->ch_user_id ($this->uid,$old_user_id,$user_id);
				$this->save_comm($this->uid,$_SESSION['userid_sess'],"Передача другому партнеру: $old_user_id -> $user_id",121,$old_user_id);
				print "<script>location='?uid=$this->uid';</script>";
			}
		}
		if(isset($_GET['ch_user_id_clr'])) {
			$old_user_id=$this->dlookup("user_id","cards","uid='$this->uid'");
			//$this->query("UPDATE cards SET user_id='0' WHERE uid='$this->uid'",0);
			$this->hold_clr($this->uid);
			$this->save_comm($this->uid,$_SESSION['userid_sess'],"Ручной сброс закрепления за партнером ($old_user_id)",121,$old_user_id);
			print "<script>location='?uid=$this->uid';</script>";
		}
		if(isset($_GET['head_control_set'])) {
			$this->query("UPDATE head_control SET del=1 WHERE uid='$this->uid' AND user_id={$_SESSION['userid_sess']}");
			if(empty($_GET['head_control_comm']))
				$_GET['head_control_comm']="under control";
			$this->query("INSERT INTO head_control SET uid='$this->uid',user_id='{$_SESSION['userid_sess']}',tm='".time()."',comm='".$this->escape($_GET['head_control_comm'])."'");
		}
		if(isset($_GET['head_control_clr'])) {
			$this->query("UPDATE head_control SET del=1 WHERE uid='$this->uid' AND user_id={$_SESSION['userid_sess']}");
		}
		?>
		<div class='alert alert-success py-0 mb-0' style='line-height:2.0;'>
			<form class='form-inline' id='FormUserList'  action='?uid=$this->uid'>
		<?
		if($user_id>0) {
			$real_user_name=$this->dlookup("real_user_name","users","id=$user_id")." ($user_id)";
			print "<div class=' d-inline-block' >Клиента рекомендовал: <span class='p-1 rounded bg-danger text-white d-inline-block' >".$real_user_name."</span></div>\n";
		} else {
			$real_user_name="";
			print "<div class=' d-inline-block'>Клиент никому не назначен</div>\n";
		}
		if($this->userdata['access_level']<=3) { //if admin
		?>
			<div class="form-group m-0 p-0">
			  <div class="input-group  m-0 p-0">
				<input type="text" class="form-control" id="userInput" placeholder="клиента рекомендовал" value='<?=$real_user_name?>'  autocomplete="off">
			  </div>
			  <input type="hidden" id="userID" name="sel_user_id"> <!-- Скрытое поле для записи ID -->
			  <input type="hidden" name="ch_user_id" value='yes'>
			</div>
			<button type='submit' class='btn btn-light btn-sm m-1' name='ch_user_id_clr' value='yes'><span class='fa fa-remove' title='удалить партнера'></span></button>
		  </form>
		  <div id="userList" class='m-0 p-0' ></div>
  		<?
		} else { //>3
		?>
			</form>
		<?
		}
		print "</div>\n";
		
		//~ if($_SESSION['access_level']<3) { //head control
			//~ $head_control_comm=$this->fetch_assoc($this->query("SELECT * FROM head_control WHERE del=0 AND uid='$this->uid' AND user_id={$_SESSION['userid_sess']} ORDER BY tm DESC LIMIT 1"))['comm'];
			//~ $head_control_chk=($head_control_comm)?"checked":"";
			//~ if(!$head_control_comm)
				//~ $head_control_comm="";
			//~ print "<div class='alert alert-info' ><form action='?uid=$this->uid' class='form-inline' >
			//~ <input type='checkbox' name='head_control_chk' $head_control_chk>
			//~ <input type='text' name='head_control_comm' class='form-control'  style='width:80%;' value='$head_control_comm'>
			//~ <button class='btn btn-primary' type='submit' name='head_control_set' value='yes'><span class='fa fa-save' ></span></button>
			//~ <button class='btn btn-warning'  type='submit' name='head_control_clr' value='yes'><span class='fa fa-remove' ></span></button>
			//~ </form></div>";
		//~ }
		return;
	}
	function tbl_manager() {
		$uid=$this->uid;
		if(isset($_GET['ch_man_id'])) {
			$man_id=intval($_GET['man_id']);
			if($man_id) {
				$old_man_id=$this->dlookup("man_id","cards","uid='$uid'");
				$this->query("UPDATE cards SET man_id='$man_id' WHERE uid='$uid'",0);
				$this->save_comm($uid,$_SESSION['userid_sess'],"Передача другому менеджеру: $old_man_idф -> $man_id",122,$old_man_id);
			}
		}
		if(isset($_GET['ch_man_id_clr'])) {
			$man_id=0;
			$old_man_id=$this->dlookup("man_id","cards","uid='$uid'");
			$this->query("UPDATE cards SET man_id='$man_id' WHERE uid='$uid'",0);
			$this->save_comm($uid,$_SESSION['userid_sess'],"Очистка привязки к менеджеру: $old_man_id",122,$old_man_id);
		}
		$man_id=$this->dlookup("man_id","cards","uid='$uid'");
		$man_name=($man_id)?$this->dlookup("real_user_name","users","id='$man_id'"):"НЕТ";
		if($man_id==3)
			$man_name='admin';
		print "<div class='alert alert-info py-0'>\n";
		if($this->userdata['access_level']<=3) { //if admin
			?>
			<div>
			<form class='form-inline'  action='?uid=<?=$uid?>' id='f_man_id'>
			Назначен менеджер: <span class=' p-1 mx-1 bg-info text-white rounded' > <?=$man_name?></span>
			&nbsp;
			<select name='man_id' id='man_id' class='p-0 form-control' >
				<?
				$res=$this->query("SELECT * FROM users WHERE users.id>=2 AND del=0 AND fl_allowlogin=1 AND (access_level<=4) ");
				print "<option value='0'>=не выбран=</option>";
				while($r=$this->fetch_assoc($res)) {
					$sel=($r['id']==$man_id)?"SELECTED":"";
					if($r['username']=='admin')
						$r['real_user_name']='admin';
					print "<option value='{$r['id']}' $sel>{$r['real_user_name']}</option>";
				}
				?>
			</select>
			<input type='hidden' name='ch_man_id' value='yes'>
<!--
			<button type='submit' name='ch_man_id'  value='yes' class='btn btn-primary mx-1' ><span class='fa fa-save' title='Изменить'></span></button>
-->
			<button type='submit' name='ch_man_id_clr' value='yes' class='btn btn-light btn-sm mx-1' ><span class='fa fa-remove'  title='Очистить'></span></button>
			</form>
			</div>
			<?
		} else { //if access_level>3
			if($man_id==$_SESSION['userid_sess']) {
				print "<div>Назначен менеджер: <span class='p-1 bg-info text-white' >$man_name</span></div>\n";
			} else {
				print "<div> Назначен менеджер:
					<span class='p-1 rounded bg-info text-white' >$man_name</span>
					<a href='?ch_man_id=yes&man_id={$_SESSION['userid_sess']}&uid=$uid'
						class='btn btn-sm btn-warning' target=''>Забрать себе
					</a></div>\n";
			}
		}
		print "</div>\n";
	}
	function uid_info_add() {
	}
	function ch_avangard_pay_end($avangard_id) {
		if($_SESSION['access_level']>3)
			return "";
		$tm_end=$this->dt2($this->dlookup("tm_end","avangard","id='$avangard_id'"));
		$dt_end=date("d.m.Y",$tm_end);
		if(isset($_GET['do_ch_avangard_tm_end'])) {
			$id=intval($_GET['avangard_id']);
			$tm=$this->dt2($this->date2tm($_GET['new_avangard_tm_end']));
			if($id && $tm) {
				//$db->query("UPDATE avangard SET tm_end='$tm' WHERE id='$id'");
				$this->avangard_tm_end_set($id,$tm);
				$this->vkt_email("VKT msg.php avangard_tm_end chanded for avangard_id=$id to ".date("d.m.Y H:i",$tm)." by {$_SESSION['userid_sess']}","");
				$uid=$this->dlookup("vk_uid","avangard","id='$id'");
				print "<script>location='?uid=$uid#tm_pay_section'</script>";
			}
			//return "HERE";
		}
		return "<form class='form-inline' >
			Продлить до: 
			<input type='text' name='new_avangard_tm_end' value='$dt_end' class='text-center' >
			<input type='hidden' name='avangard_id' value='$avangard_id'>
			<button type='submit' class='btn btn-sm btn-info'  name='do_ch_avangard_tm_end' value='yes'>Продлить</button>
			</form>";
	}

	function send_avangard_email($avangard_id) {
		global $unisender_secret,$email_from,$email_from_name,$base_prices;
		$uid=$this->uid;

		$this->connect('vkt');
		if(!$client_ctrl_id=$this->dlookup("id","0ctrl","uid='$uid'")) {
			print "<p class='alert alert-warning' >Ошибка. У клиента нет доступа к системе</p>";
			return false;
		}

		$this->connect($this->database);
		include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
		$vkt=new vkt($this->database);
		$ctrl_link=$vkt->get_ctrl_link($client_ctrl_id);
		$passw=$this->dlookup("admin_passw","0ctrl","id='$client_ctrl_id'");
		$product_id=$this->dlookup("product_id","avangard","id='$avangard_id'",0);
		$client_email=$this->dlookup("email","cards","uid='$uid'");
		$client_name=$this->dlookup("name","cards","uid='$uid'")." ".$this->dlookup("surname","cards","uid='$uid'");
		include_once "/var/www/vlav/data/www/wwl/inc/unisender.class.php";
		$uni=new unisender($unisender_secret,$email_from,$email_from_name);
		$sp_template=$base_prices[$product_id]['sp_template'];
		$uni->email_by_template($client_email,
			$sp_template,
			['uid'=>$this->uid_md5($uid),
			'passw'=>$passw,
			'client_name'=>$client_name,
			'vkt_link'=>$ctrl_link
			]);
		print "<p class='alert alert-success' >Письмо клиенту об успешном платеже отправлено</p>";
	}
	function uid_info($uid) {
		if(!intval($uid))
			return false;
		if(isset($_GET['do_ch_name'])) {
			$fname=$this->escape(trim($_GET['fname']));
			$lname=$this->escape(trim($_GET['lname']));
			if(!empty($fname) || !empty($lname)) {
				$this->query("UPDATE cards SET name='".$this->escape($fname)."',surname='".$this->escape($lname)."' WHERE uid='$uid'");
				$klid=$this->dlookup("id","cards","uid='$uid'");
				$this->query("UPDATE users SET real_user_name='".$this->escape($lname." ".$fname)."' WHERE klid='$klid'");
			}
		}
		if(isset($_GET['do_ch_city'])) {
			$city=$this->escape(trim($_GET['city_new']));
			if(!empty($city)) {
				$this->query("UPDATE cards SET city='".$this->escape($city)."' WHERE uid='$uid'");
			}
		}
		if(isset($_GET['do_ch_new_uid'])) {
			//~ $new_uid=intval($_GET['new_uid']);
			//~ if($new_uid) {
				//~ //print "$new_uid $uid"; exit;
				//~ $this->merge_cards($new_uid,$uid,$test=false);
				//~ print "<script>location='?uid=$new_uid'</script>";
			//~ }
		}
		$r=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE uid='$uid'"));
		$del=$r['del'];
		if($uid<0) {
			//~ $name=$this->dlookup("surname","cards","uid=$uid");
			//~ print "<div class='card bg-light'><h2>($uid) $name</h2></div>";
			//return;
		}
		if($uid<1000) { //chat
			//~ print "<div class='card bg-light'><h1>CHAT $uid</h1></div>";
			//~ return;
		}
		
		$vk=new vklist_api($this->token);
		$uid=$this->uid;
		if($r['vk_id']>0) { //$uid>0
			$u=$vk->vk_get_userinfo($r['vk_id']);
		//	$this->print_r($u);
			if(!$u) {
				$this->error_code=$vk->error_code;
				print "<div class='alert alert-warning' >Получить информацию для <b>$uid</b> невозможно. </div>";
				return false;
			}
		} else {
			$u['photo_50']="";
			$u['photo_100']="";
			$u['photo_200']="";
			$u['city']="";
			$u['bdate']="";
			$u['sex']="";
			$u['status']="";
			$u['is_friend']="";
			$u['can_write_private_message']="";
			$u['can_send_friend_request']="";
			$u['blacklisted']="";
			$u['occupation']="";
		}
		print "\n\n<!--uid_info-->\n";
		if($_SESSION['access_level']==1) {
			print "<div class='collapse' id='ch_new_uid'><form>
				<input type ='text' name='new_uid' value='{$r['uid']}'>
				<input type ='submit' name='do_ch_new_uid' value='Go'>
			</form></div>\n\n";
		}
		print "<div class='card bg-light card bg-light-sm'>
			<div class='media'>";
			//print_r($vk);
			$name=htmlspecialchars($this->disp_name_cp($r['name']))."<br> ".htmlspecialchars($this->disp_surname($r['surname']));
			if($this->is_partner_db($uid))
				$partner="<p class='alert alert-success' >🙋‍♀️ Это партнер</p>"; else	$partner="";

			if ($this->database=='vkt') {
				if($this->dlookup("id","0ctrl","uid='$uid'"))
					$client="<p class='alert alert-primary' >🔥 Создан аккаунт ВИНВИНЛАНД</p>"; else	$client="";
			}
			
			print "<div class='collapse' id='setname'><form>
				<input type ='text' name='fname' value='{$r['name']}'>
				<input type ='text' name='lname' value='{$r['surname']}'>
				<input type ='submit' name='do_ch_name' value='Изм.'>
				</form></div>";
			//print "<h1>$name</h1>";
			$photo_50=$u['photo_50'];
			$this->photo_50=$photo_50;
			$photo_100=$u['photo_100'];
			@$photo_200=$u['photo_200'];
			$city=(isset($u['city']))?$vk->vk_get_city_name($u['city']):"город не указан";

			print "<div class='media-left'>\n";
				print ""; //"<a href='https://vk.com/id$uid' target='_blank'><img src='$photo_100'></a>\n";
			print "</div>\n";
			$yesno=array(0=>"<span class='badge badge-warning'>No</span>",1=>"<span class='badge badge-success'>Yes</span>");
			print "<div class='media-body'>\n";
			$uid_md5=($_SESSION['access_level']==1)?$this->uid_md5($uid):'';
			print "<p><b><a href='#' class=''  data-toggle='collapse' data-target='#ch_new_uid'  target=''>$uid</a></b> <span class='badge badge-warning' >$uid_md5</span></p>\n";
			print "<p class='font24' ><b><a href='#' class=''  data-toggle='collapse' data-target='#setname'  target=''>$name</a></b></p>\n";
			print $partner;
			print $client;
			if($r['telegram_id']) {
				if(!empty($r['telegram_nic']))
					print "<p><a href='https://t.me/{$r['telegram_nic']}' class='' title='{$r['telegram_id']}' target='_blank'><img src='/css/icons/tg-48.png'> ".$this->disp_tg($r['telegram_nic'])."</a></p>\n";
				else
					print "<p title='ник телеграм скрыт'><img src='/css/icons/tg-48.png'></p>\n";
			}
		//	print "<p><a href='https://vk.com/id$uid' target='_blank'>https://vk.com/id$uid</a></p>";
			$sex=array(0=>'',1=>'Ж',2=>'М');
			//print "HERE_$uid ".$this->gid;
			if($r['vk_id']>0) {
				$allowed=($vk->vk_is_messages_from_group_allowed($this->gid,$uid))?"<span class='badge badge-success' >OK</span>":"<span class='alert alert-danger' >ЗАПРЕТ</span>\n";
				print "<a href='https://vk.com/id{$r['vk_id']}' class='' target='_blank'><img src='https://for16.ru/css/icons/vk-48.png'> <img src='$photo_100' class='img-thumbnail' ></a>\n";
				print "\n";
			} else
				$allowed="";
			?>
			
			<dl class="dl-horizontal">
				<!--<dt>bdate</dt><dd><?=@$u['bdate']?></dd>
				<dt>Sex</dt><dd><?=$sex[$u['sex']]?></dd>
				<dt>City</dt><dd><?="$city"?></dd>
				<dt>Status</dt><dd><?=$u['status']?></dd>
				<dt>СООБЩЕНИЯ</dt><dd><div><?=$allowed?></div></dd>-->
				<?if(1) { //!empty($r['city'])?> 
				<dt>Город</dt>
				<dd>
					<div class='badge badge-warning font16' ><a href='#ch_city' class='text-white' style='color:white;' data-toggle='collapse'><?=!empty($r['city'])?$r['city']:"не указан";?></a></div>
					<div class='collapse' id='ch_city' >
						<form>
							<div><input class='p-2 rounded'  type='text' name='city_new' value='<?=$r['city']?>' autocomplete="off" id='city' style="border-color: lightgray;"></div>
							<div id="cityList"></div>
							<button type='submit' name='do_ch_city' value='yes' class='btn btn-primary btn-sm' >Изм</button>
						</form>
					</div>
				</dd>
				<?}?>
				<?if(!empty($r['age'])) {?>
				<dt>Возраст </dt><dd><?=($r['age'])?"<span class='badge badge-primary font16' >".$r['age']."</span>":" - "?><br></dd>
				<?}?>
				<?if(!empty($r['tzoffset'])) {?>
				<dt>Время МСК+</dt><dd><?=($r['tzoffset'])?"<span class='badge badge-success font16' >+".(-($r['tzoffset']/60+3))." часов</span>":" - "?></dd>
				<?}?>
				<?
				if(isset($u['occupation'])) {
					foreach($u['occupation'] AS $key=>$val) {
						print "<dt>$key</dt><dd>".$val."</dd>";
					}
				}
				?>
			</dl>
			<?

		$uid=intval($this->uid);
		$r=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE uid='$uid'"));
		$klid=$r['id'];
		if(empty($r['uid_md5'])) {
			$uid_md5=$this->uid_md5($r['id']);
			$this->query("UPDATE cards SET uid_md5='".$this->escape($uid_md5)."' WHERE uid='$uid'");
		} else
			$uid_md5=$r['uid_md5'];
		$email=$r['email'];
		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
			$email="";
		if(isset($_GET['set_partner'])) {
			//~ if($r['razdel']==3) {
				//~ include_once "/var/www/html/pini/formula12/scripts/pact/papa_bot.class.php";
				//~ $p=new papa_bot();
				//~ $partner=$p->user_add($klid,$email=null);
				//~ print "<div class='alert alert-success' >Переведен в статус партнера <br>".print_r($partner,true)."</div>";
				
			//~ } else {
				//~ print "<div class='alert alert-danger' >Будущий партнер должен иметь раздел A</div>";
			//~ }
		}
		print "<dl class='dl-horizontal ' style='padding:2px;'>\n";
			if($this->userdata['access_level']<=3)  {
				$res=$this->query("SELECT * FROM utm WHERE uid='$uid' ORDER BY tm DESC");
				if($this->num_rows($res)) {
					print "<dt>UTM</dt><dd>";
					while($r=$this->fetch_assoc($res)) {
						$dt=date("d.m.Y",$r['tm']);
						print "<div class='badge badge-info'><span class='badge' >$dt</span> {$r['utm_source']} {$r['utm_medium']} {$r['utm_campaign']} {$r['utm_content']} {$r['utm_term']}</div>";
					}
					print "</dd>\n";
				}
			}

			//~ if($this->userdata['access_level']<=3)  {
				//~ print "<dt>Статус</dt>";
				//~ if($login=$this->dlookup("username","users","klid='$klid'")) {
					//~ $pact_phone=$this->dlookup("pact_phone","users","klid='$klid'");
					//~ print "<dd>
						//~ <a href='https://formula12.ru/?bc=$klid' class='' target='_blank'><span class='badge badge-success' >Это партнер</span></a>
							//~ <span class='badge' >?bc=$klid</span>
							//~ <span class='badge' >$login</span>
							//~ <span class='badge' >$pact_phone</span>
						//~ <a href='https://formula12.ru/references/add.php?uid=".$this->uid_md5($uid)."' class='' target='_blank'>заполнить отзыв</a>
						//~ <a href='https://formula12.ru/references/?ref=".$uid."' class='' target='_blank'>смотреть отзыв</a>
						//~ </dd>";
				//~ } else
					//~ print "<dd><a href='?set_partner=yes&uid=$uid' class='' target=''><button class='btn btn-success' >Сделать партнером</button></a></dd>";
			//~ }
		$this->msg_info_specprice($uid);
		print "</dl>\n";

		//$res=$this->query("SELECT * FROM quiz WHERE uid='$uid'",0);
		//~ if($answ=$this->dlookup("answ","anketa_google","uid='$uid'")) {
			//~ $dt=date("d.m.Y",$this->dlookup("tm","anketa_google","uid='$uid'"));
			//~ print "<div class='card bg-light card bg-light-sm' >
				//~ <h3>Анкета ($dt)</h3>";
			//~ print nl2br(preg_replace("//s","",$answ));
			//~ print "</div>";
		//~ }

		print "<div class='' >";
		$where_visited_webinar="";
		foreach($this->sid_visited_webinar AS $sid)
			$where_visited_webinar.="source_id='$sid' AND ";
		$where_visited_webinar.='1';
		
		$res=$this->query("SELECT custom,vote,tm FROM msgs WHERE uid='$uid' AND $where_visited_webinar ORDER BY tm");
		while($r=$this->fetch_assoc($res)) {
			$sid_name=$this->dlookup("source_name","sources","id='$sid'");
			print "<div class='badge p-1 bg-light' >$sid_name : Лэндинг : <span class='badge bg-warning' >{$r['custom']}</span> : ".date("d.m.Y",$r['tm'])." : <span class='badge' >{$r['vote']}%</span>.</div>";
		}
		//~ if(!$this->num_rows($res))
			//~ print "<div class='badge badge-warning p-2' >На вебинаре не был!</div>\n";
		print "</div>\n";

		print "<div class='' id='tm_pay_section'></div>\n";
		$res=$this->query("SELECT * FROM avangard WHERE vk_uid='$uid' AND res=1 ORDER BY tm");
		while($r=$this->fetch_assoc($res)) {
			print "<div class='p-0 pl-2 card' ><div>
				Оплата ({$r['pay_system']}): ".date("d.m.Y",$r['tm'])." {$r['order_descr']}
				<span class='badge' >{$r['amount']}р.</span>";

			if($this->database=='vkt') {
				$email_for_client=($this->dlookup("id","0ctrl","uid='$uid'"))?"<a href='#avangard_email_{$r['id']}' class='btn btn-sm btn-info' target='' data-toggle='collapse' title='Отправить клиенту письмо с доступами'>Email</a>":"";
				print "до <span class='badge bg-warning_ p-1' >".date("d.m.Y",$r['tm_end'])."</span> ";
				print "<a href='#avangard_prolong_{$r['id']}' class='btn btn-sm btn-warning' target='' data-toggle='collapse'>Продлить</a>
					$email_for_client";
			}
			
			print "</div></div>\n";

			if($this->database=='vkt') {
				print "<div class='collapse' id='avangard_prolong_{$r['id']}'>".$this->ch_avangard_pay_end($r['id'])."</div>\n";
				print "<div class='collapse' id='avangard_email_{$r['id']}'><a href='?uid=$uid&send_avangard_email=yes&avangard_id={$r['id']}' class='btn btn-info' target=''>Отправить письмо клиенту об успешном платеже</a></div>\n";
			}
		}
		if(isset($_GET['send_avangard_email'])) {
			$this->send_avangard_email(intval($_GET['avangard_id']));
		}
		//~ if(!$this->num_rows($res))
			//~ print "<div class='badge badge-warning' >Покупок не было</div>\n";
		//print "</div>";


		$this->uid_info_add();
		print "</div>";
		$this->is_friend=$u['is_friend'];
		$this->can_write_private_message=$u['can_write_private_message'];
		$this->can_send_friend_request=$u['can_send_friend_request'];
		$this->blacklisted=$u['blacklisted'];
		print "</div>
		</div>
		";
		print "\n\n<!--/uid_info-->\n";
		if($del) {
			print "<div class='alert alert-danger' ><h2>КЛИЕНТ УДАЛЕН!</h2></div>\n";
			exit;
		}
		return true;
	}
	function disp_touch_result() {
		print "<div class='alert alert-info' ><div class='form-group m0 font-weight-bold' style='display:$this->for_touch_display;'>";
		print "<badge class='control-badge text-danger' for='comm'>РЕЗУЛЬТАТ КАСАНИЯ:</badge>";
		print "<select id='touch' class='form-control'   name='touch'>";
		$res_t=$this->query("SELECT * FROM sources WHERE for_touch=1");
		while($r_t=$this->fetch_assoc($res_t)) {
			print "<option value='{$r_t['id']}'>{$r_t['source_name']}</option>";
		}
		print "</select>";
		print "</div></div>";
	}
	function friend_status($uid,$fr_status) {
		global $VK_GROUP_ID;
	//	return;
		if($uid>0) {
			$vk=new vklist_api($this->token);
			if($vk->vk_is_group_member($VK_GROUP_ID,$uid)==1) {
				//$grp="в группе"; $grp_badge="badge-success";
				print "<div class='alert alert-success' >ПОДПИСАН НА ГРУППУ ВК</div>";
			} else {
				//$grp="не в группе";  $grp_badge="badge-warning";
				print "<div class='alert alert-danger' ><b>НЕ</b> ПОДПИСАН НА ГРУППУ ВК</div>";
			}
			//print "HERE_$VK_GROUP_NAME";
			print "\n\n<!--friend_status-->\n";
		}
		
		
		//~ $acc_id_name="";
		//~ if($fr_status!=-1) {
			//~ if($fr_status==3)
				//~ print "<div class='alert alert-success'><span class='badge badge-success'><div class='badge'>$this->acc_id_name_href</div> В друзьях</span> <span class='badge $grp_badge'><span class='badge'>$VK_GROUP_NAME</span> $grp</span></div>";
			//~ if($fr_status==2)
				//~ $vk->vk_friends_add($uid , "");
			//~ if($fr_status==1)
				//~ print "<div class='alert alert-warning'><span class='badge badge-warning'><div class='badge'>$this->acc_id_name_href</div> Запрос в друзья отправлен</span> <span class='badge $grp_badge'><span class='badge'>$VK_GROUP_NAME</span> $grp</span></div>";
			//~ if($fr_status==0) {
				//~ $def=($this->request_to_friends_as_default)?"checked":"";
				//~ $r=$this->fetch_assoc($this->query("SELECT COUNT(id) AS cnt FROM msgs WHERE uid=$uid AND outg=0"));
				//~ if($r['cnt']>=3) {
					//~ $def="checked";
				//~ }
				//~ print "<div class='alert alert-danger'>	
							//~ <div class='badge'>$this->acc_id_name_href</div> 
							//~ Добавить в друзья <input type='checkbox' name='fr' $def> 
								//~ <span class='badge $grp_badge'>
									//~ <span class='badge'>$VK_GROUP_NAME</span> $grp
								//~ </span>
						//~ </div>\n";
			//~ }
		//~ } else {
			//~ if($this->vkgrp_acc && $uid>0) {
				//~ print "<div class='alert alert-success' >Это переписка в сообщениях группы </div>";
			//~ }
			//~ if($uid<0) {
				//~ print "<div class='alert alert-warning' >Это переписка по емэйл</div>";
			//~ }
			//~ $err=json_decode($vk->last_response,true)['error']['error_code'];
			//~ //$this->print_r(json_decode($vk->last_response));
			//~ if($err==5) {
				//~ $ban=true; 
				//~ print "<div class='alert alert-danger'> Аккаунт <b>$this->acc_id</b> не работает! Код ошибки: $err</div>";
				//~ $this->disabled="disabled";
			//~ }
		//~ }
		print "\n\n<!--/friend_status-->\n";
	}
	function vklist_log($acc_id,$group_id,$uid,$err,$response) {
		$tm=time();
		$dt=$this->dt1($tm);
		$this->query("INSERT INTO vklist_log 
						(tm,dt,acc_id,group_id,uid,mode,err,response) 
						VALUES 
						($tm,$dt,$acc_id,$group_id,$uid,1,$err,'".$this->escape($response)."')");
	}
	function do_invite_to_friends($uid,$acc_id,$msg) {
		if($this->vklist_mode) {
			$this->query("UPDATE vklist SET tm_msg=".time()." WHERE uid=$uid");
			$this->vklist_log($acc_id,$this->vklist_mode_gid,$uid,$err=1004,$response="hand mode:invite_to_friends");
			exit;
		}
		//print "HERE"; exit;
		$vk=new vklist_api($this->token);
		if($fr_res=$vk->vk_friends_add($uid , $msg)==0) {
			$this->query("UPDATE cards SET request_to_friends_sent=1 WHERE uid=$uid");
			$this->save_comm($uid,$this->userdata['user_id'],"Отправлен запрос в друзья",$source_id=2);
			print "<div class='alert alert-success'>";
			print "<form  class='form-horizontal' name='f1' method='POST' action='?send_msg=yes&uid=$uid&acc_id=$acc_id#send_form'>";
			print "<div class='alert alert-info'>Запрос в друзья с сообщением <b><div class='card bg-light'>".$msg."</div></b> успешно отправлен.</div>";
			print "<input type='submit' class='btn btn-default' name='mark_as_read' value='Mark read & Close'>\n";
			print "<input type='submit' class='btn btn-default' name='window_close' value='Close'>";
			print "<input type='submit' class='btn btn-default' name='window_close_and_leave_unread' value='Оставить непрочитанным'>";
			print "</form>";
			print "</div>";
		} else {
			$r_acc=$this->fetch_assoc($this->query("SELECT * FROM vklist_acc WHERE id=$acc_id"));
			print "<div class='card bg-light'>
				<div class='alert alert-info'>Добавление в друзья не сработало, вероятно требуется подтверждение, что вы не робот</div>
				Если вам важно отправлять запросы в друзья с этого промо-аккаунта в будущем, то необходима ручная разблокировка.
				Для этого зайдите в этот промо-аккаунт :
				<h4>{$r_acc['name']}</h4>
				<h4>{$r_acc['login']}</h4>
				<h4>{$r_acc['passw']}</h4>
				<p>и из него отправьте запрос в друзья этому клиенту : <a href='https://vk.com/id$uid' target='_blank' >https://vk.com/id$uid</a>, 
				там контакт запросит подтверждение, что вы не робот.
				<b>Раздел поставить C1 </b>
				</p> 
				</div>";
			print "<div class='card bg-light'>";	
			print "<form  class='form-horizontal' name='f1' method='POST' action='?send_msg=yes&uid=$uid&acc_id=$acc_id#send_form'>";
			print "<input type='submit' class='btn btn-primary' name='mark_as_read' value='Mark read & Close'>\n";
			print "<input type='submit' class='btn btn-default' name='window_close' value='Close'>\n";
			print "<input type='submit' class='btn btn-default' name='window_close_and_leave_unread' value='Оставить непрочитанным'>";
			print "</form>";
			print "</div>";
		}
		print "<script>opener.location.reload();</script>";
		//exit;
	}
	function do_send_msg_filter($uid,$msg) {
		return;
		$chk_arr=array("week0","reg");
		foreach($chk_arr AS $chk) {
			if(preg_match("|$chk|s",$msg)) {
				$this->query("UPDATE cards SET razdel=4 WHERE uid='$uid'");
			}
		}
	}
	function prepare_msg_before_sending($uid,$msg) {
		return $this->prepare_msg($uid,$msg);
	}
	function prepare_attach($msg) {
		if(preg_match("|#video_([0-9]+)|", $msg,$m)) {
			return [intval($m[1])];
		}
		if(preg_match("|#audio_([0-9]+)|", $msg,$m)) {
			return [intval($m[1])];
		}
		if(preg_match("|#image_([0-9]+)|", $msg,$m)) {
			return [intval($m[1])];
		}
		//print "HERE $msg";
		return false;
	}
	function do_send_insta($uid,$msg,$source_id=0,$num=0) {
		if(empty(trim($msg))) {
			print "<div class='alert alert-danger' >Нельзя отправить пустое сообщение</div>";
			return false;
		}
		$msg=$this->prepare_msg_before_sending($uid,$msg);
		include_once("pact.class.php");
		$p=new pact($this->pact_token);
		//print "HERE";
		$cid=$this->dlookup("pact_insta_cid","cards","uid='$uid'");
		if(!$cid) {
			print "<div class='alert alert-danger' >Ошибка: диалог в инстаграм директ не установлен</div>";
			return false;
		}
		$r=$p->send_msg($cid,$msg);
		$user_id=(isset($_SESSION['userid_sess']))?$_SESSION['userid_sess']:0;
		if($r['status']=="ok") {
			$this->query("INSERT INTO msgs SET
						uid='$uid',
						acc_id=102,
						tm=".time().",
						user_id='$user_id',
						msg='".$this->escape($msg)."',
						outg=1,
						vote='$num',
						source_id='$source_id'					
						");
			print "<div class='alert alert-success' >INSTAGRAM DIRECT сообщение отправлено : @".$this->dlookup("insta","cards","uid='$uid'")."</div>";
			$this->mark_new($uid,0);
		} else {
			print "<div class='alert alert-danger' >Ошибка send_msg. cid=$cid</div>";
			print "<div class='card bg-light' >".$this->print_r($r)."</div>";
			sleep(10);
			exit;
			return ;
		}
		if(!isset($_GET['no_reload_opener']) ) {
			$cardid=$this->dlookup("id","cards","uid=$uid");
			print "<script>opener.location='cp.php?view=yes&uid=$uid#r_$cardid';</script>";
			print "<script>opener.location.reload();</script>";
		}
	}
	function do_send_tg($uid,$msg,$attach=false) {
		if(empty(trim($msg))) {
			print "<div class='alert alert-danger' >Нельзя отправить пустое сообщение</div>";
			return false;
		}
		$msg=$this->prepare_msg_before_sending($uid,$msg);
		$tg_id=$this->dlookup("telegram_id","cards","uid='$uid'");
		//print "HERE"; exit;
		include_once("tg_bot.class.php");
		$tg=new tg_bot($this->tg_bot);

		if(isset($_SESSION['last_msg_tm'])) {
			if($_SESSION['last_msg_tm']>(time()-3) ) {
				print "<div class='alert alert-success' >Cообщение отправлено. (повторное нажатие) : $mob</div>";
				sleep(2);
				return;
			}
		}

		//$r=$wa->send_msg($cid,$msg);

		$user_id=(isset($_SESSION['userid_sess']))?$_SESSION['userid_sess']:0;
		if($tg->send_msg($tg_id,$msg) ) {
			$this->query("INSERT INTO msgs SET
						uid='$uid',
						user_id='$user_id',
						acc_id=103,
						tm='".time()."',
						msg='".$this->escape($msg)."',
						outg=1
						");
			if(isset($_SESSION['userid_sess'])) {
				$_SESSION['last_msg_tm']=time();
			}
			if(isset($_SESSION['userid_sess'])) {
				print "<div class='alert alert-success' >Telegram сообщение отправлено</div>";
				flush();
				sleep(2);
			}
			//usleep(rand(100000,500000));
			$this->mark_new($uid,0);
		} else {
			print "<div class='alert alert-danger' >Ошибка send_msg.</div>"; flush();
			sleep(10);
			//exit;
			return false;
		}
		if(!isset($_GET['no_reload_opener']) && isset($_SESSION['userid_sess']) ) {
			$cardid=$this->dlookup("id","cards","uid=$uid");
			print "<script>opener.location='cp.php?view=yes&uid=$uid#r_$cardid';</script>";
			print "<script>opener.location.reload();</script>";
		}
	}
	function do_send_wa($uid,$msg,$source_id=0,$num=0, $attach=false, $force_if_not_wa_allowed=false) {
		if(empty(trim($msg))) {
			print "<div class='alert alert-danger' >Нельзя отправить пустое сообщение</div>";
			return false;
		}
		if(!$attach)
			$attach=$this->prepare_attach($msg);
		$msg=$this->prepare_msg_before_sending($uid,$msg);
		$mob=$this->dlookup("mob_search","cards","uid='$uid'");

		include_once("pact.class.php");
		$wa=new pact($this->pact_token);

		if(isset($_SESSION['last_msg_tm'])) {
			if($_SESSION['last_msg_tm']>(time()-3) ) {
				print "<div class='alert alert-success' >Watsapp сообщение отправлено. (повторное нажатие) : ".$this->disp_mob($mob)."</div>";
				sleep(2);
				return;
			}
		}

		//$r=$wa->send_msg($cid,$msg);

		$user_id=(isset($_SESSION['userid_sess']))?$_SESSION['userid_sess']:0;
		$save_outg=($this->database!="papa")?true:false;
		if(isset($this->pact_not_save_outgoing_wa))
			$save_outg=($this->pact_not_save_outgoing_wa)?false:true;
		if($attach)
			$wa->attach=$attach;
		if($wa->send($this,$uid,$msg,$user_id,$num,$source_id,$save_outg,$force_if_not_wa_allowed) ) {
			if(isset($_SESSION['userid_sess'])) {
				$_SESSION['last_msg_tm']=time();
			}
			if(isset($_SESSION['userid_sess'])) {
				print "<div class='alert alert-success' >Watsapp сообщение отправлено : ".$this->disp_mob($mob)."</div>";
				flush();
				sleep(2);
			}
			//usleep(rand(100000,500000));
			$this->mark_new($uid,0);
		} else {
			print "<div class='alert alert-danger' >Ошибка send_msg.</div>"; flush();
			sleep(10);
			//exit;
			return false;
		}
		if(!isset($_GET['no_reload_opener']) && isset($_SESSION['userid_sess']) ) {
			$cardid=$this->dlookup("id","cards","uid=$uid");
			print "<script>opener.location='cp.php?view=yes&uid=$uid#r_$cardid';</script>";
			print "<script>opener.location.reload();</script>";
		}
	}
	function do_send_email_do($email,$subj,$msg,$from,$from_name) {
		$this->email($emails=array($email), $this->email_subj, nl2br($msg), $from=$this->email_from, $from_name, $add_globals=false);
	}
	function do_send_email($uid,$msg) {
		$msg=$this->prepare_msg_before_sending($uid,$msg);
		if(empty(trim($msg))) {
			print "<div class='alert alert-danger' >Нельзя отправить пустое сообщение</div>";
			return;
		}
		$email=$this->dlookup("email","cards","uid='$uid'");
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			print "<div class='alert alert-danger' >Емэйл ошибочен : $emai</div>";
			return;
		}

		$email_from_name=$this->dlookup("email_from_name","users","id='{$_SESSION['userid_sess']}'");
		$email_from_name=(empty($email_from_name))?$this->email_from_name:$email_from_name;
		
		$this->do_send_msg_filter($uid,$msg);
		$this->query("INSERT INTO msgs SET
					uid='$uid',
					acc_id=100,
					tm=".time().",
					user_id='{$_SESSION['userid_sess']}',
					msg='".$this->escape($msg)."',
					outg=1					
					");
		$msg_out=preg_replace('/(http[s]{0,1}\:\/\/\S{4,})[\s\n]+?/ims', '<a href="$1" target="_blank">$1</a> ', $msg." ");
		//$this->email_from="info@1-info.ru";
		//$res=$this->email($emails=array($email), $this->email_subj, nl2br($msg_out), $from=$this->email_from,$fromname=$email_from_name, $add_globals=false);
		$this->do_send_email_do($email,$this->email_subj,$msg_out,$this->email_from,$email_from_name);
		print "<div class='alert alert-success' >Емэйл отправлен: $res</div>";
		$this->mark_new($uid,0);
		if(!@$_GET['no_reload_opener']) {
			$cardid=$this->dlookup("id","cards","uid=$uid");
			print "<script>opener.location='cp.php?view=yes&uid=$uid#r_$cardid';</script>";
			print "<script>opener.location.reload();</script>";
		}
	}
	function do_send_msg($uid,$acc_id,$msg,$fr_status=1) {
		$msg=$this->prepare_msg_before_sending($uid,$msg);
		$vk_id=$this->dlookup("vk_id","cards","uid='$uid'");
		if(!$vk_id && $uid>0) {
			$this->query("UPDATE cards SET vk_id='$uid' WHERE uid='$uid'");
			$vk_id=$uid;
		}
		
		//$msg=preg_replace("~#uid~","$uid",$msg);
		//print nl2br($msg); exit;
		$attachment=false;
		$msg_save_comm=$msg;
		if(preg_match("~(photo|video).*[ ]?~",$msg,$m)) {
			if(strpos($msg,"/vktrade/video")===false) {
				//print_r($m);
				$attachment=trim($m[0]);
				//$msg=preg_replace("~https:\/\/vk.com\/(photo|video).*[ ]?~","",$msg);
				$msg=preg_replace("~https:\/\/vk.com\/(photo|video)-[0-9]+_[0-9]+~i","",$msg);
				$msg=preg_replace("~(photo|video)-[0-9]+_[0-9]+~i","",$msg);
				//~ if($_SESSION['userid_sess']==1) {
					//~ print $msg; exit;
				//~ }
			}
		}
		//print_r($m);
		//$this->here($attachment,true);
		
		print "<div class='alert alert-info'>Sending VK message to $uid</div>";
		$vk=new vklist_api($this->token);
				//~ if($_SESSION['userid_sess']==1) {
					//~ print $msg; exit;
				//~ }
		$sending_result=$vk->vk_msg_send($vk_id, $msg,false,false,$attachment);
		$this->vklist_acc_log($sending_result,"123");
		//$sending_result=1;
		if($sending_result==0) {
			$this->do_send_msg_filter($uid,$msg);
			print "<div class='alert alert-success'>СООБЩЕНИЕ ОТПРАВЛЕНО УСПЕШНО</div>";
			$this->query("UPDATE cards SET fl_newmsg=0,tm_lastmsg=".time()." WHERE uid=$uid");
			$this->query("UPDATE msgs SET new=0 WHERE uid=$uid");
			$this->vklist_acc_log(0);
			
			//~ if($this->userdata['access_level']>2) {
				//~ $user_id=$this->userdata['user_id'];
				//~ $r_user_id=$this->fetch_assoc($this->query("SELECT access_level,tm_lastmsg FROM cards JOIN users ON user_id=users.id WHERE cards.uid='$uid'"));
				//~ if(isset($r_user_id) && $r_user_id['access_level']>2) { //user access_level
					//~ if( (time()-$r_user_id['tm_lastmsg']) > 3*24*60*60) 
						//~ $this->query("UPDATE cards SET user_id='$user_id' WHERE uid='$uid'");
				//~ } else
					//~ $this->query("UPDATE cards SET user_id='$user_id' WHERE uid='$uid'");
			//~ }
			
			$res=json_decode($vk->last_response,true);
			$mid=0;
			if(isset($res['response']))
				$mid=$res['response'];
			$user_id=(isset($_SESSION['userid_sess']))?intval($_SESSION['userid_sess']):0;
			$this->query("INSERT INTO msgs (uid,acc_id,mid,tm,user_id,msg,outg,imp) VALUES ($uid,$acc_id,$mid,".time().",$user_id,'".$this->escape($msg_save_comm)."',1,0)");
			if(sizeof($this->send_talk_to_email)!=0) {
				//print_r();
				include_once('/var/www/vlav/data/www/wwl/inc/phpMailer/class.phpmailer.php');
				$mail= new PHPMailer();
				$mail->IsSMTP(); // telling the class to use SMTP
				$mail->Host="localhost"; //"1-info.ru"; // SMTP server
				$mail->ContentType="text/html";
				$mail->CharSet="utf-8";
				$mail->AltBody="";
				$mail->From=$this->send_talk_to_email_from;
				$f=explode("@",$this->send_talk_to_email_from);
				$mail->FromName=(sizeof($f)==2)?$f[0]:$this->send_talk_to_email_from;   //"test"; //$this->send_talk_to_email_from;
				foreach($this->send_talk_to_email AS $email)
					$mail->AddAddress($email, "");
				$kl_name=$vk->vk_get_name_by_uid($uid);
				$subj=$this->send_talk_to_email_from." -> ".$this->userdata['username']." -> $uid $kl_name";
				$mail->Subject='=?utf-8?B?'.base64_encode($subj).'?=';
				$referer= (isset ($_SERVER["HTTP_REFERER"]))?$_SERVER["HTTP_REFERER"]:"";
				$out="";
				$res=$this->query("SELECT *, msgs.tm AS tm FROM msgs JOIN users ON users.id=msgs.user_id WHERE (outg=0 OR outg=1) AND msgs.uid=$uid ORDER BY tm DESC");
				while($r=$this->fetch_assoc($res)) {
					if($r['outg']==0)
						$out.="<b>".date("d.m.Y H:i",$r['tm'])." $kl_name </b><br>";
					else
						$out.="<b>".date("d.m.Y H:i",$r['tm'])." {$r['username']} (acc_id=$acc_id)</b><br>";
					$out.=nl2br($r['msg'])."<br>";
				}
				$mail->MsgHTML("<body>$out</body>");
				if(!$mail->Send()) {
					echo "<div class='alert alert-danger'></div>";
				}
			}
			if(sizeof($this->send_talk_to_vk)!=0) {
				$send_token=$this->dlookup("token","vklist_acc","del=0 AND last_error=0 AND id>3");
				$out="\n==========================================\n";
				$kl_name=$this->dlookup("surname","cards","uid=$uid")." ".$this->dlookup("name","cards","uid=$uid");
				$out.="\n==========================================\n";
				$out.=$this->userdata['username']." -> $uid $kl_name\n";
				$vk_send=new vklist_api($send_token);
				$res=$this->query("SELECT *, msgs.tm AS tm FROM msgs JOIN users ON users.id=msgs.user_id WHERE (outg=0 OR outg=1) AND msgs.uid=$uid ORDER BY tm DESC");
				while($r=mysql_fetch_assoc($res)) {
					if($r['outg']==0)
						$out.="".date("d.m.Y H:i",$r['tm'])." $kl_name \n";
					else
						$out.="".date("d.m.Y H:i",$r['tm'])." {$r['username']} (acc_id=$acc_id)\n";
					$out.=$r['msg']."\n";
					$out.="-----------------------------\n";
				}
				foreach($this->send_talk_to_vk AS $uid) {
					//print "$uid $send_token $out"; exit;
					print $vk_send->vk_msg_send($uid, substr($out,0,2000));
					sleep(1);
				}
			}
			//$this->print_r($_POST);exit;
			if(@$_GET['fr']) {
				$fr_res=$vk->vk_friends_add($uid , "");
				//$this->print_r($vk->last_response);exit;
			}
			if($this->vklist_mode) {
				$cid=(isset($_GET['cid']))?intval($_GET['cid']):0;
				$this->query("UPDATE vklist SET tm_msg=".time()." WHERE uid=$uid");
				$dbname=$this->database;
				$this->connect("vklist2");
				$this->query("UPDATE vklist2 SET tm=".time().",cid='$cid' WHERE uid=$uid");
				$this->connect($dbname);
				$this->vklist_log($acc_id,$this->vklist_mode_gid,$uid,$err=0,$response="hand mode:SENT OK");
			}
		} else {
			$add_url="";
			$last_response=json_decode($vk->last_response,true);
			$err=$last_response['error'];
			if($this->vklist_mode) {
				//$this->query("UPDATE vklist SET tm_msg=".time()." WHERE uid=$uid");
				$add_url="get_from_vklist=yes";
				$this->vklist_log($acc_id,$this->vklist_mode_gid,$uid,$err['error_code'],$response="hand mode:".$vk->last_response);
			}
			//~ print_r($err);
			//~ exit;
			$this->vklist_acc_log($err['error_code'],"vk_msg_send error");
			print "<div class='alert alert-danger'>Error sending : {$err['error_code']} : {$err['error_msg']}</div>";
			if($err['error_code']==901 && $this->vkgrp_acc) {
				print "<div class='alert alert-warning'>Это аккаунт группы и пользователь отписался от сообщений из группы</div>";
			}
			//$err['error_code']=7;
			if($err['error_code']==7) {
				print "<div class='alert alert-danger'> Аккаунт <b>$acc_id</b> : Превышен дневной лимит сообщений или стоит блокировка, нужно добавиться в друзья</div>";
				print "<div class='alert alert-success'> Автоматическая смена аккаунта</div>";
				$tm=time()+(30*60);
				$this->query("UPDATE vklist_acc SET tm_next_send_msg=$tm WHERE id=$acc_id");
				//$this->print_r($err); exit;
				sleep(1);
				$this->vklist_acc_log(321,"{$_SESSION['last_acc_id']} - $acc_id");
				$res_msg_acc=$this->query("SELECT acc_id FROM msgs WHERE uid=$uid AND outg<2 ORDER BY tm DESC LIMIT 1");
				if($this->num_rows($res_msg_acc)==0) { //IF NOT INCOMING OR OUTGOING MESSAGES				
					print "Reloading.. <br>";
					print "<script>location.reload();</script>";
				} else {
					print "<div class='alert alert-danger'>
						Аккаунт не поменялся, т.к. уже велась переписка.<br>
						Ошибка ВК - отправить не удалось, повторите попытку позже.<br>
						</div>";
					//sleep(10);
					//print "<script>window.close()</script>";
					exit;
				}
			}
			if($err['error_code']==14) { //Captcha
				$r=$this->fetch_assoc($this->query("SELECT * FROM vklist_acc WHERE id=$acc_id"));
				print "<div class='alert alert-success'>
				Контакт просит подтверждение что вы не робот.
				Нужно зайти <a href='https://vk.com' target='_blank'>в контакт</a> в промо-аккаунт :<br>
				<h4>{$r['name']}</h4>
				<h4>{$r['login']}</h4>
				<h4>{$r['passw']}</h4>
				И отправить сообщение вручную, указав что вы не робот
				<div class='card bg-light'>".nl2br($msg)."</div>
				
				Второй вариант, что это может быть просто глюком контакта - попробуйте позже.
				</div>";
			}
			exit;
		}
		if(!@$_GET['no_reload_opener']) {
			$cardid=$this->dlookup("id","cards","uid=$uid");
			print "<script>opener.location='cp.php?view=yes&uid=$uid#r_$cardid';</script>";
			print "<script>opener.location.reload();</script>";
		}
		//exit;
	}
	var $scdl_opt_arr=[9=>'9:00',12=>'12:00',1440=>'14:40',1720=>'17:20',20=>'20:00'];
	var $scdl_web_arr=[];
	function scdl_opts() {
		foreach($this->scdl_opt_arr AS $key=>$val) {
			print "<div class='form-check-inline'>
					  <badge class='form-check-badge'>
						<input type='radio' class='form-check-input' value='$key' id='scdl_time_$key' t='$val' name='scdl_radio'> $val
					  </badge>
					</div>
				";
		}
	}

	function scheduling() {
		$r=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE uid=".$this->uid));
		//SCDL
		if($r['tm_schedule']>=mktime(0,0,0,date("m"),date("d"),date("Y")))
			$c1="#ffffff"; else $c1="#FF91A4"; 
		if($r['tm_schedule']>0) {
			$web=(isset($this->scdl_web_arr[$r['scdl_web_id']]))?$this->scdl_web_arr[$r['scdl_web_id']]:"ОШИБКА";
			$c="background-color:green; color:$c1;";
			$wday=array("ВС","ПН","ВТ","СР","ЧТ","ПТ","СБ",);
			$dt="".date("d.m.Y",$r['tm_schedule']); 
			$hdr="В расписании на : ".date("d.m.Y H:i",$r['tm_schedule'])." ". $wday[date("w",$r['tm_schedule'])]." <span class='badge badge-warning' >$web</span>";
		} else { $dt=date("d.m.Y",time()+(24*60*60)); $c=""; $hdr="Расписание";}
		print "\n\n<!--scheduling-->\n";
		print "<div class='card p-3'>
					<div id='scdl_hdr' class='card p-1' data-toggle='collapse' data-target='#scdl_panel' style='$c'><a href='javascript:void(0);' style='$c'>$hdr</a>
				</div>";
		print "<form class='form-inline'>\n";
		print "<div class='panel-body collapse' id='scdl_panel'>
			<div class='form-group'>
			<badge for='scdl_dt'>Дата</badge>
			<input id='scdl_dt'  class='form-control' type='text' style='$c' name='dt' value='$dt' >
			</div>";
		//~ print "<div class='form-group'>
			//~ <badge for='scdl_grp'>GRP</badge>
			//~ <input  id='scdl_grp'  class='form-control' type='text' name='grp_id' value='1' style='width:50px'>
			//~ </div>";
		print $this->scdl_opts();
		print "<div class='card bg-light card bg-light-sm' >";
		$scdl_web_s=(sizeof($this->scdl_web_arr)==1)?'checked':'';
		foreach($this->scdl_web_arr AS $key=>$val) {
			print "<div class='form-check-inline'>
					  <badge class='form-check-badge'>
						<input type='radio' class='form-check-input' value='$key' id='scdl_web_$key' t_web='$val' name='scdl_web_radio' $scdl_web_s> $val
					  </badge>
					</div>
				";
		}
		print "</div>";

		if(isset($this->scdl_web_funnel[$r['scdl_web_id']])) {
			print "<input type='hidden' id='scdl_funnel' value='{$this->scdl_web_funnel[$r['scdl_web_id']]}'>";
		} else
			print "<input type='hidden' id='scdl_funnel' value='0'>";
		
		print "<input type='hidden' name='klid' value='{$r['id']}'>";
		print "<input type='hidden' name='uid' value='{$r['uid']}'>";
		print "<input type='hidden' name='acc_id' value='".$this->acc_id."'>";
		print "&nbsp;&nbsp;<input type='submit'  class='btn btn-success' name='do_scdl' value='Записать' uid='$this->uid'  id='scdl_set' onclick='return(false);'>&nbsp;&nbsp;";
		print "<input type='submit'  class='btn btn-warning' name='do_scdl_del' value='Убрать' uid='$this->uid' id='scdl_clr' onclick='return(false);'>";
		print "</form>";
		print "</div></div>";
		print "\n\n<!--/scheduling-->\n";
	}
	function print_templates($uid,$obj) {
		print "\n\n<!--print_templates-->\n";
		print "<div class=''>";
		$name=$this->dlookup("name","cards","uid='$uid'");

		$res_t=$this->query("SELECT * FROM msgs_templates WHERE del=0 ORDER BY name");
		while($r_t=$this->fetch_assoc($res_t)) {
			$t=$this->prepare_msg($uid,$r_t['msg']);
			$t=preg_replace("/[\n\r]{2,2}/","\\n",$t);
			print "<a href='javascript:ins_text(\"".$t."\",$obj);void(0);' class='btn btn-primary btn-sm' style='margin:2px;'>{$r_t['name']}</a>";
		}
		print "<a href='javascript:ins_text(\"$name\",$obj,\"\");void(0);'  class='btn btn-info btn-sm' style='margin:2px;'>ИМЯ</a>";
		$res=$this->query("SELECT * FROM sales_script_names WHERE del=0 AND fl_call_script=0 ORDER BY sales_script_name");
		while($r=$this->fetch_assoc($res)) {
			$c=$r['fl_private']?'success':'warning';
			print "<a href='javascript:wopen_1(\"sales_script_items.php?sid={$r['id']}&view=yes&uid=$uid\")'  class='btn btn-$c btn-sm' style='margin:2px;'>{$r['sales_script_name']}</a>";
		}
		$res=$this->query("SELECT * FROM sales_script_names WHERE del=0 AND fl_call_script=1 ORDER BY sales_script_name");
		while($r=$this->fetch_assoc($res)) {
			print "<span class='badge badge-danger' ><a class='white' href='javascript:wopen_1(\"sales_script_items.php?sid={$r['id']}&call_script=yes&uid=$uid\")'>{$r['sales_script_name']}</a></span> ";
		}
		print "<a title='настроить шаблоны' href='javascript:wopen(\"msgs_templates.php\")' class='ml-1 btn btn-info btn-sm' target=''><i class='fa fa-align-justify' ></i></a>";
		print "</div>";
		print "\n\n<!--/print_templates-->\n";
	}
	function callback($mob) {
		$mob=preg_replace("/^7/i","8",$mob);
		$callback_url="".$this->dlookup("callback_url","users","id='{$_SESSION['userid_sess']}'");
		if(!empty($callback_url))
			$callback_url.="?n=$mob";
		else
			return "";
		return "<a href='javascript:wopen_1(\"$callback_url\")' class='' target=''>ZADARMA</a>";
	}
	var $for_touch_display='block';
	function send_form_comments($uid) {
		if(!$this->vklist_mode) {
			$r=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE uid=$uid"));
			if(trim($r['mob'])=="")
				$mob=""; else $mob="<br>Mob.: <b>".$this->disp_mob($mob)."</b>";
			$disabled=($this->userdata['access_level']>5)?"disabled":"";
			$mob=$this->disp_mob($r['mob']);
			$email=$this->disp_email($r['email']);
			$telegram_nic_disabled=$r['telegram_id'] ? "DISABLED" : "";
		?>
			<div class='collapse_ card ml-2' id='comment'>

				<div class='form-group m0' style=''>
				<badge class='control-badge' for='comm'>Задача:</badge>
				<textarea id='comm' class='form-control'   name='comm' rows='5'><?=htmlspecialchars($r['comm'])?></textarea>
				</div>

				<div class='form-group m0'>
				<badge class='control-badge' for='comm1'>Комментарий:</badge>
				<textarea $disabled id='comm1' class='form-control'   name='comm1' rows='3'><?=htmlspecialchars($r['comm1'])?></textarea>
				</div>

 <div class="row">
    <div class="col-md-4">
      <div class="form-group m0" style="">
        <badge class="control-badge" for="tel">телефон:</badge> 
        <?=$this->callback($r['mob_search'])?> 
        <input <?=$disabled?> id="tel" class="form-control" type="text" name="tel" value="<?=htmlspecialchars($mob)?>">
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group m0" style="">
        <badge class="control-badge" for="email">email: 
          <a href="javascript:wopen_1('merge_cards.php?uid=<?=$uid?>')" class="" target=""> 
            объединить с другим
          </a>
        </badge>
        <input <?=$disabled?> id="email" class="form-control" type="text" name="email" value="<?=htmlspecialchars($email)?>">
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group m0" style="">
        <badge class="control-badge" for="telegram_nic">telegram: 
          <a href="https://t.me/<?=$r['telegram_nic']?>" class="" target="_blank"><?=$r['telegram_nic']?></a>
        </badge>
        <input <?=$telegram_nic_disabled?> style="display:block;" id="telegram_nic" class="form-control" type="text" name="telegram_nic" value="<?=htmlspecialchars($r['telegram_nic'])?>">
      </div>
    </div>
  </div>
				
				<?=$this->disp_touch_result();?>
				
				<div class='form-group m0'>
				<badge class='control-badge' for='s_comm'>&nbsp;</badge>
				<input id='s_comm' type='submit'  user_id='<?=$this->userdata['user_id']?>' class='btn btn-danger' name='do_edit_comm' value='Записать' uid='<?=$uid?>' onclick='return(false);'>
				</div>
			</div>
		<?
		} else
				$r=false;
	}
	function delay_ctrl($pass) {
		$uid=intval($this->uid);
		$tm_delay=$this->dlookup("tm_delay","cards","uid='$uid'");
		if($tm_delay>=time()) {
			$dt_delay=date("d.m.Y",$tm_delay);
			$hi_delay=date("H:i",$tm_delay);
			$t="Назначено на <span class='badge' >".date("d.m.Y H:i",$tm_delay)."</span>";
		} else {
			$dt_delay=date("d.m.Y");
			$hi_delay=date("H:i");
			$t="Время контроля не установлено";
		}
		$fl_newmsg=$this->dlookup("fl_newmsg","cards","uid='$uid'");
		if(!$fl_newmsg && $tm_delay)
			$s="default";
		elseif($fl_newmsg==1)
			$s="warning";
		elseif($fl_newmsg==2)
			$s="danger";
		elseif($fl_newmsg==3)
			$s="success";
		elseif($fl_newmsg==4)
			$s="primary";
		else $s="";

		$tm_delay_imp=$this->dlookup("tm_delay_imp","cards","uid='$uid'");
		if($tm_delay_imp==0)
			$tm_delay_imp_s="";
		elseif($tm_delay_imp==1)
			$tm_delay_imp_s="background-color:#FFA500;";
		elseif($tm_delay_imp==2)
			$tm_delay_imp_s="background-color:#DC0000;";

		?>
		<div>
<!--
			<span class='badge badge-<?=$s?>' > </span>&nbsp;
			<span class='badge badge-warning' ><?=$t?></span>
-->
<!--
			<a href='?uid=<?=$uid?>&window_close=yes' class='' target='' title='Закрыть (оставить в задачах, и сделать просмотренным у клиента в вк)'><button class='btn btn-primary mar3' >Закрыть</button></a>
			<a href='?uid=<?=$uid?>&mark_as_read=yes' class='' target='' title='Закрыть и убрать из задач'><button class='btn btn-primary mar3' >Закрыть и убрать из задач</button></a>
-->
		</div>
		<form class='form-inline' >
		<div class='' style="display: flex; flex-wrap: wrap;">
			<select name='tm_delay_imp' class='form-control'  style='<?=$tm_delay_imp_s?>' onchange='this.form.submit();'>
				<option value='0' style=''  ><span></span></option>
				<option value='0' style='background-color:white;' <?=$tm_delay_imp==0?"SELECTED":"";?> ><span></span></option>
				<option value='1' style='background-color:#FFA500;' <?=$tm_delay_imp==1?"SELECTED":"";?>  ><span></span></option>
				<option value='2' style='background-color:#DC0000;' <?=$tm_delay_imp==2?"SELECTED":"";?>  ><span></span></option>
			</select>
			<button type='submit' name='to_0min' value='yes' class='btn btn-info btn-sm m-1' >Сейчас</button>
<!--
			<a href='?uid=<?=$uid?>&to_15min=yes' class='' target=''><button class='btn btn-info mar3' >На 15 мин</button></a>
			<a href='?uid=<?=$uid?>&to_1hour=yes' class='' target=''><button class='btn btn-info mar3' >На час</button></a>
			<a href='?uid=<?=$uid?>&to_3hour=yes' class='' target=''><button class='btn btn-info mar3' >На 3 часа</button></a>
-->
			<button type='submit' name='to_tomorrow' value='yes' class='btn btn-info btn-sm m-1' >На завтра</button>
<!--
			<a href='?uid=<?=$uid?>&to_2days=yes' class='' target=''><button class='btn btn-info mar3' >На 2 дня</button></a>
-->
			<button type='submit' name='to_1week' value='yes' class='btn btn-info btn-sm m-1' >На неделю</button>
			<button type='submit' name='to_month' value='yes' class='btn btn-info btn-sm m-1' >На месяц</button>

			<input type='text' id='__dt_delay_<?=$pass?>' name='dt_delay' value='<?=$dt_delay?>' class='form-control text-center p-1'  style='display:inline_;width:140px;'>
			<input type='time' id='__hi_delay' name='hi_delay' value='<?=$hi_delay?>' class='form-control text-center p-0' data-input style='display:inline_;width:120px;'>
			<input type='hidden' name='uid' value='<?=$uid?>'>
			<button type='submit' name='dt_hi_delay_set' value='yes' class='btn btn-primary btn-sm' >Уст</button>
			<button type='submit' name='clr_delay' value='yes' class='btn btn-warning btn-sm ml-1' >Забыть</button>
		</div>
		</form>
			<script>
				$("#__dt_delay_<?=$pass?>").datepicker({
					weekStart: 1,
					daysOfWeekHighlighted: "6,0",
					autoclose: true,
					todayHighlight: true,
					format: 'dd.mm.yyyy',
					language: 'ru',
					timeFormat: "HH:mm",
					showTime: true,
					showMinute: true,
					showSecond: false,
					showMillisec: false,
					timeSeparator: ":",
				}).on('show', function() {
					  if ($(this).val() == "00.00.0000") {
						$(this).datepicker('update', '<?=date('d.m.Y')?>');  
					  }
					});
			</script>
		<?
	}
	function send_form($uid,$acc_id, $fr_status, $no_reload_opener=false) {
		if(!intval($uid))
			return false;
		$disabled=$this->disabled;
		$r=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE uid='$uid'"));
		print "<div class='card bg-light p-3' id='send_form'>";
				$this->send_form_comments($uid);
		print "\n\n<!--SEND FORM-->\n
			<form  class='form-horizontal' name='f1' method='POST' action='#send_form'>\n";
				$this->friend_status($uid,$fr_status);
				$this->print_templates($uid,"f1.msg");
				/////////////////////////////
				if(isset($_GET['vklist_msg']))
					$vklist_msg=trim($_GET['vklist_msg']); else $vklist_msg="";
				$msg=""; //isset($_GET['msg'])?$_GET['msg']:"";
				print "<textarea id='msg_textarea' name='msg' rows='5' class='form-control' $disabled>$msg</textarea>\n";
				?>
<!--
				<script type="text/javascript">
					$( "#msg_textarea" ).emojionePicker({type : "unicode", pickerTop : 2, pickerRight : 2});
				</script>
-->
				<?
				/////////////////////////////

				$cid=(isset($_GET['cid']))?intval($_GET['cid']):false;
				if($cid)	
					print "<input type='hidden'  name='cid' value='$cid'>\n";
				print "<input type='hidden'  name='uid' value='$uid'>\n";
				print "<input type='hidden'  name='acc_id' value='$acc_id'>\n";
				print ($no_reload_opener)?"<input type='hidden' name='no_reload_opener' value='yes'>":"";
				if($this->vklist_mode) {
					print "<input type='hidden'  name='get_from_vklist' value='yes'>\n";
					print "<input type='hidden'  name='gid' value='$this->vklist_mode_gid'>\n";
				}
				//print "<input type='submit'  class='btn btn-primary' name='do_send_and_close' value='ОТПРАВИТЬ И СДЕЛАНО' $disabled>\n";
			//	print "<input type='submit' class='btn btn-warning' name='mark_as_read' value='СДЕЛАНО' $disabled>\n";
			//	print "&nbsp;&nbsp;<input type='submit' class='btn btn-success' name='mark_delayed' value='ОТЛОЖИТЬ' $disabled>\n";
				print "<div class=''>";
				$vk_disabled=($uid>0 || $r['vk_id']>0 )?"":"disabled";
				print "<button type='submit' $vk_disabled class='m-1 btn btn-primary' name='do_send_msg' value='vk' $disabled> ВК </button>\n";

				$email_disabled=(filter_var($r['email'], FILTER_VALIDATE_EMAIL) && !preg_match("/vkt1_/",$this->database))?"":"disabled";
				
				$wa_disabled=($this->pact_token=="yogahelpyou" && $this->database=='vkt')?"":"disabled";
				$wa_disabled="disabled";
				$insta_disabled=($r['pact_insta_cid'])?"":"disabled";
				$tg_disabled=($r['telegram_id'])?"":"disabled";
				//print "HERE_".$r['email'];
 				print "<button type='submit' $email_disabled class='m-1 btn btn-warning' name='do_send_email' value='send_email' $disabled>Email</button>\n";
 				print "<button type='submit' $wa_disabled class='m-1 btn btn-success' name='do_send_wa' value='send_wa' $disabled>Whatsapp</button>\n";
 				print "<button type='submit' $tg_disabled class='m-1 btn btn-info' name='do_send_tg' value='send_tg' $disabled>Telegram</button>\n";
 				if($this->database=='vkt')
					print "<button type='submit' class='btn btn-danger btn-sm' name='do_send_tg_test' value='send_tg_test'>test</button>\n";
 				//print "<button type='submit' $insta_disabled class='btn btn-info' name='do_send_insta' value='send_insta' $disabled>Instagram</button>\n";
				print "</div>";
			//	print "<input type='submit' class='btn btn-default' name='window_close' value='ЗАКРЫТЬ ОКНО' $disabled>\n";
			//	print "<input type='submit' class='btn btn-default' name='window_close_and_leave_unread' value='ЗАКРЫТЬ И Оставить непрочитанным' $disabled>";
		print "</form>\n<!--/SEND FORM-->\n\n";
		print "</div>";

		print "<div class='card bg-light p-3' >";
		$this->delay_ctrl(2);
		print "</div>";
	}
	function send_form_add_to_friends($uid,$acc_id,$no_reload_opener=true) {
		//$this->send_form($uid,$acc_id,$fr_status);
		print "<div class='card bg-light'>";
		$this->send_form_comments($uid);
		//~ print "<div class='alert alert-warning'><h3>Стоит запрет отправки личных сообщений, надо добавиться в друзья.</h3>";
		//~ print "<form  class='form-horizontal' name='f1' method='GET' action='#send_form'>";
		//~ print "<div class='card bg-light card bg-light-sm'>";
		//~ $this->print_templates($uid,"f1.msg_add_to_friends");
		//~ $this->msg_add_to_friends="";
		//~ print "</div>";	
		//~ print "<div class='alert alert-info form-group'>
			//~ <badge for='msg_add_to_friends'>Сообщение запроса в друзья</badge>
			//~ <textarea class='form-control' name='msg_add_to_friends' id='msg_add_to_friends' rows='2' maxlength='499'>".$this->msg_add_to_friends."</textarea></div>";
		//~ print "<input type='hidden'  name='uid' value='$uid'>\n";
		//~ print "<input type='hidden'  name='acc_id' value='$acc_id'>\n";
		//~ print ($no_reload_opener)?"<input type='hidden' name='no_reload_opener' value='yes'>":"";
		//~ if($this->vklist_mode) {
			//~ print "<input type='hidden'  name='get_from_vklist' value='yes'>\n";
			//~ print "<input type='hidden'  name='gid' value='$this->vklist_mode_gid'>\n";
		//~ }
		//~ print "<input type='submit' class='btn btn-primary' name='do_invite_to_friends' value='$this->acc_id_name - Отправить запрос в друзья' >\n";
		//~ print "<input type='submit' class='btn btn-default' name='mark_as_read' value='Mark read & Close'>\n";
		//~ print "<input type='submit' class='btn btn-default' name='window_close' value='Close'>\n";
		//~ print "<input type='submit' class='btn btn-default' name='window_close_and_leave_unread' value='Оставить непрочитанным'>";
		
		//~ print "</form></div>";
		//~ //$this->print_razdel_ch($r);
		print "</div>";
	}
	function send_form_add_to_friends_rqst_sent($uid,$acc_id) {
		$tm_last_outg=$this->dlookup("tm","msgs","source_id=2 AND uid=$uid AND acc_id=$acc_id");
		if(!$tm_last_outg || $tm_last_outg==0)
			$days=""; else $days="".round((time()-$tm_last_outg)/(24*60*60),0)." дней назад";
		print "<form  class='form-horizontal' name='f1' method='GET' action=''>";
		print "<div class='card bg-light'>";	
		$this->send_form_comments($uid);
		print "<div class='alert alert-warning'><h3>Запрос в друзья отправлен $days, но еще не принят</h3></div>";
		if($this->vklist_mode) {
			print "<input type='hidden'  name='get_from_vklist' value='yes'>\n";
			print "<input type='hidden'  name='gid' value='$this->vklist_mode_gid'>\n";
		}
		print "<input type='hidden'  name='uid' value='$uid'>\n";
		print "<input type='submit' class='btn btn-default' name='mark_as_read' value='Mark read & Close'>\n";
		print "<input type='submit' class='btn btn-default' name='window_close' value='Close'>\n";
		print "<input type='submit' class='btn btn-default' name='window_close_and_leave_unread' value='Оставить непрочитанным'>";
		print "</div>";
		print "</form>";
	}
	function send_form_blacklisted($uid,$acc_id) {
		print "<h2><span class='alert alert-danger'>связаться невозможно, вас заблокировали (</span></h2>";
		print "<div class='card bg-light'>";	
		$this->send_form_comments($uid);
		print "<form  class='form-horizontal' name='f1' method='GET' action=''>";
		if($this->vklist_mode) {
			print "<input type='hidden'  name='get_from_vklist' value='yes'>\n";
			print "<input type='hidden'  name='gid' value='$this->vklist_mode_gid'>\n";
		}
		print "<input type='hidden'  name='uid' value='$uid'>\n";
		print "<input type='submit' class='btn btn-default' name='mark_as_read' value='Mark read & Close'>\n";
		print "<input type='submit' class='btn btn-default' name='window_close' value='Close'>\n";
		print "<input type='submit' class='btn btn-default' name='window_close' value='Close'>\n";
		print "</form>";
		print "</div>";
	}
	function send_form_blocked($uid,$acc_id,$ban) {
		if(!$ban) {
			$this->send_form_comments($uid);
			print "<div class='alert alert-danger'>Невозможно написать сообщение. <br>
				Стоит полный запрет на все - и вх сообщения и добавление в друзья, поэтому связаться невозможно. <br>
				Попробуйте другой аккаунт, если это необходимо.
				</div>";
			$this->save_comm1($uid,"Стоит полный запрет на все - и вх сообщения и добавление в друзья, поэтому связаться невозможно");
			print "<div class='card bg-light'>";	
			print "<form  class='form-horizontal' name='f1' method='GET' action=''>";
			if($this->vklist_mode) {
				print "<input type='hidden'  name='get_from_vklist' value='yes'>\n";
				print "<input type='hidden'  name='gid' value='$this->vklist_mode_gid'>\n";
			}
			print "<input type='hidden'  name='uid' value='$uid'>\n";
			print "<input type='submit' class='btn btn-default' name='mark_as_read' value='Mark read & Close'>\n";
			print "<input type='submit' class='btn btn-default' name='window_close' value='Close'>\n";
			print "<input type='submit' class='btn btn-default' name='window_close' value='Close'>\n";
			print "</form>";
			print "</div>";
		} else {
			print "<div class='alert alert-danger'>аккаунт , с которого велась переписка не работает. Сообщения показаны из базы. Переключитесь на другой аккаунт.</div>";
			$_SESSION['msgs_list_mode']=1;
		}
	}
	function vklist_acc_log($err,$msg="0") {
		if($this->database != "vkt_dancehall")
			return false;
		//$this->query("DELETE FROM vklist_acc_log WHERE 1");
		
		$tm=time();
		$dt=date("d.m.Y H:i:s");
		$user_id=$this->userdata['user_id'];
		$this->query("INSERT INTO  vklist_acc_log SET 
				tm='$tm',
				msg='".$this->escape($msg)."',
				dt='$dt',
				uid='$this->uid',
				acc_id='$this->acc_id',
				user_id='$user_id',
				err='$err'");
		return 0;
	}
	function print_chk($r) {
		$uid=$r['uid'];
		$checked=($r['fl']==1)?"checked":"";
		if($_SESSION['userid_sess']<5)
			print "<span id='chk_cp_badge' class='badge' style='background-color:#dff0d8;'><input type='checkbox' id='chk_cp' uid='$uid' $checked></span>";
	}
	function print_razdel_ch($r) {
		$add_where=($this->userdata['access_level']>=4)?"AND id=7":"";
		$add_where="";
		$res_razd=$this->query("SELECT * FROM razdel WHERE del=0 AND id>0 $add_where AND razdel_name NOT LIKE '-%' ORDER BY razdel_num,razdel_name");
		while($r_razd=$this->fetch_assoc($res_razd)) {
			$checked=($r['razdel']==$r_razd['id'])?"checked":"";
			$s=$this->get_style_by_razdel($r_razd['id']);
			print "<span class='badge m-1' style='$s' title='отметить / снять отметку'>{$r_razd['razdel_name']} <input type='radio' name='chk' value='{$r_razd['id']}' $checked></span>";
		}
	}
	function print_tags() {
		?>
			<div class="card bg-light card bg-light-sm tag-list">

				<div class="tag-container">
					<div id="tag-list"></div>
					<button id="assign-tag-btn" style="width: 30px; height: 30px; border: none; margin-left: 5px; background-color: whitesmoke;">
						<i class='fa fa-plus text-primary' ></i>
					</button>
					<?if($_SESSION['access_level']<4) {?>
					<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tagCreationModal" style="height: 30px; padding: 0 12px; line-height: 30px;">
						Тэги
					</button>
					<?}?>
				</div>
				<input type="text" id="tagFilter" style="border: none; outline: none; display: none"/>
				<!-- <button type="button" id="assign-tag-btn" style="border: none; background-color: whitesmoke;">+</button> -->
				<div id="tagDropdown" style="display: none;">
				
				</div>
				
			</div>
		<?
		$this->print_tags_modals();
	}
	function print_tags_modals() {
		?>
			<div class="modal fade" id="tagCreationModal" tabindex="-1" role="dialog" aria-labelledby="tagCreationModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
				  <div class="modal-content">
					<div class="modal-header">
					  <h5 class="modal-title text-center" id="tagCreationModalLabel">Управление тегами</h5>
					  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					  </button>
					</div>
					<div class="modal-body container-fluid">
					  <!-- Section with 'Добавить тэг' button -->
					  <div class="d-flex justify-content-end mb-3">
						<button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#tagCreationForm" aria-expanded="false" aria-controls="tagCreationForm">Добавить тэг</button>
					  </div>
					  <!-- Collapsible Tag Creation Form -->
					  <div class="collapse" id="tagCreationForm">
						<div class="container mt-3">
							<form id="tag-form" class="d-flex flex-column align-items-center text-center w-100">
								<div class="form-group">
									<label for="tag-name" class="mr-1">Название</label>
									<input type="text" class="form-control form-control-sm" id="tag-name" name="tag_name" required="">
								</div>
								
								<div class="form-group position-relative">
									
									<div id="color-dropdown" style="width: 200px; margin: 0 auto;">
										<!-- ... color boxes ... -->
									</div>
									
									<div class="d-flex align-items-center justify-content-center mt-2">
										<div id="selected-color" class="ml-2 rounded" style="width: 100px; height: 45px; background-color: #000000;"></div>
									</div>
									
									<input type="hidden" id="tag-color" name="tag_color" value="#000000" required="">
								</div>
							
								<div class="modal-footer d-flex justify-content-center w-100">
									<button type="submit" class="btn btn-primary">Сохранить</button>
									<button type="button" class="btn btn-secondary" id="tagCreationCancel">Отмена</button>
								</div>
							</form>
						</div>
					  </div>
					  <table class="table table-striped" id="existing-tags">
						<thead>
							<tr>
								<th>№</th>
								<th>Тэг</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					</div>
				  </div>
				</div>
			</div>

			  <!-- Edit Tag Modal -->
			<div class="modal fade" id="editTagModal" tabindex="-1" role="dialog" aria-labelledby="editTagModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
					<h5 class="modal-title" id="editTagModalLabel">Изменить тэг</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					</div>
					<div class="modal-body">
						<form id="edit-tag-form" class="d-flex flex-column align-items-center text-center w-100">
							<div class="form-group">
								<label for="tag-name" class="mr-1">Название</label>
								</div>
									<input type="text" class="form-control form-control-sm" id="new-tag-name" name="tag_name" required="">
									
							</div>
							
							<div class="form-group position-relative">
								<div id="edit-color-dropdown" style="width: 200px; margin: 0 auto;">
									<!-- ... color boxes ... -->
								</div>
								
								<div class="d-flex align-items-center justify-content-center mt-2">
									<div id="edit-selected-color" class="ml-2 rounded" style="width: 100px; height: 45px; background-color: #000000;"></div>
								</div>
								
								<input type="hidden" id="new-tag-color" name="tag_color" required="">
							</div>
						
							<div class="modal-footer d-flex justify-content-center w-100">
								<button type="submit" class="btn btn-primary">Сохранить</button>
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		<?
	}
	function print_lands() {
		$res=$this->query("SELECT * FROM msgs WHERE uid=$this->uid AND source_id BETWEEN 1000 AND 1999 ORDER BY tm");
		?>
			<div class="card bg-light card bg-light-sm tag-list">
				<div>
				<?
				while($r=$this->fetch_assoc($res)) {
					$dt=date("d.m.Y H:i",$r['tm']);
					$r1=$this->fetch_assoc($this->query("SELECT * FROM lands WHERE land_num=".($r['source_id']-1000)));
					?>
					<span class='rounded badge bg-info text-white p-1' >
						<span class='badge db-light' ><?=$dt?></span>
						<?=$r1['land_name']?>
						<a href='<?=$r1['land_url']?>' class='text-white' target='_blank' title='посмотреть лэндинг'><span class="fa fa-arrow-circle-right"></span></a>
					</span>
					<?
				}
				?>
				</div>
			</div>
		<?
	}
	function print_tm_delay($uid,$acc_id,$r) {
		if($r['tm_delay']>0) { 
			$s1="background-color:#D9EDF7; color:#555;";
			$dt_delay=date("d.m.Y",$r['tm_delay']); 
		} else {
			$s1="background-color:#D9EDF7;  color:#555;";
			$dt_delay=""; //date("d.m.Y");
		}
		print "
		<span class='badge badge-warning text-right p-2'>
		<input type='hidden' name='uid' value='$uid'>
		<input type='hidden' name='acc_id' value='$acc_id'>
		<input type='hidden' name='read' value='yes'>
			Отложить на дату: 
		<input  class='' style='width:80px;$s1' type='text' id='delay_dt' name='tm_delay' value='$dt_delay'>
		<input type='submit' uid='$uid' id='delay_set' name='do_tm_delay' value='Уст' class='btn btn-info btn-xs' onclick='return(false);'>
		<!--<input type='submit' uid='$uid' id='delay_clr' name='do_tm_delay_clr' value='Сбр' class='btn btn-info btn-xs' onclick='return(false);'>-->
		</span>
		</form>\n
		";
		print "</div>";
	}
	function add_filter() {
	}
	function top_info() {}
	function run() {
		global $msg_pay_info;
		$this->test_microtime(__LINE__);

		//print "HERE_"; exit;

		//$this->vklist_acc_log(0,"run {$_GET['acc_id']}");

		if(!isset($_SESSION['uid']))
			$_SESSION['uid']=0;
		if(isset($_GET['uid']))
			$_SESSION['uid']=intval($_GET['uid']); else $_GET['uid']=intval($_SESSION['uid']);

		$uid=intval($_GET['uid']);
		if(!$uid) {
			print "<div class='alert alert-danger' >Error uid=0</div>";
			exit;
		}

		if(isset($_GET['goto_cp'])) {
			$this->lock_off($uid);
			//header("Location: cp.php?view=yes&filter={$_SESSION['filter_sess']}");
			print "<script>location='cp.php?view=yes&filter={$_SESSION['filter_sess']}'</script>";
		}
		
		print "<div><a href='?goto_cp=yes' class='btn btn-primary btn-lg mr-3 px-2' target=''>CRM</a>
						<!--<span style='margin-left:10px; color:#555;' > инструкция по работе <a href='https://youtu.be/BI6LaR-nlgs' class='' target='_blank'>на youtube</a></span>-->
						<span style='margin-left:10px; color:#555;' ><a href='https://help.winwinland.ru/docs/lichnyy-kabinet/kartochka-klienta' class='' target='_blank'><i class='fa fa-question-circle' ></i></a></span>
						&nbsp;&nbsp; $msg_pay_info
			</div>";
		if(isset($_GET['get_from_vklist'])) {
			if(!isset($_GET['gid']))
				$_GET['gid']=0;
			if(isset($_GET['mark_as_read'])) {
				$uid=intval($_GET['uid']);
				$gid=intval($_GET['gid']);
				$this->query("UPDATE vklist SET tm_msg=".time().",res_msg=102 WHERE uid=$uid",0);
				$cid=(isset($_GET['cid']))?intval($_GET['cid']):0;
				$dbname=$this->database;
				$this->connect("vklist2");
				$this->query("UPDATE vklist2 SET del=1,tm='".time()."',cid='$cid' WHERE uid=$uid",0);
				$this->connect($dbname);
				$this->vklist_log(0,$gid,$uid,1002,$response="hand mode:mark_read&close");
				print "<script>window.close();</script>";
				exit;
			}
			$this->vklist_mode=true;
			$gid=intval($_GET['gid']);
			$this->vklist_mode_gid=$gid;
			if(!isset($_GET['uid']) && $gid>0) {
				$_GET['uid']=$this->dlookup("uid","vklist","group_id='$gid' AND tm_msg=0 AND blocked=0");
				$_GET['vklist_msg']=$this->dlookup("msg","vklist_groups","id='$gid'");
				if(!$_GET['uid']) {
					print "<div class='alert alert-warning' >Список ($gid) исчерпан</div>";
					exit;
				}
				$this->lock_chk(intval($_GET['uid']));
				//if(!$this->lock_chk(intval($_GET['uid'])))
					//return false;
				if($this->dlookup("uid","cards","uid='".intval($_GET['uid'])."'")) {
					print "<div class='alert alert-warning' >Клиент (id={$_GET['uid']}) уже есть в базе!</div>";
					unset($_GET['get_from_vklist']);
					unset($_POST['get_from_vklist']);
					$this->query("UPDATE vklist SET tm_msg=".time()." WHERE uid='{$_GET['uid']}'");
					$this->vklist_log(0,$_GET['gid'],$_GET['uid'],$err=1003,$response="hand mode:already in cards"); 
				}
			}
		}
		$this->test_microtime(__LINE__);

		
		

		
		//$uid=198746774;

		$this->lock_chk($uid);
		//if(!$this->lock_chk($uid))
			//return false;
		if($this->userdata['access_level']>5)
			return false;
			
		$access_level=$this->userdata['access_level'];
		//~ if($access_level>4) {
			//~ if($this->dlookup("user_id","cards","uid='$uid'") != $_SESSION['userid_sess']) {
				//~ print "<div class='alert alert-danger' >Ошибка. Этот клиент ($uid) не назначен для ({$_SESSION['userid_sess']}).</div>";
				//~ exit;
			//~ }
		//~ }

		include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
		$p=new partnerka(false,$this->database);
		if(!$p->is_access_allowed($_SESSION['userid_sess'],$uid)) {
			print "<div class='alert alert-danger' >Ошибка. Этот клиент ($uid) не назначен для ({$_SESSION['userid_sess']}).</div>";
			exit;
		}
		
		$this->test_microtime(__LINE__);

		$this->lock_on($_GET['uid']);

		if(!$this->vklist_mode) {
			$r=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE uid='$uid'",0));
			if(!$r) {
				print "<p class='red'>Ссылка недействительна: $uid</p>";
				exit;
			}
			if(!isset($_GET['acc_id'])) {
				if($this->dlookup("fl_allow_read_from_all","vklist_acc","id={$r['acc_id']}")==1)
					$_GET['acc_id']=$r['acc_id'];
			}
			if(!$r['tm_first_time_opened'])
				$this->query("UPDATE cards SET tm_first_time_opened='".time()."' WHERE uid='$uid'");
		}

		$this->uid=$uid;
		$this->test_microtime(__LINE__);
		
		////CHOICE ACC_ID
		if(!isset($_GET['acc_id'])) {
			$res_msg_acc=$this->query("SELECT acc_id FROM msgs WHERE uid=$uid AND outg<2 ORDER BY tm DESC LIMIT 1");
			if($this->num_rows($res_msg_acc)==0) { //IF NOT INCOMING OR OUTGOING MESSAGES
				$r1=$this->fetch_row($this->query("SELECT id 
													FROM vklist_acc 
													WHERE del=0 
														AND last_error=0 
														AND fl_acc_not_allowed_for_new=0 
														AND tm_next_send_msg<".time()." 
														AND token!=''  
														AND ban_cnt<4 
														AND fl_allow_read_from_all=0
														ORDER BY num,ban_cnt,id LIMIT 1"));
				$_SESSION['chk_recycling']=0;
				if(!$r1) {
					print "<h2><div class='alert alert-danger'>Не осталось свободных аккаунтов для новой переписки. Нужно подождать некоторое время.</div></h2>";
					$this->vklist_acc_log(1000,"1000");
					exit;
				} else {
					$acc_id=$r1[0];
					$this->acc_id=$acc_id;
					$this->vklist_acc_log(0,"1");
				}
			} else {
				$r_msg_acc=$this->fetch_row($res_msg_acc);
				$acc_id=$r_msg_acc[0];
				$this->acc_id=$acc_id;
				$this->vklist_acc_log(0,"2");
			}
		} else {
			$acc_id=$_GET['acc_id'];
			$this->acc_id=$acc_id;
			$this->vklist_acc_log(0,"3");
		}
		if($acc_id==0 || !isset($acc_id)) {
			$acc_id=1;
			$this->acc_id=$acc_id;
			$this->vklist_acc_log(0,"4");
		}
		$this->test_microtime(__LINE__);

	//$acc_id=2;

	$acc_id=$this->dlookup("id","vklist_acc","del=0 AND fl_allow_read_from_all=1");

		if(!$this->vklist_mode) {
			if($acc_id!=$r['acc_id'])
				$this->query("UPDATE cards SET acc_id=$acc_id WHERE uid=$uid");
		}
		$this->test_microtime(__LINE__);
		
		$this->acc_id=$acc_id;
		$r_acc=$this->fetch_assoc($this->query("SELECT * FROM vklist_acc WHERE id=$acc_id"));
		if($r_acc['del']==1) {
			$this->disabled=true;
			$this->acc_id=$acc_id;
			$r_acc=$this->fetch_assoc($this->query("SELECT * FROM vklist_acc WHERE id=$acc_id"));
		}
		$this->test_microtime(__LINE__);
		$this->acc_id_name_href="<a href='https://vk.com/id{$r_acc['vk_uid']}' class='white' target='_blank'>{$r_acc['name']}</a>";
		$this->acc_id_name=$r_acc['name'];
		$this->token=$r_acc['token'];
		$this->vkgrp_acc=$r_acc['fl_allow_read_from_all'];
		$this->test_microtime(__LINE__);

		if($uid>0) {
			$vk=new vklist_api($this->token);
		//	$vk->vk_msg_mark_read($uid);
		}
		if(isset($_GET['tm_delay_imp'])) {
			$tm_delay_imp=intval($_GET['tm_delay_imp']);
			$this->query("UPDATE cards SET tm_delay_imp='$tm_delay_imp' WHERE uid='$uid'");
		}
		if(isset($_GET['dt_hi_delay_set'])) {
			if($tm=$this->date2tm($_GET['dt_delay'])) {
				if($hi=$this->time2tm($_GET['hi_delay'])) {
					$tm+=$hi;
					//print date("d/m/Y H:i",$tm); exit;
					$this->query("UPDATE cards SET tm_delay='$tm' WHERE uid='$uid'");
					$this->mark_new($uid,$fl=0);
					//$this->lock_off($uid);
					print "<div class='alert alert-warning' >Перенесен на <span class='bg-primary text-white p-2' >".date("d.m.Y H:i",$tm)."</span></div>";
				}
			}
		}
		if(isset($_GET['to_0min'])) {
			$this->mark_new($uid,$fl=4);
			//$this->lock_off($uid);
			print "<div class='alert alert-warning' >Оставлен в задачах</div>";
			//~ print "<a href='javascript:window.close()' class='' target=''><button class='btn btn-primary' >Закрыть</button></a>";
			//~ print "<script>opener.location.reload();</script>";
			//~ exit;
		}
		if(isset($_GET['to_15min'])) {
			$tm=(time()+(15*60));
			$this->query("UPDATE cards SET tm_delay='$tm' WHERE uid='$uid'");
			$this->mark_new($uid,$fl=0);
			//$this->lock_off($uid);
			print "<div class='alert alert-warning' >Перенесен на 15 мин (".date("H:i",$tm).")</div>";
			//~ print "<a href='javascript:window.close()' class='' target=''><button class='btn btn-primary' >Закрыть</button></a>";
			//~ print "<script>opener.location.reload();</script>";
			//~ exit;
		}
		if(isset($_GET['to_1hour'])) {
			$tm=(time()+(60*60));
			$this->query("UPDATE cards SET tm_delay='$tm' WHERE uid='$uid'");
			$this->mark_new($uid,$fl=0);
			//$this->lock_off($uid);
			print "<div class='alert alert-warning' >Перенесен на 1 час (".date("H:i",$tm).")</div>";
			//~ print "<a href='javascript:window.close()' class='' target=''><button class='btn btn-primary' >Закрыть</button></a>";
			//~ print "<script>opener.location.reload();</script>";
			//~ exit;
		}
		if(isset($_GET['to_3hour'])) {
			$tm=(time()+(3*60*60));
			$this->query("UPDATE cards SET tm_delay='$tm' WHERE uid='$uid'");
			$this->mark_new($uid,$fl=0);
			//$this->lock_off($uid);
			print "<div class='alert alert-warning' >Перенесен на 3 часа (".date("H:i",$tm).")</div>";
			//~ print "<a href='javascript:window.close()' class='' target=''><button class='btn btn-primary' >Закрыть</button></a>";
			//~ print "<script>opener.location.reload();</script>";
			//~ exit;
		}
		if(isset($_GET['to_tomorrow'])) {
			$tm=$this->dt1(time()+(1*24*60*60)+(10*60*60));
			$this->query("UPDATE cards SET tm_delay='$tm' WHERE uid='$uid'");
			$this->mark_new($uid,$fl=0);
			//$this->lock_off($uid);
			print "<div class='alert alert-warning' >Перенесен на завтра (".date("d.m.Y",$tm).")</div>";
			//~ print "<a href='javascript:window.close()' class='' target=''><button class='btn btn-primary' >Закрыть</button></a>";
			//~ print "<script>opener.location.reload();</script>";
			//~ exit;
		}
		if(isset($_GET['to_2days'])) {
			$tm=$this->dt1(time()+(2*24*60*60)+(10*60*60));
			$this->query("UPDATE cards SET tm_delay='$tm' WHERE uid='$uid'");
			$this->mark_new($uid,$fl=0);
			//$this->lock_off($uid);
			print "<div class='alert alert-warning' >Перенесен на 2 дня (".date("d.m.Y",$tm).")</div>";
			//~ print "<a href='javascript:window.close()' class='' target=''><button class='btn btn-primary' >Закрыть</button></a>";
			//~ print "<script>opener.location.reload();</script>";
			//~ exit;
		}
		if(isset($_GET['to_1week'])) {
			$tm=$this->dt1(time()+(7*24*60*60)+(10*60*60));
			$this->query("UPDATE cards SET tm_delay='$tm' WHERE uid='$uid'");
			$this->mark_new($uid,$fl=0);
			//$this->lock_off($uid);
			print "<div class='alert alert-warning' >Перенесен на неделю (".date("d.m.Y",$tm).")</div>";
			//~ print "<a href='javascript:window.close()' class='' target=''><button class='btn btn-primary' >Закрыть</button></a>";
			//~ print "<script>opener.location.reload();</script>";
			//~ exit;
		}
		if(isset($_GET['to_month'])) {
			$tm=$this->dt1(time()+(30*24*60*60)+(10*60*60));
			$this->query("UPDATE cards SET tm_delay='$tm' WHERE uid='$uid'");
			$this->mark_new($uid,$fl=0);
			//$this->lock_off($uid);
			print "<div class='alert alert-warning' >Перенесен на 30 дней (".date("d.m.Y",$tm).")</div>";
			//~ print "<a href='javascript:window.close()' class='' target=''><button class='btn btn-primary' >Закрыть</button></a>";
			//~ print "<script>opener.location.reload();</script>";
			//~ exit;
		}
		if(isset($_GET['clr_delay'])) {
			$tm=$this->dt1(time()+(30*24*60*60));
			$this->query("UPDATE cards SET tm_delay='0' WHERE uid='$uid'");
			$this->mark_new($uid,$fl=0);
			//$this->lock_off($uid);
			print "<div class='alert alert-warning' >Отслеживание прекращено! (увидим автоматически, если проявит активность)</div>";
			//~ print "<a href='javascript:window.close()' class='' target=''><button class='btn btn-primary' >Закрыть</button></a>";
			//~ print "<script>opener.location.reload();</script>";
			//~ exit;
		}
		if(isset($_POST['window_close']) || isset($_GET['window_close']) ) {
			if($uid>0) {
				$vk=new vklist_api($this->token);
				$vk->vk_msg_mark_read($uid);
			}
			$this->lock_off($uid);
			print "<script>window.close();</script>";
			exit;
		}
		if(isset($_POST['window_close_and_leave_unread']) || isset($_GET['window_close_and_leave_unread'])) {
			$this->lock_off($uid);
			print "<script>window.close();</script>";
			exit;
		}
		if(isset($_POST['mark_as_read']) || isset($_GET['mark_as_read']) ) {
			$vk=new vklist_api($this->token);
			$vk->vk_msg_mark_read($uid);
			$this->query("UPDATE cards SET fl_newmsg=0,tm_lastmsg=".time()." WHERE uid=$uid");
			//$this->query("UPDATE msgs SET new=0 WHERE uid=$uid");
			$this->lock_off($uid);
			print "<script>
				opener.location.reload();
				window.close();
				</script>";
			exit;
		}
		if(isset($_POST['mark_delayed'])) {
			$this->query("UPDATE cards SET fl_newmsg=3,tm_lastmsg=".time()." WHERE uid=$uid");
			$this->query("UPDATE msgs SET new=0 WHERE uid=$uid");
			$this->lock_off($uid);
			print "<script>opener.location.reload();window.close();</script>";
			exit;
		}
		$_POST['msg']=substr($_POST['msg'],0,4096);
		if(isset($_POST['do_send_msg'])) {
			$this->do_send_msg($uid,$acc_id,$_POST['msg'] );
			print "<script>location='msg.php?uid=$uid';</script>";
		}
		if(isset($_POST['do_send_email'])) {
			$this->do_send_email($uid,$_POST['msg'] );
			print "<script>location='msg.php?uid=$uid';</script>";
		}
		if(isset($_POST['do_send_wa'])) {
			$this->do_send_wa($uid,$_POST['msg'],0,0,false, $force_if_not_wa_allowed=true);
			print "<script>location='msg.php?uid=$uid';</script>";
		}
		if(isset($_POST['do_send_tg'])) {
			$this->do_send_tg($uid,$_POST['msg']);
			print "<script>location='msg.php?uid=$uid';</script>";
		}
		if(isset($_POST['do_send_tg_test'])) {
			$klid=$this->get_klid($_SESSION['userid_sess']);
			$my_uid=$this->dlookup("uid","cards","id='$klid'");
			$this->do_send_tg($my_uid,$this->prepare_msg($uid,$_POST['msg']));
			$msg=$_POST['msg'];
			print "<script>location='msg.php?uid=$uid';</script>";
			//print "HERE"; exit;
		}
		if(isset($_POST['do_send_insta'])) {
			$this->do_send_insta($uid,$_POST['msg'] );
			print "<script>location='msg.php?uid=$uid';</script>";
		}
		if(isset($_POST['do_send_and_close'])) {
			$this->do_send_msg($uid,$acc_id,$_POST['msg'] );
			$this->lock_off($uid);
			print "<script>window.close();</script>";
			exit;
		}
		if(isset($_POST['do_invite_to_friends'])) {
			$this->do_invite_to_friends($uid,$acc_id,$_POST['msg_add_to_friends'] );
			if(isset($_POST['send_and_close'])) {
				$this->lock_off($uid);
				print "<script>window.close();</script>";
				exit;
			}
		}
		if(@$_GET['do_tm_delay']) {
			$tm_delay=date2tm($_GET['tm_delay']);
			print "<h3>Set tm_delay=$tm_delay</h3>";
			$r=$this->fetch_assoc($this->query("SELECT comm FROM cards WHERE uid=$uid"));
			$comm="&lt;delayed to : ".date("d/m H:i",$tm_delay)."&gt; ".$r['comm'];
			//$this->query("UPDATE cards SET tm_delay=$tm_delay,fl_newmsg=0,comm='".mysql_real_escape_string($comm)."' WHERE uid=$uid") or die(mysql_error());
			$this->query("UPDATE cards SET tm_delay=$tm_delay,fl_newmsg=0 WHERE uid=$uid");
			$this->query("UPDATE msgs SET new=0 WHERE uid=$uid");
			//$this->query("INSERT INTO actions_log (uid,user_id,tm,action_id,acc_id,comm) VALUES ($uid,{$_SESSION['userid_sess']},".time().",5,$acc_id,'".date("d/m H:i",$tm_delay)."')") or die(mysql_error());
			print "<script>opener.location.reload();</script>";
		}
		if(@$_GET['do_tm_delay_clr']) {
			$this->query("UPDATE cards SET tm_delay=0 WHERE uid=$uid");
			//$this->query("INSERT INTO actions_log (uid,user_id,tm,action_id,acc_id,comm) VALUES ($uid,{$_SESSION['userid_sess']},".time().",4,$acc_id,'')") or die(mysql_error());
		}
		//~ if(@$_GET['do_scdl']) {
			//~ if($tm=date2tm($_GET['dt'])) {
				//~ $this->query("UPDATE cards SET tm_schedule=$tm,grp_id=1 WHERE uid=$uid");
				//~ //$this->query("INSERT INTO actions_log (uid,user_id,tm,action_id,acc_id,comm) VALUES ($uid,{$_SESSION['userid_sess']},".time().",12,$acc_id,'{$_GET['dt']}')") or die(mysql_error());
				//~ print "<h1>В расписании</h1>";
				//~ print "<script>opener.location.reload();</script>";
			//~ }
		//~ }
		//~ if(@$_GET['do_scdl_del']) {
			//~ if($tm=date2tm($_GET['dt'])) {
				//~ $this->query("UPDATE cards SET tm_schedule=0 WHERE uid=$uid");
				//~ //$this->query("INSERT INTO actions_log (uid,user_id,tm,action_id,acc_id,comm) VALUES ($uid,{$_SESSION['userid_sess']},".time().",13,$acc_id,'')") or die(mysql_error());
				//~ print "<h1>Расписание очищено</h1>";
				//~ print "<script>opener.location.reload();</script>";
			//~ }
		//~ }
		if(@$_GET['ch_acc_orig']) {
		}

		$this->delay_ctrl(1);
		print "\n<!--tbl_agent-->\n";
		$this->tbl_agent($this->dlookup("user_id","cards","uid='$uid'"));
		print "\n<!--/tbl_agent-->\n";

		$this->tbl_manager();

		$ban=(!$this->uid_info($uid))?true:false;
		$this->test_microtime(__LINE__);
		
		$this->scheduling();
		$this->test_microtime(__LINE__);
		//$this->disabled="";
		$chk_incoming_msgs=$this->dlookup("id","msgs","outg=0 AND uid=$uid AND acc_id=$acc_id");
		$this->test_microtime(__LINE__);
		$vk=new vklist_api($this->token);
		$this->test_microtime(__LINE__);
		$fr_status=(!$this->vkgrp_acc)?$vk->vk_get_friend_status($uid):-1;
		$this->test_microtime(__LINE__);
		//$this->here($fr_status);
		//~ if($fr_status==-1)
			//~ $this->print_r($vk->last_response);
		$no_reload_opener=(@$_GET['no_reload_opener'] || @$_POST['no_reload_opener'])?true:false;
		$this->test_microtime(__LINE__);
		if($this->can_write_private_message==1 || $fr_status==3 || $chk_incoming_msgs || $this->vkgrp_acc) {
		$this->test_microtime(__LINE__);
			//////////////////////////// SEND FORM
			$this->send_form($uid,$acc_id,$fr_status,$no_reload_opener);
			///////////////////
		} elseif($this->can_send_friend_request==1) {
			if($fr_status==0) {
				$this->send_form_add_to_friends($uid,$acc_id);
			} else {
					$this->send_form_add_to_friends_rqst_sent($uid,$acc_id);
			}
		} else {
			if($this->blacklisted==1) {
				$this->send_form_blacklisted($uid,$acc_id);
			} else {
				$this->send_form_blocked($uid,$acc_id,$ban);
			}
		}
		
		$this->test_microtime(__LINE__);
		if(!$this->vklist_mode) {
			
			print "<div class='card bg-light card bg-light-sm p-3'>";
			print "<form method='GET' name='f_tm_delay' action='#send_form'>";
			$this->print_chk($r);
			$this->print_razdel_ch($r);
			print "</form>\n";
			print "</div>";
			$this->print_tags();
			$this->print_lands();
		}
		
		////
		
		$r1=$this->fetch_assoc($this->query("SELECT * FROM vklist JOIN vklist_groups ON vklist_groups.id=group_id WHERE uid=$uid"));
		if(isset($_GET['msgs_list_mode'])) 
			$_SESSION['msgs_list_mode']=$_GET['msgs_list_mode'];
		if(!isset($_SESSION['msgs_list_mode']))
			$_SESSION['msgs_list_mode']=0;
		$this->test_microtime(__LINE__);
			
		$r=$this->fetch_assoc($this->query("SELECT acc_id,name FROM msgs JOIN vklist_acc ON vklist_acc.id=acc_id WHERE msgs.uid=$uid AND outg<2 ORDER BY msgs.tm DESC LIMIT 1",0));
		$last_acc_id=$r['acc_id'];
		
		$accs_all="<ul class='nav nav-pills accs_all' id='accs_all'>";
		$res=$this->query("SELECT acc_id,name,COUNT(acc_id) AS cnt FROM msgs JOIN vklist_acc ON vklist_acc.id=acc_id WHERE msgs.uid=$uid AND outg<2 GROUP BY acc_id",0);
		$this->test_microtime(__LINE__);
		while($r=$this->fetch_assoc($res)) {
			$last=($r['acc_id']==$last_acc_id)?"*":"";
			if ($r['acc_id']==$acc_id) {
				$vkgrp_acc=($this->vkgrp_acc)?"<span class='badge badge-success' >Это аккаунт группы</span>":"";
				$accs_all.= "<li class='bg-info active'>
								<a href='#'>
									<span class='badge'>{$r['acc_id']}</span> 
									{$r['name']} 
									<span class='badge'>{$r['cnt']} сообщ. $last</span>
									$vkgrp_acc
								</a>
							</li>";
			} else
				$accs_all.= "<li class='bg-info'><a style='' href='?acc_id={$r['acc_id']}&uid=$uid#list_mode'>({$r['acc_id']}) {$r['name']} <span class='badge'>{$r['cnt']} $last</span></a></li>";
		}
		$accs_all.="</ul>";
		$this->test_microtime(__LINE__);
		
		$acc_change="";
		if($this->allow_change_acc) {
			$acc_change.= "<span class='text-left'><div class='badge badge-danger'>CHANGE ORIG ACC";
			$acc_change.= "	<select id='ch_acc_id' style='color:#666;'>";
			$res=$this->query("SELECT * FROM vklist_acc WHERE del=0");
			while($r=$this->fetch_assoc($res)) {
				$selected=($r['id']==$acc_id)?"selected":"";
					
				$acc_change.= "<option value='{$r['id']}' $selected>{$r['id']} {$r['name']}</optin>";
			}
			$acc_change.= "	</select>
			<button onclick='location=\"?ch_acc_orig=yes&acc_id=$acc_id&acc_id_name=$this->acc_id_name&uid=$this->uid&acc_id=\"+ch_acc_id.value' style='color:black;'>Go</button>
			</div></span>";
		}
		$this->test_microtime(__LINE__);
		if($uid) {
			if($_SESSION['msgs_list_mode']==0 && !$ban) {
		$this->test_microtime(__LINE__);
				$add_filter=$this->add_filter();
		$this->test_microtime(__LINE__);
				if($access_level==0) {
					print "<div  id='list_mode' class='badge badge-info' style='border-width:3px;'>
							$add_filter
							<span class='badge'>list_mode={$_SESSION['msgs_list_mode']}</span> 
							<a style='color:white;' href='?msgs_list_mode=1&uid=$uid&acc_id=$acc_id#list_mode'>switch to 1</a>
							&nbsp;$acc_change
						</div>
						<div class='card bg-light card bg-light-sm'>$accs_all</div>";
				}
				if($uid) {
					$vk=new vklist_api($this->token);
					//print $this->dlookup("name","vklist_acc","token='$this->token'");
					usleep(300000);
		$this->test_microtime(__LINE__);
					$msgs_count=($uid==198746774)?50:50;
					$vk_id=$this->dlookup("vk_id","cards","uid='$uid'");
					if($uid>0 && !$vk_id) {
						$vk_id=$uid;
						$this->query("UPDATE cards SET vk_id='$uid' WHERE uid='$uid'");
					}
					if($vk_id>0) { //$uid==198746774
						if($this->vk_save_msgs($uid,$user_id=0,$acc_id=2,$msgs_count=5))
							if($this->vk_save_msgs($uid,$user_id=0,$acc_id=2,$msgs_count=50))
								$this->vk_save_msgs($uid,$user_id=0,$acc_id=2,$msgs_count=200);
						$res=array('response'=>1);
					} else {
						$res=$vk->vk_messages_get_by_user($acc_id,$uid,$msgs_count=50);
					}
				} else {
					$res=array('response'=>1);
				}
			//$this->print_r($res);
				$images=array();
				//print_r($res);
				if(isset($res['response'])) {
		$this->test_microtime(__LINE__);
					$emails=$this->prepare_correspondence_from_db("SELECT *,msgs.id AS id, msgs.uid AS uid,msgs.acc_id AS acc_id FROM msgs JOIN users ON users.id=user_id WHERE msgs.uid=$uid AND (msgs.acc_id=2 OR msgs.acc_id=100 OR msgs.acc_id=101 OR msgs.acc_id=102 OR msgs.acc_id=103)");
		$this->test_microtime(__LINE__);
					//print_r($emails);
					$msgs_events=$this->prepare_correspondence_from_db("SELECT *,msgs.id AS id,msgs.uid AS uid,msgs.acc_id AS acc_id FROM msgs JOIN users ON users.id=user_id WHERE msgs.uid=$uid AND outg>1");
		$this->test_microtime(__LINE__);
					//$this->print_r($msgs_events);
					//$msgs_events=array_merge($msgs_events, $emails);
					foreach($emails AS $tm=>$arr)
						$msgs_events[$tm]=$arr;
					if($res['response']['count']>0) {
						$fl=true;
						$msgs=array();
						$prev_date=0;
						foreach($res['response']['items'] AS $r) {
			//$this->print_r($r);
							if($r['date']==0)
								continue;
							$r['body']=$r['text'];
								
//							if($_SESSION['username']=='vlav') { $this->print_r($r); }

							if($prev_date==intval($r['date']) )
								$r['date']=intval($r['date'])-1;
							$prev_date=intval($r['date']);

							$r['mid']=$r['id'];
							$r['uid']=$r['user_id'];
							if($this->save_msgs) {
								if($this->num_rows($this->query("SELECT mid FROM msgs WHERE mid={$r['mid']} AND acc_id='$acc_id'"))==0) {
									//	$this->here($r['mid'],false);
									$this->query("INSERT INTO msgs SET uid=$uid,acc_id=$acc_id,mid={$r['mid']},tm={$r['date']},user_id=0,msg='".$this->escape($r['body'])."',outg={$r['out']}");
								}
							}
							if($r['out']==0) {
								$read=1;
							} else {
								$read=$r['read_state'];
								/*
								$this->token=$r_acc['token'];
								If($fl) {
									$vk=new vklist_api($this->token);
									if($vk->vk_messages_is_read($r['mid'])==1)
										$read=1; else $read=0;
									$fl=false;
								}
								*/
							}
							if(isset($r['attachment'])) {
								if(isset($r['attachment']['sticker'])) {
									if(isset($r['attachment']['sticker']['photo_64'])) { 
										$r['body'].="<img src='{$r['attachment']['sticker']['photo_64']}'>";
									} else
										$r['body'].="STICKER - can not get image";
								}
							}
							if(isset($r['attachments'])) {
								$r['body'].=$this->disp_attachments($r);
							}
							if(isset($r['fwd_messages'])) {
								foreach($r['fwd_messages'] AS $item) {
									if(isset($item['attachments'])) {
										$r['body'].=$this->disp_attachments($item);
									}
								}
							}
							//~ if($this->userdata['username']=='vlav') {
								//~ $this->print_r($r);
							//~ }
							$r1=$this->fetch_assoc($this->query("SELECT username FROM users JOIN msgs ON user_id=users.id WHERE mid={$r['mid']} AND msgs.acc_id=$this->acc_id ORDER BY msgs.id DESC LIMIT 1",0));
							$user=($r1)?"{$r1['username']}":"";
							//$this->print_r($r);
							if($_SESSION['username']=='vlav') {
				//				$this->print_r($r);
							}
							$msgs[$r['date']]=array('uid'=>$r['uid'],'txt'=>$r['body'],'outg'=>$r['out'],'read'=>$read,'acc_id'=>$acc_id,'user_id'=>$user, 'mid'=>$r['mid']);
						}
				//			if($_SESSION['username']=='vlav') { $this->print_r($msgs); }
						if($this->save_images) {
							if(sizeof($images)>0) {
								$imgs="";
								foreach($images AS $img)
									$imgs.=$img."|";
								$this->query("UPDATE cards SET images='".$this->escape($imgs)."' WHERE uid=$uid");
							}
						}
						//~ foreach($msgs_events AS $tm=>$msg_e) {
							//~ while(isset($msgs[$tm])) {
								//~ if($tm++>=time())
									//~ break;
							//~ }
							//~ $msgs[$tm]=$msg_e;
						//~ }
					} else {
						//~ if($uid>0)
							//~ print "<div class='alert alert-danger' >ВК Нет переписки</div>"; 
					}
					foreach($msgs_events AS $tm=>$msg_e) {
						while(isset($msgs[$tm])) {
							if($tm++>=time())
								break;
						}
						$msgs[$tm]=$msg_e;
					}
		$this->test_microtime(__LINE__);
					$this->print_correspondence($msgs);
		$this->test_microtime(__LINE__);
				} else {
					$err=$res['error']['error_code'];
					if($err==5)
						print "<div class='alert alert-danger'> Аккаунт <b>$acc_id</b> Не работает! Код: $err</div>";
					//print $vk->last_response;
				}
			} else {
				if($access_level<5) {
					print "<div id='list_mode' class='badge  badge-info'>
							<span class='badge'>list_mode={$_SESSION['msgs_list_mode']}</span> 
							<a style='color:white' href='?msgs_list_mode=0&uid=$uid&acc_id=$acc_id#list_mode'>switch to 0</a>
							&nbsp;$acc_change
						</div>
						<div class='card bg-light card bg-light-sm'>$accs_all</div>";
				}
				//if(!isset($err))
				$err=$this->error_code;
				if($ban) {
					print "<div class='alert alert-danger'> Аккаунт <b>$acc_id</b> недоступен! Код: $err</div>";
					if($err==5 ||$err==7 ) {
						print "<div class='alert alert-info'>Токен недействителен. Необходимо <a href='vklist_acc.php?auth=yes' class='' target=''>авторизовать</a> аккаунт!</div>";
					}
					//print $vk->last_response;
				}
		$this->test_microtime(__LINE__);
				$msgs=$this->prepare_correspondence_from_db("SELECT *,msgs.id AS id, msgs.uid AS uid,msgs.acc_id AS acc_id FROM msgs JOIN users ON users.id=user_id WHERE msgs.uid=$uid AND msgs.acc_id=$acc_id");
		$this->test_microtime(__LINE__);

				$this->print_correspondence($msgs);
		$this->test_microtime(__LINE__);
			}
		}
		$this->jquery($uid,$acc_id);
		$this->test_microtime(__LINE__);
		$this->print_r($this->runtime_log);
	}
	function vk_save_msgs($uid,$user_id=0,$acc_id=2,$msgs_count=5) {
		//~ if($uid!=198746774)
			//~ return false;
		include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
		$vk=new vklist_api($this->token);
		//$vk->token=$vk->tokens['yogahelpyou'];
	//print "HERE_"; exit;
		$vk_id=$this->dlookup("vk_id","cards","uid='$uid'");
		$res=$vk->vk_messages_get_by_user($acc_id,$vk_id,$msgs_count);
		//$user_id=0;
		$fl_new=false;
		foreach($res['response']['items'] AS $r) {
			//$this->print_r($r);
			//$msgs[$r['date']]=array('uid'=>$r['uid'],'txt'=>$r['body'],'outg'=>$r['out'],'read'=>$read,'acc_id'=>$acc_id,'user_id'=>$user, 'mid'=>$r['mid']);
			$mid=$r['id'];
			if($this->dlookup("id","msgs","uid='$uid' AND mid='$mid'")) {
				$fl_new=false;
				continue;
			}
			$fl_new=true;
			$outg=$r['out'];
			$msg=$r['text'];
			$tm=$r['date'];
			$link='';
			if(isset($r['attachments'])) {
				//print_r($r['attachments']);
				foreach($r['attachments'] AS $attachment) {
					$link=$attachment['type'];
					if(isset($attachment['video'])) {
						$link="https://vk.com/video".$attachment['video']['owner_id']."_".$attachment['video']['id'];
						//$this->query("INSERT INTO msgs_attachments SET msgs_id='$msgs_id',url='".$this->escape($link)."'");
					} elseif(isset($attachment['photo'])) {
						$link=$attachment['photo']['sizes'][2]['url'];
						//$this->query("INSERT INTO msgs_attachments SET msgs_id='$msgs_id',url='".$this->escape($link)."'");
					} elseif(isset($attachment['wall'])) {
						$link="https://vk.com/wall".$attachment['wall']['from_id']."_".$attachment['wall']['id'];
						//$this->query("INSERT INTO msgs_attachments SET msgs_id='$msgs_id',url='".$this->escape($link)."'");
					} elseif(isset($attachment['sticker'])) {
						$link=$attachment['sticker']['images'][0]['url'].'.png';
						//$this->query("INSERT INTO msgs_attachments SET msgs_id='$msgs_id',url='".$this->escape($link)."'");
					} elseif(isset($attachment['doc']['preview']['audio_msg']['link_mp3'])) {
						$link=$attachment['doc']['preview']['audio_msg']['link_mp3'];
						//$this->query("INSERT INTO msgs_attachments SET msgs_id='$msgs_id',url='".$this->escape($link)."'");
					} elseif(isset($attachment['audio_message'])) {
						$link=$attachment['audio_message']['link_mp3'];
						//$this->query("INSERT INTO msgs_attachments SET msgs_id='$msgs_id',url='".$this->escape($link)."'");
						//print "HERE_$msgs_id $link"; exit;
					} elseif(isset($attachment['doc']['url'])) {
						$link=$attachment['doc']['url'];
						//$this->query("INSERT INTO msgs_attachments SET msgs_id='$msgs_id',url='".$this->escape($link)."'");
					} elseif(isset($attachment['link']['url'])) {
						$link=$attachment['link']['url'];
						//$this->query("INSERT INTO msgs_attachments SET msgs_id='$msgs_id',url='".$this->escape($link)."'");
					} else {
						//$this->query("INSERT INTO msgs_attachments SET msgs_id='$msgs_id',url='".$this->escape($link)."'");
					}
				}
			}
			if(!$this->dlookup("id","msgs","uid='$uid' AND acc_id=2 AND tm='$tm'")) {
				$this->query("INSERT INTO msgs SET
					mid='$mid',
					msg='".$this->escape($msg." ".$link)."',
					outg='$outg',
					tm='$tm',
					uid=$uid,
					acc_id='$acc_id',
					user_id='$user_id',
					vote='vk_save_msgs'
					");
				$msgs_id=$this->insert_id();
				if(!empty($link))
					$this->query("INSERT INTO msgs_attachments SET msgs_id='$msgs_id',url='".$this->escape($link)."'");
				//print "$tm $mid $outg <a href='$link' class='' target='_blank'>$link</a> $msg <br>";
			}
		}
	$vk->vk_msg_mark_read($vk_id);
	//print " vk_save_msgs $msgs_count fl_new=$fl_new";
		return $fl_new;
	}
	function disp_attachments($r) {
		$body="";
		foreach($r['attachments'] AS $attachment) {
			if(isset($attachment['video'])) {
				$body.="<div class='card bg-light' >";
				//$body.=print_r($attachment,true);
				$body.="<div class='badge'>VIDEO</div>";
				$body.="<h4>".$attachment['video']['title']."</h4>";
				$body.="<div class='card bg-light'>".$attachment['video']['description']."</div>";
				$body.="<img src='{$attachment['video']['photo_320']}' class='img-responsive' >";
				$link="https://vk.com/video".$attachment['video']['owner_id']."_".$attachment['video']['id'];
				$body.="<p><a href='$link' class='' target='_blank'>$link</a></p>";
				/*
				$url = 'https://api.vk.com/method/video.get';
				$params=array('access_token'=>$this->token,'owner_id'=>$r['attachment']['video']['owner_id'],"videos"=>$r['attachment']['video']['owner_id']."_".$r['attachment']['video']['vid']);
				$res_v=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params))))),true);
				print_r($res_v);
				print $r['attachment']['video']['owner_id']."_".$r['attachment']['video']['vid'];
				$video=$res_v['response'][1]['player'];
				print "<div class='card bg-light'>video</div>";
				*/
				$body.="</div>";
			} elseif(isset($attachment['photo'])) {
				$body.="<div class='card bg-light' >";
	//$this->print_r($attachment);
				$body.= "<img src={$attachment['photo']['sizes'][2]['url']} class='img-responsive' >";
				//$link="https://vk.com/photo".$attachment['photo']['owner_id']."_".$attachment['photo']['id'];
				//$body.="<p><a href='$link' class='' target='_blank'>$link</a></p>";
				$body.="</div>";
				$images[]=$attachment['photo']['photo_604'];
			} elseif(isset($attachment['wall'])) {
				$body.="<div class='card bg-light' >";
				//print_r($attachment);
				//$body.= "<img src={$attachment['photo']['photo_604']} class='img-responsive' >";
				$link="https://vk.com/wall".$attachment['wall']['from_id']."_".$attachment['wall']['id'];
				$body.="<p><a href='$link' class='' target='_blank'>$link</a></p>";
				$body.="</div>";
				$images[]=$attachment['photo']['photo_604'];
			} elseif(isset($attachment['sticker'])) {
				if(isset($attachment['sticker']['photo_64'])) { 
					$body.="<img src='{$attachment['sticker']['photo_64']}'>";
				} else
					$body.="STICKER - can not get image";
			} elseif(isset($attachment['doc']['preview']['audio_msg']['link_mp3'])) {
				$body.="<div class='card bg-light' ><a href='{$attachment['doc']['preview']['audio_msg']['link_mp3']}' class='' target='_blank'>Голосовое сообщение</a></div>";
			} elseif(isset($attachment['doc']['url'])) {
				$body.="<div class='card bg-light' ><a href='{$attachment['doc']['url']}' class='' target='_blank'>Вложение</a></div>";
			} elseif(isset($attachment['link']['url'])) {
				$body.="<div class='card bg-light' ><a href='{$attachment['link']['url']}' class='' target='_blank'>Ссылка</a></div>";
			} else {
				$body.="attachment.type=".$attachment['type'];
				//$this->here(print_r($attachment,true));
				//$this->print_r($attachment['doc']['preview']['audio_msg']['link_mp3'] );
				//$attachment['sticker']['photo_64']
			}
		}
		return $body;
	}
	function prepare_correspondence_from_db($query) {
		$msgs=array();
		$res=$this->query($query,0);
		//print $query."<br>";
		?>
		<script>
			function ins_text1(text,obj) {
				obj.focus();
				obj.value=""+obj.value.substr(0,obj.selectionStart)+text+""+obj.value.substr(obj.selectionStart,obj.value.length-obj.selectionStart)+cr;
				obj.focus();
				pos=obj.value.length; //obj.selectionStart;
				obj.setSelectionRange(pos,pos);
			}
		</script>
		<?
		$last_tm=0;
		while($r=$this->fetch_assoc($res)) {
			//~ if(trim($r['msg'])=="")
				//~ continue;
			if($r['tm']==$last_tm)
				$r['tm']++;
			$last_tm=$r['tm'];
			if(strpos($r['msg'],"call_id")!==false) {
				$audio="<span class='fa fa-phone-square font28'></span>";
				if(!isset($this->domain))
					$this->domain="yogahelpyou.com";
				$audio.=" <audio controls><source src='https://".$this->domain."/zadarma/calls/\\1.mp3' type='audio/mpeg'></audio>";
				$r['msg']=preg_replace("|call_id=([0-9]+\.[0-9]+)|","$audio",$r['msg']);
				$msgs[$r['tm']]=array('id'=>$r['id'],'uid'=>0,'txt'=>($r['msg']),'outg'=>$r['outg'],'read'=>1,'acc_id'=>$r['acc_id'],'user_id'=>htmlspecialchars($r['username']),'source_id'=>$r['source_id']);
			} else {
				//print "HERE111 {$r['id']} {$r['uid']} <br>";
				$msgs[$r['tm']]=array('id'=>$r['id'],'uid'=>0,'txt'=>(htmlspecialchars($r['msg'])),'outg'=>$r['outg'],'read'=>1,'acc_id'=>$r['acc_id'],'user_id'=>htmlspecialchars($r['username']),'source_id'=>$r['source_id']);
			}
			if($r['acc_id']==100) { //email
				//$r['msg']=str_replace("blockquote","blockquote class='font12'",$r['msg']);
				$t="";
				//~ $t.= "<button data-toggle='collapse' data-target='#e_{$r['id']}'>...</button>";
				//~ $t.= "<div id='e_{$r['id']}' class='collapse'>".strip_tags($r['msg'])."</div>";
				$arr=preg_split("/[\n\r]+/",$r['msg']);
				$str="\\n\\n";
				for($i=1; $i<sizeof($arr); $i++) {
					$str.=">".strip_tags(preg_replace("/[\"\']+/","",$arr[$i]))."\\n";
				} 
				$email_quote="<a href='javascript:ins_text(\"$str\",f1.msg);void(0);' class='' target=''>ответить</a>
								<a title='".strip_tags(preg_replace("/[\"\']+/","",$r['msg']))."' >прочитать</a>";
				$r['msg']=substr(strip_tags($r['msg']),0,600)." $t";

				$tm_rec=$r['tm'];
				while(isset($msgs[$tm_rec]))
					$tm_rec++;
					
				$msgs[$tm_rec]=array('id'=>$r['id'],'uid'=>0,'mid'=>$r['id'],'txt'=>(($r['msg'])),'outg'=>$r['outg'],'read'=>1,'acc_id'=>$r['acc_id'],'user_id'=>htmlspecialchars($r['username']),'email_quote'=>$email_quote,'source_id'=>$r['source_id']);
				//print_r($r);
			}
		}
		return $msgs;
	}
	function is_fromsenler($txt) {
		return false;
		//~ preg_match_all("/img|💡|👉|✅/i",$txt,$res);
		//~ if(sizeof($res[0])>1)
			//~ return true;
		if(preg_match("|отписаться|is",$txt))
			return true;
		return false;
	}
	function print_correspondence($msgs) {
	//	$this->print_r($msgs);
		
		if(!is_array($msgs))
			return false;
		if(!empty($this->photo_50))
			$photo_50=$this->photo_50; else $photo_50="/css/user_icon.png";
		
		$style_uid="background-color:#FCF8E3";
		$style_acc_id="background-color:#D9EDF7";
		$style_acc_id_julia="background-color:#D9EDF7";
		$uid=$this->uid;
		$res=$this->query("SELECT *,msgs.id AS id, msgs.uid AS uid,msgs.acc_id AS acc_id FROM msgs JOIN users ON users.id=user_id WHERE msgs.uid=$uid AND imp=1");
		while($r=$this->fetch_assoc($res)) {
			$read=isset($r['read'])?$r['read']:1;
			$msgs[$r['tm']]=array('id'=>$r['id'],'txt'=>htmlspecialchars($r['msg']),'outg'=>$r['outg'],'read'=>$read,'acc_id'=>$r['acc_id'],'user_id'=>htmlspecialchars($r['username']));
		}
		
		krsort($msgs);
		$vk=new vklist_api;
		$fl=true;
		$out="<html><head><meta charset='utf-8'></head><body>";
		$name=$vk->vk_get_name_by_uid($this->uid);
		print "<hr class='bg-info' >";
		//print "<table class='table'>";
		foreach($msgs AS $tm=>$r) {
			if(date("d.m.Y",$tm)==date("d.m.Y", time()))
				$dt="сегодня<br>".date("H:i",$tm)."";
			elseif(date("d.m.Y",$tm)==date("d.m.Y", time()-(24*60*60)))
				$dt="вчера<br>".date("H:i",$tm)."";
			else
				$dt="".date("d/m",$tm)."<br>".date("H:i",$tm)."";
			if($_SESSION['username']=='vlav') {
				//$dt.= " ({$r['mid']})";
			}
//print $r['txt'];
			$r['txt']=$this->make_link_clickable($r['txt']);
			if($r['acc_id']!=101 && $r['acc_id']!=102 && $r['acc_id']!=103)
				$txt=nl2br($r['txt']);
			else
				$txt=$r['txt'];
	//print_r($r);
			//~ if($this->is_fromsenler($r['txt']) || $r['source_id']==3) {
				//~ $t= "<button data-toggle='collapse' data-target='#m_{$r['mid']}'><span class='glyphicon glyphicon-menu-hamburger'></span></button>";
				//~ $t.= "<div id='m_{$r['mid']}' class='collapse'>".preg_replace("/[\"\']+/","",$txt)."</div>";
				//~ //$t=preg_replace("/[\"\']+/","",$txt);
				//~ $txt=$t;
			//~ }
			if($r['id']>0) {
				$res_a=$this->query("SELECT * FROM msgs_attachments WHERE msgs_id={$r['id']}",0);
				while($r_a=$this->fetch_assoc($res_a)) {
					$fext='';
					if(isset(pathinfo($r_a['url'])['extension'])) {
						$fext=strtolower(pathinfo($r_a['url'])['extension']);
						if(preg_match("/^([a-zA-Z0-9]+)\?/",$fext,$m))
							$fext=$m[1];
					}
					//print "fext=$fext";
					if($fext=='jpeg' || $fext=='png' || $fext=='gif' || $fext=='jpg'  )
						$txt.="<div>{$r['id']}<img src='{$r_a['url']}' class='img-responsive' ></div>\n";
					if($fext=='oga' || $fext=='ogg' || preg_match("|audio|",pathinfo($r_a['url'])['dirname']) )
						$txt.="<div>
								<audio controls>
									<source src='{$r_a['url']}' type='audio/ogg; codecs=vorbis'>
								</audio>
								<br>
								<div><span class='card bg-light card bg-light-sm' ><a href='{$r_a['url']}' class='' target='_blank'>ССЫЛКА</a></span></div>
							</div>\n";
					if($fext=='mp4')
						$txt.="<br><video  controls style='padding:20px;width:360px;'><source src='{$r_a['url']}'></video>
							<br>
							<a href='{$r_a['url']}' class='' target='_blank'>ССЫЛКА</a>
							";
				}
			}
			if($r['outg']==0) {
				$c="$style_uid"; $f=""; $read_state="";
				if($r['acc_id']==100) {
					$txt="<span class='	fa fa-envelope'></span> ".$r['email_quote']."<br>".$txt;
				}	
				if($r['acc_id']==101) {
					$read_state="<img src='/css/icons/whatsapp-48.png'>"; 
				}
				if($r['acc_id']==102) {
					$read_state="<img src='/css/icons/instagram-48.png'>"; 
				}
				if($r['acc_id']==103) {
					$read_state="<img src='/css/icons/tg-48.png'>";
					$txt=nl2br($txt);
				}
				if($r['acc_id']<100) {
					$read_state="<img src='/css/icons/vk-48.png'>"; 
				}
				if($uid==126344045) { //test
					//$this->print_r($r);
				}
				print "<div class='card'   style='$c'>
							<div class='d-flex'>
								<div class='d-flex flex-column bg-light p-3' >
									<div class='badge badge-light p-2 ' >$read_state</div>
									<div class='p-2' >
										<img src='$photo_50' class='rounded-circle' style='width:50px'>
									</div>
									<div class='badge badge-info p-2 bg-info' >$dt</div>
								</div>
								<div class='pl-3' >$txt</div>
							</div>
						</div>";
				$out.="<hr>";
				$out.=($name)?"<b><a href='https://vk.com/id$this->uid' class='' target='_blank'>$name</a></b><br>":"";
				$out.="<i>$dt</i><br>";
				//print_r($r);
				$out.="$txt<br>";
			} elseif($r['outg']==1) {
				$c="$style_acc_id";
				$f="<div class='' style='background-color:#ff9966;'><h4>".$this->acc_id."</h4></div>";
				if(isset($r['user_id'])) {
					$f="{$r['user_id']}";
				}
				//~ if($r['acc_id']==1)
					//~ $c=$style_acc_id_julia;
				if($fl && $acc_id<100) {
					if($r['read']==1)
						$read_state="<div class='badge badge-success'>read</div>";
					else
						$read_state="<div class=''><div class='badge badge-danger'>not read</div>";
					$fl=false;
				} else $read_state="";
				if($r['read']==1)
					$read_state="<div class='badge badge-success'>прочитано</div>";
				else
					$read_state="<div class='badge badge-danger'>не прочитано</div>";
		$read_state="<div class=''><img src='/css/icons/vk-48.png'></div>";
				if($r['acc_id']==100) {
					$txt="<span class='red font18' ><span class='fa fa-envelope'></span></span> ".$txt;
					$read_state="<div class='badge badge-info'>емэйл</div>"; 
				}
				if($r['acc_id']==101) {
					$read_state="<div class=''><img src='/css/icons/whatsapp-48.png'></div>"; 
				}
				if($r['acc_id']==102) {
					$read_state="<div class=''><img src='/css/icons/instagram-48.png'></div>"; 
				}
				if($r['acc_id']==103) {
					$read_state="<div class=''><img src='/css/icons/tg-48.png'></div>"; 
					$txt=nl2br($txt);
				}
				print "<div class='card'   style='$c'>
							<div class='d-flex'>
								<div class='d-flex flex-column bg-light p-3' >
								<div class='badge badge-light p-2 ' >$read_state</div>
								<div class='badge badge-warning p-2 bg-warning' >$f</div>
								<div class='badge badge-info p-2 bg-info' >$dt</div>
							</div>
							<div class='pl-3' >$txt</div>
							</div>
						</div>";
				$out.="<hr>";
				$out.="<i>$dt</i><br>";
				$out.="$txt<br>";
			} elseif($r['outg']==2) {
				$arr_excl_sid=[2,106,107,101];
				if(!in_array($r['source_id'],$arr_excl_sid)) {
					print "<tr class='bg-light' '><td>";
					if(isset($r['user_id'])) {
						$f="{$r['user_id']}";
					}
					print "<div class='card' style='background-color:#EEEEEE;'   >
								<div class='d-flex'>
									<div class='d-flex flex-column bg-light p-3' >
										<div class='badge badge-warning p-2 bg-warning' >$f</div>
										<div class='badge badge-info p-2 bg-info' >$dt</div>
										<div class='badge badge-secondary p-2 bg-secondary' >{$r['source_id']}</div>
									</div>
									<div class='pl-3' >$txt</div>
								</div>
							</div>";
				}
			}
			//print "<tr style='height:3px;'><td colspan='2' style='height:3px;'></td></tr>";
		}
	//	print "</table>";
		if($this->userdata['access_level']<=3) {
			$out.="</body></html>";
			$fname=$this->uid.".html";
			file_put_contents("/var/www/vlav/data/www/wwl/tmp/".$fname,$out);
			print "<div><a href='https://for16.ru/tmp/$fname' class='' target=''>скачать переписку</a></div>";
		}
	}
	function print_correspondence_($msgs) {
		$style_uid=$this->style_uid;
		$style_acc_id=$this->style_acc_id;
		$style_acc_id_julia=$this->style_acc_id_julia;
		$uid=$this->uid;
		$res=$this->query("SELECT *,msgs.uid AS uid,msgs.acc_id AS acc_id FROM msgs JOIN users ON users.id=user_id WHERE msgs.uid=$uid AND imp=1");
		while($r=$this->fetch_assoc($res)) {
			$msgs[$r['tm']]=array('txt'=>$r['msg'],'outg'=>$r['outg'],'read'=>1,'acc_id'=>$r['acc_id'],'user_id'=>$r['username']);
		}
		krsort($msgs);
		
		$fl=true;
		foreach($msgs AS $tm=>$r) {
			if(date("d.m.Y",$tm)==date("d.m.Y", time()))
				$dt="ctujyz ".date("H:i",$tm);
			elseif(date("d.m.Y",$tm)==date("d.m.Y", time()-(24*60*60)))
				$dt="yesterday ".date("H:i",$tm);
			else
				$dt=date("d/m H:i",$tm);
			$txt=$r['txt'];
			if($r['outg']==0) {
				$c="$style_uid"; $f=""; $read_state="";
			} else {
				$c="$style_acc_id"; $f="I:<br>";
				if(isset($r['user_id']))
					$f=$r['user_id']."<br>";
				if($r['acc_id']==1)
					$c=$style_acc_id_julia;
				If($fl) {
					if($r['read']==1)
						$read_state="(read)";  else $read_state="(not_read)";
					$fl=false;
				} else $read_state="";
			}
			print "<table class='msgs' style='$c'><tr><td class='msgs_info'>$f $dt $read_state</td><td class='msgs_txt'>$txt</td></tr></table>";
		}
	}
	function jquery($uid,$acc_id) {
		?>
		<script type="text/javascript">
	//	console.log('test');
		$("input[name='chk']").change(function(){
			var id=$(this).attr('id');
		//	console.log('test'+id);
		//	console.log('ch_razdel=yes&klid='+id+'&razdel='+this.value+'&uid=<?=$uid?>&user_id=<?=$_SESSION['userid_sess']?>&acc_id=<?=$acc_id?>');
			//setup the ajax call
			$.ajax({
				type:'GET',
				url:'jquery.php',
				data:'ch_razdel=yes&&razdel='+this.value+'&uid=<?=$uid?>&user_id=<?=$_SESSION['userid_sess']?>'
			});
			window.setTimeout( window.opener.location.reload(), 1000 );
		});
		$("#delay_dt").click(function(){
			$('#accs_all').css("display", "none");
		});
		$('#delay_dt').datepicker({
			weekStart: 1,
			daysOfWeekHighlighted: "6,0",
			autoclose: true,
			todayHighlight: true,
			format: 'dd.mm.yyyy',
			language: 'ru',
		});
		$("#delay_set").click(function(){
			$('#accs_all').css("display", "block");
			var url="delay_set=yes&uid="+$(this).attr('uid')+"&dt="+$("#delay_dt").val();
		//	console.log(url);
			$.ajax({
				type:'GET',
				url:'jquery.php',
				data:url,
				success: function(data){
					$('#delay_dt').css("background-color", "#aecedd"); //background-color:green; color:white;
				}				
			});
			
		});
		$("#delay_clr").click(function(){
			$('#accs_all').css("display", "block");
			var url="delay_clr=yes&uid="+$(this).attr('uid');
		//	console.log(url);
			$.ajax({
				type:'GET',
				url:'jquery.php',
				data:url,
				success: function(data){
					$('#delay_dt').css("background-color", "#D9EDF7"); //background-color:green; color:white;
					$('#delay_dt').val("");
				}				
			});
			
		});
		$("#chk_cp").click(function(){
			if (!$(this).is(':checked')) {
				var url="chk_cp=off&uid="+$(this).attr('uid');
				var c=0;
			} else {
				var url="chk_cp=on&uid="+$(this).attr('uid');
				var c=1;
			}
		//	console.log(url);
			$.ajax({
				type:'GET',
				url:'jquery.php',
				data:url,
				success: function(data){
					if (c==1)
						$('#chk_cp_badge').css("background-color", "#5f9a48");
					else
						$('#chk_cp_badge').css("background-color", "#dff0d8");
				}				
			});
			
		});
		$("#scdl_set").click(function(){
			var scdl_opt=$("input[name='scdl_radio']:checked").val();
			var scdl_web=$("input[name='scdl_web_radio']:checked").val();
			var scdl_t=$("input[name='scdl_radio']:checked").attr('t');
			var scdl_t_web=$("input[name='scdl_web_radio']:checked").attr('t_web');
			var scdl_dt=$("#scdl_dt").val();
			var scdl_funnel=$("#scdl_funnel").val();
			var url="scdl_set=yes&uid="+$(this).attr('uid')+"&dt="+scdl_dt+"&scdl_opt="+scdl_opt+"&scdl_web="+scdl_web+"&scdl_funnel="+scdl_funnel;
			console.log(url);
			if(scdl_t==null)
				alert("Не выбрано время эфира!");
			if(scdl_t_web==null)
				alert("Не выбран вебинар!");
			$.ajax({
				type:'GET',
				url:'jquery.php',
				data:url,
				success: function(data){
					//$("#scdl_panel").toggle();
					//location='msg.php?uid=<?=$this->uid?>#scdl_hdr';
				location.reload();
					//~ $('#scdl_hdr').html('В расписании на : '+scdl_dt+' '+scdl_t+' '+scdl_t_web);
					//~ $('#scdl_hdr').css("background-color", "green"); //background-color:green; color:white;
					//~ $('#scdl_hdr').css("color", "white"); //background-color:green; color:white;
				}				
			});
			
		});
		$("#scdl_clr").click(function(){
  			var url="scdl_clr=yes&uid="+$(this).attr('uid');
		//	console.log(url);
			$.ajax({
				type:'GET',
				url:'jquery.php',
				data:url,
				success: function(data){
					location.reload();
					$('#scdl_hdr').html('Расписание очищено');
					$("#scdl_dt").val("");
				}				
			});
			
		});
		if(!$('#scdl_dt').prop('readonly')) {
			$('#scdl_dt').datepicker({
				weekStart: 1,
				daysOfWeekHighlighted: "6,0",
				autoclose: true,
				todayHighlight: true,
				format: 'dd.mm.yyyy',
				language: 'ru',
			});
		}
		$("#s_comm").click(function(){
			var comm1;
			if($("#comm1").length > 0)
				comm1='&comm1='+encodeURI($("#comm1").val()); else comm1="";
		//	console.log($("#comm1").val());
		//	console.log("comm1="+comm1);
			var url="save_comm=yes&uid="+$(this).attr('uid')+'&comm='+encodeURI($("#comm").val())+comm1+'&mob='+$("#tel").val()+'&email='+$("#email").val()+'&touch='+$("#touch").val()+'&telegram_nic='+$("#telegram_nic").val()+'&user_id='+$(this).attr('user_id');
			console.log("url= "+url);
			$.ajax({
				type:'GET',
				url:'jquery.php',
				data:url,
				success: function(data){
					//$('#disp_comm').html($("#comm").val().replace(/\n/g,"<br>")+'<br><b>'+$("#comm1").val()+'<br>Mob.: <b>'+$("#tel").val()+'</b>');
					$('#comm').css("background-color", "#eeeeee");
					$('#comm1').css("background-color", "#eeeeee");
					$('#tel').css("background-color", "#eeeeee");
					$('#email').css("background-color", "#eeeeee");
					$('#telegram_nic').css("background-color", "#eeeeee");
					$('#touch').css("background-color", "#eeeeee");
					//opener.location.reload();
				}				
			});
			
		});
		//$( window ).focusout(function() {
		$(window).bind("beforeunload", function() { 
			//console.log('unload');
			//alert("Пока, пользователь!"); 
			$.ajax({
				type:'GET',
				url:'jquery.php',
				data:'lock_off=yes&uid=<?=$uid?>'
			});
			//window.setTimeout( window.opener.location.reload(), 1000 );
			window.setTimeout( window.opener.location="cp.php?view=yes&uid=<?=$uid?>", 1000 );
		});		

        $(document).ready(function(){
            $('#city').keyup(function(){
                var msgs_city = $(this).val();
                if(msgs_city != ''){
                    $.ajax({
                        url:"jquery.php",
                        method:"POST",
                        data:{msgs_city:msgs_city},
                        success:function(data){
                            $('#cityList').fadeIn();
                            $('#cityList').html(data);
                        }
                    });
                }
            });
            $(document).on('click', 'li', function(){
                $('#city').val($(this).text());
                $('#cityList').fadeOut();
            });
        });

		$(document).ready(function() {
		  // При вводе символов в текстовое поле
		  $('#userInput').on('input', function() {
			var userInput = $(this).val(); // Значение текстового поля
			
			// Отправка AJAX-запроса на сервер для получения списка пользователей
			$.ajax({
			  url: 'jquery.php', // Путь к PHP-обработчику
			  method: 'POST',
			  data: { userInput: userInput,
					access_level: <?=$_SESSION['access_level']?>,
					user_id: <?=$_SESSION['userid_sess']?>
					}, // Передача введенного значения на сервер
			  dataType: 'json',
			  success: function(response) {
				var userList = '';

				if (response.length > 0) {
				  response.forEach(function(user) {
					userList += '<a href="#" class="list-group-item list-group-item-action" data-id="' + user.id + '">' + user.real_user_name + '</a>';
				  });
				} else {
				  userList = '<p>Пользователи не найдены</p>';
				}
				
				$('#userList').html('<div class="list-group">' + userList + '</div>'); // Вывод списка пользователей
			  }
			});
		  });

		  // При выборе значения из списка
		  $(document).on('click', '.list-group-item', function(e) {
			e.preventDefault();
			
			var selectedUserName = $(this).text(); // Выбранное значение
			var selectedUserId = $(this).data('id'); // ID выбранного пользователя
			
			$('#userInput').val(selectedUserName); // Поместить выбранное значение в поле ввода
			$('#userID').val(selectedUserId); // Записать ID в скрытое поле
			
			// Можно выполнить другие операции с выбранным значением
			
			$('#userList').html(''); // Очистить список
			$('#FormUserList').submit();
		  });

		  // При клике на иконку стирания значения
		  $(document).on('click', '#clearIcon', function() {
			$('#userInput').val(''); // Очистить поле ввода
			$('#userID').val(''); // Очистить скрытое поле
		  });
		});

		document.getElementById("man_id").addEventListener("change", function() {
			document.getElementById("f_man_id").submit(); // Отправка формы при выборе значения в списке
		  });

		</script>
		
		<?php

		$uid=$this->uid;
		include "/var/www/vlav/data/www/wwl/inc/msg_tags.inc.php";
	}
}


//-----------------------

include "/var/www/vlav/data/www/wwl/inc/top.class.php";
$db1=new db;

include "init.inc.php";

class top1 extends top {
	function nota() {
		global $tm_pay_end,$tm_pay_end_0ctrl;

		if($tm_pay_end_0ctrl)
			return;
		if( $tm_pay_end>0 && $tm_pay_end<time() ) {
			print "<p class='alert alert-warning' >Оплаченный период закончился, доступ скоро будет приостановлен. Пожалуйста, продлите оплату: <a href='billing_pay.php' class='' target='_blank'>продлить</a></p>";
		}
		if( $tm_pay_end>0 && $tm_pay_end<(time()-(1*24*60*60)) && !$tm_pay_end_0ctrl) {
			$this->bottom();
			exit;
		}
	}
}
$t=new top1($database,0,false,$favicon,true,$gid=$VK_GROUP_ID);

class fmsg extends msg__ {
	function top_info() {
	}
	function msg_info_specprice($uid) {
	}
	function discount_card($uid) {
	}
	function uid_info_add() {
		if($_SESSION['access_level']<=4) {
			if(1 || $this->database=='vkt') {
				$c=($this->price2_chk_for_any($this->uid))?"danger":"info";
				print "<a class='btn btn-$c' href='javascript:wopen_1(\"discount.php?uid=$this->uid\")'>Спеццена</a>&nbsp;";
			}
		}
		if($_SESSION['access_level']<=3) {
			print "<a class='btn btn-primary' href='javascript:wopen_1(\"pay_cash.php?uid=$this->uid\")'>Провести оплату</a>&nbsp;";
			if($this->is_partner_db($this->uid))
				$btn="Партнер инфо"; else $btn="Сделать партнером";
			print "<a class='btn btn-warning' href='javascript:wopen_1(\"partner.php?uid=$this->uid\")'>$btn</a>";
		}
		return 0;
	}
	function add_filter() {
	}
	function ch_user_id ($uid,$user_id_from,$user_id_to) {
		$klid=$this->dlookup("klid","users","id='$user_id_to'");
		$this->query("UPDATE cards SET tm_user_id=0,user_id='$user_id_to',pact_conversation_id=0,utm_affiliate='$klid' WHERE uid='$uid'",0);
		return;
		
		if($user_id_from) {
			$name_from=$this->dlookup("wa_user_name","users","id='$user_id_from'");
			$name_to=$this->dlookup("wa_user_name","users","id='$user_id_to'");
			$msg="Хочу вам представить нашего менеджера, это $name_to. Сейчас вам напишет, общайтесь дальше напрямую.";
			//$this->do_send_wa($uid,$msg,3);
			//print "<div class='well well-sm' >$msg</div>";
		}
		if($user_id_from) {
			sleep(0); //5
			$msg="Здравствуйте, меня зовут $name_to. Я ваш новый менеджер по поводу нового проекта для сетевиков \"Формула привлечения\". Где брать людей в сетевой и как работать онлайн. Вам это интересно?";
			//$this->do_send_wa($uid,$msg,3);
			//print "<div class='well well-sm' >$msg</div>";
		}
		//print "<div class='alert alert-info' >Переназначено и уведомления отправлены</div>";
		print "<div class='alert alert-danger' >Переназначено, без уведомлений</div>";
		//$this->mark_new($uid,1);
		//$this->notify($uid,"Вам назначен лид https://1-info.ru/f12/db/msg.php?uid=$uid");
	}
	function disp_touch_result() {}

	function scdl_opts() {
		return;
		foreach($this->scdl_opt_arr AS $key=>$val) {
			print "<div class='form-check-inline'>
					  <badge class='form-check-badge'>
						<input type='radio' class='form-check-input' value='$key' id='scdl_time_$key' t='$val' name='scdl_radio'> $val
					  </badge>
					</div>
				";
		}
	}
//~ $m->scdl_opt_arr=[9=>'9:00',12=>'12:00',1440=>'14:40',1720=>'17:20',20=>'20:00'];
//~ $m->scdl_web_arr=[1=>'МОЙ ВЕБИНАР 1'];

	function scheduling() {
		$res=$this->query("SELECT * FROM lands WHERE del=0 AND tm_scdl>".time());
		$cnt_events=$this->num_rows($res);
		$this->scdl_web_arr=[];
		print "<script>
			var arr_opt={
			";
		while($r=$this->fetch_assoc($res)) {
			$this->scdl_web_arr[$r['land_num']]=$r['land_name'];
			$tm_opt=$r['tm_scdl']-$this->dt1($r['tm_scdl']);
			$tm_opt=$r['tm_scdl'];
			$tm_opt_dt=date("d.m.Y H:i",$r['tm_scdl']);
			$t1=$t2=$t3="";
			if($r['tm_scdl_period']) {
				$tm_opt_1=$tm_opt+$r['tm_scdl_period'];
				$tm_opt_dt_1=date("d.m.Y H:i",$tm_opt_1);
				$t1="'$tm_opt_1' : '$tm_opt_dt_1' ,";

				$tm_opt_2=$tm_opt_1+$r['tm_scdl_period'];
				$tm_opt_dt_2=date("d.m.Y H:i",$tm_opt_2);
				$t2="'$tm_opt_2' : '$tm_opt_dt_2' ,";

				$tm_opt_3=$tm_opt_2+$r['tm_scdl_period'];
				$tm_opt_dt_3=date("d.m.Y H:i",$tm_opt_3);
				$t3="'$tm_opt_3' : '$tm_opt_dt_3' ,";

			}
			$dt=date("d.m.Y",$r['tm_scdl']);
			print "{$r['land_num']}: {
						dt: '$dt',
						dt_readonly:'readonly',
						tm_arr: {
							'$tm_opt' : '$tm_opt_dt' ,
							$t1
							$t2
							$t3
						}
					},
				";
			
		}
		print "}
			</script>"; 
		//	print "HERE_$tm_opt $tm_opt_1"; exit;

		$r=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE uid=".$this->uid));
		//SCDL
		if($r['tm_schedule']>=mktime(0,0,0,date("m"),date("d"),date("Y")))
			$c1="#ffffff"; else $c1="#FF91A4"; 
		if($r['tm_schedule']>0) {
			$web=(isset($this->scdl_web_arr[$r['scdl_web_id']]))?$this->scdl_web_arr[$r['scdl_web_id']]:"ПРОСРОЧЕНО ";
			$c="background-color:green; color:$c1;";
			$wday=array("ВС","ПН","ВТ","СР","ЧТ","ПТ","СБ",);
			$dt="".date("d.m.Y",$r['tm_schedule']); 
			$hdr="В расписании на : ".date("d.m.Y H:i",$r['tm_schedule'])." ". $wday[date("w",$r['tm_schedule'])]."  <span class='badge badge-warning' >$web</span>";
		} else { $dt=date("d.m.Y",time()+(24*60*60)); $c="background-color:#EEE;"; $hdr="Расписание";}
		print "\n\n<!--scheduling-->\n";
		?>
		<div class='card p-3'>
			<div id='scdl_hdr' class='card p-1' data-toggle='collapse' data-target='#scdl_panel' style='<?=$c?>'>
				<a href='javascript:void(0);' style='<?=$c?>'><?=$hdr?></a>
			</div>
			<form class='form-inline'>
			<div class='collapse' id='scdl_panel'>
			<?if($cnt_events) {?>
				<div class='card bg-light m-1 bg-light-sm' >
				<?
					$scdl_web_s=""; //(sizeof($this->scdl_web_arr)==1)?'checked':'';
					$res1=$this->query("SELECT * FROM lands WHERE del=0 AND tm_scdl>".time());
					while($r1=$this->fetch_assoc($res1)) {
						$val="({$r1['land_num']}) {$r1['land_name']}";
						$key=$r1['land_num'];
						$dt=date("d.m.Y H:i",$r['tm_scdl']);
						print "<div>
							<input type='radio'
								class='form-check-input'
								value='$key'
								id='scdl_web_$key'
								t_web='$val'
								name='scdl_web_radio'
								$scdl_web_s> $val
						</div>";
					}
					//~ $scdl_web_s=""; //(sizeof($this->scdl_web_arr)==1)?'checked':'';
					//~ foreach($this->scdl_web_arr AS $key=>$val) {
						//~ print "<div class='form-check-inline px-3'>
								  //~ <badge class='form-check-badge'>
									//~ <input type='radio' class='form-check-input' value='$key' id='scdl_web_$key' t_web='$val' name='scdl_web_radio' $scdl_web_s> $val
								  //~ </badge>
								//~ </div>
							//~ ";
					//~ }
				?>
				</div>

				<div class='form-group' id='scdl_opts_dt'>
<!--
					<label for='scdl_dt'>Дата</label>
					<input id='scdl_dt'  class='form-control' type='text' style='<?=$c?>' name='dt' value='<?=$dt?>' >
-->
				</div>

				<div class='card p-1' id='scdl_opts' >
					<?=$this->scdl_opts();?>
				</div>

				<?
				if(isset($this->scdl_web_funnel[$r['scdl_web_id']])) {
					print "<input type='hidden' id='scdl_funnel' value='{$this->scdl_web_funnel[$r['scdl_web_id']]}'>";
				} else
					print "<input type='hidden' id='scdl_funnel' value='0'>";
				?>
				
				<input type='hidden' name='klid' value='<?=$r['id']?>'>
				<input type='hidden' name='uid' value='<?=$r['uid']?>'>
				<input type='hidden' name='acc_id' value='<?=$this->acc_id?>'>
				&nbsp;&nbsp;
				<input type='hidden' id='scdl_dt' value='0'>
				<input type='submit'  class='btn btn-success' name='do_scdl' value='Записать' uid='<?=$this->uid?>'  id='scdl_set' onclick='return(false);'>&nbsp;&nbsp;
				<input type='submit'  class='btn btn-warning' name='do_scdl_del' value='Убрать' uid='<?=$this->uid?>' id='scdl_clr' onclick='return(false);'>
			<?
			} else {
				print "<p class='alert alert-warning' >Нет активных мероприятий
				<input type='submit'  class='btn btn-warning' name='do_scdl_del' value='Очистить' uid='$this->uid' id='scdl_clr' onclick='return(false);'>
				</p>";
			}
			?>
			</div>
			</form>
		</div>

		<script>
			<?foreach($this->scdl_web_arr AS $key=>$val) {
				?>
				$("#scdl_web_<?=$key?>").click(function(){
					console.log(arr_opt[<?=$key?>].tm_arr);

					//scdl_opts_dt.innerHTML="<input id='scdl_dt'  class='form-control' type='text' name='dt'  value='"+arr_opt[<?=$key?>].dt+"' "+arr_opt[<?=$key?>].dt_readonly+" >";

					var h="";
					for (var key in arr_opt[<?=$key?>].tm_arr) {
						let val=arr_opt[<?=$key?>].tm_arr[key];
					  //scdl_opts.innerHTML=key + ": " + arr_opt[<?=$key?>].tm_arr[key];
					  console.log("val="+val);
					  h+="<div class='form-check-inline px-3'>"+
						"<badge class='form-check-badge'>"+
						"<input type='radio' class='form-check-input' value='"+key+"' id='scdl_time_"+key+"' t='"+val+"' name='scdl_radio'> "+val+
					  "</badge>"+
					"</div>\n";
					}
					scdl_opts.innerHTML=h;
				});
				<?
			}
			?>
		</script>
		
		<!--/scheduling-->
		<?
	}
}
$m=new fmsg;
$m->gid=$t->gid;
$m->db200=$DB200;
$m->title="VKT";
$m->allow_change_acc=($t->userdata['access_level']<3)?true:false; 
$m->allow_change_acc=true;
$m->userdata=$t->userdata;
$m->connect($database);
$m->msg_add_to_friends="Привет, можно минуту твоего внимания?";
$m->send_talk_to_vk=array();
$m->send_talk_to_email=array(); //array("vlav@mail.ru");
$m->email_from="office@winwinland.ru";
$m->email_from_name="WINWINLAND";
$m->email_subj="Re:";



$m->pact_token="yogahelpyou";
$m->pact_not_save_outgoing_wa=true;
$m->domain="for16.ru";
$m->telegram_bot=$tg_bot_notif;
$m->tg_bot=$tg_bot_msg;
$m->sid_visited_webinar=[13];

$m->for_touch_display='none';

$m->scdl_opt_arr=[9=>'9:00',12=>'12:00',1440=>'14:40',1720=>'17:20',20=>'20:00'];
$m->scdl_web_arr=[1=>'МОЙ ВЕБИНАР 1'];





$m->run();
$t->bottom();

?>
