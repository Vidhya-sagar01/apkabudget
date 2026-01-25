<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'quotation_id',
        'service_name',
        'description',
        'unit',
        'quantity',
        'rate',
        'amount',
        'image',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
