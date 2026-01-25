<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAssignmentAttempt extends Model
{
    use HasFactory;
    
    protected $fillable = ['order_id', 'zone_id','provider_id','status','plan_id'];
}
