<?
$db=new top($database,"640px;",true,$favicon);
$bs=new bs;

if($db->userdata['access_level']!=1)
	exit;


print "<h1>ГРУППЫ ДЛЯ РАССЫЛКИ</h1>";

print "<div class='well'>".$bs->button_add()." <a href='vklist.php'>Назад</a></div>";
if(sizeof($_GET)==0 && sizeof($_POST)==0)
	$_GET['view']="yes";

class db1 extends simple_db {
	function view_val($key,$val,$id) {
		if($key=="tm")
			return date("d.m.Y",$val);
		if($key=="url")
			return "<a href='$val' target='_blank'>ссылка</a>";
		return $val;
	}
}

$db1=new db1;
$db1->charset="utf8mb4";
$db1->connect(mysql_user, mysql_passw,$database);
$db1->init_table("vklist_groups");
$db1->view_query="SELECT * FROM vklist_groups WHERE del=0 ORDER BY tm DESC";
	//function add_field($label,$key,$val,$type,$w)
	//var $chk=""; //validate fields - non_empty,unicum,date,time
//$fld=$db1->add_field("Источник:","url","","text",100);
$fld=$db1->add_field("Название группы:","group_name","","text",400); $fld->chk="non_empty";
//$fld=$db1->add_field("Сообщение рассылки:","msg","","textarea",400); $fld->style="style='width:400px; height:200px;'";
$fld=$db1->add_field("Date created","tm",time(),"hidden",400);
$fld=$db1->add_field("","fl_send_msg",1,"hidden",400);
$db1->run();

$db->bottom();


?>
