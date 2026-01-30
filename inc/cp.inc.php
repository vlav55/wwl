<?
class cp_top extends top {
	function menu_add() {
	}
}
$menu=( isset($_GET['getinfo']) || isset($_GET['do_getinfo']) || isset($_GET['chk_grp_ops']) || isset($_GET['do_chk_grp_ops'])  )?false:true;
$gid=(isset($VK_GROUP_ID))?$VK_GROUP_ID:false;
$admin_uid=(isset($VK_OWN_UID))?$VK_OWN_UID:false;
//print "HERE $gid $admin_uid $payed_by_uds <br>\n";
$t=new cp_top($database,"80%",$menu,$favicon,$ask_passw=true,$gid,$admin_uid);

class dcp extends cp {
	function query_new_() {
		include "query_new.inc.php";
		return $query_new;
	}
	function tbl_info_($uid,$r,$filter) {
		return;
		$no_tel="";$ops_info="";
		//if(($r['razdel']==3 || $r['razdel']==2 || $r['razdel']==11) || $filter=="dancers_only") { //IF A OR B
		$daysweek=array("Р’СЃ","РџРЅ","Р’С‚","РЎСЂ","Р§С‚","РџС‚","РЎР±",);
		$tm=$this->dt1(time()-(24*60*60));
		
		
		if($r['tm_delay']>0) {
			$dt=date("d/m",$r['tm_delay']);
			if(time()>$r['tm_delay'])
				$t1="\nNow time has come!"; else $t1="";
			$delayed="<span class='label label-info'  data-toggle='tooltip' data-placement='right' title='Delayed to $dt $t1' onclick='javascript:wopen(\"comm.php?klid={$r['id']}&active=delay\")'>".$dt."</span>";
		} else $delayed="";
		$razdel_exclude=array(0,3,5,6,8,11);
		if(!in_array($r['razdel'],$razdel_exclude)) {
			$agent="<span class='label label-danger'>V</span>";
			if($r['user_id']>0) {
				$time_1=time()-(1*24*60*60);
				if($r['tm_userid']<$time_1 && $r['tm_lastmsg']<$time_1 && $r['fl_newmsg']==1)
					$this->query("UPDATE cards SET user_id=0 WHERE id={$r['id']}");
				else
					$agent="<div class='label label-danger' title=''>".$r['user_id']."</div>"; 
			}
		} else 
			$agent="";
		$agent="";
		 
		$shdl="";
		if($r['tm_schedule']>0)
			$shdl="<div class='label label-danger' title=''>".date("d.m.Y",$r['tm_schedule'])."</div>";
		$tel=($r['mob']!="")?"<div class='label label-info'>{$r['mob']}</div>":"";
		$comm1=($r['comm1']!="")?"<div class='well well-sm'>{$r['comm1']}</div>&nbsp;":"";
		return "$delayed";
	}
	function tbl_ctrl($r) {
		return "";
	}
	function tbl_print_($res,$cnt,$filter) {
		return;
		if($filter=="from_group") {
			print "<div class='alert alert-warning'>Р¤РёР»СЊС‚СЂ : РІ РіСЂСѓРїРїРµ, РЅРѕ РЅРµС‚ РІ РґСЂСѓР·СЊСЏС…</div>";
			$vk=new vklist_api();
			$frnds=$vk->vk_friends_getlist_for_uid("70412844");
			$members=$vk->vk_group_getmembers("avto_trade_spb",$friends_only=false);
			$c1=array(); $c2=array();
			foreach($members AS $r) {
				if(isset($r['city']))
					if(!in_array($r['city'],$c2))
						$c2[]=$r['city'];
				if(isset($r['country']))
					if(!in_array($r['country'],$c1))
						$c1[]=$r['country'];
			}
			$city_arr=$vk->vk_get_city($c2);
			$country_arr=$vk->vk_get_country($c1);
			$n=1;
			foreach($members AS $r) {
				if(!in_array($r['uid'],$frnds)) {
					$name=mb_convert_encoding($r['first_name']." ".$r['last_name'],"cp1251","utf8");
					$city=(isset($r['city']))?$city_arr[$r['city']]:"n/a";
					$country=(isset($r['country']))?$country_arr[$r['country']]:"n/a";
					$sex=($r['sex']==1)?"F":"M";
					$cur=(@$_GET['uid']==$r['uid'])?"background-color:#ffcc66;":"";
					if($r1=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE uid={$r['uid']}")))
						$in_cards="<div class='badge' style='background-color:#196619;'><a style='color:white;' href='javascript:wopen(\"msg.php?uid={$r['uid']}&acc_id={$r1['acc_id']}\");'>{$r1['acc_id']}</a></div> 
						<div class='badge'><a style='color:white;' href='javascript:wopen(\"msg.php?uid={$r['uid']}&acc_id={$r1['acc_id']}\");'>{$r1['acc_id']}</a></div>"; 
					else $in_cards="<div class='badge' style='background-color:#cc3300;'><a  style='color:white;' href='?view=yes&filter=from_group&add_from_group=yes&uid={$r['uid']}&name=$name#r_{$r['uid']}' title='Not in database!'>add</a></div>";
					print "<div class='row well well-sm' id='r_{$r['uid']}' style='$cur'>";
						print "<div class='col-sm-1'>".($n++)."</div>"; 
						print "<div class='col-sm-1'><span class='badge'>{$r['uid']}</span></div>"; 
						print "<div class='col-sm-5'><a href='https://vk.com/id{$r['uid']}' target='_blank'>$name</a></div>"; 
						print "<div class='col-sm-1'>$in_cards</div>"; 
						print "<div class='col-sm-4'>$country $city</div>"; 
					print "</div>";
				}
			}
			//print_r($frnds);
		} else
			parent::tbl_print($res,$cnt,$filter);
	}
	function menu_additems() {
		return "";
	}
}
$cp=new dcp;
if(@$_GET['add_from_group']) {
	list($name,$surname)=explode(" ",$_GET['name']);
	$cp->query("INSERT INTO cards SET
			uid=".$_GET['uid'].",
			name='".mysql_real_escape_string($name)."',
			surname='".mysql_real_escape_string($surname)."',
			acc_id=1,
			comm='Added from group check - in group but not a friend',
			fl_newmsg=1,
			tm=".time()."
			");
	print "<div class='alert alert-success'><b>$name $surname</b> - added to database</div>";
}

$cp->userdata=$t->userdata;
$cp->connect($database);

$cp->run();

$t->bottom();

?>
