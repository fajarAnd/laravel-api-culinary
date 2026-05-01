<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\RequestLogResource;
use App\Models\RequestLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestLogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'method' => 'nullable|in:GET,POST,PUT,PATCH,DELETE',
            'status' => 'nullable|integer',
            'ip' => 'nullable|string',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        $query = RequestLog::query()->latest('created_at');

        if ($request->method) {
            $query->where('method', $request->method);
        }
        if ($request->status) {
            $query->where('response_status', $request->status);
        }
        if ($request->ip) {
            $query->where('ip_address', $request->ip);
        }
        if ($request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->to) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->paginate(50);

        return response()->json([
            'success' => true,
            'data' => RequestLogResource::collection($logs->items()),
            'meta' => [
                'total' => $logs->total(),
                'per_page' => $logs->perPage(),
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
            ],
        ]);
    }

    public function show(RequestLog $requestLog): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new RequestLogResource($requestLog),
        ]);
    }

    public function stats(): JsonResponse
    {
        $total = RequestLog::count();
        $avgResponseTime = RequestLog::avg('response_time_ms');

        $byStatus = RequestLog::selectRaw('response_status, count(*) as count')
            ->groupBy('response_status')
            ->orderByDesc('count')
            ->get()
            ->keyBy('response_status')
            ->map(fn($r) => $r->count);

        $topEndpoints = RequestLog::selectRaw('path, method, count(*) as count')
            ->groupBy('path', 'method')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_requests' => $total,
                'avg_response_time_ms' => round($avgResponseTime ?? 0, 2),
                'by_status' => $byStatus,
                'top_endpoints' => $topEndpoints,
            ],
        ]);
    }
}