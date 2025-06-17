<?php
$text = "The student have finish the assignment.";
$data = [
    'text' => $text,
    'language' => 'en-US'
];

$ch = curl_init('https://api.languagetoolplus.com/v2/check');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
$response = curl_exec($ch);
curl_close($ch);

echo $response;
