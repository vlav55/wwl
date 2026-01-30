<?
$title="Найти платеж";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "/var/www/vlav/data/www/wwl/inc/cp.class.php";
include "init.inc.php";
$t=new top($database,"Найти платеж",true);
$db=$t;

$phone=(isset($_GET['phone'])) ? mb_substr(trim($_GET['phone']),0,15) : "";
$email=(isset($_GET['email'])) ? mb_substr(trim($_GET['email']),0,48) : "";

if(isset($_GET['edit'])) {
	$uid=intval($_GET['uid']);
	$r=$db->fetch_assoc($db->query("SELECT * FROM avangard WHERE res=1 AND vk_uid='$uid'"));
	?>
    <form>
		<div class="form-group">
			<label for="paySystem">Pay System</label>
			<input type="text" class="form-control" id="paySystem" name="pay_system" maxlength="16" value="<?= $r['pay_system'] ?>" required>
		</div>
		<div class="form-group">
			<label for="sku">SKU</label>
			<input type="text" class="form-control" id="sku" name="sku" maxlength="32" value="<?= $r['sku'] ?>" required>
		</div>
		<div class="form-group">
			<label for="productId">Product ID</label>
			<input type="number" class="form-control" id="productId" name="product_id" value="<?= $r['product_id'] ?>" required>
		</div>
		<div class="form-group">
			<label for="orderId">Order ID</label>
			<input type="text" class="form-control" id="orderId" name="order_id" maxlength="64" value="<?= $r['order_id'] ?>" required>
		</div>
		<div class="form-group">
			<label for="orderNumber">Order Number</label>
			<input type="text" class="form-control" id="orderNumber" name="order_number" maxlength="100" value="<?= $r['order_number'] ?>" required>
		</div>
		<div class="form-group">
			<label for="orderDescr">Order Description</label>
			<textarea class="form-control" id="orderDescr" name="order_descr" required><?= $r['order_descr'] ?></textarea>
		</div>
		<div class="form-group">
			<label for="ticket">Ticket</label>
			<input type="text" class="form-control" id="ticket" name="ticket" maxlength="11" value="<?= $r['ticket'] ?>" required>
		</div>
		<div class="form-group">
			<label for="amount">Amount</label>
			<input type="number" class="form-control" id="amount" name="amount" value="<?= $r['amount'] ?>" required>
		</div>
		<div class="form-group">
			<label for="amount1">Amount1</label>
			<input type="number" class="form-control" id="amount1" name="amount1" value="<?= $r['amount1'] ?>" required>
		</div>
		<div class="form-group">
			<label for="cName">Customer Name</label>
			<input type="text" class="form-control" id="cName" name="c_name" maxlength="128" value="<?= $r['c_name'] ?>" required>
		</div>
		<div class="form-group">
			<label for="phone">Phone</label>
			<input type="text" class="form-control" id="phone" name="phone" maxlength="20" value="<?= $r['phone'] ?>" required>
		</div>
		<div class="form-group">
			<label for="email">Email</label>
			<input type="email" class="form-control" id="email" name="email" maxlength="64" value="<?= $r['email'] ?>" required>
		</div>
		<div class="form-group">
			<label for="vkUid">VK UID</label>
			<input type="number" class="form-control" id="vkUid" name="vk_uid" value="<?= $r['vk_uid'] ?>" required>
		</div>
		<button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
	<?
	exit;
}


?>
<div class="container mt-3">
<h2>Найти платеж</h2>
<p class='card rounded p-3'  >Найти платеж по номеру телефона или емэйл, которые были указаны при оплате.
<br>Номер телефона вводится единой строкой только цифры, без плюса, пробелов и тире,
возможно искать по части номера, равно как и по части емэйла.
<br>Выводится не более 50 строк результатов поиска.
</p>
	<form method="GET" class="form-inline"> <!-- Adjust the action attribute as needed -->
		<div class="form-group">
			<label for="phone">Телефон</label>
			<input type="text" class="form-control" id="phone" name="phone" placeholder="телефон" value="<?=$phone?>">
		</div>
		<br>
		<div class="form-group">
			<label for="email">Email</label>
			<input type="text" class="form-control" id="email" name="email" placeholder="емэйл" value="<?=$email?>">
		</div>
		<div>
		<button type="submit" class="btn btn-primary" name="go" valye="yes">Найти</button>
		<button type="button" class="btn btn-secondary" onclick="window.close()';">отмена</button>
		</div>
	</form>
</div>

<?
if(!empty($phone) || !empty($email)) {
	?>
	<table class='table table-striped' >
		<thead>
			<tr>
				<th>Дата</th>
				<th>Имя</th>
				<th>Тел</th>
				<th>Емэйл</th>
				<th>Продукт</th>
				<th>Сумма</th>
				<th>Вид платежа</th>
				<th>Найти в CRM</th>
			</tr>
		</thead>
	<?
	$where="1=2 ";
	if(!empty($phone))
		$where .= "OR phone LIKE '%$phone%'";
	if(!empty($email))
		$where .= "OR email LIKE '%$email%'";
	$res=$db->query("SELECT * FROM avangard WHERE res=1 AND ($where) LIMIT 50");
	while($r=$db->fetch_assoc($res)) {
		$dt=date("d.m.Y H:i",$r['tm']);
		$uid=intval($r['vk_uid']);
		?>
		<tr>
			<td><?=$dt?></td>
			<td><?=$r['c_name']?></td>
			<td><?=$r['phone']?></td>
			<td><?=$r['email']?></td>
			<td><?=$r['order_descr']?></td>
			<td><?=$r['amount']?></td>
			<td><?=$r['pay_system']?></td>
			<td>
				<a href='<?=$DB200."/cp.php?str=$uid&view=yes&filter=Search"?>' class='' target='_blank'><i class="fa fa-search" aria-hidden="true"></i></a>
<!--
				<a href='?edit=yes&uid=<?=$uid?>' class=''><i class="fa fa-edit" aria-hidden="true"></i></a>
-->
			</td>
		</tr>
		<?
	}
	print "</table>";
}
?>



<?
$t->bottom();
?>
