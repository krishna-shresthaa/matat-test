<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\OrderListingRequest;
use App\Models\Order;
use App\Resource\ApiResponse;
use App\Services\WooCommerceService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * The WooCommerce service instance.
     *
     * @var WooCommerceService
     */
    protected WooCommerceService $wooCommerceService;

    /**
     * Create a new OrderController instance.
     *
     * @param WooCommerceService $wooCommerceService
     * @return void
     */
    public function __construct(WooCommerceService $wooCommerceService)
    {
        $this->wooCommerceService = $wooCommerceService;
    }

    /**
     * Get a list of orders.
     *
     * @param OrderListingRequest $request
     * @return JsonResponse
     */
    public function index(OrderListingRequest $request)
    {
        try {
            $orders = Order::with('lineItems')
                ->when($request->has('search'), function ($query) use ($request) {
                    $search = $request->input('search');
                    $query->where('number', 'like', "%{$search}%")
                        ->orWhere('order_key', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                })
                ->when($request->has('status'), function ($query) use ($request) {
                    $query->where('status', $request->input('status'));
                })
                ->when($request->has('sort_by'), function ($query) use ($request) {
                    $sortBy        = $request->input('sort_by');
                    $sortDirection = $request->input('sort_direction', 'asc');
                    $query->orderBy($sortBy, $sortDirection);
                }, function ($query) {
                    $query->orderBy('date_created', 'desc');
                })
                ->paginate($request->input('per_page', 15));

            return response()->json($orders);
        } catch (\Exception $e) {
//            throw new ApiException($e->getMessage(), [], 500);
            throw new ApiException("Internal Server Error", [],  500);
        }
    }

    /**
     * Sync new and updated orders.
     *
     * @return JsonResponse
     */
    public function sync(): JsonResponse
    {
        try {
            $totalOrders = $this->wooCommerceService->fetchAndSyncOrders();

            return ApiResponse::make(null, "Synced {$totalOrders} orders from WooCommerce API.");
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), [], 500);
        }
    }
}
