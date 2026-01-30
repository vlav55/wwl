<?
if(preg_match("/Telegram/i",$_SERVER['HTTP_USER_AGENT'])) {
	exit;
}
$pwd_id=1001;
$land_num=4;
include "../top_code.inc.php";

if($uid) {
	$name=($uid)?$db->dlookup("name","cards","uid='$uid'").", ":"";
	$db->save_comm($uid,0,false,50);
	$db->notify($uid,"🔥 Заявка на консультацию");
	$db->mark_new($uid,3);
	//header("Location: https://winwinland.ru/x/?uid=$uid", true, 301);
}

?>
<?include "top.inc.php";?>
  <main>
    <section class="service" id="service" style='padding-top:0;'>
      <div class="service__top">
        <div class="service__top-wrapper">
          <h1 class="service__h1">
            <span>Winwinland —</span> <br />
            сервис для усиления продаж
          </h1>
        </div>
      </div>
    </section>
	  <div class="container">
		<div class="possibilities">
			<?if($uid) {?> 
		  <h2 class="possibilities__title">Успешная запись на консультацию</h2>
		  <h3 class="possibilities__suptitle title">
			<?=$name?> вы успешно подали заявку на консультацию.
			Менеджер скоро свяжется с Вами, чтобы уточнить детали!
			<br>
		  </h3>
		  <?} else {
			  ?>
			  <h2 class="possibilities__title">
				  Укажите ваши контакты
			  </h2>
				<p>Заполните ваши данные и мы сможем вам помочь настроить эффективную партнерскую программу для увеличения продаж. 
				</p>
				  <form id='f1' class="pay form" action="#" enctype="multipart/form-data" method="POST">
					<div class="login__item">
					  <input class="login__name login-input"
						id="client_name"
						name="fio" type="text"
						value="<?=$client_name?>"
						placeholder="ФИО (*)">
					</div>
					<div class="login__item">
					  <input
						id="client_phone"
						class="login__phone_ login-input"
						name="phone"
						type="tel"
						value="<?=$client_phone?>"
						placeholder="Телефон (*)">
					</div>
					<div class="login__item short">
					  <input
						id="client_email"
						class="login__email login-input"
						name="email"
						type="email"
						value="<?=$client_email?>"
						placeholder="Эл. почта">
					</div>
					<div class="pay__text-1">
					</div>
					<div class="pay__checkbox" style='margin-bottom:20px;'>
					  <div class="checkbox-wrapper">
						<input id="chk1" class="input__checkbox" type="checkbox" checked name="agree" />
					  </div>
					  <div class="pay__checkbox-right">
						Отправляя данные, я соглашаюсь
						<a href="https://winwinland.ru/agreement.pdf" target="_blank" rel="noopener noreferrer">
							на обработку персональных данных
						</a>
						и соглашаюсь
						<a href="https://winwinland.ru/privacypolicy.pdf" target="_blank" rel="noopener noreferrer">
							с политикой конфиденциальности
						</a>
					  </div>
					</div>

					<input type="hidden" name="bc" value="<?=$bc?>"/>
					<input type="hidden" name="secret" value="consult"/>
					<input type="hidden" name="land_num" value="<?=$land_num?>"/>
					<input type='text' name='tzoffset' value='0' id='tzoffset' style='display:none;'>
					<input type="hidden" name="go_submit" value="yes"/>
					
					<input type="hidden" name="utm_campaign" value="<?=$utm_campaign?>"/>
					<input type="hidden" name="utm_content" value="<?=$utm_content?>"/>
					<input type="hidden" name="utm_medium" value="<?=$utm_medium?>"/>
					<input type="hidden" name="utm_source" value="<?=$utm_source?>"/>
					<input type="hidden" name="utm_term" value="<?=$utm_term?>"/>
					<input type="hidden" name="utm_ab" value="<?=$utm_ab?>"/>
					<br>
					<button id='go_submit' class="pay__button" type="button">Отправить заявку</button>
				  </form>
			<?}?>
		</div>
	</div>
  </main>

<?if(isset($_GET['err'])) {
	if($_GET['err']=='phone_required')
		$msg="Просьба указать номер телефона";
	elseif($_GET['err']=='email_required')
		$msg="Просьба указать электронную почту";
	else
		$msg="Неверный формат данных";
?>
	<div class="modal fade" id="warningModal" tabindex="-1" role="dialog" aria-labelledby="warningModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="warningModalLabel">Предупреждение</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<?=$msg?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

<? } ?>


<? include "bottom.inc.php"; ?>
