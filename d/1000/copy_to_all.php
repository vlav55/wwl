<?
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt('vkt');
$res=$db->query("SELECT * FROM 0ctrl WHERE del=0 AND id>1");
$cmd="";
while($r=$db->fetch_assoc($res)) {
	$ctrl_dir=$db->get_ctrl_dir($r['id']);
	$cp="cp -uv /var/www/html/pini/1info/vkt/db1/*.php /var/www/html/pini/1info/vkt/d/$ctrl_dir";
	print "$cp <br>";
	$cmd.=$cp."\n";
	$cp="cp -uv /var/www/html/pini/1info/vkt/db1/lk/*.php /var/www/html/pini/1info/vkt/d/$ctrl_dir/lk";
	$cmd.=$cp."\n";
	print "$cp <br>";
	if(!file_exists("/var/www/html/pini/1info/vkt/d/$ctrl_dir/1"))
		mkdir("/var/www/html/pini/1info/vkt/d/$ctrl_dir/1");
	if(!file_exists("/var/www/html/pini/1info/vkt/d/$ctrl_dir/2"))
		mkdir("/var/www/html/pini/1info/vkt/d/$ctrl_dir/2");
	$cp="cp -uv /var/www/html/pini/1info/vkt/db1/1/*.php /var/www/html/pini/1info/vkt/d/$ctrl_dir/1";
	$cmd.=$cp."\n";
	print "$cp <br>";
	$cp="cp -uv /var/www/html/pini/1info/vkt/db1/2/*.php /var/www/html/pini/1info/vkt/d/$ctrl_dir/2";
	$cmd.=$cp."\n";
	print "$cp <br>";

	print "<hr>";
}
$fname="/var/www/html/pini/1info/vkt/db/copy_to_all.sh";
file_put_contents($fname,$cmd);
chmod($fname,0755);
print "SAVED. <br>
cd /var/www/html/pini/1info/vkt/db/ <br>
./copy_to_all.sh <br>";
?>
