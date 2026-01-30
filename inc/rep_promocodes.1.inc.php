<?
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
chdir("..");
include "init.inc.php";

$clients = [];
if (isset($_GET['query_clients'])) {
    // Ensure to properly sanitize input to prevent SQL injection
    $query = $db->escape($_GET['query_clients']);
    $sql = "SELECT uid, name, surname FROM cards WHERE del=0 AND (name LIKE '%$query%' OR surname LIKE '%$query%')";
    $result = $db->query($sql);
    
    while ($client = $db->fetch_assoc($result)) {
        $clients[] = $client;
    }
    
    echo json_encode($clients); // Return JSON response
    exit;
}
$products = [];
if (isset($_GET['query_products'])) {
    // Ensure to properly sanitize input to prevent SQL injection
    $query = $db->escape($_GET['query_products']);
    $sql = "SELECT id,descr FROM product WHERE del=0 AND (descr LIKE '%$query%')";
    $result = $db->query($sql);
    
    while ($product = $db->fetch_assoc($result)) {
        $products[] = $product;
    }
    
    echo json_encode($products); // Return JSON response
    exit;
}


$db=new top($database,'Промокоды', false);
//print "$database";
print "<div class='h1' ><a href='../cp.php?view=yes' class=' text-warning' target=''><i class='fa fa-sign-out'></i></a></div>";

// Initialization
$errors = [];
$submitType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if($_SESSION['access_level']>3) {
		$errors[] = 'У вас нет доступа, обратитесь к администратору.';
	}
	if(isset($_POST['del'])) {
		$id=intval($_POST['id']);
		$db->query("DELETE FROM promocodes WHERE id='$id'");
		print "<p class='alert alert-warning' >Промокод удален</p>";
	}
	if(isset($_POST['add'])) {
		// Capture form inputs using appropriate sanitization
		$tm2 = isset($_POST['tm2']) ? strtotime($_POST['tm2']) : null;
		//print $_POST['tm2']." ".date("d.m.Y H:i",$tm2) ." $tm2";
		$uid = isset($_POST['uid']) ? intval($_POST['uid']) : null;
		$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;
		$discount = isset($_POST['discount']) ? intval($_POST['discount']) : null;
		$price = isset($_POST['price']) ? intval($_POST['price']) : null;
		$promocode = isset($_POST['promocode']) ? $db->promocode_validate($_POST['promocode']) : ''; // Escape and trim the string
		$fee_1 = isset($_POST['fee_1']) ? floatval($_POST['fee_1']) : 0;
		$fee_2 =isset($_POST['fee_2']) ? floatval($_POST['fee_2']) : 0;
		$cnt = isset($_POST['cnt']) && intval($_POST['cnt']) ? intval($_POST['cnt']) : -1;

		// Validate fields
		if (empty($tm2) || !$tm2) {
			$errors[] = 'Неверный формат даты времени';
		}
		if (empty($uid)) {
			$errors[] = 'Не заполнен владелец промокода';
		}
		if (empty($product_id)) {
			$errors[] = 'Не заполнен продукт';
		}
		if (empty($discount) && empty($price)) {
			$errors[] = 'Скидка или цена требуется';
		}
		if (empty($promocode)) {
			$errors[] = 'Не заполнен промокод';
		}

		// Determine if we're inserting or updating
		if (isset($_POST['id']) && !empty($_POST['id'])) {
			$submitType = "edit";
			$id = intval($_POST['id']); // Make sure to cast to int

			// If no errors, perform the update query
			if (empty($errors)) {
				$query = "UPDATE promocodes SET 
						tm2 = '$tm2', 
						uid = '$uid', 
						product_id = '$product_id', 
						discount = '$discount', 
						price = '$price', 
						promocode = '$promocode', 
						fee_1 = '$fee_1', 
						fee_2 = '$fee_2', 
						cnt = '$cnt' 
						WHERE id = '$id'";
				$db->query($query);
				print "<script>location='?id=$id#$id'</script>";
			}
		} else {
			$submitType = "insert";
			if($db->dlookup("id","promocodes","promocode LIKE '$promocode'"))
				$errors[] = "Промокод <b>$promocode</b> уже существует";
			// If no errors, perform the insert query
			if (empty($errors)) {
				$query = "INSERT INTO promocodes (tm1, tm2, uid, product_id, discount, price, promocode, fee_1, fee_2, cnt) 
						VALUES ('$tm1', '$tm2', '$uid', '$product_id', '$discount', '$price', '$promocode', '$fee_1', '$fee_2', '$cnt')";
				$db->query($query);
				$id=$db->insert_id();
				print "<script>location='?id=$id#$id'</script>";
			}
		}
	}
}


