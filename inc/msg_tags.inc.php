<script>
		// Получение существующих тэгов из базы данных

		function displayTable(){
				$.ajax({
					url: 'jquery.php',
					type: 'GET',
					dataType: 'json',
					data: {action: 'fetchTags'},
					success: function(tags) {
						const tbody = $('table.table-striped tbody');
						tbody.empty();

						tags.forEach(function(tag) {
							var tr = $('<tr>').css({
								backgroundColor: tag.tag_color + 'CC',
								color: getTextColorBasedOnBgColor(tag.tag_color || '#000000')
							});

							var tdId = $('<td>').text(tag.id);
							var tdName = $('<td>').text(tag.tag_name);
							var fl;
							if(tag.fl_not_send==1)
								fl="<i class='fa fa-check'></i>";
							var tdFl=$('<td>').html(fl);
							
							var controlTd = $('<td>');
							var editButton = $('<a>').addClass('mx-3 edit-tag-btn').attr({
								'data-tag-id': tag.id,
								'data-tag-name': tag.tag_name,
								'data-tag-color': tag.tag_color,
								'data-tag-fl': tag.fl_not_send,
								title: 'изменить'
							}).html('<span class="fa fa-edit"></span>');
//console.log("HERE2_"+tag.fl_not_send);
							var deleteButton = $('<a>').addClass('mx-3 delete-tag-btn').attr({
								'data-tag-id': tag.id,
								title: 'удалить'
							}).html('<span class="fa fa-trash"></span>');

							controlTd.append(editButton);
							controlTd.append(deleteButton);

							tr.append(tdId).append(tdName).append(tdFl).append(controlTd);
							tbody.append(tr);
						});
					},
					error: function(error) {
						console.log("Error fetching data");
						console.log(error);
					}
				});
			}

			displayTable();

	function unixTimestampToReadable(unixTimestamp) {
	  // Create a new Date object based on the Unix timestamp
	  const date = new Date(unixTimestamp * 1000);

	  // Get the individual components of the date
	  const year = date.getFullYear();
	  const month = (date.getMonth() + 1).toString().padStart(2, '0');
	  const day = date.getDate().toString().padStart(2, '0');
	  const hours = date.getHours().toString().padStart(2, '0');
	  const minutes = date.getMinutes().toString().padStart(2, '0');
	  const seconds = date.getSeconds().toString().padStart(2, '0');

	  // Return the formatted string
	  return `${day}.${month}.${year} ${hours}:${minutes}`;
	}

		function displayTags(tags) {
			
			let dropdownHTML = tags.map(tag => `
				<div class="tag-option" data-tag-id="${tag.id}" style="cursor: pointer; background-color: ${tag.tag_color || '#000'}CC; color: ${getTextColorBasedOnBgColor(tag.tag_color || '#000000')}; padding: 5px;">
					${tag.tag_name}
				</div>
			`).join('');
			$('#tagDropdown').html(dropdownHTML);
		}

		function fetchAndDisplayTags() {
			$.ajax({
				url: 'jquery.php',
				type: 'GET',
				data: {action: 'fetchTags'},
				success: function(data) {
					let tags = JSON.parse(data);
					displayTags(tags);
				}
			});
		}

		function populateTags(uid) { 
			$.ajax({
				url: 'jquery.php', 
				type: 'GET',
				data: {
					action: 'fetchUserTags',
					uid: uid
				},
				success: function(data) {
					let response = JSON.parse(data);
					if(!response.error){
						let tags = response;
						let tagsHTML = tags.map(tag => `
							<span title="${unixTimestampToReadable(tag.tm)} by ${tag.user_id}" class="badge badge-pill" style="background-color: ${tag.tag_color || '#000'}CC; color: ${getTextColorBasedOnBgColor(tag.tag_color || '#000000')}; margin-right: 5px;">
								${tag.tag_name} 
								<button class="btn btn-link unassign-tag-btn" style="color: ${getTextColorBasedOnBgColor(tag.tag_color || '#000000')}; font-size: 10px; padding: 0; margin-left: 5px;" data-tag-id="${tag.id}">&#10006</button>
							</span>
						`).join('');
						$(`#tag-list`).html(tagsHTML);
					}
				}
			});
		} 

		$('.card').each(function() {
			let urlParams = new URLSearchParams(window.location.search);
			let uid = <?=$uid?>;
			populateTags(uid);
		});

		//Скрипт для создания тэгов

		$('#tag-form').on('submit', function(e) {
			e.preventDefault();


			$.ajax({
				url: 'jquery.php',
				type: 'POST',
				data: {
					action: 'createTag',
					tagName: $('#tag-name').val(),
					tagColor: $('#tag-color').val()
				},
				success: function(response) {
					console.log(response)
					console.log("Data sent successfully");
					displayTable();
					fetchAndDisplayTags();
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log("Error sending data");
					console.log("Status: " + textStatus);
					console.log("Error: " + errorThrown);
					console.log("Response: " + jqXHR.responseText);
				
				}
			});
			$('#tagCreationForm').collapse('hide');
		});

		function getTextColorBasedOnBgColor(hex) {
			var r, g, b, hsp;  

			if (hex.length == 4) {
				r = "0x" + hex[1] + hex[1];
				g = "0x" + hex[2] + hex[2];
				b = "0x" + hex[3] + hex[3];
			} else if (hex.length == 7) {
				r = "0x" + hex[1] + hex[2];
				g = "0x" + hex[3] + hex[4];
				b = "0x" + hex[5] + hex[6];
			}
			
			hsp = Math.sqrt(
				0.299 * (r * r) +
				0.587 * (g * g) +
				0.114 * (b * b)
			);

			return hsp < 127.5 ? 'white' : 'black';
			
		}

		

		//Скрипт для получения всех тэгов из базы данных
		let currentModal = '';
		/* const standardColors = [
			"#000000", "#800000", "#008000", "#808000", "#000080", 
			"#800080", "#008080", "#c0c0c0", "#808080", "#ff0000", 
			"#00ff00", "#ffff00", "#0000ff", "#ff00ff", "#00ffff", 
			"#ADD8E6", "#000000", "#993300", "#333300", "#003300", 
			"#003366", "#330099", "#333399", "#333333", "#800000", 
			"#008000", "#808000", "#800080", "#008080", "#000080", 
			"#666699", "#969696"
		]; */

		const standardColors = [
			// Reds
			"#FF5252", // Bright Red
			"#FF1744", // Vibrant Red
			"#D32F2F", // Classic Red
			"#FFE2E2", // Soft Red
			
			// Yellows
			"#FFD600", // Pure Yellow
			"#FFC400", // Golden Yellow
			"#FFB300", // Amber
			"#FFF3D6", // Soft Yellow
			
			// Greens
			"#4CAF50", // Material Green
			"#00E676", // Bright Green
			"#2E7D32", // Forest Green
			"#E8F5E9", // Soft Green
			
			// Blues
			"#2196F3", // Material Blue
			"#00B0FF", // Bright Blue
			"#1976D2", // Classic Blue
			"#E3F2FD", // Soft Blue
			
			// Mixed Bright
			"#FF4081", // Pink Red
			"#FFAB00", // Orange
			"#00BFA5", // Teal
			"#304FFE", // Indigo
			
			// Mixed Soft
			"#FF8A80", // Light Red
			"#FFE57F", // Light Yellow
			"#B2FF59", // Light Green
			"#82B1FF", // Light Blue
			
			// Mixed Medium
			"#FF6E40", // Deep Orange
			"#FFAB40", // Light Orange
			"#69F0AE", // Light Teal
			"#448AFF"  // Light Indigo
		];

		$(document).ready(function() {

			$('.btn.btn-secondary#tagCreationCancel').on('click', function() {
				$('#tagCreationForm').collapse('hide');
			});

			const colorDropdown = $('#color-dropdown');
			standardColors.forEach(function(color) {
				const colorBox = $('<div>').addClass('color-box').css('background-color', color).click(function() {
					$('#tag-color').val(color);
					$('#selected-color').css('background-color', color);
					// colorDropdown.hide();
				});
				colorDropdown.append(colorBox);
			});

			const editColorDropdown = $('#edit-color-dropdown');
			standardColors.forEach(function(color) {
				const colorBox = $('<div>').addClass('color-box').css('background-color', color).click(function() {
					$('#new-tag-color').val(color);
					$('#edit-selected-color').css('background-color', color);
					// colorDropdown.hide();
				});
				editColorDropdown.append(colorBox);
			});


			//Удаление тэга из базы

			function deleteTag(tagId) {
				$.ajax({
					url: 'jquery.php',
					type: 'POST',
					data: { 
						action: 'delTag',
						id: tagId
					},
					success: function(response) {
						console.log("delete success");
						console.log("HERE_"+response)
						// Assuming response is a string, parse it to handle JSON
						const parsedResponse = typeof response === 'string' ? JSON.parse(response) : response;

						// Check if parsedResponse has key "error"
						if (parsedResponse && parsedResponse.error) {
							// Display the error value
							 alert("Ошибка: " + parsedResponse.error);
						} else {
							let urlParams = new URLSearchParams(window.location.search);
							let uid = <?=$uid?>;
							populateTags(uid);
							displayTable();
							fetchAndDisplayTags();
						}
					}
				})
				
			}

			$('#existing-tags tbody').on('click', '.delete-tag-btn', function() {
				event.preventDefault();
    			// event.stopPropagation();
				// console.log("deleteee");
				var tagId = $(this).attr('data-tag-id');

				$.ajax({
					url: 'jquery.php',
					type: 'POST',
					data: { 
						action: 'checkTagReference',
						id: tagId},
					success: function(data) {
						//console.log(data);
						const response = JSON.parse(data);
						//console.log(response);
						if (response.status === "warning") {
							var isConfirmed = confirm(response.message + "\n\nВы хотите продолжить?");
							if (isConfirmed) {
								deleteTag(tagId);
							}
						} else {
							deleteTag(tagId);
						}
					},
					error: function(error) {
						console.log("error")
						console.log(error)
					}
				});
			});

			$('#editTagModal').on('hidden.bs.modal', function () {
				if ($('.modal:visible').length) { 
					$('body').addClass('modal-open');
				}
			});

			//Изменить тэг

			$('#existing-tags tbody').on('click', '.edit-tag-btn', function() {
				event.preventDefault();
    			// event.stopPropagation();
				// console.log("ediittt");
				const tagId = $(this).attr('data-tag-id');
				const tagName = $(this).attr('data-tag-name');
				const tagColor = $(this).attr('data-tag-color');
				const tagFl=$(this).attr('data-tag-fl');
				$('#editTagModal').find('#edit-tag-form').attr('data-tag-id', tagId);  
    			$('#editTagModal').modal('show');
				$('#new-tag-name').attr('value', tagName);
				//$('#old-color').css('background-color', tagColor);
				$('#edit-selected-color').css('background-color', tagColor);
				if(tagFl==1)
					$('#fl_not_send').prop('checked', true);
				else
					$('#fl_not_send').prop('checked', false);
				//console.log("HERE_"+tagFl);
			});

			$('#edit-tag-form').on('submit', function(e){
				e.preventDefault();
				// e.stopPropagation();
				var tagData = 
				console.log(tagData)
				const isChecked = document.getElementById('fl_not_send').checked;
			//	console.log("HERE_"+$('#new-tag-name').val() + " "+isChecked);
				$.ajax({
					url: 'jquery.php',
					type: 'POST',
					data: {
						action: 'updateTag',
						tagId: $(this).attr('data-tag-id'),
						tagName: $('#new-tag-name').val(),
						tagColor: $('#new-tag-color').val(),
						flNotSend: isChecked
					},
					success: function(response) {
						// console.log(response)
						console.log("Tag edit successfully");
						let urlParams = new URLSearchParams(window.location.search);
						let uid = <?=$uid?>;
						displayTable();
						fetchAndDisplayTags();
						populateTags(uid);
						location.reload();
					},
					error: function(error) {
						console.log("Error deleting tag");
						// console.log(error);
					}
				});
				$('#editTagModal').find('#edit-tag-form').attr('data-tag-id', undefined);  
    			$('#editTagModal').modal('hide');
			})

			


			// Присвоить тэг
			$('#assign-tag-btn').on('click', function(event) {
				event.preventDefault();  

				$('#tagDropdown').toggle();
				$('#tagFilter').toggle();
				$('#tagFilter').focus();
  				fetchAndDisplayTags();
			});

			


			$('#tagFilter').on('input', function() {
				filterTags($(this).val());
			});

			function filterTags(query) {
				$('.tag-option').each(function() {
					if ($(this).text().toLowerCase().includes(query.toLowerCase())) {
					$(this).show();
					} else {
					$(this).hide();
					}
				});
			}

			$('#tagDropdown').on('click', '.tag-option', function() {
				let tagId = $(this).data('tag-id');
				let urlParams = new URLSearchParams(window.location.search);
				let uid = <?=$uid?>;
				let user_id=<?=$_SESSION['userid_sess']?>;


				$.post('jquery.php', {action: 'addTagToUser', uid: uid, tag_id: tagId, user_id: user_id}, function(response) {
					populateTags(uid)
				});


				$('#tagDropdown').toggle();
				$('#tagFilter').val("");
    			$('#tagFilter').css("display", "none"); 
			});

			// Убрать тэг из карты
			$('#tag-list').on('click', '.unassign-tag-btn', function() {
				let tagId = $(this).data('tag-id');
				let urlParams = new URLSearchParams(window.location.search);
				let uid = <?=$uid?>;

				$.post('jquery.php', {action: 'removeTagFromUser', uid: uid, tag_id: tagId}, function(response) {
					populateTags(uid)
				});

			});


		});
</script>
