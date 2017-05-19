<?php

namespace Wzulfikar\Sms\Driver;

use Wzulfikar\Sms\SmsInterface;

/**
 * see: www.fortdigital.com.sg
 */
class FortDigital implements SmsInterface
{
    private $base_url  = 'https://mx.fortdigital.net';
    private $endpoints = [
        'send'    => '/http/send-message',
        'status'  => '/http/request-status-update',
        'balance' => '/http/balance-sms',
    ];

    private $username;
    private $password;

    public function config(array $opts)
    {
        $this->sender   = $opts['sender'];
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

        // for testing purpose
        //$sendSms = explode(":","OK: message_id_10_9");

        $raw    = $this->fetchAndParse($this->endpoints['send'], $params);
        $status = SmsInterface::STATUS['QUEUED'];
        if (trim($raw[0]) == 'ERROR') {
            $status = SmsInterface::STATUS['FAILED'];
        }
        $message_id = trim($raw[1]);

        return compact('status', 'message_id', 'raw');
    }

    public function getStatus($message_id)
    {
        $params = [
            'message-id' => $message_id,
        ];

        $resp = $this->fetchAndParse($this->endpoints['status'], $params);

        $STATUSES   = SmsInterface::STATUS;
        $statusCode = $STATUSES['QUEUED'];
        if (strtolower($resp[0]) == 'success') {
            $statusCode = $STATUSES['SENT'];
        } else if (strtolower($resp[0]) == 'error') {
            $statusCode = $STATUSES['FAILED'];
        }

        return [
            'status' => $statusCode,
            'msg'    => trim($resp[1]),
            'raw'    => json_encode($resp),
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

        if ($resp[0] == 'ERROR') {
            throw new \Exception('Error in Driver/FortDigital: failed to get balance - ' . trim($resp[1]));
        }

        return [
            'user'    => $this->username,
            'balance' => intval(trim($resp[1])),
        ];
    }
}
