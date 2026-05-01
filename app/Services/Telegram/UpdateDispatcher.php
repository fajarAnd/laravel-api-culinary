<?php

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Log;

class UpdateDispatcher
{
    public function __construct(private TelegramService $telegram) {}

    public function dispatch(array $update): void
    {
        try {
            if (isset($update['message'])) {
                $this->handleMessage($update['message']);
            } elseif (isset($update['callback_query'])) {
                $this->handleCallbackQuery($update['callback_query']);
            }
        } catch (\Exception $e) {
            Log::error('Telegram dispatch error: ' . $e->getMessage(), ['update' => $update]);
        }
    }

    private function handleMessage(array $message): void
    {
        $chatId = $message['chat']['id'];

        if (isset($message['text'])) {
            $this->handleText($chatId, $message);
        } elseif (isset($message['location'])) {
            $this->handleLocation($chatId, $message['location']);
        } elseif (isset($message['contact'])) {
            $this->handleContact($chatId, $message['contact']);
        } elseif (isset($message['photo'])) {
            $this->handleUnsupported($chatId, 'photo');
        } elseif (isset($message['video'])) {
            $this->handleUnsupported($chatId, 'video');
        } elseif (isset($message['voice'])) {
            $this->handleUnsupported($chatId, 'voice');
        } elseif (isset($message['document'])) {
            $this->handleUnsupported($chatId, 'document');
        }
    }

    private function handleText(int|string $chatId, array $message): void
    {
        $text = trim($message['text']);

        if (str_starts_with($text, '/')) {
            [$command, $args] = $this->parseCommand($text);

            match ($command) {
                '/start' => $this->telegram->sendMessage($chatId, $this->welcomeMessage()),
                '/help' => $this->telegram->sendMessage($chatId, $this->helpMessage()),
                '/search' => $this->handleSearch($chatId, $args),
                '/restaurant' => $this->handleRestaurantDetail($chatId, $args),
                '/menu' => $this->handleMenu($chatId, $args),
                '/reviews' => $this->handleReviews($chatId, $args),
                default => $this->telegram->sendMessage($chatId, "Unknown command. Type /help for available commands."),
            };
        } else {
            $this->telegram->sendMessage($chatId, "Use /search <restaurant name> to find restaurants.");
        }
    }

    private function handleLocation(int|string $chatId, array $location): void
    {
        $this->telegram->sendMessage($chatId, "📍 Location received! Searching nearby restaurants...");

        $lat = $location['latitude'];
        $lng = $location['longitude'];

        // RestaurantService will be injected in a later task
        $this->telegram->sendMessage($chatId, "Use /search to find restaurants by name for now.");
    }

    private function handleContact(int|string $chatId, array $contact): void
    {
        $name = $contact['first_name'] ?? 'there';
        $this->telegram->sendMessage($chatId, "👤 Contact received, thanks {$name}!");
    }

    private function handleCallbackQuery(array $callbackQuery): void
    {
        $this->telegram->answerCallbackQuery($callbackQuery['id']);
    }

    private function handleUnsupported(int|string $chatId, string $type): void
    {
        $messages = [
            'photo' => "📷 Photo received. Send a restaurant name or location to search.",
            'video' => "🎥 Video received. This bot only handles text and location.",
            'voice' => "🎤 Voice messages are not supported. Please use text commands.",
            'document' => "📄 Document received. This bot only handles text and location.",
        ];

        $this->telegram->sendMessage($chatId, $messages[$type] ?? "This message type is not supported.");
    }

    private function handleSearch(int|string $chatId, string $query): void
    {
        if (empty($query)) {
            $this->telegram->sendMessage($chatId, "Usage: /search <restaurant name>\nExample: /search sate");
            return;
        }

        $this->telegram->sendMessage($chatId, "🔍 Searching for: <b>{$query}</b>\n\nUse the REST API at /api/restaurants?q={$query}");
    }

    private function handleRestaurantDetail(int|string $chatId, string $id): void
    {
        if (empty($id)) {
            $this->telegram->sendMessage($chatId, "Usage: /restaurant <id>");
            return;
        }

        $this->telegram->sendMessage($chatId, "Use /api/restaurants/{$id} for restaurant details.");
    }

    private function handleMenu(int|string $chatId, string $id): void
    {
        if (empty($id)) {
            $this->telegram->sendMessage($chatId, "Usage: /menu <restaurant_id>");
            return;
        }

        $this->telegram->sendMessage($chatId, "Use /api/restaurants/{$id}/menu for the menu.");
    }

    private function handleReviews(int|string $chatId, string $id): void
    {
        if (empty($id)) {
            $this->telegram->sendMessage($chatId, "Usage: /reviews <restaurant_id>");
            return;
        }

        $this->telegram->sendMessage($chatId, "Use /api/restaurants/{$id}/reviews for reviews.");
    }

    private function parseCommand(string $text): array
    {
        $parts = explode(' ', $text, 2);
        $command = strtolower(explode('@', $parts[0])[0]);
        $args = trim($parts[1] ?? '');

        return [$command, $args];
    }

    private function welcomeMessage(): string
    {
        return "👋 Welcome to <b>Culinary Bot</b>!\n\n" . $this->helpMessage();
    }

    private function helpMessage(): string
    {
        return "Available commands:\n"
            . "/search <name> — Search restaurants\n"
            . "/restaurant <id> — Restaurant details\n"
            . "/menu <id> — Restaurant menu\n"
            . "/reviews <id> — Restaurant reviews\n\n"
            . "Or send your 📍 <b>location</b> to find nearby restaurants!";
    }
}