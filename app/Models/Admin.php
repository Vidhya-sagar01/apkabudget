<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $guard = 'admin';
    protected $fillable = ['role', 'name', 'email', 'mobile_no', 'mobile_official','mobile_emergency', 'password', 'temp_password', 'image', 'aadhaar_front', 'aadhaar_back', 'pan_card', 'marksheet_10', 'marksheet_12', 'experience_letter', 'bank_name', 'account_holder', 'account_number', 'ifsc_code', 'branch_name', 'salary_amount', 'salary_type', 'joining_date', 'status'];
    protected $hidden = ['password', 'remember_token'];
}
