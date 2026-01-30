<?php
$conn = new mysqli('localhost', 'root', 'qwerty123', 'crm_db');

$id = $_POST['id'];

if (isset($id)) {
    $query = "UPDATE tags SET del = 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $stmt->close();
        
        $query_op = "DELETE FROM tags_op WHERE tag_id = ?";
        $stmt_op = $conn->prepare($query_op);
        $stmt_op->bind_param("i", $id);
        
        if ($stmt_op->execute()) {
            echo "Tag and its references deleted successfully";
        } else {
            echo "Error deleting tag references from tags_op table: " . $stmt_op->error;
        }
        
        $stmt_op->close();
    } else {
        echo "Error deleting tag: " . $stmt->error;
    }
    
} else {
    echo "No ID provided";
}

$conn->close();
?>
