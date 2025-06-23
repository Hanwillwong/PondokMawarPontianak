<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;

/**
 * @method \NotificationChannels\WebPush\PushSubscription updatePushSubscription(string $endpoint, string|null $key = null, string|null $token = null, string|null $contentEncoding = null)
 */
class User extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable, HasPushSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'role',
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

    public function user_addresses()
    {
        return $this->belongsTo(user_addresses::class,'user_id');
    }

    public function orders()
    {
        return $this->hasMany(orders::class,'user_id');
    }

    public function user_address()
    {
        return $this->hasMany(user_addresses::class, 'user_id');
    }

}
