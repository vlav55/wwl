<?
class vk_landing extends db {
	var $uid,$first_name,$last_name,$par;
	var $comm="ЗАШЕЛ НА ЛЭНДИНГ";
	var $mode=1; //0-insert new into VKLIST / 1 - CARDS
	var $vklist_group_id=2; //vklist group where to add new customers in mode 0
	var $title="";
	var $og_sitename="";
	var $descr="";
	var $root="";
	var $favicon="images/favicon.png";
	var $og_image="images/og.jpg";
	var $og_url="";
	var $og_image_w=968;
	var $og_image_h=504;
	var $notify_emails=array();
	var $notify_vk_uids=array();
	var $notify_vk_token_from=false;
	var $array_ignore_visitors=array(198746774);
	var $in_frame=true;
	var $group_id;
	
	
	function __construct($db,$db200=false) {
		$this->connect($db);
		$this->disp_mysql_errors=false;
		$this->disp_mysql_errors=false;
	} 
	function auth() {
		$this->uid=0;
		$this->first_name="";
		$this->last_name="";
		if(!is_numeric($this->uid) || $this->uid==0) {
			if(isset($_GET['viewer_id']))
				$this->uid=intval($_GET['viewer_id']);
		}
		if(!is_numeric($this->uid) || $this->uid==0) {
			if(isset($_GET['api_result'])) {
				$api_result=json_decode($_GET['api_result'],true);
				$this->uid=intval($api_result['response'][0]['uid']);
			} 
		}
		if(!is_numeric($this->uid) || $this->uid==0) {
			if(isset($_GET['uid'])) {
				$this->uid=intval($_GET['uid']);
			}
		}
		if(!is_numeric($this->uid) || $this->uid==0) {
			return false;
			//print "Ошибка. Извините, сообщите пожалуйста об этой ошибке. "; exit;
		}
	//	print "HERE_";
		$uid=$this->uid;
		if(in_array($uid,$this->array_ignore_visitors)) {
			$this->uid=0; $first_name=""; $last_name="";
			return true;
		}
		if(isset($_GET['api_result'])) {
			$res=json_decode($_GET['api_result'],true);
			$first_name=$res['response'][0]['first_name'];
			$last_name=$res['response'][0]['last_name'];
		} elseif(isset($_GET['uid'])) {
			$uid=$_GET['uid'];
			$first_name=$_GET['first_name'];
			$last_name=$_GET['last_name'];
		} 
		
		
		
		$fl_newmsg=$this->dlookup("fl_newmsg","cards","uid=$uid");
		if($_GET['hash']) {
			//print "HASH=".$_GET['hash']; 
			list(,$utm)=explode("=",$_GET['hash']);
			$utm=trim($utm);
			//exit;
		} else
			$utm="";
		if($fl_newmsg===false) {
			if($this->mode==0) { //VKLIST
				if($this->dlookup("tm_msg","vklist","uid=$uid")!=1) {
					$this->query("DELETE FROM vklist WHERE uid=$uid");
					$this->query("INSERT INTO vklist SET uid=$uid, group_id='".$this->vklist_group_id."',tm_cr=".time());
				} else
					return false;
			} else { //CARDS
				$this->query("DELETE FROM vklist WHERE uid=$uid");
				$acc_id=$this->get_default_acc();
				$this->query("INSERT INTO cards SET 
						uid=$uid,
						acc_id=$acc_id,
						name='".$this->escape(trim($first_name))."',
						surname='".$this->escape(trim($last_name))."',
						razdel=0,
						source_id=6,
						fl_newmsg=1,
						tm_lastmsg=".time().",
						tm=".time().",
						source_vote='".$this->escape($utm)."'"
						);
				//print "HERE_$uid";exit;
				$this->save_comm($uid,0,"NEW:".$this->comm,$source_id=6);
				$this->notify($uid,$msg="",$acc_id=0);
			}
		} else {
			if((int)$fl_newmsg!=3) {
				//print "HERE_$fl_newmsg";
				$this->query("DELETE FROM vklist WHERE uid=$uid");
				$this->query("UPDATE cards SET tm_lastmsg='".time()."',fl_newmsg='1',source_id=6,source_vote='".$this->escape($utm)."' WHERE uid=$uid");
			}
			$this->save_comm($uid,0,$this->comm,6);
		}
		/*
		$vk=new vklist_api();
		$vk->token=$vk->tokens['vlav'];
		$vk->ad_add_target_contacts(25357577,array($uid));
		*/
		$this->uid=$uid;
		$this->first_name=$first_name;
		$this->last_name=$last_name;
		$this->par="uid=$uid&first_name=$first_name&last_name=$last_name";
		//print "<div class=''>$this->par</div>";
		return true;
	}
	function top() {
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<title><?=$this->title?></title>
			<meta description="<?=$this->descr?>">
			<meta charset="utf-8">
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<link href="http://1-info.ru/<?=$this->root?>/<?=$this->favicon?>" rel="icon">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
			<link rel="stylesheet" href="https://1-info.ru/css/vkt_landing.css">
			<link rel="stylesheet" href="style.css">
				<meta property="og:type" content="website">
				<meta property="og:site_name" content="Название сайта">
				<meta property="og:title" content="<?=$this->title?>"/>
				<meta property="og:description" content="<?=$this->descr?>"/>
				<meta property="og:type" content="article"/>
				<meta property="og:url" content= "https://1-info.ru/<?=$this->root?>" />
				<meta property="og:locale" content="ru_RU">
				<meta property="og:image" content="http://1-info.ru/<?=$this->root?>/<?=$this->og_image?>">
				<meta property="og:image:width" content="<?=$this->og_image_w?>">
				<meta property="og:image:height" content="<?=$this->og_image_h?>">				
		</head>
		<body>
		<?
		if($this->in_frame)
			print "<div style='overflow-y: scroll; height:500px;'>\n";
			print "<div class='container-fluid main'>\n";
		?>
		<?
	}
	function page() {
		?>
		<?
	}
	function bottom($info=true) {
		if($info) {
			?>
			<div class="container-fluid div-bottom" style='text-align:center; font-size:12px;'>
			&copy; <?=date("Y")?> &nbsp; 
			<p style='font-size:10px;'>* информация на этом сайте не является публичной офертой согласно законодательства РФ.<!-- <a href='privacypolicy.php'>Политика конфиденциальности</a>--></p>
			</div>
			<?
		}
		if($this->in_frame)
			print "</div>\n</div>\n";
		?>
		</body>
		</html>
		<?
	}
	function notify_from_landing($name,$contact,$comm) {
		global $VK_OWN_UID;
		include_once('/var/www/vlav/data/www/wwl/inc/phpMailer/class.phpmailer.php');
		include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
		
		$msg="НОВЫЙ ЗАПРОС С ЛЭНДИНГА VK - $name - uid=$this->uid (uid=$uid)\n$name\n$contact\n$comm\n";

		if(sizeof($this->notify_emails)>0 && $this->notify_emails[0]!=null) {
			$subj="НОВЫЙ ЗАПРОС С ЛЭНДИНГА VK - $name - uid=$this->uid";
			$this->email($this->notify_emails,$subj,$msg,"noreply@1-info.ru","VKTRADE",false);
		}
		
		//~ if(sizeof($this->notify_vk_uids)>0 && $this->notify_vk_token_from!==false) {
			//~ $vk=new vklist_api();
			//~ if($this->notify_vk_token_from=="vlav")
				//~ $vk->token=$vk->tokens['vlav']; else $vk->token=$this->notify_vk_token_from;
			//~ foreach($this->notify_vk_uids AS $uid) {
				//~ $vk->vk_msg_send($uid, $msg);
			//~ }
		//~ }
	}
	function contact_form() {
		global $gid;
		print "<div class='container-fluid pad40'><div id='rqst' class=' well'>";
		if(isset($_GET['do_send'])) {
			if(trim($_GET['name'])=="") {
				print "<br><div class='alert alert-danger'>Необходимо заполнить <b>Имя</b>!</div>";
			} elseif(trim($_GET['contact'])=="") {
				print "<br><div class='alert alert-danger'>Необходимо заполнить <b>способ связи</b>!</div>";
			} else {
				//	print "HERE_ $this->uid";
				if($this->uid>0) {
					$uid=$this->uid;
					if($uid==0)
						if(isset($_GET['uid']))
							$uid=intval($_GET['uid']);
					$first_name=$this->first_name;
					$last_name=$this->last_name;
					$db=new db($database);
					$comm1=$_GET['name']."\n".$_GET['contact']."\n".$_GET['comm'];
					$acc_id=$db->get_default_acc();
					if(!$db->dlookup("uid","cards","uid=$uid")) {
						$db->query("INSERT INTO cards SET 
								uid='".$db->escape($uid)."',
								name='".$db->escape($first_name)."',
								surname='".$db->escape($last_name)."',
								mob='".$db->escape($_GET['contact'])."',
								fl_newmsg='3',
								tm_lastmsg='".time()."',
								tm='".time()."',
								razdel='0',
								acc_id=$acc_id,
								source_id='7'
								");
					} else {
						$comm1.="\n\n".$db->dlookup("comm1","cards","uid=$uid");
						$db->query("UPDATE cards SET 
								tm_lastmsg='".time()."',
								fl_newmsg='3',
								source_id='7'
								WHERE uid=$uid");
					}
					$db->query("DELETE FROM vklist WHERE uid=$uid");
					$db->save_comm($uid,0,"Запрос с лэндинга - $comm1",7,$vote_vk_uid=0,$mode=0, $force=true);
					//save_comm($uid,$user_id,$comm,$source_id=0,$vote_vk_uid=0,$mode=0, $force=false)
				} else {
					$first_name=""; $last_name=""; $uid=0;
				}
				$this->notify_from_landing($_GET['name'],$_GET['contact'],$_GET['comm']);
				
				print "<br><div class='alert alert-success'>Ok</div>";
				sleep(1);
			}
		}
		if(isset($_GET['name']))
			$name=$_GET['name']; else $name="$this->first_name $this->last_name";
		//if(!isset($_GET['group_id']))
			//$gid=intval($_GET['group_id']); else $gid=0;
		?>
		<form action="?<?=$this->par?>#rqst" class='' >
		<div class="form-group">
		<label for="name">Имя</label>
		<input type="text" class="form-control" id="name" name='name' value='<?=$name?>'>
		</div>
		<div class="form-group">
		<label for="contact">Телефон</label>
		<input type="text" class="form-control" id="contact" name='contact' value='<?=@$_GET['contact']?>'>
		</div>
		<div class="form-group">
		<label for="comm">E-mail</label>
		<input type='text' class="form-control" id="comm" name='comm' value="<?=@$_GET['comm']?>" >
		</div>
		<!--<div class="checkbox">
		<label><input type="checkbox" checked name='chk'> Хочу записаться на пробное занятие</label>
		</div>-->
		<!--<div style='color:white;'>Внимание! Указанные скидки действуют для всех отправивших заявку до <?=date("d.m.Y",time()+(3*24*60*60))?> . Не упускайте свою выгоду!</div>-->
		<input type='hidden' name='group_id' value='<?=$gid?>' >
		<input type='hidden' name='first_name' value='<?=$first_name?>' >
		<input type='hidden' name='last_name' value='<?=$last_name?>' >
		<input type='hidden' name='uid' value='<?=$this->uid?>' >
		<button type="submit" class="btn btn-primary" name='do_send' value='yes'>Отправить</button>
		</form>
		<?
		print "</div></div>";
	}
}

?>
