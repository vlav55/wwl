<?
exit;
$title="КУПИТЬ ЛИДОВ";
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
//include "/var/www/html/pini/formula12/scripts/leadgen/leadgen.class.php";
include "init.inc.php";
$db=new top($database,$title,false);

//~ print "<div class='alert alert-warning' >Временно недоступно</div>";
//~ $db->bottom();
//~ exit;



$user_id=$_SESSION['userid_sess'];
$klid=$db->dlookup("klid","users","id='$user_id'");
$uid_md5_self=$db->dlookup("uid_md5","cards","id='$klid'");


if(isset($_GET['do_order'])) {
	$amount=intval($_GET['cnt']);
	if($amount) {
		$db->query("INSERT INTO leadgen_orders SET user_id='$user_id',tm='".time()."', amount='$amount'");
		print "<div class='alert alert-success' >Заказ сделан на $amount лидов</div>";
		//sleep(5);
		//print "<script>location='?'</script>";
	}
}

$leads_ordered=$db->fetch_assoc($db->query("SELECT SUM(amount) AS s FROM leadgen_orders WHERE user_id='$user_id'"))['s'];
$leads_delivered=$db->fetch_assoc($db->query("SELECT COUNT(id) AS cnt FROM leadgen_leads WHERE user_id='$user_id' AND sale=2"))['cnt'];
$rest_leads=$leads_ordered-$leads_delivered;
$tm1=$db->dt1(time());
$delivered_today=$db->fetch_assoc($db->query("SELECT COUNT(id) AS cnt FROM leadgen_leads WHERE user_id='$user_id' AND sale=2 AND tm>=$tm1"))['cnt'];;

$lg=new leadgen;
$lead_cost=$lg->get_lead_cost();
$partnerka_balance=$db->partnerka_balance($klid);
$partnerka_balance_leads=intval($partnerka_balance/$lead_cost);
//print "HERE_$partnerka_balance";

$user_id_allowed=[75,73,84];


