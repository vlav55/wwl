<!--
BOTTOM
j
-->

<div class="s_bottom_  p-3 w-75 mx-auto" style='font-size:14px; margin-top:160px;'>
	<hr>
	<div class='s_bottom_card' >
		<div class='row' >
			<div class='col-sm-0 s_bottom_card_b' >
<!--
				<a href='/feedback.php#q' class='' target='_blank'><img src='/images/footer/b.png' class='img-fluid' ></a>
-->
			</div>
			<div class='col-sm-0' >
			</div>
			<div class='col-sm-12 text-center text-secondary' >
				<div class='s_bottom_card_ip' >&copy; <?=date("Y")?> ИП Авштолис В.И. ИНН 380506954258
					Россия, г.Санкт-Петербург
				</div>
				<div class='s_bottom_card_text_'>
					Копирование материалов сайта без разрешения запрещено.
				</div>
				<div class='s_bottom_card_href'>
				<a href='../1/privacypolicy.pdf' target='_blank'>Политика конфиденциальности</a> |
				<a href='../1/dogovor.pdf' target='_blank'>Пользовательское соглашение</a> |
				<a href='../1/contacts.pdf' target='_blank'>Контактные данные</a>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
    $(document).ready(function() {
        // Smooth scrolling to anchor links
        $('a[href^="#"]').on('click', function(event) {
            var target = $($(this).attr('href'));
            if (target.length) {
                event.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top
                }, 1000);
            }
        });
    });
</script>


<script>
$(document).ready(function(){
	// Add scrollspy to <body>
	$('body').scrollspy({target: ".navbar", offset: 50});   

	// Add smooth scrolling on all links inside the navbar
	//$("#myNavbar a").on('click', function(event) {
	$('a[href*="#section"]').on('click', function(event) {
	// Make sure this.hash has a value before overriding default behavior
	if (this.hash !== "") {
	  // Prevent default anchor click behavior
	  event.preventDefault();

	  // Store hash
	  var hash = this.hash;

	  // Using jQuery's animate() method to add smooth page scroll
	  // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
	  $('html, body').animate({
		scrollTop: $(hash).offset().top
	  }, 800, function(){

		// Add hash (#) to URL when done scrolling (default click behavior)
		window.location.hash = hash;
	  });
	}  // End if
	});
	$('#datepicker').datepicker({
		locale: 'ru',
		weekStart: 1,
		daysOfWeekHighlighted: "6,0",
        dateFormat: 'dd.mm.yy',		autoclose: true,
		todayHighlight: true,
	});
	$('#datepicker').datepicker("setDate", new Date());
	$('#datepicker1').datepicker({
		weekStart: 1,
		daysOfWeekHighlighted: "6,0",
		autoclose: true,
		todayHighlight: true,
	});
	$('#datepicker1').datepicker("setDate", new Date());
});
</script>


</div>
</body>
</html>
