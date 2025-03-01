<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'size_number'
    ];

    public function products(){
        return $this->hasMany(\App\Models\Product::class);
    }
}
