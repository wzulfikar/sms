<?php

namespace Wzulfikar\Sms\Driver;

use Wzulfikar\Sms\SmsInterface;

/**
 * see: https://www.experttexting.com/appv2/Documentation
 */
class ExpertTexting implements SmsInterface
{
    private $base_url  = 'https://www.experttexting.com/ExptRestApi/sms/json';
    private $endpoints = [
        'send'    => '/Message/Send',
        'status'  => '/Message/Status',
        'balance' => '/Account/Balance',
    ];

    private $username;
    private $password;

    public function config(array $opts)
    {
        $this->sender   = $opts['sender'];
        $this->username = $opts['username'];
        $this->password = $opts['password'];
        $this->api_key  = $opts['api_key'];
    }

    private function makeEndpoint($path, array $params = [])
    {
        $params += [
            'username' => $this->username,
            'password' => $this->password,
            'api_key'  => $this->api_key,
        ];

        return $this->base_url . $path . '?' . http_build_query($params);
    }

    /**
     * Parse response from endpoint
     *
     * @param  string $resp response
     * @return array        response in form of associative array
     */
    private function parseResponse(string $resp)
    {
        return json_decode($resp, true);
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
            'to'   => $phone,
            'from' => $this->sender,
            'text' => htmlspecialchars($message),
        ];

        $sent = $this->fetchAndParse($this->endpoints['send'], $params);

        $status     = $sent['Status'];
        $message_id = $sent['Response']['message_id'];

        return compact('message_id', 'status');
    }

    /**
     * [getStatus description]
     * @param  string $message_id [description]
     * @return array             [description]
     */
    public function getStatus($message_id)
    {
        $params = [
            'message_id' => $message_id,
        ];

        $resp = $this->fetchAndParse($this->endpoints['status'], $params);

        $STATUSES   = SmsInterface::STATUS;
        $statusCode = $STATUSES['QUEUED'];
        if ($resp['Status'] == 0) {
            $statusCode = $STATUSES['SENT'];
        } else if ($resp['Status'] > 0) {
            $statusCode = $STATUSES['FAILED'];
        }

        return [
            'status' => $statusCode,
            'msg'    => $resp['Response']['Status'],
            'raw'    => json_encode($resp),
        ];
    }

    /**
     * Get status code of given message id
     *
     * @param  string $message_id [description]
     * @return array             [description]
     */
    public function getStatusCode($message_id)
    {
        return [
            'status' => $resp['Status'],
            'msg'    => $resp['Response']['Status'],
        ];
    }

    /**
     * Get balance of sms from provider.
     * ExpertTexting returns balance in USD, not in amount of sms.
     *
     * @return array    array of username & its balance
     */
    public function getBalance()
    {
        $resp = $this->fetchAndParse($this->endpoints['balance']);

        return [
            'user'    => $this->username,
            'balance' => $resp['Response']['Balance'],
        ];
    }
}
