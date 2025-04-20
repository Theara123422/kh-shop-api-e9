<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Color extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code'
    ];

    public function products(){
        return $this->hasMany(\App\Models\Product::class);
    }

    /**
     *
     * @return HasMany
     */
    public function productVariants() : HasMany
    {
        return $this->hasMany(\App\Models\ProductVariant::class);
    }
}
