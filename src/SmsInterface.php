<?php

namespace Wzulfikar\Sms;

interface SmsInterface
{
    const STATUS = [
        'QUEUED' => 0,
        'SENT'   => 1,
        'FAILED' => 2,
    ];

    /**
     * Send message to given phone number
     * @param  string $message message that will be sent
     * @param  string $phone   recipient's phone number
     * @return array           status of message and its id:
     *                         (int) status, (string) message_id, (json) raw
     */
    public function send($message, $to_phone);

    /**
     * Set configuration of sms driver
     *
     * @param  array  $opts array of config/option
     * @return void
     */
    public function config(array $opts);

    /**
     * Get status of a message
     *
     * @param  string $message_id  message id from provider
     * @return array              array representation of message's status:
     *                            (int) status, (string) msg, (json) raw
     */
    public function getStatus($message_id);

    /**
     * Get balance of sms from provider
     *
     * @return array    array of username & its balance
     */
    public function getBalance();
}
