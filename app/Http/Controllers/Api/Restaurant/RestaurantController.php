<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Contracts\RestaurantRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Restaurant\NearbyRequest;
use App\Http\Requests\Restaurant\SearchRequest;
use App\Http\Resources\Restaurant\RestaurantResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function __construct(private RestaurantRepositoryInterface $restaurants) {}

    public function index(SearchRequest $request): JsonResponse
    {
        $results = $this->restaurants->search($request->q);

        return response()->json([
            'success' => true,
            'data' => array_map(fn($r) => (new RestaurantResource($r))->toArray($request), $results),
            'meta' => ['total' => count($results)],
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $restaurant = $this->restaurants->findById($id);

        if (empty($restaurant)) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => (new RestaurantResource($restaurant))->toArray($request),
        ]);
    }

    public function menu(string $id): JsonResponse
    {
        $restaurant = $this->restaurants->findById($id);

        if (empty($restaurant)) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $menu = $this->restaurants->getMenu($id);

        return response()->json([
            'success' => true,
            'data' => $menu,
        ]);
    }

    public function reviews(string $id): JsonResponse
    {
        $restaurant = $this->restaurants->findById($id);

        if (empty($restaurant)) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $reviews = $this->restaurants->getReviews($id);

        return response()->json([
            'success' => true,
            'data' => $reviews,
            'meta' => ['total' => count($reviews)],
        ]);
    }

    public function nearby(NearbyRequest $request): JsonResponse
    {
        $results = $this->restaurants->getNearby(
            $request->float('lat'),
            $request->float('lng')
        );

        return response()->json([
            'success' => true,
            'data' => array_map(fn($r) => (new RestaurantResource($r))->toArray($request), $results),
            'meta' => ['total' => count($results)],
        ]);
    }

    public function locations(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:1']);

        $results = $this->restaurants->searchLocations($request->q);

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }
}