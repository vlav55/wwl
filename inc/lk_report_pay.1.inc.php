<?
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "../init.inc.php";
$db=new top($database,'Отчет партнерским вознаграждениям');

function where_pids_consider() {
	return "1";
}

if(isset($_GET['refresh'])) {
	include "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
	$tm1=$db->date2tm("01.10.2022");
	$tm2=time();
	$res=$db->query("SELECT * FROM users WHERE del=0");
	while($r=$db->fetch_assoc($res)) {
		$klid=$r['klid'];
		$p=new partnerka($klid,$database);
		$p->fill_op($klid,$tm1,$tm2,$ctrl_id);
	}
	print "<script>location='?'</script>";
}

print "<div class='container' >";
if(isset($_GET['alert'])) {
	$msg=htmlspecialchars($_GET['msg']);
	$alert=htmlspecialchars($_GET['alert']);
	print "<p class='alert alert-$alert' >$msg</p>";
}
print "<h2 class='text-center' >Начисления по партнерам</h2>";
print "<div class='text-center' ><a href='?refresh=yes' class='btn btn-primary btn-lg' target=''>Обновить</a></div>";

$tm1_lastweek=$db->dt1(strtotime('monday',strtotime('last week')));
$tm2_lastweek=$db->dt2(strtotime('sunday',strtotime('last week')));
$where=where_pids_consider();
$oborot=$db->fetch_assoc($db->query("SELECT SUM(amount1) AS s FROM avangard WHERE res=1 AND tm>=$tm1_lastweek AND tm<=$tm2_lastweek AND $where",0))['s'];
$oborot=$oborot?$oborot:0;
$nachisl=$db->fetch_assoc($db->query("SELECT SUM(fee_sum) AS s FROM partnerka_op WHERE tm>=$tm1_lastweek AND tm<=$tm2_lastweek AND klid_up>0",0))['s'];
$nachisl=$nachisl?$nachisl:0;
$percent=($oborot>0)?intval($nachisl/$oborot*100):0;

$dt1_lastweek=date("d.m.Y H:i",$tm1_lastweek);
$dt2_lastweek=date("d.m.Y H:i",$tm2_lastweek);

print "<div class='card bg-light p-3 my-3' >";
print "<p>Оборот за прошлую неделю ($dt1_lastweek - $dt2_lastweek) = <span class='font-weight-bold' >$oborot р.</span></p>";
print "<p>Начислено партнерам = <span class='font-weight-bold' >$nachisl р.</span></p>";
print " <p>Средний процент вознаграждения: <span class='badge' >$percent%</span></p>";
print "</div>";
$today=$db->dt1(time());
$today2=$db->dt2(time());

print "<h3>Сегодня: ".date("d.m.Y",$today)."</h3>";

print "<div class='card bg-light p-3 my-3' >";
print "<h3>Проведены выплаты сегодня:</h3>";
$n=1;
$res=$db->query("SELECT * FROM partnerka_pay WHERE tm>=$today AND tm<=$today2 ORDER BY klid,tm DESC",0);
print "<table class='table table-striped table-condenced' >
	<thead>
		<tr>
			<th>№</th>
			<th>Партнер</th>
			<th>Имя</th>
			<th>Вид</th>
			<th>Сумма</th>
			<th>Комм</th>
			<th><span class='fa fa-trash-o'></span></th>
		</tr>
	</thead>
	<tbody>";
while($r=$db->fetch_assoc($res)) {
	$login=$db->dlookup("username","users","klid={$r['klid']}");
	$name=$db->dlookup("real_user_name","users","klid={$r['klid']}");
	$vid=$r['vid']==1?"деньгами":"услугами";
	if($r['vid']==3)
		$vid="бонусы inSales";
	$comm=htmlspecialchars($r['comm']);
	print "<tr>
		<td><input type='checkbox'> <label>$n</label></td>
		 <td>$login</td>
		 <td>$name</td>
		 <td>$vid</td>
		 <td>{$r['sum_pay']} р.</td>
		 <td>$comm</td>
		<td><a href='?do_del_op=yes&id={$r['id']}' class='' target=''><span class='fa fa-trash-o'></span></a></td>
		</tr>";
	$n++;
}
print "</tbody></table>";
print "</div>
	<hr>";

if(isset($_GET['do_del_op'])) {
	$id=intval($_GET['id']);
	$db->query("DELETE FROM partnerka_pay WHERE id='$id'");
	print "<script>location='?alert=warning&msg=Операция удалена'</script>";
}

