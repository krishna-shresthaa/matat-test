<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Traits\SyncOrdersTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    use SyncOrdersTrait;

    public function index(Request $request)
    {
        $query = Order::query();

        // Search
        if ($request->has('search')) {
            $query->whereAny(["number", "customer_note"], 'LIKE', '%'.$request->input('search').'%');
        }

        // Filter
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Sort
        if ($request->has('sort_by')) {
            $query->orderBy($request->input('sort_by'), $request->input('sort_order', 'asc'));
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $orders  = $query->paginate($perPage);

        return response()->json($orders);
    }

    public function sync(Request $request)
    {
        // Fetch new and updated orders from WooCommerce API
        $url = config('woocommerce.api_url').'/orders/batch';

        $response = Http::withBasicAuth(
            config('woocommerce.api_key'),
            config('woocommerce.api_secret')
        )->get($url);

        if ($response->successful()) {
            foreach ($response->json() as $orderData) {
                $this->syncOrder($orderData);
            }
        } else {
            return response()->json(['error' => 'Failed to fetch orders from WooCommerce API.'], 500);
        }

        return response()->json(['message' => 'Orders synced successfully.']);
    }
}
