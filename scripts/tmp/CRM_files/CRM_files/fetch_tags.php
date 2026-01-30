<?php
$conn = new mysqli('localhost', 'root', 'qwerty123', 'crm_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, tag_name, tag_color FROM tags WHERE del = 0 OR del IS NULL";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $tags = [];
    while($row = $result->fetch_assoc()) {
        $tags[] = $row;
    }
    echo json_encode($tags);
} else {
    echo json_encode([]);
}

$conn->close();
?>