// Check for errors and display them if any
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<div class='alert alert-danger'>$error</div>";
    }
}

if(isset($_GET['filter_set'])) {
	$_SESSION['filter_promocode']=mb_substr(trim($_GET['filter_promocode']),0,32);
	$_SESSION['filter_name']=mb_substr(trim($_GET['filter_name']),0,32);
	$_SESSION['filter_product']=mb_substr(trim($_GET['filter_product']),0,32);
}

$filter_where="1";
if(!empty($_SESSION['filter_promocode']))
	$filter_where.=" AND promocode LIKE '%{$_SESSION['filter_promocode']}%'";
if(!empty($_SESSION['filter_name']))
	$filter_where.=" AND (name LIKE '%{$_SESSION['filter_name']}%' OR surname LIKE '%{$_SESSION['filter_name']}%')";
if(!empty($_SESSION['filter_product']))
	$filter_where.=" AND descr LIKE '%{$_SESSION['filter_product']}%'";

?>
<div class="container mt-0">
    <h2 class='' >Управление промокодами</h2>

    <!-- Button to Open Modal for Inserting a New Record -->
    <button type="button" class="btn btn-primary mb-4" data-toggle="modal" data-target="#promocodeModal">
        <i class="fa fa-plus"></i> Добавить промокод
    </button>

    <!-- Fetch Records from Database -->
    <?php
    // Fetch records from the table
$records_per_page = 50;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

// Get the total number of records for pagination
$total_query = "SELECT COUNT(*) FROM promocodes
    JOIN cards ON cards.uid = promocodes.uid
    JOIN product ON product.id = product_id
    WHERE $filter_where AND tm2 > " . time();

$total_res = $db->query($total_query);
$total_row = $db->fetch_assoc($total_res);
$total_records = $total_row['COUNT(*)'];
$total_pages = ceil($total_records / $records_per_page);

$query = "SELECT
    promocodes.id AS id,
    promocodes.promocode,
    promocodes.fee_1,
    promocodes.fee_2,
    promocodes.uid,
    promocodes.tm2,
    promocodes.product_id,
    promocodes.price,
    promocodes.cnt,
    promocodes.discount,
    ANY_VALUE(product.descr) AS descr,
    ANY_VALUE(cards.name) AS name,       -- Добавлено
    ANY_VALUE(cards.surname) AS surname  -- Добавлено
FROM promocodes
JOIN cards ON cards.uid = promocodes.uid
JOIN product ON product.id = promocodes.product_id
WHERE $filter_where 
    AND promocodes.tm2 > " . time() . " 
    AND product.del=0 
    AND cards.del=0
GROUP BY promocodes.id
ORDER BY promocodes.promocode
LIMIT $offset, $records_per_page";

$res = $db->query($query);

    ?>
    <div class='my-4 card p-3' >
<style>
    .form-inline {
        display: flex;
       : flex-start;
        align-items: flex-start;
    }
    .form-group {
        text-align: center;
        margin-right: 15px;
        flex: 1;
        position: relative; /* Needed for positioning the icon */
    }
    .form-group label {
        display: block;
    }
    .clear-icon {
        position: absolute;
        right: 10px; /* Position it inside the input field */
        top: 50%;
        transform: translateY(-50%); /* Center it vertically */
        cursor: pointer; /* Change cursor to pointer for clickable effect */
    }
