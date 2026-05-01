<?php

namespace App\Contracts;

interface RestaurantRepositoryInterface
{
    public function search(string $query): array;

    public function findById(string $id): array;

    public function getMenu(string $restaurantId): array;

    public function getReviews(string $restaurantId): array;

    public function getNearby(float $lat, float $lng): array;

    public function searchLocations(string $query): array;
}