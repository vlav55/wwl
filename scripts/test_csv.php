<?
// scp -i /var/www/vlav/data/.ssh/id_rsa  ./insales.csv vlav@194.67.117.172:/var/www/vlav/data/www/wwl/scripts/tmp
include "/var/www/vlav/data/www/wwl/inc/db.class.php";

$db=new db('vkt1_230');
//$db=new db('test');
$filename = 'tmp/laser.csv';
$data = []; // Initialize an empty array


// Check if the file exists
if (!file_exists($filename) || !is_readable($filename)) {
    die("Error: File does not exist or is not readable."); // Stop execution if file is not readable
}

// Open the file for reading
if (($handle = fopen($filename, 'r')) !== false) {
    // Read the first row to get the headers
    $headers = fgetcsv($handle, 1000, ','); // Get the first row as headers
    // Loop through each line of the file
    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
        // Check if number of headers matches the number of values in the row
        if (count($headers) === count($row)) {
            // Combine headers with each row to create an associative array
            //$db->print_r($row);
            $data[] = array_combine($headers, $row);
        } else {
            // Handle the case where the number of elements does not match
            echo "Warning: Row does not match header count and will be skipped. headers=".count($headers)." row=".count($row)."<br>";
            echo "Row: " . implode(', ', $row) . "<br>"; // Optional: Output the row for debugging
            foreach($headers AS $key=>$val) {
				print "$key=>$val row={$row[$key]} <br>";
			}
        }
    }
    fclose($handle); // Close the file
} else {
    die("Error: Could not open the file."); // Handle error in opening the file
}

print "<br>READY ".sizeof($data)." records<br>";

//$db->print_r($data);
$n=1;
foreach($data AS $r) {
	$comm="[Категории] => {$r['Категории']}
[Дата рождения] => {$r['Дата рождения']}
[Потратил, ₽] => {$r['Потратил, ₽']}
[Оплатил, ₽] => {$r['Оплатил, ₽']}
[Пол] => {$r['Пол']}
[Карта] => {$r['Карта']}
[Скидка] => {$r['Скидка']}
[Последний визит] => {$r['Последний визит']}
[Первый визит] => {$r['Первый визит']}
[Чаевые] => {$r['Чаевые']}
[Количество посещений] => {$r['Количество посещений']}
[Комментарий] => {$r['Комментарий']}
";


	//$db->print_r($comm);
	$r1=[
		'first_name'=>$r['Имя'],
		'last_name'=>$r['Фамилия'],
		'phone'=>$r['Телефон'],
		'email'=>$r['Email'],
		'razdel'=>'1', //default 4 (D)  will added
		'comm1'=>intval($r['Оплатил, ₽']) ? $r['Оплатил, ₽']."р. ".$r['Последний визит']:"",
	];
	$uid=$db->cards_add($r1,true);
	//$db->save_comm($uid,0,$comm,0);
	$db->tag_add($uid,3);
	print "uid=$uid <br>";
	//~ break;
}

print "<br>OK";
?>
