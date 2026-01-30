<?
class bs {
	function button($style,$text, $submit=false, $onclick="", $name="", $value="", $id="") {
		$type=(!$submit)?"button":"submit";
		return "<button type='$type' onclick='$onclick' name='$name' value='$value' id='$id' class='btn btn-$style'>$text</button>";
	}
	function button_close($pos="right",$text="Close") {
	  return "<div class='text-$pos'><button type='submit' class='btn btn-warning' onclick='window.close();return(false);'>$text</button></div>";
	}
	function button_add($text="Добавить", $style="primary") {
		return "<a href='?add=yes'><button type='submit' class='btn btn-$style' >$text</button></a>";
	}
	function button_view($text="Список", $style="info") {
		return "<a href='?view=yes'><button type='submit' class='btn btn-$style' >$text</button></a>";
	}
	function button_href($text="Text", $href="", $style="primary") {
		return "<a href='$href'><button type='submit' class='btn btn-$style' >$text</button></a>";
	}
	
	function table($ths=array(),$bordered=true, $th_class="") {
		$bordered=($bordered)?"table-bordered":"";
		$out= "<table class='table table-hover $bordered table-striped table-responsive'>";
		if(sizeof($ths)>0) {
			$out.= "<thead class='$th_class'><tr class='$th_class'>";
			foreach($ths AS $th) {
				$out.= "<th class='$th_class'>$th</th>";
			}
			$out.= "</tr></thead>";
		}
		return $out;
	}
	function panel($type, $heading, $content, $footer=false, $style="",$class="") {
		$out= "<div class='panel panel-$type $class' style='$style'>\n
		<div class='panel-heading'>$heading</div>\n
		<div class='panel-body'>$content</div>\n";
		if($footer)
			$out.= "<div class='panel-footer'>$footer</div>\n"; 
		$out.= "</div>\n";
		return $out;
	}
	function media($pic,$h4,$txt,$pic_size=60,$h4_class="") {
		$out="<div class='media'>
			<div class='media-left'>
			<img src='$pic' class='media-object' style='width:".$pic_size."px'>
			</div>
			<div class='media-body'>
			<h4 class='media-heading $h4_class'>$h4</h4>
			$txt
			</div>
		</div>";
		return $out;
	}
}
?>
