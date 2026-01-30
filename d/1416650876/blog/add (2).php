<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>BLOG</title>
    <script src="https://cdn.ckeditor.com/ckeditor5/16.0.0/classic/ckeditor.js"></script>
</head>
<body style='font-size:18px; padding:40px;'>
	<div>
		<a href='?view=yes' class='' target=''>View</a>
		<a href='?add=yes' class='' target=''>Add</a>
		<a href='?rewrite-all=yes' class='' target=''>rewrite-all</a>
	</div>
	<?
	include "/var/www/html/pini/inc/vklist/db.class.php";
	$db=new db("yogacenter");
	if(isset($_POST['save'])) {
		if(intval($_GET['id'])) {
			$id=intval($_GET['id']);
			$db->query("UPDATE blog SET
						author='".$db->escape($_POST['author'])."',
						topic='".$db->escape($_POST['topic'])."',
						topic_lat='".$db->escape($_POST['topic_lat'])."',
						article='".$db->escape($_POST['article'])."'
						WHERE id='$id'",0);
		} else {
		$db->query("INSERT INTO blog SET
					tm='".time()."',
					author='".$db->escape($_POST['author'])."',
					topic='".$db->escape($_POST['topic'])."',
					topic_lat='".$db->escape(translit($_POST['topic']))."',
					article='".$db->escape($_POST['article'])."'
					");
			$id=$db->insert_id();
		}
		$out="<?\$id=$id;\n?>".file_get_contents("index.php");
		$fname=$db->dlookup("topic_lat","blog","id='$id'");
		file_put_contents($fname.".html",$out);
		print "<h3>Записано $fname  <a href='?edit=yes&id=$id' class='' target=''>edit</a> <a href='/blog/$fname.html' class='' target='_blank'>view article in new window</a></h3>";
	}
	if(isset($_GET['rewrite-all'])) {
		$res=$db->query("SELECT * FROM blog WHERE del=0 ORDER BY tm DESC");
		$n=1;
		$content=file_get_contents("index.php");
		while($r=$db->fetch_assoc($res)) {
			$id=$r['id'];
			$out="<?\$id=$id;\n?>".$content;
			$fname=$r['topic_lat'];
			file_put_contents($fname.".html",$out);
			print "<div>$n. Записано $fname  <a href='?edit=yes&id=$id' class='' target=''>edit</a> <a href='/blog/$fname.html' class='' target='_blank'>view article in new window</a></div>";
			$n++;
		}
	}
	if(isset($_GET['view'])) {
		$res=$db->query("SELECT * FROM blog WHERE del=0 ORDER BY tm DESC");
		$n=1;
		while($r=$db->fetch_assoc($res)) {
			$dt=date("d.m.Y",$r['tm']);
			print "<h3>$n $dt {$r['topic']} <a href='?edit=yes&id={$r['id']}' class='' target=''>edit</a></h3>";
			$n++;
		}
	}
	if(isset($_GET['add']) || isset($_GET['edit']) ) {
		if($_GET['edit'])
			$r=$db->fetch_assoc($db->query("SELECT * FROM blog WHERE id='".intval($_GET['id'])."'"));
		else
			$r['author']=$r['topic']=$r['article']=$r['topic_lat']="";
		$sel1=($r['author']=="Викторов А.В.")?"SELECTED":"";
		$sel2=($r['author']=="Авштолис В.И.")?"SELECTED":"";
	?>
		<h1>Редактирование</h1>
		<form method='POST' action='?id=<?=$r['id']?>#'>
			<div>АВТОР 
				<select name='author' style='font-size:18px; padding:5px;'> 
					<option <?=$sel1?> >Викторов А.В.</option>
					<option <?=$sel2?> >Авштолис В.И.</option>
				</select>
			</div>
			<div style='width:100%;'>НАЗВАНИЕ <input type='text' name='topic' value='<?=$r['topic']?>' style='width:80%;margin:10px; padding:3px;font-size:18px;'></div>
			<div style='width:100%;'>TRANSLIT <input type='text' name='topic_lat' value='<?=$r['topic_lat']?>' style='width:80%;margin:10px; padding:3px;font-size:18px;'></div>
			<textarea id="editor" name='article'>
				<?=$r['article']?>
			</textarea>
			<input type='submit' name='save' value=' СОХРАНИТЬ ' style='font-size:18px; padding:5px;margin:10px;'>
		</form>
		<script>
			ClassicEditor
				.create( document.querySelector( '#editor' ) )
				.catch( error => {
					console.error( error );
				} );
		</script>
		<form method='POST' action='?id=<?=$r['id']?>&htmlcode=yes#'>
			<div>АВТОР 
				<select name='author' style='font-size:18px; padding:5px;'> 
					<option <?=$sel1?> >Викторов А.В.</option>
					<option <?=$sel2?> >Авштолис В.И.</option>
				</select>
			</div>
			<div style='width:100%;'>НАЗВАНИЕ <input type='text' name='topic' value='<?=$r['topic']?>' style='width:80%;margin:10px; padding:3px;font-size:18px;'></div>
			<div style='width:100%;'>TRANSLIT <input type='text' name='topic_lat' value='<?=$r['topic_lat']?>' style='width:80%;margin:10px; padding:3px;font-size:18px;'></div>
			<textarea name='article' style='width:100%;height:400px;'>
				<?=$r['article']?>
			</textarea>
			<input type='submit' name='save' value=' СОХРАНИТЬ ' style='font-size:18px; padding:5px;margin:10px;'>
		</form>
	<?}?>
</body>
</html>
<?
function translit($st) {
    $table = array( 
                'А' => 'A', 
                'Б' => 'B', 
                'В' => 'V', 
                'Г' => 'G', 
                'Д' => 'D', 
                'Е' => 'E', 
                'Ё' => 'YO', 
                'Ж' => 'ZH', 
                'З' => 'Z', 
                'И' => 'I', 
                'Й' => 'J', 
                'К' => 'K', 
                'Л' => 'L', 
                'М' => 'M', 
                'Н' => 'N', 
                'О' => 'O', 
                'П' => 'P', 
                'Р' => 'R', 
                'С' => 'S', 
                'Т' => 'T', 
                'У' => 'U', 
                'Ф' => 'F', 
                'Х' => 'H', 
                'Ц' => 'C', 
                'Ч' => 'CH', 
                'Ш' => 'SH', 
                'Щ' => 'CSH', 
                'Ь' => '', 
                'Ы' => 'Y', 
                'Ъ' => '', 
                'Э' => 'E', 
                'Ю' => 'YU', 
                'Я' => 'YA', 
 
                'а' => 'a', 
                'б' => 'b', 
                'в' => 'v', 
                'г' => 'g', 
                'д' => 'd', 
                'е' => 'e', 
                'ё' => 'yo', 
                'ж' => 'zh', 
                'з' => 'z', 
                'и' => 'i', 
                'й' => 'j', 
                'к' => 'k', 
                'л' => 'l', 
                'м' => 'm', 
                'н' => 'n', 
                'о' => 'o', 
                'п' => 'p', 
                'р' => 'r', 
                'с' => 's', 
                'т' => 't', 
                'у' => 'u', 
                'ф' => 'f', 
                'х' => 'h', 
                'ц' => 'c', 
                'ч' => 'ch', 
                'ш' => 'sh', 
                'щ' => 'csh', 
                'ь' => '', 
                'ы' => 'y', 
                'ъ' => '', 
                'э' => 'e', 
                'ю' => 'yu', 
                'я' => 'ya',
                ' ' => '_', 
                ',' => '_',
                '\"' => '',
                '\'' => '',
    ); 
 
    $output = str_replace( 
        array_keys($table), 
        array_values($table),$st
    ); 
 
    return strtolower($output); 
}

?>
