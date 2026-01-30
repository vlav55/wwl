#!/usr/bin/php -q
<?
include "../inc/imap.inc.php";
$CONNECT_ONLY="yes";
$_DB_="t";
$DOMEN="http://t.1-info.ru";

include "../inc/connect.inc.php";
include "func.inc.php";

function check_mbox($mb) {
	global $charset,$htmlmsg,$plainmsg,$attachments;
	global $format;
	//$msg_count = imap_num_msg($mbox);
	//print "Count=$msg_count<br>";
	$mbox=imap_open($mb[0],$mb[1],$mb[2]);
	if(!$mbox) {
		print "mbox open error: {$mb[0]} {$mb[1]}\n";
		print imap_last_error()."\n";
		return false;
	}
	$msgs=imap_search($mbox,"UNSEEN");
	if(!$msgs)
		return;
	//$n=325;
	//getmsg($mbox,$n);
	//print "HERE"; print_r($msgs); print "<br>";
	$res=mysql_query("SELECT MAX(prior) FROM tasks WHERE del=0");
	$r=mysql_fetch_row($res);
	$prior= $r[0]+100;
	foreach($msgs AS $n) {
		$hdr=gethdr($mbox,$n);
		$subj=trim($hdr['subj']);
		getmsg($mbox,$n);
		if($format=='html' AND strlen(trim($plainmsg))==0) {
			$text=$htmlmsg;
		} else {
			$text="".iconv($charset,"cp1251",$plainmsg)."\n";
		}
		$dt=$hdr['dt'];
		$from=$hdr['from'];
		//print "HERE_".$subj; exit;
		@list($s1,$s2)=explode("@",$subj);
		if(@$s2)
			$subj=mb_strtoupper(trim($s2), 'CP1251')."\n$s1"; else $subj=$s1;
		$tm=get_tm("to10");
		mysql_query("INSERT INTO tasks (task,prior,tm,notif) VALUES ('".mysql_real_escape_string($subj)."\n$dt\n".mysql_real_escape_string(trim($text))."',$prior,$tm,1)");
		$prior+=100;
	}
	imap_close($mbox);
}

check_mbox(array("{77.221.129.76:993/imap/ssl/novalidate-cert}INBOX", "t", "fokova142586"));

$res=mysql_query("SELECT * FROM tasks WHERE del=0") or die(mysql_error());
while($r=mysql_fetch_assoc($res)) {
	if($r['tm']>0 AND $r['tm']<(time()-$TIME_SHIFT_SEC)) {
		$s=preg_split("/[\n\r]+/",$r['task']);
		$subj=mb_strtoupper($s[0],"CP1251");

		$msg="<a href='$DOMEN?do_settime=yes&tm=to10&id={$r['id']}'>на 10:00 раб день</a> | ";
		$msg.="<a href='$DOMEN?do_settime=yes&tm=to18sms&sms=yes&id={$r['id']}'>СМС в 18:00 вечер</a> | ";
		$msg.="<a href='$DOMEN?settime=yes&id={$r['id']}'>settime</a> | ";
		$msg.="<a href='$DOMEN?view=yes&id={$r['id']}#r_{$r['id']}'>view</a> | ";
		$msg.="<a href='$DOMEN?do_clrtime=yes&id={$r['id']}'>clr_time</a> | ";
		$msg.="<a href='$DOMEN?del=yes&id={$r['id']}'>del</a> | ";

		$msg.="\n".$r['task'];

		if($r['notif']==1 OR $r['notif']==3) { //send email
			include_once('../inc/phpMailer/class.phpmailer.php');
			$mail= new PHPMailer();
			$mail->IsSMTP(); // telling the class to use SMTP
			$mail->Host="localhost"; //"1-info.ru"; // SMTP server
			$mail->ContentType="text/html";
			$mail->CharSet="windows-1251";
			$mail->AltBody="";
			$mail->From="t@1-info.ru";
			$mail->FromName="t@1-info.ru";
			//$mail->AddAddress("vlav2@1-info.ru", "");
			$mail->AddAddress("vlav@mail.ru", "");
			$mail->Subject='=?windows-1251?B?'.base64_encode($subj).'?=';
			$mail->MsgHTML("<body>".nl2br($msg)."\n</body>");
			if(!$mail->Send()) {
				echo "<h1>Ошибка: " . $mail->ErrorInfo."</h1>";
				exit;
			}
			print "Email notification sent: ".mb_convert_encoding ( $msg,"utf8","cp1251")."\n";
		}
		if($r['notif']==2 OR $r['notif']==3) { //send sms
			$sms="https://ssl.hqsms.com/api/sms.do?username=vlav&password=e4f30a1c738d9c644dbed24273f7e1af&to=79119326112&from=1-info&encoding=windows-1251&message=".urlencode($subj)."&test=0";
			$res=file_get_contents($sms) OR die("file_get_contents error");
			if(substr($res,0,2)!="OK") {
				sms_error($res);
			} else
				print "SMS notification sent OK;\n";
		}
		$next_day=24*60*60;
		if(jddayofweek ( cal_to_jd(CAL_GREGORIAN, date("m"),date("d"), date("Y")) , 0 )==5) //if Fryday
			$next_day=$next_day*3; //to Monday
		//mysql_query("UPDATE tasks SET prior=".get_top().", tm=0, notif=0 WHERE id={$r['id']}") or die(mysql_error());
		//mysql_query("UPDATE tasks SET  tm=".(time()+$next_day).", notif=1 WHERE id={$r['id']}") or die(mysql_error());
		mysql_query("UPDATE tasks SET  tm=".(get_tm("to10")).", notif=1 WHERE id={$r['id']}") or die(mysql_error());
	}
}
print "SCANNED\n";

?>
