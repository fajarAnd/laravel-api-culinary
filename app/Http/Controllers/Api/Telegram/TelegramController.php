<?php

namespace App\Http\Controllers\Api\Telegram;

use App\Http\Controllers\Controller;
use App\Services\Telegram\UpdateDispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function __construct(private UpdateDispatcher $dispatcher) {}

    public function webhook(Request $request): JsonResponse
    {
        // Validate secret token from Telegram
        $secretToken = config('services.telegram.webhook_secret');
        if ($secretToken && $request->header('X-Telegram-Bot-Api-Secret-Token') !== $secretToken) {
            return response()->json(['ok' => false], 403);
        }

        $update = $request->all();
        Log::debug('Telegram update received', ['update_id' => $update['update_id'] ?? null]);

        // Dispatch async-style: return 200 first, process after
        $this->dispatcher->dispatch($update);

        return response()->json(['ok' => true]);
    }
}