<?
if(isset($_GET['create'])) {
	if(file_put_contents("chk_server.txt",time()))
		print "chk_server.txt created \n";
	else
		print "err creating\n";
} else {
	if(unlink("chk_server.txt"))
		print "chk_server.txt removed\n";
	else
		print "chk_server.txt is not exists\n";
}
?>
