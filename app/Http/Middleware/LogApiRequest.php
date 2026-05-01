<?php

namespace App\Http\Middleware;

use App\Models\RequestLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequest
{
    private array $sensitiveFields = ['password', 'token', 'secret', 'access_token', 'refresh_token'];

    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        // skip telegram webhook — high frequency, noisy
        if (str_contains($request->path(), 'telegram/webhook')) {
            return $response;
        }

        $elapsed = (int) round((microtime(true) - $startTime) * 1000);

        RequestLog::create([
            'user_id' => $request->user()?->id,
            'method' => $request->method(),
            'path' => '/' . $request->path(),
            'query_params' => $request->query() ?: null,
            'request_body' => $this->maskSensitive($request->except([])),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_headers' => $this->filterHeaders($request->headers->all()),
            'response_status' => $response->getStatusCode(),
            'response_time_ms' => $elapsed,
            'created_at' => now(),
        ]);

        return $response;
    }

    private function maskSensitive(array $data): array
    {
        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $this->sensitiveFields)) {
                $data[$key] = '***';
            } elseif (is_array($value)) {
                $data[$key] = $this->maskSensitive($value);
            }
        }

        return $data;
    }

    private function filterHeaders(array $headers): array
    {
        $skip = ['cookie', 'authorization'];

        return collect($headers)
            ->except($skip)
            ->map(fn($v) => is_array($v) && count($v) === 1 ? $v[0] : $v)
            ->toArray();
    }
}