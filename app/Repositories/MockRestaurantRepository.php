<?php

namespace App\Repositories;

use App\Contracts\RestaurantRepositoryInterface;

class MockRestaurantRepository implements RestaurantRepositoryInterface
{
    private function restaurants(): array
    {
        return [
            [
                'id' => 'r001',
                'name' => 'Sate Khas Senayan',
                'address' => 'Jl. Kebon Sirih No.31A, Menteng, Jakarta Pusat',
                'lat' => -6.1864,
                'lng' => 106.8226,
                'cuisines' => ['Indonesian', 'Sate', 'Grilled'],
                'rating' => 4.5,
                'price_range' => 2,
                'open_now' => true,
                'photo_url' => null,
            ],
            [
                'id' => 'r002',
                'name' => 'Padang Merdeka',
                'address' => 'Jl. Merdeka Barat No.12, Gambir, Jakarta Pusat',
                'lat' => -6.1700,
                'lng' => 106.8295,
                'cuisines' => ['Indonesian', 'Padang', 'Minang'],
                'rating' => 4.3,
                'price_range' => 1,
                'open_now' => true,
                'photo_url' => null,
            ],
            [
                'id' => 'r003',
                'name' => 'Pizza Roma',
                'address' => 'Jl. Sudirman No.54, Setiabudi, Jakarta Selatan',
                'lat' => -6.2148,
                'lng' => 106.8229,
                'cuisines' => ['Italian', 'Pizza', 'Pasta'],
                'rating' => 4.1,
                'price_range' => 3,
                'open_now' => false,
                'photo_url' => null,
            ],
            [
                'id' => 'r004',
                'name' => 'Warung Nasi Ampera',
                'address' => 'Jl. Dago No.77, Coblong, Bandung',
                'lat' => -6.8826,
                'lng' => 107.6095,
                'cuisines' => ['Indonesian', 'Sundanese'],
                'rating' => 4.4,
                'price_range' => 1,
                'open_now' => true,
                'photo_url' => null,
            ],
            [
                'id' => 'r005',
                'name' => 'Soto Betawi Haji Husen',
                'address' => 'Jl. Otista No.18, Jatinegara, Jakarta Timur',
                'lat' => -6.2147,
                'lng' => 106.8733,
                'cuisines' => ['Indonesian', 'Soup', 'Betawi'],
                'rating' => 4.6,
                'price_range' => 1,
                'open_now' => true,
                'photo_url' => null,
            ],
        ];
    }

    private function menus(): array
    {
        return [
            'r001' => [
                ['category' => 'Sate', 'items' => [
                    ['name' => 'Sate Ayam', 'price' => 35000, 'description' => '10 tusuk sate ayam bumbu kacang'],
                    ['name' => 'Sate Kambing', 'price' => 45000, 'description' => '10 tusuk sate kambing muda'],
                    ['name' => 'Sate Lilit', 'price' => 40000, 'description' => 'Sate lilit ikan khas Bali'],
                ]],
                ['category' => 'Pelengkap', 'items' => [
                    ['name' => 'Lontong', 'price' => 5000, 'description' => 'Lontong kukus'],
                    ['name' => 'Nasi Putih', 'price' => 5000, 'description' => 'Nasi putih pulen'],
                    ['name' => 'Es Teh Manis', 'price' => 8000, 'description' => 'Teh manis dingin'],
                ]],
            ],
            'r002' => [
                ['category' => 'Lauk', 'items' => [
                    ['name' => 'Rendang Sapi', 'price' => 40000, 'description' => 'Rendang daging sapi empuk bumbu rempah'],
                    ['name' => 'Ayam Pop', 'price' => 30000, 'description' => 'Ayam rebus khas Padang'],
                    ['name' => 'Gulai Ikan', 'price' => 35000, 'description' => 'Ikan dalam kuah gulai kuning'],
                ]],
                ['category' => 'Sayur', 'items' => [
                    ['name' => 'Gulai Daun Singkong', 'price' => 10000, 'description' => 'Daun singkong dalam kuah santan'],
                    ['name' => 'Perkedel Jagung', 'price' => 8000, 'description' => 'Bakwan jagung goreng'],
                    ['name' => 'Nasi Putih', 'price' => 5000, 'description' => 'Nasi putih'],
                ]],
            ],
        ];
    }

    private function reviews(): array
    {
        return [
            'r001' => [
                ['author' => 'Budi S.', 'rating' => 5, 'text' => 'Satenya enak banget, bumbu kacangnya kental!', 'created_at' => '2024-03-10'],
                ['author' => 'Dewi R.', 'rating' => 4, 'text' => 'Tempat nyaman, pelayanan cepat. Worth it!', 'created_at' => '2024-03-08'],
                ['author' => 'Ahmad F.', 'rating' => 5, 'text' => 'Sate kambingnya empuk, tidak bau. Recommended!', 'created_at' => '2024-03-05'],
            ],
            'r002' => [
                ['author' => 'Rina M.', 'rating' => 4, 'text' => 'Rendangnya otentik, mirip masakan rumah.', 'created_at' => '2024-03-09'],
                ['author' => 'Hendra W.', 'rating' => 5, 'text' => 'Murah meriah, porsi besar. Cocok buat makan siang!', 'created_at' => '2024-03-07'],
                ['author' => 'Sari A.', 'rating' => 4, 'text' => 'Ayam popnya enak, tapi antrian panjang.', 'created_at' => '2024-03-03'],
            ],
        ];
    }

    public function search(string $query): array
    {
        $query = strtolower($query);

        return array_values(array_filter(
            $this->restaurants(),
            fn($r) => str_contains(strtolower($r['name']), $query)
                || collect($r['cuisines'])->contains(fn($c) => str_contains(strtolower($c), $query))
        ));
    }

    public function findById(string $id): array
    {
        $restaurant = collect($this->restaurants())->firstWhere('id', $id);

        return $restaurant ?? [];
    }

    public function getMenu(string $restaurantId): array
    {
        return $this->menus()[$restaurantId] ?? [];
    }

    public function getReviews(string $restaurantId): array
    {
        return $this->reviews()[$restaurantId] ?? [];
    }

    public function getNearby(float $lat, float $lng): array
    {
        // TODO: swap to RealZomatoRepository when API key available
        return array_slice($this->restaurants(), 0, 3);
    }

    public function searchLocations(string $query): array
    {
        $locations = [
            ['id' => 'l001', 'name' => 'Jakarta Pusat', 'city' => 'Jakarta'],
            ['id' => 'l002', 'name' => 'Jakarta Selatan', 'city' => 'Jakarta'],
            ['id' => 'l003', 'name' => 'Bandung', 'city' => 'Bandung'],
        ];

        $query = strtolower($query);

        return array_values(array_filter(
            $locations,
            fn($l) => str_contains(strtolower($l['name']), $query)
                || str_contains(strtolower($l['city']), $query)
        ));
    }
}