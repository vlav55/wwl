<?
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt('vkt');
$res=$db->query("SELECT * FROM 0ctrl WHERE del=0 AND id>1");
$cmd="";
$dir="/var/www/vlav/data/www/wwl/d";
?>
<pre>
for dir in /var/www/vlav/data/www/wwl/d/*/; do
    if [[ "$dir" != "/var/www/vlav/data/www/wwl/d/1000/" ]]; then
        echo cp -uv /var/www/vlav/data/www/wwl/d/1000/test.php "$dir"
    fi
done
</pre>
<?
while($r=$db->fetch_assoc($res)) {
	$ctrl_dir=$db->get_ctrl_dir($r['id']);
	$cp="cp -uv $dir/1000/*.php $dir/$ctrl_dir";
	print "$cp <br>";
	$cmd.=$cp."\n";

	$cp="cp -uv $dir/1000/lk/*.php $dir/$ctrl_dir/lk";
	$cmd.=$cp."\n";
	print "$cp <br>";

	//~ $cp="mkdir $dir/$ctrl_dir/reports";
	//~ $cmd.=$cp."\n";
	//~ print "$cp <br>";
	$cp="cp -uv $dir/1000/reports/*.* $dir/$ctrl_dir/reports";
	$cmd.=$cp."\n";
	print "$cp <br>";

	if(!file_exists("$dir/$ctrl_dir/1"))
		mkdir("$dir/$ctrl_dir/1");
	if(!file_exists("$dir/$ctrl_dir/2"))
		mkdir("$dir/$ctrl_dir/2");
	$cp="cp -uv $dir/1000/1/*.php $dir/$ctrl_dir/1";
	$cmd.=$cp."\n";
	print "$cp <br>";
	$cp="cp -uv $dir/1000/2/*.php $dir/$ctrl_dir/2";
	$cmd.=$cp."\n";
	print "$cp <br>";

	print "<hr>";
}
$fname="$dir/../scripts/copy_to_all.sh";
file_put_contents($fname,$cmd);
chmod($fname,0755);
print "SAVED. <br>
cd $dir/../scripts/; ./copy_to_all.sh <br>";
?>
