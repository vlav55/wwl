<?
$og_url="https://winwinland.ru/tube/?<?=$video?>";
$title="WinWinLand TUBE $video";
$descr=$title;
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
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

	<script src="https://winwinland.ru/tube/playerjs.js" type="text/javascript"></script>

    <style>
        body, p, div, span, a, button, input, textarea, select, label, h1, h2, h3, h4, h5, h6 {
            font-family: 'Inter', Helvetica, Arial, sans-serif !important;
            font-size: 16px !important;
        }
    </style>
    
</head>
<body>

<div id="loadingOverlay" class="loading-overlay d-none">
    <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<div class='container' >
