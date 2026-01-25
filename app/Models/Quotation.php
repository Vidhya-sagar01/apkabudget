<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'quotation_no',
        'quotation_date',
        'quotation_from',
        'quotation_for',
        'custom_address',
        'add_notes',
        'discount_value',
        'discount_type',
        'total_amount',
    ];

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'quotation_for');
    }

    public function contact() {
        return $this->belongsTo(ContactUs::class, 'quotation_from');
    }
}
