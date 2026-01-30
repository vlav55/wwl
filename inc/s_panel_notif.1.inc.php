<?
include_once "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "init.inc.php";

if($_SESSION['access_level']>3) {
	//~ print "<p class='alert alert-danger' >Нет доступа</p>";
	//~ exit;
}

$top=new top($database,'Настройка уведомлений');
$db=new db($database);

include "/var/www/vlav/data/www/wwl/inc/s_panel_upload_file.1.inc.php";
$user_id=$_SESSION['userid_sess'];
$user_name=$db->dlookup("real_user_name","users","id='$user_id'");
$user_login=$db->dlookup("username","users","id='$user_id'");

function disp_checkbox($key,$label) {
	global $db,$user_id;
	?>
    <div class="form-check" style="font-size: 1.5rem;">
		<?
			$checked=$db->users_notif_get($user_id, $key) ? "CHECKED" : "";
		?>
			<input type="checkbox" class="form-check-input" user_id="<?=$_SESSION['userid_sess']?>" id="notif_<?=$key?>" <?=$checked?> style="transform: scale(1.5);">
			<label class="form-check-label mx-2" for="notif_<?=$key?>"> <?=$label?></label>
    </div>
    <p id="result_<?=$key?>" class='alert alert-success'  style="display: none;"></p>
	<script>
		$(document).ready(function(){
			// Event listener for checkbox state change
			$('#notif_<?=$key?>').change(function(){
				// Toggle the value based on the checkbox state
				const isChecked = $(this).is(':checked');
				$(this).val(isChecked ? 1 : 0);
				const userId = $(this).attr('user_id');


				// AJAX request to set the notification
				$.ajax({
					url: 'jquery.php', // URL of the same script
					type: 'POST',
					data: {
						users_notif: 'yes',
						key: '<?=$key?>',
						val: $(this).val(),
						user_id: userId
					},
					success: function(response) {
						const data = JSON.parse(response);
						if (data.status === 'success') {
							$('#result_<?=$key?>').text('Настройки успешно сохранены! ').removeClass('hidden').show();
						} else {
							$('#result_<?=$key?>').text('Ошибка при сохранении настроек.');
						}
					},
					error: function() {
						$('#result_<?=$key?>').text('Произошла ошибка с AJAX запросом.');
					}
				});
			});
		});
	</script>
	<?
}

?>
<br>
<h2 class='text-center' >Настройка уведомлений служебного бота</h2>
<p class='text-center' >для <? print "$user_login ($user_name)";?></p>

<div class='container'>
	<?disp_checkbox('msg','Уведомлять о входящих сообщениях');?>
	<?disp_checkbox('reg','Уведомлять о регистрациях');?>
	<?disp_checkbox('order','Уведомлять о заказах');?>
	<?disp_checkbox('pay','Уведомлять об оплатах');?>
	<?disp_checkbox('fee','Уведомлять о запросах на вывод средств от партнеров');?>
	<?disp_checkbox('partner','Уведомлять о вопросах из партнерских кабинетов');?>
</div>

<?
$top->bottom();
?>
