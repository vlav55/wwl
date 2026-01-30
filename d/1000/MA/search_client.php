<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "../init.inc.php";
$db = new db($database);
?>

<?php
// Assuming $db is already defined and connects to the database

if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $res = $db->query("SELECT id, client FROM ma_clients WHERE client LIKE '%$query%' AND del = 0");
    
    if ($res->num_rows > 0) {
        while ($r = $res->fetch_assoc()) {
            echo "<a href='#' class='list-group-item list-group-item-action client-item' data-id='{$r['id']}'>{$r['client']}</a>";
        }
    } else {
        echo "<div class='list-group-item'>No results found</div>";
    }
} else {
    echo "<div class='list-group-item'>Invalid request</div>";
}
?>
