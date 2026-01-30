<?
include "/var/www/vlav/data/www/wwl/inc/cp.1.inc.php";
exit;

include "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
include "/var/www/vlav/data/www/wwl/inc/cp.class.php";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";

include "init.inc.php";

class cp_top extends top {
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
	function menu_setup_add() {
		//print "<a class='dropdown-item' href='lk/cabinet.php' class='' target='_blank'>Кабинет партнера</a>";
		print "<a class='dropdown-item' href='billing_pay.php' class='' target='_blank'>Оплатить доступ</a>";
		if($_SESSION['access_level']<=3) {
			print "<a class='dropdown-item' href='export.php' class='' target='_blank'>Экспорт данных</a>";
		}
	}
	function menu_add() {
	}
	function users_report_day($user_id,$tm,$days,$min_dur=60+30) {
		return;
	}
	function top_info() {
		return;
	}
}
$menu=( isset($_GET['getinfo']) || isset($_GET['do_getinfo']) || isset($_GET['chk_grp_ops']) || isset($_GET['do_chk_grp_ops'])  )?false:true;
$t=new cp_top(false,"80%",$menu,$favicon,$ask_passw=true,$gid,$admin_uid);
$t->prices=false;
$t->title="CRM";
$t->top_run($database,true);

if($insales_id) {
	//~ $db=$t;
	//~ $res=$db->query("SELECT cards.uid AS uid,mob_search,email,name,surname FROM cards
		//~ LEFT JOIN cards2other ON cards.uid = cards2other.uid
		//~ WHERE cards.del = 0 AND cards2other.uid IS NULL
		 //~ LIMIT 10");
	//~ if($db->num_rows($res)) {
		//~ include_once "/var/www/vlav/data/www/wwl/inc/insales.class.php";
		//~ $in=new insales($insales_id,$insales_shop);
		//~ if($ctrl_id==167) {
			//~ $in->id_app="winwinland_demo_11";
			//~ $in->secret_key='e5697c177c0f51497d069969e170dbcb';
			//~ $in->get_credentials();
		//~ }
		//~ while($r=$db->fetch_assoc($res)) {
			//~ $uid=$r['uid'];
			//~ if(!$client_id=$in->search_client([$r['mob_search'],$r['email']],$per_page=100)['id']) {
				//~ $client_id=$in->create_client($r['name']." ".$r['surname'], $r['mob_search'], $r['email'], $password = null)['id'];
			//~ }
			//~ if($client_id)
				//~ $db->query("INSERT INTO cards2other SET uid='$uid',tool_uid='$client_id',tool='insales'");
		//~ }
	//~ }
}

