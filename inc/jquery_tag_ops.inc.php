<?php
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
switch($action) {
    case 'addTagToUser':
        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : null;
        $tag_id = isset($_POST['tag_id']) ? intval($_POST['tag_id']) : null;
        $tag_name=$db->dlookup("tag_name","tags","id='$tag_id'");
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
        $tm=time();

        if ($uid && $tag_id) {
            if(!$db->dlookup("id","tags_op","uid ='$uid' AND tag_id ='$tag_id'")) {
                $db->query("INSERT INTO tags_op (uid, tag_id,user_id, tm) VALUES ($uid, $tag_id, $user_id, $tm)");
                $db->save_comm($uid,$user_id,"Добавлен ТЭГ: $tag_name",160,$tag_id); //save_comm($uid,$user_id,$comm,$source_id=0,$vote_vk_uid=0,$mode=0, $force=false)
                if ($db->insert_id()) {
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
        $id = intval($_POST['id']);

        // First check if the tag is associated with any card

        if ($db->dlookup("id","tags_op","tag_id = '$id'")) {
            echo json_encode(["status" => "warning", "message" => "Если вы удалите этот тэг, он также будет удален со всех карт."]);
        }
        else {
            echo json_encode(["message" => "There are no references of this tag $id"]);
            
        }
        exit();
    case 'createTag':
        $data = $_POST;

        if(isset($data['tagName']) && isset($data['tagColor'])) {
            $tag_name = $data['tagName'];
            $tag_color = $data['tagColor'];
            
            $db->query("INSERT INTO tags (tag_name, tag_color) VALUES ('$tag_name', '$tag_color')");
            $id = $db->insert_id();
        } else {
            echo "Error: Invalid input";
        }
        break;

    case 'delTag':
        if ($id = intval($_POST['id'])) {
			if(!$db->dlookup("id","tags_op","tag_id = '$id'")) {
				$db->query("UPDATE tags SET del = 1 WHERE id = '$id'");
				$db->query("DELETE FROM tags_op WHERE tag_id = '$id'");
				echo json_encode(['success' => 'Тэг удален']);
			} else {
				echo json_encode(['error' => 'Тэг используется']);
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
            $tag_id = intval($data['tagId']);
            $fl_not_send=($data['flNotSend']=='true')?1:0;
			//$db->notify_me("HERE_".$fl_not_send);
			if(!empty($tag_color))
				$db->query("UPDATE tags SET tag_name = '$tag_name', tag_color = '$tag_color',fl_not_send='$fl_not_send' WHERE id = '$tag_id'");
			else
				$db->query("UPDATE tags SET tag_name = '$tag_name', fl_not_send='$fl_not_send' WHERE id = '$tag_id'");
        } else {
            echo "Error: Invalid input";
        }
        break;

    case 'fetchTags':
        $res=$db->query("SELECT id, tag_name, tag_color, fl_not_send FROM tags WHERE del = 0");
        if ($db->num_rows($res) > 0) {
            $tags = [];
            while($row = $db->fetch_assoc($res)) {
                $tags[] = $row;
            }
            echo json_encode($tags);
        } else {
            echo json_encode([]);
        }
        break;

    case 'fetchUserTags':
        $uid = isset($_GET['uid']) ? intval($_GET['uid']) : null;
        if ($uid) {
            $res=$db->query("SELECT tags.id, tags.tag_name, tags.tag_color, tags_op.user_id, tags_op.tm FROM tags 
                    JOIN tags_op ON tags.id = tags_op.tag_id
                    WHERE tags_op.uid = '$uid'");
            $tags = [];
            while($row = $db->fetch_assoc($res)) {
                $tags[] = $row;
            }
            echo json_encode($tags);
        } else {
            echo json_encode(['error' => 'Invalid UID']);
        }
        break;

    case 'removeTagFromUser':
        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : null;
        $tag_id = isset($_POST['tag_id']) ? intval($_POST['tag_id']) : null;
        $tag_name=$db->dlookup("tag_name","tags","id='$tag_id'");

        if ($uid && $tag_id) {
            $db->query("DELETE FROM tags_op WHERE uid = '$uid' AND tag_id = '$tag_id'");
            $db->save_comm($uid,$user_id,"Удален ТЭГ: $tag_name",161,$tag_id); //save_comm($uid,$user_id,$comm,$source_id=0,$vote_vk_uid=0,$mode=0, $force=false)
        } else {
            echo json_encode(['error' => 'Invalid UID or Tag ID']);
        }
        break;

    case 'deleteTag':
        header('Content-Type: application/json'); // Add content type header
        if(isset($_POST['tagId'])) {
            $tag_id = intval($_POST['tagId']);
            $db->query("UPDATE tags SET del = 1 WHERE id = '$tag_id'");
            echo 'success'; //json_encode(['success' => true]);
        } else {
            echo 'error'; //json_encode(['success' => false, 'error' => 'No tag ID provided']);
        }
        break;

    default:
        //echo json_encode(['error' => 'Invalid action specified', 'action' => $action]);
        break;
}
exit;

?>