</style>

<form method="GET" class="form-inline" id="filterForm">
    <div class="form-group">
        <label for="filter_promocode" class="d-block text-center">Promocode:</label>
        <input type="text" id="filter_promocode" name="filter_promocode" value='<?=$_SESSION['filter_promocode']?>' class="form-control" placeholder="Промокод">
        <i class="fa fa-times clear-icon" onclick="clearField('filter_promocode');"></i>
    </div>
    <div class="form-group">
        <label for="filter_name" class="d-block text-center">Name:</label>
        <input type="text" id="filter_name" name="filter_name" value='<?=$_SESSION['filter_name']?>' class="form-control" placeholder="Имя">
        <i class="fa fa-times clear-icon" onclick="clearField('filter_name');"></i>
    </div>
    <div class="form-group">
        <label for="filter_product" class="d-block text-center">Product:</label>
        <input type="text" id="filter_product" name="filter_product" value='<?=$_SESSION['filter_product']?>' class="form-control" placeholder="Продукт">
        <i class="fa fa-times clear-icon" onclick="clearField('filter_product');"></i>
    </div>
    <input type='hidden' name='filter_set' value='yes'>
    <div class="form-group">
    <button type="submit" class="btn btn-primary" >Фильтр</button>
    </div>
</form>

<script>
    function clearField(fieldId) {
        document.getElementById(fieldId).value = ''; // Clear the input field
        document.getElementById('filterForm').submit(); // Submit the form
    }
