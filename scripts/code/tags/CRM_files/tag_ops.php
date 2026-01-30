<?php
$servername = "localhost";
$username = "root";
$password = "qwerty123";
$dbname = "crm_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

switch($action) {
    case 'addTagToUser':
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
        break;

    case 'checkTagReference':
        $id = $_POST['id'];

        // First check if the tag is associated with any card
        $query_check = "SELECT COUNT(*) FROM tags_op WHERE tag_id = ?";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bind_param("i", $id);
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($count > 0) {
            echo json_encode(["status" => "warning", "message" => "Если вы удалите этот тэг, он также будет удален со всех карт."]);
            exit();
        }
        else {
            echo json_encode(["message" => "There are no references of this tag"]);
        }

    case 'createTag':
        $data = $_POST;

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
        break;

    case 'deleteTag':
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
                    echo json_encode(['success' => 'Tag and references deleted successfully']);
                } else {
                    echo json_encode(['error' => 'Error deleting tag references from tags_op table']);
                }
                
                $stmt_op->close();
            } else {
                echo json_encode(['error' => 'Error deleting tag']);
            }
            
        } else {
            echo "No ID provided";
        }
        break;

    case 'updateTag':
        $data = $_POST;

        if(isset($data['tagName']) && isset($data['tagColor']) && isset($data['tagId'])) {
            $tag_name = $data['tagName'];
            $tag_color = $data['tagColor'];
            $tag_id = $data['tagId'];
            
            $stmt = $conn->prepare("UPDATE tags SET tag_name = ?, tag_color = ? WHERE id = ?");
            $stmt->bind_param("ssi", $tag_name, $tag_color, $tag_id);
            
            if ($stmt->execute()) {
                echo json_encode(array("tagName" => $tag_name, "tagColor" => $tag_color, "id" => $tag_id));
            } else {
                echo "Error: " . $stmt->error;
            }
            
            $stmt->close();
        } else {
            echo "Error: Invalid input";
        }
        break;

    case 'fetchTags':
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
        break;

    case 'fetchUserTags':
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
        break;

    case 'removeTagFromUser':
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
        break;

    default:
        echo json_encode(['error' => 'Invalid action specified', 'action' => $action]);
        break;
}

$conn->close();
?>