if( isset($_GET['do_partnerka_buy_leads']) ) {
	$tm_last_op=$db->fetch_assoc($db->query("SELECT tm FROM leadgen_orders WHERE user_id='$user_id' ORDER BY tm DESC LIMIT 1"))['tm'];
	if( (time()-$tm_last_op)>(5*60) ) {
		$leads=intval($_GET['leads']);
		$sum_pay=$lead_cost*$leads;
		if($leads && ($sum_pay<$partnerka_balance || in_array($user_id,$user_id_allowed)) ) {
			$db->query("INSERT INTO leadgen_orders SET
						tm='".time()."',
						user_id='$user_id',
						amount='$leads',
						sum_pay='$sum_pay'
						");
			$db->query("INSERT INTO partnerka_pay SET
						klid='$klid',
						tm='".time()."',
						sum_pay='$sum_pay',
						vid=2,
						comm='куплены лиды $leads * $lead_cost'
						");
			$partnerka_balance=$db->partnerka_balance($klid);
			$partnerka_balance_leads=intval($partnerka_balance/$lead_cost);
			print "<div class='alert alert-success' >ОК, вы купили $leads * $lead_cost р = $sum_pay р</div>";
			flush();
			sleep(3);
			print "<script>location='?done_partnerka_buy_leads=yes'</script>";
		} else {
			print "<div class='alert alert-danger' >Ошибка</div>";
		}
	} else
		print "div class='alert alert-danger' >Ошибка. повторная операция возможна через 5 минут</div>";
}
if( isset($_GET['done_partnerka_buy_leads']) ) {
	print "<div class='alert alert-success' >Успешно</div>";
}


print "<h2>Покупка лидов</h2>";

print "<div class='well' >";
if(isset($_GET['leadgen_stop'])) {
	$db->query("UPDATE users SET leadgen_stop_user_action=1 WHERE id='$user_id'");
	print "<div class='alert alert-warning' >Поступление к вам лидов временно ПРИОСТАНОВЛЕНО.</div>";
	//print "<div class='alert alert-danger' >Ошибка, попробуйте позже.</div>";
}
if(isset($_GET['leadgen_start'])) {
	$db->query("UPDATE users SET leadgen_stop_user_action=0 WHERE id='$user_id'");
	print "<div class='alert alert-warning' >Поступление к вам лидов ВОЗОБНОВЛЕНО.</div>";
}
if(!$db->dlookup("leadgen_stop_user_action","users","id='$user_id'")) {
	print "<p class='font18'>Вы можете временно приостановить получение лидов.
	Оплаченные лиды сохранятся за вами и можно возобновить покупку позже.</p>";
	print "<a href='?leadgen_stop=yes' class='btn btn-warning btn-lg' target=''>Поставить на паузу</a>";
	print "<p class='small' >*если цена на лиды за это время остановки изменится, до количество лидов на остатке может стать меньше или больше.</p>";
} else {
	print "<p class='font18'><b>Поступление к вам лидов временно ПРИОСТАНОВЛЕНО.</b></p>";
	print "<a href='?leadgen_start=yes' class='btn btn-warning btn-lg' target=''>Возобновить</a>";
}
print "</div>";
?>
<div class='well' >
	<h2>Заказ лидов : <span class='badge' ><?=$db->dlookup("username","users","id=$user_id");?></span></h2>
	<h3>Остаток лидов: <?=$rest_leads?></h3>
	<h3>Поставлено сегодня: <?=$delivered_today?></h3>
	<!--
	<form class='form-inline' >
	<label for='cnt'>Заказать:</label>
	<input type='text' class='form-control' id='cnt' name='cnt' value='0'>
	<button type='submit' name='do_order' class='btn btn-primary' >Заказать</button>
	</form>
	-->
	<div>
		<a href='https://formula12.ru/prodamus/order.php?s=<?=intval($lead_cost*10)?>&product_id=20&uid=<?=$uid_md5_self?>' class='' target='_blank'>
			<button class='btn btn-primary btn-lg' >Купить лиды</button>
		</a>
		<span class='font18' >по <?=intval($lead_cost)?>р.</span>
	</div>
	<div class='well well-sm' >
		<form>
			<h3>У вас начислено, но не выплачено партнерское вознаграждение: <?=$partnerka_balance?>р.</h3>
			<p class='font18' >Купить лидов на партнерское вознаграждение:
				<input type='text' name='leads' value='<?=$partnerka_balance_leads?>' style='width:60px; text-align:center;'>
				по <?=$lead_cost?>р.
				<button type='submit' name='do_partnerka_buy_leads' value='ok' class='btn btn-warning btn-sm' >Купить</button>
			</p>
		</form>
	</div>
		
</div>
<?

print "<p class='well font22' ><a href='leadgen_partners.php' class='btn btn-primary' target='_blank'>Лиды у партнеров в 1-й линии</a></p>";

print "<h2>Сводка</h2>";
//$user_id=31;
print "<table class='table table-striped' >
		<thead>
			<tr>
				<th>Дата</th>
				<th>Оплачено,р</th>
				<th>Поставлено лидов</th>
			</tr>
		</thead>
		<tbody>";
for($d=0;$d<60;$d++) {
	$tm1=$db->dt1(time()-($d*24*60*60) );
	$tm2=$db->dt2(time()-($d*24*60*60) );
	$pay=$db->fetch_assoc($db->query("SELECT SUM(sum_pay) AS s FROM leadgen_orders WHERE tm>=$tm1 AND tm<=$tm2 AND user_id='$user_id'"))['s'];
	$bought=$db->fetch_assoc($db->query("SELECT COUNT(id) AS cnt FROM leadgen_leads WHERE tm>=$tm1 AND tm<=$tm2 AND user_id='$user_id' AND sale=2"))['cnt'];
	$dt=date("d.m.Y",$tm1);
	print "<tr><td>$dt</td><td>$pay</td><td>$bought</td></tr>";
}
print "</tbody></table>";


$res=$db->query("SELECT *,leadgen_leads.tm AS tm_lead FROM leadgen_leads
				JOIN cards ON cards.uid=leadgen_leads.uid
				JOIN users ON leadgen_leads.user_id=users.id
				WHERE leadgen_leads.user_id=$user_id AND sale>0  ORDER BY leadgen_leads.tm DESC,sale DESC LIMIT 200");
$n=1;

print "<hr>";
print "<h2>Лиды по операциям</h2>";
print "<table class='table table-striped' >
		<thead>
			<tr>
				<th>#</th>
				<th>Время</th>
				<th>Партнер</th>
				<th>Клиент</th>
				<th>Действие</th>
			</tr>
		</thead>
		<tbody>";
while($r=$db->fetch_assoc($res)) {
	$dt=date("d.m.Y H:i:s",$r['tm_lead']);
	$name=$r['name']." ".$r['surname']." ".$r['mob_search'];
	$username=$r['username'];
	$action=($r['sale']==1)?"<span class='label label-info' >Продан</span>":"<span class='label label-success' >Куплен</span>";
	print "<tr>
			<td>$n</td>
			<td><span class='badge_' >$dt</span></td>
			<td>$username</td>
			<td>$name</td>
			<td>$action</td>
			</tr>";
	$n++;
}
print "</tbody></table>";



$db->bottom();


?>
