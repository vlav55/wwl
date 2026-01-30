<?php
class db_js {
	function js_select_partner($INPUT_value,
								$p1,
								$GET_userid_name="userid",
								$GET_submit_name="set_userid",
								$GET_submit_clr="clr_userid"
								) {
		global $DB200;
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
			  <input type="text" class="form-control m-0" id="userInput" placeholder="фильтр по партнеру" value='<?=$INPUT_value?>'  autocomplete="off">

			  <div class="input-group-append m-0 p-0">
				<span class="input-group-addon" id="clearInput">
				  <i class="fa fa-times-circle text-secondary" onclick="document.getElementById('userInput').value = ''"></i>
				</span>
			  </div>

			</div>
			<input type="hidden" id="userID" name="<?=$GET_userid_name?>"> <!-- Скрытое поле для записи ID -->
			<input type="hidden" value="<?=$p1?>" name="p1">
			<input type="hidden" name="<?=$GET_submit_name?>" value='yes'>
		  </div>
		  
		  <button type='submit' class='btn btn-light btn-xsm' name='<?=$GET_submit_clr?>' value='yes'><span class='fa fa-remove' title='показать всех'></span></button>
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
			$('#FormUserList').submit();
		  });

		  // При клике на иконку стирания значения
		  $(document).on('click', '#clearIcon', function() {
			$('#userInput').val(''); // Очистить поле ввода
			$('#userID').val(''); // Очистить скрытое поле
		  });
		});
		</script>
	<?
	}
}
?>