if(isset($_GET['do_pay'])) {
	$klid=intval($_GET['klid']);
	$sum_pay=intval($_GET['sum_pay']);
	$vid=intval($_GET['vid']);
	$comm=mb_substr(trim($_GET['comm']),0,1024);
	$err=false;
	$msg="";
	if($vid==3) { //insales
		include_once "/var/www/vlav/data/www/wwl/inc/insales.class.php";
		$in=new insales($insales_id,$insales_shop);
		if($ctrl_id==167) {
			$in->id_app="winwinland_demo_11";
			$in->secret_key='e5697c177c0f51497d069969e170dbcb';
			$in->get_credentials();
		}
		$uid=$db->dlookup("uid","cards","id='$klid'");
		$client_id=$db->dlookup("tool_uid","cards2other","uid='$uid' AND tool='insales'");
		//print "OK klid=$klid uid=$uid client_id=$client_id"; exit;
		$res=$in->bonus_create($client_id, $sum_pay, $comm);
		if(isset($res['error'])) {
			$err=true;
			$msg="Ошибка синхронизации с inSales.
			Для начисления бонусов в inSales должен быть клиент с таким же номером телефона или email,
			а также должен быть соответствующий тариф и стоять галочка, что разрешены бонусы";
		} else
			$msg="Бонусы inSales начислены.";
	}
	if(!$err) {
		$tm1=$db->dt1(time());
		//~ if($db->dlookup("id","partnerka_pay","klid='$klid' AND vid='$vid' AND  tm='$tm1'"))
			//~ $db->query("UPDATE partnerka_pay SET  sum_pay='$sum_pay',vid='$vid' WHERE klid='$klid' AND  tm='$tm1'");
		//~ else
			//~ $db->query("INSERT INTO partnerka_pay SET tm='$tm1', klid='$klid', sum_pay='$sum_pay' ,vid='$vid'");
		$db->query("INSERT INTO partnerka_pay SET tm='$tm1', klid='$klid', sum_pay='$sum_pay' ,vid='$vid',comm='".$db->escape($comm)."'");
		//$db->query("UPDATE partnerka_pay SET comm='".$db->escape($comm)."' WHERE klid='$klid' AND  tm='$tm1'");
		//print "<div class='alert alert-success' >Saved $sum_pay OK</div>";
		print "<script>location='?alert=success&msg=$msg . Добавлено $sum_pay OK'</script>";
	} else {
		print "<script>location='?alert=warning&msg=$msg'</script>";
	}
}

if(isset($_GET['pay'])) {
	
	$klid=intval($_GET['klid']);
	$sum_pay=intval($_GET['sum_pay']);
	$name=$db->dlookup("real_user_name","users","klid=$klid");
	$bank=nl2br($db->dlookup("bank_details","users","klid='$klid'"));
	$bank=!empty($bank)?$bank:"не указаны";
	$username=$db->dlookup("username","users","klid='$klid'");
?>
	<div class='container card mb-5 bg-light' id='payform'>
	<h2 class='text-center' ><?print "$name ($username)";?></h2>
	<p class='text-center' ><a href='#r_<?=$klid?>' class='' target=''>найти в списке</a></p>
	<h3>К выплате - <?=$sum_pay?>р.</h3>
	
		<div class='card bg-light p-3' >Реквизиты партнера для выплаты:
			<div class='card p-2 bg-light' ><?=$bank?></div>
		</div>

		<div class='card bg-light p-3' >
		<form action='#r_$klid'>

		<!--<input type='hidden' name='vid' value='1'>-->
		<!--
		<div class='p-3' >
			<div><input type='radio' name='vid' value='1' checked> Банк</div>
			<div><input type='radio' name='vid' value='2' > Зачет за трафик</div>
		</div>
		<p>Перевод средств по договору партнерской программы <br>
		НДС не облагается.
		</p>
		-->

		<div class='p-3 form-inline' >
			<label for='sum_out' class='d-inline'>Сумма:</label>
			<input type='text' name='sum_pay' value='<?=$sum_pay?>' class='form-control d-inline text-center' placeholder='Сумма' id='sum_out'>
			<div class='radio mx-3'><label><input type='radio' name='vid' value='1' checked>Деньгами</label></div>
			<div class='radio mx-3'><label><input type='radio' name='vid' value='2'>Услугами</label></div>
			<?
			if($insales_id) {
				$uid=$db->dlookup("uid","cards","id='$klid'");
				if(!$client_id=$db->dlookup("tool_uid","cards2other","uid='$uid' AND tool='insales'")) {
					include_once "/var/www/vlav/data/www/wwl/inc/insales.class.php";
					$in=new insales($insales_id,$insales_shop);
					if(!$client_id=$in->search_client($uid)['id']) {
						$client_id=$in->create_client($uid)['id'];
					}
				}
				print "<div class='radio mx-3'><label title='$client_id $ctrl_id'><input type='radio' name='vid' value='3'>Бонусами inSales</label></div>";
			}
			?>
		</div>
		<div class='p-3 pt-2'>
			<label for='comm_out'>Комментарий:</label>
			<textarea class='form-control' name='comm' rows='3' id='comm_out'></textarea>
		</div>
		<div class='p-3' ><button type='submit' name='do_pay' value='yes' class='btn btn-info' >Провести выплату</button></div>
		<input type='hidden' name='klid' value='<?=$klid?>'>
		</form>
		</div>
	</div>
	<hr>
<?
}

