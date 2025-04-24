<?php

namespace App\Http\Controllers\product;


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

    public function latestProducts(): JsonResponse
    {
        $products = Product::orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->map(function ($product) {
                return [
                    'id'            => $product->id,
                    'image_url'     => $product->image,
                    'regular_price' => $product->regular_price,
                    'sale_price'    => $product->sale_price,
                    'title'         => $product->name,
                    'star'          => $product->star,
                ];
            });

        return response()->json([
            'status'  => 200,
            'message' => 'Latest products fetched successfully.',
            'data'    => $products,
            'total'   => $products->count(),
        ]);
    }

    public function promotionalProducts(): JsonResponse
    {
        $products = Product::where('sale_price', '>', 0)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->map(function ($product) {
                return [
                    'id'            => $product->id,
                    'image_url'     => $product->image,
                    'regular_price' => $product->regular_price,
                    'sale_price'    => $product->sale_price,
                    'title'         => $product->name,
                    'star'          => $product->star,
                ];
            });

        return response()->json([
            'status'  => 200,
            'message' => 'Promotional products fetched successfully.',
            'data'    => $products,
            'total'   => $products->count(),
        ]);
    }

    public function topRatedProducts(): JsonResponse
    {
        $products = Product::where('star', '>=', 4)
            ->orderBy('star', 'desc')
            ->take(4)
            ->get()
            ->map(function ($product) {
                return [
                    'id'            => $product->id,
                    'image_url'     => $product->image,
                    'regular_price' => $product->regular_price,
                    'sale_price'    => $product->sale_price,
                    'title'         => $product->name,
                    'star'          => $product->star,
                ];
            });

        return response()->json([
            'status'  => 200,
            'message' => 'Top rated products fetched successfully.',
            'data'    => $products,
            'total'   => $products->count(),
        ]);
    }

    public function getProductsByCategory(Request $request): JsonResponse
    {
        $categoryId = $request->query('category_id');

        if (!$categoryId) {
            return response()->json([
                'status'  => 400,
                'message' => 'Missing category_id parameter.',
                'data'    => [],
            ], 400);
        }

        $products = Product::where('category_id', $categoryId)
            ->take(4)
            ->get()
            ->map(function ($product) {
                return [
                    'id'            => $product->id,
                    'image_url'     => $product->image,
                    'regular_price' => $product->regular_price,
                    'sale_price'    => $product->sale_price,
                    'title'         => $product->name,
                    'star'          => $product->star,
                ];
            });

        return response()->json([
            'status'  => 200,
            'message' => 'Products fetched by category successfully.',
            'data'    => $products,
            'total'   => $products->count(),
        ]);
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

    public function shopProducts(Request $request): JsonResponse
{
    $filter = $request->query('filter');

    $query = Product::query();

    if ($filter) {
        $filter = strtolower($filter);

        if ($filter === 'cheap') {
            $query->orderBy('regular_price', 'asc');
        } elseif ($filter === 'expensive') {
            $query->orderBy('regular_price', 'desc');
        } elseif ($filter === 'promotion') {
            $query->where('sale_price', '>', 0);
        } else {
            // Try to find a category by name
            $category = \App\Models\Category::where('name', 'like', '%' . $filter . '%')->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }
    }

    $products = $query->get();

    $formattedProducts = $products->map(function ($product) {
        $type = null;
        if ($product->sale_price > 0) {
            $type = 3;
        } elseif ($product->created_at >= now()->subDays(30)) {
            $type = 1;
        } elseif ($product->star >= 4) {
            $type = 2;
        }

        return [
            'id'            => $product->id,
            'image_url'     => $product->image,
            'regular_price' => $product->regular_price,
            'sale_price'    => $product->sale_price,
            'title'         => $product->name,
            'star'          => $product->star,
            'type'          => $type,
        ];
    });

    return response()->json([
        'code'    => 200,
        'message' => '',
        'data'    => [
            'latest'  => $formattedProducts->take(3)->values(),
            'related' => $formattedProducts->slice(4)->take(4)->values(),
        ],
    ]);
}


    public function getProductsByPriceType(Request $request): JsonResponse
    {
        $type = $request->query('type');

        $query = Product::query();

        if ($type === 'cheap') {
            $query->orderBy('regular_price', 'asc');
        } elseif ($type === 'expensive') {
            $query->orderBy('regular_price', 'desc');
        } else {
            return response()->json([
                'status'  => 400,
                'message' => 'Invalid type. Must be either "cheap" or "expensive".',
                'data'    => [],
            ], 400);
        }

        $products = $query->take(4)->get()->map(function ($product) {
            return [
                'id'            => $product->id,
                'image_url'     => $product->image,
                'regular_price' => $product->regular_price,
                'sale_price'    => $product->sale_price,
                'title'         => $product->name,
                'star'          => $product->star,
            ];
        });

        return response()->json([
            'status'  => 200,
            'message' => ucfirst($type) . ' products fetched successfully.',
            'data'    => $products,
            'total'   => $products->count(),
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
            $product = Product::findOrFail($id);
            $data = $request->validated();

            if ($request->hasFile('image')) {
                if ($product->image) {
                    unlink(public_path('images') . '/' . $product->image);
                }
                $fileName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('images'), $fileName);
            }
            $data['image'] = $fileName;


            $product->update($data);

            return $this->successResponse(
                "Product updated successfully",
            );
        } catch (ValidationException $exception) {
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
        $product = Product::findOrFail($id);

        if ($product->image) {
            unlink(public_path('images') . '/' . $product->image);
        }

        $product->delete();

        return $this->successResponse(
            "Product deleted successfully",
        );
    }
}
