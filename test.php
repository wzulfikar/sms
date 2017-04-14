<?php

require_once __DIR__ . '/vendor/autoload.php';

use Wzulfikar\Sms\Driver\FortDigital;
use Wzulfikar\Sms\SmsProvider;

$config = [
    'sender' => 'put-sender-here',
    'username' => 'your-username-here',
    'password' => 'your-pass-here',
];

// test using fort digital driver
$sms = SmsProvider::make(FortDigital::class, $config);

var_dump($sms->getStatus(2332));
var_dump($sms->getBalance());
