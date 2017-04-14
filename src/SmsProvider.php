<?php

namespace Wzulfikar\Sms;

use Wzulfikar\Sms\SmsInterface;

class SmsProvider
{
    public static function make($driver, array $config)
    {
        $interface = SmsInterface::class;
        if (!(new \ReflectionClass($driver))->implementsInterface($interface)) {
            throw new \Exception("$driver must implement $interface");
        }
        $sms = new $driver;
        $sms->config($config);
        return $sms;
    }
}
