<?php

/**
 * ExpertTexting documentation:
 * https://www.experttexting.com/appv2/Documentation
 */

require_once __DIR__ . '/vendor/autoload.php';

use Wzulfikar\Sms\Driver\ExpertTexting;
use Wzulfikar\Sms\SmsProvider;

$config = [
    'sender'   => 'sender-name',
    'username' => 'your-ET-username',
    'password' => 'your-ET-password',
    'api_key'  => 'your-api-key',
];

// test using fort digital driver
$sms = SmsProvider::make(ExpertTexting::class, $config);

// var_dump($sms->send("Hello world. Test from expert texting.", "60142616200"));
// var_dump($sms->getStatus("120129948"));
var_dump($sms->getBalance());
