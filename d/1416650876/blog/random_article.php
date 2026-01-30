<?
include "/var/www/html/pini/inc/vklist/db.class.php";
include "../top.header_new.inc.php";

$db=new db("yogacenter");
$res=$db->query("SELECT id FROM blog WHERE del=0");
$arr=array();
while($r=$db->fetch_assoc($res)) {
	$arr[]=$r['id'];
}
$id=rand(0,sizeof($arr)-1);
$r=$db->fetch_assoc($db->query("SELECT * FROM blog WHERE id='$id'"));
print "<div class='blog' >";
print "<p class='red' ><b>Случайная статья в блоге.</b></p>";
print "<h4>{$r['topic']}</h4>";
preg_match("|<blockquote>(.*?)</blockquote>|si",$r['article'],$m);
print "<blockquote>".nl2br($m[1])."</blockquote>";
print "<p><a href='https://yogahelpyou.com/blog/{$r['topic_lat']}.html' class='' target='_blank'>Читать статью в новом окне</a></p>";
print "</div>";
?>
</body>
</html>
