    <?
	function formatNumber($number) {
	  $number = strrev($number); // Reverse the number
	  $number = str_split($number, 3); // Split into groups of three digits
	  $number = implode('.', $number); // Join the groups with dots
	  $number = strrev($number); // Reverse the number back to its original order
	  return $number;
	}
?>
    <section class="rates" id="rates">
      <div class="rates-container">
        <h2 class="rates__title">
          <img src="img/rates-1.svg" alt="img" loading="lazy">
          <span>Тарифы</span>
        </h2>
        <div class="rates__bottom-hidden">
          <img src="img/rates-5.svg" alt="img" loading="lazy">
          <span>2 недели в подарок при первом подключении!</span>
        </div>
        <div class="swiper-rates">
          <div class="rates__items">
            <div class="slide-rates">
              <?
					$t=0;
					$product_id=30;
					$tm2=$db->get_price_tm2($uid,$product_id);
					$price_striked=$base_prices[$product_id][0];
					$price=$base_prices[$product_id][1];
					$descr=$base_prices[$product_id]['descr'];
					$spec="";
					if($tm2>time()) {
						$name=$db->dlookup("name","cards","uid='$uid'");
						$dt2=date('d.m.Y H:i',$tm2);
						$price_striked=$base_prices[$product_id][0];
						$price=$base_prices[$product_id][2];
						$spec= "<small class='card p-2 bg-light text-white' >
							$name, у вас действует спец предложение до $dt2 <br>
							Цена ".$price_striked."р. снижена до ".$price."р.
							</small>";
					}
				?>
              <a class="rates__item"
                href="https://winwinland.ru/2/order.php?s=0&t=<?=$t?>&product_id=<?=$product_id?>&uid=<?=$uid_md5?>">
                <div class="rates__item-img">
                  <img src="img/rates-1.png" alt="img" loading="lazy">
                </div>
                <div class="rates__item-text">
                  <?=$descr?>
                </div>
                <div class="rates__item-number blue">1 месяц</div>
                <div class="rates__item-discount">
                  <span class="rates__item-old">
                    <?=formatNumber($price_striked)?>₽
                  </span>
                  <span class="rates__item-new">
                    <?=formatNumber($price)?> ₽
                  </span>
                </div>
                <div class="rates__item-link">Оформить</div>
                <div class="rates__item-hidden">
                  <h3 class="rates__item-hidden-title blue">
                    1 месяц
                  </h3>
                  <img src="img/rates-1.png" alt="img" loading="lazy">
                  <div class="rates__item-hidden-text">
                    Абонентская плата <br> за подключение к платформе
                  </div>
                  <ul class="rates__item-hidden-ul">
                    <li class="rates__item-hidden-li">
                      Самостоятельное использование платформы
                    </li>
                    <li class="rates__item-hidden-li">
                      Техподдержка по требованию
                    </li>
                  </ul>
                </div>
              </a>
            </div>
            <div class="slide-rates">
              <?
					$t=0;
					$product_id=33;
					$tm2=$db->get_price_tm2($uid,$product_id);
					$price_striked=$base_prices[$product_id][0];
					$price=$base_prices[$product_id][1];
					$descr=$base_prices[$product_id]['descr'];
					$spec="";
					if($tm2>time()) {
						$name=$db->dlookup("name","cards","uid='$uid'");
						$dt2=date('d.m.Y H:i',$tm2);
						$price_striked=$base_prices[$product_id][0];
						$price=$base_prices[$product_id][2];
						$spec= "<small class='card p-2 bg-light text-white' >
							$name, у вас действует спец предложение до $dt2 <br>
							Цена ".$price_striked."р. снижена до ".$price."р.
							</small>";
					}
				?>
              <a class="rates__item"
                href="https://winwinland.ru/2/order.php?s=0&t=<?=$t?>&product_id=<?=$product_id?>&uid=<?=$uid_md5?>">
                <div class="rates__item-img">
                  <img src="img/rates-2.png" alt="img" loading="lazy">
                </div>
                <div class="rates__item-text">
                  <?=$descr?>
                </div>
                <div class="rates__item-number orange">1 месяц</div>
                <div class="rates__item-discount">
                  <span class="rates__item-old">
                    <?=formatNumber($price_striked)?>₽
                  </span>
                  <span class="rates__item-new">
                    <?=formatNumber($price)?> ₽
                  </span>
                </div>
                <div class="rates__item-link">Оформить</div>
                <div class="rates__item-hidden">
                  <h3 class="rates__item-hidden-title orange">
                    1 месяц
                  </h3>
                  <img src="img/rates-2.png" alt="img" loading="lazy">
                  <div class="rates__item-hidden-text">
                    Подключение к платформе <br>
                    + <span>внедрение</span>
                  </div>
                  <ul class="rates__item-hidden-ul">
                    <li class="rates__item-hidden-li">
                      доработка текстов для бота/лендинга
                    </li>
                    <li class="rates__item-hidden-li">
                      помощь с оформлением сообщества вк/тг
                    </li>
                    <li class="rates__item-hidden-li">
                      подключение системы для приема платежей с карт и оформления рассрочек для клиентов
                    </li>
                    <li class="rates__item-hidden-li">
                      тестирование сервисов/рассылок/бота/ выгрузка статистики
                    </li>
                    <li class="rates__item-hidden-li">
                      рекомендации по воронке продаже на усиление
                    </li>
                    <li class="rates__item-hidden-li">
                      сопровождение **
                    </li>
                    <li class="rates__item-hidden-li">
                      настройка ретаргетинговой рекламы (ВК, Яндекс) ***
                    </li>
                    <li class="rates__item-hidden-li">
                      настройка рекламы для привлечения внешнего трафика (ВК, Яндекс) ****
                    </li>
                  </ul>
                </div>
              </a>
            </div>
            <div class="slide-rates">
              <?
					$t=0;
					$product_id=34;
					$tm2=$db->get_price_tm2($uid,$product_id);
					$price_striked=$base_prices[$product_id][0];
					$price=$base_prices[$product_id][1];
					$descr=$base_prices[$product_id]['descr'];
					$spec="";
					if($tm2>time()) {
						$name=$db->dlookup("name","cards","uid='$uid'");
						$dt2=date('d.m.Y H:i',$tm2);
						$price_striked=$base_prices[$product_id][0];
						$price=$base_prices[$product_id][2];
						$spec= "<small class='card p-2 bg-light text-white' >
							$name, у вас действует спец предложение до $dt2 <br>
							Цена ".$price_striked."р. снижена до ".$price."р.
							</small>";
					}
				?>
              <a class="rates__item"
                href="https://winwinland.ru/2/order.php?s=0&t=<?=$t?>&product_id=<?=$product_id?>&uid=<?=$uid_md5?>">
                <div class="rates__item-img">
                  <img src="img/rates-3.png" alt="img" loading="lazy">
                </div>
                <div class="rates__item-text">
                  <?=$descr?>
                </div>
                <div class="rates__item-number purple">3 месяца</div>
                <div class="rates__item-discount">
                  <span class="rates__item-old">
                    <?=formatNumber($price_striked)?>₽
                  </span>
                  <span class="rates__item-new">
                    <?=formatNumber($price)?> ₽
                  </span>
                </div>
                <div class="rates__item-link">Оформить</div>
                <div class="rates__item-hidden">
                  <h3 class="rates__item-hidden-title purple">
                    3 месяца
                  </h3>
                  <img src="img/rates-3.png" alt="img" loading="lazy">
                  <div class="rates__item-hidden-text">
                    Бизнес пакет с созданием маркетинговой стратегии «под ключ»
                  </div>
                  <ul class="rates__item-hidden-ul">
                    <li class="rates__item-hidden-li">
                      Внедрение и ведение маркетинговой стратегии
                    </li>
                    <li class="rates__item-hidden-li">
                      Все и сразу
                    </li>
                  </ul>
                  <div class="rates__item-hidden-bottom">
                    Оптимально для крупных компаний, бла-бла-бла. Ну для тех, кому 300К в 3 месяца, что пыль..
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
        <div class="swiper swiper-hidden">
          <div class="swiper-wrapper wrapper-hidden">
            <div class="swiper-slide slide-hidden">
              <?
					$t=0;
					$product_id=30;
					$tm2=$db->get_price_tm2($uid,$product_id);
					$price_striked=$base_prices[$product_id][0];
					$price=$base_prices[$product_id][1];
					$descr=$base_prices[$product_id]['descr'];
					$spec="";
					if($tm2>time()) {
						$name=$db->dlookup("name","cards","uid='$uid'");
						$dt2=date('d.m.Y H:i',$tm2);
						$price_striked=$base_prices[$product_id][0];
						$price=$base_prices[$product_id][2];
						$spec= "<small class='card p-2 bg-light text-white' >
							$name, у вас действует спец предложение до $dt2 <br>
							Цена ".$price_striked."р. снижена до ".$price."р.
							</small>";
					}
				?>
              <a class="rates__item"
                href="https://winwinland.ru/2/order.php?s=0&t=<?=$t?>&product_id=<?=$product_id?>&uid=<?=$uid_md5?>">
                <div class="rates__item-img">
                  <img src="img/rates-1.png" alt="img" loading="lazy">
                </div>
                <div class="rates__item-text">
                  <?=$descr?>
                </div>
                <div class="rates__item-number blue">1 месяц</div>
                <div class="rates__item-discount">
                  <span class="rates__item-old">
                    <?=formatNumber($price_striked)?>₽
                  </span>
                  <span class="rates__item-new">
                    <?=formatNumber($price)?> ₽
                  </span>
                </div>
                <div class="rates__item-link">Оформить</div>
                <div class="rates__item-hidden">
                  <h3 class="rates__item-hidden-title blue">
                    1 месяц
                  </h3>
                  <img src="img/rates-1.png" alt="img" loading="lazy">
                  <div class="rates__item-hidden-text">
                    Абонентская плата <br> за подключение к платформе
                  </div>
                  <ul class="rates__item-hidden-ul">
                    <li class="rates__item-hidden-li">
                      Самостоятельное использование платформы
                    </li>
                    <li class="rates__item-hidden-li">
                      Техподдержка по требованию
                    </li>
                  </ul>
                </div>
              </a>
            </div>
            <div class="swiper-slide slide-hidden">
              <?
					$t=0;
					$product_id=33;
					$tm2=$db->get_price_tm2($uid,$product_id);
					$price_striked=$base_prices[$product_id][0];
					$price=$base_prices[$product_id][1];
					$descr=$base_prices[$product_id]['descr'];
					$spec="";
					if($tm2>time()) {
						$name=$db->dlookup("name","cards","uid='$uid'");
						$dt2=date('d.m.Y H:i',$tm2);
						$price_striked=$base_prices[$product_id][0];
						$price=$base_prices[$product_id][2];
						$spec= "<small class='card p-2 bg-light text-white' >
							$name, у вас действует спец предложение до $dt2 <br>
							Цена ".$price_striked."р. снижена до ".$price."р.
							</small>";
					}
				?>
              <a class="rates__item"
                href="https://winwinland.ru/2/order.php?s=0&t=<?=$t?>&product_id=<?=$product_id?>&uid=<?=$uid_md5?>">
                <div class="rates__item-img">
                  <img src="img/rates-2.png" alt="img" loading="lazy">
                </div>
                <div class="rates__item-text second">
                  <?=$descr?>
                </div>
                <div class="rates__item-number orange">1 месяц</div>
                <div class="rates__item-discount">
                  <span class="rates__item-old">
                    <?=formatNumber($price_striked)?>₽
                  </span>
                  <span class="rates__item-new">
                    <?=formatNumber($price)?> ₽
                  </span>
                </div>
                <div class="rates__item-link">Оформить</div>
                <div class="rates__item-hidden">
                  <h3 class="rates__item-hidden-title orange">
                    1 месяц
                  </h3>
                  <img src="img/rates-2.png" alt="img" loading="lazy">
                  <div class="rates__item-hidden-text">
                    Подключение к платформе <br>
                    + <span>внедрение</span>
                  </div>
                  <ul class="rates__item-hidden-ul scroll-y ">
                    <li class="rates__item-hidden-li">
                      доработка текстов для бота/лендинга
                    </li>
                    <li class="rates__item-hidden-li">
                      помощь с оформлением сообщества вк/тг
                    </li>
                    <li class="rates__item-hidden-li">
                      подключение системы для приема платежей с карт и оформления рассрочек для клиентов
                    </li>
                    <li class="rates__item-hidden-li">
                      тестирование сервисов/рассылок/бота/ выгрузка статистики
                    </li>
                    <li class="rates__item-hidden-li">
                      рекомендации по воронке продаже на усиление
                    </li>
                    <li class="rates__item-hidden-li">
                      сопровождение **
                    </li>
                    <li class="rates__item-hidden-li">
                      настройка ретаргетинговой рекламы (ВК, Яндекс) ***
                    </li>
                    <li class="rates__item-hidden-li">
                      настройка рекламы для привлечения внешнего трафика (ВК, Яндекс) ****
                    </li>
                  </ul>
                </div>
              </a>
            </div>
            <div class="swiper-slide slide-hidden">
              <?
					$t=0;
					$product_id=34;
					$tm2=$db->get_price_tm2($uid,$product_id);
					$price_striked=$base_prices[$product_id][0];
					$price=$base_prices[$product_id][1];
					$descr=$base_prices[$product_id]['descr'];
					$spec="";
					if($tm2>time()) {
						$name=$db->dlookup("name","cards","uid='$uid'");
						$dt2=date('d.m.Y H:i',$tm2);
						$price_striked=$base_prices[$product_id][0];
						$price=$base_prices[$product_id][2];
						$spec= "<small class='card p-2 bg-light text-white' >
							$name, у вас действует спец предложение до $dt2 <br>
							Цена ".$price_striked."р. снижена до ".$price."р.
							</small>";
					}
				?>
              <a class="rates__item"
                href="https://winwinland.ru/2/order.php?s=0&t=<?=$t?>&product_id=<?=$product_id?>&uid=<?=$uid_md5?>">
                <div class="rates__item-img">
                  <img src="img/rates-3.png" alt="img" loading="lazy">
                </div>
                <div class="rates__item-text third">
                  <?=$descr?>
                </div>
                <div class="rates__item-number purple">3 месяца</div>
                <div class="rates__item-discount">
                  <span class="rates__item-old">
                    <?=formatNumber($price_striked)?>₽
                  </span>
                  <span class="rates__item-new">
                    <?=formatNumber($price)?> ₽
                  </span>
                </div>
                <div class="rates__item-link">Оформить</div>
                <div class="rates__item-hidden">
                  <h3 class="rates__item-hidden-title purple">
                    3 месяца
                  </h3>
                  <img src="img/rates-3.png" alt="img" loading="lazy">
                  <div class="rates__item-hidden-text">
                    Бизнес пакет с созданием маркетинговой стратегии «под ключ»
                  </div>
                  <ul class="rates__item-hidden-ul">
                    <li class="rates__item-hidden-li">
                      Внедрение и ведение маркетинговой стратегии
                    </li>
                    <li class="rates__item-hidden-li">
                      Все и сразу
                    </li>
                  </ul>
                  <div class="rates__item-hidden-bottom">
                    Оптимально для крупных компаний, бла-бла-бла. Ну для тех, кому 300К в 3 месяца, что пыль..
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
        <div class="rates__bottom">
          <img src="img/rates-5.svg" alt="img" loading="lazy">
          <span>2 недели в подарок при первом подключении!</span>
        </div>
        <a class="rates__toggle"><img src="img/i.svg" alt="toggle" loading="lazy"> <span
            class="rates__toggle-span1">Сравнить тарифы</span> <span class="rates__toggle-span2">Свернуть</span>
        </a>
      </div>
    </section>
