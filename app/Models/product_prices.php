<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_prices extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'min_quantity',
        'price',
    ];

    public function product()
    {
        return $this->belongsTo(products::class, 'product_id');
    }
}
