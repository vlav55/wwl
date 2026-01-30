<?php
session_start();
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db('t');
include "func.inc.php";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<title>t</title>
	<link rel="icon" href="/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
  <meta charset="UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src="calendar_ru.js" type="text/javascript"></script>
	<style>
		@media (min-width:960px) {
		/* styles for browsers larger than 960px; */
			body {width:800px;margin-left:100px;}
		}
		input,textarea {border-collapse:collapse;border-style:solid; border-width:1px; border-color:#CCC;}
	</style>
</head>
<body>
<div class="container-fluid">
<?

if(!isset($_SESSION['mode']))
	$_SESSION['mode']=0;
if(@$_GET['goto_notes']) {
	$_SESSION['mode']=1;
	$view="yes";
}
if(@$_GET['goto_tasks']) {
	$_SESSION['mode']=0;
	$view="yes";
}
if(@$_GET['move_to_tasks']) {
	$_SESSION['mode']=0;
	$db->query("UPDATE tasks SET mode=0 WHERE id={$_GET['id']}");
}
if(@$_GET['move_to_notes']) {
	$_SESSION['mode']=1;
	$db->query("UPDATE tasks SET mode=1 WHERE id={$_GET['id']}");
	$view="yes";
}
if(@$_POST['cancel'] || @$_GET['cancel'] || sizeof($_GET)==0)
	print "<script>location='?view=yes&id={$_GET['id']}#r_{$_GET['id']}'</script>";
if(@$_GET['top']) {
	save_prior( $_GET['id'],get_top());
	$view="yes";
}
if(@$_GET['up']) {
	save_prior( $_GET['id'], get_up($_GET['id'], $_GET['count']));
	$view="yes";
}
if(@$_GET['down']) {
	save_prior( $_GET['id'], get_down($_GET['id'], $_GET['count']));
	$view="yes";
}
if(@$_GET['bottom']) {
	save_prior( $_GET['id'], get_bottom());
	$view="yes";
}
if(@$_GET['do_clrtime']) {
	$db->query("UPDATE tasks SET tm=0,notif=0 WHERE id={$_GET['id']}");
	print "<script>location='?view=yes&id={$_GET['id']}#r_{$_GET['id']}'</script>";
}
if(@$_GET['do_settime']) {
	$id=intval($_GET['id']);
	$tm=0;
	if(isset($_GET['tm0'])) {
		$tm=intval($_GET['tm0']);
	}
	if(isset($_GET['tm'])) {
		//$tm=get_tm($_GET['tm']);
		list($y,$m,$d)=explode('-',$_GET['tm']);
		$tm=intval(mktime(0,0,0,$m,$d,$y));
	}
	if(isset($_GET['tm1'])) {
		list($h,$m)=explode(':',$_GET['tm1']);
		$tm+=intval($h*60*60+$m*60);
	}
	if($tm) {
		$notif=1;
		$db->query("UPDATE tasks SET tm='$tm', notif='$notif' WHERE id='$id'");
	}
	/*
	if($tm!==false) {
		//print "$tm $id"; exit;
		$notif=0;
		if(isset($_GET['send_sms']) && isset($_GET['send_email']))
			$notif=3;
		if(isset($_GET['send_sms']) && !isset($_GET['send_email']))
			$notif=2;
		if(!isset($_GET['send_sms']) && isset($_GET['send_email']))
			$notif=1;
		if(@$_GET['sms'])
			$notif=3;
		$db->query("UPDATE tasks SET tm=$tm, notif=$notif WHERE id={$_GET['id']}");
	}
	*/
	print "<script>location='?view=yes&id=$id#r_$id'</script>";
}
if(@$_GET['settime']) {
	$d=date("d"); //initial settings for calendar
	$m=date("m");
	$y=date("Y");
	$h=0; $min=0;
	$r=$db->fetch_assoc($db->query("SELECT tm,notif FROM tasks WHERE id={$_GET['id']}"));
	if($r['tm'] !=0) {
		$tm=$r['tm']; $d=date("d",$tm);$m=date("m",$tm);$y=date("Y",$tm);$h=date("H",$tm);$min=date("i",$tm);
		//print "HERE $d $m $y h=$h i=$i"; exit;
	}
	$hm="$h:$min";
	include "calendar.php";
	if($r['notif']==1 OR $r['notif']==3)
		$chk1="CHECKED"; else $chk1="";
	if($r['notif']==2 OR $r['notif']==3)
		$chk2="CHECKED"; else $chk2="";
	$chk1="CHECKED";
	print "
	<form name='f1' method='GET'>
	SMS <input type='checkbox' name='send_sms' $chk2>
	EMAIL <input type='checkbox' name='send_email' $chk1>
	<br>
	<input type='submit' name='do_settime' value='SET' style='width:120px; height:30px; padding:5px;'>
	<input type='submit' name='cancel' value='CANCEL'>
	</td><td style='margin-left:10;'>
	<input type='hidden' name='tm' value='$d.$m.$y $hm'>
	<input type='hidden' name='id' value='{$_GET['id']}'>
	";
}
if(@$_GET['do_del']) {
	$db->query("UPDATE tasks SET del=1 WHERE id={$_GET['id']}");
	print "<script>location='?view=yes&mess=<h1>Удалено!</h1>&id={$_GET['id']}'</script>";
}
if(@$_GET['del']) {
	$msg=$db->fetch_assoc($db->query("SELECT task FROM tasks WHERE id={$_GET['id']}"))['task'];
	print "<p class='alert alert-warning' >
		".nl2br($msg)."
		<a href='?do_del=yes&id={$_GET['id']}' class='btn btn-primary' target=''>Confirm</a>
		<a href='?view=yes&id={$_GET['id']}' class='btn btn-default' target=''>Cancel</a>
		</p>";
}
if(@$_POST['do_edit']) {
	$db->query("UPDATE tasks SET task='".$db->escape(trim($_POST['task']))."' WHERE id={$_POST['id']}");
	print "<script>location='?view=yes&id={$_POST['id']}#r_{$_POST['id']}'</script>";
}
if(@$_GET['edit']) {
	print "<h1>Edit task</h1>";
	$r=$db->fetch_assoc($db->query("SELECT task FROM tasks WHERE id={$_GET['id']}"));
	print "<FORM method='POST' action='#'><div class='form-group'>";
	print "<TEXTAREA class='form-control' name='task' style='height:200px;'>".stripcslashes($r['task'])."</TEXTAREA><br>";
	print "<INPUT class='btn btn-primary' type='submit' name='do_edit' value=' Save ' class='save_button'>&nbsp;";
	print "<INPUT class='btn btn-default' type='submit' name='cancel' value='cancel' class='cancel_button'>";
	print "<INPUT type='hidden' name='id' value='{$_GET['id']}'>";
	print "</div></FORM>";
}
if(@$_POST['do_add']) {
	if(trim($_POST['task'])=="" || $db->num_rows($db->query("SELECT id FROM tasks WHERE del=0 AND task LIKE '".trim($_POST['task'])."'"))!=0) {
		print "<p class='red'>Empty or double record</p>";
		exit;
	}
	$notif=0; $tm=0;
	if(@$_POST['rem1']) {
		$tm=get_tm($_POST['rem1']);
	}
	$hm1=$db->dt1(time())+(9*60*60);
	$hm2=$db->dt1(time())+(16*60*60);
	$tm=rand($hm1,$hm2);
	if(@$_POST['sms'])
		$notif=3;
	$db->query("INSERT INTO tasks (task, prior, tm, notif) VALUES ('".$db->escape(trim($_POST['task']))."', ".get_top().",$tm,$notif)") or die($db->error());
	$id=$db->insert_id();
	$_SESSION['mode']=0;
	print "<script>location='?view=yes&id=$id#r_$id'</script>";
}
if(@$_GET['add']) {
	print "<h1>Add task</h1>";
	print "<FORM method='POST' action='#' name='f1'><div class='form-group'>";
	print "<TEXTAREA  class='form-control' name='task' style='height:150px;'></TEXTAREA><br>";

	//print "<INPUT class='form-control' type='radio' name='rem1' value='to10' CHECKED> to 10:00<br>";
	print "<INPUT class='form-control' type='hidden' name='rem1' value='to10' >";
	//~ if(date("H")<18) {
		//~ print "<INPUT class='form-control' type='radio' name='rem1' value='to18sms' onclick='f1.sms.checked=true'> to 18:00<br>";
	//~ }

	//print "<INPUT class='form-control' type='checkbox' name='sms' > SMS<br>";
	print "<INPUT class='btn btn-primary btn-lg' type='submit' name='do_add' value=' SAVE '  class='save_button'>&nbsp;&nbsp;&nbsp;";
	print "<INPUT class='btn btn-default' type='submit' name='cancel' value='cancel' class='cancel_button'>";
	print "</div></FORM>";
}
if(@$_GET['view'] || @$view) {
	if($_SESSION['mode']==0)
		print "<p><span style='background-color:yellow; padding:5px;'>TASKS</span> <a href='?goto_notes=yes'>switch to notes</a></p>"; else print "<p><span style='background-color:yellow; padding:5px;'>NOTES</span> <a href='?goto_tasks=yes'>switch to tasks</a></p>";
	if(@$_GET['mess']) {
		if(@$_GET['id']) {
			$r=$db->fetch_assoc($db->query("SELECT task FROM tasks WHERE id={$_GET['id']}"));
			$task=nl2br(substr($r['task'],0,300));
		}
		print nl2br($_GET['mess']."\n<DIV style='padding:5px; border:1px solid #AAA;'>".$task."</DIV><HR>\n\n");
	}
	print "<input type='button' class='btn btn-primary' value='ADD' onClick=\"location='?add=yes'\"><HR>";
	$res=$db->query("SELECT * FROM tasks WHERE del=0 AND mode={$_SESSION['mode']} ORDER BY tm ASC, prior DESC");
	print "<div class='panel-group_'>";
	$rec=0;
	while($r=$db->fetch_assoc($res)) {
		if($r['tm'] >0) {
			//~ if(date("H:i", $r['tm'])=="00:00")
				//~ $dt=date("d.m.Y",$r['tm']); else $dt=date("d.m.Y H:i",$r['tm']);
			$dt=date("Y-m-d",$r['tm']);
			$s1="background-color:#EEE;color:#333;";
			//$dt="<SPAN class='badge'>".$dt."</SPAN>";
			$tm1=date("H:i",$r['tm']);
		} else { $dt=""; $s1='background-color:#EEE';}
		/*if($r['tm']>0 AND $r['tm']<time()) {
			$db->query("UPDATE tasks SET prior=".get_top().", tm=0 WHERE id={$r['id']}");
			print "<script>location='?view=yes&id={$r['id']}'</script>";
		}*/
		if($r['id']==@$_GET['id'])
			$class="panel-primary"; else $class="panel-info";
		$s=preg_split("/[\n\r]+/",$r['task']);
		
		print "\n<DIV class='panel $class' id='r_{$r['id']}'>\n";
			print "<div class='panel-heading' onclick='location=\"?view=yes&id={$r['id']}#r_{$r['id']}\"'><b>{$s[0]}</b></div>\n";
			print "<div class='panel-body'>\n";
			$i_hdr=3;
			for($i=0;$i<$i_hdr;$i++) {
				if($i>=sizeof($s))
					break;
				if(empty(trim($s[$i]))) {
					$i_hdr++; continue;
				}
				print $db->make_link_clickable($s[$i])."<br>";
			}
			if(sizeof($s)>$i_hdr) {
				print " <a href='javascript:return(false);' data-toggle='collapse' data-target='#rec_$rec'>more...</a>";
				print "<div class='collapse' id='rec_$rec'>";
				for($i=$i_hdr;$i<sizeof($s);$i++) {
					print $db->make_link_clickable($s[$i])."<br>";
				}
				print "</div>";
			}
			//print nl2br($r['task']);

			
			print "\n</div>\n";
			
			if($_SESSION['mode']==1)
				$mode_ctrl="<a href='?move_to_tasks=yes&id={$r['id']}#r_{$r['id']}'>move to tasks</a>"; else $mode_ctrl="<a href='?move_to_notes=yes&id={$r['id']}#r_{$r['id']}'>move to notes</a>";
			print "<form><DIV class='panel-footer'>\n
			<a href='#TOP'><span class='glyphicon glyphicon-menu-up'></span></a>
			<span class='glyphicon glyphicon-option-vertical'></span>
			<a href='?view=yes&id={$r['id']}#r_{$r['id']}'  title='SELECT'><span class='glyphicon glyphicon-menu-hamburger'></span></a>
			<span class='glyphicon glyphicon-option-vertical'></span>
			<a href='?top=yes&id={$r['id']}#r_{$r['id']}' title='to TOP'><span class='glyphicon glyphicon-triangle-top'></a>
			<a href='?up=yes&count=1&id={$r['id']}#r_{$r['id']}' title='UP'><span class='glyphicon glyphicon-circle-arrow-up'></span></a>
			<a href='?down=yes&count=1&id={$r['id']}#r_{$r['id']}' title='DOWN'><span class='glyphicon glyphicon-circle-arrow-down'></span></a>
			<a href='?bottom=yes&id={$r['id']}#r_{$r['id']}' title='to BOTTOM'><span class='glyphicon glyphicon-triangle-bottom'></span></a>
			<span class='glyphicon glyphicon-option-vertical'></span>
			<!--
			<a href='?do_settime=yes&tm=to10&id={$r['id']}'>10:00</a>
			<a href='?do_settime=yes&tm=to18sms&sms=yes&id={$r['id']}'>18:00</a>
			-->
			<!--<a href=''><a href='?settime=yes&id={$r['id']}'>$dt</a></a>-->

			<input  class='dt' style='text-align:center;width:120px;$s1' type='date' name='tm' value='$dt' 
			>

			<input style='text-align:center;width:80px;$s1' type='time' name='tm1' value='$tm1'>
			
			<input type='hidden' name='id' value='{$r['id']}'>
			<button type='submit' name='do_settime' value='yes' class='btn btn-primary' >Set</button>
			<!--<button onclick='location=\"?do_clrtime=yes&id={$r['id']}\";return(false);'>clr</button>-->
			<span class='glyphicon glyphicon-option-vertical'></span>
			<a href='?edit=yes&id={$r['id']}' style='font-size:24px;'><span class='glyphicon glyphicon-edit'></span></a>
			<span class='glyphicon glyphicon-option-vertical'></span>
			<a href='?del=yes&id={$r['id']}'><span class='glyphicon glyphicon-remove'></span></a>
			</DIV></form>\n";
		print "</div>\n";
		$rec++;
	}
	print "</div>";
}
?>
</div>
</body>
</html>