</script>

	</div>


    <nav>
        <ul class="pagination">
            <?php if ($current_page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>"><i class="fa fa-chevron-left"></i></a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo ($current_page == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>"><i class="fa fa-chevron-right"></i></a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

	<table class="table table-bordered ">
		<thead class="thead-light  sticky-top">
			<tr>
				<th>ПРОМОКОД</th>
				<th>Владелец промокода</th>
				<th>На продукт</th>
				<th>На скидку</th>
				<th>На цену</th>
				<th>По дату</th>
				<th title='Ограничение сколько раз возможно использовать этот промокод'>Кол-во раз 
					<i class="fa fa-info-circle" style="cursor: pointer;"></i>
				</th>
				<th>Вознагр.1</th>
				<th>Вознагр.2</th>
				<th> </th>
			</tr>
		</thead>
		<tbody>
				<? while ($r = $db->fetch_assoc($res)) {
				if($r['discount'])
					$discount=$r['discount'].($r['discount']<100 ? "%" : "&nbsp;р.");
				else
					$discount="";
				if($r['price'])
					$price=$r['price']."&nbsp;р.";
				else
					$price="";
				if($r['fee_1'])
					$fee_1=$r['fee_1'].($r['fee_1']<100 ? "%" : "&nbsp;р.");
				else
					$fee_1="";
				if($r['fee_2'])
					$fee_2=$r['fee_2'].($r['fee_2']<100 ? "%" : "&nbsp;р.");
				else
					$fee_2="";
				$bg=($r['id']==$_GET['id']) ? "bg-yellow" : "";
				?>
					<tr id='<?=$r['id']?>' class='<?=$bg?>' >
						<td><?= htmlspecialchars($r['promocode']) ?></td>
						<td><?= htmlspecialchars($r['name']." ".$r['surname']).
							"&nbsp;
							<a href='../msg.php?uid={$r['uid']}' class='' target='_blank'>
								<i class='fa fa-external-link'></i>
							</a>" ?>
						</td>
						<td><?= htmlspecialchars($r['descr']) ?></td>
						<td><?= ($discount) ?></td>
						<td><?= ($price) ?></td>
						<td><?= (date("d.m.Y, H:i",$r['tm2'])) ?></td>
						<td><?= ($r['cnt']==-1 ? "без огр." : $r['cnt']) ?></td>
						<td><?= ($fee_1) ?></td>
						<td><?= ($fee_2) ?></td>
						<td>
							<a href='#' type="button" class="btn btn-link" 
									data-toggle="modal" 
									data-target="#promocodeModal" 
									data-id="<?= htmlspecialchars($r['id']) ?>"
									<?
										$dateTime = new DateTime();
										$dateTime->setTimestamp($r['tm2']);
										$tm2 = $dateTime->format('Y-m-d\TH:i');
									?>
									data-tm2="<?=$tm2?>" 
									data-uid="<?= htmlspecialchars($r['uid']) ?>" 
									data-client="<?= htmlspecialchars(trim($r['name']." ".$r['surname'])) ?>" 
									data-product_id="<?= htmlspecialchars($r['product_id']) ?>" 
									data-product="<?= htmlspecialchars($r['descr']) ?>" 
									data-discount="<?= htmlspecialchars($r['discount']) ?>" 
									data-price="<?= htmlspecialchars($r['price']) ?>" 
									data-promocode="<?= htmlspecialchars($r['promocode']) ?>" 
									data-fee_1="<?= htmlspecialchars($r['fee_1']) ?>" 
									data-fee_2="<?= htmlspecialchars($r['fee_2']) ?>" 
									data-cnt="<?= htmlspecialchars($r['cnt']<0 ? 0 : $r['cnt']) ?>" >
								<i class="fa fa-edit"></i>
							</a>
							
							<form method="POST" style="d-inline ml-2">
								<input type="hidden" name="id" value="<?= $r['id'] ?>">
								<button type="submit" name="del" class="btn btn-link" onclick="return confirm('Подтвердите удаление промокода: <?=htmlspecialchars($r['promocode'])?>');">
									<i class="fa fa-trash"></i>
								</button>
							</form>
						</td>
					</tr>
				<? } ?>
		</tbody>
	</table>
</div>

<!-- Modal for Inserting and Editing Promocode -->
<div class="modal fade" id="promocodeModal" tabindex="-1" role="dialog" aria-labelledby="promocodeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="promocodeModalLabel">Добавить/Изменить промокод</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="promocodeForm">
                    <input type="hidden" name="id" id="recordId">
                    <div class="form-group">
                        <label for="promocode">Промокод</label>
                        <input type="text" class="form-control" id="promocode" name="promocode" required>
                    </div>
                    <div class="form-group">
						<label for="clientSearch">Владелец промокода</label>
						<input type="text" class="form-control" id="clientSearch" placeholder="Type client name..." autocomplete="off">
						<input type="hidden" id="uid" name="uid" required>
						<div id="clientList" class="list-group"></div>
                    </div>
                    <div class="form-group">
                        <label for="productSearch">Для продукта</label>
						<input type="text" class="form-control" id="productSearch" placeholder="Type product name..." autocomplete="off">
                        <input type="hidden" class="form-control" id="product_id" name="product_id" required>
						<div id="productList" class="list-group"></div>
                    </div>
                    <div class="form-group">
                        <label for="discount">На скидку</label>
                        <input type="number" class="form-control" id="discount" name="discount" required>
                    </div>
                    <div class="form-group">
                        <label for="price">На цену</label>
                        <input type="number" class="form-control" id="price" name="price" required>
                    </div>
                    <div class="form-group">
                        <label for="tm2">Действует по (дд.мм.гггг чч:мм)</label>
						<input type="datetime-local" class="form-control" id="tm2" name="tm2" required>
                    </div>
                    <div class="form-group">
                        <label for="cnt">Осталось раз использования (0-без огр)</label>
                        <input type="number" class="form-control" id="cnt" name="cnt">
                    </div>
                    <div class="form-group">
                        <label for="fee_1">Вознаграждение уровень 1</label>
                        <input type="number" step="0.01" class="form-control" id="fee_1" name="fee_1">
                    </div>
                    <div class="form-group">
                        <label for="fee_2">Вознаграждение уровень 2</label>
                        <input type="number" step="0.01" class="form-control" id="fee_2" name="fee_2">
                    </div>
                    <button type="submit" name="add" value='yes' id="submitBtn" class="btn btn-primary">Add Promocode</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript to handle modal population
    $('#promocodeModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var modal = $(this);
        
        // Check if we are editing an existing record
        if (button.data('id')) {
            modal.find('.modal-title').text('Edit Promocode');
            modal.find('#recordId').val(button.data('id'));
            console.log(button.data('client'));
            modal.find('#tm2').val(button.data('tm2'));
            modal.find('#uid').val(button.data('uid'));
            modal.find('#clientSearch').val(button.data('client'));
            modal.find('#product_id').val(button.data('product_id'));
            modal.find('#productSearch').val(button.data('product'));
            modal.find('#discount').val(button.data('discount'));
            modal.find('#price').val(button.data('price'));
            modal.find('#promocode').val(button.data('promocode'));
            modal.find('#fee_1').val(button.data('fee_1'));
            modal.find('#fee_2').val(button.data('fee_2'));
            modal.find('#cnt').val(button.data('cnt'));

            // Change the button text for editing
            $('#submitBtn').text('Update Promocode');
        } else {
            // Case when we are adding a new record
            modal.find('.modal-title').text('Add Promocode');
            modal.find('input').val(''); // Clear all fields
            $('#submitBtn').text('Add Promocode');
            $('#recordId').val(''); // Clear the ID field
        }
    });
