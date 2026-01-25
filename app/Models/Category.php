<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $fillable = ['category', 'status','max_price','details'];

    public function services()
    {
        return $this->hasMany(Service::class, 'category_id'); 
    }
}
