<?
$title="Сводка по звонкам";
include "top.reports.php";

if(!isset($_SESSION['calls_report_pass_limit']))
	$_SESSION['calls_report_pass_limit']=15;

if(isset($_GET['pass_limit']))
	$_SESSION['calls_report_pass_limit']=intval($_GET['pass_limit']);
$pass_limit=$_SESSION['calls_report_pass_limit'];

if(!isset($_SESSION['man_number']))
	$_SESSION['man_number']=101;

if(isset($_GET['man_number']))
	$_SESSION['man_number']=intval($_GET['man_number']);
$man_number=$_SESSION['man_number'];

$res=$db->query("SELECT * FROM novofon_log WHERE tm>=$tm1 AND tm<=$tm2 AND man_number=$man_number");
$n=0; $talk_time_duration=0; $total_time_duration=0; $last_t2=0; //$db->dt1(time())+(10*60*60);
$total_pass=0; $t_start=0;
$n_ok=0; $n_1=0; $n_2=0; $n_3=0;

$man_id=$db->dlookup("id","users","sip_internal_number='$man_number'");
$man_name=$db->dlookup("real_user_name","users","sip_internal_number='$man_number'");


?>
<p><a href='../novofon_prompt_edit.php' class='' target='_blank'>GPT prompt edit</a></p>
<p>
<form class="form-inline">
    <div class="form-group mx-sm-3 mb-2">
        <label for="pass_limit" class="">Макс интервал между звонками, мин:</label>
        <input type="number" class="form-control text-center" id="pass_limit" name="pass_limit" value="<?=$pass_limit?>" placeholder="мин" required>
    </div>
    <div class="form-group mx-sm-3 mb-2">
        <label for="man_number" class="">Caller number:</label>
        <select class="form-control" id="man_number" name="man_number">
			<?
				$res1=$db->query("SELECT * FROM users WHERE sip_internal_number>0 AND del=0");
				while($r1=$db->fetch_assoc($res1)) {
					$sel=($man_number==$r1['sip_internal_number']) ? "SELECTED" : "";
					print "<option $sel value='{$r1['sip_internal_number']}'>{$r1['real_user_name']}</option>";
				}
			?>
        </select>
    </div>
    <input type="hidden" name="tm1" value="<?=$tm1?>">
    <input type="hidden" name="tm2" value="<?=$tm2?>">
    <button type="submit" class="btn btn-primary mb-2">Ок</button>
</form>
</p>
<table class='table table-striped' >
	<thead>
		<tr>
			<th>#</th>
			<th>Дата</th>
			<th>Начало</th>
			<th>Конец</th>
			<th>Мин от пред звонка</th>
			<th>Длит разговора, мин</th>
			<th>Длит звонка, мин</th>
			<th>Номер</th>
			<th>REC</th>
			<th>TXT</th>
			<th>AI</th>
			<th>CRM</th>
			<th>ЛПР</th>
			<th>БАЛЛ</th>
		</tr>
	</thead>
	<tbody>
