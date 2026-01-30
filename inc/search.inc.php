<?
$t=new top($database,"640px;",false,$favicon);
print "next generation";
$t->bottom();
exit;

$str="";
if(@$_GET['set_del']) { 
	mysql_query("UPDATE cards SET razdel=6 WHERE uid={$_GET['uid']}") or die(mysql_error());
}
if(@$_GET['str']) { 
	$str=$_GET['str'];
}

print "<h1>Search in incoming messages</h1>";
print "<form>
<div class='form-group'>
<input class='form-control' type='text' name='str' value='$str'>
<div class='checkbox'><label><input type='checkbox' name='chk_not'> Not included </label></div>
<button type='submit' name='search'  class='btn btn-default'>Search</button>
</div>
</form>";

if($str!="") {
	print "<div class='alert alert-warning'>Search in D only and no messages last 30 days!</div>";
	if(@$_GET['chk_not'])
		$not="NOT"; else $not="";
	$tm=time()-(30*24*60*60);
	$res=mysql_query("SELECT *, msgs.id AS id, msgs.uid AS uid, msgs.acc_id AS acc_id, msgs.tm AS tm 
		FROM msgs JOIN cards ON msgs.uid=cards.uid 
		WHERE cards.del=0 AND razdel=4 AND outg=0 AND msg $not LIKE '%$str%' AND msgs.tm<$tm
		ORDER BY cards.id, msgs.tm ");
	$prev_id=0; $uid=0; $ndiv=false;
	while($r=mysql_fetch_assoc($res)) {
		if($uid!=$r['uid']) {
			if($ndiv) {
				print "</div>";
			}
			print "<div class='well'>";
			$ndiv=true;
		}
		print "<div class='search' id='r_{$r['id']}'>
		<p class='search_p'>".date("d/m",$r['tm'])." 
		<a href='javascript:wopen(\"msg.php?uid={$r['uid']}&acc_id={$r['acc_id']}\");'>{$r['surname']} {$r['name']}</a>
		| <a href='?set_del=yes&uid={$r['uid']}&str=$str#r_$prev_id'>Set_del</a>
		</p>
		".nl2br($r['msg'])."
		</div>";
		$prev_id=$r['id'];
		$uid=$r['uid'];
	}
} else
	print "<h1>please input search string</h1>";
$t->bottom();

?>