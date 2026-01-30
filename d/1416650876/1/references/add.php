<?
include "/var/www/html/pini/inc/vklist/db.class.php";
include "/var/www/html/pini/inc/simple_db.inc.php";
include "../top.inc.php";

print "<div><a href='?add=yes'>Add</a> | <a href='?view=yes'>View</a></div>";
class db1 extends simple_db {
	function view_val($key,$val,$id) {
		if($key=='dir')
			$val="<a href='https://yogahelpyou.com/references/$val' class='' target='_blank'>$val</a>";
		if($key=='brief')
			$val=substr($val,0,80)."...";
		return $val;
	}

}
$db1=new db1;
$db1->charset="utf8mb4";
$db1->connect( mysql_user, mysql_passw,"yogacenter");
$db1->init_table("refs_new");
$db1->view_query="SELECT * FROM refs_new WHERE del=0 ";
	//function add_field($label,$key,$val,$type,$w)
	//var $chk=""; //validate fields - non_empty,unicum,date,time
$fld=$db1->add_field("dir:","dir","","text",400); 
$fld=$db1->add_field("First_name:","first_name","","text",400); 
$fld=$db1->add_field("Last_name:","last_name","","text",400); 
$fld=$db1->add_field("Age:","age","","text",40); 
$fld=$db1->add_field("brief:","brief","","textarea",400); $fld->style="style='height:100px; width:600px;'";
$db1->run();

include "../bottom.inc.php";
?>
