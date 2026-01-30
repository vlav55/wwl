<?php
$access_token = '9xopuzlqztm0h6e748l77ftohch9uoxw9vsx9s87';
$api_url = 'https://callapi-jsonrpc.novofon.ru/v4.0';
$virtual_phone_number='78124251296';
$contact_number='79119841012';

$data = [
    'jsonrpc' => '2.0',
    'method' => 'start.informer_call',
    'id' => 'test_' . time(),
    'params' => [
        'access_token' => $access_token,
        'virtual_phone_number' => $virtual_phone_number,
        'contact' => $contact_number,
        'contact_message' => [
            'type' => 'tts',
            'value' => 'Здравствуйте! Это тестовое сообщение.'
        ]
    ]
];

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));

$result = curl_exec($ch);
curl_close($ch);

echo $result;
