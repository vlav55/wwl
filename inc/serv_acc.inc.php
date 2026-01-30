<?
$db=new top($database,0);
print "<div class='alert alert-info' ><h2>Массовая публикация репостов из группы для всех промо аккаунтов</h2></div>";
print "<div class='alert alert-warning' >Будьте внимательны, если данный репост уже есть на стене у промо-аккаунта, он будет опубликован повторно!</div>";
if(isset($_GET['do_send'])) {
//	$db->print_r($_GET);
	$arr=explode("\n",$_GET['reposts']);
	//$db->print_r($arr);
	$arr_w=array();
	foreach($arr AS $w) {
		if(preg_match("|(wall-[0-9]+_[0-9]+)|",trim($w),$r)) {
			$arr_w[]=trim($r[1]);
		}
	}

	$res=$db->query("SELECT * FROM vklist_acc WHERE del=0 AND fl_allow_read_from_all=0");
	print "<div class='well_' >";
	$n=0;
	while($r=$db->fetch_assoc($res)) {
		print "<div>Аккаунт ({$r['id']}) <a href='https://vk.com/id{$r['vk_uid']}' class='' target='_blank'>{$r['name']}</a>
		<div class='well' >";
		$vk=new vklist_api($r['token']);
		foreach($arr_w AS $w) {
			print "$w ";
			if($vk->wall_repost($w))
				print "<span class='label label-success' >успешно</span>";
			else
				print "<span class='label label-danger' >Ошибка (пост в группе должен быть от имени группы)</span>";
			usleep(300000);
			print "<br>";
		}
		print "</div>
		</div>";
		if($n++==10) {
			print "<div class='alert alert-warning' >Допустимо максимум $n постов за раз</div>";
		}
		//exit;
	}
	print "<h2>Выполнено!</h2>";
	print "</div>";
}
?>
<form>
	<div class="form-group">
		<label for="comment">ссылки на посты в группе, в столбик</label>
		<textarea class="form-control" rows="5" name="reposts" style='width:500px;'></textarea>
		<button type="submit" class="btn btn-default" name='do_send'>Отправить</button></button>
	</div>	
</form>
<?
$db->bottom();


?>
