<?php
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db('vkt');

$result = $db->query("SELECT id, razdel_name FROM razdel");

$razdels = array();
while ($row = $db->fetch_assoc($result)) {
  $razdel = array(
    'id' => $row['id'],
    'razdel_name' => $row['razdel_name']
  );
  array_push($razdels, $razdel);
}

// Отправляем список разделов в формате JSON
echo json_encode($razdels);
?>
