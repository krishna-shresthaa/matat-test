<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'order_key',
        'status',
        'date_created',
        'total',
        'customer_id',
        'customer_note',
        'billing',
        'shipping',
    ];

    protected $casts = [
        'billing' => 'array',
        'shipping' => 'array',
        'date_created' => 'datetime',
    ];

    public function lineItems()
    {
        return $this->hasMany(OrderLineItem::class);
    }
}
