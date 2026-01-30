<?

$db=new top($database,0,false,$favicon);
if($db->userdata['access_level']!=1) {
	print "<div class='alert alert-warning' >Access prohibited. 1</div>";
	exit;
}
$bs=new bs;
print "<h1>Sources</h1>";
print "<div class='well'><a href='?add=yes'>".$bs->button_add()."</a></div>";
if(sizeof($_GET)==0 && sizeof($_POST)==0)
	$_GET['view']="yes";

if(isset($_GET['do_del'])) {
		print "<div class='alert alert-danger' >Удаление невозможно</div>";
		unset($_GET['do_del']);
		$_GET['view']="yes";
}

class db1 extends simple_db {
		function view() {
			$res=$this->query($this->view_query);
			$bs=new bs;
			print $bs->table(array("ID","Раздел","Контроль"));
			while($r=$this->fetch_assoc($res)) {
				print "<tr id='q_{$r['id']}'>
							<td>{$r['id']}</td>
							<td>{$r['source_name']}</td>
							<td>{$r['priority']}</td>
							<td>{$r['razdel_id']}</td>
							<td>{$r['fl_client']}</td>
							<td>
								<a href='?edit=yes&id={$r['id']}'>
									<span class='fa fa-edit' title='переименовать'></span>
								</a>
							</td>
						</tr>";
			}
			print "</table>";
		}
		function after_do_add($id) {
			//print "<script>opener.location.reload();</script>";
		}
		function after_do_edit($id) {
			//print "<script>opener.location.reload();</script>";
		}
}

$db1=new db1;
$db1->charset="utf8mb4";
$r=$db->get_mysql_env();
$mysql_user=$r['DB_USER'];
$mysql_passw=$r['DB_PASSW'];
$db1->connect( $mysql_user, $mysql_passw,$database);
$db1->init_table("sources");
$db1->view_query="SELECT * FROM sources WHERE del=0 AND id>0";
	//function add_field($label,$key,$val,$type,$w)
	//var $chk=""; //validate fields - non_empty,unicum,date,time
$fld=$db1->add_field("source_name:","source_name","","text",400); $fld->chk="unicum";
$fld=$db1->add_field("priority:","priority","0","text",40); 
$fld=$db1->add_field("razdel_id:","razdel_id","0","text",40);
$fld=$db1->add_field("fl_client:","fl_client","","checkbox",40); 
$db1->run();
$db->bottom();
?>
