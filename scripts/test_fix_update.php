<?
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt('vkt');
$res=$db->query("SELECT * FROM 0ctrl WHERE del=0");
$dbs=[];
while($r=$db->fetch_assoc($res)) {
	$dbs[$r['id']]=$db->get_ctrl_database($r['id']);
}

$companies=[];
foreach($dbs AS $ctrl_id => $database) {
	$db->connect($database);
	$cnt=$db->fetch_assoc($db->query("SELECT COUNT(id) AS cnt FROM cards WHERE del=0 AND name=''"))['cnt'];
	if(!$cnt)
		continue;
	$companies[]=$database;
	print "$ctrl_id $database 	$cnt<br>";
}
//print_r($companies);
//exit;

$arr=file("tmp/tree.txt");
//print nl2br(print_r($arr,true));
$cmd="";
$cmd1="";
$cmd_drop="";
$arr1=[];
foreach($companies AS $c) {
	foreach($arr AS $r) {
		if(preg_match("/all-(\d\d\d\d\.\d\d\.\d\d)$/",$r,$m)) {
			$dir=trim($r)."/mysql_day";
			//print "$dir <br>";
			$db_name="vkt_test_".str_replace(".", "_",$m[1])."_$c";

			//print "$c $db_name <br>";
			$arr1[$c][]=$db_name;
			
			//~ foreach($companies AS $c) {
				//~ $db_name="vkt_test_".str_replace(".", "_",$m[1])."_$c";
				//~ $cmd.="CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;\n";
				//~ $cmd.="GRANT SELECT , INSERT , UPDATE , DELETE, ALTER ON $db_name.* TO vlav@localhost;\n";
				//~ $cmd.="set character_set_results='utf8mb4';\n";
				//~ $cmd.="set collation_connection='utf8mb4_general_ci';\n";
				//~ $cmd.="set character_set_client='utf8mb4';\n";
				//~ $cmd_drop.="DROP DATABASE `$db_name`\n";
				//~ //print nl2br($cmd);
				//~ $fname="$dir/$c.sql";
				//~ $cmd1.="mysql -uroot -pvlaV^fokovA-mysql $db_name <$fname\n";
				//print nl2br($cmd1);
				//break;
			//~ }
			//break;
		}
	}
}

//print nl2br(print_r($arr1,true));
foreach($companies AS $c) {
	$c='vkt1_87';
	$db->connect($c);
	$res=$db->query("SELECT * FROM cards WHERE del=0 AND name='' AND surname=''");
	$n=0;
	while($r=$db->fetch_assoc($res)) {
		$id=$r['id'];
		//print "id=$id <br>";
		//print_r($arr1[$c]);
		foreach($arr1[$c] AS $db_name) {
			//print "$c $db_name <br>";
			$db->connect($db_name);
			$r1=$db->fetch_assoc($db->query("SELECT * FROM cards WHERE id='$id'"));
			if(!empty($r1['name'])) {
				print "$n FOUND $id={$r1['id']} uid={$r1['uid']} $c $db_name {$r1['name']} {$r1['surname']} {$r1['mob_search']} {$r1['email']} {$r1['telegram_id']}={$r['telegram_id']}<br>";
				$db->connect($c);
				$q="UPDATE cards SET
					name='".$db->escape($r1['name'])."',
					surname='".$db->escape($r1['surname'])."',
					mob='".$db->escape($r1['mob_search'])."',
					mob_search='".$db->escape($r1['mob_search'])."',
					email='".$db->escape($r1['email'])."',
					telegram_id='".$db->escape($r1['telegram_id'])."',
					vk_id='".$db->escape($r1['vk_id'])."'
					WHERE id='$id'
					";
				print "$q <br>";
				//$db->query($q);
				$db->connect($db_name);
				$n++;
				break;
			}
		}
	}
	break;
}
//~ file_put_contents("tmp/cmd.sql",$cmd);
//~ file_put_contents("tmp/cmd_drop.sh",$cmd_drop);
//~ file_put_contents("tmp/cmd1.sh",$cmd1);
print "OK";
?>
