<?php
require_once "/var/www/vlav/data/www/wwl/inc/top.class.php";
require_once "init.inc.php";

ob_start();
$top = new top($database, 'Продукты', false);
$db = new db($database);

$action = empty($_GET['action']) ? '' : $_GET['action'];
$productId = empty($_GET['id']) ? 0 : intval($_GET['id']);

?>

<div><a href='s_panel.php' class='btn btn-link'>вернуться</a></div>
<div class="container">
    <div class="row">
        <div class="col">
            <h2>Продукты</h2>
            <div class="mt-2">
                <a class="btn btn-info" href="?action=create">Добавить новый</a>
            </div>

<?php
switch($action) {
    case 'update':
    case 'create':
        $product = [];
        if ($action == 'update') {
            $stmt = $db->query("SELECT * FROM `product` WHERE `id` = '$productId'");
            $product = $db->fetch_assoc($stmt);
            if (empty($product)) {
                echo '<div class="alert alert-danger">Продукт не найден!</div>';
                break;
            }
        }

        if (!empty($_POST)) {
            $id = is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;
            $sku = htmlentities(mb_substr(trim($_POST['sku']), 0, 32)) ?? null;
            $descr = htmlentities(mb_substr(trim($_POST['descr']), 0, 255)) ?? null;
            $jc = htmlentities(mb_substr(trim($_POST['jc']), 0, 32)) ?? null;
            $price0 = is_numeric($_POST['price0']) ? (int)$_POST['price0'] : 0;
            $price1 = is_numeric($_POST['price1']) ? (int)$_POST['price1'] : 0;
            $price2 = is_numeric($_POST['price2']) ? (int)$_POST['price2'] : 0;
            $term = is_numeric($_POST['term']) ? (int)$_POST['term'] : 0;
            $sourceId = is_numeric($_POST['source_id']) ? (int)$_POST['source_id'] : 30;
            $razdel = is_numeric($_POST['razdel']) ? (int)$_POST['razdel'] : 0;
            $tag_id = is_numeric($_POST['tag_id']) ? (int)$_POST['tag_id'] : 0;
            $installment = is_numeric($_POST['installment']) ? (int)$_POST['installment'] : 0;
            $fee_1 = is_numeric($_POST['fee_1']) ? (float)$_POST['fee_1'] : 0;
            $fee_2 = is_numeric($_POST['fee_2']) ? (float)$_POST['fee_2'] : 0;
            $fee_cnt = is_numeric($_POST['fee_cnt']) ? (float)$_POST['fee_cnt'] : 0;
            $stock = is_numeric($_POST['stock']) ? (int)$_POST['stock'] : 0;
            $senler = is_numeric($_POST['senler']) ? (int)$_POST['senler'] : 0;
            $sp = is_numeric($_POST['sp']) ? (int)$_POST['sp'] : 0;
            $spTemplate = htmlentities(mb_substr(trim($_POST['sp_template']), 0, 64)) ?? null;

            if ($db->dlookup("id", "product", "id='$id' AND id!='$productId'")) {
                $errorMessage = '<div class="alert alert-warning">Продукт с ID: ' . $id . ' уже существует.</div>';
            } else {
                $fields = "`id` = '$id', `sku` = '$sku', `descr` = '$descr', `jc` = '$jc', 
                          `price0` = '$price0', `price1` = '$price1', `price2` = '$price2', 
                          `term` = '$term', `source_id` = '$sourceId', `razdel` = '$razdel', 
                          `tag_id` = '$tag_id', `installment` = '$installment', `fee_1` = '$fee_1', 
                          `fee_2` = '$fee_2', `fee_cnt` = '$fee_cnt', `stock` = '$stock', 
                          `senler` = '$senler', `sp` = '$sp', `sp_template` = '$spTemplate'";

                if ($action == 'update') {
                    $db->query("UPDATE `product` SET $fields WHERE `id` = '$productId'");
                } else {
                    $db->query("INSERT INTO `product` SET $fields");
                }
                
                header("Location: ?#item$id");
                exit;
            }
        }

        if (isset($errorMessage)) {
            echo $errorMessage;
        }

        $id_disabled = "";
        if ($action == 'update' && ($db->dlookup("id", "avangard", "product_id='$productId'") ||
            $db->dlookup("id", "lands", "product_id='$productId'"))) {
            $id_disabled = "readonly";
        }

        ?>
        <form action="#" method="POST">
            <div class="form-group">
                <label for="__id">ID</label>
                <input <?php echo $id_disabled; ?> name="id" class="form-control" id="__id" value="<?php echo $product["id"]; ?>">
            </div>
            <div class="form-group">
                <label for="sku">SKU</label>
                <input name="sku" class="form-control" id="sku" value="<?php echo $product["sku"]; ?>">
            </div>
            <div class="form-group">
                <label for="descr">Наименование</label>
                <input name="descr" class="form-control" id="descr" value="<?php echo $product["descr"]; ?>">
            </div>
            <div class="form-group">
                <label for="__price0">Цена зачеркнутая</label>
                <input name="price0" class="form-control" id="__price0" value="<?php echo $product["price0"]; ?>">
            </div>
            <div class="form-group">
                <label for="__price1">Цена базовая</label>
                <input name="price1" class="form-control" id="__price1" value="<?php echo $product["price1"]; ?>">
            </div>
            <div class="form-group">
                <label for="__price2">Цена со скидкой</label>
                <input name="price2" class="form-control" id="__price2" value="<?php echo $product["price2"]; ?>">
            </div>
            <div class="form-group">
                <label for="__term">Срок доступа, дн</label>
                <input name="term" class="form-control" id="__term" value="<?php echo $product["term"]; ?>">
            </div>
            <div class="form-group">
                <label for="__fee_1">Партнерское вознаграждение уровень 1, % или фикс</label>
                <input name="fee_1" class="form-control" id="__fee_1" value="<?php echo $product["fee_1"]; ?>">
            </div>
            <div class="form-group">
                <label for="__fee_2">Партнерское вознаграждение уровень 2, % или фикс</label>
                <input name="fee_2" class="form-control" id="__fee_2" value="<?php echo $product["fee_2"]; ?>">
            </div>
            <div class="form-group">
                <label for="__fee_cnt">На сколько продаж начислять вознаграждение (0 без ограничений)</label>
                <input name="fee_cnt" class="form-control" id="__fee_cnt" value="<?php echo $product["fee_cnt"]; ?>">
            </div>
            <div class="form-group">
                <label for="__sp_template">Шаблон email при успешной оплате</label>
                <input name="sp_template" class="form-control" id="__sp_template" value="<?php echo $product["sp_template"]; ?>">
            </div>
            <div class="form-group">
                <label for="__razdel">Установить этап при оплате</label>
                <select name="razdel" class="form-control" id="__razdel">
                    <?php
                    $res = $db->query("SELECT * FROM razdel WHERE del=0 ORDER BY razdel_num");
                    while ($r = $db->fetch_assoc($res)) {
                        $sel = ($r["id"] == $product["razdel"]) ? "SELECTED" : "";
                        if ($r["id"] == 0) {
                            $r["razdel_name"] = "не менять этап";
                        }
                        echo '<option value="' . $r["id"] . '" ' . $sel . '>' . $r["razdel_name"] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="__tag_id">Установить тэг при оплате</label>
                <select name="tag_id" class="form-control" id="__tag_id">
                    <option value="0">не присваивать тэг</option>
                    <?php
                    $res = $db->query("SELECT * FROM tags WHERE del=0 ORDER BY tag_name");
                    while ($r = $db->fetch_assoc($res)) {
                        $sel = ($r["id"] == $product["tag_id"]) ? "SELECTED" : "";
                        echo '<option value="' . $r["id"] . '" ' . $sel . '>' . $r["tag_name"] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="__jc">Доступ JC</label>
                <input name="jc" class="form-control" id="__jc" value="<?php echo $product["jc"]; ?>">
            </div>
            <div class="form-group">
                <label for="__senler">Группа Senler</label>
                <input name="senler" class="form-control" id="__senler" value="<?php echo $product["senler"]; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a class="btn btn-warning" href="?">Отменить</a>
        </form>
    <?php        break;

    case 'del':
        $db->query("UPDATE `product` SET del = 1 WHERE `id` = '$productId'");
        header("Location: ?");
        exit;
        break;

    default:
        $stmt = $db->query("SELECT * FROM `product` WHERE del = 0 ORDER BY id ASC");
        
        echo '<div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID/Действия</th>
                        <th>Основная информация</th>
                        <th>Цены</th>
                        <th>Партнерская программа</th>
                        <th>Доп. настройки</th>
                    </tr>
                </thead>
                <tbody>';

        while ($product = $db->fetch_assoc($stmt)) {
            $fee_cnt = !$product["fee_cnt"] ? 
                '<span class="badge bg-light">без огр</span>' : 
                '<span class="badge bg-light">' . $product["fee_cnt"] . '</span>';
            
            if (!$product["razdel"]) {
                $razdel = '<span class="badge bg-light">не менять</span>';
            } else {
                $razdel = $db->dlookup("razdel_name", "razdel", "id='{$product["razdel"]}'");
                $s = $db->get_style_by_razdel($product["razdel"]);
                $razdel = '<span class="badge" style="' . $s . '">' . $razdel . '</span>';
            }
            
            if (!$product["tag_id"]) {
                $tag_name = '<span class="badge bg-light">нет</span>';
            } else {
                $bg = $db->dlookup("tag_color", "tags", "id='{$product["tag_id"]}'");
                $color = $db->get_contrast_color($bg);
                $tag_name = '<span class="badge" style="color:' . $color . 
                            '; background-color:' . $bg . ';">' .
                            $db->dlookup("tag_name", "tags", "id='{$product["tag_id"]}'") . 
                            '</span>';
            }

            echo "<tr id='item{$product["id"]}'>
                    <td>
                        <div class='fw-bold mb-1'>#{$product["id"]}</div>
                        <div class='btn-group'>
                            <a class='font20' href='?action=update&id={$product["id"]}'><i class='fa fa-edit'></i></a>
                            &nbsp;
                            <a class='' href='?action=del&id={$product["id"]}' onclick='return confirm(\"Действительно удалить товар?\");'><i class='fa fa-trash-o'></i></a>
                        </div>
                    </td>
                    <td>
                        <div class='fw-bold mb-1'>{$product["descr"]}</div>
                        <div class='small text-muted'>SKU: {$product["sku"]}</div>
                    </td>
                    <td>
                        <div class='small text-muted'>Зачеркнутая: {$product["price0"]}&nbsp;₽</div>
                        <div class='fw-bold'>Базовая: {$product["price1"]}&nbsp;₽</div>
                        <div class='text-success'>Спеццена: {$product["price2"]}&nbsp;₽</div>
                        <div class='small mt-1'>Срок: <span class='badge bg-light'>{$product["term"]} дн.</span></div>
                    </td>
                    <td>
                        <div>Уровень 1: " . ($product["fee_1"] < 100 ? $product["fee_1"] . "%" : $product["fee_1"] . "₽") . "</div>
                        <div>Уровень 2: " . ($product["fee_2"] < 100 ? $product["fee_2"] . "%" : $product["fee_2"] . "₽") . "</div>
                        <div>Лимит продаж: " . $fee_cnt . "</div>
                    </td>
                    <td>
                        <div title='при продаже клиенту отправлять емэйл (шаблон u-go)'>Email: {$product["sp_template"]}</div>
                        <div title='при продаже переводить клиента на этап'>Этап: $razdel</div>
                        <div title='при продаже клиенту добавлять тэг'>Тэг: $tag_name</div>
                    </td>
                </tr>";
        }

        echo '</tbody></table></div>';
}
?>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hash = window.location.hash;
    if (hash) {
        const row = document.getElementById(hash.substring(1));
        if (row) {
            row.classList.add('bg-light');
        }
    }
});
</script>

<?php
$top->bottom();
?>

<style>
/* Table styles */
.table {
    font-size: 0.9rem;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-weight: normal;
    font-size: 0.85rem;
}

/* Make sure table is responsive but doesn't scroll horizontally */
.table-responsive {
    overflow-x: visible;
}

/* Add some spacing between elements */
.btn-group {
    margin-top: 0.5rem;
}

/* Improve readability */
.text-muted {
    color: #6c757d !important;
}

.fw-bold {
    font-weight: 500 !important;
}

/* Optional: add hover effect */
.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.02);
}
</style>
