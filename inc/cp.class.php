<?php
$INC_PATH="/var/www/vlav/data/www/wwl/inc";
include_once ($INC_PATH."/simple_db.inc.php");
include_once "db.class.php";
include "func.inc.php";
include_once "vklist_api.class.php";

class cp extends db {
	var $style_A,$style_B,$style_C,$style_D,$style_O;
	var $uid,$cardid,$filter,$access_level;
	var $cnt_new,$query_new, $user_tm_limit;

	function correction() {
		return;
	//	if($this->database!="vktrade")
	//		return;
		$res=$this->query("SELECT * FROM msgs WHERE msg LIKE '%_blanc%'");
		while($r=$this->fetch_assoc($res)) {
			$new_msg=" Проголосовал(а) в опросе: {$r['vote']}";
			//print "HERE {$r['msg']} $new_msg"; exit;
			$this->query("UPDATE msgs SET msg='$new_msg' WHERE id='{$r['id']}'");
		}
	}
	
	function query_new() {
		$userid_filter=(isset($_SESSION['userid_filter']))?intval($_SESSION['userid_filter']):0;
		if($this->userdata['access_level']>4 || $userid_filter) {
			$user_id=($userid_filter)?$userid_filter:$_SESSION['userid_sess'];
			if($user_id==-1)
				$user_id=0;
			$and_user="AND (user_id='$user_id' OR man_id='$user_id')";
			if($user_id==-1)
				$user_id=0;
		} elseif($this->userdata['access_level']==4 || $userid_filter) {
			$user_id=($userid_filter)?$userid_filter:$_SESSION['userid_sess'];
			if($user_id==-1)
				$user_id=0;
			$and_user="AND (user_id='$user_id' OR man_id='$user_id')";
			if($user_id==-1)
				$user_id=0;
		} else 	
			$and_user="";
		$and_man="";
		if($this->database=='vkt') {
			if($_SESSION['userid_sess']==1) {
				$and_man="AND man_id=254 ";
			}
		}
		$sql="SELECT *, cards.id AS id 
			FROM cards 
			LEFT JOIN sources ON sources.id=cards.source_id
			WHERE cards.del=0 
				AND dont_disp_in_new=0
				$and_user
				$and_man
				AND (fl_newmsg>0 OR razdel=0 OR 
						(tm_delay<".time()." AND tm_delay>0)) 
			ORDER BY tm_delay DESC, fl_newmsg DESC, tm_lastmsg DESC";
	//$this->notify_me("HERE_\n".$sql);
		return $sql;
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
			//print $qstr; exit;
		} elseif($access_level>4) { 
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
	function cp_query($qstr,$fl=0) {
		//if($_SESSION['userid_sess']==1) $fl=1;
		$qstr=$this->cp_qstr($qstr);
		if(strpos($qstr,"LIMIT")===false)
			$this->last_query=$qstr;
	//$this->notify_me($qstr);
		return $this->query($qstr,$fl);
	}
	
	function init() {
	$this->test_microtime(__LINE__);
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
		if(isset($_GET['filter'])) {
			if($_GET['filter']=='new')
				print "<script>location='dash.php'</script>";
			$_SESSION['filter_sess']=$_GET['filter']; 
			$_SESSION['page']=0;
		} else $_GET['filter']=$_SESSION['filter_sess'];

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
				//~ $r=$this->fetch_assoc($this->query("SELECT *,cards.id AS klid FROM cards JOIN razdel ON razdel.id=cards.razdel WHERE uid={$_GET['uid']}",0));
				//~ if($r) {
					//~ $_GET['filter']=$r['razdel_name'];
					//~ $_SESSION['filter_sess']=$_GET['filter']; 
				//~ }
			//print "HERE_{$_GET['uid']}_{$_SESSION['filter_sess']}"; exit;
			}
		}
	$this->test_microtime(__LINE__);
		if(!is_numeric($_SESSION['uid']))
			$_SESSION['uid']=0;
		$this->uid=intval($_SESSION['uid']);
		$this->cardid=$this->dlookup("id","cards","uid=".$this->uid);
		$this->filter=$_SESSION['filter_sess'];
		$this->access_level=$this->userdata['access_level'];
		$this->query_new=$this->query_new();
		$this->correction();
	$this->test_microtime(__LINE__);
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
	$this->test_microtime(__LINE__);
	}
	function menu_additems() {
		return "";
		return "
			<a href='?view=yes&filter=dancers_only'>Dancers only</a> | 
			<a href='?view=yes&filter=scheduled'>Scheduled</a> | 
			";
	}
	function menu_access($filter) {
		if($_SESSION['access_level']>3) {
			$allowed=array();
			if(!in_array($filter,$allowed))
				return false;
		}
		return true;
	}
	function add_new() {
		$_GET['add']="yes";
	}
	
	function users_filter() {
		if($_SESSION['access_level']<=5) {
//			print "S_".$_SESSION['userid_filter'];
			$real_user_name=$_SESSION['userid_filter']?$this->dlookup("real_user_name","users","id='{$_SESSION['userid_filter']}'"):"";
			?>
			  <div class=' m-0 p-0'>
				<form class='form-inline' id='FormUserList'>
				  <div class="form-group m-0 p-0">
					<div class="input-group  m-0 p-0">
					  <input type="text" class="form-control" id="userInput" placeholder="фильтр по партнеру" value='<?=$real_user_name?>'  autocomplete="off">
<!--
					  <div class="input-group-append  m-0 p-0">
						<span class="input-group-text">
						  <i class="fa fa-remove" id="clearIcon"></i>
						</span>
					  </div>
-->
					</div>
					<input type="hidden" id="userID" name="userid_filter"> <!-- Скрытое поле для записи ID -->
					<input type="hidden" name="set_userid_filter" value='yes'>
				  </div>
				  
				  <button type='submit' class='btn btn-light btn-xsm' name='clr_userid_filter' value='yes'><span class='fa fa-remove' title='показать всех'></span></button>
<!--
				  <button type='submit' class='btn btn-info btn-xsm' name='set_userid_filter'><span class='fa fa-filter' title='отфильтровать'></span></button>
-->
				</form>
				<div id="userList" class='m-0 p-0' ></div>
			  </div>
  			<?
		} else {
			?>
			<div class='d-inline-block p-0 m-0'>
				<form class='form-inline' >
				<select name='userid_filter' class='form-control' >
					<?
					include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
					$p=new partnerka(false,$this->database);
					$res=$p->get_all_partners($_SESSION['userid_sess'], 0);
					print "<option value='0' $sel>ВСЕ</option>\n";
					print "<option value='{$_SESSION['userid_sess']}' $sel>ТОЛЬКО СВОИ</option>\n";
					foreach($res AS $user_id=>$r) {
						$sel=($user_id==$_SESSION['userid_filter'])?"SELECTED":"";
						print "<option value='$user_id' $sel>{$r['login']} ({$r['name']})</option>\n";
					}
					?>
				</select>
				<button type='submit' class='btn btn-info btn-xsm' name='set_userid_filter'><span class='fa fa-filter' title='отфильтровать'></span></button>
				</form>
			</div>
			<?
		}
	}

	function users_filter_() {
		if($_SESSION['access_level']<=3) {
//			print "S_".$_SESSION['userid_filter'];
			?>
			<div class='d-inline-block'>
				<form>
				<select class='form-control' style='display:inline;width:200px;'  name='userid_filter'>
					<?
					$res=$this->query("SELECT * FROM users WHERE del=0 AND fl_allowlogin=1 ORDER BY id",0);
					print "<option value='0' $sel>ВСЕ</option>\n";
					print "<option value='-1' $sel>НЕ НАЗНАЧЕННЫЕ</option>\n";
					while($r=$this->fetch_assoc($res)) {
						$sel=($r['id']==$_SESSION['userid_filter'])?"SELECTED":"";
						print "<option value='{$r['id']}' $sel>{$r['username']} ({$r['real_user_name']})</option>\n";
					}
					?>
				</select>
				<button type='submit' class='btn btn-info btn-xsm' name='set_userid_filter'><span class='fa fa-filter' title='отфильтровать' ></span></button>
<!--
				<button type='submit' class='btn btn-info btn-xsm' name='clr_userid_filter'>Clr</button>
-->
				</form>
			</div>
			<?
		} else {
			?>
			<div class='d-inline-block'>
				<form>
				<select name='userid_filter'>
					<?
					include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
					$p=new partnerka(false,$this->database);
					$res=$p->get_all_partners($_SESSION['userid_sess'], 0);
					print "<option value='0' $sel>ВСЕ</option>\n";
					print "<option value='{$_SESSION['userid_sess']}' $sel>ТОЛЬКО СВОИ</option>\n";
					foreach($res AS $user_id=>$r) {
						$sel=($user_id==$_SESSION['userid_filter'])?"SELECTED":"";
						print "<option value='$user_id' $sel>{$r['login']} ({$r['name']})</option>\n";
					}
					?>
				</select>
				<button type='submit' class='btn btn-info btn-xsm' name='set_userid_filter'>Set</button>
				</form>
			</div>
			<?
		}
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
				if($r['razdel_name']==$this->filter)
					$active="active"; else $active="";
				$c="";
				if(1) { //$this->menu_access($r['razdel_name'])) {
					//~ print "<li class='nav-item $active' style='$c'>
							//~ <a class='nav-link $active' href='?view=yes&filter={$r['razdel_name']}'>
								//~ {$r['razdel_name']}
							//~ </a>
						//~ </li>";
					print "<a class='dropdown-item' href='?view=yes&filter=".urlencode($r['razdel_name'])."'>
								{$r['razdel_name']}
							</a>";
				}
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
			$res=$this->query("SELECT * FROM lands WHERE del=0 ORDER BY land_num");
			while($r=$this->fetch_assoc($res)) {
				$land_id=1000+$r['land_num'];
				if($land_id==$this->filter)
					$active="active"; else $active="";
				$c="";
				print "<a class='dropdown-item' href='?view=yes&filter=$land_id'>
							({$r['land_num']}) {$r['land_name']}  
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
	function is_discount_card_visited($uid) {
		return false;
		$r=$this->fetch_row($this->query("SELECT COUNT(uid) FROM opencart_log WHERE uid=$uid"));
		if(!$r)
			return false;
		return $r[0];
	}
	function disp_cnt_disc_card($cnt_disc_card,$last_mess_type,$r) {
		return "<a href='?mark_read_one=yes&uid={$r['uid']}' title='убрать из задач' target=''><i class='fa fa-remove'></i></a>";
		//return false;
		$user_id=$_SESSION['userid_filter'];
		$res=$this->query("SELECT msg,tm FROM msgs WHERE user_id='$user_id' AND uid={$r['uid']} ORDER BY tm",0);
		//$res=$this->query("SELECT msg,tm FROM msgs WHERE imp=10 AND uid={$r['uid']} ORDER BY tm");
		//$r=$this->fetch_assoc($this->query("SELECT COUNT(msgs.id) AS cnt FROM msgs JOIN sources ON sources.id=source_id WHERE imp=10 AND uid={$r['uid']}"));
		$t="";
		while($r=$this->fetch_assoc($res)) {
			$t.=date("d.m.Y H:i",$r['tm'])." ".htmlspecialchars(substr($r['msg'],0,150))."\n";
		}
		//return "<a href='#' data-toggle='popover'  data-trigger='focus' title='Активности' data-content='$t' data-html='true' title='Активности - заходы на лэндинг и т.п.'><span class='badge badge-info'>".$this->num_rows($res)."</span></a>";
		return "<a href='#' title='$t'><span class='badge badge-info bg-white ' style='color:#777;'>".$this->num_rows($res)."</span></a>";
		//return "<span class='badge badge-info' title='$t'>".$this->num_rows($res)."</span>";
		//~ return false;
	
		//~ if(!$cnt_disc_card)
			//~ return "&nbsp;"; 
		//~ $style_of_cnt_disc_card=($last_mess_type==0)?"num_visited_shop_1":"num_visited_shop_0";
		//~ $tooltip_of_cnt_disc_card=($last_mess_type==0)?"Leaved message after shop visited":"Last action is shop visited";
		//~ return "<span class='badge badge-default $style_of_cnt_disc_card'><a href='javascript:wopen_1(\"opencart_log.php?uid={$r['uid']}\")' title='$tooltip_of_cnt_disc_card'>$cnt_disc_card</a></span>";
	}
	function disp_events($cnt_disc_card,$last_mess_type,$r) {
		return false;
		$res=$this->query("SELECT msg,tm FROM msgs WHERE imp=10 AND uid={$r['uid']} ORDER BY tm");
		//$r=$this->fetch_assoc($this->query("SELECT COUNT(msgs.id) AS cnt FROM msgs JOIN sources ON sources.id=source_id WHERE imp=10 AND uid={$r['uid']}"));
		$t="";
		while($r=$this->fetch_assoc($res)) {
			if(($pos=strpos($r['msg'],"\n"))!==false)
				$comm=substr($r['msg'],0,$pos); else $comm="";
			$comm=substr($r['msg'],0,150);
			$t.="<div>".date("d.m.Y H:i",$r['tm'])." ".$comm."</div>";
		}
		//return "<a href='#' data-toggle='popover'  data-trigger='focus' title='Активности' data-content='$t' data-html='true' title='Активности - заходы на лэндинг и т.п.'><span class='badge badge-info'>".$this->num_rows($res)."</span></a>";
		return "$t";
	}
	function add_button_after_lastcontact() {
		return '';
	}

	function run() {
		global $database;
		$this->test_microtime(__LINE__);

		$this->init();
		$this->test_microtime(__LINE__);

		if(!isset($_SESSION['access_level']) || !$_SESSION['access_level']) {
				session_destroy();
				print "<script>location='?logout=yes'</script>";
		}



		if(@$_GET['do_getinfo']) {
			//~ if(strpos($_GET['vk_url'],"https://")===false)
				//~ $url="https://vk.com/".$_GET['vk_url'];
			//~ elseif(preg_match("|^id[0-9]+$|", $_GET['vk_url']))
				//~ $url="https://vk.com/".$_GET['vk_url'];
			//~ elseif(is_numeric($_GET['vk_url']))
				//~ $url="https://vk.com/id".$_GET['vk_url'];
			//~ else
			$url=$_GET['vk_url'];
			$vk=new vklist_api;
			$vk->token=$this->dlookup("token","vklist_acc","del=0 AND last_error=0");
			$uid=trim($vk->get_uid_from_url($url));
			//print "uid=$uid"; exit;
			$info=$vk->vk_get_userinfo($uid);
			if($uid>0 && $info!==false) {
				if(!$this->dlookup("uid","cards","uid=$uid")) {
					//$this->print_r($info);
					//$name=explode(" ",$vk->vk_get_name_by_uid($uid));
					//print "<p>Info from VK for <b>{$_GET['vk_url']}</b> : <b>$uid $name</b></p>";
					//print_r($info);
					if(isset($info['country'])) {
						$country=",".$info['country']['title'];
					} else
						$country="";
					if(isset($info['city'])) {
						$city=$info['city']['title'];
					} else
						$city="";
					print "<div class='card bg-light p-2'>
					<h2><div class='alert alert-info'>Найден:</div></h2>
					<form name='fw'>
					<div class='form-group'><badge for='uid'>ID ВК</badge><input id='uid' class='form-control' type='text' name='uid' value='{$info['id']}'></div>
					<div class='form-group'>
						<badge for='nam'>Имя</badge>
						<input id='nam' type='text' name='name' value='{$info['first_name']}' class='form-control'>
						<input type='text' name='surname' value='{$info['last_name']}' class='form-control'>
					</div>
					<div class='form-group'>
						<badge for='city'>Город</badge>
						<input id='city' type='text' name='city' value='".$city.$country."' class='form-control'>
					</div>
					<input type='button'  class='btn btn-primary' name='b1' value='Принять' 
						onclick='window.opener.document.f1.uid.value=fw.uid.value;
							window.opener.document.f1.name.value=fw.name.value;
							window.opener.document.f1.surname.value=fw.surname.value;
							window.opener.document.f1.city.value=fw.city.value;
							window.close();'>
					<input type='button' class='btn btn-warning' name='b2' value='Отменить' onclick='window.close()'>	
					</form>
					</div>";
				} else
					print "<div class='badge badge-warning'>Уже есть в базе : {$_GET['vk_url']} ! Забейте <b>$uid</b> в поиске</div>
					<input type='button' class='btn btn-primary' name='b2' value='Назад' onclick='history.back()'>	
					";
			} else
				print "<h2><div class='badge badge-warning'>Не могу найти : {$_GET['vk_url']}</div></h2>
				<input type='button' class='btn btn-primary' name='b2' value='Назад' onclick='history.back()'>	
				";
			$t=new top(false);
			$t->bottom();
			exit;
		}
		$this->test_microtime(__LINE__);
		if(@$_GET['getinfo']) {
			print "<div class='card bg-light p-2'>
				<h2><div class='alert alert-info'>Поиск пользователя ВК</div></h2>
				<form class=''>
				<div class='form-group'>
				<badge for='n1'>Введите ID ВК, ник или ссылку на страницу<br>
					<blockquote><div style='font-size:12px;'>Пример: 123456321 или ya_krutoy или https://vk.com/id123456321</div></blockquote>
				</badge>:
				<input id='n1' type='text' name='vk_url' value='' class='form-control'>
				</div>
				<input type='submit'  class='btn btn-primary' name='do_getinfo' value='Искать'>
				<input type='button' class='btn btn-warning' value='Отменить' onclick='window.close()'>	
				</form>
				</div>";
			//$t=new top(false);
			//$t->bottom();
			exit;
		}
		$this->test_microtime(__LINE__);
		if(@$_GET['do_chk_grp_ops']) {
			$this->do_chk_grp_ops();
			exit;	
		}
		$this->test_microtime(__LINE__);
		if(@$_GET['chk_grp_ops']) {
			$this->chk_grp_ops();
			exit;
		}
		$len=strpos($this->query_new,"LIMIT");
		if($len===false)
			$len=strlen($this->query_new);
		$this->cnt_new=$this->num_rows($this->query(substr($this->query_new,0,$len)));
		$this->test_microtime(__LINE__);
		
		$this->menu();
		$this->test_microtime(__LINE__);
		
		if(isset($_GET['view']) || isset($view))
			$this->view();
		$this->test_microtime(__LINE__);
		
		$db=new crd;
		//$db->debug=true;
		$db->charset="utf8mb4";
		$r=$this->get_mysql_env();
		$mysql_user=$r['DB_USER'];
		$mysql_passw=$r['DB_PASSW'];
		$db->connect( $mysql_user, $mysql_passw,$database);
		$db->init_table("cards");
		$db->view_query="SELECT * FROM cards WHERE del=0 ORDER BY surname, tm";
			//function add_field($badge,$key,$val,$type,$w)
			//var $chk=""; //validate fields - non_empty,unicum,date,time
		//~ $fld=$db->add_field("<a href='javascript:wopen(\"cp.php?getinfo=yes\");'>
								//~ <button type='button' class='btn btn-success btn-xs'>Найти в ВК</button>
							//~ </a> или 
							//~ <a href='#' id='get_unicum_uid'>
								//~ <button type='button' class='btn btn-info btn-xs'>Получить уникальный ID</button>
							//~ </a>:","uid","","text",400); $fld->chk="unicum"; $fld->disabled=true;
		//~ $fld=$db->add_field("<a href='javascript:wopen(\"cp.php?getinfo=yes\");'>
								//~ <button type='button' class='btn btn-success'>Найти в ВК</button>
							//~ </a>","","","badge",0); 
		//$fld=$db->add_field("","uid","","text",400); $fld->chk="unicum"; $fld->maxlength=0;
		$fld=$db->add_field("Имя:","name","","text",400); 
		$fld=$db->add_field("Фамилия:","surname","","text",400);
		$fld=$db->add_field("Город:","city","","text",400);
		$fld=$db->add_field("Мобильный:","mob","","text",400);
		$fld=$db->add_field("Email:","email","","text",400);
		$fld=$db->add_field("Этап:","razdel","0","select",400); $fld->rowsource="SELECT id,razdel_name FROM razdel WHERE del=0 ORDER BY razdel_name";
		//$fld=$db->add_field("Источник:","source_id","1","select",400); $fld->rowsource="SELECT id,source_name FROM sources WHERE del=0 ORDER BY id";
		$fld=$db->add_field("Комментарий:","comm","","textarea",400); $fld->style="style='width:400px; height:150px;'";
		if($this->userdata['access_level']<=2)  
			$fld=$db->add_field("Комм 1:","comm1","","textarea",400); $fld->style="style='width:400px; height:80px;'";
		if($this->access_level==1)
			$fld=$db->add_field("Отметить как новое","fl_newmsg",0,"checkbox",40);
		$fld=$db->add_field("tm","tm",time(),"hidden",0);
		if($this->access_level==1)
			$fld=$db->add_field("Никогда не показывать в новых","dont_disp_in_new",0,"checkbox",400);
		$db->run();
		$this->test_microtime(__LINE__);
		$this->jquery();
		$this->test_microtime(__LINE__);

	}
	function jquery() {

		?>
		<script type="text/javascript">
		$(document).on('change', '#manage_checkboxes', function() {
			var selected = $(this).val();
			var ids=$(this).attr('ids');
			var mode;
 		//	console.log("manage_checkboxes "+selected);
 			if(selected=='chk_grp_ops') {
		//		console.log("manage_checkboxes = chk_grp_ops");
				wopen("cp.php?chk_grp_ops=yes");
			}
			//~ if(selected=='set_all_razd') {
				//~ const checkboxes = document.querySelectorAll('input[type=checkbox]');
				//~ ids="";
				//~ checkboxes.forEach((checkbox) => {
				  //~ // Добавьте ваш код для выделения чекбоксов здесь
				  //~ checkbox.checked = true; // Пример: установка состояния checked всех чекбоксов
				  //~ //ids=
				//~ });
				//~ console.log(ids);
			//~ }
			$.ajax({
				type:'POST',
				data: {
					'manage_checkboxes':'yes',
					'mode':selected,
					'ids': ids
				},
				url:'jquery.php',
				success: function(data){
					location.reload();
				}				
			});
		});

		$("input[name='chk1']").change(function(){
			var id=$(this).attr('id');
			if(this.checked)
				var fl=1;  else var fl=0;
			var url='set_fl=yes&id='+id+'&fl='+fl;
		//	console.log(url);
			//setup the ajax call
			$.ajax({
				type:'GET',
				url:'jquery.php',
				data:url
			});
		});
		$("#get_unicum_uid").click(function(){
			var url="get_unicum_uid=yes";
		//	console.log(url);
			$.ajax({
				type:'GET',
				url:'jquery.php',
				data:url,
				success: function(data){
					f1.uid.value=data;
				}				
			});
			
		});

		$(document).ready(function(){
			 $('[data-toggle="popover"]').popover(); 
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



		</script>
		<?
	}
	function pagination($cnt) {
		$pages=intval($cnt/$_SESSION['per_page'])+1;
		if($pages==1)
			return false;
		if($_SESSION['page']>$pages)
			$_SESSION['page']=$pages;
		$p1=$_SESSION['page']+1-5;
		if($p1<1)
			$p1=1;
		$p2=($pages<10)?$pages:($p1+10);
		print "<ul class='pagination'>";
		print "<li class='page-item' ><a href='?page=1' class='page-link' target=''>&lt;&lt; </a></li>";
		for($p=$p1; $p<=$p2; $p++) {
			if($p==$_SESSION['page']+1)
				$a="active"; else $a="";
			if($p<=$pages)
				print "<li class='$a page-item' ><a href='?page=$p' class='page-link $a' target=''>$p</a></li>";
		}
		print "<li class='page-item'><a class='page-link' href='?page=$pages' class='' target=''> &gt;&gt;</a></li>";
		print "</ul>";
		if($_SESSION['page']==$pages-1)
			return false; else return true;
	}
	function view() {
		$access_level=$this->access_level;
		$uid=$this->uid;
		$cardid=$this->cardid;
		$filter=$this->filter;
		$td="";
		$res_scdl=$this->query("SELECT DISTINCT tm_schedule FROM cards WHERE del=0 AND tm_schedule>='".mktime(0,0,0,date("m"),date("d"),date("Y"))."'");
		$colors_shdl=array();
		$n=1;
		while($r_scdl=$this->fetch_assoc($res_scdl)) {
			$colors_shdl[$r_scdl['tm_schedule']]=$n; $n++;
		}

		$r=$this->fetch_assoc($this->query("SELECT * FROM razdel WHERE del=0 AND razdel_name='$filter'"));
		$from=intval($_SESSION['page']*$_SESSION['per_page']);
		if($r) {
			//~ if($r['id']==3)
				//~ $sort="ORDER BY surname,name"; else $sort="ORDER BY tm_delay, tm_lastmsg";
			$sort="ORDER BY tm_lastmsg DESC";
			if($r['id']==1)
				$sort="ORDER BY tm_delay,tm_lastmsg";
 			//$res=$this->cp_query("SELECT * FROM cards WHERE del=0 AND  razdel={$r['id']} AND tm_delay=0 $sort LIMIT 100"); 
 			//print "<div>SELECT * FROM cards WHERE del=0 AND  razdel={$r['id']} $sort LIMIT $from,{$_SESSION['per_page']}</div>";
 			$q="SELECT *,cards.id AS id, cards.del AS del FROM cards
							JOIN sources ON sources.id=source_id
							WHERE cards.del=0 AND  razdel={$r['id']} $sort
							LIMIT $from,{$_SESSION['per_page']}";
			$this->last_query=$q;
 			$res=$this->cp_query($q); 
 			$cnt=$this->num_rows($this->cp_query("SELECT * FROM cards WHERE del=0 AND  razdel={$r['id']}"));
 			$td="INFO";
 			$s=$this->get_style_by_razdel($r['id']);
			//print "<div class='alert' style='$s'><h2>$filter <span class='badge'>$cnt</span></h2></div>";
		//$this->here($r['id']);
 		} elseif($filter>1000 && $filter<2000) { //lands
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						WHERE cards.del=0 AND msgs.source_id='$filter'
						ORDER BY msgs.tm DESC ";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}",0);
					$td="";
		} elseif(preg_match("/tag_(\d+)/",$filter,$m)) { //tags
					$tag_id=intval($m[1]);
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, cards.tm_lastmsg AS tm_lastmsg
						FROM cards
						JOIN tags_op ON cards.uid=tags_op.uid
						WHERE cards.del=0 AND tags_op.tag_id='$tag_id'
						ORDER BY tm_lastmsg DESC ";
					//~ if($_SESSION['userid_sess']==1)
						//~ print "$q";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}",0);
					$td="";
		} elseif(preg_match("/man_(\d+)/",$filter,$m)) { //tags
					$man_id=intval($m[1]);
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, cards.tm_lastmsg AS tm_lastmsg
						FROM cards
						WHERE cards.del=0 AND man_id='$man_id'
						ORDER BY tm_lastmsg DESC ";
					//~ if($_SESSION['userid_sess']==1)
						//~ print "$q";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}",0);
					$td="";
		} else {
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
						}
						$res=$this->cp_query($q,0); 
						$cnt=$this->num_rows($res);
					} else 
						print "<script>location='cp.php?view=yes&uid=$uid&follow=yes#r_$cardid'</script>";
					//print "<div class='alert alert-info'><h2>Search : $str <span class='badge'>".$this->num_rows($res)."</span></h2></div>";
					break;
				case 'tasks':
					$fl=$_SESSION['userid_sess']==1?1:0;
					$res=$this->cp_query($this->query_new."  LIMIT $from,{$_SESSION['per_page']}",0);
					$cnt=$this->cnt_new;
					//print "<div class='alert alert-info'><h2>New messages <span class='badge'>".$this->cnt_new."</span></h2></div>";
					break;
				case 'all_checked':
					$q="SELECT *,cards.id AS id, cards.del AS del FROM cards WHERE cards.del=0 AND fl=1  ORDER BY id DESC";
					$res=$this->cp_query( $q." LIMIT $from,{$_SESSION['per_page']}");
					$cnt=$this->num_rows($this->cp_query($q));
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
					break;
				case 'delayed':
					$q="SELECT *,tm_delay AS tm_lastmsg FROM cards WHERE tm_delay>0 AND del=0 ORDER BY tm_delay";
					$res=$this->cp_query($q." LIMIT  $from,{$_SESSION['per_page']}");  
					$cnt=$this->num_rows($this->cp_query($q));
					//print "<div class='alert alert-danger'><h2>Delayed contacts <span class='badge'>$cnt</span></h2></div>";
					break;
				case 'scheduled':
					//$res=$this->query("SELECT * FROM cards WHERE fl_newmsg=1 AND acc_id!=1 ORDER BY tm_lastmsg DESC");
				//	$res=$this->query("SELECT * FROM cards WHERE tm_schedule>=".$this->dt1(time()-(24*60*60))." ORDER BY tm_schedule, tm_lastmsg");  
					$res=$this->cp_query("SELECT * FROM cards WHERE cards.del=0 AND tm_schedule>0 ORDER BY tm_schedule, tm_lastmsg");  
					$cnt=$this->num_rows($res);
					//print "<div class='alert alert-danger'><h2>Scheduled ($cnt)</h2></div>";
					//print "<p><a href='cp_reports.php?razdel=3&dont_disp_menu=yes' target='_blank'>ALL A</a></p>";
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
				case 'bb_authorized':
					//$res=$this->query("SELECT * FROM cards WHERE fl_newmsg=1 AND acc_id!=1 ORDER BY tm_lastmsg DESC");
					$res=$this->cp_query("SELECT *,msgs.tm AS tm_lastmsg FROM msgs JOIN cards ON msgs.uid=cards.uid WHERE cards.del=0 AND msgs.source_id=8 ORDER BY msgs.tm");  
					$cnt=$this->num_rows($res);
					$td="";
					break;
				case 'anketa':
					$q="SELECT *,cards.id AS id, cards.del AS del FROM cards
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 AND  anketa>0 ORDER BY anketa DESC,tm_lastmsg DESC ";
					//$res=$this->cp_query("SELECT * FROM anketa JOIN cards ON anketa.uid=cards.uid WHERE 1 GROUP BY anketa.uid ORDER BY anketa.tm DESC");  
					$cnt=$this->num_rows($this->query($q));
					//print $q; exit;
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'week0_started':
					$q="SELECT *,cards.id AS id, cards.del AS del,msgs.uid AS uid, source_name, MAX(msgs.tm) AS tm, msgs.source_id AS sid
						FROM msgs
						JOIN cards ON msgs.uid=cards.uid
						JOIN sources ON sources.id=msgs.source_id
						WHERE cards.id NOT IN 
                        	(SELECT cards.id FROM cards 
                            JOIN msgs ON msgs.uid=cards.uid 
                            WHERE msgs.source_id=17)
                        AND (msgs.source_id=16) AND razdel!=3
						GROUP BY msgs.uid 
                        ORDER BY MAX(msgs.tm) DESC";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'week0_finished':
					$q="SELECT *,cards.id AS id, cards.del AS del,msgs.uid AS uid,MAX(msgs.tm) AS tm FROM msgs
						JOIN cards ON msgs.uid=cards.uid
						JOIN sources ON sources.id=msgs.source_id
						WHERE cards.del=0 AND (msgs.source_id=17) AND razdel!=3
						GROUP BY msgs.uid ORDER BY MAX(msgs.tm) DESC";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'calls':
					$q="SELECT *,cards.id AS id, cards.del AS del FROM cards WHERE del=0 AND got_calls>0 ORDER BY tm_lastmsg DESC ";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'phone_presents':
					$q="SELECT *,cards.id AS id, cards.del AS del FROM cards WHERE del=0 AND razdel!=3 AND mob_search!='' AND got_calls='0' ORDER BY tm_lastmsg DESC ";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'special_2':
					$q="SELECT *,cards.id AS id, cards.del AS del FROM cards WHERE del=0 AND got_calls!='0' ORDER BY tm_lastmsg DESC ";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'partners_only':
					$q="SELECT *,cards.id AS id, cards.del AS del,cards.uid AS uid,cards.comm AS comm,cards.telegram_id AS telegram_id
						FROM cards JOIN users ON users.klid=cards.id
						WHERE cards.del=0 ORDER BY tm_lastmsg DESC ";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}",0);
					$td="";
					break;
				case 'special_1': //seminar-зачет
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 AND (msgs.source_id='13' OR msgs.source_id='16' OR msgs.source_id='77' OR msgs.source_id='50')
						ORDER BY msgs.tm DESC ";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
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
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 AND (msgs.source_id='12' OR msgs.source_id='39')
						ORDER BY msgs.tm DESC ";
					$cnt=$this->num_rows($this->cp_query($q));
					//$this->notify_me($cnt);
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					//$this->notify_me($this->num_rows($res));
					$td="";
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
				case 'spine':
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 AND msgs.source_id='52'
						ORDER BY msgs.tm DESC ";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'spine_report':
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 AND msgs.source_id='54'
						ORDER BY msgs.tm DESC ";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'marafon':
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 AND msgs.source_id='29'
						ORDER BY msgs.tm DESC ";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case '7praktik':
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 AND msgs.source_id='26'
						ORDER BY msgs.tm DESC ";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'cnt_active':
					$q="SELECT * FROM cards WHERE cards.del=0 AND cnt_active>0 ORDER BY tm_last_active DESC";
					$cnt=$this->num_rows($this->cp_query($q));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'reg_consult':
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 AND msgs.source_id='413' AND cards.razdel=2
						ORDER BY msgs.tm DESC ";
					$cnt=$this->num_rows($this->cp_query("SELECT msgs.uid FROM msgs JOIN cards ON cards.uid=msgs.uid
						WHERE cards.del=0 AND msgs.source_id='413' AND cards.razdel=2 GROUP BY msgs.uid"));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'did_consult':
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 AND msgs.source_id='306'
						ORDER BY msgs.tm DESC ";
					$cnt=$this->num_rows($this->cp_query("SELECT msgs.uid FROM msgs JOIN cards ON cards.uid=msgs.uid
						WHERE cards.del=0 AND msgs.source_id='306' GROUP BY msgs.uid"));
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
				case 'clients_sleep':
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 AND (msgs.source_id='130' OR msgs.source_id='131' OR msgs.source_id='132')
						ORDER BY msgs.tm DESC ";
					$cnt=$this->num_rows($this->cp_query("SELECT msgs.uid FROM msgs JOIN cards ON cards.uid=msgs.uid
						WHERE cards.del=0 AND (msgs.source_id='130' OR msgs.source_id='131' OR msgs.source_id='132') GROUP BY msgs.uid"));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'reg_sleep':
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 AND msgs.source_id='110'
						ORDER BY msgs.tm DESC ";
					$cnt=$this->num_rows($this->cp_query("SELECT msgs.uid FROM msgs JOIN cards ON cards.uid=msgs.uid
						WHERE msgs.source_id='110' GROUP BY msgs.uid"));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'web_ok_sleep':
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 AND msgs.source_id='125'
						ORDER BY msgs.tm DESC ";
					$cnt=$this->num_rows($this->cp_query("SELECT msgs.uid FROM msgs JOIN cards ON cards.uid=msgs.uid
						WHERE cards.del=0 AND msgs.source_id='125' GROUP BY msgs.uid"));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'web_short_sleep':
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 AND msgs.source_id='126'
						ORDER BY msgs.tm DESC ";
					$cnt=$this->num_rows($this->cp_query("SELECT msgs.uid FROM msgs JOIN cards ON cards.uid=msgs.uid
						WHERE cards.del=0 AND msgs.source_id='126' GROUP BY msgs.uid"));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'reg_yoga':
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 AND msgs.source_id='140'
						ORDER BY msgs.tm DESC ";
					$cnt=$this->num_rows($this->cp_query("SELECT msgs.uid FROM msgs JOIN cards ON cards.uid=msgs.uid
						WHERE cards.del=0 AND msgs.source_id='140' GROUP BY msgs.uid"));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'web_ok_yoga':
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 AND msgs.source_id='141'
						ORDER BY msgs.tm DESC ";
					$cnt=$this->num_rows($this->cp_query("SELECT msgs.uid FROM msgs JOIN cards ON cards.uid=msgs.uid
						WHERE cards.del=0 AND msgs.source_id='141' GROUP BY msgs.uid"));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				case 'web_short_yoga':
					$q="SELECT *,cards.id AS id, cards.del AS del, cards.user_id AS user_id, msgs.tm AS tm_lastmsg
						FROM cards
						JOIN msgs ON cards.uid=msgs.uid
						JOIN sources ON sources.id=cards.source_id
						WHERE cards.del=0 AND msgs.source_id='142'
						ORDER BY msgs.tm DESC ";
					$cnt=$this->num_rows($this->cp_query("SELECT msgs.uid FROM msgs JOIN cards ON cards.uid=msgs.uid
						WHERE cards.del=0 AND msgs.source_id='142' GROUP BY msgs.uid"));
					$res=$this->cp_query($q."  LIMIT  $from,{$_SESSION['per_page']}");
					$td="";
					break;
				default:
					$cnt=0;
					$res=false;
					break;
			}
		}
		
		print "
		<div>
			<div class='d-inline-flex '>
			";
		print "\n<div class=' m-0 p-0' ><a href='?view=yes&str=$uid&filter=Search#r_$cardid' class='btn btn-success mr-2'>Найти последний контакт</a></div> \n";
		$this->users_filter();
		$this->add_button_after_lastcontact();

		if($filter>1000 && $filter<2000) {
			$land_num=intval($this->filter-1000);
			$land_name=trim($this->dlookup("land_name","lands","land_num=$land_num"));
			print "<span class='ml-5 bg-warning p-2 text-black rounded' >Фильтр по лэндингу: <b>$land_name</b></span>";
		} elseif ($this->dlookup("id","razdel","razdel_name='$filter'")) {
			print "<span class='ml-5 bg-warning p-2 text-black rounded' >Фильтр по этапу: <b>$filter</b></span>";
		} elseif (preg_match("/tag_(\d+)/",$filter,$m)) { //tags
			$tag_id=intval($m[1]);
			$tag_name=$this->dlookup("tag_name","tags","id='$tag_id'");
			$tag_color=$this->dlookup("tag_color","tags","id='$tag_id'");
			$fg_color=$this->get_contrast_color($tag_color);
			print "<span class='ml-5 bg-warning p-2 text-black rounded' >Фильтр по тэгу: <span class='p-1 rounded'  style='background-color:$tag_color; color:$fg_color;'><b>$tag_name</b></span></span>";
		} elseif (preg_match("/man_(\d+)/",$filter,$m)) { //
			$man_id=intval($m[1]);
			$man_name=$this->dlookup("real_user_name","users","id='$man_id'");
			print "<span class='ml-5 bg-warning p-2 text-black rounded' >Фильтр по менеджеру: <span class='p-1 rounded bg-info text-white'  style=''><b>".$this->disp_name_cp($man_name)."</b></span></span>";
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
	function chk_grp_ops() {

		if($_SESSION['access_level']>3) {
			print "<p class='alert alert-info' >ACCESS DENIED</p>";
			return;
		}

		print "<p><a href='javascript:window.close()' class='btn btn-warning btn-sm mb-3' target=''>Закрыть</a></p>";

		$res=$this->query("SELECT uid FROM cards WHERE cards.del=0 AND fl=1");
		$cnt=$this->num_rows($res);
		print "<h2><div class='alert alert-info'>Груповые операции с отмеченными ($cnt)</div></h2>";
		
		print "<div class='card bg-light p-2'>
				<h3>Передать партнеру:</h3>
				<form class='' action=''>
				  <div class='form-group'>
					<select name='assign_to_user'>";
			print "<option value='0'>=выберите=</option>";
			print "<option value='-1'>-удалить привязку-</option>";
		if($this->database!='vkt')
			$res=$this->query("SELECT * FROM users WHERE  del=0 AND id>3 AND fl_allowlogin=1 ORDER BY access_level DESC, username ASC");
		else
			$res=$this->query("SELECT * FROM users WHERE  del=0 AND id>0 AND fl_allowlogin=1 ORDER BY access_level DESC, username ASC");
		while($r=$this->fetch_assoc($res)) {
			print "<option value='{$r['id']}'>{$r['username']} {$r['real_user_name']}</option>";
		}			
		print "			</select>
					  <button type='submit' class='btn btn-primary' name='do_chk_grp_ops' value='yes'>Выполнить</button>
				</div></form>";

		print "</div>";

		print "<div class='card bg-light p-2'>
				<h3>Назначить менеджеру:</h3>
				<form class='' action=''>
				  <div class='form-group'>
					<select name='assign_to_man'>";
			print "<option value='0'>Удалить привязку к менеджеру</option>";
		$res=$this->query("SELECT * FROM users WHERE  del=0 AND fl_allowlogin=1 AND (access_level=3 OR access_level=4) ORDER BY access_level DESC, username ASC");
		while($r=$this->fetch_assoc($res)) {
			print "<option value='{$r['id']}'>{$r['username']} {$r['real_user_name']}</option>";
		}			
		print "			</select>
					  <button type='submit' class='btn btn-primary' name='do_chk_grp_ops' value='yes'>Выполнить</button>
				</div></form>";

		print "</div>";

		print "<div class='card bg-light p-2'>
			<div>
				<h3>Перенести на другой Этап</h3>
				
				<form class='form-inline' action=''>
				  <div class='form-group'>
					<badge for='razdel'>Выберите Этап:</badge>
					<select id='razdel' name='razdel'>";
			print "<option value='0'>выберите этап</option>";
		$res=$this->query("SELECT * FROM razdel WHERE del=0 AND id>0 ORDER BY razdel_num,razdel_name");
		while($r=$this->fetch_assoc($res)) {
			print "<option value='{$r['id']}'>{$r['razdel_name']}</option>";
		}			
		print "			</select>
				  </div>
				  <input type='hidden' name='move_to_razdel' value='yes'>
				  <button type='submit' class='btn btn-primary' name='do_chk_grp_ops' value='yes'>Выполнить</button>
				</form>
				
			</div>
			</div>";

		print "<div class='card bg-light p-2'>
			<div>
				<h3>Присвоить тэг</h3>
				
				<form class='form-inline' action=''>
				  <div class='form-group'>
					<badge for='grp_tag'>Тэг:</badge>
					<select name='grp_tag_id'>";
			print "<option value='0'>выберите тэг</option>";
		$res=$this->query("SELECT * FROM tags WHERE del=0 ORDER BY tag_name");
		while($r=$this->fetch_assoc($res)) {
			print "<option value='{$r['id']}'>{$r['tag_name']}</option>";
		}			
		print "			</select>
				  </div>
				  <input type='hidden' name='grp_tag_set' value='yes'>
				  <button type='submit' class='btn btn-primary' name='do_chk_grp_ops' value='yes'>Выполнить</button>
				</form>
				
			</div>
			</div>";

		print "<div class='card bg-light p-2'>
			<div>
				<h3>Удалить тэг</h3>
				
				<form class='form-inline' action=''>
				  <div class='form-group'>
					<badge for='grp_tag'>Тэг:</badge>
					<select name='grp_tag_id'>";
			print "<option value='0'>выберите тэг</option>";
		$res=$this->query("SELECT * FROM tags WHERE del=0 ORDER BY tag_name");
		while($r=$this->fetch_assoc($res)) {
			print "<option value='{$r['id']}'>{$r['tag_name']}</option>";
		}			
		print "			</select>
				  </div>
				  <input type='hidden' name='grp_tag_del' value='yes'>
				  <button type='submit' class='btn btn-primary' name='do_chk_grp_ops' value='yes'>Выполнить</button>
				</form>
				
			</div>
			</div>";


		print "<div class='card bg-light p-2'>
			<div>
				<h3>Убрать из задач</h3>
				
				<form class='' action=''>
				  <input type='hidden' name='mark_read' value='yes'>
				  <button type='submit' class='btn btn-primary' name='do_chk_grp_ops' value='yes'>Выполнить</button>
				</form>
				
			</div>
			</div>";

		print "<div class='card bg-light p-2'>
			<div>
				<h3>Удалить</h3>
				
				<form class='form-inline' action=''>
				  <div class='form-group'>
					<label for='days'>Не удалять за последние дней:</label>
					<input type='text' name='days' id='days' value='0' class='form-control' style='width:40px;'>
				  </div>
				  <input type='hidden' name='remove' value='yes'>
				  <button type='submit' class='btn btn-default btn-primary' name='do_chk_grp_ops' value='yes'>Выполнить</button>
				</form>
				
			</div>
			</div>";
	}
	function do_chk_grp_ops() {
		print "<h2><div class='alert alert-info'>Выполнение груповых операций</div></h2>";
		print "<p><div class='alert alert-warning'>Единоразово обрабатывается не более 1000 операций, при необходимости повторите</div></p>";
		if($this->userdata['access_level']>3) {
			print "<h2><div class='alert alert-danger'>нет доступа</div></h2>";
			//~ $this->email($emails=array("vlav@mail.ru"), "Попытка выполнения групповой операции без прав доступа", "Посмотреть, зачем это кому то понадобилось", $from="vktrade@1-info.ru",$fromname="VKTRADE", $add_globals=true);
			return false;
		}

		if(isset($_GET['assign_to_user'])) {
			if($_SESSION['access_level']>3) {
				print "<p class='alert alert-danger'>Операция не разрешена</p>";
				return false;
			}
			$user_id=intval($_GET['assign_to_user']);
			if($user_id==-1) {
				$user_id=0;
				$klid=0;
				$username="Нет партнера";
			} else {
				$klid=$this->get_klid($user_id);
				$username=$this->dlookup("username","users","id='$user_id'");
			}
			$res=$this->query("SELECT * FROM cards WHERE cards.del=0 AND fl=1 LIMIT 1000");
			$cnt=$this->num_rows($res);
			while($r=$this->fetch_assoc($res)) {
				print "{$r['surname']} {$r['name']} {$r['uid']} <br>";
				//$this->query("UPDATE cards SET fl_newmsg='1',tm_lastmsg='".time()."', user_id='$user_id' WHERE id={$r['id']}");
				$this->query("UPDATE cards SET user_id='$user_id',pact_conversation_id=0,utm_affiliate='$klid' WHERE id='{$r['id']}'",0);
				$this->save_comm($r['uid'],$_SESSION['userid_sess'],"Передача другому партнеру: {$r['user_id']} -> $user_id",121,$r['user_id']);
			}
			print "<h3>Лиды ($cnt) переданы партнеру <span class='badge' >$username</span></h3>";
		}
		if(isset($_GET['assign_to_man'])) {
			if($_SESSION['access_level']>3) {
				print "<p class='alert alert-danger'>Операция не разрешена</p>";
				return false;
			}
			$man_id=intval($_GET['assign_to_man']);
			$username=$this->dlookup("username","users","id='$man_id'");
			$res=$this->query("SELECT * FROM cards WHERE cards.del=0 AND fl=1 LIMIT 1000");
			$cnt=$this->num_rows($res);
			while($r=$this->fetch_assoc($res)) {
				print "{$r['surname']} {$r['name']} {$r['uid']} <br>";
				$this->query("UPDATE cards SET man_id='$man_id' WHERE id='{$r['id']}'");
				$this->save_comm($r['uid'],$_SESSION['userid_sess'],"Передача другому менеджеру: {$r['man_id']} -> $man_id",122,$r['man_id']);
			}
			print "<h3>Лиды ($cnt) назначены менеджеру <span class='badge' >$username</span></h3>";
			//$this->query("UPDATE cards SET fl=0 WHERE fl=1");
		}
		if(isset($_GET['grp_tag_set'])) {
			if($_SESSION['access_level']>3) {
				print "<p class='alert alert-danger'>Операция не разрешена</p>";
				return false;
			}
			$tag_id=intval($_GET['grp_tag_id']);
			$tag_name=$this->dlookup("tag_name","tags","id='$tag_id'");
			$res=$this->query("SELECT * FROM cards WHERE cards.del=0 AND fl=1 LIMIT 1000");
			$cnt=$this->num_rows($res);
			while($r=$this->fetch_assoc($res)) {
				$this->tag_add($r['uid'],$tag_id);
			}
			print "<h3>Тэг ($tag_name) присвоен выбранным карточкам ($cnt)</h3>";
		}
		if(isset($_GET['grp_tag_del'])) {
			if($_SESSION['access_level']>3) {
				print "<p class='alert alert-danger'>Операция не разрешена</p>";
				return false;
			}
			$tag_id=intval($_GET['grp_tag_id']);
			$tag_name=$this->dlookup("tag_name","tags","id='$tag_id'");
			$res=$this->query("SELECT * FROM cards WHERE cards.del=0 AND fl=1 LIMIT 1000");
			$cnt=$this->num_rows($res);
			while($r=$this->fetch_assoc($res)) {
				$this->tag_del($r['uid'],$tag_id);
			}
			print "<h3>Тэг ($tag_name) удален у выбранных карточек ($cnt)</h3>";
		}
		if(@$_GET['remove']) {
			$res=$this->query("SELECT id,uid,tm_lastmsg FROM cards WHERE cards.del=0 AND fl=1 LIMIT 1000");
			$cnt=0;
			$days=intval($_GET['days']);
			$tm=time()-($days*24*60*60);
			while($r=$this->fetch_assoc($res)) {
				if($r['tm_lastmsg']>$tm)
					continue;
				$this->query("UPDATE cards SET del=1 WHERE uid='{$r['uid']}'");
				$this->query("UPDATE users SET del=1 WHERE klid='{$r['id']}'");
				//$this->query("DELETE FROM cards WHERE uid='{$r['uid']}'");
				//$this->query("DELETE FROM msgs WHERE uid='{$r['uid']}'");
				$cnt++;
			}
			print "<div class='alert alert-success' >Успешно. <b>$cnt</b> записей удалено</div>";
			//print "<div class='alert alert-success' >Функция временно недоступна</div>";
		}
		if(isset($_GET['mark_read'])) {
			$cnt=$this->num_rows($this->query("SELECT id FROM cards WHERE del=0 AND fl=1 AND (fl_newmsg>0) LIMIT 1000"));
			$this->query("UPDATE cards SET fl_newmsg=0 WHERE fl=1");
			print "<div class='alert alert-success' >Успешно. <b>$cnt</b> записей убрано из задач</div>";
		}
		if(isset($_GET['move_to_razdel'])) {
			$razdel=intval($_GET['razdel']);
			if($razdel>0) {
				$res=$this->query("SELECT uid FROM cards WHERE cards.del=0 AND fl=1 LIMIT 1000");
				$cnt=$this->num_rows($res);
				while($r=$this->fetch_assoc($res)) {
					$this->query("UPDATE cards SET razdel='$razdel' WHERE uid='{$r['uid']}'");
				}
				$rname=$this->dlookup("razdel_name","razdel","id='$razdel'");
				print "<div class='alert alert-success' >Успешно. <b>$cnt</b> записей перенесено в razdel <b>$rname</b></div>";
			} else
				print "<div class='alert alert-warning' >Не выбран Этап</div>";
		}
		if(@$_GET['move_to_vklist']) {
			$gid=intval($_GET['vklist_grp']);
			if($gid>0) {
				if(isset($_GET['remove_from_cards']))
					$remove_from_cards=true; else $remove_from_cards=false; 
				//print "$gid $remove_from_cards";
				$res=$this->query("SELECT uid FROM cards WHERE cards.del=0 AND fl=1 LIMIT 1000");
				$cnt=$this->num_rows($res);
				while($r=$this->fetch_assoc($res)) {
					if($this->dlookup("uid","vklist","uid={$r['uid']}")) { //IN VKLIST
						$this->query("UPDATE vklist SET 
									group_id='$gid', 
									tm_msg='0', 
									tm_wall='0', 
									tm_friends='0', 
									res_msg='0', 
									res_wall='0', 
									res_friends='0', 
									blocked=0
									WHERE uid={$r['uid']}");
					} else {
						$this->query("INSERT INTO vklist SET uid='{$r['uid']}',tm_cr='".time()."', group_id='$gid'");
					}
					if($remove_from_cards)
						$this->query("DELETE FROM cards WHERE uid='{$r['uid']}'");
				}
				$gname=$this->dlookup("group_name","vklist_groups","id='$gid'");
				print "<div class='alert alert-success' >Успешно. <b>$cnt</b> записей перенесено в группу рассылки <b>$gname</b></div>";
				if($remove_from_cards)
					print "<div class='alert alert-warning' >Из базы записи удалены, находятся теперь только в группе рассылки</div>";
			} else
				print "<div class='alert alert-warning' >Не выбрана группа рассылки</div>";
		}
		print "<script>opener.location.reload();</script>";
		print "<div class='card bg-light p-2' ><a href='?chk_grp_ops=yes' class='' target=''>Вернуться - групповые операции</a></div>";
	}
	function manage_checkboxes() {
		if($this->access_level>3)
			return;
		$ids="";
		if(!empty($this->last_query)) {
			$res=$this->cp_query($this->last_query);
			while($r=$this->fetch_assoc($res)) {
				$ids.=$r['id'].",";
			}
			$cnt1=$this->num_rows($res);
		} elseif($this->filter=='tasks') {
			$res=$this->cp_query($this->query_new);
			while($r=$this->fetch_assoc($res)) {
				$ids.=$r['id'].",";
			}
			$cnt1=$this->num_rows($res);
		} else
			$cnt1="-";
		$cnt=$this->num_rows($this->query("SELECT id FROM cards WHERE fl=1 AND del=0"));
		$out= "<select id='manage_checkboxes' style='width:20px;' ids='$ids' class='form-control' >
		<option></option>
		<option> = Всего отмечено - $cnt = </option>
		";
		$out.="<option value='set_all_razd'>Отметить все в разделе ($cnt1)</option>
		";
		$out.="
		<!--<option value='clr_all_razd'>Снять все в разделе ($cnt1)</option>-->
		<option value='clr_all'>Снять ВСЕ ($cnt)</option>
		<option value='clr_all'>-------------</option>
		<option value='chk_grp_ops'>ГРУППОВЫЕ ОПЕРАЦИИ</option>
		</select>";
		return $out;
	}
	function tbl_print($res,$cnt,$filter) {
		//$this->notify_me("HERE_$filter");
		if(!$res && $filter != "last_10") {
			print "<script>location='?view=yes&filter=last_10'</script>";
		}
		if(!$res) 
			return false;
		$this->test_microtime(__LINE__);
		$access_level=$this->access_level;
		$n=1+intval($_SESSION['page']*$_SESSION['per_page']);
		$uids_displayed=array();
		print "<table  class='table table-condensed  table-responsive table-hover' style='font-size:14px;'>";
			print "<thead><tr>
				<th>".$this->manage_checkboxes()."</th>
				<th><span class='badge'>$cnt</span></th>
				<th>Этап</th>
				<th>Опл</th>
				<th>Дата события</th>
				<th title='убрать из задач'>Х</th>
				<th>Статус</th>
				<th>Имя</th>
				<th>Город</th>
				<th>Комментарий</th>
				<th>Инфо</th>
				<th>Контроль</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				</tr></thead>
				<tbody>";
		while($r=$this->fetch_assoc($res)) {
			if($_SESSION['userid_sess']==1) {
				//$this->print_r($r);
			}
			//~ if(in_array($r['uid'],$uids_displayed))
				//~ continue;
			$uid=intval($r['uid']);
			$uids_displayed[]=$r['uid'];
			$tm_last_msg=(date("Y")==date("Y",$r['tm_lastmsg']))?date("d.m H:i", $r['tm_lastmsg']):date("d.m.y H:i", $r['tm_lastmsg']);
			if($filter=="C_unanswered") {
				$token=get_token_by_accid(1);
				$msgs=vk_messages_get_by_user(1,$r['uid']);
		$this->test_microtime(__LINE__);
				if(@$msgs['error']) {
					$last_res="error getting msgs";
				} elseif(!@$msgs['response'][1]) {
						$last_res="No contacts";
				} else {
					//print_r($msgs);
					$dt=date("d/m H:i",$msgs['response'][1]['date']);
					if($msgs['response'][1]['out']==0)
						$out="in"; else $out="out"; 
					if($msgs['response'][1]['read_state']==0)
						$state="read"; else $state="not read"; 
					if($msgs['response'][1]['date']!=0)
						$last_res="$dt | $state";
					usleep(300000);
				}
				
			} elseif($filter=="dancers_only") {
				$date_last_op=date("d.m.Y",$r['tm_lastops']);
				$r1=$this->fetch_assoc($this->query("SELECT  COUNT(tm) AS cnt FROM ops WHERE debit>0 AND klid={$r['id']}"));
				$cnt_ops=$r1['cnt'];
				$r1=$this->fetch_assoc($this->query("SELECT SUM(kredit) as kr,SUM(debit) as deb FROM ops WHERE klid={$r['id']}"));
				$ost=round($r1['kr']-$r1['deb'],0);
				if($ost<0) 
					$last_res="<span class='red'>$date_last_op | -$cnt_ops | $ost</span>";
				else
					$last_res="$date_last_op | $cnt_ops | $ost (".(round($ost/380,0)).")";
			} else {
					$last_res="";
			}
			if(@$_GET['uid']==$r['uid'])
				$c="success"; else $c="";
			$s=$this->get_style_by_razdel($r['razdel']);
			$tel= "\n".$this->disp_mob($r['mob']);
			print "<tr id='r_{$r['id']}'  class='$c' >";
			//print "<td style='text-align:center;$c'><input type='checkbox' name='chk_{$r['uid']}' title={$r['uid']}></td>";
			if($r['fl']==1)
				$checked="checked"; else $checked="";
			$readonly = $_SESSION['access_level']<5 ? "" :"disabled";
			print "<td><input $readonly type='checkbox' name='chk1' id='{$r['id']}' $checked title={$r['uid']}></td>";

			if($access_level<5)	
				//print "<td title={$r['id']}><a href='javascript:wopen(\"?info=yes&uid={$r['uid']}\");' onclick='location=\"?filter=$filter&uid={$r['uid']}&view=yes#r_{$r['id']}\"'>$n</a></td>";
				print "<td title={$r['id']}>$n</td>";
			else
				print "<td title={$r['id']}><span class='badge badge-info' >$n</span></td>";
			$razdel=$this->dlookup("razdel_name","razdel","id='{$r['razdel']}'"); $razdel=(!$razdel)?" ":$razdel;

			//~ if($this->fetch_assoc($this->query("SELECT fl_client FROM sources JOIN msgs ON msgs.source_id=sources.id WHERE uid='$uid' AND fl_client=1",0))['fl_client']) {
				//~ $s="background-color:red; color:white;";
			//~ }

			$sum_pay=$this->fetch_assoc($this->query("SELECT SUM(amount) AS s FROM avangard WHERE vk_uid='$uid' AND res=1"))['s'];
			$pay=($sum_pay)?"<span onclick='showUserInfoModal({$r['uid']},3)' class='badge bg-primary text-white p-1' title='сумма оплат'>$sum_pay</span>":"";

			$cnt_disc_card=$this->is_discount_card_visited($r['uid']);
		$this->test_microtime(__LINE__);
			$last_mess_type=$this->fetch_row($this->query("SELECT imp FROM msgs WHERE uid='{$r['uid']}' AND outg=0 ORDER BY id DESC LIMIT 1"));
			//$razdel="<a style='$s' href='javascript:wopen(\"comm.php?klid={$r['id']}&active=razdel\")' onclick='location=\"?view=yes&uid={$r['uid']}\"'>$razdel</a>";
			$razdel="$razdel";
			if($access_level>5) {
				$razdel=""; $s="";
			}
			print "<td class='text-center'><span id='__razdel_{$r['id']}' class='badge p-2 razdel' onclick='showRazdelModal({$r['id']},{$r['razdel']})'  style='$s'>$razdel</span></td>";
			print "<td>$pay</td>";
			print "<td  class='text-center'><div class='badge badge-success p-1' >$tm_last_msg</div></td>";
			$delete_from_tasks=($r['fl_newmsg'] || $r['tm_delay'])?"<a href='?mark_read_one=yes&uid={$r['uid']}' title='убрать из задач' target=''><i class='fa fa-remove'></i></a>":"";
			print "<td >".$delete_from_tasks."</td>";
		$this->test_microtime(__LINE__);
			//if($r_acc['id']=="" || $r_acc['id']==0 )
			//	$r_acc['id']=1;
			//~ if($r['fl_newmsg']==3)
				//~ $bg_msg_badge="background-color:#56b849;";
			//~ elseif($r['fl_newmsg']==2)
				//~ $bg_msg_badge="background-color:#d9534f;";
			//~ elseif($r['fl_newmsg']>3)
				//~ $bg_msg_badge="background-color:#ff1e00;";
			//~ else
				//~ $bg_msg_badge="background-color:#f0ad4e;";
			
			$bg_msg_badge=$this->bg_msg_badge($r['fl_newmsg'],$r['tm_delay']);
		$this->test_microtime(__LINE__);
			if(!isset($this->open_target))
				$this->open_target="popup";
			//$open_target=($this->open_target=="popup")?"javascript:wopen(\"msg.php?uid={$r['uid']}&no_reload_opener=yes\");":"msg.php?uid={$r['uid']}&no_reload_opener=yes";
			$open_target=($this->open_target=="popup")?"javascript:wopen(\"msg.php?uid={$r['uid']}\");":"msg.php?uid={$r['uid']}";
			if(!isset($this->open_target_blank))
				$open_target_blank=($this->open_target=="popup")?"":"_blank";
			else
				$open_target_blank=$this->open_target_blank;
			//onclick=location=\"?filter=$filter&uid={$r['uid']}&view=yes#r_{$r['id']}\"
			//&no_reload_opener=yes
			$mark="<span class='badge badge-$bg_msg_badge px-2 py-1' style='$bg_msg_badge'>&nbsp;</span>";
			if($this->dlookup("id","users","del=0 AND klid='{$r['id']}'"))
				$mark="<span class='badge badge-$bg_msg_badge px-2 py-1' style='$bg_msg_badge' title='это партнер'>🙋‍♀️</span>";
			print "<td ><a  href='$open_target' target='$open_target_blank'>
					$mark
					</a>
					".$this->tbl_delayed($r)."
				</td>";
			$deleted=($r['del']==1)?" (удалено)":"";
			$city=$r['city'];
			$age=($r['age'])?"({$r['age']} лет)":"";
			$age=($r['age'])?"({$r['age']} лет)":"";
			$name=$this->disp_name_cp("{$r['surname']} {$r['name']} $age $deleted");
			$email=$r['email'];
			$tzoffset=($r['tzoffset'])?"<span class='badge badge-success' >+".(-($r['tzoffset']/60+3))." часов</span>":"";
			print "<td ><b><a title='".$this->disp_email($email)."' href='$open_target' target='$open_target_blank'>$name</a></b></td>";
			print "<td><span class='badge badge-default' >$city</span> $tzoffset</td>";
			if(isset($r['source_name'])) {
				if($r['source_id']==7)
					$source_style="success"; else $source_style="info";
				$source_name="<span class='badge badge-$source_style' >".htmlspecialchars($r['source_name'])."</span>";
			} else
				$source_name="";
			$utm=(!empty($r['source_vote']))?"<div class='badge badge-warning' >{$r['source_vote']}</div>":"";

			$dt_comm=($r['tm_comm'])?date("d.m.Y H:i",$r['tm_comm']):"";
			$comm=(trim($r['comm'])!="")?"<div class=''>
				<span class='small badge bg-light text-grey p-1 mr-2'>$dt_comm</span>
				".nl2br(htmlspecialchars($this->trim_comm($r['comm'],200)))." </div>":"";
			$comm=$this->make_link_clickable($comm);
			$comm1=(trim($r['comm1'])!="")?"<div class='card bg-light p-2'>".nl2br(htmlspecialchars($this->trim_comm($r['comm1'],200)))."</div>":"";
			$comm1=$this->make_link_clickable($comm1);
			
			$events=""; //"<div class='well well-sm' >".$this->disp_events($cnt_disc_card,$last_mess_type[0],$r)."</div>";
			$mob="";//$this->disp_mob($r['mob']);
			//$mob=substr($mob,0,3)."ХХХХХХХХ";
			$res_tags=$this->query("SELECT * FROM tags_op JOIN tags ON tags.id=tag_id WHERE uid='{$r['uid']}'");
			$tags="";
			while($r_tags=$this->fetch_assoc($res_tags)) {
				$tags_bg=$r_tags['tag_color'];
				$tags_color=$this->get_contrast_color($r_tags['tag_color']);
				$tags_dt=date("d.m.Y H:i",$r_tags['tm']);
				$tags.="<span title='$tags_dt' class='p-1 mx-1 rounded small'  style='background-color:$tags_bg; color:$tags_color;'>{$r_tags['tag_name']}</span>";
			}
			//if($this->num_rows($res_tags))
			$tags.= "<a href='#' class='' data-toggle='modal' data-target='#msgTagsModal' data-uid='{$r['uid']}' title='Добавить тэг'>
				<i class='fa fa-plus'></i>
					</a>";
			$res_lands=$this->query("SELECT msgs.tm AS tm,land_num,land_name FROM msgs
						JOIN lands ON land_num=msgs.source_id-1000
						WHERE uid='{$r['uid']}' AND source_id>1000
						ORDER BY msgs.tm");
			$lands="";
			while($r_lands=$this->fetch_assoc($res_lands)) {
				$lands.="<span title='".date("d.m.Y_H:i",$r_lands['tm'])." {$r_lands['land_name']}' class='p-1 mx-1 badge-info small badge-pill text-white'><a href='{$r_lands['land_url']}' style='color:white;' target='_blank'>{$r_lands['land_num']}</a></span> ";
			}
			if($this->database=='vkt' && $uid==-1002)
				$lands="";
			global $disp_lands_icons_in_cp;
			$lands=!isset($disp_lands_icons_in_cp) || $disp_lands_icons_in_cp ? "<div>$lands</div>" : "";
			print "<td onclick=''  class='cp_comm'>
				<div class='blue' >$mob</div>
				$comm
				$comm1
				$events
				<div id='row_tags_{$r['uid']}'>$tags</div>
				$lands
				</td>";
		$this->test_microtime(__LINE__);
			print "<td  class='cp_info'>".$this->tbl_info($r['uid'],$r,$filter)."</td>";
		$this->test_microtime(__LINE__);
			$first_time_opened=($r['tm_first_time_opened'])?"<span class='fa fa-check-circle-o' title='просмотрено ".date("d.m.Y H:i",$r['tm_first_time_opened'])."'></span>":"";
			$wa_allowed=($r['wa_allowed'])?"<img src='/css/icons/wa-16.png' title='переписку в вотсап одобрил'>":"";
			$email_exists=(empty($r['email']))?"":"<img src='/css/icons/email-16.png' title='".$this->disp_email($r['email'])."'>";
			$tg_ok=(!$r['telegram_id'])?"":"<img src='/css/icons/tg-16.png' title='{$r['telegram_nic']}'>";
			$vk_ok=($r['uid']<0 && !$r['vk_id'])?"":"<img src='/css/icons/vk-16.png' title='VK'>";
			$cnt_active="";
			print "<td  class='cp_ctrl'>
				$cnt_active
				$vk_ok
				$tg_ok
				$email_exists
				$first_time_opened
				<!--<a href='javascript:wopen(\"notes.php?uid={$r['uid']}\")'><span class='fa fa-list' title='история комментариев'></span></a>-->
				$source_name $utm
				".$this->tbl_ctrl($r)."</td>";
			print "<td>";
				if($access_level<4)
					print "<a href='?edit=yes&id={$r['id']}'><span class='fa fa-edit' title='редактировать'></span></a></a>";
			print "</td>";
			print "<td>";
				if($access_level<4)
					print "<a href='?del=yes&id={$r['id']}'><span class='	fa fa-trash' title='удалить'></span></a>";
			print "</td>";
			print "</tr>";
			$n++;
		}
		print "</tbody></table>";
	//$this->print_r($this->runtime_log);
		$this->ch_razdel_modal();
		$this->user_info_modal();
		$this->tags_modal();
		?>
		<script>
			$("tr").click(function(){
		//		console.log("TR click");
				$('.tr_selected').removeClass('tr_selected');
				$(this).addClass("tr_selected");
				
			});
		</script>
		<?
	}
	function tbl_ctrl($r) {
	}
	function tbl_info_add($uid) {}
	function tbl_info($uid,$r,$filter) {
		//~ if($r['tm_delay']>0) {
			//~ $dt=date("d/m",$r['tm_delay']);
			//~ if(time()>$r['tm_delay'])
				//~ $t1="\nВремя пришло!"; else $t1="";
			//~ $delayed="<span class='badge badge-info' title='Отложенное время : $dt $t1'>".$dt."</span>";
		//~ } else $delayed="";
		$delayed=""; $shdl="";
		$tm0=mktime(0,0,0,date("m"),date("d"),date("Y"));
		if($r['tm_schedule']>$tm0) 
			$c="warning";
		elseif($r['tm_schedule']==$tm0) 
			$c="success";
		elseif($r['tm_schedule']>0)
			$c="secondary";
		//~ if($r['tm_schedule']>=time()) {
			//~ if($r['tm_schedule']>=mktime(0,0,0,date("m"),date("d"),date("Y"))) 
				//~ $c="warning"; else $c="default";
		//~ } else $shdl="";
		if($r['scdl_web_id']==3)
			$c_scdl_web_id="success";
		elseif($r['scdl_web_id']==2)
			$c_scdl_web_id="danger";
		elseif($r['scdl_web_id']==1)
			$c_scdl_web_id="primary";
		else
			$c_scdl_web_id="info";

		$shdl=($r['tm_schedule'])?"<span class='badge badge-$c' onclick='javascript:wopen_(\"comm.php?klid={$r['id']}&active=scdl\");'>".$daysweek[date("N",$r['tm_schedule'])]." ".date("d/m H:i",$r['tm_schedule'])." <div class='badge badge-$c_scdl_web_id' >{$r['scdl_web_id']}</div></span>":"";

		if($r['man_id']>0) {
			$man="<span class='badge badge-info' onclick='showUserInfoModal({$r['man_id']},2)'>".$r['man_id']."</span>"; 
		} else
			$man="<span class='badge badge-info'></span>";

		if($r['user_id']>0) {
			$agent="<span class='badge badge-danger'  onclick='showUserInfoModal({$r['user_id']},1)' >".$r['user_id']."</span>"; 
		} else
			$agent="<span class='badge badge-danger'></span>";
			
		$anketa=($r['anketa'])?"<span class='badge badge-info'><a href='javascript:wopen(\"msg.php?uid=$uid\")' class='' target=''>АНКЕТА</a></span>":"";
		$tel_color=($r['got_calls'])?"text-primary":"";
		$tel="";(!empty($r['mob']))?"<span class='fa fa-volume-control-phone $tel_color' title='".$this->disp_mob($r['mob'])."'></span>":"";
		$add=$this->tbl_info_add($uid);
		return "$man $agent $delayed $shdl $anketa $tel $add";
	}
	function user_info_modal() {
		global $ctrl_dir;
		?>
	  <div class="modal fade" id="userinfoModal" tabindex="-1" role="dialog" aria-labelledby="userinfoModalLabel" aria-hidden="true" data-backdrop="true">
		<div class="modal-dialog" role="document">
		  <div class="modal-content">
			<div class="modal-header">
			  <h5 class="modal-title" id="userinfoModalLabel">Информация</h5>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
			<div class="modal-body">
				<iframe id="userinfoIframe" width="100%" height="460"></iframe>
      		</div>
		  </div>
		</div>
	  </div>
	<script>
		function showUserInfoModal(userId,mode) {
		  $('#userinfoModal').modal({
			backdrop: 'static',
			keyboard: false
		  });
			  // Получите ссылку на iframe
		var iframe = document.getElementById('userinfoIframe');

		// Установите новый источник для iframe
		iframe.src = "https://for16.ru/d/<?=$ctrl_dir?>/user_info.php?user_id=" + userId+"&mode="+mode;
		}
	</script>
	<?
	}
	
	function ch_razdel_modal() {
		?>
	  <div class="modal fade" id="razdelModal" tabindex="-1" role="dialog" aria-labelledby="razdelModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
		  <div class="modal-content">
			<div class="modal-header">
			  <h5 class="modal-title" id="razdelModalLabel">Выберите этап</h5>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
			<div class="modal-body">
			  <ul id="razdelList"></ul>
			</div>
		  </div>
		</div>
	  </div>
	  <script>
		function showRazdelModal(cardsId,razdelId) {
		  $('#razdelModal').modal({
			backdrop: 'static',
			keyboard: false
		  });
		  loadRazdels(cardsId,razdelId);
		}

		function loadRazdels(cardsId,razdelId) {
		  var razdelSelect = document.getElementById('razdelSelect');

		  // Создаем AJAX-запрос для получения списка разделов
		  var xhr = new XMLHttpRequest();
		  xhr.open('GET', 'jquery.php?get_razdel_list=yes', true);
		  xhr.onreadystatechange = function() {
			if (xhr.readyState == 4 && xhr.status == 200) {
			  // Парсим полученный JSON и добавляем опции в выпадающий список
			  //console.log(xhr.responseText);
			  var razdels = JSON.parse(xhr.responseText);
			  //console.log(razdels);
			  razdels.forEach(function(razdel) {
				var li = document.createElement('li');
				li.textContent = razdel.razdel_name;
				li.style.padding = '10px';
				li.style.listStyleType = 'none';
				//console.log(razdel.id);
				li.addEventListener('mouseover', function() {
				  li.style.backgroundColor = '#17a2b8'; // Замените на нужный цвет подсветки
				  li.style.color = '#ffffff';
				});

				li.addEventListener('mouseout', function() {
				  li.style.backgroundColor = ''; // Возврат к исходному цвету фона
				  li.style.color = '#000000';
				});
				li.addEventListener('click', function() {
				  selectRazdel(razdel.id, razdel.razdel_name);
				  $('#razdelModal').modal('hide');
				});

				razdelList.appendChild(li);
			  });
			}
		  };
		  xhr.send();
		  
		  // Сохраняем значение cardsId для последующего использования
		  document.getElementById('razdelModal').setAttribute('data-cardsId', cardsId);
		}
		function selectRazdel(razdelId, razdelName) {
			//console.log(razdelName);
		  document.getElementById('razdelList').textContent = razdelName;
		  document.getElementById('razdelList').style.backgroundColor = '#def3eb'; // Заменить на нужный цвет
		  
		  // Отправляем AJAX-запрос для обновления cards.razdel_id
		  var xhr = new XMLHttpRequest();
		  var cardsId = document.getElementById('razdelModal').getAttribute('data-cardsId');
		  var url='cardsId=' + encodeURIComponent(cardsId) + '&razdelId=' + encodeURIComponent(razdelId);
		  xhr.open('GET', 'jquery.php?update_razdel=yes&'+url, true);
		  //xhr.open('POST', 'update_cards.php', true);
		  xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		  xhr.onreadystatechange = function() {
			if (xhr.readyState == 4 && xhr.status == 200) {
			  console.log('Запись обновлена');
			  document.getElementById('__razdel_'+cardsId).textContent = razdelName;
				var styleString = xhr.responseText;
				// Ищем индекс символа "#" в строке
				var startIndex = styleString.indexOf("#");
				// Обрезаем строку, начиная с индекса символа "#"
				var colorString = styleString.slice(startIndex);
				// Делаем поиск индекса символа ";" в строке
				var endIndex = colorString.indexOf(";");
				// Извлекаем только значение цвета
				var colorValue = colorString.slice(0, endIndex);
				//console.log(colorValue);
			  document.getElementById('__razdel_'+cardsId).style.backgroundColor = colorValue;
			}
		  };
		  xhr.send('cardsId=' + encodeURIComponent(cardsId) + '&razdelId=' + encodeURIComponent(razdelId));
		}

		$('#razdelModal').on('hidden.bs.modal', function () {
		  var razdelList = document.getElementById('razdelList');
		  razdelList.innerHTML = '';
		});
		</script>
	  <?
	}
	function tags_modal() {
	?>
    <div class="modal fade" id="msgTagsModal" tabindex="-1" role="dialog" aria-labelledby="msgTagsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="modalIframe" src="" style="width: 100%; height: 400px; border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
   <script>
        // Load the iframe content on modal show
        $('#msgTagsModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var uid = button.data('uid'); // Extract uid from data-* attributes
            var modal = $(this);
            var iframe = modal.find('#modalIframe');

            // Set the src of the iframe to the PHP file with the uid parameter
            iframe.attr('src', 'msg_tags.php?uid=' + uid);
			modal.data('uid', uid);
       });

        $('#msgTagsModal').on('hidden.bs.modal', function () {
			var modal = $(this);
			var uid = modal.data('uid'); // Retrieve uid stored in modal data
            //console.log("uid="+uid);
            $.ajax({
                url: 'jquery.php', // Your server-side script to get tags
                method: 'GET',
                data: { uid: uid, cp_disp_tags: 'yes' },
                success: function(response) {
                    $('#row_tags_' + uid).html(response); // Insert response into the correct row
                },
                error: function() {
                    $('#row_tags_' + uid).html('Error fetching data.'); // Handle errors
                }
            });
        });

    </script>

	<?	
	}



}


class crd extends simple_db {
	function view() {
		//$vk=new cp;
		//$vk->view();
	}
	function del($id) {
		$uid=$this->dlookup("uid","cards","id='$id'");
		$name=$this->dlookup("name","cards","id='$id'")." ".$this->dlookup("surname","cards","id='$id'");
		$sum_pay=$this->fetch_assoc($this->query("SELECT SUM(amount) AS s FROM avangard WHERE res=1 AND vk_uid='$uid'"))['s'];
		if($sum_pay)
			print "<p class='alert alert-danger' >По данному клиенту были проведены оплаты на сумму: $sum_pay. Удалить вместе с данными о платежах?";
		print "<p class='alert alert-info' >
			Подтвердите удаление: <span class='badge badge-info' >$name</span>
			<a href='?do_del=yes&id=$id' class='btn btn-primary btn-sm' target=''>удалить</a>
			<a href='?view=yes#r_$id' class='btn btn-warning btn-sm' target=''>отменить</a>
			</p>";
	}
	function after_do_del($id) {
		//$this->query("UPDATE cards SET del=1,telegram_id=0,vk_id=0,mob_search='',mob='',email='',name='',surname='',pact_conversation_id=0,pact_insta_cid=0 WHERE id='$id'");
		$this->query("UPDATE users SET del=1 WHERE klid='$id'");
		$uid=$this->dlookup("uid","cards","id='$id'");
		$this->query("UPDATE avangard SET res=0 WHERE res=1 AND vk_uid='$uid'");
		print "<p class='alert alert-info' >Удалено. <a href='?view=yes&filter=tasks' class='btn btn-primary btn-sm' target=''>продолжить</a></p>";
	}
	function after_do_edit($id) {
		$r=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE id='$id'"));
		$this->query("INSERT INTO msgs SET 
				uid={$r['uid']},
				acc_id=0,
				mid=0,
				tm=".time().",
				user_id=".$_SESSION['userid_sess'].",
				msg='".mysql_real_escape_string($r['comm'])."',
				outg=2,
				imp=12");
		
		$r1=$this->fetch_assoc($this->query("SELECT * FROM razdel WHERE id='{$r['razdel']}'"));
		$this->query("INSERT INTO msgs SET 
				uid={$r['uid']},
				acc_id=0,
				mid=0,
				tm=".time().",
				user_id=".$_SESSION['userid_sess'].",
				msg='".$this->escape("Этап изменен на {$r1['razdel_name']} пользователем {$_SESSION['username']}")."',
				outg=2,
				imp=11,
				razdel_id={$r['razdel']}");
		print "<script>location='?str={$r['uid']}&view=yes&filter=Search#r_{$r['id']}'</script>";
	}
	function after_do_add($id) {
		$r=$this->fetch_assoc(mysql_query("SELECT * FROM cards WHERE id='$id'"));
		$this->query("INSERT INTO msgs SET 
				uid={$r['uid']},
				acc_id=0,
				mid=0,
				tm=".time().",
				user_id=".$_SESSION['userid_sess'].",
				msg='".mysql_real_escape_string($r['comm'])."',
				outg=2,
				imp=12");
		$this->query("DELETE FROM vklist WHERE uid={$r['uid']}");		
		print "<script>location='?str={$r['uid']}&view=yes&filter=Search#r_{$r['id']}'</script>";
	}
}
?>
