<?
if(!isset($title))
	$title="";
if(!isset($descr))
	$descr=$title;
if(!isset($og_url))
	$og_url="";
if(!isset($og_image))
	$og_image="";
if(!isset($pixel_ya))
	$pixel_ya="";
if(!isset($pixel_vk))
	$pixel_vk="";
?>
<!DOCTYPE html>
<html>
<head>

	<title><?=$title?></title>
	<meta description="<?=$descr?>">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link href="https://winwinland.ru/favicon.ico" rel="icon">

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
	<link rel="stylesheet" href="https://for16.ru/css/loading_cursor.css">
	<?
	if(isset($css))
		print "<link rel='stylesheet' href='$css'>";
	if(isset($js))
		print "<script src='$js'></script>";
	?>
	<link href="https://fonts.googleapis.com/css?family=PT+Sans&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=PT+Serif&family=Roboto:wght@500&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	<script src="https://winwinland.ru/tube/playerjs.js" type="text/javascript"></script>

	<style type='text/css'>
	
	  .footer {
		background: linear-gradient(90.06deg, #0094ff -1.77%, #ff00c7 99.96%);
		color: #fff;
		font-family: 'Montserrat', sans-serif;
		font-weight: 400;
		font-size: 16px;
		line-height: 15px;
		padding: 25px 15px;
		text-align:center;
	  }
	  .footer a {color: #fff;}

	  .footer__title {
		margin-bottom: 15px;
		font-weight: 800;
		font-size: 24px;
		line-height: 33px;
	  }

	  .footer__company {
		font-size: 14px;
		line-height: 20px;
	  }

	  .footer__link {
		margin: 10px 0 18px;
		font-size: 14px;
		line-height: 16px;
	  }

	  footer img {
		width: 44px;
		height: 24px;
	  }

	  .footer__links {
		margin-bottom: 15px;
		font-size: 11px;
		line-height: 14px;
	  }
	</style>


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

<div id="loadingOverlay" class="loading-overlay d-none">
    <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

	<div class='container' >
		<div class='mt-3' ><img src='https://for16.ru/images/logo-200.png' alt='logo' class=''  ></div>
<?
?>
