<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
class vklist_reports extends db{
	var $database;
	function __construct($db=false) {
		if($db)
			$this->database=$db;
		$this->connect($this->database);
	}
	function listsend_by_days($tm,$days_ago=0) { //$days_ago=0 means one day when tm
		$tm1=$this->dt1($tm-($days_ago*24*60*60));
		$tm2=$this->dt2($tm);
		$num=$this->num_rows($this->query("SELECT uid FROM `vklist_log` WHERE dt='$tm1' GROUP BY uid",0));
		print "<div class='well'>";
		print "<div class='label label-info' >".date("d.m.Y",$tm1)." всего -  <span class='badge' >$num</span></div>";
		$res=$this->query("SELECT err,COUNT(id) AS cnt FROM `vklist_log` WHERE dt='$tm1' GROUP BY err",0);
		while($r=$this->fetch_assoc($res)) {
			if ($r['err']==1002)
				$err="Ручной режим - ПРОПУЩЕНО";
			elseif ($r['err']==0)
				$err="Отправлено успешно";
			elseif ($r['err']==1003)
				$err="Уже в базе - ПРОПУЩЕНО";
			elseif ($r['err']==1004)
				$err="Отправлено приглашение в друзья";
			else
				$err="Ошибка {$r['err']}";
			print "<div>$err  <span class='badge' >{$r['cnt']}</span></div>";
		}
		print "</div>";
	}
	function stat_by_users_detailed($tm,$days_ago=0,$razdel_name,$source_name,$username,$newonly=false) {
		$tm1=$this->dt1($tm-($days_ago*24*60*60));
		$tm2=$this->dt2($tm);
		print "<div class='alert alert-info'>".date("d.m.Y",$tm1)." - ".date("d.m.Y",$tm2)."</div>";
		if($source_name!=="X") {
			if(!$newonly)
				$res=$this->query("SELECT *,cards.comm AS comm, cards.uid AS uid 
							FROM msgs 
							JOIN cards ON msgs.uid=cards.uid 
							JOIN razdel ON cards.razdel=razdel.id 
							JOIN sources ON cards.source_id=sources.id 
							WHERE msgs.tm>=$tm1 AND msgs.tm<=$tm2 AND imp=10 AND razdel_name='$razdel_name' AND source_name='$source_name'
							GROUP BY cards.uid
							ORDER BY surname,name",0);
			else
				$res=$this->query("SELECT *,cards.comm AS comm, cards.uid AS uid 
							FROM cards 
							JOIN razdel ON cards.razdel=razdel.id 
							JOIN sources ON cards.source_id=sources.id 
							WHERE cards.tm>=$tm1 AND cards.tm<=$tm2 AND razdel_name='$razdel_name' AND source_name='$source_name'
							ORDER BY surname,name",0);
		} elseif($username!=="X")
			$res=$this->query("SELECT *,cards.comm AS comm, cards.uid AS uid 
						FROM cards 
						JOIN razdel ON cards.razdel=razdel.id 
						JOIN sources ON cards.source_id=sources.id 
						JOIN msgs ON msgs.uid=cards.uid 
						JOIN users ON msgs.user_id=users.id 
						WHERE msgs.tm>=$tm1 AND msgs.tm<=$tm2 AND razdel_name='$razdel_name' AND username='$username' AND  (outg=1 OR outg=2)
						GROUP BY msgs.uid
						ORDER BY surname,name",0);
		$n=1;
		$bs=new bs;
		print $bs->table(array("№","Раздел","Источник","Юзер","MSGS","Имя",""));
		while($r=$this->fetch_assoc($res)) {
			if(!isset($r['username']))
				$r['username']="";
			$r1=$this->fetch_assoc($this->query("SELECT COUNT(msgs.id) AS cnt FROM msgs WHERE uid={$r['uid']} AND outg=1"));
			$comm1=($r['comm1']!="")?"\n".nl2br($r['comm1']):"";
			//$this->print_r($r);
			print "<tr><td>$n</td>
				<td>{$r['razdel_name']}</td>
				<td>{$r['source_name']}</td>
				<td>{$r['username']}</td>
				<td><a href='javascript:wopen(\"msg.php?uid={$r['uid']}\")'><span class='badge'>{$r['acc_id']}</span></a>
				<td><a href='cp.php?str={$r['uid']}&view=yes&filter=Search' target='_blank'>{$r['surname']} {$r['name']}</a>
				</td>
				<td>".nl2br($r['comm'])." $comm1</td>
				</tr>";
			$n++;
		}
		print "</table>";
	}
	function stat_by_list_groups($tm,$days_ago=0) {
		$tm1=$this->dt1($tm-($days_ago*24*60*60));
		$tm2=$this->dt2($tm);
		$res_r=$this->query("SELECT * FROM razdel WHERE del=0 ORDER BY razdel_name");
		$arr_r=array();
		while($r_r=$this->fetch_assoc($res_r)) {
			$arr_r[]=$r_r['razdel_name'];
		}
		$res=$this->query("SELECT group_name,razdel_name,COUNT(cards.uid) AS cnt FROM cards 
				JOIN razdel ON cards.razdel=razdel.id
				JOIN vklist ON cards.uid=vklist.uid
				JOIN vklist_groups ON vklist_groups.id=group_id
				WHERE cards.tm>=$tm1 AND cards.tm<=$tm2 AND vklist.tm_msg >1
				GROUP BY group_id,razdel_name",0);
		$n=0; $arr=array();
		while($r=$this->fetch_assoc($res)) {
			$arr[$r['group_name']][$r['razdel_name']]=$r['cnt'];
			$n+=$r['cnt'];
		}
		print "<h2><div class='alert alert-info'>".date("d.m.Y",$tm1)." - ".date("d.m.Y",$tm2)."</div></h2>";
		print "<div class='well'>в отчете учитываются рассылки, которые проводились по посетителям из опросов и вступившим в группу, 
			поэтому значения могут быть больше, чем в основном отчете</div>";
		print "\n\n<table class='table table-striped collapse_' id='detailed_$tm1'>\n";
		print "<thead><tr>";
		print "<th>Вид</th>";
		foreach($arr_r AS $razdel) {
			print "<th>$razdel</th>";
		}
		print "</tr></thead>\n";
		foreach($arr AS $grp=>$razdels ) {
			foreach($razdels AS $razdel1=>$cnt) {
				print "<tr>";
				print "<td>$grp</td>";
				foreach($arr_r AS $razdel) {
					if($razdel==$razdel1) {
						//$grp= key($arr[$r_r['razdel_name']]);
						//$cnt=$arr[$r_r['razdel_name']][key($arr[$r_r['razdel_name']])];
						//print " $grp $cnt";
						$cnt=$arr[$grp][$razdel1];
						print "<td>$cnt</td>";
					} else 
						print "<td>&nbsp;</td>";
				}
				print "</tr>\n";
			}
		}
		print "</table>\n\n";
	}
	function stat($tm,$days_ago=0) { //$days_ago=0 means one day when tm
		$tm1=$this->dt1($tm-($days_ago*24*60*60));
		$tm2=$this->dt2($tm);
		print "<div class='well'>";
		$res=$this->query("SELECT source_name,razdel_name,COUNT(cards_uid) AS cnt FROM 
				(SELECT source_name,razdel_name,cards.uid AS cards_uid 
				FROM msgs JOIN cards ON cards.uid=msgs.uid JOIN razdel ON cards.razdel=razdel.id JOIN sources ON cards.source_id=sources.id 
				WHERE msgs.tm>=$tm1 AND msgs.tm<=$tm2 AND imp=10 GROUP BY cards.source_id,razdel,cards.uid) 
				AS Q1 WHERE 1 GROUP BY source_name,razdel_name",0);
		$arr=array();
		while($r=$this->fetch_assoc($res)) {
			$arr[$r['source_name']][$r['razdel_name']]=$r['cnt'];
		}
		//print_r($arr);
		$res_r=$this->query("SELECT * FROM razdel WHERE del=0 ORDER BY razdel_name");
		while($r_r=$this->fetch_assoc($res_r)) {
			$arr_r[]=$r_r['razdel_name'];
		}
		print "<table class='table table-striped'>";
		print "<thead><tr>";
		print "<th>Дата</th>";
		print "<th>Источник</th>";
		foreach($arr_r AS $razdel) {
			print "<th>$razdel</th>";
		}
		print "<th>Всего</th>";
		print "</tr></thead>\n";
		$sum_razd=array();
		$sum_all=0;
		foreach($arr AS $src=>$razdel_name) {
			print "<tr>";
				print "<td>".date("d.m.Y",$tm1)."</td>";
				//if($this->dlookup("id","sources","source_name='$src'")==3)
				//	$src="<a href='javascript:wopen(\"?stat_by_list_groups=yes&tm=$tm&days=$days_ago&no_menu=yes\")'>$src</a>";
				print "<td>$src</td>";
				$sum=0; 
				foreach($arr_r AS $razdel) {
					if(isset($arr[$src][$razdel])) {
						print "<td><a href='javascript:wopen_1(\"?stat_by_users_detailed=yes&tm=$tm&days=$days_ago&razdel_name=$razdel&source_name=$src&username=X&no_menu=yes\")'>".$arr[$src][$razdel]."</a></td>";
						$sum+=$arr[$src][$razdel];
						$sum_all+=$arr[$src][$razdel];
						$sum_razd[$razdel]+=$arr[$src][$razdel];
					} else 
						print "<td>&nbsp;</td>";
				}
				print "<td><b>$sum</b></td>";
			print "</tr>";
		}
		print "<tr>";
			print "<td></td>";
			print "<td>Всего</td>";
			foreach($arr_r AS $razdel) {
				if(isset($sum_razd[$razdel])) {
					$p=round($sum_razd[$razdel]/$sum_all*100,1)."%";
					print "<td><b>".$sum_razd[$razdel]."</b> ($p)</td>";
				} else 
					print "<td>&nbsp;</td>";
			}
			print "<td><span class='badge'>$sum_all</span></td>";
		print "</tr>";
		print "</table>";
		print "</div>";
	}
	function stat_newonly($tm,$days_ago=0) { //$days_ago=0 means one day when tm
		$tm1=$this->dt1($tm-($days_ago*24*60*60));
		$tm2=$this->dt2($tm);
		print "<div class='well'>";
		$res=$this->query("SELECT razdel_name,source_name, COUNT(uid) AS cnt 
					FROM cards 
					JOIN razdel ON cards.razdel=razdel.id 
					JOIN sources ON cards.source_id=sources.id 
					WHERE cards.tm>=$tm1 AND cards.tm<=$tm2
					GROUP BY source_id,razdel",0);
		$arr=array();
		while($r=$this->fetch_assoc($res)) {
			$arr[$r['razdel_name']][$r['source_name']]=$r['cnt'];
		}
		
		$res_r=$this->query("SELECT * FROM razdel WHERE del=0 ORDER BY razdel_name");
		while($r_r=$this->fetch_assoc($res_r)) {
			$arr_r[]=$r_r['razdel_name'];
		}
		print "<table class='table table-striped'>";
		print "<thead><tr>";
		print "<th>Дата</th>";
		print "<th>Вид</th>";
		foreach($arr_r AS $razdel) {
			print "<th>$razdel</th>";
		}
		print "<th>Всего</th>";
		print "</tr></thead>\n";
		$res1=$this->query("SELECT source_id,source_name FROM sources JOIN cards ON source_id=sources.id WHERE cards.tm>=$tm1 AND cards.tm<=$tm2 GROUP BY source_id ORDER BY source_id"); 
		$sum_razd=array();
		$sum_all=0;
		while($r1=$this->fetch_assoc($res1)) {
			print "<tr>";
				print "<td>".date("d.m.Y",$tm1)."</td>";
				if($this->dlookup("id","sources","source_name='{$r1['source_name']}'")==3)
					$src="<a href='javascript:wopen_1(\"?stat_by_list_groups=yes&tm=$tm&days=$days_ago&no_menu=yes\")'>{$r1['source_name']}</a>"; else $src=$r1['source_name'];
				print "<td>$src</td>";
				$sum=0; 
				foreach($arr_r AS $razdel) {
					if(isset($arr[$razdel][$r1['source_name']])) {
						print "<td><a href='javascript:wopen_1(\"?stat_by_users_detailed=yes&newonly=yes&tm=$tm&days=$days_ago&razdel_name=$razdel&source_name={$r1['source_name']}&username=X&no_menu=yes\")'>".$arr[$razdel][$r1['source_name']]."</a></td>";
						$sum+=$arr[$razdel][$r1['source_name']];
						$sum_all+=$arr[$razdel][$r1['source_name']];
						if(isset($sum_razd[$razdel]))
							$sum_razd[$razdel]+=$arr[$razdel][$r1['source_name']];
					} else 
						print "<td>&nbsp;</td>";
				}
				print "<td><b>$sum</b></td>";
			print "</tr>";
		}
		print "<tr>";
			print "<td></td>";
			print "<td>Всего</td>";
			foreach($arr_r AS $razdel) {
				if(isset($sum_razd[$razdel])) {
					$p=round($sum_razd[$razdel]/$sum_all*100,1)."%";
					print "<td><b>".$sum_razd[$razdel]."</b> ($p)</td>";
				} else 
					print "<td>&nbsp;</td>";
			}
			print "<td><span class='badge'>$sum_all</span></td>";
		print "</tr>";
		print "</table>";
		print "</div>";
	}
	function stat_by_users ($tm,$days=0) {
		print "<div class='well'>";
		$tm1=$this->dt1($tm-($days*24*60*60));
		$tm2=$this->dt2($tm);
		$res=$this->query("SELECT username,razdel_name,COUNT(uid) AS cnt FROM
			(SELECT username,razdel_name,msgs.uid AS uid,COUNT(msgs.id) AS cnt
			FROM msgs JOIN users on users.id=msgs.user_id
			JOIN cards ON cards.uid=msgs.uid
			JOIN razdel ON cards.razdel=razdel.id
			WHERE msgs.tm>=$tm1 AND msgs.tm<=$tm2
			AND  (outg=1 OR outg=2) AND imp!=11 AND msgs.user_id!=0
			GROUP BY msgs.user_id,razdel,msgs.uid) AS q1
			WHERE 1 GROUP BY username,razdel_name",0);
		$arr=array();
		while($r=$this->fetch_assoc($res)) {
			$arr[$r['username']][$r['razdel_name']]=$r['cnt'];
		}
		$res_r=$this->query("SELECT * FROM razdel WHERE del=0 ORDER BY razdel_name");
		while($r_r=$this->fetch_assoc($res_r)) {
			$arr_r[]=$r_r['razdel_name'];
		}
		
		print "<table class='table table-striped'>";
		print "<thead><tr>";
		print "<th>Дата</th>";
		print "<th>Вид</th>";
		foreach($arr_r AS $razdel) {
			print "<th>$razdel</th>";
		}
		print "<th>Всего</th>";
		print "</tr></thead>\n";
		$sum_razd=array();
		$sum_all=0;
		foreach($arr AS $username=>$razdel_name) {
			print "<tr>
				<td>".date("d.m.Y",$tm)."</td>
				<td>$username</td>
				";
			$sum=0;
			foreach($arr_r AS $razdel) {
				if(isset($arr[$username][$razdel])) {
					print "<td><a href='javascript:wopen_1(\"?stat_by_users_detailed=yes&tm=$tm&days=$days_ago&razdel_name=$razdel&source_name=X&username=$username&no_menu=yes\")'>{$arr[$username][$razdel]}</a></td>";
					$sum+=$arr[$username][$razdel];
					$sum_razd[$razdel]+=$arr[$username][$razdel];
					$sum_all+=$arr[$username][$razdel];
				} else
					print "<td>&nbsp;</td>";
			}
			print "<td>$sum</td></tr>";
		}
		print "<tr>";
			print "<td>&nbsp;</td>";
			print "<td>Всего</td>";
			foreach($arr_r AS $razdel) {
				if(isset($sum_razd[$razdel])) {
					$p=round($sum_razd[$razdel]/$sum_all*100,1)."%";
					print "<td><b>".$sum_razd[$razdel]."</b> ($p)</td>";
				} else 
					print "<td>&nbsp;</td>";
			}
			print "<td><span class='badge'>$sum_all</span></td>";
		print "</tr>";
		print "</table>";
		print "</div>";
	}
}
?>
