<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceVideo extends Model
{
    use HasFactory;

    protected $table = 'service_videos';
    protected $fillable = ['video_url'];
}
