<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class brands extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany(products::class, 'brand_id');
    }
}
