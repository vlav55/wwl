<?php

// API key
$api_key ="Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJCQlpvRXFsWl9VYWZ4cmZiRWFiVVVnIiwianRpIjoiNDNlMjc3ODctNDE4Yy00ZTBjLThiODgtMDQwZTYxZjM3MmM5IiwiaWF0IjoxNzUzNzkzMDIzLCJleHAiOjIwNjkzMjU4MjMsInVzZXIiOiJ7XCJ0eXBlXCI6XCJhcGlcIixcImlkXCI6XCJCQlpvRXFsWl9VYWZ4cmZiRWFiVVVnXCIsXCJlbWFpbFwiOm51bGwsXCJmaXJzdE5hbWVcIjpcItCw0L_QuDFcIixcImxhc3ROYW1lXCI6bnVsbCxcInBob25lXCI6bnVsbCxcInNjaG9vbElkXCI6XCJFWWdYTlJyUzFFZXp5Q3VGcUI3cmd3XCIsXCJjb21tdW5pY2F0aW9uSWRcIjpudWxsLFwiY29uY3VycmVuY3lTdGFtcFwiOm51bGwsXCJzZXNzaW9uSWRcIjpudWxsLFwiY2hhdFVzZXJEYXRhXCI6e1wiaWRcIjpcIlRfMWY4dGVEcDBtWlF6Z0J2cUZxUVFcIixcInJvbGVzXCI6WzBdfX0iLCJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9zeXN0ZW0iOiJBZG1pbiIsIm5iZiI6MTc1Mzc5MzAyMywiaXNzIjoiQWNjZWwiLCJhdWQiOiJhcHAueGwucnUifQ.B_e4oJxE3blmmaejQuhIUCI10R2pSlvOLQ4_t7mZMnk";

// Scenario ID
$scenarioId = "p41KT9H19kONkgvpDY27xw";
$scenarioId = "KUXSmRfMFk-Hy19uRBMRAg";

// Contact data
$contactData = [
    'email' => 'test@test.com',
    'firstName' => 'Имя',
    'lastName' => 'Фамилия',
    'phone' => '+79990001122'
];

// Payload
$data = [
    'scenarioId' => $scenarioId,
    'contactData' => $contactData,
    'amount' => 1234
];

// Initiate cURL
$ch = curl_init('https://app.xl.ru/api/v1/scenario/run');

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: $api_key", "Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Execute the request
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
} else {
    // Print response
    echo 'Response: ' . $response;
}

// Close cURL resource
curl_close($ch);
?>
