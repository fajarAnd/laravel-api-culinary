<?php

namespace App\Console\Commands;

use App\Services\Telegram\TelegramService;
use Illuminate\Console\Command;

class SetTelegramWebhook extends Command
{
    protected $signature = 'telegram:set-webhook {--remove : Remove the webhook}';
    protected $description = 'Register the Telegram webhook URL';

    public function handle(TelegramService $telegram): int
    {
        if ($this->option('remove')) {
            $result = $telegram->setWebhook('');
            $this->info('Webhook removed.');
            return self::SUCCESS;
        }

        $url = config('services.telegram.webhook_url');

        if (!$url) {
            $this->error('TELEGRAM_WEBHOOK_URL is not set in .env');
            return self::FAILURE;
        }

        $secret = config('services.telegram.webhook_secret');
        $result = $telegram->setWebhook($url, $secret);

        if ($result['ok'] ?? false) {
            $this->info("Webhook set to: {$url}");
        } else {
            $this->error('Failed: ' . ($result['description'] ?? 'unknown error'));
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}