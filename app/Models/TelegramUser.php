<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{
    protected $fillable = [
        'telegram_id',
        'first_name',
        'last_name',
        'username',
        'language_code',
        'last_interaction_at',
    ];

    protected function casts(): array
    {
        return [
            'telegram_id' => 'integer',
            'last_interaction_at' => 'datetime',
        ];
    }
}