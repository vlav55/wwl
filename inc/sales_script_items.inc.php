<?
include_once '/var/www/vlav/data/www/wwl/inc/pact.class.php';
$db=new top($database,"СКРИПТЫ",false,$favicon);
$db->pact_token=$PACT_COMPANY;
$bs=new bs;
print $bs->button_close();
//if($db->userdata['access_level']!=1)
	//exit;

if($_GET['sid'])
	$_SESSION['ss_sid']=intval($_GET['sid']);
$sid=$_SESSION['ss_sid'];

if($_GET['uid'])
	$_SESSION['ss_uid']=intval($_GET['uid']);
$uid=$_SESSION['ss_uid'];

//~ print "HERE_$PACT_COMPANY";
//~ $p=new pact($PACT_COMPANY);
//~ $p->test();
//~ exit;



//print "<h1>СКРИПТЫ ПРОДАЖ</h1>";
if(isset($_POST['do_upload_audio'])) {
	$audio_formats=["mp3","ogg","wav","opus","wma","flac","aif","aiff","aac","alac","ape"];
	if ($_FILES["fileToUpload"]["size"] < 50000000) {
		//print_r($_FILES);
		$fileInfo=pathinfo($_FILES['fileToUpload']['name']);
        $ext=$fileInfo['extension'];
        if(in_array($ext,$audio_formats) ) {
			$fname='tmp/'.$_SESSION['userid_sess'].'_'.time().'.'.$ext;
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $fname)) {
				print "<div class='alert alert-success' >Файл $fname успешно загружен</div>";
				$p=new pact($PACT_COMPANY);
				if($attach_id=$p->upload_attachment($fname,$conversation=$db->dlookup("pact_conversation_id","cards","uid='$uid'")) ) {
					$last_num=$db->fetch_assoc($db->query("SELECT num FROM sales_script_items WHERE del=0 AND sid='$sid' ORDER BY num DESC LIMIT 1"))['num']+10;
					$attach_msg="#audio_$attach_id";
					$db->query("INSERT INTO sales_script_items SET
								sid='$sid',
								num='$last_num',
								typ=1,
								item='".$db->escape($attach_msg)."',
								comm='".$db->escape("Голосовое ".date('d.m.Y H:i')."")."',
								user_id='{$_SESSION['userid_sess']}'
								");
					print "<div class='alert alert-success' >Аудио вложение добавлено за номером - $last_num</div>";
					$_GET['view']='yes';
				} else
					print "<div class='alert alert-danger' >Ошибка при загрузке вложения - $fname</div>";
				
			} else {
				echo "<div class='alert alert-info' >Sorry, there was an error uploading your file $fname.</div>";
			}
		} else {
			print "<div class='alert alert-danger' >Недопустимый формат аудио: $ext</div>";
		}
		
	} else {
		print "<div class='alert alert-danger' > Файл слишком большой (допустимо <50M)</div>";
	}
}
if(isset($_POST['do_upload_pic'])) {
	$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	if($check[0]<900) {
		//print "<div class='alert alert-info' >Фотография слишком маленькая! Нужно не менее 900px по ширине</div>";
	}
	//print_r($check);
	if($check !== false) {
		if ($_FILES["fileToUpload"]["size"] < 15000000) {
			$pic_fname_tmp='tmp/'.$_SESSION['userid_sess'].'_'.time();
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $pic_fname_tmp)) {
				if($check['mime']=='image/jpeg') {
					$ext='jpg';
				} elseif($check['mime']=='image/pjpeg') {
					$ext='jpg';
				} elseif($check['mime']=='image/png') {
					$ext='png';
				} elseif($check['mime']=='image/webp') {
					$ext='webp';
				} elseif($check['mime']=='image/gif') {
					$ext='gif';
				} else {
					print "<div class='alert alert-danger' >Ошибка - неизвестный тип {$check['mime']}</div>";
					exit;
				}
				rename($pic_fname_tmp, $pic_fname_tmp.'.'.$ext);
				$pic_fname_tmp=$pic_fname_tmp.'.'.$ext;
				print "<div class='alert alert-success' >Файл $pic_fname_tmp успешно загружен</div>";
				$p=new pact($PACT_COMPANY);
				if($attach_id=$p->upload_attachment($pic_fname_tmp,$conversation=$db->dlookup("pact_conversation_id","cards","uid='$uid'")) ) {
					$last_num=$db->fetch_assoc($db->query("SELECT num FROM sales_script_items WHERE del=0 AND sid='$sid' ORDER BY num DESC LIMIT 1"))['num']+10;
					$attach_msg="#image_$attach_id";
					$db->query("INSERT INTO sales_script_items SET
								sid='$sid',
								num='$last_num',
								typ=1,
								item='".$db->escape($attach_msg)."',
								comm='".$db->escape("Фото ".date('d.m.Y H:i')."")."',
								user_id='{$_SESSION['userid_sess']}'
								");
					print "<div class='alert alert-success' >Фото вложение добавлено за номером - $last_num</div>";
					$_GET['view']='yes';
				} else
					print "<div class='alert alert-danger' >Ошибка при загрузке вложения - $pic_fname_tmp</div>";
			  } else {
				echo "<div class='alert alert-info' >Sorry, there was an error uploading your file.</div>";
			  }
  		} else {
		  print "<div class='alert alert-danger' > Файл слишком большой (допустимо <15M)</div>";
		}
	} else {
		print "<div class='alert alert-danger' >Неверный тип файла</div>";
	}
}

