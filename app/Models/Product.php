<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'rate',
        'imported_from',
        'image',
        'category_id'
    ];

    /**
     * this function is used to get the products belong the category
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    /**
     * get the variant belong to this product
     * @return HasMany
     */
    public function productVariants(): HasMany
    {
        return $this->hasMany(\App\Models\ProductVariant::class);
    }
}
