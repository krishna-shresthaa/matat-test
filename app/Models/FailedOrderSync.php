<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedOrderSync extends Model
{
    protected $fillable = ['order_data'];

    protected $casts = [
        'order_data' => 'array',
    ];
}
