<?
include "/var/www/vlav/data/www/wwl/inc/unisender.class.php";
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db('test');
$db->db200=$DB200;

print "<p>?send=yes</p>";
$n=0;
$limit=0;
$res=$db->query("SELECT * FROM insales_1 WHERE email!='' AND res_email=0 LIMIT $limit");
if(isset($_GET['send'])) {
	$api_key='6s1414bffqhg69c1ggzw79wrtgw6zstdbd4k161o';
	$uni=new unisender($api_key,$from_email="winwinland@formula12.ru",$from_name="Артем Нураев");
	$templ="d6560b3c-193f-11f0-bd1b-de4d8ce5566e";
	if($uni->email_by_template("winwinland@yandex.ru",$templ,$vars=[])) {
		print "TEST email to winwinland@yandex.ru sent OK <br>\n";
	} else {
		print "TEST email to winwinland@yandex.ru sent ERROR <br>\n";
		exit;
	}
	while($r=$db->fetch_assoc($res)) {
		$email=$r['email']; //"vlav@mail.ru";
		if($uni->email_by_template($email,$templ,$vars=[])) {
			$n++;
			print "$n $email sent OK <br>\n";
			$db->query("UPDATE insales_1 SET tm_email_sent='".time()."',res_email=1 WHERE id={$r['id']}");
		} else {
			print "$n $email send ERROR <br>\n";
			$db->query("UPDATE insales_1 SET tm_email_sent='".time()."',res_email=2  WHERE id={$r['id']}");
		}
		//break;
	}
}
print "DONE. Success=$n";
?>
