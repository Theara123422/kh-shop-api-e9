<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariantRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => 'required',
            'color_id' => 'required',
            'size_id' => 'required',
            'product_stock_id' => 'required'
        ];
    }
}
