#!/usr/bin/php -q
<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db('vkt');
$tm1=$db->dt1(time());
$tm2=time();

$man_number=101;
$pass_limit=15;

$res=$db->query("SELECT * FROM novofon_log WHERE tm>=$tm1 AND tm<=$tm2 AND man_number='$man_number'");
$n=1; $talk_time_duration=0; $total_time_duration=0; $last_t2=0;//$db->dt1(time())+(10*60*60);
$total_pass=0; $t_start=0;
$n_ok=0; $n_1=0; $n_2=0; $n_3=0;

while($r=$db->fetch_assoc($res)) {
	$t1=$r['tm']-$r['total_time_duration'];
	$t2=$r['tm'];
	if(!$last_t2)
		$last_t2=$t2;
	if($last_t2 && $t1>$last_t2)
		$pass=($t1-$last_t2); else $pass=0;
	if($n==1)
		$t_start=$t1;
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
$t1=$db->format_time($talk_time_duration);
$t2=$db->format_time($total_time_duration);
$p=$db->format_time($total_pass);
$out=date("d.m.Y H:i")."
	😳 С МОМЕНТА ПОСЛЕДНЕГО ЗВОНКА ПРОШЛО БОЛЕЕ $pass_limit МИНУТ
	Первый звонок: ".date("H:i",$t_start)."
	Всего звонков за сегодня: $n 
	в т.ч. дозвонов: $n_ok 
	в т.ч. более 1 мин: $n_1 
	в т.ч. более 2 мин: $n_2 
	в т.ч. более 3 мин: $n_3 
	Общее время разговора: $t1 
	Общее время звонков: $t2 
	Общее время интервалов между звонками более $pass_limit мин.: $p 
	Общее время работы: ". $db->format_time($t_end-$t_start)."
	Прошло времени с последнего звонка: ".$db->format_time(time()-$t_end)." 
	";
print nl2br($out); 
$tmp_fname="tmp/novofon_chk_calls.tmp";
if( (time()-$t_end) > ($pass_limit*60) ) {
	if(!file_exists($tmp_fname)) {
		$db->notify_chat(-4799845674,$out);
		//$db->notify_me($out);
		touch($tmp_fname);
	}
} else {
	if(file_exists($tmp_fname)) {
		$out="😀 СЛЕДУЮЩИЙ ЗВОНОК ЗАФИКСИРОВАН";
		$db->notify_chat(-4799845674,$out);
		//$db->notify_me($out);
		unlink($tmp_fname);
	}
}
?>
