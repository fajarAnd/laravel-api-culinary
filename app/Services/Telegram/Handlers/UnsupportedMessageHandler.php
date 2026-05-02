<?php

namespace App\Services\Telegram\Handlers;

class UnsupportedMessageHandler extends MessageHandler
{
    private array $messages = [
        'photo'    => "📷 Photo received. Send a restaurant name or your location to search.",
        'video'    => "🎥 Video received. This bot only handles text and location.",
        'voice'    => "🎤 Voice messages are not supported. Please use text commands.",
        'document' => "📄 Document received. This bot only handles text and location.",
    ];

    public function __construct(
        \App\Services\Telegram\TelegramService $telegram,
        private string $type
    ) {
        parent::__construct($telegram);
    }

    public function handle(array $update): void
    {
        $chatId = $update['message']['chat']['id'];
        $this->reply($chatId, $this->messages[$this->type] ?? "This message type is not supported.");
    }
}
