<?php


$params = [
	'fio'			=>'Владимир',
	'phone'			=>'+79119999999',
	'email'			=>'test@mail.ru',
	'agree'			=>'on',
	'go_submit'		=>'yes',
	'vk_uid'		=>'12345',
	'product_id'	=>'33',
	'sum_disp'		=>'42000',
	'order_number'	=>'12-345',
	'bc'			=>'12345'
];



?>
<html>
<head>
</head>
<body>
<form method="POST" id='form' >
<?
foreach($params as $p=>$v)
echo "$p: <input type='text' name='$p' value='$v' id='$p'><br/>\r\n";
?>
  <div>
    <input type="radio" id="contactChoice1" name="system" value="alfa" onClick="document.getElementById('form').action = 'pay_alfa.php';"/>
    <label for="contactChoice1">alfa</label>

    <input type="radio" id="contactChoice2" name="system" value="tinkoff" onClick="document.getElementById('form').action = 'pay_tinkoff.php';" />
    <label for="contactChoice2">tinkoff</label>

    <input type="radio" id="contactChoice3" name="system" value="ukassa" onClick="document.getElementById('form').action = 'pay_ukassa.php';" />
    <label for="contactChoice3">yookassa</label>
  </div>
  <div>
    <button type="submit">Submit</button>
  </div>
</form>
</body>
</html>