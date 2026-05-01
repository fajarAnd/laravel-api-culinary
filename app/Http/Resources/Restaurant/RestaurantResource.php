<?php

namespace App\Http\Resources\Restaurant;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['id'],
            'name' => $this->resource['name'],
            'address' => $this->resource['address'],
            'location' => [
                'lat' => $this->resource['lat'],
                'lng' => $this->resource['lng'],
            ],
            'cuisines' => $this->resource['cuisines'],
            'rating' => $this->resource['rating'],
            'price_range' => $this->resource['price_range'],
            'open_now' => $this->resource['open_now'],
            'photo_url' => $this->resource['photo_url'],
        ];
    }
}