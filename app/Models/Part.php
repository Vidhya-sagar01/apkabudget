<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'part'];

    public function priceLists()
    {
        return $this->hasMany(PriceList::class, 'part_id');
    }
}
