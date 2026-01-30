<?
include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
$db=new top($database,0,true, $favicon);

if($db->userdata['access_level']!=1)
	exit;
if(!isset($vklist_import_mode))
	$vklist_import_mode=0; //0 - pass if exist in vklist, 1 -replace in vklist and prepare for sending

print "<h2><div class='alert alert-info'>VKLIST - ВСЕ РАССЫЛКИ</div></h2>";
print "<div class='well'>
	<a href='vklist.php?view=yes'>Список</a> | 
	<a href='vklist_groups.php?view=yes'>Группы</a> | 
	<a href='vklist_acc.php?view=yes'>Аккаунты</a> | 
	<a href='javascript:block(\"import_box\");void(0);'>Импорт</a>
	</div>";

if(isset($_POST['vklist_import_mode'])) {
	$vklist_import_mode=1;
	print "<h3>Режим замены имеющихся ключей</h3>";
}
if(@$_GET['do_import']) {
	$allowed_exts=array("htm","html","txt");
	$max_size=10; //Mb
	//$dir="/PHP/db/tmp/";
	$dir=getcwd()."/tmp";
	$error=true;
	$num_fr=0; $num_paased=0; $num_added=0;
	$f = pathinfo($_FILES['filename']['name']);
	if(in_array(strtolower($f['extension']),$allowed_exts)) {
		if($_FILES["filename"]["size"] < 1024*$max_size*1024) {
			//Testes Ok
			if(is_uploaded_file($_FILES["filename"]["tmp_name"])) {
				$fname="$dir/".$_FILES["filename"]["name"];
				if(!file_exists($dir))
					mkdir($dir);
				//print $fname." ".$dir; exit;
				if(file_exists($fname)) {
					unlink($fname);
				}
				if (move_uploaded_file($_FILES["filename"]["tmp_name"], $fname)) {
					$pic=$_FILES["filename"]["name"];
					print "<p>Файл выгружен на сервер успешно: <b>{$_FILES["filename"]["name"]}</b></p>";
					$error=false;
				} else
					print "<p class='red'>Ошибка загрузки файла. 1</p>";
			} else {
				echo("<p class='red'>Ошибка загрузки файла. 2</p>");
			}		
		} else 
			print ("<p class='red'>Размер файла превышает : $max_size Mb</p>");
	} else
		print "<p class='red'>Error : prohibited file type : {$_FILES['filename']['name']}</p>";
	if(!$error) {
	//	print "HERE_$fname";
		include_once('parser/simple_html_dom.php');
	//	print "HERE_2";
		$vk=new vklist_api();
		$vk->token=$db->dlookup("token","vklist_acc","del=0 AND last_error=0");

	//	print "HERE_3";
		$friends=$vk->vk_friends_getlist_for_uid($DO_NOT_TOUCH_FRIENDS);
	//	print "HERE_4";
		$arr=file($fname);
		$n=1; $tm_cr=time();
		if(is_numeric(trim($arr[0]))) {
			print "<h3>UID only list detected</h3>";
			//print_r($arr);
			foreach($arr AS $str) {
				$uid=intval(trim($str));
				if($uid=='' || $uid=="0") {
					//print "uid=$uid is not correct, continued<br>";
					continue;
				}
				print "$n $uid ";
				if($db->dlookup("uid","cards","uid=$uid")) {
					print " IN CARDS, passed<br>";
					$num_paased++;
					continue;
				}
				/*if(vk_is_friend($uid,$friends)) {
					print " - FRIEND passed<br>";
					$num_fr++;
					continue;
				}*/
				if(strpos($uid,"http")!==false)
					$uid=$vk->get_uid_from_url($uid);
				elseif(!is_numeric($uid))
					$uid=$vk->vk_get_uid_by_domain($uid);
				if(!$uid || $uid=='' || $uid==0) {
					print "uid=$uid is not correct, continued<br>";
					continue;
				} else
					print "uid=$uid ";
				if($db->num_rows($db->query("SELECT uid FROM vklist WHERE uid='$uid'"))==0) {
					print " - inserted<br>";
					$db->query("INSERT INTO vklist SET uid='$uid',group_id='{$_POST['groupid']}' ,tm_cr='$tm_cr'");
					$num_added++;
				} else {
					if($vklist_import_mode==1) {
						print " - already in VKLIST - replaced and prepared for sending<br>";
						$db->query("UPDATE vklist SET group_id='{$_POST['groupid']}',tm_msg=0,tm_friends=0,tm_wall=0,res_msg=0,res_friends=0,res_wall=0,blocked=0 WHERE uid=$uid");
						$num_added++;
					} else  {
						print " - already in VKLIST - passed<br>";
						$num_paased++;
					}
				}
				$n++;
			}
		} else {
			print "<h3>VK saved page converter started</h3>";
			$html = file_get_html($fname);
			foreach($html->find("div[class='labeled name'] a") as $e) {
				if(preg_match("#http(s)?://vk.com/(.*)#",$e->href,$res))
					$uid=$res[2];
				if(preg_match("#^id([0-9]*)$#",$uid,$res))
					$uid=$res[1];
				print "$n ".$e->href." <b>$uid</b> ".$e->plaintext;
				if(vk_is_friend($uid,$friends)) {
					print " - FRIEND passed<br>";
					$num_fr++;
					continue;
				}
				if(!is_numeric($uid))
					$uid=vk_get_uid_by_domain($uid);
				if($db->num_rows($db->query("SELECT uid FROM vklist WHERE uid='$uid'"))==0) {
					$db->query("INSERT INTO vklist SET uid='$uid',group_id='{$_POST['groupid']}' ,tm_cr='$tm_cr'");
					print " - inserted<br>";
					$num_added++;
				} else {
					print " - passed<br>";
					$num_paased++;
				}
				$n++;
			}
			$html->clear();
		}
	}
	print "<h1>DONE</h1>";
	print "Added=$num_added<br>";
	print "Passed=$num_paased<br>";
	print "Friends=$num_fr<br>";
}

