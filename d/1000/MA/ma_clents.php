<?
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "../init.inc.php";
$db = new top($database, 'ma_clients', false);
?>

<div class="container">
    <?
    // Variable to store the ID of the currently added or edited client
    $highlight_id = null;
    $error_message = ''; // Variable to store error messages
    $redirectto = ''; // Variable to store the redirect URL

    if(isset($_GET['filter']))
		$_SESSION['ma_filter']=mb_substr($_GET['filter'],0,64);
	if(!isset($_SESSION['ma_filter']))
		$_SESSION['ma_filter']="";

    // Handle adding a client
    if (isset($_POST['add_client'])) {
        $client_name = $_POST['client'];

        // Check for duplicates
        $duplicate_res = $db->query("SELECT COUNT(*) as count FROM ma_clients WHERE client = '$client_name' AND del = 0");
        $duplicate_row = $db->fetch_assoc($duplicate_res);
        
        if ($duplicate_row['count'] == 0) {
            // Insert new client
            $db->query("INSERT INTO ma_clients (client) VALUES ('$client_name')");
            $highlight_id = $db->insert_id(); // Get the last inserted ID using method
            
            // Determine the correct page for the new client
            $clients_before = $db->query("SELECT COUNT(*) as count FROM ma_clients WHERE client < '$client_name' AND del = 0")->fetch_assoc()['count'];
            $limit = 5; // Number of clients per page
            $page = ceil(($clients_before + 1) / $limit); // +1 to account for new client
            $redirectto = "?page=$page&highlight_id=$highlight_id"; // Set the redirect URL
        } else {
            $error_message = "Client name \"$client_name\" already exists.";
        }
    }

    // Handle editing a client
    if (isset($_POST['edit_client'])) {
        $client_name = $_POST['client'];
        $id = intval($_POST['id']); // Ensure ID is an integer

        // Check for duplicates
        $duplicate_res = $db->query("SELECT COUNT(*) as count FROM ma_clients WHERE client = '$client_name' AND id != '$id' AND del = 0");
        $duplicate_row = $db->fetch_assoc($duplicate_res);

        if ($duplicate_row['count'] == 0) {
            $db->query("UPDATE ma_clients SET client='$client_name' WHERE id='$id'");
            $highlight_id = $id; // ID of the edited client
            // Determine the correct page for the new client
            $clients_before = $db->query("SELECT COUNT(*) as count FROM ma_clients WHERE client < '$client_name' AND del = 0")->fetch_assoc()['count'];
            $limit = 5; // Number of clients per page
            $page = ceil(($clients_before + 1) / $limit); // +1 to account for new client
            $redirectto = "?page=$page&highlight_id=$highlight_id"; // Set the redirect URL
        } else {
            $error_message = "Client name \"$client_name\" already exists.";
        }
    }

    // Handle deleting a client
    if (isset($_GET['del_id'])) {
        $id = intval($_GET['del_id']); // Ensure ID is an integer
        $db->query("UPDATE ma_clients SET del=1 WHERE id='$id'");
    }

    // Pagination
    $limit = 5; // Number of rows to display per page
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Ensure page number is an integer
    $highlight_id = isset($_GET['highlight_id']) ? intval($_GET['highlight_id']) : $highlight_id; // Check for highlight_id
    $offset = ($page - 1) * $limit;

    // Get total clients count
    $total_count_res = $db->query("SELECT COUNT(*) as total FROM ma_clients WHERE del=0  AND client LIKE '%".$_SESSION['ma_filter']."%'");
    $total_count_row = $db->fetch_assoc($total_count_res);
    $total_clients = $total_count_row['total'];
    $total_pages = ceil($total_clients / $limit);

    // Fetch clients sorted by client name
    $clients_res = $db->query("SELECT * FROM ma_clients
		WHERE del=0 AND client LIKE '%".$_SESSION['ma_filter']."%'
		ORDER BY client ASC LIMIT $limit OFFSET $offset");
    ?>

    <h2>Clients List</h2>
    <? if ($error_message): ?>
        <div class="alert alert-warning"><?= htmlspecialchars($error_message) ?></div>
    <? endif; ?>

    <? if ($redirectto): ?>
        <script>
            // Using JavaScript to redirect after a brief delay to show messages
            setTimeout(function() {
                window.location.href = '<?= $redirectto ?>';
            }, 2000); // Redirect after 2 seconds
        </script>
        <div class="alert alert-success">Client added or changed successfully. You will be redirected shortly...</div>
    <? endif; ?>

	<div class="mt-5">
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
		<form class="form-inline" method="GET">
			<div class="form-group mb-2 position-relative">
				<label for="filter" class="sr-only">Filter</label>
				<input type="text" class="form-control" id="filter" name="filter" placeholder="Enter filter" value="<?=$_SESSION['ma_filter']?>">
				<span class="clear-icon" onclick="clearInput()"><i class="fa fa-times-circle"></i></span>
			</div>
			<button type="submit" class="btn btn-primary mb-2">Go</button>
		</form>
	</div>


    <a href="#" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addClientModal">Add Client</a>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Client Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <? while ($client = $db->fetch_assoc($clients_res)): ?>
                <tr id="client-<?= $client['id'] ?>" style="<?= ($client['id'] == $highlight_id) ? 'background-color: yellow;' : '' ?>">
                    <td><?= $client['id'] ?></td>
                    <td><?= htmlspecialchars($client['client']) ?></td>
                    <td>
                        <a href="#" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editClientModal" onclick="populateEditModal(<?= $client['id'] ?>, '<?= htmlspecialchars($client['client']) ?>')">
                            <i class="fa fa-pencil"></i> Edit
                        </a>
                        <a href="#" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $client['id'] ?>)">
                            <i class="fa fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
            <? endwhile; ?>
        </tbody>
    </table>

    <nav>
        <ul class="pagination">
            <? for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <? endfor; ?>
        </ul>
    </nav>

    <!-- Add Client Modal -->
    <div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addClientModalLabel">Add Client</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="client">Client Name</label>
                            <input type="text" class="form-control" id="client" name="client" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add_client">Add Client</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Client Modal -->
    <div class="modal fade" id="editClientModal" tabindex="-1" aria-labelledby="editClientModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editClientModalLabel">Edit Client</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_client_id" name="id">
                        <div class="form-group">
                            <label for="edit_client">Client Name</label>
                            <input type="text" class="form-control" id="edit_client" name="client" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="edit_client">Update Client</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function populateEditModal(id, clientName) {
    document.getElementById('edit_client_id').value = id;
    document.getElementById('edit_client').value = clientName;
}

function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this client?")) {
        window.location.href = "?del_id=" + id;
    }
}
function clearInput() {
	document.getElementById('filter').value = ''; // Clear the input field
	// Optional: You can submit the form as well after clearing
	// document.forms[0].submit(); 
}
</script>

<?
$db->bottom();
?>
