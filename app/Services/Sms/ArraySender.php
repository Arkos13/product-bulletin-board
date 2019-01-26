<?php

namespace App\Services\Sms;

class ArraySender implements SmsSender
{
    private $messages = [];

    /**
     * @param $number
     * @param $text
     */
    public function send($number, $text): void
    {
        $this->messages[] = [
            'to' => '+' . trim($number, '+'),
            'text' => $text
        ];
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}