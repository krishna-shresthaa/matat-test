<?php

namespace App\Traits;

use App\Models\Order;
use App\Models\OrderLineItem;

trait SyncOrdersTrait
{
    protected function syncOrder($orderData)
    {
        $order = Order::updateOrCreate([
            'id' => $orderData['id']
        ], [
            'number' => $orderData['number'],
            'order_key' => $orderData['order_key'],
            'status' => $orderData['status'],
            'date_created' => $orderData['date_created'],
            'total' => $orderData['total'],
            'customer_id' => $orderData['customer_id'],
            'customer_note' => $orderData['customer_note'],
            'billing' => $orderData['billing'],
            'shipping' => $orderData['shipping'],
        ]);

        foreach ($orderData['line_items'] as $lineItemData) {
            OrderLineItem::updateOrCreate([
                'id' => $lineItemData['id']
            ], [
                'order_id' => $order->id,
                'data' => $lineItemData
            ]);
        }
    }
}
