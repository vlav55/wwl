<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db("vkt");
$arr=[];
$n=0;
if (($handle = fopen("2gis_ya.csv", 'r')) !== false) {
    // Read each line of the file until the end
    while (($row = fgetcsv($handle,0,';')) !== false) {
        // Add the row to the data array
        $arr[] = $row;
        //~ if($n++==100)
			//~ break;
    }

    // Close the file handle
    fclose($handle);
}

//print nl2br(print_r($arr,true));
$out="phone,email\n";
foreach($arr AS $r) {
	$arr0=explode(",",$r[0]);
	//print_r($arr1);
	foreach($arr0 AS $r0) {
		if($mob=$db->check_mob(trim($r0))) {
			$out.=trim($mob).",\n";
			$n++;
		}
	}

	//~ $arr1=explode(",",$r[1]);
	//~ //print_r($arr1);
	//~ foreach($arr1 AS $r1) {
		//~ if($db->validate_email(trim($r1)))
			//~ $out.=",".trim($r1)."\n";
	//~ }
}
file_put_contents("2gis_ya_mob.txt",$out);
print "n=$n <a href='2gis_ya_mob.txt' class='' target=''>link</a>";

?>
