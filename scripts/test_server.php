<?php
// Подключение к базе данных MySQL
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db('vkt');

// Получение введенного значения из POST-запроса
$userInput = $_POST['userInput'];

// Поиск пользователей с помощью LIKE оператора в SQL-запросе
$sql = "SELECT id, real_user_name FROM users WHERE real_user_name LIKE '%$userInput%'";
$result = $db->query($sql);

$userList = array();

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $userList[] = array('id' => $row['id'], 'real_user_name' => $row['real_user_name']);
  }
}

// Возвращение списка пользователей в формате JSON
echo json_encode($userList);
?>
