<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$filename = 'tmp/nasledniki.csv';
$data = [];
$db=new db('vkt1_74');
//exit;
// Чтение файла и загрузка данных в массив
if (($handle = fopen($filename, 'r')) !== false) {
	$r=fgetcsv($handle, 1000, ',');
	$n=0;
	while (($r = fgetcsv($handle, 1000, ',')) !== false) {

		$db->print_r($r);
		if($n++==10)
			break;
		continue;
		
		list($first_name,$last_name)=explode(" ",$r['0']);
		$phone=$r[1];
		$email=$r[2];
		$save_comm="Импорт базы ".date("d.m.Y");
		$card=[
			'first_name'=>$first_name,
			'last_name'=>$last_name,
			'phone'=>$phone,
			'email'=>$email,
			'comm1'=>$save_comm,
			'razdel'=>15,
		];
		print_r($card);
		print "<hr>";
		$uid=$db->cards_add($card);
		$db->tag_add($uid,3);
		$n++;
		//break;
	}
	fclose($handle);
	print "OK $n <br>";
	
} else
	print "file open error : $filename";

?>
