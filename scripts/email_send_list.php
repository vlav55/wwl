<?
$title="EMAIL SEND LIST";
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "/var/www/vlav/data/www/wwl/inc/unisender.class.php";
include "init.inc.php";
$db=new db('vkt');
$unisender_secret="6ey7pd46wqmd78ez6x71k6nyaozq9e5q9pt8pahy";
$test=false; //send one email only to $test_email
$test_email="info@winwinland.ru";

// Имя CSV-файла
$filename = 'tmp/list.csv';

// Массив для хранения данных
$data = [];
$n=0;
// Открываем файл для чтения
if (($handle = fopen($filename, "r")) !== FALSE) {
    // Считываем первую строку (заголовки)
    $header = fgetcsv($handle, 1000, ",");

    // Проходим по остальным строкам
    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // Создаем ассоциативный массив, используя заголовки в качестве ключей
        $data[] = array_combine($header, $row);
    }

    // Закрываем файл
    fclose($handle);
}

// Выводим полученный массив (для демонстрации)
//print_r($data);


$arr=[];
foreach($data AS $r) {
	if($db->validate_email($r['выпускники'])) {
		$email=trim($r['выпускники']);
		if(!in_array($email,$arr))
			$arr[]=$email;
	}
}
print "Got validated emails from csv: ".sizeof($arr)."<br><br>";

if($test)
	print "TEST MODE to $test_email <br><br>";
else
	print "SENDLIST MODE. Sending started <br>";

if(isset($_GET['email_template'])) {
	$n=0;
	$email_template=substr($_GET['email_template'],0,255);
	$uni=new unisender($unisender_secret,$email_from='',$email_from_name='');
	if(!$test) {
		if($tm_last=file_get_contents("tmp/$email_template")) {
			print "Sending with this template made ".date("d.m.Y H:i",trim($tm_last))."<br>";
			print "Delete this file before tmp/$email_template <br>";
			exit;
		}

		foreach($arr AS $email) {
			$vars=[];
			if($uni->email_by_template($email,$email_template,$vars)) {
				$n++;
				print "$n $email sent OK <br>";
			} else {
				print "$email ERROR ".print_r($uni->res,true)."<br>";
			}
		}
		file_put_contents("tmp/".$email_template,time());
		print "Done <br>";
	} else { //test to $test_email
		$email=$test_email;
		if($uni->email_by_template($email,$email_template,$vars)) {
			print "Test to $email sent OK";
		} else {
			print "$email ERROR ".print_r($uni->res,true)."<br>";
		}
	}
} else
	print "Get parameter ?email_temlate needed <br>";
?>
