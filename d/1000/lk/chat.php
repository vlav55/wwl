<?
include_once "/var/www/vlav/data/www/wwl/inc/top.class.php";
chdir("..");
include "init.inc.php";
$db=new top($database,"CHAT",false);
$chat_id='-1003676790021';
if(isset($_POST['msg'])) {
	if(!empty(trim($_POST['msg']))) {
		$db->notify_chat($chat_id,$_POST['msg']);
		print "<p class='alert alert-success' >Sent</p>";
		sleep(3);
		//print "<script>location='?'</script>";
	}
}
?>
<div class='container' >
<h3><?=$chat_id?></h3>
<form method='POST' action='#'>
<textarea class='form-control' rows='10' name='msg'></textarea>
<button type='submit' class='btn btn-primary my-2' >Send</button>
</form>
</div>
<?
$db->bottom();
?>
 
