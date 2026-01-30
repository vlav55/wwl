<?php
$conn = new mysqli('localhost', 'root', 'qwerty123', 'crm_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$uid = isset($_POST['uid']) ? $_POST['uid'] : null;
$tag_id = isset($_POST['tag_id']) ? $_POST['tag_id'] : null;

if ($uid && $tag_id) {
    $sql = "DELETE FROM tags_op WHERE uid = ? AND tag_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $uid, $tag_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => 'Tag removed successfully']);
    } else {
        echo json_encode(['error' => 'Failed to remove tag']);
    }
} else {
    echo json_encode(['error' => 'Invalid UID or Tag ID']);
}

$conn->close();
?>