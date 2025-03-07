<?php

namespace TelegramBot\Api;

use TelegramBot\Api\Types\Message;

class Botlytics
{

    /**
     * @var string Tracker url
     */
    const BASE_URL = 'https://botlytics.co/api/v1/messages';

    /**
     * Guzzle object
     *
     * @var
     */
    protected $guzzle;


    /**
     * Botlytics token
     *
     * @var string
     */
    protected $token;

    /**
     * Botlytics constructor
     *
     * @param string $token
     *
     * @throws \Exception
     */
    public function __construct($token)
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException('Token should be a string');
        }

        $this->token = $token;
        $this->guzzle = new \GuzzleHttp\Client();
    }

    /**
     * Messages tracking
     *
     * @param \TelegramBot\Api\Types\Message $message
     * @param string $payload
     * @param string "incoming" or "outgoing" $kind
     *
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\HttpException
     */
    public function track(Message $message, $payload = 'Message', $kind = 'outgoing')
    {
        $headers = ['Content-Type'=>'application/json'];
        $body = [
            'message' => [
                'text'  => (empty($message->getText())) ? 'empty' : $message->getText(),
                'kind'  => $kind,
                'conversation_identifier' => $message->getChat()->getId(),
                'sender_identifier' => (!empty($message->getFrom()->getUsername())) ? $message->getFrom()->getUsername() : $message->getFrom()->getId(),
                'platform' => 'telegram',
                'payload' => $payload
            ]
        ];
        $body = json_encode($body, JSON_FORCE_OBJECT);
        $response = $this->guzzle->post(self::BASE_URL . "?token={$this->token}", ['headers' => $headers, 'body' => $body]);
        if ($response->getStatusCode() != 201) {
            throw new Exception('Error Processing Request: ' . $response->getStatusCode());
        }
    }
}
