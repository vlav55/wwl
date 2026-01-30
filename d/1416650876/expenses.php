<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "init.inc.php";
$db=new top("yogacenter","640px;",false);
$cost_insta=$cost_yd= $cost_vk=$cost_bloggers=$cost_zadarma=$cost_sms=0;
if(isset($_GET['do_add'])) {
	$tm=$db->date2tm($_GET['dt']);
	$dt=date("d.m.Y",$tm);
	$cost_insta=intval($_GET['cost_insta']);
	if($id=$db->dlookup("id","expenses","tm='$tm' AND chanal_id=1") )
		$db->query("UPDATE expenses SET amount='$cost_insta' WHERE id='$id'");
	else
		$db->query("INSERT INTO expenses SET tm='$tm',amount='$cost_insta',chanal_id=1");
	$cost_yd=intval($_GET['cost_yd']);
	if($id=$db->dlookup("id","expenses","tm='$tm' AND chanal_id=2") )
		$db->query("UPDATE expenses SET amount='$cost_yd' WHERE id='$id'");
	else
		$db->query("INSERT INTO expenses SET tm='$tm',amount='$cost_yd',chanal_id=2");
	$cost_vk=intval($_GET['cost_vk']);
	if($id=$db->dlookup("id","expenses","tm='$tm' AND chanal_id=3") )
		$db->query("UPDATE expenses SET amount='$cost_vk' WHERE id='$id'");
	else
		$db->query("INSERT INTO expenses SET tm='$tm',amount='$cost_vk',chanal_id=3");
	$cost_bloggers=intval($_GET['cost_bloggers']);
	if($id=$db->dlookup("id","expenses","tm='$tm' AND chanal_id=4") )
		$db->query("UPDATE expenses SET amount='$cost_bloggers' WHERE id='$id'");
	else
		$db->query("INSERT INTO expenses SET tm='$tm',amount='$cost_bloggers',chanal_id=4");
	$cost_zadarma=intval($_GET['cost_zadarma']);
	if($id=$db->dlookup("id","expenses","tm='$tm' AND chanal_id=5") )
		$db->query("UPDATE expenses SET amount='$cost_zadarma' WHERE id='$id'");
	else
		$db->query("INSERT INTO expenses SET tm='$tm',amount='$cost_zadarma',chanal_id=5");
	$cost_sms=intval($_GET['cost_sms']);
	if($id=$db->dlookup("id","expenses","tm='$tm' AND chanal_id=6") )
		$db->query("UPDATE expenses SET amount='$cost_sms' WHERE id='$id'");
	else
		$db->query("INSERT INTO expenses SET tm='$tm',amount='$cost_sms',chanal_id=6");
}
if(isset($_GET['edit'])) {
	$tm=intval($_GET['tm']);
	$dt=date("d.m.Y",$tm);
	$res=$db->query("SELECT * FROM expenses WHERE tm='$tm'",0);
	while($r=$db->fetch_assoc($res)) {
		switch($r['chanal_id']) {
			case 1: $cost_insta=$r['amount']; break;
			case 2: $cost_yd=$r['amount'];
			case 3: $cost_vk=$r['amount'];
			case 4: $cost_bloggers=$r['amount'];
			case 5: $cost_zadarma=$r['amount'];
			case 6: $cost_sms=$r['amount'];
		}
	}
}
?>
<div class='container'>
<form action="">
	<dl class='dl-horizontal' >
		<dt>Дата</dt><dd><input type="text" class="form-control" placeholder="" name='dt' value='<?=$dt?>' id='dt'></dd>
		<dt>Инстаграм</dt><dd><input type="text" class="form-control" placeholder="Instagram" name='cost_insta' value='<?=$cost_insta?>'></dd>
		<dt>Яндекс</dt><dd><input type="text" class="form-control" placeholder="Yandex direct" name='cost_yd' value='<?=$cost_yd?>'></dd>
		<dt>ВК</dt><dd><input type="text" class="form-control" placeholder="VK" name='cost_vk' value='<?=$cost_vk?>'></dd>
		<dt>Блоггеры</dt><dd><input type="text" class="form-control" placeholder="Bloggers" name='cost_bloggers' value='<?=$cost_bloggers?>'></dd>
		<dt>Задарма</dt><dd><input type="text" class="form-control" placeholder="Zadarma" name='cost_zadarma' value='<?=$cost_zadarma?>'></dd>
		<dt>СМС</dt><dd><input type="text" class="form-control" placeholder="sms" name='cost_sms' value='<?=$cost_sms?>'></dd>
		<dt></dt><dd><button type="submit" class="btn btn-primary" name='do_add' value='yes'> ADD </button></dd>
	</dl>
</form>
</div>
<script>
	$('#dt').datepicker({
		weekStart: 1,
		daysOfWeekHighlighted: "6,0",
		autoclose: true,
		todayHighlight: true,
		format: 'dd.mm.yyyy',
		language: 'ru',
		});
</script>
<?
$res=$db->query("SELECT * FROM expenses WHERE 1 ORDER BY tm DESC,chanal_id ASC");
$tm=0;
while($r=$db->fetch_assoc($res)) {
	if($tm!=$r['tm']) {
		$tm=$r['tm'];
		print "------------- <br>";
	}
	print "<a href='?edit=yes&tm={$r['tm']}' class='' target=''>".date("d.m.Y",$r['tm'])."</a> {$r['chanal_id']} {$r['amount']} <br>";
}


$db->bottom();
?>
