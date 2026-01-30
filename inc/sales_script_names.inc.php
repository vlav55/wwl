<?
$db=new top($database,"СКРИПТЫ",false,$favicon);
$bs=new bs;

if($db->userdata['access_level']>3) {
	print "<div class='alert alert-danger' >Ошибка: нет прав </div>";
	exit;
}

print $bs->button_close();
print "<h1>СКРИПТЫ ПРОДАЖ</h1>";

print "<div class='well'>".$bs->button_add()." ".$bs->button_view()."</div>";
if(sizeof($_GET)==0 && sizeof($_POST)==0)
	$_GET['view']="yes";

class db1 extends simple_db {
	function view() {
		$res=$this->query($this->view_query);
		$bs=new bs;
		print $bs->table(array("№","Название"," "));
		$n=1;
		while($r=$this->fetch_assoc($res)) {
			$id=$r['id'];
			if($r['id']==$this->id)
				$c='background-color:yellow;'; else $c='';
			print "<tr>
					<td width='5%'>
						<div class='badge badge-default' >$n</div>
					</td>
					<td>
						<h1><a href='sales_script_items.php?sid=$id&view=yes'>{$r['sales_script_name']}</a></h1>
					</td>
					<td width='10%'>
						<div><a href='?edit=yes&id=$id' class='' target=''><span class='fa fa-edit'></span></a></div>
						<div><a href='?del=yes&id=$id&' class='' target=''><span class='fa fa-trash-o'></span></a></div>
					</td>
					</tr>";
			$n++;
		}
		print "</table>";
	}
}

$r=$db->get_mysql_env();
$mysql_user=$r['DB_USER'];
$mysql_passw=$r['DB_PASSW'];

$db1=new db1;
$db1->charset="utf8mb4";
$db1->connect($mysql_user, $mysql_passw,$database);
$db1->init_table("sales_script_names");
$db1->view_query="SELECT * FROM sales_script_names WHERE del=0 ORDER BY fl_call_script,sales_script_name";
	//function add_field($label,$key,$val,$type,$w)
	//var $chk=""; //validate fields - non_empty,unicum,date,time
//$fld=$db1->add_field("Источник:","url","","text",100);
$fld=$db1->add_field("Название скрипта:","sales_script_name","","text",400); $fld->chk="non_empty";
$fld=$db1->add_field("Для звонка:","fl_call_script",false,"checkbox",40);
//$fld=$db1->add_field("Сообщение рассылки:","msg","","textarea",400); $fld->style="style='width:400px; height:200px;'";
//$fld=$db1->add_field("","sid",$sid,"hidden",0);
$db1->run();

$db->bottom();


?>
