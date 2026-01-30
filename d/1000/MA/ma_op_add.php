<?php
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "../init.inc.php";

$db = new top($database, 'ma_op_add', false);

// Check if an `id` is passed in the GET parameter
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$existing_data = null;

if ($id) {
    if(isset($_GET['dup'])) {
		$r=$db->fetch_assoc($db->query("SELECT * FROM ma_op WHERE id=$id"));
		$db->query("INSERT INTO ma_op SET
				tm='".time()."',
				tm_to='".time()+($r['tm_to']-$r['tm'])."',
				cat_id={$r['cat_id']},
				acc_id={$r['acc_id']},
				client_id={$r['client_id']},
				credit=0,
				debit=0,
				comm='',
				user_id={$_SESSION['userid_sess']},
				del=0
				");
		$id=$db->insert_id();
		print "<p class='alert alert-success' >Запись сдублирована ($id)</p>";
	}
    // Retrieve existing data for the given ID
    $query = "SELECT * FROM ma_op WHERE id = $id";
    $result = $db->query($query);
    
    if ($result && $db->num_rows($result) > 0) {
        $existing_data = $db->fetch_assoc($result);
    } else {
        echo "No record found for the provided ID.";
    }
} else {
	if(!isset($_POST['tm']) && isset($_SESSION['ma_tm'])) {
		$existing_data['tm']=$_SESSION['ma_tm'];
		$existing_data['tm_to']=$_SESSION['ma_tm'];
	}
}

// Function to fetch clients
function get_clients($db, $selected_id = null) {
    $clients = '';
    $client_res = $db->query("SELECT id, client FROM ma_clients WHERE del = 0");
    while ($client = $client_res->fetch_assoc()) {
        $selected = $selected_id == $client['id'] ? 'selected' : '';
        $clients .= "<option value='{$client['id']}' $selected>{$client['client']}</option>";
    }
    return $clients;
}

// Function to fetch categories
function get_categories($db, $selected_id = null) {
    $categories = '';
    $cat_res = $db->query("SELECT id, cat_name FROM ma_cat WHERE del = 0");
    while ($cat = $cat_res->fetch_assoc()) {
        $selected = $selected_id == $cat['id'] ? 'selected' : '';
        $categories .= "<option value='{$cat['id']}' $selected>{$cat['cat_name']}</option>";
    }
    return $categories;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate POST parameters
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $tm = isset($_POST['tm']) ? trim($_POST['tm']) : null;
    $tm_to = isset($_POST['tm_to']) ? trim($_POST['tm_to']) : null;
    $cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : null;
    $client_id = isset($_POST['client_id']) ? intval($_POST['client_id']) : null;
    $acc_id = isset($_POST['acc_id']) ? intval($_POST['acc_id']) : null;
    $credit = isset($_POST['credit']) ? intval($_POST['credit']) : 0;
    $debit = isset($_POST['debit']) ? intval($_POST['debit']) : 0;
    $comm = isset($_POST['comm']) ? trim($_POST['comm']) : '';

    // Validate dates
    if (!$tm || !$tm_to) {
        echo "Both date fields are required.";
    } else {
        // Convert dates to Unix timestamps
        $tm_timestamp = strtotime($tm);
        $tm_to_timestamp = strtotime($tm_to);

        // Validate timestamps
        if ($tm_timestamp === false || $tm_to_timestamp === false) {
            echo "Invalid date format.";
        } else {
            // Ensure tm_to is greater than or equal to tm
            if ($tm_to_timestamp < $tm_timestamp) {
                echo "End date must be greater than or equal to start date.";
            } else {
                // Escape other inputs
                $comm = $db->escape($comm);
                
                if ($id) { // Update existing record
                    $update_query = "UPDATE ma_op SET tm = '$tm_timestamp', tm_to = '$tm_to_timestamp', cat_id = '$cat_id', client_id = '$client_id', acc_id = '$acc_id', credit = '$credit', debit = '$debit', comm = '$comm', user_id = '{$_SESSION['userid_sess']}' WHERE id = $id";
                    
                    if ($db->query($update_query)) {
						$new_id=$id;
                        $success = true;
                    } else {
                        echo "Error updating record: " . $db->error;
                    }
                } else { // Insert new record
                    $insert_query = "INSERT INTO ma_op (tm, tm_to, cat_id, client_id, acc_id, credit, debit, comm, user_id) VALUES ('$tm_timestamp', '$tm_to_timestamp', '$cat_id', '$client_id', '$acc_id', '$credit', '$debit', '$comm', '{$_SESSION['userid_sess']}')";
                    
                    if ($db->query($insert_query)) {
                        // Get the new record ID
                        $new_id = $db->insert_id();
                        $success = true;
                        $_SESSION['ma_tm']=$tm_timestamp;
                    } else {
                        echo "Error adding record: " . $db->error;
                    }
                }
                // Handle success message and redirect
				if (isset($success) && $success) {
					echo "<script>
						if (typeof window.parent.closeModal === 'function') {
							window.parent.closeModal(); // Close the modal
						} else {
							console.warn('closeModal function is not defined in the parent window.');
						}

						// Reload parent window with the highlight ID
						window.parent.location.href = window.parent.location.href.split('?')[0] + '?highlight_id=" . ($id ? $id : $new_id) . "';
						// Optionally, you can trigger a reload explicitly
						// window.parent.location.reload();
					</script>";
					exit;
				}
            }
        }
    }
}

?>

<div class="container">
    <form method="POST" action="?">
        <input type="hidden" name="id" value="<?php echo $id; ?>"> <!-- Hidden field for id -->
        <div class="form-group row mb-1">
            <div class="col-sm-6">
                <input type="date" class="form-control" id="tm" name="tm" value="<?php echo $existing_data ? date('Y-m-d', $existing_data['tm']) : ''; ?>" required>
            </div>
            <div class="col-sm-6">
                <input type="date" class="form-control" id="tm_to" name="tm_to" value="<?php echo $existing_data ? date('Y-m-d', $existing_data['tm_to']) : ''; ?>" required>
            </div>
        </div>
        <div class="form-group mb-1">
<!--
            <label for="cat_id">Category:</label>
            <select class="form-control" id="cat_id" name="cat_id" required>
                <option value="">Select Category</option>
                <?php echo get_categories($db, $existing_data ? $existing_data['cat_id'] : null); ?>
            </select>
-->
            <input type="text" class="form-control" id="cat_search" placeholder="Enter category name" autocomplete="off"
            value="<?php echo ($id) ? trim($db->dlookup("cat_name", "ma_cat", "id='{$existing_data['cat_id']}'",0)) : null; ?>"
            >
            <div id="cat_results" class="list-group mt-2" style="display: none;"></div>
            <input type="hidden" id="cat_id" name="cat_id" value="<?=$existing_data['cat_id']?>">
        </div>
        <div class="form-group mb-1">
<!--
            <label for="client_id">Client:</label>
            <select class="form-control" id="client_id" name="client_id" required>
                <option value="">Select Client</option>
                <?php echo get_clients($db, $existing_data ? $existing_data['client_id'] : null); ?>
            </select>
-->
            <input type="text" class="form-control" id="client_search" placeholder="Enter client name" autocomplete="off"
            value="<?php echo ($id) ? trim($db->dlookup("client", "ma_clients", "id='{$existing_data['client_id']}'",0)) : null; ?>"
            >
            <div id="client_results" class="list-group mt-2" style="display: none;"></div>
            <input type="hidden" id="client_id" name="client_id" value="<?=$existing_data['client_id']?>">
        </div>
        <div class="form-group row mb-1">
            <div class="col-sm-10">
                <select class="form-control" id="acc_id" name="acc_id" required>
                    <option value="">Select Account</option>
                    <?php
                    // Fetch accounts from the database
                    $acc_res = $db->query("SELECT id, acc_name FROM ma_acc WHERE del = 0");
                    while ($acc = $acc_res->fetch_assoc()) {
                        // Select the account if it matches the existing data
                        $selected = $existing_data && $existing_data['acc_id'] == $acc['id'] ? 'selected' : '';
                        echo "<option value='{$acc['id']}' $selected>{$acc['acc_name']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="form-group row mb-1">
            <label for="credit" class="col-sm-2 col-form-label">Credit:</label>
            <div class="col-sm-4">
                <input type="number" class="form-control" id="credit" name="credit" value="<?php echo $existing_data ? $existing_data['credit'] : ''; ?>" required>
            </div>
            <label for="debit" class="col-sm-2 col-form-label">Debit:</label>
            <div class="col-sm-4">
                <input type="number" class="form-control" id="debit" name="debit" value="<?php echo $existing_data ? $existing_data['debit'] : ''; ?>" required>
            </div>
        </div>
        <div class="form-group mb-1">
            <label for="comm">Comment:</label>
            <textarea class="form-control" id="comm" name="comm"><?php echo $existing_data ? htmlspecialchars($existing_data['comm']) : ''; ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Save Record</button>
    </form>
</div>

<script>
$(document).ready(function() {
    // Set date_to to the same value as date when date changes
    $('#tm').on('change', function() {
        var selectedDate = $(this).val();
        $('#tm_to').val(selectedDate);
    });

    // Reset the opposite field when one of them is changed
    $('#credit').on('input', function() {
        if ($(this).val() !== '') {
            $('#debit').val(0);
        }
    });

    $('#debit').on('input', function() {
        if ($(this).val() !== '') {
            $('#credit').val(0);
        }
    });
    
    // Category search logic
    $('#cat_search').on('input', function() {
        var query = $(this).val();
        if (query.length > 0) {
            $.ajax({
                url: 'search_category.php',
                method: 'POST',
                data: { query: query },
                success: function(data) {
                    $('#cat_results').html(data).show();
                }
            });
        } else {
            $('#cat_results').hide();
        }
    });

    $(document).on('click', '.category-item', function() {
        $('#cat_search').val($(this).text());
        $('#cat_id').val($(this).data('id'));
        $('#cat_results').hide();
    });
    
    // Client search logic
    $('#client_search').on('input', function() {
        var query = $(this).val();
        if (query.length > 0) {
            $.ajax({
                url: 'search_client.php',
                method: 'POST',
                data: { query: query },
                success: function(data) {
                    $('#client_results').html(data).show();
                }
            });
        } else {
            $('#client_results').hide();
        }
    });

    $(document).on('click', '.client-item', function() {
        $('#client_search').val($(this).text());
        $('#client_id').val($(this).data('id'));
        $('#client_results').hide();
    });

});
</script>

<?php
$db->bottom();
?>
