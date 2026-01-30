<?php
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "init.inc.php";

if(isset($_GET['num'])) {
	$db=new db($database);
	$num=intval($_GET['num']);
	$r=$db->fetch_assoc($db->query("SELECT * FROM invoices WHERE num='$num'"));
	$dt=date("d.m.Y",$r['tm']);
	$comm=$r['comm'];
	$txt=file_get_contents("invoices/invoice_template_ao.html");
	$txt=preg_replace("/\{\{num\}\}/s","$num",$txt);
	$txt=preg_replace("/\{\{date\}\}/s","$dt",$txt);
	$txt=preg_replace("/\{\{company\}\}/s",$r['company'],$txt);
	$txt=preg_replace("/\{\{inn\}\}/s",$r['inn'],$txt);

	$res=$db->query("SELECT * FROM invoices WHERE num='$num'");
	$n=1;
	$tr_products="";
	$amount=0; $qnt_amount=0;
	while($r=$db->fetch_assoc($res)) {
		$sum=$r['qnt']*$r['price'];
		$tr_products.="<tr  class='no-bottom-border'>
					<td>".$n++."</td>
					<td>{$r['product_descr']}</td>
					<td>{$r['qnt']}</td>
					<td>{$r['qnt_descr']}</td>
					<td style='text-align: center;'>".int2money($r['price'])."</td>
					<td style='text-align: center;'>".int2money($sum)."</td>
				</tr> \n";
		$amount+=$sum;
		$qnt_amount+=$r['qnt'];
	}
	$txt=preg_replace("/\{\{tr_products\}\}/s",$tr_products,$txt);
	$txt=preg_replace("/\{\{amount\}\}/s",int2money($amount),$txt);
	$txt=preg_replace("/\{\{qnt_amount\}\}/s",$qnt_amount,$txt);
	$txt=preg_replace("/\{\{amount_words\}\}/s",num2str($amount),$txt);
	$txt=preg_replace("/\{\{comm\}\}/s",$comm,$txt);

	// Подключаем автозагрузчик Composer
	require_once '/var/www/vlav/data/www/wwl/inc/mpdf/vendor/autoload.php';
	$mpdf = new \Mpdf\Mpdf();
	$mpdf->WriteHTML($txt);
	$suf=strtolower($db->random_string(10));
	$fname = "invoices/$suf.pdf";
	$link="https://wwl.winwinland.ru/".$fname;
	$mpdf->Output($fname, \Mpdf\Output\Destination::FILE);
	print "<h3>Счет можно скачать по ссылке: <a href='$link' class='' target='_blank'>$link</a></h3>";
	//$mpdf->Output("winwinland_invoice_$suf.pdf", \Mpdf\Output\Destination::INLINE);
	exit;
}

$db = new top($database, 'Выставить счет', false,false,false);
// Check if uid is set and valid
if(isset($_GET['ctrl_id'])) {
	$_GET['uid']=$db->dlookup("uid","0ctrl","id=".intval($_GET['ctrl_id']));
}
if (!$uid = intval($_GET['uid'])) {
	$uid=0;
    //~ print "<p class='alert alert-warning'>Ошибка, отсутствует uid</p>";
    //~ $db->bottom();
    //~ exit;
}

