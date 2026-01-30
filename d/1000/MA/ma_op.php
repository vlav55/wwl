<?php
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "../init.inc.php";
$db = new top($database, 'ma_op', false);

include "ma_buttons.inc.php";

if(isset($_GET['del'])) {
	$id=intval($_GET['id']);
	if($tm=$db->dlookup("tm","ma_op","id='$id'")) {
		$id1=$db->fetch_assoc($db->query("SELECT id FROM ma_op WHERE del=0 AND tm<'$tm' ORDER BY tm DESC",0))['id'];
		$db->query("UPDATE ma_op SET del=1 WHERE id='$id'");
		if($id1)
			print "<script>location='?highlight_id=$id1'</script>";
	}
}

// Get the current page number from the URL; default is 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rows_per_page = 20; // Number of rows per page
$offset = ($page - 1) * $rows_per_page; // Calculate the offset for the query

// Get highlight ID if present
$highlight_id = isset($_GET['highlight_id']) ? (int)$_GET['highlight_id'] : null;

// Fetch total number of rows (for pagination)
$total_count_query = "SELECT COUNT(*) as total FROM ma_op WHERE del = 0";
$total_count_result = $db->query($total_count_query);
$total_count = $total_count_result->fetch_assoc()['total'];
$total_pages = ceil($total_count / $rows_per_page);

// Display the table here with JOINs to fetch corresponding names
$sql = "
SELECT 
    op.id, 
    op.tm,
    c.client AS client_name, 
    cat.cat_name AS category_name, 
    a.acc_name AS account_name, 
    op.credit, 
    op.debit, 
    op.comm 
FROM 
    ma_op op
LEFT JOIN 
    ma_clients c ON op.client_id = c.id AND c.del = 0
LEFT JOIN 
    ma_cat cat ON op.cat_id = cat.id AND cat.del = 0
LEFT JOIN 
    ma_acc a ON op.acc_id = a.id AND a.del = 0
WHERE 
    op.del = 0
ORDER BY 
    op.tm DESC  -- Order by the 'tm' column in ascending order
LIMIT $offset, $rows_per_page"; // Pagination limit
$result = $db->query($sql);

// Table and highlighting
if ($result && $result->num_rows > 0) {
    echo '<table class="table table-striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>#</th>';
    echo '<th>Date</th>';
    echo '<th>Client</th>';
    echo '<th>Category</th>';
    echo '<th>Account</th>';
    echo '<th>Credit</th>';
    echo '<th>Debit</th>';
    echo '<th>Comment</th>';
    echo '<th> </th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Fetch all rows and display them
    $n=$offset+1;
    while ($row = $result->fetch_assoc()) {
        // Highlight the row if it matches the highlight_id
        $highlight_class = ($highlight_id && $highlight_id == $row['id']) ? 'table-info' : '';
        
        echo '<tr class="' . $highlight_class . '">';
        echo '<td>' . htmlspecialchars($n++) . '</td>';
        // Format the 'tm' field as date dd.mm.YYYY
        $formatted_date = date('d.m.Y', $row['tm']);
        echo '<td>' . htmlspecialchars($formatted_date) . '</td>';
        echo '<td>' . htmlspecialchars($row['client_name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['category_name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['account_name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['credit']) . '</td>';
        echo '<td>' . htmlspecialchars($row['debit']) . '</td>';
        echo '<td>' . nl2br(htmlspecialchars($row['comm'])) . '</td>';
        
        // Actions column with Awesome icons
        echo '<td>';
        echo '<button type="button" class="btn btn-link text-info" onclick="editEntry(' . htmlspecialchars($row['id']) . ')"><i class="fa fa-edit"></i> Edit</button>&nbsp;';
        echo '<button type="button" class="btn btn-link text-info" onclick="dupEntry(' . htmlspecialchars($row['id']) . ')"><i class="fa fa-copy"></i> Dup</button>&nbsp;';
        echo ' <button type="button" class="btn btn-link text-info" onclick="deleteEntry(' . htmlspecialchars($row['id']) . ')"><i class="fa fa-trash"></i> Del</button>&nbsp;';
        echo '</td>';
        
        echo '</tr>';
    }

    $sum_credit=$db->fetch_row($db->query("SELECT SUM(credit) FROM ma_op WHERE del=0"))[0];
    $sum_debit=$db->fetch_row($db->query("SELECT SUM(debit) FROM ma_op WHERE del=0"))[0];
    print "<tr class='font-weight-bold text-black bg-info' ><td colspan='5'></td><td>$sum_credit</td><td>$sum_debit</td><td>".$sum_credit-$sum_debit."</td colspan='3'><td></td></tr>";

    echo '</tbody>';
    echo '</table>';
} else {
    echo '<p>No records found.</p>';
}

// Pagination controls
echo '<nav aria-label="Page navigation">';
echo '<ul class="pagination justify-content-center">';
for ($i = 1; $i <= $total_pages; $i++) {
    $active_class = ($i == $page) ? 'active' : '';
    echo '<li class="page-item ' . $active_class . '"><a class="page-link" href="?page=' . $i . ($highlight_id ? '&highlight_id=' . $highlight_id : '') . '">' . $i . '</a></li>';
}
echo '</ul>';
echo '</nav>';
?>


<script>
function editEntry(id) {
    const modal = new bootstrap.Modal(document.getElementById('ma_op_addModal'));
    document.getElementById('modalFrame').src = 'ma_op_add.php?id=' + id;
    modal.show();
}

function dupEntry(id) {
    const modal = new bootstrap.Modal(document.getElementById('ma_op_addModal'));
    document.getElementById('modalFrame').src = 'ma_op_add.php?dup=yes&id=' + id;
    modal.show();
}

function deleteEntry(id) {
    if (confirm('Are you sure you want to delete this entry?')) {
        window.location.href = '?del=yes&id=' + id; // Redirect to a delete handler
    }
}
</script>

<?php
$db->bottom();
?>
