<?php
require_once "/var/www/vlav/data/www/wwl/inc/yclients.class.php";
require_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
require_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
require_once "/var/www/vlav/data/www/wwl/inc/qrcode.class.php";
class cashier extends partnerka {
	private $promocode;
	private $ctrl_dir;
	public $init_pars;
	private $vkt_send_id_cashier=0;
	private $cashier_klid;
	private $cashier_setup_klid;
	private $product_id;
	private $cmd;
	private $vkt_send_id_msg1;
	private $vkt_send_id_msg2;
	private $vkt_send_id_msg3;
	public $land_num_1; //partner
	public $land_num_2; //reg
	public $land_num_3; //pay
	private $days_new;
	private $trial_days;
	public $err_msg=[];
	public $ok_msg=[];
	public $res=[];
	public $for16url;
	private $qrcode_prefix='cashier';
	public $transport='tg';
	function __construct($database,$ctrl_id,$ctrl_dir) {

		$this->cashier_klid=100;
		$this->cashier_setup_klid=101;
		$this->product_id=1;
		$this->cmd='';
		$this->vkt_send_id_msg1=1;
		$this->vkt_send_id_msg2=2;
		$this->vkt_send_id_msg3=3;
		$this->land_num_1=1;
		$this->land_num_2=2;
		$this->land_num_3=3;
		$this->trial_days=5;

		if($database) {
			$this->connect($database);
			$this->ctrl_id=$ctrl_id;

			if(!$r=$this->get_init_pars()) {
				$this->connect('vkt');
				$client_uid=$this->dlookup("uid","0ctrl","id=$ctrl_id");
				//$this->notify_me("HERE_$client_uid");
				$this->init_company($client_uid);
				$this->connect($database);
				if(!$r=$this->get_init_pars()) {
					print "init_pars error for $client_uid $ctrl_id. Ask support pls.".print_r($r);
					exit;
				}
			}
			if($r) {
				$this->cashier_klid=$r['cashier_klid'];
				$this->cashier_setup_klid=$r['cashier_setup_klid'];
				$this->product_id=$r['product_id'];
				$this->vkt_send_id_msg1=$r['vkt_send_id_msg1'];
				$this->vkt_send_id_msg2=$r['vkt_send_id_msg2'];
				$this->vkt_send_id_msg3=$r['vkt_send_id_msg3'];
				$this->land_num_1=$r['land_num_1'];
				$this->land_num_2=$r['land_num_2'];
				$this->land_num_3=$r['land_num_3'];
			}
			$this->init_pars=$r;
			$this->promocode=null;
			$this->ctrl_dir=$ctrl_dir;
			$this->for16url="https://for16.ru/d/$ctrl_dir";
			if(!$this->get_days_new())
				$this->set_days_new(180);
			if($ctrl_id==230) {
				if($this->get_prefix()!=='TOPLASER' || $this->get_apikey()['apikey']!='36626d54607b9d9e59d09c62ff95943d3eac0038a2d7b539f3b6b0a66bb9205ebb194223511695add7a48916f28b7c773cf9958e065a6e0f312a4574418c7baa') {
					$this->notify_me("TOPLASER ERROR !!!! ".$this->get_prefix()." ".$this->get_apikey()['apikey']);
					print "Error. Ask support pls";
					exit;
				}
			}
		}
	}
	function get_init_pars() {
		if(!$json=$this->ctrl_tool_get(false,'cashier','init_pars'))
			return false;
		return json_decode($json,true);
	}
	function set_init_pars($pars) {
		if(!$json=json_encode($pars))
			return false;
		return $this->ctrl_tool_set(false,'cashier','init_pars',$json);
	}
	function set_website($url) {
		$url=trim($url);
		$headers = @get_headers($url, 1);
		if(!($headers && preg_match('/^HTTP\/.*\s(200|301|302|307|308)\s/', $headers[0])))
			return false;
		$this->query("UPDATE lands SET land_url='".$this->escape($url)."' WHERE del=0 AND land_num='{$this->land_num_2}'");
		return true;
	}
	function get_website() {
		$url=$this->dlookup("land_url","lands","del=0 AND land_num='{$this->land_num_2}'");
		if(empty($url))
			$url=$this->for16url."/".$this->land_num_2;
		$headers = @get_headers($url, 1);
		if(!($headers && preg_match('/^HTTP\/.*\s(200|301|302|307|308)\s/', $headers[0])))
			$url=$this->for16url."/".$this->land_num_2;
		return $url; 
	}
	function disp_msg_modal($msg, $title = 'Уведомление') {
		$msg_escaped = json_encode($msg);
		$title_escaped = json_encode($title);
		
		echo "<script>
		// Use setTimeout to ensure DOM is ready and function exists
		setTimeout(function() {
			if (typeof disp_msg_modal === 'function') {
				disp_msg_modal($msg_escaped, $title_escaped);
			} else {
				// Fallback if function not loaded yet
				$(document).ready(function() {
					disp_msg_modal($msg_escaped, $title_escaped);
				});
			}
		}, 100);
		</script>";
	}
	function send($uid,$msg,$fname='') {
		switch($this->transport) {
			case 'wa': return $this->send_wa($uid,$msg,$fname);
			case 'tg': return $this->send_tg($uid,$msg,$fname);
			default: return false;
		}
	}
	function send_tg($uid,$msg,$fname='') {
		global $tg_bot_msg;
		if($tg_id=$this->dlookup("telegram_id","cards","uid='$uid'")) {
			require_once "/var/www/vlav/data/www/wwl/inc/tg_bot.class.php";
			$tg=new tg_bot($tg_bot_msg);
			if(!empty($fname) && file_exists($fname)) {
				$res=$tg->send_photo($tg_id,$fname,$msg);
			} else
				$res=$tg->send_msg($tg_id,$msg);
		} else
			$res=3;
		if($res===true)
			$res=1;
		$this->res=['item'=>'send','type'=>'tg','success'=>$res,'trial'=>false,'arr'=>[]];
		$this->query("INSERT INTO vkt_send_log 
						SET 
							vkt_send_id = '".$this->vkt_send_id_cashier."',
							uid = '$uid',
							tm_event = '".time()."',
							tm = '".time()."',
							tg_id = '$tg_id',
							res_tg = '$res'
							");
		return $res;
	}
	function send_wa($uid,$msg,$fname='') {
	//return true;
	//$this->notify_me($msg); return true;
		$wa_api_key_pact=$this->get_apikey('wa_pact')['apikey'];
		$pact_company_id=$this->get_apikey('wa_pact')['company_id'];
		$trial=$this->get_apikey('wa_pact')['trial'];
		$wa_api_key_boom=$this->get_apikey('wa_boom')['apikey'];
		$wa_phone_boom=$this->get_apikey('wa_boom')['phone'];
		$phone=$this->dlookup("mob_search","cards","del=0 AND uid='$uid'");
		//$this->notify_me("HERE_$uid phone=".$phone);
		$_SESSION['send_msg'][]=$msg;

		if($wa_api_key_pact=="6dac370b7133847c9230239533b7a0a1667cfd1ff30ee9695a5861b7fe6b662aacdb3988b032656858b10231f937ad2025d96a34c862a54b83435e0282b2c318")
			$pact_company_id=85300;

		if(!$wa_api_key_pact) {
			$wa_api_key_pact="6dac370b7133847c9230239533b7a0a1667cfd1ff30ee9695a5861b7fe6b662aacdb3988b032656858b10231f937ad2025d96a34c862a54b83435e0282b2c318";
			$pact_company_id=85300;
			$cnt=(int)$this->ctrl_tool_get($this->ctrl_id, 'cashier','send_test')+1;
			if($cnt<0) {
				$this->ctrl_tool_set($this->ctrl_id, 'cashier','send_test',$cnt);
				$trial=false;
				//print "<p class='text-red' >SEND WA TEST $ctrl_id - $cnt</p>";
				//return true;
			} else {
				$trial=true;
			}
		}
		if($trial) {
			//$this->disp_msg_modal($msg, $title = 'Уведомление');
			//~ $_SESSION['send_msg'][]= "<div class='card border-warning p-3 m-3 shadow' >
			//~ <p class='alert alert-warning' ><b>Тестовый режим</b><br>
			//~ <span class='small mute' >Сообщения не отправляются, так как не настроена передача через whatsapp или СМС</span>
			//~ </p>
			//~ ".htmlspecialchars($msg)."</div>
			//~ ";
			$this->res=['item'=>'send','type'=>'wa','success'=>false,'trial'=>true];
			$ok=5;
			//return 5;
		}

		sleep(1);

		if(!$trial && $this->check_mob($phone)) {
			require_once "/var/www/vlav/data/www/wwl/inc/pact.class.php";
			$p=new pact($wa_api_key_pact,$pact_company_id);
		//$this->notify_me("HERE_ $wa_api_key_pact $pact_company_id");
		//$this->notify_me("$phone $msg");
			if($cid=$p->get_cid_by_phone($phone)) {
				if($fname) {
					$p->attach=[$p->upload_attachment($fname,$cid)];
				}
				$res=$p->send_msg($cid,$msg);
				if($res['status']=='ok') {
					//$this->notify_me("PACT WA SEND OK to $phone");
					$this->res=['item'=>'send','type'=>'wa','success'=>true,'trial'=>false,'arr'=>$res];
					$ok=1;
				} else {
					$this->notify_me("PACT WA SEND ERROR".print_r($res,true));
					$this->res=['item'=>'send','type'=>'wa','success'=>false,'trial'=>false,'arr'=>$res];
					$ok=0;
				}
			} else {
				$this->res=['item'=>'send','type'=>'wa','success'=>false,'trial'=>false,'arr'=>$p->res];
				$this->notify_me("PACT WA SEND get_cid_by_phone ERROR".print_r($res,true));
				$ok=0;
			}
		} elseif($wa_api_key_boom && $this->check_mob($phone)) {
			//~ $wa = new wa_boom($wa_api_key_boom,$wa_phone_boom);
			//~ try {
				//~ if($fname) {
					//~ $qrcode_url="https://for16.ru/d/$this->ctrl_dir/$fname";
					//~ //$this->notify_me($qrcode_url); 
					//~ if($res=$wa->send_media($phone,$msg,$qrcode_url))
						//~ $this->res=['item'=>'send','type'=>'wa','success'=>true,'code'=>$res];
					//~ else
						//~ $this->res=['item'=>'send','type'=>'wa','success'=>false,'code'=>$res];
					//~ return $res;
				//~ } else {
					//~ //$this->notify_me("phone=$phone \n".$msg); 
					//~ return $wa->send_msg($phone,$msg);
				//~ }
			//~ } catch (Exception $e) {
				//~ $this->notify_me("boom $ctrl_id ".$e->getMessage());
				//~ echo "<p class='alert alert-warning' >Ошибка отправки в whatsapp: ".$e->getMessage()."</p>";
			//~ }
		}
		$this->query("INSERT INTO vkt_send_log 
						SET 
							vkt_send_id = '".$this->vkt_send_id_cashier."',
							uid = '$uid',
							tm_event = '".time()."',
							tm = '".time()."',
							wa_id = '$phone',
							res_wa = '$ok'
							");
		return $ok;
	}
	function show_message($msg) {
		$msg_escaped = json_encode($msg);
		print "<script>
		$(document).ready(function() {
			showMessage('Отправлено сообщение', $msg_escaped, 'primary');
		});
		</script>";
	}
	function get_cashier_url() {
		$direct_code=$this->dlookup("direct_code","users","klid={$this->cashier_klid}");
		return "https://for16.ru/d/{$this->ctrl_dir}/cashier.php?u=$direct_code";
	}
	function get_cashier_setup_url() {
		$direct_code=$this->dlookup("direct_code","users","klid={$this->cashier_setup_klid}");
		return "https://for16.ru/d/{$this->ctrl_dir}/cashier_setup.php?u=$direct_code";
	}
	function get_fee() {
		return $this->dlookup("fee_1","product","id={$this->product_id}");
	}
	function set_fee($fee) {
		if(!floatval($fee))
			return false;
		return $this->query("UPDATE product SET fee_1='".floatval($fee)."' WHERE id={$this->product_id}");
	}
	function get_discount() {
		return $this->dlookup("discount","product","id={$this->product_id}");
	}
	function set_discount($discount) {
		if(!floatval($discount))
			return false;
		return $this->query("UPDATE product SET discount='".floatval($discount)."' WHERE id={$this->product_id}");
	}
	function get_no_discount_for_owner() { //1 or 0
		return 	$this->ctrl_tool_get(false,'cashier','no_discount_for_owner');
	}
	function set_no_discount_for_owner($val) { //1 or 0
		$this->ctrl_tool_set(false,'cashier','no_discount_for_owner',intval($val));
	}
	function get_prefix() {
		return 	$this->ctrl_tool_get(false,'cashier','prefix');
	}
	function set_prefix($prefix) {
		if(empty(trim($prefix)))
			$prefix="promo";
		$this->ctrl_tool_set(false,'cashier','prefix',mb_substr(trim($prefix),0,16));
	}
	function get_days_new() {
		return 	$this->ctrl_tool_get(false,'cashier','days_new');
	}
	function set_days_new($days_new) {
		if(!intval($days_new))
			return false;
		$this->ctrl_tool_set(false,'cashier','days_new',intval($days_new));
	}
	function get_msg_default($n) {
		$msg[1]="По вашему промокоду была совершена покупка и Вам начислен  кэшбэк в размере *{{cashback}}*.
Всего баллов на вашем счете: {{cashback_all}}.
Вы можете оплатить баллами до 100% стоимости покупки.
Ждем Вас и спасибо, что делитесь промокодом!
";

		$msg[2]="Благодарим за визит, мы дарим вам промокод *{{promocode}}*.
Он действует для всех ваших близких и друзей и дает скидку 15% каждому, кому вы его передадите. 

Но это не обычный промокод! 
Кроме скидки вашему другу, вам начисляется кэшбэк в течении 1 года в размере 15%, при каждой оплате вашего друга вам и вы сможете использовать его при оплате наших услуг. 

Делитесь промокодом с друзьями, дарите им скидку, и получайте кэшбэк со всех их покупок, оплачивайте кэшбэком наши услуги до 100%                                                                                                    ";

		$msg[3]="{{qrcode}}
Промокод на скидку 10% *{{promocode}}*
Узнать о наших услугах можно по телефону ... или на сайте ...
";
		return $msg[$n];
	}
	function get_msg($n) {
		if($n==1)
			return $this->dlookup("msg","vkt_send_1","id={$this->vkt_send_id_msg1}");
		if($n==2)
			return $this->dlookup("msg","vkt_send_1","id={$this->vkt_send_id_msg2}");
		if($n==3)
			return $this->dlookup("msg","vkt_send_1","id={$this->vkt_send_id_msg3}");
		return false;
	}
	function set_msg($n,$msg) {
		if(empty(trim($msg)))
			return false;
		$msg=$this->escape(mb_substr(trim($msg),0,2048));
		$tm=time();
		if($n==1) {
			if($this->dlookup("id","vkt_send_1","id = '{$this->vkt_send_id_msg1}'"))
				$this->query("UPDATE vkt_send_1 SET msg='$msg' WHERE id = '{$this->vkt_send_id_msg1}'");
			else
				$this->query("INSERT INTO vkt_send_1 SET
					id = {$this->vkt_send_id_msg1},
					tm=$tm,
					msg='$msg',
					sid = 30,
					name_send = 'Оплата услуги',
					fl_cashier=1,
					del = 1");
		}
		if($n==2)
			if($this->dlookup("id","vkt_send_1","id = '{$this->vkt_send_id_msg2}'"))
				$this->query("UPDATE vkt_send_1 SET msg='$msg' WHERE id = '{$this->vkt_send_id_msg2}'");
			else
				$this->query("INSERT INTO vkt_send_1 SET
					id = {$this->vkt_send_id_msg2},
					tm=$tm,
					msg='$msg',
					sid = 26,
					name_send = 'Начислен кэшбэк',
					fl_cashier=2,
					del = 1");
		if($n==3)
			if($this->dlookup("id","vkt_send_1","id = '{$this->vkt_send_id_msg3}'"))
				$this->query("UPDATE vkt_send_1 SET msg='$msg' WHERE id = '{$this->vkt_send_id_msg3}'");
			else
				$this->query("INSERT INTO vkt_send_1 SET
					id = {$this->vkt_send_id_msg3},
					tm=$tm,
					msg='$msg',
					sid = 26,
					name_send = 'Отправить QR код',
					fl_cashier=3,
					del = 1");
		return true;
	}
	function add_client($mob,$name=null) {
		if(!$name) {
			$name=$this->dlookup("name","cards","mob_search='$mob'");
		}
		$r=[
			'first_name'=>$name ? $name : $mob,
			'phone'=>$mob,
		];
		$uid=$this->cards_add($r,$update_if_exist=true);
		$klid=$this->dlookup("id","cards","del=0 AND uid='$uid'");
		$this->partner_add($klid);
		return $uid;
	}
	function add_cashier___($ctrl_id) {
		if(!$this->dlookup("id","cards","del=0 AND id=100")) {
			$cashier_uid=$this->cards_add(['first_name'=>'Кассир_1']);
			$this->query("UPDATE cards SET id='100' WHERE uid='$cashier_uid'");
		}
		$this->ctrl_id=$ctrl_id; //need to avoid error in partner_add
		$this->partner_add(100,"","Кассир",$username_pref='cashier_');
		return $this->dlookup("direct_code","users","del=0 AND klid='100'");
	}
	function issue_promocode($uid) {
		$promocode_new=$this->promocode_gen($this->get_prefix());
		$tm2=time()+($this->get_days_new()*24*60*60);
		if(empty($this->init_pars['cmd'])) {
			if($id=$this->promocode_add($promocode_new,
						$uid,
						$tm1=0,$tm2,
						$this->product_id,
						$this->get_discount(),
						$price=0,
						$this->get_fee(),
						$fee_2=0,
						$cnt=-1,$hold=0,$keep=0)) {
				$this->promocode=$this->dlookup("promocode","promocodes","id='$id'");
				return $this->promocode;
			}
		} else {
			//$this->notify_me("CMD");
			$this->promocode=$this->prepare_msg_promocode($uid,$this->init_pars['cmd']);
			return (strpos($this->promocode,'{{') ===false) ? $this->promocode : "ERROR";
		}
		return false;
	}
	function get_promocode() {
		return $this->promocode;
	}
	function issue_qrcode($url,$fname) {
		if(!is_dir("qrcodes/"))
			mkdir("qrcodes/");
		$fname="qrcodes/".$fname;
		if(file_exists($fname))
			unlink($fname);
		$qr = new qrcode_gen();
		//$this->notify_me("HERE_$fname $url");
		return $qr->generate($url,$fname);
		//~ $qrCode = new QrCode($url);
		//~ $writer = new PngWriter();
		//~ $result = $writer->write($qrCode);
		//~ return $result->saveToFile($fname);
		//print $fname;
		//~ header('Content-Type: ' . $result->getMimeType());
		//~ echo $result->getString();
	}
	function send_loyalty_card($mob,$name=null) {
		global $ctrl_dir;
		$_SESSION['send_msg']=[];
		$uid=$this->add_client($mob,$name);
		if($this->transport=='tg') {
			if(!$tg_id=$this->dlookup("telegram_id","cards","del=0 AND mob_search='$mob'")) {
				$short_link=$this->generate_short_link(['uid'=>$uid,'m'=>$mob],$this->get_for16_url($ctrl_dir)."/short.php");
				$fname="short_link_".$uid.".png";
				$this->issue_qrcode($short_link,$fname);
				$_SESSION['show_modal_script'] = "
				<script>
				document.addEventListener('DOMContentLoaded', function() {
					showShortLinkModal('" . addslashes($short_link) . "', 'qrcodes/" . addslashes($fname) . "', '$mob');
				});
				</script>";
				return 3;
			}
		}
		$this->save_comm_tm_ignore=1*60;
		if($this->save_comm($uid,$_SESSION['userid_sess'],false,27)) {
			$this->tag_create($tag_id=2,$tag_name='Получил карту', $tag_color='#0000FF', $fl_not_send=0);
			$this->tag_add($uid,2);
			$promocode=$this->issue_promocode($uid);
			//$this->notify_me("HERE_$promocode");
			if(!$this->send($uid,$this->prepare_msg($uid,$this->get_msg(2)))) {
				return false;
			}
			sleep(1);
			$msg3=$this->get_msg(3);
			if(strpos($msg3,"{{qrcode}}")!==false) {
				$msg3=str_replace("{{qrcode}}","",$msg3);
				$fname=$this->qrcode_prefix.$uid.".png";
				//$this->issue_qrcode(strtok($this->get_cashier_url(),'?')."?p=$promocode",$fname);
				$parts = parse_url($this->get_website());
				$path = $parts['path'] ?? '/';
				if ($path !== '/' && !strpos(basename($path), '.')) {
					$path = rtrim($path, '/') . '/';
				}
				$query = [];
				if (isset($parts['query'])) {
					parse_str($parts['query'], $query);
				}
				$query['bc'] = $promocode;
				$base = ($parts['scheme'] ?? 'https') . '://' . $parts['host'] . $path;
				$url= $base . '?' . http_build_query($query);
				//$this->notify_me("HERE_$fname $url"); exit;
				$this->issue_qrcode($url,$fname);
				$_SESSION['send_msg'][]="--QR код--";
			} else
				$fname=false;
			$res=$this->send($uid,$this->prepare_msg($uid,$msg3),'qrcodes/'.$fname);
			return $res;
		} else
			return 2;
	}
	function withdraw_cashback_to_yclients($klid,$sum=0) {
		$tmp=$this->get_current_database();
		$this->mute=true;
		$this->fill_op($klid,0,time(),$this->ctrl_id);
		if($amount=$this->rest_fee($klid)) {
			if($sum) {
				$amount=$sum>$amount ? $amount : $sum;
			}
			$phone=$this->dlookup("mob_search","cards","id='$klid'");
			$salon_id=$this->is_yclients($this->ctrl_id);
			$y=new yclients($salon_id);
			$res=$y->cashback_withdraw($phone,$amount,$card_number=rand(100,999).$phone);
			$this->connect($tmp);
			if(!$res['success']) {
				$err_msg[]=$res['msg'];
				msg($res['msg'],'warning');
				return false;
			} else
				msg("Кэшбэк выведен на карту клиента в YCLIENTS",'success');
			$this->pay_fee($klid,$amount,$vid=2,$comm='выведено на карту yclients');
			$this->ok_msg[]="Кэшбэк в сумме $amount р. выведен на карту клиента в yclients";
			$p_uid=$this->dlookup("uid","cards","id='$klid'");
			$this->save_comm($p_uid,0,"Выплачены партнерские на карту yclients: $amount",28);
		} else
			$this->err_msg[]="Ошибка при выводе кэшбэка на карту клиента в yclients";
		return true;
	}
	function send_cashback_notice($klid) {
		$_SESSION['send_msg']=[];
		$p_name=$this->dlookup("name","cards","id='$klid'")." ".$this->dlookup("surname","cards","id='$klid'");
		$p_uid=$this->dlookup("uid","cards","id='$klid'");
		if($klid) {
			//$this->notify_me("klid=$klid");
			$this->mute=true;
			$this->fill_op($klid,0,time(),$this->ctrl_id);
			if($fee_last=$this->last_fee($klid, $minutes_from_now=5)) {
				$this->save_comm($p_uid,0,"Начислен кэшбэк - $fee_last",26);
				if($this->get_yclients_withdraw_cashback()) {
					$this->withdraw_cashback_to_yclients($klid);
				}
			}
		}
		//$this->notify_me($this->prepare_msg($p_uid,$this->get_msg(1))); exit;
		return $this->send($p_uid,$this->prepare_msg($p_uid,$this->get_msg(1)));
	}
	function save_apikey($vid, $apikey, $phone='') {
		if($vid=='wa_pact') {
			$ctrl_id=$this->ctrl_id;
			$msg='';
			$pact_secret=$apikey; //6dac370b7133847c9230239533b7a0a1667cfd1ff30ee9695a5861b7fe6b662aacdb3988b032656858b10231f937ad2025d96a34c862a54b83435e0282b2c318
			$pact_company_name=false; //substr(trim($_POST['pact_company_name']),0,64);
			require_once "/var/www/vlav/data/www/wwl/inc/pact.class.php";
			$p=new pact($pact_secret);
			if($p->company_id || empty(trim($pact_secret))) {
				$tmp=$this->database;
				$this->connect('vkt');
				$this->query("UPDATE 0ctrl SET
					pact_secret='".$this->escape(trim($pact_secret))."',
					pact_company_id='$p->company_id'
					WHERE id='$ctrl_id'");
				$this->connect($tmp);
				return true;
			} else {
				print "<p class='alert alert-warning' >Ошибка подключения whatsapp PACT - API ключ недействителен</p>";
			}
		}
		if($vid=='wa_boom') {
			$this->ctrl_tool_set(false,$tool='wa_boom_apikey',1,$apikey);
			$this->ctrl_tool_set(false,$tool='wa_boom_phone',1,$phone);
			return true;
		}
		return false;
	}
	function get_apikey($vid='wa_pact') {
		if($vid=='wa_pact') {
			$tmp=$this->database;
			$this->connect('vkt');
			$apikey=$this->dlookup("pact_secret","0ctrl","id='$this->ctrl_id'",0);
			$company_id=$this->dlookup("pact_company_id","0ctrl","id='$this->ctrl_id'",0);
			$trial=false;
			if(empty($apikey)) {
				$apikey=""; //$this->dlookup("pact_secret","0ctrl","id='1'");
				$company_id=""; //$this->dlookup("pact_company_id","0ctrl","id='1'",0);
				$trial=true;
			}
			$this->connect($tmp);
			return ['apikey'=>$apikey,'company_id'=>$company_id, 'trial'=>$trial];
		}
		if($vid=='wa_boom') {
			$apikey=$this->ctrl_tool_get(false,$tool='wa_boom_apikey',1);
			$phone=$this->ctrl_tool_get(false,$tool='wa_boom_phone',1);
			return ['apikey'=>$apikey,'phone'=>$phone,'trial'=>$trial];
		}
		return ['apikey'=>false,'phone'=>false,'trial'=>$trial];
	}
	function get_payments_cnt($uid,$promocode_id) {
		$uid=intval($uid); $promocode_id=intval($promocode_id);
		return $this->fetch_assoc($this->query("SELECT COUNT(id) AS cnt FROM avangard WHERE vk_uid='$uid' AND promocode_id='$promocode_id'"))['cnt'];
	}
	function init_company($client_uid) { //returns client_ctrl_id
		$vkt=new vkt('vkt');
		if($client_ctrl_id=$vkt->vkt_create_account($client_uid,false)) { //return $client_ctrl_id in any case if account exists or not
			$this->connect('vkt');
			$info=$this->cards_read_par($client_uid);
			$company=$info['company']." ИНН".$info['inn'];
			$this->query("UPDATE 0ctrl SET company='".$this->escape($company)."' WHERE id='$client_ctrl_id'");
			$client_ctrl_dir=$this->dlookup("ctrl_dir","0ctrl","id='$client_ctrl_id'");
			$client_database=$vkt->get_ctrl_database($client_ctrl_id);//  $client_ctrl_id==1 ? 'vkt' : 'vkt1_'.$client_ctrl_id;

			$this->connect($client_database);
			//~ if($this->get_init_pars())
				//~ return $client_ctrl_id;

			$this->chk_column('product', 'fl_cashier', 'tinyint', $index = true);
			if(!$pid=$this->dlookup("id","product","del=0 AND fl_cashier=1")) {
				$last_pid=$this->dlookup("id","product","del=0") ?: 0;
				$pid=$last_pid+1;
				$this->query("INSERT INTO product SET id='$pid',descr = 'Все продукты',fee_1 = 0,fl_cashier=1");
			}
			$this->product_id=$pid;
			//print "<p class='alert alert-success' >Продукт по умолчанию создан</p>";

			$website="https://for16.ru/d/$client_ctrl_dir/1";

			$this->chk_column('lands', 'fl_cashier', 'tinyint', $index = true);
			$tm=time();
			$last_land_num=$this->fetch_assoc($this->query("SELECT land_num FROM lands WHERE del=0 ORDER BY land_num DESC"))['land_num'] ?: 0;
			if(!$this->land_num_1=$this->dlookup("land_num","lands","del=0 AND fl_cashier=1")) {
				$this->land_num_1=$last_land_num+10;
				$this->query("INSERT INTO lands SET
					tm = $tm, 
					land_num = {$this->land_num_1}, 
					fl_not_disp_in_cab = 1, 
					land_url = 'https://for16.ru/d/$client_ctrl_dir/{$this->land_num_1}', 
					land_name = 'Партнерская программа', 
					land_txt = '<h2 style=\"text-align: center;\"><span style=\"font-family: arial, helvetica, sans-serif; color: #236fa1;\">Примите участие в партнерской программе</span></h2>', 
					thanks_txt = '<h2 style=\"text-align: center;\"><span style=\"font-family: arial, helvetica, sans-serif; color: #236fa1;\">Благодарим за регистрацию!</span></h2>\r\n<p style=\"text-align: center;\"><span style=\"font-family: arial, helvetica, sans-serif;\">Ваша партнерская ссылка и доступ в личный кабинет партнера придет к вам в телеграм. Подпишитесь по кнопке ниже:</span></p>\r\n<p style=\"text-align: center;\">&nbsp;</p>', 
					bot_first_msg = 'Еще раз благодарим за регистрацию в партнерской программе\r\n\r\nВаша партнерская ссылка : $website/?bc={{partner_code}}\r\n\r\nЛичный кабинет: {{cabinet_link}}', 
					fl_partner_land = 1, 
					fl_disp_phone = 1, 
					fl_disp_email = 1, 
					fl_disp_phone_rq = 1, 
					btn_label = 'Регистрация', 
					land_type = 1,
					fl_cashier=1,
					del = 0");
			}
			if(!$this->land_num_2=$this->dlookup("land_num","lands","del=0 AND fl_cashier=2")) {
				$this->land_num_2=$last_land_num+11;
				$this->query("INSERT INTO lands SET 
					tm = $tm, 
					land_num = {$this->land_num_2}, 
					fl_not_disp_in_cab = 0, 
					land_url = 'https://for16.ru/d/$client_ctrl_dir/{$this->land_num_2}', 
					land_name = 'Сайт компании', 
					fl_disp_phone = 1, 
					fl_disp_phone_rq = 1, 
					btn_label = 'Регистрация', 
					fl_cashier=2,
					del = 0");
			}
			if(!$this->land_num_3=$this->dlookup("land_num","lands","del=0 AND fl_cashier=3")) {
				$this->land_num_3=$last_land_num+12;
				$this->query("INSERT INTO lands SET 
					tm = $tm, 
					land_num = {$this->land_num_3}, 
					fl_not_disp_in_cab = 0, 
					land_url = 'https://for16.ru/d/$client_ctrl_dir/{$this->land_num_3}', 
					land_name = 'Оплата продукта', 
					land_razdel = 3, 
					fl_disp_phone = 1, 
					fl_disp_phone_rq = 1, 
					product_id = {$this->product_id}, 
					btn_label = 'Оплата', 
					fl_cashier=3,
					del = 0");
			}
			//print "<p class='alert alert-success' >Шаблонные лэндинги созданы</p>";

			$path_files="/var/www/vlav/data/www/wwl/d/$client_ctrl_dir/tg_files";
			if(!file_exists($path_files."/logo.jpg"))
				copy("/var/www/vlav/data/www/wwl/scripts/insales/logo.jpg",$path_files.'/logo.jpg');
			$path_root="/var/www/vlav/data/www/wwl/d/$client_ctrl_dir/";
			if(!is_dir($path_root.$this->land_num_1)) {
				mkdir($path_root.$this->land_num_1);
				copy($path_root.'1/index.php',$path_root.$this->land_num_1.'/index.php');
			}
			if(!is_dir($path_root.$this->land_num_2)) {
				mkdir($path_root.$this->land_num_2);
				copy($path_root.'1/index.php',$path_root.$this->land_num_2.'/index.php');
			}
			if(!is_dir($path_root.$this->land_num_3)) {
				mkdir($path_root.$this->land_num_3);
				copy($path_root.'1/index.php',$path_root.$this->land_num_3.'/index.php');
			}
			if(!file_exists($path_files."/land_pic_{$this->land_num_1}.jpg")) {
				copy("/var/www/vlav/data/www/wwl/scripts/insales/land_pic_1.jpg",$path_files."/land_pic_".$this->land_num_1.".jpg");
				copy("/var/www/vlav/data/www/wwl/scripts/insales/thanks_pic_1.jpg",$path_files."/thanks_pic_".$this->land_num_1.".jpg");
			}

			$last_vkt_send_id=$this->dlast("id","vkt_send_1","1");
			$this->chk_column('vkt_send_1', 'fl_cashier', 'tinyint', $index = true);

			if(!$this->vkt_send_id_msg1=$this->dlookup("id","vkt_send_1","fl_cashier=1"))
				$this->vkt_send_id_msg1=$last_vkt_send_id+1;
			if(!$this->vkt_send_id_msg2=$this->dlookup("id","vkt_send_1","fl_cashier=2"))
				$this->vkt_send_id_msg2=$last_vkt_send_id+2;
			if(!$this->vkt_send_id_msg3=$this->dlookup("id","vkt_send_1","fl_cashier=3"))
				$this->vkt_send_id_msg3=$last_vkt_send_id+3;
			$this->set_msg(1,$this->get_msg_default(1));
			$this->set_msg(2,$this->get_msg_default(2));
			$this->set_msg(3,$this->get_msg_default(3));

			$this->connect($client_database);
			if(!$this->dlookup("direct_code","users","del=0 AND klid='{$this->cashier_klid}' AND username='cashier'")) {
				$p=new partnerka(false,$client_database);
				$p->ctrl_id=$client_ctrl_id; //need to avoid error in partner_add
				$p->partner_add($this->cashier_klid,"","cashier",$username_pref='cashier');
				$p->set_access_level($this->cashier_klid,7);
				//$cashier_direct_code=$this->dlookup("direct_code","users","del=0 AND klid='{$this->cashier_klid}'");
			}
			//$cashier_link="https://for16.ru/d/$client_ctrl_dir/cashier.php?u=$cashier_direct_code";

			if(!$this->dlookup("direct_code","users","del=0 AND klid='{$this->cashier_setup_klid}' AND username='cashier_setup'")) {
				$p=new partnerka(false,$client_database);
				$p->ctrl_id=$client_ctrl_id; //need to avoid error in partner_add
				$p->partner_add($this->cashier_setup_klid,"","cashier_setup",$username_pref='cashier_setup');
				$p->set_access_level($this->cashier_setup_klid,6);
				//$cashier_setup_direct_code=$this->dlookup("direct_code","users","del=0 AND klid='{$this->cashier_setup_klid}'");
			}
			//$cashier_setup_link="https://for16.ru/d/$client_ctrl_dir/cashier_setup.php?u=$cashier_setup_direct_code";

			$path="/var/www/vlav/data/www/wwl/d/$client_ctrl_dir";
			$path_from="/var/www/vlav/data/www/wwl/d/1000";
			if(!file_exists("$path/cashier_setup.php")) {
				copy("$path_from/cashier_setup.php","$path/cashier_setup.php");
				copy("$path_from/cashier.php","$path/cashier.php");
			}
			$this->set_prefix('promo');
			$this->set_fee(15);
			$this->set_discount(10);

			$this->connect('vkt');
			$client_tm_end=$this->tm_end_licence($client_ctrl_id) + ($this->trial_days*24*60*60);
			$this->query("UPDATE 0ctrl SET tm_end='$client_tm_end' WHERE id='$client_ctrl_id'");

			$this->connect($client_database);
			$data = [
				'cashier_klid' => $this->cashier_klid,
				'cashier_setup_klid' => $this->cashier_setup_klid,
				'product_id' => $this->product_id,
				'cmd'=>'',
				'vkt_send_id_msg1' => $this->vkt_send_id_msg1,
				'vkt_send_id_msg2' => $this->vkt_send_id_msg2,
				'vkt_send_id_msg3' => $this->vkt_send_id_msg3,
				'land_num_1' => $this->land_num_1,
				'land_num_2' => $this->land_num_2,
				'land_num_3' => $this->land_num_3,
			];
			if(!$this->set_init_pars($data))
				return false;
			//print "<p class='alert alert-success' >Настройки сохранены</p>";
			return $client_ctrl_id;
		}
		return false;
	}
	function check_yclients($ctrl_id) {
		return $this->ctrl_tool_get($ctrl_id,'yclients','salon_id');
	}
	function set_yclients_withdraw_cashback($checked) {
		$this->ctrl_tool_set(false,'cashier','yclients_withdraw_cashback',$checked);
	}
	function get_yclients_withdraw_cashback() {
		$res=$this->ctrl_tool_get(false,'cashier','yclients_withdraw_cashback');
		if($res===false) {
			$this->set_yclients_withdraw_cashback(1);
			return 1;
		}
		return $res;
	}
}
?>
