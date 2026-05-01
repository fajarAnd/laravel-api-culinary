<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'method' => $this->method,
            'path' => $this->path,
            'query_params' => $this->query_params,
            'request_body' => $this->request_body,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'request_headers' => $this->request_headers,
            'response_status' => $this->response_status,
            'response_time_ms' => $this->response_time_ms,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}