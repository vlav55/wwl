<?php
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "init.inc.php";
$db = new top($database, 'Тэги', false);
if(!$uid=intval($_GET['uid'])) {
	print "<p class='alert alert-warning' >Ошибка</p>";
	exit;
}
$name=$db->dlookup("name","cards","uid='$uid'");
$name.=" ".$db->dlookup("surname","cards","uid='$uid'");
$res_tags=$db->query("SELECT * FROM tags_op JOIN tags ON tags.id=tag_id WHERE uid='{$r['uid']}'");
$tags="";
while($r_tags=$db->fetch_assoc($res_tags)) {
	$tags_bg=$r_tags['tag_color'];
	$tags_color=$db->get_contrast_color($r_tags['tag_color']);
	$tags.="<span class='p-1 mx-1 rounded small'  style='background-color:$tags_bg; color:$tags_color;'>{$r_tags['tag_name']}</span>";
}
?>
<div class='container' >
	<p class='font-weight-bold' ><?=$name?></p>
	<?print_tags();?>
</div>
<?
function print_tags() {
	?>
	<div class="card bg-light card bg-light-sm tag-list">

		<div class="tag-container">
			<div id="tag-list"></div>
			<button id="assign-tag-btn" style="width: 30px; height: 30px; border: none; margin-left: 5px; background-color: whitesmoke;">
				<i class='fa fa-plus text-primary' ></i>
			</button>
			<?if($_SESSION['access_level']<4 && 1==2) {?>
			<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tagCreationModal" style="height: 30px; padding: 0 12px; line-height: 30px;">
				Тэги
			</button>
			<?}?>
		</div>
		<input type="text" id="tagFilter" style="border: none; outline: none; display: none"/>
		<!-- <button type="button" id="assign-tag-btn" style="border: none; background-color: whitesmoke;">+</button> -->
		<div id="tagDropdown" style="display: none;">
		
		</div>
		
	</div>
	<?
	print_tags_modals();
}
function print_tags_modals() {
	?>
	<div class="modal fade" id="tagCreationModal" tabindex="-1" role="dialog" aria-labelledby="tagCreationModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
		  <div class="modal-content">
			<div class="modal-header">
			  <h5 class="modal-title text-center" id="tagCreationModalLabel">Управление тегами</h5>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
			<div class="modal-body container-fluid">
			  <!-- Section with 'Добавить тэг' button -->
			  <div class="d-flex justify-content-end mb-3">
				<button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#tagCreationForm" aria-expanded="false" aria-controls="tagCreationForm">Добавить тэг</button>
			  </div>
			  <!-- Collapsible Tag Creation Form -->
			  <div class="collapse" id="tagCreationForm">
				<div class="container mt-3">
					<form id="tag-form" class="d-flex flex-column align-items-center text-center w-100">
						<div class="form-group">
							<label for="tag-name" class="mr-1">Название</label>
							<input type="text" class="form-control form-control-sm" id="tag-name" name="tag_name" required="">
						</div>
						
						<div class="form-group position-relative">
							
							<div id="color-dropdown" style="width: 200px; margin: 0 auto;">
								<!-- ... color boxes ... -->
							</div>
							
							<div class="d-flex align-items-center justify-content-center mt-2">
								<div id="selected-color" class="ml-2 rounded" style="width: 100px; height: 45px; background-color: #000000;"></div>
							</div>
							
							<input type="hidden" id="tag-color" name="tag_color" value="#000000" required="">
						</div>
					
						<div class="modal-footer d-flex justify-content-center w-100">
							<button type="submit" class="btn btn-primary">Сохранить</button>
							<button type="button" class="btn btn-secondary" id="tagCreationCancel">Отмена</button>
						</div>
					</form>
				</div>
			  </div>
			  <table class="table table-striped" id="existing-tags">
				<thead>
					<tr>
						<th>№</th>
						<th>Тэг</th>
						<th title='Запрет рассылки'  data-toggle="tooltip" data-placement="right">
							<i class='fa fa-info-circle'></i>
						</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			</div>
		  </div>
		</div>
	</div>
	<script>
	$(document).ready(function(){
	  $('[data-toggle="tooltip"]').tooltip();
	});
	</script>
		  <!-- Edit Tag Modal -->
		<div class="modal fade" id="editTagModal" tabindex="-1" role="dialog" aria-labelledby="editTagModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
				<h5 class="modal-title" id="editTagModalLabel">Изменить тэг</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				</div>
				<div class="modal-body">
					<form id="edit-tag-form" class="d-flex flex-column align-items-center text-center w-100">
						<div class="form-group">
							<label for="tag-name" class="mr-1">Название</label>
							</div>
								<input type="text" class="form-control form-control-sm" id="new-tag-name" name="tag_name" required="">
								
						</div>
						
						<div class="form-group position-relative">
							<div id="edit-color-dropdown" style="width: 200px; margin: 0 auto;">
								<!-- ... color boxes ... -->
							</div>
							
							<div class="d-flex align-items-center justify-content-center mt-2">
								<div id="edit-selected-color" class="ml-2 rounded" style="width: 100px; height: 45px; background-color: #000000;"></div>
							</div>
							
							<input type="hidden" id="new-tag-color" name="tag_color" required="">
						</div>

						<div class="mx-3 form-check position-relative">
						  <input   class="form-check-input" type="checkbox" name="tag_fl" value="" id="fl_not_send">
						  <label class="form-check-label" for="fl_not_send">
							Не отправлять рассылку <br>
							<small>если установлено - подписчикам с этим тэгом никакие рассылки отправляться не будут</small>
						  </label>
						</div>						

						<div class="modal-footer d-flex justify-content-center w-100">
							<button type="submit" class="btn btn-primary">Сохранить</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	<?
}
include "/var/www/vlav/data/www/wwl/inc/msg_tags.inc.php";
$db->bottom();
?>