<?
$cnt_crm=0; $cnt_lpr=0;
while($r=$db->fetch_assoc($res)) {
	$t1=$r['tm']-$r['total_time_duration'];
	$t2=$r['tm'];
	if(!$last_t2)
		$last_t2=$t2;
	if($last_t2 && $t1>$last_t2)
		$pass=($t1-$last_t2); else $pass=0;
	if($n==1)
		$t_start=$t1;
	$rec=""; $txt=""; $ai=""; $crm=""; $lpr=""; $new_rec="";
	if($uid=$db->dlookup("uid","cards","mob_search='".$db->check_mob($r['client_number'])."'")) {
		$crm="<a href='$DB200/msg.php?uid=$uid' class='' target='_blank'><img src='https://winwinland.ru/favicon.ico' style='width:24px;'></i></a>";
		//$cnt=$db->fetch_assoc($db->query("SELECT COUNT(uid) AS cnt FROM `msgs` WHERE uid='$uid' AND source_id=165 GROUP BY uid"))['cnt'];
		$cnt_crm++;
		$lpr=$db->dlookup("id","tags_op","uid=$uid AND tag_id=8") ? "<i class='fa fa-check'></i>" : "";
		if(!empty($lpr))
			$cnt_lpr++;
		$new_rec=date("d.m.Y H:i",$db->fetch_assoc($db->query("SELECT cards.tm AS tm FROM cards
			JOIN novofon_log ON cards.mob_search=client_number
			JOIN tags_op ON cards.uid=tags_op.uid
			WHERE cards.uid='$uid' AND cards.tm>='$tm1' AND cards.tm<='$tm2' AND tag_id!=45 AND man_number=$man_number"))['tm']);// ? "*" : "";
	}
	$rec=$r['record'] ? "<a href='{$r['record']}' class='' target='_blank'><i class='fa fa-play'></i></a>" : "";
	$txt=$r['transcribe'] ? "<a href='$DB200/calls/?fname={$r['transcribe']}' class='' target='_blank'><i class='fa fa-file-text'></i></a>" : "";
	$ai=$r['gpt'] ? "<a href='$DB200/calls/?fname={$r['gpt']}' class='' target='_blank'><i class='fa fa-info-circle'></i></a>" : "";

	?>
	<tr>
		<td><?=($n+1)?></td>
		<td><?=date("d.m.Y",$t1)?></td>
		<td><?=date("H:i",$t1)?></td>
		<td><?=date("H:i",$t2)?></td>
		<td><?=$db->format_time($pass)?></td>
		<td><?=$db->format_time($r['talk_time_duration'])?></td>
		<td><?=$db->format_time($r['total_time_duration'])?></td>
		<td><?=$r['client_number']?></td>
		<td class='text-center' ><?=$rec?></td>
		<td class='text-center' ><?=$txt?></td>
		<td class='text-center' ><?=$ai?></td>
		<td class='text-center' ><?=$crm?> <?=$new_rec?></td>
		<td class='text-center' ><?=$lpr?></td>
		<td class='text-center' ><span class='badge bg-light p-2' ><?=$r['val']?></span></td>
	</tr>
	<?
	if($r['talk_time_duration'])
		$n_ok++;
	if($r['talk_time_duration']>60)
		$n_1++;
	if($r['talk_time_duration']>120)
		$n_2++;
	if($r['talk_time_duration']>180)
		$n_3++;
	$n++;
	$talk_time_duration+=$r['talk_time_duration'];
	$total_time_duration+=$r['total_time_duration'];
	$last_t2=$t2;
	if($pass>=($pass_limit*60))
		$total_pass+=$pass;
}
$t_end=$t2;
?>
</tbody></table>
<?
$t1=$db->format_time($talk_time_duration);
$t2=$db->format_time($total_time_duration);
$p=$db->format_time($total_pass);
$delay_last_call=(time()-$t_end)<(1*60*60) ? time()-$t_end : 0;

//~ $res=$db->query("SELECT * FROM cards JOIN tags_op ON cards.uid=tags_op.uid
			//~ WHERE tm>='$t1' AND tm<='$t2' AND tag_id=0");
$cnt_crm_newrecs=$db->fetch_assoc($db->query("SELECT COUNT(DISTINCT cards.uid) AS cnt FROM cards
	JOIN novofon_log ON cards.mob_search=client_number
	JOIN tags_op ON cards.uid=tags_op.uid
	WHERE (cards.tm>='$tm1' AND cards.tm<='$tm2') AND (novofon_log.tm>='$tm1' AND novofon_log.tm<='$tm2') AND tag_id!=45 AND man_number=$man_number"))['cnt'];
$cnt_crm=$db->fetch_assoc($db->query("SELECT COUNT(DISTINCT cards.uid) AS cnt FROM cards
	JOIN novofon_log ON cards.mob_search=client_number
	JOIN tags_op ON cards.uid=tags_op.uid
	WHERE novofon_log.tm>='$tm1' AND novofon_log.tm<='$tm2'  AND tag_id!=45 AND man_number=$man_number",0))['cnt'];
$cnt_lpr_newrecs=$db->fetch_assoc($db->query("SELECT COUNT(DISTINCT cards.uid) AS cnt FROM cards
	JOIN novofon_log ON cards.mob_search=client_number
	JOIN tags_op ON cards.uid=tags_op.uid
	WHERE (cards.tm>='$tm1' AND cards.tm<='$tm2') AND (novofon_log.tm>='$tm1' AND novofon_log.tm<='$tm2') AND tag_id=8 AND tag_id!=45 AND man_number=$man_number"))['cnt'];
$cnt_lpr=$db->fetch_assoc($db->query("SELECT COUNT(DISTINCT cards.uid) AS cnt FROM cards
	JOIN novofon_log ON cards.mob_search=client_number
	JOIN tags_op ON cards.uid=tags_op.uid
	WHERE (novofon_log.tm>='$tm1' AND novofon_log.tm<='$tm2') AND tag_id=8 AND tag_id!=45 AND man_number=$man_number"))['cnt'];
$n_new_leads=$n-$cnt_crm+$cnt_crm_newrecs;
?>
	<p>Всего звонков: <?=$n?></p>
	<p>в т.ч. дозвонов: <?=$n_ok?></p>
	<p>в т.ч. более 1 мин: <?=$n_1?></p>
	<p>в т.ч. более 2 мин: <?=$n_2?></p>
	<p>в т.ч. более 3 мин: <?=$n_3?></p>
	<p>Общее время разговора: <?=$t1?></p>
	<p>Общее время звонков: <?=$t2?></p>
	<p>Общее время интервалов между звонками более <?=$pass_limit?> мин.: <?=$p?></p>
	<p>Общее время работы: <?=$db->format_time($t_end-$t_start)?></p>
	<p>Первый звонок: <?=date("H:i",$t_start)?></p>
	<p>Прошло времени с последнего звонка: <?=$db->format_time($delay_last_call)?></p> 
	<p>Всего CRM: разговоров <?=$cnt_crm?>, в т.ч. новых лидов <?=$cnt_crm_newrecs?> (<?=round($cnt_crm_newrecs/$n_new_leads*100,0)?>%)</p>
	<p>Всего ЛПР: разговоров <?=$cnt_lpr?>, в т.ч. новых лидов ЛПР <?=$cnt_lpr_newrecs?> (<?=round($cnt_lpr_newrecs/$n_new_leads*100,0)?>%)</p>
<?
//print "all=$n passed time=$p talk=$t1 total=$t2";

include "reports/bottom.reports.php";


?>
