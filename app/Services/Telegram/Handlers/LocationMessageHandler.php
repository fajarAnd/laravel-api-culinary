<?php

namespace App\Services\Telegram\Handlers;

use App\Contracts\RestaurantRepositoryInterface;
use App\Services\Telegram\TelegramService;

class LocationMessageHandler extends MessageHandler
{
    public function __construct(
        TelegramService $telegram,
        private RestaurantRepositoryInterface $restaurants
    ) {
        parent::__construct($telegram);
    }

    public function handle(array $update): void
    {
        $chatId = $update['message']['chat']['id'];
        $location = $update['message']['location'];
        $lat = $location['latitude'];
        $lng = $location['longitude'];

        $results = $this->restaurants->getNearby($lat, $lng);

        if (empty($results)) {
            $this->reply($chatId, "No restaurants found near your location.");
            return;
        }

        $this->reply($chatId, "📍 Found " . count($results) . " restaurants nearby:");

        foreach (array_slice($results, 0, 3) as $r) {
            $this->telegram->sendVenue($chatId, $r['lat'], $r['lng'], $r['name'], $r['address']);
        }
    }
}
