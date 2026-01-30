<?
$url = "https://for16.ru/d/2322626082/dapi.php";
$land_num="1"; //* обязательно 1-основной лэндинг
$client_name="Вася"; //*
$client_phone="89119998877"; //*
$client_email="test@mail.ru"; //не обязательно
$secret=md5($land_num.$client_name.$client_phone.'98f13708210194c475687be6106a3b84');
$bc=1234567890; //партнерский код, который был передан в URL лэндинга ?bc=
$params=array('secret'=>$secret,
      'land_num'=>$land_num,
      'client_name'=>$client_name,
      'client_phone'=>$client_phone,
      'bc'=>$bc,
      );
// Initialize cURL session
$ch = curl_init();

// Set URL
curl_setopt($ch, CURLOPT_URL, $url);

// Return the response instead of outputting it
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Set the request method to POST
curl_setopt($ch, CURLOPT_POST, true);

// Set the data to send in the POST request
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

// Execute the request
$res = curl_exec($ch);

// Check for errors
if(curl_error($ch)) {
    echo 'Error: ' . curl_error($ch);
}

// Close the session
curl_close($ch);

// Process the result
echo $res;   // "ok" or "err";

?>
