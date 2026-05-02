<?php

namespace App\Services\Telegram;

use App\Contracts\RestaurantRepositoryInterface;
use App\Services\Telegram\Handlers\ContactMessageHandler;
use App\Services\Telegram\Handlers\LocationMessageHandler;
use App\Services\Telegram\Handlers\TextMessageHandler;
use App\Services\Telegram\Handlers\UnsupportedMessageHandler;
use Illuminate\Support\Facades\Log;

class UpdateDispatcher
{
    public function __construct(
        private TelegramService $telegram,
        private RestaurantRepositoryInterface $restaurants
    ) {}

    public function dispatch(array $update): void
    {
        try {
            if (isset($update['message'])) {
                $this->handleMessage($update['message'], $update);
            } elseif (isset($update['callback_query'])) {
                $this->telegram->answerCallbackQuery($update['callback_query']['id']);
            }
        } catch (\Exception $e) {
            Log::error('Telegram dispatch error: ' . $e->getMessage(), [
                'update_id' => $update['update_id'] ?? null,
            ]);
        }
    }

    private function handleMessage(array $message, array $update): void
    {
        $handler = match (true) {
            isset($message['text'])     => new TextMessageHandler($this->telegram, $this->restaurants),
            isset($message['location']) => new LocationMessageHandler($this->telegram, $this->restaurants),
            isset($message['contact'])  => new ContactMessageHandler($this->telegram),
            isset($message['photo'])    => new UnsupportedMessageHandler($this->telegram, 'photo'),
            isset($message['video'])    => new UnsupportedMessageHandler($this->telegram, 'video'),
            isset($message['voice'])    => new UnsupportedMessageHandler($this->telegram, 'voice'),
            isset($message['document']) => new UnsupportedMessageHandler($this->telegram, 'document'),
            default                     => null,
        };

        $handler?->handle($update);
    }
}
