<?
session_start();
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
chdir("/var/www/vlav/data/www/wwl/d/1416650876/");
include "init.inc.php";
$db=new db($database);

$bc=isset($_GET['bc'])?intval($_GET['bc']):0;
$uid=isset($_GET['uid'])?$db->get_uid($_GET['uid']):0;
$uid_md5=$uid ? $db->uid_md5($uid) : 0;

if(!isset($title))
	$title="Онлайн школа йоги YOGAHELPYOU";
if(!isset($descr))
	$descr=$title;
$salt="x2MyGv";
if(!isset($justclick))
	$justclick="";

if(!isset($og_image))
	$og_image="https://yogahelpyou.ru/images/og.jpg";
if(!isset($fb_event))
	$fb_event="";

$res=$db->query("SELECT * FROM product WHERE del=0");
$base_prices=array();
while($r=$db->fetch_assoc($res)) {
	$base_prices[$r['id']]=[
		0=>$r['price0'],
		1=>$r['price1'],
		2=>$r['price2'],
		'descr'=>$r['descr'],
		'term'=>$r['term'],
		'stock'=>$r['stock'],
		'jc'=>$r['jc'],
		'sp'=>0,
		'sp_template'=>$r['sp_template'],
		'source_id'=>$r['source_id'],
		'razdel'=>$r['razdel'],
		'tag_id'=>$r['tag_id'],
		'use'=>$r['in_use'],
		'vid'=>$r['vid'],
		'installment'=>$r['installment'],
		'fee_1'=>$r['fee_1'],
		'fee_2'=>$r['fee_2'],
	];
}

function shop_get_price($uid,$product_id,$status=false) {
	global $db, $base_prices,$price_per_month;
	$striked=number_format($base_prices[$product_id][0], 0, '', '&nbsp;').'₽';
	if($tm2=$db->price2_chk_timeto($uid,$product_id)) {
		$dt2=date("d.m.Y H:i",$tm2);
		$name=$db->dlookup("name","cards","uid='$uid'")." ".$db->dlookup("surname","cards","uid='$uid'");
		$price2=number_format($base_prices[$product_id][2], 0, '', '&nbsp;').'₽';
		$price1=number_format($base_prices[$product_id][1], 0, '', '&nbsp;').'₽';
		$msg="<div class='alert alert-success' style='font-size:14px;'>$name
		для вас до $dt2 действует спецпредложение, новая цена $price2 вместо $striked
		</div>";
		$price=$price2;
		$price_per_month=number_format($base_prices[$product_id][2]/$base_prices[$product_id]['term']*30, 0, '', '&nbsp;').'₽';
	} else {
		$msg="";
		$price=number_format($base_prices[$product_id][1], 0, '', '&nbsp;').'₽';
		$price_per_month=number_format($base_prices[$product_id][1]/$base_prices[$product_id]['term']*30, 0, '', '&nbsp;').'₽';
	}
	return "$msg
		<div class='ROBOTO' >
		<del><span class='text-decoration-line-through'>$striked</span></del>
		<span>$price</span>
		</div>
	";
}

?>
<!DOCTYPE html>
<html>
<head>

	<title><?=$title?></title>
	<meta description="<?=$descr?>">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link href="https://yogahelpyou.ru/images/favicon.ico" rel="icon">

	<meta property="og:type" content="website" />
	<meta property="og:title" content="<?=$title?>" />
	<meta property="og:description" content="<?=$descr?>" />
	<meta property="og:url" content="https://yogahelpyou.com" />
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

<!--
	<link href="https://vjs.zencdn.net/7.4.1/video-js.css" rel="stylesheet">
-->
	<link rel="stylesheet" href="https://for16.ru/d/1416650876/1/css/btn1.css">
	<link rel="stylesheet" href="https://for16.ru/d/1416650876/1/css/style.css">
	<link rel="stylesheet" href="https://for16.ru/d/1416650876/1/css/refs1.css">
	<link rel="stylesheet" href="https://for16.ru/d/1416650876/1/css/refs1_video.css">
	<?
	if(isset($css))
		print "<link rel='stylesheet' href=\"$css\">\n";
	if(isset($css_style))
		print "<style type='text/css'>
		$css_style
		</style>
		";
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

	<!-- Yandex.Metrika counter -->
	<script type="text/javascript" >
	   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
	   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
	   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

	   ym(54452332, "init", {
			clickmap:true,
			trackLinks:true,
			accurateTrackBounce:true,
			webvisor:true
	   });
	</script>
	<noscript><div><img src="https://mc.yandex.ru/watch/54452332" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
	<!-- /Yandex.Metrika counter -->

	<!-- VK -->
	<script type="text/javascript">!function(){var t=document.createElement("script");t.type="text/javascript",t.async=!0,t.src="https://vk.com/js/api/openapi.js?161",t.onload=function(){VK.Retargeting.Init("VK-RTRG-385987-5XLJ"),VK.Retargeting.Hit()},document.head.appendChild(t)}();</script><noscript><img src="https://vk.com/rtrg?p=VK-RTRG-385987-5XLJ" style="position:fixed; left:-999px;" alt=""/></noscript>


	<!-- Rating Mail.ru counter -->
	<script type="text/javascript">
	var _tmr = window._tmr || (window._tmr = []);
	_tmr.push({id: "3166280", type: "pageView", start: (new Date()).getTime(), pid: "USER_ID"});
	(function (d, w, id) {
	  if (d.getElementById(id)) return;
	  var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true; ts.id = id;
	  ts.src = "https://top-fwz1.mail.ru/js/code.js";
	  var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};
	  if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
	})(document, window, "topmailru-code");
	</script><noscript><div>
	<img src="https://top-fwz1.mail.ru/counter?id=3166280;js=na" style="border:0;position:absolute;left:-9999px;" alt="Top.Mail.Ru" />
	</div></noscript>
	<!-- //Rating Mail.ru counter -->

</head>
<body>
<div class='container-fluid p-0 m-0' >
