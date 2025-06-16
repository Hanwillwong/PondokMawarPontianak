<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orders extends Model
{
    use HasFactory;

    protected $table = 'orders';

        public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function order_detail()
    {
        return $this->hasMany(order_details::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(user::class,'user_id');
    }

}
