<?php
$servername = "localhost";
$username = "root";
$password = "qwerty123";
$dbname = "crm_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);

if(isset($data['tagName']) && isset($data['tagColor'])) {
    $tag_name = $data['tagName'];
    $tag_color = $data['tagColor'];
    
    $stmt = $conn->prepare("INSERT INTO tags (tag_name, tag_color) VALUES (?, ?)");
    $stmt->bind_param("ss", $tag_name, $tag_color);
    
    if ($stmt->execute()) {
        $id = $stmt->insert_id;
        echo json_encode(array("tagName" => $tag_name, "tagColor" => $tag_color, "id" => $id));
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
} else {
    echo "Error: Invalid input";
}

$conn->close();
?>