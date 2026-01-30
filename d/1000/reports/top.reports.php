<?
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
chdir("..");
include "init.inc.php";
$t=new top($database,$title, false);
$db=new db($database);

$tm1=$db->dt1(time());
$tm2=$db->dt2(time());

if(isset($_GET['go'])) {
	$tm1=strtotime($_GET['dt1'])?$db->dt1(strtotime($_GET['dt1'])):$db->dt1(time()-(30*24*60*60));
	$tm2=strtotime($_GET['dt2'])?$db->dt2(strtotime($_GET['dt2'])):$db->dt2(time());
}
if(isset($_GET['today'])) {
	$tm1=$db->dt1(time());
	$tm2=$db->dt2(time());
}
if(isset($_GET['yesterday'])) {
	$tm1=$db->dt1(time()-(1*24*60*60));
	$tm2=$db->dt2(time()-(1*24*60*60));
}
if(isset($_GET['7days'])) {
	$tm1=$db->dt1(time()-(7*24*60*60));
	$tm2=$db->dt2(time());
}
if(isset($_GET['14days'])) {
	$tm1=$db->dt1(time()-(14*24*60*60));
	$tm2=$db->dt2(time());
}
if(isset($_GET['30days'])) {
	$tm1=$db->dt1(time()-(30*24*60*60));
	$tm2=$db->dt2(time());
}

?>

<div class='container' >
	<p>
		<a href='../cp.php?view=yes' class='' target=''>Уйти в CRM</a>
		<a href='javascript:window.close()' class='' target=''>Вернуться назад</a>
	</p>
<h2 class='text-center py-4' ><?=$title?></h2>

<?if(!isset($no_menu)) { ?>
<div class='py-4' >
<form >
	<a href='?today=yes' class='btn btn-info m-1' target=''>Сегодня</a>
	<a href='?yesterday=yes' class='btn btn-info m-1' target=''>Вчера</a>
	<a href='?7days=yes' class='btn btn-info m-1' target=''>7_дней</a>
	<a href='?14days=yes' class='btn btn-info m-1' target=''>14_дней</a>
	<a href='?30days=yes' class='btn btn-info m-1' target=''>30_дней</a>
	<div class='d-flex' >
	<input type='date' name='dt1' value='<?=date('Y-m-d',$tm1)?>' class='form-control' >
	<input type='date' name='dt2' value='<?=date('Y-m-d',$tm2)?>' class='form-control'>
	<button type='submit' name='go' value='yes' class='btn btn-primary btn-sm' >Ok</button>
	</div>
</form>
</div>
<?}?>

