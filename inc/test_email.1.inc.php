<?
$title="TEST EMAIL";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "/var/www/vlav/data/www/wwl/inc/unisender.class.php";
include "init.inc.php";
$db=new top($database,$title,true);
$email="";
$email_template="";
if(isset($_POST['email'])) {
	if($db->validate_email($_POST['email'])) {
		$email=$_POST['email'];
		$email_template=substr($_POST['email_template'],0,255);
		$uni=new unisender($unisender_secret,$email_from='',$email_from_name='');
		$db->db200=$DB200;
		$vars=[];
		if($uni->email_by_template($email,$email_template,$vars)) {
			print "<p class='alert alert-success' >Тест емэйл отправлен на $email <br>
			".print_r($uni->res,true)."
			</p>";
			$res_email=1;
		} else {
			$res_email=2;
			print "<p class='alert alert-warning' >Ошибка отправки емэйл: $email <br>
			".print_r($uni->res,true)."
			</p>";
			$db->print_r($vars);
		}
		
	} else {
		print "<p class='alert alert-danger' >Email ($email) invalid</p>";
		$email=$_POST['email'];
	}
}
?>
<div class="container mt-5">
	<h2 class='text-center' >Test UnisenderGo email</h2>
	<form action="" method="POST">
		<div class="form-group">
			<label for="email">Email:</label>
			<input type="email" class="form-control" id="email" name="email" value="<?=$email?>" required>
		</div>
		<div class="form-group">
			<label for="email_template">Шаблон:</label>
			<input type="text" class="form-control" id="email_template" name="email_template"  value="<?=$email_template?>"  required>
			<p class='small' >UnisenderGo - Инструменты - Шаблоны - Свойства - ID</p>
		</div>
		<button type="submit" class="btn btn-primary">Отправить</button>
	</form>
</div>
<?
$db->bottom();
?>
