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
          <img src="/img/rates-1.svg" alt="img" loading="lazy">
          <span>Тарифы</span>
        </h2>
        <div class="rates__bottom-hidden">
          <img src="/img/rates-5.svg" alt="img" loading="lazy">
          <span>2 недели в подарок при первом подключении!</span>
        </div>
        <div class="swiper-rates">
          <div class="rates__items">
            <div class="slide-rates">
				<?
					$t=0;
					$product_id=32;
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
						$spec= "<div style='margin:20px; padding:10px;background-color:#b9efff;color:#000;line-height:1.2;border-radius:8px;' >
							$name, у вас действует спец предложение до $dt2 <br>
							Цена ".$price_striked."р. снижена до ".$price."р.
							</div>";
					}
				?>
              <a class="rates__item"
                href="order.php?s=0&t=<?=$t?>&product_id=<?=$product_id?>&uid=<?=$uid_md5?>">
                <div class="rates__item-img">
                  <img src="/img/rates-1.png" alt="img" loading="lazy">
                </div>
                <div class="rates__item-text">
                  <?=$descr?>
                </div>
                <div class="rates__item-number blue">ПОДКЛЮЧЕНИЕ</div>
                <div class="rates__item-discount">
                  <span class="rates__item-old">
                    <?=formatNumber($price_striked)?>₽
                  </span>
                  <span class="rates__item-new">
                    <?=formatNumber($price)?>&nbsp;₽
                  </span>
                </div>
                <?=$spec?>
                <div class="rates__item-link">Оформить</div>

				<?include "section_prices_1.inc.php";?>

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
						$spec= "<div style='margin:20px; padding:10px;background-color:#ffd2b9;color:#000;line-height:1.2;border-radius:8px;' >
							$name, у вас действует спец предложение до $dt2 <br>
							Цена ".$price_striked."р. снижена до ".$price."р.
							</div>";
					}
				?>
              <a class="rates__item"
                href="order.php?s=0&t=<?=$t?>&product_id=<?=$product_id?>&uid=<?=$uid_md5?>">
                <div class="rates__item-img">
                  <img src="/img/rates-2.png" alt="img" loading="lazy">
                </div>
                <div class="rates__item-text">
                  <?=$descr?>
                </div>
                <div class="rates__item-number orange">+НАСТРОЙКА</div>
                <div class="rates__item-discount">
                  <span class="rates__item-old">
                    <?=formatNumber($price_striked)?>₽
                  </span>
                  <span class="rates__item-new">
                    <?=formatNumber($price)?>&nbsp;₽
                  </span>
                </div>
                <?=$spec?>
                <div class="rates__item-link">Оформить</div>

				<?include "section_prices_2.inc.php";?>

<!--
                <div class="rates__item-hidden">
                  <h3 class="rates__item-hidden-title orange">
                    1 месяц
                  </h3>
                  <img src="/img/rates-2.png" alt="img" loading="lazy">
                  <div class="rates__item-hidden-text">
                    Подключение 
                    + <span>внедрение</span>
                  </div>
                  <div class="rates__item-hidden-text">
                    <span style='color:#ffd2b9;' >МЫ ЗА ВАС ВСЕ НАСТРОИМ <br> НЕ НУЖНО НИ В ЧЕМ РАЗБИРАТЬСЯ</span>
                  </div>
                  <ul class="rates__item-hidden-ul">
                    <li class="rates__item-hidden-li">
                      создание партнерской программы с любым алгоритмом расчета вознаграждений
                    </li>
                    <li class="rates__item-hidden-li">
                      создание воронки продаж/чат-ботов/лендингов
                    </li>
                    <li class="rates__item-hidden-li">
                      подключение платежных систем для приема платежей с карт
                    </li>
                     <li class="rates__item-hidden-li">
                      настройка рассылок через чат-боты и емэйл
                    </li>
                   <li class="rates__item-hidden-li">
                      подключение интеграций с CRM системами, интернет магазинами и др сервисами
                    </li>
                  </ul>
                </div>
-->
                
              </a>
            </div>
            <div class="slide-rates">
				<?
					$t=0;
					$product_id=37;
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
						$spec= "<div style='margin:20px; padding:10px;background-color:#ff00c7;color:#000;line-height:1.2;border-radius:8px;' >
							$name, у вас действует спец предложение до $dt2 <br>
							Цена ".$price_striked."р. снижена до ".$price."р.
							</div>";
					}
				?>
              <a class="rates__item"
                href="order.php?s=0&t=<?=$t?>&product_id=<?=$product_id?>&uid=<?=$uid_md5?>">
                <div class="rates__item-img">
                  <img src="/img/rates-3.png" alt="img" loading="lazy">
                </div>
                <div class="rates__item-text">
                  <?=trim(explode(',',$descr)[0])?>
                </div>
                <div class="rates__item-number purple">+ТРЕКИНГ</div>
                <div class="rates__item-discount">
                  <span class="rates__item-old">
                    <?=formatNumber($price_striked)?>₽
                  </span>
                  <span class="rates__item-new">
                    <?=formatNumber($price)?>&nbsp;₽
                  </span>
                </div>
                <?=$spec?>
                <div class="rates__item-link">Оформить</div>

				<?include "section_prices_3.inc.php";?>

