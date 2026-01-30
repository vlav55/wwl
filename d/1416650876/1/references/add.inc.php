	<p class='text-center' ><a href='..' class='' target=''>Еще отзывы</a></p>
	<p class='text-center' ><a href='../..#refs' class='' target=''>Вернуться</a></p>
<?

						//~ print "
							//~ <p class='text-center mt-5' ><a href='/trial/?uid=$uid' class='' target=''>Приобрести курс</a></p>
							//~ ";

						//~ if($uid) {
							//~ $r=$db->fetch_assoc($db->query("SELECT * FROM msgs WHERE uid='$uid' AND (source_id=39 OR source_id=13) ORDER BY tm DESC LIMIT 1"));
							//~ if($r) {
								//~ $client_name=$db->dlookup("name","cards","uid='$uid'");
								//~ if( $r['tm']>$db->dt1(time()) ) { //today
									//~ print "<div class='alert alert-info' >$client_name, вы зарегистрированы на бесплатный онлайн семинар,
									//~ трансляцию вы сможете посмотреть завтра (в 12:00 и 20:00 МСК) и после завтра.
									//~ Не пропустите и смотрите внимательно!
									//~ </div>";
								//~ } elseif( $r['tm']>$db->dt1(time()-(1*24*60*60)) ) { //yesterday
									//~ print "<div class='alert alert-info' >$client_name, вы зарегистрированы на бесплатный онлайн семинар,
									//~ трансляция запланирована сегодня (в 12:00 и 20:00 МСК) и завтра.
									//~ Не пропустите и смотрите внимательно!
									//~ </div>";
								//~ } elseif( $r['tm']>$db->dt1(time()-(2*24*60*60)) ) { //2 days
									//~ print "<div class='alert alert-info' >$client_name, вы зарегистрированы на бесплатный онлайн семинар,
									//~ трансляция состоится сегодня (в 12:00 и 20:00 МСК).
									//~ Не пропустите и смотрите внимательно!
									//~ </div>";
								//~ } else { //before
									//~ print "<div class='alert alert-info' >$client_name, вы можете <a href='/trip/?uid=$uid' class='' target=''>зарегистрироваться</a> на бесплатный онлайн семинар,
									//~ трансляция состоится завтра (в 12:00 и 20:00 МСК) и послезавтра.	
									//~ Не пропустите и смотрите внимательно!
									//~ </div>";
								//~ }
								
							//~ } else {
								//~ print "<div class='alert alert-info' >Вы можете <a href='/trip/?uid=$uid' class='' target=''>зарегистрироваться</a> на бесплатный онлайн семинар,
								//~ трансляция состоится завтра (в 12:00 и 20:00 МСК) и послезавтра.	
								//~ Не пропустите и смотрите внимательно!
								//~ </div>";
							//~ }
						//~ } else {
							//~ print "<div class='alert alert-info' >Вы можете <a href='/trip/?uid=$uid' class='' target=''>зарегистрироваться</a> на бесплатный онлайн семинар,
							//~ трансляция состоится завтра (в 12:00 и 20:00 МСК) и послезавтра.
							//~ </div>";
						//~ }

						//~ print "<div class='mt-5 card p-3' >";
						//~ $product_id=1;
						//~ include "../../buy_button.inc.php";
						//~ print "</div>";
?>
