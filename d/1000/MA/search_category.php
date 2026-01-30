<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "../init.inc.php";
$db = new db($database);
?>

<?php
// Assuming $db is already defined and connects to the database

if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $res = $db->query("SELECT id, cat_name FROM ma_cat WHERE cat_name LIKE '%$query%' AND del = 0");
    
    if ($res->num_rows > 0) {
        while ($r = $res->fetch_assoc()) {
            echo "<a href='#' class='list-group-item list-group-item-action category-item' data-id='{$r['id']}'>{$r['cat_name']}</a>";
        }
    } else {
        echo "<div class='list-group-item'>No results found</div>";
    }
} else {
    echo "<div class='list-group-item'>Invalid request</div>";
}
?>
