<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;
    
    protected $fillable = ['category_id', 'pro_name', 'phone_number', 'city', 'amount_paid', 'category', 'pending_amount', 'payment_gateway', 'hub', 'tshirt_cap', 'source', 'status'];

    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
