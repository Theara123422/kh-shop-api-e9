<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        "product_id",
        "variant_id",
        "color_id",
        "size_id",
    ];

    protected $table = 'product_variants';

    /**
     *
     * @return BelongsTo
     * get product associate with variant
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product::class);
    }
}
