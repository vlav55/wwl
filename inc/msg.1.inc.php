<?
include "/var/www/vlav/data/www/wwl/inc/msg.class.php";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
//$db1=new db;

include "init.inc.php";

class top1 extends top {
	function nota() {
		global $tm_pay_end,$tm_pay_end_0ctrl;

		if($tm_pay_end_0ctrl)
			return;
		if( $tm_pay_end>0 && $tm_pay_end<time() ) {
			print "<p class='alert alert-warning' >Оплаченный период закончился, доступ скоро будет приостановлен. Пожалуйста, продлите оплату: <a href='billing_pay.php' class='' target='_blank'>продлить</a></p>";
		}
		if( $tm_pay_end>0 && $tm_pay_end<(time()-(1*24*60*60)) && !$tm_pay_end_0ctrl && $_SESSION['userid_sess']!=2) {
			$this->bottom();
			exit;
		}
	}
}
$db=new db($database);
if($uid=intval($_GET['uid']))
	$title=$db->dlookup("name","cards","uid='$uid'")." ".$db->dlookup("surname","cards","uid='$uid'");
else
	$title="CRM CARD";
$t=new top1($database,$title,false,$favicon,true,$gid=$VK_GROUP_ID);

class fmsg extends msg {
	function top_info() {
	}
	function msg_info_specprice($uid) {
	}
	function discount_card($uid) {
	}
	function uid_info_add() {
		print "<div class='d-flex align-items-center'>";
		if($_SESSION['access_level']<=4) {
			if(1 || $this->database=='vkt') {
				$c=($this->price2_chk_for_any($this->uid))?"danger":"info";
				print "<a class='btn btn-$c' href='javascript:wopen_1(\"discount.php?uid=$this->uid\")'>Промокод</a>&nbsp;";
			}
		}
		if($_SESSION['access_level']<=4) {
			print "<a class='btn btn-primary' href='javascript:wopen_1(\"pay_cash.php?uid=$this->uid\")'>Провести оплату</a>&nbsp;";
			if($this->is_partner_db($this->uid))
				$btn="Партнер инфо"; else $btn="Сделать партнером";
			print "<a class='btn btn-warning' href='javascript:wopen_1(\"partner.php?uid=$this->uid\")'>$btn</a>&nbsp;";
		}
		if($this->database=='vkt') {
			if($_SESSION['access_level']<=4) {
				print "<a class='btn btn-success' href='javascript:wopen_1(\"invoice.php?uid=$this->uid\")'>Выставить счет</a>&nbsp;";
			}
			if($_SESSION['access_level']<=3) {
				print "<a class='btn btn-default bg-white' href='javascript:wopen_1(\"vkt_create_acc.php?uid=$this->uid\")'>Создать аккаунт ВВЛ</a>&nbsp;";
			}
		}
		if($_SESSION['access_level']<=3) {
			if(1 || $this->database=='vkt') {
				//print "<a class='btn btn-info' href='javascript:wopen_1(\"test_webhook.php?uid=$this->uid\")'>test_webhook</a>&nbsp;";
				?>
				<div class="dropdown">
					<button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						&#8230; <!-- Three dots -->
					</button>
					<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
						<a class="dropdown-item" href="javascript:wopen_1('test_webhook.php?uid=<?=$this->uid?>')">Webhook Test</a>
					</div>
				</div>
				<?
			}
		}
		print "</div>";
		return 0;
	}
	function add_filter() {
	}
	public $hold,$keep;
	function ch_user_id ($uid,$user_id_from,$user_id_to) {
		$klid=$this->dlookup("klid","users","id='$user_id_to'");
		$this->query("UPDATE cards SET
				tm_user_id=0,
				user_id='$user_id_to',
				pact_conversation_id=0,
				utm_affiliate='$klid',
				card_hold_tm='".(time()+($this->hold*24*60*60))."',
				card_keep='$this->keep'
				WHERE uid='$uid'",0);
		//$this->notify_me("HERE");
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

$m->ctrl_id=$ctrl_id;


$m->pact_secret=$pact_secret;
$m->pact_company_id=$pact_company_id;
$m->pact_not_save_outgoing_wa=true;

$m->hold=$hold;
$m->keep=$keep;

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
