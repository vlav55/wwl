<?
$db=new top($database,"640px;",false);
$reload="opener.location.reload()";
if(@$_GET['clr_comm']) {
	$user_id=$db->userdata['user_id'];
	$db->query("UPDATE cards SET comm='' WHERE uid={$_GET['uid']}");
}
if(@$_GET['do_save']) {
	if(trim($_GET['comm'])!="") {
		$user_id=$db->userdata['user_id'];
		$db->save_comm($_GET['uid'],$user_id,$_GET['comm'],0,0,0,true);
		//save_comm($uid,$user_id,$comm,$source_id=0,$vote_vk_uid=0,$mode=0, $force=false) { //mode=1 - shift down mode=0 - replace
		$reload="opener.location.reload()";
	}
}
$r=$db->fetch_assoc($db->query("SELECT * FROM cards WHERE uid={$_GET['uid']}"));
print "<div class='text-right'><button type='button' class='btn btn-warning' onclick='$reload; window.close();'>Close</button></div>";
print "<div class='alert alert-success'><h3>{$r['surname']} {$r['name']}</h3></div>";
//print "<button type='button'  data-toggle='collapse' data-target='#comm' class='btn btn-success'>Add comment</button>&nbsp;";
?>
<div class='collapse_' id='comm'>
<form>
<textarea class="form-control" name='comm' style='height:150px;'><?=$r['comm']?></textarea>
<input type='hidden' name='uid' value='<?=$_GET['uid']?>'>
<button type="submit" class="btn btn-default" name='do_save' value='yes'>Save</button>&nbsp;
<button type='button'  class='btn btn-warning' onclick='location="?clr_comm=yes&uid=<?=$r['uid']?>"'>Clear comment</button>
</form>
</div>
<?

if(!$db->dlookup("uid","msgs","uid={$_GET['uid']} AND imp=10")) {
	$comm=$db->dlookup("comm","cards","uid={$_GET['uid']}");
	if(trim($comm)!="") {
		$user_id=$db->userdata['user_id'];
		$db->save_comm($_GET['uid'],$user_id,$comm,0,0,0);
	}
}

$res=$db->query("SELECT msg,msgs.tm AS tm, username
		 FROM msgs 
		JOIN cards ON cards.uid=msgs.uid 
		JOIN users ON msgs.user_id=users.id 
		WHERE cards.del=0 AND msgs.uid={$_GET['uid']} and imp>0 
		ORDER BY msgs.tm DESC");
while($r=$db->fetch_assoc($res)) {
	$dt="<span class='badge'>".date("d.m.Y H:i",$r['tm'])."</span>";
	$uname="<span class='badge'>{$r['username']}</span>";
	print "<div class='card bg-light p-2'>
		<div class='alert alert-default'>$dt $uname</div>
		".nl2br(htmlspecialchars($r['msg']))."
		</div>";
}

$db->bottom();
?>
