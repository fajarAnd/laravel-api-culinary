<?php

namespace App\Services\Telegram\Handlers;

use App\Contracts\RestaurantRepositoryInterface;
use App\Services\Telegram\TelegramService;

class TextMessageHandler extends MessageHandler
{
    public function __construct(
        TelegramService $telegram,
        private RestaurantRepositoryInterface $restaurants
    ) {
        parent::__construct($telegram);
    }

    public function handle(array $update): void
    {
        $message = $update['message'];
        $chatId = $message['chat']['id'];
        $text = trim($message['text']);

        if (!str_starts_with($text, '/')) {
            $this->reply($chatId, "Use /search <restaurant name> to find restaurants.");
            return;
        }

        [$command, $args] = $this->parseCommand($text);

        match ($command) {
            '/start'  => $this->reply($chatId, $this->welcomeMessage()),
            '/help'   => $this->reply($chatId, $this->helpMessage()),
            '/search' => $this->handleSearch($chatId, $args),
            '/restaurant' => $this->handleDetail($chatId, $args),
            '/menu'   => $this->handleMenu($chatId, $args),
            '/reviews' => $this->handleReviews($chatId, $args),
            default   => $this->reply($chatId, "Unknown command. Type /help for available commands."),
        };
    }

    private function handleSearch(int|string $chatId, string $query): void
    {
        if (empty($query)) {
            $this->reply($chatId, "Usage: /search <restaurant name>\nExample: /search sate");
            return;
        }

        $results = $this->restaurants->search($query);

        if (empty($results)) {
            $this->reply($chatId, "No restaurants found for \"<b>{$query}</b>\".");
            return;
        }

        $lines = ["🔍 Results for <b>{$query}</b>:\n"];
        foreach ($results as $r) {
            $lines[] = "🍽 <b>{$r['name']}</b>\n"
                . "📍 {$r['address']}\n"
                . "⭐ {$r['rating']} | /restaurant {$r['id']}";
        }

        $this->reply($chatId, implode("\n\n", $lines));
    }

    private function handleDetail(int|string $chatId, string $id): void
    {
        if (empty($id)) {
            $this->reply($chatId, "Usage: /restaurant <id>");
            return;
        }

        $r = $this->restaurants->findById($id);

        if (empty($r)) {
            $this->reply($chatId, "Restaurant not found.");
            return;
        }

        $status = $r['open_now'] ? '🟢 Open' : '🔴 Closed';
        $text = "<b>{$r['name']}</b>\n"
            . "📍 {$r['address']}\n"
            . "🍴 " . implode(', ', $r['cuisines']) . "\n"
            . "⭐ {$r['rating']} | {$status}\n\n"
            . "/menu {$r['id']} | /reviews {$r['id']}";

        $this->reply($chatId, $text);
    }

    private function handleMenu(int|string $chatId, string $id): void
    {
        if (empty($id)) {
            $this->reply($chatId, "Usage: /menu <restaurant_id>");
            return;
        }

        $menu = $this->restaurants->getMenu($id);

        if (empty($menu)) {
            $this->reply($chatId, "Menu not available for this restaurant.");
            return;
        }

        $lines = ["📋 <b>Menu</b>\n"];
        foreach ($menu as $category) {
            $lines[] = "<b>{$category['category']}</b>";
            foreach ($category['items'] as $item) {
                $price = number_format($item['price'], 0, ',', '.');
                $lines[] = "• {$item['name']} — Rp {$price}";
            }
        }

        $this->reply($chatId, implode("\n", $lines));
    }

    private function handleReviews(int|string $chatId, string $id): void
    {
        if (empty($id)) {
            $this->reply($chatId, "Usage: /reviews <restaurant_id>");
            return;
        }

        $reviews = $this->restaurants->getReviews($id);

        if (empty($reviews)) {
            $this->reply($chatId, "No reviews yet for this restaurant.");
            return;
        }

        $lines = ["💬 <b>Reviews</b>\n"];
        foreach (array_slice($reviews, 0, 5) as $review) {
            $stars = str_repeat('⭐', $review['rating']);
            $lines[] = "{$stars} <b>{$review['author']}</b>\n{$review['text']}";
        }

        $this->reply($chatId, implode("\n\n", $lines));
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
            . "/search &lt;name&gt; — Search restaurants\n"
            . "/restaurant &lt;id&gt; — Restaurant details\n"
            . "/menu &lt;id&gt; — Restaurant menu\n"
            . "/reviews &lt;id&gt; — Restaurant reviews\n\n"
            . "Or send your 📍 <b>location</b> to find nearby restaurants!";
    }
}
