<?php

namespace App\Http\Controllers\product;

use App\Enums\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Traits\GeneralResponse;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class ProductController extends Controller
{
    use GeneralResponse;

    /**
     * list limit the products by on client request
     * @return JsonResponse
     * @group Product Page
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $limit  = (int) $request->get('limit', 10);
        $page   = $request->get('page');

        $query = Product::query();

        // Apply search if provided
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }


        if ($page) {
            $products = $query->paginate($limit, ['*'], 'page', $page);
        } else {

            $products = $query->get();
        }

        return $this->successReponseWithData(
            'Get products successfully',
            $products
        );
    }


    public function getProductsByType(Request $request): JsonResponse
    {
        // Get the type(s) from the request (allow multiple types)
        $requestedTypes = $request->get('type', null);

        // Start building the query
        $query = Product::query();

        if ($requestedTypes) {
            // Allow multiple types by splitting the 'type' parameter if it's a comma-separated string
            $requestedTypes = explode(',', $requestedTypes);

            // Apply filter based on the requested types
            $query->where(function ($query) use ($requestedTypes) {
                foreach ($requestedTypes as $type) {
                    switch ($type) {
                        case '1': 
                            $query->orWhere('created_at', '>=', now()->subDays(30));
                            break;

                        case '2':
                            $query->orWhere('star', '>=', 4);
                            break;

                        case '3': // Promotion Products (sale_price = 0)
                            $query->orWhere('sale_price', '>', 0);
                            break;
                    }
                }
            });
        }

        // Get the products after applying the filter
        $products = $query->get()->map(function ($product) use ($requestedTypes) {
            // Dynamically assign the 'type' based on the product properties
            $types = [];

            // Check if the product matches each condition
            if ($product->created_at >= now()->subDays(30)) {
                $types[] = '1'; // New Products
            }
            if ($product->star >= 4) {
                $types[] = '2'; // Popular Products
            }
            if ($product->sale_price > 0) {
                $types[] = '3'; // Promotion Products
            }

            // Return product with dynamically assigned types
            return [
                'id'             => $product->id,
                'image_url'      => $product->image,
                'regular_price'  => $product->regular_price,
                'sale_price'     => $product->sale_price,
                'title'          => $product->name,
                'star'           => $product->rating,
                'type'           => implode(',', $types), // Assign multiple types as a string
            ];
        });

        return response()->json([
            'status'  => 200,
            'message' => 'Successfully fetched products by dynamic type.',
            'data'    => $products,
            'total'   => $products->count(),
        ]);
    }




    /**
     * create new product
     * @param ProductRequest $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            // dump($data);
            if ($request->hasFile('image')) {
                $fileName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('images'), $fileName);
            }

            $data['image'] = 'http://localhost:8000/images/' . $fileName;

            Product::create($data);

            return $this->successResponse(
                "Product created successfully",
            );
        } catch (QueryException $exception) {
            return $this->errorResponse(
                $exception->getMessage(),
                402
            );
        } catch (ValidationException $exception) {
            return $this->errorResponse(
                'Validation Error',
                402
            );
        }
    }

    /**
     * show the specific product
     * @param $id
     * @return JsonResponse
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
                'code' => $size->size_number ?? '',
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


    public function getShopProducts(Request $request)
    {
        // Retrieve all products
        $products = Product::query()->get();

        // Map over products to assign types dynamically
        $filteredProducts = $products->map(function ($product) {
            // Initialize type as an empty array
            $productTypes = [];

            // Check for promotion product (sale_price = 0)
            if ($product->sale_price > 0) {
                $productTypes[] = '3'; // Promotion Product
            }

            // Check for new product (created in the last 30 days)
            if ($product->created_at >= now()->subDays(30)) {
                $productTypes[] = '1'; // New Product
            }

            // Check for popular product (star >= 4)
            if ($product->star >= 4) {
                $productTypes[] = '2'; // Popular Product
            }

            // If the product has at least one valid type, return it
            if (!empty($productTypes)) {
                return [
                    'id'             => $product->id,
                    'image_url'      => $product->image,
                    'regular_price'  => $product->regular_price,
                    'sale_price'     => $product->sale_price,
                    'title'          => $product->name,
                    'star'           => $product->star,
                    'type'           => implode(',', $productTypes), // Comma-separated types
                ];
            }

            // If no type is assigned, do not return this product
            return null;
        })->filter(); // Remove products without a valid type

        // Return the response
        return response()->json([
            'status'  => 200,
            'message' => 'Successfully fetched products by dynamic type.',
            'data'    => $filteredProducts,
            'total'   => $filteredProducts->count(),
        ]);
    }



    /**
     * edit the specific product
     * @param ProductRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ProductRequest $request, $id)
    {
        try {
            $product = \App\Models\Product::findOrFail($id);
            $data = $request->validated();

            if ($request->hasFile('image')) {
                if ($product->image) {
                    unlink(public_path('images') . '/' . $product->image);
                }
                $fileName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('images'), $fileName);
            }
            $data['image'] = $fileName;

            //        dd($data);
            $product->update($data);

            return $this->successResponse(
                "Product updated successfully",
            );
        } catch (\Illuminate\Validation\ValidationException $exception) {
            return $this->errorResponse(
                $exception->getMessage(),
                $exception->getCode()
            );
        }
    }

    /**
     * delete the specific product
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $product = \App\Models\Product::findOrFail($id);

        if ($product->image) {
            unlink(public_path('images') . '/' . $product->image);
        }

        $product->delete();

        return $this->successResponse(
            "Product deleted successfully",
        );
    }
}