$pids = [];
if (isset($_GET['pids'])) {
    if (is_array($_GET['pids'])) {
        foreach ($_GET['pids'] as $pid) {
            $pids[] = intval($pid);
        }
    }
} else {
	?>
	<div class="container mt-5">
		<h2>Создание счета</h2>
		<p>Выберите Продукты</p>
		<form >
			<?php foreach ($base_prices as $product_id => $product): ?>
				<div class="form-check">
					<input class="form-check-input" type="checkbox" name="pids[]" value="<?php echo $product_id; ?>" id="product-<?php echo $product_id; ?>">
					<label class="form-check-label" for="product-<?php echo $product_id; ?>">
						<?php echo htmlspecialchars($product['descr']); ?>
					</label>
				</div>
			<?php endforeach; ?>
			<input type='hidden' name='uid' value='<?=$uid?>'>
			<button type="submit" class="btn btn-primary mt-3">
				<i class="fa fa-check"></i> Submit
			</button>
		</form>
	</div>
	<?
	$db->bottom();
	exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and escape form data
    $tm = intval(strtotime($_POST['tm'])); // Convert date to timestamp
    //$num = intval($_POST['num']);
	$num = intval($db->fetch_row($db->query("SELECT num FROM invoices WHERE 1 ORDER BY num DESC LIMIT 1"))[0]) + 1;
    $company = $db->escape($_POST['company']);
    $inn = intval($_POST['inn']);
    $total_sum = 0; // Initialize total sum for the invoice

    // Insert data for each product
    foreach ($pids as $pid) {
        if (isset($base_prices[$pid])) {
            $product_id = $pid;
            $product_descr = $db->escape($base_prices[$pid]['descr']);
            $price = intval($base_prices[$pid][1]); // Assuming price is the second element in the array
            $qnt = intval($_POST['product_qnt'][$pid]); // Get the quantity from the submitted form
            $price = intval($_POST['product_price'][$pid]); // Get the quantity from the submitted form
            $sum = $price * $qnt; // Calculate individual sum
            $total_sum += $sum; // Add to total sum
            $comm=mb_substr($db->escape($_POST['comm']),0,1024);
            
            $insert = $db->query("INSERT INTO invoices 
                (tm, num, company, inn, uid, product_id, product_descr, qnt, qnt_descr, price, valuta, nds, comm) 
                VALUES 
                ($tm, $num, '$company', $inn, $uid, $product_id, '$product_descr', $qnt, 'шт', $price, 'RUB', 0, 'comm')",0);
        }
    }

    if ($insert) {
        $insert_id = $db->insert_id();
        echo "<p class='alert alert-success'>Счет успешно сохранен! ID: $insert_id. Общая сумма: $total_sum руб.</p>";
        sleep(3);
        print "<script>window.location.href = '?num=$num'</script>";
        exit;
    } else {
        echo "<p class='alert alert-danger'>Ошибка при сохранении счета!</p>";
    }
}

// Fetch next invoice number
$num = intval($db->fetch_row($db->query("SELECT num FROM invoices WHERE 1 ORDER BY num DESC LIMIT 1"))[0]) + 1;


?>

<?
	$display=(isset($_GET['ctrl_id'])) ? "none" : "block";
?>


<div class="container mt-5">
    <h2>Выписать счет</h2>
    <p style='display:<?=$display?>;'><a href='#disp_all' class='' data-toggle='collapse'>Все счета</a></p>
    <?
	$res=$db->query("SELECT num,tm,company,inn,SUM(price*qnt) AS s FROM invoices WHERE 1 GROUP BY num,tm,company,inn ORDER BY tm DESC LIMIT 50");
	?>
	<div class='collapse' id='disp_all' >
		<table class='table table-striped' >
			<thead>
				<tr>
					<th>Номер</th>
					<th>Дата</th>
					<th>Юр.лицо</th>
					<th>ИНН</th>
					<th>Сумма</th>
					<th>Ссылка</th>
				</tr>
			</thead>
			<tbody>
		<?
		while($r=$db->fetch_assoc($res)) {
			$dt=date("d.m.Y H:i",$r['tm']);
			$link="invoices/winwinland_invoice_{$r['num']}.pdf";
			$link_= (!file_exists($link)) ? "^" : "ссылка";
			$link= (!file_exists($link)) ? "invoice.php?num={$r['num']}" : $link;
			?>
			<tr>
				<td><?=$r['num']?></td>
				<td><?=$dt?></td>
				<td><?=$r['company']?></td>
				<td><?=$r['inn']?></td>
				<td><?=round($r['s'],0)?></td>
				<td><a href='<?="https://wwl.winwinland.ru/".$link?>' class='' target='_blank'><?=$link_?></a></td>
			</tr>
			<?
		}
		?>
		</tbody></table>
	</div>
	<?
	?>

    <form method="POST">
        <div class="form-group row" style='display:<?=$display?>;'>
            <div class="col-md-6">
                <label for="num">Номер</label>
                <input type="number" name="num" value="<?= $num ?>" class="form-control" id="num" placeholder="Enter an integer" required>
            </div>
            <div class="col-md-6">
                <label for="tm">Дата</label>
                <input type="date" name="tm" value="<?= date("Y-m-d") ?>" class="form-control" id="tm" required>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-8">
                <label for="company">Плательщик</label>
                <input type="text" name="company" class="form-control" id="company" placeholder="Введите юрлицо плательщика" required>
            </div>
            <div class="col-md-4">
                <label for="inn">ИНН</label>
                <input type="number" name="inn" class="form-control" id="inn" placeholder="ИНН плательщика" required>
            </div>
        </div>

        <?php if (!empty($pids)): ?>
            <h3 style='display:<?=$display?>;'>Продукты <a href='?uid=<?=$uid?>' class='small' target=''>выбрать продукты</a></h3>
            <table class="table" style='display:<?=$display?>;'>
                <thead>
                    <tr>
                        <th>Описание продукта</th>
                        <th>Цена</th>
<!--
                        <th>Количество</th>
                        <th>Сумма</th>
-->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pids as $pid): ?>
                        <?php if (isset($base_prices[$pid])): ?>
                            <tr>
                                <td width='70%'>
                                    <textarea rows='2' name="product_descr[<?= $pid ?>]" class="form-control" rows="3"><?= htmlspecialchars($base_prices[$pid]['descr']) ?></textarea>
                                </td>
                                <td width='20%'>
                                    <?//= htmlspecialchars($base_prices[$pid][1])
										$price= isset($_GET['product_price'][$pid]) ? intval($_GET['product_price'][$pid]) : $base_prices[$pid][1];
                                    ?>
                                    <input type="number" name="product_price[<?= $pid ?>]" class="form-control product-count" value="<?=$price?>" min="1" >
                                </td>
                                    <input type="hidden" name="product_qnt[<?= $pid ?>]" class="form-control product-count" value="1" min="1" >