if(isset($_GET['upload'])) {
	?>
	<div class='card bg-light p-3' >
	<h2>Загрузить фотографию</h2>
	<form action="" method="post" enctype="multipart/form-data">
	  Фотография для загрузки:
	  <input class='form-control m-2 p-2' type="file" name="fileToUpload" id="fileToUpload">
	  <button type="submit" class='btn btn-primary' value="go" name="do_upload_pic">Загрузить</button>
	</form>
	</div>

	<div class='card bg-light p-3' >
	<h2>Загрузить аудио</h2>
	<p class='alert alert-info' >Если вы используете айфон, то файл с голосовым после айфона (m4a) нужно предварительно <a href='https://online-audio-converter.com/ru/' class='btn btn-info btn-sm' target='_blank'>преобразовать в mp3</a>. Загружать здесь нужно уже mp3 файл.</p>
	<form action="" method="post" enctype="multipart/form-data">
	  Файл для загрузки:
	  <input class='form-control m-2 p-2' type="file" name="fileToUpload" id="fileToUpload">
	  <button type="submit" class='btn btn-danger' value="go" name="do_upload_audio">Загрузить</button>
	</form>
	</div>
	<?
}

if(isset($_GET['call_script'])) {
	$uid=$_SESSION['ss_uid'];
	$client_name=$db->dlookup("name","cards","uid='$uid'");
	print "<div class='container' >";
	print "<h3 class='blue' >Клиент: $client_name</h3>";
	if(isset($_GET['last_num']))
		$last_num=intval($_GET['last_num']);
	$last_num=(!isset($last_num))?0:$last_num;
	//$sid=intval($_GET['sid']);
	$r=$db->fetch_assoc($db->query("SELECT * FROM sales_script_items WHERE num>$last_num AND del=0 AND sid=$sid ORDER BY num ASC LIMIT 1"));
	$prev_num=$last_num;
	$last_num=$r['num'];
	$msg=nl2br($r['comm']);
	$msg=preg_replace("|\#name|s","$client_name",$msg );
	$age=$db->dlookup("age","cards","uid='$uid'");
	if(!$age)
		$age="в районе 50 лет";
	$msg=preg_replace("|\#age|s",$age,$msg );
	print "<div class='card bg-light p-3' ><h3 style='line-height:2.0;' >".$msg."</h3></div>";
	$prev=intval($_GET['prev_num']-1);
	$fwd=$last_num;
	print "<div class='card bg-light p-3 blue font20'>Клиент: ".nl2br($r['item'])."</div>";
	print "<div>
		<a href='?sid=$sid&last_num=$prev&call_script=yes' class='' target=''><button class='btn btn-default' >Назад</button></a>
		<a href='?sid=$sid&last_num=$fwd&&prev_num=$prev_num&call_script=yes' class='' target=''><button class='btn btn-primary btn-lg' >Вперед</button></a>
		</div>";
	//~ print "
		//~ <div class='top10' ><a href='#top' class='' target=''><span class='glyphicon glyphicon-arrow-up'></span></a></div>
		//~ <div><a href='?add=yes&num={$r['num']}&sid=$sid' class='' target=''><span class='glyphicon glyphicon-plus'></span></a></div>
		//~ <div><a href='?edit=yes&id=$id&sid=$sid' class='' target=''><span class='glyphicon glyphicon-edit'></span></a></div>
		//~ <div><a href='?del=yes&id=$id&sid=$sid' class='' target=''><span class='glyphicon glyphicon-remove'></span></a></div>
		//~ ";
	print "</div>";

	$db->bottom();
	exit;
}


