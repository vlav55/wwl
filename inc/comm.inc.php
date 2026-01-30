<?
exit;
$db=new top($database,"640px;",false, $favicon);

$c_razdel=(@$_GET['active']=='razdel')?"active":"";
$c_comm=(@$_GET['active']=='comm')?"active":"";
$c_mob="";
$c_insta="";
$c_scdl=(@$_GET['active']=='scdl')?"active":"";
$c_delay=(@$_GET['active']=='delay')?"active":"";

if(@$_GET['do_comm_save']) {
	$c_comm="active";
	$db->save_comm($_GET['uid'],$db->userdata['user_id'],$_GET['comm'],0,0,0);
	print "<script>opener.location.reload();</script>";
}
if(@$_GET['do_insta_save']) {
	$c_insta="active";
	$db->query("UPDATE cards SET insta='".$db->escape($_GET['insta'])."' WHERE id={$_GET['klid']}");
	print "<script>opener.location.reload();</script>";
}
if(@$_GET['do_mob_save']) {
	$c_mob="active";
	$db->query("UPDATE cards SET mob='".$db->escape($_GET['mobile'])."' WHERE id={$_GET['klid']}");
	print "<script>opener.location.reload();</script>";
}
if(@$_GET['do_razdel_save']) {
	$c_razdel="active";
	$db->query("UPDATE cards SET razdel='{$_GET['razdel']}' WHERE id={$_GET['klid']}");
	$r1=$db->fetch_assoc($db->query("SELECT * FROM razdel WHERE id={$_GET['razdel']}"));
	$db->query("INSERT INTO msgs SET 
			uid={$_GET['uid']},
			acc_id=0,
			mid=0,
			tm=".time().",
			user_id=".$_SESSION['userid_sess'].",
			msg='".$db->escape("Раздел изменен на {$r1['razdel_name']} пользователем {$_SESSION['username']}")."',
			outg=2,
			imp=11,
			razdel_id={$_GET['razdel']}");
	print "<script>opener.location.reload();</script>";
}
if(@$_GET['do_scdl_set']) {
	$c_scdl="active";
	if($tm=$db->date2tm($_GET['dt_scdl'])) {
		$db->query("UPDATE cards SET tm_schedule=$tm WHERE id={$_GET['klid']} ",0);
		print "<div class='alert alert-success'>Scheduled</div>";
		print "<script>opener.location.reload();</script>";
	}
}
if(@$_GET['do_scdl_del']) {
	$c_scdl="active";
	if($tm=$db->date2tm($_GET['dt_scdl'])) {
		$db->query("UPDATE cards SET tm_schedule=0 WHERE id={$_GET['klid']} ");
		print "<div class='alert alert-warning'>Schedule cleared</div>";
		print "<script>opener.location.reload();</script>";
	}
}
if(@$_GET['do_delay_set']) {
	$c_delay="active";
	if($tm=$db->date2tm($_GET['dt_delay'])) {
		$db->query("UPDATE cards SET tm_delay=$tm WHERE id={$_GET['klid']} ");
		print "<div class='alert alert-success'>Delayed</div>";
		print "<script>opener.location.reload();</script>";
	}
}
if(@$_GET['do_delay_del']) {
	$c_delay="active";
	if($tm=$db->date2tm($_GET['dt_delay'])) {
		$db->query("UPDATE cards SET tm_delay=0 WHERE id={$_GET['klid']} ");
		print "<div class='alert alert-warning'>Delay cleared</div>";
		print "<script>opener.location.reload();</script>";
	}
}

$r=$db->fetch_assoc($db->query("SELECT * FROM cards WHERE id={$_GET['klid']}"));
$name=$r['name']." ".$r['surname'];
$razd_id=$r['razdel'];
$id=$r['id'];
$uid=$r['uid'];
?>
<ul class="nav nav-tabs">
  <li class="<?=$c_razdel?>"><a href="#"  data-toggle="collapse" data-target="#razdel">Razdel</a></li>
  <li class="<?=$c_comm?>"><a href="#"  data-toggle="collapse" data-target="#comm">Comment</a></li>
  <li class="<?=$c_mob?>"><a href="#"  data-toggle="collapse" data-target="#mobile">Mobile</a></li>
  <li class="<?=$c_insta?>"><a href="#"  data-toggle="collapse" data-target="#insta">Instagram</a></li>
  <li class="<?=$c_scdl?>"><a href="#"  data-toggle="collapse" data-target="#scdl">Scheduling</a></li>
  <li class="<?=$c_delay?>"><a href="#"  data-toggle="collapse" data-target="#delay">Delaying</a></li>
</ul>
<div class='well'><h3><?=$name?></h3></div>
<form>
	<div class='collapse<?=$c_scdl?>' id='scdl'>
	<?
	if($r['tm_schedule']>=$db->dt1(time())) {
		$dt=date("d.m.Y",$r['tm_schedule']);
		$dt_s='background-color:#DFF0D8;';
	} else {$dt="";$dt_s='';}
	
	print "<div class='panel panel-success'><div class='panel-heading'>Scheduling</div>
		<div class='form-group panel-body'>
		<input id='dt'  class='form-control' type='text' style='width:160px; text-align:center; $dt_s' name='dt_scdl' value='$dt' onfocus='this.select();lcs(this)' onclick='event.cancelBubble=true;this.select();lcs(this)'>
		<br>
		<input  class='btn btn-primary' type='submit' name='do_scdl_set' value=' Set '>
		<input  class='btn btn-warning' type='submit' name='do_scdl_del' value=' Clear '>
		</div></div>";
	print "";
	?>
	</div>
	<div class='collapse<?=$c_delay?>' id='delay'>
  	<?
	if($r['tm_delay']>0) {
		$dt=date("d.m.Y",$r['tm_delay']);
		$dt_s='background-color:#D9EDF7;';
	} else {$dt="";$dt_s='';}
	
	print "<div class='panel panel-info'><div class='panel-heading'>Time delay</div>
		<div class='form-group panel-body'>
		<input id='dt'  class='form-control' type='text' style='width:160px; text-align:center; $dt_s' name='dt_delay' value='$dt' onfocus='this.select();lcs(this)' onclick='event.cancelBubble=true;this.select();lcs(this)'>
		<br>
		<input  class='btn btn-primary' type='submit' name='do_delay_set' value=' Set '>
		<input  class='btn btn-warning' type='submit' name='do_delay_del' value=' Clear '>
		</div></div>";
	print "";
	?>
	</div>
	<div class='collapse<?=$c_comm?>' id='comm'>	
		<label for="comm">Comment for: <b><?=$name?></b></label>
		<textarea class="form-control" id="comm" name='comm' style='height:200px;'><?=$r['comm']?></textarea>
		<button type="submit" class="btn btn-primary" name='do_comm_save' value='yes'>Save</button>
	</div>    
	<div class='collapse<?=$c_insta?>' id='insta'>	
		<label for="insta">Instagram</label>
		<input type='text' class="form-control" id="insta" name='insta' value='<?=$r['insta']?>'>
		<button type="submit" class="btn btn-default" name='do_insta_save' value='yes'>Save</button>
	</div>
	<div class='collapse<?=$c_mob?>' id='mobile'>	
		<label for="mobile">Mobile</label>
		<input type='text' class="form-control" id="mobile" name='mobile' value='<?=$r['mob']?>'>
		<button type="submit" class="btn btn-primary" name='do_mob_save' value='yes'>Save</button>
	</div>
	<div class='collapse<?=$c_razdel?>' id='razdel'>	
		<label for="razdel">Razdel</label>
		<select  class="form-control" name='razdel' id='razdel'>
		<?
		$res=$db->query("SELECT * FROM razdel WHERE del=0  AND razdel_name NOT LIKE '-%' ORDER BY razdel_name");
		while($r=mysql_fetch_assoc($res)) {
		$sel=($r['id']==$razd_id)?"selected":"";
		print "<option value='{$r['id']}' $sel>{$r['razdel_name']}</option>";
		}
		?>
		</select>
		<button type="submit" class="btn btn-primary" name='do_razdel_save' value='yes'>Save</button>
	</div>
  <input type='hidden' name='klid' value='<?=$id?>'>
  <input type='hidden' name='uid' value='<?=$uid?>'>
  <div class='text-right'><button type="submit" class="btn btn-warning" onclick='window.close();return(false);'>Close</button></div>
</form>

<?


$db->bottom();
?>
