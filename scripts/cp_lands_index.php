<?
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt('vkt');
$res=$db->query("SELECT * FROM 0ctrl WHERE del=0");
$cmd="";
while($r=$db->fetch_assoc($res)) {
	$ctrl_dir=$db->get_ctrl_dir($r['id']);
	//print "$ctrl_dir <br>";
	for($n=1; $n<10; $n++) {
		if($ctrl_dir==1000 && $n==1)
			continue;
		$dir="/var/www/vlav/data/www/wwl/d/$ctrl_dir/$n/";
		if(!file_exists($dir))
			continue;
		$cmd="cp -v /var/www/vlav/data/www/wwl/d/1000/1/index.php $dir";
		print "$cmd <br>";
	}
	//print "<hr>";
}

?>
