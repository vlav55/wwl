<?
$db=new top($database,"Шаблоны",false,$favicon);
$bs=new bs;

print $bs->button_close();
print "<h2 class='text-center' >Шаблоны сообщений
	<span style='margin-left:10px; color:#555;' > <a href='https://help.winwinland.ru/docs/d0-b8-d0-bd-d1-81-d1-82-d1-80-d1-83-d0-ba-d1-86-d0-b8-d1-8f-1/' class='' target='_blank'><i class='fa fa-question-circle' ></i></a> </span>
	</h2>";
print "";
print "<div class=''>".$bs->button_add()." ".$bs->button_href($text="Скрипты", $href="sales_script_names.php", $style="info")."</div>";
if(sizeof($_GET)==0 && sizeof($_POST)==0)
	$_GET['view']="yes";

class db1 extends simple_db {
	var $codes="";
	function view() {
		$db=new db($this->database);
		print "<table class='table table-striped' >
			<thead>
			 <tr>
				<th>Ключ</th>
				<th>Текст</th>
				<th>Упр</th>
			 </tr>
			</thead>";
		$res=$this->query($this->view_query);
		while($r=$this->fetch_assoc($res)) {
			print "<tr>
				<td>{$r['name']}</td><td>".nl2br(stripslashes($r['msg']))."</td>
				<td>
					<a href='?edit=yes&id={$r['id']}'><i class='fa fa-edit'></i></a>
					<a href='?do_del=yes&id={$r['id']}'><i class='fa fa-trash'></i></a>
				</td>
				</tr>";                     
		}
		print "</table>";

	}
	function conv($key,$val) { //user checking for any
		$fld=$this->get_fld_by_key($key);
		$val=preg_replace("|[\<\>]+|i","|",$val);
		return addslashes(str_replace("'","\"",$val));
	}
	function after_do_edit($id) {
		print "<script>opener.location.reload();</script>";
	}
	function after_do_add($id) {
		print "<script>opener.location.reload();</script>";
	}
	function before_disp_form($id) {
		print "<p class='card p-2 my-3 bg-light' >Сокращения: ".nl2br($this->codes)."</p>";
	}
}

$db1=new db1;
$db1->charset="utf8mb4";
$r=$db->get_mysql_env();
$mysql_user=$r['DB_USER'];
$mysql_passw=$r['DB_PASSW'];
$db1->connect( $mysql_user, $mysql_passw,$database);
$db1->init_table("msgs_templates");
$db1->view_query="SELECT * FROM msgs_templates WHERE del=0 ORDER BY name DESC";
	//function add_field($label,$key,$val,$type,$w)
	//var $chk=""; //validate fields - non_empty,unicum,date,time
$fld=$db1->add_field("Ключ:","name","","text",400);
$fld=$db1->add_field("","msg","","textarea",600);  $fld->style="style='width:100%; height:200px;'";
$db1->codes=$db->prepare_msg_codes();
$db1->run();

$db->bottom();


?>
