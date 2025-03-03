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

class ProductController extends Controller
{
    use GeneralResponse;

    /**
     * list limit the products by on client request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        // Retrieve filter, order, limit, page, and search parameters from request
        $filter = strtolower($request->get('filter', 'default')); // Default filter
        $order = $request->get('order', 'desc'); // Default order
        $limit = (int)$request->get('limit', 4); // Default pagination limit
        $page = (int)$request->get('page', 1); // Default page number (1)
        $search = $request->get('search', '');

        // Ensure orderBy is only 'asc' or 'desc'
        $orderBy = in_array($order, ['asc', 'desc']) ? $order : 'desc';

        // Build query
        $query = Product::query();

        // Apply search filter if provided
        if (!empty($search)) {
            $query->where("name", "like", "%{$search}%");
        }

        // Apply the filter type
        if ($filter === "latest") {
            $query->latest();
        } elseif ($filter === "rating") {
            $query->orderBy('rate', $orderBy);
        } elseif ($filter === "promotion") {
            $query->where("sale_price", ">", 0)
                ->orderBy("id", $orderBy);
        } else {
            // Default case: sort by ID if no valid filter provided
            $query->orderBy('id', $orderBy);
        }

        // Paginate results
        $products = $query->paginate($limit, ['*'], 'page', $page);

        // Return the response
        return $this->successReponseWithData(
            "Get all products successfully",
            $products
        );
    }

    public function getByCategory(Request $request)
    {
        // Get the category from the request and validate it
        $categoryId = $request->get('category');
        $category = \App\Models\Category::find($categoryId); // Check if the category exists

        // If category not found, return error response
        if (!$category) {
            return response()->json([
                'error' => 'Category not found.',
            ], 404);
        }

        // Define allowed filter types and ensure the filter is valid
        $allowedFilters = ['promotion', 'low_price', 'high_price', 'default'];
        $filter = $request->get('filter', 'default');
        if (!in_array($filter, $allowedFilters)) {
            $filter = 'default';  // Set default filter if the requested filter is invalid
        }

        // Ensure the order is valid ('asc' or 'desc')
        $order = $request->get('order', 'desc');
        if (!in_array($order, ['asc', 'desc'])) {
            $order = 'desc'; // Default order if invalid
        }

        // Query based on filter and category
        $query = Product::where('category_id', $categoryId); // Filter by category

        // Apply the filter condition
        switch ($filter) {
            case 'promotion':
                $query->where('sale_price', '>', 0);
                break;

            case 'low_price':
                $query->where('sale_price', '>', 0)->where('sale_price', '<', 20);
                break;

            case 'high_price':
                $query->where('sale_price', '>=', 20);
                break;

            case 'default':
            default:
                // If default, we show the latest products
                $query->latest();
                break;
        }

        // Order the results by rating or other fields if needed
        $products = $query->orderBy('rating', $order)->get();

        // If no products found, return a custom message
        if ($products->isEmpty()) {
            return response()->json([
                'message' => 'No products found for the selected filter.',
                'related_products' => [],
            ], 200);
        }

        // Get related products (e.g., other products from the same category)
        $relatedProducts = Product::where('category_id', $categoryId)
            ->where('id', '!=', $products->first()->id) // Exclude the main products from related ones
            ->limit(4) // Get a maximum of 5 related products
            ->get();

        // Return the response with products and related products
        return response()->json([
            'message' => 'Products fetched successfully.',
            'products' => $products,
            'related_products' => $relatedProducts,
        ]);
    }



    /**
     * create new product
     * @param ProductRequest $request
     * @return JsonResponse
     */
    public function store(ProductRequest $request){
        try{
            $data = $request->validated();

            if($request->hasFile('image')){
                $fileName = time().'.'.$request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('images'), $fileName);
            }

            $data['image'] = $fileName;

            \App\Models\Product::create($data);

            return $this->successResponse(
                "Product created successfully",
            );
        }catch (QueryException $exception){
            return $this->errorResponse(
                $exception->getMessage(),
                $exception->getCode()
            );
        }
        catch (ValidationException $exception){
            return $this->errorResponse(
                'Validation Error',
                $exception->getCode()
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
        $product = \App\Models\Product::findOrFail($id);

        return $this->successReponseWithData(
            "Get product successfully",
            $product
        );
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

            if($request->hasFile('image')){
                if($product->image){
                    unlink(public_path('images').'/'.$product->image);
                }
                $fileName = time().'.'.$request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('images'), $fileName);
            }
            $data['image'] = $fileName;

//        dd($data);
            $product->update($data);

            return $this->successResponse(
                "Product updated successfully",
            );
        }catch (\Illuminate\Validation\ValidationException $exception){
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

        if($product->image){
            unlink(public_path('images').'/'.$product->image);
        }

        $product->delete();

        return $this->successResponse(
            "Product deleted successfully",
        );
    }
}
