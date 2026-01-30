<?
$title=mb_convert_case(mb_substr($_GET['title'],0,512),MB_CASE_UPPER);
$m3u8="/".substr($_GET['hls'],0,2048);
include "../lms_top.inc.php";
print "<div class='container' >";
print "<h4 class='text-center' >$title</h4>";
print "<p class='text-center' ><a href='javascript:history.back()' class='' target=''>вернуться</a></p>";
include "../video.inc.php";

print "</div>";
include "../lms_bottom.inc.php";
?>
