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
    <img src="/img/footer-1.svg" alt="img" loading="lazy">
  </footer>


  <script src="https://code.jquery.com/jquery-3.6.4.min.js"
    integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/just-validate@4.2.0/dist/just-validate.production.min.js"></script>
  <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>
  <script src="js/main.js"></script>

<script type="text/javascript">
	$("#login_form_submit").click(function() {
		//console.log("HERE_");
		$('#login_form').attr('action', 'goto_crm.php').submit();
	});
</script>

<script type="text/javascript">
	$("#go_submit").click(function() {
		if($("#client_name").val().trim()=="") {
			alert("Необходимо указать ваше имя!");
		} else if($("#client_phone").val().trim()=="") {
			alert("Укажите, пожалуйста, телефон для связи!");
		} else if(!$("#chk1").is(":checked")) {
			alert("Необходимо согласиться с обработкой персональных данных !");
		} else {
			$('#f1').attr('action', 'https://for16.ru/d/1000/thanks.php').submit();
		}
	});
</script>

<script>
	$(document).ready(function() {
		$('#warningModal').modal('show');
	});
</script>

<script>
$(document).ready(function(){
	var tzOffset = new Date().getTimezoneOffset();
	document.getElementById('tzoffset').value = tzOffset;
});
</script>

  
</body>

</html>
