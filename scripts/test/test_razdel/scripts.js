function showRazdelModal() {
  $('#razdelModal').modal('show');
  loadRazdels();
}

function loadRazdels() {
  var razdelSelect = document.getElementById('razdelSelect');

  // Создаем AJAX-запрос для получения списка разделов
  var xhr = new XMLHttpRequest();
  xhr.open('GET', 'get_razdels.php', true);
  xhr.onreadystatechange = function() {
    if (xhr.readyState == 4 && xhr.status == 200) {
      // Парсим полученный JSON и добавляем опции в выпадающий список
      var razdels = JSON.parse(xhr.responseText);
      razdels.forEach(function(razdel) {
        var option = document.createElement('option');
        option.value = razdel.id;
        option.textContent = razdel.razdel_name;
        razdelSelect.appendChild(option);
      });
    }
  }
  xhr.send();
}

function saveRazdel() {
  var razdelSelect = document.getElementById('razdelSelect');
  var selectedOption = razdelSelect.options[razdelSelect.selectedIndex];
  var razdelId = selectedOption.value;

  // Получаем значение cards.id, которое известно
  var cardsId = 123; // Заменить на соответствующую логику получения cards.id

  // Отправляем AJAX-запрос для обновления cards.razdel_id
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'update_cards.php', true);
  xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function() {
    if (xhr.readyState == 4 && xhr.status == 200) {
      console.log('Запись обновлена');
      $('#razdelModal').modal('hide');
    }
  }
  xhr.send('cardsId=' + encodeURIComponent(cardsId) + '&razdelId=' + encodeURIComponent(razdelId));
}
