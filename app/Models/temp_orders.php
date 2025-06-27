<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class temp_orders extends Model
{
    use HasFactory;

    protected $table = 'temp_orders';
    protected $dates = ['expired_at'];
    protected $fillable = [
        'user_id',
        'reference_number',
        'cart',
        'payment_method',
        'purchase_type',
        'address_id',
        'expired_at',
    ];
}
