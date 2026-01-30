<?
http_response_code(200);
$txt=print_r($_GET,true)."\n\n";
file_put_contents("test_gk.log",date("d.m.Y H:i:s")."\n".print_r($_GET,true)."\n\n",FILE_APPEND);
print nl2br($txt);
?>
