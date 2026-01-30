<?
session_start();
//include "top.code.php";
//insta_c=$conversation_id&u

if(!isset($title))
	$title="КЛИЕНТЫ ДЛЯ ЙОГИ И ФИТНЕСА";
if(!isset($descr))
	$descr=$title;
if(!isset($justclick))
	$justclick="";
if(!isset($og_image))
	$og_image="";

include_once "prices.inc.php";
//include_once "func.inc.php";
?>
<!DOCTYPE html>
<html>
<head>

	<title><?=$title?></title>
	<meta description="<?=$descr?>">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link href="" rel="icon">

	<meta property="og:type" content="website" />
	<meta property="og:title" content="<?=$title?>" />
	<meta property="og:description" content="<?=$descr?>" />
	<meta property="og:url" content="" />
	<meta property="og:image" content="<?=$og_image?>" />
	<meta property="vk:image"  content="<?=$og_image?>" />
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

	<!-- Popper JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

	<!-- DatePicker -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.standalone.min.css" rel="stylesheet"/>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
	<style type="text/css">
		.datepicker td, .datepicker th {
			width: 2.5em;
			height: 1.5em;
		}
		.datepicker.dropdown-menu {
			font-size:14px;
		}
   </style>


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

<?//include "top.pixels.php";?>

<meta name="mailru-domain" content="9NWTiLUmFLEFweLm" />
</head>
<body>
