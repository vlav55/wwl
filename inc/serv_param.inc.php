<?
$db=new top($database,0,false);
$db->connect("vktrade");

$r=$db->fetch_assoc($db->query("SELECT * FROM customers WHERE id='$customer_id'"));
$grpadd_grp=$r['grpadd_grp'];
$grpadd_mode=$r['grpadd_mode'];
$landing_grp=$r['landing_grp'];
$landing_mode=$r['landing_mode'];
$vote_mode=$r['vote_mode'];
$bdate_grp=$r['bdate_grp'];
$bdate_mode=$r['bdate_mode'];
$razdel_list=$r['bdate_razdel'];
$bdate_days_before=intval($r['bdate_days_before']);
if($bdate_days_before>30)
	$bdate_days_before=30;
$bdate_time=intval($r['bdate_time']);
if(!$bdate_time || $bdate_time>23)
	$bdate_time=12;
	

$bs=new bs;
print "<div class='alert alert-info' ><h2>Прочие настройки</h2></div>";

if(isset($_GET['do_save'])) {
	//$db->print_r($_GET);
	$grpadd_grp=intval($_GET['sel_grpadd']);
	$grpadd_mode=intval($_GET['radio_grpadd']);
	$landing_grp=intval($_GET['sel_landing']);
	$landing_mode=intval($_GET['radio_landing']);
	$vote_mode=intval($_GET['radio_vote']);
	$bdate_grp=intval($_GET['sel_bdate']);
	$bdate_mode=intval($_GET['radio_bdate']);
	$bdate_days_before=intval($_GET['bdate_days_before']);
	if($bdate_days_before>30)
		$bdate_days_before=30;
	$bdate_time=intval($_GET['bdate_time']);
	if(!$bdate_time || $bdate_time>23)
		$bdate_time=12;

	$razdel_list="";
	foreach($_GET['razdel'] AS $val)
		$razdel_list.=$val.",";
	//print $razdel_list;

	$db->query("UPDATE customers SET 
				grpadd_grp=$grpadd_grp,
				grpadd_mode=$grpadd_mode,
				landing_grp=$landing_grp,
				landing_mode=$landing_mode,
				vote_mode=$vote_mode,
				bdate_grp=$bdate_grp,
				bdate_mode=$bdate_mode,
				bdate_razdel='".$db->escape($razdel_list)."',
				bdate_days_before='$bdate_days_before',
				bdate_time=$bdate_time
				WHERE id='$customer_id'");
	print "<div class='alert alert-success' ><h3>Записано!</h3></div>";
}


$db->connect($database);
?>

<form action="">
	<div class='alert alert-warning' >
		<h3>Куда сканировать опросы</h3>
		<?
		$chk1=($vote_mode==1)?"checked":"";
		$chk2=($vote_mode==0)?"checked":"";
		?>
		<div class="radio"><label><input type="radio" name="radio_vote" value="1" <?=$chk1?> >в Новые</label></div>
		<div class="radio">
			<label><input type="radio" name="radio_vote" value="0" <?=$chk2?> >в группу рассылки</label>
		</div>
	</div>
	<div class='alert alert-warning' >
		<h3>Куда сканировать новых вступивших в группу</h3>
		<?
		$chk1=($grpadd_mode==1)?"checked":"";
		$chk2=($grpadd_mode==0)?"checked":"";
		?>
		<div class="radio"><label><input type="radio" name="radio_grpadd" value="1" <?=$chk1?> >в Новые</label></div>
		<div class="radio">
			<label><input type="radio" name="radio_grpadd"  value="0" <?=$chk2?> >в группу рассылки</label>
			<select class="form-control" id="sel_grpadd" name="sel_grpadd">
			<?
			$res=$db->query("SELECT * FROM vklist_groups WHERE del=0");
			while($r=$db->fetch_assoc($res)) {
				$selected=($r['id']==$grpadd_grp)?"selected":"";
				print "<option value='{$r['id']}' $selected >{$r['group_name']}</option>";
			}
			?>
			</select>
		</div>
	</div>
	<div class='alert alert-warning' >
		<h3>Куда помещать новых, зашедших на лэндинг</h3>
		<?
		$chk1=($landing_mode==1)?"checked":"";
		$chk2=($landing_mode==0)?"checked":"";
		?>
		<div class="radio"><label><input type="radio" name="radio_landing" value="1" <?=$chk1?> >в Новые</label></div>
		<div class="radio">
			<label><input type="radio" name="radio_landing" value="0"  <?=$chk2?> >в группу рассылки</label>
			<select class="form-control" id="sel_landing" name="sel_landing">
			<?
			$res=$db->query("SELECT * FROM vklist_groups WHERE del=0");
			while($r=$db->fetch_assoc($res)) {
				$selected=($r['id']==$landing_grp)?"selected":"";
				print "<option value='{$r['id']}' $selected >{$r['group_name']}</option>";
			}
			?>
			</select>
		</div>
	</div>
	<div class='alert alert-warning' >
		<h3>Куда помещать ДНИ РОЖДЕНИЯ</h3>
		<?
		$chk1=($bdate_mode==1)?"checked":"";
		$chk2=($bdate_mode==0)?"checked":"";

		$chk1="";
		$chk2="checked";
		?>
		<div class="radio"><label><input type="radio" name="radio_bdate" value="1" <?=$chk1?>  disabled>в Новые</label></div>
		<div class="radio">
			<label><input type="radio" name="radio_bdate" value="0"  <?=$chk2?> >в группу рассылки</label>
			<select class="form-control" id="sel_bdate" name="sel_bdate">
			<?
			$res=$db->query("SELECT * FROM vklist_groups WHERE del=0");
			while($r=$db->fetch_assoc($res)) {
				$selected=($r['id']==$bdate_grp)?"selected":"";
				print "<option value='{$r['id']}' $selected >{$r['group_name']}</option>";
			}
			?>
			</select>
		</div>
		<p><a href='#bdate_settings' data-toggle="collapse" data-target="#bdate_settings">Настройки</a></p>
		<div  id="bdate_settings" class="collapse">
			<?
			print "<div>
					За сколько дней до ДР отправлять рассылку:
					<input type='text' name='bdate_days_before' value='$bdate_days_before' maxlen='2'>
				</div>";
			print "<div>
					Время рассылки поздравлений (по Москве):
					<input type='text' name='bdate_time' value='$bdate_time' maxlen='2'>
				</div>";
			print "<h3>Клиентов в каких разделах поздравлять с днем рождения:</h3>";
			$res=$db->query("SELECT * FROM razdel WHERE del=0 AND id>0 ORDER BY razdel_name");
			$razd_arr=explode(",",$razdel_list);
			$checked="";
			foreach($razd_arr AS $val) {
				if($val==0) {
					$checked="checked";
					break;
				}
			}
			print "<div>
					<input type='checkbox' name='razdel[]' value='0' $checked >
					УЧАСТНИКОВ ГРУППЫ
				</div>";

			print $bs->table(array("Выбор","Раздел"));
			
			while($r=$db->fetch_assoc($res)) {
				$checked="";
				foreach($razd_arr AS $val) {
					if($val==$r['id']) {
						$checked="checked";
						break;
					}
				}
				print "<tr>
						<td><input type='checkbox' name='razdel[]' value='{$r['id']}' $checked></td>
						<td>{$r['razdel_name']}</td>
					</tr>";
			}
			print "</table>";
			?>
		</div>
	</div>
<button type="submit" name='do_save' value='yes' class="btn btn-primary">Сохранить</button>
</form>

<?

$db->bottom();

?>
