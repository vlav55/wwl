<?php
function get_style_by_razdel($razdel) {
	$style_C="color:black; background-color:#FFC6B6;";
	$style_B="color:black; background-color:#CBFF81;";
	$style_A="color:black; background-color:yellow;";
	$style_D="color:white; background-color:#444;";
	$style_Del="color:yellow; background-color:#111;";
	$style_O="color:black; background-color:#F3C8FF;";
	$style_C1="color:black; background-color:#FFC6B6;";
	$style_B1="color:black; background-color:#CBFF81;";
	$style_A1="color:black; background-color:yellow;";
	switch($razdel) {
		case 1: return $style_C; 
		case 2: return $style_B; 
		case 3: return $style_A; 
		case 4: return $style_D; 
		case 5: return $style_O; 
		case 6: return $style_Del; 
		case 7: return $style_C1; 
		case 8: return $style_B1; 
		case 9: return $style_A1; 
	}
	if($razdel==1)
		$s=$style_C; elseif($razdel==2)
			$s=$style_B; elseif($razdel==3)
				$s=$style_A; elseif($razdel==4)
					$s=$style_D; elseif($razdel==5)
						$s=$style_O; else $s="";
	return $s;
}


function get_ops_info_($id) {
	return 0;
	$db=new db;
	$res=$db->query("SELECT COUNT(debit) AS cnt, MIN(tm) AS tm1, MAX(tm) AS tm2 FROM ops WHERE klid=$id AND debit>0 AND fake=0");
	if(!$res || mysql_num_rows($res)==0)
		return false;
	$r=$db->fetch_assoc($res);
	return array('tm1'=>$r['tm1'],'tm2'=>$r['tm2'],'cnt'=>$r['cnt']);
}
function print_ops_($klid,$ctrl=true,$lastid=0) {
	return 0;
	$db=new db;
	$r=$db->fetch_assoc($db->query("SELECT (SUM(kredit)-SUM(debit)) AS dif FROM `ops` WHERE klid=$klid"));
	//if($r['dif']==0) return;
	print "<h1>Остаток на сегодня :{$r['dif']}</h1>";
	
	$res=mysql_query("SELECT * FROM ops WHERE klid=$klid ORDER BY tm ASC,kredit DESC");
	print "<HR><table class='ops' width='100%'>";
		print "<tr style='color:blue;'><td style='width:80px;text-align:center;'>Дата</td>
		<td style='width:60px;'>Приход</td>
		<td style='width:60px;'>Расход</td>
		<td style='width:80px;'>Остаток в нак.</td>
		<td style='width:60px;'>Цена за занятие</td>
		<td style='width:60px;'>Осталось занятий</td>
		<td style='width:20px;'>fake</td>
		<td style='text-align:left;'>Комментарий</td>
		<td style='width:50px;'>Уд.</td></tr>";
	$ost=0; $price=0;
	while($r=mysql_fetch_assoc($res)) {
		if($r['id']==$lastid)
			$cur="class='ops_tr_cur'"; else $cur="class='ops_tr'";
		if(@$_GET['ops_edit'] && @$_GET['id']==$r['id'] && $ctrl) {
			print "<form><tr $cur id='r_{$r['id']}'>
			<td style='width:80px;text-align:center;'><input type='text' name='dt' value='".date("d.m.Y",$r['tm'])."' style='width:80px;text-align:center;' onfocus='this.select();lcs(this)' onclick='event.cancelBubble=true;this.select();lcs(this)'></td>
			<td style='width:60px;'><input type='text' name='kredit' value='{$r['kredit']}' style='width:60px;text-align:center;'></td>
			<td style='width:60px;'><input type='text' name='debit' value='{$r['debit']}' style='width:60px;text-align:center;'></td>
			<td style='width:80px;'>&nbsp;</td>
			<td style='width:60px;'><input type='text' name='price' value='{$r['price']}' style='width:60px;text-align:center;'></td>
			<td style='width:60px;'>&nbsp;</td>";
			if($r['fake']==1)
				$chk="checked"; else $chk="";
			print "<td style='width:60px;'><input type='checkbox' name='fake' $chk style='width:60px;text-align:center;'></td>
			<td style='text-align:left;'><textarea name='comm' style='width:150px;height:40px;text-align:left;'>{$r['comm']}</textarea></td>
			<td style='width:50px;'>
				<input type='hidden' name='id' value='{$r['id']}'>
				<input type='submit' name='do_ops_edit' value='save'>
			</td></tr></form>";
		} else {
			//if($r['kredit']>0)
			$price=$r['price'];
			if($price==0) {
				$r1=mysql_fetch_assoc(mysql_query("SELECT * FROM ops WHERE klid=$klid AND tm<={$r['tm']} AND price>0 ORDER BY tm DESC"));
				if($r1) {
					$price=$r1['price']; 
					mysql_query("UPDATE ops SET price='$price' WHERE id={$r['id']}");
				} else $price=0;
				//print "HERE_$price"; 
			}
			$ost=$ost+$r['kredit']-$r['debit'];
			if($price>0)
				$cnt=round($ost/$price,0); else $cnt="-";
			print "<tr $cur id='r_{$r['id']}'>
			<td style='width:80px;text-align:center;'>".date("d.m.Y",$r['tm'])."</td>
			<td style='width:60px;'>{$r['kredit']}</td>
			<td style='width:60px;'>{$r['debit']}</td>
			<td style='width:80px;'>$ost</td>
			<td style='width:60px;'>$price</td>
			<td style='width:60px;'>".$cnt."</td>
			<td style='width:60px;'>{$r['fake']}</td>
			<td style='text-align:left;'>".nl2br($r['comm'])."</td>";
			print "<td style='width:50px;'>";
			if($ctrl)
				print "<a href='?ops_edit=yes&id={$r['id']}#r_{$r['id']}'>edit</a> <a href='?ops_del=yes&id={$r['id']}'>del</a>"; else print "&nbsp;";
			print "</td></tr>";
		}
	}
	print "<table>";
	print "<script>location.hash='r_$lastid'</script>";
}

function date2tm($str) {
	$dmy=explode(".",$str);
	$tm=mktime(0,0,0,$dmy[1],$dmy[0],$dmy[2]);
	if($str!=date("d.m.Y",$tm)) {
		print "<p class='red'>Ошибка в формате даты : <b>$str</b>. Должно быть dd.mm.YYYY</p>";
		return false;
	}
	return $tm;
}
function sum2int($str) {
	if(is_numeric($str)) {
		if($str<=0) {
			print "<p class='red'>Ошибка: сумма <b>$str</b> не может быть нулем</p>";
			$err=true;
		}
		return (int)$str;
	} else {
		print "<p class='red'>Ошибка: сумма <b>$str</b> не цифровое значение</p>";
		return false;
	}
}
function sum2int_0($str) {
	if(is_numeric($str)) {
		return (int)$str;
	} else {
		print "<p class='red'>Ошибка: сумма <b>$str</b> не цифровое значение</p>";
		return false;
	}
}
function get_price($klid) {
	$p=mysql_fetch_row(mysql_query("SELECT price FROM ops WHERE klid=$klid AND price>0 ORDER BY tm DESC LIMIT 1"));
	if($p) return($p[0]); else return 400;
}
function login() {
	print "<form>";
	print "Login : <input type='text' name='username' value=''>";
	print "<input type='password' name='passw' value=''>";
	print "<input type='submit' name='do_login' value='Login'>";
	print "</form>";
}
?>