<?php

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $baseUrl;

    public function __construct()
    {
        $token = config('services.telegram.bot_token');
        $this->baseUrl = "https://api.telegram.org/bot{$token}";
    }

    public function setWebhook(string $url, string $secretToken = null): array
    {
        $params = ['url' => $url];

        if ($secretToken) {
            $params['secret_token'] = $secretToken;
        }

        return $this->call('setWebhook', $params);
    }

    public function getMe(): array
    {
        return $this->call('getMe');
    }

    public function sendMessage(int|string $chatId, string $text, array $options = []): array
    {
        return $this->call('sendMessage', array_merge([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ], $options));
    }

    public function sendPhoto(int|string $chatId, string $photo, string $caption = ''): array
    {
        return $this->call('sendPhoto', [
            'chat_id' => $chatId,
            'photo' => $photo,
            'caption' => $caption,
        ]);
    }

    public function sendLocation(int|string $chatId, float $lat, float $lng): array
    {
        return $this->call('sendLocation', [
            'chat_id' => $chatId,
            'latitude' => $lat,
            'longitude' => $lng,
        ]);
    }

    public function sendVenue(int|string $chatId, float $lat, float $lng, string $title, string $address): array
    {
        return $this->call('sendVenue', [
            'chat_id' => $chatId,
            'latitude' => $lat,
            'longitude' => $lng,
            'title' => $title,
            'address' => $address,
        ]);
    }

    public function sendContact(int|string $chatId, string $phone, string $firstName): array
    {
        return $this->call('sendContact', [
            'chat_id' => $chatId,
            'phone_number' => $phone,
            'first_name' => $firstName,
        ]);
    }

    public function answerCallbackQuery(string $callbackQueryId, string $text = ''): array
    {
        return $this->call('answerCallbackQuery', [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
        ]);
    }

    private function call(string $method, array $params = []): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/{$method}", $params);
            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::error("Telegram API error [{$method}]: " . $e->getMessage());
            return ['ok' => false, 'description' => $e->getMessage()];
        }
    }
}