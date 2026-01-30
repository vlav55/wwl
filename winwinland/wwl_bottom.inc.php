  </main>
  <footer class="footer">
    <h2 class="footer__title" style='margin-bottom:20px;'>Контакты</h2>
    <div class="footer__company"  style='margin:10px 0 10px;'>
		<a href='https://winwinland.ru/contacts_ao.pdf' class='footer__link' target=''>АО «ВИНВИНЛЭНД»</a>
	</div>
	<div class='small' >ИНН 7810961157 ОГРН 1247800054050 г.Санкт-Петербург </div>

    <div class="footer__links" style='margin:10px 0 10px;'><a href='https://winwinland.ru/product.php' class='' target=''>Деятельность в сфере IT</a></div>
<!--
    <a href='https://winwinland.ru/75/' class='' target=''>test</a>
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
    <a class="footer__links" href="tel:8124251296">+7 (812) 425-12-96</a>

    <div class="footer__links">
      Используя функции платформы АО ВИНВИНЛЭНД, я соглашаюсь <br>
      c <a href="https://winwinland.ru/privacypolicy.pdf" target="_blank" rel="noopener noreferrer">Политикой конфиденциальности</a>, <br>
       условиями <a href="https://winwinland.ru/dogovor.pdf" target="_blank" rel="noopener noreferrer">Договора-оферты</a> <br>
       и подтверждаю <a href="https://winwinland.ru/agreement.pdf" target="_blank" rel="noopener noreferrer">Согласие на обработку персональных данных</a>
    </div>
    <img src="https://winwinland.ru/img/footer-1.svg" alt="img" loading="lazy">
  </footer>

  <div class="scrollUp">
    <a href="#service"><img src="https://winwinland.ru/img/arrow-up.svg" alt="scrollUp"> </a>
  </div>

  <div class="login" id="login">
    <img class="login__img" src="https://winwinland.ru/img/modal-1.svg" alt="img" loading="lazy">
    <h3 class="login__title">Панель управления</h3>
    <form id="login_form" class="login__form form" action="https://winwinland.ru/goto_crm.php" enctype="multipart/form-data" method="POST">
      <div class="login__item">
        <input class="login__email login-input" name="email" type="email" placeholder="Эл. почта">
      </div>
<!--
      <div class="login__item">
        <input class="login__password login-input" name="password" type="password" placeholder="Пароль">
      </div>
-->
      <button class="login__btn" type="submit" form="login_form" id="login_form_submit">Войти</button>
    </form>
    <div class="login__agree">
      Чтобы получить доступ, оформите любой
      <a href="#rates" onclick="$.fancybox.close();">Тарифный план</a>
    </div>
  </div>

  <div class="mobile-menu" id="mobile-menu">
    <nav class="mobile-menu__nav" onclick="event.stopPropagation()">
      <ul class="mobile-menu__ul">
        <li class="mobile-menu__li">
          <a class="mobile-menu__link" href="product.php" >О продукте</a>
        </li>
        <li class="mobile-menu__li">
          <a class="mobile-menu__link" href="#rates">Тарифы</a>
        </li>
        <li class="mobile-menu__li">
          <a class="mobile-menu__link" href="partners.php">Партнеры</a>
        </li>
        <li class="mobile-menu__li">
          <a class="mobile-menu__link" href="#questions">Контакты</a>
        </li>
      </ul>
    </nav>
  </div>

  <a class="burger" onclick="event.stopPropagation()">
    <span class="burger__line burger__line-first"></span>
    <span class="burger__line burger__line-second"></span>
    <span class="burger__line burger__line-third"></span>
  </a>

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"
    integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
  <script src="
    https://cdn.jsdelivr.net/npm/just-validate@4.2.0/dist/just-validate.production.min.js
    "></script>
  <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
  <script src="https://winwinland.ru/js/main.js"></script>

	<script type="text/javascript">
		$("#login_form_submit").click(function() {
			//console.log("HERE_");
			$('#login_form').attr('action', 'goto_crm.php').submit();
		});
	</script>

<!--
	<link rel="stylesheet" href="https://cdn.envybox.io/widget/cbk.css">
	<script type="text/javascript" src="https://cdn.envybox.io/widget/cbk.js?wcb_code=f94ec5afad5c76fadf45f19e859fea38" charset="UTF-8" async></script>
-->

</body>

</html>
