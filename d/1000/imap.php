#!/usr/bin/php -q
<?
include_once "/var/www/vlav/data/www/wwl/inc/imap.inc.php";
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include('/var/www/vlav/data/www/wwl/inc/phpMailer/class.phpmailer.php');
function notify($user,$msg) {
	$mail= new PHPMailer();
	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->Host="localhost"; // SMTP server
	$mail->ContentType="text/html";
	$mail->CharSet="utf8";
	$mail->AltBody="";
	$mail->From="baza@test.ru";
	$mail->FromName="baza";
	$mail->AddAddress($user.'@test.ru', "");
	$mail->Subject='baza: new letter for this email grabbed';
	$mail->MsgHTML("<body>".nl2br($msg)."</body>");
	if(!$mail->Send()) {
		echo "<h1>Ошибка: " . $mail->ErrorInfo."</h1>";
		//exit;
	} 
}
function process_email ($mb,$mbox,$hdr,$n) {
	global $charset,$htmlmsg,$plainmsg,$attachments, $tm_lastchecking;
	global $format;
	$db=new db("papa");
	$db->print_r($hdr);
	print "CHARSET=".$charset."<br>\n";

	getmsg($mbox,$n);
//	$msg=preg_replace("|\<img src=\"https:\/\/jc\.yogahelpyou\.com\/mailview\/(.*)\<\/small\>|s","",$htmlmsg);
//	$msg=preg_replace("~(\<a(.*)\>)|(\<\/a\>)~","~",$msg);
	$e=preg_match("/\%(.*?)\%/",$hdr['subj'],$match);
//	print_r($match);
	if(@filter_var($match[1], FILTER_VALIDATE_EMAIL))
		$email_from=$match[1]; else $email_from=$hdr['from'];
	print "email_from=$email_from <br>\n";

	if(empty($plainmsg))
		$msg= $htmlmsg; else $msg=$plainmsg;
	
	$msg="EMAIL:{$hdr['subj']}\n".$msg;
	$uid=$db->dlookup("uid","cards","email='$email_from'",0);
	if($uid) {
		print "uid=$uid \n";
		$db->query("INSERT INTO msgs SET
					uid='$uid',
					acc_id=100,
					tm='".time()."',
					msg='".$db->escape($msg)."',
					outg=0
					");
		$db->mark_new($uid,$fl=2);
		$db->notify($uid,substr($msg,0,100),100);
	} else {
		print "email_from=$email_from is not in cards \n";
		imap_clearflag_full($mbox, $n, "\\Seen");
	}
	return; 
}
function check_mbox($mb) {
	global $charset,$htmlmsg,$plainmsg,$attachments, $tm_lastchecking;
	global $format;
	//$msg_count = imap_num_msg($mbox);
	//print "Count=$msg_count<br>";
	
	$mbox=imap_open($mb[0],$mb[1],$mb[2]);
	if(!$mbox) {
		print "mbox open error: {$mb[0]} {$mb[1]}\n";
		print imap_last_error()."\n";
		return false;
	}
	
	//$n=325;
	//getmsg($mbox,$n);
	//print "HERE"; print_r($msgs); print "<br>";
	
	if(strpos($mb[0],"pop3")===false) { //if IMAP 
		$msgs=imap_search($mbox,"UNSEEN");
		if(!$msgs) return;
		foreach($msgs AS $n) {
			$hdr=gethdr($mbox,$n);
			process_email ($mb,$mbox,$hdr,$n);
		}
	} else {
		$count = imap_num_msg($mbox);
		print "CNT=".$count."<br>\n";
		for($n=$count; $n>0; $n--) {
			$hdr=gethdr($mbox,$n);
			if($hdr['tm']<$tm_lastchecking)
				break;
			print "N=".$n."<br>\n";
			print_r ($hdr);
			process_email ($mb,$mbox,$hdr,$n);
		}
	}
	imap_close($mbox);
	print "<hr>\n";


	/*
	foreach($attachments AS $key=>$val) {
		$f=imap_mime_header_decode($key);
		$fname=trim(iconv($f[0]->charset,"cp1251",$f[0]->text));
		print $fname."<br>";
		//file_put_contents("/home/vlav/".iconv("cp1251","utf8",$fname),$val);;
	}

	*/
}


$mboxes=array(
	array("{imap.mail.ru:993/imap/ssl/novalidate-cert}INBOX", "info@formula12.ru", "iyaG:A96nuBP", 5),
	);
print "<br>\n";

foreach($mboxes AS $mb) {
	print $mb[0]." ".$mb[1]."<br>\n";
	$res=check_mbox($mb);
}
//check_wrong_emails($mboxes);
?>
