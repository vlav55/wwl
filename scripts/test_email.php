<?
include "/var/www/vlav/data/www/wwl/inc/unisender.class.php";
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$title='test_email';
include "../top.inc.php";
chdir("../d/1000/");
include("init.inc.php");
$db=new db('vkt');
$db->db200=$DB200;

if(isset($_GET['send'])) {
	$api_key='6s1414bffqhg69c1ggzw79wrtgw6zstdbd4k161o';
	$uni=new unisender($api_key,$from_email="info@winwinland.ru",$from_name="WinWInLand");
	//$uni=new unisender($api_key,$from_email="an@winwinland.ru",$from_name="Артем Нураев");
	$email=$_GET['email'];
	$templ=$_GET['templ'];
	$uid=$db->dlookup("uid_md5","cards","email='$email'");
	$klid=$db->dlookup("id","cards","uid='$uid'");
	$cabinet_link=$db->get_direct_code_link($klid);

	//print $res = $uni->check_email($email) ? "true <br>" : "false <br>"; 

	$uni->email_by_template($email,$templ,$vars=['client_name'=>'Антонина','email'=>'test']);
	print "OK\n".print_r($uni->res,true);

	//$uni->email($email='vlav@mail.ru',$subj='test123',$body='test');
}
?>
<div class='container' >
	<br><br><br>
<form>
	Template: <input type='text' name='templ' class='form-control my-1' value='<?=$templ?>'>
	Email: <input type='text' class='form-control  my-1' name='email' value='<?=$email?>'> 
<button type='submit' class=' btn btn-primary my-1' name='send' value='yes'>Send</button>
</form>
</div>
<?

include "bottom.inc.php";
exit;

include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "/var/www/vlav/data/www/wwl/inc/sendpulse.class.php";

				$sp=new sendpulse('vkt');
				$sp->email_by_template($sp_template='sarafan_payment_success.html',
									$client_email='vlav@mail.ru',
									$client_name='VLAV',
									$subj="🔶 descr",
									$from_email='info@winwinland.ru',
									$from_name='WINWINLAND',
									$uid=-1002,$passw='12345');
print "OK";
exit;

$db=new db('vkt');
$db->vkt_email("321","123");
//~ if($db->email($emails=['info@winwinland.ru'], "123", "test", $from="info@winwinland.ru",$fromname="WWL", $add_globals=true))
	//~ print "OK"; else print "FALSE";
exit;
$us=new unisender('6s1414bffqhg69c1ggzw79wrtgw6zstdbd4k161o','info@winwinland.ru','WWL');
$us->email('info@winwinland.ru',"test","test \n test");
exit;

require 'guzzle/vendor/autoload.php';

$headers = array(
    'Content-Type' => 'application/json',
    'Accept' => 'application/json',
    'X-API-KEY' => '6s1414bffqhg69c1ggzw79wrtgw6zstdbd4k161o',
);

$client = new \GuzzleHttp\Client([
    'base_uri' => 'https://go1.unisender.ru/ru/transactional/api/v1/'
]);

$requestBody = [
  "message" => [
    "recipients" => [
      [
        "email" => "info@winwinland.ru",
      ]
    ],
    "template_id" => "d5779d52-02f3-11ee-bea2-020fb5ac13a7",
    "skip_unsubscribe" => 0,
    "global_language" => "ru",
    "template_engine" => "simple",
    "global_substitutions" => [
      "property1" => "string",
      "property2" => "string"
    ],
    "global_metadata" => [
      "property1" => "string",
      "property2" => "string"
    ],
    "from_email" => "info@winwinland.ru",
    "from_name" => "WINWINLAND",
    "reply_to" => "info@winwinland.ru",
    "track_links" => 0,
    "track_read" => 0,
    "bypass_global" => 0,
    "bypass_unavailable" => 0,
    "bypass_unsubscribed" => 0,
    "bypass_complained" => 0,
    "headers" => [
      "X-MyHeader" => "some data",
      "List-Unsubscribe" => "<mailto: unsubscribe@example.com?subject=unsubscribe>, <http://www.example.com/unsubscribe/{{CustomerId}}>"
    ],
    "options" => [
    ]
  ]
];

try {
    $response = $client->request('POST','email/send.json', array(
        'headers' => $headers,
        'json' => $requestBody,
       )
    );
    print_r(json_decode($response->getBody()->getContents(),true));
 }
 catch (\GuzzleHttp\Exception\BadResponseException $e) {
    // handle exception or api errors.
    print_r($e->getMessage());
 }

try {
    $response = $client->request('POST','email/send.json', array(
        'headers' => $headers,
        'json' => $requestBody,
       )
    );
    print_r($response->getBody()->getContents());
 }
 catch (\GuzzleHttp\Exception\BadResponseException $e) {
    // handle exception or api errors.
    print_r($e->getMessage());
 }
?>