</script>
<script>
    $(document).ready(function(){
        $('#clientSearch').keyup(function(){
            let query = $(this).val();
            if (query.length > 0) {
                $.ajax({
                    url: 'promocodes.php', // Replace with the actual URL to this PHP file
                    method: 'GET',
                    data: { query_clients: query }, // Change search to query for clarity
                    success: function(data) {
                        let clients = JSON.parse(data);
                        $('#clientList').empty();
                        clients.forEach(function(client) {
                            $('#clientList').append('<a href="#" class="list-group-item" onclick="selectClient(' + client.uid + ', \'' + client.name + '\')">' + client.name + ' '+client.surname + '</a>');
                        });
                    }
                });
            } else {
                $('#clientList').empty(); // Clear the list if no input
            }
        });
        $('#productSearch').keyup(function(){
            let query = $(this).val();
            if (query.length > 0) {
                $.ajax({
                    url: 'promocodes.php', // Replace with the actual URL to this PHP file
                    method: 'GET',
                    data: { query_products: query }, // Change search to query for clarity
                    success: function(data) {
                        let product = JSON.parse(data);
                        $('#productList').empty();
                        product.forEach(function(product) {
                            $('#productList').append('<a href="#" class="list-group-item" onclick="selectProduct(' + product.id + ', \'' + product.descr + '\')">' + product.descr + '</a>');
                        });
                    }
                });
            } else {
                $('#productList').empty(); // Clear the list if no input
            }
        });
    });

    function selectClient(uid, name) {
        $("#uid").val(uid); // Set User ID
        $("#clientSearch").val(name); // Set the client name in the search box
        $("#clientList").empty(); // Clear suggestions
    }
    function selectProduct(id, name) {
		console.log('id='+id);
        $("#product_id").val(id); // Set User ID
        $("#productSearch").val(name); // Set the client name in the search box
        $("#productList").empty(); // Clear suggestions
    }
</script>

<script>
	document.getElementById('price').addEventListener('input', function() {
		const price = parseFloat(this.value); // Get the value of the price and convert it to a float
		const discountInput = document.getElementById('discount');

		if (price !== 0) {
			discountInput.value = 0; // Set discount to 0 if price is not zero
		}
	});
	document.getElementById('discount').addEventListener('input', function() {
		const discount = parseFloat(this.value); // Get the value of the price and convert it to a float
		const priceInput = document.getElementById('price');

		if (discount !== 0) {
			priceInput.value = 0; // Set discount to 0 if price is not zero
		}
	});
</script>


<?
$db->bottom();
?>
