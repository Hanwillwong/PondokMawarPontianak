<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_addresses extends Model
{
    use HasFactory;

    protected $table = 'user_addresses';
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'province',
        'city',
        'address',
        'post_code',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
