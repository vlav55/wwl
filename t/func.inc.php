<?
define ('STEP', 100);
$TIME_SHIFT_SEC=1*60*60; //summer/winter time correction

function get_tm($val) {
	global $db;
	if(is_numeric($val))
		return $val;

	$offset=(24*60*60);
	switch($val) {
		case "to10":
			$notif=1;
			if(date("H")<10)
				$offset=0;
			$tm1=time()+$offset;
			$tm=mktime(10,0,0,date("m",$tm1), date("d",$tm1), date("Y",$tm1));
			//print "HERE_".date("d.m.Y H:i",$tm)." $tm\n"; exit;
			//~ if(date("N",$tm)==6) //Saturday
				//~ $tm+=$offset*2;
			//~ if(date("N",$tm)==7) //Sunday
				//~ $tm+=$offset*1;
			return $tm;
		case "to18sms":
			$notif=3;
			$tm1=time();
			$tm=mktime(18,0,0,date("m",$tm1), date("d",$tm1), date("Y",$tm1));
			return $tm;
	}

	$val=trim($val);
	$arr=explode(" ",$val);
	if(sizeof($arr)!=2) {
		$val.=" 10:00";
	}
	$arr=explode(" ",$val);
	$arr_dt=explode(".",$arr[0]); $arr_tm=explode(":",$arr[1]);
	if(sizeof($arr_dt)==3) {
		list($d,$m,$y)=$arr_dt;
		if(sizeof($arr_tm)==2) {
			list($h,$i)=$arr_tm;
			$tm=mktime($h,$i,0,$m,$d,$y);
					//print "$d $m $y $h $i $tm"; exit;
			if( date("d",$tm)==$d && date("m",$tm)==$m && date("Y",$tm)==$y && date("H",$tm)==$h && date("i",$tm)==$i) {
				return $tm; //print "$d $m $y $h $i"; exit;
				if($tm>time()) {
					return $tm; //print "$d $m $y $h $i"; exit;
				} else {
					if(date("d.m.Y")==date("d.m.Y",$tm)) {
						return time()+(1*60*60);
					} else {
						$now=date("d.m.Y H:i");
						print "<p class='red'> - <b>$now</b>,  <b>$val</b><br></p>";
						return $tm;
					}
				}
			}
		}
	}
	print "<p class='red'>: <b>$val</b></p>";
	return false;
}
function save_prior($id, $prior) {
	global $db;
	$db->query("UPDATE tasks SET prior=$prior WHERE id=$id");
}
function get_top() {
	global $db;
	$res=$db->query("SELECT MAX(prior) FROM tasks WHERE del=0");
	$r=$db->fetch_row($res);
	return $r[0]+STEP;
}
function get_bottom() {
	global $db;
	return 0;
}
function get_up($id, $num) {
	global $db;
	$res=$db->query("SELECT prior FROM tasks WHERE id=$id");
	if(!$res) return false;
	$r=$db->fetch_row($res);
	$prior=$r[0];
	$res=$db->query("SELECT prior FROM tasks WHERE del=0 AND prior>0 AND prior>$prior ORDER BY prior ASC");
	if(!$res) return false;
	if(mysql_num_rows($res)>$num) {
		for($i=0; $i<$num; $i++) {
			$r=$db->fetch_row($res); $p1=$r[0];
		}
		$r=$db->fetch_row($res); $p2=$r[0];
		$p=($p1+$p2)/2;
	} elseif(mysql_num_rows($res)==$num) {
		return get_top();
	} else return false;
	return $p;
}
function get_down($id, $num) {
	global $db;
	$res=$db->query("SELECT prior FROM tasks WHERE id=$id");
	if(!$res) return false;
	$r=$db->fetch_row($res);
	$prior=$r[0];
	$res=$db->query("SELECT prior FROM tasks WHERE del=0 AND prior>0 AND prior<$prior ORDER BY prior DESC");
	if(!$res) return false;
	if(mysql_num_rows($res)>$num) {
		for($i=0; $i<$num; $i++) {
			$r=$db->fetch_row($res); $p1=$r[0];
			//print "$i $p1<br>";
		}
		$r=$db->fetch_row($res); $p2=$r[0];
		$p=($p1+$p2)/2;
	} elseif(mysql_num_rows($res)==$num) {
		return get_bottom();
	} else return false;
	return $p;
}
function sms_error($res) {
	global $db;
	include_once('../inc/phpMailer/class.phpmailer.php');
	$mail= new PHPMailer();
	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->Host="localhost"; //"1-info.ru"; // SMTP server
	$mail->ContentType="text/html";
	$mail->CharSet="windows-1251";
	$mail->AltBody="";
	$mail->From="t@1-info.ru";
	$mail->FromName="t@1-info.ru";
	$mail->AddAddress("vlav@mail.ru", "");
	$mail->Subject='=?windows-1251?B?'.base64_encode("SMS SEND ERROR").'?=';
	$mail->MsgHTML("<body>".nl2br($res)."</body>");
	if(!$mail->Send()) {
		echo "<h1>Îøèáêà: " . $mail->ErrorInfo."</h1>";
		exit;
	}
}

?>
