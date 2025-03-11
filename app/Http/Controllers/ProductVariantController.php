<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Traits\GeneralResponse;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

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

    /**
     * Create new Variants
     * @param ProductVariantRequest $request
     * @return JsonResponse
     */
    public function store(ProductVariantRequest $request)
    {
        try{
            $data = $request->validated();
            ProductVariant::create($data);
            return $this->successResponse(
                "Add Product Variant Successfully",
            );
        }catch (QueryException|ValidationException $exception){
            return $this->errorResponse(
                $exception->getMessage(),
                $exception->getCode()
            );
        }
    }

    public function update(ProductVariantRequest $request, $id){
        try{
            $product_variant = ProductVariant::findOrFail($id);
            $data = $request->validated();
            $product_variant->update($data);
            return $this->successResponse(
                "Update Product Variant Successfully",
            );
        }catch (QueryException|ValidationException $exception){
            return $this->errorResponse(
                $exception->getMessage(),
                $exception->getCode()
            );
        }
    }

    public function destroy($id){
        $product_variant = ProductVariant::findOrFail($id);

        $product_variant->delete();

        return $this->successResponse(
            "Delete Product Variant Successfully",
        );
    }
}
