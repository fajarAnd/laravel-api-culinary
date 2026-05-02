<?php

namespace App\Services\Telegram\Handlers;

use App\Services\Telegram\TelegramService;

abstract class MessageHandler
{
    public function __construct(protected TelegramService $telegram) {}

    abstract public function handle(array $update): void;

    protected function chatId(array $update): int|string
    {
        return $update['message']['chat']['id']
            ?? $update['callback_query']['message']['chat']['id']
            ?? 0;
    }

    protected function reply(int|string $chatId, string $text, array $options = []): void
    {
        $this->telegram->sendMessage($chatId, $text, $options);
    }
}
