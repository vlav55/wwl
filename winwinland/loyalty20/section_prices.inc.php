    <?
	function formatNumber($number) {
	  $number = strrev($number); // Reverse the number
	  $number = str_split($number, 3); // Split into groups of three digits
	  $number = implode('.', $number); // Join the groups with dots
	  $number = strrev($number); // Reverse the number back to its original order
	  return $number;
	}
	$bc_=$bc ? "&bc=$bc" : "";

	function print_price($product_id,$img,$color,$mobile=false) {
		global $base_prices,$db,$uid,$uid_md5;
		$c=$mobile ? "swiper-slide slide-hidden" : "slide-rates";
		$font_size=$mobile ? '0.8' : '1.2';
		if($base_prices[$product_id]['term']<=31)
			$suf='';
		elseif($base_prices[$product_id]['term']<=90)
			$suf='а';
		else
			$suf='ев';
		?>
		<div class="<?=$c?>">
			<?
				$t=0;
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
			href="../order.php?product_id=<?=$product_id?>&uid=<?=$uid_md5?><?=$bc_?>" >
			<div class="rates__item-img">
			  <img src="/img/<?=$img?>" alt="img" loading="lazy">
			</div>
			<div class="rates__item-text">
			  <?=$descr?>
			</div>
			<div class="rates__item-number <?=$color?>"  style='margin-bottom:10px;'><?=round($base_prices[$product_id]['term']/30,0)?> месяц<?=$suf?></div>
			<div class="rates__item-number" style='color:black;font-size:<?=$font_size?>rem;'><?=round($price/($base_prices[$product_id]['term']/30),0)?>₽/мес</div>
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

			<?//include "section_prices_1.inc.php";?>

		  </a>
		</div>
        <?
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
          <span>Абонетская плата фиксирована и не зависит от количества лидов, партнеров, оферов или других факторов.</span>
        </div>
        <div class="swiper-rates">
          <div class="rates__items">
			<?=print_price(132,'rates-3.png','purple')?>
			<?=print_price(135,'rates-2.png','orange')?>
			<?=print_price(131,'rates-1.png','blue')?>
			<?=print_price(130,'rates-4.svg','violet')?>
          </div>
        </div>
        <div class="swiper swiper-hidden">
          <div class="swiper-wrapper wrapper-hidden">
			<?=print_price(132,'rates-3.png','purple',true)?>
			<?=print_price(135,'rates-2.png','orange',true)?>
			<?=print_price(131,'rates-1.png','blue',true)?>
			<?=print_price(130,'rates-4.svg','violet',true)?>
          </div>
        </div>
        
        <div class="rates__bottom">
          <img src="/img/rates-5.svg" alt="img" loading="lazy">
          <span>Абонетская плата фиксирована и не зависит от количества лидов, партнеров, оферов или других факторов.</span>
        </div>

        <a class="rates__toggle"><img src="/img/i.svg" alt="toggle" loading="lazy">
<!--
			<span class="rates__toggle-span1">Сравнить тарифы</span>
			<span class="rates__toggle-span2">Свернуть</span>
-->
        </a>
     </div>
    </section>
