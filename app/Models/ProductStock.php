<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductStock extends Model
{
    use HasFactory;

    protected $table = 'product_stocks';

    protected $fillable = [
        'qty_available',
        'qty_sold'
    ];

    /**
     * @return HasOne
     */
    public function productVariant(): HasOne
    {
        return $this->hasOne(ProductVariant::class, 'product_stock_id', 'id');
    }
}
