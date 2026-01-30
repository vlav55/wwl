<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db('vkt');
header('Content-Type: application/json');
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$db->notify_me(print_r($data,true));
//~ Array
//~ (
    //~ [salon_id] => 1504761
    //~ [application_id] => 28143
    //~ [event] => uninstall
    //~ [partner_token] => 6zpzztnajb842aaubpxm
//~ )
if (json_last_error() === JSON_ERROR_NONE) {
    http_response_code(200);
    echo json_encode(['status' => 'success']);
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
}
print "<br>OK disconnect";
?>
