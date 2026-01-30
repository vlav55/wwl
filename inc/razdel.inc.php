<?
$db=new top($database,0,false,$favicon);
print "<p class='mb-3' ><a href='javascript:window.close();' class='btn btn-warning btn-sm' target=''>Закрыть</a></p>";
print "<container>";
if($db->userdata['access_level']>3) {
	print "<div class='alert alert-warning' >Access prohibited. 1</div>";
	exit;
}
$bs=new bs;
print "<h2 class='text-center' >Управление этапами<span style='margin-left:10px; color:#555;' > <a href='https://help.winwinland.ru/docs/razdely/' class='' target='_blank'><i class='fa fa-question-circle' ></i></a> </span></h2>";
print "<div class='py-3'><a href='?add=yes'>".$bs->button_add()."</a></div>";
if(sizeof($_GET)==0 && sizeof($_POST)==0)
	$_GET['view']="yes";

if(isset($_GET['do_del'])) {
	if($db->num_rows($db->query("SELECT id FROM cards WHERE del=0 AND razdel='".intval($_GET['id'])."'"))!=0) {
		print "<div class='alert alert-danger' >Удаление невозможно, т.к.  используется в базе</div>";
		unset($_GET['do_del']);
		$_GET['view']="yes";
	}	else
		print "<div class='alert alert-danger' >Удалено!</div>";
}

class db1 extends simple_db {
		function view() {
			$res=$this->query($this->view_query);
			print "<table class='table table-striped' >
				<thead>
					<tr>
						<th>№</th>
						<th>Номер для сортировки</th></th>
						<th>Название этапа</th>
						<th>Не отправлять рассылки</th>
						<th>Контроль</th>
					</tr>
				</thead>";
			while($r=$this->fetch_assoc($res)) {
				if($r['id']!=4)
					$ctrl="<a class='mx-3' href='?edit=yes&id={$r['id']}'>
									<span class='fa fa-edit' title='переименовать'></span>
								</a>
								<a class='mx-3' href='?do_del=yes&id={$r['id']}'>
									<span class='fa fa-trash-o' title='удалить'></span>
								</a>";
				else
					$ctrl=" ";
				$ctrl="<a class='mx-3' href='?edit=yes&id={$r['id']}'>
								<span class='fa fa-edit' title='переименовать'></span>
							</a>
							<a class='mx-3' href='?do_del=yes&id={$r['id']}'>
								<span class='fa fa-trash-o' title='удалить'></span>
							</a>";
				$fl_not_send=$r['fl_not_send'] ? "X" : "";
				print "<tr id='q_{$r['id']}'>
							<td>{$r['id']}</td>
							<td>{$r['razdel_num']}</td>
							<td>{$r['razdel_name']}</td>
							<td>$fl_not_send</td>
							<td>
								$ctrl
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
$db1->init_table("razdel");
$db1->view_query="SELECT * FROM razdel WHERE del=0 AND id>0 ORDER BY razdel_num,razdel_name";
	//function add_field($label,$key,$val,$type,$w)
	//var $chk=""; //validate fields - non_empty,unicum,date,time
$fld=$db1->add_field("Номер для сортировки:","razdel_num","","text",80); 
$fld=$db1->add_field("Название этапа:","razdel_name","","text",400); $fld->chk="unicum";
$fld=$db1->add_field("Не отправлять рассылки:","fl_not_send","","checkbox",400); 
$db1->run();
print "</container>";

$db->bottom();
?>
