<?php

namespace Wzulfikar\Sms\Driver;

use Wzulfikar\Sms\SmsInterface;

/**
 * see: www.fortdigital.com.sg
 */
class FortDigital implements SmsInterface
{
    private $base_url = 'https://mx.fortdigital.net';
    private $endpoints  = [
        'send'    => '/http/send-message',
        'status'  => '/http/request-status-update',
        'balance' => '/http/balance-sms',
    ];

    private $username;
    private $password;

    public function config(array $opts)
    {
        $this->sender = $opts['sender'];
        $this->username = $opts['username'];
        $this->password = $opts['password'];
    }

    private function makeEndpoint($path, array $params = [])
    {
        $params += [
            'username' => $this->username,
            'password' => $this->password,
        ];

        return $this->base_url . $path . '?' . http_build_query($params);
    }

    /**
     * Parse response from endpoint
     *
     * @param  string $resp response
     * @return array        array representation of response
     */
    private function parseResponse(string $resp)
    {
        return explode(":", $resp);
    }

    /**
     * Send request to endpoint
     *
     * @param  string $path   url path to endpoint
     * @param  array  $params query parameters to send
     * @return string         response
     */
    private function fetch($path, array $params = [])
    {
        $endpoint = $this->makeEndpoint($path, $params);
        return file_get_contents($endpoint);
    }
    
    private function fetchAndParse($path, array $params = [])
    {
        $resp = $this->fetch($path, $params);
        return $this->parseResponse($resp);
    }

    public function send($message, $phone)
    {
        $params = [
            'to'      => $phone,
            'from'    => $this->sender,
            'message' => htmlspecialchars($message),
        ];

        $sent = $this->fetchAndParse($this->endpoints['send-message'], $params);

        //$sendSms = explode(":","OK: utamastudio_10_9"); // for testing purpose
        $status = trim($sent[0]);
        $message_id = trim($sent[1]);
 
        return compact('message_id', 'status');
    }

    public function getStatus($message_id)
    {
        $params = [
            'message-id' => $message_id
        ];

        $resp = $this->fetchAndParse($this->endpoints['status'], $params);
        
        return [
            'status' => strtolower($resp[0]),
            'msg' => trim($resp[1])
        ];
    }

    /**
     * Get balance of sms from provider
     *
     * @return array    array of username & its balance
     */
    public function getBalance()
    {
        $resp = $this->fetchAndParse($this->endpoints['balance']);
        return [
            'user' => $this->username,
            'balance' => trim((int)$resp[1])
        ];
    }
}
