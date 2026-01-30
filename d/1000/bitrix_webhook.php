<?
file_put_contents("bitrix_webhook.log","\n------------\n".date("d.m.Y H:i:s")."\n".print_r($_POST,true),FILE_APPEND);
print "ok";
?>
