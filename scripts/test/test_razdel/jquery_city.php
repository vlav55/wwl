<?php
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db('vkt');

// Получение данных из запроса
if(isset($_POST["msgs_city"])){
    $search = $_POST["msgs_city"];

    // Поиск городов в таблице
    $sql = "SELECT DISTINCT city FROM cards WHERE city LIKE '%$search%'";
    $result = $db->query($sql);

    // Вывод результатов
    if(mysqli_num_rows($result) > 0){
        echo "<ul class='list-group'>";
        while($row = $db->fetch_assoc($result)){
            echo "<li class='list-group-item'>".$row['city']."</li>";
        }
        echo "</ul>";
    } else{
        echo "<p class='text-muted'>Город не найден</p>";
    }
}

?>
