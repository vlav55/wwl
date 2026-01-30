<?
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "../init.inc.php";

$db=new top($database,'Начисления',false);

if(isset($_GET['do_save'])) {
	?>
	<script>
		$(document).ready(function(){
			$("#partnerModal").modal("show");
		});
    </script>
    <?
}

print "<div class='container' >";

$klid=intval($_GET['klid']);
$username=$db->dlookup("username","users","klid=$klid");
$real_user_name=$db->dlookup("real_user_name","users","klid=$klid");

print "<p class='p-3 text-right' ><a href='javascript:window.close();' class='btn btn-warning' target=''>закрыть</a></p>";
print "<h3 class='py-4' >Начисления по партнерской программе - $real_user_name ($username)</h3>";

if($_SESSION['username']=='admin' || $_SESSION['username']=='vlav') {
	print "<p class='small text-danger mb-3' >Внимание: вы, как администратор, можете передать любую операцию
	другому партнеру либо изменить сумму начислений.
	Для этого кликните на партнере или на сумме. Чтобы удалить операцию достаточно сумму начислений сделать равной 0.
	</p>";
}

$res=$db->query("SELECT *,partnerka_op.id AS partnerka_id, partnerka_op.tm AS tm, partnerka_op.comm AS comm
			FROM partnerka_op
			JOIN cards ON cards.uid=partnerka_op.uid
			WHERE cards.del=0 AND partnerka_op.klid_up='$klid'
			ORDER BY partnerka_op.tm DESC");
			
print "<table class='table table-striped' >
	<thead>
		<tr>
			<th>Дата</th>
			<th>Партнер</th>
			<th>Имя клиента</th>
			<!--<th>Продукт</th>-->
			<th>Сумма продажи</th>
			<th>% вознагр</th>
			<th>Сумма вознагр</th>
			<th>Комм</th>
		</tr>
	</thead>
	<tbody>";
$s=0;
while($r=$db->fetch_assoc($res)) {
	$dt=date("d.m.Y",$r['tm']);
	$name=$r['name']." ".$r['surname'];
	$partner=$db->dlookup("username","users","klid={$r['klid']}");
	$partner_name=$db->dlookup("real_user_name","users","klid={$r['klid']}");
	//$product=$base_prices[$r['product_id']]['descr'];
	$amount=$r['amount'];
	$fee=$r['fee'];
	$fee_sum=$r['fee_sum'];
	$id=$r['partnerka_id'];
	$userid=$db->get_user_id($r['klid']);
	print "<tr id='p_$id'>
		<td title='$id'>$dt</td>
		<td class='partner-cell' partnerka_id='$id' p_name='$partner' userid='$userid' title='$partner_name'>$partner</td>
		<td>$name</td>
		<!--<td>$product</td>-->
		<td>$amount</td>
		<td>$fee</td>
		<td class='fee-cell' partnerka_id='$id' klid='$klid'>$fee_sum</td>
		<td>".htmlspecialchars(nl2br($r['comm']))."</td>
		</tr>";
	$s+=$fee_sum;
}
print "</tbody></table>";
print "<h3 class='text-right' >Всего: $s р.</h3>";
print "</div>";

?>
<!-- CHANGE FEE SUM -->
<?
if(isset($_GET['ch_fee'])) {
	$fee_sum=intval($_GET['fee']);
	$id=intval($_GET['partnerka_id']);
	if($id) {
		$db->query("UPDATE partnerka_op SET fee_sum='$fee_sum' WHERE id='$id'");
		print "<script>location='?klid=$klid#p_$id'</script>";
	}
}

if($_SESSION['username']=='admin' || $_SESSION['username']=='vlav') {
?>
<div class="modal fade" id="feeModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Изменить сумму начислений</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="ch_fee">Введите сумму</label>
            <input type="text" class="form-control" id="fee" name="fee">
            <input type="hidden" id="partnerka_id" name="partnerka_id">
            <input type="hidden" id="klid" name="klid">
            <input type="hidden" name="ch_fee" value='yes'>
          </div>
          <button type="submit" class="btn btn-primary">Записать</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  $('.fee-cell').click(function() {
    var feeSum = $(this).text();
	var p_id=this.getAttribute('partnerka_id');
	var klid=this.getAttribute('klid');
    $('#fee').val(feeSum);
    $('#partnerka_id').val(p_id);
    $('#klid').val(klid);
    $('#feeModal').modal('show');
  });
});
</script>
<? } ?>

