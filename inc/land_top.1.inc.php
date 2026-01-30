<?
if(!isset($title))
	$title="";
if(!isset($descr))
	$descr=$title;
if(!isset($og_url))
	$og_url="";
if(!isset($og_image))
	$og_image="";

?>
<!DOCTYPE html>
<html>
<head>

	<title><?=$title?></title>
	<meta description="<?=$descr?>">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link href="<?=$favicon?>" rel="icon">

	<meta property="og:type" content="website" />
	<meta property="og:title" content="<?=$title?>" />
	<meta property="og:description" content="<?=$descr?>" />
	<meta property="og:url" content="<?=$og_url?>" />
	<meta property="og:image" content="<?=$og_image?>" />
	<meta property="vk:image"  content="<?=$og_image?>" />
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

	<!-- Latest compiled JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

	<link rel="stylesheet" href="https://for16.ru/css/btn1.css">
	<link rel="stylesheet" href="https://for16.ru/css/style.css">
	<link rel="stylesheet" href="https://for16.ru/css/timer.css">
	<?
	if(isset($css))
		print "<link rel='stylesheet' href='$css'>";
	if(isset($js))
		print "<script src='$js'></script>";
	?>
	<link href="https://fonts.googleapis.com/css?family=PT+Sans&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=PT+Serif&family=Roboto:wght@500&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<script type="text/javascript" >
		function wopen(url) {
			w1=window.open(url, "w1", "fullscreen=yes");
		}
	</script>

<?
?>

<?if(intval($pixel_ya)) {?>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();
   for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
   k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(<?=$pixel_ya?>, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/<?=$pixel_ya?>" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<? } ?>

<?if(!empty($pixel_vk)) {?>
<script type="text/javascript">!function(){var t=document.createElement("script");t.type="text/javascript",t.async=!0,t.src='https://vk.com/js/api/openapi.js?169',t.onload=function(){VK.Retargeting.Init("<?=$pixel_vk?>"),VK.Retargeting.Hit()},document.head.appendChild(t)}();</script><noscript><img src="https://vk.com/rtrg?p=<?=$pixel_vk?>" style="position:fixed; left:-999px;" alt=""/></noscript>
<? } ?>

</head>
<body>
<?
?>
