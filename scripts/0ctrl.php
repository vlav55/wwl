<?
include "chk.inc.php";
$title="0ctrl";
include "../top.inc.php";
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";

$db=new vkt('vkt');

if(!isset($_SESSION['ctrl_id']))
	$_SESSION['ctrl_id']=0;
if(isset($_GET['ctrl_id'])) {
	$_SESSION['ctrl_id']=intval($_GET['ctrl_id']);
}
if(isset($_GET['send_passw'])) {
	$passw=$db->dlookup("admin_passw","0ctrl","id={$_GET['ctrl_id']}");
	$dir=$db->dlookup("ctrl_dir","0ctrl","id={$_GET['ctrl_id']}");
	$uid=$db->dlookup("uid","0ctrl","id={$_GET['ctrl_id']}");
	$name=$db->dlookup("name","cards","uid='$uid'")." ".$db->dlookup("surname","cards","uid='$uid'");
	$db->notify_me("{$_GET['ctrl_id']}
$name
https://for16.ru/d/$dir/cp.php?view=yes&filter=last10
admin $passw");
}
if(isset($_GET['do_del_uid'])) {
	$uid=intval($_GET['uid']);
	$db->query("UPDATE avangard SET res=0 WHERE vk_uid='$uid'");
	$db->query("UPDATE cards SET email='',mob='',mob_search='',del=1 WHERE uid='$uid'");
	print "<p class='alert alert-warning' >uid=$uid deleted (del=1)</p>";
}
if(isset($_GET['do_del'])) {
	$ctrl_id=intval($_GET['ctrl_id']);
	$db->connect($db->get_ctrl_database($ctrl_id));
	$res=$db->query("SELECT id FROM cards WHERE del=0 ");
	if(($n=$db->num_rows($res))<10) {
		$db->connect('vkt');
		$uid=$db->vkt_delete_account($ctrl_id);
		print "<p class='alert alert-warning' >ctrl_id=$ctrl_id deleted (del=1) <br>
		Delete uid=$uid from cards also?
		<a href='?do_del_uid=yes&uid=$uid' class='btn btn-sm btn-warning' target=''>yes</a>
		<a href='?view=yes' class='btn btn-sm btn-primary' target=''>CANCEL</a>
		</p>";
	} else {
		print "<p class='alert alert-warning'>Passed because it has $n records (more then 10)</p>";
	}
}
if(isset($_GET['del'])) {
	print "<p class='alert alert-danger' >Confirm deleting of ctrl_id={$_GET['ctrl_id']}?
	<a href='?do_del=yes&ctrl_id={$_GET['ctrl_id']}' class='btn btn-sm btn-warning' target=''>yes</a>
	<a href='?view=yes&ctrl_id={$_GET['ctrl_id']}' class='btn btn-sm btn-primary' target=''>cancel</a>
	</p>";
}
if(isset($_GET['disp_company'])) {
	print "<script>window.opener.location='?ctrl_id=$ctrl_id'</script>";
	$ctrl_id=intval($_GET['ctrl_id']);
	$company=$db->dlookup("company","0ctrl","id=$ctrl_id");
	?>
	<div class='container' >
	<h1><?=$company?></h1>
	<?
	$db->connect($db->get_ctrl_database($ctrl_id));
	$res=$db->query("SELECT * FROM lands WHERE del=0 AND land_txt!=''");
	while($r=$db->fetch_assoc($res)) {
		if($r['fl_partner_land']==1)
			$vid="🙋‍♀️";
		elseif($r['product_id']>0)
			$vid="📦";
		else
			$vid="⭐";
		$url=$r['land_url'];
		print "<h3>$vid <a href='$url' class='' target='_blank'>{$r['land_name']}</a></h3>
			<p>$url</p>";
	}
	?>
	</div>
	<?
	include "../bottom.inc.php";
	exit;
}

if(isset($_GET['tm_end_0ctrl_switch'])) {
	$ctrl_id=intval($_GET['ctrl_id']);
	$tm_end_0ctrl=$db->dt2($db->date2tm($_GET['dt_end_0ctrl_val'])); //$db->dlookup("tm_end","0ctrl","id='$ctrl_id'");
	$tm_pay_end=$db->dt2($db->date2tm($_GET['dt_pay_end_val']));
	if($tm_end_0ctrl) {
		$db->query("UPDATE 0ctrl SET tm_end='$tm_end_0ctrl' WHERE id='$ctrl_id'");
	} else {
		$db->query("UPDATE 0ctrl SET tm_end=0 WHERE id='$ctrl_id'");
		if($tm_pay_end) {
			$avangard_id=intval($_GET['avangard_id']);
			//print "HERE_$avangard_id";
			//$db->query("UPDATE avangard SET tm_end='$tm_pay_end' WHERE id='$avangard_id'");
			$ctrl_id=1;
			$db->avangard_tm_end_set($avangard_id,$tm_pay_end);
			$ctrl_id=intval($_GET['ctrl_id']);
		}
	}
}
if(isset($_GET['do_add'])) {
	//~ print_r($_GET);
	//~ 
	//~ exit;
	$f=$db->split_fio($_GET['name']);
	$r=[
		'first_name'=>$f['f_name'],
		'last_name'=>$f['l_name'],
		'phone'=>$_GET['phone'],
		'email'=>$_GET['email'],
		'comm1'=>$_GET['comm'],
	];
	if($client_uid=$db->cards_add($r,true)) {
		$db->save_comm($uid,$user_id,"Добавлен вручную",1);
		print "<p class='alert alert-success mt-4' >cards_add OK uid=$client_uid</p>";
		if($client_ctrl_id=$db->vkt_create_account($client_uid,20)) {
			print "<p class='alert alert-success mt-4' >Account created ctrl_id=$client_ctrl_id</p>";
		} else
			print "<p class='alert alert-danger' >vkt_create_account error</p>";
	} else
		print "<p class='alert alert-danger' >cards_add error</p>";
}

print "<div class='container' >";
print "<h1>0ctrl list</h1>";
print "<p>
	<a href='#__add' class='' target='' data-toggle='collapse'>Add</a>
	<a href='?view=yes' class='' target=''>View</a>
	</p>";
?>
<div class='collapse my-4' id='__add' >
<form method="GET" action="">
    <input type="hidden" name="do_add" value="1">
    
    <div class="form-group">
        <label for="name">
            <i class="fas fa-user"></i> Name
        </label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    
    <div class="form-group">
        <label for="phone">
            <i class="fas fa-phone"></i> Phone
        </label>
        <input type="tel" class="form-control" id="phone" name="phone" required>
    </div>
    
    <div class="form-group">
        <label for="email">
            <i class="fas fa-envelope"></i> Email
        </label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    
    <div class="form-group">
        <label for="comm">
            <i class="fas fa-comment"></i> Comments
        </label>
        <textarea class="form-control" id="comm" name="comm" rows="4"></textarea>
    </div>
    
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-paper-plane"></i> Submit
    </button>
</form>
</div>
<?

$products=[20,30,31,32,33,34];
$res=$db->query("SELECT * FROM 0ctrl WHERE del=0 ORDER BY id DESC");
print "<table class='table table-striped'>
	<thead>
		<tr>
			<th>#</th>
			<th>ctrl_id</th>
			<th>admin_uid</th>
			<th>admin_name</th>
			<th>company</th>
			<th>ctrl_dir</th>
			<th>ctrl_db</th>
			<th>p</th>
			<th>link</th>
			<th>last_pay</th>
			<th>next_pay</th>
			<th>next_pay_0ctrl</th>
			<th>del</th>
		</tr>
	</thead>
	<tbody>
	";
while($r=$db->fetch_assoc($res)) {
	$ctrl_id=$r['id'];
	$ctrl_dir=$db->get_ctrl_dir($ctrl_id); 
	$ctrl_db=$db->get_ctrl_database($ctrl_id);
	$ctrl_link=$db->get_ctrl_link($ctrl_id);
	$uid=$r['uid'];
	$name=$db->dlookup("name","cards","uid='$uid'")." ".$db->dlookup("surname","cards","uid='$uid'");

	//~ $pay_end=$db->dlookup("tm_end","avangard"," res=1 AND vk_uid='$uid' AND
		//~ (product_id=30 OR product_id=31 OR product_id=32)");
	$avangard_id=$db->avangard_last_pay_id($uid,$products);
	if(!$avangard_id) {
		$avangard_id=$db->dlookup("id","avangard","res=1 AND vk_uid='$uid'");
	}
	$tm_last_pay=intval($avangard_id)?$db->dlookup("tm","avangard","id=$avangard_id"):0;
	$dt_last_pay=($tm_last_pay)?date("d.m.Y",$tm_last_pay):"-";

	$tm_pay_end=intval($avangard_id)?$db->dlookup("tm_end","avangard","id=$avangard_id"):0;
	$dt_pay_end_val=($tm_pay_end)?date("d.m.Y",$tm_pay_end):"-";
	$dt_pay_end="
				<input type='text' style='font-size:12px;width:80px;' id='text-input' name='dt_pay_end_val' value='$dt_pay_end_val'>
				<input type='hidden' name='avangard_id' value='$avangard_id'>
    		";

	$tm_end_0ctrl=$db->dlookup("tm_end","0ctrl","id='$ctrl_id'");
	$dt_end_0ctrl_val=$tm_end_0ctrl?date('d.m.Y',$tm_end_0ctrl):'-';
	$dt_end_0ctrl="
				<input type='text' style='font-size:12px;width:80px;' id='text-input' name='dt_end_0ctrl_val' value='$dt_end_0ctrl_val'>&nbsp;<button class='btn btn-info btn-sm' type='submit' name='tm_end_0ctrl_switch' value='yes'>Ok</button>
				<input type='hidden' name='ctrl_id' value='$ctrl_id'>
    		";

	$n=1;

	$klid=$db->get_klid_by_uid($uid);
	$bc=$db->dlookup("bc","users","klid='$klid'");
	//print "klid=$klid uid=$uid bc=$bc<br>";
	$wwl_link=($bc)?"https://winwinland.ru/1/?bc=$bc":"";
	
	$tmp=$db->database;
	$db->connect($ctrl_db);
	if($vlav_klid=$db->dlookup("id","cards","telegram_id='315058329'")) {
		$vlav_bc="?bc=".$db->dlookup("bc","users","klid='$vlav_klid'");
	} else
		$vlav_bc="";
	$db->connect($tmp);

	$s=($r['id']==$_SESSION['ctrl_id'])?"bg-warning":"";
	$insales_id=$r['insales_shop_id'] ? "<br>insales=".$r['insales_shop_id'] : "";

	print "<form>
		<tr class='font18 $s'  id='r_{$r['id']}'>
		<td>$n</td>
		<td>$ctrl_id</td>
		<td>$uid</td>
		<td><a href='https://for16.ru/d/1000/msg.php?uid=$uid' class='' target='_blank'>$name</a></td>
		<td>
			<a href='?disp_company=yes&ctrl_id={$r['id']}' class='' target='_blank'>- {$r['company']} -</a>
			$insales_id
		</td>
		<td>$ctrl_dir</td>
		<td>$ctrl_db</td>
		<td><a href='?send_passw=yes&ctrl_id=$ctrl_id' class='' target='' itle='notify_me with admin passw'>^</a></td>
		<td><a href='$ctrl_link' class='' target='_blank'>crm</a></td>
		<td title='avangard_id=$avangard_id'>$dt_last_pay</td>
		<td>$dt_pay_end</td>
		<td>$dt_end_0ctrl</td>
		<td><a href='?del=yes&ctrl_id=$ctrl_id' class='' target=''>del</a></td>
		</tr>
	</form>
	";
	$n++;
}
print "</tbody></table></div>";
?>
<script>
// Прокрутка к метке после загрузки страницы
window.addEventListener('load', function () {
  var targetElement = document.getElementById('<?=$_SESSION['ctrl_id']?>');
  if (targetElement) {
    targetElement.scrollIntoView({ behavior: 'smooth' });
  }
});
</script>

<?
include "../bottom.inc.php";
?>
