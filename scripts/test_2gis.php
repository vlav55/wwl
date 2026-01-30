<?
// scp -i /var/www/vlav/data/.ssh/id_rsa  ./insales.csv vlav@194.67.117.172:/var/www/vlav/data/www/wwl/scripts/tmp
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db('test');
$filename = '2gis/2GIS.csv';
$data = []; // Initialize an empty array

// Check if the file exists
if (!file_exists($filename) || !is_readable($filename)) {
    die("Error: File does not exist or is not readable."); // Stop execution if file is not readable
}

// Open the file for reading
if (($handle = fopen($filename, 'r')) !== false) {
    // Read the first row to get the headers
    $headers = fgetcsv($handle, 1000, ';'); // Get the first row as headers
    // Loop through each line of the file
    $num_err=0;
    $n=0;
    while (($row = fgetcsv($handle, 1000, ';')) !== false) {
        // Check if number of headers matches the number of values in the row
        if (count($headers) === count($row)) {
            // Combine headers with each row to create an associative array
           // $db->print_r($row);
            if(strpos(mb_strtolower($row[1]),"бухг")===false )
				continue;
        //    print mb_strtolower($row[1])."<br>";
            $data[] = array_combine($headers, $row);
        } else {
            $num_err++;
        }
        //~ if($n++==10000)
			//~ break;
    }
    fclose($handle); // Close the file
} else {
    die("Error: Could not open the file."); // Handle error in opening the file
}

print "<br>READY ".sizeof($data)." records $num_err errors<br>";

// Specify the CSV file name
$outputFilename = '2gis/accounting.csv';

// Open the file for writing
if (($handle = fopen($outputFilename, 'w')) !== false) {
    // Write headers to the output CSV
    fputcsv($handle, $headers, ';'); // Use ';' as the delimiter

    // Write each filtered data row to the output CSV
    foreach ($data as $row) {
        fputcsv($handle, $row, ';');
    }

    fclose($handle); // Close the output file
    print "Data has been written to <a href='$outputFilename' class='' target='_blank'>$outputFilename</a> successfully.";
} else {
    die("Error: Could not create the output file."); // Handle error in creating the file
}
exit;


$n=0;
foreach($data AS $r) {
	$db->print_r($r);
	print "<hr>";
	if($n++==10)
		break;
	//~ $email=($db->validate_email($r['email'])) ? trim($r['email']) : "";
	//~ $mob='';
	//~ if (preg_match('/\+(\d+)/', $r['wa'], $m)) {
		//~ $mob=$db->check_mob($m[1]);
	//~ }
	//~ $db->query("INSERT INTO insales_1 SET
		//~ url='".$db->escape(trim($r['url']))."',
		//~ email='".$db->escape($email)."',
		//~ mob='".$db->escape($mob)."',
		//~ tg='".$db->escape(trim($r['tg']))."',
		//~ vk='".$db->escape(trim($r['vk']))."'
		//~ ");
	//break;
}
print "Ok";
?>
