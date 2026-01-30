<?
if(isset($VK_GROUP_ID))
	$gid=intval($VK_GROUP_ID); else $gid=0;
$t=new top("vktrade",$w="",$disp_menu=true, $favicon=false, $ask_passw=true, $gid);

$bs=new bs;
if(@$_GET['payment_success']) {
	print "<div class='alert alert-success'><h2>Оплата успешно зачислена. Спасибо! Просьба уведомить об оплате.</h2></div>";
}
if(isset($_GET['code']))
	$code=$_GET['code']; else $code="";
$success_url="";
//print $success_url; 
$sum=5000;
$target="VKTRADE ($gid - $code)";
$promo="
<form class='form-inline'>
  <div class='form-group'>
    <label for='promo'>Введите промо-код:</label>
    <input type='text' class='form-control' id='promo' name='code' value=''>
  </div>
  <button type='submit' class='btn btn-default' name='promo' value='yes'>Отправить</button>
</form>
";

?>
<br><br>


<div class='panel panel-primary' style=''>
<!--
	<div class='panel-heading'>Прием платежей с банковских карт и мобильных телефонов</div>
-->
	<div class='panel-body text-center'>
	<form action='#payform'>
	<input type="hidden" name='uid' value="<?=$uid?>">
	<input type="hidden" name='first_name' value="<?=$first_name?>">
	<input type="hidden" name='last_name' value="<?=$last_name?>">
	</form>
	<h4 id='payform'>Оплата с банковских карт, яндекс-денег и моб телефонов</h4>
	<div class='well' >О факте оплаты просьба сообщить</div>
	<div class='alert alert-info font12'>установите способ платежа - VISA (оплата с банковских карт). Можно также оплатить в яндекс-деньгах или с мобильного телефона</div>
	<iframe src="https://money.yandex.ru/quickpay/shop-widget?writer=seller&targets=<?=$target?>&targets-hint=&default-sum=<?=$sum?>&button-text=11&payment-type-choice=on&mobile-payment-type-choice=on&hint=%D0%A3%D0%BA%D0%B0%D0%B6%D0%B8%D1%82%D0%B5&successURL=<?=$success_url?>&quickpay=shop&account=410015074823072" width="450" height="198" frameborder="0" allowtransparency="true" scrolling="no"></iframe>	
	<?=$promo?>
	</div>
</div>
<br><br><br><br><br><br>
<?

?>



<?
$t->bottom();
?>
