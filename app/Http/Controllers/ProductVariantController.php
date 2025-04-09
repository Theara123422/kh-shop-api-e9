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
    public function index()
    {
        $productVariants = ProductVariant::all();

        return $this->successReponseWithData(
            "Get All Product Variants Successfully",
            $productVariants
        );
    }

    /**
     * Summary of show
     * @param mixed $id
     * @return JsonResponse|mixed
     * @group Product Detail
     */
    public function show($id)
    {
        $product = Product::with(['productVariants.color', 'productVariants.size', 'productVariants.productStock', 'category'])
            ->findOrFail($id);

        $colors = $product->productVariants->pluck('color')->unique('id')->values()->map(function ($color) {
            return [
                'code' => $color->code ?? '',
                'name' => $color->name ?? '',
            ];
        });

        $sizes = $product->productVariants->pluck('size')->unique('id')->values()->map(function ($size) {
            return [
                'code' => $size->code ?? '',
                'name' => $size->name ?? '',
            ];
        });

        $categoryName = $product->category->name ?? null;

        
        if (!$categoryName && $product->category_id) {
            $categoryName = \App\Models\Category::find($product->category_id)?->name ?? '';
        }

        $data = [
            'id'             => $product->id,
            'image_url'      => $product->image ?? '',
            'regular_price'  => $product->regular_price ?? '',
            'sale_price'     => $product->sale_price ?? '',
            'category'       => $categoryName,
            'title'          => $product->name ?? '',
            'color'          => $colors,
            'size'           => $sizes,
        ];

        return response()->json([
            'status'  => 200,
            'message' => 'Successfully',
            'data'    => $data,
            'total'   => $product->productVariants->count(),
        ]);
    }



    /**
     * Create new Variants
     * @param ProductVariantRequest $request
     * @return JsonResponse
     */
    public function store(ProductVariantRequest $request)
    {
        try {
            $data = $request->validated();
            ProductVariant::create($data);
            return $this->successResponse(
                "Add Product Variant Successfully",
            );
        } catch (QueryException | ValidationException $exception) {
            return $this->errorResponse(
                $exception->getMessage(),
                $exception->getCode()
            );
        }
    }

    public function update(ProductVariantRequest $request, $id)
    {
        try {
            $product_variant = ProductVariant::findOrFail($id);
            $data = $request->validated();
            $product_variant->update($data);
            return $this->successResponse(
                "Update Product Variant Successfully",
            );
        } catch (QueryException | ValidationException $exception) {
            return $this->errorResponse(
                $exception->getMessage(),
                $exception->getCode()
            );
        }
    }

    public function destroy($id)
    {
        $product_variant = ProductVariant::findOrFail($id);

        $product_variant->delete();

        return $this->successResponse(
            "Delete Product Variant Successfully",
        );
    }
}