class dcp extends cp {
	function init() {
		if(!isset($_SESSION['page']))
			$_SESSION['page']=0;
		if(!isset($_SESSION['per_page']))
			$_SESSION['per_page']=50;
		if(isset($_GET['page'])) {
			$_SESSION['page']=intval($_GET['page'])-1;
			if($_SESSION['page']<0)
				$_SESSION['page']=0;
			$_GET['view']="yes";
		}
		if(@$_POST['uid'])
			$_GET['uid']=intval($_POST['uid']);
		if(!isset($_SESSION['uid']))
			$_SESSION['uid']=0;
		if(!isset($_SESSION['filter_sess']))
			$_SESSION['filter_sess']="C";
		if(!isset($_SESSION['filter_sess_arr']['razdel']))
			$_SESSION['filter_sess_arr']['razdel']=array();
		if(!isset($_SESSION['filter_sess_arr']['land']))
			$_SESSION['filter_sess_arr']['land']=array();
		if(!isset($_SESSION['filter_sess_arr']['tags']))
			$_SESSION['filter_sess_arr']['tags']=array();
		if(!isset($_SESSION['filter_sess_arr']['mans']))
			$_SESSION['filter_sess_arr']['mans']=array();
		if(isset($_GET['filter'])) {
			if($_GET['filter']=='new')
				print "<script>location='dash.php'</script>";
			if(preg_match("/razdel_(\d+)/",$_GET['filter'],$m)) {
				$razdel_id=intval($m[1]);
				if(isset($_SESSION['filter_sess_arr']['razdel'])) {
					if(!in_array($razdel_id,$_SESSION['filter_sess_arr']['razdel']))
						$_SESSION['filter_sess_arr']['razdel'][]=$razdel_id;
				} else
					$_SESSION['filter_sess_arr']['razdel'][]=$razdel_id;
			}
			elseif(preg_match("/land_(\d+)/",$_GET['filter'],$m)) {
				$land_id=intval($m[1]);
				if(isset($_SESSION['filter_sess_arr']['land'])) {
					if(!in_array($land_id,$_SESSION['filter_sess_arr']['land']))
						$_SESSION['filter_sess_arr']['land'][]=$land_id;
				} else
					$_SESSION['filter_sess_arr']['land'][]=$land_id;
			}
			elseif(preg_match("/tag_(\d+)/",$_GET['filter'],$m)) {
				$tag_id=intval($m[1]);
				if(isset($_SESSION['filter_sess_arr']['tags'])) {
					if(!in_array($tag_id,$_SESSION['filter_sess_arr']['tags']))
						$_SESSION['filter_sess_arr']['tags'][]=$tag_id;
				} else
					$_SESSION['filter_sess_arr']['tags'][]=$tag_id;
			}
			elseif(preg_match("/man_(\d+)/",$_GET['filter'],$m)) {
				$man_id=intval($m[1]);
				if(isset($_SESSION['filter_sess_arr']['mans'])) {
					if(!in_array($man_id,$_SESSION['filter_sess_arr']['mans']))
						$_SESSION['filter_sess_arr']['mans'][]=$man_id;
				} else
					$_SESSION['filter_sess_arr']['mans'][]=$man_id;
			}
			else
				$_SESSION['filter_sess']=$_GET['filter']; 
			$_SESSION['page']=0;
		} else
			$_GET['filter']=$_SESSION['filter_sess'];

		if(isset($_GET['filter_arr_clr'])) {
			if(isset($_GET['razdel_id'])) {
				$razdel_id=intval($_GET['razdel_id']);
				unset($_SESSION['filter_sess_arr']['razdel'][array_search($razdel_id, $_SESSION['filter_sess_arr']['razdel'])]);
				$_GET['view']="yes";
			}
			if(isset($_GET['land_id'])) {
				$land_id=intval($_GET['land_id']);
				unset($_SESSION['filter_sess_arr']['land'][array_search($land_id, $_SESSION['filter_sess_arr']['land'])]);
				$_GET['view']="yes";
			}
			if(isset($_GET['man_id'])) {
				$man_id=intval($_GET['man_id']);
				unset($_SESSION['filter_sess_arr']['mans'][array_search($man_id, $_SESSION['filter_sess_arr']['mans'])]);
				$_GET['view']="yes";
			}
			if(isset($_GET['tag_id'])) {
				$tag_id=intval($_GET['tag_id']);
				unset($_SESSION['filter_sess_arr']['tags'][array_search($tag_id, $_SESSION['filter_sess_arr']['tags'])]);
				$_GET['view']="yes";
			}
		}

		if(!isset($_SESSION['filter_by_sources']))
			$_SESSION['filter_by_sources']=0;
		if(isset($_GET['set_filter_by_sources'])) {
			$_SESSION['filter_by_sources']=intval($_GET['filter_by_sources']);
			$_SESSION['page']=0;
		}

		if(!isset($_SESSION['filter_by_touching']))
			$_SESSION['filter_by_touching']=0;
		if(isset($_GET['set_filter_by_touching'])) {
			$_SESSION['filter_by_touching']=intval($_GET['filter_by_touching']);
			$_SESSION['page']=0;
		}

		if(isset($_GET['uid'])) {
			$_SESSION['uid']=intval($_GET['uid']);
			if(isset($_GET['follow'])) {
			}
		}
		if(!is_numeric($_SESSION['uid']))
			$_SESSION['uid']=0;
		$this->uid=intval($_SESSION['uid']);
		$this->cardid=$this->dlookup("id","cards","uid=".$this->uid);
		$this->filter=$_SESSION['filter_sess'];
		$this->access_level=$this->userdata['access_level'];
		$this->query_new=$this->query_new();
		$this->correction();
		if(@$_GET['mark_read']) {
			$this->query("UPDATE cards SET fl_newmsg=0 WHERE uid=$this->uid");
			unset($_GET['uid']);
			$_GET['view']="yes";
		}
		if(isset($_GET['mark_read_one'])) {
			$uid=intval($_GET['uid']);
			$this->mark_new($uid,0);
			$this->query("UPDATE cards SET tm_delay=0 WHERE uid='$uid'",0);
			$_GET['view']="yes";
		}
		//~ if(isset($_GET['to_tomorrow'])) {
			//~ $this->mark_new(intval($_GET['uid']),0);
			//~ $tm=$this->dt1(time()+(24*60*60));
			//~ $uid=intval($_GET['uid']);
			//~ $this->query("UPDATE cards SET tm_delay='$tm' WHERE uid='$uid'");
		//~ }
	}
	function menu() {
		print "<nav class='navbar navbar-expand-sm bg-light'>"; //navbar-default
		if(isset($_GET['clr_userid_filter'])) {
			$_SESSION['userid_filter']=0;
			print "<script>location='cp.php?view=yes&filter=last_10'</script>";
		}
		if(isset($_GET['set_userid_filter'])) {
			$_SESSION['userid_filter']=intval($_GET['userid_filter']);
			print "<script>location='cp.php?view=yes&filter=last_10'</script>";
		}
			//~ print "<div class='navbar-header'>
					//~ <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#myNavbar'>
						//~ <span class='navbar-toggler-icon'></span>
					//~ </button>
				//~ </div>";
		if(@$_GET['str'])
			$str=$_GET['str']; else $str="";
		if(isset($_GET['add_new'])) {
			$this->add_new();
		}
		print "<ul class='nav nav-tabs'>";
			print "<li class='nav-item'>
				<form class='form-inline'>
				<input class='form-control mr-sm-2'  type='text' name='str' value='$str' style='width:100px;'>
				<input type='hidden' name='view' value='yes'>
				<button  class='btn btn-info btn-sm' type='submit' name='filter' value='Search'>Поиск</button>
				</form>
			</li>";
			if($_SESSION['access_level']<5)
				print "<li class='nav-item'>
						<a href='javascript:wopen(\"?add_new=yes\")' class='nav-link btn btn-light btn-sm mx-2'  title='Добавить нового'>Новый</a>
					</li> ";

			print "<li class='nav-item dropdown'>
						<a class='nav-link dropdown-toggle' data-toggle='dropdown' href='#'>Этапы</span>
							<span class='caret'></span>
						</a>
			<div class='dropdown-menu'>";
			$res=$this->query("SELECT * FROM razdel WHERE del=0 AND id>0 AND razdel_name NOT LIKE '-%' ORDER BY razdel_num,razdel_name");
			while($r=$this->fetch_assoc($res)) {
				$c="";
	 			$s=$this->get_style_by_razdel($r['id']);
				print "<a class='dropdown-item' style='$s' href='?view=yes&filter=razdel_{$r['id']}'>
							{$r['razdel_name']}
						</a>";
			}
			print "</div></li>";

			print "<li class='nav-item dropdown'>
						<a class='nav-link dropdown-toggle' data-toggle='dropdown' href='#'>Лэндинги</span>
							<span class='caret'></span>
						</a>
			<div class='dropdown-menu'>";
			//~ print "<a class='dropdown-item' href='?view=yes&filter=1001'>
						//~ Лэндинг_1
					//~ </a>";
			//~ print "<a class='dropdown-item' href='?view=yes&filter=1002'>
						//~ Лэндинг ПАРТНЕРСКИЙ
					//~ </a>";
			$res=$this->query("SELECT * FROM lands WHERE del=0 ORDER BY id");
			while($r=$this->fetch_assoc($res)) {
				$land_id=1000+$r['land_num'];
				$c="";
				print "<a class='dropdown-item' href='?view=yes&filter=land_$land_id'>
							{$r['land_name']} ({$r['land_num']})
						</a>";
			}
			print "</div></li>";

			print "<li class='nav-item dropdown'>
						<a class='nav-link dropdown-toggle' data-toggle='dropdown' href='#'>Тэги</span>
							<span class='caret'></span>
						</a>
			<div class='dropdown-menu'>";
			//print "<a class='dropdown-item' href='?view=yes&filter=1001'>ВСЕ</a>";
			//~ print "<a class='dropdown-item' href='?view=yes&filter=1002'>
						//~ Лэндинг ПАРТНЕРСКИЙ
					//~ </a>";
			$res=$this->query("SELECT * FROM tags WHERE del=0");
			while($r=$this->fetch_assoc($res)) {
				$tag_id=$r['id'];
				$tag_color=$r['tag_color'];
				$contrast_color=$this->get_contrast_color($tag_color);
				print "<a class='dropdown-item' href='?view=yes&filter=tag_$tag_id' style='background-color:$tag_color; color:$contrast_color;'>
							{$r['tag_name']}
						</a>";
			}
			print "</div></li>";

			print "<li class='nav-item dropdown'>
						<a class='nav-link dropdown-toggle' data-toggle='dropdown' href='#'>Менеджеры</span>
							<span class='caret'></span>
						</a>
			<div class='dropdown-menu'>";
			//~ print "<a class='dropdown-item' href='?view=yes&filter=1001'>
						//~ Лэндинг_1
					//~ </a>";
			$res=$this->query("SELECT *,users.id AS id FROM users JOIN cards on cards.id=klid WHERE cards.del=0 AND (access_level BETWEEN 3 AND 4) AND fl_allowlogin=1 AND users.del=0 AND klid>3 ORDER BY users.id");
			while($r=$this->fetch_assoc($res)) {
				$man_id=$r['id'];
				if($man_id==$this->filter)
					$active="active"; else $active="";
				$c="";
				print "<a class='dropdown-item' href='?view=yes&filter=man_$man_id'>
							".$this->disp_name_cp($r['real_user_name'])."
						</a>";
			}
			print "</div></li>";
			

			$active=($this->filter=="tasks")?"active":"";
			print "<li class='nav-item $active'><a href='?view=yes&filter=tasks' class='nav-link $active' >ЗАДАЧИ <span class='badge badge-info badge-pil'>".$this->cnt_new."</span></a></li>";

			if($this->menu_access("delayed")) {
				$active=($this->filter=="delayed")?"active":"";
				print "<li class='nav-item $active'><a class='nav-link $active' href='?view=yes&filter=delayed' title='запланированные контакты'><i class='fa fa-clock-o' ></i></a></li>";
			}

			if($this->menu_access("head_control")) {
				$head_control_cnt=$this->num_rows($this->query("SELECT * FROM head_control WHERE del=0 AND user_id={$_SESSION['userid_sess']}"));
				$active=($this->filter=="head_control")?"active":"";
				print "<li class='nav-item $active'><a class='nav-link $active' href='?view=yes&filter=head_control'><span class='badge' style='background-color:blue;'>$head_control_cnt</span></a></li>";
			}
			if($this->menu_access("all_checked")) {
				$active=($this->filter=="all_checked")?"active":"";
				print "<li class='nav-item $active'><a class='nav-link $active' href='?view=yes&filter=all_checked' title='отмеченные'><i class='fa fa-check-square' ></i></a></li>";
			}
			if($this->menu_access("last_10")) {
				$active=($this->filter=="last_10")?"active":"";
				print "<li class='nav-item $active'><a class='nav-link $active' href='?view=yes&filter=last_10' title='Все контакты - ИСТОРИЯ'>ВСЕ</a></li>";
			}
			if($this->menu_access("scheduled")) {
				$active=($this->filter=="scheduled")?"active":"";
				print "<li class='nav-item $active'><a class='nav-link $active' href='?view=yes&filter=scheduled' title='Записаны на мероприятия'><i class='fa fa-calendar' ></i></a></li>";
			}
			if($this->menu_access("phone_presents")) {
				$active=($this->filter=="phone_presents")?"active $active":"";
				//print "<li class='$active'><a href='?view=yes&filter=phone_presents'>Тел</a></li>";
			}
			if($this->menu_access("calls")) {
				$active=($this->filter=="calls")?"active":"";
	//			print "<li class='$active'><a href='?view=yes&filter=calls'>Звонки</a></li>";
			}
			if($this->menu_access("anketa")) {
				$active=($this->filter=="anketa")?"active":"";
	//			print "<li class='$active'><a href='?view=yes&filter=anketa'>АНКЕТЫ</a></li>";
			}
			if($this->menu_access("week0_started")) {
				$active=($this->filter=="week0_started")?"active":"";
				//print "<li class='$active'><a href='?view=yes&filter=week0_started'>НА ПРОБНЫХ</a></li>";
			}
			if($this->menu_access("week0_finished")) {
				$active=($this->filter=="week0_finished")?"active":"";
				//print "<li class='$active'><a href='?view=yes&filter=week0_finished'>Пробные закончились</a></li>";
			}
			if($this->menu_access("special_2")) {
				$active=($this->filter=="special_2")?"active":"";
				print "<li class='nav-item $active'><a class='nav-link $active' href='?view=yes&filter=special_2'>S1</a></li>";
			}
			if($this->menu_access("partners_only")) {
				$active=($this->filter=="partners_only")?"active":"";
				print "<li class='nav-item $active'><a class='nav-link $active' href='?view=yes&filter=partners_only' title='только партнеры'>🙋‍♀️</a></li>";
			}
			if($this->menu_access("by_sources")) {
				$active=($this->filter=="by_source_id")?"active":"";
				print "<li class='nav-item $active $active'>
					<form>
						<select name='filter_by_sources'>
						";
						print "<option value='0'>=не установлено=</option>";
						$res=$this->query("SELECT * FROM sources WHERE del=0 AND id>0 AND access_level>={$_SESSION['access_level']} ORDER BY id");
						while($r=$this->fetch_assoc($res)) {
							$sel=($r['id']==$_SESSION['filter_by_sources'])?"SELECTED":"";
							print "<option value='{$r['id']}' $sel>({$r['id']}) {$r['source_name']}</option>";
						}
				print 	"</select>
						<input type='hidden' name='filter' value='by_sources'>
						<input type='hidden' name='view' value='yes'>
						<button type='submit' class='btn btn-sm btn-info' name='set_filter_by_sources' value='yes'>Set</button>
					</form>
					</li>";
			}
			if($this->menu_access("by_touching")) {
				$active=($this->filter=="by_touching")?"active":"";
				print "<li class='nav-item $active $active'>
					<form>
						<select name='filter_by_touching'>
						";
						print "<option value='0'>=ВСЕ=</option>";
						$sel=$_SESSION['filter_by_touching']==1?'SELECTED':'';
						print "<option value='1' $sel>🔶НОВЫЕ</option>";
						$res=$this->query("SELECT * FROM sources WHERE for_touch=1 AND access_level>={$_SESSION['access_level']} ORDER BY id");
						while($r=$this->fetch_assoc($res)) {
							$sel=($r['id']==$_SESSION['filter_by_touching'])?"SELECTED":"";
							print "<option value='{$r['id']}' $sel>({$r['id']}) {$r['source_name']}</option>";
						}
				print 	"</select>
						<input type='hidden' name='filter' value='by_touching'>
						<input type='hidden' name='view' value='yes'>
						<button type='submit' class='btn btn-sm btn-info' name='set_filter_by_touching' value='yes'>Последнее касание</button>
					</form>
					</li>";
			}
			print $this->menu_additems();
			if($this->menu_access("cnt_active")) {
				$active=($this->filter=="cnt_active")?"active":"";
				print "<li class='nav-item $active'><a class='nav-link $active' href='?view=yes&filter=cnt_active'>⭐АКТИВНЫЕ⭐</a></li>";
			}
			if($this->menu_access("reg_consult")) {
				$active=($this->filter=="reg_consult")?"active":"";
				print "<li class='nav-item $active'><a class='nav-link $active' href='?view=yes&filter=reg_consult'>🟢 Заявка на консультацию</a></li>";
			}
			if($this->menu_access("did_consult")) {
				$active=($this->filter=="did_consult")?"active":"";
				print "<li class='nav-item $active'><a class='nav-link $active' href='?view=yes&filter=did_consult'>🔴 Проведена консультация</a></li>";
			}
		print "</ul>";
		//print "</div>";
		print "</nav>";
	}
	function view() {
		$access_level=$this->access_level;
		$uid=$this->uid;
		$cardid=$this->cardid;
		$filter=$this->filter;
		$td="";
		$res_scdl=$this->query("SELECT DISTINCT tm_schedule FROM cards WHERE del=0 AND tm_schedule>=".mktime(0,0,0,date("m"),date("d"),date("Y")));
		$colors_shdl=array();
		$n=1;
	//print_r($_SESSION['filter_sess_arr']); print "<br><br>";
		while($r_scdl=$this->fetch_assoc($res_scdl)) {
			$colors_shdl[$r_scdl['tm_schedule']]=$n; $n++;
		}

		$add_razdel=1;
		if(sizeof($_SESSION['filter_sess_arr']['razdel'])>0) {
			$add_razdel="(1=2";
			foreach($_SESSION['filter_sess_arr']['razdel'] AS $razdel_id)
				$add_razdel.=" OR razdel=$razdel_id";
			$add_razdel.=") ";
		}
		$add_land=1;
		$add_1=1;
		if(sizeof($_SESSION['filter_sess_arr']['land'])>0) {
			$add_lands="AND (1=2 ";
			foreach($_SESSION['filter_sess_arr']['land'] AS $land_id) {
				$add_lands.=" OR SUM(msgs.source_id = $land_id) > 0";
			}
			$add_lands.=")";
		}
		$add_tags="";
		if(sizeof($_SESSION['filter_sess_arr']['tags'])>0) {
			$add_tags.="";
			foreach($_SESSION['filter_sess_arr']['tags'] AS $tag_id)
				$add_tags.=" AND SUM(tag_id = $tag_id) > 0";
			$add_tags.=" ";
		}
		$add_mans=1;
		if(sizeof($_SESSION['filter_sess_arr']['mans'])>0) {
			$add_mans="(1=2";
			foreach($_SESSION['filter_sess_arr']['mans'] AS $man_id)
				$add_mans.=" OR man_id=$man_id";
			$add_mans.=") ";
		}
		$add_0="1 ";
		$from=intval($_SESSION['page']*$_SESSION['per_page']);
		//~ if(!isset($from))
			//~ $from="0";
		$order_by="MAX(cards.tm_lastmsg) DESC";
		$tm_lastmsg="cards.tm_lastmsg AS tm_lastmsg";
		if(1) {
			switch($filter) {
				case 'dancers_only':
					$res=$this->query("SELECT *,cards.comm AS comm,cards.id AS id, MAX(ops.tm) AS tm_lastops FROM cards  JOIN ops ON klid=cards.id LEFT JOIN razdel ON razdel.id=cards.razdel WHERE cards.del=0 GROUP BY cards.id  ORDER BY razdel_name ASC, cards.surname ");
					$cnt=$this->num_rows($res);
					//print "<div class='alert alert-success'><h2>Dancers only</h2></div>";
					//$td="date_last_op | cnt_ops | ost_sum (ost_cnt)";
					break;
				case 'Search':
				case 'Поиск':
					$search_str=isset($_GET['str'])?mb_substr(trim($_GET['str']),0,32):"";
					$search_str = (strpos($search_str, '@') === 0) ? substr($search_str, 1) : $search_str;
					if(!empty($search_str)) {
						$str=$this->escape($search_str);
						if($this->database!='vkt') {
							$q="SELECT *,cards.comm AS comm,
								cards.uid AS uid,
								cards.id AS id,
								cards.telegram_id AS telegram_id
							FROM cards
							LEFT JOIN users ON cards.id=klid
							WHERE cards.del=0 AND
							(name LIKE '%$str%'
							OR surname LIKE '%$str%'
							OR cards.uid  LIKE '%$str%'
							OR cards.comm  LIKE '%$str%'
							OR cards.comm1  LIKE '%$str%'
							OR mob_search  LIKE '%$str%'
							OR cards.email  LIKE '%$str%'
							OR telegram_nic LIKE '%$str%'
							OR city  LIKE '%$str%'
							OR bc='$str'
							 ) ORDER BY tm_lastmsg DESC LIMIT $from,{$_SESSION['per_page']}";
							$add_0="(name LIKE '%$str%'
							OR surname LIKE '%$str%'
							OR cards.uid  LIKE '%$str%'
							OR cards.comm  LIKE '%$str%'
							OR cards.comm1  LIKE '%$str%'
							OR mob_search  LIKE '%$str%'
							OR cards.email  LIKE '%$str%'
							OR telegram_nic LIKE '%$str%'
							OR city  LIKE '%$str%'
							OR bc='$str'
							 )";
						} else {
							$insales=is_numeric($str) ? "OR insales_shop_id  = '$str'" : "";
							$q="SELECT *,cards.comm AS comm,
								cards.uid AS uid,
								cards.id AS id,
								cards.telegram_id AS telegram_id
							FROM cards
							LEFT JOIN users ON cards.id=klid
							LEFT JOIN 0ctrl ON cards.uid=0ctrl.uid
							WHERE cards.del=0 AND
							(name LIKE '%$str%'
							OR surname LIKE '%$str%'
							OR cards.uid  LIKE '%$str%'
							OR cards.comm  LIKE '%$str%'
							OR cards.comm1  LIKE '%$str%'
							OR mob_search  LIKE '%$str%'
							OR cards.email  LIKE '%$str%'
							OR telegram_nic LIKE '%$str%'
							OR city  LIKE '%$str%'
							OR bc='$str'
							$insales
							 ) ORDER BY tm_lastmsg DESC LIMIT $from,{$_SESSION['per_page']}";
							$add_0="(name LIKE '%$str%'
							OR surname LIKE '%$str%'
							OR cards.uid  LIKE '%$str%'
							OR cards.comm  LIKE '%$str%'
							OR cards.comm1  LIKE '%$str%'
							OR mob_search  LIKE '%$str%'
							OR cards.email  LIKE '%$str%'
							OR telegram_nic LIKE '%$str%'
							OR city  LIKE '%$str%'
							OR bc='$str'
							$insales
							 )";
						}
						$res=$this->cp_query($q,0); 
						$cnt=$this->num_rows($res);
					} else 
						print "<script>location='cp.php?view=yes&uid=$uid&follow=yes#r_$cardid'</script>";
					//print "<div class='alert alert-info'><h2>Search : $str <span class='badge'>".$this->num_rows($res)."</span></h2></div>";
					break;
				case 'tasks':
					$add_0="dont_disp_in_new=0 AND (fl_newmsg>0 OR razdel=0 OR (tm_delay<".time()." AND tm_delay>0))";
					$order_by="tm_delay DESC, fl_newmsg DESC, tm_lastmsg DESC";
					//~ AND dont_disp_in_new=0
						//~ $and_user
						//~ $and_man
						//~ AND (fl_newmsg>0 OR razdel=0 OR 
								//~ (tm_delay<".time()." AND tm_delay>0)) 
					//~ ORDER BY tm_delay DESC, fl_newmsg DESC, tm_lastmsg DESC";
					//$fl=$_SESSION['userid_sess']==1?1:0;
					//$res=$this->cp_query($this->query_new."  LIMIT $from,{$_SESSION['per_page']}",0);
					//$cnt=$this->cnt_new;
					//print "<div class='alert alert-info'><h2>New messages <span class='badge'>".$this->cnt_new."</span></h2></div>";
					break;
				case 'all_checked':
					$add_0="fl=1";
					//~ $q="SELECT *,cards.id AS id, cards.del AS del FROM cards WHERE fl=1  ORDER BY id DESC";
					//~ $res=$this->cp_query( $q." LIMIT $from,{$_SESSION['per_page']}");
					//~ $cnt=$this->num_rows($this->cp_query($q));
					//print "<div class='alert alert-success'><h2>All checked <span class='badge'>$cnt</span></h2></div>";
					//$td="date_last_op | cnt_ops | ost_sum (ost_cnt)";
					break;
				case 'last_10':
					$q="SELECT *,cards.id AS id, cards.del AS del FROM cards
									JOIN sources ON sources.id=cards.source_id
									WHERE cards.del=0 AND  tm_lastmsg>0 ORDER BY tm_lastmsg DESC";
					$res=$this->cp_query($q." LIMIT  $from,{$_SESSION['per_page']}"); 
					$cnt=$this->num_rows($this->cp_query($q));
					//print "<div class='alert alert-success'><h2>Last 10 history <span class='badge'>$cnt</span></h2></div>";
					$td="LAST RESULT";
					//unset($_SESSION['filter_sess_arr']);
					break;
				case 'delayed':
					$add_0="tm_delay>0 ";
					$order_by="tm_delay,tm_lastmsg";
					break;
				case 'scheduled':
					$add_0="tm_schedule>0";
					$order_by="tm_schedule, tm_lastmsg";
					//$res=$this->cp_query("SELECT * FROM cards WHERE cards.del=0 AND tm_schedule>0 ORDER BY tm_schedule, tm_lastmsg");  
					//$cnt=$this->num_rows($res);
					print "<p>Отчет на дату : ";
					$res_scdl=$this->query("SELECT tm_schedule, scdl_web_id, COUNT(uid) AS cnt FROM cards
						WHERE del=0 AND tm_schedule>=".mktime(0,0,0,date("m"),date("d"),date("Y"))."
						 GROUP BY tm_schedule,scdl_web_id ORDER BY tm_schedule,scdl_web_id");
					while($r_scdl=$this->fetch_assoc($res_scdl)) {
						print "<span class='badge badge-info' ><a href='cp_reports.php?tm={$r_scdl['tm_schedule']}&dont_disp_menu=yes' target='_blank'><span class='badge' >".date("d/m H:i",$r_scdl['tm_schedule'])."</span></a> <span class='badge' >{$r_scdl['cnt']}</span> {$r_scdl['scdl_web_id']}</span> ";
					}
					print "</p>";
					break;
				case 'all':
					$q="SELECT *,cards.id AS id, cards.del AS del FROM cards WHERE del=0 ORDER BY tm_lastmsg DESC";
					$res=$this->cp_query($q." LIMIT  $from,{$_SESSION['per_page']}"); 
					$cnt=$this->num_rows($this->query($q));
					//print "<div class='alert alert-success'><h2>Last 10 history <span class='badge'>$cnt</span></h2></div>";
					$td="ALL";
					break;
				case 'special_2':
					$q="SELECT *,cards.id AS id, cards.del AS del FROM cards WHERE del=0 AND got_calls!='0' ORDER BY tm_lastmsg DESC ";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'partners_only':
					$add_0="users.klid>0";
					//~ $q="SELECT *,cards.id AS id, cards.del AS del,cards.uid AS uid,cards.comm AS comm,cards.telegram_id AS telegram_id
						//~ FROM cards JOIN users ON users.klid=cards.id
						//~ WHERE cards.del=0 ORDER BY tm_lastmsg DESC ";
					//~ $cnt=$this->num_rows($this->cp_query($q));
					//~ $res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}",0);
					//~ $td="";
					break;
				case 'special_1': //seminar-зачет
					$add_0="(msgs.source_id='13' OR msgs.source_id='16' OR msgs.source_id='77' OR msgs.source_id='50')";
					$order_by="tm_lastmsg DESC";
					$tm_lastmsg="MAX(msgs.tm) AS tm_lastmsg";
					//~ $q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						//~ FROM cards
						//~ JOIN msgs ON cards.uid=msgs.uid
						//~ JOIN sources ON sources.id=cards.source_id
						//~ WHERE cards.del=0 AND (msgs.source_id='13' OR msgs.source_id='16' OR msgs.source_id='77' OR msgs.source_id='50')
						//~ ORDER BY msgs.tm DESC ";
					//~ $cnt=$this->num_rows($this->cp_query($q));
					//~ $res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					//~ $td="";
					break;
				case 'scheduled_not_visited': //seminar-зачет
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 AND (msgs.source_id='15' OR msgs.source_id='14' OR msgs.source_id='53')
						ORDER BY msgs.tm DESC ";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'reg':
					$add_regs=" AND (SUM(msgs.source_id = 12) > 0 OR SUM(msgs.source_id = 39) > 0)";
					$order_by="tm_lastmsg DESC";
					$tm_lastmsg="MAX(msgs.tm) AS tm_lastmsg";
					$tm_lastmsg=" MAX(CASE WHEN msgs.source_id = 12 OR msgs.source_id=39 THEN msgs.tm END) AS tm_lastmsg";
					//~ $q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						//~ FROM cards
						//~ JOIN msgs ON cards.uid=msgs.uid
						//~ JOIN sources ON sources.id=cards.source_id
						//~ WHERE cards.del=0 AND (msgs.source_id='12' OR msgs.source_id='39')
						//~ ORDER BY msgs.tm DESC ";
					//~ $cnt=$this->num_rows($this->cp_query($q));
					//~ //$this->notify_me($cnt);
					//~ $res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					//~ //$this->notify_me($this->num_rows($res));
					//~ $td="";
					break;
				case 'consulted':
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 AND (msgs.source_id='60' OR msgs.source_id='60')
						ORDER BY msgs.tm DESC ";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'by_sources':
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 AND msgs.source_id='{$_SESSION['filter_by_sources']}'
						ORDER BY msgs.tm DESC ";
					//print $q;
					$cnt=$this->num_rows($this->cp_query("SELECT msgs.uid FROM msgs JOIN cards ON cards.uid=msgs.uid WHERE cards.del=0 AND msgs.source_id='{$_SESSION['filter_by_sources']}' GROUP BY msgs.uid"));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'by_touching':
					$sid=$_SESSION['filter_by_touching'];
					if($sid==0) {
						print "<script>location='?filter=last_10&view=yes'</script>";
						exit;
					}
					if($sid==1) {
						$q=$this->cp_qstr("SELECT  cards.uid AS uid
								FROM cards
								WHERE cards.del=0
								");
						$tmp_tbl="tmp_{$_SESSION['userid_sess']}";
						$this->query("CREATE TEMPORARY TABLE IF NOT EXISTS $tmp_tbl AS ($q)");
						$res=$this->query("SELECT uid FROM $tmp_tbl WHERE 1 GROUP BY uid");
						while($r=$this->fetch_assoc($res)) {
							if($uid=$this->dlookup("uid","msgs","uid={$r['uid']} AND
								(
								source_id>300 AND source_id<500 
								)
								"))
								$this->query("DELETE FROM $tmp_tbl WHERE uid='$uid'");
								//$this->query("UPDATE $tmp_tbl SET uid=0 WHERE uid='$uid'");
						}
					} else {
						$q=$this->cp_qstr("SELECT  cards.uid AS uid
								 FROM cards
								JOIN msgs ON cards.uid=msgs.uid
								WHERE  cards.del=0  AND msgs.source_id='$sid'
								");
						$tmp_tbl="tmp_{$_SESSION['userid_sess']}";
						$this->query("CREATE TEMPORARY TABLE IF NOT EXISTS $tmp_tbl AS ($q)");
						$res=$this->query("SELECT uid FROM $tmp_tbl WHERE 1 GROUP BY uid");
						while($r=$this->fetch_assoc($res)) {
							$last_sid=$this->fetch_assoc($this->query("SELECT * FROM $tmp_tbl
								JOIN msgs ON msgs.uid=$tmp_tbl.uid
								WHERE msgs.uid={$r['uid']} AND source_id>300 AND source_id<500
								ORDER BY msgs.tm DESC LIMIT 1"))['source_id'];
							//print "$sid $last_sid uid={$r['uid']} <br>";
							if($last_sid!=$sid) {
								$this->query("DELETE FROM $tmp_tbl WHERE uid='{$r['uid']}'");
								//print "{$r['uid']} DELETED <br>";
							}
						}
					}
					$res=$this->query("SELECT *,cards.id AS id, cards.user_id AS user_id,cards.uid AS uid,
								msgs.tm AS tm_lastmsg
						FROM $tmp_tbl
						JOIN cards ON $tmp_tbl.uid=cards.uid
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 GROUP BY cards.uid",0); //  LIMIT  $from,{$_SESSION['per_page']}
					$cnt=$this->num_rows($this->query("SELECT uid FROM $tmp_tbl"));
					$td="";
					break;
				case 'head_control':
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, head_control.tm AS tm_lastmsg
						FROM cards JOIN head_control ON cards.uid=head_control.uid
						WHERE cards.del=0 AND head_control.del=0 AND head_control.user_id='{$_SESSION['userid_sess']}'
						ORDER BY head_control.tm DESC ";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'clients':
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=msgs.source_id
						WHERE cards.del=0 AND sources.fl_client>0
						GROUP BY cards.uid
						ORDER BY msgs.tm DESC ";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				default:
					//~ $cnt=0;
					//~ $res=false;
					break;
			}
		}

		$q="
			SELECT 
				tm_delay, 
				man_id, 
				cards.email AS email, 
				cards.telegram_id AS telegram_id, 
				vk_id, 
				tm_first_time_opened, 
				wa_allowed, 
				age, 
				tzoffset, 
				source_name, 
				cards.comm AS comm, 
				comm1, 
				name, 
				surname, 
				city, 
				razdel, 
				cards.id AS id, 
				cards.uid AS uid, 
				cards.user_id AS user_id, 
				$tm_lastmsg,
				fl_newmsg,
				fl,
				tm_schedule
			FROM 
				cards
			 LEFT JOIN tags_op ON cards.uid = tags_op.uid
			 JOIN msgs ON cards.uid = msgs.uid
			 JOIN sources ON cards.source_id = sources.id
			 LEFT JOIN users ON cards.id=klid
			WHERE cards.del=0
				AND $add_0 
				AND $add_razdel
				AND $add_mans
			GROUP BY 
				cards.uid
			HAVING 1
				$add_tags
				$add_lands
				$add_regs
			ORDER BY 
				$order_by
			";

	if($_SESSION['userid_sess']==1)	print "<pre>$q</pre>";

		$cnt=$this->num_rows($this->cp_query($q));
		$this->cnt_new=$cnt;
		$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}",0);
		$td="";
		
		print "
		<div>
			<div class='d-inline-flex '>
			";
		print "\n<div class=' m-0 p-0' ><a href='?view=yes&str=$uid&filter=Search#r_$cardid' class='btn btn-success mr-2'>Найти последний контакт</a></div> \n";
		$this->users_filter();
		$this->add_button_after_lastcontact();

		if(isset($_SESSION['filter_sess_arr'])) {
			if (sizeof($_SESSION['filter_sess_arr']['razdel'])>0) { //
				$razdel_name="";
				foreach($_SESSION['filter_sess_arr']['razdel'] AS $razdel_id) {
					$s=$this->get_style_by_razdel($razdel_id);
					$razdel_name.="<span class='mx-1 p-1 rounded'  style='$s; white-space: nowrap;'>
							".$this->dlookup("razdel_name","razdel","del=0 AND id='$razdel_id'")."
							<a href='?filter_arr_clr=yes&razdel_id=$razdel_id' class='' target=''>
								<i class='fa fa-times ml-2' style='color:white; cursor: pointer;' aria-hidden='true' title='Очистить'></i>
							</a>
						</span>
						";
				}
				print "<div class='ml-3 bg-light p-2 text-black rounded' >Фильтр по этапу:$razdel_name</div>";
			}
			if (sizeof($_SESSION['filter_sess_arr']['land'])>0) { //
				$land_name="";
				foreach($_SESSION['filter_sess_arr']['land'] AS $land_id) {
					$land_name.="<span class='mx-1 p-1 rounded bg-info'  style='white-space: nowrap;'>
							_".($land_id-1000)."_<a href='?filter_arr_clr=yes&land_id=$land_id' class='' target=''>
								<i class='fa fa-times ml-0 text-light'  style='cursor: pointer;' aria-hidden='true' title='Очистить'></i>
							</a>
						</span>
						";
				}
				print "<div class='ml-3 bg-light p-2 text-black rounded' >Фильтр по лэндингу:$land_name</div>";
			}
			if (sizeof($_SESSION['filter_sess_arr']['tags'])>0) { //
				$tag_name="";
				foreach($_SESSION['filter_sess_arr']['tags'] AS $tag_id) {
					$tag_color=$this->dlookup("tag_color","tags","del=0 AND id='$tag_id'");
					$fg_color=$this->get_contrast_color($tag_color);
					$tag_name.="<span class='mx-1 p-1 rounded'  style='background-color:$tag_color; color:$fg_color; white-space: nowrap;'>
							".$this->dlookup("tag_name","tags","del=0 AND id='$tag_id'")."
							<a href='?filter_arr_clr=yes&tag_id=$tag_id' class='' target=''>
								<i class='fa fa-times ml-2' style='color:$fg_color; cursor: pointer;' aria-hidden='true' title='Очистить'></i>
							</a>
						</span>
						";
				}
				print "<div class='ml-3 bg-light p-2 text-black rounded' >Фильтр по тэгу:$tag_name</div>";
			}
			if (sizeof($_SESSION['filter_sess_arr']['mans'])>0) { //
				$man_name="";
				foreach($_SESSION['filter_sess_arr']['mans'] AS $man_id)
					$man_name.="<span class='mx-1 p-1 rounded bg-info text-white' style='white-space: nowrap;'>
							".$this->dlookup("real_user_name","users","del=0 AND id='$man_id'")."
							<a href='?filter_arr_clr=yes&man_id=$man_id' class='' target=''>
								<i class='fa fa-times text-secondary ml-2' style='cursor: pointer;' aria-hidden='true' title='Очистить'></i>
							</a>
						</span>
						";
				print "<div class='ml-3 bg-light p-2 text-black rounded' >Фильтр по менеджеру:".$this->disp_name_cp($man_name)."</div>";
			}
		}

		print "
			</div>
		</div>
		";
		
		$last_pagination=$this->pagination($cnt);
		$this->tbl_print($res,$cnt,$filter);
		if($last_pagination)
			$this->pagination($cnt);
		
		if(isset($_GET['uid'])) {
			$klid=$this->dlookup("id","cards","uid=$uid");
			//print "HERE $klid";
			print "<script>location.hash='r_$klid' </script>";
		}
	}
	function cp_qstr($qstr) {
		//return $this->query($qstr,0);
		$userid_filter=(isset($_SESSION['userid_filter']))?intval($_SESSION['userid_filter']):0;
		$user_id=($userid_filter)?$userid_filter:$_SESSION['userid_sess'];
		$access_level=$_SESSION['access_level'];
		//~ if($_SESSION['userid_sess']==1)
			//~ $access_level=1;
		if($access_level==4) { //manager
			if($user_id==-1)
				$user_id=0;
			$user_tm_limit=time()-$this->user_tm_limit;
			//$qstr= str_replace("WHERE cards.del=0","WHERE cards.del=0 AND (cards.user_id='$user_id' OR man_id='$user_id' OR man_id=0) ",$qstr);
			$qstr= str_replace("WHERE cards.del=0","WHERE cards.del=0 AND (cards.user_id='$user_id' OR man_id='$user_id') ",$qstr);
			//print "<pre>$qstr</pre>";
		} elseif($access_level>4) { //partner
			if($user_id==-1)
				$user_id=0;
			$user_tm_limit=time()-$this->user_tm_limit;
			$qstr= str_replace("WHERE cards.del=0","WHERE cards.del=0 AND (cards.user_id='$user_id' OR man_id='$user_id') ",$qstr);
			//print $qstr; exit;
		}
		if($userid_filter>0) {
			$qstr= str_replace("WHERE cards.del=0","WHERE cards.del=0 AND (cards.user_id='$user_id') ",$qstr);
		}
		if($this->database=='vkt') {
			if($_SESSION['userid_sess']==1) {
			//$qstr= str_replace("WHERE","WHERE (cards.del=0 AND man_id='254') AND ",$qstr);
			//$qstr= str_replace("WHERE","WHERE (cards.del=0 AND razdel=32) AND ",$qstr);
			}
		}
		if(1) {
			//$qstr=str_replace("WHERE","WHERE (msgs.source_id=1006) AND ",$qstr);
		}
		//~ if($_SESSION['userid_sess']==1)
			//~ print $qstr;
		return $qstr;
	}
	function add_new() {
		//print "<script>wopen(\"add_new.php\");</script>";
		print "<script>location='add_new.php';</script>";
	}
	function query_new_() {
		include "query_new.inc.php";
		return $query_new;
	}
	function tbl_info_($uid,$r,$filter) {
		return;
	}
	function tbl_ctrl($r) {
		return "";
	}
	function menu_access($filter) {
		$allowed=array("A","B","C","D","БАН","all_checked","last_10","reg","partners_only","scheduled","special_1","delayed");
		if(!in_array($filter,$allowed))
			return false;
		return true;
	}
	function menu_additems() {
		$out="";
		if($this->menu_access("clients")) {
			$active=($this->filter=="clients")?"active":"";
			$out.= "<li class='nav-item $active'><a class='nav-link $active' href='?view=yes&filter=clients'>КЛИЕНТЫ</a></li>";
		}
		if($this->menu_access("reg")) {
			$active=($this->filter=="reg")?"active":"";
			$out.= "<li class='nav-item $active'><a class='nav-link $active' href='?view=yes&filter=reg'>⭐РЕГИСТРАЦИИ</a></li>";
		}
		if($this->menu_access("special_1")) {
			$active=($this->filter=="special_1")?"active":"";
			print "<li class='nav-item $active'><a class='nav-link $active' href='?view=yes&filter=special_1' title='Были на вебинаре'><i class='fa fa-coffee' ></i></a></li>";
		}
		if($this->menu_access("scheduled_not_visited")) {
			$active=($this->filter=="scheduled_not_visited")?"active":"";
			print "<li class='nav-item $active'><a class='nav-link $active' href='?view=yes&filter=scheduled_not_visited'>ВЕБИНАР-НЕ БЫЛ</a></li>";
		}
		return $out;
	}
}
$cp=new dcp;
if(@$_GET['add_from_group']) {
}

$cp->userdata=$t->userdata;
$cp->connect($database);
$cp->open_target="new_window";
$cp->open_target_blank="";
$cp->telegram_bot=$TELEGRAM_BOT;

$cp->run();

$t->bottom();



?>