?>
<div id='import_box' style='display:none;'>
<fieldset><legend>ИМПОРТ</legend>
	<div class='alert alert-info'>
	Укажите подготовленный файл, который может быть двух видов:
	<ol>
	<li>Сохраненный html страница списка участников группы ВК</li>
	<li>Список url, id (ников) пользователей ВК. В этом случае первой строкой должен быть цифровой ID (или 0)</li>
	</ol>
	</div>
	<form action='?do_import=yes' method='post' enctype='multipart/form-data'>
	<input type='file' name='filename' style='width:400px;'><br> 
	<p>Выберите группу (<a href='vklist_groups.php?view=yes'>создать</a>)
	<select name='groupid'>
	<?
	$res=$db->query("SELECT * FROM vklist_groups WHERE del=0 ORDER BY tm DESC");
	while($r=$db->fetch_assoc($res)) {
		print "<option value='{$r['id']}'>{$r['group_name']}</option>";
	}
	?>
	</select>
	</p>
	<div>
		Режим замены:
		<input type='checkbox' name='vklist_import_mode' >
	</div>
	<input type='submit' value='Загрузить'><br>
	</form>
</fieldset>
</div>
<?

if(@$_GET['fl_send_msg']) {
	$db->query("UPDATE vklist_groups SET fl_send_msg={$_GET['fl']} WHERE id={$_GET['groupid']}");
	$lastid=$_GET['groupid'];
}
if(@$_GET['fl_send_wall']) {
	$db->query("UPDATE vklist_groups SET fl_send_wall={$_GET['fl']} WHERE id={$_GET['groupid']}");
	$lastid=$_GET['groupid'];
}
if(@$_GET['fl_send_friends']) {
	$db->query("UPDATE vklist_groups SET fl_send_friends={$_GET['fl']} WHERE id={$_GET['groupid']}");
	$lastid=$_GET['groupid'];
}
$group_res=$db->query("SELECT * FROM vklist_groups WHERE del=0 ORDER BY tm DESC");
print "<hr>";
$res=$db->query("SELECT * FROM vklist_acc WHERE del=0 AND last_error!=0");
while($r=$db->fetch_assoc($res)) {
	print "<p class='red'>Warning: Account {$r['name']} last_error={$r['last_error']} ({$r['last_error_msg']})</p>";
}
print "<hr>";