<!--
                                <td width='10%'>
                                    <input type="number" name="product_qnt[<?= $pid ?>]" class="form-control product-count" value="1" min="1" >
                                </td>
-->
<!--
                                <td>
                                    <span class="product-sum" id="sum_<?= $pid ?>"> <?=$base_prices[$pid][1]?> руб.</span>
                                </td>
-->
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
<!--
            <h5 id="totalSum" style='display:<?=$display?>;'>Общая сумма: <strong>0</strong> руб.</h5>
-->
        <?php endif; ?>

        <div class="form-group" style='display:<?=$display?>;'>
            <label for="comm">Комментарий</label>
            <small>(будет напечатан внизу счета)</small>
            <textarea name="comm" class="form-control" id="comm" rows="3"></textarea>
        </div>
        
        <input type="hidden" name="uid" value="<?= htmlspecialchars($uid) ?>">
        <input type="hidden" name="cnt_descr" value="шт">
        <input type="hidden" name="valuta" value="RUB">
        <input type="hidden" name="nds" value="0">
        
        <button type="submit" class="btn btn-primary">Скачать счет</button>
    </form>
</div>

<script>
    function calculateSum() {
		return;
        let totalSum = 0;
        const productCounts = document.querySelectorAll('.product-count');
        const productSums = document.querySelectorAll('.product-sum');

        productCounts.forEach((input, index) => {
            const price = parseFloat(input.value); //parseFloat(input.closest('tr').querySelector('td:nth-child(2)').innerText);
            const quantity = parseInt(input.value);
            const sum = price * quantity;

            productSums[index].innerText = sum.toFixed(2) + ' руб.';
            totalSum += sum;
        });

        document.getElementById('totalSum').querySelector('strong').innerText = totalSum.toFixed(2);
    }

    const inputs = document.querySelectorAll('.product-count');
    inputs.forEach(input => {
        input.addEventListener('input', calculateSum);
    });

    // Initial calculation
    calculateSum();
</script>

<?php
$db->bottom();

function num2str($num) {
	$nul='ноль';
	$ten=array(
		array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
		array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
	);
	$a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
	$tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
	$hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
	$unit=array( // Units
		array('копейка' ,'копейки' ,'копеек',	 1),
		array('рубль'   ,'рубля'   ,'рублей'    ,0),
		array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
		array('миллион' ,'миллиона','миллионов' ,0),
		array('миллиард','милиарда','миллиардов',0),
	);
	//
	list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
	$out = array();
	if (intval($rub)>0) {
		foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
			if (!intval($v)) continue;
			$uk = sizeof($unit)-$uk-1; // unit key
			$gender = $unit[$uk][3];
			list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
			// mega-logic
			$out[] = $hundred[$i1]; # 1xx-9xx
			if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
			else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
			// units without rub & kop
			if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
		} //foreach
	}
	else $out[] = $nul;
	$out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
	$out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
	$string=trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
	$string=mb_convert_case(mb_substr($string, 0, 1), MB_CASE_UPPER) . mb_substr($string, 1, null);
	return $string;
}

/**
 * Склоняем словоформу
 * @ author runcore
 */
function morph($n, $f1, $f2, $f5) {
	$n = abs(intval($n)) % 100;
	if ($n>10 && $n<20) return $f5;
	$n = $n % 10;
	if ($n>1 && $n<5) return $f2;
	if ($n==1) return $f1;
	return $f5;
}
function int2money($sum) {
	return $sum.".00";
}


?>
