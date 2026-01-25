<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoneRoundRobinTracker extends Model
{
    use HasFactory;
    
    protected $fillable = ['zone_id', 'last_assigned_user_id'];
}