class db1 extends simple_db {
	var $codes="";
	function after_do_add($id) { 
		print "<script>location='?view=yes&id=$id#q_$id'</script>";
	}
	function after_do_edit($id) { 
		print "<script>location='?view=yes&id=$id#q_$id'</script>";
	}
	function before_disp_form($id) {
		print "<p class='card p-2 bg-light my-3' >Сокращения: ".nl2br($this->codes)."</p>";
	}
	function view() {
		global $sid,$uid,$db;
		//$this->mode_mess();
		$bs=new bs;
		$res=$this->query($this->view_query);
		print "<div class='card bg-light p-3' >";
		while($r=$this->fetch_assoc($res)) {
			if(empty($r['comm']))
				continue;
			print "<div class='top5' ><a href='#q_{$r['id']}' class='' target=''>".nl2br(htmlspecialchars($r['comm']))."</a></div>\n";
		}
		print "</div>";
		$res=$this->query($this->view_query);
		$obj="window.opener.f1.msg";
		print " <table class='table table-hover'><tbody>";
		while($r=$this->fetch_assoc($res)) {
			$id=$r['id'];
			if($id==$this->id)
				$c='success'; else $c='';
			$typ_c="";
			$uid_md5=$db->uid_md5($uid);
			$r['item']=str_replace("{{uid}}",$uid_md5,$r['item']);
			$btn_insert="<a href='javascript:ins_text(\"".preg_replace("/[\n\r]{2,2}/","\\n",addslashes($r['item']))."\",$obj);window.close();void(0);' class='' target=''><button type='button' class='btn btn-info btn-xs'>Вставить</button></a>";
			if($r['typ']==1) {
				$typ_c="alert alert-info";
			} elseif($r['typ']==2) {
				$btn_insert="";
				$typ_c="alert alert-warning";
			} elseif($r['typ']==3) {
				$typ_c="alert alert-danger";
			} elseif($r['typ']==4) {
				$typ_c="alert alert-success";
			} elseif($r['typ']==5) {
				$typ_c="card bg-light p-3 card bg-light p-3-sm";
				$btn_insert="";
			}
			if(!empty($r['comm']))
				$comm="<div class='card bg-light p-3 card bg-light p-3-sm' ><b>".nl2br(htmlspecialchars($r['comm']))."</b></div>"; else $comm="";
			print "<tr class='$c' id='q_$id'>
					<td width='5%'>
						<div class='badge badge-secondary' >{$r['num']}</div>
					</td>
					<td>
						<div class='$typ_c' >
						$comm
						".nl2br(htmlspecialchars($r['item']))."
						$btn_insert
						</div>
					</td>";
			if(!isset($_GET['print'])) {
			print "
					<td width='10%'>
						<div class='top10' ><a href='#top' class='' target=''><span class='fa fa-arrow-circle-up'></span></a></div>
						<div><a href='?add=yes&num={$r['num']}&sid=$sid' class='' target=''><span class='fa fa-plus'></span></a></div>
						<div><a href='?edit=yes&id=$id&sid=$sid' class='' target=''><span class='fa fa-edit'></span></a></div>
						<div><a href='?del=yes&id=$id&sid=$sid' class='' target=''><span class='fa fa-trash-o'></span></a></div>
					</td>";
			}
			print "
					</tr>";
		}
		print "</tbody></table>";
	}
}

