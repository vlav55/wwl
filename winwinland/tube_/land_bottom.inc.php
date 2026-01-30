	<!-- Modal Structure -->
	<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<img id="modalImage" src="" alt="Sample Image" class="img-fluid">
				</div>
			</div>
		</div>
	</div>
	<script>
		function openImage(element) {
			// Get the image URL from the data-image attribute
			const imgSrc = element.getAttribute('data-image');
			
			// Set the src of the image in the modal
			document.getElementById('modalImage').src = imgSrc;
			
			// Show the modal
			$('#imageModal').modal('show');
		}
	</script>
 <script>
    function copySpanContent(span_id) {
      // Get the span element by its ID
      var spanElement = document.getElementById(span_id);

      // Create a temporary input element
      var tempInput = document.createElement("input");

      // Set the value of the input element to the content of the span
      tempInput.value = spanElement.textContent;

      // Append the input element to the document
      document.body.appendChild(tempInput);

      // Select the content of the input element
      tempInput.select();

      // Copy the selected content to the clipboard
      document.execCommand("copy");

      // Remove the temporary input element
      document.body.removeChild(tempInput);

      // Alert the user that the content has been copied
      alert("Ссылка скопирована!");
    }
  </script>
</div>

<footer class="footer">
<h2 class="footer__title" style='margin-bottom:20px;'>Контакты</h2>
<div class="footer__company"  style='margin:10px 0 10px;'>
	<a href='/contacts_ao.pdf' class='footer__link' target=''>АО «ВИНВИНЛЭНД»</a>
</div>
<div class='small' >ИНН 7810961157 ОГРН 1247800054050 г.Санкт-Петербург </div>
<div class="footer__company" style='margin:10px 0 10px;'>Разработка программного обеспечения</div>
<!--
<a class="footer__link" href="tel:8124251296">(812) 425-12-96</a>
-->

<span id="email" style='margin:10px 0 10px;'></span>
<script>
	// This function will create a mailto link and display the email
	function displayEmail() {
		var user = "info";
		var domain = "winwinland.ru";
		var emailAddress = user + "@" + domain;
		document.getElementById("email").innerHTML = `<a  class="footer__link" href="mailto:${emailAddress}">${emailAddress}</a>`;
	}

	displayEmail();
</script>

<div class="footer__links">
  Используя функции платформы Winwinland, я соглашаюсь <br>
  c <a href="https://winwinland.ru/privacypolicy.pdf" target="_blank" rel="noopener noreferrer">Политикой конфиденциальности</a>, <br>
   условиями <a href="https://winwinland.ru/dogovor.pdf" target="_blank" rel="noopener noreferrer">Договора-оферты</a> <br>
   и подтверждаю <a href="https://winwinland.ru/agreement.pdf" target="_blank" rel="noopener noreferrer">Согласие на обработку персональных данных</a>
</div>
<img src="https://winwinland.ru/img/footer-1.svg" alt="img" loading="lazy">
</footer>

</body>
</html>
