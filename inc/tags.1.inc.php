<?
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "/var/www/vlav/data/www/wwl/inc/msg.class.php";
include "init.inc.php";
$db=new top($database,0,false,$favicon);
print "<div class='container' >";

print "<p class='mb-3' ><a href='javascript:window.close();' class='btn btn-warning btn-sm' target=''>Закрыть</a></p>";
if($db->userdata['access_level']>3) {
	print "<div class='alert alert-warning' >Access prohibited. 1</div>";
	exit;
}

$m=new msg; 
print "<a class='' href='#tagCreationModal'  data-toggle='modal' data-target='#tagCreationModal'>Тэги</a>";
$uid=0;
$m->print_tags_modals();
include "msg_tags.inc.php";

?>
<script>
  // Определяем функцию, которая будет отображать модальное окно
  function showTagCreationModal() {
    $('#tagCreationModal').modal('show'); // используем функцию 'show' для отображения модального окна
  }

  // Вызываем функцию показа модального окна при загрузке страницы
  $(document).ready(function(){
    showTagCreationModal(); // вызываем функцию при загрузке страницы
  });
</script>
<?
print "</div>";
$db->bottom();
?>
