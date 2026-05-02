<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OAuthClientSeeder extends Seeder
{
    public function run(): void
    {
        // Password Grant client
        $passwordId = env('PASSPORT_PASSWORD_CLIENT_ID', Str::uuid());
        if (!DB::table('oauth_clients')->find($passwordId)) {
            DB::table('oauth_clients')->insert([
                'id' => $passwordId,
                'name' => 'Password Grant',
                'secret' => env('PASSPORT_PASSWORD_CLIENT_SECRET', Str::random(40)),
                'provider' => 'users',
                'password_client' => true,
                'personal_access_client' => false,
                'revoked' => false,
                'redirect' => 'http://localhost',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Personal Access client
        $accessId = env('PASSPORT_PERSONAL_ACCESS_CLIENT_ID');
        if ($accessId && !DB::table('oauth_clients')->find($accessId)) {
            DB::table('oauth_clients')->insert([
                'id' => $accessId,
                'name' => 'Personal Access',
                'secret' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET', Str::random(40)),
                'personal_access_client' => true,
                'password_client' => false,
                'revoked' => false,
                'redirect' => 'http://localhost',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