<!--
                <div class="rates__item-hidden">
                  <h3 class="rates__item-hidden-title orange">
                    12 месяцев
                  </h3>
                  <img src="/img/rates-2.png" alt="img" loading="lazy">
                  <div class="rates__item-hidden-text">
                    Подключение 
                    + <span>внедрение</span>
                  </div>
                  <div class="rates__item-hidden-text">
                    <span style='color:#ffd2b9;' >МЫ ЗА ВАС ВСЕ НАСТРОИМ <br> НЕ НУЖНО НИ В ЧЕМ РАЗБИРАТЬСЯ</span>
                  </div>
                  <ul class="rates__item-hidden-ul">
                    <li class="rates__item-hidden-li">
                      создание партнерской программы с любым алгоритмом расчета вознаграждений
                    </li>
                    <li class="rates__item-hidden-li">
                      создание воронки продаж/чат-ботов/лендингов
                    </li>
                    <li class="rates__item-hidden-li">
                      подключение платежных систем для приема платежей с карт
                    </li>
                     <li class="rates__item-hidden-li">
                      настройка рассылок через чат-боты и емэйл
                    </li>
                   <li class="rates__item-hidden-li">
                      подключение интеграций с CRM системами, интернет магазинами и др сервисами
                    </li>
                  </ul>
                </div>
-->

              </a>
            </div>
          </div>
        </div>
        <div class="swiper swiper-hidden">
          <div class="swiper-wrapper wrapper-hidden">
            <div class="swiper-slide slide-hidden">
				<?
					$t=0;
					$product_id=32;
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
						$spec= "<div style='margin:20px; padding:10px;background-color:#b9efff;color:#000;line-height:1.2;border-radius:8px;' >
							$name, у вас действует спец предложение до $dt2 <br>
							Цена ".$price_striked."р. снижена до ".$price."р.
							</div>";
					}
				?>
              <a class="rates__item"
                href="order.php?s=0&t=<?=$t?>&product_id=<?=$product_id?>&uid=<?=$uid_md5?>">
                <div class="rates__item-img">
                  <img src="/img/rates-1.png" alt="img" loading="lazy">
                </div>
                <div class="rates__item-text">
                  <?=$descr?>
                </div>
                <div class="rates__item-number blue">ПОДКЛЮЧЕНИЕ</div>
                <div class="rates__item-discount">
                  <span class="rates__item-old">
                    <?=formatNumber($price_striked)?>₽
                  </span>
                  <span class="rates__item-new">
                    <?=formatNumber($price)?>&nbsp;₽
                  </span>
                </div>
				<?=$spec?>
                <div class="rates__item-link">Оформить</div>

				<?include "section_prices_1.inc.php";?>
				
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
						$spec= "<div style='margin:20px; padding:10px;background-color:#ffd2b9;color:#000;line-height:1.2;border-radius:8px;' >
							$name, у вас действует спец предложение до $dt2 <br>
							Цена ".$price_striked."р. снижена до ".$price."р.
							</div>";
					}
				?>
              <a class="rates__item"
                href="order.php?s=0&t=<?=$t?>&product_id=<?=$product_id?>&uid=<?=$uid_md5?>">
                <div class="rates__item-img">
                  <img src="/img/rates-2.png" alt="img" loading="lazy">
                </div>
                <div class="rates__item-text second">
                  <?=$descr?>
                </div>
                <div class="rates__item-number orange">+НАСТРОЙКА</div>
                <div class="rates__item-discount">
                  <span class="rates__item-old">
                    <?=formatNumber($price_striked)?>₽
                  </span>
                  <span class="rates__item-new">
                    <?=formatNumber($price)?>&nbsp;₽
                  </span>
                </div>
                <?=$spec?>
                <div class="rates__item-link">Оформить</div>
                
				<?include "section_prices_2.inc.php";?>
                
              </a>
            </div>
            
            <div class="swiper-slide slide-hidden">
				<?
					$t=0;
					$product_id=37;
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
						$spec= "<div style='margin:20px; padding:10px;background-color:#ff00c7;color:#000;line-height:1.2;border-radius:8px;' >
							$name, у вас действует спец предложение до $dt2 <br>
							Цена ".$price_striked."р. снижена до ".$price."р.
							</div>";
					}
				?>
              <a class="rates__item"
                href="order.php?s=0&t=<?=$t?>&product_id=<?=$product_id?>&uid=<?=$uid_md5?>">
                <div class="rates__item-img">
                  <img src="/img/rates-3.png" alt="img" loading="lazy">
                </div>
                <div class="rates__item-text third">
                  <?=trim(explode(',',$descr)[0])?>
                </div>
                <div class="rates__item-number purple">+ТРЕКИНГ</div>
                <div class="rates__item-discount">
                  <span class="rates__item-old">
                    <?=formatNumber($price_striked)?>₽
                  </span>
                  <span class="rates__item-new">
                    <?=formatNumber($price)?>&nbsp;₽
                  </span>
                </div>
                <?=$spec?>
                <div class="rates__item-link">Оформить</div>

				<?include "section_prices_3.inc.php";?>

              </a>
            </div>
          </div>
        </div>
        <div class="rates__bottom">
          <img src="/img/rates-5.svg" alt="img" loading="lazy">
          <span>2 недели в подарок при первом подключении!</span>
        </div>

        <a class="rates__toggle"><img src="/img/i.svg" alt="toggle" loading="lazy"> <span
            class="rates__toggle-span1">Сравнить тарифы</span> <span class="rates__toggle-span2">Свернуть</span>
        </a>
      </div>
    </section>
