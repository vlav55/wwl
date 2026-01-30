<?
class field {
	var $label="";
	var $key;
	var $val;
	var $type="text";
	var $maxlength=false;
	var $width;
	var $style="";
	var $mysql=true;
	var $chk=""; //validate fields - non_empty,unicum,date,time,numeric
}
class simple_db {
	var $conn;
	var $phpver=8;
	var $debug=false;
	var $table;
	var $view_query="";
	var $fields=array();
	var $error=false;
	var $error_mess="";
	var $rowsource="";
	var $mode;
	var $id=false;
	var $view_type=1; //1-table; 2-form
	var $charset="cp1251";
	function simple_db() {
		list($this->phpver,,)=explode(".",phpversion());
		//print phpversion()." ".$this->phpver; exit;
	}
	function before_connect() {
		$error=false;
		$chk_numeric=array('id','uid','klid');
		foreach($chk_numeric AS $val) {
			if(isset($_GET[$val])) {
				if(!is_numeric($_GET[$val])) 
					$error=true; else $_GET[$val]=intval($_GET[$val]);
			}
			if(isset($_POST[$val])) {
				if(!is_numeric($_POST[$val]))
					$error=true; else $_POST[$val]=intval($_POST[$val]);
			} 
		}
		if($error) {
			print "error 5-2";
			exit;
		}
	}
	function connect($user,$passw,$db) {
		$this->before_connect();
		if($this->phpver<7) {
			$this->conn=mysql_connect ("localhost", $user, $passw) or die ("conn :: Database connect error!");
			mysql_select_db ($db, $this->conn) or die ("error select db: $db");
			mysql_query ("set character_set_results='".$this->charset."'");
			mysql_query ("set collation_connection='".$this->charset."_general_ci'");
			mysql_query("set character_set_client='".$this->charset."'");
		} else {
			$this->conn=mysqli_connect ("localhost", $user, $passw) or die ("conn :: Database connect error!");
			mysqli_select_db ($this->conn,$db) or die ("error select db: $db");
			mysqli_query ($this->conn,"set character_set_results='".$this->charset."'");
			mysqli_query ($this->conn,"set collation_connection='".$this->charset."_general_ci'");
			mysqli_query($this->conn,"set character_set_client='".$this->charset."'");
		}
	}
	function query($qstr,$print_query=0) {
		if($print_query>0)
			print "<p class='red'>$qstr</p>";
		if($this->phpver<7)
			if($this->debug) {
				$res=mysql_query($qstr)  or die("<div class='alert alert-danger'>mysql_error in ".__LINE__." :<br>$qstr<br>".mysql_error()."</div>");
			} else  {
				$res=mysql_query($qstr);
				if(!$res) {
					//print getcwd();
					if(!file_put_contents("mysql_last_error.txt",$qstr."\n".mysql_error()))
						print "err";
					exit();
				}
			}
		else
			if($this->debug)
				$res=mysqli_query($this->conn,$qstr)  or die("<div class='alert alert-danger'>mysql_error in ".__LINE__." :<br>$qstr<br>".mysqli_error($this->conn)."</div>");
			else
				$res=mysqli_query($this->conn,$qstr) or die();
		return $res;
	}
	function fetch_assoc($res) {
		if($this->phpver<7) 
			return mysql_fetch_assoc($res); else return mysqli_fetch_assoc($res);
	}
	function fetch_row($res) {
		if($this->phpver<7) 
			return mysql_fetch_row($res); else return mysqli_fetch_row($res);
	}
	function dlookup($fld,$table,$query) {
		$res=$this->fetch_row($this->query("SELECT $fld FROM $table WHERE $query"));
		if($res===false)
			return false; else return $res[0];
	}
	function escape($str) {
		if($this->phpver<7) 
			return mysql_real_escape_string($str); else return mysqli_real_escape_string($this->conn,$str);
	}
	function num_rows($res) {
		if($this->phpver<7) 
			return mysql_num_rows($res); else return mysqli_num_rows($res); 
	}
	function insert_id() {
		if($this->phpver<7) 
			return mysql_insert_id(); else return mysqli_insert_id($this->conn);
	}
	function init_table($table) {
		$this->table=$table;
	}
	function add_field($label,$key,$val,$type,$w) {
		$fld=new field;
		$fld->label=$label;
		$fld->key=$key;
		$fld->val=$val;
		$fld->type=$type;
		$fld->width=$w;
		$this->fields[]=$fld;
		return $fld;
	}
	function get_fld_by_key($key) {
		foreach($this->fields AS $fld)
			if($fld->key==$key)
				return $fld;
		return false;
	}
	function validate1($key,$val) { //user checking for any
		$fld=$this->get_fld_by_key($key);
		if(!$fld) return;
	}
	function validate($key,$val) { //system checking for $this->chk standart values
		$fld=$this->get_fld_by_key($key);
		if(!$fld) return;
		$fld->val=trim($val);
		//print "chk=".$fld->chk."<br>";
		switch($fld->chk) {
			case 'numeric':
				if(!is_numeric($fld->val)) {
					$this->error=true;
					$this->error_mess="Error: must be numeric <b>".$fld->label."</b>";
				}
				break;
			case 'date':
				if($fld->val==0) break;
				list($d,$m,$y)=explode(".",$fld->val);
				if(date("d.m.Y",mktime(0,0,0,$m,$d,$y)) != $fld->val) {
					$this->error=true;
					$this->error_mess="Error: Date format error (dd.mm.YYYY)<b>".$fld->val."</b>";
				}
				break;
			case 'non_empty':
				if($fld->val=="") {
					$this->error=true;
					$this->error_mess="Error: Can't to be empty <b>".$fld->label."</b>";
				}
				break;
			case 'unicum':
				if($fld->val=="") {
					$this->error=true;
					$this->error_mess="Error: Can't to be empty <b>".$fld->label."</b>";
				}
				if($this->id)
					$andid="AND id!=".$this->id; else $andid="";
				if($this->num_rows($this->query("SELECT id FROM ".$this->table." WHERE del=0 AND $key='".$this->escape($val)."' $andid"))>0) {
					$this->error=true;
					$this->error_mess="Error: field must to be unicum <b>$key=$val</b> already exists!";
				}
				break;
		}
	}
	function do_del($id) {
		$this->query("UPDATE ".$this->table." SET del=1 WHERE id=$id",0);
		$this->after_do_del($id);
	}
 	function del($id) {
		$this->mode_mess();
		$r=$this->fetch_assoc($this->query("SELECT * FROM ".$this->table." WHERE id=$id"));
		$keys=array_keys($r);
		foreach($this->fields AS $fld) {
			if(in_array($fld->key, $keys)) {
				if($fld->type != "hidden")
					print nl2br(stripslashes($r[$fld->key]))."<br>";
			}
		}
		print "<form method='POST' action='?'><br>
		<input type='hidden' name='id' value='$id'>
		<input type='submit' name='do_del' value='Удалить'>
		<input type='submit' name='cancel' value='Отменить'>
		</form>";
 	}
 	function conv($key,$val) {
 		return $val;
 	}
	function after_do_add($id) { }
	function do_add() {
		$keys=array();
		foreach($this->fields AS $fld) {
			if($fld->mysql)
				$keys[]=$fld->key;
		}
		$q="INSERT INTO ".$this->table." (del,";
		foreach($_POST AS $key=>$val) {
			if(in_array($key,$keys))
				$q.="$key,";
		}
		$q=substr($q,0,strlen($q)-1).") VALUES (0,";
		foreach($_POST AS $key=>$val) {
			if(in_array($key,$keys)) {
				$this->validate($key,$val); //system checking for $this->chk standart values
				$this->validate1($key,$val); //user checking for any
				$val=$this->conv($key,$val);
				if($this->error) {
					print "<p class='edit_error'>".$this->error_mess."</p>";
					$this->error_mess="";
				}
				if(strval($val)=='on') $val=1;
				if(strval($val)=='off') $val=0;
				$q.="'".$this->escape($val)."',";
			}
		}
		$q=substr($q,0,strlen($q)-1).")";
	//	print $q;
		if($this->error) {
			$this->disp_form(0);
		} else {
			$this->query($q);
			$this->after_do_add($this->insert_id());
		}
	}
	function add() {
		$this->disp_form(0);
	}
	function after_do_edit($id) { }
	function do_edit($id) {
		$this->id=$id;
		$r=$this->fetch_assoc($this->query("SELECT * FROM ".$this->table." WHERE id=$id"));
		$keys=array_keys($r);
		$q="UPDATE ".$this->table." SET ";
		foreach($_POST AS $key=>$val) {
			if(in_array($key,$keys)) {
				$this->validate($key,$val);
				$this->validate1($key,$val);
				$val=$this->conv($key,$val);
				if($this->error) {
					if($this->error_mess!="") {
						print "<div class='alert alert-danger'>".$this->error_mess."</div>";
						$this->error_mess="";
					}
				}
				//print "HERE_$key $val<br>";
				if(strval($val)=='on') $val=1;
				if(strval($val)=='off') $val=0;
				//print "HERE_$key $val<br>";
				$q.="$key='".$this->escape($val)."',";
				//print $q."<br>";
			}
		}
		$q=substr($q,0,strlen($q)-1)." WHERE id=$id";
		//print $q;exit;
		if($this->error) {
			$this->disp_form($id);
		} else {
			$this->query($q) or die("db insert error");
			$this->after_do_edit($id);
		}
	}
	function edit($id) {
		if(!$this->error) {
			$r=$this->fetch_assoc($this->query("SELECT * FROM ".$this->table." WHERE id=$id"));
			$keys=array_keys($r);
			foreach($this->fields AS $fld) {
				if(in_array($fld->key, $keys)) {
					$fld->val=stripslashes($r[$fld->key]);
				}
			}
		}
		$this->disp_form($id);
	}
	function mode_mess() {
		if($this->error) return;
		switch($this->mode) {
			case "add": print "<p class='mode_mess'>ДОБАВЛЕНИЕ</p>"; break;
			case "do_add": print "<p class='mode_mess'>ДОБАВЛЕНО!</p>"; break;
			case "edit": print "<p class='mode_mess'>РЕДАКТИРОВАНИЕ</p>"; break;
			case "do_edit": print "<p class='mode_mess'>ОТРЕДАКТИРОВАНО!</p>"; break;
			case "del": print "<p class='mode_mess'>УДАЛЕНИЕ</p>"; break;
			case "do_del": print "<p class='mode_mess'>УДАЛЕНО!</p>"; break;
			//case "view": print "<p class='mode_mess'></p>"; break;
		}
	}
	function prepare_fld($key,$val,$style,$type) {
		if($type=='text') {
			$fld=$this->get_fld_by_key($key);
			$maxlength=($fld->maxlength!==false)?"maxlength='$fld->maxlength'":"";
			return "<input class='edit_".$key."' type='text' name='".$key."' value='".$val."' $style $maxlength>";
		}
	}
	function before_disp_form($id) {
	}
	function disp_form($id) {
		$this->before_disp_form($id);
		$this->mode_mess();
		print "<form name='f1' method='POST' action='?'>";
		print "<table class='disp_form'>";
		foreach($this->fields AS $fld) {
			switch($fld->type) {
				case "label":
					if($fld->width>0)
						$style="style='width:".$fld->width."px;'"; else $style="";
					if($fld->style!="")
						$style=$fld->style;
					print "<tr>
					<td class='edit_names'>".$fld->label."</td>
					<td class='edit_fields'>".$fld->val."</td>
					</tr>";
					break;
				case "textarea":
					if($fld->width>0)
						$style="style='width:".$fld->width."px;'"; else $style="";
					if($fld->style!="")
						$style=$fld->style;
					print "<tr>
					<td class='edit_names'>".$fld->label."</td>
					<td class='edit_fields'><textarea class='edit_".$fld->key."' id='edit_".$fld->key."' name='".$fld->key."' $style>".$fld->val."</textarea></td>
					</tr>";
					break;
				case "text":
					if($fld->width>0)
						$style="style='width:".$fld->width."px;'"; else $style="";
					if($fld->style!="")
						$style=$fld->style;
					print "<tr>
					<td class='edit_names'>".$fld->label."</td>
					<td class='edit_fields'>".$this->prepare_fld($fld->key,$fld->val,$style,$fld->type)."</td>
					</tr>";
					break;
				case "select":
					if($fld->width>0)
						$style="style='width:".$fld->width."px;'"; else $style="";
					if($fld->style!="")
						$style=$fld->style;
					print "<tr>
					<td class='edit_names'>".$fld->label."</td>
					<td class='edit_fields'>
					<SELECT class='edit_".$fld->key."' name='".$fld->key."' $style>";
					if(!is_array($fld->rowsource)) {
						$res=$this->query($fld->rowsource);
						while($r=$this->fetch_row($res)) {
							if( $r[0]==$fld->val)
								$sel="selected"; else $sel="";
							print "<option value='{$r[0]}' $sel>{$r[1]}</option>";
						}
					} else {
						foreach($fld->rowsource AS $key=>$val) {
							if( $val==$fld->val)
								$sel="selected"; else $sel="";
							print "<option value='$val' $sel>$key</option>";
						}
					}
					print "</SELECT></td></tr>";
					break;
				case "checkbox":
					$style="";
					if($fld->style!="")
						$style=$fld->style;
					if($fld->val==0)
						$chk=""; else $chk="checked";
					print "<tr>
					<td class='edit_names'>".$fld->label."</td>
					<td class='edit_fields'>
					<input type='hidden' name='".$fld->key."' value='off'>
					<input class='edit_".$fld->key."' type='checkbox' name='".$fld->key."'  $chk $style>
					</td>
					</tr>";
					break;
			}
		}
		print "<tr><td  class='edit_names'>&nbsp;</td><td class='edit_fields'>";
		if($id>0) {
			print "<input type='submit' name='do_edit' value=' СОХРАНИТЬ '>";
		} else {
			print "<input type='submit' name='do_add' value=' СОХРАНИТЬ '>";
		}
		print "&nbsp;&nbsp;<input type='submit' name='cancel' value='ОТМЕНА'>
		</td></tr>";
		print "</table>";
		foreach($this->fields AS $fld) {
			if($fld->type=="hidden")
				print "<input type='hidden' name='".$fld->key."' value='".$fld->val."'>";
		}
		if($id>0) {
				print "<input type='hidden' name='id' value='$id'>";
		}
		print "</form>";
	}
	function view_val($key,$val,$id) {
		return $val;
	}
	function view() {
		$this->mode_mess();
		$res=$this->query($this->view_query);
		//print $this->view_query;
		preg_match("/([_0-9a-zA-Z]+)\.php/",$_SERVER["SCRIPT_NAME"],$m);
		print "<table style='border-color:black;border-style:solid;border-width:1;border-collapse:collapse;' class='table table-striped table-hover table-bordered view_".$m[1]."'>";
		foreach($this->fields AS $fld) {
			if($fld->type=="hidden")	continue;
			if($this->view_type==1)
				print "<td  style='border-color:black;border-style:solid;border-width:1;width:".$fld->width."'>".$fld->label."</td>";
		}
		while($r=$this->fetch_assoc($res)) {
			if($r['id']==$this->id)
				$c='background-color:yellow;'; else $c='';
			if($this->view_type==1)
				print "<tr id='q_{$r['id']}' style='$c' title='{$r['id']}'>";
			foreach($this->fields AS $fld) {
				if($fld->type=="hidden")	continue;
				if($this->view_type==2)
					print "<tr id='q_{$r['id']}' style='$c' class='tr_".$fld->key."'>";
				if($this->view_type==2)
					print "<td  style='border-color:black;border-style:solid;border-width:1;' class='label_".$fld->key."'>".$fld->label."</td>";
				if($fld->type=="text" || $fld->type=="textarea") {
					$val=nl2br(stripslashes($r[$fld->key]));
					print "<td  style='border-color:black;border-style:solid;border-width:1;width:".$fld->width."px;' class='val_".$fld->key."'>".$this->view_val($fld->key,$val,$r['id'])."</td>";
				}
				if($fld->type=="checkbox") {
					if($r[$fld->key]==0)
						$chk="-"; else $chk="X";
					print "<td  style='border-color:black;border-style:solid;border-width:1;width:15;text-align:center;' class='val_".$fld->key."'>$chk</td>";
				}
				if($this->view_type==2)
					print "</tr>";
			}
			if($this->view_type==2)
				print "<tr style='background-color:#DDD;'><td>&nbsp;</td>";
			print "<td  style='border-color:black;border-style:solid;border-width:1;padding:3;'><a href='?edit=yes&id={$r['id']}'>edit</a>&nbsp;<a href='?del=yes&id={$r['id']}'>del</a></td>";
			if($this->view_type==1)
				print "</tr>";
		}
		print "</table>";
	}
	function after_do_del($id) {
		//print "HERE_123 $id";
	}
	function run() {
		if(sizeof($_GET)==0 && sizeof($_POST)==0)
			$_GET['view']="yes";
		if(isset($_GET['id']))
			$this->id=intval($_GET['id']);
		if(isset($_POST['id']))
			$this->id=intval($_POST['id']);
		if(isset($_GET['lastid']))
			$this->id=intval($_GET['lastid']);
		if(isset($_POST['lastid']))
			$this->id=intval($_POST['lastid']);
		if(@$_POST['do_add']) {
			$this->mode="do_add";
			$this->do_add();
			if(!$this->error)
				$this->view();
			print "<script>location.hash='#q_".$this->insert_id()."'</script>";
		}
		if(@$_GET['add']) {
			$this->mode="add";
			$this->add();
		}
		if(@$_POST['do_edit']) {
			$this->mode="do_edit";
			$this->do_edit($_POST['id']);
			if(!$this->error)
				$this->view();
			print "<script>location.hash='#q_".$_POST['id']."'</script>";
		}
		if(@$_GET['edit']) {
			$this->mode="edit";
			$this->edit($_GET['id']);
		}
		if(@$_POST['do_del'] || @$_GET['do_del']) {
			$this->mode="do_del";
			$id=(isset($_POST['do_del']))?$_POST['id']:$_GET['id'];
			$this->do_del($id);
			if(!$this->error) {
				$this->view();
			}
		}
		if(@$_GET['del']) {
			$this->mode="del";
			$this->del($_GET['id']);
		}
		if(@$_GET['view']) {
			//$this->mode="view";
			if($this->view_query=="") {
				print "<p class='red'>view_query undefined</p>"; exit;
			}
			if(!$this->error)
				$this->view();
		}
		if(@$_POST['cancel'] || @$_GET['cancel']) {
			$this->mode="cancel";
			$this->view();
		}
	}
}
/*
include_once "../inc/simple_db.inc.php";
class db extends simple_db {
}
print "<h1>EXAMPLE</h1>";
print "<a href='?add=yes'>Add</a> | <a href='?view=yes'>View</a><br>";
if(sizeof($_GET)==0 && sizeof($_POST)==0)
	$_GET['view']="yes";
$db=new db;
$db->init_table("portfolio");
$db->view_query="SELECT * FROM portfolio WHERE del=0 ORDER BY num";
	//function add_field($label,$key,$val,$type,$w)
	//var $chk=""; //validate fields - non_empty,unicum,date,time
$fld=$db->add_field("num:","num","","text",40);
$fld=$db->add_field("pic:","pic","","text",100);
$fld=$db->add_field("hdr:","hdr","","text",400);
$fld=$db->add_field("comm:","comm","","textarea",400); $fld->style="style='width:400px; height:200px;'";
$db->run();
*/
?>
