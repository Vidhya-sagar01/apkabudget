<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeTiming extends Model
{
    use HasFactory;

    protected $fillable = ['admin_id', 'day_of_week', 'start_time', 'end_time', 'lunch_start', 'lunch_end'];
}
