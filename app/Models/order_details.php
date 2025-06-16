<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order_details extends Model
{
    use HasFactory;

    protected $table = 'order_details'; // jika nama tabelnya tidak jamak otomatis

    protected $fillable = [
        'order_id',
        'product_id',
        'price_at_order',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(products::class, 'product_id');
    }

    
}
