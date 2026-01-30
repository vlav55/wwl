#!/usr/bin/php -q
<?
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
	$msgs=imap_search($mbox,"UNSEEN FROM e0035@yandex.ru");
	if(!$msgs)
		return;
	foreach($msgs AS $n) {
		$hdr=gethdr($mbox,$n);
		$subj=trim($hdr['subj']);
		getmsg($mbox,$n);
		if($format=='html' AND strlen(trim($plainmsg))==0) { //HTML
			$msg="".iconv($charset,"cp1251",strip_tags($htmlmsg))."\n";
		} else { //PLAIN
			$res=iconv("utf-8","cp1251",$plainmsg);
			if(!$res)
				$res=iconv("koi8-r","cp1251",$plainmsg);
			if(!$res)
				$res=iconv("cp1251","cp1251",$plainmsg);
			if(!$res) {
				$res=iconv($charset,"cp1251//TRANSLIT",strip_tags($htmlmsg));
				file_put_contents("2.txt",$plainmsg);
				file_put_contents("1.html",$htmlmsg);
				file_put_contents("3.html",strip_tags($htmlmsg));
			}
			$msg="".$res."\n";
		}
		$dt=$hdr['dt'];
		$from=$hdr['from'];
		print_r($hdr);

		print "<HR><b>f=$format $charset $n $dt $from $subj</b><br>";
		print nl2br($msg);

	}
	imap_close($mbox);
}

include "../../inc/imap.inc.php";
$CONNECT_ONLY="yes";
$_DB_="t";
include "../../inc/connect.inc.php";

check_mbox(array("{77.221.129.76:993/imap/ssl/novalidate-cert}INBOX", "vlav2", "vlav@142586-email"));

?>