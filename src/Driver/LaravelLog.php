<?php

namespace Wzulfikar\Sms\Driver;

use Wzulfikar\Sms\SmsInterface;

/**
 * Log sms to laravel log
 */
class LaravelLog implements SmsInterface
{
    public function config(array $opts)
    {
        $this->sender = $opts['sender'];
        $this->username = $opts['username'];
        $this->password = $opts['password'];
    }

    public function send($message, $phone)
    {
        $params = [
            'to'      => $phone,
            'from'    => $this->sender,
            'message' => htmlspecialchars($message),
        ];

        \Log::info('Sending message with params', $params);

        $status = 1;
        $message_id = 1;
 
        return compact('message_id', 'status');
    }

    public function getStatus($message_id)
    {
        $params = [
            'message-id' => $message_id
        ];

        \Log::info('Getting status of message #' . $message_id);
        
        return [
            'status' => 1,
            'msg' => 1
        ];
    }

    /**
     * Get balance of sms from provider
     *
     * @return array    array of username & its balance
     */
    public function getBalance()
    {
        $bal = [
            'user' => $this->username,
            'balance' => 100
        ];
        \Log::info('Getting balance', $bal);
        return $bal;
    }
}
