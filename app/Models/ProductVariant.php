<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    /**
     *
     * @return BelongsTo
     * get color associate with variant
     */
    public function color(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Color::class);
    }

    /**
     *
     * @return BelongsTo
     * get color associate with variant
     */
    public function size(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Size::class);
    }

    /**
     * @return HasOne
     */
    public function productStock(): BelongsTo
    {
        return $this->belongsTo(ProductStock::class, 'product_stock_id', 'id');
    }
}
