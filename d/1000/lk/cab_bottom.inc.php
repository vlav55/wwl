	  </div>
	</section>
<?include "cab3_buy_license_modal.inc.php";?>
  </main>
  
	<?
	?>
  <footer class="mt-5 footer">
    <div class="footer__company"><?=$company?></div>
    <div class="footer__links">
      Используя функции партнерского кабинета, я соглашаюсь <br> c <a href="<?=$pp?>"
        target="_blank" rel="noopener noreferrer">Политикой конфиденциальности</a> и условиями 
        <br>
        <a href="<?=$oferta_referal?>" target="_blank" rel="noopener noreferrer">ДОГОВОРА ОФЕРТЫ ОБ УЧАСТИИ В ПАРТНЕРСКОЙ ПРОГРАММЕ</a>
    </div>
    <img src="https://winwinland.ru/img/footer-1.svg" alt="img" loading="lazy">
  </footer>

  <div class="scrollUp">
    <a href="#service"><img src="https://winwinland.ru/img/arrow-up.svg" alt="scrollUp"> </a>
  </div>

  <div class="login" id="login">
<!--
    <img class="login__img" src="https://winwinland.ru/img/modal-1.svg" alt="img" loading="lazy">
-->
    <img class="login__img" src="<?=$DB200."/".$destinationImage?>" alt="img" loading="lazy">
    <h3 class="login__title">Ваш вопрос</h3>
    <form class="login__form form" action="" enctype="multipart/form-data" method="POST">
      <div class="login__item" id='login_div'>
        <input class="login__name login-input" name="q" type="text" placeholder="">
        <input type='hidden' name='uid' value='<?=$uid?>'>
      </div>
      <button class="login__btn" type="submit" name='send' value='yes'>Отправить</button>
    </form>
  </div>


  <a class="burger" onclick="" href='#' class='' target=''>
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
<!--
  <script src="/d/1000/lk/js/main.js"></script>
-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>

</body>

</html>

