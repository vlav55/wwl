                <div class="rates__item-hidden">
                  <h3 class="rates__item-hidden-title blue">
                    <?=$base_prices[$product_id]['term']?> дней
                  </h3>
                  <img src="/img/rates-1.png" alt="img" loading="lazy">

                  <div class="rates__item-hidden-text">
                    Абонентская плата <br> за использование сервиса WINWINLAND
                  </div>
                  <ul class="rates__item-hidden-ul">
                    <li class="rates__item-hidden-li">
                      Самостоятельное использование платформы
                    </li>
                    <li class="rates__item-hidden-li">
                      Техподдержка по запросу
                    </li>
                    <li class="rates__item-hidden-li">
                      Доступ к полному функционалу платформы, документация
                    </li>
                  </ul>
                  <br><br>
                  <h3 class="rates__item-hidden-title blue">
<!--
                    При оплате подключения на год -
-->
					АБОНЕНТСКАЯ ПЛАТА                   
                  </h3>
                  <div class="rates__item-hidden-li" style='text-align:center;' >( <?=round( ($db->price2_chk($uid, $product_id) ?: $base_prices[$product_id][1]) / 12,0);?>р/мес.) </div>
                </div>
