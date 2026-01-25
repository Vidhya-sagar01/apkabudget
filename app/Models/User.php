<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role',
        'name',
        'email',
        'mobile_no',
        'otp',
        'password',
        'temp_password',
        'country_id',
        'state_id',
        'city_id',
        'pincode',
        // 'address',
        'latitude',
        'longitude',
        'token',
        'device_id',
        'device_token',
        'device_type',
        'device_model',
        'ip_address',
        'login_at',
        'logout_at',
        'category_id',
        'experience',
        'identity_id',
        'identity_number',
        'identity_image',
        'identity_image_back',
        'is_blocked'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function givenReviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'user_id');
    }
    public function transactions()
{
    return $this->hasMany(Transaction::class, 'user_id');
}
  public function zones()
    {
        return $this->belongsToMany(Zone::class, 'zone_provider');
    }
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function identityType()
    {
        return $this->belongsTo(IdentityType::class, 'identity_id');
    }

}
