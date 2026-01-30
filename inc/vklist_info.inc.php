<?

$db=new top($database,"100%",false,$favicon);
$bs=new bs;
$token=$db->dlookup("token","vklist_acc","del=0 AND last_error=0 AND fl_acc_not_allowed_for_new=0");
$vk=new vklist_api($token);

$limit=30;

$gid=$_GET['gid'];
$r_grp=$db->fetch_assoc($db->query("SELECT * FROM vklist_groups WHERE id=$gid"));
$yesno=array(0=>"No",1=>"Yes");
print $bs->button_close();
print "<h2><div class='alert alert-info'>{$r_grp['group_name']}</div></h2>";
print "<div class=''>Выводить первых :  <span class='badge'>$limit</span></div>";
//print "<div class=''><a href='?gid=$gid'>view</a> | <a href='vklist.php'>back to vklist</a></div>";

if(@$_GET['do_remove']) {
	$n=1;
	//$db->print_r($_GET);
	foreach($_GET['chk'] AS $uid) {
		$db->query("UPDATE vklist SET tm_msg=1 WHERE uid=$uid");
		print "<div class='alert alert-info'>$n <b>$uid</b> - удален(а) из списка рассылки</div>";
		$n++;
	}
	print "<script>opener.location.reload();</script>";
}

print "<form>";
$res=$db->query("SELECT * FROM vklist WHERE group_id=$gid AND tm_msg=0 AND res_msg=0 LIMIT $limit");
while($r=mysql_fetch_assoc($res)) {
	$u=$vk->vk_get_userinfo($r['uid']);
	if(!$u) {
		for($i=0;$i<5;$i++) {
			usleep(1000000);
			$u=$vk->vk_get_userinfo($r['uid']);
			if($u)
				break;
		}
		print "<div class='alert alert-danger'>Error getting vk_get_userinfo code=".$vk->error_code." uid=<b>{$r['uid']}</b> </div>";
		continue;
	}
	$city=$vk->vk_get_city_name($u['city']);
	//print_r($u);
	?>
	<div class="media">
		<div class="media-left">
			<img src="<?=$u['photo_200']?>" class="media-object" style="width:200px">
		</div>
		<div class="media-body">
			<h4 class="media-heading"><a href='https://vk.com/id<?=$u['id']?>' target='_blank'><?=$u['first_name']." ".$u['last_name']?></a></h4>
			<dl class="dl-horizontal">
				<dt>bdate</dt><dd><?=$u['bdate']?></dd>
				<dt>Sex</dt><dd><?=$u['sex']?></dd>
				<dt>City</dt><dd><?=$city?></dd>
				<dt>Status</dt><dd><?=$u['status']?></dd>
				<dt>Friend</dt><dd><?=$yesno[$u['is_friend']]?></dd>
				<dt>can_write_private_message</dt><dd><?=$yesno[$u['can_write_private_message']]?></dd>
				<dt>can_send_friend_request</dt><dd><?=$yesno[$u['can_send_friend_request']]?></dd>
				<dt>blacklisted</dt><dd><?=$yesno[$u['blacklisted']]?></dd>
				<?
				if(isset($u['occupation'])) {
					foreach($u['occupation'] AS $key=>$val) {
						print "<dt>$key</dt><dd>".$val."</dd>";
					}
				}
				?>
				<dt style='color:red;'>remove</dt><dd><input type='checkbox' name='chk[]' value='<?=$u['id']?>'></div></dd>
			</dl>
		</div>
	</div>
	<?
	usleep(100000);
	//break;
}
?>
	<div class='well'><h2>Удалить отмеченных из списка для рассылки</h2><button type="submit" class="btn btn-primary" name='do_remove' value='yes'>Удалить</button></div>
	<input type='hidden' name='gid' value='<?=$gid?>'>
<?
print "</form>";

$db->bottom();
?>
