<?php

namespace App\Services\Telegram\Handlers;

class ContactMessageHandler extends MessageHandler
{
    public function handle(array $update): void
    {
        $chatId = $update['message']['chat']['id'];
        $contact = $update['message']['contact'];
        $name = $contact['first_name'] ?? 'there';

        $this->reply($chatId, "👤 Contact received, thanks {$name}!");
    }
}