$r=$db->fetch_assoc($db->query("SELECT COUNT(uid) AS cnt FROM vklist WHERE tm_msg=0 AND blocked=0"));
print "<h3>Now for sending : {$r['cnt']}</h3>";

print "<table class='vklist table-bordered table-striped table-hover table-responsive'>";
print "<tr>
	<td width='80'>Дата создания</td>
	<td>Группа рассылки</td>
	<td>Сообщение рассылки</td>
	<td width='60'>Всего</td>
	<td width='60'>Отправлено</td>
	<td width='60'>Не доставлено</td>
	<td width='60'>Доставлено</td>
	<td width='60' title='MSG-tm=1'>Блокировано</td>
	<td width='60'>Готово к отправке</td>
	<td width='60'>Рассылка разрешена</td>
	</tr>";
while($group_r=$db->fetch_assoc($group_res)) {
	if($group_r['fl_send_msg']==0)
		$fl_send_msg_1=1; else $fl_send_msg_1=0;
	if($group_r['fl_send_wall']==0)
		$fl_send_wall_1=1; else $fl_send_wall_1=0;
	if($group_r['fl_send_friends']==0)
		$fl_send_friends_1=1; else $fl_send_friends_1=0;
	$r=$db->fetch_row($db->query("SELECT COUNT(uid) FROM vklist WHERE group_id={$group_r['id']}")); $cnt_all=$r[0];
	$r=$db->fetch_row($db->query("SELECT COUNT(uid) FROM vklist WHERE group_id={$group_r['id']} AND tm_msg>1")); $cnt_msg_sent=$r[0];
	$r=$db->fetch_row($db->query("SELECT COUNT(uid) FROM vklist WHERE group_id={$group_r['id']} AND (tm_msg=1 || blocked!=0)")); $cnt_msg_sent_1=$r[0];
	$r=$db->fetch_row($db->query("SELECT COUNT(uid) FROM vklist WHERE group_id={$group_r['id']} AND tm_msg>1 AND res_msg>0")); $cnt_msg_err=$r[0];
	
		
	if($group_r['id']==@$lastid)
		$c="yellow"; else $c="";
	$dt=($group_r['tm']!=0)?date("d.m.Y",$group_r['tm']):"";
	print "<tr>
	<td style='background-color:$c;'>".$dt."</td>
	<td style='background-color:$c;'><a href='javascript:wopen(\"vklist_groups.php?edit=yes&id={$group_r['id']}\")'>{$group_r['group_name']}</a></td>
	<td style='background-color:$c; width:300px;' >".nl2br($group_r['msg'])."</td>
	<td style='background-color:$c;'>$cnt_all</td>
	<td style='background-color:$c;'>$cnt_msg_sent</td>
	<td style='background-color:$c;'>$cnt_msg_err</td>
	<td style='background-color:$c;'>".($cnt_msg_sent-$cnt_msg_err)."</td>
	<td style='background-color:$c;'>$cnt_msg_sent_1</td>
	<td style='background-color:$c;'><a href='javascript:wopen(\"vklist_info.php?gid={$group_r['id']}\")'>".($cnt_all-$cnt_msg_sent_1-$cnt_msg_err-($cnt_msg_sent-$cnt_msg_err))."</a></td>
	
	<td style='background-color:$c;'><a href='?fl_send_msg=yes&fl=$fl_send_msg_1&groupid={$group_r['id']}' title='0-отправка запрещена / 1 - разрешена'>{$group_r['fl_send_msg']}</a></td>
	</tr>";
}
print "</table>";



function import_from_text($fname) {
	$fp=fopen($fname,"r");
	while(!feof($fp)) {
		$str=trim(fgets($fp));
		print $str." ";
		if($str=="")
			continue;
		if(preg_match("#http(s)?://vk.com/(.*)#",$str,$res))
			$str=$res[2];
		if(preg_match("#^id([0-9]*)$#",$str,$res))
			$str=$res[1];
		$tm=time();
		if($db->num_rows($db->query("SELECT uid FROM vklist WHERE uid='$str'"))==0)
			$db->query("INSERT INTO vklist VALUES ('$str',1,$tm,$tm,$tm,$tm,0,0,0)") or die(mysql_error());
		print " inserted $str<br>";
	
	}
	fclose($fp);
}
$db->bottom();


?>
