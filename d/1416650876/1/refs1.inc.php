<?
	if(@$refs_sorting=="num1")
		$q="SELECT * FROM refs WHERE del=0 ORDER BY num1 DESC, num DESC";
	else
		$q="SELECT * FROM refs WHERE del=0 ORDER BY num DESC";
	$res=$db->query("$q");
	$nails="<div class='row'>";
	$n=1;
	while($r=$db->fetch_assoc($res)) {
		$nails.= "<div class='col-2 p-0 d-flex align-items-end' >
					<a href='javascript:showSlides(slideIndex = $n);void(0);' class='' target=''>
						<img src='../1/images/ref_pics/{$r['pic']}'
							style_='height:60px;'
							style__='width: calc(100% / 5); height: auto; object-fit: cover;'
							class='img-fluid' >
					</a>
				</div>";
		$n++;
	}
	$nails.="</div>";
	$nails.= "<p><a href='../1/references/'	class='font18' target='_blank'  style='color: #777_;' class='pl-3' onclick='plusSlides(1)'>еще отзывы</a></p>";
	$res=$db->query("$q");
	$cnt=$db->num_rows($res);
 
?>
	<!-- Slideshow container -->
	<div class='container-fluid' >
	<div class="slideshow-container">
		<?while($r=$db->fetch_assoc($res)) {
			$ref_text=$r['ref_text'];
			$len=800; $max_len=$len+50;
			if(strlen($ref_text)>$len) {
					while(substr($ref_text,$len,1)!=" ") {
						$len++;
						if($len>$max_len)
							break;
					}
					$ref_text=nl2br(substr($ref_text,0,$len))."
					<a href='javascript:wopen(\"../1/refs_w.php?id={$r['id']}\")' class='' target='' >... читать полностью</a>";
			}
			?>
			<div class='mySlides' style='padding-top:30px;'>

				<div style='margin-bottom:20px;'>
					<img src='../1/images/ref_pics/<?=$r['pic']?>'
						class="rounded-circle"
						style='width:135px;height:auto ;'
						>
				</div>

				<q class='PS font20' ><?=$r['ref_problem']?></q>
				<p class='BS author' >
					- <?=$r['ref_name']?>
				<?if(!empty($r['age']) && $r['age']!=0) {?>, <?=$r['age']?><?}?>
				</p>
				<div class='BS read_all'><a href='javascript:wopen("../1/refs_w.php?id=<?=$r['id']?>")' class='' target=''>
					Читать полностью &gt&gt;
				</a></div>
			</div> 
		<?}?>

	  <!-- Next/prev buttons -->
	  <a style='' class="refs_prev text-secondary" onclick="plusSlides(-1)"><i class='far fa-caret-square-left font32'></i></a>
	  <a style='' class="refs_next text-secondary" onclick="plusSlides(1)"><i class='far fa-caret-square-right font32'></i></a>

	  <div class='p-2' >
		<?=$nails?>
	  </div>
	</div>
	</div>
<?
?>

<script>
	var slideIndex = 1;
	showSlides(slideIndex);

	function plusSlides(n) {
	  showSlides(slideIndex += n);
	}

	function currentSlide(n) {
	  showSlides(slideIndex = n);
	}

	function showSlides(n) {
	  var i;
	  var slides = document.getElementsByClassName("mySlides");
//	  var dots = document.getElementsByClassName("dot");
	  if (n > slides.length) {slideIndex = 1} 
		if (n < 1) {slideIndex = slides.length}
		for (i = 0; i < slides.length; i++) {
		  slides[i].style.display = "none"; 
		}
		//~ for (i = 0; i < dots.length; i++) {
		  //~ dots[i].className = dots[i].className.replace(" active", "");
		//~ }
	  slides[slideIndex-1].style.display = "block"; 
//	  dots[slideIndex-1].className += " active";
	}
</script>
