<?php

namespace Wzulfikar\Sms;

interface SmsInterface
{
    /**
     * Send message to given phone number
     * @param  string $message message that will be sent
     * @param  string $phone   recipient's phone number
     * @return array           status of message and its id
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
     * @param  mixed $message_id  message id from provider
     * @return array              array representation of message's status
     */
    public function getStatus($message_id);

    /**
     * Get balance of sms from provider
     *
     * @return array    array of username & its balance
     */
    public function getBalance();
}
