<?
$CONNECT_ONLY="yes";
$_DB_="t";
session_start();
include "../inc/connect.inc.php";
include "func.inc.php";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<title>t</title>
<link rel="icon" href="/favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="StyleSheet" href="calendar.css" type="text/css">
<style type='text/css'>
body,table {font-size:9pt;font-family:Verdana,Arial,Helvetica,sans-serif;}
table {border-collapse:collapse;border-width:0px;border-style:solid;border-color:#777;}
P {margin:3;}
H1 {font-size:12pt;}
TD {border-width:0px;border-style:solid;border-color:#774F38;padding:3;}
INPUT,TEXTAREA,SELECT {border-width:1px;border-style:solid;border-color:#777;padding:2;}
TEXTAREA {width: 100%; height:100px;}
FORM {margin:1;}
.red {color:red;}
DIV.task {margin:0 0 20px 0; padding:3px; background-color:#EEE;}
DIV.active_task {margin:0 0 20px 0; padding:3px; background-color:yellow;}
DIV.control {padding:3px; background-color:#DDD; margin-top: 5px;}
.add_button {width:100%; height:80px;margin:10px 0px 10px 0px;}
.add_button:hover {color:blue;}
.save_button {width:150px; height:50px; margin:3px;}
.cancel_button {margin:3px; float:right;}
</style>
<SCRIPT>
function block(id) {
	//alert(id);
	if (document.getElementById(id).style.display == "none") {
		document.getElementById(id).style.display = "block";
	} else {
		document.getElementById(id).style.display = "none";
	}
}
</SCRIPT>
</head>
<body style='width:640px;' >
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
	mysql_query("UPDATE tasks SET mode=0 WHERE id={$_GET['id']}");
}
if(@$_GET['move_to_notes']) {
	$_SESSION['mode']=1;
	mysql_query("UPDATE tasks SET mode=1 WHERE id={$_GET['id']}");
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
	mysql_query("UPDATE tasks SET tm=0,notif=0 WHERE id={$_GET['id']}");
	print "<script>location='?view=yes&id={$_GET['id']}#r_{$_GET['id']}'</script>";
}
if(@$_GET['do_settime']) {
	$tm=get_tm($_GET['tm']);
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
		mysql_query("UPDATE tasks SET tm=$tm, notif=$notif WHERE id={$_GET['id']}");
	}
	print "<script>location='?view=yes&id={$_GET['id']}#r_{$_GET['id']}'</script>";
}
if(@$_GET['settime']) {
	$d=date("d"); //initial settings for calendar
	$m=date("m");
	$y=date("Y");
	$h=0; $min=0;
	$r=mysql_fetch_assoc(mysql_query("SELECT tm,notif FROM tasks WHERE id={$_GET['id']}"));
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
if(@$_GET['del'] || @$_GET['do_del']) {
	mysql_query("UPDATE tasks SET del=1 WHERE id={$_GET['id']}");
	print "<script>location='?view=yes&mess=<h1>Удалено!</h1>&id={$_GET['id']}'</script>";
}
if(@$_POST['do_edit']) {
	mysql_query("UPDATE tasks SET task='".mysql_real_escape_string(trim($_POST['task']))."' WHERE id={$_POST['id']}");
	print "<script>location='?view=yes&id={$_POST['id']}#r_{$_POST['id']}'</script>";
}
if(@$_GET['edit']) {
	print "<h1>Edit task</h1>";
	$r=mysql_fetch_assoc(mysql_query("SELECT task FROM tasks WHERE id={$_GET['id']}"));
	print "<FORM method='POST' action=''>";
	print "<TEXTAREA name='task'>".stripcslashes($r['task'])."</TEXTAREA><br>";
	print "<INPUT type='submit' name='do_edit' value=' Save ' class='save_button'>";
	print "<INPUT type='submit' name='cancel' value='cancel' class='cancel_button'>";
	print "<INPUT type='hidden' name='id' value='{$_GET['id']}'>";
	print "</FORM>";
}
if(@$_POST['do_add']) {
	if(trim($_POST['task'])=="" OR mysql_num_rows(mysql_query("SELECT id FROM tasks WHERE del=0 AND task LIKE '".trim($_POST['task'])."'"))!=0) {
		print "<p class='red'>Empty or double record</p>"; exit;
	}
	$notif=0; $tm=0;
	if(@$_POST['rem1']) {
		$tm=get_tm($_POST['rem1']);
	}
	if(@$_POST['sms'])
		$notif=3;
	mysql_query("INSERT INTO tasks (task, prior, tm, notif) VALUES ('".mysql_real_escape_string(trim($_POST['task']))."', ".get_top().",$tm,$notif)") or die(mysql_error());
	$id=mysql_insert_id();
	$_SESSION['mode']=0;
	print "<script>location='?view=yes&id=$id#r_$id'</script>";
}
if(@$_GET['add']) {
	print "<h1>Add task</h1>";
	print "<FORM method='POST' action='' name='f1'>";
	print "<TEXTAREA name='task'></TEXTAREA><br>";

	print "<INPUT type='radio' name='rem1' value='to10' CHECKED> на 10:00 раб день<br>";
	if(date("H")<18) {
		print "<INPUT type='radio' name='rem1' value='to18sms' onclick='f1.sms.checked=true'> на 18:00 cмс<br>";
	}

	print "<INPUT type='checkbox' name='sms' > также и СМС<br>";
	print "<INPUT type='submit' name='do_add' value=' SAVE '  class='save_button'>";
	print "<INPUT type='submit' name='cancel' value='cancel' class='cancel_button'>";
	print "</FORM>";
}
if(@$_GET['view'] || @$view) {
	if($_SESSION['mode']==0)
		print "<p><span style='background-color:yellow; padding:5px;'>TASKS</span> <a href='?goto_notes=yes'>switch to notes</a></p>"; else print "<p><span style='background-color:yellow; padding:5px;'>NOTES</span> <a href='?goto_tasks=yes'>switch to tasks</a></p>";
	if(@$_GET['mess']) {
		if(@$_GET['id']) {
			$r=mysql_fetch_assoc(mysql_query("SELECT task FROM tasks WHERE id={$_GET['id']}"));
			$task=nl2br(substr($r['task'],0,300));
		}
		print nl2br($_GET['mess']."\n<DIV style='padding:5px; border:1px solid #AAA;'>".$task."</DIV><HR>\n\n");
	}
	print "<input type='button' value='ADD' onClick=\"location='?add=yes'\" class='add_button'><HR>";
	$res=mysql_query("SELECT * FROM tasks WHERE del=0 AND mode={$_SESSION['mode']} ORDER BY tm ASC, prior DESC");
	while($r=mysql_fetch_assoc($res)) {
		if($r['tm'] >0) {
			if(date("H:i", $r['tm'])=="00:00")
				$dt=date("d.m.Y",$r['tm']); else $dt=date("d.m.Y H:i",$r['tm']);
			$dt="<SPAN style='background-color:blue; color:white; padding:2px;'>".$dt."</SPAN>";
		} else $dt="SETTIME";
		/*if($r['tm']>0 AND $r['tm']<time()) {
			mysql_query("UPDATE tasks SET prior=".get_top().", tm=0 WHERE id={$r['id']}");
			print "<script>location='?view=yes&id={$r['id']}'</script>";
		}*/
		if($r['id']==@$_GET['id'])
			$class="active_task"; else $class="task";
		print "<DIV class='$class' id='r_{$r['id']}' onclick='this.style.background_color=\"yellow\"'>";
		$s=preg_split("/[\n\r]+/",$r['task']);
		$i=0;
		foreach($s AS $str) {
			if($i==3) {
				print "<a href='javascript:block(\"b_{$r['id']}\"); void(0);'> ... <a>";
				print "<DIV style='display:none;' id='b_{$r['id']}'>";
			}
			if($i==0)
				print "<SPAN style='color:black;'><b>".mb_strtoupper($str,"CP1251")."</b></SPAN><BR>";
			else
				print "$str<BR>";
			$i++;
		}
		if($i>3) {
			print "</DIV>";
		}
		if($_SESSION['mode']==1)
			$mode_ctrl="<a href='?move_to_tasks=yes&id={$r['id']}#r_{$r['id']}'>move to tasks</a>"; else $mode_ctrl="<a href='?move_to_notes=yes&id={$r['id']}#r_{$r['id']}'>move to notes</a>";

		print "<DIV class='control'>
		<a href='#TOP'>^</a>
		|
		<a href='?top=yes&id={$r['id']}#r_{$r['id']}'>TOP</a>
		<a href='?view=yes&id={$r['id']}#r_{$r['id']}'>SELECT</a>
		<a href='?up=yes&count=1&id={$r['id']}#r_{$r['id']}'>up</a>
		<a href='?down=yes&count=1&id={$r['id']}#r_{$r['id']}'>down</a>
		<a href='?bottom=yes&id={$r['id']}#r_{$r['id']}'>bottom</a>
		|
		<a href='?do_settime=yes&tm=to10&id={$r['id']}'>10:00</a>
		<a href='?do_settime=yes&tm=to18sms&sms=yes&id={$r['id']}'>18:00</a>
		<a href='?do_clrtime=yes&id={$r['id']}'>clr</a>
		<a href='?settime=yes&id={$r['id']}'>$dt</a>
		|
		$mode_ctrl
		|
		<a href='?edit=yes&id={$r['id']}'>EDIT</a>
		<a href='?del=yes&id={$r['id']}'>DEL</a>
		</DIV>

		</DIV>\n";
	}
}
?>
</body>
</html>