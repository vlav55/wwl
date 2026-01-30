<?
$db=new top($database,0,false);

if(!isset($limit))
	$limit=3;


$db->connect("vktrade");
$bs=new bs;
print "<div class='alert alert-info' ><h2>Сканирование опросов</h2></div>";

if(isset($_GET['do_send'])) {
//	$db->print_r($_GET);
	$arr=explode("\n",$_GET['posts']);
	//$db->print_r($arr);
	$votes=""; $n=0; $error=false;
	foreach($arr AS $w) {
		if(empty(trim($w)))
			continue;
		if($n++ >=$limit) {
			print "<div class='alert alert-warning'>Ограничение - 3 ссылки, поставлены на сканирование первые три ссылки</div>";
			break;
		}
		if(preg_match("|wall-[0-9]+_([0-9]+)|",trim($w),$r)) {
			$vote=intval(trim($r[1]));
			if(!$vote) {
				print "<div class='alert alert-warning'>Ошибка в ссылке <b>$w</b> - см. примеры ниже</div>";
				$error=true;
				break;
			}
			$votes.=$vote.",";
		} else {
			print "<div class='alert alert-warning'>Ошибка в ссылке <b>$w</b> - см. примеры ниже</div>";
			$error=true;
			break;
		}
	}
	if(!$error) {
		print "<div class='label label-info' >$votes</div>";
		$db->query("UPDATE customers SET votes='".$db->escape($votes)."' WHERE id=$customer_id");
		print "<h3>Готово!</h3>";
		print "<div class='alert alert-success' >Один раз в час новые проголосовавшие в указанных опросах будут сканироваться в клиентскую базу</div>";
	}
	print "</div><hr>";
}

$arr=explode(",",$db->dlookup("votes","customers","id=$customer_id"));
$votes="";
foreach($arr AS $vote) {
	if(!$vote)
		continue;
	$votes.="https://vk.com/$VK_GROUP_NAME?w=wall-$VK_GROUP_ID"."_".$vote."\n";
}
?>

<?
$r=$db->fetch_assoc($db->query("SELECT * FROM vklist_acc WHERE del=0 AND last_error=0 ORDER BY id LIMIT 1"));
$f_name=$r['name'];
$l_name=$r['surname'];
$login=$r['login'];
$passw=$r['passw'];
?>
<div class='well' >
<form>
	<div class="form-group">
		<label for="comment">
			Cсылки на посты в группе, опросы по которым нужно сканировать (в столбик, не более <b><?=$limit?></b>) <br>
			пример: <br>
			https://vk.com/vktradecrm?w=wall-156136843_73 <br>
			https://vk.com/wall-63082191_989 <br>
<!--
			<div class='alert alert-danger' >* Важно : необходимо зайти в аккаунт :<?=$f_name?> <?=$l_name?> <br>
			 (<?=$login?> / <?=$passw?> ) <br>
			и проголосовать в каждом опросе, иначе сканирование работать не будет!</div>
-->
			
		</label>
		<textarea class="form-control" rows="5" name="posts" style='width:500px;'><?=$votes?></textarea>
		<button type="submit" class="btn btn-default" name='do_send'>Сохранить</button></button>
	</div>	
</form>
</div>
<?
$db->bottom();



?>
