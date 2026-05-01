<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'method',
        'path',
        'query_params',
        'request_body',
        'ip_address',
        'user_agent',
        'request_headers',
        'response_status',
        'response_time_ms',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'query_params' => 'array',
            'request_body' => 'array',
            'request_headers' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}