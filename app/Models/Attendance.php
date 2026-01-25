<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['admin_id', 'date', 'check_in', 'check_out', 'status', 'working_minutes'];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

}
