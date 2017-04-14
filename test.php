<?php

// require __DIR__ . '/Sms.php';
require __DIR__ . '/providers/FortDigitalSmsProvider.php';

$config = [
    'sender' => 'put-sender-here',
    'username' => 'your-username-here',
    'password' => 'your-password-here',
];

$class = 'FortDigitalSmsProvider';
$interface = 'SmsInterface';

if (!(new ReflectionClass($class))->implementsInterface($interface)) {
    throw new Exception("$class must implemenst $interface");
}

$sms = new $class;
$sms->config($config);
var_dump($sms->getStatus(2332));
var_dump($sms->getBalance());
