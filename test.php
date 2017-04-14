<?php

require __DIR__ . '/Sms.php';

$config = [
    'base_url' => 'https://mx.fortdigital.net',
    'sender' => 'put-sender-here',
    'username' => 'your-username-here',
    'password' => 'your-password-here',
];
$sms = new Sms();
$sms->config($config);
echo $sms->getStatus(2332);