<!-- CHANGE PARTNER -->
<div class="modal" id="partnerModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Передать операцию на другого партнера</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
		  <?
			if(isset($_GET['do_save'])) {
				//$db->print_r($_GET);
				$partnerka_id=intval($_GET['partnerka_id']);
				if($klid_new=$db->get_klid(intval($_GET['userid']))) {
					//print "klid=$klid_new";
					$partner_login=$db->dlookup("username","users","klid='$klid_new'");
					$partner_name=$db->dlookup("real_user_name","users","klid='$klid_new'");
					$level=$db->dlookup("level","partnerka_op","id='$partnerka_id'");
					if($level==1)
						$db->query("UPDATE partnerka_op SET klid_up='$klid_new',klid='$klid_new' WHERE id='$partnerka_id'");
					else
						$db->query("UPDATE partnerka_op SET klid_up='$klid_new' WHERE id='$partnerka_id'");
					?>
					<p class='alert alert-success' >Операция передана на партнера:<br>
					<b><?=$partner_login?> <?=$partner_name?></b>
					<br>
					<a href='?klid=<?=$klid_new?>' class='' target=''>посмотреть</a>
					</p>
					<p><button class='btn btn-warning btn-sm'  type="button" id="closeModalBtn">Закрыть</button></p>
					<?
				}
			} else {
		  ?>
		<style>
		.input-group-addon {
			position: relative; /* Set position to relative for the parent */
		  }
		.input-group-addon .fa-times-circle {
			position: absolute;
			right: 10px;
			top:10px;
			cursor: pointer;
		  }
		</style>
		<div class='m-0 p-0'>
		<form class='form-inline m-0 p-0' id='FormUserList'>
		  <div class="form-group m-0 p-0">
			<div class="input-group">
			  <input type="text"
					class="form-control m-0"
					id="userInput"
					placeholder="начните вводить имя партнера"
					value=''
					autocomplete="off" >

			  <div class="input-group-append m-0 p-0">
				<span class="input-group-addon" id="clearInput">
				  <i class="fa fa-times-circle text-secondary" onclick="document.getElementById('userInput').value = ''"></i>
				</span>
			  </div>

			</div>
			<input type="hidden" id="userID" name="userid"> <!-- Скрытое поле для записи ID -->
			<input type="hidden" id="partnerkaId" value="" name="partnerka_id">
			<input type="hidden" name="klid" value='<?=$klid?>'>
			<input type="hidden" name="save" value='yes'>
		  </div>
		  
		  <button type='submit' class='btn btn-primary btn-xsm' name='do_save' value='yes'><span class='	fa fa-save' title='смнить партнера'></span></button>
<!--
		  <button type='submit' class='btn btn-light btn-xsm' name='do_clr' value='yes'><span class='fa fa-remove' title=''></span></button>
-->
		</form>
		<div id="userList" class='m-0 p-0' ></div>
		</div>

		<script>
		$(document).ready(function() {
		  // При вводе символов в текстовое поле
		  $('#userInput').on('input', function() {
			var userInput = $(this).val(); // Значение текстового поля
			
			// Отправка AJAX-запроса на сервер для получения списка пользователей
			$.ajax({
			  url: '<?=$DB200?>/jquery.php', // Путь к PHP-обработчику
			  method: 'POST',
			  data: { userInput: userInput,
					access_level: <?=$_SESSION['access_level']?>,
					user_id: <?=$_SESSION['userid_sess']?>
					}, // Передача введенного значения на сервер
			  dataType: 'json',
			  success: function(response) {
				var userList = '';

				if (response.length > 0) {
				  response.forEach(function(user) {
					userList += '<a href="#" class="list-group-item list-group-item-action" data-id="' + user.id + '">' + user.real_user_name + '</a>';
				  });
				} else {
				  userList = '<p>Пользователи не найдены</p>';
				}
				
				$('#userList').html('<div class="list-group">' + userList + '</div>'); // Вывод списка пользователей
			  }
			});
		  });

		  // При выборе значения из списка
		  $(document).on('click', '.list-group-item', function(e) {
			e.preventDefault();
			
			var selectedUserName = $(this).text(); // Выбранное значение
			var selectedUserId = $(this).data('id'); // ID выбранного пользователя
			
			$('#userInput').val(selectedUserName); // Поместить выбранное значение в поле ввода
			$('#userID').val(selectedUserId); // Записать ID в скрытое поле
			
			// Можно выполнить другие операции с выбранным значением
			
			$('#userList').html(''); // Очистить список
			//$('#FormUserList').submit();
		  });

		  // При клике на иконку стирания значения
		  $(document).on('click', '#clearIcon', function() {
			$('#userInput').val(''); // Очистить поле ввода
			$('#userID').val(''); // Очистить скрытое поле
		  });
		});
		</script>

		<? } ?>
		
      </div>
    </div>
  </div>
</div>

<?if($_SESSION['username']=='admin' || $_SESSION['username']=='vlav') { ?>
<!-- JavaScript to handle modal opening with form field population -->
<script>
  // Add a click event listener to the partner cells
  document.querySelectorAll('.partner-cell').forEach(cell => {
    cell.addEventListener('click', function() {
      // Extract the $id and $partner values from the cell's attributes
      let partnerId = this.getAttribute('partnerka_id');
      let partnerName = this.getAttribute('p_name');
      let partnerUserId = this.getAttribute('userid');
      // Populate the form input fields in the modal with the values
      document.getElementById('partnerkaId').value = partnerId;
      document.getElementById('userInput').value = partnerName;
      document.getElementById('userID').value = partnerUserId;
      // Open the modal
      $('#partnerModal').modal('show');
    });
  });
</script>
<? } ?>

<script>
  $('#partnerModal').on('hidden.bs.modal', function (e) {
    // Perform the redirection
    window.location = '?klid=<?=$klid?>';
  });
</script>

<script>
  $(document).ready(function(){
    // Attach click event to the close button inside the modal
    $('#closeModalBtn').on('click', function(){
      $('#partnerModal').modal('hide'); // Close the modal by directly calling the 'hide' method
    });
  });
</script>


<?

$db->bottom();
?>
