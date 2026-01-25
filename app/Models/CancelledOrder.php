<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelledOrder extends Model
{
    use HasFactory;

    protected $table = 'cancelled_order';
    protected $fillable = ['order_id', 'provider_id', 'reason','subscription_id'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
