<?
include_once "/var/www/vlav/data/www/wwl/inc/cashier.class.php";
//chdir("../d/1000/"); //wwl
chdir("../d/1267114885/"); //Юрист 248
//chdir("../d/1015124755/"); //SUTTON 249
//chdir("../d/1110503342/"); //244
include "init.inc.php";
$c=new cashier($database,$ctrl_id,$ctrl_dir);
$uid=$c->dlookup("uid","cards","mob_search='79119841012'");

$hash=$c->generate_short_link(['m'=>'79119841012','n'=>'Вася']);
print "<br>$hash <br>";
print_r( $c->get_short_link_url($hash));

print "<br>OK $uid";
exit;
?>
