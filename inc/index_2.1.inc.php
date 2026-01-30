<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "../init.inc.php";
$db=new db($database);

if($db->dlookup("id","lands","land_num='2'")) {
    header("Location: index2.php", true, 302);
}

if(isset($_GET['bc'])) {
	$bc=intval($_GET['bc']);
}

$db->connect('vkt');
$land_pic_p=(file_exists('../tg_files/land_pic_p.jpg'))?"<img src='../tg_files/land_pic_p.jpg' class='img-fluid' >":"";
$land_txt_p=$db->dlookup("land_txt_p","0ctrl","id=$ctrl_id");
$pp=$db->dlookup("pp","0ctrl","id=$ctrl_id");


$title='Добро пожаловать!';
$descr=$title;
$og_image="../tg_files/land_pic_p.jpg";
$og_url=$land_p;
$favicon="https://for16.ru/images/favicon.png";
include "../land_top.inc.php";

?>
<div class='container-fluid' >
	<div class='row' >
		<div class='text-center col-md-6 m-0 p-0' ><?=$land_pic_p?></div>
		<div class='col-md-6 pl-md-5' >
			<div class='container' >
				<?=$land_txt_p?>
				<div class='text-center mb-5 mt-4' >
					<a  href="#" data-toggle="modal" data-target="#regModal" class='btn btn-info btn-lg' target=''>Регистрация</a>
				</div>
			</div>
		</div>
	</div>
</div>
<?
//print "uid=$uid tg_code=$tg_code<br>";
//$db->print_r($_POST);
?>

	<div class="modal fade" id="regModal" tabindex="-1" role="dialog" aria-labelledby="regModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
<!--
					<img src="images/fin.png" width="130" height="60" alt="">
-->
				</div>
				<div class="modal-body"  id="regMsg_body">
					<h4 class="modal-title text-center pb-4" id="regModalLabel">Укажите ваши контакты</h4>
					<form id='f_reg' method='POST' action='../thanks_p.php'>
						<div class="form-group"> <label for="regName">Имя <span class='text-danger' >*</span></label> <input type="text" class="form-control" id="regName" placeholder="Имя" name='regName'> </div>
						<div class="form-group"> <label for="regPhone">Телефон <span class='text-danger' >*</span></label> <input type="phone" class="form-control" id="regPhone" placeholder="Ваша телефон" name='regPhone'> </div>
						<div class="form-group" style='display:block;'> <label for="regEmail">E-mail <span class='text-danger' >*</span></label> <input type="email" class="form-control" id="regEmail" placeholder="Ваш email" name='regEmail'> </div>
						<div class='checkbox small text-muted'><label><input id='chk1' type='checkbox' value='' CHECKED> отправляя форму, я выражаю согласие с <a href='<?=$pp?>' style='text-decoration: underline;' target='_blank'>политикой конфиденциальности</a></label></div>
<!--
						<div class="form-group"> <label for="regEmail">Email</label> <input type="email" class="form-control" id="regEmail" placeholder="Ваша почта" name='regEmail'> </div>
-->
						<input type='hidden' id='bc' name='bc' value='<?=$bc?>'>
<?
	if(isset($_GET['utm_campaign']))
		print "<input type='hidden' name='utm_campaign' value='{$_GET['utm_campaign']}'>\n";
	if(isset($_GET['utm_content']))
		print "<input type='hidden' name='utm_content' value='{$_GET['utm_content']}'>\n";
	if(isset($_GET['utm_medium']))
		print "<input type='hidden' name='utm_medium' value='{$_GET['utm_medium']}'>\n";
	if(isset($_GET['utm_source']))
		print "<input type='hidden' name='utm_source' value='{$_GET['utm_source']}'>\n";
	if(isset($_GET['utm_term']))
		print "<input type='hidden' name='utm_term' value='{$_GET['utm_term']}'>\n";
	if(isset($_GET['utm_ab']))
		print "<input type='hidden' name='utm_ab' value='{$_GET['utm_ab']}'>\n";
?>
					</form>
					<div class='contact-us' ><div class='contact-us-bgimage grid-margin' id='regMsg_' style='display:none;'></div></div>
				</div>
				<div class="modal-footer"  id="regMsg_footer">
					<button type="button" class="btn btn-success"  form="f_reg" id='regSend'>Отправить</button>
<!--
					<button type="button" class="btn btn-light" data-dismiss="modal">Закрыть</button>
-->
				</div>
			</div>
		</div>
	</div>

<script type="text/javascript">
	console.log('test');
	$("#regSend").click(function() {
		console.log("HERE_");
		//alert($("#c_name").val());
		if($("#regName").val().trim()=="") {
			alert("Необходимо указать ваше имя!");
		} else if($("#regPhone").val().trim()=="") {
			alert("Укажите, пожалуйста, телефон для связи!");
		//~ } else if($("#regEmail").val().trim()=="") {
			//~ alert("Укажите, пожалуйста, email!");
		} else if(!$("#chk1").is(":checked")) {
			alert("Необходимо согласиться с обработкой персональных данных !");
		//~ } else if(!$("#chk2").is(":checked")) {
			//~ alert("Необходимо согласиться с договором оферты !");
		} else {
			$( "#f_reg" ).submit();
		}
	});
</script>

<? include "../land_bottom.inc.php"; ?>
