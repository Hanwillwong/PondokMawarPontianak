<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(categories::class,'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(brands::class,'brand_id');
    }

    public function supplier()
    {
        return $this->belongsTo(suppliers::class,'supplier_id');
    }

    public function product_price()
    {
        return $this->belongsTo(product_prices::class,'product_id');
    }
}
