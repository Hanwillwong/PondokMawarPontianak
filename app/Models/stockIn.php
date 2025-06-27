<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class stockIn extends Model
{
    use HasFactory;

    public function supplier()
    {
        return $this->belongsTo(suppliers::class, 'supplier_id');
    }

    public function product()
    {
        return $this->belongsTo(products::class, 'product_id');
    }
}
