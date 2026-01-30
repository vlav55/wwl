<?php
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "../init.inc.php";
$db = new top($database, 'ma_acc', false);
?>

<div class="container">
    <?php
    $highlight_id = null;
    $error_message = '';
    $redirectto = '';

    if(isset($_GET['filter']))
        $_SESSION['ma_filter_acc'] = mb_substr($_GET['filter'], 0, 64);
    if (!isset($_SESSION['ma_filter_acc']))
        $_SESSION['ma_filter_acc'] = "";

    // Handle adding an account
    if (isset($_POST['add_account'])) {
        $acc_name = $_POST['account'];

        // Check for duplicates
        $duplicate_res = $db->query("SELECT COUNT(*) as count FROM ma_acc WHERE acc_name = '$acc_name' AND del = 0");
        $duplicate_row = $db->fetch_assoc($duplicate_res);
        
        if ($duplicate_row['count'] == 0) {
            // Insert new account
            $db->query("INSERT INTO ma_acc (acc_name) VALUES ('$acc_name')");
            $highlight_id = $db->insert_id(); 
            
            // Determine the correct page for the new account
            $accounts_before = $db->query("SELECT COUNT(*) as count FROM ma_acc WHERE acc_name < '$acc_name' AND del = 0")->fetch_assoc()['count'];
            $limit = 5; 
            $page = ceil(($accounts_before + 1) / $limit); 
            $redirectto = "?page=$page&highlight_id=$highlight_id"; 
        } else {
            $error_message = "Account name \"$acc_name\" already exists.";
        }
    }

    // Handle editing an account
    if (isset($_POST['edit_account'])) {
        $acc_name = $_POST['account'];
        $id = intval($_POST['id']); 

        // Check for duplicates
        $duplicate_res = $db->query("SELECT COUNT(*) as count FROM ma_acc WHERE acc_name = '$acc_name' AND id != '$id' AND del = 0");
        $duplicate_row = $db->fetch_assoc($duplicate_res);

        if ($duplicate_row['count'] == 0) {
            $db->query("UPDATE ma_acc SET acc_name='$acc_name' WHERE id='$id'");
            $highlight_id = $id; 
            // Determine the correct page for the edited account
            $accounts_before = $db->query("SELECT COUNT(*) as count FROM ma_acc WHERE acc_name < '$acc_name' AND del = 0")->fetch_assoc()['count'];
            $limit = 5; 
            $page = ceil(($accounts_before + 1) / $limit); 
            $redirectto = "?page=$page&highlight_id=$highlight_id"; 
        } else {
            $error_message = "Account name \"$acc_name\" already exists.";
        }
    }

    // Handle deleting an account
    if (isset($_GET['del_id'])) {
        $id = intval($_GET['del_id']); 
        $db->query("UPDATE ma_acc SET del=1 WHERE id='$id'");
    }

    // Pagination
    $limit = 5; 
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $highlight_id = isset($_GET['highlight_id']) ? intval($_GET['highlight_id']) : $highlight_id; 
    $offset = ($page - 1) * $limit;

    // Get total accounts count
    $total_count_res = $db->query("SELECT COUNT(*) as total FROM ma_acc WHERE del=0 AND acc_name LIKE '%".$_SESSION['ma_filter_acc']."%'");
    $total_count_row = $db->fetch_assoc($total_count_res);
    $total_accounts = $total_count_row['total'];
    $total_pages = ceil($total_accounts / $limit);

    // Fetch accounts sorted by account name
    $accounts_res = $db->query("SELECT * FROM ma_acc
        WHERE del=0 AND acc_name LIKE '%".$_SESSION['ma_filter_acc']."%'
        ORDER BY acc_name ASC LIMIT $limit OFFSET $offset");
    ?>

    <?php if ($error_message): ?>
        <div class="alert alert-warning"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <?php if ($redirectto): ?>
        <script>
            setTimeout(function() {
                window.location.href = '<?= $redirectto ?>';
            }, 2000); 
        </script>
        <div class="alert alert-success">Account added or changed successfully. You will be redirected shortly...</div>
    <?php endif; ?>

	<style>
		.position-relative {
			position: relative;
		}

		.clear-icon {
			position: absolute;
			right: 10px; /* Adjust this value to position the icon */
			top: 50%;
			transform: translateY(-50%);
			cursor: pointer;
		}

		.clear-icon i {
			font-size: 18px; /* Adjust this value as needed */
			color: #aaa; /* Optionally change the color */
		}

		.clear-icon i:hover {
			color: #000; /* Change the color on hover */
		}
	</style>
    <div class="my-0 py-0">
        <div class="d-flex align-items-center mb-2">
            <a href="#" class="btn btn-primary mr-2" data-toggle="modal" data-target="#addAccountModal">Add</a>
            
            <form class="form-inline">
                <div class="form-group my-0 position-relative">
                    <label for="filter" class="sr-only">Filter</label>
                    <input type="text" class="form-control" id="filter" name="filter" placeholder="Enter filter" value="<?=$_SESSION['ma_filter_acc']?>">
                    <span class="clear-icon" onclick="clearInput()"><i class="fa fa-times-circle"></i></span>
                </div>
                <button type="submit" class="btn btn-info btn-sm ml-1 mb-0">Go</button>
            </form>
        </div>
    </div>

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Account Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $n=$offset; while ($account = $db->fetch_assoc($accounts_res)): ?>
                <tr id="account-<?= $account['id'] ?>" style="<?= ($account['id'] == $highlight_id) ? 'background-color: yellow;' : '' ?>">
                    <td title='<?= $n++ ?>' width='5%'> <?= $n ?> </td>
                    <td title='<?= $account['id'] ?>'><?= htmlspecialchars($account['acc_name']) ?></td>
                    <td width='5%'>
                        <a href="#" class="btn btn-light btn-sm" data-toggle="modal" data-target="#editAccountModal" onclick="populateEditModal(<?= $account['id'] ?>, '<?= htmlspecialchars($account['acc_name']) ?>')">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a href="#" class="btn btn-light btn-sm" onclick="confirmDelete(<?= $account['id'] ?>)">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>

    <!-- Add Account Modal -->
    <div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAccountModalLabel">Add Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="account">Account Name</label>
                            <input type="text" class="form-control" id="account" name="account" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add_account">Add Account</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Account Modal -->
    <div class="modal fade" id="editAccountModal" tabindex="-1" aria-labelledby="editAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAccountModalLabel">Edit Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_account_id" name="id">
                        <div class="form-group">
                            <label for="edit_account">Account Name</label>
                            <input type="text" class="form-control" id="edit_account" name="account" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="edit_account">Update Account</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function populateEditModal(id, accountName) {
    document.getElementById('edit_account_id').value = id;
    document.getElementById('edit_account').value = accountName;
}

function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this account?")) {
        window.location.href = "?del_id=" + id;
    }
}

function clearInput() {
    document.getElementById('filter').value = ''; // Clear the input field
    // Optional: You can submit the form as well after clearing
    // document.forms[0].submit(); 
}
</script>

<?php
$db->bottom();
?>
