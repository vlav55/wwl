<?
define('CLIENT_SECRET',"c4ca4238a0b923820dcc509a6f75849b"); //"ec8ce6abb3e952a85b8551ba726a1227";
define('CLIENT_ID','19802'); //"4356436";
$data = [
    'land_num' => 3,
    'client_name' => 'Тест',
    'client_phone' => '+79119998877',
    'client_email' => '79119998877@mail.ru',
    'order_number' => '1223345',
    'product_id' => 1,
    'product_descr' => 'Все',
    'pay_system' => 'test',
    'payed_money' => 10,
    'promocode' => 'WinWinLand2554',
];

//print_r(api_call('/pay/',$data,'POST'));
print "<br>OK";

function api_call($endpoint,$data,$method) {
	$client_secret=CLIENT_SECRET;
	$client_id=CLIENT_ID;
	$url="https://api.winwinland.ru".$endpoint;
    $ch = curl_init();
    $data = http_build_query($data);
    if($method=='GET') {
		curl_setopt($ch, CURLOPT_URL, $url.'?'.$data);
	} elseif($method=='POST') {
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	} else
        return ['error'=>'method undefined'];
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode("$client_id:$client_secret") // Кодируем учетные данные в формате Base64
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    if ($response === false) {
        return ['error'=>'cURL Error: ' . curl_error($ch)];
    } else {
        return json_decode($response, true);
    }
}


exit;

$direct_code='';
$login='partner_152';
$passw='y67kz2p75l';
$par=[
	'save_bank_details' => 'yes',
	'uid' => -1002,
	'bank_details'=>'test123',
	'secret' => md5('-1002'.md5($ctrl_id)),
	];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://for16.ru/d/$ctrl_dir/dapi.php");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($par));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
echo "response: ".$response;
curl_close($ch);
?>
