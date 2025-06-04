<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categories extends Model
{
    use HasFactory;

    protected $table = 'categories';

    public function products()
    {
        return $this->hasMany(Products::class);
    }

        public function sampleProduct()
    {
        return $this->hasOne(Products::class, 'category_id');
    }
}