if(isset($_GET['sid'])) {
	$sid=intval($_GET['sid']);
} elseif(isset($_POST['sid'])) {
	$sid=intval($_POST['sid']);
} elseif(isset($_SESSION['sales_script_sid'])) {
		$sid=$_SESSION['sales_script_sid'];
} else {
	print "<div class='alert alert-danger' >Ошибка.</div>"; exit;
}
$_SESSION['sales_script_sid']=$sid;
$script_name=$db->dlookup("sales_script_name","sales_script_names","id=$sid");
if(!$sid || !$script_name) {
	print "<div class='alert alert-danger' >Ошибка.</div>"; exit;
}

$fl_private=$db->dlookup("fl_private","sales_script_names","id=$sid");


print "<h3 class='alert alert-info' >$script_name</h3>";
if(!isset($_GET['print'])) {
	print "<div class=''>".$bs->button_add()." 
							".$bs->button_view()." 
							".$bs->button_href($text="Все скрипты", $href="sales_script_names.php", $style="default")." 
							".$bs->button_href($text="Распечатать", $href="?print=yes&view=yes", $style="warning")."
			</div>";
}

if($fl_private) {
	print "<div>".$bs->button_href($text="ЗАГРУЗИТЬ ВЛОЖЕНИЕ", $href="?upload=yes", $style="success")."</div>";
}

if(sizeof($_GET)==0 && sizeof($_POST)==0)
	$_GET['view']="yes";

$db1=new db1;
$db1->charset="utf8mb4";
$r=$db->get_mysql_env();
$mysql_user=$r['DB_USER'];
$mysql_passw=$r['DB_PASSW'];
$db1->connect($mysql_user, $mysql_passw,$database);
$db1->init_table("sales_script_items");
if($fl_private) 
	$db1->view_query="SELECT * FROM sales_script_items WHERE del=0  AND user_id={$_SESSION['userid_sess']} AND sid=$sid ORDER BY num";
else
	$db1->view_query="SELECT * FROM sales_script_items WHERE del=0 AND sid=$sid ORDER BY num";
	//function add_field($label,$key,$val,$type,$w)
	//var $chk=""; //validate fields - non_empty,unicum,date,time
//$fld=$db1->add_field("Источник:","url","","text",100);

//print "sid=$sid $sid_lichka";
if($fl_private) 
	$r=$db->fetch_assoc($db->query("SELECT num FROM sales_script_items WHERE del=0 AND user_id={$_SESSION['userid_sess']} AND sid=$sid ORDER BY num DESC LIMIT 1",0));
else
	$r=$db->fetch_assoc($db->query("SELECT num FROM sales_script_items WHERE del=0 AND sid=$sid ORDER BY num DESC LIMIT 1"));
if(!isset($_GET['num'])) {
	$next_num=intval($r['num'])+10;
} else {
	$next_num=(intval($r['num'])==intval($_GET['num']))?intval($_GET['num'])+10:intval($_GET['num'])+5;
}


$fld=$db1->add_field("№:","num",$next_num,"text",40); $fld->chk="non_empty";
$fld=$db1->add_field("Тип записи","typ",0,"select",200); $fld->rowsource=array("Вопрос"=>"1","Ответ"=>2,"Идея"=>3,"Преимущество"=>4,"Комментарий"=>5);
$fld=$db1->add_field("Заголовок:","comm","","textarea",400); $fld->style="style='height:80px; width:480px;'";
$fld=$db1->add_field("Текст:","item","","textarea",400); $fld->style="style='height:200px; width:480px;'";
$fld=$db1->add_field("Скрипт","sid",$sid,"select",480); $fld->rowsource="SELECT id,sales_script_name FROM sales_script_names WHERE del=0 ORDER BY sales_script_name";
//$fld=$db1->add_field("Сообщение рассылки:","msg","","textarea",400); $fld->style="style='width:100%; height:200px;'";
if($fl_private) 
	$fld=$db1->add_field("","user_id",$_SESSION['userid_sess'],"hidden",480);

if($_SESSION['access_level']>3 && !$fl_private) {
	unset($_GET['add']);
	unset($_GET['do_add']);
	unset($_GET['edit']);
	unset($_GET['do_edit']);
	unset($_GET['del']);
	unset($_GET['do_del']);
	$_GET['view']='yes';
}

$db1->codes=$db->prepare_msg_codes();

$db1->run();

$db->bottom();


?>
