<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariantRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:variants,id',
            'color_id' => 'required|exists:colors,id'
        ];
    }
}
