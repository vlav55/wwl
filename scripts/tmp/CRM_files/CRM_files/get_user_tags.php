<?php
$conn = new mysqli('localhost', 'root', 'qwerty123', 'crm_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$uid = isset($_GET['uid']) ? $_GET['uid'] : null;
if ($uid) {
    $sql = "SELECT tags.id, tags.tag_name, tags.tag_color FROM tags 
            JOIN tags_op ON tags.id = tags_op.tag_id
            WHERE tags_op.uid = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    $tags = [];
    while($row = $result->fetch_assoc()) {
        $tags[] = $row;
    }
    echo json_encode($tags);
} else {
    echo json_encode(['error' => 'Invalid UID']);
}

$conn->close();
?>