//print "<div class='p-3 top10' ><a href='?' class='btn btn-primary' target=''>Обновить</a></div>";
$a_all=(isset($_GET['all']))?'active':'';
$a_non_zero=(!isset($_GET['all']))?'active':'';
print "<ul class='nav nav-tabs' >
		<li class='$a_all nav-item'><a class='$a_all nav-link' href='?all=yes' target=''>ВСЕ</a></li>
		<li class='$a_non_zero nav-item'><a class='$a_non_zero nav-link' href='?non_zero=yes' target=''>Только с остатками >100р</a></li>
	</ul>";

$dt=date("d.m.Y H:i");
print "<h3 class='text-right' >$dt</h3>";

$res=$db->query("SELECT * FROM users WHERE del=0 AND klid>0");
print "<table class='table table-striped' >";
print "<thead>
		<th>#</th>
		<th>login</th>
		<th>klid</th>
		<th>Имя в CRM</th>
		<th>Начислено</th>
		<th>Выплачено</th>
		<th>Дата посл выплаты</th>
		<th>Остаток</th>
		<th><span class='fa fa-credit-card'></span></th>
	</thead>
	<tbody>";
$n=1; $s=0;
//~ include_once "../leadgen/leadgen.class.php";
//~ $l=new leadgen;
while($r=$db->fetch_assoc($res)) {
	$user_id=$r['id'];
	$klid=$r['klid'];
	$email=$db->dlookup("email","users","klid=$klid");
	$bank=(!empty($db->dlookup("bank_details","users","klid=$klid")))?"<span class='badge bg-success text-white' >Ok</span>":"-";
	if(empty($name=trim($db->dlookup("real_user_name","users","klid=$klid")))) {
		$name=$db->dlookup("surname","cards","id='$klid'")." ".$db->dlookup("name","cards","id='$klid'");
		$db->query("UPDATE users SET real_user_name='".$db->escape($name)."' WHERE klid='$klid'");
	}
	$tg=$db->dlookup("tg_nick","users","klid=$klid");
	$mob=$db->dlookup("mob","cards","id=$klid");
	$uid=$db->dlookup("uid","cards","id=$klid");
	$user_id1=$db->dlookup("user_id","cards","id=$klid");
	$login1=($user_id1)?$db->dlookup("username","users","id=$user_id1"):"";
	$name1=($user_id1)?$db->dlookup("real_user_name","users","id=$user_id1"):"";
	$sum_r=$db->fetch_assoc($db->query("SELECT SUM(sum_pay) AS s_r FROM partnerka_pay WHERE sum_pay>0 AND klid='$klid'"))['s_r'];
	if(!$sum_r)
		$sum_r=0;
	$sum_p=$db->fetch_assoc($db->query("SELECT SUM(fee_sum) AS s_p FROM partnerka_op WHERE klid_up='$klid'"))['s_p'];
	$sum_p+=-(int)$db->fetch_assoc($db->query("SELECT SUM(sum_pay) AS s_r FROM partnerka_pay WHERE sum_pay<0 AND klid='$klid'"))['s_r'];
	if(!$sum_p)
		$sum_p=0;
	if(!$sum_r && !$sum_p)
		continue;
	$dt_last_pay=date("d.m.Y",$db->fetch_assoc($db->query("SELECT tm FROM partnerka_pay WHERE klid='$klid' ORDER BY tm DESC LIMIT 1"))['tm']);
	//print "HERE_$dt_last_pay ";
	if($dt_last_pay==date("d.m.Y"))
		$dt_last_pay='';
	$sum_pay=$sum_p-$sum_r;
	$s+=$sum_pay;
	if(!$_GET['all'] && $sum_pay>-100 && $sum_pay<100)
		continue;
	$sum_pay_=($sum_pay)?"<span class='ROBOTO font-weight-bold' >$sum_pay</span>":$sum_pay;
	$hl=($klid==$_GET['klid'])?"bg-warning text-white":"";
	$leads_rest=0; //$l->get_leads_rest($r['id']);
	print "<tr class='$hl' id='r_$klid'>
		<td>$n</td>
		<td title='klid=$klid'><b>{$r['username']}</b></td>
		<td>$klid</td>
		<td title='партнер выше-$name1'>$name</td>
		<td><a href='report_pay_detailed.php?klid=$klid' class='' target='_blank'>$sum_p</a></td>
		<td><a href='report_pay_detailed_2.php?klid=$klid' class='' target='_blank'>$sum_r</a></td>
		<td><span class='badge' >$dt_last_pay</span></td>
		<td>$sum_pay_</a></td>
		<td class='pl-3' ><a href='?pay=yes&klid=$klid&sum_pay=$sum_pay#payform' class='' target=''><span class='fa fa-credit-card' title='провести оплату'></span></a></td>
		</tr>";
	$n++;
}
print "</tbody></table>";
print "<h3 class='text-right' >Всего к выплате: $s р.</h3>";
print "</div>";
$db->bottom();
?>
