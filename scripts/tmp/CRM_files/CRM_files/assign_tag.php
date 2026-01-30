<?php
$conn = new mysqli('localhost', 'root', 'qwerty123', 'crm_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$uid = isset($_POST['uid']) ? $_POST['uid'] : null;
$tag_id = isset($_POST['tag_id']) ? $_POST['tag_id'] : null;

if ($uid && $tag_id) {
    $checkSQL = "SELECT * FROM tags_op WHERE uid = ? AND tag_id = ?";
    $checkStmt = $conn->prepare($checkSQL);
    $checkStmt->bind_param("ii", $uid, $tag_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if($result->num_rows == 0) {
        $sql = "INSERT INTO tags_op (uid, tag_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $uid, $tag_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => 'Tag added successfully']);
        } else {
            echo json_encode(['error' => 'Failed to add tag']);
        }
    } else {
        echo json_encode(['error' => 'Tag is already assigned to this user']);
    }
} else {
    echo json_encode(['error' => 'Invalid UID or Tag ID']);
}

$conn->close();
?>
