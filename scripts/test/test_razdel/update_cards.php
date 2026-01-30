<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db('vkt');

$cardsId = intval($_POST['cardsId']);
$razdelId = intval($_POST['razdelId']);

// Обновляем cards.razdel_id
//mysqli_query($conn, "UPDATE cards SET razdel_id='$razdelId' WHERE id='$cardsId'");

?>
