<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Traits\GeneralResponse;

class ProductVariantController extends Controller
{
    use GeneralResponse;
    public function index(){
        $productVariants = ProductVariant::all();

        return $this->successReponseWithData(
            "Get All Product Variants Successfully",
            $productVariants
        );
    }

    public function show($id)
    {
        // Retrieve the product with its associated variants
        $product = Product::with('productVariants')->findOrFail($id);

        // Return a success response with the product's variants
        return $this->successReponseWithData(
            "Get Product Variant Successfully",
            $product->productVariants
        );
    }

}
