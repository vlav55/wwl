<?
$csvFile = '/var/www/vlav/data/www/wwl/d/1416650876/1/lms/selectel.csv'; // Replace 'example.csv' with the path to your CSV file

if (($handle = fopen($csvFile, 'r')) !== false) {
    $header = fgetcsv($handle); // Get the header row
    $arr = [];

    while (($row = fgetcsv($handle)) !== false) {
        $arr[] = array_combine($header, $row); // Combine header with each row
    }

    fclose($handle); // Close the file handle

	$asanas=[];
	foreach ($arr as $item) {
		if (isset($item['NAME'])) {
			$key = $item['NAME'];
			$asanas[$key] = $item; // Copy the entire item to the new array with 'NAME' as the key
		}
	}
    //print nl2br(print_r($asanas,true)); // Output or process the associative array as needed
} else {
    echo "Failed to open asanas csv file for reading.";
}

$list_asanas=[];
if(isset($asanas_included)) {
	if($arr=file($asanas_included)) {
		foreach($arr AS $str) {
			if(preg_match("/transitions/",$str))
				continue;
			$a=pathinfo(trim($str),PATHINFO_FILENAME);
			if(!isset($asanas[$a]))
				continue;
			$list_asanas[]=$a;
		}
	} else
		print ""; //"<p class='alert alert warning' >file $asanas_included not found</p>";
}
?>
<?
if(!isset($title))
	$title="Клуб классической йоги YOGAHELPYOU";
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link href="https://yogahelpyou.com/images/favicon.png" rel="icon">
	<title><?=$title?></title>
	<meta description="<?=$title?>">

	<meta property="og:type" content="website" />
	<meta property="og:title" content="<?=$title?>" />
	<meta property="og:description" content="Клуб классической йоги YOGAHELPYOU" />
	<meta property="og:url" content="https://yogahelpyou.com" />
	<meta property="og:image" content="https://yogahelpyou.com/images/og.jpg" />
	<meta property="vk:image"  content="https://yogahelpyou.com/images/og.jpg" />
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

	<!-- Popper JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

	<!-- Latest compiled JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

	<link rel="stylesheet" href="https://yogahelpyou.ru/1/lms/style.css">
	<link href="https://fonts.googleapis.com/css?family=PT+Sans&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=PT+Serif&family=Roboto:wght@500&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	<style type='text/css'>
		H1 {text-align:center;}
	</style>
		
</head>
<body>
<div class='container-fluid main' >
<div class='p-5' ><a href='/' class='' target=''><img src='https://yogahelpyou.ru/1/lms/logo.png' alt='logo' class='img-fluid' ></a></div>

<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db("vkt1_101");

if(!isset($fl_no_md5_allowed))
	$fl_no_md5_allowed=false;
	
if(isset($fl_catalog_asan_1))
	$fl_no_md5_allowed=true;
	
$md5=array_keys($_GET)[0];
if($fl_no_md5_allowed) {
	if($db->is_md5($md5))
		$uid=0;
} else
	$uid=0;
//print "HERE_$md5 uid=$uid"; exit;
if(1 || !isset($grant_all)) {
	if(($pids=file_get_contents("pid.txt"))!==false) {
		$pid_arr=explode(",",$pids);
	} else {
		print "Error open pid file"; exit;
	}
	$first_pid=intval($pid_arr[0])?intval($pid_arr[0]):0;
	if(!$db->is_md5($md5)) { 
		if(!$fl_no_md5_allowed) {
			//~ print "<div class='alert alert-danger' >Ошибка: ссылка неверна</div>";
			//~ include "../lms_bottom.inc.php";
			//~ exit;
		} else {
			$uid=$_SESSION['vk_uid'];
			$md5=$db->uid_md5($uid);
		}
	} else {
		$uid=$db->dlookup("uid","cards","uid_md5='$md5'");
		$_SESSION['vk_uid']=$uid;
	}
	if(!$uid) {
		//~ print "<div class='alert alert-danger' >Ошибка: 3</div>";
		//~ include "../lms_bottom.inc.php";
		//~ exit;
	}
	$uid_md5=$md5;
	//print $uid;
	print "<div class=' p-2 ml-5' ><a href='https://yogahelpyou.ru/1/lms/?$md5' class='' target=''>Все курсы</a></div>";
	if(!$client_name=$db->dlookup("name","cards","uid='$uid'"))
		$client_name="Дорогой друг";
	print "<div class='badge badge-info p-2 ml-5' >
		Приветствуем, $client_name</div>";
		//print_r($source_id_arr);

	//check access
	$tm_end=0;
	//$db->notify_me(print_r($pid_arr,true));
	foreach($pid_arr AS $pid) {
		if($pid==0)
			break;
		$tm=$db->avangard_tm_end($uid,[$pid]);
		if($tm>$tm_end)
			$tm_end=$tm;
	}
	if($tm_end<time() && $pid>0) {
		if(file_exists('no_access.inc.php'))
			include "no_access.inc.php";
		else {
			//~ print "<div class='alert alert-info mt-3' >$client_name - извините, у вас нет доступа.</div>";
			//~ $tm2=$tm_end; //$db->course_access_finished_tm($uid,$source_id_arr[0]);
			//~ print "<div class='text-secondary font14 pl-3' >окончание ".date("d.m.Y H:i",$tm2)."</div>";
			//~ include "../lms_bottom.inc.php";
			//~ exit;
		}
	}


	if($pid>0 && $tm_end>0)
		print "<div class='p-2 ml-5 font14 text-secondary' >доступно до <br>".date("d.m.Y H:i",$tm_end)."</div>";
	else
		print "<div class='p-2 ml-5 font14 text-secondary' ></div>";
} else {
	$uid_md5=$db->is_md5(array_keys($_GET)[0]) ? array_keys($_GET)[0] : 0;
}

?>
