<?
include_once "/var/www/vlav/data/www/wwl/inc/products.1.inc.php";
exit;
?>

<?
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "init.inc.php";
$top=new top($database,'Товары',false);
$db=new db($database);

ob_start();
$action = empty($_GET['action']) ? '' : filter_input(INPUT_GET, 'action');
$productId = empty($_GET['id']) ? 0 : intval($_GET['id']);
?>

<div><a href='s_panel.php' class='' target=''>вернуться</a></div>
    <div class="container">
        <div class="row">
            <div class="col">
                <h2>Товары и услуги</h2>
			<div class="mt-2">
				<a class="btn btn-info" href="?action=create">Добавить новый</a>
			</div>

<?php
switch($action) {
    case 'update':
        $product = $db->fetch_assoc($db->query("SELECT * FROM `product` WHERE `id` = " . $productId));

       // print_r($product);

        if (empty($product)) {
            echo '<div class="alert alert-danger">Продукт не найден!</div>';
        } else {
            if (! empty($_POST)) {
                $id = is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;
                $descr = htmlentities(mb_substr(trim($_POST['descr']), 0, 255)) ?? null;
                $jc = htmlentities(mb_substr(trim($_POST['jc']), 0, 32)) ?? null;
                $price0 = is_numeric($_POST['price0']) ? (int)$_POST['price0'] : 0;
                $price1 = is_numeric($_POST['price1']) ? (int)$_POST['price1'] : 0;
                $price2 = is_numeric($_POST['price2']) ? (int)$_POST['price2'] : 0;
                $term = is_numeric($_POST['term']) ? (int)$_POST['term'] : 0;
                $sourceId = is_numeric($_POST['source_id']) ? (int)$_POST['source_id'] : 0;
                $razdel = is_numeric($_POST['razdel']) ? (int)$_POST['razdel'] : 0;
                $installment = is_numeric($_POST['installment']) ? (int)$_POST['installment'] : 0;
                $fee_1 = is_numeric($_POST['fee_1']) ? (int)$_POST['fee_1'] : 0;
                $fee_2 = is_numeric($_POST['fee_2']) ? (int)$_POST['fee_2'] : 0;
                $stock = is_numeric($_POST['stock']) ? (int)$_POST['stock'] : 0;
                $senler = is_numeric($_POST['senler']) ? (int)$_POST['senler'] : 0;
                $sp = is_numeric($_POST['sp']) ? (int)$_POST['sp'] : 0;
                $spTemplate = htmlentities(mb_substr(trim($_POST['sp_template']), 0, 32)) ?? null;
                $use = is_numeric($_POST['use']) ? (int)$_POST['use'] : 0;
                $vid = is_numeric($_POST['vid']) ? (int)$_POST['vid'] : 0;

                if ( intval($product['id']) !== intval($id) && $db->dlookup("id","product","id='$id'")) {
                    $errorMessage = '<div class="alert alert-info">Продукт с ID: ' . $id . ' уже существует.</div>';
                } else {
                    $db->query("UPDATE `product` SET `id` = '" . $id . "', `descr` = '" . $descr . "', `price0` = '" . $price0 . "', `price1` = '" . $price1 . "',
                        `price2` = '" . $price2 . "', `term` = '" . $term . "', `source_id` = '" . $sourceId . "',
                        `razdel` = '" . $razdel . "', `installment` = '" . $installment . "', `fee_1` = '" . $fee_1 . "', `fee_2` = '" . $fee_2 . "',
                        `stock` = '" . $stock . "', `senler` = '" . $senler . "', `sp` = '" . $sp . "', `sp_template` = '" . $spTemplate . "',
                        `jc` = '" . $jc . "', `in_use` = '" . $use . "', `vid` = '" . $vid . "'
                        WHERE `id` = " . $productId);
                    header("Location: ?action=list#item" . $id, true, 302);
                    exit;
                }
            }

            if (isset($errorMessage)) {
                echo $errorMessage;
            }

            echo '
            <form action="#" method="post">
                <div class="form-group">
                    <label for="__id">ID</label>
                    <input name="id" class="form-control" id="__id" value="' . $product['id'] . '">
                </div>
                <div class="form-group">
                    <label for="descr">Наименование</label>
                    <input name="descr" class="form-control" id="descr" value="' . $product['descr'] . '">
                </div>
                <div class="form-group">
                    <label for="__price0">Цена зачеркнутая</label>
                    <input name="price0" class="form-control" id="__price0" value="' . $product['price0'] . '">
                </div>
                <div class="form-group">
                    <label for="__price1">Цена без скидки</label>
                    <input name="price1" class="form-control" id="__price1" value="' . $product['price1'] . '">
                </div>
                <div class="form-group">
                    <label for="__price2">Цена со скидкой</label>
                    <input name="price2" class="form-control" id="__price2" value="' . $product['price2'] . '">
                </div>
                <div class="form-group">
                    <label for="__term">Срок доступа, дн</label>
                    <input name="term" class="form-control" id="__term" value="' . $product['term'] . '">
                </div>
                <div class="form-group">
                    <label for="__fee_1">Партнерское вознаграждение уровень 1, %</label>
                    <input name="fee_1" class="form-control" id="__fee_1" value="' . $product['fee_1'] . '">
                </div>
                <div class="form-group">
                    <label for="__fee_2">Партнерское вознаграждение уровень 2, %</label>
                    <input name="fee_2" class="form-control" id="__fee_2" value="' . $product['fee_2'] . '">
                </div>
                <div class="form-group">
                    <label for="__senler">Группа Senler</label>
                    <input name="senler" class="form-control" id="__senler" type="number" min="0" value="' . $product['senler'] . '">
                </div>
                <div class="form-group">
                    <label for="__sp_template">Шаблон email</label>
                    <input name="sp_template" class="form-control" id="__sp_template" type="text" max="32" value="' . $product['sp_template'] . '">
                </div>
                <div class="form-group">
                    <label for="__jc">Доступ JC</label>
                    <input name="jc" class="form-control" id="__jc" value="' . $product['jc'] . '">
                </div>

                <button name="_update" type="submit" class="btn btn-primary">Сохранить</button>
                <a class="btn btn-warning" href="?">Отменить</a>
            </form>
            ';
        }
        break;

    case 'del':
        $sqldel = $db->query("DELETE FROM `product` WHERE `id` = " . $productId);
        header('Location: ?', true, 302);
        exit;
        break;

    case 'create':
        if (! empty($_POST)) {
            $id = is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;
            if(!$id) {
				$id=intval($db->fetch_assoc($db->query("SELECT MAX(id) AS max_id FROM product WHERE 1"))['max_id'])+1;
			}
            $descr = htmlentities(mb_substr(trim($_POST['descr']), 0, 255)) ?? null;
            $jc = htmlentities(mb_substr(trim($_POST['jc']), 0, 32)) ?? null;
            $price0 = is_numeric($_POST['price0']) ? (int)$_POST['price0'] : 0;
            $price1 = is_numeric($_POST['price1']) ? (int)$_POST['price1'] : 0;
            $price2 = is_numeric($_POST['price2']) ? (int)$_POST['price2'] : 0;
            $term = is_numeric($_POST['term']) ? (int)$_POST['term'] : 0;
            $sourceId = is_numeric($_POST['source_id']) ? (int)$_POST['source_id'] : 0;
            $razdel = is_numeric($_POST['razdel']) ? (int)$_POST['razdel'] : 0;
            $installment = is_numeric($_POST['installment']) ? (int)$_POST['installment'] : 0;
            $fee_1 = is_numeric($_POST['fee_1']) ? (int)$_POST['fee_1'] : 0;
            $fee_2 = is_numeric($_POST['fee_2']) ? (int)$_POST['fee_2'] : 0;
            $stock = is_numeric($_POST['stock']) ? (int)$_POST['stock'] : 0;
            $senler = is_numeric($_POST['senler']) ? (int)$_POST['senler'] : 0;
            $sp = is_numeric($_POST['sp']) ? (int)$_POST['sp'] : 0;
            $spTemplate = htmlentities(mb_substr(trim($_POST['sp_template']), 0, 32)) ?? null;
            $use = is_numeric($_POST['use']) ? (int)$_POST['use'] : 0;
            $vid = is_numeric($_POST['vid']) ? (int)$_POST['vid'] : 0;

            if ($db->dlookup("id","product","id='$id'")) {
                $errorMessage = '<div class="alert alert-info">Продукт с ID: ' . $id . ' уже добавлен.</div>';
            } else {
                $db->query("INSERT INTO `product`
                    (`id`,`descr`, `price0`, `price1`, `price2`, `term`, `source_id`, `razdel`, `installment`, `fee_1`, `fee_2`, `stock`, `senler`, `sp`, `sp_template`, `jc`, `in_use`, `vid`)
                    VALUES
                    ('" . $id . "', '" . $descr . "', '" . $price0 . "', '" . $price1 . "', '" . $price2 . "', '" . $term . "', '" . $sourceId . "', '" . $razdel . "', '" . $installment . "', '" . $fee_1 . "', '" . $fee_2 . "',
                    '" . $stock ."', '" . $senler ."', '" . $sp ."', '" . $spTemplate ."', '" . $jc ."', '" . $use ."', '" . $vid ."'
                    )
                ");

                header("Location: ?action=list#item" . $id, true, 302);
                exit;
            }
        }

        if (isset($errorMessage)) {
            echo $errorMessage;
        }

        echo '
        <form action="#" method="post">
            <div class="form-group">
                <label for="__id">ID</label>
                <input name="id" class="form-control" id="__id" type="number" min="0">
            </div>
            <div class="form-group">
                <label for="descr">Наименование</label>
                <input name="descr" class="form-control" id="descr" max="255">
            </div>
            <div class="form-group">
                <label for="__price0">Цена зачеркнутая</label>
                <input name="price0" class="form-control" id="__price0" type="number" min="0">
            </div>
            <div class="form-group">
                <label for="__price1">Цена без скидки</label>
                <input name="price1" class="form-control" id="__price1" type="number" min="0">
            </div>
            <div class="form-group">
                <label for="__price2">Цена со скидкой</label>
                <input name="price2" class="form-control" id="__price2" type="number" min="0">
            </div>
            <div class="form-group">
                <label for="__term">Срок доступа, дн</label>
                <input name="term" class="form-control" id="__term" type="number" min="0">
            </div>

			<input name="source_id" class="form-control" id="__source_id" type="hidden" min="0" value="30">
			<input name="razdel" class="form-control" id="__razdel" type="hidden" min="0" value="0">
			<input name="installment" class="form-control" id="__installment" type="hidden" min="0" value="">

            <div class="form-group">
                <label for="__fee_1">Партнерское вознаграждение уровень 1, %</label>
                <input name="fee_1" class="form-control" id="__fee_1" type="number" min="0">
            </div>
            <div class="form-group">
                <label for="__fee_2">Партнерское вознаграждение уровень 2, %</label>
                <input name="fee_2" class="form-control" id="__fee_2" type="number" min="0">
            </div>

            <input name="stock" class="form-control" id="__stock" type="hidden" min="0" value="0">

            <div class="form-group">
                <label for="__senler">Группа Senler</label>
                <input name="senler" class="form-control" id="__senler" type="number" min="0">
            </div>

            <input name="vid" class="form-control" id="__vid" type="hidden" min="0" value="0">

            <input name="sp" class="form-control" id="__sp" type="hidden" min="0" value="0">

            <div class="form-group">
                <label for="__sp_template">Шаблон email</label>
                <input name="sp_template" class="form-control" id="__sp_template" type="text" max="32">
            </div>
            <div class="form-group">
                <label for="__jc">Доступ JC</label>
                <input name="jc" class="form-control" id="__jc" max="32">
            </div>

            <input name="in_use" class="form-control" id="__use" type="hidden" min="0" value="0">

            <button type="submit" class="btn btn-primary">Добавить</button>
                <a class="btn btn-warning" href="?">Отменить</a>
        </form>
        ';
        break;

    case 'list':
    default:
        $res = $db->query("SELECT * FROM `product`");

        echo '
        <div class="table-responsive">
            <table class="table table-sm">
                <thead class="thead-dark_">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Наименование</th>
                        <th scope="col">Цена зачеркнутая</th>
                        <th scope="col">Цена без скидки</th>
                        <th scope="col">Цена со скидкой</th>
                        <th scope="col">Срок, дн.</th>
                        <th scope="col">Партн. вознагр. уровень_1</th>
                        <th scope="col">Партн. вознагр. уровень_2</th>
                        <th scope="col">Действия</th>
                    </tr>
                </thead>
                <tbody>
        ';

        while ($product=$db->fetch_assoc($res)) {
            echo '
                <tr id="item' . $product['id'] . '">
                    <th scope="row">' . $product['id'] . '</th>
                    <td>' . $product['descr'] . '</td>
                    <td>'. $product['price0'] .'</td>
                    <td>'. $product['price1'] .'</td>
                    <td>'. $product['price2'] .'</td>
                    <td>'. $product['term'] .'</td>
                    <td>'. $product['fee_1'] .'</td>
                    <td>'. $product['fee_2'] .'</td>
                    <td>
                        <a class="btn btn-sm btn-success" href="?action=update&id='. $product['id'] .'"><span class="fa fa-edit" ></span></a>
                        <a class="btn btn-sm btn-danger" href="?action=del&id='. $product['id'] .'" onclick="return confirm(\'Действительно удалить товар\');"><span class="fa fa-trash-o" ></span></a>
                    </td>
                </tr>
                ';
        }

        echo '
                </tbody>
            </table>
        </div>
        ';
}
?>

            </div>
        </div>
    </div>
    <script>
        let className = '';
        const hash = window.location.hash;
        if (hash.length > 1) {
            className = hash.split('#')[1];
        }
        if (className) {
            let activeRow = document.getElementById(className);
            activeRow.classList.add('bg-warning');
        }
    </script>

<?
$top->bottom();
?>
